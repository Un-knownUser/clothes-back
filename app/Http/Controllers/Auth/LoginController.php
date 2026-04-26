<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ], [
            'email.required' => 'Пожалуйста, укажите адрес электронной почты.',
            'email.email' => 'Указанный адрес электронной почты недействителен.',
            'password.required' => 'Пожалуйста, укажите пароль.',
            'password.min' => 'Пароль должен содержать не менее 8 символов.',
            'otp_code.required' => 'Пожалуйста, укажите OTP-код.',
            'otp_code.size' => 'OTP-код должен состоять ровно из 6 символов.',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Неверные учетные данные'], 401);
        }

        $user = Auth::user();

        // Генерация и сохранение OTP-кода
        $otpCode = random_int(100000, 999999);
        OtpCode::updateOrCreate(
            ['user_id' => $user->id],
            ['code' => $otpCode, 'expires_at' => now()->addMinutes(5)]
        );

        Mail::to($user->email)->send(new OtpMail($otpCode));

        return response()->json(['message' => 'OTP-код отправлен на ваш email']);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp_code' => 'required|string|size:6',
        ]);

        $user = User::where('email', trim($request->email))->first();
        if (!$user) {
            return response()->json(['message' => 'Пользователь не найден'], 404);
        }

        $otpCode = OtpCode::where('user_id', $user->id)->where('code', $request->otp_code)->first();

        if (!$otpCode || $otpCode->expires_at->isPast()) {
            return response()->json(['message' => 'Неверный или просроченный OTP-код'], 401);
        }

        $otpCode->delete();
        $token = $user->createToken('token')->plainTextToken;

        return response()->json(['token' => $token, 'user' => $user]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Вы вышли из системы']);
    }
}

// rqefytbzwysbycic
