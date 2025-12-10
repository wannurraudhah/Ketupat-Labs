<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'category_slug', // matches BadgeCategory code
        'icon',
        'requirement_value',
        'color',
        'xp_reward',
        'code' // important for pivot
    ];

    // Relationship to category
    public function category()
    {
        return $this->belongsTo(BadgeCategory::class, 'category_slug', 'code');
    }
}
