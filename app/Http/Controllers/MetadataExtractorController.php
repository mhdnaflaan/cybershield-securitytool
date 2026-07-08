<?php

namespace App\Http\Controllers;

use App\Models\Scan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MetadataExtractorController extends Controller
{
    public function index()
    {
        return view('student.metadata-extractor');
    }

    public function extract(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:20480', // 20MB max
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $extension = strtolower($file->getClientOriginalExtension());
        $fileSize = $file->getSize();

        // Extract metadata based on file type
        $metadata = $this->extractMetadata($file->getPathname(), $extension);

        // Check for sensitive data alerts
        $alerts = $this->checkSensitiveData($metadata);

        $result = [
            'file_name' => $originalName,
            'file_size' => $this->formatSize($fileSize),
            'file_type' => $file->getMimeType(),
            'extension' => $extension,
            'uploaded_at' => now()->format('Y-m-d H:i:s'),
            'metadata' => $metadata,
            'alerts' => $alerts,
        ];

        // Save to database
        if (auth()->check()) {
            Scan::create([
                'user_id' => auth()->id(),
                'tool_name' => 'metadata_extractor',
                'input_data' => $originalName,
                'result_data' => $result,
            ]);
        }

        return view('student.metadata-extractor', ['result' => $result]);
    }

    /**
     * Extract metadata from file based on type
     */
    private function extractMetadata($filePath, $extension)
    {
        $metadata = [];

        // Images (EXIF data)
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'heic', 'heif'])) {
            $metadata = $this->extractImageMetadata($filePath);
        }
        // PDFs
        elseif ($extension === 'pdf') {
            $metadata = $this->extractPdfMetadata($filePath);
        }
        // Office documents
        elseif (in_array($extension, ['docx', 'xlsx', 'pptx', 'doc', 'xls', 'ppt'])) {
            $metadata = $this->extractOfficeMetadata($filePath, $extension);
        }
        // Audio files
        elseif (in_array($extension, ['mp3', 'wav', 'flac', 'aac', 'ogg', 'm4a'])) {
            $metadata = $this->extractAudioMetadata($filePath);
        }
        // Video files
        elseif (in_array($extension, ['mp4', 'avi', 'mov', 'mkv', 'webm'])) {
            $metadata = $this->extractVideoMetadata($filePath);
        }
        // General file info
        else {
            $stat = stat($filePath);
            if ($stat) {
                $metadata['File Size'] = $this->formatSize($stat['size']);
                $metadata['Created'] = date('Y-m-d H:i:s', $stat['ctime']);
                $metadata['Modified'] = date('Y-m-d H:i:s', $stat['mtime']);
                $metadata['Accessed'] = date('Y-m-d H:i:s', $stat['atime']);
            }
        }

        return $metadata;
    }

    /**
     * Extract image metadata (EXIF)
     */
    private function extractImageMetadata($filePath)
    {
        $data = [];

        // Basic image info
        $imageInfo = @getimagesize($filePath);
        if ($imageInfo) {
            $data['Width'] = $imageInfo[0] . 'px';
            $data['Height'] = $imageInfo[1] . 'px';
            $data['Mime Type'] = $imageInfo['mime'] ?? 'N/A';
        }

        // EXIF data
        if (function_exists('exif_read_data')) {
            $exif = @exif_read_data($filePath);
            if ($exif) {
                $map = [
                    'Make' => 'Camera Make',
                    'Model' => 'Camera Model',
                    'DateTime' => 'Date/Time',
                    'DateTimeOriginal' => 'Original Date',
                    'DateTimeDigitized' => 'Digitized Date',
                    'ExposureTime' => 'Exposure Time',
                    'FNumber' => 'Aperture',
                    'ISOSpeedRatings' => 'ISO',
                    'FocalLength' => 'Focal Length',
                    'Software' => 'Software Used',
                    'Artist' => 'Artist',
                    'Copyright' => 'Copyright',
                    'GPSLatitude' => 'GPS Latitude',
                    'GPSLongitude' => 'GPS Longitude',
                    'GPSAltitude' => 'GPS Altitude',
                ];

                foreach ($map as $key => $label) {
                    if (isset($exif[$key])) {
                        $value = $exif[$key];
                        if (is_array($value)) {
                            $value = implode(', ', $value);
                        }
                        $data[$label] = $value;
                    }
                }

                // GPS coordinates
                if (isset($exif['GPSLatitude']) && isset($exif['GPSLongitude'])) {
                    $lat = $this->gpsToDecimal($exif['GPSLatitude'], $exif['GPSLatitudeRef'] ?? 'N');
                    $lng = $this->gpsToDecimal($exif['GPSLongitude'], $exif['GPSLongitudeRef'] ?? 'E');
                    if ($lat && $lng) {
                        $data['GPS Coordinates'] = $lat . ', ' . $lng;
                        $data['Google Maps'] = 'https://www.google.com/maps?q=' . $lat . ',' . $lng;
                    }
                }
            }
        } else {
            $data['Note'] = 'EXIF extension not available. Install php-exif for full metadata.';
        }

        return $data;
    }

    /**
     * Convert GPS coordinates to decimal
     */
    private function gpsToDecimal($gps, $ref)
    {
        if (!is_array($gps) || count($gps) < 3) {
            return null;
        }

        $degrees = $this->gpsFloat($gps[0]);
        $minutes = $this->gpsFloat($gps[1]);
        $seconds = $this->gpsFloat($gps[2]);

        $decimal = $degrees + ($minutes / 60) + ($seconds / 3600);

        if (in_array($ref, ['S', 'W'])) {
            $decimal = -$decimal;
        }

        return number_format($decimal, 6);
    }

    private function gpsFloat($coord)
    {
        if (is_array($coord)) {
            return $coord[0] / $coord[1];
        }
        return (float) $coord;
    }

    /**
     * Extract PDF metadata
     */
    private function extractPdfMetadata($filePath)
    {
        $data = [];

        try {
            // Check if PDF parser is installed
            if (class_exists(\Smalot\PdfParser\Parser::class)) {
                $parser = new \Smalot\PdfParser\Parser();
                $pdf = $parser->parseFile($filePath);
                $details = $pdf->getDetails();

                foreach ($details as $key => $value) {
                    $data[ucfirst($key)] = $value;
                }
            } else {
                // Fallback: Use basic file info
                $data['Note'] = 'Install smalot/pdfparser for full PDF metadata extraction.';
            }
        } catch (\Exception $e) {
            $data['Error'] = 'Could not parse PDF metadata.';
            Log::warning('PDF parsing failed: ' . $e->getMessage());
        }

        return $data;
    }

    /**
     * Extract Office document metadata
     */
    private function extractOfficeMetadata($filePath, $extension)
    {
        $data = [];

        // Only works for modern Office formats (ZIP-based)
        if (in_array($extension, ['docx', 'xlsx', 'pptx'])) {
            $zip = new \ZipArchive();
            if ($zip->open($filePath) === true) {
                // Read core.xml
                $coreXml = $zip->getFromName('docProps/core.xml');
                if ($coreXml) {
                    $xml = simplexml_load_string($coreXml);
                    if ($xml) {
                        $namespaces = $xml->getNamespaces(true);
                        $dc = $namespaces['dc'] ?? null;
                        $cp = $namespaces['cp'] ?? null;
                        $dcterms = $namespaces['dcterms'] ?? null;

                        if ($dc) {
                            $data['Author'] = (string) $xml->children($dc)->creator;
                            $data['Title'] = (string) $xml->children($dc)->title;
                            $data['Subject'] = (string) $xml->children($dc)->subject;
                            $data['Description'] = (string) $xml->children($dc)->description;
                        }

                        if ($cp) {
                            $data['Last Modified By'] = (string) $xml->children($cp)->lastModifiedBy;
                            $data['Revision'] = (string) $xml->children($cp)->revision;
                        }
                    }
                }

                // Read app.xml
                $appXml = $zip->getFromName('docProps/app.xml');
                if ($appXml) {
                    $xml = simplexml_load_string($appXml);
                    if ($xml) {
                        $data['Application'] = (string) $xml->Application;
                        $data['Company'] = (string) $xml->Company;
                        $data['Word Count'] = (string) $xml->Words;
                        $data['Character Count'] = (string) $xml->Characters;
                        $data['Page Count'] = (string) $xml->Pages;
                    }
                }

                $zip->close();
            } else {
                $data['Error'] = 'Could not read document metadata.';
            }
        } else {
            // Older formats (doc, xls, ppt) - limited support
            $data['Note'] = 'Limited metadata available for older Office formats.';
        }

        return $data;
    }

    /**
     * Extract audio metadata
     */
    private function extractAudioMetadata($filePath)
    {
        $data = [];

        if (function_exists('id3_get_tag')) {
            $tag = @id3_get_tag($filePath);
            if ($tag) {
                $map = [
                    'title' => 'Title',
                    'artist' => 'Artist',
                    'album' => 'Album',
                    'year' => 'Year',
                    'genre' => 'Genre',
                    'track' => 'Track Number',
                    'comment' => 'Comment',
                    'copyright' => 'Copyright',
                    'band' => 'Band/Orchestra',
                    'composer' => 'Composer',
                    'lyrics' => 'Lyrics',
                ];

                foreach ($map as $key => $label) {
                    if (isset($tag[$key])) {
                        $data[$label] = $tag[$key];
                    }
                }
            }
        } else {
            $data['Note'] = 'ID3 extension not available. Install php-id3 for audio metadata.';
        }

        return $data;
    }

    /**
     * Extract video metadata
     */
    private function extractVideoMetadata($filePath)
    {
        $data = [];

        // Basic file info for videos
        $stat = stat($filePath);
        if ($stat) {
            $data['File Size'] = $this->formatSize($stat['size']);
            $data['Created'] = date('Y-m-d H:i:s', $stat['ctime']);
            $data['Modified'] = date('Y-m-d H:i:s', $stat['mtime']);
        }

        // Try to get media info using FFprobe (if available)
        $ffprobe = shell_exec('which ffprobe 2>/dev/null');
        if (!empty($ffprobe)) {
            try {
                $output = shell_exec(
                    'ffprobe -v quiet -print_format json -show_format -show_streams "' . $filePath . '" 2>&1'
                );
                $info = json_decode($output, true);

                if ($info && isset($info['streams'][0])) {
                    $stream = $info['streams'][0];
                    $data['Codec'] = $stream['codec_name'] ?? 'N/A';
                    $data['Width'] = ($stream['width'] ?? 0) . 'px';
                    $data['Height'] = ($stream['height'] ?? 0) . 'px';
                    $data['Duration'] = isset($stream['duration']) ? round($stream['duration'], 2) . 's' : 'N/A';
                    $data['Bitrate'] = $stream['bit_rate'] ?? 'N/A';
                }
            } catch (\Exception $e) {
                // FFprobe failed
            }
        }

        return $data;
    }

    /**
     * Check for sensitive data alerts
     */
    private function checkSensitiveData($metadata)
    {
        $alerts = [];

        $sensitiveKeys = [
            'GPS Coordinates' => '⚠️ GPS coordinates found! This reveals the exact location where the photo was taken.',
            'GPS Latitude' => '⚠️ GPS data found!',
            'GPS Longitude' => '⚠️ GPS data found!',
            'Google Maps' => '📍 Click to view location on Google Maps.',
            'Artist' => 'ℹ️ Artist/Creator name found.',
            'Author' => 'ℹ️ Author name found.',
            'Last Modified By' => 'ℹ️ Last modified by user found.',
            'Company' => 'ℹ️ Company name found.',
            'Software Used' => 'ℹ️ Software used to create the file found.',
            'Copyright' => 'ℹ️ Copyright information found.',
            'Registrant' => 'ℹ️ Registrant information found.',
            'Registrant Email' => 'ℹ️ Registrant email found.',
            'Admin Email' => 'ℹ️ Administrator email found.',
            'Tech Email' => 'ℹ️ Technical contact email found.',
        ];

        foreach ($sensitiveKeys as $key => $message) {
            if (isset($metadata[$key]) && !empty($metadata[$key])) {
                $alerts[] = $message;
            }
        }

        return $alerts;
    }

    private function formatSize($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        }
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' bytes';
    }
}