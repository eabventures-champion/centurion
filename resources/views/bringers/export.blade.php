<table>
    <thead>
        <tr>
            <th colspan="7" style="text-align: center; font-size: 16px; font-weight: bold;">BRINGERS DIRECTORY</th>
        </tr>
        <tr>
            <th style="font-weight: bold;">CATEGORY</th>
            <th style="font-weight: bold;">GROUP</th>
            <th style="font-weight: bold;">ENTITY</th>
            <th style="font-weight: bold;">NAME</th>
            <th style="font-weight: bold;">CONTACT</th>
            <th style="font-weight: bold; text-align: center;">FIRST TIMERS</th>
            <th style="font-weight: bold; text-align: center;">RETAINED</th>
        </tr>
    </thead>
    <tbody>
        @foreach($groupedBringers as $categoryName => $groups)
            @foreach($groups as $groupName => $entities)
                @foreach($entities as $entityName => $bringers)
                    @foreach($bringers as $bringer)
                        <tr>
                            <td>{{ $categoryName }}</td>
                            <td>{{ $groupName }}</td>
                            <td>{{ $entityName }}</td>
                            <td>{{ $bringer->name }}</td>
                            <td>{{ $bringer->contact }}</td>
                            <td style="text-align: center;">{{ $bringer->first_timers_count }}</td>
                            <td style="text-align: center;">{{ $bringer->retained_members_count }}</td>
                        </tr>
                    @endforeach
                @endforeach
            @endforeach
        @endforeach
    </tbody>
</table>