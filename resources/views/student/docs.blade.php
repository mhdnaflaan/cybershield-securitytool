@extends('layouts.app')

@section('title', 'Student Documentation')

@section('content')
<div class="container mx-auto px-4 py-10 max-w-4xl">
    <div class="bg-white rounded-2xl shadow-lg p-8">

        <h1 class="text-3xl font-bold text-gray-800 mb-2">Student Documentation</h1>
        <p class="text-gray-500 mb-8">Learn how to use each cybersecurity tool effectively.</p>

        
        <div class="bg-gray-50 rounded-xl p-4 mb-8">
            <h3 class="font-bold text-gray-700 mb-2"> Table of Contents</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
                <a href="#dns" class="text-blue-600 hover:underline">1. DNS Lookup Tool</a>
                <a href="#whois" class="text-blue-600 hover:underline">2. Whois Lookup</a>
                <a href="#ip" class="text-blue-600 hover:underline">3. IP Reputation Checker</a>
                <a href="#encoder" class="text-blue-600 hover:underline">4. Base64 & URL Encoder/Decoder</a>
                <a href="#hash" class="text-blue-600 hover:underline">5. Hash Tool</a>
                <a href="#metadata" class="text-blue-600 hover:underline">6. Metadata Extractor</a>
                <a href="#tips" class="text-blue-600 hover:underline">7. Security Tips</a>
            </div>
        </div>

        
        <div id="dns" class="mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-2"> DNS Lookup Tool</h2>
            <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-gray-700 text-sm mb-3"><strong>What it does:</strong> Fetches DNS records for any domain.</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
                    <div class="bg-white p-3 rounded border">
                        <span class="font-semibold">A Records</span>
                        <p class="text-gray-500 text-xs">Maps domain to IPv4 address</p>
                    </div>
                    <div class="bg-white p-3 rounded border">
                        <span class="font-semibold">AAAA Records</span>
                        <p class="text-gray-500 text-xs">Maps domain to IPv6 address</p>
                    </div>
                    <div class="bg-white p-3 rounded border">
                        <span class="font-semibold">MX Records</span>
                        <p class="text-gray-500 text-xs">Mail servers for the domain</p>
                    </div>
                    <div class="bg-white p-3 rounded border">
                        <span class="font-semibold">CNAME Records</span>
                        <p class="text-gray-500 text-xs">Alias that points to another domain</p>
                    </div>
                </div>
                <p class="text-sm text-gray-600 mt-3"><strong>How to use:</strong> Enter a domain (e.g., google.com) and click "Lookup DNS".</p>
            </div>
        </div>

        
        <div id="whois" class="mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Whois Lookup</h2>
            <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-gray-700 text-sm mb-3"><strong>What it does:</strong> Finds domain registration details.</p>
                <ul class="list-disc list-inside text-sm text-gray-600 space-y-1">
                    <li>Domain registrar and WHOIS server</li>
                    <li>Registration and expiry dates</li>
                    <li>Name servers</li>
                    <li>Domain status</li>
                    <li>Registrant and contact information</li>
                </ul>
                <p class="text-sm text-gray-600 mt-3"><strong>How to use:</strong> Enter a domain and click "Lookup Whois".</p>
                <div class="mt-2 p-2 bg-blue-50 border-l-4 border-blue-400 text-blue-700 text-xs rounded">
                     Tip: Some domains use privacy protection to hide registrant details.
                </div>
            </div>
        </div>

        
        <div id="ip" class="mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-2"> IP Reputation Checker</h2>
            <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-gray-700 text-sm mb-3"><strong>What it does:</strong> Checks if an IP address has malicious activity.</p>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-2 text-sm">
                    <div class="bg-white p-3 rounded border text-center">
                        <span class="font-semibold text-red-600">High Risk</span>
                        <p class="text-gray-500 text-xs">80-100% Confidence</p>
                    </div>
                    <div class="bg-white p-3 rounded border text-center">
                        <span class="font-semibold text-yellow-600">Medium Risk</span>
                        <p class="text-gray-500 text-xs">50-79% Confidence</p>
                    </div>
                    <div class="bg-white p-3 rounded border text-center">
                        <span class="font-semibold text-green-600">Safe</span>
                        <p class="text-gray-500 text-xs">0-20% Confidence</p>
                    </div>
                </div>
                <p class="text-sm text-gray-600 mt-3"><strong>How to use:</strong> Enter an IP address and click "Check IP".</p>
            </div>
        </div>

       
        <div id="encoder" class="mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-2"> Base64 & URL Encoder/Decoder</h2>
            <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-gray-700 text-sm mb-3"><strong>What it does:</strong> Encodes and decodes text using Base64 and URL encoding.</p>
               
                <div class="space-y-2">
                    <div class="bg-white p-3 rounded border">
                        <span class="font-semibold text-blue-600">Base64 Encode</span>
                        <p class="text-gray-500 text-xs">Converts text to Base64 format (e.g., "Hello" → "SGVsbG8=")</p>
                    </div>
                    <div class="bg-white p-3 rounded border">
                        <span class="font-semibold text-blue-600">Base64 Decode</span>
                        <p class="text-gray-500 text-xs">Converts Base64 back to text (e.g., "SGVsbG8=" → "Hello")</p>
                    </div>
                    <div class="bg-white p-3 rounded border">
                        <span class="font-semibold text-green-600">URL Encode</span>
                        <p class="text-gray-500 text-xs">Makes text URL-safe (e.g., "Hello World" → "Hello%20World")</p>
                    </div>
                    <div class="bg-white p-3 rounded border">
                        <span class="font-semibold text-green-600">URL Decode</span>
                        <p class="text-gray-500 text-xs">Converts URL-encoded text back (e.g., "Hello%20World" → "Hello World")</p>
                    </div>
                </div>

                <p class="text-sm text-gray-600 mt-3"><strong>How to use:</strong> Enter text, select an operation, and click "Process".</p>
               
                <div class="mt-2 p-2 bg-blue-50 border-l-4 border-blue-400 text-blue-700 text-xs rounded">
                     <strong>Why this matters:</strong> Base64 is used in JWT tokens, email attachments, and API authentication. URL encoding is essential for safe web requests.
                </div>
                <div class="mt-2 p-2 bg-yellow-50 border-l-4 border-yellow-400 text-yellow-700 text-xs rounded">
                     <strong>Note:</strong> Encoding is NOT encryption. It only transforms data for compatibility, not security.
                </div>
            </div>
        </div>

      
        <div id="hash" class="mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-2"> Hash Tool</h2>
            <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-gray-700 text-sm mb-3"><strong>What it does:</strong> Generates and identifies cryptographic hashes.</p>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-sm">
                    <div class="bg-white p-3 rounded border text-center">
                        <span class="font-semibold text-blue-600">MD5</span>
                        <p class="text-gray-500 text-xs">32 characters</p>
                    </div>
                    <div class="bg-white p-3 rounded border text-center">
                        <span class="font-semibold text-green-600">SHA1</span>
                        <p class="text-gray-500 text-xs">40 characters</p>
                    </div>
                    <div class="bg-white p-3 rounded border text-center">
                        <span class="font-semibold text-orange-600">SHA256</span>
                        <p class="text-gray-500 text-xs">64 characters</p>
                    </div>
                    <div class="bg-white p-3 rounded border text-center">
                        <span class="font-semibold text-red-600">SHA512</span>
                        <p class="text-gray-500 text-xs">128 characters</p>
                    </div>
                </div>
                <p class="text-sm text-gray-600 mt-3"><strong>How to use:</strong> Enter text to generate a hash, or paste a hash to identify its type.</p>
                <div class="mt-2 p-2 bg-blue-50 border-l-4 border-blue-400 text-blue-700 text-xs rounded">
                     Hashes are used for password storage, file integrity, and digital signatures.
                </div>
            </div>
        </div>

       
        <div id="metadata" class="mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-2"> Metadata Extractor</h2>
            <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-gray-700 text-sm mb-3"><strong>What it does:</strong> Extracts hidden metadata from files.</p>
                <ul class="list-disc list-inside text-sm text-gray-600 space-y-1">
                    <li>EXIF data from images (GPS, camera, date)</li>
                    <li>Author and company from documents</li>
                    <li>Artist and album from audio files</li>
                    <li>Creation and modification dates</li>
                </ul>
                <div class="mt-2 p-2 bg-red-50 border-l-4 border-red-400 text-red-700 text-xs rounded">
                     Sensitive data found? Always remove metadata before sharing files!
                </div>
                <p class="text-sm text-gray-600 mt-3"><strong>How to use:</strong> Upload a file and click "Extract Metadata".</p>
            </div>
        </div>

        
        <div id="tips" class="mt-8 p-6 bg-blue-50 rounded-xl border border-blue-200">
            <h3 class="font-bold text-blue-800 mb-2">🛡️ Security Tips</h3>
            <ul class="list-disc list-inside text-sm text-blue-700 space-y-1">
                <li>Always verify domain ownership before trusting a website</li>
                <li>Use IP reputation checks before allowing access to your network</li>
                <li>Remove metadata from files before sharing them publicly</li>
                <li>Keep software updated to avoid known vulnerabilities</li>
                <li>Use strong, unique passwords for all accounts</li>
                <li>Always check suspicious links using the URL Safety Checker</li>
                <li>Base64 and URL encoding are for compatibility, not encryption</li>
            </ul>
        </div>

        <div class="mt-8 text-center">
            <a href="#" class="text-sm text-blue-600 hover:underline">
                ↑ Back to Top
            </a>
        </div>

    </div>
</div>
@endsection