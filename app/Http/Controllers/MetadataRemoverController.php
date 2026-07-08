<?php

namespace App\Http\Controllers;

use App\Models\Scan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MetadataRemoverController extends Controller
{
    public function index()
    {
        return view('metadata-remover');
    }

    public function remove(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:20480|mimes:jpeg,jpg,png,gif,bmp,webp',
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $extension = strtolower($file->getClientOriginalExtension());
        $fileSize = $file->getSize();

        // Extract metadata for preview
        $metadata = $this->extractImageMetadata($file->getPathname());

        // Remove metadata
        $cleanedPath = $this->removeImageMetadata($file->getPathname(), $extension);

        if (!$cleanedPath || !file_exists($cleanedPath)) {
            return back()->withErrors(['file' => 'Could not remove metadata from this file.']);
        }

        $cleanedFilename = basename($cleanedPath);

        // Save to database
        if (auth()->check()) {
            Scan::create([
                'user_id' => auth()->id(),
                'tool_name' => 'metadata_remover',
                'input_data' => $originalName,
                'result_data' => [
                    'original_name' => $originalName,
                    'cleaned_name' => $cleanedFilename,
                    'file_size' => $fileSize,
                    'metadata_removed' => $metadata,
                    'processed_at' => now()->format('Y-m-d H:i:s'),
                ],
            ]);
        }

        return view('metadata-remover', [
            'original_name' => $originalName,
            'cleaned_name' => $cleanedFilename,
            'cleaned_path' => $cleanedFilename,
            'metadata' => $metadata,
            'file_size' => $this->formatSize($fileSize),
        ]);
    }

    public function download($filename)
    {
        $filename = basename($filename);
        $path = storage_path('app/temp/' . $filename);

        if (!file_exists($path)) {
            abort(404, 'File not found.');
        }

        $downloadName = str_replace('cleaned_', '', $filename);

        if (!pathinfo($downloadName, PATHINFO_EXTENSION)) {
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            if ($ext) {
                $downloadName = pathinfo($downloadName, PATHINFO_FILENAME) . '.' . $ext;
            }
        }

        return response()->download($path, $downloadName, [
            'Content-Type' => mime_content_type($path) ?: 'application/octet-stream',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Extract EXIF metadata from image
     */
    private function extractImageMetadata($filePath)
    {
        $data = [];

        if (!function_exists('exif_read_data')) {
            $data['Note'] = 'EXIF extension not available';
            return $data;
        }

        $exif = @exif_read_data($filePath);
        if (!$exif || empty($exif)) {
            $data['Note'] = 'No EXIF metadata found';
            return $data;
        }

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

        // GPS Coordinates
        if (isset($exif['GPSLatitude']) && isset($exif['GPSLongitude'])) {
            $lat = $this->gpsToDecimal($exif['GPSLatitude'], $exif['GPSLatitudeRef'] ?? 'N');
            $lng = $this->gpsToDecimal($exif['GPSLongitude'], $exif['GPSLongitudeRef'] ?? 'E');
            if ($lat && $lng) {
                $data['GPS Coordinates'] = $lat . ', ' . $lng;
            }
        }

        return $data;
    }

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
     * Remove metadata from image (GD + fallbacks)
     */
    private function removeImageMetadata($filePath, $extension)
    {
        // Method 1: GD (PHP)
        if (extension_loaded('gd')) {
            $result = $this->removeImageGD($filePath, $extension);
            if ($result && file_exists($result) && filesize($result) > 0) {
                return $result;
            }
        }

        // Method 2: exiftool (system)
        $exiftool = shell_exec('which exiftool 2>/dev/null');
        if (!empty($exiftool)) {
            $result = $this->removeExiftool($filePath, $extension);
            if ($result && file_exists($result) && filesize($result) > 0) {
                return $result;
            }
        }

        // Method 3: ImageMagick (system)
        $convert = shell_exec('which convert 2>/dev/null');
        if (!empty($convert)) {
            $result = $this->removeImagemagick($filePath, $extension);
            if ($result && file_exists($result) && filesize($result) > 0) {
                return $result;
            }
        }

        // Fallback: copy (metadata remains)
        Log::warning('No metadata removal method available, copying file.');
        return $this->copyFile($filePath);
    }

    private function removeImageGD($filePath, $extension)
    {
        $image = null;

        switch ($extension) {
            case 'jpg':
            case 'jpeg':
                $image = @imagecreatefromjpeg($filePath);
                break;
            case 'png':
                $image = @imagecreatefrompng($filePath);
                break;
            case 'gif':
                $image = @imagecreatefromgif($filePath);
                break;
            case 'bmp':
                if (function_exists('imagecreatefrombmp')) {
                    $image = @imagecreatefrombmp($filePath);
                }
                break;
            case 'webp':
                if (function_exists('imagecreatefromwebp')) {
                    $image = @imagecreatefromwebp($filePath);
                }
                break;
            default:
                return null;
        }

        if (!$image) {
            return null;
        }

        $tempPath = $this->getTempPath($extension);
        $saved = false;

        switch ($extension) {
            case 'jpg':
            case 'jpeg':
                $saved = imagejpeg($image, $tempPath, 90);
                break;
            case 'png':
                $saved = imagepng($image, $tempPath, 9);
                break;
            case 'gif':
                $saved = imagegif($image, $tempPath);
                break;
            case 'bmp':
                if (function_exists('imagebmp')) {
                    $saved = imagebmp($image, $tempPath);
                }
                break;
            case 'webp':
                if (function_exists('imagewebp')) {
                    $saved = imagewebp($image, $tempPath, 80);
                }
                break;
        }

        imagedestroy($image);

        if ($saved && file_exists($tempPath) && filesize($tempPath) > 0) {
            return $tempPath;
        }
        return null;
    }

    private function removeExiftool($filePath, $extension)
    {
        $tempPath = $this->getTempPath($extension);
        $command = 'exiftool -all= -overwrite_original "' . $filePath . '" -o "' . $tempPath . '" 2>&1';
        shell_exec($command);
        if (file_exists($tempPath) && filesize($tempPath) > 0) {
            return $tempPath;
        }
        return null;
    }

    private function removeImagemagick($filePath, $extension)
    {
        $tempPath = $this->getTempPath($extension);
        $command = 'convert "' . $filePath . '" -strip "' . $tempPath . '" 2>&1';
        shell_exec($command);
        if (file_exists($tempPath) && filesize($tempPath) > 0) {
            return $tempPath;
        }
        return null;
    }

    private function copyFile($filePath)
    {
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $tempPath = $this->getTempPath($extension);
        copy($filePath, $tempPath);
        return $tempPath;
    }

    private function getTempPath($extension)
    {
        if (empty($extension)) {
            $extension = 'jpg';
        }

        $extension = preg_replace('/[^a-zA-Z0-9]/', '', $extension);

        if (empty($extension)) {
            $extension = 'jpg';
        }

        $filename = 'cleaned_' . uniqid() . '.' . $extension;
        $path = storage_path('app/temp/' . $filename);

        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        return $path;
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