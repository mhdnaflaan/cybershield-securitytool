@extends('layouts.app')

@section('title', 'Feedback & Support')

@section('content')
<div class="container mx-auto px-4 py-10 max-w-3xl">
    <div class="bg-white rounded-2xl shadow-lg p-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Feedback & Support</h1>
        <p class="text-gray-500 mb-6">We'd love to hear your thoughts! Report bugs, suggest features, or send feedback.</p>

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-lg">
                <p class="text-green-700 text-sm">{{ session('success') }}</p>
            </div>
        @endif

        <form method="POST" action="{{ route('feedback.store') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Feedback Type <span class="text-red-500">*</span></label>
                <select name="type" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="feedback">General Feedback</option>
                    <option value="bug_report"> Bug Report</option>
                    <option value="feature_request"> Feature Request</option>
                    <option value="support"> Support Request</option>
                </select>
                @error('type')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Subject <span class="text-red-500">*</span></label>
                <input type="text" name="subject" value="{{ old('subject') }}" required
                       placeholder="Brief summary of your feedback"
                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none">
                @error('subject')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Message <span class="text-red-500">*</span></label>
                <textarea name="message" rows="6" required
                          placeholder="Please provide as much detail as possible..."
                          class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none">{{ old('message') }}</textarea>
                @error('message')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-xl transition">
                 Submit Feedback
            </button>
        </form>

        <div class="mt-8 p-4 bg-blue-50 rounded-xl border border-blue-200">
            <h3 class="font-bold text-blue-800 mb-2">📌 What happens next?</h3>
            <ul class="text-sm text-blue-700 space-y-1">
                <li>✓ We'll review your feedback within 1-2 business days</li>
                <li>✓ Bug reports will be prioritized</li>
                <li>✓ Feature requests will be considered for future updates</li>
                <li>✓ You'll be notified when your feedback is addressed</li>
            </ul>
        </div>
    </div>
</div>
@endsection