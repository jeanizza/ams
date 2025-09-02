<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

use App\Models\Division;
use App\Models\Section;
use App\Models\AddRecord;
use App\Models\Serviceable;
use App\Models\Equipment;
use App\Models\Transfer;
use App\Models\MaintenanceLedger;
use App\Models\LedgerDetail;

use App\Exports\DisposalDetailsExport;
use App\Exports\EquipmentExport;

use Maatwebsite\Excel\Facades\Excel;

use PhpOffice\PhpSpreadsheet\IOFactory;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;




class GssAdminController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $office = $user->office;
        $dateFrom = Carbon::now();
        $dateTo = Carbon::now()->addDays(5);
        $division = $request->input('division', '');

        // Fetch equipment items matching the user's office, division, and date_end within 5 days from today
        $query = Equipment::where('office', 'like',  '%' . $office . '%')
            ->where('date_end', '<=', $dateTo)
            ->where('status', 'serviceable');

        if ($division) {
            $query->where('division', $division);
        }

        $equipmentItems = $query->paginate(20)->appends($request->query()); // Preserve query parameters

        //$equipmentItems = $query->get();
        \Log::info('Retrieved equipment items:', ['items' => $equipmentItems]);

        // Fetch divisions with equipment items due within the next 5 days and count them
        $divisions = Equipment::where('office', 'like',  '%' . $office . '%')
            ->where('date_end', '<=', $dateTo)
            ->where('status', 'serviceable')
            ->select('division')
            ->groupBy('division')
            ->get();
        
        \Log::info('Retrieved divisions:', ['divisions' => $divisions]);

        return view('gss.admin.dashboard', compact('user', 'equipmentItems', 'divisions'));
    }

    public function fetchDisplayedEquipment(Request $request)
    {
        $user = Auth::user();
        $office = $user->office;
        $dateTo = Carbon::now()->addDays(5);
        $division = $request->input('division', '');

        // Apply the same filters as in the table view
        $query = Equipment::where('office', 'like', '%' . $office . '%')
            ->where('date_end', '<=', $dateTo)
            ->where('status', 'serviceable');

        if ($division) {
            $query->where('division', $division);
        }

        // Fetch ALL matching records (no pagination)
        $equipmentItems = $query->select([
            'property_number', 'particular', 'description', 'amount', 'division',
            'section', 'date_acquired', 'lifespan', 'date_end'
        ])->get();

        return response()->json($equipmentItems);
    }


    public function adminNotification(Request $request)
    {
        $user = Auth::user(); 
        $office = $user->office;

        $divisions = DB::table('equipment')
            ->where('office', $office)
            ->pluck('division')
            ->unique();

        $query = DB::table('request_update')
            ->select(
                'request_update.property_number',
                'request_update.reasons as reason',
                DB::raw("'Request for Update' as source_table"),
                'equipment.equipment_id as equipment_id',
                'equipment.particular',
                'equipment.description',
                'equipment.amount',
                'equipment.division'
            )
            ->join('equipment', 'request_update.property_number', '=', 'equipment.property_number')
            ->where('equipment.office', $office)
            ->where('request_update.status', 'Pending')

            ->union(
                DB::table('request_transfer')
                    ->select(
                        'request_transfer.property_number',
                        'request_transfer.reason_transfer as reason',
                        DB::raw("'For Transfer' as source_table"),
                        'equipment.equipment_id as equipment_id',
                        'equipment.particular',
                        'equipment.description',
                        'equipment.amount',
                        'equipment.division'
                    )
                    ->join('equipment', 'request_transfer.property_number', '=', 'equipment.property_number')
                    ->where('equipment.office', $office)
                    ->where('request_transfer.status', 'Pending')
            )

            ->union(
                DB::table('request_unserviceable')
                    ->select(
                        'request_unserviceable.property_number',
                        'request_unserviceable.unserviceable_condition as reason',
                        DB::raw("'Unserviceable' as source_table"),
                        'equipment.equipment_id as equipment_id',
                        'equipment.particular',
                        'equipment.description',
                        'equipment.amount',
                        'equipment.division'
                    )
                    ->join('equipment', 'request_unserviceable.property_number', '=', 'equipment.property_number')
                    ->where('equipment.office', $office)
                    ->where('request_unserviceable.status', 'Pending')
            );

        // Optional filters
        if ($request->has('search') && !empty($request->search)) {
            $query->where('property_number', 'LIKE', '%' . $request->search . '%');
        }

        if ($request->has('division') && !empty($request->division)) {
            $query->where('equipment.division', $request->division);
        }

        if ($request->has('from') && $request->has('to') && !empty($request->from) && !empty($request->to)) {
            $query->whereBetween('request_update.date_created', [$request->from, $request->to]);
        }

        $notifications = $query->paginate(20);

        return view('gss.admin.notification', compact('notifications', 'divisions'));
    }


    // Add Record
    public function add_record()
    {
        $divisions = Division::all();

        // Fetch distinct years from the date_acquired column (2015 onwards)
        $years = Equipment::selectRaw('YEAR(date_acquired) as year')
            ->whereYear('date_acquired', '>=', 2015)
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');
    
        $lvCount = AddRecord::where('property_type', 'ICS')->where('amount', '<', 5000)->count();
        $hvCount = AddRecord::where('property_type', 'ICS')->where('amount', '>=', 5000)->count();
        $parCount = AddRecord::where('property_type', 'PAR')->count();
    
        return view('gss.admin.serviceable.add_record', compact('divisions', 'years', 'lvCount', 'hvCount', 'parCount'));
    }

    public function getSections($div_name)
    {
        $division = Division::whereRaw("BINARY UPPER(div_name) = ?", [strtoupper($div_name)])->first();

        if ($division) {
            $sections = Section::where('div_id', $division->div_id)->pluck('sec_name');
            return response()->json($sections);
        }
        return response()->json([]);
    }

    public function storeAddRecord(Request $request)
    {
        $validatedData = $request->validate([
            'property_type' => 'required|string',
            'property_number' => 'required|string',
            'category' => 'required|string',
            'particular' => 'required|string',
            'description' => 'required|string|max:300',
            'brand' => 'required|string|max:50',
            'model' => 'required|string|max:50',
            'serial_no' => 'required|string|max:50',
            'amount' => 'required|numeric|between:0,9999999999.99',
            'date_acquired' => 'required|date',
            'po_number' => 'required|string|max:20',
            'end_user' => 'required|string|max:150',
            'position' => 'required|string|max:150',
            'office' => 'required|string',
            'division' => 'required|string|exists:division_pits,div_name',
            'section' => 'required|string|exists:section,sec_name',
            'actual_user' => 'required|string|max:150',
            'position_actual_user' => 'required|string|max:150',
            'remarks' => 'required|string|max:150',
            'fund' => 'required|string|max:20',
            'lifespan' => 'required|integer|min:1',
            'upload_image' => 'required|image|mimes:jpeg,png|max:2048',
        ]);
    
        // Retrieve div_name and sec_name
        $div_name = $request->input('division');
        $sec_name = $request->input('section');
    
        // Assign additional fields
        $validatedData['div_name'] = $div_name;
        $validatedData['sec_name'] = $sec_name;
        $validatedData['uploaded_by'] = Auth::user()->name;
        $validatedData['date_created'] = now();
       // $validatedData['date_end'] = Carbon::parse($validatedData['date_acquired'])->addMonths((int)$validatedData['lifespan'])->format('Y-m-d');
       $validatedData['date_end'] = $request->input('date_end'); // Use the date_end from the form

    
        // Handle file upload
        if ($request->hasFile('upload_image')) {
            $validatedData['upload_image'] = $request->file('upload_image')->store('uploads', 'public');
        }
    
        // Create the Equipment record
        $equipmentData = [
            'property_type' => $validatedData['property_type'],
            'property_number' => $validatedData['property_number'],
            'category' => $validatedData['category'],
            'particular' => $validatedData['particular'],
            'description' => $validatedData['description'],
            'brand' => $validatedData['brand'],
            'model' => $validatedData['model'],
            'serial_no' => $validatedData['serial_no'],
            'amount' => $validatedData['amount'],
            'date_acquired' => $validatedData['date_acquired'],
            'po_number' => $validatedData['po_number'],
            'end_user' => $validatedData['end_user'],
            'position' => $validatedData['position'],
            'office' => $validatedData['office'],
            'division' => $validatedData['division'],
            'section' => $validatedData['section'],
            'actual_user' => $validatedData['actual_user'],
            'position_actual_user' => $validatedData['position_actual_user'],
            'remarks' => $validatedData['remarks'],
            'fund' => $validatedData['fund'],
            'lifespan' => $validatedData['lifespan'],
            'date_end' => $validatedData['date_end'],
            'upload_image' => $validatedData['upload_image'],
            'uploaded_by' => $validatedData['uploaded_by'],
            'status' => 'serviceable',
        ];
    
        Equipment::create($equipmentData);
    
        // Redirect with success message and property number
        return redirect()->route('gss.admin.add_record')->with(['success' => 'Record added successfully', 'propertyNumber' => $validatedData['property_number']]);
    }


    public function fetchCounters(Request $request)
    {
        $query = Equipment::query();

        // Filter by property type
        if ($request->property_type) {
            $query->where('property_type', $request->property_type);
        }

        // Filter by High Value / Low Value if ICS is selected
        if ($request->property_type == 'ICS' && $request->value_type) {
            if ($request->value_type == 'High Value') {
                $query->whereBetween('amount', [5000, 49999.99]);
            } elseif ($request->value_type == 'Low Value') {
                $query->where('amount', '<', 5000);
            }
        }

        // Filter by year
        if ($request->year) {
            $query->whereYear('date_acquired', $request->year);
        }

        // Count the records
        $count = $query->count();

        return response()->json(['count' => $count]);
    }





    public function list_serviceable(Request $request)
    {
            $user = Auth::user();
            $query = DB::table('equipment')
                ->select('equipment_id', 'property_number', 'particular', 'description', 'office', 'end_user', 'division', 'section', 'amount', 'upload_image', 'property_type', 'status', 'serial_no', 'model', 'brand', 'date_acquired', 'po_number', 'position', 'actual_user', 'position_actual_user', 'remarks', 'fund', 'lifespan', 'date_end', 'uploaded_by')
                ->where(function ($q) use ($user) {
                    $q->where('office', 'like', '%' . $user->office . '%')
                      ->orWhere('office', 'like', '%region%')
                      ->orWhere('office', 'like', '%regional office%');
                })
                ->whereRaw('LOWER(status) = ?', ['serviceable']);
        
            // Search functionality
            if ($request->has('search')) {
                $searchTerm = $request->search;
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('property_type', 'like', '%' . $searchTerm . '%')
                        ->orWhere('property_number', 'like', '%' . $searchTerm . '%')
                        ->orWhere('category', 'like', '%' . $searchTerm . '%')
                        ->orWhere('status', 'like', '%' . $searchTerm . '%')
                        ->orWhere('particular', 'like', '%' . $searchTerm . '%')
                        ->orWhere('description', 'like', '%' . $searchTerm . '%')
                        ->orWhere('serial_no', 'like', '%' . $searchTerm . '%')
                        ->orWhere('model', 'like', '%' . $searchTerm . '%')
                        ->orWhere('brand', 'like', '%' . $searchTerm . '%')
                        ->orWhere('amount', 'like', '%' . $searchTerm . '%')
                        ->orWhere('date_acquired', 'like', '%' . $searchTerm . '%')
                        ->orWhere('po_number', 'like', '%' . $searchTerm . '%')
                        ->orWhere('end_user', 'like', '%' . $searchTerm . '%')
                        ->orWhere('position', 'like', '%' . $searchTerm . '%')
                        ->orWhere('office', 'like', '%' . $searchTerm . '%')
                        ->orWhere('division', 'like', '%' . $searchTerm . '%')
                        ->orWhere('section', 'like', '%' . $searchTerm . '%')
                        ->orWhere('actual_user', 'like', '%' . $searchTerm . '%')
                        ->orWhere('position_actual_user', 'like', '%' . $searchTerm . '%')
                        ->orWhere('remarks', 'like', '%' . $searchTerm . '%')
                        ->orWhere('fund', 'like', '%' . $searchTerm . '%')
                        ->orWhere('lifespan', 'like', '%' . $searchTerm . '%')
                        ->orWhere('date_end', 'like', '%' . $searchTerm . '%')
                        ->orWhere('uploaded_by', 'like', '%' . $searchTerm . '%');
                });
            }
        
            // Sort functionality
            if ($request->has('sort_by') && $request->has('sort_direction')) {
                $sortBy = $request->sort_by;
                $sortDirection = $request->sort_direction;
                $query->orderBy($sortBy, $sortDirection);
            } else {
                $query->orderBy('date_created', 'desc');
            }
        
            // Paginate results
            $serviceables = $query->paginate(20);
        
            if ($request->ajax()) {
                return response()->json([
                    'table_data' => view('gss.admin.serviceable.table_data', compact('serviceables'))->render(),
                    'pagination' => view('gss.admin.serviceable.pagination_links', compact('serviceables'))->render()
                ]);
            }
        
            return view('gss.admin.serviceable.list_serviceable', compact('serviceables'));
    }

    private function fetchServiceableData($id)
    {
        // Fetch the serviceable item by ID
        $serviceable = Equipment::find($id);

        if (!$serviceable) {
            return null; // Return null if the serviceable item doesn't exist
        }

        // Fetch all divisions for the dropdown
        $divisions = Division::all();

        // Fetch the division details for the current item
        $division = Division::where('div_name', trim($serviceable->division))->first();

        // Fetch sections based on the division
        $sections = $division ? Section::where('div_id', $division->div_id)->get() : [];

        return compact('serviceable', 'divisions', 'sections');
    }

    public function updateServiceableForm($id) 
    {
        $data = $this->fetchServiceableData($id);
        if (!$data) {
            return redirect()->back()->with('error', 'Serviceable item not found.');
        }
        return view('gss.admin.serviceable.update_serviceable', $data);
    }

    public function updateServiceable(Request $request, $id)
    {

        // Remove commas from numeric fields
        $request->merge([
            'amount' => str_replace(',', '', $request->input('amount')),
        ]);

        // Validate input
        $validatedData = $request->validate([
            'category' => 'nullable|string|max:255',
            'particular' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'serial_no' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'amount' => 'nullable|numeric|min:0',
            'po_number' => 'required|string|max:255',
            'date_acquired' => 'required|date',
            'end_user' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'section' => 'nullable|string|max:255',
            'division' => 'nullable|string|max:255',
            'actual_user' => 'nullable|string|max:255',
            'position_actual_user' => 'nullable|string|max:255',
            'remarks' => 'nullable|string|max:255',
            'fund' => 'nullable|string|max:255',
            'lifespan' => 'nullable|integer|min:0',
            'date_end' => 'nullable|date',
            'upload_image' => 'nullable|file|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Fetch the record
        $serviceable = Equipment::findOrFail($id);

        // Update the record
        $serviceable->fill($validatedData);

        // Add the updated_by and date_updated fields
        $serviceable->updated_by = Auth::user()->name; // Current authenticated user's name
        $serviceable->date_updated = now(); // Current date and time

        // Handle file upload
        if ($request->hasFile('upload_image')) {
            // Delete the old file if it exists
            if ($serviceable->upload_image && Storage::exists('public/' . $serviceable->upload_image)) {
                Storage::delete('public/' . $serviceable->upload_image);
            }
            // Store the new file
            $serviceable->upload_image = $request->file('upload_image')->store('uploads', 'public');
        }

        // Save the record
        $serviceable->save();

        // Log query for debugging
        \Log::info(DB::getQueryLog());

        // Redirect with success message
        return redirect()
        ->route('gss.admin.list_serviceable')
        ->with('success', 'Serviceable record updated successfully.');
    }

    public function transferServiceableForm($id)
    {
            // Fetch the serviceable item by ID
            $serviceable = Equipment::findOrFail($id); // Fetches all fields from the Equipment table
           // $serviceable = Equipment::select('equipment_id', 'property_number', 'property_type', 'category', 'description')->findOrFail($id);
        
            // Fetch the transfer details if they exist
            $transfer = DB::table('transfer_serviceable')->where('equipment_id', $id)->first();
        
            // Define the list of offices
            $offices = [
                'Regional Office', 
                'PENRO Camiguin', 
                'PENRO Bukidnon', 
                'CENRO Don Carlos', 
                'CENRO Talakag', 
                'CENRO Valencia',
                'PENRO Lanao del Norte',
                'CENRO Iligan',
                'CENRO Kolambugan',
                'PENRO Misamis Occidental',
                'CENRO Oroquieta',
                'CENRO Ozamis',
                'PENRO Misamis Oriental',
                'CENRO Gingoog',
                'CENRO Initao',
            ];
        
            // Fetch the list of divisions (if needed)
            $divisions = DB::table('division')->select('division_name')->get();
        
            // Pass all necessary data to the view
            return view('gss.admin.serviceable.transfer_serviceable', compact('serviceable', 'transfer', 'offices', 'divisions'));
        }

    public function transferServiceable(Request $request, $id)
    {
        Log::info('Transfer request data received:', $request->all());

    // Fetch the serviceable item by ID from the equipment table
    $serviceable = Equipment::where('equipment_id', $id)->first();

    if (!$serviceable) {
        Log::error('Serviceable item not found for equipment_id: ' . $id);
        return redirect()->back()->with('error', 'Serviceable item not found.');
    }

    // Validate incoming data 
    $validatedData = $request->validate([
        'property_number' => 'required|string|max:100',
        'end_user' => 'required|string|max:100',
        'position' => 'required|string|max:100',
        'transfer_office' => 'required|string|max:100',
        'transfer_enduser' => 'required|string|max:100',
        'transfer_position' => 'required|string|max:100',
        'transfer_condition' => 'required|string|max:100',
        'reason_transfer' => 'nullable|string|max:255',
        'date_transfer' => 'required|date',
    ]);

    Log::info('Validated transfer data:', $validatedData);

    try {
        $message = '';
        DB::transaction(function () use ($serviceable, $validatedData, $id, &$message) {
            // Check if a record already exists for this equipment in transfer_serviceable
            $existingTransfer = DB::table('transfer_serviceable')->where('equipment_id', $id)->first();


            if ($existingTransfer) {
                // Update existing transfer record
                DB::table('transfer_serviceable')
                    ->where('equipment_id', $id)
                    ->update([
                        'property_number' => $validatedData['property_number'],
                        'category' => $serviceable->category,
                        'particular' => $serviceable->particular,
                        'description' => $serviceable->description,
                        'amount' => $serviceable->amount,
                        'end_user' => $validatedData['end_user'],
                        'position' => $validatedData['position'],
                        'office' => $serviceable->office,
                        'date_acquired' => $serviceable->date_acquired,
                        'remarks' => $serviceable->remarks,
                        'upload_image' => $serviceable->upload_image,
                        'transfer_office' => $validatedData['transfer_office'],
                        'transfer_enduser' => $validatedData['transfer_enduser'],
                        'transfer_position' => $validatedData['transfer_position'],
                        'transfer_condition' => $validatedData['transfer_condition'],
                        'reason_transfer' => $validatedData['reason_transfer'],
                        'date_transfer' => $validatedData['date_transfer'],
                        'date_updated' => now(),
                    ]);

                    Log::info('Transfer data successfully updated for equipment_id: ' . $id);
                    $message = 'Item is successfully updated for Property Number: ' . $validatedData['property_number'];
                
            } else {
                // Insert new transfer record
                DB::table('transfer_serviceable')->insert([
                    'transfer_id' => 'TR-' . time(), // Generate a unique transfer ID
                    'equipment_id' => $serviceable->equipment_id,
                    'property_number' => $validatedData['property_number'],
                    'category' => $serviceable->category,
                    'particular' => $serviceable->particular,
                    'description' => $serviceable->description,
                    'amount' => $serviceable->amount,
                    'end_user' => $validatedData['end_user'],
                    'position' => $validatedData['position'],
                    'office' => $serviceable->office,
                    'date_acquired' => $serviceable->date_acquired,
                    'remarks' => $serviceable->remarks,
                    'upload_image' => $serviceable->upload_image,
                    'transfer_office' => $validatedData['transfer_office'],
                    'transfer_enduser' => $validatedData['transfer_enduser'],
                    'transfer_position' => $validatedData['transfer_position'],
                    'transfer_condition' => $validatedData['transfer_condition'],
                    'reason_transfer' => $validatedData['reason_transfer'],
                    'date_transfer' => $validatedData['date_transfer'],
                    'date_created' => now(),
                ]);

                Log::info('Item is successfully Transferred to ' . $validatedData['transfer_enduser']);
                $message = 'Item is successfully Transferred to ' . $validatedData['transfer_enduser'];
            }

            // Update the status column in the equipment table
            $serviceable->update(['status' => 'Transferred']);
            Log::info('Equipment status updated to "Transferred" for equipment_id: ' . $serviceable->property_number);
        });

        return redirect()->route('gss.admin.transferred_items')->with('success', $message);
    } catch (\Exception $e) {
        Log::error('Error transferring serviceable item: ' . $e->getMessage());
        return redirect()->back()->with('error', 'An error occurred while transferring the serviceable item.');
    }
    }

    

    public function unserviceableForm($id)
    {
        // Find the equipment record by its ID
        $serviceable = DB::table('equipment')->where('equipment_id', $id)->first();

        // Check if the record exists
        if (!$serviceable) {
            return redirect()->route('gss.admin.unserviceable_items')
                ->with('error', 'Equipment not found.');
        }

        // Find the corresponding unserviceable record (if any)
        $unserviceable = DB::table('unserviceable')->where('equipment_id', $id)->first();

        // Update the view path to match the folder structure
        return view('gss.admin.serviceable.unserviceable', compact('serviceable', 'unserviceable'));
    }

    public function unserviceableUpdate(Request $request, $id)
    {
        // Validate input data
        $validator = $request->validate([
            'unserviceable_condition' => 'required|string|max:255',
            'unserviceable_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'returned_by' => 'nullable|string|max:255',
            'date_returned' => 'nullable|date',
        ]);

        // Fetch equipment details
        $equipment = DB::table('equipment')->where('equipment_id', $id)->first();

        if (!$equipment) {
            return redirect()->route('gss.admin.unserviceable_items')
                ->with('error', 'Equipment not found.');
        }

        // Check if unserviceable record exists
        $existingUnserviceable = DB::table('unserviceable')->where('equipment_id', $id)->first();

        // Generate unique unserviceable_id
        $unserviceableId = $existingUnserviceable
            ? $existingUnserviceable->unserviceable_id
            : 'UR-' . time();

        // Handle file upload and delete old image if exists
        $unserviceableImagePath = $existingUnserviceable->unserviceable_image ?? null;
        if ($request->hasFile('unserviceable_image')) {
            // Delete old image if it exists
            if ($unserviceableImagePath && Storage::exists('public/' . $unserviceableImagePath)) {
                Storage::delete('public/' . $unserviceableImagePath);
            }

            // Store the new image
            $unserviceableImagePath = $request->file('unserviceable_image')->store('unserviceable_images', 'public');
        }

        // Prepare data for update or insert
        $data = [
            'unserviceable_id' => $unserviceableId,
            'equipment_id' => $id,
            'property_number' => $equipment->property_number,
            'category' => $equipment->category,
            'particular' => $equipment->particular,
            'description' => $equipment->description,
            'amount' => $equipment->amount,
            'end_user' => $equipment->end_user,
            'position' => $equipment->position,
            'office' => $equipment->office,
            'division' => $equipment->division,
            'date_acquired' => $equipment->date_acquired,
            'lifespan' => $equipment->lifespan,
            'date_end' => $equipment->date_end,
            'remarks' => $equipment->remarks,
            'upload_image' => $equipment->upload_image,
            'unserviceable_condition' => $request->unserviceable_condition,
            'unserviceable_image' => $unserviceableImagePath,
            'updated_by' => auth()->user()->name,
            'returned_by' => $request->returned_by,
            'date_returned' => $request->date_returned,
            'date_created' => now(),
        ];

        if ($existingUnserviceable) {
            // Update existing record
            $data['date_updated'] = now();
            DB::table('unserviceable')->where('equipment_id', $id)->update($data);
        } else {
            // Insert new record
            $data['date_created'] = now();
            DB::table('unserviceable')->insert($data);
        }

        // Update equipment status to "Unserviceable"
        DB::table('equipment')->where('equipment_id', $id)->update([
            'status' => 'Unserviceable',
        ]);

        return redirect()->route('gss.admin.unserviceable_items')
            ->with('success', 'Unserviceable record saved/updated and equipment status updated successfully.');
}


    public function transferredItems(Request $request)
    {
        $user = Auth::user();

    // Query using 'office' field instead of 'transfer_office'
    $query = DB::table('transfer_serviceable')
                ->whereRaw('LOWER(TRIM(office)) = ?', [strtolower(trim($user->office))]);

    // Count total transferred items
    $totalTransferredCount = (clone $query)->count();

    // Calculate total amount for all transferred items
    $totalAmountAll = (clone $query)->sum('amount');
    $totalAmountYear = 0;

    // Get the latest transfer date
    $latestDateTransfer = DB::table('transfer_serviceable')
                            ->whereRaw('LOWER(TRIM(office)) = ?', [strtolower(trim($user->office))])
                            ->max('date_transfer');

    // Filter only the latest transferred items
    if ($request->has('latest') && $request->latest == 'true') {
        $query->where('date_transfer', $latestDateTransfer);
    }

    // Filter by year if provided
    if ($request->has('year') && !empty($request->year)) {
        $year = $request->year;
        $totalAmountYear = (clone $query)->whereYear('date_transfer', $year)->sum('amount');
        $query->whereYear('date_transfer', $year);
    }

    // Get distinct years from the table
    $years = DB::table('transfer_serviceable')
                ->whereRaw('LOWER(TRIM(office)) = ?', [strtolower(trim($user->office))])
                ->selectRaw('YEAR(date_transfer) as year')
                ->distinct()
                ->orderBy('year', 'desc')
                ->pluck('year');

    // Add search functionality
    if ($request->has('search')) {
        $searchTerm = $request->search;
        $query->where(function ($q) use ($searchTerm) {
            $q->where('property_number', 'like', '%' . $searchTerm . '%')
              ->orWhere('particular', 'like', '%' . $searchTerm . '%')
              ->orWhere('description', 'like', '%' . $searchTerm . '%')
              ->orWhere('end_user', 'like', '%' . $searchTerm . '%')
              ->orWhere('office', 'like', '%' . $searchTerm . '%')
              ->orWhere('amount', 'like', '%' . $searchTerm . '%');
        });
    }

    // Sort the results if sorting parameters are provided
    if ($request->has('sort_by') && $request->has('sort_direction')) {
        $sortBy = $request->sort_by;
        $sortDirection = $request->sort_direction;
        $query->orderBy($sortBy, $sortDirection);
    } else {
        // Default sort: latest transfers first
        $query->orderBy('date_transfer', 'desc');
    }

    // Paginate the results
    $transferredItems = $query->paginate(20);

    return view('gss.admin.transferred.transferred_items', compact('transferredItems', 'totalAmountAll', 'totalAmountYear', 'totalTransferredCount', 'years', 'latestDateTransfer'));
}

    public function unserviceableItems(Request $request)
    {
        $user = Auth::user();

        // Query the unserviceable table
        $query = DB::table('unserviceable')
            ->where('office', $user->office);
    
        // Calculate total amount for all unserviceable items
        $totalAmountAll = $query->sum('amount');
    
        // Default total amount for the selected year to 0
        $totalAmountYear = 0;
    
        // Filter by year if selected
        if ($request->has('year') && !empty($request->year)) {
            $year = $request->year;
            $query->whereYear('date_returned', $year);
            $totalAmountYear = $query->sum('amount');
        }
    
        // Get distinct years from the date_returned column
        $years = DB::table('unserviceable')
            ->where('office', $user->office)
            ->selectRaw('YEAR(date_returned) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');
    
        // Search functionality
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('property_number', 'like', '%' . $searchTerm . '%')
                    ->orWhere('particular', 'like', '%' . $searchTerm . '%')
                    ->orWhere('description', 'like', '%' . $searchTerm . '%')
                    ->orWhere('end_user', 'like', '%' . $searchTerm . '%')
                    ->orWhere('office', 'like', '%' . $searchTerm . '%')
                    ->orWhere('division', 'like', '%' . $searchTerm . '%')
                    ->orWhere('amount', 'like', '%' . $searchTerm . '%')
                    ->orWhere('unserviceable_condition', 'like', '%' . $searchTerm . '%');
            });
        }
    
        // Sort functionality
        if ($request->has('sort_by') && $request->has('sort_direction')) {
            $query->orderBy($request->sort_by, $request->sort_direction);
        } else {
            $query->orderBy('date_returned', 'desc'); // Default sorting
        }
    
        // Paginate results
        $unserviceableItems = $query->paginate(20)->appends($request->query());
    
        return view('gss.admin.unserviceable.unserviceable_items', compact('unserviceableItems', 'totalAmountAll', 'totalAmountYear', 'years'));
    
    }

    public function addMaintenanceDetails($maintenance_ledger_id = null)
{
    $user = Auth::user();

    // Fetch property numbers related to the logged-in user
    $propertyNumbers = Equipment::where('status', 'serviceable')
                                ->where('office', $user->office)
                                ->get();

    $ledger = null;

    if ($maintenance_ledger_id) {
        // Fetch maintenance ledger details for updating
        $ledger = DB::table('maintenance_ledger')->where('maintenance_ledger_id', $maintenance_ledger_id)->first();
    }

   

    return view('gss.admin.maintenance.partials.add_maintenance_details', compact('propertyNumbers', 'ledger'));

}
    

    public function storeMaintenanceDetails(Request $request)
    {
        try {
            // Pre-process numeric values before validation (remove commas)
            $request->merge([
                'unit_cost' => str_replace(',', '', $request->input('unit_cost', '0')),
                'total_amount' => str_replace(',', '', $request->input('total_amount', '0'))
            ]);
    
            // Validate input data
            $validatedData = $request->validate([
                'property_number' => 'required|string|max:50',
                'date_delivered'  => 'required|date',
                'quantity'        => 'required|numeric|min:1',
                'unit'            => 'required|string|max:20',
                'po_number'       => 'nullable|string|max:50',
                'supplier'        => 'nullable|string|max:255',
                'unit_cost'       => 'bail|required|numeric|min:0',
                'total_amount'    => 'bail|required|numeric|min:0',
                'defects'         => 'nullable|string|max:255',
            ]);
    
            // Fetch equipment details based on `property_number`
            $equipment = DB::table('equipment')->where('property_number', $validatedData['property_number'])->first();
    
            if (!$equipment) {
                Log::error("Equipment not found for property number: {$validatedData['property_number']}");
                return redirect()->back()->with('error', 'Property number not found in the equipment table.');
            }
    
            // Generate a new `maintenance_ledger_id`
            $maintenanceLedgerId = 'ML-' . now()->format('YmdHis');
    
            // Prepare data for insertion
            $data = [
                'maintenance_ledger_id' => $maintenanceLedgerId,
                'equipment_id' => $equipment->equipment_id,
                'property_number' => $equipment->property_number,
                'particular_ledger' => $equipment->particular,
                'description_ledger' => $equipment->description,
                'unit_cost' => (float) $validatedData['unit_cost'],
                'total_amount' => (float) $validatedData['total_amount'],
                'date_delivered' => $validatedData['date_delivered'],
                'quantity' => $validatedData['quantity'],
                'unit' => $validatedData['unit'],
                'po_number' => $validatedData['po_number'],
                'supplier' => $validatedData['supplier'],
                'remarks' => $equipment->remarks ?? '',
                'defects' => $validatedData['defects'],
                'date_created' => now(),
            ];
    
            // Insert new record
            DB::table('maintenance_ledger')->insert($data);
    
            return redirect()->route('gss.admin.add_maintenance_details')
                ->with('success', 'Maintenance record saved successfully.');
    
        } catch (\Exception $e) {
            Log::error('Error in storeMaintenanceDetails: ' . $e->getMessage());
            return redirect()->route('gss.admin.add_maintenance_details')
                ->with('error', 'An error occurred while saving.');
        }
    
    }

    public function updateMaintenanceDetails(Request $request, $maintenance_ledger_id)
    {
        try {
            // Pre-process numeric values before validation (remove commas)
            $request->merge([
                'unit_cost' => str_replace(',', '', $request->input('unit_cost', '0')),
                'total_amount' => str_replace(',', '', $request->input('total_amount', '0'))
            ]);

            // Validate input data
            $validatedData = $request->validate([
                'property_number' => 'required|string|max:50',
                'date_delivered'  => 'required|date',
                'quantity'        => 'required|numeric|min:1',
                'unit'            => 'required|string|max:20',
                'po_number'       => 'nullable|string|max:50',
                'supplier'        => 'nullable|string|max:255',
                'unit_cost'       => 'bail|required|numeric|min:0',
                'total_amount'    => 'bail|required|numeric|min:0',
                'defects'         => 'nullable|string|max:255',
            ]);

            // Check if the record exists in the maintenance ledger
            $existingLedger = DB::table('maintenance_ledger')->where('maintenance_ledger_id', $maintenance_ledger_id)->first();

            if (!$existingLedger) {
                Log::error("Maintenance ledger not found for ID: {$maintenance_ledger_id}");
                return redirect()->back()->with('error', 'Maintenance ledger record not found.');
            }

            // Fetch equipment details based on property_number
            $equipment = DB::table('equipment')->where('property_number', $validatedData['property_number'])->first();

            if (!$equipment) {
                Log::error("Equipment not found for property number: {$validatedData['property_number']}");
                return redirect()->back()->with('error', 'Property number not found in the equipment table.');
            }

            // Prepare data for update
            $data = [
                'equipment_id' => $equipment->equipment_id,
                'property_number' => $equipment->property_number,
                'particular_ledger' => $equipment->particular,
                'description_ledger' => $equipment->description,
                'unit_cost' => (float) $validatedData['unit_cost'],
                'total_amount' => (float) $validatedData['total_amount'],
                'date_delivered' => $validatedData['date_delivered'],
                'quantity' => $validatedData['quantity'],
                'unit' => $validatedData['unit'],
                'po_number' => $validatedData['po_number'],
                'supplier' => $validatedData['supplier'],
                'defects' => $validatedData['defects'],
                'date_updated' => now(),
            ];

            // Update existing record
            DB::table('maintenance_ledger')->where('maintenance_ledger_id', $maintenance_ledger_id)->update($data);

            return redirect()->route('gss.admin.ledger')
                ->with('success', 'Maintenance record updated successfully.');

        } catch (\Exception $e) {
            Log::error('Error in updateMaintenanceDetails: ' . $e->getMessage());
            return redirect()->route('gss.admin.ledger')
                ->with('error', 'An error occurred while updating.');
        }
    }

    public function getPropertyNumbers(Request $request)
    {
        $propertyNumber = $request->input('property_number');
        $userOffice = auth()->user()->office; // Get the logged-in user's office

        $equipment = Equipment::where('property_number', $propertyNumber)
                            ->where('office', $userOffice)
                            ->where('status', 'serviceable')
                            ->first();

        if ($equipment) {
            return response()->json([
                'exists' => true,
                'data' => [
                    'particular' => $equipment->particular,
                    'description' => $equipment->description,
                    'division' => $equipment->division,
                    'end_user' => $equipment->end_user,
                    'serial_no' => $equipment->serial_no,
                    'date_acquired' => $equipment->date_acquired,
                    'amount' => $equipment->amount,
                    'model' => $equipment->model,
                    'remarks' => $equipment->remarks,
                ],
            ]);
        }

        return response()->json(['exists' => false]);
    }


    public function ledger()
    {
        $propertyNumbers = Equipment::where('status', 'serviceable')
        ->where('office', auth()->user()->office)
        ->get(['property_number', 'particular', 'division', 'end_user', 'serial_no', 'date_acquired', 'amount', 'model']);

        return view('gss.admin.maintenance.ledger', compact('propertyNumbers'));
    }

    public function searchLedger(Request $request)
    {
        $propertyNumber = $request->input('property_number');

        // Query to fetch ledger details from the view
        $ledgerDetails = DB::table('maintenance_ledger')
            ->where('property_number', $propertyNumber)
            ->paginate(10);

        if ($ledgerDetails->isEmpty()) {
            return redirect()->route('gss.admin.ledger')->with('message', 'Property Number has no ledger');
        }

        return view('gss.admin.maintenance.ledger', compact('ledgerDetails'));
    }

    public function processWasteMaterial(Request $request)
    {
        $maintenanceLedgerIds = $request->maintenance_ledger_ids;

        if (!$maintenanceLedgerIds) {
            return response()->json(['error' => 'No items selected.'], 400);
        }

        // Update the status of selected items to 'Waste Material'
        MaintenanceLedger::whereIn('maintenance_ledger_id', $maintenanceLedgerIds)
            ->update(['remarks' => 'Processed as Waste Material']);

        return response()->json(['success' => 'Waste material processed successfully.']);
    }

 

    private function getReconciliationQuery($request)
{
    $status = $request->input('status', 'Serviceable');
    $fromDate = $request->input('from_date');
    $toDate = $request->input('to_date');
    $equipmentType = $request->input('equipment_type', 'all');

    switch ($status) {
        case 'Transferred':
            $query = DB::table('transfer_serviceable')
                ->select('transfer_id as id', 'property_number', 'particular', 'amount', 'date_transfer as date', DB::raw("'Transferred' AS status"));
            if ($fromDate && $toDate) {
                $query->whereBetween('date_transfer', [$fromDate, $toDate]);
            }
            break;

        case 'Unserviceable':
            $query = DB::table('unserviceable')
                ->select('unserviceable_id as id', 'property_number', 'particular', 'amount', 'date_returned as date', DB::raw("'Unserviceable' AS status"));
            if ($fromDate && $toDate) {
                $query->whereBetween('date_returned', [$fromDate, $toDate]);
            }
            break;

        default: // Serviceable
            $query = DB::table('equipment')
                ->select('equipment_id as id', 'property_number', 'particular', 'amount', 'date_acquired as date', DB::raw("'Serviceable' AS status"));
            if ($fromDate && $toDate) {
                $query->whereBetween('date_acquired', [$fromDate, $toDate]);
            }
            break;
    }

    // Apply Equipment Type Filter for all statuses
    if ($equipmentType === 'semi-expendable') {
        $query->where('amount', '<', 50000);
    } elseif ($equipmentType === 'ppe') {
        $query->where('amount', '>=', 50000);
    }

    return $query;
}


