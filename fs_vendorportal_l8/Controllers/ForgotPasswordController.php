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
            'vendor_id' => 'required|integer'
        ]);

        // Join vendors and vendor_contacts to fetch email
        $vendor = DB::table('vendors')
            ->leftJoin('vendor_contacts', 'vendors.id', '=', 'vendor_contacts.vendor_id')
            ->where('vendors.id', $request->vendor_id)
            ->select('vendors.id as vendor_id', 'vendor_contacts.email')
            ->first();

        if (!$vendor || !$vendor->email) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor or email not found.'
            ], 404);
        }

        $otp = rand(100000, 999999);

        session([
            'password_otp' => $otp,
            'password_email' => $vendor->email,
            'password_vendor_id' => $vendor->vendor_id,
        ]);

        Mail::to($vendor->email)->send(new OtpMail($otp));

        return response()->json([
            'success' => true,
            'message' => "OTP sent to your registered email.",
            'email' => $vendor->email
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

        // Use session vendor ID and email to find the user
        $vendorId = session('password_vendor_id');

        $user = User::where('id', $vendorId)
                    ->where('email', $email)
                    ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found for provided email and vendor ID.'
            ], 404);
        }

        $user->password = Hash::make($password);
        $user->save();

        session()->forget(['password_email', 'password_otp', 'password_vendor_id']);

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully.'
        ]);
    }
}
