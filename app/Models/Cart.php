<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;
use App\Models\Cart;
use Session;
use App\Models\Product;
use App\Models\ProductAttribute;
use DB;
use App\Models\Coupon;
class Cart extends Model
{
    public static function getCartItems(){
        if(Auth::check()){
            // If the user is logged in, check from Auth (user_id)
            $user_id = Auth::user()->id;
            $getCartItems = Cart::with('product')->where('user_id',$user_id)->get()->toArray();
        }else{
            // If the user is not logged in, check from Session (session_id)
            $session_id = Session::get('session_id');
            $getCartItems = Cart::with('product')->where('session_id',$session_id)->get()->toArray();
        }
        return $getCartItems;
    }

    public function user(){
        return $this->belongsTo('App\Models\User','user_id')->select('id','name','email');
    }

    public function product(){
    	return $this->belongsTo('App\Models\Product','product_id')->select('id','category_id','brand_id','product_name','product_url','product_code','main_image','discount_type','product_discount','status','product_color','product_gst','product_weight')->with('product_image')->with('category')->with('attributes')->where('status',1);
    }

    public static function items($type){
        $rate = 1;
        $query = Cart::with('product')->join('products','products.id','=','carts.product_id')->join('categories','categories.id','=','products.category_id')->select('carts.*','products.product_name','products.main_image','products_attributes.size','products_attributes.stock','products_attributes.sku','products_attributes.price',DB::raw("CONCAT(FORMAT(ROUND((products_attributes.price - (products_attributes.price*products.product_discount)/100)/'".$rate."'),2 )* carts.qty) AS total_price"))->join('products_attributes','products_attributes.product_id','=','products.id')->whereColumn('products_attributes.size','carts.size')/*->whereColumn('carts.qty','<=','products_attributes.stock')*/->orderby('carts.id','desc')/*->where('products_attributes.stock','>',0)*/->where('categories.status',1)->where('products.status',1)->where('products_attributes.status',1);
        if(Auth::check()){
            $query->where('carts.user_id',Auth::user()->id);  
        }else{
            $query->where('carts.session_id',Session::get('cartsessionId'));
        }
        if($type=="listing"){
            $query = $query->get();  
            $query = json_decode(json_encode($query),true);
        }else{
            $query = $query(); 
        } 
		$totalCartItems = 0;
		$result = [];
		foreach($query as $val){ 
		    $totalCartItems += $val['qty'];
			$val['priceDetails'] = Cart::calProPricing($val);
			$result[] = $val;
		}  
		$cartitems['items'] = $result;
		$cartitems['cartPricing'] = Cart::cartdetails($result);
		$cartitems['totalCartItems'] = $totalCartItems; 
        return $cartitems;
    }

    public static function totalitems(){
        $cartcount = Cart::items('count');
        return $cartcount;
    }

    public static function cartitems(){
        $getcartitems = Cart::items('listing');
        return $getcartitems;
    }

    public static function CalculateShipping($subtotal) {
        $getShippingDetails = DB::table('app_settings')->where('type','shipping')->first();
        if($subtotal > $getShippingDetails->shipping_not_apply_above){
            return 0;
        }else{
            if(Session::has('couponinfo')){
                if(Session::get('couponinfo')->type=="staff"){
                   return 0;
                }
            }  
            if(Auth::check()){
                $testEmails = array('jaspreet@rtpltech.com');
                if(in_array(Auth::user()->email, $testEmails)){
                    return 0;
                }else{
                    return $getShippingDetails->shipping_charges; //if need change the value 0 to actual amount
                }
            }else{
                return $getShippingDetails->shipping_charges; //if need change the value 0 to actual amount
            }
        }
        
    }

    public static function CalculateCODCharges($subtotal) {
        $getShippingDetails = DB::table('app_settings')->where('type','cod_charges')->first();
        if($subtotal > $getShippingDetails->shipping_not_apply_above){
            return 0;
        }else{
            if(Session::has('couponinfo')){
                if(Session::get('couponinfo')->type=="staff"){
                   return 0;
                }
            }  
            if(Auth::check()){
                $testEmails = array('amit1@rtpltech.com');
                if(in_array(Auth::user()->email, $testEmails)){
                    return 0;
                }else{
                    return $getShippingDetails->cod_charges; //if need change the value 0 to actual amount
                }
            }else{
                return $getShippingDetails->cod_charges; //if need change the value 0 to actual amount
            }
        }
        
    }

    public static function calculateTotalGSTOld($cartitems){
        $totalTax = 0;
        foreach($cartitems as $item){
            
            $productTotal = Product::calculateProductsTotal();

            // Get Product Price after deducting Product/Category/Brand Discount
            $priceArr = Product::getProductAttrPrice($item['product_id'],$item['size']);
            /*echo "<pre>"; print_r($priceArr);*/

            // Get Product Price after deducting Coupon Discount (Promotional)
            $discount = 0;
            if(Session::has('couponinfo')){
                $couponinfo = Session::get('couponinfo');
                $couponcode = Session::get('couponinfo')['coupon_code'];
                $amountType = Session::get('couponinfo')['amount_type'];
                $amount = Session::get('couponinfo')['amount'];
                /*echo "<pre>"; print_r(Session::get('couponinfo')); die;*/
                
                    $totaldiscount = Coupon::getCouponAmount(Session::get('couponinfo'),$priceArr['final_price']);

                    if($amountType=="Percentage"){ // Percent
                        $discount = $priceArr['final_price']*$amount/100;
                    }else if($amountType=="Rupees"){ // Rs.
                        // GST Calculation for Promotional Discount in Rs.
                        // promotional_discount/product total after product/category/brand discount
                        // 500/2000  discount/total_price
                        /*echo $priceArr['final_price']*$item['qty']; echo "--";*/
                        $discount_per = ($priceArr['final_price']*$item['qty'])/$productTotal*100;
                        $discount = $totaldiscount*$discount_per/100;
                    }
                
            }

            $product_price = $priceArr['final_price']*$item['qty'];
            $tax_on_amount = $product_price - $discount;

            $getGST = Product::select('product_gst')->where(['id'=>$item['product_id']])->first();
            $tax = $getGST->product_gst; 
            $tax_amount = round($tax_on_amount*$tax/100,2);
            $product_gst[] = $tax_amount;
            $totalTax = $totalTax + $tax_amount;
        }
        if(!isset($product_gst)){
            $product_gst = 0;    
        }
        return array('totalTax'=>$totalTax,'product_gst'=>$product_gst);
    }

