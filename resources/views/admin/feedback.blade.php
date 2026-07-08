@extends('layouts.app')

@section('title', 'Admin - Feedback')

@section('content')
<div class="container mx-auto px-4 py-10">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">💬 Feedback Management</h1>
        <a href="{{ route('admin.dashboard') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm transition">
            ← Back
        </a>
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
                        <th class="px-4 py-3 text-left">User</th>
                        <th class="px-4 py-3 text-left">Type</th>
                        <th class="px-4 py-3 text-left">Subject</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Date</th>
                        <th class="px-4 py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($feedbacks as $feedback)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3">#{{ $feedback->id }}</td>
                            <td class="px-4 py-3">{{ $feedback->user->name ?? 'Unknown' }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded-full text-xs
                                    @if($feedback->type == 'bug_report') bg-red-100 text-red-600
                                    @elseif($feedback->type == 'feature_request') bg-purple-100 text-purple-600
                                    @elseif($feedback->type == 'support') bg-yellow-100 text-yellow-600
                                    @else bg-blue-100 text-blue-600
                                    @endif">
                                    {{ str_replace('_', ' ', ucfirst($feedback->type)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 max-w-[150px] truncate">{{ $feedback->subject }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded-full text-xs
                                    @if($feedback->status == 'resolved') bg-green-100 text-green-600
                                    @elseif($feedback->status == 'read') bg-blue-100 text-blue-600
                                    @else bg-yellow-100 text-yellow-600
                                    @endif">
                                    {{ ucfirst($feedback->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-400">{{ $feedback->created_at->format('Y-m-d') }}</td>
                            <td class="px-4 py-3 text-center">
                                <button onclick="viewFeedback({{ $feedback->id }})" class="text-blue-600 hover:underline text-xs">View</button>
                                <form action="{{ route('admin.feedback.update', $feedback->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="text-green-600 hover:underline text-xs ml-2">
                                        Resolve
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-400">No feedback found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t">
            {{ $feedbacks->links() }}
        </div>
    </div>

    <!-- Feedback Modal -->
    <div id="feedbackModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full p-6 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-800">Feedback Details</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 text-2xl">×</button>
            </div>
            <div id="feedbackContent"></div>
        </div>
    </div>
</div>

<script>
    function viewFeedback(id) {
        fetch(`/admin/feedback/${id}/view`)
            .then(response => response.json())
            .then(data => {
                const modal = document.getElementById('feedbackContent');
                modal.innerHTML = `
                    <div class="space-y-3 text-sm">
                        <div><span class="font-semibold">User:</span> ${data.user_name}</div>
                        <div><span class="font-semibold">Type:</span> <span class="px-2 py-1 rounded-full text-xs ${data.type_class}">${data.type_label}</span></div>
                        <div><span class="font-semibold">Subject:</span> ${data.subject}</div>
                        <div><span class="font-semibold">Message:</span></div>
                        <div class="bg-gray-50 p-4 rounded-lg whitespace-pre-wrap">${data.message}</div>
                        <div><span class="font-semibold">Status:</span> ${data.status}</div>
                        <div><span class="font-semibold">Submitted:</span> ${data.created_at}</div>
                    </div>
                `;
                document.getElementById('feedbackModal').classList.remove('hidden');
            });
    }

    function closeModal() {
        document.getElementById('feedbackModal').classList.add('hidden');
    }

    // Close modal on outside click
    document.getElementById('feedbackModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
</script>
@endsection