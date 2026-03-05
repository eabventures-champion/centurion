<table>
    <thead>
        <tr>
            <th colspan="7" style="text-align: center; font-size: 16px; font-weight: bold;">PERFORMANCE REPORT
                ({{ strtoupper($entityType) }})</th>
        </tr>
        <tr>
            <th style="font-weight: bold;">ENTITY</th>
            <th style="font-weight: bold;">OFFICER</th>
            <th style="font-weight: bold; text-align: center;">BRINGERS</th>
            <th style="font-weight: bold; text-align: center;">TOTAL FT</th>
            <th style="font-weight: bold; text-align: center;">NEW FT</th>
            <th style="font-weight: bold; text-align: center;">RETAINED MEMBERS</th>
            <th style="font-weight: bold; text-align: right;">RETENTION %</th>
        </tr>
    </thead>
    <tbody>
        @foreach($hierarchy as $category)
            <tr>
                <td colspan="7" style="background-color: #f3f4f6; font-weight: bold;">{{ strtoupper($category['name']) }}
                    (AVG: {{ $category['avg_retention'] }}%)</td>
            </tr>
            @foreach($category['groups'] as $group)
                <tr>
                    <td colspan="7" style="background-color: #f9fafb; font-weight: bold; padding-left: 20px;">
                        {{ $group['name'] }} (Group: {{ $group['avg_retention'] }}%)</td>
                </tr>
                @foreach($group['entities'] as $entity)
                    <tr>
                        <td>{{ $entity['name'] }}</td>
                        <td>{{ $entity['officer'] }}</td>
                        <td style="text-align: center;">{{ $entity['bringers'] }}</td>
                        <td style="text-align: center;">{{ $entity['total_ft'] }}</td>
                        <td style="text-align: center;">{{ $entity['new_ft'] }}</td>
                        <td style="text-align: center;">{{ $entity['total_rm'] }}</td>
                        <td style="text-align: right; color: {{ $entity['retention_rate'] >= 50 ? '#10b981' : '#6366f1' }};">
                            {{ $entity['retention_rate'] }}%
                        </td>
                    </tr>
                @endforeach
            @endforeach
        @endforeach
    </tbody>
</table>