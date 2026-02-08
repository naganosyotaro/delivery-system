<x-app-layout>
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('invoices.index') }}">請求書一覧</a></li>
                <li class="breadcrumb-item active">新規作成</li>
            </ol>
        </nav>
        <h1 class="h3 mb-0">請求書の作成</h1>
    </div>

    <!-- 顧客選択 -->
    @if(!$selectedCustomerId)
    <div class="card">
        <div class="card-header">
            <i class="bi bi-building me-2"></i>顧客を選択
        </div>
        <div class="card-body">
            <form action="{{ route('invoices.create') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-6">
                        <select name="customer_id" class="form-select" required>
                            <option value="">顧客を選択してください</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->company_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary">選択</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @else
    <form action="{{ route('invoices.store') }}" method="POST">
        @csrf
        <input type="hidden" name="customer_id" value="{{ $selectedCustomerId }}">

        <div class="row">
            <div class="col-lg-8">
                <!-- 未請求の配送一覧 -->
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="bi bi-box-seam me-2"></i>請求対象の配送
                        <span class="text-muted small">（配達完了・未請求分）</span>
                    </div>
                    <div class="card-body p-0">
                        @if($shipments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 30px;">
                                            <input type="checkbox" id="selectAll" class="form-check-input">
                                        </th>
                                        <th>伝票番号</th>
                                        <th>届け先</th>
                                        <th>配達日</th>
                                        <th class="text-end">料金</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($shipments as $shipment)
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="shipment_ids[]" value="{{ $shipment->id }}" 
                                                   class="form-check-input shipment-checkbox" 
                                                   data-amount="{{ $shipment->shipping_fee }}">
                                        </td>
                                        <td>{{ $shipment->tracking_number }}</td>
                                        <td>{{ $shipment->recipient_name }}</td>
                                        <td>{{ $shipment->updated_at->format('Y/m/d') }}</td>
                                        <td class="text-end">¥{{ number_format($shipment->shipping_fee) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="table-light">
                                        <td colspan="4" class="text-end fw-bold">選択合計</td>
                                        <td class="text-end fw-bold text-primary" id="totalAmount">¥0</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        @else
                        <div class="p-4 text-center text-muted">
                            未請求の配送データがありません
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- 請求期間 -->
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="bi bi-calendar me-2"></i>請求情報
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">請求期間（開始）<span class="text-danger">*</span></label>
                            <input type="date" name="billing_period_start" class="form-control @error('billing_period_start') is-invalid @enderror" 
                                   value="{{ old('billing_period_start', now()->startOfMonth()->format('Y-m-d')) }}" required>
                            @error('billing_period_start')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">請求期間（終了）<span class="text-danger">*</span></label>
                            <input type="date" name="billing_period_end" class="form-control @error('billing_period_end') is-invalid @enderror" 
                                   value="{{ old('billing_period_end', now()->endOfMonth()->format('Y-m-d')) }}" required>
                            @error('billing_period_end')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">支払期限 <span class="text-danger">*</span></label>
                            <input type="date" name="due_date" class="form-control @error('due_date') is-invalid @enderror" 
                                   value="{{ old('due_date', now()->addMonth()->format('Y-m-d')) }}" required>
                            @error('due_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg" id="submitBtn" disabled>
                        <i class="bi bi-file-text me-2"></i>請求書を作成
                    </button>
                    <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary">
                        キャンセル
                    </a>
                </div>
            </div>
        </div>
    </form>

    @push('scripts')
    <script>
        // 全選択チェックボックス
        document.getElementById('selectAll')?.addEventListener('change', function() {
            document.querySelectorAll('.shipment-checkbox').forEach(cb => {
                cb.checked = this.checked;
            });
            updateTotal();
        });

        // 個別チェックボックス
        document.querySelectorAll('.shipment-checkbox').forEach(cb => {
            cb.addEventListener('change', updateTotal);
        });

        // 合計金額を更新
        function updateTotal() {
            let total = 0;
            let count = 0;
            document.querySelectorAll('.shipment-checkbox:checked').forEach(cb => {
                total += parseInt(cb.dataset.amount);
                count++;
            });
            document.getElementById('totalAmount').textContent = '¥' + total.toLocaleString();
            document.getElementById('submitBtn').disabled = count === 0;
        }
    </script>
    @endpush
    @endif
</x-app-layout>
