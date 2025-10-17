<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    public static function getProductRating($product_id){
        // Get Average Rating of product
        $ratingSum = Rating::where(['product_id'=>$product_id,'status'=>1])->sum('rating');
        $ratingCount = Rating::where(['product_id'=>$product_id,'status'=>1])->count();

        $avgRating = 0;
        $avgStarRating = 0;

        if($ratingCount>0){
            $avgRating = round($ratingSum/$ratingCount,2);
            $avgStarRating = round($ratingSum/$ratingCount);
        } 
        return array("ratingSum"=>$ratingSum,"ratingCount"=>$ratingCount,"avgRating"=>$avgRating,"avgStarRating"=>$avgStarRating);   
    }

    public function product(){
        return $this->belongsTo('App\Models\Product','product_id');
    }
            
}
