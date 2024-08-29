<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Models\JobRequest;
use App\Models\ComplaintDefect;
use App\Models\Equipment;
use App\Models\Unserviceable;


class UserController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = now()->startOfDay();
        $dateLimit = $today->copy()->addDays(15);

        // Query to get equipment where date_end is within 15 days and division matches the user's division
        $equipmentItems = DB::table('equipment')
            ->where('division', $user->div_name)
            ->whereBetween('date_end', [$today, $dateLimit])
            ->get();

        return view('user.dashboard', compact('user', 'equipmentItems'));
    }

    public function defectsAndComplaintsForm()
    {
        return view('user.user-gss.defects_and_complaints_form');
    }

    public function jobRequestForm()
    {
        return view('user.user-gss.job_request_form');
    }

    public function gatePassForm()
    {
        return view('user.user-gss.gate_pass_form');
    }

    public function inventory()
    {
        return view('user.user-gss.inventory');
    }

    public function viewRequest()
    {
        return view('user.user-gss.view_request');
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