public function reconciliation(Request $request)
{
    $query = $this->getReconciliationQuery($request);
    $reconcileRecords = $query->paginate(20);

    // Define query based on selected status
    switch ($request->status) {
        case 'Transferred':
            $queryStats = DB::table('transfer_serviceable')
                ->whereYear('date_transfer', now()->year);
            if ($request->from_date && $request->to_date) {
                $queryStats->whereBetween('date_transfer', [$request->from_date, $request->to_date]);
            }
            break;

        case 'Unserviceable':
            $queryStats = DB::table('unserviceable')
                ->whereYear('date_returned', now()->year);
            if ($request->from_date && $request->to_date) {
                $queryStats->whereBetween('date_returned', [$request->from_date, $request->to_date]);
            }
            break;

        default: // Serviceable
            $queryStats = DB::table('equipment');
            if ($request->from_date && $request->to_date) {
                $queryStats->whereBetween('date_acquired', [$request->from_date, $request->to_date]);
            }
            break;
    }

    // Apply Equipment Type Filter for Unserviceable and other statuses
    if ($request->equipment_type === 'semi-expendable') {
        $queryStats->where('amount', '<', 50000);
    } elseif ($request->equipment_type === 'ppe') {
        $queryStats->where('amount', '>=', 50000);
    }

    // Corrected Total Calculations
    $totalAmountSemiExpendable = (clone $queryStats)->where('amount', '<', 50000)->sum('amount');
    $totalAmountPPE = (clone $queryStats)->where('amount', '>=', 50000)->sum('amount');
    $totalAmountForSelectedStatus = (clone $queryStats)->sum('amount');

    return view('gss.admin.reconciliation', compact(
        'totalAmountSemiExpendable',
        'totalAmountPPE',
        'totalAmountForSelectedStatus',
        'reconcileRecords'
    ));
}




