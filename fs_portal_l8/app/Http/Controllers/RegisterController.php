<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);
        $user = User::where('email', $validated['email'])->first();
        if ($user) {
            return redirect()->route('login')->with('error', 'User already exists. Please log in.');
        }
        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);
        auth()->login($user);
        return redirect()->route('vendor.dashboard')->with('success', 'Registration successful! Please complete your profile.');
    }
}