    public static function calculateTotalGSTOld1($cartitems){
        $totalTax = 0;
        $taxable_amount = array();
        $discount_amount = array();
        $gst_percent = array();
        foreach($cartitems as $item){
            
            $productTotal = Product::calculateProductsTotal();

            // Get Product Price after deducting Product/Category/Brand Discount
            $priceArr = Product::getProductAttrPrice($item['product_id'],$item['size']);
            /*echo "<pre>"; print_r($priceArr);*/

            // Get Product Price after deducting Coupon Discount (Promotional)
            $discount = 0;
            if(Session::has('couponinfo')){
                $couponinfo = Session::get('couponinfo');
                $couponcode = Session::get('couponinfo')['coupon_code'];
                $amountType = Session::get('couponinfo')['amount_type'];
                $amount = Session::get('couponinfo')['amount'];
                /*echo "<pre>"; print_r(Session::get('couponinfo')); die;*/
                
                    $totaldiscount = Coupon::getCouponAmount(Session::get('couponinfo'),$priceArr['final_price']);
                    if($amountType=="Percentage"){ // Percent
                        $discount_per = $amount;
                        $discount = $priceArr['final_price']*$amount/100;
                        //$discount_amount[] = $discount; 
                    }else if($amountType=="Rupees" || $amountType=="Fixed"){ // Rs.
                        // GST Calculation for Promotional Discount in Rs.
                        // promotional_discount/product total after product/category/brand discount
                        // 500/2000  discount/total_price
                        /*echo $priceArr['final_price']*$item['qty']; echo "--";*/
                        $discount_per = ($priceArr['final_price']*$item['qty'])/$productTotal*100;
                        $discount = $totaldiscount*$discount_per/100;
                        //$discount_amount[] = $discount;
                        /*echo "<pre>"; print_r($discount_amount); die;*/
                    }
                
            }

            $product_price = $priceArr['final_price']*$item['qty'];
            //echo Session::get('order_credit'); die;
            $tax_on_amount = $product_price - $discount - Session::get('order_credit');

            $taxable_amount[] = $tax_on_amount;
            $discount_amount[] = $discount;

            $getGST = Product::select('product_gst')->where(['id'=>$item['product_id']])->first();
            $tax = $getGST->product_gst; 
            $gst_percent[] = $getGST->product_gst;  

            $tax_amount = round($tax_on_amount*$tax/100,2);

            $product_gst[] = $tax_amount;
            $totalTax = $totalTax + $tax_amount;
        }
        if(!isset($product_gst)){
            $product_gst = 0;    
        }
        //echo "<pre>"; print_r($discount_amount); die;
        return array('totalTax'=>$totalTax,'product_gst'=>$product_gst,'taxable_amount'=>$taxable_amount,'gst_percent'=>$gst_percent,'discount_amount'=>$discount_amount);
    }

    public static function calculateTotalGSTOld2($cartitems){
        $totalTax = 0;
        $taxable_amount = array();
        $discount_amount = array();
        $gst_percent = array();
        $total_product_price = 0;
        
        // Step 1: Calculate total product price after discount for proportional credit deduction
        foreach($cartitems as $item){
            $priceArr = Product::getProductAttrPrice($item['product_id'], $item['size']);
            $product_price = $priceArr['final_price'] * $item['qty'];
            $discount = 0;

            if (Session::has('couponinfo')) {
                $couponinfo = Session::get('couponinfo');
                $amountType = $couponinfo['amount_type'];
                $amount = $couponinfo['amount'];
                $totaldiscount = Coupon::getCouponAmount($couponinfo, $priceArr['final_price']);

                if ($amountType == "Percentage") { 
                    $discount = $priceArr['final_price'] * $amount / 100;
                } else if ($amountType == "Rupees" || $amountType == "Fixed") { 
                    $discount_per = ($product_price) / Product::calculateProductsTotal() * 100;
                    $discount = $totaldiscount * $discount_per / 100;
                }
            }

            // Subtract the discount from product price
            $total_product_price += ($product_price - $discount);
        }

        // Step 2: Deduct order credit proportionally
        $order_credit = Session::get('order_credit', 0);
        
        foreach($cartitems as $item){
            $priceArr = Product::getProductAttrPrice($item['product_id'], $item['size']);
            $product_price = $priceArr['final_price'] * $item['qty'];
            $discount = 0;

            if (Session::has('couponinfo')) {
                $couponinfo = Session::get('couponinfo');
                $amountType = $couponinfo['amount_type'];
                $amount = $couponinfo['amount'];
                $totaldiscount = Coupon::getCouponAmount($couponinfo, $priceArr['final_price']);

                if ($amountType == "Percentage") { 
                    $discount = $priceArr['final_price'] * $amount / 100;
                } else if ($amountType == "Rupees" || $amountType == "Fixed") { 
                    $discount_per = ($product_price) / Product::calculateProductsTotal() * 100;
                    $discount = $totaldiscount * $discount_per / 100;
                }
            }

            // Calculate proportional order credit deduction
            // 1. Get the product price after applying discounts
            $product_price_after_discount = $product_price - $discount;

            /* 
               2. Distribute the order credit proportionally:
               - Each product contributes a certain percentage to the total order value.
               - Order credit is distributed based on this percentage.
               - Formula:
                   (Product's Post-Discount Price / Total Post-Discount Price) * Total Order Credit
               - This ensures fairness in credit distribution across multiple products.
               - Prevents negative tax calculations when credit is applied.
               
               Example:
               - Suppose there are 2 products:
                 - Product A: ₹1000 after discount
                 - Product B: ₹500 after discount
                 - Total Order Credit: ₹300
                 - Total product price after discount: ₹1500

               - Product A's share: (1000 / 1500) * 300 = ₹200
               - Product B's share: (500 / 1500) * 300 = ₹100

               - This ensures ₹200 credit is deducted from Product A and ₹100 from Product B.
            */

            // Ensure total_product_price is greater than zero to avoid division by zero error
            $credit_deduction = ($total_product_price > 0) ? ($product_price_after_discount / $total_product_price) * $order_credit : 0;

            // Ensure tax_on_amount is never negative
            $tax_on_amount = max(0, $product_price_after_discount - $credit_deduction);

            $taxable_amount[] = $tax_on_amount;
            $discount_amount[] = $discount;

            $getGST = Product::select('product_gst')->where(['id'=>$item['product_id']])->first();
            $tax = $getGST->product_gst;
            $gst_percent[] = $getGST->product_gst;

            $tax_amount = round($tax_on_amount * $tax / 100, 2);
            $product_gst[] = $tax_amount;
            $totalTax += $tax_amount;
        }

        if(!isset($product_gst)){
            $product_gst = 0;    
        }

        return array(
            'totalTax' => $totalTax,
            'product_gst' => $product_gst,
            'taxable_amount' => $taxable_amount,
            'gst_percent' => $gst_percent,
            'discount_amount' => $discount_amount
        );
    }

