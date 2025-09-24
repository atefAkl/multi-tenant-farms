<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Harvest extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'palm_tree_id',
        'harvest_date',
        'season',
        'total_quantity',
        'total_revenue',
        'notes',
    ];

    protected $casts = [
        'harvest_date' => 'date',
        'total_quantity' => 'decimal:2',
        'total_revenue' => 'decimal:2',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope('tenant', function ($query) {
            $query->where('tenant_id', tenant()->id ?? null);
        });
    }

    /**
     * Get the palm tree that was harvested.
     */
    public function palmTree(): BelongsTo
    {
        return $this->belongsTo(PalmTree::class);
    }

    /**
     * Get the harvest details for workers.
     */
    public function harvestDetails(): HasMany
    {
        return $this->hasMany(HarvestDetail::class);
    }

    /**
     * Get the formatted total revenue with currency.
     */
    public function getFormattedRevenueAttribute(): string
    {
        return number_format($this->total_revenue, 2) . ' ريال';
    }
}
