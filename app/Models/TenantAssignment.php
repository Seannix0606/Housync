<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\FirebaseSyncTrait;

class TenantAssignment extends Model
{
    use HasFactory, FirebaseSyncTrait;

    protected $fillable = [
        'unit_id',
        'tenant_id',
        'landlord_id',
        'assigned_at',
        'lease_start_date',
        'lease_end_date',
        'rent_amount',
        'security_deposit',
        'status',
        'notes',
        'documents_uploaded',
        'documents_verified',
        'verification_notes',
        'generated_password',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'lease_start_date' => 'date',
        'lease_end_date' => 'date',
        'rent_amount' => 'decimal:2',
        'security_deposit' => 'decimal:2',
        'documents_uploaded' => 'boolean',
        'documents_verified' => 'boolean',
    ];

    // Relationships
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    public function landlord()
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    public function documents()
    {
        return $this->hasMany(TenantDocument::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePendingDocuments($query)
    {
        return $query->where('documents_uploaded', false);
    }

    public function scopeDocumentsUploaded($query)
    {
        return $query->where('documents_uploaded', true);
    }

    public function scopeDocumentsVerified($query)
    {
        return $query->where('documents_verified', true);
    }

    // Helper methods
    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isTerminated()
    {
        return $this->status === 'terminated';
    }

    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'active' => 'success',
            'pending' => 'warning',
            'terminated' => 'danger',
            default => 'secondary'
        };
    }

    public function getDocumentsStatusAttribute()
    {
        if (!$this->documents_uploaded) {
            return 'pending';
        }
        
        if (!$this->documents_verified) {
            return 'uploaded';
        }
        
        return 'verified';
    }

    public function getDocumentsStatusBadgeClassAttribute()
    {
        return match($this->getDocumentsStatusAttribute()) {
            'pending' => 'danger',
            'uploaded' => 'warning',
            'verified' => 'success',
            default => 'secondary'
        };
    }
} 