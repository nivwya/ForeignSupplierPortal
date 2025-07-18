<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Vendor;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Validate vendor_id and password input
        $credentials = $request->validate([
            'vendor_id' => 'required|integer',
            'password' => 'required|string',
        ]);

        // Find user by vendor_id (same as user ID)
        $user = User::find($credentials['vendor_id']);

        if (!$user) {
            return $this->sendInvalidCredentials($request);
        }

        // Clean and compare password (accept both plain text and hash)
        $inputPassword = trim($credentials['password']);
        $storedPassword = trim($user->password);

        $passwordMatches = $storedPassword === $inputPassword || Hash::check($inputPassword, $storedPassword);

        if (!$passwordMatches) {
            return $this->sendInvalidCredentials($request);
        }

        // Login the user
        Auth::login($user);
        $request->session()->regenerate();

        // API response
        if ($request->expectsJson()) {
            $token = $user->createToken('api-token')->plainTextToken;

            return response()->json([
                'message' => 'Login successful',
                'token' => $token,
                'user' => $user,
            ]);
        }

        // Web redirect
        return redirect()->route('dashboard')->with('success', 'Login successful');
    }

    private function sendInvalidCredentials(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Invalid credentialss'], 401);
        } else {
            return back()
                ->withInput()
                ->with('error', 'Invalid Vendor ID or password')
                ->with('mode', 'login');
        }
    }
}
