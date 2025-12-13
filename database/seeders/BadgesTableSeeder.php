<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BadgesTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('badge_categories')->insert([
            ['name' => 'Tutorial', 'code' => 'tutorial', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Challenge', 'code' => 'challenge', 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('badges')->insert([
            [
                'name_bm' => 'Tutorial 1 Complete',
                'description_bm' => 'Selesaikan tutorial pertama',
                'category_id' => 1, 
                'color' => '#f39c12',
                'icon' => 'fas fa-star',
                'points_required' => 10,
                'xp_reward' => 50,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_bm' => 'Tutorial 2 Complete',
                'description_bm' => 'Selesaikan tutorial kedua',
                'category_id' => 1,
                'color' => '#00a65a',
                'icon' => 'fas fa-award',
                'points_required' => 20,
                'xp_reward' => 100,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
