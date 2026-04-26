<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'outfit_id', 'content'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function outfit()
    {
        return $this->belongsTo(Outfit::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->latest();
    }
}
