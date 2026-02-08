<x-app-layout>
    <div class="mb-4">
        <h1 class="h3 mb-0">本日の配送リスト</h1>
        <p class="text-muted">配送中・集荷済みの荷物一覧</p>
    </div>

    @if($shipments->isEmpty())
    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>
        現在、配送待ちの荷物はありません。
    </div>
    @else
    <div class="list-group">
        @foreach($shipments as $shipment)
        <div class="list-group-item">
            <div class="d-flex w-100 justify-content-between align-items-start">
                <div>
                    <h6 class="mb-1">
                        {{ $shipment->recipient_name }}
                        @php
                            $statusColors = [
                                'picked_up' => 'info',
                                'in_transit' => 'primary',
                            ];
                        @endphp
                        <span class="badge bg-{{ $statusColors[$shipment->status] ?? 'secondary' }} ms-2">
                            {{ $shipment->status_label }}
                        </span>
                    </h6>
                    <p class="mb-1 text-muted">
                        <i class="bi bi-geo-alt me-1"></i>{{ Str::limit($shipment->recipient_address, 50) }}
                    </p>
                    <small class="text-muted">
                        <i class="bi bi-telephone me-1"></i>{{ $shipment->recipient_phone ?: '-' }}
                    </small>
                </div>
                <div class="text-end">
                    <div class="fw-bold text-primary">{{ $shipment->tracking_number }}</div>
                    @if($shipment->preferred_delivery_at)
                    <small class="text-muted">
                        希望: {{ $shipment->preferred_delivery_at->format('H:i') }}
                    </small>
                    @endif
                </div>
            </div>
            <div class="mt-2">
                <a href="{{ route('driver.scan') }}?tracking={{ $shipment->tracking_number }}" 
                   class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-arrow-repeat me-1"></i>ステータス更新
                </a>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</x-app-layout>
