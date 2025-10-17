<?php
namespace App\Models;

use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\WebSetting;
use App\Models\Order;
use App\Models\Contact;
use App\Models\CustomFit;
use App\Models\ReturnHistory;
use Auth;

class Mails extends Model{

	public static function RegistrationMail(){
		if(env('MAIL_MODE') == 'live'){
			$websettings = WebSetting::settings(); 
			$to_emails[] = Auth::user()->email;
			$bcc_emails = $websettings['admin_bcc_emails']; 
			$userdetails = Auth::user();
			$messageData = [
				'data' => $userdetails,
				'websettings' => $websettings,
			];
            $messageData = json_decode(json_encode($messageData),true); 
			Mail::send('emails.user-register', $messageData, function($message) use ($to_emails,$bcc_emails){
				$message->to($to_emails)
				->subject('Successfully Registered with '.config('constants.project_name'))
				->bcc($bcc_emails);
			}); 
		}
	}
	
	
	public static function ResetPasswordMail($email,$password){
		if(env('MAIL_MODE') == 'live'){
			$websettings = WebSetting::settings(); 
			$userDetails = User::where('email',$email)->first();
			$messageData = [
				'data' => $userDetails,
				'websettings' => $websettings,
				'password' => $password
			];
			Mail::send('emails.forgot-user-password', $messageData, function($message) use ($email){
				$message->to($email)->subject(config('constants.project_name').' Password Changed successfully!');
			});
		}
	}
	
	public static function orderMail($order_id){
		if(env('MAIL_MODE') == 'live'){
			$orderdetails = Order::with('order_products','order_address','getuser')->where('id',$order_id)->first()->toArray();
			$websettings = WebSetting::settings();
			$to_emails[] = $orderdetails['getuser']['email'];
			$bcc_emails = $websettings['admin_bcc_emails']; 
			$messageData = [
				'orderDetails' => $orderdetails,
				'websettings' => $websettings
			]; 
			Mail::send('emails.order-success-email', $messageData, function($message) use ($to_emails,$bcc_emails){
			$message->to($to_emails)->subject('Thanks for Placing the Order with '.config('constants.project_name'))
				->bcc($bcc_emails);
			}); 
		}
	}
	
	
	public static function ReturnItemMail($order_id,$order_product_id){
		if(env('MAIL_MODE') == 'live'){
			$orderdetails = Order::with('order_products','order_address','getuser')->where('id',$order_id)->first()->toArray();
			$order_product = OrderProduct::where('id',$order_product_id)->first();
			$websettings = WebSetting::settings();
			$to_emails[] = $orderdetails['getuser']['email'];
			$bcc_emails = $websettings['admin_bcc_emails']; 
			$messageData = [
				'orderDetails' => $orderdetails,
				'order_product' => $order_product,
				'websettings' => $websettings
			];
			
			$subject = 'Return Request Received – Order #'.$order_id;
			Mail::send('emails.admin_return_request', $messageData, function($message) use ($to_emails,$bcc_emails,$subject){
			$message->to($to_emails)->subject($subject)
				->bcc($bcc_emails);
			}); 
		}
	}
	
	
	public static function ReturnItemStatusMail($order_id,$return_order_product_id){
		if(env('MAIL_MODE') == 'live'){ 
			$orderdetails = Order::with('order_products','order_address','getuser')->where('id',$order_id)->first()->toArray();
			
			$returnhistory = ReturnHistory::where('return_order_product_id',$return_order_product_id)->where('return_order_product_id',$order_id)->orderby('id','desc')->first(); 
			$order_product = OrderProduct::where('id',$returnhistory->order_product_id)->first();
			$websettings = WebSetting::settings(); 
			$to_emails[] = $orderdetails['getuser']['email'];
			$bcc_emails = $websettings['admin_bcc_emails']; 
			$messageData = [
				'orderDetails' => $orderdetails,
				'order_product' => $order_product,
				'returnhistory' => $returnhistory,
				'websettings' => $websettings
			]; 
			
			$subject = 'Return Request Received – Order #'.$order_id;
			Mail::send('emails.return_request_status', $messageData, function($message) use ($to_emails,$bcc_emails,$subject){
			$message->to($to_emails)->subject($subject)
				->bcc($bcc_emails);
			}); 
		}
	}
	
	
	
	
	
