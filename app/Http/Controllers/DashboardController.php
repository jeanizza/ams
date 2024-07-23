<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->div_name === 'Administrative Division') {
            if ($user->sec_name === 'General Services Section') {
                if ($user->role === 'admin') {
                    return redirect()->route('gss.admin.dashboard');
                } elseif ($user->role === 'user') {
                    return redirect()->route('gss.user.dashboard');
                }
            } elseif ($user->sec_name === 'Procurement Services Section') {
                if ($user->role === 'admin') {
                    return redirect()->route('procurement.admin.dashboard');
                } elseif ($user->role === 'user') {
                    return redirect()->route('procurement.user.dashboard');
                }
            }
        } elseif ($user->div_name === 'Finance Division') {
            if ($user->role === 'admin') {
                return redirect()->route('accounting.admin.dashboard');
            } elseif ($user->role === 'user') {
                return redirect()->route('accounting.user.dashboard');
            }
        }

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role === 'user') {
            return redirect()->route('user.dashboard');
        }

        return abort(403, 'Unauthorized action.');
    }
    
}
