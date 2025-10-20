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
        $electric_system_checks = [
            [
                'title_ru' => 'Проверка включения системы (READY / ON без ошибок)',
                'title_uz' => 'Tizimni yoqish (READY / ON) holati xatosiz tekshiriladi',
                'title_en' => 'System activation check (READY / ON without errors)',
            ],
            [
                'title_ru' => 'Проверка отсутствия ошибок на панели (Check EV System и др.)',
                'title_uz' => 'Panelda xatoliklar yo‘qligi tekshiriladi (Check EV System va boshqalar)',
                'title_en' => 'Dashboard error check (Check EV System, etc.)',
            ],
            [
                'title_ru' => 'Проверка состояния тягового аккумулятора (SoH, остаточная ёмкость)',
                'title_uz' => 'Tortish akkumulyatori holati (SoH, qolgan sig‘im) tekshiriladi',
                'title_en' => 'Traction battery condition check (SoH, remaining capacity)',
            ],
            [
                'title_ru' => 'Проверка напряжения HV-батареи',
                'title_uz' => 'HV-batareya kuchlanishi tekshiriladi',
                'title_en' => 'HV battery voltage check',
            ],
            [
                'title_ru' => 'Проверка температуры HV-батареи',
                'title_uz' => 'HV-batareya harorati tekshiriladi',
                'title_en' => 'HV battery temperature check',
            ],
            [
                'title_ru' => 'Проверка инвертора (охлаждение, шум, состояние)',
                'title_uz' => 'Invertor holati (sovutish, shovqin) tekshiriladi',
                'title_en' => 'Inverter check (cooling, noise, condition)',
            ],
            [
                'title_ru' => 'Проверка редуктора (уровень масла, шумы)',
                'title_uz' => 'Reduktor (moy darajasi, shovqin) tekshiriladi',
                'title_en' => 'Reducer check (oil level, noises)',
            ],
            [
                'title_ru' => 'Проверка соединений HV-кабелей (оранжевые провода, разъёмы)',
                'title_uz' => 'HV-kabel ulanishlari (to‘q sariq simlar, raz’yomlar) tekshiriladi',
                'title_en' => 'HV cable connections check (orange wires, connectors)',
            ],
            [
                'title_ru' => 'Проверка герметичности HV-системы',
                'title_uz' => 'HV tizimi germetikligi tekshiriladi',
                'title_en' => 'HV system tightness check',
            ],
            [
                'title_ru' => 'Проверка охлаждения батареи (жидкость / вентилятор)',
                'title_uz' => 'Batareya sovutish tizimi (suyuqlik / ventilyator) tekshiriladi',
                'title_en' => 'Battery cooling system check (liquid / fan)',
            ],
            [
                'title_ru' => 'Проверка работы электромотора при разгоне',
                'title_uz' => 'Tezlashishda elektromotor ishlashi tekshiriladi',
                'title_en' => 'Electric motor performance check during acceleration',
            ],
            [
                'title_ru' => 'Проверка отклика на педаль акселератора',
                'title_uz' => 'Gaz pedali javob tezligi tekshiriladi',
                'title_en' => 'Throttle pedal response check',
            ],
            [
                'title_ru' => 'Проверка плавности старта',
                'title_uz' => 'Harakatni yumshoq boshlanishi tekshiriladi',
                'title_en' => 'Smooth start check',
            ],
            [
                'title_ru' => 'Проверка рекуперации (заряд при торможении)',
                'title_uz' => 'Rekuperatsiya tizimi (tormozda zaryad) tekshiriladi',
                'title_en' => 'Regeneration system check (charging during braking)',
            ],
            [
                'title_ru' => 'Проверка уровня тока при рекуперации (по сканеру)',
                'title_uz' => 'Rekuperatsiya paytidagi tok darajasi skaner orqali tekshiriladi',
                'title_en' => 'Regenerative current level check (via scanner)',
            ],
            [
                'title_ru' => 'Проверка работы режима Eco / Normal / Sport',
                'title_uz' => 'Eco / Normal / Sport rejimlari ishlashi tekshiriladi',
                'title_en' => 'Eco / Normal / Sport mode functionality check',
            ],
            [
                'title_ru' => 'Проверка температуры электромотора',
                'title_uz' => 'Elektromotor harorati tekshiriladi',
                'title_en' => 'Electric motor temperature check',
            ],
            [
                'title_ru' => 'Проверка вибраций и шумов при движении',
                'title_uz' => 'Harakatda tebranish va shovqinlar tekshiriladi',
                'title_en' => 'Vibration and noise check while driving',
            ],
            [
                'title_ru' => 'Проверка герметичности охлаждающей системы',
                'title_uz' => 'Sovutish tizimi germetikligi tekshiriladi',
                'title_en' => 'Cooling system tightness check',
            ],
            [
                'title_ru' => 'Проверка состояния контактора HV (щёлкает при включении)',
                'title_uz' => 'HV kontakti (yoqilganda bosim tovushi) tekshiriladi',
                'title_en' => 'HV contactor check (clicks when activated)',
            ],
            [
                'title_ru' => 'Проверка зарядки 12V аккумулятора от DC/DC преобразователя',
                'title_uz' => '12V akkumulyator DC/DC o‘zgartirgich orqali zaryadlanishi tekshiriladi',
                'title_en' => '12V battery charging check via DC/DC converter',
            ],
            [
                'title_ru' => 'Проверка изоляции HV-системы',
                'title_uz' => 'HV tizimi izolyatsiyasi tekshiriladi',
                'title_en' => 'HV system insulation check',
            ],
            [
                'title_ru' => 'Проверка эффективности торможения при рекуперации',
                'title_uz' => 'Rekuperatsiya paytidagi tormoz samaradorligi tekshiriladi',
                'title_en' => 'Regenerative braking efficiency check',
            ],
            [
                'title_ru' => 'Проверка ошибок по системе управления электроприводом',
                'title_uz' => 'Elektroprivod boshqaruv tizimi xatolari tekshiriladi',
                'title_en' => 'Electric drive control system error check',
            ],
            [
                'title_ru' => 'Общая оценка состояния тяговой установки (норма / требует внимания / критично)',
                'title_uz' => 'Tortish tizimi umumiy holati (normal / e’tibor kerak / kritik)',
                'title_en' => 'Overall traction system condition (normal / attention required / critical)',
            ],
        ];


        $i = 1;
        foreach ($electric_system_checks as $key => $value) {
            $checkcategory = new CarCheck();
            $checkcategory->title_ru = $value['title_ru'];
            $checkcategory->title_uz = $value['title_uz'];
            $checkcategory->title_en = $value['title_en'];
            $checkcategory->order = $i++;
            $checkcategory->type = 'electro';
            $checkcategory->car_check_category_id = 5;
            $checkcategory->car_check_sub_category_id = 8;
            $checkcategory->save();
        }
    }
}
