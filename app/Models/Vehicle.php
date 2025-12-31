<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vehicle extends Model
{
    use HasFactory;

    protected $guarded = []; // or specify fillable if you prefer

    protected $casts = [
        'is_approved'     => 'boolean',
        'year'            => 'integer',
        'created_at'      => 'datetime',
        'updated_at'      => 'datetime',
    ];

    // Relationships
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    // Optional: if you have User through Driver
    public function owner()
    {
        return $this->driver->user();
    }

    // Accessors (very useful for frontend)
    public function getFullNameAttribute(): string
    {
        return "{$this->brand} {$this->model} ({$this->year})";
    }

    public function getPlateFormattedAttribute(): string
    {
        return strtoupper($this->plate_number);
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopeActive($query)
    {
        return $query->whereHas('driver', fn($q) => $q->where('is_active', true));
    }
}
