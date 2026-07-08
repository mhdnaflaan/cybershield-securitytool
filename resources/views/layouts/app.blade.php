<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'CyberShield.lk') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        /* Fix navbar gap */
        nav.bg-white {
            margin-bottom: -1px;
            position: relative;
            z-index: 10;
        }
        main {
            padding-top: 0 !important;
            margin-top: 0 !important;
        }
        .flex.justify-between.h-16 {
            padding-bottom: 0 !important;
        }
        #mobile-menu {
            margin-top: 0 !important;
            padding-top: 4px !important;
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50 flex flex-col min-h-screen">
    <div class="min-h-screen flex flex-col">

        <!-- Network Status Component -->
        <x-network-status />

        <!-- Navigation Bar -->
        <nav class="bg-white border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                   
                    <!-- Logo -->
                    <div class="flex items-center">
                        <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
                            <i class="fas fa-shield-alt text-2xl text-blue-600"></i>
                            <span class="text-xl font-bold text-gray-800">CyberShield</span>
                        </a>
                    </div>

                   <!-- Desktop Navigation -->
            <div class="hidden md:flex items-center space-x-6">
              @if(auth()->check())
               @if(auth()->user()->role === 'student')
                <a href="{{ route('student.dashboard') }}" class="text-gray-600 hover:text-blue-600 transition">Dashboard</a>
             <a href="{{ route('profile') }}" class="text-gray-600 hover:text-blue-600 transition">Profile</a>
                <a href="{{ route('student.docs') }}" class="text-gray-600 hover:text-blue-600 transition">Docs</a>
               @endif
               @if(auth()->user()->role === 'user')
               <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-blue-600 transition">Dashboard</a>
                <a href="{{ route('profile') }}" class="text-gray-600 hover:text-blue-600 transition">Profile</a>
                <a href="{{ route('docs') }}" class="text-gray-600 hover:text-blue-600 transition">Docs</a>
                @endif
               @if(auth()->user()->role === 'admin')
               <a href="{{ route('admin.dashboard') }}" class="text-red-600 hover:text-red-700 transition">Admin</a>
               <a href="{{ route('profile') }}" class="text-gray-600 hover:text-blue-600 transition">Profile</a>
               @endif
               @endif
               </div>

                    <!-- Right Side: Network Status + User -->
                    <div class="hidden md:flex items-center space-x-4">
                        <!-- Network Status Indicator -->
                        <div class="flex items-center gap-2 mr-5">
                            <span id="network-dot" class="w-2.5 h-2.5 rounded-full bg-green-500 inline-block transition-all duration-300"></span>
                            <span id="network-text" class="text-xs text-gray-500">Online</span>
                        </div>

                        <!-- User Dropdown -->
                        @auth
                            <div class="relative" x-data="{ open: false }" @click.away="open = false">
                                <button @click="open = !open" class="flex items-center space-x-2 text-gray-600 hover:text-gray-800">
                                    <span>{{ Auth::user()->name }}</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <div x-show="open" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                                    <a href="{{ route('profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">Logout</button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-800">Login</a>
                            <a href="{{ route('register') }}" class="text-gray-600 hover:text-gray-800">Register</a>
                        @endauth
                    </div>

                    <!-- Mobile Controls -->
                    <div class="md:hidden flex items-center gap-4">
                        <!-- Network Status Dot (Mobile) -->
                        <span id="network-dot-mobile" class="w-2.5 h-2.5 rounded-full bg-green-500 inline-block transition-all duration-300"></span>

                        <!-- Mobile Menu Button -->
                        <button id="mobile-menu-button" class="text-gray-600 hover:text-blue-600 focus:outline-none transition" aria-label="Toggle menu">
                            <i id="menu-icon" class="fas fa-bars text-2xl"></i>
                        </button>
                    </div>

                </div>

                <!-- Mobile Menu -->
                <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-gray-100 py-4 space-y-3">
                    <a href="{{ route('home') }}" class="block px-4 py-2 text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition">
                        <i class="fas fa-home mr-2"></i> Home
                        @if(auth()->check() && auth()->user()->role === 'user')
                        <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition">
                        <i class="fas fa-chart-pie mr-2"></i> Dashboard
                    </a>
                    @endif
                    </a>
                    @if(auth()->check() && auth()->user()->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition">
                        <i class="fas fa-chart-pie mr-2"></i> Dashboard
                    </a>
                    @endif

                     @if(auth()->check() && auth()->user()->role === 'student')
                    <a href="{{ route('student.dashboard') }}" class="block px-4 py-2 text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition">
                        <i class="fas fa-chart-pie mr-2"></i> Dashboard
                    </a>
                    @endif
            
                    <a href="{{ route('profile') }}" class="block px-4 py-2 text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition">
                        <i class="fas fa-user mr-2"></i> Profile
                    </a>
                     @if(auth()->check() && auth()->user()->role === 'user')
                    <a href="{{ route('docs') }}" class="block px-4 py-2 text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition">
                        <i class="fas fa-book mr-2"></i> Docs
                    </a>
                    @endif

                     @if(auth()->check() && auth()->user()->role === 'student')
                    <a href="{{ route('student.docs') }}" class="block px-4 py-2 text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition">
                        <i class="fas fa-book mr-2"></i> Docs
                    </a>
                    @endif

                    <!-- Tools Section -->
                     @if(auth()->check() && auth()->user()->role === 'user')
                    <div class="px-4 pt-2 pb-1">
                        <p class="text-xs text-gray-400 uppercase tracking-wider">Tools</p>
                    </div>
                    <a href="{{ route('password.checker') }}" class="block px-4 py-2 text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition text-sm">
                        <i class="fas fa-key mr-2"></i> Password Analyzer
                    </a>
                    <a href="{{ route('url.checker') }}" class="block px-4 py-2 text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition text-sm">
                        <i class="fas fa-link mr-2"></i> URL Checker
                    </a>
                    <a href="{{ route('smishing.analyzer') }}" class="block px-4 py-2 text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition text-sm">
                        <i class="fas fa-hashtag mr-2"></i> Smishing Analyzer
                    </a>
                    <a href="{{ route('ssl.checker') }}" class="block px-4 py-2 text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition text-sm">
                        <i class="fas fa-lock mr-2"></i> SSL Checker
                    </a>
                    <a href="{{ route('qr.checker') }}" class="block px-4 py-2 text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition text-sm">
                        <i class="fas fa-lock mr-2"></i> Qr Checker
                    </a>
                    <a href="{{ route('metadata.remover') }}" class="block px-4 py-2 text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition text-sm">
                        <i class="fas fa-lock mr-2"></i> Metadata Remover
                    </a>
                    @endif

                    @if(auth()->check() && auth()->user()->role === 'student')
                    <div class="px-4 pt-2 pb-1">
                        <p class="text-xs text-gray-400 uppercase tracking-wider">Tools</p>
                    </div>
                    <a href="{{ route('student.dns-lookup') }}" class="block px-4 py-2 text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition text-sm">
                        <i class="fas fa-key mr-2"></i> Dns Lookup
                    </a>
                    <a href="{{ route('student.ip-reputation') }}" class="block px-4 py-2 text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition text-sm">
                        <i class="fas fa-link mr-2"></i> Ip Reputation
                    </a>
                    <a href="{{ route('hash.tool') }}" class="block px-4 py-2 text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition text-sm">
                        <i class="fas fa-hashtag mr-2"></i> Hash Tool
                    </a>
                    <a href="{{ route('student.metadata.extract') }}" class="block px-4 py-2 text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition text-sm">
                        <i class="fas fa-lock mr-2"></i> Metadata Extractor
                    </a>
                    <a href="{{ route('student.whois-lookup') }}" class="block px-4 py-2 text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition text-sm">
                        <i class="fas fa-lock mr-2"></i> Whois Lookup
                    </a>
                     <a href="{{ route('student.encoder') }}" class="block px-4 py-2 text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition text-sm">
                        <i class="fas fa-lock mr-2"></i> Encoder
                    </a> 
                    @endif

                    @if(auth()->check() && auth()->user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-red-600 hover:bg-red-50 hover:text-red-700 rounded-lg transition">
                            <i class="fas fa-shield-alt mr-2"></i> Admin Dashboard
                        </a>
                    @endif

                    <!-- Mobile Logout -->
                    <div class="border-t border-gray-100 pt-3 mt-2">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-red-600 hover:bg-red-50 hover:text-red-700 rounded-lg transition">
                                <i class="fas fa-sign-out-alt mr-2"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </nav>

        <!-- Page Content -->
        <main class="flex-grow">
            @yield('content')
        </main>

        <!-- Footer -->
<footer class="bg-gray-800 text-white">
    <div class="container mx-auto px-4 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
           
            <!-- Brand -->
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <i class="fas fa-shield-alt text-xl text-blue-400"></i>
                    <span class="text-lg font-bold">CyberShield</span></span>
                </div>
                <p class="text-gray-400 text-sm leading-relaxed">
                    Protecting Sri Lankan internet users with free security tools.
                    Built with ❤️ in Sri Lanka.
                </p>
                <div class="mt-4 flex space-x-4">
                    <a href="#" class="text-gray-400 hover:text-white transition">
                        <i class="fab fa-github text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition">
                        <i class="fab fa-linkedin text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition">
                        <i class="fab fa-twitter text-xl"></i>
                    </a>
                </div>
            </div>

            
            <div>
                <h4 class="font-bold text-white mb-3 text-sm uppercase tracking-wider">Quick Links</h4>
                <ul class="space-y-2 text-sm text-gray-400">
                    @if (auth()->check())
                    @if (auth()->user()->role ==='admin')
                    <li><a href="{{ route('admin.dashboard') }}" class="hover:text-white transition">Dashboard</a></li>
                    @endif
                    @if (auth()->user()->role ==='student')
                    <li><a href="{{ route('student.dashboard') }}" class="hover:text-white transition">Dashboard</a></li>
                    @endif
                    @if (auth()->user()->role ==='user')
                    <li><a href="{{ route('dashboard') }}" class="hover:text-white transition">Dashboard</a></li>
                    @endif
                    @endif
                    <li><a href="{{ route('profile') }}" class="hover:text-white transition">Profile</a></li>
                    <li><a href="{{ route('pages.faq') }}" class="hover:text-white transition">FAQ</a></li>
                    <li><a href="{{ route('pages.feedback') }}" class="hover:text-white transition">Feedback</a></li>
                </ul>
            </div>

            
            <div>
                <h4 class="font-bold text-white mb-3 text-sm uppercase tracking-wider">Resources</h4>
                <ul class="space-y-2 text-sm text-gray-400">
                    <li><a href="{{ route('docs') }}" class="hover:text-white transition">User Manual</a></li>
                    <li><a href="{{ route('student.docs') }}" class="hover:text-white transition">Student Docs</a></li>
                    <li><a href="#" class="hover:text-white transition">API Reference</a></li>
                </ul>
            </div>

            
            <div>
                <h4 class="font-bold text-white mb-3 text-sm uppercase tracking-wider">Legal</h4>
                <ul class="space-y-2 text-sm text-gray-400">
                    <li><a href="{{ route('pages.privacy') }}" class="hover:text-white transition">Privacy Policy</a></li>
                    <li><a href="{{ route('pages.terms') }}" class="hover:text-white transition">Terms & Conditions</a></li>
                    <li><a href="{{ route('pages.about') }}" class="hover:text-white transition">About Us</a></li>
                </ul>
                <div class="mt-4 pt-4 border-t border-gray-700">
                    <p class="text-sm text-gray-400">
                        <span class="font-medium text-white">Contact:</span>
                        <a href="mailto:info@cybershield.lk" class="hover:text-white transition">info@cybershield.lk</a>
                    </p>
                    <p class="text-xs text-gray-500 mt-1">ATI Nawalapitiya | HND IT</p>
                </div>
            </div>

        </div>

        <div class="border-t border-gray-700 mt-8 pt-6 text-center text-sm text-gray-400">
            <p>&copy; {{ date('Y') }} CyberShield – Protecting Sri Lankan internet users.</p>
            <p class="text-xs text-gray-500 mt-1">Made with ❤️ in Sri Lanka | All tools are for educational purposes only</p>
        </div>
    </div>
</footer>

    @stack('scripts')

    <!-- Network Status & Mobile Menu Script -->
    <script>
        
        function updateNetworkIndicator() {
            const dots = document.querySelectorAll('#network-dot, #network-dot-mobile');
            const text = document.getElementById('network-text');

            dots.forEach(dot => {
                if (navigator.onLine) {
                    dot.className = 'w-2.5 h-2.5 rounded-full bg-green-500 inline-block transition-all duration-300';
                } else {
                    dot.className = 'w-2.5 h-2.5 rounded-full bg-red-500 inline-block transition-all duration-300';
                }
            });

            if (text) {
                text.textContent = navigator.onLine ? 'Online' : 'Offline';
            }
        }

        window.addEventListener('online', updateNetworkIndicator);
        window.addEventListener('offline', updateNetworkIndicator);
        document.addEventListener('DOMContentLoaded', updateNetworkIndicator);

    
        document.addEventListener('DOMContentLoaded', function() {
            const menuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');
            const menuIcon = document.getElementById('menu-icon');

            if (menuButton && mobileMenu) {
                menuButton.addEventListener('click', function() {
                    mobileMenu.classList.toggle('hidden');

                    if (mobileMenu.classList.contains('hidden')) {
                        menuIcon.className = 'fas fa-bars text-2xl';
                    } else {
                        menuIcon.className = 'fas fa-times text-2xl';
                    }
                });
            }
        });

        
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenu = document.getElementById('mobile-menu');
            const links = mobileMenu ? mobileMenu.querySelectorAll('a') : [];

            links.forEach(link => {
                link.addEventListener('click', function() {
                    mobileMenu.classList.add('hidden');
                    const menuIcon = document.getElementById('menu-icon');
                    if (menuIcon) {
                        menuIcon.className = 'fas fa-bars text-2xl';
                    }
                });
            });
        });
    </script>

</body>
</html>