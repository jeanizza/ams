<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GssUserController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('gss.user.dashboard', compact('user'));
    }
}
