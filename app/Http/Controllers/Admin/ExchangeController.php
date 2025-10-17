<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ExchangeRequest;
use App\Models\AdminsRole;
use App\Models\User;
use Carbon\Carbon;
use Session;
use Auth;
use DB;

class ExchangeController extends Controller
{
    public function exchangeRequests(){
        Session::put('page','exchange_requests');
        $exchangeRequests = ExchangeRequest::with('order')->orderby('id','Desc')->get()->toArray();
        //dd($exchangeRequests);

        // Set Admin/Subadmins Permissions for Exchange Requests
        $exchangeModuleCount = AdminsRole::where(['subadmin_id'=>Auth::guard('admin')->user()->id,'module'=>'exchange_requests'])->count();
        $exchangeModule = array();
        if(Auth::guard('admin')->user()->type=="admin"){
            $exchangeModule['view_access'] = 1;
            $exchangeModule['edit_access'] = 1;
            $exchangeModule['full_access'] = 1;
        }else if($exchangeModuleCount==0){
            $message = "This feature is restricted for you!";
            return redirect('admin/dashboard')->with('error_message',$message);
        }else{
            $exchangeModule = AdminsRole::where(['subadmin_id'=>Auth::guard('admin')->user()->id,'module'=>'exchange_requests'])->first()->toArray();
        }

        return view('admin.exchanges.exchange_requests')->with(compact('exchangeRequests','exchangeModule'));
    }

    public function exchangeRequestUpdate(Request $request){
        if($request->isMethod('post')){
            $data = $request->all();
            /*echo "<pre>"; print_r($data); die;*/


            if(isset($data['exchange_status'])){
                $comment = "";
                $comment_datetime = null;
                $send_comment = "";
                if(isset($data['comment'])){
                    $comment = $data['comment'];
                    $comment_datetime = Carbon::now();
                }
                if($data['exchange_status']=="Exchange Approved"){
                    $exchange_status = "Approved";
                }else if($data['exchange_status']=="Exchange Rejected"){
                    $exchange_status = "Rejected";
                }else if($data['exchange_status']=="Exchange Pending"){
                    $exchange_status = "Pending";
                }
                ExchangeRequest::where('id',$data['exchange_id'])->update(['exchange_status'=>$data['exchange_status'],'comment'=>$comment,'comment_datetime'=>$comment_datetime]);
                $orderProductDetails = ExchangeRequest::updateOrderPro($data['exchange_id'],$data['exchange_status']);

                $updated_date = date("Y-m-d");
                date_default_timezone_set("Asia/Kolkata");
                $updated_time = date('H:i:s');
                $updated_at = $updated_date . " " . $updated_time;

                DB::table('orders_products_status')->insert([
                    'order_id' => $data['order_id'],
                    'product_code' => $data['product_code'],
                    'product_status' => $exchange_status,
                    'updated_at' => $updated_at,
                    'created_at' => $updated_at
                ]);

                /*Order::where('id', $data['order_id'])->update(['order_status' => $exchange_status]);*/

                $exchangeDetails = ExchangeRequest::exchangeDetails($data['exchange_id']);
                $getUserDetails = User::getUserDetails($exchangeDetails['user_id']);

                /*Code for SMS Script Start*/
                /*$smsdetails['message'] = "Dear ".$getUserDetails['name'].", your exchange request at Onvers for order #".$exchangeDetails['order_id']." with Product Code ".$exchangeDetails['product_code']." has been ".$data['exchange_status'].". ".$send_comment;
                $smsdetails['mobile'] = $getUserDetails['mobile'];
                SMS::sendSms($smsdetails);*/
                /*Code for SMS Script Ends*/

                $order_id = $data['order_id'];
                $product_code = $data['product_code'];
                $email = $getUserDetails['email'];
                $messageData = ['name'=>$getUserDetails['name'],'email'=>$getUserDetails['email'],'exchangeDetails'=>$exchangeDetails,'exchange_status'=>$exchange_status,'product_code'=>$product_code];
                Mail::send('emails.exchange_status',$messageData,function($message)use($email,$exchange_status,$order_id){
                    $message->to($email)->subject('Exchange '.$exchange_status.' for Order #'.$order_id.' - '.config('constants.project_name').'');
                });

                return redirect('admin/exchange-requests')->with('success_message', 'Exchange Request '.$exchange_status.'!');      
            }
            
            
        }
    }
}