    public static function calculateTotalGSTOld3($cartitems) {
        $totalTax = 0;
        $taxable_amount = [];
        $discount_amount = [];
        $gst_percent = [];
        $total_product_price = 0;
        
        foreach ($cartitems as $item) {
            $priceArr = Product::getProductAttrPrice($item['product_id'], $item['size']);
            $product_price = $priceArr['final_price'] * $item['qty'];
            $discount = 0;

            if (Session::has('couponinfo')) {
                $couponinfo = Session::get('couponinfo');
                $amountType = $couponinfo['amount_type'];
                $amount = $couponinfo['amount'];
                $totaldiscount = Coupon::getCouponAmount($couponinfo, $priceArr['final_price']);

                if ($amountType == "Percentage") { 
                    $discount = $priceArr['final_price'] * $amount / 100;
                } else if ($amountType == "Rupees" || $amountType == "Fixed") { 
                    $discount_per = ($product_price) / Product::calculateProductsTotal() * 100;
                    $discount = $totaldiscount * $discount_per / 100;
                }
            }

            $total_product_price += ($product_price - $discount);
        }

        $order_credit = Session::get('order_credit', 0);
        
        foreach ($cartitems as $item) {
            $priceArr = Product::getProductAttrPrice($item['product_id'], $item['size']);
            $product_price = $priceArr['final_price'] * $item['qty'];
            $discount = 0;

            if (Session::has('couponinfo')) {
                $couponinfo = Session::get('couponinfo');
                $amountType = $couponinfo['amount_type'];
                $amount = $couponinfo['amount'];
                $totaldiscount = Coupon::getCouponAmount($couponinfo, $priceArr['final_price']);

                if ($amountType == "Percentage") { 
                    $discount = $priceArr['final_price'] * $amount / 100;
                } else if ($amountType == "Rupees" || $amountType == "Fixed") { 
                    $discount_per = ($product_price) / Product::calculateProductsTotal() * 100;
                    $discount = $totaldiscount * $discount_per / 100;
                }
            }

            $product_price_after_discount = $product_price - $discount;
            $credit_deduction = ($total_product_price > 0) ? ($product_price_after_discount / $total_product_price) * $order_credit : 0;
            $tax_on_amount = max(0, $product_price_after_discount - $credit_deduction);

            $taxable_amount[] = $tax_on_amount;
            $discount_amount[] = $discount;

            $getGST = Product::select('product_gst')->where(['id' => $item['product_id']])->first();
            $tax = $getGST->product_gst;
            $gst_percent[] = $getGST->product_gst;

            // Calculate GST from the inclusive price
            $tax_amount = round($tax_on_amount * $tax / (100 + $tax), 2);
            $product_gst[] = $tax_amount;
            $totalTax += $tax_amount;
        }

        if (!isset($product_gst)) {
            $product_gst = 0;    
        }

        return array(
            'totalTax' => $totalTax,
            'product_gst' => $product_gst,
            'taxable_amount' => $taxable_amount,
            'gst_percent' => $gst_percent,
            'discount_amount' => $discount_amount
        );
    }

