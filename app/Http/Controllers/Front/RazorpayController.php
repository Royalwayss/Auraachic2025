<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;
use App\Models\Razorpay;
use App\Models\Mails;
use Session;
use Illuminate\Support\Facades\Mail;
use App\Models\OrdersHistory;
use Illuminate\Support\Facades\Log;
use Auth;
use Redirect;
use Razorpay\Api\Api;
class RazorpayController extends Controller
{
    //
	public function __construct(Order $order,OrdersHistory $orderhistory){
        $this->order           = $order;
        $this->orderhistory    = $orderhistory;
    }

    public function razorpayPayment(){  
        if(isset($_GET) && !empty($_GET['id'])){
			$credentials =  Razorpay::credentials();
            $checkValidPayment = $this->order->where('id',$_GET['id'])->first();
            if($checkValidPayment){ 
                if($checkValidPayment->razorpay_order_id==""){
                    $api = new Api($credentials['RAZORPAY_KEY'], $credentials['RAZORPAY_SECRET_KEY']);
                    $order  = $api->order->create(array('receipt' => $checkValidPayment->id, 'amount' => ($checkValidPayment->grand_total*100), 'currency' => 'INR','payment_capture'=>1)); // Creates order
                    $orderid = $order['id']; 
                    //Update Orderid In orders Table
                    $this->order->where('id',$checkValidPayment->id)->update(['txn_id'=>$orderid,'razorpay_order_id'=>$orderid,'payment_gateway'=>'Razorpay']);
                }else{
                    if($checkValidPayment->payment_status=="captured"){
                        return redirect::to('/');
                    }else{
                        $orderid = $checkValidPayment->razorpay_order_id;
                    }
                } 
                return view('front.pages.products.razorpay.razor-payment')->with(compact('orderid','checkValidPayment','credentials'));
            }else{
                abort(404);
            }
        }else{
            abort(404);
        }
    }

    
	
	 public function rozerpay_webhook(Request $request)
    {
        Log::info('Razorpay webhook trigger', ['status' => true]);
		return response()->json(['status' => 'success']); exit; die();
		$data = $request->getContent();
        $signature = $request->header('X-Razorpay-Signature');
         $razorpay_credentials = Razorpay::credentials();  
        $webhookSecret = $razorpay_credentials['RAZORPAY_WEBHOOK_SECRET']; 
        
        // Verify signature
        $expectedSignature = hash_hmac('sha256', $data, $webhookSecret);

        if ($signature !== $expectedSignature) {
            Log::warning('Razorpay webhook signature mismatch');
            return response()->json(['status' => 'invalid signature'], 400);
        }

        // Log or handle the webhook
        $payload = json_decode($data, true);
        $event = $payload['event'];

        Log::info('Razorpay webhook received', ['event' => $event, 'payload' => $payload]);
         
        // Example: Handle payment captured
        if ($event === 'payment.captured') {
            $paymentId = $payload['payload']['payment']['entity']['order_id'];
            $amount = $payload['payload']['payment']['entity']['amount'];

            Order::where('razorpay_order_id',$paymentId)->update(['payment_status'=>'captured','order_status'=>'Payment Captured','razorpay_response'=>$payload,'signature'=>$signature,'order_status'=>'Payment Captured' ]);
            $orderDetails = Order::where('razorpay_order_id',$paymentId)->first();
			
			$requestdata['order_status'] = 'Payment Captured';
			$requestdata['comments'] =  'Payment has been received';
			$requestdata['order_id'] =  $orderDetails->id;
			OrdersHistory::create($requestdata);
			
			Log::info('RAZORPAY_ORDER_ID', [$paymentId ]);
		}else{
		/*	 Log::warning('Razorpay payment failed');
			 $paymentId = $payload['payload']['payment']['entity']['order_id'];
             $amount = $payload['payload']['payment']['entity']['amount'];
			 Order::where('razorpay_order_id',$paymentId)->update(['order_status'=>'Cancelled','webhook_payment_response'=>$payload]); */
		}

        return response()->json(['status' => 'success']);
    }
 
 
 
 
 
	
	
	
	
	
	
