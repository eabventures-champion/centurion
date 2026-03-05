<!DOCTYPE html>
<html>

<head>
    <title>Performance Report</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 11px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #e2e8f0;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f8fafc;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9px;
            color: #64748b;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h2 {
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }

        .header p {
            color: #64748b;
            font-size: 9px;
        }

        .category-row {
            background-color: #f1f5f9;
            font-weight: bold;
        }

        .group-row {
            background-color: #f8fafc;
            font-weight: bold;
            color: #475569;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .success {
            color: #10b981;
        }

        .primary {
            color: #6366f1;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>Performance Overview ({{ $entityType }})</h2>
        <p>Generated on {{ now()->format('M d, Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Entity</th>
                <th>Officer</th>
                <th class="text-center">Bringers</th>
                <th class="text-center">Total FT</th>
                <th class="text-center">New FT</th>
                <th class="text-center">Retained</th>
                <th class="text-right">Retention %</th>
            </tr>
        </thead>
        <tbody>
            @foreach($hierarchy as $category)
                <tr class="category-row">
                    <td colspan="7">{{ strtoupper($category['name']) }} (AVG: {{ $category['avg_retention'] }}%)</td>
                </tr>
                @foreach($category['groups'] as $group)
                    <tr class="group-row">
                        <td colspan="7" style="padding-left: 15px;">{{ $group['name'] }} (Group: {{ $group['avg_retention'] }}%)
                        </td>
                    </tr>
                    @foreach($group['entities'] as $entity)
                        <tr>
                            <td style="padding-left: 25px;">{{ $entity['name'] }}</td>
                            <td>{{ $entity['officer'] }}</td>
                            <td class="text-center">{{ $entity['bringers'] }}</td>
                            <td class="text-center">{{ $entity['total_ft'] }}</td>
                            <td class="text-center">{{ $entity['new_ft'] }}</td>
                            <td class="text-center">{{ $entity['total_rm'] }}</td>
                            <td class="text-right {{ $entity['retention_rate'] >= 50 ? 'success' : 'primary' }}"
                                style="font-weight: bold;">
                                {{ $entity['retention_rate'] }}%
                            </td>
                        </tr>
                    @endforeach
                @endforeach
            @endforeach
        </tbody>
    </table>
</body>

</html>