<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Category;
use Session;
use DB;
use Auth;

class Coupon extends Model
{
    use HasFactory;

    public static function applycredit($code){
        if(Auth::check()){
            Session::forget('couponinfo');
            Session::forget('order_credit');
            if($code>0){
                $creditDetails = User::select('credit')->where(['id' => Auth::user()->id])->first();
                if($creditDetails->credit>0){
                    $cartitems = Cart::cartitems();
                    $priceArr = array();$totalitems =0;
                    foreach($cartitems as $ckey=> $cart){
                        /*echo "<pre>"; print_r($cart); die;*/
                        $totalitems += $cart['qty'];
                        /*echo "<pre>"; print_r($cart); die;*/
                        $priceDetails = Cart::calProPricing($cart);
                        /*echo "<pre>"; print_r($priceDetails); die;*/              
                        $priceArr[] =  $priceDetails['subtotal'];
                    }
                    $producttotal = array_sum($priceArr);
                    if($code<=$creditDetails->credit){
                        if(round($producttotal)>$code){
                            Session::put('order_credit',$code);
                            $response = array('status'=> true,'message' =>'Credit Applied successfully!');
                        }else{
                            $response = array('status'=> false,'message'=>"Please add more products to avail that much Credit!");   
                        }
                    }else{
                        $response = array('status'=> false,'message'=>"You do not have enough Credit!");    
                    }
                }else{
                    $response = array('status'=> false,'message'=>"You don't have any Credits in your Account");    
                }    
            }else{
                $response = array('status'=> false,'message'=>"Please add some positive credit amount!");
            }
        }else{
            $response = array('status'=> false,'message'=>'Please login to apply Credits');
        }
        return $response;
    }

