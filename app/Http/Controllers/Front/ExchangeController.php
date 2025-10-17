<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrdersProduct;
use App\Models\ExchangeRequest;
use Carbon\Carbon;
use Auth;
use DB;

class ExchangeController extends Controller
{
    public function verifyExchangeProduct(Request $request){
        if ($request->isMethod('post')) {
            $data = $request->all();

            if (isset($data['order_id'])) {
                $exchangeCount = ExchangeRequest::where([
                    'order_id' => $data['order_id'],
                    'order_product_id' => $data['order_product_id']
                ])->count();

                if ($exchangeCount > 0) {
                    return redirect()->back()->with('flash_message_error', 'Exchange is already Initiated for this Order Item!');
                }

                $comment = "";
                $comment_datetime = null;
                $send_comment = "";

                if (isset($data['comment'])) {
                    $comment = $data['comment'];
                    $comment_datetime = Carbon::now();
                }

                $pushed_date_time = Carbon::now();

                $exchange_reason = $data['exchange_reason'];
                if ($exchange_reason == 'Other' && !empty($data['exchange_more_details'])) {
                    $exchange_reason = $data['exchange_more_details'];
                }

                $exchange = new ExchangeRequest;
                $exchange->order_id = $data['order_id'];
                $exchange->order_product_id = $data['order_product_id'];
                $exchange->user_id = Auth::user()->id;
                $exchange->product_id = $data['product_id'];    
                $exchange->product_code = $data['sku'];
                $exchange->current_size = $data['current_size'];
                $exchange->requested_size = $data['requested_size'];
                $exchange->exchange_reason = $exchange_reason;
                $exchange->exchange_more_details = $data['exchange_more_details'] ?? null;
                $exchange->exchange_status = "Exchange Initiated";
                $exchange->save();
                $exchange_id = DB::getPdo()->lastInsertId();

                $updated_date = date("Y-m-d");
                date_default_timezone_set("Asia/Kolkata");
                $updated_time = date('H:i:s');
                $updated_at = $updated_date . " " . $updated_time;

                OrdersProduct::where([
                    'order_id' => $data['order_id'],
                    'product_sku' => $data['sku']
                ])->update(['item_status' => 'Exchange Initiated']);

                DB::table('orders_products_status')->insert([
                    'order_id' => $data['order_id'],
                    'product_code' => $data['sku'],
                    'product_status' => 'Exchange Initiated',
                    'updated_at' => $updated_at,
                    'created_at' => $updated_at
                ]);

                /*Order::where('id', $data['order_id'])->update(['order_status' => 'Exchange Initiated']);*/

                return redirect()->back()->with('flash_message_success', 'Exchange is successfully Initiated');
            }
        }
    }

}
