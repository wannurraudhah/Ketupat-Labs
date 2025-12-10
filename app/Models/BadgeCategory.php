<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BadgeCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code'];

    // Relationship to badges
    public function badges()
    {
        return $this->hasMany(Badge::class, 'category_slug', 'code');
    }
}
