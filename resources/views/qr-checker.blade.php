@extends('layouts.app')

@section('title', 'QR Code Phishing Checker')

@section('content')
<div class="container mx-auto px-4 py-10 max-w-4xl">
    <div class="bg-white rounded-2xl shadow-lg p-8">

        <!-- Header -->
        <div class="flex items-center gap-3 mb-6">
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 text-xl">
                <i class="fas fa-qrcode"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">QR Code Phishing Checker</h1>
                <p class="text-gray-500 text-sm">Upload a QR code image or paste its content to check if the link is safe.</p>
            </div>
        </div>

        <!-- Info Box -->
        <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
            <p class="text-blue-700 text-sm">
                <strong>ℹ️ How it works:</strong> You can either upload a QR code image (JPG, PNG) or paste the decoded QR code URL.
                The system will check it against VirusTotal and Google Safe Browsing databases.
            </p>
        </div>

        <!-- Two Options in One Form -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Option 1: Upload QR Image -->
            <div class="bg-gray-50 rounded-xl p-6 text-center border-2 border-dashed border-gray-300 hover:border-blue-500 transition">
                <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
                <h3 class="font-semibold text-gray-700">Upload QR Code Image</h3>
                <p class="text-xs text-gray-500 mb-3">Upload a QR code image file (JPG, PNG, BMP, GIF)</p>

                <form method="POST" action="{{ route('qr.checker.check') }}" enctype="multipart/form-data" id="uploadForm">
                    @csrf
                    <div class="mb-3">
                        <input type="file" name="qr_image" accept="image/*"
                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded-lg transition">
                        📤 Upload & Check
                    </button>
                </form>
            </div>

            <!-- Option 2: Paste QR Content -->
            <div class="bg-gray-50 rounded-xl p-6 text-center border-2 border-dashed border-gray-300 hover:border-purple-500 transition">
                <i class="fas fa-paste text-4xl text-gray-400 mb-3"></i>
                <h3 class="font-semibold text-gray-700">Paste QR Code Content</h3>
                <p class="text-xs text-gray-500 mb-3">Paste the decoded QR code text (URL)</p>

                <form method="POST" action="{{ route('qr.checker.check') }}" id="pasteForm">
                    @csrf
                    <div class="mb-3">
                        <input type="text" name="qr_data" placeholder="https://example.com" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:outline-none">
                    </div>
                    <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 rounded-lg transition">
                        🔍 Check URL
                    </button>
                </form>
            </div>

        </div>

        <!-- Errors -->
        @if ($errors->any())
            <div class="mt-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg">
                @foreach ($errors->all() as $error)
                    <p class="text-red-600 text-sm"><i class="fas fa-exclamation-circle mr-2"></i>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        @if(isset($error))
            <div class="mt-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg">
                <p class="text-red-600 text-sm"><i class="fas fa-exclamation-circle mr-2"></i>{{ $error }}</p>
            </div>
        @endif

        <!-- Results -->
        @if(isset($result))
    <div class="mt-8 p-6 bg-gray-50 rounded-xl border border-gray-200">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h3 class="font-bold text-lg text-gray-800">📋 Safety Report</h3>
                <p class="text-sm text-gray-500">
                    Checked: {{ $result['checked_at'] ?? now()->format('Y-m-d H:i:s') }}
                    @if(isset($input_type))
                        <span class="ml-2 text-xs px-2 py-1 rounded-full
                            @if($input_type == 'image') bg-blue-100 text-blue-600
                            @else bg-gray-100 text-gray-500
                            @endif">
                            @if($input_type == 'image') 📷 Uploaded QR @else 📝 Pasted Text @endif
                        </span>
                    @endif
                    <!-- ✅ ADD THIS DECODING METHOD BADGE -->
                    @if(isset($decoding_method))
                        <span class="ml-2 text-xs px-2 py-1 rounded-full
                            @if($decoding_method == 'gd_local') bg-green-100 text-green-600
                            @elseif($decoding_method == 'api_fallback') bg-yellow-100 text-yellow-600
                            @else bg-gray-100 text-gray-500
                            @endif">
                            @if($decoding_method == 'gd_local') ✅ Decoded Locally (GD)
                            @elseif($decoding_method == 'api_fallback') 🌐 Decoded via API
                            @else 📝 Pasted Text
                            @endif
                        </span>
                    @endif
                </p>
            </div>
        </div>

                <!-- Decoded Text -->
                @if(isset($decoded_text))
                    <div class="bg-white p-4 rounded-lg border mb-4">
                        <div class="flex flex-wrap justify-between">
                            <span class="text-gray-600 text-sm">Decoded QR Content:</span>
                            <span class="font-mono text-sm break-all text-blue-600">{{ $decoded_text }}</span>
                        </div>
                    </div>
                @endif

                <!-- URL -->
                <div class="bg-white p-4 rounded-lg border mb-4">
                    <div class="flex flex-wrap justify-between">
                        <span class="text-gray-600 text-sm">Normalized URL:</span>
                        <span class="font-mono text-sm break-all text-blue-600">{{ $result['normalized_url'] ?? 'N/A' }}</span>
                    </div>
                </div>

                <!-- Risk Level -->
                <div class="flex justify-between items-center p-4 rounded-lg mb-4
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

                <!-- VirusTotal -->
                @if(isset($result['virustotal']))
                    <div class="bg-white p-4 rounded-lg border mb-3">
                        <h4 class="font-bold text-gray-700 mb-2">🦠 VirusTotal</h4>
                        <div class="space-y-1 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Malicious:</span>
                                <span class="font-bold text-red-600">{{ $result['virustotal']['malicious'] ?? 0 }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Suspicious:</span>
                                <span class="font-bold text-yellow-600">{{ $result['virustotal']['suspicious'] ?? 0 }}</span>
                            </div>
                            <div class="flex justify-between">
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
                            @if(!empty($result['virustotal']['detections']))
                                <div class="mt-2 p-2 bg-red-50 rounded">
                                    <p class="font-bold text-red-700 text-xs">Detected Threats:</p>
                                    @foreach($result['virustotal']['detections'] as $detect)
                                        <p class="text-red-600 text-xs">• {{ $detect['engine'] }}: {{ $detect['result'] }}</p>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Google Safe Browsing -->
                @if(isset($result['google_safe_browsing']))
                    <div class="bg-white p-4 rounded-lg border mb-3">
                        <h4 class="font-bold text-gray-700 mb-2">🔒 Google Safe Browsing</h4>
                        <div class="space-y-1 text-sm">
                            <div class="flex justify-between">
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
                                <div class="text-red-600 text-sm">{{ $result['google_safe_browsing']['message'] }}</div>
                            @endif
                            @if(!empty($result['google_safe_browsing']['threats']))
                                <div class="mt-2 p-2 bg-red-50 rounded">
                                    <p class="font-bold text-red-700 text-xs">Threats:</p>
                                    @foreach($result['google_safe_browsing']['threats'] as $threat)
                                        <p class="text-red-600 text-xs">• {{ $threat['threat_type'] ?? 'Unknown' }}</p>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Summary Messages -->
                @if(!empty($result['messages']))
                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                        <p class="font-bold text-blue-700 text-sm mb-2">📋 Summary</p>
                        @foreach($result['messages'] as $message)
                            <p class="text-blue-600 text-sm">• {{ $message }}</p>
                        @endforeach
                    </div>
                @endif

                <!-- Recommendation -->
                <div class="mt-4 p-4 rounded-lg border
                    @if($result['risk_color'] == 'red') bg-red-50 border-red-200
                    @elseif($result['risk_color'] == 'yellow') bg-yellow-50 border-yellow-200
                    @else bg-green-50 border-green-200
                    @endif">
                    <p class="text-sm">
                        <strong>Recommendation:</strong>
                        @if($result['risk_color'] == 'red')
                            <span class="text-red-700">❌ DO NOT scan this QR code. It leads to a malicious website.</span>
                        @elseif($result['risk_color'] == 'yellow')
                            <span class="text-yellow-700">⚠️ Exercise caution. This QR code leads to a suspicious website.</span>
                        @else
                            <span class="text-green-700">✅ This QR code appears safe. No threats detected.</span>
                        @endif
                    </p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection