<?php

use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// 配送検索API（QRスキャン用）
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/shipments/search', function (Request $request) {
        $trackingNumber = $request->query('tracking_number');
        
        $shipment = Shipment::where('tracking_number', $trackingNumber)->first();
        
        if (!$shipment) {
            return response()->json(['error' => '配送情報が見つかりません'], 404);
        }

        return response()->json(['shipment' => $shipment]);
    });
});
