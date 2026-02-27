<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clothing extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'name', 'image_path', 'main_tag_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function mainTag()
    {
        return $this->belongsTo(Tag::class, 'main_tag_id');
    }
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'clothes_tags');
    }
    public function mainTagWithTags()
    {
        return $this->with(['mainTag', 'tags' => fn($q) => $q->where('group', '!=', 'main')]);
    }
    public function outfits()
    {
        return $this->belongsToMany(Outfit::class, 'outfit_clothing', 'clothing_id', 'outfit_id')
            ->using(Outfit::class);
    }
}
