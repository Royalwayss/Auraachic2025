<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrdersProduct;
use App\Models\ReturnRequest;
use App\Models\ReturnAccountDetail;
use Carbon\Carbon;
use Auth;
use DB;

class ReturnController extends Controller
{
    public function verifyReturnProduct(Request $request){
        if($request->isMethod('post')){
            $data = $request->all();
            /*echo "<pre>"; print_r($data); die;*/

            if(isset($data['order_id'])){

                $returnCount = ReturnRequest::where(['order_id'=>$data['order_id'],'order_product_id'=>$data['order_product_id']])->count();
                if($returnCount>0){
                     return redirect()->back()->with('flash_message_error','Return is already Initiated for this Order Item!'); 
                }

                DB::beginTransaction();

                $comment = "";
                $comment_datetime = null;
                $send_comment = "";
                if(isset($data['comment'])){
                    $comment = $data['comment'];
                    $comment_datetime = Carbon::now();
                }

                $pushed_date_time = Carbon::now();

                $return = new ReturnRequest;
                $return->order_id = $data['order_id'];    
                $return->order_product_id = $data['order_product_id'];    
                $return->user_id = Auth::user()->id;    
                $return->product_id = $data['product_id'];    
                $return->product_code = $data['sku'];    
                $return->return_type = "Return";    
                $return->return_status = "Return Initiated";    
                $return->payment_method = $data['payment_method'];     
                $return->return_reason = $data['return_reason'];     
                $return->is_pushed = "Yes";    
                $return->pushed_response = "";    
                $return->pushed_date_time = $pushed_date_time;    
                $return->save();
                $return_id = DB::getPdo()->lastInsertId();

                $account = new ReturnAccountDetail;
                $account->return_id = $return_id;    
                $account->account_holder_name = $data['account_holder_name'];   
                $account->bank_name = $data['bank_name'];    
                $account->account_number = $data['account_number'];  
                $account->account_type = $data['account_type']; 
                $account->ifsc_code = $data['ifsc_code'];
                $account->save();    

                $updated_date = date("Y-m-d");
                date_default_timezone_set("Asia/Kolkata");
                $updated_time =  date('H:i:s'); //Returns IST

                $updated_at = $updated_date." ".$updated_time;

                /*echo "<pre>"; print_r($item['itemSku']); die;*/
                OrdersProduct::where(['order_id'=>$data['order_id'],'product_sku'=>$data['sku']])->update(['item_status'=>'Return Initiated']);
                //echo "Order ".$orders[$key]['id']." Item ".$item['itemSku']." status updated<br>";

                /*echo $data['order_id']; echo "--"; echo $data['sku']; die;*/

                // Orders Items Status Log
                DB::table('orders_products_status')->insert(['order_id' => $data['order_id'], 
                'product_code' => $data['sku'],'product_status' => 'Return Initiated','updated_at'=>$updated_at,'created_at'=>$updated_at]);

                /*Order::where('id',$data['order_id'])->update(['order_status'=>'Return Initiated']);*/

                DB::commit();

                return redirect()->back()->with('flash_message_success','Return is successfully Initiated');    
            }
                        
        }
    }
}