    public static function calculateTotalGSTOld4($cartitems, $paymentMode) {
        $totalTax = 0;
        $taxable_amount = [];
        $discount_amount = [];
        $prepaid_discount = [];
        $gst_percent = [];
        $total_product_price = 0;
        $product_gst = [];

        // Step 1: Calculate total product price after discount (for proportional credit deduction)
        foreach ($cartitems as $item) {
            $priceArr = Product::getProductAttrPrice($item['product_id'], $item['size']);
            $product_price = $priceArr['final_price'] * $item['qty'];

            // Apply Prepaid Discount if paymentMode is NOT COD (5% discount)
            $prepaid_disc = ($paymentMode !== "COD") ? round($product_price * 5 / 100, 2) : 0;
            $product_price_after_prepaid = $product_price - $prepaid_disc; // Reduce price by prepaid discount

            // Calculate discount from coupon (if any)
            $discount = 0;
            if (Session::has('couponinfo')) {
                $couponinfo = Session::get('couponinfo');
                $amountType = $couponinfo['amount_type'];
                $amount = $couponinfo['amount'];
                $totaldiscount = Coupon::getCouponAmount($couponinfo, $priceArr['final_price']);

                if ($amountType == "Percentage") { 
                    $discount = $priceArr['final_price'] * $amount / 100;
                } else if ($amountType == "Rupees" || $amountType == "Fixed") { 
                    $discount_per = ($product_price) / Product::calculateProductsTotal() * 100;
                    $discount = $totaldiscount * $discount_per / 100;
                }
            }

            // Final taxable amount after prepaid and coupon discount
            $taxable_value = max(0, $product_price_after_prepaid - $discount); // Ensure no negative values

            $total_product_price += $taxable_value;
        }

        // Step 2: Apply order credit deduction proportionally
        $order_credit = Session::get('order_credit', 0);

        foreach ($cartitems as $item) {
            $priceArr = Product::getProductAttrPrice($item['product_id'], $item['size']);
            $product_price = $priceArr['final_price'] * $item['qty'];

            // Apply Prepaid Discount if paymentMode is NOT COD
            $prepaid_disc = ($paymentMode !== "COD") ? round($product_price * 5 / 100, 2) : 0;
            $product_price_after_prepaid = $product_price - $prepaid_disc;

            // Calculate discount from coupon (if any)
            $discount = 0;
            if (Session::has('couponinfo')) {
                $couponinfo = Session::get('couponinfo');
                $amountType = $couponinfo['amount_type'];
                $amount = $couponinfo['amount'];
                $totaldiscount = Coupon::getCouponAmount($couponinfo, $priceArr['final_price']);

                if ($amountType == "Percentage") { 
                    $discount = $priceArr['final_price'] * $amount / 100;
                } else if ($amountType == "Rupees" || $amountType == "Fixed") { 
                    $discount_per = ($product_price) / Product::calculateProductsTotal() * 100;
                    $discount = $totaldiscount * $discount_per / 100;
                }
            }

            // Calculate proportional order credit deduction
            $product_price_after_discount = $product_price_after_prepaid - $discount;
            $credit_deduction = ($total_product_price > 0) ? ($product_price_after_discount / $total_product_price) * $order_credit : 0;

            // Ensure tax_on_amount is never negative
            $tax_on_amount = max(0, $product_price_after_discount - $credit_deduction);

            $taxable_amount[] = $tax_on_amount;
            $discount_amount[] = $discount;
            $prepaid_discount[] = $prepaid_disc; // Store prepaid discount separately

            // Fetch GST % from the database
            $getGST = Product::select('product_gst')->where(['id' => $item['product_id']])->first();
            $gstRate = $getGST ? $getGST->product_gst : 0;
            $gst_percent[] = $gstRate;

            // Calculate GST from an inclusive price
            $tax_amount = round($tax_on_amount * $gstRate / (100 + $gstRate), 2);
            $product_gst[] = $tax_amount;
            $totalTax += $tax_amount;
        }

        return [
            'totalTax' => $totalTax,
            'product_gst' => $product_gst,
            'taxable_amount' => $taxable_amount,
            'gst_percent' => $gst_percent,
            'discount_amount' => $discount_amount,
            'prepaid_discount' => $prepaid_discount // New key for prepaid discount
        ];
    }

    public static function calculateTotalGSTold5($cartitems, $paymentMode) {
        $totalTax = 0;
        $taxable_amount = [];
        $discount_amount = [];
        $prepaid_discount = [];
        $gst_percent = [];
        $total_product_price = 0;
        $product_gst = [];
        $subtotal = 0;
        $total_coupon_discount = 0;

        // Step 1: Calculate subtotal (before applying coupon or prepaid discount)
        foreach ($cartitems as $item) {
            $priceArr = Product::getProductAttrPrice($item['product_id'], $item['size']);
            $subtotal += $priceArr['final_price'] * $item['qty'];
        }

        // Step 2: Get Coupon Discount (If applicable)
        if (Session::has('couponinfo')) {
            $couponinfo = Session::get('couponinfo');
            $amountType = $couponinfo['amount_type'];
            $amount = $couponinfo['amount'];

            if ($amountType == "Percentage") { 
                $total_coupon_discount = round($subtotal * $amount / 100, 2);
            } else if ($amountType == "Rupees" || $amountType == "Fixed") { 
                $total_coupon_discount = min($amount, $subtotal); // Ensure discount is not greater than subtotal
            }
        }

        // Step 3: Apply Coupon Discount to get taxable subtotal
        $subtotal_after_coupon = max(0, $subtotal - $total_coupon_discount);

        // Step 4: Apply Prepaid Discount (if applicable, 5% of subtotal after coupon)
        $prepaid_disc = ($paymentMode !== "COD") ? round($subtotal_after_coupon * 5 / 100, 2) : 0;
        $final_taxable_amount = max(0, $subtotal_after_coupon - $prepaid_disc);

        // Step 5: Calculate GST per product
        foreach ($cartitems as $item) {
            $priceArr = Product::getProductAttrPrice($item['product_id'], $item['size']);
            $product_price = $priceArr['final_price'] * $item['qty'];

            // Proportionate share of coupon discount
            $discount_share = ($subtotal > 0) ? ($product_price / $subtotal) * $total_coupon_discount : 0;

            // Calculate product's share of prepaid discount
            $prepaid_share = ($paymentMode !== "COD") ? ($product_price / $subtotal) * $prepaid_disc : 0;

            // Final taxable amount for this product
            $taxable_value = max(0, $product_price - $discount_share - $prepaid_share);

            $taxable_amount[] = $taxable_value;
            $discount_amount[] = $discount_share;
            $prepaid_discount[] = $prepaid_share;

            // Fetch GST % from database
            $getGST = Product::select('product_gst')->where(['id' => $item['product_id']])->first();
            $gstRate = $getGST ? $getGST->product_gst : 0;
            $gst_percent[] = $gstRate;

            // Calculate GST from an inclusive price
            $tax_amount = round($taxable_value * $gstRate / (100 + $gstRate), 2);
            $product_gst[] = $tax_amount;
            $totalTax += $tax_amount;
        }

        return [
            'totalTax' => $totalTax,
            'product_gst' => $product_gst,
            'taxable_amount' => $taxable_amount,
            'gst_percent' => $gst_percent,
            'discount_amount' => $discount_amount, // Now dynamic based on session coupon
            'prepaid_discount' => $prepaid_discount // Now correctly applied after coupon discount
        ];
    }

