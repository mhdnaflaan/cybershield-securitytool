@extends('layouts.app')

@section('title', 'Admin - System Logs')

@section('content')
<div class="container mx-auto px-4 py-10">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800"> System Logs</h1>
            <p class="text-gray-500 text-sm mt-1">Monitor application activity and debug errors.</p>
        </div>
        <div class="flex gap-3">
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

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <!-- Log Controls -->
        <div class="p-4 border-b bg-gray-50 flex flex-wrap justify-between items-center gap-4">
            <div class="flex items-center gap-3">
                <span class="text-sm text-gray-600">Total: <strong>{{ count($logs) }}</strong> entries</span>
                <span class="text-sm text-gray-600">|</span>
                <span class="text-sm text-gray-600">Showing latest <strong>{{ count($logs) }}</strong> lines</span>
            </div>
            <div class="flex gap-2">
                <button onclick="window.location.reload()" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm transition">
                    🔄 Refresh
                </button>
                <button onclick="clearLogs()" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm transition">
                    🗑️ Clear
                </button>
            </div>
        </div>

        <!-- Log Entries -->
        <div class="overflow-x-auto max-h-[600px] overflow-y-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-100 sticky top-0">
                    <tr>
                        <th class="px-4 py-2 text-left w-16">#</th>
                        <th class="px-4 py-2 text-left w-48">Timestamp</th>
                        <th class="px-4 py-2 text-left w-24">Level</th>
                        <th class="px-4 py-2 text-left">Message</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $index => $log)
                        @php
                            // Parse log entry
                            $level = 'info';
                            $message = $log;
                            $timestamp = '';

                            // Extract level if present
                            if (preg_match('/\[(.*?)\]/', $log, $matches)) {
                                $timestamp = $matches[1] ?? '';
                            }

                            // Check for error levels
                            if (str_contains(strtolower($log), 'error')) {
                                $level = 'error';
                            } elseif (str_contains(strtolower($log), 'warning')) {
                                $level = 'warning';
                            } elseif (str_contains(strtolower($log), 'info')) {
                                $level = 'info';
                            }
                        @endphp
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-2 text-gray-400">{{ $index + 1 }}</td>
                            <td class="px-4 py-2 text-xs text-gray-500 font-mono">
                                {{ $timestamp }}
                            </td>
                            <td class="px-4 py-2">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold
                                    @if($level == 'error') bg-red-100 text-red-600
                                    @elseif($level == 'warning') bg-yellow-100 text-yellow-600
                                    @else bg-green-100 text-green-600
                                    @endif">
                                    {{ strtoupper($level) }}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-xs font-mono break-all">
                                {{ $log }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-400">
                                <i class="fas fa-check-circle text-green-500 text-2xl block mb-2"></i>
                                No log entries found. Everything looks clean!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div class="p-4 bg-gray-50 border-t text-xs text-gray-400">
            Log file: <span class="font-mono">{{ storage_path('logs/laravel.log') }}</span>
        </div>
    </div>
</div>

<script>
    function clearLogs() {
        if (confirm('Are you sure you want to clear all logs? This action cannot be undone.')) {
            fetch('{{ route('admin.logs.clear') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            }).then(response => response.json())
              .then(data => {
                  if (data.success) {
                      window.location.reload();
                  }
              });
        }
    }
</script>
@endsection