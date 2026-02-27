<?php

namespace App\Http\Controllers;

use App\Models\Clothing;
use App\Models\Outfit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
}
