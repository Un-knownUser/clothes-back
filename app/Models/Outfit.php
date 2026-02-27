<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Outfit extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'name', 'deg', 'clothes_count'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function clothing()
    {
        return $this->belongsToMany(Clothing::class, 'outfit_clothing')->withTimestamps();
    }
}
