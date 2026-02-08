<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    /**
     * 請求書一覧
     */
    public function index(Request $request)
    {
        $query = Invoice::with('customer')
            ->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        $invoices = $query->paginate(20)->withQueryString();
        $customers = Customer::orderBy('company_name')->get();

        return view('invoices.index', compact('invoices', 'customers'));
    }

    /**
     * 請求書作成フォーム
     */
    public function create(Request $request)
    {
        $customers = Customer::orderBy('company_name')->get();
        $selectedCustomerId = $request->customer_id;

        // 未請求の配送を取得
        $shipments = collect();
        if ($selectedCustomerId) {
            $shipments = Shipment::where('customer_id', $selectedCustomerId)
                ->where('status', 'delivered')
                ->whereDoesntHave('invoiceItems')
                ->orderBy('created_at')
                ->get();
        }

        return view('invoices.create', compact('customers', 'selectedCustomerId', 'shipments'));
    }

    /**
     * 請求書作成処理
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'billing_period_start' => 'required|date',
            'billing_period_end' => 'required|date|after_or_equal:billing_period_start',
            'due_date' => 'required|date',
            'shipment_ids' => 'required|array|min:1',
            'shipment_ids.*' => 'exists:shipments,id',
        ]);

        DB::transaction(function () use ($validated) {
            // 請求書作成
            $invoice = Invoice::create([
                'customer_id' => $validated['customer_id'],
                'invoice_number' => Invoice::generateInvoiceNumber(),
                'billing_period_start' => $validated['billing_period_start'],
                'billing_period_end' => $validated['billing_period_end'],
                'due_date' => $validated['due_date'],
                'status' => 'pending',
                'total_amount' => 0,
            ]);

            // 明細を追加
            $totalAmount = 0;
            foreach ($validated['shipment_ids'] as $shipmentId) {
                $shipment = Shipment::find($shipmentId);
                if ($shipment) {
                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'shipment_id' => $shipment->id,
                        'description' => "配送 {$shipment->tracking_number} ({$shipment->recipient_name})",
                        'amount' => $shipment->shipping_fee,
                    ]);
                    $totalAmount += $shipment->shipping_fee;
                }
            }

            // 合計金額を更新
            $invoice->update(['total_amount' => $totalAmount]);
        });

        return redirect()
            ->route('invoices.index')
            ->with('success', '請求書を作成しました。');
    }

    /**
     * 請求書詳細
     */
    public function show(Invoice $invoice)
    {
        $invoice->load(['customer', 'items.shipment']);
        return view('invoices.show', compact('invoice'));
    }

    /**
     * 請求書編集（ステータス更新）
     */
    public function edit(Invoice $invoice)
    {
        return view('invoices.edit', compact('invoice'));
    }

    /**
     * 請求書更新
     */
    public function update(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,paid,overdue',
            'paid_at' => 'nullable|date',
        ]);

        if ($validated['status'] === 'paid' && !$validated['paid_at']) {
            $validated['paid_at'] = now();
        }

        $invoice->update($validated);

        return redirect()
            ->route('invoices.show', $invoice)
            ->with('success', '請求書を更新しました。');
    }

    /**
     * 請求書削除
     */
    public function destroy(Invoice $invoice)
    {
        $invoice->delete();

        return redirect()
            ->route('invoices.index')
            ->with('success', '請求書を削除しました。');
    }
}
