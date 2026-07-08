@extends('layouts.app')

@section('title', 'Metadata Extractor')

@section('content')
<div class="container mx-auto px-4 py-10 max-w-4xl">
    <div class="bg-white rounded-2xl shadow-lg p-8">

        <!-- Header -->
        <div class="flex items-center gap-3 mb-6">
            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center text-purple-600 text-xl">
                <i class="fas fa-file-alt"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-800"> Metadata Extractor</h1>
                <p class="text-gray-500 text-sm">Extract and analyze metadata from files (Images,EXIF, document properties, etc.)</p>
            </div>
        </div>

        <!-- Info Box -->
        <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
            <p class="text-blue-700 text-sm">
                <strong> What is Metadata?</strong>
                Metadata is "data about data." It can reveal sensitive information like GPS coordinates, author names,
                software used, and creation dates. This tool helps you discover what information is hidden in files.
            </p>
        </div>

        <!-- Upload Form -->
        <form method="POST" action="{{ route('student.metadata.extract') }}" enctype="multipart/form-data" class="mb-8">
            @csrf
            <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-purple-500 transition">
                <i class="fas fa-cloud-upload-alt text-5xl text-gray-400 mb-4"></i>
                <p class="text-gray-600 mb-2">Upload a file to extract metadata</p>
                <p class="text-xs text-gray-400 mb-4">Supports: Images, PDFs, Office Docs, Audio, Video</p>
                <input type="file" name="file" id="fileInput" class="hidden" required>
                <button type="button" id="uploadBtn" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-xl transition">
                     Choose File
                </button>
                <span id="fileName" class="ml-3 text-sm text-gray-500"></span>
            </div>
            <div class="mt-4 text-center">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-8 py-3 rounded-xl transition">
                     Extract Metadata
                </button>
            </div>
        </form>

        @error('file')
            <div class="p-4 bg-red-50 border-l-4 border-red-500 rounded-lg text-red-600 text-sm mb-4">
                {{ $message }}
            </div>
        @enderror

        <!-- Results -->
        @if(isset($result))
            <div class="mt-8 p-6 bg-gray-50 rounded-xl border border-gray-200">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="font-bold text-lg text-gray-800">📊 Metadata Report</h3>
                        <p class="text-sm text-gray-500">File: <span class="font-mono">{{ $result['file_name'] ?? 'N/A' }}</span></p>
                        <p class="text-xs text-gray-400">Uploaded: {{ $result['uploaded_at'] ?? 'N/A' }}</p>
                    </div>
                    <span class="text-xs text-gray-400">Size: {{ $result['file_size'] ?? 'N/A' }}</span>
                </div>

                <!-- File Information -->
                <div class="bg-white p-4 rounded-lg border mb-4">
                    <h4 class="font-bold text-gray-700 mb-3"> File Information</h4>
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        <div class="flex justify-between border-b pb-1">
                            <span class="text-gray-500">File Name:</span>
                            <span class="font-mono">{{ $result['file_name'] ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between border-b pb-1">
                            <span class="text-gray-500">File Size:</span>
                            <span>{{ $result['file_size'] ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between border-b pb-1">
                            <span class="text-gray-500">File Type:</span>
                            <span>{{ $result['file_type'] ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between border-b pb-1">
                            <span class="text-gray-500">Extension:</span>
                            <span>{{ $result['extension'] ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Metadata Table -->
                @if(!empty($result['metadata']))
                    <div class="bg-white p-4 rounded-lg border mb-4">
                        <h4 class="font-bold text-gray-700 mb-3">Extracted Metadata</h4>
                        <div class="space-y-1 text-sm max-h-96 overflow-y-auto">
                            @foreach($result['metadata'] as $key => $value)
                                <div class="flex justify-between border-b pb-1">
                                    <span class="text-gray-600 font-medium">{{ $key }}:</span>
                                    <span class="font-mono text-xs break-all text-right max-w-[60%]">
                                        @if(str_contains($key, 'Maps'))
                                            <a href="{{ $value }}" target="_blank" class="text-blue-600 hover:underline">
                                                {{ $value }}
                                            </a>
                                        @else
                                            {{ $value }}
                                        @endif
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200 mb-4">
                        <p class="text-yellow-700 text-sm">No metadata found for this file type.</p>
                    </div>
                @endif

                <!-- Alerts -->
                @if(!empty($result['alerts']))
                    <div class="p-4 bg-red-50 rounded-lg border border-red-200">
                        <h4 class="font-bold text-red-700 mb-2">⚠️ Sensitive Data Found</h4>
                        <ul class="space-y-1 text-sm">
                            @foreach($result['alerts'] as $alert)
                                <li class="text-red-600">• {{ $alert }}</li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <div class="p-4 bg-green-50 rounded-lg border border-green-200">
                        <p class="text-green-700 text-sm">No sensitive metadata detected in this file.</p>
                    </div>
                @endif

                <!-- Educational Note -->
                <div class="mt-4 p-4 bg-purple-50 rounded-lg border border-purple-200">
                    <p class="text-purple-700 text-sm">
                        <strong>Why this matters:</strong>
                        Metadata can reveal sensitive information like GPS coordinates, author names, and software used.
                        Always remove metadata before sharing files publicly to protect your privacy.
                    </p>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const uploadBtn = document.getElementById('uploadBtn');
        const fileInput = document.getElementById('fileInput');
        const fileName = document.getElementById('fileName');

        uploadBtn.addEventListener('click', function() {
            fileInput.click();
        });

        fileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                fileName.textContent = this.files[0].name;
            }
        });
    });
</script>
@endsection