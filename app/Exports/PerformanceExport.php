<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class PerformanceExport implements FromView, ShouldAutoSize, WithTitle
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('performance.export', [
            'hierarchy' => $this->data['hierarchy'],
            'entityType' => $this->data['entityType'],
            'labels' => $this->data['labels']
        ]);
    }

    public function title(): string
    {
        return 'Performance Report - ' . strtoupper($this->data['entityType']);
    }
}
