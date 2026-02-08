<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'today_shipments' => Shipment::whereDate('created_at', today())->count(),
            'pending' => Shipment::where('status', 'pending')->count(),
            'in_transit' => Shipment::where('status', 'in_transit')->count(),
            'delivered_today' => Shipment::where('status', 'delivered')
                ->whereDate('updated_at', today())->count(),
            'undelivered' => Shipment::where('status', 'undelivered')->count(),
        ];

        $recentShipments = Shipment::with(['customer', 'creator'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('dashboard', compact('stats', 'recentShipments'));
    }
}
