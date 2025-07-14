<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    /**
     * Handle user logout
     */
    public function logout(Request $request)
    {
        // Clear any authentication data
        Auth::logout();
        
        // Clear session data
        Session::flush();
        
        // Clear any stored sidebar state
        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Logged out successfully']);
        }
        
        // Redirect to login page
        return redirect()->route('login')->with('message', 'You have been logged out successfully.');
    }
} 