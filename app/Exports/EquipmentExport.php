<?php

namespace App\Exports;

use App\Models\Equipment;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EquipmentExport implements FromCollection, WithHeadings
{
    protected $equipmentItems;

    public function __construct($equipmentItems)
    {
        $this->equipmentItems = $equipmentItems;
    }

    public function collection()
    {
        return collect($this->equipmentItems)->map(function ($item) {
            return [
                'Property Number' => $item->property_number,
                'Particular' => $item->particular,
                'Description' => $item->description,
                'Division' => $item->division,
                'Section' => $item->section,
                'Date Acquired' => $item->date_acquired,
                'Lifespan' => $item->lifespan,
                'Date End' => $item->date_end,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Property Number', 'Particular', 'Description', 'Division',
            'Section', 'Date Acquired', 'Lifespan', 'Date End'
        ];
    }
}
