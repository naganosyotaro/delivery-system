<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * 顧客一覧
     */
    public function index(Request $request)
    {
        $query = Customer::withCount('shipments')
            ->orderBy('company_name');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('contact_name', 'like', "%{$search}%");
            });
        }

        $customers = $query->paginate(20)->withQueryString();

        return view('customers.index', compact('customers'));
    }

    /**
     * 顧客登録フォーム
     */
    public function create()
    {
        return view('customers.create');
    }

    /**
     * 顧客登録処理
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        Customer::create($validated);

        return redirect()
            ->route('customers.index')
            ->with('success', '顧客を登録しました。');
    }

    /**
     * 顧客詳細
     */
    public function show(Customer $customer)
    {
        $customer->load(['shipments' => function($query) {
            $query->orderBy('created_at', 'desc')->limit(10);
        }, 'invoices' => function($query) {
            $query->orderBy('created_at', 'desc')->limit(5);
        }]);

        return view('customers.show', compact('customer'));
    }

    /**
     * 顧客編集フォーム
     */
    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    /**
     * 顧客更新処理
     */
    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $customer->update($validated);

        return redirect()
            ->route('customers.show', $customer)
            ->with('success', '顧客情報を更新しました。');
    }

    /**
     * 顧客削除
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()
            ->route('customers.index')
            ->with('success', '顧客を削除しました。');
    }
}
