<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;


class AuthPageController extends Controller
{
    public function showRegister()
    {
        return view('auth', ['mode' => 'register']);
    }

    public function show()
    {
        return view('auth', ['mode' => 'login']);
    }

    public function handle(Request $request)
    {
        $mode = $request->input('mode', 'login');

        if ($mode === 'register') {
            $request->validate([
                'fullname' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6|confirmed',
            ]);
        
            // Generate random unique ID for manual insert
            do {
                $randomId = random_int(100000, 999999);
            } while (User::where('id', $randomId)->exists());
        
            $user = User::create([
                'id' => $randomId,
                'name' => $request->fullname,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
        
            $user->assignRole('vendor');
        
            Auth::login($user);
        
            return redirect()->route('dashboard')->with('success', 'Registration successful!');
        }
         else {
            // LOGIN FLOW (accept both hashed and plain-text passwords)
            $request->validate([
                'vendor_id' => 'required|integer',
                'password' => 'required|string',
            ]);

            $user = User::find($request->vendor_id);

            if (!$user) {
                return back()->withInput()->with('error', 'Invalid credentials')->with('mode', 'login');
            }

            $inputPassword = trim($request->password);
            $storedPassword = trim($user->password);

            $passwordMatches = $storedPassword === $inputPassword || Hash::check($inputPassword, $storedPassword);

            if (!$passwordMatches) {
                return back()->withInput()->with('error', 'Invalid credentials')->with('mode', 'login');
            }

            Auth::login($user);
            $request->session()->regenerate();

            if ($user->hasRole('admin')) {
                return redirect()->route('admin.dashboard')->with('success', 'Login successful!');
            } else {
                return redirect()->route('dashboard')->with('success', 'Login successful!');
            }

            return redirect()->route('dashboard')->with('success', 'Login successful!');
        }
    }

    public function username()
    {
        return 'id'; // Use vendor_id as login key
    }
}
