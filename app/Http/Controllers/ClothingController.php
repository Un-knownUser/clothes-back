<?php

namespace App\Http\Controllers;

use App\Models\Clothing;
use App\Models\Outfit;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ClothingController extends Controller
{
    public function lastAdded(Request $request)
    {
        $user = Auth::user();

        $clothes = Clothing::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->select('id', 'user_id', 'name', 'image_path', 'created_at')
            ->get();

        return response()->json([
            'data' => $clothes,
            'total' => $clothes->count()
        ]);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'images' => 'required|array|max:20',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
            'names' => 'required|array',
            'names.*' => 'required|string|max:255',
            'main_tag_ids' => 'required|array',
            'main_tag_ids.*' => 'required|exists:tags,id',
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'nullable|array',
            'tag_ids.*.*' => 'exists:tags,id'
        ]);

        $results = [];

        foreach ($request->file('images') as $index => $image) {
            $path = $image->store('clothes', 'public');

            $clothing = Clothing::create([
                'user_id' => Auth::id(),
                'name' => $validated['names'][$index],
                'image_path' => $path,
                'main_tag_id' => $validated['main_tag_ids'][$index],
            ]);

            if (!empty($validated['tag_ids'][$index])) {
                $clothing->tags()->attach($validated['tag_ids'][$index]);
            }

            $results[] = $clothing->load(['mainTag', 'tags']);
        }

        return response()->json([
            'success' => true,
            'count' => count($results),
            'data' => $results
        ], 201);
    }

    public function index(Request $request) {
        $user = Auth::user();
        return Clothing::where('user_id', $user->id)
            ->with(['mainTag', 'tags' => fn($q) => $q->group('color')])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function outfitsUsingClothing(Clothing $clothing)
    {
        $outfits = Outfit::whereHas('clothing', function ($query) use ($clothing) {
            $query->where('clothing.id', $clothing->id);
        })
            ->with(['clothing' => function ($query) {
                $query->select('clothing.id', 'clothing.name', 'clothing.image_path');
            }])
            ->select('outfits.id', 'outfits.name', 'outfits.deg')
            ->get();

        return response()->json($outfits);
    }

    public function destroy(Clothing $clothing)
    {
        $clothing->delete();
        return response()->json(['message' => 'Одежда удалена']);
    }
}
