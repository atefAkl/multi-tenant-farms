<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResourceMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'resource_id',
        'movement_type',
        'quantity',
        'reason',
        'notes',
        'worker_id',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
    ];

    /**
     * Get the resource that was moved.
     */
    public function resource(): BelongsTo
    {
        return $this->belongsTo(Resource::class);
    }

    /**
     * Get the worker who performed the movement.
     */
    public function worker(): BelongsTo
    {
        return $this->belongsTo(Worker::class);
    }

    /**
     * Get the movement type label.
     */
    public function getMovementTypeLabelAttribute(): string
    {
        return match ($this->movement_type) {
            'stock_in' => 'إدخال مخزون',
            'stock_out' => 'إخراج مخزون',
            'treatment_usage' => 'استخدام في علاج',
            'adjustment' => 'تعديل',
            'damaged' => 'تالف',
            'expired' => 'منتهي الصلاحية',
            default => $this->movement_type,
        };
    }
}
