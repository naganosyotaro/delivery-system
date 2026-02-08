<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $fillable = [
        'customer_id',
        'invoice_number',
        'billing_period_start',
        'billing_period_end',
        'total_amount',
        'status',
        'due_date',
        'paid_at',
    ];

    protected $casts = [
        'billing_period_start' => 'date',
        'billing_period_end' => 'date',
        'due_date' => 'date',
        'paid_at' => 'date',
        'total_amount' => 'decimal:2',
    ];

    /**
     * ステータスの日本語ラベル
     */
    public const STATUS_LABELS = [
        'pending' => '未払い',
        'paid' => '支払済',
        'overdue' => '延滞',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
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
     * 請求書番号を生成
     */
    public static function generateInvoiceNumber(): string
    {
        $date = now()->format('Ym');
        $lastInvoice = self::where('invoice_number', 'like', "INV-{$date}-%")
            ->orderBy('id', 'desc')
            ->first();

        if ($lastInvoice) {
            $lastNumber = (int) substr($lastInvoice->invoice_number, -4);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return "INV-{$date}-" . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