    public static function applycouponcode($code){
        /*if(Auth::check()){*/
            Session::forget('order_credit');
            $cartitems = Cart::cartitems();
            $response = array('status'=> false,'message'=>'Invalid coupon code. Please try with some vaild coupon code.'); 
            if(Auth::check()){
                $checkcoupon =Coupon::where(['coupon_code'=>$code,'status'=>1])
                            ->where('expiry_date','>=',date('Y-m-d'))
                            ->where('start_date','<=',date('Y-m-d'))  
                            /*->where('max_qty','>=',$totalitems)
                            ->where('max_amount','>=',$producttotal)*/
                            ->where(function ($q) {
                                $q->whereRaw('FIND_IN_SET("'.Auth::user()->email.'",users)')->orwhere('users','=','');
                            })
                            ->first();
            }else{
                $checkcoupon =Coupon::where(['coupon_code'=>$code,'status'=>1])
                            ->where('expiry_date','>=',date('Y-m-d'))
							->where('start_date','<=',date('Y-m-d'))
                            ->first();    
            }
            /*echo "<pre>"; print_r($checkcoupon); die; */
            if($checkcoupon){
                
				$check_used_coupon = Order::where(['coupon_code'=>$code,'user_id'=>Auth::user()->id])->count();
				if(empty($check_used_coupon)){
				$priceArr = array();$totalitems =0;$catids =array();
                foreach($cartitems['items'] as $ckey=> $cart){
                    /*echo "<pre>"; print_r($cart); die;*/
                    $catids[] = $cart['product']['category_id'];
                    $brandids[] = $cart['product']['brand_id'];
                    $totalitems += $cart['qty'];
                    /*echo "<pre>"; print_r($cart); die;*/
                    $priceDetails = Cart::calProPricing($cart);
                    /*echo "<pre>"; print_r($priceDetails); die;*/
                    if($checkcoupon->coupon_applicable_on =="all"){               
                        $priceArr[] =  $priceDetails['subtotal'];
                    }elseif($checkcoupon->coupon_applicable_on =="discounted" && $priceDetails['discount'] >0){ 
                        $priceArr[] =  $priceDetails['subtotal'];
                    }elseif($checkcoupon->coupon_applicable_on =="non-discounted" && $priceDetails['discount'] == 0){ 
                        $priceArr[] =  $priceDetails['subtotal'];
                    }
                }
                $catids = array_unique($catids);
                $brandids = array_unique($brandids);
                $producttotal = array_sum($priceArr);
                /*if($checkcoupon->max_amount>$producttotal){
                    Session::forget('couponinfo');
                    $response = array('status'=> false,'message'=>'Please remove products to apply this Coupon');
                    break();    
                }*/
                if($checkcoupon->categories ==""){
                    $explodeCats = Category::pluck('id')->toArray();
                }else{
                    $explodeCats = explode(',',$checkcoupon->categories);
                }
                if($checkcoupon->brands ==""){
                    $explodeBrands = Brand::pluck('id')->toArray();
                }else{
                    $explodeBrands = explode(',',$checkcoupon->brands);
                }

                if(count(array_intersect($explodeCats, $catids)) == count($catids)){
                    if($checkcoupon->min_qty <= $totalitems){
						if($checkcoupon->max_qty >= $totalitems){
							if(round($checkcoupon->min_amount) <= $producttotal){
								$minusCouponAmount = Coupon::getCouponAmount($checkcoupon,$producttotal);
								if($producttotal > $minusCouponAmount ){
									if(Auth::check()){
										if($checkcoupon->coupon_type =="Single Times" || $checkcoupon->coupon_type =="Single Time"){
											/*echo "<pre>"; print_r($checkcoupon); die;*/
											$checkCouponUsed=0;
											//Later will used for orders
												$checkCouponUsed = DB::table('orders')->where('user_id',Auth::user()->id)->where('coupon_code',$checkcoupon->coupon_code)->wherein('payment_status',['cod','captured','COD','Payment Captured'])->count();
											if($checkCouponUsed==0){
												Session::put('couponinfo',$checkcoupon);
												$response = array('status'=> true,'message' =>'Coupon Applied successfully!');
											}else{
												Session::forget('couponinfo');
											}
										}else{
											Session::put('couponinfo',$checkcoupon);
											$response = array('status'=> true,'message'=>' Coupon Applied successfully!');
										}
									}else{
										if($checkcoupon['users']!=""){
											Session::forget('couponinfo');
											$response = array('status'=> false,'message'=>'This coupon is not valid');    
										}else if($checkcoupon->coupon_type =="Single Times" || $checkcoupon->coupon_type =="Single Time"){
											Session::forget('couponinfo');
											$response = array('status'=> false,'message'=>'This coupon is not valid');
										}else{
											Session::put('couponinfo',$checkcoupon);
											$response = array('status'=> true,'message'=>' Coupon Applied successfully!');
										}   
									}
								}else{
									Session::forget('couponinfo');
									$response = array('status'=> false,'message'=>' Please add more products in cart to avail this coupon');
								}
								if($checkcoupon->max_amount<$producttotal){
									Session::forget('couponinfo');
									$response = array('status'=> false,'message'=>' Shop for $'.$checkcoupon->max_amount.' or less to avail this coupon.');  
								}
							}else{
								Session::forget('couponinfo');
								if($checkcoupon->coupon_applicable_on =="all"){
									$response = array('status'=> false,'message'=>' Shop for $'.$checkcoupon->min_amount.' or above to avail this coupon.');
								}elseif($checkcoupon->coupon_applicable_on =="non-discounted"){
									$response = array('status'=> false,'message'=>'Avail this coupon for Non discounted products of Rs '.$checkcoupon->min_amount ." or above");   
								}else{
									$response = array('status'=> false,'message'=>'Avail this coupon for discounted products of Rs '.$checkcoupon->min_amount ." or above");   
								}
							}
							}else{
								Session::forget('couponinfo');
								if($checkcoupon->coupon_applicable_on =="all"){
									$response = array('status'=> false,'message'=>'This coupon is valid only when purchasing a maximum of '.$checkcoupon->max_qty.' product.');
								}elseif($checkcoupon->coupon_applicable_on =="non-discounted"){
									$response = array('status'=> false,'message'=>'This coupon is valid only when purchasing a maximum of '.$checkcoupon->max_qty.' product.');  
								}else{
									$response = array('status'=> false,'message'=>'This coupon is valid only when purchasing a maximum of '.$checkcoupon->max_qty.' product.');
								}
						   }
                    }else{
                        Session::forget('couponinfo');
                        if($checkcoupon->coupon_applicable_on =="all"){
                            $response = array('status'=> false,'message'=>'Shop for '.$checkcoupon->min_qty ." or more products to avail this coupon");
                        }elseif($checkcoupon->coupon_applicable_on =="non-discounted"){
                            $response = array('status'=> false,'message'=>'Shop  Non discounted roducts for '.$checkcoupon->min_qty ." or more products to avail this coupon");  
                        }else{
                            $response = array('status'=> false,'message'=>'Shop for '.$checkcoupon->min_qty ." or more products to avail this coupon");
                        }
                    }
                }else{
                    Session::forget('couponinfo');
                    $response = array('status'=> false,'message'=>'This coupon is not valid for products in cart');
                }
			}else{
                Session::forget('couponinfo');
				$response = array('status'=> false,'message'=>'This coupon is already availed by you');
            }
            }else{
                Session::forget('couponinfo');
            }
        /*}else{
            $response = array('status'=> false,'message'=>'Please login to apply Coupon');    
        }*/
        return $response;
    }

