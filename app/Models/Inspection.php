<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inspection extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'palm_tree_id',
        'worker_id',
        'inspection_date',
        'health_status',
        'notes',
        'recommendations',
    ];

    protected $casts = [
        'inspection_date' => 'date',
    ];

    /**
     * Get the palm tree that was inspected.
     */
    public function palmTree(): BelongsTo
    {
        return $this->belongsTo(PalmTree::class);
    }

    /**
     * Get the worker who performed the inspection.
     */
    public function worker(): BelongsTo
    {
        return $this->belongsTo(Worker::class);
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope('tenant', function ($query) {
            $query->where('tenant_id', tenant()->id ?? null);
        });
    }
}
