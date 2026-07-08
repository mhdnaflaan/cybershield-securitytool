@extends('layouts.app')

@section('title', 'DNS Lookup Tool')

@section('content')
<div class="container mx-auto px-4 py-10 max-w-4xl">
    <div class="bg-white rounded-2xl shadow-lg p-8">

        <!-- Header -->
        <div class="flex items-center gap-3 mb-6">
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center text-green-600 text-xl">
                <i class="fas fa-globe"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-800"> DNS Lookup Tool</h1>
                <p class="text-gray-500 text-sm">View DNS records for any domain (A, MX, CNAME, NS, TXT).</p>
            </div>
        </div>

        <!-- Info Box -->
        <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
            <p class="text-blue-700 text-sm">
                <strong> What is DNS?</strong> DNS (Domain Name System) translates domain names to IP addresses.
                This tool helps you understand how a domain is configured.
            </p>
        </div>

        <!-- Input Form -->
        <form method="POST" action="{{ route('student.dns-lookup') }}">
            @csrf
            <div class="flex flex-col md:flex-row gap-3">
                <input type="text" name="domain" value="{{ old('domain', $domain ?? '') }}" required
                       placeholder="example.com"
                       class="flex-1 px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:outline-none">
                <button type="submit" class="md:w-auto w-full bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-3 rounded-xl transition">
                     Lookup DNS
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
        @if(isset($records) && !empty($records))
            <div class="mt-8">
                <h3 class="font-bold text-lg text-gray-800 mb-4">
                     DNS Records for <span class="font-mono text-blue-600">{{ $domain }}</span>
                </h3>

                @foreach($records as $type => $recordList)
                    <div class="mb-4">
                        <h4 class="font-bold text-gray-700 mb-2">
                            <span class="inline-block px-3 py-1 rounded-lg text-sm font-semibold
                                @if($type == 'A') bg-blue-100 text-blue-700
                                @elseif($type == 'AAAA') bg-indigo-100 text-indigo-700
                                @elseif($type == 'MX') bg-green-100 text-green-700
                                @elseif($type == 'CNAME') bg-purple-100 text-purple-700
                                @elseif($type == 'NS') bg-yellow-100 text-yellow-700
                                @elseif($type == 'TXT') bg-orange-100 text-orange-700
                                @elseif($type == 'SOA') bg-red-100 text-red-700
                                @else bg-gray-100 text-gray-700
                                @endif">
                                {{ $type }}
                            </span>
                            <span class="text-xs text-gray-400 ml-2">{{ count($recordList) }} record(s)</span>
                        </h4>

                        <div class="bg-gray-50 rounded-lg overflow-hidden border border-gray-200">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-100">
                                    <tr>
                                        @if($type == 'A' || $type == 'AAAA' || $type == 'CNAME')
                                            <th class="px-4 py-2 text-left">Host</th>
                                            <th class="px-4 py-2 text-left">TTL</th>
                                            <th class="px-4 py-2 text-left">Value</th>
                                        @elseif($type == 'MX')
                                            <th class="px-4 py-2 text-left">Priority</th>
                                            <th class="px-4 py-2 text-left">TTL</th>
                                            <th class="px-4 py-2 text-left">Mail Server</th>
                                        @elseif($type == 'NS')
                                            <th class="px-4 py-2 text-left">TTL</th>
                                            <th class="px-4 py-2 text-left">Name Server</th>
                                        @elseif($type == 'TXT')
                                            <th class="px-4 py-2 text-left">TTL</th>
                                            <th class="px-4 py-2 text-left">Value</th>
                                        @elseif($type == 'SOA')
                                            <th class="px-4 py-2 text-left">Primary NS</th>
                                            <th class="px-4 py-2 text-left">Email</th>
                                            <th class="px-4 py-2 text-left">Serial</th>
                                            <th class="px-4 py-2 text-left">Refresh</th>
                                            <th class="px-4 py-2 text-left">Retry</th>
                                            <th class="px-4 py-2 text-left">Expire</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recordList as $record)
                                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                                            @if($type == 'A' || $type == 'AAAA' || $type == 'CNAME')
                                                <td class="px-4 py-2 font-mono text-xs">{{ $record['host'] ?? '-' }}</td>
                                                <td class="px-4 py-2 font-mono text-xs">{{ $record['ttl'] ?? '-' }}</td>
                                                <td class="px-4 py-2 font-mono text-xs text-blue-600">{{ $record['ip'] ?? $record['target'] ?? '-' }}</td>
                                            @elseif($type == 'MX')
                                                <td class="px-4 py-2 font-mono text-xs">{{ $record['pri'] ?? '-' }}</td>
                                                <td class="px-4 py-2 font-mono text-xs">{{ $record['ttl'] ?? '-' }}</td>
                                                <td class="px-4 py-2 font-mono text-xs text-blue-600">{{ $record['target'] ?? '-' }}</td>
                                            @elseif($type == 'NS')
                                                <td class="px-4 py-2 font-mono text-xs">{{ $record['ttl'] ?? '-' }}</td>
                                                <td class="px-4 py-2 font-mono text-xs text-blue-600">{{ $record['target'] ?? '-' }}</td>
                                            @elseif($type == 'TXT')
                                                <td class="px-4 py-2 font-mono text-xs">{{ $record['ttl'] ?? '-' }}</td>
                                                <td class="px-4 py-2 font-mono text-xs break-all">{{ $record['txt'] ?? '-' }}</td>
                                            @elseif($type == 'SOA')
                                                <td class="px-4 py-2 font-mono text-xs">{{ $record['mname'] ?? '-' }}</td>
                                                <td class="px-4 py-2 font-mono text-xs">{{ $record['rname'] ?? '-' }}</td>
                                                <td class="px-4 py-2 font-mono text-xs">{{ $record['serial'] ?? '-' }}</td>
                                                <td class="px-4 py-2 font-mono text-xs">{{ $record['refresh'] ?? '-' }}</td>
                                                <td class="px-4 py-2 font-mono text-xs">{{ $record['retry'] ?? '-' }}</td>
                                                <td class="px-4 py-2 font-mono text-xs">{{ $record['expire'] ?? '-' }}</td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach

                <!-- Educational Note -->
                <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <p class="text-blue-700 text-sm">
                        <strong> What these records mean:</strong>
                    </p>
                    <ul class="mt-2 text-sm text-blue-600 space-y-1">
                        <li>• <strong>A / AAAA</strong> Maps domain to IP address (IPv4 / IPv6)</li>
                        <li>• <strong>MX</strong>  Mail servers that handle email for the domain</li>
                        <li>• <strong>CNAME</strong>  Alias that points one domain to another</li>
                        <li>• <strong>NS</strong>  Name servers that manage the domain</li>
                        <li>• <strong>TXT</strong> Text records for verification (SPF, DKIM, etc.)</li>
                        <li>• <strong>SOA</strong> Start of Authority (domain administrative info)</li>
                    </ul>
                </div>
            </div>
        @elseif(isset($domain) && !isset($error))
            <div class="mt-6 p-4 bg-gray-50 rounded-lg border border-gray-200 text-center">
                <p class="text-gray-500 text-sm">No DNS records found for <span class="font-mono">{{ $domain }}</span></p>
            </div>
        @endif

    </div>
</div>
@endsection