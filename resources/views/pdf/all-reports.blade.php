<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>CyberShield - All Reports</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            color: #333;
            padding: 20px;
            font-size: 11px;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #2563eb;
            font-size: 22px;
            margin: 0;
        }
        .header p {
            color: #666;
            margin: 5px 0 0 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th {
            background: #2563eb;
            color: white;
            padding: 8px 12px;
            text-align: left;
            font-size: 11px;
        }
        td {
            padding: 6px 12px;
            border-bottom: 1px solid #eee;
        }
        .badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 12px;
            font-size: 10px;
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
        .footer {
            text-align: center;
            color: #999;
            font-size: 10px;
            border-top: 1px solid #eee;
            padding-top: 15px;
            margin-top: 30px;
        }
        .summary {
            background: #f3f4f6;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>🛡️ CyberShield</h1>
        <p>Complete Security Report History</p>
    </div>

    <div class="summary">
        <p><strong>User:</strong> {{ $user->name }} ({{ $user->email }})</p>
        <p><strong>Generated:</strong> {{ $generated_at }}</p>
        <p><strong>Total Scans:</strong> {{ $total_scans }}</p>
    </div>

    @if($scans->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tool</th>
                    <th>Input</th>
                    <th>Result</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($scans as $scan)
                    <tr>
                        <td>{{ $scan->id }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $scan->tool_name)) }}</td>
                        <td> @if($scan->tool_name == 'password_analyzer')
                                 {{ maskPassword($scan->input_data) }} 
                              @else   
                                 {{ Str::limit($scan->input_data, 30) }}
                              @endif
                                </td>
                        <td>
                            @if($scan->tool_name == 'password_analyzer')
                                 
                                <span class="badge
                                    @if($scan->result_data['strength'] == 'Weak') badge-red
                                    @elseif($scan->result_data['strength'] == 'Medium') badge-yellow
                                    @else badge-green
                                    @endif">
                                    {{ $scan->result_data['strength'] }}
                                </span>
                            @elseif($scan->tool_name == 'url_checker')
                                <span class="badge
                                    @if($scan->result_data['risk_color'] == 'red') badge-red
                                    @elseif($scan->result_data['risk_color'] == 'yellow') badge-yellow
                                    @else badge-green
                                    @endif">
                                    {{ $scan->result_data['risk_level'] }}
                                </span>
                            @elseif($scan->tool_name == 'ssl_checker')
                                <span class="badge
                                    @if(in_array($scan->result_data['grade'], ['A+', 'A'])) badge-green
                                    @elseif($scan->result_data['grade'] == 'B') badge-yellow
                                    @else badge-red
                                    @endif">
                                    {{ $scan->result_data['grade'] }}
                                </span>
                            @else
                                <span class="badge badge-green">Done</span>
                            @endif
                        </td>
                        <td>{{ $scan->created_at->format('Y-m-d H:i') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>No scans found.</p>
    @endif

    <div class="footer">
        Generated by CyberShield | {{ $generated_at }}
        <br>
        This report contains all your security scan history.
    </div>

</body>
</html>