	public static function otpmail($email ='',$otp=''){
		    if(env('MAIL_MODE') == 'live'){ 
				$to_emails = $email;
				$user['otp'] = $otp;
				$websettings = WebSetting::settings(); 
				$messageData = [
					'data' => $user,
					'websettings' => $websettings
				];
				
				$subject = $websettings['site_name'].' - Your OTP Code for Account Access'; 
				Mail::send('emails.otp', $messageData, function($message) use ($to_emails,$subject){
					$message->to($to_emails)->subject($subject);
				}); 
			}
		
	}
	public static function order_status_update($data){
	if(env('MAIL_MODE') == "live"){
                    $orderDetails = Order::with(['getuser','order_products','order_address'])->where('id',$data['order_id'])->first();
                    $orderDetails = json_decode(json_encode($orderDetails),true);
                    $email = $orderDetails['getuser']['email'];
                    $websettings = WebSetting::settings(); 
					$messageData = [
                        'orderDetails' => $orderDetails,
                        'websettings' =>$websettings,
                        'order_status' =>$data['order_status'],
                        'comments' =>$data['comments'],
                    ]; 
                    if($data['order_status'] !=""){
                        Mail::send('emails.order-status-email', $messageData, function($message) use ($email,$orderDetails){
                            $message->to($email)->subject('Order Status Updated for Order #'.$orderDetails['id']);
                        });
                    }
                } 
	
	}
	
	public static function contact_mail($id){
	        if(env('MAIL_MODE') == "live"){
                  $websettings = WebSetting::settings(); 
				    $contact = Contact::where('id',$id)->first(); 
					$emails = $websettings['admin_emails']; 
					$bcc = $websettings['admin_bcc_emails'];  
					$messageData = [
						'data' => $contact,
						'websettings' => $websettings
					];
					Mail::send('emails.contact-email', $messageData, function ($message) use ($emails,$bcc) {
						$message->to($emails)->bcc($bcc)->subject('Contact Us Information Received from '.config('constants.project_name').' Date -'.date('d-m-Y'));
					});
				
                } 
	
	}
	public static function subscriber_mail($user_email){
	        if(env('MAIL_MODE') == "live"){
                   $websettings = WebSetting::settings(); 
					$emails = $websettings['admin_emails']; 
					$bcc = $websettings['admin_bcc_emails'];  
					$messageData = [
						'user_email' => $user_email,
						'websettings' => $websettings
					];
					Mail::send('emails.subscriber-email', $messageData, function ($message) use ($emails,$bcc) {
						$message->to($emails)->bcc($bcc)->subject('Newsletter enquiry has been Received from '.config('constants.project_name').' Date -'.date('d-m-Y'));
					});
				
                } 
	
	}
	
	
	
	
	
	public static function customfit_mail($id){
	        if(env('MAIL_MODE') == "live"){
                  $websettings = WebSetting::settings(); 
				    $customfit = CustomFit::where('id',$id)->first();  
				    $product = Product::select('id','product_name','product_url')->with('product_image')->where('id',$customfit->product_id)->first();  
					$product = json_decode(json_encode($product),true); 
					$emails = $websettings['admin_emails']; 
					$bcc = $websettings['admin_bcc_emails'];  
					$messageData = [
						'data' => $customfit,
						'product' => $product,
						'websettings' => $websettings
					]; //pd($product);
					Mail::send('emails.custom-fit', $messageData, function ($message) use ($emails,$bcc) {
						$message->to($emails)->bcc($bcc)->subject('Custome Fit Information Received from '.config('constants.project_name').' Date -'.date('d-m-Y'));
					});
				
                } 
	
	}
	
}
