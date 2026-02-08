<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Shipment;
use App\Models\StatusUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShipmentController extends Controller
{
    /**
     * 発送一覧
     */
    public function index(Request $request)
    {
        $query = Shipment::with(['customer', 'creator'])
            ->orderBy('created_at', 'desc');

        // 検索フィルター
        if ($request->filled('tracking_number')) {
            $query->where('tracking_number', 'like', '%' . $request->tracking_number . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('recipient_name')) {
            $query->where('recipient_name', 'like', '%' . $request->recipient_name . '%');
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $shipments = $query->paginate(20)->withQueryString();

        return view('shipments.index', compact('shipments'));
    }

    /**
     * 発送登録フォーム
     */
    public function create()
    {
        $customers = Customer::orderBy('company_name')->get();
        return view('shipments.create', compact('customers'));
    }

    /**
     * 発送登録処理
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'sender_name' => 'required|string|max:255',
            'sender_address' => 'required|string',
            'sender_phone' => 'nullable|string|max:20',
            'recipient_name' => 'required|string|max:255',
            'recipient_address' => 'required|string',
            'recipient_phone' => 'nullable|string|max:20',
            'item_name' => 'nullable|string|max:255',
            'size' => 'required|in:S,M,L,XL',
            'weight' => 'nullable|numeric|min:0',
            'quantity' => 'required|integer|min:1',
            'preferred_delivery_at' => 'nullable|date',
            'notes' => 'nullable|string',
            'shipping_fee' => 'nullable|numeric|min:0',
        ]);

        $validated['tracking_number'] = Shipment::generateTrackingNumber();
        $validated['created_by'] = Auth::id();
        $validated['status'] = 'pending';

        $shipment = Shipment::create($validated);

        // ステータス履歴を記録
        StatusUpdate::create([
            'shipment_id' => $shipment->id,
            'user_id' => Auth::id(),
            'status' => 'pending',
            'notes' => '発送受付',
        ]);

        return redirect()
            ->route('shipments.show', $shipment)
            ->with('success', '発送情報を登録しました。伝票番号: ' . $shipment->tracking_number);
    }

    /**
     * 発送詳細
     */
    public function show(Shipment $shipment)
    {
        $shipment->load(['customer', 'creator', 'statusUpdates.user']);
        return view('shipments.show', compact('shipment'));
    }

    /**
     * 発送編集フォーム
     */
    public function edit(Shipment $shipment)
    {
        $customers = Customer::orderBy('company_name')->get();
        return view('shipments.edit', compact('shipment', 'customers'));
    }

    /**
     * 発送更新処理
     */
    public function update(Request $request, Shipment $shipment)
    {
        $validated = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'sender_name' => 'required|string|max:255',
            'sender_address' => 'required|string',
            'sender_phone' => 'nullable|string|max:20',
            'recipient_name' => 'required|string|max:255',
            'recipient_address' => 'required|string',
            'recipient_phone' => 'nullable|string|max:20',
            'item_name' => 'nullable|string|max:255',
            'size' => 'required|in:S,M,L,XL',
            'weight' => 'nullable|numeric|min:0',
            'quantity' => 'required|integer|min:1',
            'preferred_delivery_at' => 'nullable|date',
            'notes' => 'nullable|string',
            'shipping_fee' => 'nullable|numeric|min:0',
        ]);

        $shipment->update($validated);

        return redirect()
            ->route('shipments.show', $shipment)
            ->with('success', '発送情報を更新しました。');
    }

    /**
     * ステータス更新
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

        return redirect()
            ->back()
            ->with('success', 'ステータスを更新しました。');
    }

    /**
     * 発送削除
     */
    public function destroy(Shipment $shipment)
    {
        $shipment->delete();

        return redirect()
            ->route('shipments.index')
            ->with('success', '発送情報を削除しました。');
    }

    /**
     * 伝票PDF出力
     */
    public function pdf(Shipment $shipment)
    {
        // QRコード生成
        $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(100)
            ->generate($shipment->tracking_number);

        // バーコード生成
        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
        $barcode = base64_encode($generator->getBarcode($shipment->tracking_number, $generator::TYPE_CODE_128));

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('shipments.pdf', [
            'shipment' => $shipment,
            'qrCode' => $qrCode,
            'barcode' => $barcode,
        ]);

        return $pdf->download("伝票_{$shipment->tracking_number}.pdf");
    }
}
