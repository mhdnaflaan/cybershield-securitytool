@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container mx-auto px-4 py-10">

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">🛡️ Admin Dashboard</h1>
            <p class="text-gray-500 text-sm mt-1">Monitor and manage your security platform.</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.users') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition">
                👥 Users
            </a>
            <a href="{{ route('admin.scans') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm transition">
                📋 Scans
            </a>
            <a href="{{ route('admin.feedback') }}" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg text-sm transition">
                💬 Feedback
            </a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
        <div class="bg-white rounded-xl shadow-md p-6 text-center hover:shadow-lg transition">
            <div class="text-3xl font-bold text-blue-600">{{ $totalUsers }}</div>
            <div class="text-sm text-gray-500">Total Users</div>
            <div class="text-xs text-green-600">{{ $activeUsers }} active</div>
        </div>
        <div class="bg-white rounded-xl shadow-md p-6 text-center hover:shadow-lg transition">
            <div class="text-3xl font-bold text-purple-600">{{ $totalScans }}</div>
            <div class="text-sm text-gray-500">Total Scans</div>
            <div class="text-xs text-gray-400">{{ $scansToday ?? 0 }} today</div>
        </div>
        <div class="bg-white rounded-xl shadow-md p-6 text-center hover:shadow-lg transition">
            <div class="text-3xl font-bold text-green-600">{{ $totalPasswordScans }}</div>
            <div class="text-sm text-gray-500">Password Checks</div>
        </div>
        <div class="bg-white rounded-xl shadow-md p-6 text-center hover:shadow-lg transition">
            <div class="text-3xl font-bold text-blue-600">{{ $totalUrlScans }}</div>
            <div class="text-sm text-gray-500">URL Checks</div>
        </div>
        <div class="bg-white rounded-xl shadow-md p-6 text-center hover:shadow-lg transition">
            <div class="text-3xl font-bold text-red-600">{{ $totalSslScans }}</div>
            <div class="text-sm text-gray-500">SSL Checks</div>
        </div>
        <div class="bg-white rounded-xl shadow-md p-6 text-center hover:shadow-lg transition">
            <div class="text-3xl font-bold text-yellow-600">{{ $pendingFeedbackCount }}</div>
            <div class="text-sm text-gray-500">Pending Feedback</div>
            <div class="text-xs text-gray-400">
                <a href="{{ route('admin.feedback') }}" class="text-blue-600 hover:underline">View all</a>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
       
        <!-- Scans Per Day Chart -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">📈 Scans Per Day (Last 30 Days)</h3>
            @if($scansPerDay->count() > 0)
                <canvas id="scansChart" height="200"></canvas>
            @else
                <div class="text-center py-8 text-gray-400">
                    <i class="fas fa-chart-line text-4xl mb-2 block"></i>
                    <p>No scan data in the last 30 days.</p>
                </div>
            @endif
        </div>

        <!-- Tool Usage Chart -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">📊 Tool Usage Distribution</h3>
            @if($toolUsage->count() > 0)
                <canvas id="toolsChart" height="200"></canvas>
            @else
                <div class="text-center py-8 text-gray-400">
                    <i class="fas fa-chart-pie text-4xl mb-2 block"></i>
                    <p>No tool usage data available.</p>
                </div>
            @endif
        </div>

    </div>

    <!-- Second Row: User Growth + Quick Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">

        <!-- User Growth Chart -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-md p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">📈 User Growth (Last 30 Days)</h3>
            @if($usersPerDay->count() > 0)
                <canvas id="userGrowthChart" height="150"></canvas>
            @else
                <div class="text-center py-8 text-gray-400">
                    <i class="fas fa-users text-4xl mb-2 block"></i>
                    <p>No user registration data available.</p>
                </div>
            @endif
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">⚡ Quick Actions</h3>
            <div class="space-y-3">
                <form action="{{ route('admin.ssl.monitor') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg text-sm transition flex items-center justify-center gap-2">
                        <i class="fas fa-sync-alt"></i> Run SSL Health Check
                    </button>
                </form>
               
                <form action="{{ route('admin.cache.clear') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm transition flex items-center justify-center gap-2">
                        <i class="fas fa-trash"></i> Clear System Cache
                    </button>
                </form>
               
                <a href="{{ route('admin.export.users') }}" class="w-full block text-center bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm transition flex items-center justify-center gap-2">
                    <i class="fas fa-file-csv"></i> Export Users CSV
                </a>
               
                <a href="{{ route('admin.export.scans') }}" class="w-full block text-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition flex items-center justify-center gap-2">
                    <i class="fas fa-file-csv"></i> Export Scans CSV
                </a>

                <a href="{{ route('admin.logs') }}" class="w-full block text-center bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm transition flex items-center justify-center gap-2">
                    <i class="fas fa-list-ul"></i> View System Logs
                </a>
               
                <a href="{{ route('admin.health') }}" class="w-full block text-center bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm transition flex items-center justify-center gap-2">
                    <i class="fas fa-heartbeat"></i> System Health Check
                </a>
            </div>
        </div>

    </div>

    <!-- Recent Activity + Top Users -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- Recent Scans -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-md p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-bold text-gray-800">📊 Recent Activity (All Users)</h2>
                <a href="{{ route('admin.scans') }}" class="text-sm text-blue-600 hover:underline">View all →</a>
            </div>
            <div class="space-y-3 max-h-96 overflow-y-auto">
                @forelse($recentScans as $scan)
                    <div class="flex justify-between items-center border-b pb-2 text-sm">
                        <div>
                            <span class="text-gray-600">{{ $scan->user->name ?? 'Unknown' }}</span>
                            <span class="text-xs text-gray-400 ml-2">{{ ucfirst(str_replace('_', ' ', $scan->tool_name)) }}</span>
                            <span class="text-xs text-gray-400 ml-2">@if($scan->tool_name == 'password_analyzer')
                                  {{ maskPassword($scan->input_data) }}  
                               @else
                                  {{ Str::limit($scan->input_data, 15) }}
                                  @endif</span>
                        </div>
                        <span class="text-xs text-gray-400">{{ $scan->created_at->diffForHumans() }}</span>
                    </div>
                @empty
                    <p class="text-gray-400 text-sm">No recent scans.</p>
                @endforelse
            </div>
        </div>

       <!-- Top Active Users -->
