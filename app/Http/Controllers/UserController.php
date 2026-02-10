<?php

namespace App\Http\Controllers;

use App\Models\Clothing;
use App\Models\Outfit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function storeOutfit(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'clothes' => 'required|array|min:1',
            'clothes.*' => 'exists:clothing,id'
        ]);

        $user = Auth::user();

        $validClothingIds = Clothing::where('user_id', $user->id)
            ->pluck('id')
            ->toArray();

        $invalidIds = array_diff($request->clothes, $validClothingIds);
        if (!empty($invalidIds)) {
            return response()->json([
                'message' => 'Некоторые вещи не принадлежат вам',
                'invalid_clothes' => $invalidIds
            ], 422);
        }

        $outfit = Outfit::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'clothes_count' => count($request->clothes)
        ]);

        $outfit->clothes()->attach($request->clothes);

        return response()->json([
            'message' => 'Образ успешно сохранен!',
            'outfit' => $outfit->loadCount('clothes')
        ], 201);
    }

    public function outfits()
    {
        $user = Auth::user();

        $outfits = Outfit::with(['clothes' => function($q) {
            $q->select('clothing.id', 'clothing.name', 'clothing.image_path');
        }])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($outfits);
    }

    public function clothes(Request $request)
    {
        $user = Auth::user();

        $query = Clothing::with([
            'mainTag:id,key,label,group',
            'tags' => function($q) {
                $q->select('tags.id', 'tags.key', 'tags.label', 'tags.group');
            }
        ])->where('user_id', $user->id);

        if ($request->has('main') && is_array($request->main) && count($request->main) > 0) {
            $query->whereIn('main_tag_id', $request->main);
        }

        $filterGroups = ['color', 'season', 'style', 'occasion'];

        foreach ($filterGroups as $group) {
            if ($request->has($group) && is_array($request->$group) && count($request->$group) > 0) {
                $query->whereHas('tags', function($q) use ($request, $group) {
                    $q->whereIn('tags.id', $request->$group);
                });
            }
        }

        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        $allowedSortFields = ['created_at', 'name'];
        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'created_at';
        }

        $query->orderBy($sortBy, $sortOrder);

        $clothes = $query->select('id', 'name', 'image_path', 'main_tag_id', 'created_at')->get();

        return response()->json($clothes);
    }

    public function profile()
    {
        $user = Auth::user();
        return response()->json($user);
    }
}
