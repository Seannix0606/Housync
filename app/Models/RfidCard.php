<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

/**
 * @property int $id
 * @property string $card_uid
 * @property string|null $card_number
 * @property int|null $tenant_id
 * @property int $landlord_id
 * @property int|null $unit_id
 * @property int|null $apartment_id
 * @property string $status
 * @property Carbon|null $assigned_at
 * @property Carbon|null $activated_at
 * @property Carbon|null $deactivated_at
 * @property Carbon|null $last_used_at
 * @property string|null $notes
 * @property bool $access_common_areas
 * @property bool $access_building
 * @property bool $access_parking
 * @property array|null $access_schedule
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class RfidCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'card_uid',
        'card_number',
        'tenant_id',
        'landlord_id',
        'unit_id',
        'apartment_id',
        'status',
        'assigned_at',
        'activated_at',
        'deactivated_at',
        'last_used_at',
        'notes',
        'access_common_areas',
        'access_building',
        'access_parking',
        'access_schedule',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'activated_at' => 'datetime',
        'deactivated_at' => 'datetime',
        'last_used_at' => 'datetime',
        'access_common_areas' => 'boolean',
        'access_building' => 'boolean',
        'access_parking' => 'boolean',
        'access_schedule' => 'array',
    ];

    // Relationships
    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    public function landlord()
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }

    public function securityLogs()
    {
        return $this->hasMany(SecurityLog::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function scopeByTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeByLandlord($query, $landlordId)
    {
        return $query->where('landlord_id', $landlordId);
    }

    public function scopeByUnit($query, $unitId)
    {
        return $query->where('unit_id', $unitId);
    }

    public function scopeByApartment($query, $apartmentId)
    {
        return $query->where('apartment_id', $apartmentId);
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

    public function isSuspended()
    {
        return $this->status === 'suspended';
    }

    public function isLost()
    {
        return $this->status === 'lost';
    }

    public function activate()
    {
        $this->update([
            'status' => 'active',
            'activated_at' => now(),
            'deactivated_at' => null,
        ]);
    }

    public function deactivate()
    {
        $this->update([
            'status' => 'inactive',
            'deactivated_at' => now(),
        ]);
    }

    public function suspend()
    {
        $this->update([
            'status' => 'suspended',
            'deactivated_at' => now(),
        ]);
    }

    public function markAsLost()
    {
        $this->update([
            'status' => 'lost',
            'deactivated_at' => now(),
        ]);
    }

    public function updateLastUsed()
    {
        $this->update(['last_used_at' => now()]);
    }

    public function assignToTenant($tenantId, $unitId = null, $apartmentId = null)
    {
        $this->update([
            'tenant_id' => $tenantId,
            'unit_id' => $unitId,
            'apartment_id' => $apartmentId,
            'assigned_at' => now(),
            'status' => 'active',
            'activated_at' => now(),
        ]);
    }

    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'active' => 'success',
            'inactive' => 'secondary',
            'suspended' => 'warning',
            'lost' => 'danger',
            default => 'secondary'
        };
    }

    // Check if card has access at current time
    public function hasCurrentAccess()
    {
        if (!$this->isActive()) {
            return false;
        }

        if (!$this->access_schedule) {
            return true; // No time restrictions
        }

        $currentTime = now();
        $currentDay = strtolower($currentTime->format('l')); // monday, tuesday, etc.
        $currentHour = $currentTime->format('H:i');

        if (isset($this->access_schedule[$currentDay])) {
            $daySchedule = $this->access_schedule[$currentDay];
            
            if (isset($daySchedule['start']) && isset($daySchedule['end'])) {
                return $currentHour >= $daySchedule['start'] && $currentHour <= $daySchedule['end'];
            }
        }

        return false;
    }
}
