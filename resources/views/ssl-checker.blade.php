@extends('layouts.app')

@section('title', 'SSL & Headers Checker')

@section('content')
<div class="container mx-auto px-4 py-10 max-w-4xl">
    <div class="bg-white rounded-2xl shadow-lg p-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">🛡️ SSL & Headers Checker</h1>
        <p class="text-gray-500 mb-6">Verify SSL certificate, TLS handshake, and security headers for any domain.</p>

        <!-- Input Form -->
        <form method="POST" action="{{ route('ssl.checker.check') }}">
            @csrf
            <div class="flex flex-col md:flex-row gap-3">
                <input type="text" name="domain" value="{{ old('domain') }}" required
                       placeholder="example.com"
                       class="flex-1 px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:outline-none">
                <button type="submit" class="md:w-auto w-full bg-red-600 hover:bg-red-700 text-white font-semibold px-6 py-3 rounded-xl transition">
                    🔍 Check Security
                </button>
            </div>
            @error('domain')
                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
            @enderror
        </form>

        <!-- Results -->
        @if(isset($result))
            <div class="mt-8 p-6 bg-gray-50 rounded-xl border border-gray-200">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="font-bold text-lg text-gray-800">📋 Security Report</h3>
                        <p class="text-sm text-gray-500">Domain: <span class="font-mono">{{ $result['domain'] ?? 'N/A' }}</span></p>
                    </div>
                    <div class="text-right">
                        <span class="text-xs text-gray-400">Checked: {{ now()->format('Y-m-d H:i:s') }}</span>
                    </div>
                </div>

                <!-- Overall Grade -->
                <div class="flex justify-between items-center p-4 rounded-lg mb-6
                    @if($result['grade'] == 'A+') bg-green-100 border-green-400 border-2
                    @elseif($result['grade'] == 'A') bg-green-50 border-green-300 border-2
                    @elseif($result['grade'] == 'B') bg-blue-50 border-blue-300 border-2
                    @elseif($result['grade'] == 'C') bg-yellow-50 border-yellow-300 border-2
                    @elseif($result['grade'] == 'D') bg-orange-50 border-orange-300 border-2
                    @else bg-red-50 border-red-400 border-2
                    @endif">
                    <span class="font-bold text-gray-700">Overall Security Grade:</span>
                    <span class="text-3xl font-bold
                        @if(in_array($result['grade'], ['A+', 'A'])) text-green-600
                        @elseif($result['grade'] == 'B') text-blue-600
                        @elseif($result['grade'] == 'C') text-yellow-600
                        @elseif($result['grade'] == 'D') text-orange-600
                        @else text-red-600
                        @endif">
                        {{ $result['grade'] }}
                    </span>
                </div>

                
                <!-- TLS HANDSHAKE DETAILS -->
                
                <div class="mb-6">
                    <h4 class="font-bold text-gray-700 mb-2">🔐 TLS Handshake Details</h4>
                    <div class="bg-white p-4 rounded-lg border space-y-2 text-sm">
                        <div class="flex flex-wrap justify-between border-b pb-2">
                            <span class="text-gray-600">SSL/TLS Status:</span>
                            <span class="font-semibold
                                @if($result['has_ssl'] && $result['valid']) text-green-600
                                @else text-red-600
                                @endif">
                                @if($result['has_ssl'] && $result['valid'])
                                    Valid
                                @else
                                     Invalid / Not Found
                                @endif
                            </span>
                        </div>

                        @if(isset($result['protocol_version']))
                            <div class="flex flex-wrap justify-between border-b pb-2">
                                <span class="text-gray-600">Protocol Version:</span>
                                <span class="font-mono font-semibold text-blue-600">{{ $result['protocol_version'] }}</span>
                            </div>
                        @endif

                        @if(isset($result['cipher_suite']))
                            <div class="flex flex-wrap justify-between border-b pb-2">
                                <span class="text-gray-600">Cipher Suite:</span>
                                <span class="font-mono text-xs break-all">{{ $result['cipher_suite'] }}</span>
                            </div>
                        @endif

                        <div class="flex flex-wrap justify-between border-b pb-2">
                            <span class="text-gray-600">Subject:</span>
                            <span class="font-mono text-sm">{{ $result['subject'] ?? 'N/A' }}</span>
                        </div>

                        <div class="flex flex-wrap justify-between border-b pb-2">
                            <span class="text-gray-600">Issuer:</span>
                            <span class="font-mono text-sm">{{ $result['issuer'] ?? 'N/A' }}</span>
                        </div>

                        <div class="flex flex-wrap justify-between border-b pb-2">
                            <span class="text-gray-600">Expires:</span>
                            <span>{{ $result['expiry_date'] ?? 'N/A' }}</span>
                        </div>

                        <div class="flex flex-wrap justify-between">
                            <span class="text-gray-600">Days Left:</span>
                            <span class="font-semibold
                                @if(isset($result['days_left']) && $result['days_left'] < 7) text-red-600
                                @elseif(isset($result['days_left']) && $result['days_left'] < 30) text-yellow-600
                                @else text-green-600
                                @endif">
                                @if(isset($result['days_left']) && $result['days_left'] > 0)
                                    {{ $result['days_left'] }} days
                                @elseif(isset($result['days_left']) && $result['days_left'] == 0)
                                    Expires today!
                                @else
                                    N/A
                                @endif
                            </span>
                        </div>

                        @if(isset($result['error']))
                            <div class="mt-2 p-2 bg-red-50 rounded text-red-600 text-xs">
                                {{ $result['error'] }}
                            </div>
                        @endif
                    </div>
                </div>

                <!-- CERTIFICATE CHAIN -->
                
                @if(!empty($result['certificate_chain']))
                    <div class="mb-6">
                        <h4 class="font-bold text-gray-700 mb-2">🔗 Certificate Chain</h4>
                        <div class="bg-white p-4 rounded-lg border space-y-2 text-sm">
                            @foreach($result['certificate_chain'] as $index => $cert)
                                <div class="flex flex-wrap justify-between border-b pb-2 last:border-0">
                                    <span class="text-gray-600">
                                        @if($index == 0) 📜 Server @elseif($index == 1) 🏢 Intermediate @else 🔗 Chain @endif
                                    </span>
                                    <span class="font-mono text-xs">
                                        {{ $cert['subject'] ?? 'Unknown' }} →
                                        {{ $cert['issuer'] ?? 'Unknown' }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- ============================================ -->
                <!-- SECURITY HEADERS -->
                <!-- ============================================ -->
                <div class="mb-6">
                    <h4 class="font-bold text-gray-700 mb-2">🛡️ Security Headers</h4>
                    <div class="bg-white p-4 rounded-lg border space-y-2 text-sm">
                        @php
                            $headerChecks = [
                                'hsts' => [
                                    'label' => 'HSTS',
                                    'desc' => 'Enforces HTTPS connections',
                                    'icon' => '🔒',
                                    'recommendation' => 'Add: Strict-Transport-Security: max-age=31536000; includeSubDomains'
                                ],
                                'csp' => [
                                    'label' => 'CSP',
                                    'desc' => 'Prevents XSS attacks',
                                    'icon' => '🛡️',
                                    'recommendation' => 'Add: Content-Security-Policy: default-src \'self\''
                                ],
                                'x_frame_options' => [
                                    'label' => 'X-Frame-Options',
                                    'desc' => 'Prevents clickjacking',
                                    'icon' => '🖼️',
                                    'recommendation' => 'Add: X-Frame-Options: DENY'
                                ],
                                'x_content_type_options' => [
                                    'label' => 'X-Content-Type-Options',
                                    'desc' => 'Prevents MIME sniffing',
                                    'icon' => '📄',
                                    'recommendation' => 'Add: X-Content-Type-Options: nosniff'
                                ],
                                'referrer_policy' => [
                                    'label' => 'Referrer-Policy',
                                    'desc' => 'Controls referrer information',
                                    'icon' => '🔗',
                                    'recommendation' => 'Add: Referrer-Policy: strict-origin-when-cross-origin'
                                ],
                                'permissions_policy' => [
                                    'label' => 'Permissions-Policy',
                                    'desc' => 'Controls browser features',
                                    'icon' => '⚙️',
                                    'recommendation' => 'Add: Permissions-Policy: geolocation=(), microphone=()'
                                ],
                                'x_xss_protection' => [
                                    'label' => 'X-XSS-Protection',
                                    'desc' => 'Enables XSS filtering',
                                    'icon' => '🛡️',
                                    'recommendation' => 'Add: X-XSS-Protection: 1; mode=block'
                                ],
                            ];
                        @endphp

                        @foreach($headerChecks as $key => $info)
                            @php
                                $present = $result['headers'][$key] ?? false;
                            @endphp
                            <div class="flex flex-wrap justify-between items-center border-b pb-2 last:border-0">
                                <div class="flex items-center gap-2">
                                    <span>{{ $info['icon'] }}</span>
                                    <span class="font-medium">{{ $info['label'] }}</span>
                                    <span class="text-xs text-gray-400 hidden sm:inline">{{ $info['desc'] }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="font-semibold
                                        @if($present) text-green-600 @else text-red-600 @endif">
                                        @if($present) ✅ Present @else ❌ Missing @endif
                                    </span>
                                    @if(!$present)
                                        <button onclick="toggleRecommendation('{{ $key }}')" class="text-blue-500 hover:text-blue-700 text-xs underline">
                                            💡 Fix
                                        </button>
                                    @endif
                                </div>
                            </div>
                            @if(!$present)
                                <div id="rec-{{ $key }}" class="hidden p-2 bg-blue-50 rounded text-xs text-blue-700 border border-blue-200 mb-1">
                                    <strong>Recommendation:</strong> {{ $info['recommendation'] }}
                                </div>
                            @endif
                        @endforeach

                        @if(isset($result['headers']['error']))
                            <div class="text-yellow-600 text-sm mt-2 p-2 bg-yellow-50 rounded">
                                ⚠️ {{ $result['headers']['error'] }}
                            </div>
                        @endif
                    </div>
                </div>

                <!-- ============================================ -->
                <!-- SECURITY WARNINGS -->
                <!-- ============================================ -->
                @if(!empty($result['warnings']))
                    <div class="mb-6">
                        <h4 class="font-bold text-gray-700 mb-2">⚠️ Actionable Warnings</h4>
                        <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200 space-y-2">
                            @foreach($result['warnings'] as $warning)
                                <div class="flex items-start gap-2 text-sm text-yellow-700">
                                    <span>⚠️</span>
                                    <span>{{ $warning }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- ============================================ -->
                <!-- SUMMARY / RECOMMENDATIONS -->
                <!-- ============================================ -->
                <div class="mt-4 p-4 rounded-lg border
                    @if($result['grade'] == 'A+' || $result['grade'] == 'A') bg-green-50 border-green-200
                    @elseif($result['grade'] == 'B') bg-blue-50 border-blue-200
                    @elseif($result['grade'] == 'C') bg-yellow-50 border-yellow-200
                    @else bg-red-50 border-red-200
                    @endif">
                    <p class="text-sm">
                        <strong>Recommendation:</strong>
                        @if(in_array($result['grade'], ['A+', 'A']))
                            ✅ <span class="text-green-700">Excellent security configuration. This website is well-protected.</span>
                        @elseif($result['grade'] == 'B')
                            ✅ <span class="text-blue-700">Good security. Consider adding missing headers for better protection.</span>
                        @elseif($result['grade'] == 'C')
                            ⚠️ <span class="text-yellow-700">Moderate security. Implement missing security headers.</span>
                        @elseif($result['grade'] == 'D')
                            ⚠️ <span class="text-orange-700">Poor security. Missing critical security headers.</span>
                        @else
                            ❌ <span class="text-red-700">Very poor security. No valid SSL or missing essential security headers.</span>
                        @endif
                    </p>
                </div>

                <!-- ============================================ -->
                <!-- RAW HEADERS (Debug - Optional) -->
                <!-- ============================================ -->
                @if(isset($result['headers']['raw']) && auth()->user()->role === 'admin')
                    <details class="mt-4 p-3 bg-gray-100 rounded-lg text-xs">
                        <summary class="cursor-pointer font-medium text-gray-600">🔧 Raw Headers (Admin Only)</summary>
                        <pre class="mt-2 overflow-x-auto text-gray-500">{{ json_encode($result['headers']['raw'], JSON_PRETTY_PRINT) }}</pre>
                    </details>
                @endif

                <div class="mt-4 text-xs text-gray-400 border-t pt-4">
                    Checked at: {{ now()->format('Y-m-d H:i:s') }}
                    @if($result['cached'] ?? false)
                        <span class="ml-2 bg-blue-100 text-blue-600 px-2 py-1 rounded-full">Cached</span>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

<script>
    function toggleRecommendation(id) {
        const el = document.getElementById('rec-' + id);
        if (el) {
            el.classList.toggle('hidden');
        }
    }
</script>
@endsection