public function exportExcel(Request $request)
{
    $query = $this->getReconciliationQuery($request);
    $records = $query->get(); // Fetch ALL records (no pagination)

    return response()->json(['data' => $records]);
}




    // Export reconciliation data to Excel
    public function exportReconciliation()
    {
        return Excel::download(new ReconciliationExport, 'reconciliation.xlsx');
    }

    public function previewPdf($encodedPropertyNumber)
    {
        // Decode the property number
        $propertyNumber = urldecode($encodedPropertyNumber);
        Log::info("Attempting to generate PDF preview for property number: " . $propertyNumber);

        // Log the property number length to check for any hidden characters
        Log::info("Property number length: " . strlen($propertyNumber));

        // Retrieve the record from the database
        $record = Equipment::where('property_number', $propertyNumber)->first();

        if (!$record) {
            Log::error("Record not found for property number: " . $propertyNumber);

            return redirect()->route('gss.admin.add_record')->with('error', 'Record not found.');
        }

        Log::info("Record found: " . json_encode($record));

        // Render the HTML content for preview
        return view('gss.admin.serviceable.serviceable_template_preview', compact('record'));
    }

    public function generatePdf($encodedPropertyNumber)
    {
        // Decode the property number
        $propertyNumber = urldecode($encodedPropertyNumber);
        Log::info("Attempting to generate PDF for property number: " . $propertyNumber);

        // Log the property number length to check for any hidden characters
        Log::info("Property number length: " . strlen($propertyNumber));

        // Retrieve the record from the database
        $record = Equipment::where('property_number', $propertyNumber)->first();

        if (!$record) {
            Log::error("Record not found for property number: " . $propertyNumber);

            // Fetch all property numbers to see what's in the database for comparison
            $allRecords = Equipment::all();
            foreach ($allRecords as $rec) {
                Log::info("Existing property number: " . $rec->property_number . " (length: " . strlen($rec->property_number) . ")");
            }

            return redirect()->route('gss.admin.add_record')->with('error', 'Record not found.');
        }

        Log::info("Record found: " . json_encode($record));

        // Create the HTML content for the PDF
        $htmlContent = view('gss.admin.serviceable.serviceable_template', compact('record'))->render();
        Log::info("Generated HTML content for PDF: " . $htmlContent);

        // Set up Dompdf options
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        // Initialize Dompdf
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($htmlContent);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Output the generated PDF to a string
        $pdfOutput = $dompdf->output();
        $filename = 'Record_' . $record->property_number . '.pdf';
        $filePath = storage_path('app/public/' . $filename);

        Log::info("PDF generated, saving to: " . $filePath);

        // Save the PDF to a file
        try {
            Storage::disk('public')->put($filename, $pdfOutput);
            Log::info("PDF saved successfully");
        } catch (\Exception $e) {
            Log::error('Failed to save PDF: ' . $e->getMessage());
            return redirect()->route('gss.admin.add_record')->with('error', 'Failed to save PDF.');
        }

        if (Storage::disk('public')->exists($filename)) {
            Log::info("PDF file exists, preparing download response");
            return response()->download($filePath)->deleteFileAfterSend(true);
        } else {
            Log::error('PDF file not found after creation: ' . $filePath);
            return redirect()->route('gss.admin.add_record')->with('error', 'File not found.');
        }
    }

    public function addDisposalValue(Request $request)
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

    return view('gss.admin.add_disposal_value', [
        'unserviceableRecords' => $unserviceableRecords,
        'recordsWithDepreciation' => $recordsWithDepreciation,
    ]);
}









