<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Models\AdminsRole;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrdersHistory;
use App\Models\Uniware;
use App\Models\Razorpay;
use App\Models\User;
use App\Models\Warranty;
use App\Models\AwbNumber;
use App\Models\WhatsappApi;
use App\Models\WebSetting;
use App\Models\Mails;
use Session;
use Auth;
use DB;

class OrderController extends Controller
{
    public function orders(){
        Session::put('page','orders');
        $orders = Order::join('users','users.id','=','orders.user_id')->select('orders.*','users.email','users.name','users.mobile')->withCount(['order_products as total_items'=>function($query){
            $query->select(DB::raw('sum(product_qty)'));
        }])->orderby('id','DESC')->get()->toArray();
        /*dd($orders);*/

        // Set Admin/Subadmins Permissions for Brands
        $ordersModuleCount = AdminsRole::where(['subadmin_id'=>Auth::guard('admin')->user()->id,'module'=>'orders'])->count();
        $ordersModule = array();
        if(Auth::guard('admin')->user()->type=="admin"){
            $ordersModule['view_access'] = 1;
            $ordersModule['edit_access'] = 1;
            $ordersModule['full_access'] = 1;
        }else if($ordersModuleCount==0){
            $message = "This feature is restricted for you!";
            return redirect('admin/dashboard')->with('error_message',$message);
        }else{
            $ordersModule = AdminsRole::where(['subadmin_id'=>Auth::guard('admin')->user()->id,'module'=>'orders'])->first()->toArray();
        }

        return view('admin.orders.orders')->with(compact('orders','ordersModule'));
    }

    public function orderDetails($id){
        Session::put('page','orders');
        $orderDetails = Order::with(['order_address','getuser','order_products','histories'])->where('id',$id)->first();
        $orderDetails = json_decode(json_encode($orderDetails),true);
        $getorderstatus = DB::table('orders_statuses')->where('type','yes')->where('status',1)->orderby('sort','ASC')->get();
        $getorderstatus = json_decode(json_encode($getorderstatus),true);
        $title="Order Details";
        /*dd($orderDetails);*/
        // Next & Prev Orders
        $model = 'Order'; // Fully qualified model name
        $prevId = findPreviousId($id, $model); // Start checking with $id - 1
        $nextId = findNextId($id, $model);  // Start checking with $id + 1
        return view('admin.orders.order_details')->with(compact('title','orderDetails','getorderstatus','prevId','nextId'));
    }

    public function viewOrderInvoice($id){
        $orderDetails = Order::withCount(['order_products as total_items'=>function($query){
                $query->select(DB::raw('sum(product_qty)'));
            }])->with(['order_address','getuser','order_products'])->where('id',$id)->first();
        $orderDetails = json_decode(json_encode($orderDetails),true);
        $title="Invoice";
        $numberWords =  convert_number_to_words(round($orderDetails['grand_total'],2));
		$web_settings = WebSetting::find(1);
        return view('admin.orders.order_invoice')->with(compact('title','orderDetails','numberWords','web_settings'));
    }

    public function printInvoice($id){
        $orderDetails = Order::withCount(['order_products as total_items'=>function($query){
                $query->select(DB::raw('sum(product_qty)'));
            }])->with(['order_address','getuser','order_products'])->where('id',$id)->first();
        $orderDetails = json_decode(json_encode($orderDetails),true);
        $title="Invoice";
        $numberWords =  convert_number_to_words(round($orderDetails['grand_total'],2));
        $web_settings = WebSetting::find(1);
		return view('admin.orders.print_invoice')->with(compact('title','orderDetails','numberWords','web_settings'));
    }

    public function viewUserOrderInvoice($id){
        $order_id = decrypt($id);
        $orderDetails = Order::withCount(['order_products as total_items'=>function($query){
                $query->select(DB::raw('sum(product_qty)'));
            }])->with(['order_address','getuser','order_products'])->where('id',$order_id)->first();
        $orderDetails = json_decode(json_encode($orderDetails),true);
        //dd($orderDetails);
        $title="Invoice";
        $numberWords =  convert_number_to_words(round($orderDetails['grand_total'],2));
        return view('admin.orders.order_invoice')->with(compact('title','orderDetails','numberWords'));
    }

