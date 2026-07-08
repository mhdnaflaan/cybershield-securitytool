@extends('layouts.app')

@section('title', 'Hash Tool')

@section('content')
<div class="container mx-auto px-4 py-10 max-w-3xl">
    <div class="bg-white rounded-2xl shadow-lg p-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">🔑 Hash Generator & Identifier</h1>
        <p class="text-gray-500 mb-6">Generate hashes or identify unknown hash strings.</p>

        <!-- Generate Section -->
        <div class="mb-10">
            <h2 class="text-lg font-semibold text-gray-700 mb-3">Generate Hash</h2>
            <form method="POST" action="{{ route('hash.tool.generate') }}">
                @csrf
                <div class="flex flex-col md:flex-row gap-3">
                    <input type="text" name="text" placeholder="Enter text to hash" required
                           class="flex-1 px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:outline-none">
                    <select name="algorithm" class="px-4 py-3 border border-gray-300 rounded-xl bg-white">
                        <option value="md5">MD5</option>
                        <option value="sha1">SHA1</option>
                        <option value="sha256">SHA256</option>
                        <option value="sha512">SHA512</option>
                    </select>
                    <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white font-semibold px-6 py-3 rounded-xl transition">
                        Generate
                    </button>
                </div>
            </form>

            @if(isset($generatedHash))
                <div class="mt-4 p-4 bg-gray-100 rounded-lg">
                    <div class="flex flex-wrap justify-between text-sm">
                        <span class="text-gray-600">Text:</span>
                        <span class="font-mono">{{ $inputText ?? 'N/A' }}</span>
                    </div>
                    <div class="flex flex-wrap justify-between text-sm mt-2">
                        <span class="text-gray-600">Algorithm:</span>
                        <span class="font-mono uppercase">{{ $algorithm ?? 'N/A' }}</span>
                    </div>
                    <div class="flex flex-wrap justify-between text-sm mt-2">
                        <span class="text-gray-600">Hash:</span>
                        <span class="font-mono text-xs break-all">{{ $generatedHash }}</span>
                    </div>
                </div>
            @endif
        </div>

        <!-- Identify Section -->
        <div>
            <h2 class="text-lg font-semibold text-gray-700 mb-3">Identify Hash Type</h2>
            <form method="POST" action="{{ route('hash.tool.identify') }}">
                @csrf
                <div class="flex flex-col md:flex-row gap-3">
                    <input type="text" name="hash" placeholder="Paste a hash (e.g., 5d41402abc4b2a76b9719d911017c592)"
                           class="flex-1 px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:outline-none">
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-3 rounded-xl transition">
                        Identify
                    </button>
                </div>
            </form>

            @if(isset($identifiedType))
                <div class="mt-4 p-4 bg-gray-100 rounded-lg">
                    <div class="flex flex-wrap justify-between text-sm">
                        <span class="text-gray-600">Hash:</span>
                        <span class="font-mono text-xs break-all">{{ $hashInput ?? 'N/A' }}</span>
                    </div>
                    <div class="flex flex-wrap justify-between text-sm mt-2">
                        <span class="text-gray-600">Identified Type:</span>
                        <span class="font-semibold text-purple-600">{{ $identifiedType }}</span>
                    </div>
                </div>
            @endif
        </div>
    </div>

    
</div>
@endsection