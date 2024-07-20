<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Division;
use App\Models\Section;
use App\Models\AddRecord;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Storage;

class GssAdminController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('gss.admin.dashboard', compact('user'));
    }

    //Add Record
    public function add_record()
    {
        $divisions = Division::all();
        $lvCount = AddRecord::where('property_type', 'ICS')->where('amount', '<', 5000)->count();
        $hvCount = AddRecord::where('property_type', 'ICS')->where('amount', '>=', 5000)->count();
        $parCount = AddRecord::where('property_type', 'PAR')->count();
       
        return view('gss.admin.serviceable.add_record', compact('divisions', 'lvCount', 'hvCount', 'parCount'));
    }

        public function getSections($div_id)
        {
            $sections = Section::where('div_id', $div_id)->pluck('sec_name', 'sec_id');
            return response()->json($sections);
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
                'division' => 'required|exists:division_pits,div_id',
                'section' => 'required|exists:section,sec_id',
                'actual_user' => 'required|string|max:150',
                'position_actual_user' => 'required|string|max:150',
                'remarks' => 'required|string|max:150',
                'fund' => 'required|string|max:20',
                'lifespan' => 'required|integer|min:1',
                'upload_image' => 'required|image|mimes:jpeg,png|max:2048',
            ]);

             // Assign additional fields
            $validatedData['div_id'] = $request->division;
            $validatedData['sec_id'] = $request->section;
            $validatedData['uploaded_by'] = Auth::user()->name; // Save the name of the logged-in user
            $validatedData['date_created'] = now();
            $validatedData['date_renewed'] = Carbon::parse($validatedData['date_acquired'])->addMonths((int)$validatedData['lifespan']);

            // Handle file upload
            if ($request->hasFile('upload_image')) {
                $validatedData['upload_image'] = $request->file('upload_image')->store('uploads', 'public');
            }

            // Create the record and get the instance
            $record = AddRecord::create($validatedData); 

            // Generate and save the Excel file
           // $this->generateExcel($record->id);
            
             // Redirect with success message and property number
            return redirect()->route('gss.admin.add_record')->with(['success' => 'Record added successfully', 'propertyNumber' => $record->property_number]);
        
        }


    public function transfer_property()
    {
        return view('gss.admin.serviceable.transfer_property');
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