<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function index(Request $request)
    {
        $query = Tag::select('id', 'key', 'label', 'group', 'is_required')
            ->orderBy('group')
            ->orderBy('label');

        if ($request->filled('group')) {
            $query->where('group', $request->group);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('label', 'ilike', "%{$search}%")
                    ->orWhere('key', 'ilike', "%{$search}%");
            });
        }

        $tags = $query->limit(100)->get();

        return response()->json($tags);
    }
}
