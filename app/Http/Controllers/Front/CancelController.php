<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrdersHistory;

class CancelController extends Controller
{
    public function cancelOrder($id){
        try {
            $cancelOrderEligible = Order::check_order_cancel($id);
            
            if ($cancelOrderEligible == 0) {
                return redirect()->back()->with('flash_message_error', 'Sorry! Order is not allowed for Cancellation.');
            }

            // Update Order Status to "Cancelled by User"
            Order::where('id', $id)->update(['order_status' => 'Cancelled by User']);

            // Update Order Log
            $log = new OrdersHistory;
            $log->order_id = $id;
            $log->order_status = 'Cancelled by User';
            $log->save();

            return redirect()->back()->with('flash_message_success', 'Your Order has been successfully Cancelled.');
            
        } catch (\Exception $e) {
            \Log::error("Order Cancellation Error: " . $e->getMessage());
            return redirect()->back()->with('flash_message_error', 'Something went wrong. Please try again later.');
        }
    }

}
