<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Models\Equipment;
use App\Models\Reconcile;

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
        $status = $request->input('status'); // New: Filter for reconciled/for reconcile

        // Base query for items
        $allItemsQuery = Equipment::leftJoin('reconcile', 'equipment.property_number', '=', 'reconcile.property_number')
            ->select(
                'equipment.*', 
                'reconcile.remarks_reconcile as reconcile_status',
                'reconcile.amount as reconcile_amount'
            )
            ->where('equipment.office', $office);

        // Filter by year
        if ($year) {
            $allItemsQuery->whereYear('equipment.date_acquired', $year);
        }

        // Filter by remarks (Semi-expendable, PPE)
        if ($remarks && $remarks !== 'All') {
            $allItemsQuery->where('reconcile.remarks_reconcile', $remarks);
        }

        // Filter by Reconcile Status
        if ($status === 'reconciled') {
            $allItemsQuery->whereNotNull('reconcile.remarks_reconcile'); // Only reconciled items
        } elseif ($status === 'for_reconcile') {
            $allItemsQuery->whereNull('reconcile.remarks_reconcile'); // Only items needing reconciliation
        }

        // Search by Property Number or Particular
        if ($search) {
            $allItemsQuery->where(function ($q) use ($search) {
                $q->where('equipment.property_number', 'like', "%$search%")
                ->orWhere('equipment.particular', 'like', "%$search%")
                ->orWhere('equipment.description', 'like', "%$search%");
            });
        }

        // Paginate results
        $allItems = $allItemsQuery->orderBy('equipment.date_acquired', 'desc')->paginate(20);

        // Fetch available years
        $years = Equipment::select(DB::raw('YEAR(date_acquired) as year'))
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        // Total Amount Calculations (Optimized)
        $totalAmountSemiExpendable = Reconcile::where('remarks_reconcile', 'Semi-expendable')->sum('amount');
        $totalAmountPPE = Reconcile::where('remarks_reconcile', 'PPE')->sum('amount');

        // Total Amount Filtered by Year (if selected)
        $reconcileTotalAmountYear = $year ? $allItemsQuery->sum('reconcile.amount') : 0;

        return view('finance.reconcile.reconcile_items', compact(
            'years',
            'year',
            'remarks',
            'status',  // New: Pass status to view
            'allItems',
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

    public function addCarrying()
    {
        // Get all property numbers that are already in the carrying_value table
        $existingPropertyNumbers = DB::table('carrying_value')->pluck('property_number')->toArray();

        // Fetch data from the unserviceable table where the property_number is not in the carrying_value table
        $unserviceableRecords = DB::table('unserviceable')
            ->whereNotIn('property_number', $existingPropertyNumbers)
            ->where('status', 'Acted')
            ->paginate(20);

        // Return the view with the fetched data
        return view('finance.reconcile.add_carrying_value', compact('unserviceableRecords'));
    }

    public function storeCarrying(Request $request)
    {
        $request->validate([
            'carrying_value' => 'required|numeric',
            'property_number' => 'required|string',
            'unserviceable_id' => 'required|integer',
        ]);

        DB::table('carrying_value')->insert([
            'property_number' => $request->input('property_number'),
            'carrying_value' => $request->input('carrying_value'),
            'unserviceable_id' => $request->input('unserviceable_id'),
        ]);

        return redirect()->route('finance.add_carrying_value')->with('success', 'Carrying value added successfully.');
    }

    public function storeCarryingValue(Request $request)
    {
        try {
            Log::info("Incoming Request Data", ['data' => $request->all()]); 

            // Validate input
            $validated = $request->validate([
                'selected_items' => 'required|array',
                'selected_items.*' => 'exists:unserviceable,unserviceable_id',
                'accumulated_depreciation' => 'sometimes|array',
                'accumulated_depreciation.*' => 'nullable|numeric',
                'carrying_values' => 'sometimes|array',
                'carrying_values.*' => 'nullable|numeric',
            ]);

            DB::beginTransaction();

            foreach ($validated['selected_items'] as $unserviceable_id) {
                $unserviceableRecord = DB::table('unserviceable')
                    ->select('unserviceable_id', 'property_number', 'amount')
                    ->where('unserviceable_id', $unserviceable_id)
                    ->first();

                if ($unserviceableRecord) {
                    DB::table('disposal_value')->insert([
                        'unserviceable_id' => $unserviceableRecord->unserviceable_id,
                        'property_number' => $unserviceableRecord->property_number,
                        'amount' => $unserviceableRecord->amount,
                        'accumulated_depreciation' => $validated['accumulated_depreciation'][$unserviceable_id] ?? 0,
                        'carrying_value' => ($unserviceableRecord->amount >= 50000) 
                            ? ($validated['carrying_values'][$unserviceable_id] ?? null) 
                            : null,
                        'date_created' => now(),
                    ]);
                } else {
                    Log::warning("Unserviceable record not found for ID: $unserviceable_id");
                }
            }

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Disposal values added successfully.']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error storing disposal value: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Something went wrong!'], 500);
        }
    }



    public function addCarryingValue(Request $request)
    {
        $existingPropertyNumbers = DB::table('disposal_value')
            ->where('amount', '>=', 50000)
            ->pluck('property_number')
            ->filter()
            ->toArray();
    
        $query = DB::table('unserviceable')
            ->whereNotIn('property_number', $existingPropertyNumbers)
            ->where('amount', '>=', 50000); // Only show amounts >= 50000
    
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('property_number', 'like', "%{$request->search}%")
                  ->orWhere('particular', 'like', "%{$request->search}%");
            });
        }
    
        if ($request->filled('from_date') && $request->filled('to_date')) {
            if (strtotime($request->from_date) && strtotime($request->to_date)) {
                $query->whereBetween('date_returned', [$request->from_date, $request->to_date]);
            }
        }
    
        $unserviceableRecords = $query->paginate(20);
    
        $recordsWithDepreciation = $unserviceableRecords->map(function ($record) {
            $amount = (float) $record->amount ?? 0;
            $lifespan = (int) $record->lifespan ?? 1;
            $dateAcquired = \Carbon\Carbon::parse($record->date_acquired);
            $dateReturned = \Carbon\Carbon::parse($record->date_returned);
    
            // Calculate depreciation
            $diff = $dateAcquired->diff($dateReturned);
            $yearsDiff = $diff->y;
            $monthsDiff = $diff->m;
            $daysDiff = $diff->d;
    
            $salvageValue = $amount * 0.05;
            $depreciableAmount = $amount - $salvageValue;
    
            $annualDepreciation = $depreciableAmount / $lifespan;
            $monthlyDepreciation = $annualDepreciation / 12;
            $dailyDepreciation = $monthlyDepreciation / 30;
    
            $accumulatedDepreciation = ($annualDepreciation * $yearsDiff) + ($monthlyDepreciation * $monthsDiff) + ($dailyDepreciation * $daysDiff);
            $carryingValue = $amount - $accumulatedDepreciation;
    
            return [
                'unserviceable_id' => $record->unserviceable_id,
                'property_number' => $record->property_number,
                'particular' => $record->particular,
                'amount' => number_format($amount, 2, '.', ''),
                'lifespan' => $record->lifespan,
                'date_acquired' => $record->date_acquired,
                'date_end' => $record->date_end,
                'date_returned' => $record->date_returned,
                'years_months_days' => "{$yearsDiff} years, {$monthsDiff} months, {$daysDiff} days",
                'accumulated_depreciation' => number_format($accumulatedDepreciation, 2, '.', ''),
                'carrying_value' => number_format($carryingValue, 2, '.', ''),
            ];
        });
    
        if ($request->ajax()) {
            return response()->json([
                'records' => $recordsWithDepreciation,
                'pagination' => (string) $unserviceableRecords->links(),
            ]);
        }
    
        return view('finance.reconcile.add_carrying_value', [
            'unserviceableRecords' => $unserviceableRecords,
            'recordsWithDepreciation' => $recordsWithDepreciation,
        ]);
    }



    public function disposalDetails()
    {
        // Fetch data from the equipment, disposal_value, and carrying_value tables
        $disposalDetails = DB::table('equipment')
            ->join('disposal_value', 'equipment.property_number', '=', 'disposal_value.property_number')
            ->join('carrying_value', 'equipment.property_number', '=', 'carrying_value.property_number')
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
                'disposal_value.disposal_value',
                'carrying_value.carrying_value'
            )
            ->get();

        // Return the view with the fetched data
        return view('finance.disposal_details', compact('disposalDetails'));
    }
}
