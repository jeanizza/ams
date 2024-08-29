<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProcurementAdminController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $username = $user->username;

        //return view('procurement.admin.dashboard', compact('user'));
        return redirect()->to('http://localhost:8080/admin?username='.$username);
    }
}
