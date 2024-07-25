<?php

namespace App\Models;

use App\Helper\ErrorHelperResponse;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;

class Tarif extends Model
{
    use HasFactory;
    public function saveModel($data){
        try {
            $this->title = $data['title'];
            $this->body = json_encode($data['body']);
            $this->save();
            return response()->json($this, Response::HTTP_OK);
        } catch (\Throwable $th) {
            $lang['ru']= 'Ошибка: '.$th->getMessage();
            $lang['uz']= 'Xatolik: '.$th->getMessage();
            return ErrorHelperResponse::returnError($lang,Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        
    }
}
