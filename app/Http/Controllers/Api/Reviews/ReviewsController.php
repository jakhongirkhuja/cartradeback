<?php

namespace App\Http\Controllers\Api\Reviews;

use App\Helper\ErrorHelperResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Reviews\ReviewsPostRequest;
use App\Models\Review;
use App\Services\Reviews\ReviewService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReviewsController extends Controller
{
    public function reviews(){
        $reviews = Review::witht('user')->paginate(50);
        return response()->json($reviews, Response::HTTP_OK);
    }
    public function reviewsPost(ReviewsPostRequest $request, ReviewService $reviewService){
        
        return $reviewService->reviewPost($request->validated());
    }
    public function reviewsEdit(ReviewsPostRequest $request, ReviewService $reviewService, $id){
        return $reviewService->reviewEdit($request->validated(),$id);
    }
    public function reviewsDelete($id){
        $reviews = Review::find($id);
        if(!$reviews){
            $lang['ru']= 'Не найден';
            $lang['uz']= 'Topilmadi';
            return ErrorHelperResponse::returnError($lang,Response::HTTP_NOT_FOUND);
        } 
        try {
            if($reviews->avatar && file_exists(public_path('/files/reviews/'.$reviews->avatar))){
                unlink(public_path('/files/reviews/'.$reviews->avatar));
            }
            $reviews->delete();
            $lang['ru']= 'Удален';
            $lang['uz']= 'O`chirildi';
            return response()->json($lang, Response::HTTP_OK);
        } catch (\Throwable $th) {
            $lang['ru']= 'Ошибка';
            $lang['uz']= 'Xatolik';
            return ErrorHelperResponse::returnError($lang,Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
