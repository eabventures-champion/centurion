<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class FirstTimersTemplateExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        return [];
    }

    public function headings(): array
    {
        return [
            'Full Name',
            'Email',
            'Primary Contact',
            'Alternate Contact',
            'Gender',
            'Date of Birth',
            'Residential Address',
            'Occupation',
            'Date of Visit',
            'Marital Status',
            'Born Again',
            'Water Baptism',
            'Prayer Requests',
            'Bringer Name',
            'Bringer Contact',
            'Bringer Senior Cell Name',
            'Bringer Cell Name',
        ];
    }
}
