<?php

namespace App\Http\Controllers;

use App\Models\Scan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


class StudentController extends Controller
{
    public function dashboard()
    {
        $studentTools = [
            // Cybersecurity Learning Tools
            'ip_reputation' => [
                'name' => 'IP Reputation Checker',
                'icon' => 'fa-network-wired',
                'color' => 'blue',
                'route' => 'student.ip-reputation',
                'description' => 'Check if an IP address is malicious using AbuseIPDB.',
                'category' => 'Network Reconnaissance'
            ],
            'dns_lookup' => [
                'name' => 'DNS Lookup Tool',
                'icon' => 'fa-globe',
                'color' => 'green',
                'route' => 'student.dns-lookup',
                'description' => 'View DNS records for any domain.',
                'category' => 'Network Reconnaissance'
            ],
            'whois_lookup' => [
                'name' => 'Whois Lookup',
                'icon' => 'fa-address-card',
                'color' => 'purple',
                'route' => 'student.whois-lookup',
                'description' => 'Find domain registration details.',
                'category' => 'Network Reconnaissance'
            ],
            'cve_lookup' => [
                'name' => 'CVE Lookup',
                'icon' => 'fa-bug',
                'color' => 'red',
                'route' => 'student.cve-lookup',
                'description' => 'Search vulnerability database (NVD).',
                'category' => 'Vulnerability Research'
            ],
            'metadata_extractor' => [
                'name' => 'Metadata Extractor',
                'icon' => 'fa-file-alt',
                'color' => 'purple',
                'route' => 'student.metadata',
                'description' => 'Extract hidden metadata from files (EXIF, document properties).',
                'category' => 'Forensics'
            ],
        
            'hash_tool' => [
                'name' => 'Hash Tool',
                'icon' => 'fa-hashtag',
                'color' => 'purple',
                'route' => 'hash.tool',
                'description' => 'Generate and identify hashes.',
                'category' => 'Cryptography'
            ],
            'password_analyzer' => [
                'name' => 'Password Analyzer',
                'icon' => 'fa-key',
                'color' => 'green',
                'route' => 'password.checker',
                'description' => 'Test password strength and breach check.',
                'category' => 'Secure Development'
            ],
            'url_checker' => [
                'name' => 'URL Safety Checker',
                'icon' => 'fa-link',
                'color' => 'blue',
                'route' => 'url.checker',
                'description' => 'Detect phishing and malicious URLs.',
                'category' => 'Threat Detection'
            ],
            'ssl_checker' => [
                'name' => 'SSL & Headers Checker',
                'icon' => 'fa-lock',
                'color' => 'red',
                'route' => 'ssl.checker',
                'description' => 'Check SSL certificates and security headers.',
                'category' => 'Threat Detection'
            ],
            'qr_checker' => [
                'name' => 'QR Code Phishing Checker',
                'icon' => 'fa-qrcode',
                'color' => 'blue',
                'route' => 'qr.checker',
                'description' => 'Check if QR codes lead to malicious websites.',
                'category' => 'Threat Detection'
            ],
        ];

        $recentScans = Scan::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('student.dashboard', compact('studentTools', 'recentScans'));
    }

