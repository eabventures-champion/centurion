<table>
    <thead>
        <tr>
            <th colspan="3" style="font-weight: bold; font-size: 14px; text-align: center;">Weekly Reporting Overview
            </th>
        </tr>
        <tr>
            <th colspan="3" style="text-align: center;">Week of {{ $weekStart->format('M d, Y') }} -
                {{ $weekEnd->format('M d, Y') }}
            </th>
        </tr>
        <tr>
            <th colspan="3"></th>
        </tr>
        <tr>
            <th style="font-weight: bold; background-color: #f3f4f6;">Church Group</th>
            <th style="font-weight: bold; background-color: #f3f4f6;">Category Focus</th>
            <th style="font-weight: bold; background-color: #f3f4f6; text-align: right;">Total First Timers</th>
        </tr>
    </thead>
    <tbody>
        @foreach($groups as $group)
            <tr>
                <td>{{ $group->group_name }}</td>
                <td>{{ $group->is_pcf_focused ? 'PCF Focused' : 'Church Focused' }}</td>
                <td style="text-align: right;">{{ $group->grand_total }}</td>
            </tr>
        @endforeach
        <tr>
            <td colspan="2" style="font-weight: bold; text-align: right;">Grand Total:</td>
            <td style="font-weight: bold; text-align: right;">{{ $totalFirstTimers }}</td>
        </tr>
        <tr>
            <th colspan="3"></th>
        </tr>
        <tr>
            <th colspan="3" style="font-weight: bold; background-color: #e5e7eb;">PCF Detailed Breakdown</th>
        </tr>
        <tr>
            <th style="font-weight: bold; background-color: #f9fafb;">PCF Name</th>
            <th style="font-weight: bold; background-color: #f9fafb;">Church Group</th>
            <th style="font-weight: bold; background-color: #f9fafb; text-align: right;">First Timers</th>
        </tr>
        @foreach($pcfs as $pcf)
            <tr>
                <td>{{ $pcf->name }}</td>
                <td>{{ $pcf->churchGroup->group_name }}</td>
                <td style="text-align: right;">{{ $pcf->visitor_count }}</td>
            </tr>
        @endforeach
        <tr>
            <th colspan="3"></th>
        </tr>
        <tr>
            <th colspan="3" style="font-weight: bold; background-color: #e5e7eb;">Church Detailed Breakdown</th>
        </tr>
        <tr>
            <th style="font-weight: bold; background-color: #f9fafb;">Church Name</th>
            <th style="font-weight: bold; background-color: #f9fafb;">Church Group</th>
            <th style="font-weight: bold; background-color: #f9fafb; text-align: right;">First Timers</th>
        </tr>
        @foreach($churches as $church)
            <tr>
                <td>{{ $church->name }}</td>
                <td>{{ $church->churchGroup->group_name }}</td>
                <td style="text-align: right;">{{ $church->visitor_count }}</td>
            </tr>
        @endforeach
    </tbody>
</table>