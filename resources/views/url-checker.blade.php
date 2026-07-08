@extends('layouts.app')

@section('title', 'URL Safety Checker')

@section('content')
<div class="container mx-auto px-4 py-10 max-w-4xl">
    <div class="bg-white rounded-2xl shadow-lg p-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">🔗 URL Safety Checker</h1>
        <p class="text-gray-500 mb-6">Check if a link is phishing, malicious, or safe. Powered by VirusTotal and Google Safe Browsing.</p>

        <!-- Input Form -->
        <form method="POST" action="{{ route('url.checker.check') }}">
            @csrf
            <div class="flex flex-col md:flex-row gap-3">
                <input type="url" name="url" value="{{ old('url') }}" required
                       placeholder="https://example.com"
                       class="flex-1 px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <button type="submit" class="md:w-auto w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-xl transition">
                    🔍 Check Safety
                </button>
            </div>
            @error('url')
                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
            @enderror
        </form>

        <!-- Results -->
        @if(isset($result))
            <div class="mt-8 p-6 bg-gray-50 rounded-xl border border-gray-200">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="font-bold text-lg text-gray-800">📋 Safety Report</h3>
                        <p class="text-sm text-gray-500">Checked: {{ $result['checked_at'] ?? now()->format('Y-m-d H:i:s') }}</p>
                    </div>
                    @if($result['cached'] ?? false)
                        <span class="bg-blue-100 text-blue-600 text-xs px-3 py-1 rounded-full">Cached</span>
                    @endif
                </div>

                <!-- URL Details -->
                <div class="space-y-2 text-sm bg-white p-4 rounded-lg border mb-4">
                    <div class="flex flex-wrap justify-between border-b pb-2">
                        <span class="text-gray-600">Original URL:</span>
                        <span class="font-mono text-xs break-all">{{ $result['original_url'] ?? 'N/A' }}</span>
                    </div>
                    <div class="flex flex-wrap justify-between">
                        <span class="text-gray-600">Normalized URL:</span>
                        <span class="font-mono text-xs break-all">{{ $result['normalized_url'] ?? 'N/A' }}</span>
                    </div>
                </div>

                <!-- Risk Level -->
                <div class="flex justify-between items-center p-4 rounded-lg mb-6
                    @if($result['risk_color'] == 'red') bg-red-50 border-red-400 border-2
                    @elseif($result['risk_color'] == 'yellow') bg-yellow-50 border-yellow-400 border-2
                    @else bg-green-50 border-green-400 border-2
                    @endif">
                    <span class="font-bold text-gray-700">Risk Level:</span>
                    <span class="text-2xl font-bold
                        @if($result['risk_color'] == 'red') text-red-600
                        @elseif($result['risk_color'] == 'yellow') text-yellow-600
                        @else text-green-600
                        @endif">
                        {{ $result['risk_level'] ?? 'Unknown' }}
                    </span>
                </div>

                <!-- ============================================ -->
                <!-- VIRUSTOTAL RESULTS -->
                <!-- ============================================ -->
                <div class="mb-6">
                    <h4 class="font-bold text-gray-700 mb-2">🦠 VirusTotal Results</h4>
                    <div class="bg-white p-4 rounded-lg border space-y-2 text-sm">
                        @if(isset($result['virustotal']['error']))
                            <div class="text-yellow-600 text-sm p-2 bg-yellow-50 rounded">
                                ⚠️ {{ $result['virustotal']['error'] }}
                            </div>
                        @else
                            <div class="flex flex-wrap justify-between border-b pb-2">
                                <span class="text-gray-600">Status:</span>
                                <span class="font-semibold
                                    @if($result['virustotal']['is_safe'] ?? false) text-green-600
                                    @else text-red-600
                                    @endif">
                                    @if($result['virustotal']['is_safe'] ?? false)
                                        ✅ Safe
                                    @else
                                        ⚠️ Threat Detected
                                    @endif
                                </span>
                            </div>
                            <div class="flex flex-wrap justify-between border-b pb-2">
                                <span class="text-gray-600">Malicious:</span>
                                <span class="font-bold text-red-600">{{ $result['virustotal']['malicious'] ?? 0 }}</span>
                            </div>
                            <div class="flex flex-wrap justify-between border-b pb-2">
                                <span class="text-gray-600">Suspicious:</span>
                                <span class="font-bold text-yellow-600">{{ $result['virustotal']['suspicious'] ?? 0 }}</span>
                            </div>
                            <div class="flex flex-wrap justify-between border-b pb-2">
                                <span class="text-gray-600">Safe:</span>
                                <span class="font-bold text-green-600">{{ $result['virustotal']['harmless'] ?? 0 }}</span>
                            </div>
                            <div class="flex flex-wrap justify-between">
                                <span class="text-gray-600">Total Engines:</span>
                                <span class="font-bold">{{ $result['virustotal']['total_engines'] ?? 0 }}</span>
                            </div>

                            @if(!empty($result['virustotal']['detections']))
                                <div class="mt-2 p-3 bg-red-50 rounded-lg border border-red-200">
                                    <p class="font-bold text-red-700 text-sm">⚠️ Detected Threats:</p>
                                    <ul class="mt-1 space-y-1 text-xs">
                                        @foreach($result['virustotal']['detections'] as $detect)
                                            <li class="text-red-600">• {{ $detect['engine'] }}: {{ $detect['result'] }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if(isset($result['virustotal']['permalink']))
                                <div class="mt-2 text-center">
                                    <a href="{{ $result['virustotal']['permalink'] }}" target="_blank"
                                       class="text-blue-600 hover:underline text-xs">
                                        View full report on VirusTotal →
                                    </a>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>

                <!-- ============================================ -->
                <!-- GOOGLE SAFE BROWSING RESULTS -->
                <!-- ============================================ -->
                <div class="mb-6">
                    <h4 class="font-bold text-gray-700 mb-2">🔒 Google Safe Browsing Results</h4>
                    <div class="bg-white p-4 rounded-lg border space-y-2 text-sm">
                        @if(isset($result['google_safe_browsing']['error']))
                            <div class="text-yellow-600 text-sm p-2 bg-yellow-50 rounded">
                                ⚠️ {{ $result['google_safe_browsing']['error'] }}
                            </div>
                        @else
                            <div class="flex flex-wrap justify-between border-b pb-2">
                                <span class="text-gray-600">Status:</span>
                                <span class="font-semibold
                                    @if(isset($result['google_safe_browsing']['is_safe']) && $result['google_safe_browsing']['is_safe'])
                                        text-green-600
                                    @else
                                        text-red-600
                                    @endif">
                                    @if(isset($result['google_safe_browsing']['is_safe']) && $result['google_safe_browsing']['is_safe'])
                                        ✅ Safe
                                    @else
                                        ⚠️ Threat Detected
                                    @endif
                                </span>
                            </div>

                            @if(isset($result['google_safe_browsing']['message']))
                                <div class="flex flex-wrap justify-between">
                                    <span class="text-gray-600">Message:</span>
                                    <span class="text-red-600 font-semibold">{{ $result['google_safe_browsing']['message'] }}</span>
                                </div>
                            @endif

                            @if(!empty($result['google_safe_browsing']['threats']))
                                <div class="mt-2 p-3 bg-red-50 rounded-lg border border-red-200">
                                    <p class="font-bold text-red-700 text-sm">⚠️ Threats Detected:</p>
                                    <ul class="mt-1 space-y-1 text-xs">
                                        @foreach($result['google_safe_browsing']['threats'] as $threat)
                                            <li class="text-red-600">• {{ $threat['threat_type'] ?? 'Unknown' }}
                                                @if(isset($threat['cache_duration']))
                                                    <span class="text-gray-400">({{ $threat['cache_duration'] }})</span>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>

                <!-- ============================================ -->
                <!-- COMBINED SUMMARY -->
                <!-- ============================================ -->
                @if(!empty($result['messages']))
                    <div class="mb-6">
                        <h4 class="font-bold text-gray-700 mb-2">📋 Summary</h4>
                        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200 space-y-1">
                            @foreach($result['messages'] as $message)
                                <p class="text-blue-700 text-sm">• {{ $message }}</p>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- ============================================ -->
                <!-- RECOMMENDATION -->
                <!-- ============================================ -->
                <div class="mt-4 p-4 rounded-lg border
                    @if($result['risk_color'] == 'red') bg-red-50 border-red-200
                    @elseif($result['risk_color'] == 'yellow') bg-yellow-50 border-yellow-200
                    @else bg-green-50 border-green-200
                    @endif">
                    <p class="text-sm">
                        <strong>Recommendation:</strong>
                        @if($result['risk_color'] == 'red')
                            <span class="text-red-700">❌ DO NOT visit this URL. It has been flagged as malicious by multiple security services.</span>
                        @elseif($result['risk_color'] == 'yellow')
                            <span class="text-yellow-700">⚠️ Exercise caution. This URL has suspicious indicators. Proceed with care.</span>
                        @else
                            <span class="text-green-700">✅ This URL appears safe. No threats detected by VirusTotal or Google Safe Browsing.</span>
                        @endif
                    </p>
                </div>

                <!-- ============================================ -->
                <!-- RAW DATA (Admin Only) -->
                <!-- ============================================ -->
                @auth
                    @if(auth()->user()->role === 'admin')
                        <details class="mt-4 p-3 bg-gray-100 rounded-lg text-xs">
                            <summary class="cursor-pointer font-medium text-gray-600">🔧 Raw Data (Admin Only)</summary>
                            <pre class="mt-2 overflow-x-auto text-gray-500">{{ json_encode($result, JSON_PRETTY_PRINT) }}</pre>
                        </details>
                    @endif
                @endauth

                <div class="mt-4 text-xs text-gray-400 border-t pt-4">
                    Checked at: {{ $result['checked_at'] ?? now()->format('Y-m-d H:i:s') }}
                    @if($result['cached'] ?? false)
                        <span class="ml-2 bg-blue-100 text-blue-600 px-2 py-1 rounded-full">Cached</span>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
@endsection