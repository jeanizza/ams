<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use App\Models\Division;
use App\Models\Section;
use App\Models\AddRecord;
use App\Models\Serviceable;
use App\Models\Equipment;
use App\Models\Transfer;

use PhpOffice\PhpSpreadsheet\IOFactory;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;




class GssAdminController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('gss.admin.dashboard', compact('user'));
    }

    // Add Record
    public function add_record()
    {
        $divisions = Division::all();
        $lvCount = AddRecord::where('property_type', 'ICS')->where('amount', '<', 5000)->count();
        $hvCount = AddRecord::where('property_type', 'ICS')->where('amount', '>=', 5000)->count();
        $parCount = AddRecord::where('property_type', 'PAR')->count();

        return view('gss.admin.serviceable.add_record', compact('divisions', 'lvCount', 'hvCount', 'parCount'));
    }

    public function getSections($div_name)
    {
        $division = Division::where('div_name', $div_name)->first();
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
        $validatedData['date_renewed'] = Carbon::parse($validatedData['date_acquired'])->addMonths((int)$validatedData['lifespan']);

        // Handle file upload
        if ($request->hasFile('upload_image')) {
            $validatedData['upload_image'] = $request->file('upload_image')->store('uploads', 'public');
        }

        // Create the record and get the instance
        $record = AddRecord::create($validatedData);

        // Redirect with success message and property number
        return redirect()->route('gss.admin.add_record')->with(['success' => 'Record added successfully', 'propertyNumber' => $record->property_number]);
    
}


    public function list_serviceable(Request $request)
    {

        $user = Auth::user();
        $query = DB::table('serviceable')
            ->where(function($q) use ($user) {
                $q->where('office', 'like', '%' . $user->office . '%')
                  ->orWhere('office', 'like', '%region%')
                  ->orWhere('office', 'like', '%regional office%');
            });

        // Search functionality
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
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
                    ->orWhere('date_renewed', 'like', '%' . $searchTerm . '%')
                    ->orWhere('uploaded_by', 'like', '%' . $searchTerm . '%');
            });
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

    public function updateServiceableForm($id)
{
    // Fetch the serviceable item by ID from the view
    $serviceable = DB::table('serviceable')->where('id', $id)->first();

    // Check if serviceable item exists and has division/section data
    if (!$serviceable) {
        return redirect()->back()->with('error', 'Serviceable item not found.');
    }

    // Fetch divisions for the dropdown
    $divisions = Division::all();

    // Fetch div_id for the given division name
    $division = Division::where('div_name', $serviceable->division)->first();

    // Fetch sections based on the div_id
    $sections = $division ? Section::where('div_id', $division->div_id)->get() : [];

    return view('gss.admin.serviceable.update_serviceable', compact('serviceable', 'divisions', 'sections'));
}

