<?php

namespace App\Http\Controllers;

use App\Models\Clothing;
use App\Models\Outfit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function profile()
    {
        $user = Auth::user();
        return response()->json($user);
    }
    public function weatherOutfits(Request $request)
    {
        $request->validate([
            'suitable_temp' => 'required|numeric|min:-30|max:50',
            'tolerance' => 'sometimes|integer|min:3|max:15'
        ]);

        $user = Auth::user();
        $currentTemp = (float) $request->query('suitable_temp');
        $tolerance = (int) $request->query('tolerance', 5);

        $outfits = Outfit::where('user_id', $user->id)
            ->whereNotNull('deg')
            ->whereBetween('deg', [
                $currentTemp - $tolerance,
                $currentTemp + $tolerance
            ])
            ->with(['clothing.mainTag', 'clothing.tags'])
            ->orderByRaw('ABS(deg - ?) ASC', [$currentTemp])
            ->get();

        return response()->json($outfits);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // Максимум 5МБ
        ]);

        $user->name = $validated['name'];
        $user->username = $validated['username'];
        $user->email = $validated['email'];

        if ($request->hasFile('avatar')) {
            // Удаляем старую аватарку, если она была
            if ($user->image_url) {
                $oldPath = str_replace('storage/', '', $user->image_url);
                Storage::disk('public')->delete($oldPath);
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            $user->image_url = 'storage/' . $path;
        }

        $user->save();

        return response()->json([
            'message' => 'Профиль успешно обновлен',
            'user' => $user
        ]);
    }
}
