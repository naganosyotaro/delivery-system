<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StatusUpdate extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'shipment_id',
        'user_id',
        'status',
        'notes',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * ステータスの日本語ラベルを取得
     */
    public function getStatusLabelAttribute(): string
    {
        return Shipment::STATUS_LABELS[$this->status] ?? $this->status;
    }
}
