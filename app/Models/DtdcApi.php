<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Order;
use App\Models\OrdersProduct;
class DtdcApi extends Model
{
    //
    public static function dtdcinfo(){ exit;
        if(env('DTDC_MODE') =="test"){
            $username = "GL017_trk_json";
            $password = "4Fcc8";
            $apikey = "b01ed3562b088ab9c52822e3c18f9e";
            $trackingToken = "GL017_trk_json:521ce7881cb576b9a084489e02534e2e";
            $urls = array(
                    /*'PUSH_ORDER_URL'=> "http://demodashboardapi.shipsy.in/api/customer/integration/consignment/softdata",*/
                    'PUSH_ORDER_URL'=> "https://alphademodashboardapi.shipsy.io/api/customer/integration/consignment/softdata",
                    'TRACKING_URL'=>'https://blktracksvc.dtdc.com/dtdc-api/rest/JSONCnTrk/getTrackDetails',
                    'TOKEN_GENERATE_URL' => 'http://dtdcstagingapi.dtdc.com/dtdc-api/intlapi/splcustomer/authenticate',
                    'PINCODE_URL' => 'http://fareyesvc.ctbsplus.dtdc.com/ratecalapi/PincodeApiCall'
                );
        }else{
            $username = "JO2625_trk_json";
            $password = "4Fcc8";
            $apikey = "00a12d90ac4312d979a91dc6a4c3df";
            $urls = array(
                    'PUSH_ORDER_URL'=> "https://dtdcapi.shipsy.io/api/customer/integration/consignment/softdata",
                    'TRACKING_URL'=>'https://blktracksvc.dtdc.com/dtdc-api/rest/JSONCnTrk/getTrackDetails',
                    'TOKEN_GENERATE_URL' => ' https://blktracksvc.dtdc.com/dtdc-api/api/dtdc/authenticate',
                    'PINCODE_URL' => 'http://fareyesvc.ctbsplus.dtdc.com/ratecalapi/PincodeApiCall'
                );
            $trackingToken = "JO2625_trk_json:a948de486ce9bc964b75ea399b05027d";
        }
        return array('username'=>$username,'password'=>$password,'apikey'=>$apikey,'tracking_token'=>$trackingToken,'urls'=>$urls);
    }

