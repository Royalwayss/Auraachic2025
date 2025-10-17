<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;

class SavedProduct extends Model
{
    use HasFactory;

    public function product(){
        return $this->belongsTo('App\Models\Product','product_id')->select('id','category_id','product_name','product_code','discount_type','product_discount','product_price','final_price','product_color')->with('product_image');
    }

    public static function savedproducts(){
        $savedproducts = array();
        if(Auth::check()){
            $savedproducts = SavedProduct::with('product')->where('user_id',Auth::user()->id)->get()->toArray();  
        }
        return $savedproducts;
    }

    public static function checksavedproduct($proid,$size){
        $check = SavedProduct::where([
                'user_id'=>Auth::user()->id,
                'product_id' => $proid,
                'size' => $size
            ])->count();
        return $check;
    }
}
