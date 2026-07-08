@extends('layouts.app')

@section('content')

<div class="bg-gradient-to-r from-blue-700 to-purple-700 text-white">
    <div class="max-w-7xl mx-auto px-4 py-12 sm:px-6 lg:px-8">
        <h1 class="text-3xl md:text-4xl font-bold">Welcome to CyberShield</h1>
        <p class="text-blue-100 mt-2">Your all-in-one security toolkit for Sri Lankan internet users.</p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 py-10 sm:px-6 lg:px-8">

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- URL Checker -->
        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition p-6 border border-gray-100">
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-link text-blue-600 text-xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800">URL Safety Checker</h3>
            <p class="text-gray-500 text-sm mt-2">Detect phishing links, expand short URLs, check domain age.</p>
            <a href="{{ route('url.checker') }}" class="mt-4 inline-block text-blue-600 font-medium hover:underline">Check URL →</a>
        </div>

        <!-- Password Analyzer -->
        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition p-6 border border-gray-100">
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-key text-green-600 text-xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800">Password Analyzer</h3>
            <p class="text-gray-500 text-sm mt-2">Test password strength, crack time & breach check.</p>
            <a href="{{ route('password.checker') }}" class="mt-4 inline-block text-green-600 font-medium hover:underline">Analyze →</a>
        </div>

        {{-- <!-- Hash Tool -->
        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition p-6 border border-gray-100">
            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-hashtag text-purple-600 text-xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800">Hash Tool</h3>
            <p class="text-gray-500 text-sm mt-2">Generate or identify MD5, SHA1, SHA256 hashes.</p>
            <a href="{{ route('hash.tool') }}" class="mt-4 inline-block text-purple-600 font-medium hover:underline">Try Hash →</a>
        </div> --}}

        <!-- SSL Checker -->
        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition p-6 border border-gray-100">
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-lock text-red-600 text-xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800">SSL & Headers</h3>
            <p class="text-gray-500 text-sm mt-2">Verify SSL certificates & security headers.</p>
            <a href="{{ route('ssl.checker') }}" class="mt-4 inline-block text-red-600 font-medium hover:underline">Check Site →</a>
        </div>

        <!-- Smishing/Scam Analyzer -->
        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border border-gray-100">
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mb-4">
             <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
            </div>
             <h3 class="text-xl font-bold text-gray-800">Smishing/Scam Analyzer</h3>
            <p class="text-gray-500 text-sm mt-2">Analyze suspicious messages for scam indicators.</p>
            <a href="{{ route('smishing.analyzer') }}" class="mt-4 inline-block text-red-600 font-medium hover:underline">Analyze →</a>
        </div>
        
        <!-- Metadata Remover -->
        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border border-gray-100">
           <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mb-4">
             <i class="fas fa-shield-alt text-green-600 text-xl"></i>
           </div>
              <h3 class="text-xl font-bold text-gray-800">Metadata Remover</h3>
              <p class="text-gray-500 text-sm mt-2">Remove sensitive metadata from images (GPS, author, etc.).</p>
              <a href="{{ route('metadata.remover') }}" class="mt-4 inline-block text-green-600 font-medium hover:underline">Clean File →</a>
        </div>

        <!-- QR Code Phishing Checker -->
        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border border-gray-100">
         <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mb-4">
         <i class="fas fa-qrcode text-blue-600 text-xl"></i>
        </div>
        <h3 class="text-xl font-bold text-gray-800">QR Code Phishing Checker</h3>
        <p class="text-gray-500 text-sm mt-2">Paste QR code content to check if the link is malicious.</p>
        <a href="{{ route('qr.checker') }}" class="mt-4 inline-block text-blue-600 font-medium hover:underline">Check QR →</a>
        </div>

     
    </div>

    <!-- Recent Activity -->
    <div class="mt-12 bg-white rounded-2xl shadow-md p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">📊 Recent Activity</h2>
        @if(isset($recentScans) && $recentScans->count() > 0)
            <div class="space-y-3">
                @foreach($recentScans as $scan)
                    <div class="flex justify-between items-center border-b pb-2">
                        <div>
                            <span class="text-gray-600 text-sm">
                               @if($scan->tool_name == 'password_analyzer')
                                  {{ maskPassword($scan->input_data) }}  
                               @else
                                  {{ Str::limit($scan->input_data, 30) }}
                                  @endif
                                @if($scan->tool_name == 'url_checker')
                                    🔗 Checked URL
                                @elseif($scan->tool_name == 'hash_tool')
                                    🔑 Generated hash
                                @elseif($scan->tool_name == 'ssl_checker')
                                    🛡️ Checked SSL
                                @else
                                    {{ ucfirst(str_replace('_', ' ', $scan->tool_name)) }}
                                @endif
                            </span>
                            <br>
                            <span class="text-xs text-gray-400 font-mono">
                                {{ Str::limit($scan->input_data, 30) }}
                            </span>
                            @if(isset($scan->result_data['strength']))
                                <span class="text-xs ml-2 px-2 py-0.5 rounded-full
                                    @if($scan->result_data['strength'] == 'Weak') bg-red-100 text-red-600
                                    @elseif($scan->result_data['strength'] == 'Medium') bg-yellow-100 text-yellow-600
                                    @else bg-green-100 text-green-600
                                    @endif">
                                    {{ $scan->result_data['strength'] }}
                                </span>
                            @endif
                        </div>
                        
                        <span class="text-xs text-gray-400">
                            {{ $scan->created_at->diffForHumans() }}
                        </span>
                        
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-400 text-sm">No recent checks yet. Start using the tools above to see your history here.</p>
        @endif
    </div>
               
</div>
@endsection