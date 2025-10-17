<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;
use App\Models\OrdersProduct;
use App\Models\OrdersHistory;
use Carbon\Carbon;
use DateTime;

class Order extends Model
{
    //
    protected $fillable = ['id','user_id','payment_method','payment_gateway','country_code','currency','currency_symbol','razorpay_order_id','razorpay_payment_id','signature','coupon_code','coupon_discount','prepaid_discount','credit','shipping_charges','cod_charges','gst','sub_total','grand_total','payment_status','order_status','comments','gift_id','gift_name','gift_mrp','delivery_method','awb_number','invoice_no','invoice_date','shipment_response','shipment_transaction_identifier','shipment_identification_number','ip_address','created_at','updated_at','manifest_resp','gst_number','customer_number','company_name','taxes','total_weight'];

    public function getuser(){
    	return $this->belongsTo('App\Models\User','user_id');
    }

    public function order_products(){
    	return $this->hasMany('App\Models\OrdersProduct','order_id')->with('productdetail');
    }

    public function order_address(){
    	return $this->hasOne('App\Models\OrdersAddress','order_id');
    }

    public function histories(){
        return $this->hasMany('App\Models\OrdersHistory')->orderby('id','DESC');
    }

    public static function checkCouponUsed($couponcode){
        $checkCouponUsed = DB::table('orders')->where('user_id',Auth::user()->id)->where('coupon_code',$couponcode)->wherein('payment_status',['cod','captured'])->count();
        return $checkCouponUsed;
    }

