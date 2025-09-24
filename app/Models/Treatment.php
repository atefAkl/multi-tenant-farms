<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Treatment extends Model
{
    use HasFactory;

    protected $fillable = [
        'palm_tree_id',
        'worker_id',
        'treatment_date',
        'treatment_type',
        'description',
        'cost',
        'effectiveness',
    ];

    protected $casts = [
        'treatment_date' => 'date',
        'cost' => 'decimal:2',
    ];

    /**
     * Get the palm tree that was treated.
     */
    public function palmTree(): BelongsTo
    {
        return $this->belongsTo(PalmTree::class);
    }

    /**
     * Get the worker who performed the treatment.
     */
    public function worker(): BelongsTo
    {
        return $this->belongsTo(Worker::class);
    }

    /**
     * Get the resources used in the treatment.
     */
    public function resources(): HasMany
    {
        return $this->hasMany(TreatmentResource::class);
    }

    /**
     * Get the total cost of resources used.
     */
    public function getTotalResourceCostAttribute(): float
    {
        return $this->resources->sum('cost');
    }
}
