<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        switch ($user->office) {
            case 'procurement services section':
                if ($user->role === 'admin') {
                    return redirect()->route('procurement.admin.dashboard');
                } elseif ($user->role === 'user') {
                    return redirect()->route('procurement.user.dashboard');
                }
                break;

            case 'general services section':
                if ($user->role === 'admin') {
                    return redirect()->route('gss.admin.dashboard');
                } elseif ($user->role === 'user') {
                    return redirect()->route('gss.user.dashboard');
                }
                break;

            case 'accounting section':
                return redirect()->route('accounting.dashboard');

            default:
                if ($user->role === 'admin') {
                    return redirect()->route('admin.dashboard');
                } elseif ($user->role === 'user') {
                    return redirect()->route('user.dashboard');
                }
                break;
        }

        return abort(403, 'Unauthorized action.');
    }
}
