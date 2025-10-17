<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\OrdersProduct;

class ExchangeRequest extends Model
{
    //
    public static function updateOrderPro($exchange_id,$exchange_status){
        $exchangeRequestDetails = ExchangeRequest::select('order_id','product_id','product_code')->where('id',$exchange_id)->first()->toArray();
        $orderProductDetails = OrdersProduct::where(['order_id'=>$exchangeRequestDetails['order_id'],'product_id'=>$exchangeRequestDetails['product_id'],'product_sku'=>$exchangeRequestDetails['product_code']])->update(['item_status'=>$exchange_status]);
        $orderProductDetails = json_decode(json_encode($orderProductDetails),true);
        /*echo "<pre>"; print_r($orderProductDetails); die;*/
    }

    public static function exchangeDetails($exchange_id){
        $exchangeDetails = ExchangeRequest::select('order_id','product_code','user_id','current_size','requested_size')->where('id',$exchange_id)->first()->toArray();    
        return $exchangeDetails;
    }

    public function order(){
        return $this->belongsTo('App\Models\Order','order_id');
    }

    public static function checkExchangeStatus($order_id,$order_product_id){
        $exchange_status = "";
        $exchangeCount = ExchangeRequest::where('order_id',$order_id)->where('order_product_id',$order_product_id)->count();
        if($exchangeCount>0){
            $exchangeStatus = ExchangeRequest::where('order_id',$order_id)->where('order_product_id',$order_product_id)->first()->toArray();
            $exchange_status = $exchangeStatus['exchange_status'];   
        }else{
            $exchange_status = "";    
        }
        return $exchange_status;
    }
}
