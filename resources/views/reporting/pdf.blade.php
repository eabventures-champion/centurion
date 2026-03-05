<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Weekly Reporting - {{ $weekStart->format('M d, Y') }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            margin: 0;
            color: #111;
            font-size: 24px;
        }

        .header p {
            margin: 5px 0 0;
            color: #666;
            font-size: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        th {
            background: #f3f4f6;
            color: #374151;
            font-weight: bold;
            text-align: left;
            padding: 10px;
            border: 1px solid #e5e7eb;
            font-size: 10px;
            text-transform: uppercase;
        }

        td {
            padding: 10px;
            border: 1px solid #e5e7eb;
            vertical-align: top;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #111;
            border-left: 4px solid #6366f1;
            padding-left: 10px;
        }

        .summary-grid {
            margin-bottom: 30px;
        }

        .summary-card {
            width: 30%;
            display: inline-block;
            border: 1px solid #e5e7eb;
            padding: 15px;
            border-radius: 8px;
            margin-right: 2%;
        }

        .summary-card h3 {
            margin: 0;
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
        }

        .summary-card p {
            margin: 5px 0 0;
            font-size: 20px;
            font-weight: bold;
            color: #111;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }

        .text-right {
            text-align: right;
        }

        .text-indigo {
            color: #4f46e5;
        }

        .text-emerald {
            color: #059669;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Weekly Reporting Overview</h1>
        <p>Week: {{ $weekStart->format('M d') }} - {{ $weekEnd->format('M d, Y') }}</p>
    </div>

    <div class="section-title">Church Group Summations</div>
    <table>
        <thead>
            <tr>
                <th>Church Group</th>
                <th>Focus</th>
                <th class="text-right">Total First Timers</th>
            </tr>
        </thead>
        <tbody>
            @foreach($groups as $group)
                <tr>
                    <td><strong>{{ $group->group_name }}</strong></td>
                    <td>{{ $group->is_pcf_focused ? 'PCF Focused' : 'Church Focused' }}</td>
                    <td class="text-right"><strong>{{ $group->grand_total }}</strong></td>
                </tr>
            @endforeach
            <tr style="background: #f9fafb;">
                <td colspan="2" class="text-right"><strong>Grand Total First Timers:</strong></td>
                <td class="text-right"><strong style="font-size: 14px;">{{ $totalFirstTimers }}</strong></td>
            </tr>
        </tbody>
    </table>

    <div class="section-title">PCF Detailed Breakdown</div>
    <table>
        <thead>
            <tr>
                <th>PCF Name</th>
                <th>Group</th>
                <th class="text-right">First Timers</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pcfs as $pcf)
                <tr>
                    <td>{{ $pcf->name }}<br><small style="color: #666;">Leader: {{ $pcf->leader_name }}</small></td>
                    <td>{{ $pcf->churchGroup->group_name }}</td>
                    <td class="text-right text-indigo"><strong>{{ $pcf->visitor_count }}</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title" style="border-left-color: #10b981;">Church Detailed Breakdown</div>
    <table>
        <thead>
            <tr>
                <th>Church Name</th>
                <th>Group</th>
                <th class="text-right">First Timers</th>
            </tr>
        </thead>
        <tbody>
            @foreach($churches as $church)
                <tr>
                    <td>{{ $church->name }}<br><small style="color: #666;">Leader: {{ $church->leader_name }}</small></td>
                    <td>{{ $church->churchGroup->group_name }}</td>
                    <td class="text-right text-emerald"><strong>{{ $church->visitor_count }}</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Generated on {{ now()->format('M d, Y H:i') }} | Centurion Campaign Management System
    </div>
</body>

</html>