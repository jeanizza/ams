<?php

namespace App\Exports;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Equipment;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class DisposalDetailsExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */

    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = DB::table('equipment')
            ->join('unserviceable', 'equipment.property_number', '=', 'unserviceable.property_number')
            ->join('disposal_value', 'equipment.property_number', '=', 'disposal_value.property_number')
            ->select(
                'equipment.property_type',
                'equipment.property_number',
                'equipment.particular',
                'equipment.description',
                'equipment.division',
                'equipment.amount',
                'equipment.po_number',
                'equipment.date_acquired',
                'equipment.date_end',
                'unserviceable.date_returned',
                'disposal_value.accumulated_depreciation',
                'disposal_value.carrying_value'
            );

        // Check if filters exist, otherwise return all data
        if (!empty($this->request->from_date) && !empty($this->request->to_date)) {
            $query->whereBetween('unserviceable.date_returned', [$this->request->from_date, $this->request->to_date]);
        }

        if (!empty($this->request->search)) {
            $search = $this->request->search;
            $query->where(function ($q) use ($search) {
                $q->where('equipment.property_number', 'LIKE', "%$search%")
                    ->orWhere('equipment.division', 'LIKE', "%$search%")
                    ->orWhere('equipment.particular', 'LIKE', "%$search%");
            });
        }

        // Debugging: Log the number of results
        $data = $query->get();
        \Log::info('Export Data Count: ' . $data->count());

        return $data;
    }

    public function headings(): array
    {
        return [
            'Property Type',
            'Property Number',
            'Particular',
            'Description',
            'Division',
            'Amount',
            'PO Number',
            'Date Acquired',
            'Date End',
            'Date Returned',
            'Accumulated Depreciation',
            'Carrying Value'
        ];
    }
}
