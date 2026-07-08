@extends('layouts.app')

@section('title', 'Privacy Policy')

@section('content')
<div class="container mx-auto px-4 py-10 max-w-4xl">
    <div class="bg-white rounded-2xl shadow-lg p-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-4">Privacy Policy</h1>
        <p class="text-gray-500 text-sm mb-6">Last updated: {{ date('F d, Y') }}</p>

        <div class="prose prose-sm max-w-none text-gray-600 space-y-4">
            <p>CyberShield is committed to protecting your privacy. This Privacy Policy explains how we collect, use, and safeguard your information.</p>

            <h3 class="font-bold text-gray-800 text-lg mt-6">1. Information We Collect</h3>
            <ul class="list-disc list-inside space-y-1">
                <li><strong>Account Information:</strong> Name, email address, and password (stored securely)</li>
                <li><strong>Scan History:</strong> URLs, passwords, domains, and hashes you check (stored for your reference)</li>
                <li><strong>File Uploads:</strong> Files uploaded for metadata extraction are processed temporarily and not stored permanently</li>
            </ul>

            <h3 class="font-bold text-gray-800 text-lg mt-6">2. How We Use Your Information</h3>
            <ul class="list-disc list-inside space-y-1">
                <li>To provide and improve our security tools</li>
                <li>To save your scan history for future reference</li>
                <li>To send you important notifications (e.g., SSL expiry alerts)</li>
                <li>To analyze usage patterns and improve our services</li>
            </ul>

            <h3 class="font-bold text-gray-800 text-lg mt-6">3. Data Security</h3>
            <ul class="list-disc list-inside space-y-1">
                <li>All passwords are hashed using bcrypt</li>
                <li>No sensitive data is stored in plain text</li>
                <li>Your data is never shared with third parties without your consent</li>
            </ul>

            <h3 class="font-bold text-gray-800 text-lg mt-6">4. Third-Party APIs</h3>
            <ul class="list-disc list-inside space-y-1">
                <li>We use VirusTotal, Google Safe Browsing, and AbuseIPDB for threat detection</li>
                <li>These services may receive the URLs and IPs you check</li>
                <li>All API calls are made securely and are not logged beyond our system</li>
            </ul>

            <h3 class="font-bold text-gray-800 text-lg mt-6">5. Your Rights</h3>
            <ul class="list-disc list-inside space-y-1">
                <li>You can view and delete your scan history at any time</li>
                <li>You can request account deletion by contacting us</li>
                <li>You can export your data as PDF reports</li>
            </ul>

            <h3 class="font-bold text-gray-800 text-lg mt-6">6. Contact Us</h3>
            <p>If you have any questions about this Privacy Policy, please contact us at <a href="mailto:privacy@cybershield.lk" class="text-blue-600 hover:underline">privacy@cybershield</a></p>
        </div>
    </div>
</div>
@endsection