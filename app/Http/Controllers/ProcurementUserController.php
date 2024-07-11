<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProcurementUserController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('procurement.user.dashboard', compact('user'));
    }
}