    public static function calculateTotalGSTold6($cartitems, $paymentMode) {
        $totalTax = 0;
        $taxable_amount = [];
        $discount_amount = [];
        $prepaid_discount = [];
        $order_credit_discount = [];
        $gst_percent = [];
        $total_product_price = 0;
        $product_gst = [];
        $subtotal = 0;
        $total_coupon_discount = 0;
        $total_order_credit = Session::get('order_credit', 0); // Get Order Credit Discount

        // Step 1: Calculate subtotal (before applying any discounts)
        foreach ($cartitems as $item) {
            $priceArr = Product::getProductAttrPrice($item['product_id'], $item['size']);
            $subtotal += $priceArr['final_price'] * $item['qty'];
        }

        // Step 2: Get Coupon Discount (If applicable)
        if (Session::has('couponinfo')) {
            $couponinfo = Session::get('couponinfo');
            $amountType = $couponinfo['amount_type'];
            $amount = $couponinfo['amount'];

            if ($amountType == "Percentage") { 
                $total_coupon_discount = round($subtotal * $amount / 100, 2);
            } else if ($amountType == "Rupees" || $amountType == "Fixed") { 
                $total_coupon_discount = min($amount, $subtotal); // Ensure discount is not greater than subtotal
            }
        }

        // Step 3: Apply Coupon Discount to get taxable subtotal
        $subtotal_after_coupon = max(0, $subtotal - $total_coupon_discount);

        // Step 4: Apply Prepaid Discount (if applicable, 5% of subtotal after coupon)
        $prepaid_disc = ($paymentMode !== "COD") ? round($subtotal_after_coupon * 5 / 100, 2) : 0;
        $subtotal_after_prepaid = max(0, $subtotal_after_coupon - $prepaid_disc);

        // Step 5: Apply Order Credit Discount (if applicable)
        $final_taxable_amount = max(0, $subtotal_after_prepaid - $total_order_credit);

        // Step 6: Calculate GST per product
        foreach ($cartitems as $item) {
            $priceArr = Product::getProductAttrPrice($item['product_id'], $item['size']);
            $product_price = $priceArr['final_price'] * $item['qty'];

            // Proportionate share of Coupon Discount
            $discount_share = ($subtotal > 0) ? ($product_price / $subtotal) * $total_coupon_discount : 0;

            // Proportionate share of Prepaid Discount
            $prepaid_share = ($paymentMode !== "COD") ? ($product_price / $subtotal) * $prepaid_disc : 0;

            // Proportionate share of Order Credit Discount
            $order_credit_share = ($subtotal_after_prepaid > 0) ? ($product_price / $subtotal_after_prepaid) * $total_order_credit : 0;

            // Final taxable amount for this product
            $taxable_value = max(0, $product_price - $discount_share - $prepaid_share - $order_credit_share);

            $taxable_amount[] = $taxable_value;
            $discount_amount[] = $discount_share;
            $prepaid_discount[] = $prepaid_share;
            $order_credit_discount[] = $order_credit_share;

            // Fetch GST % from database
            $getGST = Product::select('product_gst')->where(['id' => $item['product_id']])->first();
            $gstRate = $getGST ? $getGST->product_gst : 0;
            $gst_percent[] = $gstRate;

            // Calculate GST from an inclusive price
            $tax_amount = round($taxable_value * $gstRate / (100 + $gstRate), 2);
            $product_gst[] = $tax_amount;
            $totalTax += $tax_amount;
        }

        return [
            'totalTax' => $totalTax,
            'product_gst' => $product_gst,
            'taxable_amount' => $taxable_amount,
            'gst_percent' => $gst_percent,
            'discount_amount' => $discount_amount, // Coupon Discount
            'prepaid_discount' => $prepaid_discount, // Prepaid Discount
            'order_credit_discount' => $order_credit_discount // Order Credit Discount
        ];
    }

