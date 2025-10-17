<?php

namespace App\Http\Controllers\Front;

use Illuminate\Support\Facades\View;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ShippingAddress;
use App\Models\BillingAddress;
use App\Models\State;
use App\Models\User;
use Validator;
use Auth;
use DB;

class AddressController extends Controller
{
    
	 public function orderaddressform(Request $request){
		$data = $request->all(); 
		$id = $data['id'];
		$address = [];
		if($data['type'] =='shipping'){
			$form_type = 'Shipping';
			if(!empty($id)){
				$address = ShippingAddress::where(['id'=>$id,'user_id'=>Auth::user()->id])->first();
			}
		}else{
			$form_type = 'Billing';
			$address = BillingAddress::where(['id'=>$id,'user_id'=>Auth::user()->id])->first();
		}
		$states = State::orderby('name','ASC')->pluck('name')->toArray();
		$html = (String)View::make('front.pages.account.order-address-form')->with(compact('address','states','id','form_type'));
		return response()->json(['status'=>true,'html'=>$html]);
	}
	
	public function saveorderaddress(Request $request){ 
		   $validator = Validator::make($request->all(), [
                'first_name'=>'bail|required',
                'last_name'=>'bail|required',
                'email' => 'required|string|regex:/^\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b$/i|max:255',
                'mobile' => 'required|numeric|digits:10',
				'postcode'=>'bail|required|numeric|digits:6|exists:cities,pincode',
				'state'=>'bail|required',
				'city'=>'bail|required',
				'address'=>'bail|required',
			]);
            if($validator->passes()){
				
				$data = $request->all();
				$id = $data['id'];
				$form_type = $data['form_type'];
				
				if($id == ''){
					if($form_type == "Shipping"){
						$order_address = new ShippingAddress;
						$order_address ->user_id =  Auth::user()->id;
						if(ShippingAddress::addresscount() == 0){
						  $order_address ->is_default =  1;
						}
						
					}else{
						$order_address = new BillingAddress;
						$order_address ->user_id =  Auth::user()->id;
						$order_address ->is_default =  1;
					}
				}else{
					if($form_type == "Shipping"){
						$order_address = ShippingAddress::where(['id'=>$id,'user_id'=>Auth::user()->id])->first();
					}else{
						$order_address = BillingAddress::where(['id'=>$id,'user_id'=>Auth::user()->id])->first();
					}
				}
				
				$order_address->name =  $data['first_name'].' '.$data['last_name'];
				$order_address->first_name =  $data['first_name'];
				$order_address->last_name =  $data['last_name'];
				$order_address->email =  $data['email'];
				$order_address->mobile =  $data['mobile'];
				$order_address->email =  $data['email'];
				$order_address->postcode =  $data['postcode'];
				$order_address->city =  $data['city'];
				$order_address->state =  $data['state'];
				$order_address->address =  $data['address'];
				$order_address->address_line2 =  $data['address_line2'];
				$order_address->save();
				
				if($form_type == "Shipping"){
					
					if(empty(Auth::user()->name)){
							$updateAddr['name'] = $data['first_name']." ". $data['last_name'];
							$updateAddr['first_name'] = $data['first_name'];
							$updateAddr['last_name'] = $data['last_name'];
							$updateAddr['state'] = $data['state'];
							$updateAddr['city'] = $data['city'];
                            $updateAddr['pincode'] = $data['postcode'];
                            $updateAddr['address'] = $data['address'];
                            $updateAddr['address_line2'] = $data['address_line2'];
                            User::where('id',Auth::user()->id)->update($updateAddr);    
                        }
					
					
					
					if(isset($data['billing_address_same_as_shipping_address']) &&  $data['billing_address_same_as_shipping_address'] == '1'){
					    $billing_address = new BillingAddress;
						$billing_address ->user_id =  Auth::user()->id;
						$billing_address ->is_default =  1;
						$billing_address->name =  $data['first_name'].' '.$data['last_name'];
						$billing_address->first_name =  $data['first_name'];
						$billing_address->last_name =  $data['last_name'];
						$billing_address->email =  $data['email'];
						$billing_address->mobile =  $data['mobile'];
						$billing_address->email =  $data['email'];
						$billing_address->postcode =  $data['postcode'];
						$billing_address->city =  $data['city'];
						$billing_address->state =  $data['state'];
						$billing_address->address =  $data['address'];
						$billing_address->address_line2 =  $data['address_line2'];
						$billing_address->save();
					}
					
				}
				
			
				$html = (String)View::make('front.pages.products.checkout.include.order_address');
				$messages = array('Address has been added successfully!');
				return response()->json(['status'=>true,'message'=>$messages,'html'=>$html]);
			}else{
                return response()->json(['status'=>false,'type'=>'validation','errors'=>$validator->messages()]);
            }
	}
	
	
	
