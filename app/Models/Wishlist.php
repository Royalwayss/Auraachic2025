<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Auth;
class Wishlist extends Model
{
    //
    public function product(){
    	return $this->belongsTo('App\Models\Product','product_id')->select('id','category_id','product_name','product_url','product_code','discount_type','product_discount','product_price','final_price','product_color','main_image')->with('productimages');
    }

	public static function wishlists(){
		$wishlists= Wishlist::with('product')->where('user_id',Auth::user()->id)->get();
		return $wishlists;
	}

    public static function checkwishlist($proid){
    	$check = Wishlist::where([
                'user_id'=>Auth::user()->id,
                'product_id' => $proid
            ])->count();
    	return $check;
    }
}
