<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'label', 'group', 'is_required'];

    public function clothings()
    {
        return $this->belongsToMany(Clothing::class, 'clothes_tags');
    }
    public function scopeGroup($query, $group)
    {
        return $query->where('group', $group);
    }
    public function scopeMain($query)
    {
        return $query->where('group', 'main')->where('is_required', true);
    }
}
