<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 

use App\Models\Equipment;
use App\Models\Reconcile;

use DB;
use Carbon\Carbon;

class FinanceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $division = $user->div_name; // Make sure to fetch the correct division
        $equipmentItems = $this->fetchEquipmentNearEnd($user->office, $division);
        $equipmentCount = $equipmentItems->count();

        return view('finance.dashboard', compact('user', 'equipmentItems', 'equipmentCount'));
    }

    public function reconcileItems(Request $request)
    {
        $user = Auth::user();
        $office = $user->office;
        $search = $request->input('search');
        $year = $request->input('year');
        $remarks = $request->input('remarks');

        // Fetching default items from the equipment table
        $equipmentQuery = Equipment::where('office', $office)
            ->where('status', 'serviceable')
            ->whereNotIn('property_number', function ($query) {
                $query->select('property_number')
                    ->from('reconcile');
            });

        if ($search) {
            $equipmentQuery->where(function ($q) use ($search) {
                $q->where('property_number', 'like', "%$search%")
                ->orWhere('particular', 'like', "%$search%")
                ->orWhere('description', 'like', "%$search%");
            });
        }

        $items = $equipmentQuery->orderBy('date_acquired', 'desc')->paginate(20);

        // Fetching years for the dropdown
        $years = Equipment::select(DB::raw('YEAR(date_acquired) as year'))
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        // Reconcile query with filters
        $reconcileQuery = Reconcile::query()
            ->join('equipment', 'reconcile.property_number', '=', 'equipment.property_number')
            ->select('reconcile.*', 'equipment.particular' ,'equipment.description', 'equipment.office', 'equipment.division')
            ->where('equipment.office', $office);

        if ($year) {
            $reconcileQuery->whereYear('reconcile.date_acquired', $year);
        }

        if ($remarks) {
            $reconcileQuery->where('reconcile.remarks_reconcile', $remarks);
        }

        if ($search) {
            $reconcileQuery->where(function ($q) use ($search) {
                $q->where('reconcile.property_number', 'like', "%$search%")
                ->orWhere('equipment.description', 'like', "%$search%");
            });
        }

        $reconcileItems = $reconcileQuery->orderBy('reconcile.date_acquired', 'desc')->paginate(20);
        $reconcileTotalAmountAll = Reconcile::join('equipment', 'reconcile.property_number', '=', 'equipment.property_number')
            ->where('equipment.office', $office)
            ->sum('reconcile.amount');
        $reconcileTotalAmountYear = ($year || $remarks) ? $reconcileQuery->sum('reconcile.amount') : 0;

        // Calculate totals for all Semi-expendable and PPE
        $totalAmountSemiExpendable = Reconcile::join('equipment', 'reconcile.property_number', '=', 'equipment.property_number')
            ->where('equipment.office', $office)
            ->where('reconcile.remarks_reconcile', 'Semi-expendable')
            ->sum('reconcile.amount');
        $totalAmountPPE = Reconcile::join('equipment', 'reconcile.property_number', '=', 'equipment.property_number')
            ->where('equipment.office', $office)
            ->where('reconcile.remarks_reconcile', 'PPE')
            ->sum('reconcile.amount');

        return view('finance.reconcile.reconcile_items', compact(
            'items',
            'years',
            'year',
            'remarks',
            'reconcileItems',
            'reconcileTotalAmountAll',
            'reconcileTotalAmountYear',
            'totalAmountSemiExpendable',
            'totalAmountPPE',
            'search'
        ));
    }

    public function updateReconcile(Request $request)
    {
        $request->validate([
            'remarks' => 'required',
            'reconcile_id' => 'required|exists:reconcile,reconcile_id',
            'po_number' => 'required',
            'amount' => 'required|numeric',
            'date_acquired' => 'required|date',
            'user' => 'required'
        ]);
    
        $reconcileId = trim($request->input('reconcile_id'));
    
        DB::table('reconcile')
            ->where('reconcile_id', $reconcileId)
            ->update(['remarks_reconcile' => $request->input('remarks')]);
    
        return redirect()->route('finance.reconcile_items')->with('success', 'Remarks updated successfully.');
    }

    public function addReconcile(Request $request)
    {
        $request->validate([
            'remarks' => 'required',
            'property_number' => 'required',
            'po_number' => 'required',
            'amount' => 'required|numeric',
            'date_acquired' => 'required|date',
            'user' => 'required'
        ]);

        $reconcile = new Reconcile();
        $reconcile->property_number = $request->property_number;
        $reconcile->remarks_reconcile = $request->remarks;
        $reconcile->po_number = $request->po_number;
        $reconcile->amount = $request->amount;
        $reconcile->date_acquired = $request->date_acquired;
        $reconcile->user = $request->user;
        $reconcile->date_created = now();
        $reconcile->save();

        return redirect()->route('finance.reconcile_items')->with('success', 'Item added to reconcile successfully.');
    }

    public function equipmentNearEnd(Request $request)
    {
        $user = Auth::user();
        $division = $user->div_name; 
        $equipmentItems = $this->fetchEquipmentNearEnd($user->office, $division);

        return view('finance.dashboard.equipment_near_end', compact('equipmentItems'));
    }

    private function fetchEquipmentNearEnd($office, $division)
    {
        $dateFrom = Carbon::now();
        $dateTo = Carbon::now()->addDays(5);

        // Fetch equipment items matching the user's office and division, and date_end within 5 days from today
        $equipmentItems = DB::table('equipment')
            ->where('office', $office)
            ->where('division', $division)
            ->where(function($query) use ($dateFrom, $dateTo) {
                $query->whereBetween('date_end', [$dateFrom, $dateTo])
                      ->orWhere('date_end', '<', $dateFrom);
            })
            ->where('status', 'serviceable')
            ->get();

        foreach ($equipmentItems as $item) {
            $item->remarks = 'For Update';
        }

        return $equipmentItems;
    }
}
