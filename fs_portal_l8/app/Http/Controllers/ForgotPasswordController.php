<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Mail\OtpMail;
use App\Models\User;
class ForgotPasswordController extends Controller
{
    public function sendOtp(Request $request)
    {
        $request->validate([
            'user_id' => 'required|string',
        ]);
        $user = User::where('id', $request->user_id)->first();

        if (!$user || !$user->email) {
            return response()->json([
                'success' => false,
                'message' => 'User or email not found.'
            ], 404);
        }
        $otp = rand(100000, 999999);
        session([
            'password_otp' => $otp,
            'password_email' => $user->email,
            'password_user_id' => $user->id,
        ]);
        Mail::to($user->email)->send(new OtpMail($otp));
        return response()->json([
            'success' => true,
            'message' => "OTP sent to your registered email.",
            'email' => $user->email
        ]);
    }
    public function verifyOtp(Request $request)
    {
        $email = $request->email;
        $otp = $request->otp;
        if (
            session('password_email') !== $email ||
            session('password_otp') != $otp
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP.'
            ], 400);
        }
        return response()->json([
            'success' => true,
            'message' => 'OTP verified.'
        ]);
    }
    public function resetPassword(Request $request)
    {
        $email = $request->email;
        $password = $request->password;
        $userId = session('password_user_id');

        $user = User::where('id', $userId)
                    ->where('email', $email)
                    ->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found for provided email and user ID.'
            ], 404);
        }
        $user->password = Hash::make($password);
        $user->save();
        session()->forget(['password_email', 'password_otp', 'password_user_id']);
        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully.'
        ]);
    }
}
