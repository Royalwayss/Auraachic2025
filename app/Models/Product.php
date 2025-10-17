<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Models\Category;
use App\Models\Fit;
use App\Models\Neck;
use App\Models\Occasion;
use App\Models\Sleeve;
use App\Models\Fabric;
use App\Models\WashCare;
use Session;
use Auth;

class Product extends Model
{
    use HasFactory;

    public function brand(){
        return $this->belongsTo('App\Models\Brand','brand_id');
    }

    public function category(){
        return $this->belongsTo('App\Models\Category','category_id')->with('parentcategory');
    }

    public function products_categories(){
        return $this->belongsToMany('App\Models\ProductsCategory','products_categories','product_id','category_id');
    }

    public static function productsFilters(){
        // Product Filters
        $productsFilters['fabricArray'] = array('Cotton','Polyester','Wool');
        $productsFilters['sleeveArray'] = array('Full Sleeve','Half Sleeve','Short Sleeve','Sleeveless');
        $productsFilters['patternArray'] = array('Checked','Plain','Printed','Self','Solid');
        $productsFilters['fitArray'] = array('Regular','Slim');
        $productsFilters['occasionArray'] = array('Casual','Formal');
        return $productsFilters;
    }

    public function images(){
        return $this->hasMany('App\Models\ProductsImage')->orderby('image_sort','ASC');
    }

    public function productimages(){
        return $this->hasMany('App\Models\ProductsImage','product_id')->orderby('image_sort','ASC');
    }

    public function product_image(){
        return $this->hasOne('App\Models\ProductsImage','product_id')->orderby('image_sort','ASC');
    }

    public function attributes(){
        return $this->hasMany('App\Models\ProductsAttribute')->orderby('sort','ASC');
    }

    public static function CheckProduct($id,$product_url=''){
        $productCount = Product::join('categories','categories.id','=','products.category_id')->where('products.id',$id);
		if($product_url != ''){
		  $productCount =  $productCount->where('products.product_url',$product_url);
		}
		$productCount =  $productCount->where('categories.status',1)->where('products.status',1)->count(); 
		
        if($productCount==0){
            abort(404);
        }
        /*$getCategoryId = Product::select('category_id')->where('id',$id)->first()->toArray();
        echo $getCategoryId['category_id']; die;
        $category_id = $getCategoryId['category_id'];*/
        $getproductdetails = Product::with(['brand','productimages','attributes','category'=>function($query){
            $query->with('subcat');
        },'relatedproducts'=>function($query) use($id){
            $query->where('products.id','!=',$id);
        },'groups'=>function($query) use($id){
            //$query->where('products.id','!=',$id);
        }])->where('products.id',$id);
		$getproductdetails  = $getproductdetails->select('products.*');
        $getproductdetails = $getproductdetails->where('products.status',1)->first();
        $getproductdetails = json_decode(json_encode($getproductdetails),true);
        /*echo "<pre>"; print_r($getproductdetails); die;*/
        $response = array('status'=>false);
        if(!empty($getproductdetails)){
            if($getproductdetails['category']['status']==1){
                $response = array('status'=>true,'productdetails'=>$getproductdetails);
            }
        }
        return $response;
    }

    public static function CheckProductQuickView($id){
        $productCount = Product::join('categories','categories.id','=','products.category_id')->join('brands','brands.id','=','products.brand_id')->where('products.id',$id)->where('categories.status',1)->where('brands.status',1)->where('products.status',1)->count(); 
        if($productCount==0){
            abort(404);
        }
        /*$getCategoryId = Product::select('category_id')->where('id',$id)->first()->toArray();
        echo $getCategoryId['category_id']; die;
        $category_id = $getCategoryId['category_id'];*/
        $getproductdetails = Product::with(['brand','productimages','attributes','category'=>function($query){
            $query->with('subcat');
        },'shades'=>function($query) use($id){
            $query->where('products.id','!=',$id);
        }])->where('products.id',$id);
        $getproductdetails  = $getproductdetails->select('products.*');
        $getproductdetails = $getproductdetails->where('products.status',1)->first();
        $getproductdetails = json_decode(json_encode($getproductdetails),true);
        /*echo "<pre>"; print_r($getproductdetails); die;*/
        $response = array('status'=>false);
        if(!empty($getproductdetails)){
            if($getproductdetails['category']['status']==1){
                $response = array('status'=>true,'productdetails'=>$getproductdetails);
            }
        }
        return $response;
    }

    public function relatedproducts(){
        return $this->hasMany('App\Models\Product','category_id','category_id')->select('id','category_id','status','product_name','product_url','product_discount','discount_type','final_price','product_price','main_image','is_featured','is_new')->where('status',1)->where('stock','>',0)->with(['product_image','category']);
    }

    /*public function groups(){
        return $this->hasMany('App\Models\Product','group_code','group_code')->select('id','product_color','status','group_code','product_name')->where('group_code','!=','')->where('status',1)->has('colordetails','>',0)->with(['colordetails','product_image']);
    }*/

    public function groups(){
        return $this->hasMany('App\Models\Product','group_code','group_code')->select('id','product_color','family_color','status','group_code','product_name','product_url')->where('group_code','!=','')->where('status',1)->with(['product_image']);
    }

    public function shades(){
        return $this->hasMany('App\Models\Product','group_code','group_code')->select('id','product_color','family_color','status','group_code','product_name','product_url','main_image')->where('group_code','!=','')->where('status',1)->with(['product_image']);
    }

    public function colordetails(){
        return $this->belongsto('App\Models\Color','product_color','color_name')->select('color_name');
    }

    public static function productURL($product_name){
        $product_name = strtolower($product_name);
        $product_name = str_replace(' ','-',$product_name);
        return $product_name;
    }

