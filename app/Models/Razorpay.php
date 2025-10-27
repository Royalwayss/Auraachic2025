<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Razorpay extends Model
{
    use HasFactory;

    public static function credentials()
     {
		 $mode = env('PAY_MODE');
		 if($mode == 'live'){ 
			 $RAZORPAY_KEY = env('LIVE_RAZORPAY_KEY'); 
			 $RAZORPAY_SECRET_KEY = env('LIVE_RAZORPAY_SECRET_KEY'); 
			 $RAZORPAY_WEBHOOK_SECRET = env('LIVE_RAZORPAY_WEBHOOK_SECRET'); 
		 }else{
			 $RAZORPAY_KEY = env('TEST_RAZORPAY_KEY'); 
			 $RAZORPAY_SECRET_KEY = env('TEST_RAZORPAY_SECRET_KEY'); 
			 $RAZORPAY_WEBHOOK_SECRET = env('TEST_RAZORPAY_WEBHOOK_SECRET'); 
		 }
		 
		 
		 
		 
		 $credentials = array(
		      'RAZORPAY_KEY'=>$RAZORPAY_KEY,
		      'RAZORPAY_SECRET_KEY'=>$RAZORPAY_SECRET_KEY,
		      'RAZORPAY_WEBHOOK_SECRET'=>$RAZORPAY_WEBHOOK_SECRET
		 );
		 
		 return $credentials;
		 
	 }
	 
	 
	 public static function createOrder($order_id='')
        {  
		   
		     $razorpay_order_id = '';
			 $ordeDetails = Order::where('id',$order_id)->first();
				 if(!empty($ordeDetails)){
				
				 if(empty($ordeDetails['razorpay_order_id'])){
					 
					 $razorpay_credentials = Razorpay::credentials(); 
					 $RAZORPAY_KEY = $razorpay_credentials['RAZORPAY_KEY']; 
					 $RAZORPAY_KEY_SECRET = $razorpay_credentials['RAZORPAY_SECRET_KEY']; 
					 $api = new Api($RAZORPAY_KEY, $RAZORPAY_KEY_SECRET);
					 $amount = $ordeDetails->grand_total;
					 $user_id = $ordeDetails->user_id;
					 $item_id = $ordeDetails->machine_id;
					 
					 try {
						$order  = $api->order->create([
							'receipt'  => 'order_rcptid_' . time(), // Unique receipt ID
							'amount'   => $amount * 100, // Amount in smallest currency unit (e.g., paise for INR)
							'currency' => 'INR', // Or your desired currency
							'payment_capture' => 1,
							'notes'    => [
								'user_id' =>  $user_id,
								'item_id' =>  $item_id ,
							]
						]);
						$razorpay_order_id = $order['id'];
                        Order::where('id',$order_id)->update(['razorpay_order_id'=>$order['id'],'payment_method'=>'razorpay']);
						

					 } catch (\Exception $e) {
						//return response()->json(['error' => $e->getMessage()], 500);
					 }
				 }else{
					   $razorpay_order_id = $ordeDetails['razorpay_order_id'];
				 }
				 
				 
				  $result = [
				      'mode'=>env('RAZORPAY_MODE'),
				      'razorpay_order_id'=>$razorpay_order_id,
				      'order_id'=>$order_id,
				      'amount'=>$ordeDetails->grand_total,
				  ];
				 
				return $result;
			 }
        }
	
	
	
	 
	 
	 
}
