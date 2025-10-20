<?php

namespace Database\Seeders;

use App\Models\CarCheckSubCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AddSubcategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $categories = [
        //     [
        //         'title_ru' => 'Общие повреждения',
        //         'title_uz' => 'Umumiy shikastlanishlar',
        //         'title_en' => 'General damages',
        //         'order' => 1,
        //     ],

        // ];
        $categories = [
            [
                'title_ru' => 'Гибридная система и ДВС',
                'title_uz' => 'Gibrid tizimi va ichki yonuv dvigateli',
                'title_en' => 'Hybrid system and internal combustion engine',
                'order' => 1,
            ],
            [
                'title_ru' => 'Коробка передач (гибридная трансмиссия)',
                'title_uz' => 'Uzatmalar qutisi (gibrid transmissiya)',
                'title_en' => 'Transmission (hybrid gearbox)',
                'order' => 2,
            ],
            [
                'title_ru' => 'Подвеска',
                'title_uz' => 'Osma tizimi',
                'title_en' => 'Suspension',
                'order' => 3,
            ],
            [
                'title_ru' => 'Рулевое управление',
                'title_uz' => 'Rul boshqaruvi',
                'title_en' => 'Steering system',
                'order' => 4,
            ],
            [
                'title_ru' => 'Зарядная и электрическая система',
                'title_uz' => 'Zaryadlash va elektr tizimi',
                'title_en' => 'Charging and electrical system',
                'order' => 5,
            ],
        ];


        foreach ($categories as $key => $value) {
            $checkcategory = new CarCheckSubCategory();
            $checkcategory->title_ru = $value['title_ru'];
            $checkcategory->title_uz = $value['title_uz'];
            $checkcategory->title_en = $value['title_en'];
            $checkcategory->order = $value['order'];
            $checkcategory->type = 'electro';
            $checkcategory->car_check_category_id = 5;
            $checkcategory->save();
        }
    }
}
