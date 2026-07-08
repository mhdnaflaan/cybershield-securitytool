<?php

namespace App\Http\Controllers;

use App\Models\Scan;
use Illuminate\Http\Request;

class HashToolController extends Controller
{
    // Show the hash tool form
    public function index()
    {
        return view('hash-tool');
    }

    // Generate hash
    public function generate(Request $request)
    {
        $request->validate([
            'text' => 'required|string',
            'algorithm' => 'required|in:md5,sha1,sha256,sha512',
        ]);

        $text = $request->input('text');
        $algorithm = $request->input('algorithm');

        // Generate hash
        $hash = hash($algorithm, $text);

        // Prepare result data
        $resultData = [
            'text' => $text,
            'algorithm' => $algorithm,
            'hash' => $hash,
        ];

        // Save to database if user is logged in
        if (auth()->check()) {
            Scan::create([
                'user_id' => auth()->id(),
                'tool_name' => 'hash_tool',
                'input_data' => $text,
                'result_data' => $resultData,
            ]);
        }

        return view('hash-tool', [
            'generatedHash' => $hash,
            'algorithm' => $algorithm,
            'inputText' => $text,
        ]);
    }

    // Identify hash type
    public function identify(Request $request)
    {
        $request->validate([
            'hash' => 'required|string',
        ]);

        $hash = $request->input('hash');
        $type = $this->identifyHashType($hash);

        // Prepare result data
        $resultData = [
            'hash' => $hash,
            'identified_type' => $type,
        ];

        // Save to database if user is logged in
        if (auth()->check()) {
            Scan::create([
                'user_id' => auth()->id(),
                'tool_name' => 'hash_tool',
                'input_data' => $hash,
                'result_data' => $resultData,
            ]);
        }

        return view('hash-tool', [
            'identifiedType' => $type,
            'hashInput' => $hash,
        ]);
    }

    /**
     * Identify hash type based on length and format
     */
    private function identifyHashType($hash)
    {
        $hash = trim($hash);
        $length = strlen($hash);

        // Check if it's a valid hex string
        $isHex = preg_match('/^[a-f0-9]+$/i', $hash);

        if (!$isHex) {
            return 'Not a valid hash (contains non-hex characters)';
        }

        switch ($length) {
            case 32:
                return 'MD5 (32 characters)';
            case 40:
                return 'SHA1 (40 characters)';
            case 64:
                return 'SHA256 (64 characters)';
            case 128:
                return 'SHA512 (128 characters)';
            default:
                return 'Unknown hash type (' . $length . ' characters)';
        }
    }
}