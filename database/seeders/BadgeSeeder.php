<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Badge;

class BadgeSeeder extends Seeder
{
    public function run()
    {
        Badge::create([
            'name' => 'Lencana Permulaan',
            'description' => 'Lengkapkan aktiviti pertama anda.',
            'requirement_type' => 'points',
            'requirement_value' => 10,
            'icon' => 'fas fa-rocket',
            'color' => '#007bff',
            'category_slug' => 'general',
        ]);

        Badge::create([
            'name' => 'Cabaran Mingguan',
            'description' => 'Selesaikan cabaran mingguan pertama.',
            'requirement_type' => 'points',
            'requirement_value' => 50,
            'icon' => 'fas fa-star',
            'color' => '#ffc107',
            'category_slug' => 'challenge',
        ]);
    }
}
