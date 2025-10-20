<?php

namespace Database\Seeders;

use App\Models\CarCheckCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AddCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'title_ru' => 'Техническое состояние',
                'title_uz' => 'Texnik holat',
                'title_en' => 'Technical condition',
                'order' => 1,
            ],
            [
                'title_ru' => 'Салон',
                'title_uz' => 'Salon',
                'title_en' => 'Salon',
                'order' => 2,
            ],
            [
                'title_ru' => 'Кузов',
                'title_uz' => 'Tashqi tomon',
                'title_en' => 'Body',
                'order' => 3,
            ],
            [
                'title_ru' => 'Сопутствующие факторы',
                'title_uz' => "Ta'sir etuvchi omillar",
                'title_en' => 'Contributing factors',
                'order' => 4,
            ],
        ];

        foreach ($categories as $key => $value) {
            $checkcategory = new CarCheckCategory();
            $checkcategory->title_ru = $value['title_ru'];
            $checkcategory->title_uz = $value['title_uz'];
            $checkcategory->title_en = $value['title_en'];
            $checkcategory->order = $value['order'];
            $checkcategory->type = 'electro';
            $checkcategory->save();
        }
    }
}
