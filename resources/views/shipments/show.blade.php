<x-app-layout>
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('shipments.index') }}">発送一覧</a></li>
                <li class="breadcrumb-item active">{{ $shipment->tracking_number }}</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <!-- 左カラム -->
        <div class="col-lg-8">
            <!-- 伝票情報 -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-receipt me-2"></i>伝票情報</span>
                    <div class="btn-group btn-group-sm">
                        <a href="{{ route('shipments.edit', $shipment) }}" class="btn btn-outline-primary">
                            <i class="bi bi-pencil me-1"></i>編集
                        </a>
                        <a href="{{ route('shipments.pdf', $shipment) }}" class="btn btn-outline-success" target="_blank">
                            <i class="bi bi-file-pdf me-1"></i>PDF出力
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <strong class="text-muted">伝票番号</strong>
                                <div class="fs-4 fw-bold">{{ $shipment->tracking_number }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <strong class="text-muted">ステータス</strong>
                                <div>
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
                                    <span class="badge bg-{{ $statusColors[$shipment->status] ?? 'secondary' }} fs-6">
                                        {{ $shipment->status_label }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 発送者情報 -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-person me-2"></i>発送者情報
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <strong class="text-muted">氏名</strong>
                            <div>{{ $shipment->sender_name }}</div>
                        </div>
                        <div class="col-md-6">
                            <strong class="text-muted">電話番号</strong>
                            <div>{{ $shipment->sender_phone ?: '-' }}</div>
                        </div>
                        <div class="col-12 mt-3">
                            <strong class="text-muted">住所</strong>
                            <div>{{ $shipment->sender_address }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 届け先情報 -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-geo-alt me-2"></i>届け先情報
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <strong class="text-muted">氏名</strong>
                            <div>{{ $shipment->recipient_name }}</div>
                        </div>
                        <div class="col-md-6">
                            <strong class="text-muted">電話番号</strong>
                            <div>{{ $shipment->recipient_phone ?: '-' }}</div>
                        </div>
                        <div class="col-12 mt-3">
                            <strong class="text-muted">住所</strong>
                            <div>{{ $shipment->recipient_address }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 荷物情報 -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-box me-2"></i>荷物情報
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong class="text-muted">品名</strong>
                            <div>{{ $shipment->item_name ?: '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <strong class="text-muted">サイズ</strong>
                            <div>{{ $shipment->size_label }}</div>
                        </div>
                        <div class="col-md-3">
                            <strong class="text-muted">重量</strong>
                            <div>{{ $shipment->weight ? $shipment->weight . ' kg' : '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <strong class="text-muted">個数</strong>
                            <div>{{ $shipment->quantity }}個</div>
                        </div>
                        <div class="col-md-6 mt-3">
                            <strong class="text-muted">配送希望日時</strong>
                            <div>{{ $shipment->preferred_delivery_at?->format('Y/m/d H:i') ?? '-' }}</div>
                        </div>
                        <div class="col-md-6 mt-3">
                            <strong class="text-muted">配送料金</strong>
                            <div class="fs-5 fw-bold text-primary">¥{{ number_format($shipment->shipping_fee) }}</div>
                        </div>
                        @if($shipment->notes)
                        <div class="col-12 mt-3">
                            <strong class="text-muted">備考</strong>
                            <div class="bg-light p-2 rounded">{{ $shipment->notes }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- ステータス履歴 -->
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-clock-history me-2"></i>ステータス履歴
                </div>
                <div class="card-body">
                    @forelse($shipment->statusUpdates as $update)
                    <div class="d-flex mb-3">
                        <div class="me-3">
                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-2" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-check"></i>
                            </div>
                        </div>
                        <div>
                            <div class="fw-medium">{{ \App\Models\Shipment::STATUS_LABELS[$update->status] ?? $update->status }}</div>
                            <div class="text-muted small">
                                {{ $update->created_at->format('Y/m/d H:i') }} - {{ $update->user->name }}
                            </div>
                            @if($update->notes)
                            <div class="text-muted small">{{ $update->notes }}</div>
                            @endif
                        </div>
                    </div>
                    @empty
                    <p class="text-muted mb-0">履歴がありません</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- 右カラム -->
        <div class="col-lg-4">
            <!-- ステータス更新 -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-arrow-repeat me-2"></i>ステータス更新
                </div>
                <div class="card-body">
                    <form action="{{ route('shipments.status', $shipment) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">新しいステータス</label>
                            <select name="status" class="form-select" required>
                                @foreach(\App\Models\Shipment::STATUS_LABELS as $key => $label)
                                    <option value="{{ $key }}" {{ $shipment->status == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">メモ</label>
                            <textarea name="notes" class="form-control" rows="2"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-check-lg me-1"></i>更新
                        </button>
                    </form>
                </div>
            </div>

            <!-- 顧客情報 -->
            @if($shipment->customer)
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-building me-2"></i>顧客情報
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <strong>{{ $shipment->customer->company_name }}</strong>
                    </div>
                    <div class="text-muted small">{{ $shipment->customer->contact_name }}</div>
                    <div class="text-muted small">{{ $shipment->customer->phone }}</div>
                </div>
            </div>
            @endif

            <!-- 登録情報 -->
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-info-circle me-2"></i>登録情報
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <strong class="text-muted">登録者</strong>
                        <div>{{ $shipment->creator->name }}</div>
                    </div>
                    <div class="mb-2">
                        <strong class="text-muted">登録日時</strong>
                        <div>{{ $shipment->created_at->format('Y/m/d H:i') }}</div>
                    </div>
                    <div>
                        <strong class="text-muted">最終更新</strong>
                        <div>{{ $shipment->updated_at->format('Y/m/d H:i') }}</div>
                    </div>
                </div>
            </div>

            <!-- 削除 -->
            <div class="mt-4">
                <form action="{{ route('shipments.destroy', $shipment) }}" method="POST" 
                      onsubmit="return confirm('本当に削除しますか？')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger w-100">
                        <i class="bi bi-trash me-1"></i>この発送を削除
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
