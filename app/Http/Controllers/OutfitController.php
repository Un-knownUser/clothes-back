<?php

namespace App\Http\Controllers;

use App\Models\Clothing;
use App\Models\Outfit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OutfitController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $outfits = Outfit::with(['clothing.mainTag', 'clothing.tags'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($outfits);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'deg' => 'required|integer|max:50|min:-50',
            'clothing_ids' => 'required|array|min:1|max:20',
            'clothing_ids.*' => 'required|exists:clothing,id'
        ]);

        $userClothingIds = Clothing::where('user_id', Auth::id())
            ->whereIn('id', $validated['clothing_ids'])
            ->pluck('id')
            ->toArray();

        if (count($userClothingIds) !== count($validated['clothing_ids'])) {
            return response()->json([
                'message' => 'Некоторые вещи вам не принадлежат'
            ], 403);
        }

        $outfit = Outfit::create([
            'user_id' => Auth::id(),
            'name' => $validated['name'],
            'deg' => $validated['deg'],
            'clothes_count' => count($validated['clothing_ids']),
        ]);

        $outfit->clothing()->attach($validated['clothing_ids']);

        return response()->json([
            'success' => true,
            'data' => $outfit->load(['clothing.mainTag', 'clothing.tags'])
        ], 201);
    }

    public function getClothingByCategories()
    {
        $user = Auth::user();

        $clothing = Clothing::with(['mainTag:id,key,label,group', 'tags:id,key,label,group'])
            ->where('user_id', $user->id)
            ->select('id', 'name', 'image_path', 'main_tag_id', 'created_at')
            ->get()
            ->groupBy('mainTag.key');

        return response()->json($clothing);
    }

    public function destroy($id)
    {
        $outfit = Outfit::where('user_id', Auth::id())->findOrFail($id);
        $outfit->delete();

        return response()->json(['success' => true]);
    }
}
