<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>配送伝票 {{ $shipment->tracking_number }}</title>
    <style>
        @font-face {
            font-family: 'ipag';
            src: url('{{ storage_path('fonts/ipag.ttf') }}');
            font-weight: normal;
            font-style: normal;
        }
        body {
            font-family: 'ipag', sans-serif;
            font-size: 12pt;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24pt;
            letter-spacing: 5px;
        }
        .tracking-number {
            font-size: 20pt;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
            padding: 10px;
            background-color: #f0f0f0;
            border: 2px solid #333;
        }
        .codes {
            text-align: center;
            margin: 20px 0;
        }
        .codes img {
            margin: 10px;
        }
        .section {
            margin-bottom: 20px;
            border: 1px solid #ccc;
            padding: 15px;
        }
        .section-title {
            font-weight: bold;
            font-size: 14pt;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        .row {
            margin-bottom: 8px;
        }
        .label {
            color: #666;
            font-size: 10pt;
        }
        .value {
            font-weight: bold;
        }
        .two-column {
            width: 100%;
        }
        .two-column td {
            width: 50%;
            vertical-align: top;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table th, .info-table td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        .info-table th {
            background-color: #f5f5f5;
            width: 100px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10pt;
            color: #666;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            background-color: #333;
            color: #fff;
            font-weight: bold;
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>配 送 伝 票</h1>
    </div>

    <div class="tracking-number">
        伝票番号: {{ $shipment->tracking_number }}
    </div>

    <div class="codes">
        <div style="display: inline-block; text-align: center; margin-right: 30px;">
            <div>{!! $qrCode !!}</div>
            <div style="font-size: 10pt; color: #666;">QRコード</div>
        </div>
        <div style="display: inline-block; text-align: center;">
            <div><img src="data:image/png;base64,{{ $barcode }}" alt="バーコード" style="height: 50px;"></div>
            <div style="font-size: 10pt; color: #666;">バーコード</div>
        </div>
    </div>

    <table class="two-column">
        <tr>
            <td>
                <div class="section">
                    <div class="section-title">発送者</div>
                    <table class="info-table">
                        <tr>
                            <th>氏名</th>
                            <td>{{ $shipment->sender_name }}</td>
                        </tr>
                        <tr>
                            <th>電話</th>
                            <td>{{ $shipment->sender_phone ?: '-' }}</td>
                        </tr>
                        <tr>
                            <th>住所</th>
                            <td>{{ $shipment->sender_address }}</td>
                        </tr>
                    </table>
                </div>
            </td>
            <td>
                <div class="section">
                    <div class="section-title">届け先</div>
                    <table class="info-table">
                        <tr>
                            <th>氏名</th>
                            <td><strong>{{ $shipment->recipient_name }}</strong></td>
                        </tr>
                        <tr>
                            <th>電話</th>
                            <td>{{ $shipment->recipient_phone ?: '-' }}</td>
                        </tr>
                        <tr>
                            <th>住所</th>
                            <td><strong>{{ $shipment->recipient_address }}</strong></td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <div class="section">
        <div class="section-title">荷物情報</div>
        <table class="info-table">
            <tr>
                <th>品名</th>
                <td>{{ $shipment->item_name ?: '-' }}</td>
                <th>サイズ</th>
                <td>{{ $shipment->size_label }}</td>
                <th>個数</th>
                <td>{{ $shipment->quantity }}個</td>
            </tr>
            <tr>
                <th>重量</th>
                <td>{{ $shipment->weight ? $shipment->weight . ' kg' : '-' }}</td>
                <th>希望日時</th>
                <td colspan="3">{{ $shipment->preferred_delivery_at?->format('Y/m/d H:i') ?? '-' }}</td>
            </tr>
            @if($shipment->notes)
            <tr>
                <th>備考</th>
                <td colspan="5">{{ $shipment->notes }}</td>
            </tr>
            @endif
        </table>
    </div>

    <div class="section">
        <div class="section-title">配送料金</div>
        <div style="font-size: 24pt; font-weight: bold; text-align: center;">
            ¥{{ number_format($shipment->shipping_fee) }}
        </div>
    </div>

    <div class="footer">
        <p>発行日: {{ now()->format('Y年m月d日 H:i') }}</p>
        <p>配送管理システム</p>
    </div>
</body>
</html>