    public function dopayment(Request $request) {
        if($request->ajax()){
             
			$data = $request->all();
            $details = $this->order->where('razorpay_order_id',$data['data']['razorpay_order_id'])->orderby('id','DESC')->first();
            if($details){
                $raorpayPaymentId = $data['data']['razorpay_payment_id'];
                $razorpaySignature = $data['data']['razorpay_signature'];
                
				//check Payment details
                $this->order->where('razorpay_order_id',$data['data']['razorpay_order_id'])->update(['razorpay_payment_id'=>$raorpayPaymentId,'payment_status'=>'captured','signature'=>$razorpaySignature,'order_status'=>'Payment Captured']);
                OrdersHistory::where('order_id',$details->id)->delete();
                $requestdata['order_status'] = 'Payment Captured';
                $requestdata['comments'] =  'Payment has been received';
                $requestdata['order_id'] =  $details->id;
                OrdersHistory::create($requestdata);
                
                
                    Mails::orderMail($details->id);
                
                return response()->json([
                    'status'=>true,
                ]);
            }else{
                return response()->json([
                    'status'=>false,
                ]);
            }
        }
    }

    public function verifyRazorpayPayment($id=null){
        if($id>0){
            $prepaidOrders = Order::where('id',$id)->limit(1)->get();
        }else{
            // Check for last 10 prepaid orders
             $prepaidOrders = Order::where(['payment_gateway'=>'Razorpay'])->orderBy('id','DESC')->limit(100)->get();    
        }
        
        $prepaidOrders = json_decode(json_encode($prepaidOrders));
        /*echo "<pre>"; print_r($prepaidOrders); die;*/

        /*if($id>0){
            echo "<pre>"; print_r($prepaidOrders);
        }*/

        if(!empty($prepaidOrders)){
            foreach($prepaidOrders as $order){

                $api_key = env('RAZORPAY_KEY');
                $api_secret = env('RAZORPAY_SECRET');
                
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    /*CURLOPT_URL => "https://".$api_key.":".$api_secret."@api.razorpay.com/v1/payments?count=100&notes=".$order->id,*/
                    CURLOPT_URL => "https://".$api_key.":".$api_secret."@api.razorpay.com/v1/payments?count=100&order_id=".$order->razorpay_order_id,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_TIMEOUT => 30,
                    /*CURLOPT_HTTP_VERSION => CURLOPT_HTTP_VERSION_1_1,*/
                    CURLOPT_CUSTOMREQUEST => "GET",
                    CURLOPT_HTTPHEADER => array(
                        "cache-control: no-cache",
                        "content-type: application/x-www-form-urlencoded"
                    ),
                ));

                $payments = curl_exec($curl);
                curl_close($curl);
                $payments = json_decode($payments,true);

                /*echo "<pre>"; print_r($payments); die;*/

                if($id>0){
                    echo "<pre>"; print_r($payments); die;
                }
                
                if(isset($payments['items'][0])){
                    $itemsArr = $payments['items'];
                    $key = array_search('captured', array_column($itemsArr, 'status'));
                    /*echo $payments['items'][$key]['status'];
                    echo $order->order_status;
                    echo "<pre>"; print_r($key); die;*/

                    if($payments['items'][$key]['status'] =="captured"){
                        if($order->order_status == "Cancelled"){

                            // Update Orders table with Payment Captured Status
                            Order::where(['id' => $order->id])->update(['order_status' => 'Payment Captured','payment_status' => $payments['items'][$key]['status'],'razorpay_payment_id'=>$payments['items'][$key]['id']]);

                            // Update Logs
                            $requestdata['order_status'] = 'Payment Captured';
                            $requestdata['comments'] =  'Payment has been received';
                            $requestdata['order_id'] =  $order->id;
                            OrdersHistory::create($requestdata);

                            echo $order->id." payment made, status updated to Success<br>";
                        }                       
                    
                    }else{
                        
                    }
                }else{
                    
                }
            }
            echo "cron job run successfully";
        }else{
            echo "nothing to run";
        }
        
    }

    public function cancel(){
        return view('front.pages.products.razorpay.cancel');
    } 

}
?>
