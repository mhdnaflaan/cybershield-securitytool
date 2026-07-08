@extends('layouts.app')

@section('title', 'Whois Lookup Tool')

@section('content')
<div class="container mx-auto px-4 py-10 max-w-4xl">
    <div class="bg-white rounded-2xl shadow-lg p-8">

        <!-- Header -->
        <div class="flex items-center gap-3 mb-6">
            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center text-purple-600 text-xl">
                <i class="fas fa-address-card"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-800"> Whois Lookup Tool</h1>
                <p class="text-gray-500 text-sm">Find domain registration details (owner, registrar, expiry).</p>
            </div>
        </div>

        <!-- Info Box -->
        <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
            <p class="text-blue-700 text-sm">
                <strong>What is Whois?</strong> Whois is a protocol used to query databases that store
                domain registration information. It helps identify domain owners and expiration dates.
            </p>
        </div>

        <!-- Input Form -->
        <form method="POST" action="{{ route('student.whois-lookup') }}">
            @csrf
            <div class="flex flex-col md:flex-row gap-3">
                <input type="text" name="domain" value="{{ old('domain', $domain ?? '') }}" required
                       placeholder="example.com"
                       class="flex-1 px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:outline-none">
                <button type="submit" class="md:w-auto w-full bg-purple-600 hover:bg-purple-700 text-white font-semibold px-6 py-3 rounded-xl transition">
                     Lookup Whois
                </button>
            </div>
            @error('domain')
                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
            @enderror
        </form>

        <!-- Error -->
        @if(isset($error))
            <div class="mt-6 p-4 bg-red-50 border-l-4 border-red-500 rounded">
                <p class="text-red-600 text-sm">{{ $error }}</p>
            </div>
        @endif

        <!-- Results -->
        @if(isset($parsedData) && !empty($parsedData))
            <div class="mt-8">
                <h3 class="font-bold text-lg text-gray-800 mb-4">
                WHOIS Records for <span class="font-mono text-purple-600">{{ $domain }}</span>
                </h3>

                <div class="bg-white rounded-lg overflow-hidden border border-gray-200">
                    <table class="w-full text-sm">
                        <tbody>
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="px-4 py-3 font-semibold text-gray-600 w-1/3">Domain Name</td>
                                <td class="px-4 py-3 font-mono text-blue-600 font-medium break-all">{{ $parsedData['Domain Name'] ?? 'N/A' }}</td>
                            </tr>
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="px-4 py-3 font-semibold text-gray-600">Registrar</td>
                                <td class="px-4 py-3 break-all">{{ $parsedData['Registrar'] ?? 'N/A' }}</td>
                            </tr>
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="px-4 py-3 font-semibold text-gray-600">Creation Date</td>
                                <td class="px-4 py-3 text-green-600 font-medium">{{ $parsedData['Creation Date'] ?? 'N/A' }}</td>
                            </tr>
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="px-4 py-3 font-semibold text-gray-600">Expiry Date</td>
                                <td class="px-4 py-3 text-red-600 font-medium">{{ $parsedData['Expiry Date'] ?? 'N/A' }}</td>
                            </tr>
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="px-4 py-3 font-semibold text-gray-600">Updated Date</td>
                                <td class="px-4 py-3">{{ $parsedData['Updated Date'] ?? 'N/A' }}</td>
                            </tr>
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="px-4 py-3 font-semibold text-gray-600">Name Servers</td>
                                <td class="px-4 py-3 font-mono text-xs break-all">{{ $parsedData['Name Servers'] ?? 'N/A' }}</td>
                            </tr>
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="px-4 py-3 font-semibold text-gray-600">Domain Status</td>
                                <td class="px-4 py-3">
                                    @if(!empty($parsedData['Domain Status']) && $parsedData['Domain Status'] != 'N/A')
                                        <span class="inline-block px-2 py-1 bg-yellow-100 text-yellow-700 rounded text-xs">{{ $parsedData['Domain Status'] }}</span>
                                    @else
                                        {{ $parsedData['Domain Status'] ?? 'N/A' }}
                                    @endif
                                </td>
                            </tr>
                            @if(isset($parsedData['Registrant']) && $parsedData['Registrant'] != 'N/A')
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="px-4 py-3 font-semibold text-gray-600">Registrant</td>
                                <td class="px-4 py-3">{{ $parsedData['Registrant'] }}</td>
                            </tr>
                            @endif
                            @if(isset($parsedData['Registrant Email']) && $parsedData['Registrant Email'] != 'N/A')
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="px-4 py-3 font-semibold text-gray-600">Registrant Email</td>
                                <td class="px-4 py-3 font-mono text-xs">{{ $parsedData['Registrant Email'] }}</td>
                            </tr>
                            @endif
                            @if(isset($parsedData['DNSSEC']) && $parsedData['DNSSEC'] != 'N/A')
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="px-4 py-3 font-semibold text-gray-600">DNSSEC</td>
                                <td class="px-4 py-3">{{ $parsedData['DNSSEC'] }}</td>
                            </tr>
                            @endif
                            @if(isset($parsedData['WHOIS Server']) && $parsedData['WHOIS Server'] != 'N/A')
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-semibold text-gray-600">WHOIS Server</td>
                                <td class="px-4 py-3 font-mono text-xs">{{ $parsedData['WHOIS Server'] }}</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <!-- Summary Cards -->
                <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-green-50 p-4 rounded-lg border border-green-200 text-center">
                        <p class="text-xs text-gray-500">Creation Date</p>
                        <p class="font-bold text-green-600 text-sm">{{ $parsedData['Creation Date'] ?? 'N/A' }}</p>
                    </div>
                    <div class="bg-red-50 p-4 rounded-lg border border-red-200 text-center">
                        <p class="text-xs text-gray-500">Expiry Date</p>
                        <p class="font-bold text-red-600 text-sm">{{ $parsedData['Expiry Date'] ?? 'N/A' }}</p>
                    </div>
                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-200 text-center">
                        <p class="text-xs text-gray-500">Registrar</p>
                        <p class="font-bold text-blue-600 text-sm">{{ $parsedData['Registrar'] ?? 'N/A' }}</p>
                    </div>
                </div>

                <!-- Copy Button -->
                <div class="mt-4 text-right">
                    <button onclick="copyToClipboard()" class="text-xs text-blue-600 hover:underline">
                         Copy Raw WHOIS Data
                    </button>
                </div>

                <!-- Raw Data (Toggle) -->
                <details class="mt-4">
                    <summary class="cursor-pointer text-sm text-gray-500 hover:text-gray-700">
                        🔍 View Raw WHOIS Data
                    </summary>
                    <pre class="mt-2 p-4 bg-gray-900 text-green-400 text-xs rounded-lg overflow-x-auto max-h-96 leading-relaxed">{{ $parsedData['raw'] ?? 'No raw data available.' }}</pre>
                </details>

                <!-- Educational Note -->
                <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <p class="text-blue-700 text-sm">
                        <strong>Why Whois matters:</strong>
                        Whois helps you verify domain ownership, check expiration dates,
                        and investigate suspicious domains. It's an essential OSINT tool.
                    </p>
                </div>
            </div>
        @elseif(isset($domain) && !isset($error))
            <div class="mt-6 p-4 bg-gray-50 rounded-lg border border-gray-200 text-center">
                <p class="text-gray-500 text-sm">No WHOIS data found for <span class="font-mono">{{ $domain }}</span></p>
            </div>
        @endif

    </div>
</div>

<script>
function copyToClipboard() {
    const pre = document.querySelector('pre');
    if (!pre) return;
    const text = pre.innerText;
    navigator.clipboard.writeText(text).then(() => {
        alert('Raw WHOIS data copied to clipboard!');
    }).catch(() => {
        const range = document.createRange();
        const selection = window.getSelection();
        range.selectNode(pre);
        selection.removeAllRanges();
        selection.addRange(range);
        document.execCommand('copy');
        alert('Raw WHOIS data copied to clipboard!');
    });
}
</script>

@endsection