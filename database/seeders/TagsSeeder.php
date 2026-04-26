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
            // --- Группа: Main (Тип изделия) ---
            ['tops', 'Верх', 'main', true],
            ['bottoms', 'Низ', 'main', true],
            ['shoes', 'Обувь', 'main', true],
            ['accessories', 'Аксессуары', 'main', true],
            ['outerwear', 'Верхняя одежда', 'main', false],
            ['headwear', 'Головные уборы', 'main', false],

            // --- Группа: Season (Сезоны) ---
            ['summer', 'Лето', 'season', false],
            ['autumn', 'Осень', 'season', false],
            ['winter', 'Зима', 'season', false],
            ['spring', 'Весна', 'season', false],
            ['demiseason', 'Демисезон', 'season', false],

            // --- Группа: Color (Цвета) ---
            ['black', 'Чёрный', 'color', false],
            ['white', 'Белый', 'color', false],
            ['blue', 'Синий', 'color', false],
            ['grey', 'Серый', 'color', false],
            ['beige', 'Бежевый', 'color', false],
            ['brown', 'Коричневый', 'color', false],
            ['green', 'Зелёный', 'color', false],
            ['red', 'Красный', 'color', false],

            // --- Группа: Style (Стили) ---
            ['casual', 'Кэжуал', 'style', false],
            ['office', 'Офис', 'style', false],
            ['sport', 'Спорт', 'style', false],
            ['classic', 'Классика', 'style', false],
            ['streetwear', 'Уличный стиль', 'style', false],

            // --- Группа: Occasion (Повод) ---
            ['work', 'Работа', 'occasion', false],
            ['date', 'Свидание', 'occasion', false],
            ['party', 'Вечеринка', 'occasion', false],
            ['travel', 'Путешествие', 'occasion', false],
            ['gym', 'Спортзал', 'occasion', false],
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
