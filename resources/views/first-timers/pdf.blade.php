<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; color: #334155; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #e2e8f0; padding: 6px; text-align: left; }
        th { background-color: #f8fafc; font-weight: bold; text-transform: uppercase; font-size: 8px; color: #64748b; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px; color: #1e293b; }
        .header p { color: #64748b; font-size: 8px; }
        .category-row { background-color: #f1f5f9; font-weight: bold; }
        .group-row { background-color: #f8fafc; font-weight: bold; color: #475569; }
        .text-center { text-align: center; }
        .status-retained { color: #10b981; font-weight: bold; }
        .status-pending { color: #f59e0b; font-weight: bold; }
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
                <th>Status</th>
                <th class="text-center">Visits</th>
                <th>First Visit</th>
                <th>Bringer</th>
                <th>Bringer Contact</th>
            </tr>
        </thead>
        <tbody>
            @foreach($groupedFirstTimers as $categoryName => $groups)
                <tr class="category-row">
                    <td colspan="9">{{ $categoryName }}</td>
                </tr>
                @foreach($groups as $groupName => $entities)
                    <tr class="group-row">
                        <td colspan="9" style="padding-left: 15px;">{{ $groupName }}</td>
                    </tr>
                    @foreach($entities as $entityName => $firstTimers)
                        @foreach($firstTimers as $ft)
                            <tr>
                                <td style="padding-left: 20px; font-weight: bold;">{{ $ft->full_name }}</td>
                                <td>{{ $ft->primary_contact }}</td>
                                <td>{{ $ft->gender }}</td>
                                <td>{{ $entityName }}</td>
                                <td class="{{ $ft->locked ? 'status-retained' : 'status-pending' }}">
                                    {{ $ft->locked ? 'Retained' : 'Pending' }}
                                </td>
                                <td class="text-center">{{ $ft->service_count }} / 3</td>
                                <td>{{ $ft->earliest_visit_date ? \Carbon\Carbon::parse($ft->earliest_visit_date)->format('M d, Y') : 'N/A' }}</td>
                                <td>{{ $ft->bringer ? $ft->bringer->name : 'N/A' }}</td>
                                <td>{{ $ft->bringer ? $ft->bringer->contact : 'N/A' }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                @endforeach
            @endforeach
        </tbody>
    </table>
</body>
</html>
