<x-app-layout>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">請求書一覧</h1>
        <a href="{{ route('invoices.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> 新規作成
        </a>
    </div>

    <!-- 検索フィルター -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('invoices.index') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">顧客</label>
                        <select name="customer_id" class="form-select">
                            <option value="">すべて</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->company_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">ステータス</label>
                        <select name="status" class="form-select">
                            <option value="">すべて</option>
                            @foreach(\App\Models\Invoice::STATUS_LABELS as $key => $label)
                                <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">検索</button>
                        <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary">クリア</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- 請求書一覧 -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>請求書番号</th>
                            <th>顧客</th>
                            <th>請求期間</th>
                            <th class="text-end">金額</th>
                            <th>支払期限</th>
                            <th>ステータス</th>
                            <th class="text-end">操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $invoice)
                        <tr>
                            <td>
                                <a href="{{ route('invoices.show', $invoice) }}" class="text-decoration-none fw-medium">
                                    {{ $invoice->invoice_number }}
                                </a>
                            </td>
                            <td>{{ $invoice->customer->company_name }}</td>
                            <td>
                                {{ $invoice->billing_period_start->format('Y/m/d') }} ～ 
                                {{ $invoice->billing_period_end->format('Y/m/d') }}
                            </td>
                            <td class="text-end fw-bold">¥{{ number_format($invoice->total_amount) }}</td>
                            <td>{{ $invoice->due_date->format('Y/m/d') }}</td>
                            <td>
                                @php
                                    $statusColors = [
                                        'pending' => 'warning',
                                        'paid' => 'success',
                                        'overdue' => 'danger',
                                    ];
                                @endphp
                                <span class="badge bg-{{ $statusColors[$invoice->status] ?? 'secondary' }}">
                                    {{ $invoice->status_label }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                請求書がありません
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($invoices->hasPages())
        <div class="card-footer">
            {{ $invoices->links() }}
        </div>
        @endif
    </div>
</x-app-layout>
