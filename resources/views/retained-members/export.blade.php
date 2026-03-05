<table>
    <thead>
        <tr>
            <th colspan="11" style="text-align: center; font-size: 16px; font-weight: bold;">RETAINED MEMBERS DIRECTORY
            </th>
        </tr>
        <tr>
            <th style="font-weight: bold;">CATEGORY</th>
            <th style="font-weight: bold;">GROUP</th>
            <th style="font-weight: bold;">ENTITY</th>
            <th style="font-weight: bold;">NAME</th>
            <th style="font-weight: bold;">CONTACT</th>
            <th style="font-weight: bold;">GENDER</th>
            <th style="font-weight: bold; text-align: center;">VISITS</th>
            <th style="font-weight: bold;">FIRST VISIT</th>
            <th style="font-weight: bold;">JOINED</th>
            <th style="font-weight: bold;">BRINGER</th>
            <th style="font-weight: bold;">BRINGER CONTACT</th>
        </tr>
    </thead>
    <tbody>
        @foreach($groupedMembers as $categoryName => $groups)
            @foreach($groups as $groupName => $entities)
                @foreach($entities as $entityName => $members)
                    @foreach($members as $member)
                        <tr>
                            <td>{{ $categoryName }}</td>
                            <td>{{ $groupName }}</td>
                            <td>{{ $entityName }}</td>
                            <td>{{ $member->full_name }}</td>
                            <td>{{ $member->primary_contact }}</td>
                            <td>{{ $member->gender }}</td>
                            <td style="text-align: center;">{{ $member->service_count }}</td>
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