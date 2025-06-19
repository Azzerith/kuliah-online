<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }

    public function show(User $user)
    {
        return response()->json($user);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,'.$user->id,
            'phone' => 'sometimes|string|max:20',
            'profile_photo' => 'sometimes|string'
        ]);

        $user->update($validated);

        return response()->json($user);
    }

    public function promoteToAssistant(Request $request, User $user)
    {
        if ($user->role !== 'mahasiswa') {
            return response()->json(['message' => 'Hanya mahasiswa yang bisa dijadikan asisten'], 400);
        }

        $user->update(['is_asisten' => true]);

        return response()->json(['message' => 'Mahasiswa berhasil dijadikan asisten']);
    }
}