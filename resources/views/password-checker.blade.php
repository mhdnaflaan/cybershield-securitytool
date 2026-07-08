@extends('layouts.app')

@section('title', 'Password Analyzer')

@section('content')
<div class="container mx-auto px-4 py-10 max-w-2xl">
    <div class="bg-white rounded-2xl shadow-lg p-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">🔐 Password Strength Analyzer</h1>
        <p class="text-gray-500 mb-6">Type a password to see its strength, crack time, and breach status.</p>

        <!-- Input Form -->
        <div class="mb-6">
            <form method="POST" action="{{ route('password.checker.check') }}">
                @csrf
                <label class="block text-gray-700 font-medium mb-2">Enter password:</label>
                <input type="password" name="password" id="password" required
                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none"
                       placeholder="e.g., MyS3cure!P@ss">
                <button type="submit" class="mt-4 w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-xl transition">
                    Check Strength
                </button>
            </form>
        </div>

        <!-- Results -->
        @if(isset($result))
            <div class="mt-6 p-5 bg-gray-50 rounded-xl border border-gray-200">
                <h3 class="font-bold text-gray-800 mb-3">📊 Results</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Password:</span>
                        <span class="font-mono text-sm">{{ $password }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Strength:</span>
                        <span id="strength" class="font-semibold
                            @if($strength == 'Weak') text-red-600
                            @elseif($strength == 'Medium') text-yellow-600
                            @else text-green-600
                            @endif">
                            {{ $strength }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Estimated crack time:</span>
                        <span class="font-mono">{{ $crackTime }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Breach check:</span>
                        <span class="font-semibold {{ $breachClass ?? '' }}">
                            {{ $breachMessage }}
                        </span>
                    </div>
                </div>
                <!-- Strength Bar -->
                <div class="mt-4">
                    <div class="w-full bg-gray-300 rounded-full h-2.5">
                        <div class="h-2.5 rounded-full
                            @if($strength == 'Weak') bg-red-500 w-1/3
                            @elseif($strength == 'Medium') bg-yellow-500 w-2/3
                            @else bg-green-500 w-full
                            @endif">
                        </div>
                    </div>
                </div>
                <!-- Breach Detail (if pwned) -->
                @if(isset($breachMessage) && str_contains($breachMessage, 'data breach'))
                    <div class="mt-4 p-3 bg-red-50 border-l-4 border-red-500 rounded text-red-700 text-sm">
                        <strong>⚠️ Security Alert:</strong> This password is compromised. Choose a different, stronger password.
                    </div>
                @endif
            </div>
        @endif
        
    </div>

    
</div>
@endsection