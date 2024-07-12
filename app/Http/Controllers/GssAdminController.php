<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GssAdminController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('gss.admin.dashboard', compact('user'));
    }

    public function par()
    {
        return view('gss.admin.serviceable.par');
    }

    public function ics()
    {
        return view('gss.admin.serviceable.ics');
    }

    public function ptr()
    {
        return view('gss.admin.serviceable.ptr');
    }

    public function itr()
    {
        return view('gss.admin.serviceable.itr');
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