    public static function createManifest($order_id,$AwbNumber){
        $orderArray = array();
        $orderArray['pickup_location']['pin'] = "141001";
        $orderArray['pickup_location']['add'] = "LUDHIANA, Punjab, 141001, India";
        $orderArray['pickup_location']['phone'] = "";
        $orderArray['pickup_location']['state'] = "Punjab";
        $orderArray['pickup_location']['city'] = "Ludhiana";
        $orderArray['pickup_location']['country'] = "India";
        $orderArray['pickup_location']['name'] = "";

        $orderDetails = Order::join('orders_addresses','orders_addresses.order_id','=','orders.id')->select('orders.id as order','orders_addresses.shipping_mobile as phone','orders.grand_total as cod_amount','orders_addresses.shipping_name as name','orders_addresses.shipping_country as country','orders.created_at as order_date','orders.grand_total as total_amount','orders_addresses.shipping_address as add','orders_addresses.shipping_postcode as pin','orders.payment_method as payment_mode','orders_addresses.shipping_state as state','orders_addresses.shipping_city as city')->where('orders.id',$order_id)->first();
        if($orderDetails->payment_mode=="payu"){
            $orderDetails->payment_mode = "Prepaid";
            $orderDetails->cod_amount = 0;
        }else{
            $orderDetails->payment_mode = "COD";
            $orderDetails->cod_amount = $orderDetails->cod_amount;
        }
        $orderDetails->add = $orderDetails->add." ".$orderDetails->add2;
        $orderDetails->add = str_replace('&', 'and', $orderDetails->add);
        $orderDetails->add = str_replace('\\', '-', $orderDetails->add);
        $orderDetails->add = str_replace('"', '-', $orderDetails->add);
        $orderDetails->add = str_replace(';', '-', $orderDetails->add);
        $orderDetails = json_decode(json_encode($orderDetails),true);
        unset($orderDetails['add2']);
        //echo "<pre>"; print_r($orderDetails); die;
        $orderArray['shipments'][0] = $orderDetails;    
        $productDetails = OrdersProduct::select('product_code','product_name','product_size','product_qty')->where('order_id',$order_id)->get();
        $productDetails = json_decode(json_encode($productDetails),true);
        $products_desc = "";
        $proqty =0;
        foreach($productDetails as $key => $pro){
            $proqty += $pro['product_qty']; 
            $products_desc .= ++$key.'). '.$pro['product_code']."-".$pro['product_name']."-".$pro['product_size']."-";
            $products_desc .= "  ";
        }
        $orderArray['shipments'][0]['quantity'] = $proqty;
        $orderArray['shipments'][0]['return_name'] = "MOHAN 0041210";
        $orderArray['shipments'][0]['return_pin'] = "141001";
        $orderArray['shipments'][0]['return_city'] = "Ludhiana";
        $orderArray['shipments'][0]['return_phone'] = "919501777770";
        $orderArray['shipments'][0]['return_add'] = "SHOP.NO.6 ,193-ANEAR G.T.B.HOSPITAL , LUDHIANA 141001SHASHTRI NAGAR, Ludhiana, Punjab, 141001, India";
        $orderArray['shipments'][0]['return_state'] = "Punjab";
        $orderArray['shipments'][0]['return_country'] = "India";
        $orderArray['shipments'][0]['seller_add'] = "SHOP.NO.6 ,193-ANEAR G.T.B.HOSPITAL , LUDHIANA 141001SHASHTRI NAGAR, Ludhiana, Punjab, 141001, India";
        $orderArray['shipments'][0]['seller_cst'] = "";
        $orderArray['shipments'][0]['seller_name'] = "MOHAN 0041210";
        $orderArray['shipments'][0]['seller_inv'] = "";
        $orderArray['shipments'][0]['seller_tin'] = "";
        $products_desc = str_replace('&', 'and', $products_desc);
        $products_desc = str_replace('\\', '-', $products_desc);
        $products_desc = str_replace('"', '-', $products_desc);
        $products_desc = str_replace(';', '-', $products_desc);
        $orderArray['shipments'][0]['products_desc'] = $products_desc; 
        $orderArray['shipments'][0]['waybill'] = $AwbNumber; 
        $orderArray = 'format=json&data='.json_encode($orderArray);
        $deliveryDetails = DelhiveryApi::delhiveryinfo();
        $url = $deliveryDetails['baseurl'].'/api/cmu/create.json';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $orderArray);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization: Token '.$deliveryDetails['token']));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        return array('status'=>true,'result'=> $result);
    }

    public static function trackOrder($awbNumber){
        $details = DelhiveryApi::delhiveryinfo();
        $url = $details['baseurl']."/api/v1/packages/json/?waybill=".$awbNumber."&token=".$details['token'];
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $contents = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($contents,true);
        //echo "<pre>"; print_r($data); die;
        $finalTrackArr = array();
        if(isset($data['ShipmentData'][0]['Shipment']['Scans'])){
            $trackingDetail = $data['ShipmentData'][0]['Shipment']['Scans'];
            $status = true;
            if(!empty($trackingDetail)){
                foreach ($trackingDetail as $key => $trackings) {
                    $trackers[] =  $trackings['ScanDetail'];
                }
                $orderTrackArr = array('Manifested'=>'Order Placed','In Transit'=>'In Transit','Dispatched'=>'Out For Delivery','Delivered'=>'Delivered');
                $trackinginfos[] = Order::group_by($trackers);
                $trackinginfos  = array_values($trackinginfos[0]);
                $finalTrackArr = array();
                foreach ($orderTrackArr as $tkey => $track) {
                    $key = array_search($tkey, array_column($trackinginfos, 'Scan'));
                    if(is_numeric($key)){
                        $finalTrackArr[$tkey] = $trackinginfos[$key];
                        $nextkey = $key+1;
                        if(isset($trackinginfos[$nextkey]['Scan'])){
                            $finalTrackArr[$tkey]['track_status'] = 'complete';
                        }else{
                            $finalTrackArr[$tkey]['track_status'] = 'active';
                        }
                        $finalTrackArr[$tkey]['track_name'] = $track;
                    }else{
                        $finalTrackArr[$tkey]['track_status'] = 'disabled';
                        $finalTrackArr[$tkey]['track_name'] = $track;
                    }
                }
            }
        }else{
            $trackingDetail = array();
            $status = false;
        }
        //echo "<pre>"; print_r($finalTrackArr); die;
        return array('status'=>$status,'tracker'=>$finalTrackArr);
    }

    public static function group_by($arr) {
        foreach($arr as $k => $v) {
            foreach($arr as $key => $value) {
                if($k != $key && $v['Scan'] == $value['Scan'])
                {
                    unset($arr[$k]);
                }
            }
        }
        return $arr;
    }

    public static function updateDeliveredOrdersProductsStatus($params){
        if(isset($params['order_id']) && $params['order_id']>0){
            $orders = Order::with('order_products')->where('id',$params['order_id'])->get();
        }else{
            $order_status = array("Successful","Dispatched");
            $orders = Order::with('order_products')->whereIn('order_status',$order_status)->where('created_at','>=', Carbon::today()->subDays(120))->orderBy('id','DESC')->limit(120)->get();    
        }
        
        /*$ids = array('100163882');
        $orders = Order::with('order_products')->whereIn('id', $ids)->get();*/
        $orders = json_decode(json_encode($orders),true);
        /*echo "<pre>"; print_r($orders); die;*/
        foreach($orders as $key => $order){
            
            /*$orders[$key]['mobile'] = "9990951960";*/

            DB::beginTransaction();

            $user_mobile = User::select('name','mobile')->where('id',$order['user_id'])->first();

            $getOrderTracking = Uniware::getOrderTracking($orders[$key]['id']);
            /*echo "<pre>"; print_r($getOrderTracking); die;*/
            
            if(isset($getOrderTracking['saleOrderDTO']['saleOrderItems']) && count($getOrderTracking['saleOrderDTO']['saleOrderItems'])>0){
                foreach($getOrderTracking['saleOrderDTO']['saleOrderItems'] as $key1 => $item){
                    if($item['shippingPackageStatus']=="DELIVERED"){

                        $countItemStatus = DB::table('orders_products_status')->where(['order_id'=>$orders[$key]['id'],'product_code'=>$item['itemSku'],'product_status'=>'DELIVERED'])->count();
                        if($countItemStatus==0){

                            $updated_date = date("Y-m-d");
                            date_default_timezone_set("Asia/Kolkata");
                            $updated_time =  date('H:i:s'); //Returns IST

                            $updated_at = $updated_date." ".$updated_time;

                            /*echo "<pre>"; print_r($item['itemSku']); die;*/
                            OrdersProduct::where(['order_id'=>$orders[$key]['id'],'product_code'=>$item['itemSku']])->update(['item_status'=>$item['shippingPackageStatus']]);
                            //echo "Order ".$orders[$key]['id']." Item ".$item['itemSku']." status updated<br>";

                            // Orders Items Status Log
                            DB::table('orders_products_status')->insert(['order_id' => $orders[$key]['id'], 
                    'product_code' => $item['itemSku'],'product_status' => $item['shippingPackageStatus'],'updated_at'=>$updated_at,'created_at'=>$updated_at]);

                            Order::where('id',$orders[$key]['id'])->update(['order_status'=>'Delivered']);
                            //echo "Order ".$orders[$key]['id']." status updated<br>";

                            /*$history = array('order_status'=>'Delivered','comments'=>$comments,'order_id'=>$orders[$key]['id']);
                            OrdersHistory::create($history);*/

                            if(isset($params['sms']) && $params['sms']=="yes"){

                                /*Code for SMS Script Start*/
                                $smsdetails['message'] = "Dear ".$user_mobile->name.", ".$orders[$key]['id']." is delivered successfully. We hope you loved this purchase, Write to ".config('constants.project_email')." for any queries.";
                                $smsdetails['mobile'] = $user_mobile->mobile;

                                /*SMS::sendSms($smsdetails);*/
                                /*Code for SMS Script Ends*/

                            }

                        }

                    } 
                }
            }


            if(isset($getOrderTracking['saleOrderDTO']['shippingPackages']) && count($getOrderTracking['saleOrderDTO']['shippingPackages'])>0){
                foreach($getOrderTracking['saleOrderDTO']['shippingPackages'] as $key2 => $item1){


                    if($item1['status']=="DELIVERED"){
                        

                         $countItemStatus1 = DB::table('order_histories')->where(['order_id'=>$orders[$key]['id'],'order_status'=>'Delivered'])->count();

                        if($countItemStatus1==0){

                            /*echo $orders[$key1]['id'];
                    echo "<pre>"; print_r($item1['trackingNumber']); die;*/

                            $updated_date = date("Y-m-d");
                            date_default_timezone_set("Asia/Kolkata");
                            $updated_time =  date('H:i:s'); //Returns IST

                            $updated_at = $updated_date." ".$updated_time;

                            /*echo "<pre>"; print_r($item['itemSku']); die;*/
                            /*OrdersProduct::where(['order_id'=>$orders[$key]['id'],'product_code'=>$item['itemSku']])->update(['item_status'=>$item['status']]);*/
                            //echo "Order ".$orders[$key]['id']." Item ".$item['itemSku']." status updated<br>";

                            // Orders Items Status Log
                            /*DB::table('orders_products_status')->insert(['order_id' => $orders[$key]['id'], 
                    'product_code' => $item['itemSku'],'product_status' => $item['shippingPackageStatus'],'updated_at'=>$updated_at,'created_at'=>$updated_at]);*/

                            Order::where('id',$orders[$key1]['id'])->update(['delivery_method'=>$item1['shippingCourier'],'awb_number'=>$item1['trackingNumber']]);
                            //echo "Order ".$orders[$key]['id']." status updated<br>";

                            $comments = "Order Status updated by Uniware API";
                            $history = array('order_status'=>'Delivered','comments'=>$comments,'order_id'=>$orders[$key1]['id'],'shipped_by'=>$item1['shippingCourier'],'awb_number'=>$item1['trackingNumber']);
                            OrdersHistory::create($history);

                            if(isset($params['sms']) && $params['sms']=="yes"){

                                /*Code for SMS Script Start*/
                                $smsdetails['message'] = "Dear ".$user_mobile->name.", ".$orders[$key]['id']." is delivered successfully. We hope you loved this purchase, Write to ".config('constants.project_email')." for any queries.";
                                $smsdetails['mobile'] = $user_mobile->mobile;

                                /*SMS::sendSms($smsdetails);*/
                                /*Code for SMS Script Ends*/

                            }

                        }

                    } 
                }
            }

            DB::Commit();

        }    
    }

    public static function updateDispatchedOrdersProductsStatus($params){
        if(isset($params['order_id']) && $params['order_id']>0){
            $orders = Order::with('order_products')->where('id',$params['order_id'])->get();
        }else{
            $orders = Order::with('order_products')->where(['order_status'=>'Successful'])->where('created_at','>=', Carbon::today()->subDays(7))->orderBy('id','DESC')->limit(20)->get();    
        }
        
        /*$ids = array('100163882');
        $orders = Order::with('order_products')->whereIn('id', $ids)->get();*/
        $orders = json_decode(json_encode($orders),true);
        /*echo "<pre>"; print_r($orders); die;*/
        foreach($orders as $key => $order){
            
            /*$orders[$key]['mobile'] = "9990951960";*/

            DB::beginTransaction();

            $user_mobile = User::select('name','mobile')->where('id',$order['user_id'])->first();

            $getOrderTracking = Uniware::getOrderTracking($orders[$key]['id']);
            /*echo "<pre>"; print_r($getOrderTracking); die;*/
            
            if(isset($getOrderTracking['saleOrderDTO']['saleOrderItems']) && count($getOrderTracking['saleOrderDTO']['saleOrderItems'])>0){
                foreach($getOrderTracking['saleOrderDTO']['saleOrderItems'] as $key1 => $item){
                    if($item['shippingPackageStatus']=="DISPATCHED"){

                        $countItemStatus = DB::table('orders_products_status')->where(['order_id'=>$orders[$key]['id'],'product_code'=>$item['itemSku'],'product_status'=>'DISPATCHED'])->count();
                        if($countItemStatus==0){

                            $updated_date = date("Y-m-d");
                            date_default_timezone_set("Asia/Kolkata");
                            $updated_time =  date('H:i:s'); //Returns IST

                            $updated_at = $updated_date." ".$updated_time;

                            /*echo "<pre>"; print_r($item['itemSku']); die;*/
                            OrdersProduct::where(['order_id'=>$orders[$key]['id'],'product_code'=>$item['itemSku']])->update(['item_status'=>$item['shippingPackageStatus']]);
                            //echo "Order ".$orders[$key]['id']." Item ".$item['itemSku']." status updated<br>";

                            // Orders Items Status Log
                            DB::table('orders_products_status')->insert(['order_id' => $orders[$key]['id'], 
                    'product_code' => $item['itemSku'],'product_status' => $item['shippingPackageStatus'],'updated_at'=>$updated_at,'created_at'=>$updated_at]);

                            Order::where('id',$orders[$key]['id'])->update(['order_status'=>'Dispatched']);
                            //echo "Order ".$orders[$key]['id']." status updated<br>";

                            /*$history = array('order_status'=>'Dispatched','comments'=>$comments,'order_id'=>$orders[$key]['id']);
                            OrdersHistory::create($history);*/

                            if(isset($params['sms']) && $params['sms']=="yes"){

                                /*Code for SMS Script Start*/
                                $smsdetails['message'] = "Dear ".$user_mobile->name.", ".$orders[$key]['id']." is dispatched successfully. We hope you loved this purchase, Write to ".config('constants.project_email')." for any queries.";
                                $smsdetails['mobile'] = $user_mobile->mobile;

                                /*SMS::sendSms($smsdetails);*/
                                /*Code for SMS Script Ends*/

                            }

                        }

                    } 
                }
            }


            if(isset($getOrderTracking['saleOrderDTO']['shippingPackages']) && count($getOrderTracking['saleOrderDTO']['shippingPackages'])>0){
                foreach($getOrderTracking['saleOrderDTO']['shippingPackages'] as $key2 => $item1){


                    if($item1['status']=="DISPATCHED"){
                        

                         $countItemStatus1 = DB::table('order_histories')->where(['order_id'=>$orders[$key]['id'],'order_status'=>'Dispatched'])->count();

                        if($countItemStatus1==0){

                            /*echo $orders[$key1]['id'];
                    echo "<pre>"; print_r($item1['trackingNumber']); die;*/

                            $updated_date = date("Y-m-d");
                            date_default_timezone_set("Asia/Kolkata");
                            $updated_time =  date('H:i:s'); //Returns IST

                            $updated_at = $updated_date." ".$updated_time;

                            /*echo "<pre>"; print_r($item['itemSku']); die;*/
                            /*OrdersProduct::where(['order_id'=>$orders[$key]['id'],'product_code'=>$item['itemSku']])->update(['item_status'=>$item['status']]);*/
                            //echo "Order ".$orders[$key]['id']." Item ".$item['itemSku']." status updated<br>";

                            // Orders Items Status Log
                            /*DB::table('orders_products_status')->insert(['order_id' => $orders[$key]['id'], 
                    'product_code' => $item['itemSku'],'product_status' => $item['shippingPackageStatus'],'updated_at'=>$updated_at,'created_at'=>$updated_at]);*/

                            Order::where('id',$orders[$key1]['id'])->update(['delivery_method'=>$item1['shippingCourier'],'awb_number'=>$item1['trackingNumber']]);
                            //echo "Order ".$orders[$key]['id']." status updated<br>";

                            $comments = "Order Status updated by Uniware API";
                            $history = array('order_status'=>'Dispatched','comments'=>$comments,'order_id'=>$orders[$key1]['id'],'shipped_by'=>$item1['shippingCourier'],'awb_number'=>$item1['trackingNumber']);
                            OrdersHistory::create($history);

                            if(isset($params['sms']) && $params['sms']=="yes"){

                                /*Code for SMS Script Start*/
                                $smsdetails['message'] = "Dear ".$user_mobile->name.", ".$orders[$key]['id']." is delivered successfully. We hope you loved this purchase, Write to ".config('constants.project_email')." for any queries.";
                                $smsdetails['mobile'] = $user_mobile->mobile;

                                /*SMS::sendSms($smsdetails);*/
                                /*Code for SMS Script Ends*/

                            }

                        }

                    } 
                }
            }

            DB::Commit();

        }    
    }

    public static function checkOrderReturn($order_id,$order_product_id){
        $orderStatusArr = array("DELIVERED");
        $details = Order::where(['id'=>$order_id])->whereIn('order_status',$orderStatusArr)->orderBy('id','Desc')->first();
        $details = json_decode(json_encode($details),true);
        /*echo "<pre>"; print_r($details); die;*/
        if($details){
            $returnCount = ReturnRequest::where(['order_id'=>$order_id,'order_product_id'=>$order_product_id])->count();
                if($returnCount>0){
                    return false; 
                }else{
                    return true;    
                }
        }else{
            return false;
        }
    }


    public static function checkOrderReturnApproved($order_id){
        $details = Order::where(['id'=>$order_id,'order_status'=>'Return Approved'])->orderBy('id','Desc')->first();
        $details = json_decode(json_encode($details),true);
        /*echo "<pre>"; print_r($details); die;*/
        if($details){
            return true;
        }else{
            return false;
        }
    }


    public function checkPincode(){
        $url = "https://apiv2.shiprocket.in/v1/external/courier/
        serviceability?pickup_postcode=110058&delivery_postcode=110030&weight=1.00&
        cod=0";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json',
                                                'Authorization: Bearer <YOUR_TOKEN_HERE>']);
        $result = curl_exec($curl);
        curl_close($curl);
        print_r(json_decode($result, true));
    }
    
	
	
	public static function get_invoice_no(){
		$get_invoice = Order::select('invoice_no')->where('invoice_no', '!=','')->orderby('id','desc')->first();
	    if(!empty($get_invoice)){
			 
			$invoiceNumber =  (int) filter_var($get_invoice['invoice_no'], FILTER_SANITIZE_NUMBER_INT);
		    $invoiceNumber = $invoiceNumber+1;
			$invoice_no = 'INV'.sprintf('%06d', $invoiceNumber);

		}else{
			$invoice_no = 'INV000001';
		}
		return  $invoice_no; 
		
	}
	
	public static function update_product_sale($order_id){
	  $order = Order::with(['order_products'])->where('id',$order_id)->first();
	  
	  foreach($order['order_products'] as $order_product){
		 $product_qty = $order_product['product_qty'];
		 $product_id = $order_product['product_id'];
		 $update_product = Product::find($product_id);
		 
		  if(!empty($update_product)){
			  
			  $update_product->no_of_sales = $update_product->no_of_sales + $product_qty;
			  $update_product->save();
		  }
		  
	  }
	  
	}
	
    public static function cancelOrderEligible($order_id){
        $status = 1;
        $orderDetails = Order::where('id',$order_id)->first()->toArray();

        // Order is not eligible for Cancel if Order Status is not Payment Captured or Confirmed
        $orderStatus = array("Payment Captured","Confirmed");
        if(!in_array($orderDetails['order_status'], $orderStatus)){
            $status = 0;  
        }

        // Order is not eligible for Cancel if Order Status is more then 24 hours old
        $in24Hour = new DateTime();
        $in24Hour->modify('-24 hour');
        $time = new DateTime($orderDetails['created_at']);

        if ($time < $in24Hour) {
            $status = 0;
        }

        return $status;
    }
	
	
	  public static function check_order_cancel($order_id){
          $order = Order::where('user_id',Auth::user()->id)->where('id',$order_id)->first();	
          if(!empty($order) && $order['order_status'] != 'Delivered' && $order['order_status'] != 'Cancelled by User' && $order['order_status'] != 'Successful'){
		        $in24Hour = new DateTime();
				$in24Hour->modify('-24 hour');
				$time = new DateTime($order->created_at);

				if ($time < $in24Hour) {
					return 0;
				}else{
					 return 1;
				}
			  
			  
			 
		  }else{
			  return 0;
		  }			  
	  }
	
	
	

}
