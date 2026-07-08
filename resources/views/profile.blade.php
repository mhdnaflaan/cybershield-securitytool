@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="container mx-auto px-4 py-10 max-w-4xl">
    <div class="bg-white rounded-2xl shadow-lg p-8">
        <div class="flex items-center space-x-4 border-b pb-6 mb-6">
            <img src="https://ui-avatars.com/api/?background=1e3a8a&color=fff&name={{ urlencode(auth()->user()->name) }}" class="w-20 h-20 rounded-full">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">{{ auth()->user()->name }}</h1>
                <p class="text-gray-500">{{ auth()->user()->email }}</p>
                <p class="text-sm text-gray-400 mt-1">Member since {{ auth()->user()->created_at->format('F Y') }}</p>
                <span class="text-xs bg-gray-200 px-2 py-1 rounded-full">{{ ucfirst(auth()->user()->role) }}</span>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            @php
                $stats = [
                    'Total Scans' => $scans->count(),
                    'Password Checks' => $scans->where('tool_name', 'password_analyzer')->count(),
                    'URL Checks' => $scans->where('tool_name', 'url_checker')->count(),
                    'SSL Checks' => $scans->where('tool_name', 'ssl_checker')->count(),
                ];
            @endphp
            @foreach($stats as $label => $value)
                <div class="bg-gray-50 rounded-xl p-4 text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ $value }}</div>
                    <div class="text-xs text-gray-500">{{ $label }}</div>
                </div>
            @endforeach
        </div>

        <!-- History Table -->
        <h2 class="text-xl font-bold text-gray-800 mb-4">📊 Scan History</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-2 text-left">Tool</th>
                        <th class="px-4 py-2 text-left">Input</th>
                        <th class="px-4 py-2 text-left">Result</th>
                        <th class="px-4 py-2 text-left">Date</th>
                        <th class="px-4 py-2 text-center">PDF</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($scans as $scan)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-2">{{ ucfirst(str_replace('_', ' ', $scan->tool_name)) }}</td>
                            <td class="px-4 py-2 font-mono text-xs">  @if($scan->tool_name == 'password_analyzer')
                                 {{ maskPassword($scan->input_data) }} 
                                 @else
                                 {{ Str::limit($scan->input_data, 25) }}
                                 @endif
                                </td>
                            <td class="px-4 py-2">
                                @if($scan->tool_name == 'password_analyzer')
                                    <span class="px-2 py-1 rounded-full text-xs
                                        @if($scan->result_data['strength'] == 'Weak') bg-red-100 text-red-600
                                        @elseif($scan->result_data['strength'] == 'Medium') bg-yellow-100 text-yellow-600
                                        @else bg-green-100 text-green-600
                                        @endif">
                                        {{ $scan->result_data['strength'] }}
                                    </span>
                                @elseif($scan->tool_name == 'url_checker')
                                    <span class="px-2 py-1 rounded-full text-xs
                                        @if($scan->result_data['risk_color'] == 'red') bg-red-100 text-red-600
                                        @elseif($scan->result_data['risk_color'] == 'yellow') bg-yellow-100 text-yellow-600
                                        @else bg-green-100 text-green-600
                                        @endif">
                                        {{ $scan->result_data['risk_level'] }}
                                    </span>
                                @elseif($scan->tool_name == 'ssl_checker')
                                    <span class="px-2 py-1 rounded-full text-xs
                                        @if(in_array($scan->result_data['grade'], ['A+', 'A'])) bg-green-100 text-green-600
                                        @elseif($scan->result_data['grade'] == 'B') bg-yellow-100 text-yellow-600
                                        @else bg-red-100 text-red-600
                                        @endif">
                                        {{ $scan->result_data['grade'] }}
                                    </span>
                                @else
                                    <span class="text-gray-500 text-xs">Done</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-xs text-gray-400">{{ $scan->created_at->format('Y-m-d H:i') }}</td>
                            <td class="px-4 py-2 text-center">
                                <a href="{{ route('pdf_report', $scan->id) }}" target="_blank" class="text-blue-600 hover:underline text-xs">📄</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-400">No scans yet. Start using the tools!</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6 text-center">
            <a href="{{ route('pdf_all') }}" target="_blank" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg text-sm transition">
                📄 Download All Reports (PDF)
            </a>
        </div>
    </div>
</div>
@endsection