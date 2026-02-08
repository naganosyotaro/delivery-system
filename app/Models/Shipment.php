<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shipment extends Model
{
    protected $fillable = [
        'tracking_number',
        'customer_id',
        'created_by',
        'sender_name',
        'sender_address',
        'sender_phone',
        'recipient_name',
        'recipient_address',
        'recipient_phone',
        'item_name',
        'size',
        'weight',
        'quantity',
        'preferred_delivery_at',
        'notes',
        'status',
        'shipping_fee',
    ];

    protected $casts = [
        'preferred_delivery_at' => 'datetime',
        'shipping_fee' => 'decimal:2',
        'weight' => 'decimal:2',
    ];

    /**
     * ステータスの日本語ラベル
     */
    public const STATUS_LABELS = [
        'pending' => '受付',
        'picked_up' => '集荷済',
        'in_transit' => '配送中',
        'delivered' => '配達完了',
        'undelivered' => '不在',
        'storage' => '保管中',
    ];

    /**
     * サイズの日本語ラベル
     */
    public const SIZE_LABELS = [
        'S' => '小',
        'M' => '中',
        'L' => '大',
        'XL' => '特大',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function statusUpdates(): HasMany
    {
        return $this->hasMany(StatusUpdate::class)->orderBy('created_at', 'desc');
    }

    public function invoiceItems(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    /**
     * ステータスの日本語ラベルを取得
     */
    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    /**
     * サイズの日本語ラベルを取得
     */
    public function getSizeLabelAttribute(): string
    {
        return self::SIZE_LABELS[$this->size] ?? $this->size;
    }

    /**
     * 伝票番号を生成
     */
    public static function generateTrackingNumber(): string
    {
        $date = now()->format('ymd');
        
        // 最後の伝票番号を取得（全体から）
        $lastShipment = self::where('tracking_number', 'like', $date . '%')
            ->orderBy('tracking_number', 'desc')
            ->first();

        if ($lastShipment) {
            $lastNumber = (int) substr($lastShipment->tracking_number, 6);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        $trackingNumber = $date . str_pad($nextNumber, 7, '0', STR_PAD_LEFT);
        
        // 念のため重複チェック
        while (self::where('tracking_number', $trackingNumber)->exists()) {
            $nextNumber++;
            $trackingNumber = $date . str_pad($nextNumber, 7, '0', STR_PAD_LEFT);
        }
        
        return $trackingNumber;
    }
}
