<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\RfidCard;
use App\Models\SecurityLog;
use App\Models\User;
use App\Models\Unit;
use App\Models\Apartment;
use App\Models\TenantAssignment;

class SecurityController extends Controller
{
    /**
     * Display security dashboard with logs and cards overview
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        if ($user->isLandlord()) {
            return $this->landlordDashboard();
        } elseif ($user->isSuperAdmin()) {
            return $this->adminDashboard();
        } elseif ($user->isTenant()) {
            return $this->tenantDashboard();
        }
        
        abort(403, 'Unauthorized access to security dashboard');
    }

    /**
     * Landlord security dashboard
     */
    private function landlordDashboard()
    {
        $user = Auth::user();
        
        // Get recent security logs for landlord's properties
        $recentLogs = SecurityLog::byLandlord($user->id)
            ->with(['rfidCard', 'tenant', 'apartment', 'unit'])
            ->recent(24)
            ->orderBy('scanned_at', 'desc')
            ->limit(50)
            ->get();
        
        // Get RFID cards for landlord's properties
        $rfidCards = RfidCard::where('landlord_id', $user->id)
            ->with(['tenant', 'unit', 'apartment'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Security statistics
        $stats = [
            'total_cards' => $rfidCards->count(),
            'active_cards' => $rfidCards->where('status', 'active')->count(),
            'total_logs_today' => SecurityLog::byLandlord($user->id)->recent(24)->count(),
            'access_granted_today' => SecurityLog::byLandlord($user->id)->recent(24)->accessGranted()->count(),
            'access_denied_today' => SecurityLog::byLandlord($user->id)->recent(24)->accessDenied()->count(),
            'unknown_cards_today' => SecurityLog::byLandlord($user->id)->recent(24)->unknownCards()->count(),
        ];
        
        return view('landlord.security.dashboard', compact('recentLogs', 'rfidCards', 'stats'));
    }

    /**
     * Admin security dashboard
     */
    private function adminDashboard()
    {
        // System-wide security overview
        $recentLogs = SecurityLog::with(['rfidCard', 'tenant', 'apartment', 'unit'])
            ->recent(24)
            ->orderBy('scanned_at', 'desc')
            ->limit(100)
            ->get();
        
        $stats = [
            'total_cards' => RfidCard::count(),
            'active_cards' => RfidCard::active()->count(),
            'total_logs_today' => SecurityLog::recent(24)->count(),
            'access_granted_today' => SecurityLog::recent(24)->accessGranted()->count(),
            'access_denied_today' => SecurityLog::recent(24)->accessDenied()->count(),
            'unknown_cards_today' => SecurityLog::recent(24)->unknownCards()->count(),
        ];
        
        return view('super-admin.security.dashboard', compact('recentLogs', 'stats'));
    }

    /**
     * Tenant security dashboard
     */
    private function tenantDashboard()
    {
        $user = Auth::user();
        
        // Get tenant's RFID cards
        $rfidCards = RfidCard::where('tenant_id', $user->id)
            ->with(['unit', 'apartment'])
            ->get();
        
        // Get tenant's security logs
        $recentLogs = SecurityLog::where('tenant_id', $user->id)
            ->with(['rfidCard'])
            ->recent(168) // Last week
            ->orderBy('scanned_at', 'desc')
            ->get();
        
        return view('tenant.security.dashboard', compact('rfidCards', 'recentLogs'));
    }

    /**
     * Display RFID cards for landlord
     */
    public function cards()
    {
        $user = Auth::user();
        
        if (!$user->isLandlord() && !$user->isSuperAdmin()) {
            abort(403, 'Unauthorized access');
        }
        
        $query = RfidCard::with(['tenant', 'unit', 'apartment']);
        
        if ($user->isLandlord()) {
            $query->where('landlord_id', $user->id);
        }
        
        $rfidCards = $query->orderBy('created_at', 'desc')->get();
        
        return view('landlord.security.cards', compact('rfidCards'));
    }

    /**
     * Show form to create new RFID card
     */
    public function createCard()
    {
        $user = Auth::user();
        
        if (!$user->isLandlord()) {
            abort(403, 'Only landlords can create RFID cards');
        }
        
        // Get landlord's apartments and units
        $apartments = $user->apartments()->with('units')->get();
        
        // Get available tenants (those with active assignments but no RFID card)
        $availableTenants = User::where('role', 'tenant')
            ->whereHas('tenantAssignments', function($q) use ($user) {
                $q->where('landlord_id', $user->id)->where('status', 'active');
            })
            ->whereDoesntHave('rfidCards', function($q) use ($user) {
                $q->where('landlord_id', $user->id)->where('status', 'active');
            })
            ->get();
        
        return view('landlord.security.create-card', compact('apartments', 'availableTenants'));
    }

    /**
     * Store new RFID card
     */
    public function storeCard(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isLandlord()) {
            abort(403, 'Only landlords can create RFID cards');
        }
        
        $request->validate([
            'card_uid' => 'required|string|unique:rfid_cards,card_uid',
            'card_number' => 'nullable|string',
            'tenant_id' => 'required|exists:users,id',
            'unit_id' => 'nullable|exists:units,id',
            'apartment_id' => 'required|exists:apartments,id',
            'access_common_areas' => 'boolean',
            'access_building' => 'boolean',
            'access_parking' => 'boolean',
            'notes' => 'nullable|string',
        ]);
        
        // Verify tenant belongs to landlord
        $tenant = User::findOrFail($request->tenant_id);
        $tenantAssignment = TenantAssignment::where('tenant_id', $tenant->id)
            ->where('landlord_id', $user->id)
            ->where('status', 'active')
            ->first();
        
        if (!$tenantAssignment) {
            return back()->withErrors(['tenant_id' => 'Selected tenant is not assigned to any of your units.']);
        }
        
        $rfidCard = RfidCard::create([
            'card_uid' => $request->card_uid,
            'card_number' => $request->card_number,
            'tenant_id' => $request->tenant_id,
            'landlord_id' => $user->id,
            'unit_id' => $request->unit_id ?: $tenantAssignment->unit_id,
            'apartment_id' => $request->apartment_id,
            'status' => 'active',
            'assigned_at' => now(),
            'activated_at' => now(),
            'access_common_areas' => $request->boolean('access_common_areas', true),
            'access_building' => $request->boolean('access_building', true),
            'access_parking' => $request->boolean('access_parking', false),
            'notes' => $request->notes,
        ]);
        
        // Log the card creation
        SecurityLog::logCardAction('card_registered', $rfidCard, 'Card created by landlord');
        
        return redirect()->route('landlord.security.cards')
            ->with('success', 'RFID card created successfully for ' . $tenant->name);
    }

    /**
     * Show RFID card details
     */
    public function showCard(RfidCard $card)
    {
        $user = Auth::user();
        
        if ($user->isLandlord() && $card->landlord_id !== $user->id) {
            abort(403, 'Unauthorized access to this card');
        }
        
        if ($user->isTenant() && $card->tenant_id !== $user->id) {
            abort(403, 'Unauthorized access to this card');
        }
        
        $card->load(['tenant', 'unit', 'apartment']);
        
        // Get recent logs for this card
        $recentLogs = SecurityLog::where('rfid_card_id', $card->id)
            ->recent(168) // Last week
            ->orderBy('scanned_at', 'desc')
            ->limit(50)
            ->get();
        
        return view('landlord.security.card-details', compact('card', 'recentLogs'));
    }

    /**
     * Update RFID card status
     */
    public function updateCardStatus(Request $request, RfidCard $card)
    {
        $user = Auth::user();
        
        if (!$user->isLandlord() || $card->landlord_id !== $user->id) {
            abort(403, 'Unauthorized access');
        }
        
        $request->validate([
            'status' => 'required|in:active,inactive,suspended,lost',
            'notes' => 'nullable|string',
        ]);
        
        $oldStatus = $card->status;
        $newStatus = $request->status;
        
        $card->update([
            'status' => $newStatus,
            'notes' => $request->notes ?: $card->notes,
        ]);
        
        if ($newStatus === 'active' && $oldStatus !== 'active') {
            $card->activate();
            SecurityLog::logCardAction('card_activated', $card, 'Card activated by landlord');
        } elseif ($newStatus !== 'active' && $oldStatus === 'active') {
            $card->deactivate();
            SecurityLog::logCardAction('card_deactivated', $card, 'Card deactivated by landlord');
        }
        
        return back()->with('success', 'Card status updated successfully');
    }

    /**
     * Security logs listing
     */
    public function logs(Request $request)
    {
        $user = Auth::user();
        
        $query = SecurityLog::with(['rfidCard', 'tenant', 'apartment', 'unit']);
        
        if ($user->isLandlord()) {
            $query->byLandlord($user->id);
        }
        
        // Apply filters
        if ($request->filled('date_from')) {
            $query->where('scanned_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->where('scanned_at', '<=', $request->date_to . ' 23:59:59');
        }
        
        if ($request->filled('access_result')) {
            $query->where('access_result', $request->access_result);
        }
        
        if ($request->filled('device_id')) {
            $query->where('device_id', $request->device_id);
        }
        
        if ($request->filled('apartment_id')) {
            $query->where('apartment_id', $request->apartment_id);
        }
        
        $logs = $query->orderBy('scanned_at', 'desc')->paginate(50);
        
        // Get filter options
        $apartments = [];
        $devices = [];
        
        if ($user->isLandlord()) {
            $apartments = $user->apartments()->get();
            $devices = SecurityLog::byLandlord($user->id)
                ->whereNotNull('device_id')
                ->distinct()
                ->pluck('device_id');
        } elseif ($user->isSuperAdmin()) {
            $apartments = Apartment::all();
            $devices = SecurityLog::whereNotNull('device_id')
                ->distinct()
                ->pluck('device_id');
        }
        
        return view('landlord.security.logs', compact('logs', 'apartments', 'devices'));
    }

    /**
     * Handle ESP32 RFID scan via serial/USB connection
     * This endpoint receives data from the ESP32 connected via USB
     */
    public function handleRfidScan(Request $request)
    {
        $request->validate([
            'card_uid' => 'required|string',
            'device_id' => 'required|string',
            'scanner_location' => 'nullable|string',
            'timestamp' => 'nullable|date',
            'additional_data' => 'nullable|array',
        ]);
        
        try {
            // Log the RFID scan and determine access
            $securityLog = SecurityLog::logCardScan(
                $request->card_uid,
                $request->device_id,
                $request->scanner_location ?: 'Unknown Location',
                $request->additional_data
            );
            
            // Prepare response for ESP32
            $response = [
                'success' => true,
                'access_granted' => $securityLog->isAccessGranted(),
                'access_result' => $securityLog->access_result,
                'timestamp' => now()->toDateTimeString(),
                'message' => $this->getAccessMessage($securityLog->access_result),
            ];
            
            // If card found, include tenant info
            if ($securityLog->rfidCard) {
                $response['tenant_name'] = $securityLog->tenant ? $securityLog->tenant->name : 'Unknown';
                $response['unit_number'] = $securityLog->unit ? $securityLog->unit->unit_number : null;
            }
            
            Log::info('RFID scan processed', [
                'card_uid' => $request->card_uid,
                'device_id' => $request->device_id,
                'access_result' => $securityLog->access_result,
                'tenant_id' => $securityLog->tenant_id,
            ]);
            
            return response()->json($response);
            
        } catch (\Exception $e) {
            Log::error('RFID scan processing failed', [
                'card_uid' => $request->card_uid,
                'device_id' => $request->device_id,
                'error' => $e->getMessage(),
            ]);
            
            return response()->json([
                'success' => false,
                'access_granted' => false,
                'message' => 'System error - access denied',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get access message based on result
     */
    private function getAccessMessage($accessResult)
    {
        return match($accessResult) {
            'granted' => 'Access Granted',
            'denied' => 'Access Denied',
            'unknown_card' => 'Unknown Card',
            'inactive_card' => 'Inactive Card',
            'expired_access' => 'Expired Access',
            'time_restricted' => 'Time Restricted',
            default => 'Access Denied'
        };
    }

    /**
     * API endpoint to get device status
     */
    public function deviceStatus(Request $request)
    {
        $deviceId = $request->query('device_id');
        
        if (!$deviceId) {
            return response()->json(['error' => 'Device ID required'], 400);
        }
        
        // Get recent activity for this device
        $recentScans = SecurityLog::where('device_id', $deviceId)
            ->recent(24)
            ->count();
        
        $lastScan = SecurityLog::where('device_id', $deviceId)
            ->latest('scanned_at')
            ->first();
        
        return response()->json([
            'device_id' => $deviceId,
            'status' => 'online',
            'last_activity' => $lastScan ? $lastScan->scanned_at : null,
            'scans_today' => $recentScans,
            'timestamp' => now()->toDateTimeString(),
        ]);
    }
}