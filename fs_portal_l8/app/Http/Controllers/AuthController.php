<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'id' => 'required|string|regex:/^\d+$/',
            'password' => 'required|string',
        ]);
        $credentials['id'] = trim($credentials['id']); 
        $user = User::where('id', $credentials['id'])->first();
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        $$inputPassword = trim($credentials['password']);
        $storedPassword = trim($user->password);
        if (Hash::check($inputPassword, $storedPassword) || $inputPassword === $storedPassword) {
            Auth::login($user);
            $token = $user->createToken('api-token')->plainTextToken;
            return response()->json(['token' => $token, 'user' => $user]);
        }
        return response()->json(['message' => 'Invalid credentials'], 401);

    }
}
