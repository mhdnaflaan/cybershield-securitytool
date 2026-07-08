@extends('layouts.app')

@section('title', 'Admin - Scans')

@section('content')
<div class="container mx-auto px-4 py-10">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">📋 All Scans</h1>
        <div class="flex gap-3">
            <a href="{{ route('admin.export.scans') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm transition">
                📥 Export CSV
            </a>
            <a href="{{ route('admin.dashboard') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm transition">
                ← Back
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded">
            {{ session('success') }}
        </div>
    @endif

    <!-- Filter Form -->
    <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
        <form method="GET" action="{{ route('admin.scans') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-gray-700 text-sm font-medium mb-1">Tool</label>
                <select name="tool" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">All Tools</option>
                    @foreach($tools as $tool)
                        <option value="{{ $tool }}" {{ request('tool') == $tool ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $tool)) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-medium mb-1">User</label>
                <select name="user_id" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">All Users</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-medium mb-1">Date</label>
                <input type="date" name="date" value="{{ request('date') }}" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                    🔍 Filter
                </button>
                <a href="{{ route('admin.scans') }}" class="w-full ml-2 bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-lg transition text-center">
                    Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Scans Table -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-3 text-left">ID</th>
                        <th class="px-4 py-3 text-left">User</th>
                        <th class="px-4 py-3 text-left">Tool</th>
                        <th class="px-4 py-3 text-left">Input</th>
                        <th class="px-4 py-3 text-left">Result</th>
                        <th class="px-4 py-3 text-left">Date</th>
                        <th class="px-4 py-3 text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($scans as $scan)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3">#{{ $scan->id }}</td>
                            <td class="px-4 py-3">{{ $scan->user->name ?? 'Unknown' }}</td>
                            <td class="px-4 py-3">{{ ucfirst(str_replace('_', ' ', $scan->tool_name)) }}</td>
                            <td class="px-4 py-3 font-mono text-xs">@if($scan->tool_name == 'password_analyzer')
                                  {{ maskPassword($scan->input_data) }}  
                               @else
                                  {{ Str::limit($scan->input_data, 30) }}
                                  @endif</td>
                            <td class="px-4 py-3">
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
                            <td class="px-4 py-3 text-xs text-gray-400">{{ $scan->created_at->format('Y-m-d H:i') }}</td>
                            <td class="px-4 py-3 text-center">
                                <form action="{{ route('admin.scan.delete', $scan->id) }}" method="POST" onsubmit="return confirm('Delete this scan?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline text-xs">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-400">No scans found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t">
            {{ $scans->links() }}
        </div>
    </div>
</div>
@endsection