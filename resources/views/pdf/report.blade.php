<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>CyberShield Security Report</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            color: #333;
            padding: 20px;
            font-size: 12px;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #2563eb;
            font-size: 24px;
            margin: 0;
        }
        .header h1 span {
            color: #1e40af;
        }
        .header p {
            color: #666;
            margin: 5px 0 0 0;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            background: #2563eb;
            color: white;
            padding: 8px 15px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .row {
            display: flex;
            padding: 5px 0;
            border-bottom: 1px solid #eee;
        }
        .row-label {
            width: 150px;
            font-weight: bold;
            color: #555;
        }
        .row-value {
            flex: 1;
            word-wrap: break-word;
        }
        .risk-high {
            color: #dc2626;
            font-weight: bold;
        }
        .risk-medium {
            color: #ca8a04;
            font-weight: bold;
        }
        .risk-low {
            color: #16a34a;
            font-weight: bold;
        }
        .badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
        }
        .badge-red {
            background: #fee2e2;
            color: #dc2626;
        }
        .badge-yellow {
            background: #fef9c3;
            color: #ca8a04;
        }
        .badge-green {
            background: #dcfce7;
            color: #16a34a;
        }
        .badge-gray {
            background: #f3f4f6;
            color: #6b7280;
        }
        .footer {
            text-align: center;
            color: #999;
            font-size: 10px;
            border-top: 1px solid #eee;
            padding-top: 15px;
            margin-top: 30px;
        }
        .detection-item {
            padding: 4px 0;
            color: #dc2626;
            font-size: 11px;
        }
        .warning-item {
            padding: 4px 0;
            color: #ca8a04;
            font-size: 11px;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <div class="header">
        <h1>🛡️ CyberShield</h1>
        <p>Security Report - {{ ucfirst(str_replace('_', ' ', $scan->tool_name)) }}</p>
    </div>

    <!-- Report Info -->
    <div class="section">
        <div class="section-title"> Report Information</div>
        <div class="row">
            <span class="row-label">Report ID:</span>
            <span class="row-value">#{{ $scan->id }}</span>
        </div>
        <div class="row">
            <span class="row-label">Generated:</span>
            <span class="row-value">{{ $generated_at }}</span>
        </div>
        <div class="row">
            <span class="row-label">User:</span>
            <span class="row-value">{{ $user->name }} ({{ $user->email }})</span>
        </div>
        <div class="row">
            <span class="row-label">Tool:</span>
            <span class="row-value">{{ ucfirst(str_replace('_', ' ', $scan->tool_name)) }}</span>
        </div>
    </div>

    <!-- Input Data -->
    <div class="section">
        <div class="section-title"> Input Data</div>
        <div class="row">
            <span class="row-label">Input:</span>
            <span class="row-value">@if($scan->tool_name == 'password_analyzer')
                                  {{ maskPassword($scan->input_data) }}  
                               @else
                                  {{ Str::limit($scan->input_data, 30) }}
                                  @endif</span>
        </div>
    </div>

    <!-- Results -->
    <div class="section">
        <div class="section-title"> Results</div>

        @if($scan->tool_name == 'password_analyzer')
            <div class="row">
                <span class="row-label">Strength:</span>
                <span class="row-value">
                    @if($result['strength'] == 'Weak')
                        <span class="badge badge-red">Weak</span>
                    @elseif($result['strength'] == 'Medium')
                        <span class="badge badge-yellow">Medium</span>
                    @else
                        <span class="badge badge-green">Strong</span>
                    @endif
                </span>
            </div>
            <div class="row">
                <span class="row-label">Crack Time:</span>
                <span class="row-value">{{ $result['crack_time'] ?? 'N/A' }}</span>
            </div>
            <div class="row">
                <span class="row-label">Breach Status:</span>
                <span class="row-value">{!! $result['breach_status'] ?? 'Not checked' !!}</span>
            </div>

        @elseif($scan->tool_name == 'url_checker')
            <div class="row">
                <span class="row-label">URL:</span>
                <span class="row-value">{{ $result['normalized_url'] ?? $result['original_url'] }}</span>
            </div>
            <div class="row">
                <span class="row-label">Risk Level:</span>
                <span class="row-value">
                    <span class="badge
                        @if($result['risk_color'] == 'red') badge-red
                        @elseif($result['risk_color'] == 'yellow') badge-yellow
                        @else badge-green
                        @endif">
                        {{ $result['risk_level'] ?? 'Unknown' }}
                    </span>
                </span>
            </div>
            <div class="row">
                <span class="row-label">Is Malicious:</span>
                <span class="row-value">{{ ($result['is_malicious'] ?? false) ? 'Yes' : 'No' }}</span>
            </div>

            <!-- VirusTotal Results -->
            @if(isset($result['virustotal']))
                <div class="row" style="flex-direction: column; align-items: flex-start; background: #f8f9fa; padding: 8px; margin-top: 8px; border-radius: 4px;">
                    <span class="row-label" style="width: 100%;"> VirusTotal:</span>
                    <div style="width: 100%; padding-left: 10px;">
                        <div>Malicious: <strong>{{ $result['virustotal']['malicious'] ?? 0 }}</strong></div>
                        <div>Suspicious: <strong>{{ $result['virustotal']['suspicious'] ?? 0 }}</strong></div>
                        <div>Safe: <strong>{{ $result['virustotal']['harmless'] ?? 0 }}</strong></div>
                        <div>Status: @if($result['virustotal']['is_safe'] ?? false) ✅ Safe @else ⚠️ Threat Detected @endif</div>
                        @if(!empty($result['virustotal']['detections']))
                            <div style="margin-top: 4px; color: #dc2626;">
                                @foreach($result['virustotal']['detections'] as $detect)
                                    <div class="detection-item">• {{ $detect['engine'] }}: {{ $detect['result'] }}</div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Google Safe Browsing Results -->
            @if(isset($result['google_safe_browsing']))
                <div class="row" style="flex-direction: column; align-items: flex-start; background: #f8f9fa; padding: 8px; margin-top: 4px; border-radius: 4px;">
                    <span class="row-label" style="width: 100%;"> Google Safe Browsing:</span>
                    <div style="width: 100%; padding-left: 10px;">
                        <div>Status: @if(isset($result['google_safe_browsing']['is_safe']) && $result['google_safe_browsing']['is_safe']) ✅ Safe @else ⚠️ Threat Detected @endif</div>
                        @if(isset($result['google_safe_browsing']['message']))
                            <div>{{ $result['google_safe_browsing']['message'] }}</div>
                        @endif
                        @if(!empty($result['google_safe_browsing']['threats']))
                            <div style="margin-top: 4px; color: #dc2626;">
                                @foreach($result['google_safe_browsing']['threats'] as $threat)
                                    <div class="detection-item">• {{ $threat['threat_type'] ?? 'Unknown' }}</div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Summary Messages -->
            @if(!empty($result['messages']))
                <div class="row" style="flex-direction: column; align-items: flex-start; background: #e0f2fe; padding: 8px; margin-top: 4px; border-radius: 4px;">
                    <span class="row-label" style="width: 100%;">📋 Summary:</span>
                    <div style="width: 100%; padding-left: 10px;">
                        @foreach($result['messages'] as $message)
                            <div>• {{ $message }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

        @elseif($scan->tool_name == 'ssl_checker')
            <div class="row">
                <span class="row-label">Domain:</span>
                <span class="row-value">{{ $result['domain'] ?? 'N/A' }}</span>
            </div>
            <div class="row">
                <span class="row-label">SSL Grade:</span>
                <span class="row-value">
                    <span class="badge
                        @if(in_array($result['grade'] ?? 'F', ['A+', 'A'])) badge-green
                        @elseif(($result['grade'] ?? 'F') == 'B') badge-yellow
                        @else badge-red
                        @endif">
                        {{ $result['grade'] ?? 'F' }}
                    </span>
                </span>
            </div>
            <div class="row">
                <span class="row-label">SSL Status:</span>
                <span class="row-value">
                    @if(($result['has_ssl'] ?? false) && ($result['valid'] ?? false))
                         Valid
                    @else
                         Invalid / Not Found
                    @endif
                </span>
            </div>
            <div class="row">
                <span class="row-label">Protocol:</span>
                <span class="row-value">{{ $result['protocol_version'] ?? 'N/A' }}</span>
            </div>
            <div class="row">
                <span class="row-label">Cipher Suite:</span>
                <span class="row-value">{{ $result['cipher_suite'] ?? 'N/A' }}</span>
            </div>
            <div class="row">
                <span class="row-label">Subject:</span>
                <span class="row-value">{{ $result['subject'] ?? 'N/A' }}</span>
            </div>
            <div class="row">
                <span class="row-label">Issuer:</span>
                <span class="row-value">{{ $result['issuer'] ?? 'N/A' }}</span>
            </div>
            <div class="row">
                <span class="row-label">Expires:</span>
                <span class="row-value">{{ $result['expiry_date'] ?? 'N/A' }}</span>
            </div>
            <div class="row">
                <span class="row-label">Days Left:</span>
                <span class="row-value">
                    @if(isset($result['days_left']) && $result['days_left'] > 0)
                        {{ $result['days_left'] }} days
                    @elseif(isset($result['days_left']) && $result['days_left'] == 0)
                        Expires today!
                    @else
                        N/A
                    @endif
                </span>
            </div>

            <!-- Certificate Chain -->
            @if(!empty($result['certificate_chain']))
                <div class="row" style="flex-direction: column; align-items: flex-start; background: #f8f9fa; padding: 8px; margin-top: 4px; border-radius: 4px;">
                    <span class="row-label" style="width: 100%;">🔗 Certificate Chain:</span>
                    <div style="width: 100%; padding-left: 10px;">
                        @foreach($result['certificate_chain'] as $index => $cert)
                            <div>{{ $index == 0 ? '📜 Server' : ($index == 1 ? '🏢 Intermediate' : '🔗 Chain') }}: {{ $cert['subject'] ?? 'Unknown' }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Headers -->
            @if(!empty($result['headers']))
                <div class="row" style="flex-direction: column; align-items: flex-start; background: #f8f9fa; padding: 8px; margin-top: 4px; border-radius: 4px;">
                    <span class="row-label" style="width: 100%;">🛡️ Security Headers:</span>
                    <div style="width: 100%; padding-left: 10px;">
                        <div>HSTS: {{ ($result['headers']['hsts'] ?? false) ? '✅ Present' : '❌ Missing' }}</div>
                        <div>CSP: {{ ($result['headers']['csp'] ?? false) ? '✅ Present' : '❌ Missing' }}</div>
                        <div>X-Frame-Options: {{ ($result['headers']['x_frame_options'] ?? false) ? '✅ Present' : '❌ Missing' }}</div>
                        <div>X-Content-Type-Options: {{ ($result['headers']['x_content_type_options'] ?? false) ? '✅ Present' : '❌ Missing' }}</div>
                        <div>Referrer-Policy: {{ ($result['headers']['referrer_policy'] ?? false) ? '✅ Present' : '❌ Missing' }}</div>
                        <div>Permissions-Policy: {{ ($result['headers']['permissions_policy'] ?? false) ? '✅ Present' : '❌ Missing' }}</div>
                    </div>
                </div>
            @endif

            <!-- Warnings -->
            @if(!empty($result['warnings']))
                <div class="row" style="flex-direction: column; align-items: flex-start; background: #fef9c3; padding: 8px; margin-top: 4px; border-radius: 4px;">
                    <span class="row-label" style="width: 100%;">⚠️ Warnings:</span>
                    <div style="width: 100%; padding-left: 10px;">
                        @foreach($result['warnings'] as $warning)
                            <div class="warning-item">• {{ $warning }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

        @elseif($scan->tool_name == 'hash_tool')
            <div class="row">
                <span class="row-label">Algorithm:</span>
                <span class="row-value">{{ strtoupper($result['algorithm'] ?? 'N/A') }}</span>
            </div>
            <div class="row">
                <span class="row-label">Input:</span>
                <span class="row-value">{{ $result['text'] ?? $scan->input_data }}</span>
            </div>
            <div class="row">
                <span class="row-label">Hash:</span>
                <span class="row-value" style="font-size: 11px; word-break: break-all;">{{ $result['hash'] ?? $result['identified_type'] ?? 'N/A' }}</span>
            </div>
            @if(isset($result['identified_type']))
                <div class="row">
                    <span class="row-label">Identified Type:</span>
                    <span class="row-value">{{ $result['identified_type'] }}</span>
                </div>
            @endif

        @else
            <div class="row">
                <span class="row-label">Result:</span>
                <span class="row-value">{{ json_encode($result) }}</span>
            </div>
        @endif
    </div>

    <!-- Footer -->
    <div class="footer">
        Generated by CyberShield | {{ $generated_at }}
        <br>
        This report is for informational purposes only.
    </div>

</body>
</html>