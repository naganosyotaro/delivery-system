<x-app-layout>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">発送一覧</h1>
        <a href="{{ route('shipments.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> 新規登録
        </a>
    </div>

    <!-- 検索フィルター -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('shipments.index') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">伝票番号</label>
                        <input type="text" name="tracking_number" class="form-control" 
                               value="{{ request('tracking_number') }}" placeholder="伝票番号を検索">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">届け先名</label>
                        <input type="text" name="recipient_name" class="form-control" 
                               value="{{ request('recipient_name') }}" placeholder="届け先名を検索">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">ステータス</label>
                        <select name="status" class="form-select">
                            <option value="">すべて</option>
                            @foreach(\App\Models\Shipment::STATUS_LABELS as $key => $label)
                                <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">開始日</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">終了日</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search me-1"></i> 検索
                    </button>
                    <a href="{{ route('shipments.index') }}" class="btn btn-outline-secondary">
                        クリア
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- 発送一覧 -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>伝票番号</th>
                            <th>顧客</th>
                            <th>届け先</th>
                            <th>サイズ</th>
                            <th>料金</th>
                            <th>ステータス</th>
                            <th>登録日</th>
                            <th class="text-end">操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($shipments as $shipment)
                        <tr>
                            <td>
                                <a href="{{ route('shipments.show', $shipment) }}" class="fw-medium text-decoration-none">
                                    {{ $shipment->tracking_number }}
                                </a>
                            </td>
                            <td>{{ $shipment->customer?->company_name ?? '-' }}</td>
                            <td>
                                <div>{{ $shipment->recipient_name }}</div>
                                <small class="text-muted">{{ Str::limit($shipment->recipient_address, 30) }}</small>
                            </td>
                            <td>{{ $shipment->size_label }}</td>
                            <td>¥{{ number_format($shipment->shipping_fee) }}</td>
                            <td>
                                @php
                                    $statusColors = [
                                        'pending' => 'secondary',
                                        'picked_up' => 'info',
                                        'in_transit' => 'primary',
                                        'delivered' => 'success',
                                        'undelivered' => 'danger',
                                        'storage' => 'warning',
                                    ];
                                @endphp
                                <span class="badge bg-{{ $statusColors[$shipment->status] ?? 'secondary' }} status-badge">
                                    {{ $shipment->status_label }}
                                </span>
                            </td>
                            <td>{{ $shipment->created_at->format('Y/m/d') }}</td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('shipments.show', $shipment) }}" class="btn btn-outline-primary" title="詳細">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('shipments.edit', $shipment) }}" class="btn btn-outline-secondary" title="編集">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="{{ route('shipments.pdf', $shipment) }}" class="btn btn-outline-success" title="PDF" target="_blank">
                                        <i class="bi bi-file-pdf"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                発送データがありません
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($shipments->hasPages())
        <div class="card-footer">
            {{ $shipments->links() }}
        </div>
        @endif
    </div>
</x-app-layout>
