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
                <th>Gender</th>
                <th>Entity</th>
                <th class="text-center">Visits</th>
                <th>First Visit</th>
                <th>Joined</th>
                <th>Bringer</th>
                <th>Bringer Contact</th>
            </tr>
        </thead>
        <tbody>
            @foreach($groupedMembers as $categoryName => $groups)
                <tr class="category-row">
                    <td colspan="9">{{ $categoryName }}</td>
                </tr>
                @foreach($groups as $groupName => $entities)
                    <tr class="group-row">
                        <td colspan="9" style="padding-left: 15px;">{{ $groupName }}</td>
                    </tr>
                    @foreach($entities as $entityName => $members)
                        @foreach($members as $member)
                            <tr>
                                <td style="padding-left: 20px; font-weight: bold;">{{ $member->full_name }}</td>
                                <td>{{ $member->primary_contact }}</td>
                                <td>{{ $member->gender }}</td>
                                <td>{{ $entityName }}</td>
                                <td class="text-center">{{ $member->service_count }}</td>
                                <td>{{ $member->global_first_visit ? \Carbon\Carbon::parse($member->global_first_visit)->format('M d, Y') : 'N/A' }}
                                </td>
                                <td>{{ $member->retained_date ? \Carbon\Carbon::parse($member->retained_date)->format('M d, Y') : 'N/A' }}
                                </td>
                                <td>{{ $member->bringer ? $member->bringer->name : 'N/A' }}</td>
                                <td>{{ $member->bringer ? $member->bringer->contact : 'N/A' }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                @endforeach
            @endforeach
        </tbody>
    </table>
</body>

</html>