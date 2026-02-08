<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Models\StatusUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DriverController extends Controller
{
    /**
     * QRスキャン・ステータス更新画面
     */
    public function scan()
    {
        return view('driver.scan');
    }

    /**
     * ドライバーの担当配送一覧
     */
    public function shipments()
    {
        $shipments = Shipment::whereIn('status', ['picked_up', 'in_transit'])
            ->orderBy('preferred_delivery_at')
            ->orderBy('created_at')
            ->get();

        return view('driver.shipments', compact('shipments'));
    }

    /**
     * ステータス更新（API）
     */
    public function updateStatus(Request $request, Shipment $shipment)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,picked_up,in_transit,delivered,undelivered,storage',
            'notes' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $shipment->update(['status' => $validated['status']]);

        StatusUpdate::create([
            'shipment_id' => $shipment->id,
            'user_id' => Auth::id(),
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'ステータスを更新しました',
                'shipment' => $shipment->fresh(),
            ]);
        }

        return redirect()->back()->with('success', 'ステータスを更新しました。');
    }
}
