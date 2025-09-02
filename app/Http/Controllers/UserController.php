<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

use App\Models\JobRequest;
use App\Models\ComplaintDefect;
use App\Models\Equipment;
use App\Models\Unserviceable;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ServiceableExport;


class UserController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $division = $user->div_name; 

        // Get property numbers that are 'Pending' or 'Approved' in request tables
        $excludedPropertyNumbers = DB::table('request_update')
            ->whereIn('status', ['Pending', 'Approved']) // Exclude both Pending and Approved
            ->pluck('property_number')
            ->merge(
                DB::table('request_transfer')
                    ->whereIn('status', ['Pending', 'Approved'])
                    ->pluck('property_number')
            )
            ->merge(
                DB::table('request_unserviceable')
                    ->whereIn('status', ['Pending', 'Approved'])
                    ->pluck('property_number')
            )
            ->unique()
            ->toArray();

        // Base query: Fetch equipment that is "serviceable" and near expiration
        $query = DB::table('equipment')
            ->where('office', $user->office)
            ->where('division', $division)
            ->where('status', 'serviceable')
            ->where(function ($query) {
                $query->where('date_end', '<', Carbon::now()) // Already expired
                    ->orWhereBetween('date_end', [Carbon::now(), Carbon::now()->addDays(15)]); // Expiring soon
            })
            ->whereNotIn('property_number', $excludedPropertyNumbers); // Exclude Pending and Approved requests

        // Apply live search filter
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('property_number', 'like', "%$search%")
                ->orWhere('particular', 'like', "%$search%")
                ->orWhere('end_user', 'like', "%$search%");
            });
        }

        // If the request is AJAX, return JSON response (for live search)
        if ($request->ajax()) {
            return response()->json($query->orderBy('date_end', 'ASC')->get());
        }

        // Normal page load (return view with paginated results)
        $equipmentItems = $query->orderBy('date_end', 'ASC')->paginate(20);
        $equipmentCount = $equipmentItems->total();

        return view('user.dashboard', compact('user', 'equipmentItems', 'equipmentCount'));
    }





    public function equipmentNearEnd(Request $request)
    {
        $user = Auth::user();
        $division = $user->div_name; 
        $equipmentItems = $this->fetchEquipmentNearEnd($user->office, $division);

        return view('user.dashboard.equipment_near_end', compact('equipmentItems'));
    }

    private function fetchEquipmentNearEnd($office, $division)
    {
        $dateFrom = Carbon::now();
        $dateTo = Carbon::now()->addDays(15);
        $fiveYearsAgo = Carbon::now()->subYears(5);

        // Get property numbers already present in request tables within the last 5 years
        $excludedPropertyNumbers = DB::table('request_update')
            ->whereBetween('date_created', [$fiveYearsAgo, $dateFrom])
            ->pluck('property_number')
            ->merge(
                DB::table('request_transfer')
                    ->whereBetween('date_created', [$fiveYearsAgo, $dateFrom])
                    ->pluck('property_number')
            )
            ->merge(
                DB::table('request_unserviceable')
                    ->whereBetween('date_created', [$fiveYearsAgo, $dateFrom])
                    ->pluck('property_number')
            )
            ->unique()
            ->toArray();

        // Fetch equipment items matching conditions, excluding already requested property_numbers
        $equipmentItems = DB::table('equipment')
            ->where('office', $office)
            ->where('division', $division)
            ->where(function ($query) use ($dateFrom, $dateTo) {
                $query->whereBetween('date_end', [$dateFrom, $dateTo])
                    ->orWhere('date_end', '<', $dateFrom);
            })
            ->where('status', 'serviceable')
            ->whereNotIn('property_number', $excludedPropertyNumbers) // Exclude already requested items
            ->orderBy('date_end', 'ASC')
            ->paginate(20);

        
        return $equipmentItems;
    }

    public function requestTransfer($id)
    {
        $equipment = Equipment::findOrFail($id);
        return view('user.requests.request_transfer', compact('equipment'));
    }

    public function storeTransferRequest(Request $request, $id)
    {
        // Validate all required fields
        $request->validate([
            'property_number' => 'required|string|max:255',
            'transfer_office' => 'required|string|max:255',
            'transfer_enduser' => 'required|string|max:255',
            'transfer_position' => 'required|string|max:255',
            'reason_transfer' => 'required|string',
        ]);

        // Insert transfer request
        DB::table('request_transfer')->insert([
            'equipment_id' => $id,
            'property_number' => $request->property_number,
            'transfer_office' => $request->transfer_office,
            'transfer_enduser' => $request->transfer_enduser,
            'transfer_position' => $request->transfer_position,
            'reason_transfer' => $request->reason_transfer,
            'status' => 'Pending',
            'date_created' => now(),
        ]);

        return redirect()->route('user.dashboard')->with('success', 'Transfer request submitted.');
    }


    public function requestUpdate($id)
    {
        $equipment = Equipment::findOrFail($id);
        return view('user.requests.request_update', compact('equipment'));
    }

    public function storeUpdateRequest(Request $request, $id) 
    {
        // Validate the input
        $request->validate([
            'property_number' => 'required|string|max:255',
            'division' => 'required|string|max:255',
            'section' => 'required|string|max:255',
            'reasons' => 'required|string',
           
        ]);

        // Retrieve the equipment record
        $equipment = Equipment::findOrFail($id);

        // Insert into the `request_update` table
        DB::table('request_update')->insert([
            'equipment_id' => $equipment->equipment_id,
            'property_number' => $request->property_number,
            'division' => $request->division,
            'section' => $request->section,
            'reasons' => $request->reasons,
            'status' => 'Pending',
            'date_created' => now(),
        ]);

        
        return redirect()->route('user.dashboard')->with('success', 'Equipment update request submitted successfully.');
    }

    public function requestUnserviceable($id)
    {
        // Fetch equipment details with error handling
        $equipment = Equipment::where('equipment_id', $id)->first();

        if (!$equipment) {
            return redirect()->route('user.dashboard')->with('error', 'Equipment not found.');
        }

        return view('user.requests.request_unserviceable', compact('equipment'));
    }

    public function storeUnserviceableRequest(Request $request, $id)
    {
        // Fetch equipment details
        $equipment = Equipment::where('equipment_id', $id)->first();

        if (!$equipment) {
            return redirect()->route('user.dashboard')->with('error', 'Equipment not found.');
        }

        // Validate required fields
        $request->validate([
            'unserviceable_condition' => 'required|string|max:255',
        ]);

        // Try to insert into request_unserviceable table
        try {
            DB::table('request_unserviceable')->insert([
                'equipment_id' => $id,
                'property_number' => $equipment->property_number, // ✅ Fixed: Get from DB
                'end_user' => $equipment->end_user, // ✅ Fixed: Get from DB
                'unserviceable_condition' => $request->unserviceable_condition,
                'status' => 'Pending',
                'date_created' => now(),
            ]);

            return redirect()->route('user.dashboard')->with('success', 'Unserviceable request submitted.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to submit the request. Please try again.');
        }
    }


    public function inventory(Request $request)
{
    $user = Auth::user();
    $office = $user->office;
    $division = $user->div_name;

    // ✅ Base Query
    $query = Equipment::where('status', 'serviceable')
        ->where('office', 'like', '%' . $office . '%')
        ->where('division', 'like', '%' . $division . '%');

    // ✅ Apply Filters
    if ($request->filled('search')) {
        $query->where(function ($q) use ($request) {
            $q->where('property_number', 'like', "%{$request->search}%")
                ->orWhere('particular', 'like', "%{$request->search}%")
                ->orWhere('description', 'like', "%{$request->search}%")
                ->orWhere('end_user', 'like', "%{$request->search}%");
        });
    }

    if ($request->filled('date_from')) {
        $query->whereDate('date_acquired', '>=', $request->date_from);
    }
    if ($request->filled('date_to')) {
        $query->whereDate('date_acquired', '<=', $request->date_to);
    }

    if ($request->ppe_category === 'ppe') {
        $query->where('amount', '>=', 50000);
    } elseif ($request->ppe_category === 'semi_expendables') {
        $query->where('amount', '<', 50000);
    }

    // ✅ Fix Pagination (Preserve Filters)
    $serviceables = $query->paginate(20)->appends($request->query());

    return view('user.user-gss.inventory', compact('serviceables'));
}

    




