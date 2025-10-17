<?php

namespace App\Http\Controllers\Front;

use Illuminate\Support\Facades\View;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Order;
use App\Models\OrdersHistory;
use App\Models\PushOrderLog;
use App\Models\Crypto;
use Session;
use Auth;

class PaymentController extends Controller
{
    public function ccavenue($id){
        $id =  convert_uudecode(base64_decode($id));
        $user_id = Auth::user()->id;
        $userDetails = User::where(['id' => $user_id])->first();
        $userDetails = json_decode(json_encode($userDetails),true);

        $orderDetails = Order::with(['order_address','getuser','order_products','histories'])->where('id',$id)->first();
        $orderDetails = json_decode(json_encode($orderDetails),true);
        /*echo "<pre>"; print_r($order); die;*/

        $redirect_url = url('ccavenue-response'); 
        $cancel_url = url('ccavenue-fail'); 

        $request ="";
        $param = array();
        $param['tid']=$id;
        $param['merchant_id']="183432";
        $param['order_id']=$id;
        $param['amount']=$orderDetails['grand_total'];
        $param['currency'] = "INR";
        $param['redirect_url'] = $redirect_url;
        $param['cancel_url'] = $cancel_url;
        $param['language'] = "EN";
        $param['billing_name'] = $userDetails['name'];
        $param['billing_address'] = $userDetails['address'];
        $param['billing_city'] = $userDetails['city'];
        $param['billing_state'] = $userDetails['state'];
        $param['billing_zip'] = $userDetails['pincode'];
        $param['billing_country'] = $userDetails['country'];
        $param['billing_tel'] = $userDetails['mobile'];
        $param['billing_email'] = $userDetails['email'];
        $param['delivery_name'] = $orderDetails['order_address']['shipping_name'];
        $param['delivery_address'] =$orderDetails['order_address']['shipping_address'];
        $param['delivery_city'] =$orderDetails['order_address']['shipping_city'];
        $param['delivery_state'] =$orderDetails['order_address']['shipping_state'];
        $param['delivery_zip'] =$orderDetails['order_address']['shipping_postcode'];
        $param['delivery_country'] =$orderDetails['order_address']['shipping_country'];
        $param['delivery_tel'] =$orderDetails['order_address']['shipping_mobile'];
        foreach($param as $key=>$val) {
            $request.= $key."=".urlencode($val);
            $request.= "&";
        }

        

        $working_key='C213B0600CE5217F7FA288AE4B304BAD';//Shared by CCAVENUES
        $access_code='AVVO79FG15AZ41OVZA';//Shared by CCAVENUES
        $encrypted_data = Crypto::encrypt($request,$working_key); // Method for encrypting the data.

        /*echo "<pre>"; print_r($encrypted_data); die;*/
  
        return view('front.ccavenue.ccavenue')->with(compact('encrypted_data','access_code'));
    }

    public function ccavenuefail(){
        return view('front.ccavenue.fail');
    }

    public function ccavenueresponse(Request $request){

        $workingKey='C213B0600CE5217F7FA288AE4B304BAD';     //Working Key should be provided here.
        $encResponse=$request->encResp;         //This is the response sent by the CCAvenue Server
        $rcvdString=Crypto::decrypt($encResponse,$workingKey);      //Crypto Decryption used as per the specified working key.
        $order_status="";
        $decryptValues=explode('&', $rcvdString);
        $dataSize=sizeof($decryptValues);
        echo "<center>";

        for($i = 0; $i < $dataSize; $i++) 
        {
            $information=explode('=',$decryptValues[$i]);
            if($i==3)   $order_status=$information[1];
        }

        if($order_status==="Success")
        {
            /*echo "<br>Thank you for shopping with us. Your credit card has been charged and your transaction is successful. We will be shipping your order to you soon.";*/

            //////////// testing code

            $user_id = Auth::user()->id;
            $ordno = Session::get('orderid'); 
            $amount = round(Session::get('grand_total')); 

            //Update Payment details
            Order::where('id',Session::get('orderid'))->update(['order_status'=>'Payment Captured']);
            OrdersHistory::where('order_id',Session::get('orderid'))->delete();
            $requestdata['order_status'] = 'Payment Captured';
            $requestdata['comments'] =  'Payment has been received';
            $requestdata['order_id'] =  Session::get('orderid');
            OrdersHistory::create($requestdata);

            $orderDetails = Order::with(['order_products','order_address'])->where(['orders.id' => $ordno])->first();
            $orderDetails = json_decode(json_encode($orderDetails),true);

            if($orderDetails['order_status']=='Cancelled'){
                Order::where(['orders.id' => $ordno])->update(['order_status' => 'Payment Captured']);
            }

            $userDetails = User::where(['id' => $user_id])->first();
            $userDetails = json_decode(json_encode($userDetails),true);

            /*Code for Order Email Start*/
            if(env('MAIL_MODE') == "live"){
                $email = Auth::user()->email;
                $messageData = [
                    'orderDetails' => $orderDetails
                ];
                Mail::send('emails.order-success-email', $messageData, function($message) use ($email,$admin_emails){
                    $message->to($email)->subject('Order placed with '.config('constants.project_name'));
                });
            }
            /*Code for Order Email Ends*/

            /*Code for Order Email Start for Admin*/
            if(env('MAIL_MODE') == "live"){
                $admin_email = array("customercare@onvers.com","sumit@royalways.com","kapilseth@versatilegroup.in","jaspreet@rtpltech.com");
                $messageData = [
                    'orderDetails' => $orderDetails
                ];

                Mail::send('emails.admin-order-success-email', $messageData, function($message) use ($admin_email){
                    $message->to($admin_email)->subject('New Order placed at '.config('constants.project_name'));
                });
            }
            /*Code for Order Email Ends for Admin*/

            return redirect('/thanks');

            //////////////// testing code
        }
        else if($order_status==="Aborted")
        {
            /*echo "<br>Thank you for shopping with us.We will keep you posted regarding the status of your order through e-mail";*/
            return redirect('ccavenue-fail');
        
        }
        else if($order_status==="Failure")
        {
            /*echo "<br>Thank you for shopping with us.However,the transaction has been declined.";*/
            return redirect('ccavenue-fail');
        }
        else
        {
            /*echo "<br>Security Error. Illegal access detected";*/
            return redirect('ccavenue-fail');
        }

        return redirect('ccavenue-fail');

        echo "<br><br>";

        echo "<table cellspacing=4 cellpadding=4>";
        for($i = 0; $i < $dataSize; $i++) 
        {
            $information=explode('=',$decryptValues[$i]);
                echo '<tr><td>'.$information[0].'</td><td>'.urldecode($information[1]).'</td></tr>';
        }

        echo "</table><br>";
        echo "</center>";
    }
}