    public function viewCourierInvoice($id){
        $orderDetails = Order::withCount(['order_products as total_items'=>function($query){
                $query->select(DB::raw('sum(product_qty)'));
            }])->with(['order_address','getuser','order_products'])->where('id',$id)->first();
        $orderDetails = json_decode(json_encode($orderDetails),true);
        $title="Invoice";
        $numberWords =  convert_number_to_words(round($orderDetails['grand_total'],2));
        return view('admin.orders.courier_invoice')->with(compact('title','orderDetails','numberWords'));
    }

    public function updateOrdersStatus(Request $request){
        if($request->isMethod('post')){
            $data = $request->all();

            if($data['order_status'] == "Successful"){
                $dtdcManifestResp = \App\Models\DtdcApi::createDTDCmanifest($data['order_id']);
                if(isset($dtdcManifestResp['result']['data'][0]['success']) && $dtdcManifestResp['result']['data'][0]['success'] == 1){
                        //Create AWB Number
                        $awbno = new AwbNumber;
                        $awbno->type = "dtdc";
                        $awbno->awb_number = $dtdcManifestResp['result']['data'][0]['reference_number'];
                        $awbno->flag = "Y";
                        $awbno->save();
                        $AwbNumber = $awbno->awb_number;
                        $history = array('order_status'=>'Shipped','awb_number'=>$AwbNumber,'shipped_by'=>'DTDC','updated_by'=>Auth::guard('admin')->user()->id,'order_id'=>$data['order_id']);
                        OrdersHistory::create($history);
                        Order::where('id',$data['order_id'])->update(['order_status'=>'Shipped','awb_number'=>$AwbNumber,'delivery_method'=>'DTDC','manifest_request'=>$dtdcManifestResp['manifest_request'],'manifest_resp'=>$dtdcManifestResp['manifest_result']]);

                        $orderDetails = Order::with(['getuser','order_products','order_address'])->where('id',$data['order_id'])->first();
                        $orderDetails = json_decode(json_encode($orderDetails),true);
                        $email = $orderDetails['getuser']['email'];
                       

                        DB::commit();
                }else{
                    DB::rollback();
                    Order::where('id',$data['order_id'])->update(['manifest_request'=>$dtdcManifestResp['manifest_request'],'manifest_resp'=>$dtdcManifestResp['manifest_result']]);
                    $reason = "";
                    if(isset($dtdcManifestResp['result']['data'][0]['message'])){
                        $reason = $dtdcManifestResp['result']['data'][0]['message'];
                    }
                    return redirect()->back()->with('error_message','Something went wrong. Please contact DTDC customer support. Reason:-'.$reason);
                }
            }else{
                // Update Order Status
                Order::where('id',$data['order_id'])->update(['order_status'=>$data['order_status']]);
                Session::put('success_message','Order Status has been updated Successfully!');

                // Update Courier Name and Tracking Number
                if(!empty($data['delivery_method']) && !empty($data['awb_number'])){
                    Order::where('id',$data['order_id'])->update(['delivery_method'=>$data['delivery_method'],'awb_number'=>$data['awb_number']]);
                }

                // Update Order Log
                $log = new OrdersHistory;
                $log->order_id = $data['order_id'];
                $log->order_status = $data['order_status'];
                if(!empty($data['awb_number'])){
                    $log->awb_number = $data['awb_number'];
                }
                if(!empty($data['delivery_method'])){
                    $log->shipped_by = $data['delivery_method'];
                }
                $log->save();  


                $orderDetails = Order::with(['getuser','order_products','order_address'])->where('id',$data['order_id'])->first();
                $orderDetails = json_decode(json_encode($orderDetails),true);

                if(env('MAIL_MODE') == "live"){
                    
                    $email = $orderDetails['getuser']['email'];
                    $messageData = [
                        'orderDetails' => $orderDetails,
                        'order_status' =>$data['order_status']
                    ];
                    if($data['order_status'] !=""){
                        $data['comments'] = '';
                      Mails::order_status_update($data);
                    }
                } 

                if($data['order_status']=="Cancelled"){
                    // Send WhatsApp Message
                    $template = "order_cancelled";
                    $orderid_string = "#".$data['order_id'];
                    $cancelled_date = date('d F Y h:ia');
                    $parameters = array($orderid_string, $cancelled_date);
                    $sendWhatsappMessage = WhatsappApi::sendWhatsappMessage($template, $orderDetails['order_address']['shipping_mobile'], $parameters);
                }

                if($data['order_status']=="In Transit"){
                    if($data['awb_number']==""){
                        return redirect()->back()->with('error_message','Tracking Number is not generated yet. Please make sure Order is Shipped and Tracking Number is generated from DTDC.');
                    }
                    // Send WhatsApp Message
                    $template = "order_on_the_way";
                    $orderid_string = "#".$data['order_id'];
                    $tracking_number = $data['awb_number'];
                    $parameters = array($orderid_string, $tracking_number);
                    $sendWhatsappMessage = WhatsappApi::sendWhatsappMessage($template, $orderDetails['order_address']['shipping_mobile'], $parameters);
                }

                if($data['order_status']=="Delivered"){
                    // Send WhatsApp Message
                    $template = "delivered";
                    $orderid_string = "#".$data['order_id'];
                    $delivered_date = date('d F Y h:ia');
                    $order_total = "₹ ".round($orderDetails['grand_total'],2);
                    $parameters = array($orderid_string, $delivered_date,$order_total);
                    $sendWhatsappMessage = WhatsappApi::sendWhatsappMessage($template, $orderDetails['order_address']['shipping_mobile'], $parameters);
                }

            }

            $orderDetails = Order::with(['getuser','order_products','order_address'])->where('id',$data['order_id'])->first();
            $orderDetails = json_decode(json_encode($orderDetails),true);
            $email = $orderDetails['getuser']['email'];
            if($data['order_status']=="Shipped"){
                if(env('MAIL_MODE') == "live"){
                    $messageData = [
                        'orderDetails' => $orderDetails
                    ];
                   
                }
            }else{
                /*if(env('MAIL_MODE') == "live"){
                    $messageData = [
                        'orderDetails' => $orderDetails,
                        'order_status' =>$data['order_status']
                    ];
                    if($data['order_status'] !=""){
                        Mail::send('emails.order-status-email', $messageData, function($message) use ($email,$orderDetails){
                            $message->to($email)->subject('Order Status Updated for Order #'.$orderDetails['id']);
                        });
                    }
                }*/
            }

            return redirect()->back();   
            
        }
    }

