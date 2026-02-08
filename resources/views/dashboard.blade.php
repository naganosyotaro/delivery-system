<x-app-layout>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">ダッシュボード</h1>
        <a href="{{ route('shipments.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> 新規発送登録
        </a>
    </div>

    <!-- 統計カード -->
    <div class="row mb-4">
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="card stat-card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary me-3">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <div>
                        <div class="text-muted small">本日の受付</div>
                        <div class="fs-4 fw-bold">{{ $stats['today_shipments'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="card stat-card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon bg-warning bg-opacity-10 text-warning me-3">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                    <div>
                        <div class="text-muted small">受付待ち</div>
                        <div class="fs-4 fw-bold">{{ $stats['pending'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="card stat-card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon bg-info bg-opacity-10 text-info me-3">
                        <i class="bi bi-truck"></i>
                    </div>
                    <div>
                        <div class="text-muted small">配送中</div>
                        <div class="fs-4 fw-bold">{{ $stats['in_transit'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="card stat-card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon bg-success bg-opacity-10 text-success me-3">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div>
                        <div class="text-muted small">本日完了</div>
                        <div class="fs-4 fw-bold">{{ $stats['delivered_today'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($stats['undelivered'] > 0)
    <div class="alert alert-warning mb-4">
        <i class="bi bi-exclamation-triangle me-2"></i>
        <strong>{{ $stats['undelivered'] }}件</strong>の未配達（不在）があります。
    </div>
    @endif

    <!-- 最近の発送 -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-clock-history me-2"></i>最近の発送</span>
            <a href="{{ route('shipments.index') }}" class="btn btn-sm btn-outline-primary">すべて表示</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>伝票番号</th>
                            <th>届け先</th>
                            <th>サイズ</th>
                            <th>ステータス</th>
                            <th>登録日時</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentShipments as $shipment)
                        <tr>
                            <td>
                                <a href="{{ route('shipments.show', $shipment) }}" class="fw-medium text-decoration-none">
                                    {{ $shipment->tracking_number }}
                                </a>
                            </td>
                            <td>{{ $shipment->recipient_name }}</td>
                            <td>{{ $shipment->size_label }}</td>
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
                            <td>{{ $shipment->created_at->format('m/d H:i') }}</td>
                            <td>
                                <a href="{{ route('shipments.show', $shipment) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                発送データがありません
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
