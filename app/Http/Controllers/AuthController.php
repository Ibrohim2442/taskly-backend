<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // 1. Request'dan kelgan malumotlarni validate qilish (name, email, password + confirmation)
        // 2. User yaratish (hashed password bilan)
        // 3. JSON formatda muvaffaqiyatli ro‘yxatdan o‘tish xabarini qaytarish (201 status bilan)

        $fields = $request->validate([
           'name' => 'required|string|max:255',
           'email' => 'required|string|email|unique:users',
           'password' => 'required|string|min:6|confirmed'
        ]);

        $user = User::create($fields);

        $token = $user->createToken($request->name);

        return [
            'user' => $user,
            'token' => $token->plainTextToken
        ];
    }

    public function login(Request $request)
    {
        // 1. Request'dan kelgan malumotlarni validate qilish (name, email, password + confirmation)
        // 2. User yaratish (hashed password bilan)
        // 3. JSON formatda muvaffaqiyatli ro‘yxatdan o‘tish xabarini qaytarish (201 status bilan)

        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();


        if (!$user || !Hash::check($request->password, $user->password)) {
            return [
                'errors' => [
                    'email' => ['The provided credentials are incorrect.']
                ]
            ];
        }

        $token = $user->createToken($user->name);

        return [
            'user' => $user,
            'token' => $token->plainTextToken
        ];

    }

    public function logout(Request $request)
    {
        // 1. Hozirgi foydalanuvchining access tokenini o‘chirish (faqat bitta token)
        // 2. Logout muvaffaqiyatli bo‘lganini bildiruvchi JSON javob qaytarish

        $request->user()->currentAccessToken()->delete();

        return [
            'message' => 'You are logged out.'
        ];
    }

    public function me(Request $request)
    {
        // 1. Auth qilingan foydalanuvchi (user()) malumotlarini JSON formatda qaytarish

        return response()->json($request->user());
    }
}