public function exportInventory(Request $request)
{
    $user = Auth::user();
    $query = Equipment::where('status', 'serviceable')
        ->where('office', 'like', '%' . $user->office . '%')
        ->where('division', 'like', '%' . $user->div_name . '%');

    // ✅ Apply Filters
    if ($request->filled('search')) {
        $query->where(function ($q) use ($request) {
            $q->where('property_number', 'like', "%{$request->search}%")
                ->orWhere('particular', 'like', "%{$request->search}%")
                ->orWhere('description', 'like', "%{$request->search}%")
                ->orWhere('end_user', 'like', "%{$request->search}%");
        });
    }

    if ($request->filled('date_from')) {
        $query->whereDate('date_acquired', '>=', $request->date_from);
    }
    if ($request->filled('date_to')) {
        $query->whereDate('date_acquired', '<=', $request->date_to);
    }

    if ($request->ppe_category === 'ppe') {
        $query->where('amount', '>=', 50000);
    } elseif ($request->ppe_category === 'semi_expendables') {
        $query->where('amount', '<', 50000);
    }

    // ✅ Get Filtered Data
    $filteredData = $query->get();

    if ($filteredData->isEmpty()) {
        return redirect()->back()->with('error', 'No data available for export.');
    }

    return Excel::download(new ServiceableExport($filteredData), 'filtered_serviceable_inventory.xlsx');
}


    






    public function defectsAndComplaintsForm()
    {
        return view('user.user-gss.defects_and_complaints_form');
    }

    public function jobRequestForm($id = null)
    {
        // Your logic here
        return view('user.user-gss.job_request_form');
    }

    public function gatePassForm()
    {
        return view('user.user-gss.gate_pass_form');
    }

    

      

    public function viewRequest(Request $request) 
    {
        $user = Auth::user();
        $division = $user->div_name;
        $search = $request->get('search');
    
        // Common search filter (explicitly using table alias to avoid ambiguity)
        $statusFilter = function ($query, $tableAlias) use ($search) {
            $query->where(function ($q) use ($tableAlias) {
                $q->where("$tableAlias.status", 'Pending')
                  ->orWhereNull("$tableAlias.status")
                  ->orWhere("$tableAlias.status", '');
            });
    
            if ($search) {
                $query->where(function ($q) use ($search, $tableAlias) {
                    $q->where("$tableAlias.property_number", 'LIKE', "%$search%")
                      ->orWhere("$tableAlias.description", 'LIKE', "%$search%");
                });
            }
        };
    
        // Fetching data from request tables with explicit table alias for 'status'
        $requestUpdate = DB::table('request_update as ru')
            ->join('equipment as e', 'ru.property_number', '=', 'e.property_number')
            ->select(
                'e.equipment_id',
                'ru.request_update_id as id', 
                'ru.property_number', 
                'ru.reasons as description', 
                'e.description as equipment_description', 
                'ru.status', 
                DB::raw("'Request for Update' as source")
            )
            ->where('e.division', $division)
            ->where(function ($query) use ($statusFilter) {
                $statusFilter($query, 'ru'); // Apply filter
            });
    
        $requestTransfer = DB::table('request_transfer as rt')
            ->join('equipment as e', 'rt.property_number', '=', 'e.property_number')
            ->select(
                'e.equipment_id',
                'rt.request_transfer_id as id', 
                'rt.property_number', 
                'rt.reason_transfer as description', 
                'e.description as equipment_description', 
                'rt.status', 
                DB::raw("'Request for Transfer' as source")
            )
            ->where('e.division', $division)
            ->where(function ($query) use ($statusFilter) {
                $statusFilter($query, 'rt'); // Apply filter
            });
    
        $requestUnserviceable = DB::table('request_unserviceable as ru')
            ->join('equipment as e', 'ru.property_number', '=', 'e.property_number')
            ->select(
                'e.equipment_id',
                'ru.request_unserviceable_id as id', 
                'ru.property_number', 
                'ru.unserviceable_condition as description', 
                'e.description as equipment_description', 
                'ru.status', 
                DB::raw("'Request for Return' as source")
            )
            ->where('e.division', $division)
            ->where(function ($query) use ($statusFilter) {
                $statusFilter($query, 'ru'); // Apply filter
            });
    
        // Combine all requests (excluding job_requests and complaints_defects)
        $requests = $requestUpdate
            ->union($requestTransfer)
            ->union($requestUnserviceable)
            ->orderBy('source', 'desc')
            ->paginate(20);
    
        return view('user.user-gss.view_request', compact('requests'));
    }
    
    public function getEquipmentDetails(Request $request)
    {
        $propertyNumber = $request->input('property_number');

        // Fetch the equipment details based on property number only
        $equipment = DB::table('equipment')
            ->where('property_number', $propertyNumber)
            ->first();

        if ($equipment) {
            return response()->json([
                'particular' => $equipment->particular,
                'serial_no' => $equipment->serial_no,
            ]);
        } else {
            return response()->json(null, 404);
        }
    }

    public function storeDefectsAndComplaintsForm(Request $request)
    {
         // Validate the incoming request data
        $request->validate([
            'PropertyNumber' => 'required|string|max:255',
            'TypeOfEquipment' => 'required|string|max:255',
            'SerialNo' => 'required|string|max:255',
            'Division' => 'required|string|max:255',
            'Complaints' => 'required|string',
            'Defects' => 'required|string',
            'PartsToBeRepaired' => 'required|string',
            'Remarks' => 'required|string',
        ]);

        // Store the data using the ComplaintDefect model
        ComplaintDefect::create([
            'property_number' => $request->PropertyNumber,
            'type_of_equipment' => $request->TypeOfEquipment,
            'serial_no' => $request->SerialNo,
            'division' => $request->Division,
            'complaints' => $request->Complaints,
            'defects' => $request->Defects,
            'parts_to_be_repaired' => $request->PartsToBeRepaired,
            'remarks' => $request->Remarks,
            
        ]);

        // Redirect back with a success message
        return redirect()->route('user.general-services.defects_and_complaints_form')
            ->with('success', 'Defects and Complaints have been successfully created');
    }

    public function storeJobRequest(Request $request)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'Type' => 'required|array',
            'TypeOfRequest' => 'required|array',
            'Specify' => 'nullable|string|max:255',
            'job_description' => 'required|string|max:255',
        ]);

        // Store the data using the JobRequest model
        JobRequest::create([
            'type' => implode(',', $validated['Type']), // Convert array to comma-separated string
            'date_time_requested' => now(), // Capture the current time
            'name' => auth()->user()->name,
            'division' => auth()->user()->div_name,
            'type_of_request' => implode(',', $validated['TypeOfRequest']), // Convert array to comma-separated string
            'specify' => $request->Specify,
            'job_description' => $validated['job_description'],
        ]);

        // Redirect back with a success message
        return redirect()->route('user.general-services.job_request_form')
            ->with('success', 'Job request submitted successfully!');
    }

    public function returnedUnserviceableForm(Request $request)
    {
        // Check if it's an AJAX request for equipment details
        if ($request->ajax() && $request->has('property_number')) {
            $propertyNumber = $request->input('property_number');

            // Fetch the equipment details based on the property number
            $equipment = DB::table('equipment')
                ->where('property_number', $propertyNumber)
                ->first();

            if ($equipment) {
                return response()->json([
                    'property_type' => $equipment->property_type,
                    'particular' => $equipment->particular,
                    'amount' => $equipment->amount,
                    'date_acquired' => $equipment->date_acquired,
                ]);
            } else {
                return response()->json(null, 404);
            }
        }

        // If not an AJAX request, proceed to load the form with all equipment data
        $equipment = Equipment::all();

        // Pass the equipment data to the view
        return view('user.user-gss.returned_unserviceable_form', compact('equipment'));
    }

    public function storeReturnedUnserviceableForm(Request $request)
    {
        // Clean up unit_price by removing commas
        $request->merge([
            'unit_price' => str_replace(',', '', $request->input('unit_price')),
        ]);

        // Validate the incoming request data after modification
        $validatedData = $request->validate([
            'property_number' => 'required|string|max:255',
            'property_type' => 'nullable|string|max:100',
            'item_description' => 'nullable|string',
            'unit_price' => 'nullable|numeric',
            'date_acquired' => 'nullable|date',
            'quantity' => 'nullable|integer',
            'remarks' => 'nullable|string',
            'returned_by' => 'required|string|max:255',
            'status' => 'nullable|string|max:50',
        ]);

        // Store the data using the Unserviceable model
        try {
            $unserviceable = Unserviceable::create([
                'property_number' => $validatedData['property_number'],
                'property_type' => $validatedData['property_type'],
                'item_description' => $validatedData['item_description'],
                'unit_price' => $validatedData['unit_price'],
                'date_acquired' => $validatedData['date_acquired'],
                'quantity' => $validatedData['quantity'],
                'remarks' => $validatedData['remarks'],
                'returned_by' => $validatedData['returned_by'],
                'status' => $validatedData['status'] ?? 'Pending',
            ]);

            // Log the model's attributes as an array
            Log::info('Unserviceable item saved successfully:', $unserviceable->toArray());

            // Redirect back with a success message
            return redirect()->route('user.general-services.returned_unserviceable_form')
                ->with('success', 'Unserviceable item has been successfully recorded.');
        } catch (\Exception $e) {
            Log::error('Error saving unserviceable item: ' . $e->getMessage());

            return redirect()->back()->withErrors('Failed to save the unserviceable item. Please try again.');
        }
    }

     
    
    
}