    public static function calculateTotalGST($cartitems, $paymentMode) {
        $totalTax = 0;
        $taxable_amount = [];
        $discount_amount = [];
        $prepaid_discount = [];
        $order_credit_discount = [];
        $gst_percent = [];
        $total_product_price = 0;
        $product_gst = [];
        $final_price = [];
        $subtotal = 0;
        $total_coupon_discount = 0;
        $total_order_credit = Session::get('order_credit', 0); // Get Order Credit Discount

        // Step 1: Calculate subtotal (before applying any discounts)
        foreach ($cartitems as $item) {
            $priceArr = Product::getProductAttrPrice($item['product_id'], $item['size']);
            $subtotal += $priceArr['final_price'] * $item['qty'];
        }

        // Step 2: Get Coupon Discount (If applicable)
        if (Session::has('couponinfo')) {
            $couponinfo = Session::get('couponinfo');
            $amountType = $couponinfo['amount_type'];
            $amount = $couponinfo['amount'];

            if ($amountType == "Percentage") { 
                $total_coupon_discount = round($subtotal * $amount / 100, 2);
            } else if ($amountType == "Rupees" || $amountType == "Fixed") { 
                $total_coupon_discount = min($amount, $subtotal); // Ensure discount is not greater than subtotal
            }
        }

        // Step 3: Apply Coupon Discount to get taxable subtotal
        $subtotal_after_coupon = max(0, $subtotal - $total_coupon_discount);

        // Step 4: Apply Prepaid Discount (if applicable, 5% of subtotal after coupon)
        $prepaid_disc = ($paymentMode !== "COD") ? round($subtotal_after_coupon * 5 / 100, 2) : 0;
        $subtotal_after_prepaid = max(0, $subtotal_after_coupon - $prepaid_disc);

        // Step 5: Apply Order Credit Discount (if applicable)
        $final_taxable_amount = max(0, $subtotal_after_prepaid - $total_order_credit);

        // Step 6: Calculate GST per product and final price
        foreach ($cartitems as $item) {
            $priceArr = Product::getProductAttrPrice($item['product_id'], $item['size']);
            $product_price = $priceArr['final_price'] * $item['qty'];

            // Proportionate share of Coupon Discount
            $discount_share = ($subtotal > 0) ? ($product_price / $subtotal) * $total_coupon_discount : 0;

            // Proportionate share of Prepaid Discount
            $prepaid_share = ($paymentMode !== "COD") ? ($product_price / $subtotal) * $prepaid_disc : 0;

            // Proportionate share of Order Credit Discount
            $order_credit_share = ($subtotal_after_prepaid > 0) ? ($product_price / $subtotal_after_prepaid) * $total_order_credit : 0;

            // Final taxable amount for this product
            $taxable_value = max(0, $product_price - $discount_share - $prepaid_share - $order_credit_share);

            $taxable_amount[] = $taxable_value;
            $discount_amount[] = $discount_share;
            $prepaid_discount[] = $prepaid_share;
            $order_credit_discount[] = $order_credit_share;

            // Fetch GST % from database
            $getGST = Product::select('product_gst')->where(['id' => $item['product_id']])->first();
            $gstRate = $getGST ? $getGST->product_gst : 0;
            $gst_percent[] = $gstRate;

            // Calculate GST from an inclusive price
            $tax_amount = round($taxable_value * $gstRate / (100 + $gstRate), 2);
            $product_gst[] = $tax_amount;
            $totalTax += $tax_amount;

            // Calculate final price per product
            $final_price[] = $taxable_value + $tax_amount;
        }

        return [
            'totalTax' => $totalTax,
            'product_gst' => $product_gst,
            'taxable_amount' => $taxable_amount,
            'gst_percent' => $gst_percent,
            'discount_amount' => $discount_amount, // Coupon Discount
            'prepaid_discount' => $prepaid_discount, // Prepaid Discount
            'order_credit_discount' => $order_credit_discount, // Order Credit Discount
            'final_price' => $final_price // Final Price after all calculations
        ];
    }



    public static function calProPricing($cartitem){
        /*echo "<pre>"; print_r($cartitem); die;*/
        if(isset($cartitem['product']['product_discount']) && !empty($cartitem['product']['product_discount'])){
            $price = $cartitem['price'] - ($cartitem['price'] * $cartitem['product']['product_discount'] /100);
            $strikePrice =  $cartitem['price'];
            $discount = $cartitem['product']['product_discount'];
            $prosubtotal = $price * $cartitem['qty'];
        }else{
            $price = $cartitem['price'];
            $strikePrice =  0;
            $discount = 0;
            $prosubtotal = $cartitem['price'] * $cartitem['qty'];
        }
        $priceArr['strikePriceString'] =round($strikePrice,2);
        $priceArr['priceString'] =round($price,2);
        $priceArr['subtotalString'] =round($prosubtotal,2);
        $priceArr['strikePrice'] =round($strikePrice,2);
        $priceArr['price'] =round($price,2);
        $priceArr['subtotal'] =round($prosubtotal,2);
        $priceArr['discount'] = $discount;
        return $priceArr;
    }

