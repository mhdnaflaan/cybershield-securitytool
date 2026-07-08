@extends('layouts.app')

@section('title', 'User Manual')

@section('content')
<div class="container mx-auto px-4 py-10 max-w-4xl">
    <div class="bg-white rounded-2xl shadow-lg p-8">

        <h1 class="text-3xl font-bold text-gray-800 mb-2"> CyberShield User Manual</h1>
        <p class="text-gray-500 mb-8">Learn how to use each security tool to protect yourself online.</p>

        
        <div class="bg-gray-50 rounded-xl p-4 mb-8">
            <h3 class="font-bold text-gray-700 mb-2">Table of Contents</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
                <a href="#password" class="text-blue-600 hover:underline">1. Password Analyzer</a>
                <a href="#url" class="text-blue-600 hover:underline">2. URL Safety Checker</a>
                <a href="#ssl" class="text-blue-600 hover:underline">3. SSL & Headers Checker</a>
                <a href="#qr" class="text-blue-600 hover:underline">4. QR Phishing Checker</a>
                <a href="#smishing" class="text-blue-600 hover:underline">5. Smishing/Scam Analyzer</a>
                <a href="#metadata" class="text-blue-600 hover:underline">6. Metadata Remover</a>
                <a href="#history" class="text-blue-600 hover:underline">7. Your History</a>
            </div>
        </div>

       
        <div id="password" class="mb-10">
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Password Analyzer</h2>
            <p class="text-gray-600 mb-3"><strong>What it does:</strong> Checks if your password is strong and secure.</p>
            <div class="bg-gray-50 rounded-xl p-4 text-sm space-y-2">
                <p><strong>How to use:</strong></p>
                <ul class="list-disc list-inside space-y-1 text-gray-600 ml-4">
                    <li>Type or paste a password in the input field</li>
                    <li>Click "Check Strength"</li>
                    <li>View results: Strength (Weak/Medium/Strong), Estimated crack time, Breach status</li>
                </ul>
                <p class="mt-2"><strong>Why it matters:</strong> Weak passwords are the #1 cause of account hacks. This tool helps you create better passwords.</p>
                <div class="mt-2 bg-blue-50 border-l-4 border-blue-400 p-3 text-blue-700">
                     <strong>Tip:</strong> Use at least 12 characters with uppercase, lowercase, numbers, and symbols.
                </div>
                <div class="mt-2 bg-red-50 border-l-4 border-red-400 p-3 text-red-700">
                    If your password appears in the breach database, <strong>change it immediately!</strong>
                </div>
            </div>
        </div>

        
        <div id="url" class="mb-10">
            <h2 class="text-2xl font-bold text-gray-800 mb-2"> URL Safety Checker</h2>
            <p class="text-gray-600 mb-3"><strong>What it does:</strong> Detects phishing and malicious websites.</p>
            <div class="bg-gray-50 rounded-xl p-4 text-sm space-y-2">
                <p><strong>How to use:</strong></p>
                <ul class="list-disc list-inside space-y-1 text-gray-600 ml-4">
                    <li>Paste a suspicious link into the input field</li>
                    <li>Click "Check Safety"</li>
                    <li>View results: Risk Level, Detection engines, Malicious/Suspicious count</li>
                </ul>
                <p class="mt-2"><strong>Why it matters:</strong> One wrong click can steal your passwords or install viruses. Always check before clicking.</p>
                <div class="mt-2 bg-yellow-50 border-l-4 border-yellow-400 p-3 text-yellow-700">
                     <strong>Warning:</strong> Never enter personal info on sites you haven't verified.
                </div>
                <div class="mt-2 bg-blue-50 border-l-4 border-blue-400 p-3 text-blue-700">
                     <strong>How it works:</strong> Uses VirusTotal (70+ antivirus engines) and Google Safe Browsing (4+ billion devices).
                </div>
            </div>
        </div>

        <!-- Tool 3: SSL Checker -->
        <div id="ssl" class="mb-10">
            <h2 class="text-2xl font-bold text-gray-800 mb-2"> SSL & Headers Checker</h2>
            <p class="text-gray-600 mb-3"><strong>What it does:</strong> Checks if a website has proper SSL security.</p>
            <div class="bg-gray-50 rounded-xl p-4 text-sm space-y-2">
                <p><strong>How to use:</strong></p>
                <ul class="list-disc list-inside space-y-1 text-gray-600 ml-4">
                    <li>Enter a domain name (e.g., google.com)</li>
                    <li>Click "Check Security"</li>
                    <li>View grade (A+ to F), SSL certificate details, and security headers</li>
                </ul>
                <p class="mt-2"><strong>Why it matters:</strong> If a site has weak or expired SSL, your data can be intercepted.</p>
                <div class="mt-2 bg-red-50 border-l-4 border-red-400 p-3 text-red-700">
                     <strong>Warning:</strong> Never enter passwords or card details on sites with "Not Secure" warning.
                </div>
                <div class="mt-2 bg-green-50 border-l-4 border-green-400 p-3 text-green-700">
                    <strong>Grade Guide:</strong> A+ / A = Excellent, B = Good, C = Moderate, D = Poor, F = Very Poor
                </div>
            </div>
        </div>

        <!-- Tool 4: Hash Tool -->
        <div id="hash" class="mb-10">
            <h2 class="text-2xl font-bold text-gray-800 mb-2"> Hash Tool</h2>
            <p class="text-gray-600 mb-3"><strong>What it does:</strong> Creates secret codes (hashes) from text or identifies unknown hashes.</p>
            <div class="bg-gray-50 rounded-xl p-4 text-sm space-y-2">
                <p><strong>How to use:</strong></p>
                <ul class="list-disc list-inside space-y-1 text-gray-600 ml-4">
                    <li><strong>Generate:</strong> Type text → select algorithm → click Generate</li>
                    <li><strong>Identify:</strong> Paste a hash → click Identify</li>
                </ul>
                <p class="mt-2"><strong>Why it matters:</strong> Used in cybersecurity to verify file integrity and store passwords securely.</p>
                <div class="mt-2 bg-purple-50 border-l-4 border-purple-400 p-3 text-purple-700">
                     <strong>Common algorithms:</strong> MD5 (32 chars), SHA1 (40 chars), SHA256 (64 chars), SHA512 (128 chars).
                </div>
            </div>
        </div>

        

       
        <div id="smishing" class="mb-10">
            <h2 class="text-2xl font-bold text-gray-800 mb-2"> Smishing/Scam Analyzer</h2>
            <p class="text-gray-600 mb-3"><strong>What it does:</strong> Analyzes suspicious messages for scam indicators.</p>
            <div class="bg-gray-50 rounded-xl p-4 text-sm space-y-2">
                <p><strong>How to use:</strong></p>
                <ul class="list-disc list-inside space-y-1 text-gray-600 ml-4">
                    <li>Paste a suspicious SMS, WhatsApp, or email message</li>
                    <li>Click "Analyze Message"</li>
                    <li>View results: Risk Score, Detected issues, Recommendations</li>
                </ul>
                <p class="mt-2"><strong>Why it matters:</strong> Scammers use urgency, fake links, and impersonation to steal your information.</p>
                <div class="mt-2 bg-red-50 border-l-4 border-red-400 p-3 text-red-700">
                     <strong>Red Flags:</strong> Urgent language, suspicious links, requests for personal info, spelling errors.
                </div>
                <div class="mt-2 bg-blue-50 border-l-4 border-blue-400 p-3 text-blue-700">
                     <strong>Tip:</strong> Legitimate companies never ask for passwords or OTPs via SMS.
                </div>
            </div>
        </div>

        
        <div id="metadata" class="mb-10">
            <h2 class="text-2xl font-bold text-gray-800 mb-2"> Metadata Remover</h2>
            <p class="text-gray-600 mb-3"><strong>What it does:</strong> Removes sensitive metadata from images.</p>
            <div class="bg-gray-50 rounded-xl p-4 text-sm space-y-2">
                <p><strong>How to use:</strong></p>
                <ul class="list-disc list-inside space-y-1 text-gray-600 ml-4">
                    <li>Upload an image (JPG, PNG, GIF, BMP, WEBP)</li>
                    <li>Click "Remove Metadata"</li>
                    <li>Download the cleaned file</li>
                </ul>
                <p class="mt-2"><strong>What it removes:</strong></p>
                <ul class="list-disc list-inside space-y-1 text-gray-600 ml-4">
                    <li>GPS coordinates (location where the photo was taken)</li>
                    <li>Camera make and model</li>
                    <li>Date and time</li>
                    <li>Software used</li>
                    <li>Artist and copyright information</li>
                </ul>
                <p class="mt-2"><strong>Why it matters:</strong> Metadata can reveal your location, device, and personal details. Always remove it before sharing photos online.</p>
                <div class="mt-2 bg-red-50 border-l-4 border-red-400 p-3 text-red-700">
                     <strong>Privacy Alert:</strong> Photos taken with smartphones often contain GPS coordinates. Remove metadata before sharing!
                </div>
            </div>
        </div>

        
        <div id="history" class="mb-10">
            <h2 class="text-2xl font-bold text-gray-800 mb-2"> Your History</h2>
            <p class="text-gray-600 mb-3"><strong>What it does:</strong> Shows all your previous security checks.</p>
            <div class="bg-gray-50 rounded-xl p-4 text-sm space-y-2">
                <p><strong>How to use:</strong></p>
                <ul class="list-disc list-inside space-y-1 text-gray-600 ml-4">
                    <li>All your scans are saved automatically</li>
                    <li>View recent activity on your dashboard</li>
                    <li>Download PDF reports for any scan</li>
                    <li>Export all reports as a single PDF</li>
                </ul>
                <p class="mt-2"><strong>Why it matters:</strong> Your history helps you track what you've checked and provides records for future reference.</p>
                <div class="mt-2 bg-green-50 border-l-4 border-green-400 p-3 text-green-700">
                     <strong>Privacy:</strong> Your history is private and only visible to you.
                </div>
            </div>
        </div>

        
        <div id="tips" class="mt-8 p-6 bg-blue-50 rounded-xl border border-blue-200">
            <h3 class="font-bold text-blue-800 mb-2"> Quick Security Tips</h3>
            <ul class="list-disc list-inside text-sm text-blue-700 space-y-1">
                <li>Always use strong, unique passwords for each account</li>
                <li>Check suspicious links before clicking</li>
                <li>Verify SSL certificates before entering personal info</li>
                <li>Remove metadata from photos before sharing online</li>
                <li>Be suspicious of urgent messages asking for personal info</li>
                <li>Scan QR codes only from trusted sources</li>
                <li>Enable two-factor authentication (2FA) wherever possible</li>
                <li>Keep your software and apps updated</li>
            </ul>
        </div>

     
        <div class="mt-8 p-6 bg-gray-50 rounded-xl border border-gray-200 text-center">
            <p class="text-gray-600 text-sm">
                <strong> Need help?</strong>
            </p>
            <p class="text-gray-500 text-sm mt-1">
                Check our <a href="{{ route('pages.faq') }}" class="text-blue-600 hover:underline">FAQ</a> or
                <a href="{{ route('pages.feedback') }}" class="text-blue-600 hover:underline">send us feedback</a>.
            </p>
        </div>

    </div>
</div>
@endsection