<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentChecklist extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'document_type', 'description', 'is_required', 'applicable_for', 'country', 'order'
    ];

    protected $casts = [
        'is_required' => 'boolean',
    ];

    /**
     * Scope: checklist items applicable for a given country.
     * Returns items where country is null (applies to all) or matches the given country.
     */
    public function scopeForCountry(Builder $query, ?string $country): Builder
    {
        return $query->where(function ($q) use ($country) {
            $q->whereNull('country');
            if ($country !== null && $country !== '') {
                $q->orWhere('country', $country);
            }
        });
    }
}
