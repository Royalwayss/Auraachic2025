<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductsAttribute extends Model
{
    use HasFactory;

    public static function sizes($catids,$catseo=''){
        /*echo "<pre>"; print_r($catids); die;*/
        $ProductIDs = ProductsCategory::select('products_categories.product_id')->whereIn('products_categories.category_id',$catids);
		if($catseo == 'new-arrival'){
			$ProductIDs = $ProductIDs->join('products','products.id','products_categories.product_id')->where('products.is_new','Yes');
		}
		$ProductIDs = $ProductIDs->get();
        $ProductIDs = \Arr::flatten(json_decode(json_encode($ProductIDs),true));
        /*echo "<pre>"; print_r($ProductIDs); die;*/
        $sizes = ProductsAttribute::select('size')->where('status',1)->wherein('product_id',$ProductIDs)->groupby('size')->pluck('size');
        /*dd($sizes); die;*/
        return $sizes;
    }

    public static function prosizes($productid){
        $sizes = ProductsAttribute::select('size')->where('product_id',$productid)->where('status',1)->where('stock','>',0)->groupby('size')->pluck('size')->toArray();
        return $sizes;
    }

    public static function availableSizes($productid,$size){
        $sizes = ProductsAttribute::select('size')->where('product_id',$productid)->where('status',1)->where('stock','>',0)->where('size','!=',$size)->groupby('size')->pluck('size')->toArray();
        return $sizes;
    }

    public static function attributeDetail($proid,$size){
        $details = ProductsAttribute::where(['product_id'=>$proid,'size'=>$size])->where('status',1)->first();
        return $details;
    }

    public static function attributeDetails($attrid){
        $attributeDetails = ProductAttribute::where(['id'=>$attrid])->first();
        return $attributeDetails;    
    }

    public static function attributeCount($proid,$attribute){
        $attributeCount = ProductsAttribute::where(['product_id'=>$proid])->where($attribute,'!=','')->count();
        return $attributeCount;
    }

    public static function sizechartDetails($proid,$attribute){
        $sizechartDetails = ProductsAttribute::select($attribute)->where(['product_id'=>$proid])->pluck($attribute)->toArray();
        return $sizechartDetails;
    }
}