    public function ipReputation(Request $request)
{
    $ip = null;
    $result = null;
    $error = null;

    if ($request->isMethod('post')) {
        $request->validate([
            'ip' => 'required|string|max:255',
        ]);

        $ip = $request->input('ip');
        $ip = trim($ip);

        // Check cache first (24 hours)
        $cacheKey = 'iprep_' . md5($ip);
        $cached = Cache::get($cacheKey);

        if ($cached) {
            $result = $cached;
        } else {
            // If input is a domain, resolve to IP first
            if (!filter_var($ip, FILTER_VALIDATE_IP)) {
                $resolvedIp = gethostbyname($ip);
                if ($resolvedIp && $resolvedIp !== $ip) {
                    $ip = $resolvedIp;
                }
            }

            // Validate IP
            if (!filter_var($ip, FILTER_VALIDATE_IP)) {
                $error = 'Invalid IP address or domain could not be resolved.';
                return view('student.ip-reputation', compact('ip', 'result', 'error'));
            }

            // Check with AbuseIPDB
            $result = $this->checkIpReputation($ip);

            if ($result) {
                Cache::put($cacheKey, $result, 1440); // 24 hours
            } else {
                $error = 'Could not retrieve IP reputation. Please try again later.';
            }
        }
    }

    return view('student.ip-reputation', compact('ip', 'result', 'error'));
}

/**
* Check IP reputation using AbuseIPDB API
*/
private function checkIpReputation($ip)
{
    $apiKey = config('services.abuseipdb.api_key');

    if (empty($apiKey)) {
        Log::error('AbuseIPDB API key not configured');
        return null;
    }

    try {
        $response = Http::withHeaders([
            'Key' => $apiKey,
            'Accept' => 'application/json',
        ])->timeout(30)->get('https://api.abuseipdb.com/api/v2/check', [
            'ipAddress' => $ip,
            'maxAgeInDays' => 90,
            'verbose' => true,
        ]);

        if ($response->successful()) {
            $data = $response->json();

            if (isset($data['data'])) {
                $info = $data['data'];
                return [
                    'ip' => $info['ipAddress'] ?? $ip,
                    'is_public' => $info['isPublic'] ?? false,
                    'is_whitelisted' => $info['isWhitelisted'] ?? false,
                    'abuse_confidence_score' => $info['abuseConfidenceScore'] ?? 0,
                    'country_code' => $info['countryCode'] ?? 'N/A',
                    'country_name' => $info['countryName'] ?? 'N/A',
                    'usage_type' => $info['usageType'] ?? 'Unknown',
                    'domain' => $info['domain'] ?? 'N/A',
                    'isp' => $info['isp'] ?? 'N/A',
                    'total_reports' => $info['totalReports'] ?? 0,
                    'num_distinct_users' => $info['numDistinctUsers'] ?? 0,
                    'last_reported_at' => $info['lastReportedAt'] ?? null,
                    'reports' => $info['reports'] ?? [],
                    'categories' => $this->getCategories($info['reports'] ?? []),
                    'confidence_level' => $this->getConfidenceLevel($info['abuseConfidenceScore'] ?? 0),
                    'recommendation' => $this->getRecommendation($info['abuseConfidenceScore'] ?? 0),
                ];
            }
        } else {
            Log::warning('AbuseIPDB API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        }
    } catch (\Exception $e) {
        Log::error('AbuseIPDB check failed: ' . $e->getMessage());
    }

    return null;
}

/**
* Extract categories from reports
*/
private function getCategories($reports)
{
    $categories = [];
    $categoryMap = [
        1 => 'DNS Compromise',
        2 => 'DNS Poisoning',
        3 => 'Fraud Orders',
        4 => 'DDoS Attack',
        5 => 'FTP Brute-Force',
        6 => 'Ping of Death',
        7 => 'Phishing',
        8 => 'Fraud VoIP',
        9 => 'Open Proxy',
        10 => 'Web Spam',
        11 => 'Email Spam',
        12 => 'Blog Spam',
        13 => 'VPN IP',
        14 => 'Port Scan',
        15 => 'Hacking',
        16 => 'SQL Injection',
        17 => 'Spoofing',
        18 => 'Brute-Force',
        19 => 'Bad Web Bot',
        20 => 'Exploited Host',
        21 => 'Web App Attack',
        22 => 'SSH',
        23 => 'IoT Targeted',
    ];

    foreach ($reports as $report) {
        if (isset($report['categories'])) {
            foreach ($report['categories'] as $catId) {
                if (isset($categoryMap[$catId]) && !in_array($categoryMap[$catId], $categories)) {
                    $categories[] = $categoryMap[$catId];
                }
            }
        }
    }

    return array_slice($categories, 0, 10); // Limit to 10
}

/**
* Get confidence level label
*/
private function getConfidenceLevel($score)
{
    if ($score >= 80) return ['label' => 'High Risk', 'color' => 'red'];
    if ($score >= 50) return ['label' => 'Medium Risk', 'color' => 'yellow'];
    if ($score >= 20) return ['label' => 'Low Risk', 'color' => 'orange'];
    return ['label' => 'Safe', 'color' => 'green'];
}

/**
* Get recommendation based on score
*/
private function getRecommendation($score)
{
    if ($score >= 80) {
        return 'This IP has a very high risk. Block it immediately.';
    } elseif ($score >= 50) {
        return ' This IP has moderate risk. Investigate further before allowing access.';
    } elseif ($score >= 20) {
        return 'This IP has low risk. Monitor for suspicious activity.';
    } else {
        return 'This IP appears to be safe. No action needed.';
    }
}

    public function dnsLookup(Request $request)
{
    $domain = null;
    $records = [];
    $error = null;

    if ($request->isMethod('post')) {
        $request->validate([
            'domain' => 'required|string|max:255',
        ]);

        $domain = $request->input('domain');
        $domain = preg_replace('#^https?://#', '', $domain);
        $domain = rtrim($domain, '/');

        try {
            $records = $this->getDnsRecords($domain);
        } catch (\Exception $e) {
            $error = 'DNS lookup failed: ' . $e->getMessage();
        }
    }

    return view('student.dns-lookup', compact('domain', 'records', 'error'));
}

/**
* Fetch DNS records for a domain
*/
private function getDnsRecords($domain)
{
    $records = [];

    // A Records (IPv4)
    $aRecords = @dns_get_record($domain, DNS_A);
    if ($aRecords) {
        $records['A'] = $aRecords;
    }

    // AAAA Records (IPv6)
    $aaaaRecords = @dns_get_record($domain, DNS_AAAA);
    if ($aaaaRecords) {
        $records['AAAA'] = $aaaaRecords;
    }

    // MX Records (Mail Exchange)
    $mxRecords = @dns_get_record($domain, DNS_MX);
    if ($mxRecords) {
        // Sort by priority
        usort($mxRecords, function($a, $b) {
            return $a['pri'] - $b['pri'];
        });
        $records['MX'] = $mxRecords;
    }

    // CNAME Records
    $cnameRecords = @dns_get_record($domain, DNS_CNAME);
    if ($cnameRecords) {
        $records['CNAME'] = $cnameRecords;
    }

    // NS Records (Name Servers)
    $nsRecords = @dns_get_record($domain, DNS_NS);
    if ($nsRecords) {
        $records['NS'] = $nsRecords;
    }

    // TXT Records
    $txtRecords = @dns_get_record($domain, DNS_TXT);
    if ($txtRecords) {
        $records['TXT'] = $txtRecords;
    }

    // SOA Record (Start of Authority)
    $soaRecords = @dns_get_record($domain, DNS_SOA);
    if ($soaRecords) {
        $records['SOA'] = $soaRecords;
    }

    return $records;
}

  
   // ... inside the class ...

public function whoisLookup(Request $request)
{
    $domain = null;
    $parsedData = [];
    $error = null;

    if ($request->isMethod('post')) {
        $request->validate([
            'domain' => 'required|string|max:255',
        ]);

        $domain = $request->input('domain');
        $domain = preg_replace('#^https?://#', '', $domain);
        $domain = rtrim($domain, '/');

        $cacheKey = 'whois_' . md5($domain);
        $cached = Cache::get($cacheKey);

        if ($cached) {
            $parsedData = $cached;
            Log::info('WHOIS from cache', ['domain' => $domain]);
        } else {
            // Try multiple sources in order
            $rawData = null;

            // 1. System WHOIS command
            $rawData = $this->getWhoisFromCommand($domain);

            // 2. Hackertarget API (50/day)
            if (!$rawData) {
                $rawData = $this->getWhoisFromApi1($domain);
            }

            // 3. whois-api.com (100/day)
            if (!$rawData) {
                $rawData = $this->getWhoisFromApi2($domain);
            }

            // 4. whoisxmlapi.com (free tier)
            if (!$rawData) {
                $rawData = $this->getWhoisFromApi3($domain);
            }

            if ($rawData) {
                $parsedData = $this->parseWhoisOutput($rawData);
                // Always store raw for fallback display
                $parsedData['raw'] = $rawData;

                // If no useful parsed data, mark it
                if (empty(array_filter($parsedData, function($v) {
                    return !in_array($v, ['N/A', 'raw']) && !empty($v);
                }))) {
                    $parsedData['note'] = 'Parsing failed – showing raw WHOIS output.';
                }

                Cache::put($cacheKey, $parsedData, 1440); // 24 hours
                Log::info('WHOIS fetched', ['domain' => $domain, 'source' => 'API/Command']);
            } else {
                $error = 'All WHOIS sources failed. Please try again later.';
                Log::warning('All WHOIS sources failed for', ['domain' => $domain]);
            }
        }
    }

    return view('student.whois-lookup', compact('domain', 'parsedData', 'error'));
}

/**
* Method 1: System WHOIS command (fastest, no limits)
*/
private function getWhoisFromCommand($domain)
{
    try {
        // Try full path first
        $whoisPaths = [
            'C:\Windows\System32\whois.exe',
            'C:\Windows\whois.exe',
            'whois' // fallback to PATH
        ];

        $executable = null;
        foreach ($whoisPaths as $path) {
            if (file_exists($path) || $path === 'whois') {
                $executable = $path;
                break;
            }
        }

        if (!$executable) {
            return null;
        }

        $command = $executable . ' ' . escapeshellarg($domain) . ' 2>&1';
        $output = shell_exec($command);

        if (!empty($output) &&
            !str_contains($output, 'not recognized') &&
            !str_contains($output, 'No such domain') &&
            !str_contains($output, 'Connection refused')) {
            return $output;
        }
    } catch (\Exception $e) {
        Log::error('WHOIS command error: ' . $e->getMessage());
    }

    return null;
}

/**
* Method 2: Hackertarget API (free, 50/day)
*/
private function getWhoisFromApi1($domain)
{
    try {
        $response = Http::timeout(30)
            ->get('https://api.hackertarget.com/whois/', [
                'q' => $domain,
            ]);

        if ($response->successful() && !empty($response->body())) {
            $body = $response->body();
            if (!str_contains($body, 'error') && !str_contains($body, 'not found')) {
                return $body;
            }
        }
    } catch (\Exception $e) {
        Log::warning('Hackertarget API failed: ' . $e->getMessage());
    }

    return null;
}

/**
* Method 3: whois-api.com (free, 100/day)
*/
private function getWhoisFromApi2($domain)
{
    try {
        $response = Http::timeout(30)
            ->get('https://whois-api.com/api/v1/whois', [
                'domain' => $domain,
            ]);

        if ($response->successful()) {
            $data = $response->json();
            if (!empty($data) && !isset($data['error'])) {
                // Convert to readable string
                return json_encode($data, JSON_PRETTY_PRINT);
            }
        }
    } catch (\Exception $e) {
        Log::warning('whois-api.com failed: ' . $e->getMessage());
    }

    return null;
}

/**
* Method 4: whois.whoisxmlapi.com (free tier)
*/
private function getWhoisFromApi3($domain)
{
    try {
        $response = Http::timeout(30)
            ->get('https://whois.whoisxmlapi.com/api/v1/whois', [
                'domainName' => $domain,
                'apiKey' => 'free',
                'outputFormat' => 'JSON',
            ]);

        if ($response->successful()) {
            $data = $response->json();
            if (!empty($data) && !isset($data['error'])) {
                return json_encode($data, JSON_PRETTY_PRINT);
            }
        }
    } catch (\Exception $e) {
        Log::warning('whoisxmlapi.com failed: ' . $e->getMessage());
    }

    return null;
}

/**
* Parse WHOIS output into structured data
*/
private function parseWhoisOutput($rawOutput)
{
    $parsed = [
        'Domain Name' => 'N/A',
        'Registrar' => 'N/A',
        'Creation Date' => 'N/A',
        'Expiry Date' => 'N/A',
        'Updated Date' => 'N/A',
        'Name Servers' => 'N/A',
        'Domain Status' => 'N/A',
        'Registrant' => 'N/A',
        'Registrant Email' => 'N/A',
        'Admin Email' => 'N/A',
        'Tech Email' => 'N/A',
        'DNSSEC' => 'N/A',
        'WHOIS Server' => 'N/A',
    ];

    $lines = explode("\n", $rawOutput);

    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line) || strpos($line, '#') === 0 || strpos($line, '%') === 0) {
            continue;
        }

        if (preg_match('/^Domain Name:\s*(.+)/i', $line, $m)) {
            $parsed['Domain Name'] = trim($m[1]);
        } elseif (preg_match('/^Registrar:\s*(.+)/i', $line, $m)) {
            $parsed['Registrar'] = trim($m[1]);
        } elseif (preg_match('/^(Creation Date|Created):\s*(.+)/i', $line, $m)) {
            $parsed['Creation Date'] = trim($m[2]);
        } elseif (preg_match('/^(Registry Expiry Date|Expiration Date|Expires):\s*(.+)/i', $line, $m)) {
            $parsed['Expiry Date'] = trim($m[2]);
        } elseif (preg_match('/^Updated Date:\s*(.+)/i', $line, $m)) {
            $parsed['Updated Date'] = trim($m[1]);
        } elseif (preg_match('/^Name Server:\s*(.+)/i', $line, $m)) {
            if ($parsed['Name Servers'] === 'N/A') {
                $parsed['Name Servers'] = trim($m[1]);
            } else {
                $parsed['Name Servers'] .= ', ' . trim($m[1]);
            }
        } elseif (preg_match('/^Domain Status:\s*(.+)/i', $line, $m)) {
            if ($parsed['Domain Status'] === 'N/A') {
                $parsed['Domain Status'] = trim($m[1]);
            } else {
                $parsed['Domain Status'] .= ', ' . trim($m[1]);
            }
        } elseif (preg_match('/^(Registrant|Registrant Name):\s*(.+)/i', $line, $m)) {
            $parsed['Registrant'] = trim($m[2]);
        } elseif (preg_match('/^Registrant Email:\s*(.+)/i', $line, $m)) {
            $parsed['Registrant Email'] = trim($m[1]);
        } elseif (preg_match('/^Admin Email:\s*(.+)/i', $line, $m)) {
            $parsed['Admin Email'] = trim($m[1]);
        } elseif (preg_match('/^Tech Email:\s*(.+)/i', $line, $m)) {
            $parsed['Tech Email'] = trim($m[1]);
        } elseif (preg_match('/^DNSSEC:\s*(.+)/i', $line, $m)) {
            $parsed['DNSSEC'] = trim($m[1]);
        } elseif (preg_match('/^Whois Server:\s*(.+)/i', $line, $m)) {
            $parsed['WHOIS Server'] = trim($m[1]);
        }
    }