    public function updateOrdersStatusOld(Request $request){
        if($request->isMethod('post')){
            $data = $request->all();

            /*if(empty($data['delivery_method']) && empty($data['awb_number']) && $data['order_status']=="Shipped"){
                $getResults = Order::pushOrder($data['order_id']);
                if(!isset($getResults['status']) || (isset($getResults['status']) && $getResults['status']=="false")){
                    Session::put('error_message',$getResults['message']);
                    return redirect()->back();
                }
            }*/


            if($data['order_status'] == "Successful"){
                $tokenResp = Uniware::generateToken();
                if(isset($tokenResp['status']) && isset($tokenResp['data']['access_token'])){
                    $orderResp = Uniware::push_orders($tokenResp,$data['order_id']);
                    /*echo "<pre>"; print_r($orderResp); die;*/
                    $finalResp = Uniware::createPushOrderLog($orderResp);
                    if(isset($finalResp['response'][0]['status'])){
                        if($finalResp['response'][0]['status']=="success"){
                            DB::commit();
                            return redirect()->back()->with('flash_message_success','Order Status has been updated successfully!');
                        }else{
                            DB::rollback();
                            return redirect()->back()->with('flash_message_error',$finalResp['response'][0]['error']);
                        }
                    }else{
                        DB::rollback();
                        return redirect()->back()->with('flash_message_error','Order may be cancelled or already pushed to uniware or something went wrong at uniware side');
                    }
                }else{
                    return redirect()->back()->with('flash_message_error','Error from Uniware. Please contact Uniware team');
                }
            }

            if($data['order_status'] == "Refund Complete Payment"){
                $data['order_status'] = "Complete Payment Refunded";
                $prepaidOrder = Order::where('id',$data['order_id'])->first();
                if($prepaidOrder->is_refund=="Yes" || $prepaidOrder->is_refund=="Partial"){
                    return redirect()->back()->with('error_message','Payment is already refunded for this Order!');
                }
                $refundResp = Razorpay::refundRazorpayPayment($data['order_id']);
                if($refundResp['status']==false){
                    return redirect()->back()->with('error_message',$refundResp['message']);
                }
                if($refundResp['status']==true){
                    return redirect()->back()->with('success_message',$refundResp['message']);
                }
                
            }else if($data['order_status'] == "Refund Complete Payment (COD)"){
                $data['order_status'] = "Complete Payment Refunded (COD)";
                $Order = Order::where('id',$data['order_id'])->first();
                if($Order->is_refund=="Yes" || $Order->is_refund=="Partial"){
                    return redirect()->back()->with('error_message','Payment is already refunded for this Order!');
                }

                // Update Orders table with Refund Status
                Order::where(['id' => $data['order_id']])->update(['order_status' => 'Complete Payment Refunded (COD)','is_refund' => 'Yes']);

                // Update Order Log
                $log = new OrdersHistory;
                $log->order_id = $data['order_id'];
                $log->order_status = "Complete Payment Refunded (COD)";
                $log->comments = "COD Payment has been Refunded";            
                $log->save();

                // Get Order Details
                $orderDetails = Order::select('id','user_id','grand_total')->where('id',$data['order_id'])->first();

                // Get User Details
                $userDetails = User::select('name','email')->where('id',$orderDetails->user_id)->first();
                $customer_name = $userDetails->name;
                $customer_email = $userDetails->email;

                $messageData = [
                    'customer_name' => $customer_name,
                    'customer_email' => $customer_email,
                    'order_id' => $orderDetails->id,
                    'refund_amount' => $orderDetails->grand_total
                ];
               

                $message = "Complete Payment is Refunded for this COD Order.";
                return redirect()->back()->with('success_message',$message);
                
            }else if($data['order_status'] == "Refund Partial Payment (COD)"){
                $data['order_status'] = "Partial Payment Refunded (COD)";
                $Order = Order::where('id',$data['order_id'])->first();
                if($Order->is_refund=="Yes"){
                    return redirect()->back()->with('error_message','Payment is already refunded for this Order!');
                }

                // Update Orders table with Refund Status
                Order::where(['id' => $data['order_id']])->update(['order_status' => 'Partial Payment Refunded (COD)','is_refund' => 'Yes']);

                // Update Order Log
                $log = new OrdersHistory;
                $log->order_id = $data['order_id'];
                $log->order_status = "Partial Payment Refunded (COD)";
                $log->comments = "COD Partial Payment has been Refunded";            
                $log->save();

                // Get Order Details
                $orderDetails = Order::select('id','user_id','grand_total')->where('id',$data['order_id'])->first();

                // Get User Details
                $userDetails = User::select('name','email')->where('id',$orderDetails->user_id)->first();
                $customer_name = $userDetails->name;
                $customer_email = $userDetails->email;

                $messageData = [
                    'customer_name' => $customer_name,
                    'customer_email' => $customer_email,
                    'order_id' => $orderDetails->id,
                    'refund_amount' => $orderDetails->grand_total
                ];
               

                $message = "Partial Payment is Refunded for this COD Order.";
                return redirect()->back()->with('success_message',$message);
                
            }


            // Update Order Status
            Order::where('id',$data['order_id'])->update(['order_status'=>$data['order_status']]);
            Session::put('success_message','Order Status has been updated Successfully!');

            // Update Courier Name and Tracking Number
            if(!empty($data['delivery_method']) && !empty($data['awb_number'])){
                Order::where('id',$data['order_id'])->update(['delivery_method'=>$data['delivery_method'],'awb_number'=>$data['awb_number']]);
            }

            // Update Order Log
            $log = new OrdersHistory;
            $log->order_id = $data['order_id'];
            $log->order_status = $data['order_status'];
            if(!empty($data['awb_number'])){
                $log->awb_number = $data['awb_number'];
            }
            if(!empty($data['delivery_method'])){
                $log->shipped_by = $data['delivery_method'];
            }
            $log->save();

            $orderDetails = Order::with(['getuser','order_products','order_address'])->where('id',$data['order_id'])->first();
            $orderDetails = json_decode(json_encode($orderDetails),true);
            $email = $orderDetails['getuser']['email'];
            if($data['order_status']=="Shipped"){
                if(env('MAIL_MODE') == "live"){
                    $messageData = [
                        'orderDetails' => $orderDetails
                    ];
                   
                }
            }else{
                if(env('MAIL_MODE') == "live"){
                    $messageData = [
                        'orderDetails' => $orderDetails,
                        'order_status' =>$data['order_status'],
                        /*'remarks' =>$data['comments'],*/
                    ];
                    
                }   
            }

            return redirect()->back();   
            
        }
    }

