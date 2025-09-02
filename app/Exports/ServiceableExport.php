<?php

namespace App\Exports;

use App\Models\Equipment;
use Maatwebsite\Excel\Concerns\FromCollection;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithHeadings;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class ServiceableExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data->map(function ($item, $index) {
            return [
                'No.' => $index + 1,
                'ID' => $item->equipment_id,
                'Property Number' => $item->property_number,
                'Particular' => $item->particular,
                'Description' => $item->description,
                'Office' => $item->office,
                'End User' => $item->end_user,
                'Division' => $item->division,
                'Section' => $item->section,
                'Amount' => number_format((float)$item->amount, 2, '.', ','),
            ];
        });
    }

    public function headings(): array
    {
        return ['No.', 'ID', 'Property Number', 'Particular', 'Description', 'Office', 'End User', 'Division', 'Section', 'Amount'];
    }

}
