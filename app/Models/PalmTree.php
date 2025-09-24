<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PalmTree extends Model
{
    use HasFactory;

    protected $fillable = [
        'block_id',
        'tree_code',
        'row_no',
        'col_no',
        'stage_id',
        'variety',
        'planting_date',
        'status',
    ];

    protected $casts = [
        'planting_date' => 'date',
    ];

    /**
     * Get the block that owns the palm tree.
     */
    public function block(): BelongsTo
    {
        return $this->belongsTo(Block::class);
    }

    /**
     * Get the stage of the palm tree.
     */
    public function stage(): BelongsTo
    {
        return $this->belongsTo(PalmStage::class);
    }

    /**
     * Get the inspections for the palm tree.
     */
    public function inspections(): HasMany
    {
        return $this->hasMany(Inspection::class);
    }

    /**
     * Get the treatments for the palm tree.
     */
    public function treatments(): HasMany
    {
        return $this->hasMany(Treatment::class);
    }

    /**
     * Get the harvests for the palm tree.
     */
    public function harvests(): HasMany
    {
        return $this->hasMany(Harvest::class);
    }

    /**
     * Scope a query to only include active palm trees.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
