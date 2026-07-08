@extends('layouts.app')

@section('title', 'Student Dashboard')

@section('content')
<div class="container mx-auto px-4 py-10">

    <!-- Welcome Section -->
    <div class="bg-gradient-to-r from-purple-700 to-blue-700 text-white rounded-xl shadow-lg p-6 mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold">Welcome, {{ Auth::user()->name }}! </h1>
                <p class="text-purple-100 mt-1">Your cybersecurity learning toolkit is ready.</p>
                <div class="mt-2 flex items-center gap-3">
                    <span class="bg-purple-600 text-white px-3 py-1 rounded-lg text-xs font-semibold">
                         Student Mode
                    </span>
                    <span class="bg-blue-600 text-white px-3 py-1 rounded-lg text-xs font-semibold">
                        {{ \App\Models\Scan::where('user_id', auth()->id())->count() }} Scans Completed
                    </span>
                </div>
            </div>
            <div class="hidden md:block">
                <img src="https://ui-avatars.com/api/?background=7c3aed&color=fff&name={{ urlencode(Auth::user()->name) }}"
                     class="w-16 h-16 rounded-full border-4 border-white shadow-lg">
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl shadow-md p-4 text-center">
            <div class="text-2xl font-bold text-purple-600">{{ \App\Models\Scan::where('user_id', auth()->id())->count() }}</div>
            <div class="text-xs text-gray-500">Total Scans</div>
        </div>
        <div class="bg-white rounded-xl shadow-md p-4 text-center">
            <div class="text-2xl font-bold text-blue-600">{{ \App\Models\Scan::where('user_id', auth()->id())->where('tool_name', 'dns_lookup')->count() }}</div>
            <div class="text-xs text-gray-500">DNS Lookups</div>
        </div>
        <div class="bg-white rounded-xl shadow-md p-4 text-center">
            <div class="text-2xl font-bold text-green-600">{{ \App\Models\Scan::where('user_id', auth()->id())->where('tool_name', 'whois_lookup')->count() }}</div>
            <div class="text-xs text-gray-500">WHOIS Lookups</div>
        </div>
        <div class="bg-white rounded-xl shadow-md p-4 text-center">
            <div class="text-2xl font-bold text-red-600">{{ \App\Models\Scan::where('user_id', auth()->id())->where('tool_name', 'ip_reputation')->count() }}</div>
            <div class="text-xs text-gray-500">IP Reputation Checks</div>
        </div>
    </div>

    <!-- Tools Grid -->
    <h2 class="text-2xl font-bold text-gray-800 mb-4"> Your Learning Toolkit</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">

        <!-- Tool 1: DNS Lookup -->
        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border-t-4 border-green-500">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center text-green-600 text-xl">
                    <i class="fas fa-globe"></i>
                </div>
                <span class="text-xs bg-gray-100 text-gray-500 px-2 py-1 rounded-full">Network Recon</span>
            </div>
            <h3 class="text-lg font-bold text-gray-800">DNS Lookup Tool</h3>
            <p class="text-gray-500 text-sm mt-1">View DNS records for any domain (A, MX, CNAME, NS, TXT).</p>
            <a href="{{ route('student.dns-lookup') }}" class="mt-4 inline-block text-green-600 hover:text-green-800 font-medium hover:underline">
                Open Tool →
            </a>
        </div>

        <!-- Tool 2: Whois Lookup -->
        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border-t-4 border-purple-500">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center text-purple-600 text-xl">
                    <i class="fas fa-address-card"></i>
                </div>
                <span class="text-xs bg-gray-100 text-gray-500 px-2 py-1 rounded-full">Network Recon</span>
            </div>
            <h3 class="text-lg font-bold text-gray-800">Whois Lookup</h3>
            <p class="text-gray-500 text-sm mt-1">Find domain registration details (owner, registrar, expiry).</p>
            <a href="{{ route('student.whois-lookup') }}" class="mt-4 inline-block text-purple-600 hover:text-purple-800 font-medium hover:underline">
                Open Tool →
            </a>
        </div>

        <!-- Tool 3: IP Reputation -->
        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border-t-4 border-blue-500">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 text-xl">
                    <i class="fas fa-network-wired"></i>
                </div>
                <span class="text-xs bg-gray-100 text-gray-500 px-2 py-1 rounded-full">Threat Intel</span>
            </div>
            <h3 class="text-lg font-bold text-gray-800">IP Reputation Checker</h3>
            <p class="text-gray-500 text-sm mt-1">Check if an IP address is malicious using AbuseIPDB.</p>
            <a href="{{ route('student.ip-reputation') }}" class="mt-4 inline-block text-blue-600 hover:text-blue-800 font-medium hover:underline">
                Open Tool →
            </a>
        </div>

        <!-- Hash Tool (Reused) -->
       <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border-t-4 border-purple-500">
            <div class="flex items-center justify-between mb-3">
            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center text-purple-600 text-xl">
            <i class="fas fa-hashtag"></i>
            </div>
            </div>
            <h3 class="text-lg font-bold text-gray-800">Hash Tool</h3>
            <p class="text-gray-500 text-sm mt-1">Generate and identify cryptographic hashes (MD5, SHA1, SHA256).</p>
            <a href="{{ route('hash.tool') }}" class="mt-4 inline-block text-purple-600 hover:text-purple-800 font-medium hover:underline">
                Open Tool →
            </a>
       </div>

        <!-- Tool 5: Metadata Extractor -->
        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border-t-4 border-purple-500">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center text-purple-600 text-xl">
                    <i class="fas fa-file-alt"></i>
                </div>
                <span class="text-xs bg-gray-100 text-gray-500 px-2 py-1 rounded-full">Forensics</span>
            </div>
            <h3 class="text-lg font-bold text-gray-800">Metadata Extractor</h3>
            <p class="text-gray-500 text-sm mt-1">Extract hidden metadata from files (EXIF, document properties).</p>
            <a href="{{ route('student.metadata') }}" class="mt-4 inline-block text-purple-600 hover:text-purple-800 font-medium hover:underline">
                Open Tool →
            </a>
        </div>

            <!-- Encoder/Decoder Tool -->
        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border-t-4 border-blue-500">
            <div class="flex items-center justify-between mb-3">
        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 text-xl">
            <i class="fas fa-code"></i>
        </div>
        <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full">Essential</span>
    </div>
    <h3 class="text-lg font-bold text-gray-800">Base64 & URL Encoder</h3>
    <p class="text-gray-500 text-sm mt-1">Encode and decode text for web security and development.</p>
    <a href="{{ route('student.encoder') }}" class="mt-4 inline-block text-blue-600 hover:text-blue-800 font-medium hover:underline">
        Open Tool →
    </a>