    public function pushOrders(){
        $order_status = array("Confirmed","COD Confirmed","Payment Captured","Captured");
        $payment_status = array("COD","captured");
        $qryOrders = Order::where(['delivery_method'=>''])->whereIn('payment_status',$payment_status)->whereIn('order_status',$order_status)->orderBy('id','DESC')->limit(15)->get()->toArray();
        //echo "<pre>"; print_r($qryOrders); die;
        foreach($qryOrders as $data){

            // Update Order Status to Successful
            Order::where('id',$data['id'])->update(['order_status'=>"Successful"]);

            $tokenResp = Uniware::generateToken();
            if(isset($tokenResp['status']) && isset($tokenResp['data']['access_token'])){
                $orderResp = Uniware::push_orders($tokenResp,$data['id']);
                $finalResp = Uniware::createPushOrderLog($orderResp);
            }
        }
    }
	
	
	
	public function update_product_sale(Request $request){
		Product::where('id','!=','')->update(['no_of_sales'=>'0']);
		$orders = Order::select('id')->get();
		$orders = json_decode(json_encode($orders),true);
		foreach($orders as $order){
		     Order::update_product_sale($order['id']);
		}
		echo 'ok';
	}
	
	
	public function refundPartialOrder(Request $request){
        if($request->isMethod('post')){
            $data = $request->all();
            //echo "<pre>"; print_r($data); die;
            $prepaidOrder = Order::where('id',$data['order_id'])->first();
            if($prepaidOrder->is_refund=="Yes"){
                return redirect()->back()->with('error_message','Payment is already refunded for this Order!');
            }
            $refundResp = Razorpay::refundPartialRazorpayPayment($data['order_id'],$data['order_product_id'],$data['amount']);
            if($refundResp['status']==false){
                return redirect()->back()->with('error_message',$refundResp['message']);
            }
            if($refundResp['status']==true){
                return redirect()->back()->with('success_message',$refundResp['message']);
            }    
        }
        
    }