    public static function cartdetailsOld($cartitems){
        $priceArr = array(); $couponPriceArr =array(); $couponCartItems =array();
        /*echo "<pre>"; print_r($cartitems); die;*/
        foreach($cartitems as $ckey=> $cart){
            $priceDetails = Cart::calProPricing($cart);
            /*echo "<pre>"; print_r($priceDetails); die;*/
            $priceArr[] =  $priceDetails['subtotal'];
            if(Session::has('couponinfo')){
                if(Session::get('couponinfo')['coupon_applicable_on'] =="all"){
                    $couponCartItems[$ckey]['cart_id']   =  $cart['id'];
                    $couponCartItems[$ckey]['qty']       =  $cart['qty'];
                    $couponCartItems[$ckey]['subtotal'] =  $priceDetails['subtotal'];
                    $couponCartItems[$ckey]['unit_price'] =  $priceDetails['price'];
                    $couponPriceArr[] =  $priceDetails['subtotal'];
                }elseif(Session::get('couponinfo')['coupon_applicable_on'] =="discounted" && $priceDetails['discount'] >0){ 
                    $couponCartItems[$ckey]['cart_id']   =  $cart['id'];
                    $couponCartItems[$ckey]['qty']       =  $cart['qty'];
                    $couponCartItems[$ckey]['subtotal'] =  $priceDetails['subtotal'];
                    $couponCartItems[$ckey]['unit_price'] =  $priceDetails['price'];
                    $couponPriceArr[] =  $priceDetails['subtotal'];
                }elseif(Session::get('couponinfo')['coupon_applicable_on'] =="non-discounted" && $priceDetails['discount'] == 0){
                    $couponCartItems[$ckey]['cart_id']   =  $cart['id'];
                    $couponCartItems[$ckey]['qty']       =  $cart['qty'];
                    $couponCartItems[$ckey]['subtotal'] =  $priceDetails['subtotal'];
                    $couponCartItems[$ckey]['unit_price'] =  $priceDetails['price'];
                    $couponPriceArr[] =  $priceDetails['subtotal'];
                }
            }
        }
        $subtotal = array_sum($priceArr);
        $couponPriceSubtotal = round(array_sum($couponPriceArr));
        $discount =0;
        $gstArr = Cart::CalculateTotalGST($cartitems);
        $gst = $gstArr['totalTax'];
        $grandtotal = round($subtotal,2);
        $couponcode = '';
        if(Session::has('couponinfo')){
            $couponcode = Session::get('couponinfo')->code;
            $discount = Coupon::getCouponAmount(Session::get('couponinfo'),$couponPriceSubtotal);
        }
        $totalAfterDis = round($subtotal - $discount);
        $shipping = Cart::CalculateShipping($totalAfterDis);
        $cod_charges = Cart::CalculateCODCharges($totalAfterDis);
        /*$shipping = round($shipping);*/
        //$grandtotal = round(($subtotal - $discount + $shipping + $cod_charges + $gst),2);
        $grandtotal = round(($subtotal - $discount + $shipping + $cod_charges),2);
        $summaryArr['subtotalString'] =round($subtotal,2);
        $summaryArr['subtotal'] =round($subtotal,2);
        $summaryArr['discountString'] =round($discount,2);
        $summaryArr['discount'] =round($discount,2);
        $summaryArr['shippingString'] =round($shipping,2);
        $summaryArr['shipping'] =round($shipping,2);
        $summaryArr['CODChargesString'] =round($cod_charges,2);
        $summaryArr['cod_charges'] =round($cod_charges,2);
        $summaryArr['gstString'] =round($gst,2);
        $summaryArr['gst'] =round($gst,2);
        $summaryArr['grandtotalString'] =round($grandtotal,2);
        $summaryArr['grandtotal'] =round($grandtotal,2);
        return $summaryArr;
    }

    public static function cartdetailsOld1($cartitems) {
        $subtotal = 0;
        $totalGST = 0;
        $productGST = [];

        foreach ($cartitems as $item) {
            $priceArr = Product::getProductAttrPrice($item['product_id'], $item['size']);
            $product_price = $priceArr['final_price'] * $item['qty'];

            // Get GST for the product
            $getGST = Product::select('product_gst')->where('id', $item['product_id'])->first();
            $gst_percent = $getGST ? $getGST->product_gst : 0;
            $gst_amount = round($product_price * $gst_percent / 100, 2);

            $subtotal += $product_price;
            $totalGST += $gst_amount;
            $productGST[] = $gst_amount;
        }

        $grandtotalCOD = $subtotal + $totalGST;

        // 5% Discount for Prepaid Orders
        $prepaidDiscount = round($subtotal * 5 / 100, 2);
        $subtotalPrepaid = $subtotal - $prepaidDiscount;
        
        // GST is recalculated on the new subtotal for Prepaid
        $totalGSTPrepaid = round($subtotalPrepaid * ($totalGST / $subtotal), 2);
        $grandtotalPrepaid = $subtotalPrepaid + $totalGSTPrepaid;

        return [
            "subtotalString" => $subtotal,
            "subtotal" => $subtotal,
            "discountString" => 0.0,
            "discount" => 0.0,
            "shippingString" => 0.0,
            "shipping" => 0.0,
            "CODChargesString" => 0.0,
            "cod_charges" => 0.0,
            "gstString" => $totalGST,
            "gst" => $totalGST,
            "gstCOD" => $totalGST, // GST for COD
            "gstPrepaid" => $totalGSTPrepaid, // GST for Prepaid
            "grandtotalString" => $grandtotalCOD,
            "grandtotal" => $grandtotalCOD, // Default is COD
            "grandtotalCOD" => $grandtotalCOD,
            "grandtotalPrepaid" => $grandtotalPrepaid,
            "prepaidDiscount" => $prepaidDiscount // Displayed for clarity
        ];
    }

    public static function cartdetailsOld2($cartitems) {
        $subtotal = 0;
        $totalGST = 0;
        $productGST = [];

        foreach ($cartitems as $item) {
            $priceArr = Product::getProductAttrPrice($item['product_id'], $item['size']);
            $product_price = $priceArr['final_price'] * $item['qty'];

            // Get GST for the product (Only for Display)
            $getGST = Product::select('product_gst')->where('id', $item['product_id'])->first();
            $gst_percent = $getGST ? $getGST->product_gst : 0;
            $gst_amount = round($product_price * $gst_percent / 100, 2);

            $subtotal += $product_price;
            $totalGST += $gst_amount;
            $productGST[] = $gst_amount;
        }

        // 5% Discount for Prepaid Orders
        $prepaidDiscount = round($subtotal * 5 / 100, 2);
        $subtotalPrepaid = $subtotal - $prepaidDiscount;

        return [
            "subtotalString" => $subtotal,
            "subtotal" => $subtotal,
            "discountString" => 0.0,
            "discount" => 0.0,
            "shippingString" => 0.0,
            "shipping" => 0.0,
            "CODChargesString" => 0.0,
            "cod_charges" => 0.0,
            "gstString" => $totalGST, // GST is only for display
            "gst" => $totalGST, // Display GST only, do not add to Grand Total
            "gstCOD" => $totalGST, // Same for COD
            "gstPrepaid" => $totalGST, // Same for Prepaid
            "grandtotalString" => $subtotal, // No GST addition
            "grandtotal" => $subtotal, // Default Grand Total (no GST added)
            "grandtotalCOD" => $subtotal, // COD Grand Total (no GST added)
            "grandtotalPrepaid" => $subtotalPrepaid, // Prepaid Grand Total (discount applied)
            "prepaidDiscount" => $prepaidDiscount // Displayed for clarity
        ];
    }