    public static function getCouponAmount($checkcoupon,$producttotal){
        $currencyinfo = currencyinfo();
        if($checkcoupon['amount_type'] == "Fixed") {
            $minusCouponAmount = $checkcoupon['amount'];
            $minusCouponAmount = getPrice($currencyinfo,$minusCouponAmount);
        }else{
            $minusCouponAmount = ($producttotal * $checkcoupon['amount'])/100;
        }
        return round($minusCouponAmount,2);
    }

    public static function checkCouponStatus(){
        if(Session::has('couponinfo')){
           
			$checkstatus = Coupon::where('coupon_code',Session::get('couponinfo')['coupon_code'])->where('expiry_date','>=',date('Y-m-d'))
			                ->where('start_date','<=',date('Y-m-d'))
                            ->where('status',1)
                            ->first();
							
			if(empty($checkstatus)){
                Session::forget('couponinfo');
            }
           
        }
        return true;
    }

    public static function availableCoupons($cartitems){
        $priceArr = array();$totalitems =0;$catids =array();
        /*echo "<pre>"; print_r($cartitems); die;*/
        foreach($cartitems['items'] as $ckey=> $cart){
            $catids[] = $cart['product']['category_id'];
            $totalitems += $cart['qty'];
            $currencyinfo = currencyinfo();
            $priceDetails = Cart::calProPricing($cart);
            $priceArr[] =  $priceDetails['subtotal'];
        }
        $catids = array_unique($catids);
        $producttotal = array_sum($priceArr);
       /* $coupons = Coupon::where('expiry_date','>=',date('Y-m-d'))
                            ->where('min_qty','<=',$totalitems)
                            ->where('max_qty','>=',$totalitems)
                            ->where('min_amount','<=',$producttotal)
                            ->where('max_amount','>=',$producttotal)
                            ->where('status',1)
                            ->where('visible',1)
                            ->where(function ($q) {
                                $q->whereRaw('FIND_IN_SET("'.Auth::user()->email.'",users)')->orwhere('users','=','');
                            })
                            ->get()
                            ->toArray(); */
        if(Auth::check()){                    
		    $coupons = Coupon::where('expiry_date','>=',date('Y-m-d'))
			                ->where('start_date','<=',date('Y-m-d')) 
                            ->where('status',1)
                            ->where('visible',1)
                            ->where(function ($q) {
                                $q->whereRaw('FIND_IN_SET("'.Auth::user()->email.'",users)')->orwhere('users','=','');
                            })
                            ->get()
                            ->toArray();
        }else{
            $coupons = Coupon::where('expiry_date','>=',date('Y-m-d'))
			                ->where('start_date','<=',date('Y-m-d'))
                            ->where('status',1)
                            ->where('visible',1)
                            ->get()
                            ->toArray();
        }                    
        $availableCoupons = array();
        //this below code is not as much good we can do it in more better way later
        foreach($coupons as $ckey=> $coupon){
            //Check coupon used in orders
            if(Auth::check() && $coupon['coupon_type'] =='Single Time'){
                $checkCouponUsed = Order::checkCouponUsed($coupon['code']);
                if($checkCouponUsed == 0){
                    $availableCoupons[$ckey] = $coupon;
                }
            }else{
                $availableCoupons[$ckey] = $coupon;
            }
        }
        //echo "<pre>"; print_r($availableCoupons); die;
        return $availableCoupons;
    }
}