	public function saveAddress(Request $request){
        try{
            if($request->ajax()){
                $validator = Validator::make($request->all(), [
                    'first_name'=>'bail|required|regex:/^[a-zA-Z]+$/u|max:20',
                    'last_name'=>'bail|required|regex:/^[a-zA-Z]+$/u|max:20',
                    'address'=>'bail|required|max:150',
                    'city'=>'bail|required|max:20',
                    'state'=>'bail|required|max:20',
                    'postcode'=>'bail|required|digits:6',
                    'mobile' => 'bail|required|digits:10',
                    /*'email' => 'required|string|regex:/^\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b$/i|max:255'*/
                ],
                [
                    /*'email.regex' =>'This email is not a valid email address',*/
                    'mobile.required' => 'The contact field is required.'
                ]);
                if($validator->passes()) {
                    $data = $request->all();
                    //echo "<pre>"; print_r($data); die;
                    unset($data['_token']);
                    unset($data['account']);
                    /*$data['country'] = 'India';*/
                    $data['user_id'] = Auth::user()->id;
                    $data['name'] = $data['first_name']." ". $data['last_name'];
                    if(ShippingAddress::addresscount(Auth::user()->id) ==0){
                        $data['is_default'] = 1;
                        if(empty(Auth::user()->name)){
                            //Update Address in Users Table
                            $updateAddr['city'] = $data['city'];
                            $updateAddr['state'] = $data['state'];
                            $updateAddr['pincode'] = $data['postcode'];
                            $updateAddr['address'] = $data['address'];
                            /*$updateAddr['email'] = $data['email'];*/
                            
                            if(Auth::user()->name ==""){
                                $updateAddr['name'] = $data['first_name']." ". $data['last_name'];
                            }
                            unset($updateAddr['email']);
                            User::where('id',Auth::user()->id)->update($updateAddr);    
                        }
                    }
                    if(!empty($data['shipping_id'])){
                        $shippingid = $data['shipping_id'];
                        unset($data['shipping_id']);
                        ShippingAddress::where('id',$shippingid)->update($data);
                        $success_message = "Delivery Address has been updated!";
                    }else{
                        ShippingAddress::where('user_id',Auth::user()->id)->update(['is_default'=>'0']);
                        $success_message = "Delivery Address has been added!";
                        $data['is_default'] = 1;
                        ShippingAddress::create($data);
                    }
                    return response()->json([
                        'status' => true,
                        'success_message' => $success_message,
                        'view' => (String)View::make('front.products.delivery_address')
                    ]);
                }else{
                    return response()->json(['status'=>false,'type'=>'validation','errors'=>$validator->messages()]);
                }
            }
        }catch(\Exception $e){
            return response()->json(exceptionMessage($e),423);
        }
    }

    public function getDeliveryAddress(Request $request){
        try{
            if($request->ajax()){
                $validator = Validator::make($request->all(), [
                    'id'=>'bail|required|exists:shipping_addresses,id',
                ]);
                $states = State::orderby('name','ASC')->pluck('name')->toArray();
                if($validator->passes()) {
                    $data = $request->all();
                    /*echo "<pre>"; print_r($data); die;*/
                    $address = ShippingAddress::where('id',$data['id'])->first()->toArray();
                    return response()->json([
                        'status' => true,
                        'address' => $address,
                        'states' => $states
                    ]);
                }else{
                    return response()->json([
                        'status' => false,
                        'states' => $states,
                        'view' => (String)View::make('front.products.delivery_address')
                    ]);
                }
            }
        }catch(\Exception $e){
            return response()->json(exceptionMessage($e),423);
        }
    }

    public function setDefaultAddress(Request $request){
        try{
            if($request->ajax()){ 
                $validator = Validator::make($request->all(), [
                    'addressid'=>'bail|required|exists:shipping_addresses,id',
                ]);
                if($validator->passes()) { 
                    $data = $request->all();
                    DB::beginTransaction();
                    ShippingAddress::where('id',$data['addressid'])->update(['is_default'=>1]);
                    ShippingAddress::where('id','!=',$data['addressid'])->where('user_id',Auth::user()->id)->update(['is_default'=>'0']);
                    DB::commit();
                }
				
                $html = (String)View::make('front.pages.products.checkout.include.order_address');
				$messages = array('Default Address has been seted successfully!');
                return response()->json([
                    'status' => true,
                    'html' => $html,
                    'messages' => $messages,
                ]);
            }
        }catch(\Exception $e){
            return response()->json(exceptionMessage($e),423);
        }
    }

    public function removeDeliveryAddress(Request $request){
        try{
            if($request->ajax()){
                $validator = Validator::make($request->all(), [
                    'id'=>'bail|required|exists:shipping_addresses,id',
                ]);
                if($validator->passes()) {
                    $data = $request->all();
                    $id = $data['id'];
                    ShippingAddress::where(['id'=>$id,'user_id'=>Auth::user()->id])->delete();
                }
                $html = (String)View::make('front.pages.products.checkout.include.order_address');
				$messages = array('Shipping Address has been deleted successfully!');
                return response()->json([
                    'status' => true,
                    'html' => $html,
                    'messages' => $messages,
                ]);
            }
        }catch(\Exception $e){
            return response()->json(exceptionMessage($e),423);
        }
    }
}