    public static function cartdetailsOld3($cartitems) {
        $subtotal = 0;
        $totalGST = 0;
        $productGST = [];

        foreach ($cartitems as $item) {
            $priceArr = Product::getProductAttrPrice($item['product_id'], $item['size']);
            $product_price = $priceArr['final_price'] * $item['qty'];

            // Get GST for the product (Only for Display)
            $getGST = Product::select('product_gst')->where('id', $item['product_id'])->first();
            $gst_percent = $getGST ? $getGST->product_gst : 0;
            $gst_amount = round($product_price * $gst_percent / 100, 2);

            $subtotal += $product_price;
            $totalGST += $gst_amount;
            $productGST[] = $gst_amount;
        }

        // 5% Discount for Prepaid Orders
        $prepaidDiscount = round($subtotal * 5 / 100, 2);
        $subtotalPrepaid = $subtotal - $prepaidDiscount;

        // Shipping Charges Calculation
        $shippingCOD = ($subtotal < 1000) ? 70 : 0;
        $shippingPrepaid = ($subtotalPrepaid < 1000) ? 70 : 0;

        return [
            "subtotalString" => $subtotal,
            "subtotal" => $subtotal,
            "discountString" => 0.0,
            "discount" => 0.0,
            "shippingString" => $shippingCOD,
            "shipping" => $shippingCOD, // Default is COD shipping
            "CODChargesString" => 0.0,
            "cod_charges" => 0.0,
            "gstString" => $totalGST, // GST is only for display
            "gst" => $totalGST, // Display GST only, do not add to Grand Total
            "gstCOD" => $totalGST, // Same for COD
            "gstPrepaid" => $totalGST, // Same for Prepaid
            "grandtotalString" => $subtotal + $shippingCOD, // No GST addition
            "grandtotal" => $subtotal + $shippingCOD, // Default Grand Total (COD)
            "grandtotalCOD" => $subtotal + $shippingCOD, // COD Grand Total
            "grandtotalPrepaid" => $subtotalPrepaid + $shippingPrepaid, // Prepaid Grand Total
            "prepaidDiscount" => $prepaidDiscount, // Displayed for clarity
            "shippingPrepaid" => $shippingPrepaid // Prepaid Shipping Charges
        ];
    }


    public static function cartdetails($cartitems) { 
        $subtotal = 0;
        $totalGST = 0;
        $productGST = [];
        $couponPriceArr = [];

        foreach ($cartitems as $item) { 
            $priceArr = Product::getProductAttrPrice($item['product_id'], $item['size']);
            $product_price = $priceArr['final_price'] * $item['qty'];

            // Get GST for the product (Only for Display)
            $getGST = Product::select('product_gst')->where('id', $item['product_id'])->first();
            $gst_percent = $getGST ? $getGST->product_gst : 0;
            $gst_amount = round($product_price * $gst_percent / 100, 2);

            $subtotal += $product_price;
            $totalGST += $gst_amount;
            $productGST[] = $gst_amount;

            // Collect prices for coupon calculations
            if (Session::has('couponinfo')) {
                $couponInfo = Session::get('couponinfo');
                if ($couponInfo['coupon_applicable_on'] == "all" ||
                    ($couponInfo['coupon_applicable_on'] == "discounted" && $priceArr['discount'] > 0) ||
                    ($couponInfo['coupon_applicable_on'] == "non-discounted" && $priceArr['discount'] == 0)) {
                    $couponPriceArr[] = $product_price;
                }
            }
        }

        // Calculate Coupon Discount
        $discount = 0;
        if (Session::has('couponinfo')) {
            $couponPriceSubtotal = round(array_sum($couponPriceArr));
            $discount = Coupon::getCouponAmount(Session::get('couponinfo'), $couponPriceSubtotal);
        }

        // Prepaid Discount (5%)
        $prepaidDiscount = round(($subtotal - $discount) * 5 / 100, 2);
        $subtotalPrepaid = $subtotal - $discount - $prepaidDiscount;

        // Dynamic Shipping Charges
        $shippingCOD = ($subtotal < 1000) ? 70 : 0;  
        $shippingPrepaid = ($subtotalPrepaid < 1000) ? 70 : 0;

        $shippingCOD = $shippingPrepaid = 0;

        return [
            "subtotalString" => $subtotal,
            "subtotal" => $subtotal,
            "discountString" => $discount,
            "discount" => $discount,
            "shippingString" => $shippingCOD,
            "shipping" => $shippingCOD, // Default COD shipping
            "CODChargesString" => 0.0,
            "cod_charges" => 0.0,
            "gstString" => $totalGST,
            "gst" => $totalGST, 
            "gstCOD" => $totalGST, 
            "gstPrepaid" => $totalGST, 
            "grandtotalString" => $subtotal-$discount+$shippingCOD-Session::get('order_credit'), 
            "grandtotal" => $subtotal - $discount + $shippingCOD - Session::get('order_credit'), 
            "grandtotalCOD" => $subtotal - $discount + $shippingCOD - Session::get('order_credit'), 
            "grandtotalPrepaid" => $subtotalPrepaid+$shippingPrepaid-Session::get('order_credit'), 
            "prepaidDiscount" => $prepaidDiscount, 
            "shippingPrepaid" => $shippingPrepaid
        ];
    }


}
