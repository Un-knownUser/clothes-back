<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Outfit extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'name', 'deg', 'clothes_count', 'is_public'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function clothing()
    {
        return $this->belongsToMany(Clothing::class, 'outfit_clothing')->withTimestamps();
    }

    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function likedBy(User $user)
    {
        return $this->likes->where('user_id', $user->id)->isNotEmpty();
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->latest();
    }
}
