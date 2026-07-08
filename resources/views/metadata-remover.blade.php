@extends('layouts.app')

@section('title', 'Metadata Remover')

@section('content')
<div class="container mx-auto px-4 py-10 max-w-4xl">
    <div class="bg-white rounded-2xl shadow-lg p-8">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center text-green-600 text-xl">
                <i class="fas fa-shield-alt"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-800"> Metadata Remover</h1>
                <p class="text-gray-500 text-sm">Remove sensitive metadata from images before sharing them publicly.</p>
            </div>
        </div>

        <div class="mb-6 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
            <p class="text-yellow-700 text-sm">
                <strong> Privacy Warning:</strong>
                Files often contain hidden metadata like GPS coordinates, author names, and software details.
                This tool strips that data to protect your privacy.
            </p>
        </div>

        <form method="POST" action="{{ route('metadata.remove') }}" enctype="multipart/form-data" class="mb-8">
            @csrf
            <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-green-500 transition">
                <i class="fas fa-cloud-upload-alt text-5xl text-gray-400 mb-4"></i>
                <p class="text-gray-600 mb-2">Upload a file to remove metadata</p>
                <p class="text-xs text-gray-400 mb-4">Supports: JPG, PNG</p>
                <input type="file" name="file" id="fileInput" class="hidden" required>
                <button type="button" id="uploadBtn" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-xl transition">
                     Choose File
                </button>
                <span id="fileName" class="ml-3 text-sm text-gray-500"></span>
            </div>
            <div class="mt-4 text-center">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-8 py-3 rounded-xl transition">
                    Remove Metadata
                </button>
            </div>
        </form>

        @error('file')
            <div class="p-4 bg-red-50 border-l-4 border-red-500 rounded-lg text-red-600 text-sm mb-4">
                {{ $message }}
            </div>
        @enderror

        
        @if(isset($metadata))
            <div class="mt-8 p-6 bg-gray-50 rounded-xl border border-gray-200">
                <h3 class="font-bold text-lg text-gray-800 mb-4"> Metadata Report</h3>
                <p class="text-sm text-gray-500">File: <span class="font-mono">{{ $original_name ?? 'N/A' }}</span></p>
                <p class="text-xs text-gray-400 mb-4">Size: {{ $file_size ?? 'N/A' }}</p>

                @if(!empty($metadata))
                    <div class="bg-white p-4 rounded-lg border mb-4">
                        <h4 class="font-bold text-red-700 mb-3">Detected Metadata</h4>
                        <div class="space-y-1 text-sm max-h-60 overflow-y-auto">
                            @foreach($metadata as $key => $value)
                                <div class="flex justify-between border-b pb-1">
                                    <span class="text-gray-600 font-medium">{{ $key }}:</span>
                                    <span class="font-mono text-xs text-red-600">{{ $value }}</span>
                                </div>
                            @endforeach
                        </div>
                        <p class="mt-2 text-xs text-gray-400">
                             This data will be removed from the cleaned image.
                        </p>
                    </div>
                @else
                    <div class="bg-green-50 p-4 rounded-lg border border-green-200 mb-4">
                        <p class="text-green-700 text-sm"> No metadata detected in this image.</p>
                    </div>
                @endif

                @if(isset($cleaned_path))
                    <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                        <h4 class="font-bold text-green-700 mb-2"> Metadata Removed!</h4>
                        <p class="text-green-600 text-sm mb-3">
                            Your image has been cleaned. All sensitive metadata has been stripped.
                        </p>
                        <a href="{{ route('metadata.download', $cleaned_path) }}" class="inline-block bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-3 rounded-xl transition">
                             Download Cleaned image
                        </a>
                        <p class="text-xs text-gray-400 mt-2">
                            File: <span class="font-mono">{{ $cleaned_name ?? 'cleaned_file' }}</span>
                        </p>
                    </div>
                @endif
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