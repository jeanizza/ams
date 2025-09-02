<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('gss.admin.partials.sidebar', function ($view) {
            if (Auth::check()) {
                $user = Auth::user();
                $office = $user->office;
    
                // Count pending requests where office matches the equipment table
                $pendingRequestCount = DB::table('request_update')
                    ->join('equipment', 'request_update.property_number', '=', 'equipment.property_number')
                    ->where('request_update.status', 'Pending')
                    ->where('equipment.office', $office)
                    ->count();
    
                $pendingTransferCount = DB::table('request_transfer')
                    ->join('equipment', 'request_transfer.property_number', '=', 'equipment.property_number')
                    ->where('request_transfer.status', 'Pending')
                    ->where('equipment.office', $office)
                    ->count();
    
                $pendingUnserviceableCount = DB::table('request_unserviceable')
                    ->join('equipment', 'request_unserviceable.property_number', '=', 'equipment.property_number')
                    ->where('request_unserviceable.status', 'Pending')
                    ->where('equipment.office', $office)
                    ->count();
    
                // Total pending requests
                $totalPendingRequests = $pendingRequestCount + $pendingTransferCount + $pendingUnserviceableCount;
    
                // Share the variable with the sidebar view
                $view->with('totalPendingRequests', $totalPendingRequests);
            }
        });
    }
}
