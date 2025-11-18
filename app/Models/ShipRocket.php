<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShipRocket extends Model
{
    public static function ShipRocketLogin(){
		  $curl = curl_init();
		  curl_setopt_array($curl, array(
				  CURLOPT_URL => 'https://apiv2.shiprocket.in/v1/external/auth/login',
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_ENCODING => '',
				  CURLOPT_MAXREDIRS => 10,
				  CURLOPT_TIMEOUT => 0,
				  CURLOPT_FOLLOWLOCATION => true,
				  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  CURLOPT_CUSTOMREQUEST => 'POST',
				  CURLOPT_POSTFIELDS =>'{
					"email": "'.env('SHIP_ROCKET_API_USERNAME').'",
					"password": "'.env('SHIP_ROCKET_API_PASSWORD').'"
				  }',
				  CURLOPT_HTTPHEADER => array(
					'Content-Type: application/json'
				  ),
			));
		 $response = curl_exec($curl);
		 curl_close($curl);
		 return $response;
    }
	
	
	public static function CreateOrder($orderId){  
		        
				$shiprocket_response = ShipRocket::ShipRocketLogin();
				$shiprocket_response = json_decode($shiprocket_response, TRUE);
			    
				if(isset($shiprocket_response['token']) && !empty($shiprocket_response['token'])){
				  
				//$email=$shiprocket_response['email'];
			    $token= $shiprocket_response['token'];
			   
				$orderDetails = Order::with(['getuser','order_products','order_address'])->where('orders.id',$orderId)->first();
				$orderDetails = json_decode(json_encode($orderDetails),true);
				
				$pickup_address = 'Home';
				
				
				$billing_address = $orderDetails['order_address'];
				$user_details = $orderDetails['getuser'];
				$products = $orderDetails['order_products'];
			
				$param2['order_id']= $orderId;
				$param2['order_date']=$orderDetails['created_at'];
				$param2['pickup_location']= $pickup_address;
				$param2['comment']=$orderDetails['comments'];
				$param2['billing_customer_name']=$billing_address['billing_first_name'];
				$param2['billing_last_name']=$billing_address['billing_last_name'];
				$param2['billing_address']=$billing_address['billing_address'];
				$param2['billing_address_2']='';
				$param2['billing_city']=$billing_address['billing_city'];
				$param2['billing_pincode']=$billing_address['billing_postcode'];
				$param2['billing_state']=$billing_address['billing_state'];
				$param2['billing_country']=$billing_address['billing_country'];
				$param2['billing_email']=$user_details['email'];
				$param2['billing_country']=$billing_address['billing_country'];
				$param2['billing_phone']=$billing_address['shipping_mobile'];
				$param2['shipping_is_billing']=true; 
				
				
				
				foreach($products as $product_key=>$product){
					
					$param2['order_items'][$product_key]=
							array(
								"name" => $product['product_name'],
								"sku" => $product['product_sku'],
								"hsn" => '',
								"units" => $product['product_qty'],
								"selling_price" =>$product['product_price'],
								"discount"=> $product['discount']
							);
					
				}
				
				
				
				if($orderDetails['payment_method'] == 'cod'){
					$payment_method = 'cod';
				}else{
					$payment_method = 'Prepaid';
				}
				
				
				
				
				$param2['payment_method']=$payment_method;
				$param2['shipping_charges']=$orderDetails['shipping_charges']; 
				$param2['giftwrap_charges']= 0;; 
				$param2['transaction_charges']= 0;
				if($orderDetails['coupon_discount'] > 0){
					$total_discount= $orderDetails['coupon_discount'];
				}
				else{
					$total_discount= 0;
				}

				$param2['total_discount']= $total_discount;
				$param2['sub_total']= $orderDetails['grand_total']+$total_discount;
				
				
				$param2['length']= $orderDetails['length'];
				$param2['breadth']= $orderDetails['width'];
				$param2['height']= $orderDetails['height'];
				$param2['weight']= $orderDetails['weight'];
				
				
				$token=$token; 
				$req=json_encode($param2);
                
				$headers = array(
					'Content-type: application/json',
					'Authorization:  Bearer '.$token,
				); 
                
				$url ="https://apiv2.shiprocket.in/v1/external/orders/create/adhoc";
				$curl = curl_init();
				curl_setopt($curl, CURLOPT_URL, $url);
				curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $req);
				$result21 = curl_exec($curl);
				$result22=json_decode($result21, true); pd($result21);
				 curl_close($curl); 
                 return $result22;     
				}else{
					return false;
				}
			   
    } 
    
	
	
	
	
	public static function TrackOrder($orderId=''){
		
		$shiprocket_response = ShipRocket::ShipRocketLogin();
		$shiprocket_response = json_decode($shiprocket_response, TRUE);
		//&channel_id=1713415
	     if(isset($shiprocket_response['token']) && !empty($shiprocket_response['token'])){
		        $company_id = $shiprocket_response['company_id'];
		        $token = $shiprocket_response['token'];
				$curl = curl_init();
				curl_setopt_array($curl, array(
				  CURLOPT_URL => 'https://apiv2.shiprocket.in/v1/external/courier/track?order_id='.$orderId,
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_ENCODING => '',
				  CURLOPT_MAXREDIRS => 10,
				  CURLOPT_TIMEOUT => 0,
				  CURLOPT_FOLLOWLOCATION => true,
				  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  CURLOPT_CUSTOMREQUEST => 'GET',
				  CURLOPT_HTTPHEADER => array(
					'Content-Type: application/json',
					'Authorization: Bearer '.$token.''
				),
				));

				$response = curl_exec($curl);

				curl_close($curl);
				
		        $response = json_decode($response, TRUE);
			   return $response;   
					 
		 }
	}
	public static function printManifests($orderId=''){
		try{
		$shiprocket_response = ShipRocket::ShipRocketLogin();
		$shiprocket_response = json_decode($shiprocket_response, TRUE);
		
	    if(isset($shiprocket_response['token']) && !empty($shiprocket_response['token'])){
		$token = $shiprocket_response['token'];
		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => 'https://apiv2.shiprocket.in/v1/external/manifests/print',
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS =>'{
			"order_ids": ['.$orderId.']
		}',
		  CURLOPT_HTTPHEADER => array(
			'Content-Type: application/json',
			'Authorization: Bearer '.$token.''
		  ),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		$response = json_decode($response, TRUE);
		return $response;
		}
		}catch (\Exception $e) {
        	return array();
        }
	
	}
	
	public static function Print_Manifests($shipment_order_id=''){
		try{
		$shiprocket_response = ShipRocket::ShipRocketLogin();
		$shiprocket_response = json_decode($shiprocket_response, TRUE);
		
	    if(isset($shiprocket_response['token']) && !empty($shiprocket_response['token'])){
		$token = $shiprocket_response['token'];
		      
			  $curl = curl_init();
			  curl_setopt_array($curl, array(
			  CURLOPT_URL => 'https://apiv2.shiprocket.in/v1/external/manifests/print',
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => '',
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => 'POST',
			  CURLOPT_POSTFIELDS =>'{
				"order_ids": ['.$shipment_order_id.']
			}',
			  CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json',
				'Authorization: Bearer '.$token.''
			  ),
			));

			$response = curl_exec($curl);

			curl_close($curl);
			$response = json_decode($response, TRUE);
			return $response;
		}
		}catch (\Exception $e) {
        	return array();
        }
	
	}
	
	
	public static function label_Manifests($shipment_id=''){
		try{
		$shiprocket_response = ShipRocket::ShipRocketLogin();
		$shiprocket_response = json_decode($shiprocket_response, TRUE);
		
	    if(isset($shiprocket_response['token']) && !empty($shiprocket_response['token'])){
		$token = $shiprocket_response['token'];
		      
			  $curl = curl_init();
			  curl_setopt_array($curl, array(
			  CURLOPT_URL => 'https://apiv2.shiprocket.in/v1/external/courier/generate/label',
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => '',
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => 'POST',
			  CURLOPT_POSTFIELDS =>'{
				"shipment_id": ['.$shipment_id.']
			}',
			  CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json',
				'Authorization: Bearer '.$token.''
			  ),
			));

			$response = curl_exec($curl);

			curl_close($curl);
			$response = json_decode($response, TRUE);
			return $response;
		}
		}catch (\Exception $e) {
        	return array();
        }
	
	}
	
	public static function labelManifests($shipment_id=''){
		try{
		$shiprocket_response = ShipRocket::ShipRocketLogin();
		$shiprocket_response = json_decode($shiprocket_response, TRUE);
		
	    if(isset($shiprocket_response['token']) && !empty($shiprocket_response['token'])){
		$token = $shiprocket_response['token'];
		      
			  $curl = curl_init();
			  curl_setopt_array($curl, array(
			  CURLOPT_URL => 'https://apiv2.shiprocket.in/v1/external/manifests/generate',
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => '',
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => 'POST',
			  CURLOPT_POSTFIELDS =>'{
				"shipment_id": ['.$shipment_id.']
			}',
			  CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json',
				'Authorization: Bearer '.$token.''
			  ),
			));

			$response = curl_exec($curl);

			curl_close($curl);
			$response = json_decode($response, TRUE);
			return $response;
		}
		}catch (\Exception $e) {
        	return array();
        }
	
	}
	
	public static function checkb2b_order_shipment($order_id){
        $package_ids = [];
		$orderDetails = PackageOrder::with('order_packages','getuser')->where('id',$order_id)->first();
		$orderDetails = json_decode(json_encode($orderDetails),true);
		foreach($orderDetails['order_packages'] as $orderDetail){
		   $package_ids[] = $orderDetail['package_id'];
		} 
	    $package_branch = ProductPackage::select('branch')->wherein('id',$package_ids)->groupby('branch')->get()->toarray(); 
        $branch_id = $package_branch[0]['branch'];
		$package_branch = ProductPackage::select('branch')->wherein('id',$package_ids)->groupby('branch')->get()->toarray();
			   
		if(count($package_branch) == 1 && !empty($orderDetails['getuser']['logistic']) && $orderDetails['getuser']['logistic'] != 'To Pay' ){
			return '1';
		}else{
			return '0';
		}
	}
	
	
}