    public static function createDTDCmanifest($order_id){ exit;
        $orderArray = array();
        $orderDetails = Order::join('orders_addresses','orders_addresses.order_id','=','orders.id')->select('orders.id as order','orders_addresses.shipping_mobile as phone','orders.grand_total as cod_amount','orders_addresses.shipping_name as name','orders_addresses.shipping_country as country','orders.created_at as order_date','orders.grand_total as total_amount','orders_addresses.shipping_address as add','orders_addresses.shipping_postcode as pin','orders.payment_method as payment_mode','orders_addresses.shipping_state as state','orders_addresses.shipping_city as city','orders.payment_status','orders.sub_total')->where('orders.id',$order_id)->first();
        if($orderDetails->payment_mode=="COD"){
            $orderDetails->payment_mode = "COD";
            $orderDetails->cod_amount = $orderDetails->cod_amount;
        }else{
            $orderDetails->payment_mode = "Prepaid";
            $orderDetails->cod_amount = 0;   
        }
        $orderDetails->add = $orderDetails->add." ".$orderDetails->add2;
        $orderDetails->add = str_replace('&', 'and', $orderDetails->add);
        $orderDetails->add = str_replace('\\', '-', $orderDetails->add);
        $orderDetails->add = str_replace('"', '-', $orderDetails->add);
        $orderDetails->add = str_replace(';', '-', $orderDetails->add);
        $orderDetails->city = str_replace('&', 'and', $orderDetails->city);
        $orderDetails->state = str_replace('&', 'and', $orderDetails->state);
        $orderDetails = json_decode(json_encode($orderDetails),true);
        
        unset($orderDetails['add2']);
        //echo "<pre>"; print_r($orderDetails); die;    
        $productDetails = OrdersProduct::with('productdetail')->select('product_id','product_code','product_name','product_size','product_qty','category_name','line_amount')->where('order_id',$order_id)->get();
        $productDetails = json_decode(json_encode($productDetails),true);
        //echo "<pre>"; print_r($productDetails); die;
        $products_desc = "";
        $proqty =0;$commodity_ids ="";$pcs = array();
        $totalWeight = 0;
        foreach($productDetails as $key => $pro){
            if($pro['productdetail']['product_weight']>0){
                $totalWeight += $pro['productdetail']['product_weight'];
            }else{
                $totalWeight += 0.50; 
            }
            $proqty += $pro['product_qty']; 
            $products_desc .= ++$key.'). '.$pro['product_code']."-".$pro['product_name']."-".$pro['product_size']."-";
            $products_desc .= "  ";
            $commodity_ids .= $pro['category_name'].", ";
        }
        $products_desc = str_replace('&', 'and', $products_desc);
        $products_desc = str_replace('\\', '-', $products_desc);
        $products_desc = str_replace('"', '-', $products_desc);
        $products_desc = str_replace(';', '-', $products_desc);
        $products_desc = substr($products_desc, 0, 250);
        $wginkg = $totalWeight/1000;
        $pcs[0]['description'] = $products_desc;
        if($orderDetails['payment_status']== "exchange checkout" || $orderDetails['payment_status']== "special_checkout" ){
            $pcs[0]['declared_value'] = (string)$orderDetails['sub_total'];
        }else{
            $pcs[0]['declared_value'] = (string)$orderDetails['total_amount'];
        }
        $orderArray['consignments'][0]['customer_code'] = 'JO2625';
        if($orderDetails['payment_mode']=="COD"){
            if($wginkg <=1){
                $orderArray['consignments'][0]['service_type_id'] = "B2C SMART EXPRESS";
            }else{
                $orderArray['consignments'][0]['service_type_id'] = "B2C GROUND ECONOMY";
            }
        }else{
            if($wginkg <=1){
                $orderArray['consignments'][0]['service_type_id'] = "B2C PRIORITY";
            }else{
                $orderArray['consignments'][0]['service_type_id'] = "B2C SMART EXPRESS"; 
            }
        }
        $pcs[0]['weight'] = (string)$wginkg;
        $pcs[0]['height'] = "1";
        $pcs[0]['length'] = "1";
        $pcs[0]['width']  = "1";
        $pcs = array_values($pcs);
        $orderArray['consignments'][0]['load_type'] = "NON-DOCUMENT";
        $orderArray['consignments'][0]['description'] = $products_desc;
        if($orderDetails['payment_mode']=="COD"){
            $orderArray['consignments'][0]['cod_amount'] = (string)$orderDetails['cod_amount'];
            $orderArray['consignments'][0]['cod_collection_mode'] = "cash";
        }
        $orderArray['consignments'][0]['num_pieces'] = 1;
        $orderArray['consignments'][0]['dimension_unit'] = "cm";
        $orderArray['consignments'][0]['length'] = "1";
        $orderArray['consignments'][0]['width'] = "1";
        $orderArray['consignments'][0]['height'] = "1";
        $orderArray['consignments'][0]['weight_unit'] = "kg";
        $orderArray['consignments'][0]['weight'] = (string)$wginkg;
        if($orderDetails['payment_status']== "exchange checkout"){
            $orderArray['consignments'][0]['declared_value'] = (string)$orderDetails['sub_total'];
        }else{
            $orderArray['consignments'][0]['declared_value'] = (string)$orderDetails['total_amount'];
        }
        $orderArray['consignments'][0]['invoice_number'] = (string)$orderDetails['order'];
        $orderArray['consignments'][0]['invoice_date'] = $orderDetails['order_date'];
        $orderArray['consignments'][0]['customer_reference_number'] = (string)$orderDetails['order'];
        $orderArray['consignments'][0]['commodity_id'] = $commodity_ids;
        $orderArray['consignments'][0]['consignment_type'] = "Forward";
        //Origin Details
        $orderArray['consignments'][0]['origin_details']['name'] = "Onvers";
        $orderArray['consignments'][0]['origin_details']['phone'] = "9781923036";
        $orderArray['consignments'][0]['origin_details']['alternate_phone'] = "8727918814";
        $orderArray['consignments'][0]['origin_details']['address_line_1'] = "Village Rajgarh, Near Doraha Canal Bridge, GT Road, Doraha, Ludhiana, Punjab, India 141421";
        $orderArray['consignments'][0]['origin_details']['address_line_2'] = "Village Rajgarh, Near Doraha Canal Bridge, GT Road, Doraha, Ludhiana, Punjab, India 141421";
        $orderArray['consignments'][0]['origin_details']['pincode'] = "141421";
        $orderArray['consignments'][0]['origin_details']['city'] = "Ludhiana";
        $orderArray['consignments'][0]['origin_details']['state'] = "Punjab";
        // Desitnation details
        $orderArray['consignments'][0]['destination_details']['name'] = $orderDetails['name'];
        $orderArray['consignments'][0]['destination_details']['phone'] = $orderDetails['phone'];
        $orderArray['consignments'][0]['destination_details']['address_line_1'] = $orderDetails['add'];
        /*$orderArray['consignments'][0]['destination_details']['address_line_2'] = "test";*/
        $orderArray['consignments'][0]['destination_details']['pincode'] = $orderDetails['pin'];
        $orderArray['consignments'][0]['destination_details']['state'] = $orderDetails['state'];
        $orderArray['consignments'][0]['destination_details']['city'] = $orderDetails['city'];
        //Products Details
        $orderArray['consignments'][0]['pieces_detail'] = $pcs;
        $orderArray = json_encode($orderArray);
        /*echo "<pre>"; print_r($orderArray); die;*/
        $dtdcInfo = \App\Models\DtdcApi::dtdcinfo();
        $url = $dtdcInfo['urls']['PUSH_ORDER_URL'];
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $orderArray);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json','api-key:'.$dtdcInfo['apikey']));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $manifest_result = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($manifest_result,true);
        /*echo "<pre>"; print_r($manifest_result); die;*/
        return array('status'=>true,'result'=> $result,'manifest_request'=>$orderArray,'manifest_result'=>$manifest_result);
    }

    public static function trackOrder($awbNumber){ exit;
        $details = DtdcApi::dtdcinfo();
        $token = $details['tracking_token'];
        //Get Trackings 
        if(env('DTDC_MODE') =="test"){
            $trackingUrl = $details['urls']['TRACKING_URL'];
        }else{
            $trackingUrl = $details['urls']['TRACKING_URL'].'?strcnno='.$awbNumber.'&TrkType=cnno&addtnlDtl=Y&apikey='.$token;
        }
        $trackingData = file_get_contents($trackingUrl);
        $xml = simplexml_load_string($trackingData, "SimpleXMLElement", LIBXML_NOCDATA);
        $json = json_encode($xml);
        $trackings = json_decode($json,TRUE);
        if(isset($trackings['CONSIGNMENT']['CNBODY']['CNACTION'][0])){
            $status = true;
            $trackings = $trackings['CONSIGNMENT']['CNBODY']['CNACTION'];
            //echo "<pre>"; print_r($trackings); die;
            foreach($trackings as $tkey=>  $track){
                foreach($track['FIELD'] as $trackinfo){
                    foreach($trackinfo as $attributeInfo){
                        if($attributeInfo['name'] == 'strAction'){
                            $trackers[$tkey]['track_status'] = $attributeInfo['value'];
                        }
                        if($attributeInfo['name'] == 'strActionDate'){
                            $actionDate = $attributeInfo['value'];
                            $actionDate = explode('-',chunk_split($actionDate,2,"-"));
                            $actionDate = $actionDate[0].'-'.$actionDate[1].'-'.$actionDate[2].$actionDate[3];
                            $trackers[$tkey]['ScanDateTime'] = $actionDate;
                        }
                        if($attributeInfo['name'] == 'strDestination'){
                            $trackers[$tkey]['ScannedLocation'] =  $attributeInfo['value'];
                        }
                    }
                }
            }
            $trackers = DtdcApi::group_by($trackers);
            $orderTrackArr = array('Booked'=>'Order Placed','In Transit'=>'In Transit','Out For Delivery'=>'Out For Delivery','Delivered'=>'Delivered');
            foreach ($orderTrackArr as $tkey => $track) {
                $key = array_search($tkey, array_column($trackers, 'track_status'));
                if(is_numeric($key)){
                    $finalTrackArr[$tkey] = $trackers[$key];
                    $nextkey = $key+1;
                    if(isset($trackers[$nextkey]['track_status'])){
                        $finalTrackArr[$tkey]['track_status'] = 'complete';
                    }else{
                        $finalTrackArr[$tkey]['track_status'] = 'active';
                    }
                    $finalTrackArr[$tkey]['track_name'] = $track;
                }else{
                    $finalTrackArr[$tkey]['track_status'] = 'disabled';
                    $finalTrackArr[$tkey]['track_name'] = $track;
                }
            }
        }else{
            $trackingDetail = array();
            $status = false;
            $finalTrackArr = array();
        }
        return array('status'=>$status,'tracker'=>$finalTrackArr);
    }

    public static function group_by($arr) { exit;
        foreach($arr as $k => $v) {
            foreach($arr as $key => $value) {
                if($k != $key && $v['track_status'] == $value['track_status'])
                {
                    unset($arr[$k]);
                }
            }
        }
        return array_values($arr);
    }

    public static function checkPincode($pincode,$paymentMethod){ exit;
        $details = DtdcApi::dtdcinfo();
        $url = $details['urls']['PINCODE_URL'];
        $pincodeArr = array('orgPincode'=>'141123','desPincode'=>$pincode);
        $pincodeArr = json_encode($pincodeArr);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $pincodeArr);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($result,true);
        $serviceable = false;
        if(isset($result['SERV_LIST'][0]['COD_Serviceable']) && isset($result['SERV_LIST'][0]['LITE_Serviceable'])){
            if($paymentMethod=="cod"){
                if($result['SERV_LIST'][0]['COD_Serviceable']=="YES"){
                    $serviceable = true;
                }
            }else{
                if($result['SERV_LIST'][0]['LITE_Serviceable']=="YES"){
                    $serviceable = true;
                }
            }
        }
        return $serviceable;
    }
}
