<!DOCTYPE html>
<html>

<head>
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 10px;
            color: #334155;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #e2e8f0;
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #f8fafc;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8px;
            color: #64748b;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h2 {
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
            color: #1e293b;
        }

        .header p {
            color: #64748b;
            font-size: 8px;
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
    </style>
</head>

<body>
    <div class="header">
        <h2>{{ $title }}</h2>
        <p>Generated on {{ now()->format('M d, Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Contact</th>
                <th>Entity</th>
                <th class="text-center">First Timers</th>
                <th class="text-center">Retained</th>
                <th class="text-center">Total Souls</th>
            </tr>
        </thead>
        <tbody>
            @foreach($groupedBringers as $categoryName => $groups)
                <tr class="category-row">
                    <td colspan="6">{{ $categoryName }}</td>
                </tr>
                @foreach($groups as $groupName => $entities)
                    <tr class="group-row">
                        <td colspan="6" style="padding-left: 15px;">{{ $groupName }}</td>
                    </tr>
                    @foreach($entities as $entityName => $bringers)
                        @foreach($bringers as $bringer)
                            <tr>
                                <td style="padding-left: 20px; font-weight: bold;">{{ $bringer->name }}</td>
                                <td>{{ $bringer->contact }}</td>
                                <td>{{ $entityName }}</td>
                                <td class="text-center">{{ $bringer->first_timers_count }}</td>
                                <td class="text-center">{{ $bringer->retained_members_count }}</td>
                                <td class="text-center" style="font-weight: bold;">
                                    {{ $bringer->first_timers_count + $bringer->retained_members_count }}
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                @endforeach
            @endforeach
        </tbody>
    </table>
</body>

</html>