    return $parsed;
}



/**
* CVE Lookup Tool (No API Key Required)
*/
public function cveLookup(Request $request)
{
    $query = null;
    $results = [];
    $error = null;
    $searchType = 'cve';

    if ($request->isMethod('post')) {
        $request->validate([
            'query' => 'required|string|max:255',
        ]);

        $query = $request->input('query');
        $searchType = $request->input('search_type', 'cve');
        $query = trim($query);

        $cacheKey = 'cve_' . md5($searchType . '_' . $query);
        $cached = Cache::get($cacheKey);

        if ($cached) {
            $results = $cached;
        } else {
            if ($searchType === 'cve') {
                // Search by CVE ID using CIRCL API (no key required)
                $results = $this->searchCveCircl($query);
               
                // If CIRCL fails, try local database
                if (!$results) {
                    $results = $this->getLocalCveData($query);
                }
            } else {
                // Keyword search using local data
                $results = $this->searchLocalCveByKeyword($query);
            }

            if ($results) {
                Cache::put($cacheKey, $results, 1440); // 24 hours
            } else {
                $error = 'Could not retrieve CVE data. Please try a different CVE ID or keyword.';
            }
        }
    }

    return view('student.cve-lookup', compact('query', 'results', 'error', 'searchType'));
}

/**
* Search CVE using CIRCL API (No API key, 100% free)
*/
private function searchCveCircl($cveId)
{
    try {
        $response = Http::timeout(15)
            ->withHeaders(['User-Agent' => 'CyberShield.lk Student Tool/1.0'])
            ->get('https://cve.circl.lu/api/cve/' . $cveId);

        if ($response->successful()) {
            $data = $response->json();
            if (!empty($data) && isset($data['id'])) {
                return [$this->formatCirclCveData($data)];
            }
        }
    } catch (\Exception $e) {
        \Log::warning('CIRCL API failed: ' . $e->getMessage());
    }

    return null;
}

