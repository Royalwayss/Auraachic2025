<?php 
use App\Models\BannerImage;
use App\Models\Cart;
use App\Models\Wishlist;
use App\Models\Product;
use App\Models\Category;

    function getCartItems(){
        if(Auth::check()){
            // If the user is logged in, check from Auth (user_id)
            $user_id = Auth::user()->id;
            $getCartItems = Cart::with('product')->where('user_id',$user_id)->get()->toArray();
        }else{
            // If the user is not logged in, check from Session (session_id)
            $session_id = Session::get('cartsessionId');
            $getCartItems = Cart::with('product')->where('session_id',$session_id)->get()->toArray();
        }
        return $getCartItems;
    }
    function amount_format($amount=0){
       return  number_format((float)$amount, 2, '.', '');
    }
    function formatAmt($amount){
        list ($number, $decimal) = explode('.', sprintf('%.2f', floatval($amount)));
        $sign = $number < 0 ? '-' : '';
        $number = abs($number);
        for ($i = 3; $i < strlen($number); $i += 3){
            $number = substr_replace($number, ',', -$i, 0);
        }
        if($decimal==00){
            return  $sign . $number;
        }else{
            return  $sign . $number;
        }
    }

    function currencyinfo(){
        if(!\Session::has('currency')){
            \Session::put('currency','INR');
        }
        //\Session::put('currency','INR');
        $currency = \Session::get('currency');
        $currencyinfo = \App\Models\Country::where('currency_code',$currency)->first();
        $currencyinfo = json_decode(json_encode($currencyinfo),true);
        return $currencyinfo;
    }

    function formatPrice($currencyinfo,$amount,$convert=null){
        if($convert =="no"){
            $amount  =  round($amount);
        }else{
            $amount  =  round($amount/$currencyinfo['rate']);
        }
        list ($number, $decimal) = explode('.', sprintf('%.2f', floatval($amount)));
        $sign = $number < 0 ? '-' : '';
        $number = abs($number);
        for ($i = 3; $i < strlen($number); $i += 3){
            $number = substr_replace($number, ',', -$i, 0);
        }
        return  $currencyinfo['currency_symbol']." ".$sign . $number.".".$decimal;
    }

    function formatPriceNoDec($currencyinfo,$amount,$convert=null){
        if($convert =="no"){
            $amount  =  round($amount);
        }else{
            $amount  =  round($amount/$currencyinfo['rate']);
        }
        list ($number, $decimal) = explode('.', sprintf('%.2f', floatval($amount)));
        $sign = $number < 0 ? '-' : '';
        $number = abs($number);
        for ($i = 3; $i < strlen($number); $i += 3){
            $number = substr_replace($number, ',', -$i, 0);
        }
        return  $currencyinfo['currency_symbol']." ".$sign . $number;
    }

    function getPrice($currencyinfo,$amount,$convert=null){
        $convert = "no";
        if($convert =="no"){
            $amount  =  $amount;
        }else{
            $amount  =  $amount/$currencyinfo['rate'];
        }
        return  $amount;
    }

    function convert_number_to_words($number) {
        $hyphen      = '-';
        $conjunction = ' and ';
        $separator   = ', ';
        $negative    = 'negative ';
        $decimal     = ' point ';
        $dictionary  = array(
            0                   => 'zero',
            1                   => 'one',
            2                   => 'two',
            3                   => 'three',
            4                   => 'four',
            5                   => 'five',
            6                   => 'six',
            7                   => 'seven',
            8                   => 'eight',
            9                   => 'nine',
            10                  => 'ten',
            11                  => 'eleven',
            12                  => 'twelve',
            13                  => 'thirteen',
            14                  => 'fourteen',
            15                  => 'fifteen',
            16                  => 'sixteen',
            17                  => 'seventeen',
            18                  => 'eighteen',
            19                  => 'nineteen',
            20                  => 'twenty',
            30                  => 'thirty',
            40                  => 'fourty',
            50                  => 'fifty',
            60                  => 'sixty',
            70                  => 'seventy',
            80                  => 'eighty',
            90                  => 'ninety',
            100                 => 'hundred',
            1000                => 'thousand',
            1000000             => 'million',
            1000000000          => 'billion',
            1000000000000       => 'trillion',
            1000000000000000    => 'quadrillion',
            1000000000000000000 => 'quintillion'
        );
        
        if (!is_numeric($number)) {
            return false;
        }
        
        if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
            // overflow
            trigger_error(
                'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
                E_USER_WARNING
            );
            return false;
        }

        if ($number < 0) {
            return $negative . convert_number_to_words(abs($number));
        }
        
        $string = $fraction = null;
        
        if (strpos($number, '.') !== false) {
            list($number, $fraction) = explode('.', $number);
        }
    
        switch (true) {
            case $number < 21:
                $string = $dictionary[$number];
                break;
            case $number < 100:
                $tens   = ((int) ($number / 10)) * 10;
                $units  = $number % 10;
                $string = $dictionary[$tens];
                if ($units) {
                    $string .= $hyphen . $dictionary[$units];
                }
                break;
            case $number < 1000:
                $hundreds  = $number / 100;
                $remainder = $number % 100;
                $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
                if ($remainder) {
                    $string .= $conjunction . convert_number_to_words($remainder);
                }
                break;
            default:
                $baseUnit = pow(1000, floor(log($number, 1000)));
                $numBaseUnits = (int) ($number / $baseUnit);
                $remainder = $number % $baseUnit;
                $string = convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
                if ($remainder) {
                    $string .= $remainder < 100 ? $conjunction : $separator;
                    $string .= convert_number_to_words($remainder);
                }
                break;
        }
    
        if (null !== $fraction && is_numeric($fraction)) {
            $string .= $decimal;
            $words = array();
            foreach (str_split((string) $fraction) as $number) {
                $words[] = $dictionary[$number];
            }
            $string .= implode(' ', $words);
        }
        return $string;
    }

    function sendSms($smsdetails){
        /*
        $request =""; //initialise the request variable
        $param['uname']="20171736";
        $param['pass']="9ge99e9r";
        $param['send']="LAKSTA";
        $param['dest']=$smsdetails['mobile'];
        $param['msg']=$smsdetails['message'];
        $param['priority'] = 1;

        //Have to URL encode the values
        foreach($param as $key=>$val) {
          $request.= $key."=".urlencode($val);
          //we have to urlencode the values
          $request.= "&";
          //append the ampersand (&) sign after each parameter/value pair
        }

        $request = substr($request, 0, strlen($request)-1);
        //remove final (&) sign from the request

        $url = "http://164.52.195.161/API/SendMsg.aspx?".$request;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $curl_scraped_page = curl_exec($ch);
        //echo "<pre>"; print_r($curl_scraped_page); die;
        curl_close($ch);
        */
    }

    function sendWhatsApp($whatsappDetails){
        /*
        $request =""; //initialise the request variable
        $postData['phone'] = "91-".$whatsappDetails['mobile'];
        $postData['contentType'] = 1;
        $postData['content'] = $whatsappDetails['message'];
        $postData['caption'] = "";
        $postData['fileName'] = "";
        $postData['access-token'] = "67988-dfd0d317b7e542d0b9331bd29f839394";

        //Have to URL encode the values
        foreach($postData as $key=>$val) {
          $request.= $key."=".urlencode($val);
          //we have to urlencode the values
          $request.= "&";
          //append the ampersand (&) sign after each parameter/value pair
        }

        $request = substr($request, 0, strlen($request)-1);
        //remove final (&) sign from the request

        $url = "http://chat.chatmybot.in/whatsapp/api/v1/sendmessage?".$request;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $curl_scraped_page = curl_exec($ch);
        // echo "<pre>"; print_r($curl_scraped_page); die;
        curl_close($ch);
        */
    }

    // address 
    function validationResp($validator){
        foreach($validator->errors()->toArray() as $v => $a){
            $validationError = [
                'success' => false,
                'message' => $a[0],
            ];
            return $validationError;
        }
    }

    function exceptionError($message,$code=422){
        $error = [
            'status' => false,
            'code' => $code,
            'message' => $message,
        ];
        return $error;
    }
    function exceptionMessage($e){
        $message = $e->getMessage() . " Line Number ". $e->getLine()." File :- ".$e->getFile();
        return exceptionError($message);
    }

    function  get_banners(){
        // $typeArray = array('home'=>'Home Page Section 1 (Top Slider)','home2'=>'Home Page Section 2','home3'=>'Home Page Section 3','home4'=>'Home Page Section 4','home5'=>'Home Page Section 5','home6'=>'Home Page Section 6','home7'=>'Home Page Section 7','home8'=>'Home Page Section 8','home9'=>'Last Banner','top-offer-banner'=>'Top Offer Banner','offer-banners'=>'Offer Banners');
        $typeArray = array('home'=>'Home Page Section 1 (Top Slider)','home2'=>'Home Page Section 2','home3'=>'Home Page Section 3');
        $bannerImages = array();
        foreach($typeArray as $tkey=> $type){
            $banners = BannerImage::where('type',$tkey)->where('status',1)->orderby('sort','DESC')->get()->toArray();
            $bannerImages[$tkey] = $banners;
        }
        return $bannerImages;
    }

    function apiSuccessResponse($message,$data=NULL){
        $success = [
            'status'       => true,
            'code'          => 200,
            'message'       => $message,  
            'data'          => $data
        ];
        return $success;
    }

    function apiErrorResponse($message,$code=422){
        $error = [
            'status'       => false,
            'code'          => $code,
            'message'       => $message,
        ];
        return $error;
    }


    function searchforAttr($array, $key, $value){
        $results = array();
        if (is_array($array)) {
            if (isset($array[$key]) && $array[$key] == $value) {
                $results[] = $array;
            }
            foreach ($array as $subarray) {
                $results = array_merge($results, searchforAttr($subarray, $key, $value));
            }
        }
        return $results;
    }

    function brands($getproducts){
        $productArr = json_decode(json_encode($getproducts),true);
        $brandids = array_column($productArr['data'], 'brand_id');
        $brands = DB::table('brands')->select('id','brand_name')->where('status',1)->wherein('id',$brandids)->orderby('brand_name','ASC')->get();
        $brands = json_decode(json_encode($brands),true);
        return $brands;
    }

    function totalCartItems(){
        if(Auth::check()){
            $user_id = Auth::user()->id;
            $totalCartItems = Cart::where('user_id',$user_id)->sum('qty');
        }else{
            $session_id = Session::get('cartsessionId');
            $totalCartItems = Cart::where('session_id',$session_id)->sum('qty');
        }
        return $totalCartItems;
    }

    function totalWishlistItems(){
        if(Auth::check()){
            $user_id = Auth::user()->id;
            $totalWishlistItems = Wishlist::where('user_id',$user_id)->count();
        }else{
            $totalWishlistItems = 0;
        }
        return $totalWishlistItems;
    }

    function checkProFile($prodata,$type,$val){
        if($type =="category"){
            $count =  DB::table('categories')->where('id',$val)->count();
            if($count >0){
                $status = true;
            }else{
                $status = false;
                $message = "Invalid  category_id ";
            }
        }elseif($type =="brand"){
            $count =  DB::table('brands')->where('id',$val)->count();
            if($count >0){
                $status = true;
            }else{
                $status = false;
                $message = "Invalid  brand_id ";
            }
        }elseif($type =="product_price"){
            $status = true;
            if(empty($val) && $val ==0){
                $status = false;
                $message = "Please Enter valid price "; 
            }
        }elseif($type =="product_code"){
            $status = true;
            if(strpos($val, ".") !== false) {
                $status = false;
                $message = "Invalid product_code "; 
            }else{
                // Check Product Code already exists in database
                $count =  DB::table('products')->where('product_code',$val)->count();
                if($count >0){
                    $status = false;
                    $message = "product_code already been taken ";
                }
            }
        }elseif($type =="product_discount"){
            $status = true;
            if($val >=100){
                $status = false;
                $message = "Discount can not be greater then or equal to 100 ";
            }
        }elseif($type =="main_image"){
            $status = true;
            if(empty($val)){
                $status = false;
                $message = "Please add main image of the Product ";
            }
        }elseif($type =="is_featured"){
            $status = true;
            $sellerTypes = array('Yes','No');
            if(!in_array($val,$sellerTypes)){
                $status = false;
                $message = "Invalid is_featured value ";
            }
        }elseif($type =="is_new"){
            $status = true;
            $newTypes = array('Yes','No');
            if(!in_array($val,$newTypes)){
                $status = false;
                $message = "Invalid is_new value ";
            }
        }elseif($type =="status"){
            $status = true;
            $statusTypes = array('0','1');
            if(!in_array($val,$statusTypes)){
                $status = false;
                $message = "Invalid status value ";
            }
        }elseif($type =="other_cat_ids"){
            $status = true;
            if(empty($val)){
                $status = false;
                $message = "Please enter atleast one other_cat_ids ";
            }else{
                $otherCats = explode(',',$val);
                foreach($otherCats as $cat){
                    $checkCat = DB::table('categories')->where('id',$cat)->count();
                    if($checkCat ==0){
                        $message =  "Invalid Other Cat id (".$cat.")" ;
                        return array('status'=>false,'message'=>$message);
                    }
                }
            }
        }elseif($type =="image_0"){
            $status = true;
            if(empty($val)){
                $status = false;
                $message = "Please enter atleast one image ";
            }else{
                $supportedExtensions = array('gif','jpg','jpeg','png');
                for($img = 0; $img <=9; $img ++){
                    $imgKey = "image_".$img;
                    if(isset($prodata[$imgKey])){
                        $ext = strtolower(pathinfo($prodata[$imgKey], PATHINFO_EXTENSION));
                        if(preg_match('/\s/',$prodata[$imgKey])){
                            $status = false;
                            $message = "You added spaces on ".$imgKey." ";
                            return array('status'=>false,'message'=>$message);
                        }
                        if(!in_array($ext, $supportedExtensions)){
                            $status = false;
                            $message = "You have missed the image extension ".$imgKey." ";
                            return array('status'=>false,'message'=>$message);
                        }
                    }
                }
            }
        }elseif ($type =='attr_sku_0'){
            $status = true;
            if(empty($val)){
                $status = false;
                $message = "Please enter atleast one attr_sku_0 ";
            }else{
                for($attr = 0; $attr <=9; $attr ++){
                    if(isset($prodata['attr_sku_'.$attr]) && empty($prodata['attr_sku_'.$attr]) && isset($prodata['attr_size_'.$attr]) && empty($prodata['attr_size_'.$attr])  && isset($prodata['attr_stock_'.$attr]) && empty($prodata['attr_stock_'.$attr]) && isset($prodata['attr_price_'.$attr]) && empty($prodata['attr_price_'.$attr])){

                    }else{
                        if(isset($prodata['attr_sku_'.$attr]) && !empty($prodata['attr_sku_'.$attr])){
                            if(strpos($prodata['attr_sku_'.$attr], ".") !== false) {
                                $status = false;
                                $message = "Invalid attr_sku_".$attr." or column not found ";
                                return array('status'=>false,'message'=>$message);
                            }
                            //Check for Size
                            if(isset($prodata['attr_size_'.$attr]) && !empty($prodata['attr_size_'.$attr])){
                                //nothing to do
                                if(strpos($prodata['attr_sku_'.$attr], ".") !== false) {
                                    $status = false;
                                    $message = "Invalid attr_size_".$attr." or column not found ";
                                    return array('status'=>false,'message'=>$message);
                                }
                            }else{
                            }
                            //Check for Stock
                            if(isset($prodata['attr_stock_'.$attr]) && !empty($prodata['attr_stock_'.$attr]) && is_numeric($prodata['attr_stock_'.$attr])){

                                if(strpos($prodata['attr_sku_'.$attr], ".") !== false) {
                                    $status = false;
                                    $message = "Invalid value for attr_stock_".$attr." or column not found ";
                                    return array('status'=>false,'message'=>$message);
                                }
                            }else{
                            }
                            //Check for Price
                            if(isset($prodata['attr_price_'.$attr]) && !empty($prodata['attr_price_'.$attr]) && is_numeric($prodata['attr_price_'.$attr])){
                                //nothing to do
                            }else{
                                /*$status = false;
                                $message = "Invalid value for attr_price_".$attr." or column not found ";
                                return array('status'=>false,'message'=>$message);*/
                            }
                        }
                    }
                }
            }
        }
        if($status){
            return array('status'=>true);
        }else{
            return array('status'=>false,'message'=>$message);
        }
    }

    function checkReviewFile($prodata,$type,$val){
        $status = true;
        if($type =="product_id"){
            $count =  DB::table('products')->where('id',$val)->count();
            if($count >0){
                $status = true;
            }else{
                $status = false;
                $message = "Invalid product_id";
            }
        }elseif($type =="user_id"){
            $count =  DB::table('users')->where('id',$val)->count();
            if($count >0){
                $status = true;
            }else{
                $status = false;
                $message = "Invalid user_id";
            }
        }elseif($type =="rating"){
            $status = true;
            if((empty($val) && $val ==0) || $val>5){
                $status = false;
                $message = "Please Enter valid rating "; 
            }
        }elseif($type =="status"){
            $status = true;
            $statusTypes = array('0','1');
            if(!in_array($val,$statusTypes)){
                $status = false;
                $message = "Invalid status value ";
            }
        }
        if($status){
            return array('status'=>true);
        }else{
            return array('status'=>false,'message'=>$message);
        }
    }
	
	function pd($data=[]){
		echo "<pre>"; print_r($data);die(); exit; 
	}

    function findPreviousId($id, $model) {
        // Dynamically construct the model class
        $modelClass = "\\App\\Models\\" . $model;

        // Ensure $id is an integer
        $id = (int) $id;

        // Check if the table is empty
        if ($modelClass::count() == 0) {
            return 0; // No records exist in the table
        }

        if ($id <= 1) {
            return 0; // No valid previous ID
        }

        $prevCount = $modelClass::where('id', $id-1)->count();
        if ($prevCount > 0) {
            return $id-1; // Found a valid previous ID
        }

        // Recursively check the next lower ID
        return findPreviousId($id - 1, $model);
    }

    function findNextId($id, $model) {
        // Dynamically construct the model class
        $modelClass = "\\App\\Models\\" . $model;

        $lastId = $modelClass::orderby('id','Desc')->first();

        // Check if the table is empty
        if ($modelClass::count() == 0 || $id==0 || $id>$lastId->id) {
            return 0; // No records exist in the table
        }

        $nextCount = $modelClass::where('id', $id+1)->count();
        if ($nextCount > 0) {
            return $id+1; // Found a valid next ID
        }

        // Recursively check the next higher ID
        return findNextId($id + 1, $model);
    }

    // for Widgets

    function categories(){
        $getcategories = Category::with(['subcategories'=>function($query){
            $query->with('subcategories');
        }])->where(['parent_id'=>0,'status'=>1])->orderby('sort','ASC')->get();
        $getcategories = json_decode(json_encode($getcategories),true);
        return $getcategories;
    }

    function rootCategories(){
        $getcategories = Category::where(['parent_id'=>0,'status'=>1])->orderby('sort','ASC')->get();
        $getcategories = json_decode(json_encode($getcategories),true);
        return $getcategories;
    }

    function products(){
        $products = Product::where('status',1)->select('id','product_name','product_code')->get();
        $products = json_decode(json_encode($products),true);
        return $products;
    }

    function getbrands(){
        $brands = DB::table('brands')->select('id','brand_name')->where('status',1)->orderby('brand_name','ASC')->get();
        $brands = json_decode(json_encode($brands),true);
        return $brands;
    }

    function widgetTypes(){
        return  [
            'MULTIPLE_BANNERS',
            'SINGLE_BANNER',
            'SINGLE_DESCRIPTIVE_BANNER',
            /*'SINGLE_BANNER_WITH_MULTIPLE_PRODUCTS',
            'SINGLE_VIDEO_WITH_MULTIPLE_PRODUCTS',*/
            'TAB_WISE_PRODUCTS',
            'MULTIPLE_PRODUCTS',
            'MULTIPLE_CATEGORIES',
            'PRODUCTS_FROM_CATEGORIES',
            'MULTIPLE_BRANDS',
            'THIRD_PARTY_SCRIPTS',
        ];
    }

    function widgetLinkTypes(){
        return [
            'Product',
            'Category',
            'Brand',
            'Search Page',
        ];
    }

    function ajaxSuccessResponse($message,$data=NULL){
        $success = [
            'status'       => true,
            'code'          => 200,
            'message'       => $message,
            'data'          => $data
        ];
        return $success;
    }
	
	function getMessage($messages){
		$result = [];
		$messages = json_decode(json_encode($messages),true);
		foreach($messages as $message){
			$result[] = $message;
		}
		return $result;
	}
	
	function from_input_error_message($field=''){
		return '<p class="error_message" id="input-error-'.$field.'"></p>';
	}
	