<?php

namespace Database\Seeders;

use App\Models\CarCheck;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CarChecksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $checks = [
            [
                'title_ru' => 'Запуск с первого раза',
                'title_uz' => 'Birinchi urinishda ishga tushadi',
                'title_en' => 'Starts on the first try',
            ],
            [
                'title_ru' => 'Холостой ход стабильный',
                'title_uz' => 'Bo‘sh holatda barqaror ishlaydi',
                'title_en' => 'Idle is stable',
            ],
            [
                'title_ru' => 'Отсутствие посторонних шумов',
                'title_uz' => 'Begona shovqinlar yo‘q',
                'title_en' => 'No unusual noises',
            ],
            [
                'title_ru' => 'Нет вибраций при работе',
                'title_uz' => 'Ish paytida tebranish yo‘q',
                'title_en' => 'No vibrations during operation',
            ],
            [
                'title_ru' => 'Нет утечек масла',
                'title_uz' => 'Moy sizishi yo‘q',
                'title_en' => 'No oil leaks',
            ],
            [
                'title_ru' => 'Нет подтёков антифриза',
                'title_uz' => 'Antifriz sizishi yo‘q',
                'title_en' => 'No coolant leaks',
            ],
            [
                'title_ru' => 'Нет подтёков топлива',
                'title_uz' => 'Yoqilg‘i sizishi yo‘q',
                'title_en' => 'No fuel leaks',
            ],
            [
                'title_ru' => 'Уровень масла в норме',
                'title_uz' => 'Moy darajasi me’yorda',
                'title_en' => 'Oil level is normal',
            ],
            [
                'title_ru' => 'Цвет масла нормальный',
                'title_uz' => 'Moy rangi me’yorda',
                'title_en' => 'Oil color is normal',
            ],
            [
                'title_ru' => 'Отсутствие запаха гари',
                'title_uz' => 'Yonish hidi yo‘q',
                'title_en' => 'No burning smell',
            ],
            [
                'title_ru' => 'Проверка компрессии',
                'title_uz' => 'Siqilish (kompressiya) tekshirildi',
                'title_en' => 'Compression check',
            ],
            [
                'title_ru' => 'Проверка давления масла',
                'title_uz' => 'Moy bosimi tekshirildi',
                'title_en' => 'Oil pressure check',
            ],
            [
                'title_ru' => 'Проверка ремня ГРМ / цепи',
                'title_uz' => 'GRM kamar / zanjiri tekshirildi',
                'title_en' => 'Timing belt/chain check',
            ],
            [
                'title_ru' => 'Проверка роликов и натяжителя',
                'title_uz' => 'Roliklar va tortkich tekshirildi',
                'title_en' => 'Pulleys and tensioner check',
            ],
            [
                'title_ru' => 'Проверка опор двигателя',
                'title_uz' => 'Dvigatel tayanchlari tekshirildi',
                'title_en' => 'Engine mounts check',
            ],
            [
                'title_ru' => 'Проверка вентиляции картера',
                'title_uz' => 'Karter ventilyatsiyasi tekshirildi',
                'title_en' => 'Crankcase ventilation check',
            ],
            [
                'title_ru' => 'Проверка помпы',
                'title_uz' => 'Nasos (pompa) tekshirildi',
                'title_en' => 'Water pump check',
            ],
            [
                'title_ru' => 'Проверка термостата',
                'title_uz' => 'Termostat tekshirildi',
                'title_en' => 'Thermostat check',
            ],
            [
                'title_ru' => 'Проверка радиатора и вентилятора',
                'title_uz' => 'Radiator va ventilyator tekshirildi',
                'title_en' => 'Radiator and fan check',
            ],
            [
                'title_ru' => 'Проверка дымности выхлопа',
                'title_uz' => 'Chiqarilayotgan tutun tekshirildi',
                'title_en' => 'Exhaust smoke check',
            ],
            [
                'title_ru' => 'Проверка лямбда-зонда',
                'title_uz' => 'Lambda datchigi tekshirildi',
                'title_en' => 'Oxygen sensor (lambda) check',
            ],
            [
                'title_ru' => 'Проверка катализатора',
                'title_uz' => 'Katalizator tekshirildi',
                'title_en' => 'Catalytic converter check',
            ],
            [
                'title_ru' => 'Проверка утечек выхлопа',
                'title_uz' => 'Chiqarish tizimida sizish yo‘q',
                'title_en' => 'Exhaust leaks check',
            ],
            [
                'title_ru' => 'Проверка ЭБУ на ошибки (OBD-II)',
                'title_uz' => 'ECU xatoliklari (OBD-II) tekshirildi',
                'title_en' => 'ECU error check (OBD-II)',
            ],
            [
                'title_ru' => 'Проверка общего состояния ДВС (оценка: норма / требует ремонта / критично)',
                'title_uz' => 'Dvigatelning umumiy holati (me’yorda / ta’mir talab / jiddiy)',
                'title_en' => 'Overall engine condition (normal / needs repair / critical)',
            ],
        ];

        $i = 1;
        foreach ($checks as $key => $value) {
            $checkcategory = new CarCheck();
            $checkcategory->title_ru = $value['title_ru'];
            $checkcategory->title_uz = $value['title_uz'];
            $checkcategory->title_en = $value['title_en'];
            $checkcategory->order = $i++;
            $checkcategory->type = 'ico';
            $checkcategory->car_check_category_id = 1;
            $checkcategory->car_check_sub_category_id = 3;
            $checkcategory->save();
        }
    }
}
