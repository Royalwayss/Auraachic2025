<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\OrdersProduct;

class ReturnRequest extends Model
{
    //
    public static function updateOrderPro($return_id,$return_status){
        $returnRequestDetails = ReturnRequest::select('order_id','product_id','product_code')->where('id',$return_id)->first()->toArray();
        $orderProductDetails = OrdersProduct::where(['order_id'=>$returnRequestDetails['order_id'],'product_id'=>$returnRequestDetails['product_id'],'product_sku'=>$returnRequestDetails['product_code']])->update(['item_status'=>$return_status]);
        $orderProductDetails = json_decode(json_encode($orderProductDetails),true);
        /*echo "<pre>"; print_r($orderProductDetails); die;*/
    }

    public static function returnDetails($return_id){
        $returnDetails = ReturnRequest::select('order_id','product_code','user_id')->where('id',$return_id)->first()->toArray();    
        return $returnDetails;
    }

    public function order(){
        return $this->belongsTo('App\Models\Order','order_id');
    }

    public function account(){
        return $this->hasOne('App\Models\ReturnAccountDetail','return_id');
    }

    public static function checkReturnStatus($order_id,$order_product_id){
        $return_status = "";
        $returnCount = ReturnRequest::where('order_id',$order_id)->where('order_product_id',$order_product_id)->count();
        if($returnCount>0){
            $returnStatus = ReturnRequest::where('order_id',$order_id)->where('order_product_id',$order_product_id)->first()->toArray();
            $return_status = $returnStatus['return_status'];   
        }else{
            $return_status = "";    
        }
        return $return_status;
    }
}
