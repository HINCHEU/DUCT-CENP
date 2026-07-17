<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $stats = [
            'rejected_orders' => 0,
            'awaiting_approval' => 0,
            'workshop_queued' => 0,
            'total_users' => 0,
        ];

        if ($user->hasRole('engineer') || $user->hasRole('super_admin')) {
            $query = Order::where('status', 'REJECTED');
            if (!$user->hasRole('super_admin')) {
                $query->where('created_by', $user->id);
            }
            $stats['rejected_orders'] = $query->count();
        }
        
        if ($user->hasRole('manager') || $user->hasRole('super_admin')) {
            $query = Order::where('status', 'SUBMITTED');
            if (!$user->hasRole('super_admin')) {
                $managedSiteIds = $user->managedSites()->pluck('id');
                $query->whereIn('site_id', $managedSiteIds);
            }
            $stats['awaiting_approval'] = $query->count();
        }
        
        if ($user->hasRole('workshop') || $user->hasRole('super_admin')) {
            $stats['workshop_queued'] = Order::whereIn('status', ['APPROVED', 'FABRICATING'])
                                            ->count();
        }
        
        if ($user->hasRole('admin') || $user->hasRole('super_admin')) {
            $stats['total_users'] = User::count();
        }

        return view('home', compact('stats'));
    }
}
