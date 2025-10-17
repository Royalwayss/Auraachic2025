<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class OrdersProduct extends Model
{
    //
    protected $fillable = ['id','order_id','user_id','product_id','product_name','product_url','category_name','product_code','product_color','mrp','discount','discount_type','product_price','product_qty','sub_total','product_size','product_sku','grand_total','taxable_amount','product_gst','gst_percent','discount_amount','product_discount_amount','prepaid_discount','credit_discount','final_price','item_status','created_at','updated_at'];

    public function productdetail(){
    	return $this->belongsTo('App\Models\Product','product_id')->select('id','category_id','product_name','product_url','description','final_price','product_color','product_weight')->with('product_image','category');
    }

    public static function itemStatus($order_product_id) {
        $orderProduct = OrdersProduct::select('item_status')
            ->where('id', $order_product_id)
            ->whereRaw('created_at >= CURRENT_DATE - INTERVAL 7 DAY')
            ->first();

        if ($orderProduct) {
            return $orderProduct->item_status;
        } else {
            // return default or handle as needed
            return null; // or 'unknown', 'not found', etc.
        }
    }

}
