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
}