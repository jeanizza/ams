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

        $equipmentItems = $query->get();
        \Log::info('Retrieved equipment items:', ['items' => $equipmentItems]);

        // Fetch divisions with equipment items due within the next 5 days and count them
        $divisions = Equipment::where('office', 'like',  '%' . $office . '%')
            ->where('date_end', '<=', $dateTo)
            ->where('status', 'serviceable')
            ->select('division')
            ->groupBy('division')
            ->get();
        
        \Log::info('Retrieved divisions:', ['divisions' => $divisions]);

        return view('gss.admin.table_date_end', compact('user', 'equipmentItems', 'divisions'));
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





    public function list_serviceable(Request $request)
    {
            $user = Auth::user();
            $query = DB::table('equipment')
                ->select('equipment_id', 'property_number', 'particular', 'description', 'office', 'end_user', 'division', 'section', 'amount', 'upload_image', 'property_type', 'status', 'serial_no', 'model', 'brand', 'date_acquired', 'po_number', 'position', 'actual_user', 'position_actual_user', 'remarks', 'fund', 'lifespan', 'date_end', 'uploaded_by')
                ->where(function ($q) use ($user) {
                    $q->where('office', 'like', '%' . $user->office . '%')
                      ->orWhere('office', 'like', '%region%')
                      ->orWhere('office', 'like', '%regional office%');
                });
        
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
        $serviceable = Equipment::where('equipment_id', $id)->first();

        // Check if serviceable item exists
        if (!$serviceable) {
            return null;
        }

        // Fetch divisions for the dropdown
        $divisions = Division::all();

        // Fetch div_id for the given division name
        $division = Division::where('div_name', $serviceable->division)->first();

        // Fetch sections based on the div_id
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
        Log::info('Request data received:', $request->all());

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
        $serviceable = Equipment::where('equipment_id', $id)->first();

        if (!$serviceable) {
            Log::error('Serviceable item not found.');
            return redirect()->back()->with('error', 'Serviceable item not found.');
        }

        // Filter the request data to include only necessary fields
        $commonData = $request->only([
            'category', 'particular', 'description', 'brand', 'model', 'serial_no', 'date_acquired',
            'po_number', 'end_user', 'position', 'actual_user', 'remarks', 'fund', 'lifespan', 'amount', 'date_renewed', 'division', 'section'
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

        Log::info('Filtered common data:', $commonData);

        try {
            // Update equipment table using Eloquent
            $serviceable->update($commonData);

            Log::info("Updated equipment table, ID: {$serviceable->equipment_id}");

            return redirect()->route('gss.admin.list_serviceable')->with(['success' => 'Serviceable record updated successfully', 'propertyNumber' => $serviceable->property_number]);
        } catch (\Exception $e) {
            Log::error('Error updating serviceable record: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error updating serviceable record.');
        }
    }

    public function transferServiceableForm($id)
    {
        $data = $this->fetchServiceableData($id);
        if (!$data) {
            return redirect()->back()->with('error', 'Serviceable item not found.');
        }
        return view('gss.admin.serviceable.transfer_serviceable', $data);
    }

    public function transferServiceable(Request $request, $id)
    {
        Log::info('Transfer request data received:', $request->all());

        // Fetch the serviceable item by ID from the view
        $serviceable = Equipment::where('equipment_id', $id)->first();

        if (!$serviceable) {
            Log::error('Serviceable item not found.');
            return redirect()->back()->with('error', 'Serviceable item not found.');
        }

        $transferData = $request->only([
            'property_number', 'end_user', 'position', 'transfer_office', 'transfer_division', 'transfer_enduser', 'transfer_position', 'transfer_condition', 'reason_transfer', 'date_transfer'
        ]);

        Log::info('Prepared transfer data:', $transferData);

        try {
            DB::transaction(function () use ($serviceable, $transferData) {
                // Insert into transfer table
                Transfer::create($transferData);
                Log::info('Inserted into transfer_data table:', $transferData);

                // Update status column in the equipment table
                $serviceable->update(['status' => 'transferred']);
                Log::info('Updated equipment table, ID:', $serviceable->equipment_id);
            });

            return redirect()->route('gss.admin.list_serviceable')->with('success', 'Serviceable record transferred successfully.');
        } catch (\Exception $e) {
            Log::error('Error transferring serviceable item: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error transferring serviceable item: ' . $e->getMessage());
        }
    }


    public function unserviceableForm($id)
    {
        $data = $this->fetchServiceableData($id);
        if (!$data) {
            return redirect()->back()->with('error', 'Serviceable item not found.');
        }
        return view('gss.admin.serviceable.unserviceable', $data);
    }

    public function unserviceableUpdate(Request $request, $id)
    {
        // Fetch the serviceable item by ID from the view
        $serviceable = Equipment::where('equipment_id', $id)->first();

        if (!$serviceable) {
            return redirect()->back()->with('error', 'Serviceable item not found.');
        }

        $commonData = $request->except('_token', '_method');

        try {
            DB::transaction(function () use ($serviceable, $commonData) {
                $serviceable->update(array_merge($commonData, ['status' => 'unserviceable']));
            });

            return redirect()->route('gss.admin.list_serviceable')->with('success', 'Serviceable record updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error marking serviceable item as unserviceable: ' . $e->getMessage());
        }
    }

    public function transferredItems(Request $request)
    {
        $user = Auth::user();
        $query = Equipment::where('status', 'transferred')
                        ->where('office', $user->office);

        // Calculate total amount for all transferred items
        $totalAmountAll = $query->sum('amount');

        // Default total amount for selected year to 0
        $totalAmountYear = 0;

        // If a year is selected, calculate the total amount for that year
        if ($request->has('year') && !empty($request->year)) {
            $year = $request->year;
            $totalAmountYear = $query->whereYear('date_acquired', $year)->sum('amount');
            \Log::info("Total Amount for Year $year: $totalAmountYear");
        }

        // Get distinct years from date_acquired
        $years = Equipment::where('status', 'transferred')
                        ->where('office', $user->office)
                        ->selectRaw('YEAR(date_acquired) as year')
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
                ->orWhere('office', 'like', '%' . $searchTerm . '%')
                ->orWhere('division', 'like', '%' . $searchTerm . '%')
                ->orWhere('amount', 'like', '%' . $searchTerm . '%');
            });
        }

        // Sort functionality
        if ($request->has('sort_by') && $request->has('sort_direction')) {
            $sortBy = $request->sort_by;
            $sortDirection = $request->sort_direction;
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->orderBy('date_acquired', 'desc');
        }

        // Paginate results
        $transferredItems = $query->paginate(20);

        return view('gss.admin.transferred.transferred_items', compact('transferredItems', 'totalAmountAll', 'totalAmountYear', 'years'));
    }

    public function unserviceableItems(Request $request)
    {
        $user = Auth::user();
        $query = Equipment::where('status', 'unserviceable')
                        ->where('office', $user->office);

        // Calculate total amount for all unserviceable items
        $totalAmountAll = $query->sum('amount');

        // Default total amount for selected year to 0
        $totalAmountYear = 0;

        // If a year is selected, calculate the total amount for that year
        if ($request->has('year') && !empty($request->year)) {
            $year = $request->year;
            $totalAmountYear = $query->whereYear('date_acquired', $year)->sum('amount');
            \Log::info("Total Amount for Year $year: $totalAmountYear");
        }

        // Get distinct years from date_acquired
        $years = Equipment::where('status', 'unserviceable')
                        ->where('office', $user->office)
                        ->selectRaw('YEAR(date_acquired) as year')
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
                ->orWhere('office', 'like', '%' . $searchTerm . '%')
                ->orWhere('division', 'like', '%' . $searchTerm . '%')
                ->orWhere('amount', 'like', '%' . $searchTerm . '%');
            });
        }

        // Sort functionality
        if ($request->has('sort_by') && $request->has('sort_direction')) {
            $sortBy = $request->sort_by;
            $sortDirection = $request->sort_direction;
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->orderBy('date_acquired', 'desc');
        }

        // Paginate results
        $unserviceableItems = $query->paginate(20);

        return view('gss.admin.unserviceable.unserviceable_items', compact('unserviceableItems', 'totalAmountAll', 'totalAmountYear', 'years'));
    }

    public function addMaintenanceDetails()
    {
        $user = Auth::user();
        $propertyNumbers = Equipment::where('status', 'serviceable')
                                    ->where('office', $user->office)
                                    ->get(['property_number', 'particular', 'division', 'end_user', 'serial_no', 'date_acquired', 'amount', 'model']);
        
        return view('gss.admin.maintenance.add_maintenance_details', compact('propertyNumbers'));
    }

    public function storeMaintenanceDetails(Request $request)
    {
        $request->validate([
            'property_number' => 'required',
            'date_created' => 'required|date',
            'quantity' => 'required|integer',
            'unit' => 'required|string|max:50',
            'particular' => 'required|string|max:255',
            'defects' => 'nullable|string',
            'po_number' => 'nullable|string|max:255',
            'supplier' => 'nullable|string|max:255',
            'unit_cost' => 'required|numeric',
            'total_amount' => 'required|numeric',
            'remarks' => 'nullable|string',
        ]);
    
        MaintenanceLedger::create($request->all());
    
        return redirect()->route('gss.admin.add_maintenance_details')->with('success', 'Maintenance details added successfully.');
    }

    public function getPropertyNumbers(Request $request)
    {
        $search = $request->input('query');
        $userOffice = auth()->user()->office; // Get the logged-in user's office

        $results = Equipment::where('property_number', 'LIKE', "%{$search}%")
                            ->where('office', $userOffice)
                            ->where('status', 'serviceable')
                            ->limit(5)
                            ->get(['property_number', 'particular', 'division', 'end_user', 'serial_no', 'date_acquired', 'amount', 'model']);

        return response()->json($results);
    }

    public function ledger()
    {
        return view('gss.admin.maintenance.ledger');
    }

    public function searchLedger(Request $request)
    {
        $propertyNumber = $request->input('property_number');

        // Query to fetch ledger details from the view
        $ledgerDetails = DB::table('ledger_details')
            ->where('property_number', $propertyNumber)
            ->get();

        if ($ledgerDetails->isEmpty()) {
            return redirect()->route('gss.admin.ledger')->with('message', 'Property Number has no ledger');
        }

        return view('gss.admin.maintenance.ledger', compact('ledgerDetails'));
    }

    // public function downloadLedger($propertyNumber)
    // {
    //     return Excel::download(new LedgerExport($propertyNumber), 'maintenance_ledger.xlsx');
    // }

    public function reconciliation()
    {
        return view('gss.admin.reconciliation');
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
    
}