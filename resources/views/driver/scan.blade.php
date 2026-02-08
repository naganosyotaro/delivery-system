<x-app-layout>
    <div class="mb-4">
        <h1 class="h3 mb-0">ステータス更新</h1>
        <p class="text-muted">QRコードをスキャンするか、伝票番号を入力してください</p>
    </div>

    <!-- QRスキャナー（カメラ使用） -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="bi bi-qr-code-scan me-2"></i>QRコードスキャン
        </div>
        <div class="card-body text-center">
            <div id="qr-reader" style="width: 100%; max-width: 400px; margin: 0 auto;"></div>
            <div id="qr-reader-results" class="mt-3"></div>
        </div>
    </div>

    <!-- 手動入力 -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="bi bi-keyboard me-2"></i>伝票番号を入力
        </div>
        <div class="card-body">
            <form id="searchForm">
                <div class="input-group">
                    <input type="text" id="trackingNumber" class="form-control form-control-lg" 
                           placeholder="伝票番号を入力" pattern="[0-9]{13}" required>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> 検索
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 検索結果・ステータス更新フォーム -->
    <div id="shipmentDetails" class="card d-none">
        <div class="card-header">
            <i class="bi bi-box-seam me-2"></i>配送情報
        </div>
        <div class="card-body">
            <div id="shipmentInfo"></div>
            
            <hr>
            
            <form id="statusForm" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-bold">ステータスを変更</label>
                    <div class="row g-2">
                        @foreach(\App\Models\Shipment::STATUS_LABELS as $key => $label)
                        <div class="col-6 col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status" 
                                       id="status_{{ $key }}" value="{{ $key }}">
                                <label class="form-check-label" for="status_{{ $key }}">
                                    {{ $label }}
                                </label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="notes" class="form-label">メモ</label>
                    <textarea name="notes" id="notes" class="form-control" rows="2" 
                              placeholder="不在理由など"></textarea>
                </div>

                <input type="hidden" name="latitude" id="latitude">
                <input type="hidden" name="longitude" id="longitude">
                
                <button type="submit" class="btn btn-primary btn-lg w-100">
                    <i class="bi bi-check-lg me-2"></i>ステータスを更新
                </button>
            </form>
        </div>
    </div>

    @push('scripts')
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        let currentShipmentId = null;

        // 位置情報を取得
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                document.getElementById('latitude').value = position.coords.latitude;
                document.getElementById('longitude').value = position.coords.longitude;
            });
        }

        // QRスキャナーの初期化
        const html5QrCode = new Html5Qrcode("qr-reader");
        const qrConfig = { fps: 10, qrbox: { width: 250, height: 250 } };

        html5QrCode.start(
            { facingMode: "environment" },
            qrConfig,
            (decodedText) => {
                html5QrCode.stop();
                document.getElementById('trackingNumber').value = decodedText;
                searchShipment(decodedText);
            },
            (errorMessage) => {
                // スキャンエラーは無視
            }
        ).catch(err => {
            document.getElementById('qr-reader').innerHTML = 
                '<div class="alert alert-warning">カメラにアクセスできません。伝票番号を手動で入力してください。</div>';
        });

        // 手動検索フォーム
        document.getElementById('searchForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const trackingNumber = document.getElementById('trackingNumber').value;
            searchShipment(trackingNumber);
        });

        // 配送情報を検索
        function searchShipment(trackingNumber) {
            fetch(`/api/shipments/search?tracking_number=${trackingNumber}`)
                .then(response => response.json())
                .then(data => {
                    if (data.shipment) {
                        displayShipment(data.shipment);
                    } else {
                        alert('配送情報が見つかりません');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('検索中にエラーが発生しました');
                });
        }

        // 配送情報を表示
        function displayShipment(shipment) {
            currentShipmentId = shipment.id;
            
            const statusLabels = {
                'pending': '受付',
                'picked_up': '集荷済',
                'in_transit': '配送中',
                'delivered': '配達完了',
                'undelivered': '不在',
                'storage': '保管中'
            };

            document.getElementById('shipmentInfo').innerHTML = `
                <div class="row">
                    <div class="col-6">
                        <strong>伝票番号</strong>
                        <div class="fs-5">${shipment.tracking_number}</div>
                    </div>
                    <div class="col-6">
                        <strong>現在のステータス</strong>
                        <div class="fs-5">${statusLabels[shipment.status] || shipment.status}</div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-12">
                        <strong>届け先</strong>
                        <div>${shipment.recipient_name}</div>
                        <div class="text-muted small">${shipment.recipient_address}</div>
                        <div class="text-muted small">${shipment.recipient_phone || ''}</div>
                    </div>
                </div>
            `;

            // 現在のステータスを選択
            const currentStatus = document.querySelector(`input[value="${shipment.status}"]`);
            if (currentStatus) {
                currentStatus.checked = true;
            }

            document.getElementById('shipmentDetails').classList.remove('d-none');
            document.getElementById('statusForm').action = `/driver/shipments/${shipment.id}/status`;
        }

        // ステータス更新フォーム
        document.getElementById('statusForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!currentShipmentId) {
                alert('配送情報を先に検索してください');
                return;
            }

            const formData = new FormData(this);

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('ステータスを更新しました');
                    location.reload();
                } else {
                    alert('更新に失敗しました');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // フォームを通常送信
                this.submit();
            });
        });
    </script>
    @endpush
</x-app-layout>
