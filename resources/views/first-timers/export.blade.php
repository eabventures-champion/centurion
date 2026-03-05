<table>
    <thead>
        <tr>
            <th colspan="11" style="text-align: center; font-size: 16px; font-weight: bold;">FIRST TIMERS REGISTER</th>
        </tr>
        <tr>
            <th style="font-weight: bold;">CATEGORY</th>
            <th style="font-weight: bold;">GROUP</th>
            <th style="font-weight: bold;">ENTITY</th>
            <th style="font-weight: bold;">NAME</th>
            <th style="font-weight: bold;">CONTACT</th>
            <th style="font-weight: bold;">GENDER</th>
            <th style="font-weight: bold;">STATUS</th>
            <th style="font-weight: bold; text-align: center;">VISITS</th>
            <th style="font-weight: bold;">FIRST VISIT</th>
            <th style="font-weight: bold;">BRINGER</th>
            <th style="font-weight: bold;">BRINGER CONTACT</th>
        </tr>
    </thead>
    <tbody>
        @foreach($groupedFirstTimers as $categoryName => $groups)
            @foreach($groups as $groupName => $entities)
                @foreach($entities as $entityName => $firstTimers)
                    @foreach($firstTimers as $ft)
                        <tr>
                            <td>{{ $categoryName }}</td>
                            <td>{{ $groupName }}</td>
                            <td>{{ $entityName }}</td>
                            <td>{{ $ft->full_name }}</td>
                            <td>{{ $ft->primary_contact }}</td>
                            <td>{{ $ft->gender }}</td>
                            <td>{{ $ft->locked ? 'Retained' : 'Pending' }}</td>
                            <td style="text-align: center;">{{ $ft->service_count }} / 3</td>
                            <td>{{ $ft->earliest_visit_date ? \Carbon\Carbon::parse($ft->earliest_visit_date)->format('M d, Y') : 'N/A' }}
                            </td>
                            <td>{{ $ft->bringer ? $ft->bringer->name : 'N/A' }}</td>
                            <td>{{ $ft->bringer ? $ft->bringer->contact : 'N/A' }}</td>
                        </tr>
                    @endforeach
                @endforeach
            @endforeach
        @endforeach
    </tbody>
</table>