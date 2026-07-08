@extends('layouts.app')

@section('title', 'Admin - System Health')

@section('content')
<div class="container mx-auto px-4 py-10">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">🩺 System Health</h1>
            <p class="text-gray-500 text-sm mt-1">System status and configuration overview.</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.dashboard') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm transition">
                ← Back
            </a>
            <button onclick="window.location.reload()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition">
                🔄 Refresh
            </button>
        </div>
    </div>

    <!-- Status Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm opacity-80">Overall Status</div>
                    <div class="text-2xl font-bold">Operational</div>
                </div>
                <i class="fas fa-check-circle text-4xl opacity-50"></i>
            </div>
            <div class="mt-4 text-xs opacity-70">All systems are functioning normally</div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-gray-500">PHP Version</div>
                    <div class="text-xl font-bold text-gray-800">{{ $health['php_version'] }}</div>
                </div>
                <i class="fab fa-php text-3xl text-blue-500"></i>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-gray-500">Laravel Version</div>
                    <div class="text-xl font-bold text-gray-800">{{ $health['laravel_version'] }}</div>
                </div>
                <i class="fab fa-laravel text-3xl text-purple-500"></i>
            </div>
        </div>
    </div>

    <!-- System Information Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Environment -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center gap-2 mb-4">
                <i class="fas fa-server text-blue-500"></i>
                <h3 class="font-bold text-gray-700">Environment</h3>
            </div>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between border-b pb-1">
                    <span class="text-gray-500">Environment</span>
                    <span class="font-semibold
                        @if($health['environment'] == 'production') text-green-600
                        @else text-yellow-600 @endif">
                        {{ ucfirst($health['environment']) }}
                    </span>
                </div>
                <div class="flex justify-between border-b pb-1">
                    <span class="text-gray-500">Debug Mode</span>
                    <span class="font-semibold
                        @if($health['debug_mode'] == 'OFF') text-green-600
                        @else text-red-600 @endif">
                        {{ $health['debug_mode'] }}
                    </span>
                </div>
                <div class="flex justify-between border-b pb-1">
                    <span class="text-gray-500">Timezone</span>
                    <span class="font-mono text-sm">{{ $health['timezone'] }}</span>
                </div>
            </div>
        </div>

        <!-- Database -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center gap-2 mb-4">
                <i class="fas fa-database text-green-500"></i>
                <h3 class="font-bold text-gray-700">Database</h3>
            </div>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between border-b pb-1">
                    <span class="text-gray-500">Connection</span>
                    <span class="font-semibold text-green-600">{{ $health['db_connection'] }}</span>
                </div>
                <div class="flex justify-between border-b pb-1">
                    <span class="text-gray-500">Status</span>
                    <span class="font-semibold text-green-600">✅ Connected</span>
                </div>
            </div>
        </div>

        <!-- Cache & Session -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center gap-2 mb-4">
                <i class="fas fa-bolt text-yellow-500"></i>
                <h3 class="font-bold text-gray-700">Cache & Session</h3>
            </div>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between border-b pb-1">
                    <span class="text-gray-500">Cache Driver</span>
                    <span class="font-semibold">{{ ucfirst($health['cache_driver']) }}</span>
                </div>
                <div class="flex justify-between border-b pb-1">
                    <span class="text-gray-500">Session Driver</span>
                    <span class="font-semibold">{{ ucfirst($health['session_driver']) }}</span>
                </div>
                <div class="flex justify-between border-b pb-1">
                    <span class="text-gray-500">Queue Driver</span>
                    <span class="font-semibold">{{ ucfirst($health['queue_driver']) }}</span>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center gap-2 mb-4">
                <i class="fas fa-chart-bar text-purple-500"></i>
                <h3 class="font-bold text-gray-700">Statistics</h3>
            </div>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between border-b pb-1">
                    <span class="text-gray-500">Total Users</span>
                    <span class="font-semibold">{{ $health['total_users'] }}</span>
                </div>
                <div class="flex justify-between border-b pb-1">
                    <span class="text-gray-500">Total Scans</span>
                    <span class="font-semibold">{{ $health['total_scans'] }}</span>
                </div>
                <div class="flex justify-between border-b pb-1">
                    <span class="text-gray-500">Last Scan</span>
                    <span class="font-semibold">{{ $health['last_scan'] }}</span>
                </div>
            </div>
        </div>

        <!-- PHP Extensions -->
        <div class="bg-white rounded-xl shadow-md p-6 col-span-1 md:col-span-2">
            <div class="flex items-center gap-2 mb-4">
                <i class="fas fa-puzzle-piece text-orange-500"></i>
                <h3 class="font-bold text-gray-700">PHP Extensions</h3>
            </div>
            <div class="flex flex-wrap gap-2">
                @php
                    $extensions = ['openssl', 'curl', 'mbstring', 'json', 'pdo', 'pdo_mysql', 'gd', 'xml', 'zip', 'bcmath'];
                    $loaded = [];
                    foreach ($extensions as $ext) {
                        $loaded[$ext] = extension_loaded($ext);
                    }
                @endphp
                @foreach($loaded as $ext => $loaded)
                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                        @if($loaded) bg-green-100 text-green-600
                        @else bg-red-100 text-red-600
                        @endif">
                        @if($loaded) ✅ @else ❌ @endif
                        {{ strtoupper($ext) }}
                    </span>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-8 bg-white rounded-xl shadow-md p-6">
        <h3 class="font-bold text-gray-700 mb-4">⚡ Quick Actions</h3>
        <div class="flex flex-wrap gap-4">
            <form action="{{ route('admin.cache.clear') }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg text-sm transition">
                     Clear Cache
                </button>
            </form>
            <a href="{{ route('admin.logs') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition">
                 View Logs
            </a>
            <a href="{{ route('admin.users') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm transition">
                 Manage Users
            </a>
        </div>
    </div>
</div>
@endsection