<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TagsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tagsData = [
            // ['key', 'label', 'group', 'is_required']
            ['tops', 'Верх', 'main', true],
            ['bottoms', 'Низ', 'main', true],
            ['shoes', 'Обувь', 'main', true],
            ['accessories', 'Аксессуары', 'main', true],
            ['black', 'Чёрный', 'color', false],
            ['white', 'Белый', 'color', false],
            ['blue', 'Синий', 'color', false],
            ['summer', 'Лето', 'season', false],
            ['autumn', 'Осень', 'season', false],
            ['winter', 'Зима', 'season', false],
            ['spring', 'Весна', 'season', false],
            ['casual', 'Кэжуал', 'style', false],
            ['office', 'Офис', 'style', false],
        ];

        $insertData = collect($tagsData)->map(function ($tag) {
            return [
                'key' => $tag[0],
                'label' => $tag[1],
                'group' => $tag[2],
                'is_required' => $tag[3],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->toArray();

        DB::table('tags')->insertOrIgnore($insertData);
    }
}
