<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

/**
 * @property int $id
 * @property string|null $card_uid
 * @property int|null $rfid_card_id
 * @property int|null $tenant_id
 * @property int|null $unit_id
 * @property int|null $apartment_id
 * @property string|null $location
 * @property string $action_type
 * @property string|null $access_result
 * @property string|null $device_id
 * @property string|null $scanner_location
 * @property Carbon $scanned_at
 * @property string|null $notes
 * @property array|null $additional_data
 * @property string|null $device_ip
 * @property bool $is_valid_scan
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class SecurityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'card_uid',
        'rfid_card_id',
        'tenant_id',
        'unit_id',
        'apartment_id',
        'location',
        'action_type',
        'access_result',
        'device_id',
        'scanner_location',
        'scanned_at',
        'notes',
        'additional_data',
        'device_ip',
        'is_valid_scan',
    ];

    protected $casts = [
        'scanned_at' => 'datetime',
        'additional_data' => 'array',
        'is_valid_scan' => 'boolean',
    ];

    // Relationships
    public function rfidCard()
    {
        return $this->belongsTo(RfidCard::class);
    }

    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }

    // Scopes
    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('scanned_at', '>=', now()->subHours($hours));
    }

    public function scopeByCardUid($query, $cardUid)
    {
        return $query->where('card_uid', $cardUid);
    }

    public function scopeByTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeByDevice($query, $deviceId)
    {
        return $query->where('device_id', $deviceId);
    }

    public function scopeByLocation($query, $location)
    {
        return $query->where('location', $location);
    }

    public function scopeAccessGranted($query)
    {
        return $query->where('access_result', 'granted');
    }

    public function scopeAccessDenied($query)
    {
        return $query->where('access_result', 'denied');
    }

    public function scopeUnknownCards($query)
    {
        return $query->where('access_result', 'unknown_card');
    }

    public function scopeValidScans($query)
    {
        return $query->where('is_valid_scan', true);
    }

    public function scopeInvalidScans($query)
    {
        return $query->where('is_valid_scan', false);
    }

    public function scopeByApartment($query, $apartmentId)
    {
        return $query->where('apartment_id', $apartmentId);
    }

    public function scopeByLandlord($query, $landlordId)
    {
        return $query->whereHas('apartment', function($q) use ($landlordId) {
            $q->where('landlord_id', $landlordId);
        });
    }

    // Helper methods
    public function isAccessGranted()
    {
        return $this->access_result === 'granted';
    }

    public function isAccessDenied()
    {
        return $this->access_result === 'denied';
    }

    public function isUnknownCard()
    {
        return $this->access_result === 'unknown_card';
    }

    public function getAccessResultBadgeClassAttribute()
    {
        return match($this->access_result) {
            'granted' => 'success',
            'denied' => 'danger',
            'unknown_card' => 'warning',
            'inactive_card' => 'secondary',
            'expired_access' => 'warning',
            'time_restricted' => 'info',
            default => 'secondary'
        };
    }

    public function getActionTypeBadgeClassAttribute()
    {
        return match($this->action_type) {
            'card_scan' => 'primary',
            'access_granted' => 'success',
            'access_denied' => 'danger',
            'card_registered' => 'info',
            'card_deactivated' => 'warning',
            default => 'secondary'
        };
    }

    // Static methods for logging
    public static function logCardScan($cardUid, $deviceId, $scannerLocation, $additionalData = null)
    {
        $rfidCard = RfidCard::where('card_uid', $cardUid)->first();
        
        $accessResult = 'unknown_card';
        $tenantId = null;
        $unitId = null;
        $apartmentId = null;

        if ($rfidCard) {
            $tenantId = $rfidCard->tenant_id;
            $unitId = $rfidCard->unit_id;
            $apartmentId = $rfidCard->apartment_id;

            if ($rfidCard->hasCurrentAccess()) {
                $accessResult = 'granted';
                $rfidCard->updateLastUsed();
            } else {
                if (!$rfidCard->isActive()) {
                    $accessResult = 'inactive_card';
                } else {
                    $accessResult = 'time_restricted';
                }
            }
        }

        return self::create([
            'card_uid' => $cardUid,
            'rfid_card_id' => $rfidCard ? $rfidCard->id : null,
            'tenant_id' => $tenantId,
            'unit_id' => $unitId,
            'apartment_id' => $apartmentId,
            'action_type' => 'card_scan',
            'access_result' => $accessResult,
            'device_id' => $deviceId,
            'scanner_location' => $scannerLocation,
            'scanned_at' => now(),
            'additional_data' => $additionalData,
            'is_valid_scan' => true,
        ]);
    }

    public static function logCardAction($action, RfidCard $card, $notes = null)
    {
        return self::create([
            'card_uid' => $card->card_uid,
            'rfid_card_id' => $card->id,
            'tenant_id' => $card->tenant_id,
            'unit_id' => $card->unit_id,
            'apartment_id' => $card->apartment_id,
            'action_type' => $action,
            'scanned_at' => now(),
            'notes' => $notes,
            'is_valid_scan' => true,
        ]);
    }
}
