<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

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

            $user = User::create([
                'name' => $request->fullname,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $user->assignRole('vendor'); // Only assign vendor role on portal registration

            Auth::login($user);

            return redirect()->route('dashboard')->with('success', 'Registration successful!');
        } else {

            //changes made by niveditha

            $credentials = $request->validate([
                'id' => 'required|string|regex:/^\d+$/',
                'password' => 'required|string',
            ]);

            $id = $credentials['id'];
            $user = User::where('id', $credentials['id'])->first();


            if (!$user && is_numeric($id)) {
                $user = User::where('id', '=', (string)(int)$id)->first();
            }

            if (!$user || (!Hash::check($request->password, $user->password) && $request->password !== $user->password)) {
                \Log::warning('Login failed for user id', ['input_id' => $id, 'string_id' => (string)$id]);
                return back()->withInput()->with('error', 'Invalid credentials')->with('mode', 'login');
            }

            Auth::login($user);

            // Superadmin logic
            if ($user->isSuperAdmin()) {
                return redirect()->route('admin.dashboard')->with('success', 'Login successful!');
            }

            $adminCompany = \App\Models\AdminCompanyCode::where('admin_email', $user->email)->first();
            if ($adminCompany) {
                session(['admin_company_profile' => $adminCompany->toArray()]);
                return redirect()->route('admin.dashboard')->with('success', 'Login successful!');
            }

            return redirect()->route('dashboard')->with('success', 'Login successful!');
            //changes end
        }
    }
}