<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Division;
use App\Models\Section;
use App\Models\AddRecord;

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
        return view('gss.admin.serviceable.add_record', compact('divisions'));
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
                'description' => 'required|string',
                'brand' => 'required|string',
                'model' => 'required|string',
                'serial_no' => 'required|string',
                'amount' => 'required|numeric',
                'date_acquired' => 'required|date',
                'po_number' => 'required|string',
                'end_user' => 'required|string',
                'position' => 'required|string',
                'division' => 'required|exists:division_pits,div_id',
                'section' => 'required|exists:section,sec_id',
                'actual_user' => 'required|string',
                'position_actual_user' => 'required|string',
                'status' => 'required|string',
                'fund' => 'required|string',
                'lifespan' => 'required|integer',
                'upload_image' => 'required|image|mimes:jpeg,png|max:2048',
            ]);

            $validatedData['division_id'] = $request->division;
            $validatedData['section_id'] = $request->section;
            $validatedData['uploaded_by'] = Auth::users_id();
            $validatedData['date_created'] = now();
            $validatedData['date_renewed'] = \Carbon\Carbon::parse($validatedData['date_acquired'])->addYears($validatedData['lifespan']);

            if ($request->hasFile('upload_image')) {
                $validatedData['upload_image'] = $request->file('upload_image')->store('uploads', 'public');
            }

            AddRecord::create($validatedData);

            return redirect()->route('gss.admin.add_record')->with('success', 'Record added successfully');
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
}