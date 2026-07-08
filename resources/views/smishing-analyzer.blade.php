@extends('layouts.app')

@section('title', 'Smishing/Scam Analyzer')

@section('content')
<div class="container mx-auto px-4 py-10 max-w-4xl">
    <div class="bg-white rounded-2xl shadow-lg p-8">

        <!-- Header -->
        <div class="flex items-center gap-3 mb-6">
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center text-red-600 text-xl">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">📱 Smishing/Scam Analyzer</h1>
                <p class="text-gray-500 text-sm">Analyze suspicious messages for scam indicators.</p>
            </div>
        </div>

        <!-- Info Box -->
        <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
            <p class="text-blue-700 text-sm">
                <strong>🔍 How it works:</strong> Paste any suspicious SMS, WhatsApp, or email message.
                The tool will analyze it for phishing keywords, urgency tactics, suspicious links, and grammar errors.
            </p>
        </div>

        <!-- Input Form -->
        <form method="POST" action="{{ route('smishing.analyzer') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Paste the message to analyze:</label>
                <textarea name="message" rows="8" required
                          class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:outline-none"
                          placeholder="Paste your suspicious message here...">{{ old('message') }}</textarea>
                @error('message')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-3 rounded-xl transition">
                🔍 Analyze Message
            </button>
        </form>

        <!-- Results -->
        @if(isset($analysis))
            <div class="mt-8 p-6 bg-gray-50 rounded-xl border border-gray-200">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="font-bold text-lg text-gray-800">📊 Analysis Results</h3>
                        <p class="text-sm text-gray-500">Analyzed: {{ $analysis['analyzed_at'] ?? 'N/A' }}</p>
                    </div>
                    <div class="text-right">
                        <span class="text-xs text-gray-400">{{ $analysis['word_count'] ?? 0 }} words</span>
                    </div>
                </div>

                <!-- Risk Score -->
                <div class="flex justify-between items-center p-4 rounded-lg mb-4
                    @if($analysis['threat_level']['level'] == 'High Risk') bg-red-50 border-red-400 border-2
                    @elseif($analysis['threat_level']['level'] == 'Medium Risk') bg-yellow-50 border-yellow-400 border-2
                    @else bg-green-50 border-green-400 border-2
                    @endif">
                    <div>
                        <span class="font-bold text-gray-700">Risk Score:</span>
                        <span class="text-2xl font-bold
                            @if($analysis['risk_score'] >= 60) text-red-600
                            @elseif($analysis['risk_score'] >= 30) text-yellow-600
                            @else text-green-600
                            @endif">
                            {{ $analysis['risk_score'] }}/100
                        </span>
                    </div>
                    <div>
                        <span class="text-2xl font-bold
                            @if($analysis['threat_level']['level'] == 'High Risk') text-red-600
                            @elseif($analysis['threat_level']['level'] == 'Medium Risk') text-yellow-600
                            @else text-green-600
                            @endif">
                            {{ $analysis['threat_level']['icon'] }} {{ $analysis['threat_level']['level'] }}
                        </span>
                    </div>
                </div>

                <!-- Threat Level Progress Bar -->
                <div class="mb-4">
                    <div class="w-full bg-gray-300 rounded-full h-2.5">
                        <div class="h-2.5 rounded-full
                            @if($analysis['risk_score'] >= 60) bg-red-600
                            @elseif($analysis['risk_score'] >= 30) bg-yellow-500
                            @else bg-green-500
                            @endif"
                            style="width: {{ $analysis['risk_score'] }}%">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Detected Keywords -->
                    <div class="bg-white p-4 rounded-lg border">
                        <h4 class="font-bold text-gray-700 mb-2">🚨 Detected Indicators</h4>
                        @if(!empty($analysis['detected_keywords']))
                            <ul class="space-y-1 text-sm">
                                @foreach($analysis['detected_keywords'] as $warning)
                                    <li class="text-red-600">• {{ $warning }}</li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-gray-400 text-sm">No suspicious keywords detected.</p>
                        @endif
                    </div>

                    <!-- Urgency Tactics -->
                    <div class="bg-white p-4 rounded-lg border">
                        <h4 class="font-bold text-gray-700 mb-2">⏰ Urgency Tactics</h4>
                        @if(!empty($analysis['urgency_detected']))
                            <ul class="space-y-1 text-sm">
                                @foreach($analysis['urgency_detected'] as $word)
                                    <li class="text-yellow-600">• "{{ $word }}" creates urgency</li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-gray-400 text-sm">No urgency tactics detected.</p>
                        @endif
                    </div>

                    <!-- Suspicious Links -->
                    <div class="bg-white p-4 rounded-lg border">
                        <h4 class="font-bold text-gray-700 mb-2">🔗 Suspicious Links</h4>
                        @if(!empty($analysis['suspicious_links']))
                            <ul class="space-y-1 text-sm">
                                @foreach($analysis['suspicious_links'] as $link)
                                    <li class="text-red-600 break-all">• {{ $link }}</li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-gray-400 text-sm">No suspicious links detected.</p>
                        @endif
                    </div>

                    <!-- Grammar Issues -->
                    <div class="bg-white p-4 rounded-lg border">
                        <h4 class="font-bold text-gray-700 mb-2">✍️ Grammar/Spelling Issues</h4>
                        @if(!empty($analysis['grammar_issues']))
                            <ul class="space-y-1 text-sm">
                                @foreach($analysis['grammar_issues'] as $issue)
                                    <li class="text-yellow-600">• {{ $issue }}</li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-gray-400 text-sm">No grammar issues detected.</p>
                        @endif
                    </div>
                </div>

                <!-- Original Message Preview -->
                <div class="mt-4 bg-white p-4 rounded-lg border">
                    <h4 class="font-bold text-gray-700 mb-2">📝 Message Preview</h4>
                    <p class="text-gray-600 text-sm whitespace-pre-wrap">{{ Str::limit($message, 500) }}</p>
                    @if(strlen($message) > 500)
                        <p class="text-gray-400 text-xs mt-1">... (truncated, full message analyzed)</p>
                    @endif
                </div>

                <!-- Recommendations -->
                <div class="mt-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <h4 class="font-bold text-blue-700 mb-2">💡 Recommendations</h4>
                    <ul class="space-y-1 text-sm">
                        @foreach($analysis['recommendations'] as $recommendation)
                            <li class="text-blue-600">• {{ $recommendation }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection