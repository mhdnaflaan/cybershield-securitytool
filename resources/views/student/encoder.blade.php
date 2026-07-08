@extends('layouts.app')

@section('title', 'Base64 & URL Encoder/Decoder')

@section('content')
<div class="container mx-auto px-4 py-10 max-w-3xl">
    <div class="bg-white rounded-2xl shadow-lg p-8">

        
        <div class="flex items-center gap-3 mb-6">
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 text-xl">
                <i class="fas fa-code"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-800"> Base64 & URL Encoder/Decoder</h1>
                <p class="text-gray-500 text-sm">Encode and decode text using Base64 and URL encoding.</p>
            </div>
        </div>

        
        <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
            <p class="text-blue-700 text-sm">
                <strong> What is encoding?</strong> Encoding converts data into a different format for safe transmission.
                <br>• <strong>Base64</strong> – Used for binary data in text (images, files, JWT tokens)
                <br>• <strong>URL Encoding</strong> – Makes special characters safe for URLs
            </p>
        </div>

        
        <form method="POST" action="{{ route('student.encoder.process') }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Select Operation</label>
                    <select name="type" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <option value="base64_encode">Base64 Encode</option>
                        <option value="base64_decode">Base64 Decode</option>
                        <option value="url_encode">URL Encode</option>
                        <option value="url_decode">URL Decode</option>
                    </select>
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Enter Text</label>
                    <textarea name="text" rows="5" required
                              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none font-mono text-sm"
                              placeholder="Enter text to encode or decode...">{{ old('text', $input ?? '') }}</textarea>
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-xl transition">
                    ⚡ Process
                </button>
            </div>
        </form>

        
        @if(isset($result))
            <div class="mt-8 p-6 bg-gray-50 rounded-xl border border-gray-200">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-bold text-lg text-gray-800">📋 Result</h3>
                    <span class="text-xs bg-blue-100 text-blue-600 px-3 py-1 rounded-full">
                        {{ $method ?? 'Processed' }}
                    </span>
                </div>

                <div class="bg-white p-4 rounded-lg border">
                    <p class="text-xs text-gray-500 mb-2">Input:</p>
                    <pre class="text-sm font-mono text-gray-700 whitespace-pre-wrap break-all">{{ $input }}</pre>
                </div>

                <div class="bg-white p-4 rounded-lg border mt-3">
                    <p class="text-xs text-gray-500 mb-2">Output:</p>
                    <pre class="text-sm font-mono text-blue-600 whitespace-pre-wrap break-all">{{ $result }}</pre>
                </div>

                <button onclick="copyResult()" class="mt-4 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm transition">
                     Copy Result
                </button>
            </div>
        @endif

    
        <div class="mt-6 p-4 bg-gray-50 rounded-xl border border-gray-200">
            <h3 class="font-bold text-gray-700 mb-2"> Quick Reference</h3>
            <div class="grid grid-cols-2 gap-2 text-sm">
                <div class="bg-white p-2 rounded border">
                    <span class="font-semibold">Base64 Encode:</span>
                    <span class="text-xs text-gray-500 block">Hello → SGVsbG8=</span>
                </div>
                <div class="bg-white p-2 rounded border">
                    <span class="font-semibold">Base64 Decode:</span>
                    <span class="text-xs text-gray-500 block">SGVsbG8= → Hello</span>
                </div>
                <div class="bg-white p-2 rounded border">
                    <span class="font-semibold">URL Encode:</span>
                    <span class="text-xs text-gray-500 block">Hello World → Hello%20World</span>
                </div>
                <div class="bg-white p-2 rounded border">
                    <span class="font-semibold">URL Decode:</span>
                    <span class="text-xs text-gray-500 block">Hello%20World → Hello World</span>
                </div>
            </div>
        </div>

        <!-- Educational Note -->
        <div class="mt-6 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
            <p class="text-yellow-700 text-sm">
                <strong> Note:</strong> Encoding is NOT encryption. It does not secure data – it only
                transforms it for compatibility. Use proper encryption for sensitive data.
            </p>
        </div>

    </div>
</div>

<script>
function copyResult() {
    const result = document.querySelector('.bg-gray-50 .bg-white:last-of-type pre');
    if (result) {
        navigator.clipboard.writeText(result.textContent).then(() => {
            alert('Copied to clipboard!');
        }).catch(() => {
            // Fallback for older browsers
            const range = document.createRange();
            const selection = window.getSelection();
            range.selectNode(result);
            selection.removeAllRanges();
            selection.addRange(range);
            document.execCommand('copy');
            alert('Copied to clipboard!');
        });
    }
}
</script>

@endsection