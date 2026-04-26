<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:60|unique:users',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'username.required' => 'Пожалуйста, укажите имя пользователя.',
            'username.string' => 'Имя пользователя должно быть строкой.',
            'username.max' => 'Имя пользователя не должно превышать 60 символов.',
            'username.unique' => 'Это имя пользователя уже занято.',
            'name.required' => 'Пожалуйста, укажите ваше имя.',
            'name.string' => 'Имя должно быть строкой.',
            'name.max' => 'Имя не должно превышать 255 символов.',
            'email.required' => 'Пожалуйста, укажите адрес электронной почты.',
            'email.string' => 'Адрес электронной почты должен быть строкой.',
            'email.email' => 'Указанный адрес электронной почты недействителен.',
            'email.max' => 'Адрес электронной почты не должен превышать 255 символов.',
            'email.unique' => 'Этот адрес электронной почты уже зарегистрирован.',
            'password.required' => 'Пожалуйста, укажите пароль.',
            'password.string' => 'Пароль должен быть строкой.',
            'password.min' => 'Пароль должен содержать не менее 8 символов.',
            'password.confirmed' => 'Пароли не совпадают.',
        ]);

        $user = User::create([
            'username' => $request->username,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['user' => $user], 201);
    }
}
