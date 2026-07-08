@extends('layouts.app')

@section('title', 'About Us')

@section('content')
<div class="container mx-auto px-4 py-10 max-w-4xl">
    <div class="bg-white rounded-2xl shadow-lg p-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-4"> About CyberShield</h1>

        <div class="space-y-4 text-gray-600">
            <p class="text-lg font-medium text-gray-800">"Protecting Sri Lankan internet users, one click at a time."</p>

            <h3 class="font-bold text-gray-800 text-lg mt-6">Our Mission</h3>
            <p>
                CyberShield was created to provide <strong>free, simple, and accessible security tools</strong> to Sri Lankan internet users.
                We believe that everyone deserves to stay safe online, regardless of their technical background.
            </p>

            <h3 class="font-bold text-gray-800 text-lg mt-6">Who We Serve</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-blue-50 p-4 rounded-lg text-center">
                    <i class="fas fa-user-graduate text-3xl text-blue-600 mb-2"></i>
                    <h4 class="font-bold text-gray-700">Students</h4>
                    <p class="text-xs text-gray-500">Learning cybersecurity through hands-on tools</p>
                </div>
                <div class="bg-green-50 p-4 rounded-lg text-center">
                    <i class="fas fa-briefcase text-3xl text-green-600 mb-2"></i>
                    <h4 class="font-bold text-gray-700">Small Businesses</h4>
                    <p class="text-xs text-gray-500">Protecting their online presence</p>
                </div>
                <div class="bg-purple-50 p-4 rounded-lg text-center">
                    <i class="fas fa-users text-3xl text-purple-600 mb-2"></i>
                    <h4 class="font-bold text-gray-700">Regular Users</h4>
                    <p class="text-xs text-gray-500">Staying safe from phishing and scams</p>
                </div>
            </div>

            <h3 class="font-bold text-gray-800 text-lg mt-6">Our Tools</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                <div class="bg-gray-50 p-3 rounded flex items-center gap-3">
                    <span class="text-green-600">✅</span>
                    <span>Password Strength Analyzer</span>
                </div>
                <div class="bg-gray-50 p-3 rounded flex items-center gap-3">
                    <span class="text-green-600">✅</span>
                    <span>URL Safety Checker</span>
                </div>
                <div class="bg-gray-50 p-3 rounded flex items-center gap-3">
                    <span class="text-green-600">✅</span>
                    <span>SSL & Headers Checker</span>
                </div>
                <div class="bg-gray-50 p-3 rounded flex items-center gap-3">
                    <span class="text-green-600">✅</span>
                    <span>Hash Tool</span>
                </div>
                <div class="bg-gray-50 p-3 rounded flex items-center gap-3">
                    <span class="text-green-600">✅</span>
                    <span>More....</span>
                </div>
                
            </div>

            <h3 class="font-bold text-gray-800 text-lg mt-6">Why CyberShield?</h3>
            <ul class="list-disc list-inside space-y-1">
                <li> <strong>Privacy-first</strong> – Your data is encrypted and never shared</li>
                <li> <strong>Completely Free</strong> – No hidden costs or subscriptions</li>
                <li> <strong>Educational</strong> – Learn about cybersecurity while using our tools</li>
                <li><strong>Built for Sri Lanka</strong> – Addressing local threats and needs</li>
                <li> <strong>Real Security</strong> – Powered by industry-standard APIs</li>
            </ul>

            <h3 class="font-bold text-gray-800 text-lg mt-6">Our Team</h3>
            <p>
                CyberShield is a project developed by an HND IT student at the Advanced Technological Institute,
                Nawalapitiya, Sri Lanka. It was built to solve real cybersecurity challenges faced by Sri Lankan users.
            </p>

            <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                <p class="text-blue-700 text-sm text-center">
                    <strong> Contact Us:</strong>
                    <a href="mailto:info@cybershield.lk" class="hover:underline">info@cybershield</a>
                    <span class="mx-2">|</span>
                    <strong>📍 Location:</strong>  Sri Lanka
                </p>
            </div>
        </div>
    </div>
</div>
@endsection