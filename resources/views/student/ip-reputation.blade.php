@extends('layouts.app')

@section('title', 'IP Reputation Checker')

@section('content')
<div class="container mx-auto px-4 py-10 max-w-4xl">
    <div class="bg-white rounded-2xl shadow-lg p-8">

        <!-- Header -->
        <div class="flex items-center gap-3 mb-6">
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 text-xl">
                <i class="fas fa-network-wired"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-800"> IP Reputation Checker</h1>
                <p class="text-gray-500 text-sm">Check if an IP address has malicious activity using AbuseIPDB.</p>
            </div>
        </div>

        <!-- Info Box -->
        <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
            <p class="text-blue-700 text-sm">
                <strong>What is IP Reputation?</strong> IP reputation is a score that indicates how likely an IP address
                is to be malicious. It's based on reports from security researchers and automated systems.
            </p>
        </div>

        <!-- Input Form -->
        <form method="POST" action="{{ route('student.ip-reputation') }}">
            @csrf
            <div class="flex flex-col md:flex-row gap-3">
                <input type="text" name="ip" value="{{ old('ip', $ip ?? '') }}" required
                       placeholder="Enter IP address or domain (e.g., 8.8.8.8 or google.com)"
                       class="flex-1 px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <button type="submit" class="md:w-auto w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-xl transition">
                     Check IP
                </button>
            </div>
            @error('ip')
                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
            @enderror
        </form>

        <!-- Error -->
        @if(isset($error))
            <div class="mt-6 p-4 bg-red-50 border-l-4 border-red-500 rounded">
                <p class="text-red-600 text-sm">{{ $error }}</p>
            </div>
        @endif

        <!-- Results -->
        @if(isset($result))
            <div class="mt-8">
                <h3 class="font-bold text-lg text-gray-800 mb-4">
                     Reputation Report for <span class="font-mono text-blue-600">{{ $result['ip'] ?? $ip }}</span>
                </h3>

                <!-- Confidence Score -->
                <div class="flex items-center justify-between p-4 rounded-lg mb-4
                    @if($result['confidence_level']['color'] == 'red') bg-red-50 border-red-400 border-2
                    @elseif($result['confidence_level']['color'] == 'yellow') bg-yellow-50 border-yellow-400 border-2
                    @elseif($result['confidence_level']['color'] == 'orange') bg-orange-50 border-orange-400 border-2
                    @else bg-green-50 border-green-400 border-2
                    @endif">
                    <div>
                        <span class="font-bold text-gray-700">Abuse Confidence Score:</span>
                        <span class="text-2xl font-bold
                            @if($result['confidence_level']['color'] == 'red') text-red-600
                            @elseif($result['confidence_level']['color'] == 'yellow') text-yellow-600
                            @elseif($result['confidence_level']['color'] == 'orange') text-orange-600
                            @else text-green-600
                            @endif">
                            {{ $result['abuse_confidence_score'] ?? 0 }}%
                        </span>
                    </div>
                    <div>
                        <span class="text-xl font-bold
                            @if($result['confidence_level']['color'] == 'red') text-red-600
                            @elseif($result['confidence_level']['color'] == 'yellow') text-yellow-600
                            @elseif($result['confidence_level']['color'] == 'orange') text-orange-600
                            @else text-green-600
                            @endif">
                            {{ $result['confidence_level']['label'] }}
                        </span>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="mb-4">
                    <div class="w-full bg-gray-300 rounded-full h-2.5">
                        <div class="h-2.5 rounded-full
                            @if($result['abuse_confidence_score'] >= 80) bg-red-600
                            @elseif($result['abuse_confidence_score'] >= 50) bg-yellow-500
                            @elseif($result['abuse_confidence_score'] >= 20) bg-orange-500
                            @else bg-green-500
                            @endif"
                            style="width: {{ $result['abuse_confidence_score'] ?? 0 }}%">
                        </div>
                    </div>
                </div>

                <!-- Recommendation -->
                <div class="p-3 rounded-lg mb-4
                    @if($result['confidence_level']['color'] == 'red') bg-red-50 border border-red-200
                    @elseif($result['confidence_level']['color'] == 'yellow') bg-yellow-50 border border-yellow-200
                    @elseif($result['confidence_level']['color'] == 'orange') bg-orange-50 border border-orange-200
                    @else bg-green-50 border border-green-200
                    @endif">
                    <p class="text-sm">{{ $result['recommendation'] }}</p>
                </div>

                <!-- Details Table -->
                <div class="bg-white rounded-lg overflow-hidden border border-gray-200">
                    <table class="w-full text-sm">
                        <tbody>
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="px-4 py-3 font-semibold text-gray-600 w-1/3">IP Address</td>
                                <td class="px-4 py-3 font-mono">{{ $result['ip'] ?? 'N/A' }}</td>
                            </tr>
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="px-4 py-3 font-semibold text-gray-600">Country</td>
                                <td class="px-4 py-3">{{ $result['country_name'] ?? 'N/A' }} ({{ $result['country_code'] ?? 'N/A' }})</td>
                            </tr>
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="px-4 py-3 font-semibold text-gray-600">ISP</td>
                                <td class="px-4 py-3">{{ $result['isp'] ?? 'N/A' }}</td>
                            </tr>
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="px-4 py-3 font-semibold text-gray-600">Usage Type</td>
                                <td class="px-4 py-3">{{ $result['usage_type'] ?? 'Unknown' }}</td>
                            </tr>
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="px-4 py-3 font-semibold text-gray-600">Total Reports</td>
                                <td class="px-4 py-3">{{ $result['total_reports'] ?? 0 }}</td>
                            </tr>
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="px-4 py-3 font-semibold text-gray-600">Distinct Users</td>
                                <td class="px-4 py-3">{{ $result['num_distinct_users'] ?? 0 }}</td>
                            </tr>
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="px-4 py-3 font-semibold text-gray-600">Last Reported</td>
                                <td class="px-4 py-3">{{ isset($result['last_reported_at']) ? \Carbon\Carbon::parse($result['last_reported_at'])->diffForHumans() : 'Never' }}</td>
                            </tr>
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="px-4 py-3 font-semibold text-gray-600">Whitelisted</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 rounded-full text-xs
                                        @if($result['is_whitelisted']) bg-green-100 text-green-600
                                        @else bg-gray-100 text-gray-500
                                        @endif">
                                        {{ $result['is_whitelisted'] ? 'Yes' : 'No' }}
                                    </span>
                                </td>
                            </tr>
                            @if(!empty($result['categories']))
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-semibold text-gray-600">Abuse Categories</td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($result['categories'] as $category)
                                            <span class="px-2 py-1 bg-red-100 text-red-600 rounded-full text-xs">{{ $category }}</span>
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <!-- Educational Note -->
                <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <p class="text-blue-700 text-sm">
                        <strong> Why this matters:</strong>
                        IP reputation helps identify malicious actors, prevent attacks, and protect networks.
                        A high confidence score indicates that multiple sources have reported this IP for abuse.
                    </p>
                </div>
            </div>
        @elseif(isset($ip) && !isset($error))
            <div class="mt-6 p-4 bg-gray-50 rounded-lg border border-gray-200 text-center">
                <p class="text-gray-500 text-sm">No data found for <span class="font-mono">{{ $ip }}</span></p>
            </div>
        @endif

    </div>
</div>
@endsection