<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountingSectionController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('accounting.dashboard', compact('user'));
    }
}
