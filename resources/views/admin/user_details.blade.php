@extends('layouts.app')

@section('title', 'Admin - User Details')

@section('content')
<div class="container mx-auto px-4 py-10 max-w-4xl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">👤 User Details</h1>
        <a href="{{ route('admin.users') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm transition">
            ← Back to Users
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-lg p-8">
        <div class="flex items-center space-x-4 border-b pb-6 mb-6">
            <img src="https://ui-avatars.com/api/?background=1e3a8a&color=fff&name={{ urlencode($user->name) }}" class="w-20 h-20 rounded-full">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">{{ $user->name }}</h2>
                <p class="text-gray-500">{{ $user->email }}</p>
                <p class="text-sm text-gray-400 mt-1">Member since {{ $user->created_at->format('F Y') }}</p>
                <span class="text-xs px-2 py-1 rounded-full
                    @if($user->role == 'admin') bg-red-100 text-red-600
                    @else bg-blue-100 text-blue-600
                    @endif">
                    {{ ucfirst($user->role) }}
                </span>
                <span class="text-xs px-2 py-1 rounded-full ml-2
                    @if($user->is_active) bg-green-100 text-green-600
                    @else bg-red-100 text-red-600
                    @endif">
                    {{ $user->is_active ? 'Active' : 'Blocked' }}
                </span>
            </div>
        </div>

        <h3 class="text-xl font-bold text-gray-800 mb-4">📊 Scan History ({{ $user->scans->count() }})</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-2 text-left">Tool</th>
                        <th class="px-4 py-2 text-left">Input</th>
                        <th class="px-4 py-2 text-left">Result</th>
                        <th class="px-4 py-2 text-left">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($user->scans as $scan)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-2">{{ ucfirst(str_replace('_', ' ', $scan->tool_name)) }}</td>
                            <td class="px-4 py-2 font-mono text-xs">{{ Str::limit($scan->input_data, 25) }}</td>
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
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-400">No scans found for this user.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection