@extends('layouts.app')

@section('title', 'Admin - Users')

@section('content')
<div class="container mx-auto px-4 py-10">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">👥 User Management</h1>
        <div class="flex gap-3">
            <a href="{{ route('admin.export.users') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm transition">
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

    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-3 text-left">ID</th>
                        <th class="px-4 py-3 text-left">Name</th>
                        <th class="px-4 py-3 text-left">Email</th>
                        <th class="px-4 py-3 text-left">Role</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Scans</th>
                        <th class="px-4 py-3 text-left">Joined</th>
                        <th class="px-4 py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3">#{{ $user->id }}</td>
                            <td class="px-4 py-3">{{ $user->name }}</td>
                            <td class="px-4 py-3">{{ $user->email }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded-full text-xs
                                    @if($user->role == 'admin') bg-red-100 text-red-600
                                    @else bg-blue-100 text-blue-600
                                    @endif">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded-full text-xs
                                    @if($user->is_active) bg-green-100 text-green-600
                                    @else bg-red-100 text-red-600
                                    @endif">
                                    {{ $user->is_active ? 'Active' : 'Blocked' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">{{ $user->scans_count ?? 0 }}</td>
                            <td class="px-4 py-3 text-xs text-gray-400">{{ $user->created_at->format('Y-m-d') }}</td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex justify-center gap-2">
                                    <a href="{{ route('admin.user_details', $user->id) }}" class="text-blue-600 hover:underline text-xs">View</a>
                                    <form action="{{ route('admin.user.toggle', $user->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="text-{{ $user->is_active ? 'yellow' : 'green' }}-600 hover:underline text-xs">
                                            {{ $user->is_active ? 'Block' : 'Unblock' }}
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.user.delete', $user->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this user and all their scans?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline text-xs">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-gray-400">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection