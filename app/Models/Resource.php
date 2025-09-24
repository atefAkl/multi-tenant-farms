<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Resource extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'sku',
        'name',
        'category',
        'unit',
        'stock_qty',
        'cost_price',
        'selling_price',
        'barcode',
        'location',
        'min_stock_level',
        'max_stock_level',
        'description',
    ];

    protected $casts = [
        'stock_qty' => 'decimal:3',
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'min_stock_level' => 'decimal:3',
        'max_stock_level' => 'decimal:3',
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
     * Get the movements for the resource.
     */
    public function movements(): HasMany
    {
        return $this->hasMany(ResourceMovement::class);
    }

    /**
     * Get the treatments that use this resource.
     */
    public function treatmentResources(): HasMany
    {
        return $this->hasMany(\App\Models\TreatmentResource::class);
    }

    /**
     * Check if resource is low on stock.
     */
    public function isLowStock(): bool
    {
        return $this->stock_qty <= ($this->min_stock_level ?? 0);
    }

    /**
     * Check if resource is out of stock.
     */
    public function isOutOfStock(): bool
    {
        return $this->stock_qty <= 0;
    }
}
