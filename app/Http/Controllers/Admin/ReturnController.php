<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ReturnRequest;
use App\Models\AdminsRole;
use App\Models\User;
use Carbon\Carbon;
use Session;
use Auth;

class ReturnController extends Controller
{
    public function returnRequests(){
        Session::put('page','return_requests');
        $returnRequests = ReturnRequest::with('order','account')->orderby('id','Desc')->get()->toArray();
        //dd($returnRequests);

        // Set Admin/Subadmins Permissions for Return Requests
        $returnModuleCount = AdminsRole::where(['subadmin_id'=>Auth::guard('admin')->user()->id,'module'=>'return_requests'])->count();
        $returnModule = array();
        if(Auth::guard('admin')->user()->type=="admin"){
            $returnModule['view_access'] = 1;
            $returnModule['edit_access'] = 1;
            $returnModule['full_access'] = 1;
        }else if($returnModuleCount==0){
            $message = "This feature is restricted for you!";
            return redirect('admin/dashboard')->with('error_message',$message);
        }else{
            $returnModule = AdminsRole::where(['subadmin_id'=>Auth::guard('admin')->user()->id,'module'=>'return_requests'])->first()->toArray();
        }

        return view('admin.returns.return_requests')->with(compact('returnRequests','returnModule'));
    }

    public function returnRequestUpdate(Request $request){
        if($request->isMethod('post')){
            $data = $request->all();
            /*echo "<pre>"; print_r($data); die;*/


            if(isset($data['return_status'])){
                $comment = "";
                $comment_datetime = null;
                $send_comment = "";
                if(isset($data['comment'])){
                    $comment = $data['comment'];
                    $comment_datetime = Carbon::now();
                }
                if($data['return_status']=="Return Approved"){
                    $return_status = "Approved";
                }else if($data['return_status']=="Return Rejected"){
                    $return_status = "Rejected";
                }else if($data['return_status']=="Return Pending"){
                    $return_status = "Pending";
                }
                ReturnRequest::where('id',$data['return_id'])->update(['return_status'=>$data['return_status'],'comment'=>$comment,'comment_datetime'=>$comment_datetime]);
                $orderProductDetails = ReturnRequest::updateOrderPro($data['return_id'],$data['return_status']);
                $returnDetails = ReturnRequest::returnDetails($data['return_id']);
                $getUserDetails = User::getUserDetails($returnDetails['user_id']);

                /*Code for SMS Script Start*/
                /*$smsdetails['message'] = "Dear ".$getUserDetails['name'].", your return request at Onvers for order #".$returnDetails['order_id']." with Product Code ".$returnDetails['product_code']." has been ".$data['return_status'].". ".$send_comment;
                $smsdetails['mobile'] = $getUserDetails['mobile'];
                SMS::sendSms($smsdetails);*/
                /*Code for SMS Script Ends*/

                $order_id = $returnDetails['order_id'];
                $product_code = $returnDetails['product_code'];
                $email = $getUserDetails['email'];
                $messageData = ['name'=>$getUserDetails['name'],'email'=>$getUserDetails['email'],'returnDetails'=>$returnDetails,'return_status'=>$return_status,'product_code'=>$product_code];
               

                return redirect('admin/return-requests')->with('success_message', 'Return Request '.$return_status.'!');      
            }
            
            
        }
    }
}
