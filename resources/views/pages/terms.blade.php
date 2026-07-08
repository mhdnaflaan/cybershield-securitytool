@extends('layouts.app')

@section('title', 'Terms & Conditions')

@section('content')
<div class="container mx-auto px-4 py-10 max-w-4xl">
    <div class="bg-white rounded-2xl shadow-lg p-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-4"> Terms & Conditions</h1>
        <p class="text-gray-500 text-sm mb-6">Last updated: {{ date('F d, Y') }}</p>

        <div class="prose prose-sm max-w-none text-gray-600 space-y-4">
            <p>Welcome to CyberShield By using our services, you agree to the following terms and conditions.</p>

            <h3 class="font-bold text-gray-800 text-lg mt-6">1. Acceptance of Terms</h3>
            <p>By creating an account and using our tools, you agree to these Terms & Conditions. If you do not agree, please do not use our services.</p>

            <h3 class="font-bold text-gray-800 text-lg mt-6">2. Usage Guidelines</h3>
            <ul class="list-disc list-inside space-y-1">
                <li>You agree to use the tools for <strong>educational and lawful purposes only</strong></li>
                <li>You will not use the tools to scan or target systems without authorization</li>
                <li>You will not attempt to bypass security measures or exploit the platform</li>
                <li>You are responsible for the security of your own account and password</li>
            </ul>

            <h3 class="font-bold text-gray-800 text-lg mt-6">3. Disclaimer of Warranties</h3>
            <ul class="list-disc list-inside space-y-1">
                <li>All tools are provided <strong>"as is"</strong> without any warranties</li>
                <li>We do not guarantee the accuracy of results from third-party APIs</li>
                <li>You use the tools at your own risk</li>
            </ul>

            <h3 class="font-bold text-gray-800 text-lg mt-6">4. Limitation of Liability</h3>
            <p>CyberShield.lk is not liable for any damages arising from the use of our tools, including:</p>
            <ul class="list-disc list-inside space-y-1">
                <li>Loss of data</li>
                <li>Loss of revenue or profits</li>
                <li>System downtime or interruptions</li>
                <li>Any indirect or consequential damages</li>
            </ul>

            <h3 class="font-bold text-gray-800 text-lg mt-6">5. Intellectual Property</h3>
            <p>All content, code, and tools on CyberShield are the intellectual property of CyberShield You may not copy, distribute, or modify the platform without permission.</p>

            <h3 class="font-bold text-gray-800 text-lg mt-6">6. Termination</h3>
            <p>We reserve the right to terminate or suspend your account at any time for violations of these terms or misuse of the platform.</p>

            <h3 class="font-bold text-gray-800 text-lg mt-6">7. Changes to Terms</h3>
            <p>We may update these terms from time to time. You will be notified of any significant changes.</p>

            <h3 class="font-bold text-gray-800 text-lg mt-6">8. Contact Us</h3>
            <p>For any questions about these terms, please contact us at <a href="mailto:legal@cybershield.lk" class="text-blue-600 hover:underline">legal@cybershield</a></p>
        </div>
    </div>
</div>
@endsection