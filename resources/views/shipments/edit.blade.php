<x-app-layout>
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('shipments.index') }}">発送一覧</a></li>
                <li class="breadcrumb-item"><a href="{{ route('shipments.show', $shipment) }}">{{ $shipment->tracking_number }}</a></li>
                <li class="breadcrumb-item active">編集</li>
            </ol>
        </nav>
        <h1 class="h3 mb-0">発送情報の編集</h1>
    </div>

    <form action="{{ route('shipments.update', $shipment) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- 左カラム -->
            <div class="col-lg-8">
                <!-- 発送者情報 -->
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="bi bi-person me-2"></i>発送者情報
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">発送者名 <span class="text-danger">*</span></label>
                                <input type="text" name="sender_name" class="form-control @error('sender_name') is-invalid @enderror" 
                                       value="{{ old('sender_name', $shipment->sender_name) }}" required>
                                @error('sender_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">電話番号</label>
                                <input type="tel" name="sender_phone" class="form-control @error('sender_phone') is-invalid @enderror" 
                                       value="{{ old('sender_phone', $shipment->sender_phone) }}">
                                @error('sender_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">住所 <span class="text-danger">*</span></label>
                                <textarea name="sender_address" class="form-control @error('sender_address') is-invalid @enderror" 
                                          rows="2" required>{{ old('sender_address', $shipment->sender_address) }}</textarea>
                                @error('sender_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
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
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">届け先名 <span class="text-danger">*</span></label>
                                <input type="text" name="recipient_name" class="form-control @error('recipient_name') is-invalid @enderror" 
                                       value="{{ old('recipient_name', $shipment->recipient_name) }}" required>
                                @error('recipient_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">電話番号</label>
                                <input type="tel" name="recipient_phone" class="form-control @error('recipient_phone') is-invalid @enderror" 
                                       value="{{ old('recipient_phone', $shipment->recipient_phone) }}">
                                @error('recipient_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">住所 <span class="text-danger">*</span></label>
                                <textarea name="recipient_address" class="form-control @error('recipient_address') is-invalid @enderror" 
                                          rows="2" required>{{ old('recipient_address', $shipment->recipient_address) }}</textarea>
                                @error('recipient_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
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
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">品名</label>
                                <input type="text" name="item_name" class="form-control @error('item_name') is-invalid @enderror" 
                                       value="{{ old('item_name', $shipment->item_name) }}">
                                @error('item_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">サイズ <span class="text-danger">*</span></label>
                                <select name="size" class="form-select @error('size') is-invalid @enderror" required>
                                    @foreach(\App\Models\Shipment::SIZE_LABELS as $key => $label)
                                        <option value="{{ $key }}" {{ old('size', $shipment->size) == $key ? 'selected' : '' }}>
                                            {{ $label }} ({{ $key }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('size')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">個数 <span class="text-danger">*</span></label>
                                <input type="number" name="quantity" class="form-control @error('quantity') is-invalid @enderror" 
                                       value="{{ old('quantity', $shipment->quantity) }}" min="1" required>
                                @error('quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">重量 (kg)</label>
                                <input type="number" name="weight" class="form-control @error('weight') is-invalid @enderror" 
                                       value="{{ old('weight', $shipment->weight) }}" step="0.1" min="0">
                                @error('weight')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">配送希望日時</label>
                                <input type="datetime-local" name="preferred_delivery_at" 
                                       class="form-control @error('preferred_delivery_at') is-invalid @enderror" 
                                       value="{{ old('preferred_delivery_at', $shipment->preferred_delivery_at?->format('Y-m-d\TH:i')) }}">
                                @error('preferred_delivery_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">備考</label>
                                <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" 
                                          rows="3">{{ old('notes', $shipment->notes) }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 右カラム -->
            <div class="col-lg-4">
                <!-- 顧客・料金 -->
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="bi bi-building me-2"></i>顧客・料金
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">顧客</label>
                            <select name="customer_id" class="form-select @error('customer_id') is-invalid @enderror">
                                <option value="">顧客を選択（任意）</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ old('customer_id', $shipment->customer_id) == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->company_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('customer_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">配送料金 (円)</label>
                            <input type="number" name="shipping_fee" class="form-control @error('shipping_fee') is-invalid @enderror" 
                                   value="{{ old('shipping_fee', $shipment->shipping_fee) }}" min="0">
                            @error('shipping_fee')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- 更新ボタン -->
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-check-lg me-2"></i>更新する
                    </button>
                    <a href="{{ route('shipments.show', $shipment) }}" class="btn btn-outline-secondary">
                        キャンセル
                    </a>
                </div>
            </div>
        </div>
    </form>
</x-app-layout>