<div class="bg-white rounded-xl shadow-md p-6">
    <h2 class="text-lg font-bold text-gray-800 mb-4">🏆 Top Active Users</h2>
    @if(isset($activeUsersList) && $activeUsersList->count() > 0)
        <div class="space-y-3">
            @foreach($activeUsersList as $user)
                <div class="flex justify-between items-center border-b pb-2 text-sm">
                    <span class="text-gray-600">
                        {{ $user->name ?? 'Unknown User' }}
                        
                    </span>
                    <span class="text-xs bg-blue-100 text-blue-600 font-semibold px-2 py-1 rounded-full">
                        {{ $user->scans_count ?? 0 }} scans
                    </span>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-gray-400 text-sm">No user activity yet.</p>
    @endif
</div>

    </div>

    <!-- Recent Users -->
    <div class="mt-8 bg-white rounded-xl shadow-md p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-bold text-gray-800">👤 Recent Registrations</h2>
            <a href="{{ route('admin.users') }}" class="text-sm text-blue-600 hover:underline">View all →</a>
        </div>
        <div class="space-y-2">
            @forelse($recentUsers as $user)
                <div class="flex justify-between items-center border-b pb-2 text-sm">
                    <div class="flex items-center gap-2">
                        <span class="text-gray-600">{{ $user->name }}</span>
                        <span class="text-xs text-gray-400">{{ $user->email }}</span>
                        <span class="text-xs px-2 py-0.5 rounded-full
                            @if($user->role === 'admin') bg-red-100 text-red-600
                            @elseif($user->role === 'student') bg-purple-100 text-purple-600
                            @else bg-blue-100 text-blue-600
                            @endif">
                            {{ ucfirst($user->role) }}
                        </span>
                    </div>
                    <span class="text-xs text-gray-400">{{ $user->created_at->diffForHumans() }}</span>
                </div>
            @empty
                <p class="text-gray-400 text-sm">No recent registrations.</p>
            @endforelse
        </div>
    </div>

    <!-- System Info Footer -->
    <div class="mt-8 p-4 bg-gray-50 rounded-xl border border-gray-200 text-xs text-gray-500 grid grid-cols-2 md:grid-cols-4 gap-4">
        <div><strong>PHP Version:</strong> {{ phpversion() }}</div>
        <div><strong>Laravel:</strong> {{ app()->version() }}</div>
        <div><strong>Environment:</strong> {{ config('app.env') }}</div>
        <div><strong>Cache Driver:</strong> {{ config('cache.default') }}</div>
    </div>

</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        @if($scansPerDay->count() > 0)
        // Scans Per Day Chart
        const ctx1 = document.getElementById('scansChart').getContext('2d');
        new Chart(ctx1, {
            type: 'line',
            data: {
                labels: @json($scansPerDay->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('M d'))),
                datasets: [{
                    label: 'Scans',
                    data: @json($scansPerDay->pluck('count')),
                    borderColor: '#7c3aed',
                    backgroundColor: 'rgba(124, 58, 237, 0.1)',
                    fill: true,
                    tension: 0.4,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                }
            }
        });
        @endif

        @if($toolUsage->count() > 0)
        // Tool Usage Chart
        const ctx2 = document.getElementById('toolsChart').getContext('2d');
        const toolLabels = @json($toolUsage->pluck('tool_name')->map(fn($t) => ucfirst(str_replace('_', ' ', $t))));
        const toolData = @json($toolUsage->pluck('count'));
        const colors = ['#7c3aed', '#2563eb', '#dc2626', '#16a34a', '#f59e0b', '#ec4899'];

        new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: toolLabels,
                datasets: [{
                    data: toolData,
                    backgroundColor: colors.slice(0, toolData.length),
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
        @endif

        @if($usersPerDay->count() > 0)
        // User Growth Chart
        const ctx3 = document.getElementById('userGrowthChart').getContext('2d');
        new Chart(ctx3, {
            type: 'bar',
            data: {
                labels: @json($usersPerDay->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('M d'))),
                datasets: [{
                    label: 'New Users',
                    data: @json($usersPerDay->pluck('count')),
                    backgroundColor: '#7c3aed',
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                }
            }
        });
        @endif

    });
</script>
@endsection