public function updateServiceable(Request $request, $id)
{
    \Log::info('Request data received:', $request->all());

     // Validate the input data
     $request->validate([
        'division' => 'required|string',
        'section' => 'required|string',
        'category' => 'nullable|string',
        'particular' => 'nullable|string',
        'description' => 'nullable|string',
        'brand' => 'nullable|string',
        'model' => 'nullable|string',
        'serial_no' => 'nullable|string',
        'date_acquired' => 'nullable|date',
        'po_number' => 'nullable|string',
        'end_user' => 'nullable|string',
        'position' => 'nullable|string',
        'actual_user' => 'nullable|string',
        'remarks' => 'nullable|string',
        'fund' => 'nullable|string',
        'lifespan' => 'nullable|integer',
        'amount' => 'nullable|numeric',
        'date_renewed' => 'nullable|date',
        'upload_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
    ]);

    // Fetch the serviceable item by ID from the view
    $serviceable = DB::table('serviceable')->where('id', $id)->first();

    if (!$serviceable) {
        \Log::error('Serviceable item not found.');
        return redirect()->back()->with('error', 'Serviceable item not found.');
    }

    // Check the source field to determine which table to update
    if (!isset($serviceable->source)) {
        \Log::error('Source field is not set.');
        return redirect()->back()->with('error', 'Serviceable item is invalid.');
    }

    // Filter the request data to include only necessary fields
    $commonData = $request->only([
        'category', 'particular', 'description', 'brand', 'model', 'serial_no', 'date_acquired',
        'po_number', 'end_user', 'position', 'actual_user', 'remarks', 'fund', 'lifespan', 'amount', 'date_renewed'
    ]);

    // Ensure amount is a float
    if (isset($commonData['amount'])) {
        $commonData['amount'] = (float)str_replace(',', '', $commonData['amount']);
    }

    // Handle date_renewed properly
    $commonData['date_renewed'] = ($request->input('date_renewed') == '0000-00-00' || empty($request->input('date_renewed'))) ? null : $request->input('date_renewed');

    // Check if upload_image is provided and include it in commonData
    if ($request->hasFile('upload_image')) {
        $commonData['upload_image'] = $request->file('upload_image')->store('uploads', 'public');
    }

    \Log::info('Filtered common data:', $commonData);

    try {
        if ($serviceable->source === 'add_record') {
            // Map division and section to names for add_record table
            $commonData['div_name'] = $request->input('division');
            $commonData['sec_name'] = $request->input('section');

            if (empty($commonData['sec_name'])) {
                \Log::error('Section name cannot be empty.');
                return redirect()->back()->with('error', 'Section name cannot be empty.');
            }

            $commonData['position_actual_user'] = $request->input('position_actual_user');

            \Log::info('Data with division and section names for add_record:', $commonData);

            // Update add_record table
            $affectedRows = DB::table('add_record')
                ->where('add_record_id', $serviceable->id)
                ->update($commonData);

            \Log::info("Updated add_record table, ID: {$serviceable->id}, Affected Rows: {$affectedRows}");
        } elseif ($serviceable->source === 'equipment') {
            // Map division and section to names for equipment table
            $commonData['division'] = $request->input('division');
            $commonData['section'] = $request->input('section');

            if (empty($commonData['section'])) {
                \Log::error('Section name cannot be empty.');
                return redirect()->back()->with('error', 'Section name cannot be empty.');
            }

            \Log::info('Data with names for equipment:', $commonData);

            // Ensure commonData keys match equipment columns
            $columns = \Schema::getColumnListing('equipment');
            $filteredData = array_intersect_key($commonData, array_flip($columns));
            \Log::info('Filtered common data after column match:', $filteredData);

            // Update equipment table using Eloquent
            $equipment = Equipment::find($serviceable->id);

            if (!$equipment) {
                \Log::error('Equipment item not found.');
                return redirect()->back()->with('error', 'Equipment item not found.');
            }

            $equipment->update($filteredData);

            \Log::info("Updated equipment table, ID: {$serviceable->id}");
        } else {
            \Log::error('Unable to determine the source table.');
            return redirect()->back()->with('error', 'Unable to determine the source table.');
        }

        return redirect()->route('gss.admin.list_serviceable')->with(['success' => 'Serviceable record updated successfully', 'propertyNumber' => $serviceable->property_number]);
    } catch (\Exception $e) {
        \Log::error('Error updating serviceable record: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Error updating serviceable record.');
    }
}



    public function transferServiceableForm($id)
    {
        // Fetch the serviceable item by ID
        $serviceable = DB::table('serviceable')->where('id', $id)->first();

        // Fetch divisions for the dropdown
        $divisions = Division::all();

        // Fetch div_id for the given division name
        $division = Division::where('div_name', $serviceable->division)->first();

        // Fetch sections based on the div_id
        $sections = $division ? Section::where('div_id', $division->div_id)->get() : [];

        return view('gss.admin.serviceable.transfer_serviceable', compact('serviceable', 'divisions', 'sections'));
    }

    public function transferServiceable(Request $request, $id)
    {
            \Log::info('Transfer request data received:', $request->all());
        
            // Fetch the serviceable item by ID from the view
            $serviceable = DB::table('serviceable')->where('id', $id)->first();
        
            if (!$serviceable) {
                \Log::error('Serviceable item not found.');
                return redirect()->back()->with('error', 'Serviceable item not found.');
            }
        
            $transferData = $request->only([
                'property_number', 'end_user', 'position', 'transfer_office',
                'division as transfer_division', 'transfer_enduser', 'transfer_position', 
                'transfer_condition', 'reason_transfer', 'date_transfer'
            ]);
        
            \Log::info('Prepared transfer data:', $transferData);
        
            try {
                DB::transaction(function () use ($serviceable, $transferData) {
                    // Insert into transfer table
                    Transfer::create($transferData);
                    \Log::info('Inserted into transfer_data table:', $transferData);
        
                    // Update remarks column in the source table
                    if ($serviceable->source_table == 'add_record') {
                        DB::table('add_record')->where('id', $serviceable->id)->update(['remarks' => 'transfer']);
                        \Log::info('Updated add_record table, ID:', $serviceable->id);
                    } else {
                        DB::table('equipment')->where('id', $serviceable->id)->update(['remarks' => 'transfer']);
                        \Log::info('Updated equipment table, ID:', $serviceable->id);
                    }
                });
        
                return redirect()->route('gss.admin.list_serviceable')->with('success', 'Serviceable record transferred successfully.');
            } catch (\Exception $e) {
                \Log::error('Error transferring serviceable item: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Error transferring serviceable item: ' . $e->getMessage());
            }
        }

    public function unserviceableForm($id)
    {
        // Fetch the serviceable item by ID
    $serviceable = DB::table('serviceable')->where('id', $id)->first();

    // Fetch divisions for the dropdown
    $divisions = Division::all();

    // Fetch div_id for the given division name
    $division = Division::where('div_name', $serviceable->division)->first();

    // Fetch sections based on the div_id
    $sections = $division ? Section::where('div_id', $division->div_id)->get() : [];

    return view('gss.admin.serviceable.unserviceable', compact('serviceable', 'divisions', 'sections'));
    }

    public function unserviceableUpdate(Request $request, $id)
    {
// Fetch the serviceable item by ID from the view
$serviceable = DB::table('serviceable')->where('id', $id)->first();

if (!$serviceable) {
    return redirect()->back()->with('error', 'Serviceable item not found.');
}

$commonData = $request->except('_token', '_method');

try {
    DB::transaction(function () use ($serviceable, $commonData) {
        if ($serviceable->source_table == 'add_record') {
            DB::table('add_record')->where('id', $serviceable->id)->update(array_merge($commonData, ['status' => 'unserviceable']));
        } else {
            DB::table('equipment')->where('id', $serviceable->id)->update(array_merge($commonData, ['status' => 'unserviceable']));
        }
    });

    return redirect()->route('gss.admin.list_serviceable')->with('success', 'Serviceable record updated successfully.');
} catch (\Exception $e) {
    return redirect()->back()->with('error', 'Error marking serviceable item as unserviceable: ' . $e->getMessage());
}
    }

    public function unserviceable()
    {
        return view('gss.admin.unserviceable');
    }

    public function maintenanceLedger()
    {
        return view('gss.admin.maintenance_ledger');
    }

    public function reconciliation()
    {
        return view('gss.admin.reconciliation');
    }

    public function generatePdf($propertyNumber)
    {
        $record = AddRecord::where('property_number', $propertyNumber)->first();

        if (!$record) {
            return redirect()->back()->with('error', 'Record not found.');
        }

        // Create the HTML content for the PDF
        $htmlContent = view('gss.admin.serviceable.serviceable_template', compact('record'))->render();

        // Set up Dompdf options
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        // Initialize Dompdf
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($htmlContent);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Output the generated PDF to a file
        $filename = 'Record_' . $record->property_number . '.pdf';
        $filePath = storage_path('app/public/' . $filename);

        try {
            file_put_contents($filePath, $dompdf->output());
        } catch (Exception $e) {
            \Log::error('File put contents error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to save PDF.');
        }

        \Log::info('File path: ' . $filePath);
        \Log::info('File exists: ' . (file_exists($filePath) ? 'yes' : 'no'));

        if (file_exists($filePath)) {
            return response()->download($filePath)->deleteFileAfterSend(true);
        } else {
            \Log::error('PDF file not found after creation: ' . $filePath);
            return redirect()->back()->with('error', 'File not found.');
        }
    }
    
}