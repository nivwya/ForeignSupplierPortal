<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Check if user already exists
        $user = User::where('email', $validated['email'])->first();
        if ($user) {
            return redirect()->route('login')->with('error', 'User already exists. Please log in.');
        }

        // Generate a random unique ID for the user
        do {
            $randomId = random_int(1, 20); // Or use UUID if needed
        } while (DB::table('users')->where('id', $randomId)->exists());

        // Create user with random ID
        $user = User::create([
            'id'       => $randomId,
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Optionally log the user in
        auth()->login($user);

        // Redirect to vendor dashboard
        return redirect()->route('vendor.dashboard')->with('success', 'Registration successful! Please complete your profile.');
    }
}