/**
* Local CVE database (fallback when API fails)
*/
private function getLocalCveData($cveId)
{
    // Common CVEs for demonstration
    $cveDatabase = [
        'CVE-2021-44228' => [
            'id' => 'CVE-2021-44228',
            'description' => 'Apache Log4j2 JNDI features do not protect against attacker-controlled LDAP and other JNDI related endpoints. An attacker who can control log messages or log message parameters can execute arbitrary code loaded from LDAP servers when message lookup substitution is enabled.',
            'cvss_score' => 10.0,
            'severity' => 'Critical',
            'published_date' => '2021-12-10',
            'last_modified' => '2023-01-05',
            'references' => [
                ['url' => 'https://nvd.nist.gov/vuln/detail/CVE-2021-44228', 'source' => 'NVD'],
                ['url' => 'https://www.cisa.gov/known-exploited-vulnerabilities-catalog', 'source' => 'CISA'],
            ],
        ],
        'CVE-2017-5638' => [
            'id' => 'CVE-2017-5638',
            'description' => 'The Jakarta Multipart parser in Apache Struts 2 2.3.x before 2.3.32 and 2.5.x before 2.5.10.1 mishandles file upload, which allows remote attackers to execute arbitrary commands via a #cmd= string in a crafted Content-Type header.',
            'cvss_score' => 10.0,
            'severity' => 'Critical',
            'published_date' => '2017-03-10',
            'last_modified' => '2017-03-15',
            'references' => [
                ['url' => 'https://nvd.nist.gov/vuln/detail/CVE-2017-5638', 'source' => 'NVD'],
            ],
        ],
        'CVE-2022-22965' => [
            'id' => 'CVE-2022-22965',
            'description' => 'Spring Framework prior to versions 5.2.20 and 5.3.18 contains a vulnerability that allows a remote attacker to execute arbitrary code on the server via a crafted request.',
            'cvss_score' => 9.8,
            'severity' => 'Critical',
            'published_date' => '2022-04-01',
            'last_modified' => '2022-04-07',
            'references' => [
                ['url' => 'https://nvd.nist.gov/vuln/detail/CVE-2022-22965', 'source' => 'NVD'],
            ],
        ],
        'CVE-2014-0160' => [
            'id' => 'CVE-2014-0160',
            'description' => 'The TLS/DTLS Heartbeat extension in OpenSSL 1.0.1 before 1.0.1g allows remote attackers to read sensitive memory contents by sending a specially crafted heartbeat request.',
            'cvss_score' => 5.0,
            'severity' => 'Medium',
            'published_date' => '2014-04-07',
            'last_modified' => '2023-05-01',
            'references' => [
                ['url' => 'https://nvd.nist.gov/vuln/detail/CVE-2014-0160', 'source' => 'NVD'],
            ],
        ],
        'CVE-2023-3519' => [
            'id' => 'CVE-2023-3519',
            'description' => 'Unauthenticated remote code execution vulnerability in Citrix NetScaler ADC and NetScaler Gateway allows attackers to execute arbitrary code on the target system.',
            'cvss_score' => 9.8,
            'severity' => 'Critical',
            'published_date' => '2023-07-18',
            'last_modified' => '2023-07-25',
            'references' => [
                ['url' => 'https://nvd.nist.gov/vuln/detail/CVE-2023-3519', 'source' => 'NVD'],
            ],
        ],
    ];

    return isset($cveDatabase[$cveId]) ? [$cveDatabase[$cveId]] : null;
}