    public function warranties(){
        Session::put('page','warranties');
        //$warranties = Warranty::with('product')->get();
        $warranties = Warranty::get();
        /*$warranties = json_decode(json_encode($warranties));
        echo "<pre>"; print_r($warranties); die;*/

        // Set Admin/Subadmins Permissions for Warranties
        $warrantiesModuleCount = AdminsRole::where(['subadmin_id'=>Auth::guard('admin')->user()->id,'module'=>'warranties'])->count();
        $warrantiesModule = array();
        if(Auth::guard('admin')->user()->type=="admin"){
            $warrantiesModule['view_access'] = 1;
            $warrantiesModule['edit_access'] = 1;
            $warrantiesModule['full_access'] = 1;
        }else if($warrantiesModuleCount==0){
            $message = "This feature is restricted for you!";
            return redirect('admin/dashboard')->with('error_message',$message);
        }else{
            $warrantiesModule = AdminsRole::where(['subadmin_id'=>Auth::guard('admin')->user()->id,'module'=>'warranties'])->first()->toArray();
        }

        return view('admin.warranties.warranties')->with(compact('warranties','warrantiesModule'));
    }

    public function searchOrdersOld(Request $request){
        $query = $request->get('term', ''); // Get the search term
        $orderIDs = Order::where('id', 'LIKE', '%' . $query . '%') // Search by order ID
                         ->pluck('id'); // Fetch matching order IDs
        return response()->json($orderIDs); // Return as JSON
    }

    public function searchOrders(Request $request){
    $query = $request->get('term', '');
    $orders = Order::where('id', 'LIKE', '%' . $query . '%')
                   ->get(['id', 'created_at', 'grand_total']); // Fetch additional details

    $formattedOrders = $orders->map(function ($order) {
        return [
            'id' => $order->id,
            'text' => "Order #{$order->id} - ₹{$order->grand_total} ({$order->created_at->format('d M Y')})"
        ];
    });

    return response()->json($formattedOrders);
}
	
	
}
