<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PalmStage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'min_age_years',
        'max_age_years',
        'expected_yield',
        'is_active',
    ];

    protected $casts = [
        'min_age_years' => 'integer',
        'max_age_years' => 'integer',
        'expected_yield' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the palm trees for this stage.
     */
    public function palmTrees(): HasMany
    {
        return $this->hasMany(PalmTree::class, 'stage_id');
    }

    /**
     * Scope a query to only include active stages.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the stage age range as a formatted string.
     */
    public function getAgeRangeAttribute(): string
    {
        if ($this->min_age_years && $this->max_age_years) {
            return $this->min_age_years . ' - ' . $this->max_age_years . ' سنة';
        } elseif ($this->min_age_years) {
            return $this->min_age_years . '+ سنة';
        } elseif ($this->max_age_years) {
            return 'حتى ' . $this->max_age_years . ' سنة';
        }

        return 'غير محدد';
    }
}