/**
* Keyword search using local database
*/
private function searchLocalCveByKeyword($keyword)
{
    $keyword = strtolower($keyword);
   
    // Map keywords to common CVEs
    $keywordMap = [
        'log4j' => ['CVE-2021-44228'],
        'log' => ['CVE-2021-44228'],
        'struts' => ['CVE-2017-5638'],
        'spring' => ['CVE-2022-22965'],
        'openssl' => ['CVE-2014-0160'],
        'heartbleed' => ['CVE-2014-0160'],
        'citrix' => ['CVE-2023-3519'],
        'netscaler' => ['CVE-2023-3519'],
    ];

    $cveIds = [];
    foreach ($keywordMap as $key => $ids) {
        if (str_contains($keyword, $key)) {
            $cveIds = array_merge($cveIds, $ids);
        }
    }

    if (empty($cveIds)) {
        return null;
    }

    $results = [];
    foreach ($cveIds as $cveId) {
        $cve = $this->getLocalCveData($cveId);
        if ($cve) {
            $results = array_merge($results, $cve);
        }
    }

    return !empty($results) ? $results : null;
}

/**
* Format CIRCL CVE data
*/
private function formatCirclCveData($data)
{
    $cvssScore = 0;
    $severity = 'Unknown';
    $severityColor = 'gray';

    if (isset($data['cvss'])) {
        if (isset($data['cvss']['cvss-v3'])) {
            $cvssScore = $data['cvss']['cvss-v3']['baseScore'] ?? 0;
            $severity = $this->getSeverityFromScore($cvssScore);
        } elseif (isset($data['cvss']['cvss-v2'])) {
            $cvssScore = $data['cvss']['cvss-v2']['baseScore'] ?? 0;
            $severity = $this->getSeverityFromScore($cvssScore);
        }
    }

    $severityColor = $this->getSeverityColor($severity);

    $references = [];
    if (isset($data['references'])) {
        foreach ($data['references'] as $ref) {
            $references[] = ['url' => $ref, 'source' => 'CIRCL'];
        }
    }

    return [
        'id' => $data['id'] ?? 'N/A',
        'description' => $data['summary'] ?? 'No description available.',
        'cvss_score' => $cvssScore,
        'severity' => $severity,
        'severity_color' => $severityColor,
        'published_date' => $data['Published'] ?? $data['published'] ?? 'N/A',
        'last_modified' => $data['Modified'] ?? $data['modified'] ?? 'N/A',
        'references' => $references,
        'source' => 'CIRCL API',
    ];
}

/**
* Get severity from CVSS score
*/
private function getSeverityFromScore($score)
{
    if ($score >= 9.0) return 'Critical';
    if ($score >= 7.0) return 'High';
    if ($score >= 4.0) return 'Medium';
    if ($score > 0) return 'Low';
    return 'Unknown';
}

/**
* Get severity color for display
*/
private function getSeverityColor($severity)
{
    $colors = [
        'Critical' => 'red',
        'High' => 'orange',
        'Medium' => 'yellow',
        'Low' => 'blue',
        'Unknown' => 'gray',
    ];
    return $colors[$severity] ?? 'gray';
}
}