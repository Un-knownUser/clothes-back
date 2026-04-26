<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($role = $request->input('role')) {
            $query->where('role', $role);
        }

        $sortBy = $request->input('sort_by', 'id');
        $sortDir = $request->input('sort_dir', 'desc');

        if (in_array($sortBy, ['id', 'name', 'email', 'role', 'created_at'])) {
            $query->orderBy($sortBy, $sortDir === 'asc' ? 'asc' : 'desc');
        }

        return response()->json($query->paginate($request->input('per_page', 15)));
    }

    public function update(Request $request, User $user)
    {
        // Валидируем входящие данные (в данном случае нас интересует только смена роли)
        $validated = $request->validate([
            'role' => 'required|string|in:user,admin',
        ]);

        // Не даем администратору случайно лишить прав самого себя
        if (auth()->id() === $user->id && $validated['role'] !== 'admin') {
            return response()->json(['message' => 'Вы не можете изменить свою собственную роль'], 403);
        }

        $user->update($validated);

        return response()->json($user);
    }
}
