<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Cart;
use App\Models\State;
use App\Models\Order;
use App\Models\OrdersHistory;
use App\Models\Wishlist;
use App\Models\Mails;
use App\Models\WebSetting;
use Validator;
use Session;
use Hash;
use Auth;
use DB;
use Str;
use Illuminate\Support\Facades\Cache;
use App\Services\Front\SmsService;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{

    protected $smsService;

    public function __construct(SmsService $smsService){
        $this->smsService = $smsService;
    }

    public function showLoginForm() {
        
		if(Auth::check()){
			return Redirect('/');
		}else{
			$page = "login";
			$meta_title = "";
			$meta_keyword = "";
			$meta_description = "";
			return view('front.pages.account.login')->with(compact('page','meta_title','meta_keyword','meta_description'));
		}
	}

   
    public function login(Request $request){
		
		if($request->ajax()){
            
			 $data = $request->all(); 
			 $action = $data['action']; 
			if($action == ''){
				$validator = Validator::make($request->all(), [
						'email' => 'bail|required|email|regex:/^\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b$/i|exists:users,email',
					    'password'=>'bail|required',
					],
					[
						'email.required'=>'Enter the email address',
						'email.regex'=>'This email is not a valid email address',
						'email.exists'=>'This email address isn’t registered yet. Please sign up!',
						'password.required'=>'Enter the password'
					]
				);
			}else if($action == 'sent_otp'){
				$validator = Validator::make($request->all(), [
						'email' => 'bail|required|email|regex:/^\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b$/i|exists:users,email',
					],
					[
						'email.required'=>'Enter the email address',
						'email.regex'=>'This email is not a valid email address',
						'email.exists'=>'This email address isn’t registered yet. Please sign up!'
					]
				);
			}else if($action == 'verify_otp'){
				$validator = Validator::make($request->all(), [
						'email' => 'bail|required|email|regex:/^\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b$/i|exists:users,email',
						'otp' => 'bail|required',
					],
					[
						'email.required'=>'Enter the email address',
						'email.regex'=>'This email is not a valid email address',
						'email.exists'=>'This email address isn’t registered yet. Please sign up!',
						'otp.required'=>'Enter the otp',
					]
				);
			}
			if($validator->passes()) {
				if($action == 'sent_otp'){
					
					$user = User::where('email',$data['email'])->where('status','1')->first();
					if(empty($user)){
					   return response()->json(['status'=>false,'type'=>'validation','errors'=>['email'=>"Your account is inactive. Please contact support team."]]);
					}
					
					if(env('MAIL_MODE') == 'live'){
						$otp  = rand(111111, 999999);
						Mails::otpmail($data['email'],$otp);
					}else{
					    $otp = '123456';
					}
					
					Cache::put('otp_' . $data['email'], $otp, now()->addMinutes(5));
					
					$message = 'Otp sent to '.$data['email'];
					return response()->json(['status'=>true,'action'=>$action, 'message'=>$message]);
				}else if($action == 'verify_otp'){
					$user = User::where('email',$data['email'])->first();
					$storedOtp = Cache::get('otp_' . $data['email']);
					if ($storedOtp && $storedOtp == $data['otp']) {
						Auth::login($user);
						$this->updatingCartSessionToUser();
						
						if(Session::has('previousurl')){
							$redirectTo = url(Session::get('previousurl'));
							Session::forget('previousurl');
						}else{
							if(isset($data['redirect']) && !empty($data['redirect'])){
								$redirectTo = route('home');
							}else{
								$redirectTo = route('home');
							}
					    }
						
						return response()->json(['status'=>true,'action'=>'done','message'=>'Login successfully. It will automatically redirected','url'=>$redirectTo]);
					}else{
						return response()->json(['status'=>false,'type'=>'validation','errors'=>['otp'=>"You have entered wrong otp!"]]);
					}
					
					
				}else if($action == ''){
					if (Auth::attempt(['email' => $data['email'], 'password' => $data['password']])) {
						$this->updatingCartSessionToUser();
						
						if(Session::has('previousurl')){
							$redirectTo = url(Session::get('previousurl'));
							Session::forget('previousurl');
						}else{
							if(isset($data['redirect']) && !empty($data['redirect'])){
								$redirectTo = route('home');
							}else{
								$redirectTo = route('home');
							}
					    }
						
						return response()->json(['status'=>true,'action'=>'done','message'=>'Login successfully. It will automatically redirected','url'=>$redirectTo]);
					}else{
						return response()->json(['status' => false, 'type' => 'normal', 'errors' => 'Invalid email or password']);
					}
					
					
				}
				
			}else{
                return response()->json(['status'=>false,'type'=>'validation','errors'=>$validator->messages()]);
            }
			
			
			
			
			
		}
		
		
	}
    
	
    public function register(Request $request){
        if($request->ajax()){
			$data = $request->all();
			$action = $data['action'];
			if($action == 'send_otp'){
					$validator = Validator::make($request->all(), [
							'email' => 'required|string|regex:/^\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b$/i|max:255|unique:users',
						],
						[
							'email.regex' => 'This email is not a valid email address',
							'email.unique' => 'This email address is already registered', 
						]
					);
					
			}else{
				$validator = Validator::make($request->all(), [			
							'mobile' => 'required|numeric|digits:10|unique:users',
							'email' => 'required|string|regex:/^\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b$/i|max:255|unique:users',
						],
						[
							'email.regex' => 'This email is not a valid email address',
							'email.unique' => 'This email address is already registered', 
							'mobile.unique' => 'This mobile number is already registered' 
						]
					);
			}
			

            if($validator->passes()) {
                $data = $request->all();
				  if($action == 'send_otp'){
					if(env('MAIL_MODE') == 'live'){
						$otp = $otp = rand(111111, 999999);
						Mails::otpmail($data['email'],$otp);
					}else{
					    $otp = '123456';
					}
					Cache::put('otp_' . $data['email'], $otp, now()->addMinutes(5));
					$message = 'Otp sent to '.$data['email'];
					return response()->json(['status'=>true,'action'=>$action, 'message'=>$message]);
				}else{

				    $storedOtp = Cache::get('otp_' . $data['email']);
					if (!empty($storedOtp) && $storedOtp == $data['otp']) {

						$user_data['email'] = $data['email'];
						$user_data['mobile'] = $data['mobile'];
						$user_data['country'] = 'India';
						$user_data['status'] = 1;
						$plainPassword = Str::random(10);
						$user_data['password'] = Hash::make($plainPassword);
						$user_data['ip_address'] = $_SERVER['REMOTE_ADDR'];
						User::create($user_data);
						$registerUserId = DB::getPdo()->lastInsertId();
						if(Auth::guard('web')->attempt(['email' => $request->email, 'password' => $plainPassword])){
							$this->updatingCartSessionToUser();
							if(env('MAIL_MODE') =="live"){
								Mails::RegistrationMail();
							}

						   /*
							$mobile = $request->mobile;
							$smsMessage = "Welcome to ON-VERS We're excited to have you - enjoy exclusive deals and a seamless shopping experience. Start exploring now at https://onvers.com/";
							$this->smsService->sendSms($mobile, $smsMessage);
							 */
							
							if(Session::has('previousurl')){
							    $redirectTo = url(Session::get('previousurl'));
							    Session::forget('previousurl');
							}else{
								$redirectTo = route('account',['profile']);
							}
							
							$message = "Dear Customer, you have been successfully registered with ".config('constants.project_name').".It will be redirected in <b></b> seconds.";
						    return response()->json(['status' => true, 'action'=>'done', 'type' => 'success', 'message' => array($message), 'redirectTo' => $redirectTo]);	
						}
						     
							}else{
								return response()->json(['status'=>false,'type'=>'validation','errors'=>['otp'=>"You have entered wrong otp!"]]);
							}
							
						}
			
			
			} else {
                return response()->json(['status' => false, 'type' => 'validation', 'errors' => $validator->messages()]);
            }
        } else {
            $page = "register";
            return view('front.pages.account.register')->with(compact('page'));
        }
    }

    public function verifyOtp(Request $request) {
        $request->validate([
            'mobile' => 'required|digits:10',
            'otp' => 'required|digits:6'
        ]);

        // ✅ Correct key format while fetching from cache
        $cachedOtp = Cache::get('otp_' . $request->mobile);

        \Log::info('Cached OTP: ' . $cachedOtp);
        \Log::info('Entered OTP: ' . $request->otp);

        if ($cachedOtp && $cachedOtp == $request->otp) {
            Cache::forget('otp_' . $request->mobile); // ✅ Remove OTP after successful verification
            return response()->json(['status' => true, 'message' => 'OTP verified successfully']);
        } else {
            return response()->json(['status' => false, 'error' => 'Invalid OTP']);
        }
    }

    public function checkEmail(Request $request) {
        $exists = User::where('email', $request->email)->exists();
        if ($exists) {
            return response()->json(['status' => false, 'message' => 'This email is already registered']);
        }
        return response()->json(['status' => true]);
    }

    public function checkMobile(Request $request) {
        $exists = User::where('mobile', $request->mobile)->exists();
        if ($exists) {
            return response()->json(['status' => false, 'message' => 'This mobile number is already registered']);
        }
        return response()->json(['status' => true]);
    }

    public function checkMobileExists(Request $request)
    {
        $request->validate([
            'mobile' => 'required|digits:10',
        ]);

        $exists = User::where('mobile', $request->mobile)->exists();

        if ($exists) {
            return response()->json(['exists' => true]);
        } else {
            return response()->json(['exists' => false]); // ✅ Correct response for non-existing number
        }
    }


    public function updatingCartSessionToUser(){
        if(Session::has('cartsessionId')){
            Cart::where('session_id',Session::get('cartsessionId'))->update(['user_id'=>Auth::user()->id,'session_id'=>'']);
            DB::select("DELETE FROM carts WHERE id NOT IN (SELECT * FROM (SELECT MAX(n.id) FROM carts n GROUP BY n.product_id,n.size) x) and user_id = ".Auth::user()->id."");
        }
        $mesage = "success";
        return $mesage;
    }

    
	public function account($slug=null,request $request){
		$accountSlugs = array('profile','address','orders','settings','wishlist');
		if(in_array($slug,$accountSlugs)){
			$data['slug'] = $slug;
			if($slug == "profile"){
				$user = User::find(Auth::user()->id);
				$states = State::orderby('name','ASC')->pluck('name')->toArray();
				return view('front.pages.account.profile')->with(compact('data','user','states'));
				
			}else if($slug == "address"){
				return view('front.pages.account.address')->with(compact('data'));
			}else if($slug == "orders"){
				   
				   if(isset($_GET['id']) && !empty($_GET['id'])){
						$title ="Order Details";
						$orderDetails = Order::withCount(['order_products as total_items'=>function($query){
							$query->select(DB::raw('sum(product_qty)'));
						}])->with(['order_products','order_address','histories'])->where(['user_id'=>Auth::user()->id,'id'=>$_GET['id']])->first();
						if(!$orderDetails){
							$orderDetails = json_decode(json_encode($orderDetails),true);
							return redirect()->route('account',['orders']);
						}else{
							return view('front.pages.account.order_details')->with(compact('data','orderDetails'));    
						}
                    }
				   
				   $orders = Order::withCount(['order_products as total_items'=>function($query){
                        $query->select(DB::raw('sum(product_qty)'));
                    }])->where('user_id',Auth::user()->id)->orderby('id','DESC')->get(); 
				return view('front.pages.account.orders')->with(compact('data','orders'));
			}else if($slug == "settings"){
				return view('front.pages.account.settings')->with(compact('data'));
			}else if($slug == "wishlist"){
				$wishlists =Wishlist::wishlists();
				$wishlists = json_decode(json_encode($wishlists),true); 
				return view('front.pages.account.wishlist')->with(compact('data','wishlists'));
			}
		}else{
			abort(404);
		}
	}
	
	
    public function saveAccount(Request $request){
        if($request->ajax()){
           
			$data = $request->all();
            /*echo "<pre>"; print_r($data); die;*/
            $validator = Validator::make($request->all(), [
                    'first_name' => 'required|string|max:100',
                    'last_name' => 'required|string|max:100',
					'address' => 'required|string|max:150',
					'pincode' => 'required',
                    'city' => 'required|string|max:100',
                    'state' => 'required|string',
                ]
            );

            if($validator->passes()){

                $name = $data['first_name']." ".$data['last_name'];

                User::where('id',Auth::user()->id)->update(['name'=>$name,'first_name'=>$data['first_name'],'last_name'=>$data['last_name'],'city'=>$data['city'],'state'=>$data['state'],'pincode'=>$data['pincode'],'address'=>$data['address'],'address_line2'=>$data['address_line2']]);

                return response()->json(['status'=>true,'message'=>array('Your profile have been updated successfully!')]);

            }else{
                return response()->json(['status'=>false,'errors'=>$validator->messages()]);
            }
        }
    }

    public function userUpdatePassword(Request $request){
        if($request->ajax()){
            $data = $request->all();
			$validator = Validator::make($request->all(), [
					'current_password' => 'required',
					'new_password' => 'required|min:6',
					'confirm_password' => 'required|min:6|same:new_password'
			]);
            if($validator->passes()){
				 $current_password = $data['current_password'];
				 $checkPassword = User::where('id',Auth::user()->id)->first();
				 $check_current_password = Hash::check($current_password,$checkPassword->password);
                if($check_current_password){
                    $user = User::find(Auth::user()->id);
                    $user->password = bcrypt($data['new_password']);
                    $user->save();
                    return response()->json(['status'=>true,'message'=>array('Account password successfully updated!')]);
                }else{
                    return response()->json(['status'=>false,'errors'=>array('current_password'=>['Your current password is incorrect!'])]);    
                }
            }else{
                return response()->json(['status'=>false,'errors'=>$validator->messages()]);
            }
        }
    }

    public function forgotPassword(Request $request){
        if($request->ajax()){
            $data = $request->all();
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email|max:255',
            ]);
            if ($validator->passes()) {
                $userDetails = User::where('email',$data['email'])->first();
                if($userDetails){
                    
					
				
					if(!empty($userDetails->status)){
					   
						$user = User::find($userDetails->id);
						if(env('MAIL_MODE')=="live"){
							$password = Str::random(6);
						}else{
							$password = "123456";
						}
						$user->password  = bcrypt($password);
						$user->save();
						if(env('MAIL_MODE') =="live"){
							$email = $userDetails->email;
							Mails::ResetPasswordMail($email,$password);
						}
						$messages = array('New password sent to your registered email!');
						return response()->json(['status'=>true,'message'=>$messages]);
					}else{
						
						 return response()->json(['status'=>false,'type'=>'validation','errors'=>['email'=>"Your account is inactive. Please contact support team."]]);
					}
                }else{
                    $errors = array('email'=>'This email does not exists!');
                    return response()->json(['status'=>false,'type'=>'validation','errors'=>$errors]);
                }
            }else{
                return response()->json(['status'=>false,'type'=>'validation','errors'=>$validator->messages()]);
            }
        }else{
            return view('front.users.forgot_password');
        }
    }

    public function orderDetails(){
        return view('front.users.order_details');
    }

	public function orderinvoice ($id){
        $orderDetails = Order::withCount(['order_products as total_items'=>function($query){
                $query->select(DB::raw('sum(product_qty)'));
            }])->with(['order_address','getuser','order_products'])->where('user_id',Auth::user()->id)->where('id',$id)->firstorFail();
        $orderDetails = json_decode(json_encode($orderDetails),true);
        $title="Invoice";
        $numberWords =  convert_number_to_words(round($orderDetails['grand_total'],2));
        $web_settings = WebSetting::find(1);
		return view('admin.orders.order_invoice')->with(compact('title','orderDetails','numberWords','web_settings'));
    }

    public function logout(){
        Session::forget('previousurl');
        Session::forget('couponinfo');
        Auth::logout();
        return redirect('/');
    }

    public function guestCheckout(Request $request){
        if($request->ajax()){
            $data = $request->all();
            $validator = Validator::make($request->all(), [
                /*'mobile'=>'bail|required|numeric|digits:10',*/
                'email' => 'required|string|regex:/^\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b$/i|max:255|unique:users',
            ]);
            if($validator->passes()){
                $checkGuestUser = User::select('id')->where(['user_type'=>'Guest','email'=>$data['email']/*,'mobile'=>$data['mobile']*/])->first();
                if($checkGuestUser){
                    $guestUserId = $checkGuestUser->id;
                }else{
                    //create Guest User
                    $lastUser = User::select('id')->orderby('id','DESC')->first();
                    $lastid = $lastUser->id +1;
                    /*$guestEmail="guest".$lastid.str_random(2)."@yopmail.com";*/
                    $password = Str::random(6);
                    $guestUser = new User;
                    $guestUser->user_type = "Guest";
                    $guestUser->name = "Guest";
                    /*$guestUser->mobile = $data['mobile'];*/
                    $guestUser->password = bcrypt($password);
                    $guestUser->email = $data['email'];
                    $guestUser->status =1;
                    $guestUser->save();
                    $guestUserId = $guestUser->id;

                   

                }
                Auth::loginUsingId($guestUserId);
                $this->updatingCartSessionToUser();
                $redirectTo = url('/order-checkout');
                return response()->json(['status'=>true,'message'=>'ok','url'=>$redirectTo]);
            }else{
                return response()->json(['status'=>false,'type'=>'validation','errors'=>$validator->messages()]);
            }
        }
    }

   
    public function getStateCity(Request $request){
        if($request->ajax()){
            $data =$request->all();
            $getdata = DB::table('cities')->where('pincode',$data['pincode'])->first();
            if($getdata){
                $state = $getdata->state;
                $city  = $getdata->city;
            }else{
                $state = "";
                $city  = "";
            }
			return response()->json(['status'=>true,'state'=>$state,'city'=>$city]);
        }
    }

    public function userWishlist(){
        $wishlistProductIds = Wishlist::where('user_id',Auth::user()->id)->pluck('product_id');
        /*dd($wishlistProductIds);*/
        $wishlistProducts = Wishlist::with(['product'=>function($query){
            $query->with(['productimages','product_image','category']);
        }])->whereIn('product_id',$wishlistProductIds)->get()->toArray();
        /*dd($wishlistProducts); die;*/
        return view('front.users.wish_list')->with(compact('wishlistProducts'));
    }

    public function removeWishlist(request $request){
         if($request->ajax()){
			$data =$request->all();
			$check = Wishlist::where(['id'=>$data['id'],'user_id'=>Auth::user()->id])->count();
			if($check>0){
				Wishlist::where(['user_id'=>Auth::user()->id,'id'=>$data['id']])->delete();
				$wishlists = Wishlist::wishlists();
				$wishlists = json_decode(json_encode($wishlists),true); 
				$html = (String)View::make('front.pages.account.wishlist-list')->with(compact('wishlists')); 
				$count = count($wishlists);
				return response()->json(['status'=>true,'html'=>$html,'count'=>$count,'message'=>array('Wishlist item has been deleted successfully')]);
			}else{
				return response()->json(['status'=>true,'message'=>array('Something went to wrong')]);
			}
        }
    }
}
