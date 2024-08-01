<?php

namespace App\Http\Controllers;

use App\Models\Auksion;
use App\Models\Cars\BodyType;
use App\Models\Cars\CarColor;
use App\Models\Cars\CarCondition;
use App\Models\Cars\CarModel;
use App\Models\Cars\FuilType;
use App\Models\Cars\Mark;
use App\Models\Cars\Transmission;
use App\Models\Tarif;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function saveCar(){
        $response = Http::get('https://cars-base.ru/api/cars?full=1');
        $marks = $response->json();
        
        foreach ($marks as $key => $mark) {
             $smark = new Mark();
             $smark->name = $mark['name'];
             $smark->cyrillic_name = $mark['cyrillic-name'];
             $smark->country = $mark['country'];
             $smark->save();
             if(count( $mark['models'])>0){
                foreach ($mark['models'] as $key => $model) {
                    $smodel = new CarModel();
                    $smodel->mark_id = $smark->id;
                    $smodel->name = $model['name'];
                    $smodel->cyrillic_name = $model['cyrillic-name'];
                    $smodel->class = $model['class'];
                    $smodel->year_from = $model['year-from'];
                    $smodel->year_to = $model['year-to'];
                    $smodel->save();
                }
             }
             
        }
       
        
        return '404';
    }
    public function saveOthers(){
        $kuzov = [ ['Кабриолет','Kabriolet'],
        ['Пикап','Пікап'],
        ['Купе','Kupe'],
        ['Универсал','Universal'],
        ['Хэтчбек','Xetchbek'],
        ['Минивэн','Miniven'],
        ['Внедорожник','Yo‘l tanlamas'],
        ['Седан','Sedan'],
        ['Другой','Boshqa'] ];
        foreach ($kuzov as $key => $kuz) {
            $bodyType = new BodyType();
            $bodyType->name = json_encode($kuz);
            $bodyType->save();
        }
        $fuil = [ ['Бензин','Benzin'],
        ['Дизель','Dizel'],
        ['Гибрид','Gibrid'],
        ['Газ/Бензин','Gaz/Benzin'],
        ['Другой','Boshqa'],
        ];
        foreach ($fuil as $key => $kuz) {
            $bodyType = new FuilType();
            $bodyType->name = json_encode($kuz);
            $bodyType->save();
        }
        $karobka = [ ['Механическая','Mexanik'],
        ['Автоматическая','Avtomatik'],
        ['Другой','Boshqa'],
        ];
        foreach ($karobka as $key => $kuz) {
            $bodyType = new Transmission();
            $bodyType->name = json_encode($kuz);
            $bodyType->save();
        }
        $sostayaniya = [ 
            ['Новый','Yangi'],
            ['Отличное','A`lo'],
        ['Хорошее','Yaxshi'],
        ['Среднее','O‘rta'],
        ['Требует ремонта','Ta`mir talab '],
        ];
        foreach ($sostayaniya as $key => $kuz) {
            $bodyType = new CarCondition();
            $bodyType->name = json_encode($kuz);
            $bodyType->save();
        }
        $color = [ 
            ['Белый','Oq'],
            ['Черный','Qora'],
        ['Синий','Ko‘k'],
        ['Серый','Kul rang'],
        ['Серебристый','Kumush rang'],
        ['Красный','Qizil'],
        ['Зеленый','Yashil'],
        ['Апельсин','Apelsin'],
        ['Асфальт','Asalt'],
        ['Бежевый','Och jigar rang'],
        ['Бирюзовый','Feruza rang'],
        ['Бронзовый','Bronza rang'],
        ['Вишнёвый','Olcha rang'],
        ['Голубой','Moviy rang'],
        ['Желтый','Sariq'],
        ['Золотой','Tilla rang'],
        ['Коричневый','Jigar rang'],
        ['Магнолии','Magnolii'],
        ['Матовый','Jilosiz'],
        ['Оливковый','Och jigar rang'],
        ['Розовый','Pushti rang'],
        ['Сафари','Safari'],
        ['Фиолетовый','Binafsha rang'],
        ['Хамелеон','Hamelion'],
        ];
        foreach ($color as $key => $kuz) {
            $bodyType = new CarColor();
            $bodyType->name = json_encode($kuz);
            $bodyType->save();
        }
        $tarif1['name'] =  'Депозит Лайт';
        $arr = ['Более 200 автомобилей каждый день которые не видят  автодилеры без депозита.','Доступ к экспресс и длительным к аукционам.','Покупка машин до 30.000 $','Возможность купить машину по блиц цене.','Покупка не более одной машины.'];
        $tarif1['body'] = json_encode($arr);
        $tarif1['price'] = '1000000';
        $tarif1['order'] = 1;
        $tarif2['name'] = 'Депозит Стандарт';
        $arr = ['Доступ к экспресс и длительным к аукционам.',
        'Возможность покупать автомобиль по любой стоимости.',
        'Возможность купить автомобиль по блиц-цене.',
        'Покупка неограниченного количества машин.',
        'Низкая конкуренция между автодилерами.',
        'Доступ к неограниченному количеству автомобилей.',
    ];
        $tarif2['body'] = json_encode($arr);
        $tarif2['price'] = '2000000';
        $tarif2['order'] = 2;
        $tarif = new Tarif();
        $tarif->name = $tarif1['name'];
        $tarif->body = $tarif1['body'];
        $tarif->price = $tarif1['price'];
        $tarif->save();
        $tarif = new Tarif();
        $tarif->name = $tarif2['name'];
        $tarif->body = $tarif2['body'];
        $tarif->price = $tarif2['price'];
        $tarif->save();
    }
    public function index(){
       
        // $tarif = User::find(3);
        // $tarif->role ='admin';
        // $tarif->save();
        // dd($tarif);
        // $this->saveOthers();
        // $this->saveCar();
       
        return response()->json('404');
    }
}
