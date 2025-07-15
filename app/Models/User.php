<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'phone',
        'address',
        'business_info',
        'approved_at',
        'approved_by',
        'rejection_reason',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'approved_at' => 'datetime',
        ];
    }

    // Relationships
    public function apartments()
    {
        return $this->hasMany(Apartment::class, 'landlord_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function approvedUsers()
    {
        return $this->hasMany(User::class, 'approved_by');
    }

    // Role helper methods
    public function isSuperAdmin()
    {
        return $this->role === 'super_admin';
    }

    public function isLandlord()
    {
        return $this->role === 'landlord';
    }

    public function isTenant()
    {
        return $this->role === 'tenant';
    }

    // Status helper methods
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    // Scopes
    public function scopePendingLandlords($query)
    {
        return $query->where('role', 'landlord')->where('status', 'pending');
    }

    public function scopeApprovedLandlords($query)
    {
        return $query->where('role', 'landlord')->where('status', 'approved');
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    // Methods
    public function approve($adminId)
    {
        $this->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $adminId,
            'rejection_reason' => null,
        ]);
    }

    public function reject($adminId, $reason = null)
    {
        $this->update([
            'status' => 'rejected',
            'approved_at' => null,
            'approved_by' => $adminId,
            'rejection_reason' => $reason,
        ]);
    }
}
