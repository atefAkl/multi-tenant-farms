<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Worker extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'name',
        'national_id',
        'phone',
        'farm_id',
        'block_id',
        'role_in_farm',
        'employment_status',
        'salary',
        'hire_date',
        'notes',
    ];

    protected $casts = [
        'salary' => 'decimal:2',
        'hire_date' => 'date',
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
     * Get the farm that the worker belongs to.
     */
    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    /**
     * Get the block that the worker is assigned to.
     */
    public function block(): BelongsTo
    {
        return $this->belongsTo(Block::class);
    }

    /**
     * Get the inspections done by the worker.
     */
    public function inspections(): HasMany
    {
        return $this->hasMany(Inspection::class);
    }

    /**
     * Get the treatments done by the worker.
     */
    public function treatments(): HasMany
    {
        return $this->hasMany(Treatment::class);
    }

    /**
     * Get the harvest details for the worker.
     */
    public function harvestDetails(): HasMany
    {
        return $this->hasMany(HarvestDetail::class);
    }

    /**
     * Get the resource movements done by the worker.
     */
    public function resourceMovements(): HasMany
    {
        return $this->hasMany(ResourceMovement::class);
    }

    /**
     * Scope a query to only include active workers.
     */
    public function scopeActive($query)
    {
        return $query->where('employment_status', 'active');
    }
}