</div>

    </div>

    <!-- Recent Activity -->
    <div class="bg-white rounded-xl shadow-md p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-800"> Recent Activity</h2>
            <a href="{{ route('profile') }}" class="text-sm text-blue-600 hover:underline">View all →</a>
        </div>
        @if(isset($recentScans) && $recentScans->count() > 0)
            <div class="space-y-3">
                @foreach($recentScans as $scan)
                    <div class="flex justify-between items-center border-b pb-2">
                        <div>
                            <span class="text-gray-600 text-sm">
                                {{ ucfirst(str_replace('_', ' ', $scan->tool_name)) }}
                                <span class="text-gray-400 text-xs ml-2">{{ Str::limit($scan->input_data, 20) }}</span>
                            </span>
                            <span class="text-xs text-gray-400 ml-2">
                                {{ $scan->created_at->diffForHumans() }}
                            </span>
                        </div>
                        <a href="{{ route('profile') }}" class="text-xs text-blue-600 hover:underline">View</a>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-400 text-sm">No recent activity. Start exploring your tools!</p>
        @endif
    </div>

    <!-- Learning Resources -->
    <div class="mt-8 bg-blue-50 rounded-xl p-6 border border-blue-200">
        <h3 class="font-bold text-blue-800 mb-2"> Learning Resources</h3>
        <a href="{{ route('student.docs') }}" class="text-sm text-blue-600 hover:underline">View all →</a>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
            <div class="bg-white rounded-lg p-3 shadow-sm">
                <span class="text-blue-600 font-medium"> DNS Lookup</span>
                <p class="text-gray-500 text-xs mt-1">Understand DNS records and how they work.</p>
            </div>
            <div class="bg-white rounded-lg p-3 shadow-sm">
                <span class="text-blue-600 font-medium"> Whois</span>
                <p class="text-gray-500 text-xs mt-1">Learn about domain registration and ownership.</p>
            </div>
            <div class="bg-white rounded-lg p-3 shadow-sm">
                <span class="text-blue-600 font-medium"> Hash</span>
                <p class="text-gray-500 text-xs mt-1">Generate and identify cryptographic hashes (MD5, SHA1, SHA256).</p>
            </div>
            <div class="bg-white rounded-lg p-3 shadow-sm">
                <span class="text-blue-600 font-medium"> IP Reputation Checker</span>
                <p class="text-gray-500 text-xs mt-1">Check if an IP address is malicious using AbuseIPDB.</p>
            </div>
            <div class="bg-white rounded-lg p-3 shadow-sm">
                <span class="text-blue-600 font-medium"> Metadata Extractor</span>
                <p class="text-gray-500 text-xs mt-1">Extract hidden metadata from files (EXIF, document properties).</p>
            </div>
            <div class="bg-white rounded-lg p-3 shadow-sm">
                <span class="text-blue-600 font-medium"> Base64 & URL Encoder</span>
                <p class="text-gray-500 text-xs mt-1">Encode and decode text for web security and development.</p>
            </div>
        </div>
    </div>
</div>
@endsection