public function storeDisposalValue(Request $request)
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







    public function disposalDetails(Request $request)
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

        // Apply Search Filter
        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('equipment.property_number', 'LIKE', "%{$request->search}%")
                    ->orWhere('equipment.division', 'LIKE', "%{$request->search}%")
                    ->orWhere('equipment.particular', 'LIKE', "%{$request->search}%");
            });
        }

        // Apply Date Range Filter
        if ($request->has('from_date') && $request->has('to_date')) {
            $query->whereBetween('unserviceable.date_returned', [$request->from_date, $request->to_date]);
        }

        // Use `paginate()` instead of `get()`
        $disposalDetails = $query->paginate(10); // Adjust the number of items per page

        return view('gss.admin.disposal_details', compact('disposalDetails'));
    }


    public function exportToExcel(Request $request)
    {
        return Excel::download(new DisposalDetailsExport($request), 'Disposal_Details.xlsx');
    }

    public function downloadExcel(Request $request)
    {
        $division = $request->input('division');

        // Fetch the same filtered data as in the displayed table
        $query = Equipment::whereRaw('DATE_ADD(date_acquired, INTERVAL lifespan YEAR) <= CURDATE()');

        if (!empty($division)) {
            $query->where('division', $division);
        }

        // Get the same paginated records but without pagination
        $equipmentItems = $query->get();

        return Excel::download(new EquipmentExport($equipmentItems), 'filtered_equipment.xlsx');
    }

    public function fetchAllEquipment(Request $request)
    {
        $division = $request->input('division');

        // Fetch all equipment items based on filters
        $query = Equipment::query();

        if (!empty($division)) {
            $query->where('division', $division);
        }

        // Select only the required columns, excluding Image & Actions
        $equipmentItems = $query->select([
            'property_number', 'particular', 'description', 'division',
            'section', 'date_acquired', 'lifespan', 'date_end'
        ])->get();

        return response()->json($equipmentItems);
    }

    
}