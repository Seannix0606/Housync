<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_number',
        'owner_name',
        'unit_type',
        'rent_amount',
        'status',
        'leasing_type',
        'tenant_count',
        'description',
        'floor_area',
        'bedrooms',
        'bathrooms',
        'is_furnished',
        'amenities',
        'notes',
    ];

    protected $casts = [
        'rent_amount' => 'decimal:2',
        'floor_area' => 'decimal:2',
        'is_furnished' => 'boolean',
        'amenities' => 'array',
        'tenant_count' => 'integer',
        'bedrooms' => 'integer',
        'bathrooms' => 'integer',
    ];

    // Scopes for filtering
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopeOccupied($query)
    {
        return $query->where('status', 'occupied');
    }

    public function scopeUnderMaintenance($query)
    {
        return $query->where('status', 'maintenance');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('unit_type', $type);
    }

    public function scopeRentRange($query, $min, $max)
    {
        return $query->whereBetween('rent_amount', [$min, $max]);
    }

    // Helper methods
    public function getFormattedRentAttribute()
    {
        return '₱' . number_format($this->rent_amount, 2);
    }

    public function getIsAvailableAttribute()
    {
        return $this->status === 'available';
    }

    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'occupied' => 'occupied',
            'available' => 'available',
            'maintenance' => 'maintenance',
            default => 'available'
        };
    }

    // Leasing type helper methods
    public function getLeasingTypeLabelAttribute()
    {
        return match($this->leasing_type) {
            'separate' => 'Separate Bills',
            'inclusive' => 'All Inclusive',
            default => 'Separate Bills'
        };
    }

    public function getLeasingTypeDescriptionAttribute()
    {
        return match($this->leasing_type) {
            'separate' => 'Tenant pays rent + utilities separately',
            'inclusive' => 'Rent includes all utilities and bills',
            default => 'Tenant pays rent + utilities separately'
        };
    }

    public function isInclusiveLeasing()
    {
        return $this->leasing_type === 'inclusive';
    }

    public function isSeparateLeasing()
    {
        return $this->leasing_type === 'separate';
    }
}
