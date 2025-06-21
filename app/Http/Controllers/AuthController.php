<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
{
    $validated = $request->validate([
        'nidn_nim' => 'required|string|max:20|unique:users',
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
        'role' => 'required|in:admin,dosen,mahasiswa'
    ]);

    $user = User::create([
        'nidn_nim' => $validated['nidn_nim'],
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']),
        'role' => $validated['role'],
        'status' => 'non-aktif' // Default non-aktif, harus diaktifkan oleh admin
    ]);

    // Notifikasi ke admin (opsional)
    // $admin = User::where('role', 'admin')->first();
    // $admin->notify(new NewUserRegistrationNotification($user));

    return response()->json([
        'message' => 'Registrasi berhasil. Akun Anda sedang menunggu persetujuan admin.',
        'user' => $user->makeHidden(['password']) // Sembunyikan password di response
    ], 201);
}

    public function login(Request $request)
    {
        $request->validate([
            'email_or_id' => 'required|string',
            'password' => 'required|string',
            'device_name' => 'required|string'
        ]);

        $user = User::where('email', $request->email_or_id)
            ->orWhere('nidn_nim', $request->email_or_id)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email_or_id' => ['Kredensial tidak valid.'],
            ]);
        }

        $token = $user->createToken($request->device_name)->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'token' => $token,
            'user' => $user
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout berhasil']);
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }
}