<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CyberShield.lk - Security Toolkit</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .gradient-hero {
            background: linear-gradient(135deg, #1e3a8a 0%, #7c3aed 100%);
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }
        .btn-hover:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(124, 58, 237, 0.4);
        }
        .tool-card {
            transition: all 0.3s ease;
        }
        .tool-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        .floating-shapes {
            position: absolute;
            border-radius: 50%;
            opacity: 0.1;
            pointer-events: none;
        }
        .section-divider {
            background: linear-gradient(90deg, transparent, #7c3aed, transparent);
            height: 1px;
        }
    </style>
</head>
<body class="bg-gray-50 font-sans antialiased">

    <!-- Navbar -->
    <nav class="bg-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <a href="/" class="flex items-center space-x-2">
                <i class="fas fa-shield-alt text-2xl text-blue-600"></i>
                <span class="text-xl font-bold text-gray-800">CyberShield</span>
            </a>
            <div class="flex items-center space-x-4">
                <a href="{{ route('login') }}" class="text-gray-600 hover:text-blue-600 transition">Login</a>
                <a href="{{ route('register') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">Get Started</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="gradient-hero text-white py-20 relative overflow-hidden">
        <div class="floating-shapes w-64 h-64 bg-white top-10 left-10"></div>
        <div class="floating-shapes w-96 h-96 bg-white bottom-20 right-10"></div>
        <div class="container mx-auto px-4 text-center relative z-10">
            <h1 class="text-4xl md:text-6xl font-bold mb-4">🛡️ CyberShield</h1>
            <p class="text-xl md:text-2xl text-blue-100 max-w-2xl mx-auto">
                Your all-in-one security toolkit for Sri Lankan internet users.
            </p>
            <p class="text-md text-blue-200 mt-2 max-w-xl mx-auto">
                Free tools to detect phishing, test passwords, verify SSL, analyze threats, and more.
            </p>
            <div class="mt-8 flex flex-wrap justify-center gap-4">
                <a href="{{ route('register') }}" class="bg-white text-blue-700 hover:bg-gray-100 font-semibold px-8 py-3 rounded-xl transition shadow-lg btn-hover">
                    🚀 Get Started Free
                </a>
                <a href="#tools" class="bg-transparent border-2 border-white hover:bg-white hover:text-blue-700 text-white font-semibold px-8 py-3 rounded-xl transition">
                    🔍 Explore Tools
                </a>
            </div>
            <div class="mt-6 flex flex-wrap justify-center gap-6 text-sm text-blue-200">
                <span><i class="fas fa-check-circle text-green-400 mr-1"></i> 100% Free</span>
                <span><i class="fas fa-lock text-green-400 mr-1"></i> Privacy First</span>
                <span><i class="fas fa-graduation-cap text-green-400 mr-1"></i> Educational</span>
            </div>
        </div>
    </section>

    <!-- Tools Section -->
    <section id="tools" class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-gray-800 text-center mb-4">🔧 Our Security Tools</h2>
            <p class="text-gray-500 text-center mb-12 max-w-xl mx-auto">Protect yourself online with our free, easy-to-use tools.</p>

            <!-- User Tools -->
            <h3 class="text-2xl font-bold text-gray-800 mb-6">👤 For Everyone</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">

                <!-- Password Analyzer -->
                <div class="tool-card bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center text-green-600 text-xl mb-4">
                        <i class="fas fa-key"></i>
                    </div>
                    <h4 class="font-bold text-gray-800">Password Analyzer</h4>
                    <p class="text-gray-500 text-sm">Check password strength, crack time, and breach status.</p>
                </div>

                <!-- URL Safety Checker -->
                <div class="tool-card bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 text-xl mb-4">
                        <i class="fas fa-link"></i>
                    </div>
                    <h4 class="font-bold text-gray-800">URL Safety Checker</h4>
                    <p class="text-gray-500 text-sm">Detect phishing and malware with VirusTotal + Google Safe Browsing.</p>
                </div>

                <!-- SSL Checker -->
                <div class="tool-card bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center text-red-600 text-xl mb-4">
                        <i class="fas fa-lock"></i>
                    </div>
                    <h4 class="font-bold text-gray-800">SSL Checker</h4>
                    <p class="text-gray-500 text-sm">Verify SSL certificates and security headers.</p>
                </div>

                

                <!-- QR Code Phishing Checker -->
                <div class="tool-card bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                    <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 text-xl mb-4">
                        <i class="fas fa-qrcode"></i>
                    </div>
                    <h4 class="font-bold text-gray-800">QR Code Checker</h4>
                    <p class="text-gray-500 text-sm">Check if QR codes lead to malicious websites.</p>
                </div>

                <!-- Smishing/Scam Analyzer -->
                <div class="tool-card bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                    <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center text-yellow-600 text-xl mb-4">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h4 class="font-bold text-gray-800">Smishing Analyzer</h4>
                    <p class="text-gray-500 text-sm">Analyze suspicious messages for scam indicators.</p>
                </div>

                <!-- Metadata Remover -->
                <div class="tool-card bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                    <div class="w-12 h-12 bg-teal-100 rounded-full flex items-center justify-center text-teal-600 text-xl mb-4">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h4 class="font-bold text-gray-800">Metadata Remover</h4>
                    <p class="text-gray-500 text-sm">Remove sensitive metadata from images.</p>
                </div>

            </div>

            <!-- Student Tools -->
            <h3 class="text-2xl font-bold text-gray-800 mb-6">🎓 For Cybersecurity Students</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">

                <!-- DNS Lookup -->
                <div class="tool-card bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 text-xl mb-4">
                        <i class="fas fa-globe"></i>
                    </div>
                    <h4 class="font-bold text-gray-800">DNS Lookup</h4>
                    <p class="text-gray-500 text-sm">View DNS records for any domain.</p>
                </div>

                <!-- Whois Lookup -->
                <div class="tool-card bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center text-purple-600 text-xl mb-4">
                        <i class="fas fa-address-card"></i>
                    </div>
                    <h4 class="font-bold text-gray-800">Whois Lookup</h4>
                    <p class="text-gray-500 text-sm">Find domain registration details.</p>
                </div>

                <!-- IP Reputation -->
                <div class="tool-card bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                    <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center text-orange-600 text-xl mb-4">
                        <i class="fas fa-network-wired"></i>
                    </div>
                    <h4 class="font-bold text-gray-800">IP Reputation</h4>
                    <p class="text-gray-500 text-sm">Check malicious IPs via AbuseIPDB.</p>
                </div>

                <!-- Encoder -->
                <div class="tool-card bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center text-red-600 text-xl mb-4">
                        <i class="fas fa-code"></i>
                    </div>
                    <h4 class="font-bold text-gray-800">Base64 & URL Encoder</h4>
                    <p class="text-gray-500 text-sm">Encode and Decode.</p>
                </div>

                <!-- Metadata Extractor -->
                <div class="tool-card bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                    <div class="w-12 h-12 bg-pink-100 rounded-full flex items-center justify-center text-pink-600 text-xl mb-4">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <h4 class="font-bold text-gray-800">Metadata Extractor</h4>
                    <p class="text-gray-500 text-sm">Extract hidden metadata from files.</p>
                </div>


                <!-- Hash Tool -->
                <div class="tool-card bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center text-purple-600 text-xl mb-4">
                        <i class="fas fa-hashtag"></i>
                    </div>
                    <h4 class="font-bold text-gray-800">Hash Tool</h4>
                    <p class="text-gray-500 text-sm">Generate and identify hashes (MD5, SHA1, SHA256).</p>
                </div>

            </div>

         

            
        </div>
    </section>

    <!-- Features / Why Us -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-gray-800 text-center mb-4">🌟 Why CyberShield?</h2>;
            <p class="text-gray-500 text-center mb-12 max-w-xl mx-auto">Built for Sri Lankan users, by a Sri Lankan developer.</p>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 max-w-5xl mx-auto">
                <div class="text-center bg-white p-6 rounded-xl shadow-md">
                    <i class="fas fa-shield-alt text-4xl text-blue-600 mb-4"></i>
                    <h4 class="font-bold text-gray-800">Free & Private</h4>
                    <p class="text-sm text-gray-500">No hidden costs. Your data stays encrypted and private.</p>
                </div>
                <div class="text-center bg-white p-6 rounded-xl shadow-md">
                    <i class="fas fa-graduation-cap text-4xl text-blue-600 mb-4"></i>
                    <h4 class="font-bold text-gray-800">Educational</h4>
                    <p class="text-sm text-gray-500">Learn cybersecurity while using practical tools.</p>
                </div>
                <div class="text-center bg-white p-6 rounded-xl shadow-md">
                    <i class="fas fa-sri-lanka text-4xl text-blue-600 mb-4"></i>
                    <h4 class="font-bold text-gray-800">Built for SL</h4>
                    <p class="text-sm text-gray-500">Designed for local threats and users.</p>
                </div>
                <div class="text-center bg-white p-6 rounded-xl shadow-md">
                    <i class="fas fa-tools text-4xl text-blue-600 mb-4"></i>
                    <h4 class="font-bold text-gray-800">12+ Tools</h4>
                    <p class="text-sm text-gray-500">Comprehensive security toolkit for all needs.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="gradient-hero text-white py-16">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold mb-4">Ready to Stay Safe Online?</h2>
            <p class="text-blue-100 mb-8 max-w-xl mx-auto">Join thousands of Sri Lankan users protecting themselves with CyberShield.lk.</p>
            <a href="{{ route('register') }}" class="bg-white text-blue-700 hover:bg-gray-100 font-semibold px-8 py-3 rounded-xl transition shadow-lg btn-hover inline-block">
                🚀 Create Free Account
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8">
        <div class="container mx-auto px-4 text-center text-sm">
            <p>&copy; {{ date('Y') }} CyberShield – Protecting Sri Lankan internet users.</p>
            <p class="text-gray-400 text-xs mt-1">Built with ❤️ in Sri Lanka </p>
        </div>
    </footer>

</body>
</html>