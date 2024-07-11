<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProcurementAdminController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('procurement.admin.dashboard', compact('user'));
    }
}