    // calculate Products Total after Discount
    public static function calculateProductsTotal(){
        if(Auth::check()){
            $user_id = Auth::user()->id;
            $userCart = Cart::where(['user_id' => $user_id])->get();   
        }else{
            $session_id = Session::get('cartsessionId');
            $userCart = Cart::where(['session_id' => $session_id])->get(); 
        }
        
        $userCart = json_decode(json_encode($userCart));
        $productTotal = 0;
        foreach($userCart as $item){
            // Get Product Price after deducting Product/Category/Brand Discount
            $priceArr = Product::getProductAttrPrice($item->product_id,$item->size);
            $productTotal = $productTotal + ($priceArr['final_price']*$item->qty);
        }
        return $productTotal;    
    }

    public static function getProductAttrPrice($product_id,$size){
        $proPrice = ProductsAttribute::where(['product_id' => $product_id, 'size' => $size])->first();
        /*$proPrice = json_decode(json_encode($proPrice));
        echo "<pre>"; print_r($proPrice); die;*/
        $proDetails = Product::select('product_discount','category_id')->where(['id' => $product_id])->first();
        $catDetails = Category::select('category_discount')->where(['id' => $proDetails['category_id']])->first();
        $final_price = 0;
        if($proDetails['product_discount'] > 0)
        {
            $final_price = $proPrice['price'] - ($proPrice['price']*$proDetails['product_discount']/100);
        } else if($catDetails['category_discount'] > 0) {
            $final_price = $proPrice['price'] - ($proPrice['price']*$catDetails['category_discount']/100);
        } else {
            $final_price = $proPrice['price'];
        }

        $mrp = sprintf("%.2f",$proPrice['price']);
        $final_price = sprintf("%.2f",$final_price);

        $discount = $mrp - $final_price;
        $discount = sprintf("%.2f",$discount);

        $finalpriceArr = array('final_price'=>$final_price,'mrp'=>$mrp,'discount'=>$discount);

        return $finalpriceArr;     
    }

    /*public static function getOrderedBySize(array $data): array {
        $result = [];
        foreach (["XXS", "XS", "S", "M", "L", "XL", "XXL", "2XL", "XXXL", "3XL"] as $key) {
            if (array_key_exists($key, $data)) {
                $result[$key] = $data[$key];
            }
        }
        return $result;
    }*/

    /*public static function getProductSize($product_id){
        $getProductSizeCount = ProductsAttribute::where(['product_id' => $product_id])->count();
        if($getProductSizeCount>0){
            $getProductSize = ProductsAttribute::select('size')->where(['product_id' => $product_id])->first(); 
            return $getProductSize->size;   
        }else{
            return null;
        }     
    }*/

    public static function getProductSize($product_id) {
        $getProductSize = ProductsAttribute::select('size')
            ->where('product_id', $product_id)
            ->where('stock', '>', 0) // Check for non-zero stock
            ->orderBy('id', 'asc') // Optional: Order by ID to get the first available size
            ->first();

        if ($getProductSize) {
            return $getProductSize->size;
        } else {
            return null; // No size with stock available
        }
    }

    public static function getOrderedBySize(array $data): array {
        $result = [];
        foreach (["XXS", "XS", "S", "M", "L", "XL", "XXL", "2XL", "XXXL", "3XL", "4XL"] as $key) {
            if (array_key_exists($key, $data)) {
                $result[$key] = $data[$key];
            }
        }

        return $result;
    }

    public static function getCategoryProducts($catid){
        $getCategoryProducts = Product::with(['productimages','product_image','category'])->where('category_id',$catid)->where('status',1)->orderBy('id','Desc')->limit(3)->get()->toArray();
        return $getCategoryProducts;
    }

    public static function getFeaturedProducts(){
        $getFeaturedProducts = Product::select('id','product_name','product_url','product_price','final_price','product_discount','discount_type','main_image','is_featured','is_new')->with(['productimages'])->where('is_featured','Yes')->where('status',1)->limit(6)->get()->toArray();
		return $getFeaturedProducts;
    }
	 public static function getNewArrivalProducts(){
        $getFeaturedProducts = Product::select('id','product_name','product_url','product_price','final_price','product_discount','discount_type','main_image','is_featured','is_new')->with(['productimages'])->where('is_new','Yes')->where('status',1)->limit(12)->get()->toArray();
		return $getFeaturedProducts;
    }
	public static function product_filters(){
        $fits = Fit::select('name')->orderby('id','asc')->get()->toArray(); 
        $necks = Neck::select('name')->orderby('id','asc')->get()->toArray(); 
        $occasions = Occasion::select('name')->orderby('id','asc')->get()->toArray(); 
        $sleeves = Sleeve::select('name')->orderby('id','asc')->get()->toArray(); 
        $fabrics = Fabric::select('name')->orderby('id','asc')->get()->toArray(); 
        $wash_cares = WashCare::select('name')->get()->toArray(); 
		
		$product_filters = [
		
		    ['key'=>'fit','label'=>'Fits','list'=>array_column($fits, 'name')],
		    ['key'=>'neck','label'=>'Necks','list'=>array_column($necks, 'name')],
		    ['key'=>'occasion','label'=>'Occasions','list'=>array_column($occasions, 'name')],
		    ['key'=>'sleeve','label'=>'Sleeves','list'=>array_column($sleeves, 'name')],
		    ['key'=>'fabric','label'=>'Fabrics','list'=>array_column($fabrics, 'name')],
		    ['key'=>'wash_care','label'=>'Wash Care','list'=>array_column($wash_cares, 'name')],
		
		];
		return $product_filters;
    }
}
