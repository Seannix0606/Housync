<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\FirebaseSyncTrait;

class Apartment extends Model
{
    use HasFactory, FirebaseSyncTrait;

    protected $fillable = [
        'name',
        'address',
        'description',
        'landlord_id',
        'total_units',
        'amenities',
        'contact_person',
        'contact_phone',
        'contact_email',
        'status',
    ];

    protected $casts = [
        'amenities' => 'array',
        'total_units' => 'integer',
    ];

    // Relationships
    public function landlord()
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    public function units()
    {
        return $this->hasMany(Unit::class);
    }

    // Helper methods
    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isInactive()
    {
        return $this->status === 'inactive';
    }

    public function isUnderMaintenance()
    {
        return $this->status === 'maintenance';
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByLandlord($query, $landlordId)
    {
        return $query->where('landlord_id', $landlordId);
    }

    // Statistics
    public function getOccupiedUnitsCount()
    {
        return $this->units()->where('status', 'occupied')->count();
    }

    public function getAvailableUnitsCount()
    {
        return $this->units()->where('status', 'available')->count();
    }

    public function getMaintenanceUnitsCount()
    {
        return $this->units()->where('status', 'maintenance')->count();
    }

    public function getOccupancyRate()
    {
        $totalUnits = $this->units()->count();
        if ($totalUnits === 0) return 0;
        
        $occupiedUnits = $this->getOccupiedUnitsCount();
        return round(($occupiedUnits / $totalUnits) * 100, 2);
    }

    public function getTotalRevenue()
    {
        return $this->units()->where('status', 'occupied')->sum('rent_amount');
    }
}
