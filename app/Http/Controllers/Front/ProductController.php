<?php

namespace App\Http\Controllers\Front;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Brand;
use App\Models\ProductsFilter;
use App\Models\Product;
use App\Models\ProductsAttribute;
use App\Models\SavedProduct;
use App\Models\Rating;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\State;
use App\Models\ShippingAddress;
use App\Models\BillingAddress;
use App\Models\Order;
use App\Models\OrdersProduct;
use App\Models\OrdersAddress;
use App\Models\OrdersHistory;
use App\Models\SearchResult;
use App\Models\User;
use App\Models\Ups;
use App\Models\PushOrderLog;
use App\Models\Wishlist;
use App\Models\UsersCredit;
use App\Models\CustomFit;
use App\Models\Mails;
use Validator;
use Session;
use Auth;
use DB;
use App\Models\StockAlert;
use App\Models\PaymentMethod;
use App\Models\RecentViewProduct;
use App\Models\WhatsappApi;
use App\Models\ReturnRequest;
use Carbon\Carbon;

class ProductController extends Controller
{

    public function cart(){
        Coupon::checkCouponStatus();
        $title ="Cart";
        $meta_title ="";
        $meta_description="";
        $cartitems = Cart::cartitems(); 
        $availableCoupons = Coupon::availableCoupons($cartitems); 
        Session::forget('previousurl');
        return view('front.pages.products.cart.cart')->with(compact('title','meta_title','meta_description','cartitems','availableCoupons'));
    }

	public function listing(Request $request){
        $catseo = Route::getFacadeRoot()->current()->uri();
        $countBrand = Brand::countBrand($catseo);
        $response = Category::getcatdetails($catseo); 
        $sortname ="";$selPrice="";$proage="";
        if($response['status']){
            Session::put('previousurl',$catseo);
			$listing_type = "Categories";
            $catids = $response['catids'];
            //dd($catids);
            $getfilters = ProductsFilter::getfilters($catids);
            $topfilters = ProductsFilter::topfilters($catids);
            $getproducts = Product::with(['product_image'])->where('products.stock','>',0)->where('products.status',1)->join('categories','categories.id','=','products.category_id')->join('products_attributes', 'products_attributes.product_id', '=', 'products.id')->where('products_attributes.stock', '>', 0)->select('products.*','categories.category_discount',DB::raw("(case when products.product_discount = 0 then categories.category_discount else  products.product_discount end ) as 'item_discount'"));
            
			if($catseo == 'new-arrival'){
				$getproducts = $getproducts->where('products.is_new','Yes');
			}
			if($catseo == 'featured-collection'){
				$getproducts = $getproducts->where('products.is_featured','Yes');
			}
			
			if($request->isMethod('get')){

                $data = $request->all();

                $getproducts = $getproducts->leftjoin('products_categories','products_categories.product_id','=','products.id')->join('categories as cats','cats.id','=','products_categories.category_id')->wherein('products_categories.category_id',$catids)->groupby('products_categories.product_id');

				
				if(isset($data['fabric']) && !empty($data['fabric'])){
                    $fabrics = $data['fabric'];
                    $fabrics = explode('~',$fabrics);
                    $getproducts->wherein('products.fabric',$fabrics); 
                }
				
				if(isset($data['neck']) && !empty($data['neck'])){ 
                    $necks = $data['neck'];
                    $necks = explode('~',$necks);
                    $getproducts->wherein('products.neck',$necks); 
                }
				
				if(isset($data['color']) && !empty($data['color'])){ 
                    $family_colors = $data['color'];
                    $family_colors = explode('~',$family_colors);
                    $getproducts->wherein('products.family_color',$family_colors); 
                }
				
                if(isset($data['size']) && !empty($data['size'])){
                    $prosize = $data['size'];
                    $prosizes = explode('~',$prosize);
                    $getproducts->wherein('products_attributes.size',$prosizes)->where('products_attributes.status',1)->groupby('products_attributes.product_id'); 
                }
               
                if(isset($data['price']) && !empty($data['price'])){
                    $selPrice =  $data['price'];
                    $priceArr = explode('~',$data['price']);
                    $getproducts = $getproducts->where(function($q) use($priceArr) {
                        $price0Explode =  explode('-',$priceArr[0]);
                        $q->whereBetween('products.final_price', [$price0Explode[0], $price0Explode[1]]);
                        if(isset($priceArr[1])){
                          $price1Explode =  explode('-',$priceArr[1]);
                            $q->orwhereBetween('products.final_price', [$price1Explode[0], $price1Explode[1]]);  
                        }
                        if(isset($priceArr[2])){
                          $price2Explode =  explode('-',$priceArr[2]);
                            $q->orwhereBetween('products.final_price', [$price2Explode[0], $price2Explode[1]]);  
                        }
                        if(isset($priceArr[3])){
                          $price3Explode =  explode('-',$priceArr[3]);
                            $q->orwhereBetween('products.final_price', [$price3Explode[0], $price3Explode[1]]);  
                        }
                    });
                }
                if(isset($data['sort']) && !empty($data['sort'])){
                    $sortname =$data['sort'];

                    if($sortname=="out-of-stock"){
                        $getproducts = $getproducts->where('stock','=',0);
                    }else if($sortname=="in-stock"){
                        $getproducts = $getproducts->where('stock','>',0);
                    }else if($sortname=="new-arrivals"){
                        $getproducts = $getproducts->where('products.new_arrival','yes');
                    }else if($sortname=="featured"){
						$getproducts = $getproducts->where('products.is_featured','Yes');
                    }else if($sortname=="new-arrival"){
                       $getproducts = $getproducts->where('products.is_new','Yes'); 
                    }else if($sortname=="asc"){
                        $getproducts = $getproducts->orderby('products.product_name','asc');
                    }else if($sortname=="desc"){
                        $getproducts = $getproducts->orderby('products.product_name','Desc');
                    }else if($sortname=="lth"){
                        $getproducts = $getproducts->orderby('products.final_price','ASC');
                    }else if($sortname=="htl"){
                        $getproducts = $getproducts->orderby('products.final_price','DESC');
                    }else if($sortname=="best"){
                        $getproducts = $getproducts->where('products.best_seller','yes');
                    }else if($sortname=="stockasc"){
                        $getproducts = $getproducts->orderBy('stock_count','ASC');
                    }else if($sortname=="stockdesc"){
                        $getproducts = $getproducts->orderBy('stock_count','DESC');
                    }else if($sortname=="discounted"){
                        $getproducts = $getproducts->wherein('discount_type',['category','product'])->orderBy('item_discount','DESC');
                    }else if($sortname=="popular"){
                       
                        $getproducts = $getproducts->orderby('products.product_sort','ASC');
                    }
                }else{
                    $getproducts = $getproducts->orderby('products.product_sort','ASC');
                }
            }
            
          
           
            $getproducts = $getproducts->whereExists( function ($query)  {
                $query->from('categories')
                ->whereRaw('products.category_id = categories.id')
                ->where('status','1');
            })->paginate(9); 
            $all_products = json_decode(json_encode($getproducts),true);  
             $pagination_links = $all_products['links']; 
            $products = $getproducts->appends(request()->except('page')); 
            //$users->appends(request()->input())->links();
            $title = $response['catdetail']['meta_title'];
            $meta_title = $response['catdetail']['meta_title'];
            if(empty($title)){
                $title = $response['catdetail']['category_name'];    
                $meta_title = $response['catdetail']['category_name'];    
            }
            $meta_description = $response['catdetail']['meta_description'];
            $meta_keyword = $response['catdetail']['meta_keywords'];
            $catdetails  = $response['catdetail'];
            $breadcrumbs  = $response['breadcrumbs'];
            $total_products  = count($products);
            /*echo "<pre>"; print_r($getproducts); die;*/
        }else{
            abort(404);
        }
        if($request->ajax()){
			$ajax_call = true;
            return response()->json([
                'view' => (String)View::make('front.pages.products.listing.include.product-list')->with(compact('products','catseo','catdetails','pagination_links','sortname','catseo','listing_type','catids','getfilters','ajax_call')),
                'total_products' => count($products)
            ]);
        }else{  
            return view('front.pages.products.listing.index')->with(compact('catdetails','catseo','products','pagination_links','total_products','title','meta_title','meta_keyword','meta_description','sortname','selPrice','proage','catids','getfilters','topfilters','catseo','breadcrumbs','listing_type'));
        }
    }

    public function detail($product_url,$id){ 
        $response = Product::CheckProduct($id,$product_url);
        /*echo "<pre>"; print_r($response); die;*/
        if(empty($response['status'])){
             abort(404); die; exit;
        }
        $getcatdetails = Category::getcatdetails($response['productdetails']['category']['url']);
        $breadcrumbs  = @$getcatdetails['breadcrumbs'];
        $size_chart = $getcatdetails['catdetail']['size_chart'];
        /*echo "<pre>"; print_r($getcatdetails); die;*/
        if(isset($response['productdetails']['discount_type'])&&$response['productdetails']['discount_type']=="category"){
            $response['productdetails']['product_discount'] = $response['productdetails']['category']['category_discount'];  
        }
        if($response['status']){ 
            $getProductURL = $response['productdetails']['product_url'];
            Session::put('previousurl',"/product/".$getProductURL."/".$response['productdetails']['id']);
            $productdetails = $response['productdetails'];
            //echo "<pre>"; print_r($productdetails); die;
            $title = $productdetails['product_name'];
            $meta_title = "Buy ".$title." - ".config('constants.project_name')."";
            $meta_description = "Shop ".$title." - ".config('constants.project_name')."";
            //RECENT VIEW ITEMS
            if(Session::has('recentSession')){
                Session::get('recentSession');
            }else{
                $recentSession = Session::getId();
                Session::put('recentSession',$recentSession);
            }
            //Save Recent Item
            $check = RecentViewProduct::where('session_id',Session::get('recentSession'))->where('product_id',$productdetails['id'])->count();
            if($check ==0){
                $recent = new RecentViewProduct;
                $recent->session_id = Session::get('recentSession');
                $recent->product_id = $productdetails['id'];
                $recent->save();
            }
            $recentitems = RecentViewProduct::with(['product'=>function($query){
                $query->with(['productimages','product_image','category']);
            }])->where('session_id',Session::get('recentSession'))->where('product_id','!=',$productdetails['id'])->orderby('id','DESC')->get()->toArray();
            /*echo "<pre>"; print_r($recentitems); die;*/

            // Get Average Rating of product
            $getProductRating = Rating::getProductRating($id);
            $avgRating = $getProductRating['avgRating'];
            $avgStarRating = $getProductRating['avgStarRating'];
            $ratingCount = $getProductRating['ratingCount'];
        
            //Reviews
            $reviews = Rating::where('status','1')->where('product_id',$productdetails['id'])->orderby('id','DESC')->get();
            if($reviews->count() > 0){
                $reviews = json_decode(json_encode($reviews), true);
            }
           
            $getCategoryId = Product::select('category_id')->where('id',$id)->first()->toArray();
            $category_id = $getCategoryId['category_id'];

            $getParentId = Category::select('parent_id')->where('id',$category_id)->first()->toArray();
            $parent_id = $getParentId['parent_id'];
            
			
            return view('front.pages.products.details.detail')->with(compact('title','meta_title','meta_description','productdetails','reviews','avgRating','avgStarRating','ratingCount','breadcrumbs','size_chart','recentitems'));
        }else{
            return redirect('/');
        }
    }

   
    public function productquickview(request $request){
		$data = $request->all(); 
		$id = @$data['id'];
        $response = Product::CheckProduct($id);
		if($id && $response['status']){
			$productdetails = $response['productdetails']; 
			$getProductRating = Rating::getProductRating($id);
            $avgRating = $getProductRating['avgRating'];
            $avgStarRating = $getProductRating['avgStarRating'];
            $ratingCount = $getProductRating['ratingCount'];
			$html = (String)View::make('front.pages.products.details.include.quick-view')->with(compact('productdetails','avgStarRating','ratingCount'));
			return response()->json(['status'=>true,'html'=>$html]);
		}else{
			return response()->json(['status'=>false]);
		}			
			
	}


   public function writeReview(Request $request){
        if(Auth::check()){  
            $data = $request->all(); 
            $product_id = $data['product_id'];
            $productCheck = Product::select('id')->where('id',$product_id)->first();
            $productCheck = json_decode(json_encode($productCheck), true);
    
             $validator = Validator::make($request->all(), 
			 [
                'rating' => 'required',
                'review_title' => 'required',
                'review' => 'required'
              ], 
			  [
                'rating.required' => 'Please select stars to rate product.',
                'review_title.required' => 'Please enter title',
                'review' => 'Please enter your message.',
              ]);

           if($validator->passes()) {
				$reviewCount = Rating::where(['user_id'=>Auth::user()->id,'product_id'=>$product_id])->count();
				if($reviewCount>0){
				   // return redirect()->back()->with('flash_message_error', 'Review is already added by you for this Product.');
				}
		
				$title = $data['review_title'];
				$review = $data['review'];
				if($data['rating'] != ""){
					$starrating = $data['rating'];
				}
				else{
					$starrating = 4;
				}
				
		
				
					$star_rating = new Rating;
					$star_rating->product_id = $product_id;
					$star_rating->user_id = Auth::user()->id;
					$star_rating->name = Auth::user()->name;
					$star_rating->email = Auth::user()->email;
					$star_rating->title = $title;
					$star_rating->review = $review;
					$star_rating->rating = $starrating;
					$star_rating->save();
					return response()->json(['status'=>true,'message'=>'Your review has been submitted successfully.']);
					
				
				
		  }else{
                return response()->json(['status'=>false,'type'=>'validation','errors'=>$validator->messages()]);
            }
			
        }else{
			 return response()->json(['status'=>false]);
		}
		
    }
	
	
	
	 public function savecustomfit(Request $request){
        
            $data = $request->all(); 
           
             $validator = Validator::make($request->all(), 
			   [
                'title' => 'required',
				'mobile' => 'required|numeric|digits:10',
                'message' => 'required'
               ], 
			   [
                'title.required' => 'Please enter the title.',
                'mobile.required' => 'Please enter the mobile number',
                'message' => 'Please enter your message.',
               ]);

           if($validator->passes()) {
				
					$custom_fit = new CustomFit;
					$custom_fit->product_id = $data['product_id'];
					$custom_fit->title = $data['title'];
					$custom_fit->mobile = $data['mobile'];
					$custom_fit->message = $data['message'];
					$custom_fit->save();
					Mails::customfit_mail($custom_fit->id);
					return response()->json(['status'=>true,'message'=>'Thank you for reaching out. Our team will review your request and get back to you soon.']);
					
		  }else{
                return response()->json(['status'=>false,'type'=>'validation','errors'=>$validator->messages()]);
            }

    }
	
	
	

    public function searchProduct(Request $request){
		$query = $request->get('keyword'); 

		$searchCount = SearchResult::where('query',$query)->count();
		
		// Insert Search Result
		$search = new SearchResult;
		$search->query = $query;
		if(Auth::check()){
			$search->user_id = Auth::user()->id;
		}
		$search->count = $searchCount+1;
		$search->save();

		
		$terms = explode(' ', str_replace(['-', '_'], ' ', $query)); // Split terms and normalize spaces

       $products = Product::with(['product_image'])->where('products.stock','>',0)->where('products.status',1)->join('categories','categories.id','=','products.category_id')->join('products_attributes', 'products_attributes.product_id', '=', 'products.id')->where('products_attributes.stock', '>', 0)->select('products.*','categories.category_discount',DB::raw("(case when products.product_discount = 0 then categories.category_discount else  products.product_discount end ) as 'item_discount'"))
            
            ->where(function ($queryBuilder) use ($terms) {
                foreach ($terms as $term) {
                    if (!empty($term)) {
                        $queryBuilder->whereRaw("REPLACE(product_name, '-', '') LIKE ?", ['%' . $term . '%'])
                                     ->orWhereRaw("REPLACE(product_code, '-', '') LIKE ?", ['%' . $term . '%']);
                    }
                }
            })
            ->groupby('products.id') // Limit to 10 results
            ->limit(100) // Limit to 10 results
            ->get(); 
		 $products = json_decode(json_encode($products), true); 
		 $total_products = count($products);
		 $catdetails = [];
		 $catseo = [];
		 $breadcrumbs = ' <li>Search Products</li><li> <svg class="svg-inline--fa fa-angle-right" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="angle-right" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" data-fa-i2svg=""><path fill="currentColor" d="M278.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-160 160c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L210.7 256 73.4 118.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l160 160z"></path></svg><!-- <i class="fa-solid fa-angle-right"></i> Font Awesome fontawesome.com --> </li><li>'.$request->get('keyword').'</li>';
		 return view('front.pages.products.listing.index')->with(compact('catdetails','catseo','products','total_products','catseo','breadcrumbs'));
		
	}
	
	
	
	
    public function detailProPrice(Request $request){
        $data = $request->all();
        /*echo "<pre>"; print_r($data); die;*/
        $proPrice = ProductsAttribute::where(['product_id' => $data['product_id'], 'size' => $data['size']])->first();
        $proDetails = Product::select('product_discount','category_id')->where(['id' => $data['product_id']])->first();
        $catDetails = Category::select('category_discount')->where(['id' => $proDetails['category_id']])->first();
        $discounted_price = 0;
        if($proDetails['product_discount'] > 0)
        {
            $discounted_price = $proPrice['price'] - ($proPrice['price']*$proDetails['product_discount']/100);
            $discount = $proDetails['product_discount'];
		} else if($catDetails['category_discount'] > 0) {
            $discounted_price = $proPrice['price'] - ($proPrice['price']*$catDetails['category_discount']/100);
            $discount = $proDetails['category_discount'];
		}

        /*$proPrice['price'] = sprintf("%.2f",$proPrice['price']);
        $discounted_price = sprintf("%.2f",$discounted_price);*/

        $product_price = $proPrice['price'];
        $final_price = $discounted_price; 
        $product_price_details = '';
		$product_price_details = 'MRP';
		$product_price_details .= '<span class="product-price regular-price product_final_price">&nbsp;₹'.round($final_price,2).'&nbsp;</span>';
		if($discounted_price>0){
		$product_price_details .='<del class="product-price compare-price">₹'.round($product_price,2).'</del>';
		}
	   if($discounted_price>0){
		$product_price_details .='<span class="text-include">&nbsp;&nbsp;'.$discount.'% off (Incl. of all taxes)</span>';
	   }
	   if(auth::check()){
		   $wishlist_count = Wishlist::where(['user_id'=>Auth::user()->id,'product_id' =>$data['product_id'],'size' => $data['size']])->count();
		   if(!empty($wishlist_count)){
				$wishlist_check = 1;
		   }else{
				$wishlist_check = 0;
		   }
	   }else{
		   $wishlist_check = 0;
	   }
	   
	   return response()->json(['status'=>true,'product_price_details'=>$product_price_details,'wishlist_check'=>$wishlist_check]);
	   
		
							
							
							
						
		
        //echo $proPrice['price']."#".$discounted_price;
    }

    public function addtoCart(Request $request){
        if($request->isMethod('post')){
            $data =  $request->all();
            /*echo "<pre>"; print_r($data); die;*/
            $data['proid'] = $data['product_id'];
            $validator = Validator::make($request->all(), [
                    'size' => 'bail|required',
                    'qty'  => 'bail|required|numeric'
                ],
                [
                    'size.required' => 'Please select size',
                    'size.in' => 'Selected size is out of stock',
                ]
            );
            if($validator->passes()) {
                $checkStockDetails = ProductsAttribute::attributeDetail($data['proid'],$data['size']);
                if(empty($data['qty'])){
                    $data['qty']=1;
                }
                if($data['qty'] > $checkStockDetails->stock){
                    return response()->json(['status'=>false,'type'=>'validation','errors'=>array("We're sorry! The requested quantity not available at this moment")]);
                }
                //Check product Status
                $product = Product::where('status',1)->where('id',$data['proid'])->count();
                if($product ==0){
                    return response()->json(['status'=>false,'type'=>'validation','errors'=>array('Product is not available at the moment')]);
                }
                if(Auth::check()){
                    Cart::where(['product_id'=>$data['proid'],'size'=>$data['size'],'user_id'=>Auth::user()->id])->delete();
                }else{
                    Cart::where('session_id',Session::get('cartsessionId'))->where(['product_id'=>$data['proid'],'size'=>$data['size']])->delete();
                }
                $checkcart = Cart::where(['product_id'=>$data['proid'],'size'=>$data['size']]);
                if(Auth::check()){
                    $checkcart = $checkcart->where('user_id',Auth::user()->id);
                }else{
                    $checkcart = $checkcart->where('session_id',Session::get('cartsessionId'));
                }
                $checkcart = $checkcart->first();
                $checkcart = json_decode(json_encode($checkcart),true);
                //Session::forget('couponinfo');
                //Pushing item in cart
                $todayDate = date('Y-m-d');
                $expiry_date = date('Y-m-d', strtotime("+7 days", strtotime($todayDate)));
                if(!Session::has('cartsessionId')){
                    $session_id = Session::getId();
                    Session::put('cartsessionId',$session_id);
                }
                $cartmessage = 'Product added successfully in Cart! <a style="text-decoration:underline !important;" href="'.route('cart').'">View Cart</a>';
                if(empty($checkcart)){
                    $cart = new Cart;
                }else{
                    $cart = Cart::find($checkcart['id']); 
                }
                $cart->session_id = (Auth::check()) ? '' : Session::get('cartsessionId');
                $cart->product_id = $data['proid'];
                $cart->size = $data['size'];
                $cart->qty = $data['qty'];
                $cart->expiry_date = $expiry_date;
                if(Auth::check()){
                    $cart->user_id = Auth::user()->id;
                }
                $cart->save();
                $wishlist = [];
				if(isset($data['wishlist_id']) && !empty($data['wishlist_id'])){
					Wishlist::where(['id'=>$data['wishlist_id'],'user_id'=>Auth::user()->id])->delete();
					$wishlists =Wishlist::wishlists();
				    $wishlists = json_decode(json_encode($wishlists),true); 
					$wishlist['html'] = (String)View::make('front.pages.account.wishlist-list')->with(compact('wishlists')); 
					$wishlist['count'] = count($wishlists);
				}
				$cartitems = Cart::cartitems();
				$totalItems = $cartitems['totalCartItems'];
				$cart_popup = (String)View::make('front.pages.products.cart.include.cart-popup')->with(compact('cartitems'));
                return response()->json(['status'=>true,'message'=>$cartmessage,'totalitems'=>$totalItems,'cart_popup'=>$cart_popup,'product_id'=>$data['proid'],'wishlist'=>$wishlist]);
            }else{
                $cartitems = Cart::cartitems();
				$totalItems = $cartitems['totalCartItems'];
				$cart_popup = (String)View::make('front.pages.products.cart.include.cart-popup')->with(compact('cartitems'));
				$error_message = getMessage($validator->messages());
				return response()->json(['status'=>false,'type'=>'validation','cart_popup'=>$cart_popup,'totalItems'=>$totalItems,'errors'=>$error_message]);
            }
        }
    }

  
    public function deleteCartItem(Request $request){
        if($request->ajax()){
            $data = $request->all();
            $cart = Cart::where('id',$data['cartid']);
			if(Auth::check()){
				 $cart =  $cart->where('user_id',Auth::user()->id);
			}else{
				$cart =  $cart->where('session_id',Session::get('cartsessionId')); 
			}
			$cart =  $cart->delete();
            $cartitems = Cart::cartitems(); 
			$totalCartItems = $cartitems['totalCartItems'];
			return response()->json([
				'status'=>true,
				'totalCartItems'=>$totalCartItems,
				'message' =>array('Cart item has been deleted successfully'),
				'view'=>(String)View::make('front.pages.products.cart.include.cart_details')->with(compact('cartitems')),
				'cart_popup'=>(String)View::make('front.pages.products.cart.include.cart-popup')->with(compact('cartitems'))
			]);
        }
    }

    public function updateCartItem(Request $request){
        if($request->ajax()){
            $data = $request->all(); 
            if(Auth::check()){
                  $cartDetails = Cart::where('id',$data['cartid'])->where('user_id',Auth::user()->id)->first();
            }else{
                  $cartDetails = Cart::where('id',$data['cartid'])->where('session_id',Session::get('cartsessionId'))->first();
            }
            
            $availableStockCount = ProductsAttribute::select('stock')->where(['product_id'=>$cartDetails['product_id'],'size'=>$cartDetails['size']])->count();
            if($availableStockCount==0){
                $result  = ['status'=>false,'message'=>'Product Stock is not available'];   
            }else{
				
                $availableStock = ProductsAttribute::select('stock')->where(['product_id'=>$cartDetails['product_id'],'size'=>$cartDetails['size']])->first()->toArray();
				if($data['qty']>$availableStock['stock']){
                   $result  = ['status'=>false,'message'=>'Product Stock is not available'];   
				}else{
					    if($data['qty'] > 0){
                            $availableSize = ProductsAttribute::where(['product_id'=>$cartDetails['product_id'],'size'=>$cartDetails['size'],'status'=>1])->count();
                            if($availableSize==0){
                                $result  = ['status'=>false,'message'=>'Product Size is not available. Please remove this Product and choose another one!'];   
                            }else{
                                Session::forget('couponinfo');
								Cart::where('id',$data['cartid'])->update(['qty'=>$data['qty']]);   
                                $result  = ['status'=>true,'message'=>'Quantity has been updated successfully'];   
                                
                            }
                        }else{ 
                            Session::forget('couponinfo');
							Cart::where('id',$data['cartid'])->delete();   
                            $result  = ['status'=>true,'message'=>'Cart item has been deleted successfully'];   
                        
                        }



				}
				
			}
			
			            $cartitems = Cart::cartitems(); 
			            $totalCartItems = $cartitems['totalCartItems'];
						
						
						
						$response['status'] = $result['status'];
						$response['totalCartItems'] = $totalCartItems;
						$response['message'] = array($result['message']);
						if($data['cartpage'] == 1){
						$response['view'] = (String)View::make('front.pages.products.cart.include.cart_details')->with(compact('cartitems'));
						}
						$response['cart_popup'] = (String)View::make('front.pages.products.cart.include.cart-popup')->with(compact('cartitems'));
						return response()->json($response);
			
			
 
        }
    }

    public function applyCoupon(Request $request){
        if($request->ajax()){
            if(Auth::check()){
                $data = $request->all();
                /*echo "<pre>"; print_r($data); die;*/
                $validator = Validator::make($request->all(), [
                        'coupon' => 'bail|required',
                    ]
                );
                if($validator->passes()){
                    if(is_numeric($data['coupon'])){
                        $response = Coupon::applycredit($data['coupon']);
                    }else{
                        $response = Coupon::applycouponcode($data['coupon']);    
                    } 
                    $cartitems = Cart::cartitems();
                    return response()->json([
                        'status'=>$response['status'],
                        'message' =>array($response['message']),
                        'view' => (String)View::make('front.pages.products.cart.include.cart_details')->with(compact('cartitems'))
                    ]);
                }else{
                    Session::forget('couponinfo');
                    $cartitems = Cart::cartitems();
                    $totalItems = count($cartitems);
                    return response()->json([
                        'status'=>false,
                        'view' => (String)View::make('front.pages.products.cart.include.cart_details')->with(compact('cartitems')),
                        'message' =>'Coupon Code is required'
                    ]);
                }
            }else{
                $cartitems = Cart::cartitems();
                $totalItems = count($cartitems);
                return response()->json([
                    'status'=>false,
                    'type' =>'login',
                    'message' =>'You need to login to apply this coupon'
                ]);
            }
        }
    }

    public function orderCheckout(){
        $cartitems = Cart::cartitems();
        if(!$cartitems){
            return redirect('cart')->with('flash_message_error','Please add products in cart before checkout.');
        }
        $title ="Order Checkout";
        $metakeywords ="";
        $metadescription="";
        $currencyinfo = currencyinfo();
        $states = State::orderby('name','ASC')->pluck('name')->toArray();
        $deliveryAddressCount = ShippingAddress::where('user_id',Auth::user()->id)->count();
        $codPincodeCount = 0;
        $prepaidPincodeCount = 0;
        if($deliveryAddressCount>0){
            $deliveryAddress = ShippingAddress::select('postcode')->where('user_id',Auth::user()->id)->first();
            $codPincodeCount = DB::table('cod_zipcodes')->where('zipcode',$deliveryAddress->postcode)->where('is_available','yes')->count(); 
            $prepaidPincodeCount = DB::table('prepaid_zipcodes')->where('zipcode',$deliveryAddress->postcode)->where('is_available','yes')->count();   
        }
        $currencyinfo = currencyinfo();
        $paymentMethods = PaymentMethod::where('status',1)->orderby('sort')->get()->toArray(); 
        return view('front.pages.products.checkout.checkout')->with(compact('title','metakeywords','metadescription','cartitems','states','paymentMethods'));
    }

    public function placeOrder(Request $request){
        if($request->isMethod('post')){
            $data = $request->all();
            
             /*$invoice_no = Order::get_invoice_no();
             $invoice_date = date('Y-m-d'); */
            
            /*Session::flash('message', 'This is a message!'); 

            if(Session::has('message')){
                echo Session::get('message');
            }*/

            $deliveryAddressCount = ShippingAddress::where('user_id',Auth::user()->id)->count();
            if($deliveryAddressCount==0){
                return redirect()->back()->with('flash_message_error','Please add your Delivery Address!');
            }
			
			$billingAddressCount = BillingAddress::where('user_id',Auth::user()->id)->count();
            if($billingAddressCount==0){
                return redirect()->back()->with('flash_message_error','Please add your Billing Address!');
            }
			

            if(!isset($data['agree'])){
                return redirect()->back()->with('flash_message_error','Please agree to our T&C!');
            }

            if(!isset($data['paymentMode'])){
                return redirect()->back()->with('Please select Payment Method!');
            }

            $validator = Validator::make($request->all(), [
                    'paymentMode' => 'required|string|max:255',
                ]);
            

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors());
            }

            $deliveryAddressCount = ShippingAddress::where('user_id',Auth::user()->id)->count();
            $codPincodeCount = 0;
            $prepaidPincodeCount = 0;

           

            $cartitems = Cart::cartitems(); 
            if(!$cartitems['items']){
                return redirect('cart')->with('flash_message_error','Please add products in cart before placing an order.');
            }
            $shippingAddress = DB::table('shipping_addresses')->where('user_id',Auth::user()->id)->where('is_default',1)->first();
            $billingAddress = DB::table('billing_addresses')->where('user_id',Auth::user()->id)->first();
            if(empty($shippingAddress) || empty($billingAddress)){
                return redirect('checkout')->with('flash_message_error','Address is not Selected');
            }else{

                $total_weight = 0;
                foreach($cartitems['items'] as $cartitem){
                    /*echo "<pre>"; print_r($cartitem); die;*/
                    $total_weight = $total_weight + $cartitem['product']['product_weight'];
                }

                if($data['paymentMode']=="COD"){
                    $cod = 1;
                }else{
                    $cod = 0;
                }

                $delivery_postcode = $shippingAddress->postcode;
               
            }

            if(isset($data['paymentMode'])){

            }else{
                return redirect()->back()->with('flash_message_error','Please select Payment Method');
            }
            $paymentModeArr = array('COD','payu','ccavenue','phonepe','Razorpay','moneris','CCAvenue'); 
            if(isset($data['paymentMode']) && in_array($data['paymentMode'],$paymentModeArr)){
                $paymentGateway = $data['paymentMode'];

                if($data['paymentMode'] =='payu' || $data['paymentMode'] =='Razorpay'){
                    /*echo "Payment Gateway coming soon.. Please check back later"; die;*/
                    $paymentStatus = "cancelled";
                    $orderstatus = "Cancelled";
                    $comments = "Payment has not been made";
                }else if($data['paymentMode'] =='CCAvenue'){
                    /*echo "Payment Gateway coming soon.. Please check back later"; die;*/
                    $paymentStatus = "cancelled";
                    $orderstatus = "Cancelled";
                    $comments = "Payment has not been made";
                }else if($data['paymentMode'] =='moneris'){
                    /*echo "CCAvenue Payment Gateway coming soon.. Please check back later"; die;*/
                    $paymentStatus = "cancelled";
                    $orderstatus = "Cancelled";
                    $comments = "Payment has not been made";
                }else if($data['paymentMode'] =='ccavenue'){
                    /*echo "CCAvenue Payment Gateway coming soon.. Please check back later"; die;*/
                    $paymentStatus = "cancelled";
                    $orderstatus = "Cancelled";
                    $comments = "Payment has not been made";
                }else if($data['paymentMode'] =='phonepe'){
                    /*echo "Phonepe Payment Gateway coming soon.. Please check back later"; die;*/
                    $paymentStatus = "cancelled";
                    $orderstatus = "Cancelled";
                    $comments = "Payment has not been made";
                }else{
                    $paymentStatus ="COD";
                    $orderstatus = "Confirmed";
                    $comments = "COD order created";
                }
            }else{
                return redirect()->back()->with('flash_message_error','Something went wrong!. Please try again');
            }
            //Get Cart details grand total, discount, subtotal
            Coupon::checkCouponStatus(); 
            $cartDetails = $cartitems['cartPricing']; 

            // old code for product wise gst (disabled)
            $gst = Cart::calculateTotalGST($cartitems['items'],$data['paymentMode']);

            /*echo "<pre>"; print_r($gst); die;*/

            if(empty(Auth::user()->mobile)){
                User::where('email',Auth::user()->email)->update(['mobile'=>$shippingAddress->mobile]);
                $billing_mobile = $shippingAddress->mobile;
            }else{
                $billing_mobile = Auth::user()->mobile;
            }

            DB::beginTransaction();

            if(!(Session::has('couponinfo'))){
                $cartDetails['couponcode'] = "";
                $cartDetails['discount'] = "";                
            }else{
                $cartDetails['couponcode'] = Session::get('couponinfo')['coupon_code'];
            }

            if(empty($data['comments'])){
                $data['comments'] = "";
            }
             
            
            if($data['paymentMode']!="COD"){
                $cartDetails['grandtotal'] = $cartDetails['grandtotal'] - $cartDetails['cod_charges'];
                $cartDetails['grandtotalString'] = "₹ ".$cartDetails['grandtotal'];
                $cartDetails['cod_charges'] = 0;
                $cartDetails['CODChargesString'] = "₹ ".$cartDetails['cod_charges'];
            }
            /*echo $data['paymentMode'];
            echo "<pre>"; print_r($cartDetails); die;*/
            
           
            if(Auth::user()->name=="Guest" || Auth::user()->name==""){
                Auth::user()->name = $shippingAddress->name;
                $nameArr = explode(" ",Auth::user()->name);
                Auth::user()->first_name = $nameArr[0];
                if(!empty($nameArr[1])){
                    Auth::user()->last_name = $nameArr[1];    
                } 
                // Update User Details
                User::where('email',Auth::user()->email)->update(['name'=>Auth::user()->name,'first_name'=>Auth::user()->first_name,'last_name'=>Auth::user()->last_name,'user_type'=>Auth::user()->user_type,'country'=>Auth::user()->country]);
            }

            if(empty($cartDetails['discount'])){
                $coupon_discount = 0;    
            }else{
                $coupon_discount = $cartDetails['discount'];
            }

            if(empty($cartDetails['shipping'])){
                $shipping_charges = 0;    
            }else{
                $shipping_charges = $cartDetails['shipping'];
            }

            if(empty($cartDetails['cod_charges'])){
                $cod_charges = 0;    
            }else{
                $cod_charges = $cartDetails['cod_charges'];
            }

            /*echo "<pre>"; print_r($cartDetails); die;*/

            if($data['paymentMode']=="COD"){
                $paymentmethod = "COD";
                //$taxes = $cartDetails['gstCOD'];
                $prepaid_discount = 0;
                $grand_total = round($cartDetails['subtotal']+$shipping_charges-$coupon_discount,2)-Session::get('order_credit');
            }else{
                $paymentmethod = "Prepaid";
                //$taxes = $cartDetails['gstPrepaid'];
                $prepaid_discount = $cartDetails['prepaidDiscount'];
                $grand_total = round($cartDetails['subtotal']+$shipping_charges-$prepaid_discount-$coupon_discount,2)-Session::get('order_credit');
            }

            $orderArray = array('user_id'=>Auth::user()->id,'payment_gateway'=>$paymentGateway,'payment_method'=>$paymentmethod,'coupon_code'=> $cartDetails['couponcode'],'coupon_discount'=>round($coupon_discount,2),'prepaid_discount'=>round($prepaid_discount,2),'credit'=>round(Session::get('order_credit'),2),'shipping_charges'=>round($shipping_charges),'cod_charges'=>round($cod_charges,2),'sub_total'=>round($cartDetails['subtotal'],2),'grand_total'=>round($grand_total,2),'payment_status'=>$paymentStatus,'order_status'=>$orderstatus,'comments'=>$data['comments'],'ip_address'=>$_SERVER['REMOTE_ADDR'],'taxes'=>$gst['totalTax'],'total_weight'=>$total_weight);
            //Create Order 
            Order::create($orderArray);
            $orderid = DB::getPdo()->lastInsertId();
            //Create Addresses in Order Address Model
            $orderAddrArr = array(
				  'order_id'=>$orderid,
				  'billing_name'=>$billingAddress->name,
				  'billing_first_name'=>$billingAddress->first_name,
				  'billing_last_name'=>$billingAddress->last_name,
				  'billing_mobile'=>$billingAddress->mobile,
				  'billing_postcode'=>$billingAddress->postcode,
				  'billing_address'=>$billingAddress->address,
				  'billing_address_line2'=>$billingAddress->address_line2,
				  'billing_state'=>$billingAddress->state,
				  'billing_city'=>$billingAddress->city,
				  'billing_country'=>$billingAddress->country,
				  'shipping_name'=>$shippingAddress->name,
				  'shipping_first_name'=>$shippingAddress->first_name,
				  'shipping_last_name'=>$shippingAddress->last_name,
				  'shipping_mobile'=>$shippingAddress->mobile,
				  'shipping_postcode'=>$shippingAddress->postcode,
				  'shipping_address'=>$shippingAddress->address,
				  'shipping_address_line2'=>$shippingAddress->address_line2,
				  'shipping_state'=>$shippingAddress->state,
				  'shipping_city'=>$shippingAddress->city,
				  'shipping_country'=>$shippingAddress->country,
			  );
            OrdersAddress::create($orderAddrArr);
            //Create Order Products
            /*echo "<pre>"; print_r($cartitems); die;*/ 
            foreach($cartitems['items'] as $okey => $cartitem){

                //echo "<pre>"; print_r($cartitem); die;

                //reducing stock
                ProductsAttribute::where('product_id',$cartitem['product_id'])->where('size',$cartitem['size'])->where('status',1)->decrement('stock',$cartitem['qty']);

                $currentStock = ProductsAttribute::where('product_id',$cartitem['product_id'])->where('size',$cartitem['size'])->first();

              

                $priceDetails = Cart::calProPricing($cartitem);
                //dd($priceDetails);
                if(empty($priceDetails['discount'])){
                    $priceDetails['discount'] = 0;
                    $product_discount_amount = 0;
                }else{
                    $product_discount_amount = $priceDetails['strikePrice'] * $priceDetails['discount']/100;
                }

                //Create Order Products array
                $OrderProArr = array('order_id'=>$orderid,'user_id'=>Auth::user()->id,'product_id'=>$cartitem['product_id'],'category_name'=>$cartitem['product']['category']['category_name'],'product_name'=>$cartitem['product']['product_name'],'product_code'=>$cartitem['product']['product_code'],'product_color'=>$cartitem['product']['product_color'],'product_weight'=>$cartitem['product']['product_weight'],'product_sku'=>$cartitem['sku'],'product_size'=>$cartitem['size'],'mrp'=>$cartitem['price'],'discount_type'=>$cartitem['product']['discount_type'],'discount' =>$priceDetails['discount'],'product_price'=>$priceDetails['price'],'product_qty'=>$cartitem['qty'],'sub_total'=> round($priceDetails['subtotal'],2),'grand_total'=>round($grand_total,2),'product_gst'=>$gst['product_gst'][$okey],'gst_percent'=>$gst['gst_percent'][$okey],'product_gst'=>$gst['product_gst'][$okey],'taxable_amount'=>$gst['taxable_amount'][$okey],'discount_amount'=>$gst['discount_amount'][$okey],'product_discount_amount'=>$product_discount_amount,'prepaid_discount'=>$gst['prepaid_discount'][$okey],'credit_discount'=>$gst['order_credit_discount'][$okey],'final_price'=>$gst['final_price'][$okey]);
                OrdersProduct::create($OrderProArr);
            }
            
            
            Session::put('orderid',$orderid);
            Session::put('grandtotal',round($grand_total,2));

          /*  if(Session::get('order_credit')>0){
                $credit = new UsersCredit;
                $credit->user_email = Auth::user()->email;
                $credit->user_id = Auth::user()->id;
                $credit->amount = Session::get('order_credit');
                $credit->type = 'debit';
                $credit->action = "₹".Session::get('order_credit')." Credit Amount availed for Order #".$orderid;
                $credit->order_id = $orderid;
                $credit->created_by = 'admin';
                $credit->ip_address = $_SERVER['REMOTE_ADDR'];
                $credit->status = 1;
                $credit->save();
                User::where('id', Auth::user()->id)->decrement('credit', Session::get('order_credit'));
            } */

            Session::forget('couponinfo');
            Session::forget('order_credit');

            $history = array('order_status'=>$orderstatus,'comments'=>$comments,'order_id'=>$orderid);
            OrdersHistory::create($history);
            DB::commit();
            Order::update_product_sale($orderid);
            if(isset($data['paymentMode']) && !empty($data['paymentMode']) && $data['paymentMode'] =="payu" ){
                    //For prepaid Orders 
                return redirect()->action('Front\PaymentController@payuPayment');
            }else if(isset($data['paymentMode']) && !empty($data['paymentMode']) && $data['paymentMode'] =="CCAvenue" ){
                    //For prepaid Orders 
                $secure_order_id =  base64_encode(convert_uuencode($orderid));
                return redirect::to('/ccavenue/'.$secure_order_id);
            }else if(isset($data['paymentMode']) && !empty($data['paymentMode']) && $data['paymentMode'] =="Razorpay" ){
                    //For prepaid Orders 
                return redirect::to('/razorpay-payment?id='.$orderid);
            }else if(isset($data['paymentMode']) && !empty($data['paymentMode']) && $data['paymentMode'] =="moneris" ){
                    //For prepaid Orders 
                return redirect::to('/moneris-payment');
            }else if(isset($data['paymentMode']) && !empty($data['paymentMode']) && $data['paymentMode'] =="phonepe" ){
                    //For prepaid Orders 
                return redirect::to('/phonepe-payment?id='.$orderid);
            }else if(isset($data['paymentMode']) && !empty($data['paymentMode']) && $data['paymentMode'] =="ccavenue" ){
                    //For prepaid Orders 
                return redirect()->action('App\Http\Controllers\Front\ProductsController@ccavenuePayment');
            }else{

                

                if(env('MAIL_MODE') == "live"){
                    Mails::orderMail($orderid);
                }

               

                return redirect('thanks');
            }
        }
    }

    public function thanks(){ 
        if(Session::has('orderid')){
            Cart::where('user_id',Auth::user()->id)->delete();
            $orderdetails = Order::with('order_products')->where('id',Session::get('orderid'))->first();
            $orderdetails = json_decode(json_encode($orderdetails),true);
            $title = "Thanks";
            $orderid  = \Session::get('orderid');
            return view('front.pages.products.checkout.thanks')->with(compact('title','orderdetails'));
        }else{
            return redirect('./');
        }
    } 

    public function searchResults(Request $request){
        if(isset($_GET['q']) && !empty($_GET['q'])){

            $string = $_GET['q'];

            // Save Searches
            $string = $_GET['q'];
            // Count Search Results
            $searchCount = SearchResult::where('query',$string)->count();
            // Insert Search Result
            $search = new SearchResult;
            $search->query = $string;
            if(Auth::check()){
                $search->user_id = Auth::user()->id;
            }
            $search->count = $searchCount+1;
            $search->save();

            $categories = Category::where('status',1)->pluck('category_name','url')->toArray();
            /*dd($categories); die;*/
            if(in_array(strtolower($string), array_map('strtolower',$categories))){
                /*echo "found";*/ 
                $key = array_search (strtolower($string), array_map('strtolower',$categories));
                return redirect($key);
            }

            /*$getproducts  =  Product::join('categories','categories.id','=','products.category_id')->join('brands','brands.id','=','products.brand_id')->where('brands.status',1)->where('categories.status',1);*/

            $getproducts = Product::with(['productimages','product_image','category','groups'])->where('products.stock','>',0)->where('products.status',1)->join('categories','categories.id','=','products.category_id')->join('brands','brands.id','=','products.brand_id')->join('products_attributes', 'products_attributes.product_id', '=', 'products.id')->where('products_attributes.stock', '>', 0)->select('products.*','categories.category_discount',DB::raw("(case when products.product_discount = 0 then categories.category_discount else  products.product_discount end ) as 'item_discount'"))->where('brands.status',1);

            /*echo "not found"; die;*/

            $sortname ="";$selPrice="";$proage="";
            $title="Product Results";
            $data = $request->all();
            $string = trim($_GET['q']);
            if($string == "new-arrivals"){
                $title="New Arrivals";
                $getproducts = $getproducts->where(['is_new'=>'yes'])->orderby('id','DESC')->paginate(90);
            }else if($string == "best-seller"){
                $title="Best Sellers";
                $getproducts = $getproducts->where(['products.status'=>1,'best_seller'=>'yes'])->orderby('id','DESC')->paginate(90);
            }else if($string == "featured"){
                $title="Featured Products";
                $getproducts = $getproducts->select('products.*','categories.category_name')->with(['attributes','productimages','product_image','brand'])->where(['products.status'=>1,'products.is_featured'=>'Yes'])->where('products.status',1)->orderby('products.id','DESC')->paginate(90);
            }else if($string == "sale"){
                $title="Products on Sale";
                $getproducts = $getproducts->where(['products.status'=>1])->where('product_discount','>',0)->orderby('id','DESC')->paginate(90);
            }else{
                // Check if Brand searched
                $getBrandCount = Brand::where('url',trim($string))->orwhere('brand_name', 'like', '%'.trim($string).'%')->count();
                if($getBrandCount){
                    $getBrand = Brand::where('url',trim($string))->orwhere('brand_name', 'like', '%'.trim($string).'%')->first();
                    $getproducts = $getproducts->with(['attributes','productimages','product_image','brand'])->where(['products.status'=>1])->where('brand_id',$getBrand->id)->orderby('id','DESC')->paginate(90);
                    $catdetails['brand_image']  = $getBrand->brand_image; 
                }else{
                    // Check if Product code searched
                    $getproductsCount = $getproducts->select('products.*','categories.category_name')->where(function ($q) use ($string) {
                        $q->where('products.product_code', 'like', '%'.trim($string).'%');
                    })->whereExists( function ($query)  {
                        $query->from('categories')
                        ->whereRaw('products.category_id = categories.id')
                        ->where('categories.status',1);
                    })->where('products.status',1)->count();
                                
                    if($getproductsCount>0){
                        $getproducts = Product::with(['productimages','product_image','category','groups'])->join('categories','categories.id','=','products.category_id')->join('products_attributes', 'products_attributes.product_id', '=', 'products.id')->where('products_attributes.stock', '>', 0)->select('products.*','categories.category_name')->where(function ($q) use ($string) {
                        $q->where('products.product_code', 'like', '%'.trim($string).'%');
                    })->whereExists( function ($query)  {
                        $query->from('categories')
                        ->whereRaw('products.category_id = categories.id')
                        ->where('categories.status',1);
                    })->where('products.status',1)->where('products.stock','>',0)->orderby('id','DESC')->paginate(90);    
                    }else{

                        /*$terms = explode(' ', str_replace(['-', '_'], ' ', $string)); // Normalize terms

                        $getproducts = Product::with([
                                'attributes',
                                'productimages',
                                'product_image',
                                'brand',
                                'groups'
                            ])
                            ->join('categories', 'categories.id', '=', 'products.category_id')
                            ->join('brands', 'brands.id', '=', 'products.brand_id')
                            ->join('products_attributes', 'products_attributes.product_id', '=', 'products.id')
                            ->where('products_attributes.stock', '>', 0)
                            ->where(['brands.status' => 1, 'categories.status' => 1])
                            ->select('products.*', 'categories.category_name')
                            ->where(function ($q) use ($terms) {
                                foreach ($terms as $term) {
                                    if (!empty($term)) {
                                        $q->whereRaw("REPLACE(products.product_name, '-', '') LIKE ?", ['%' . $term . '%'])
                                          ->orWhereRaw("REPLACE(products.product_code, '-', '') LIKE ?", ['%' . $term . '%'])
                                          ->orWhereRaw("REPLACE(categories.category_name, '-', '') LIKE ?", ['%' . $term . '%'])
                                          ->orWhereRaw("REPLACE(products.search_keywords, '-', '') LIKE ?", ['%' . $term . '%']);
                                    }
                                }
                            })
                            ->whereExists(function ($query) {
                                $query->from('categories')
                                    ->whereRaw('products.category_id = categories.id')
                                    ->where('categories.status', 1);
                            })
                            ->where('products.status', 1)
                            ->where('products.stock', '>', 0)
                            ->groupBy('products.id'); // Prevent duplicate results from joins

                        $getproducts = $getproducts->paginate(240)->onEachSide(1);
                        $getproducts = $getproducts->appends(request()->query());*/

                        $terms = explode(' ', str_replace(['-', '_'], ' ', $string)); // Normalize terms

                        $getproducts = Product::with([
                                'attributes',
                                'productimages',
                                'product_image',
                                'brand',
                                'groups'
                            ])
                            ->join('categories', 'categories.id', '=', 'products.category_id')
                            ->join('brands', 'brands.id', '=', 'products.brand_id')
                            ->join('products_attributes', 'products_attributes.product_id', '=', 'products.id')
                            ->where('products_attributes.stock', '>', 0)
                            ->where(['brands.status' => 1, 'categories.status' => 1])
                            ->select('products.*', 'categories.category_name')
                            ->where(function ($q) use ($terms) {
                                $q->where(function ($query) use ($terms) {
                                    foreach ($terms as $term) {
                                        if (!empty($term)) {
                                            $query->where(function ($innerQuery) use ($term) {
                                                $innerQuery->whereRaw("REPLACE(products.product_name, '-', '') LIKE ?", ['%' . $term . '%'])
                                                    ->orWhereRaw("REPLACE(products.product_code, '-', '') LIKE ?", ['%' . $term . '%'])
                                                    ->orWhereRaw("REPLACE(categories.category_name, '-', '') LIKE ?", ['%' . $term . '%'])
                                                    ->orWhereRaw("REPLACE(products.search_keywords, '-', '') LIKE ?", ['%' . $term . '%']);
                                            });
                                        }
                                    }
                                });
                            })
                            ->whereExists(function ($query) {
                                $query->from('categories')
                                    ->whereRaw('products.category_id = categories.id')
                                    ->where('categories.status', 1);
                            })
                            ->where('products.status', 1)
                            ->where('products.stock', '>', 0)
                            ->groupBy('products.id');

                        $getproducts = $getproducts->paginate(240)->onEachSide(1);
                        $getproducts = $getproducts->appends(request()->query());
                        
                    }  
                }
                $title = $string;
            }
            if(isset($data['price']) && !empty($data['price'])){
                    $priceArr = explode('-',$data['price']);
                    if(isset($priceArr[1])){
                        if($priceArr[1] =="above"){
                            $getproducts->where('products.final_price','>',$priceArr[0]);
                        }else{
                            $getproducts->whereBetween('products.final_price',[$priceArr[0],$priceArr[1]]);
                        }
                    }
                }
            /*$getproducts = $getproducts->appends(Input::except('page'));*/
            //echo "<pre>"; print_r($getproducts); die;
            $catdetails['category_name']  = $title;
            if($string=="men"){
                $meta_title = "Men Clothing";
                $meta_description = "";
                $meta_keyword = "";

            }else if($string=="women"){
                $meta_title = "Women Clothing";
                $meta_description = "";
                $meta_keyword = "";

            }else{
                $meta_description = '';
                $meta_keyword = '';
                $meta_title = $title;
            }
            $catseo = Route::getFacadeRoot()->current()->uri();

            $breadcrumbs  = $title;

            return view('front.products.listing')->with(compact('catdetails','getproducts','title','meta_title','meta_keyword','meta_description','proage','selPrice','sortname','catseo','breadcrumbs'));
        }else{
            return redirect('/');
        }
    }

    public function addtoWishlist(Request $request){
        if($request->ajax()){
            if(Auth::check()){
                $data = $request->all();
                $size = $request->input('size');
				if(!empty($size)){
					$checkifExits = Wishlist::where([
						'user_id'=>Auth::user()->id,
						'product_id' => $data['proid'],
						'size' => $size
					])->count();
					if($checkifExits ==0){
						$wishlist = new Wishlist;
						$wishlist->user_id = Auth::user()->id;
						$wishlist->product_id = $data['proid'];
						$wishlist->size = $size;
						$wishlist->save();
						$totalWishlistItems = totalWishlistItems();
						$message =  'Product added successfully in Wishlist! <a style="text-decoration:underline !important;" href="'.route('account',['wishlist']).'">View Wishlist</a>';
						return response()->json(['status'=>true,'mode'=>'set','message'=>array($message),'totalWishlistItems'=>$totalWishlistItems]);
					}else{
						Wishlist::where(['user_id'=>Auth::user()->id,'product_id' => $data['proid'],'size' => $data['size']])->delete();
						$totalWishlistItems = totalWishlistItems();
						return response()->json(['status'=>true,'mode'=>'unset','message'=>array('Product removed successfully from wishlist.'),'totalWishlistItems'=>$totalWishlistItems]);
					}
			}else{
			   return response()->json(['status'=>false,'login'=>true,'message'=>'Select the size.']);
		    }
            }else{
				$redirectTo = route('signin');
                return response()->json(['status'=>false,'login'=>false,'message'=>array('Kindly Login to add Products in Wishlist'),'url'=>$redirectTo]);
            }
        }
    }

    public function storeStockAlert(Request $request){
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'size' => 'required|string',
            'email' => 'required|email',
        ]);

        try {
            // Use firstOrCreate to prevent duplicates
            $alert = StockAlert::firstOrCreate(
                [
                    'product_id' => $request->product_id,
                    'product_size' => $request->size,
                    'user_email' => $request->email
                ],
                ['email_sent' => 'No'] // Default value if record doesn't exist
            );

            if ($alert->wasRecentlyCreated) {
                return response()->json(['status' => 'success', 'message' => 'You will be notified when the product is back in stock.']);
            } else {
                return response()->json(['status' => 'info', 'message' => 'You have already subscribed for this size.']);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Something went wrong. Please try again later.']);
        }
    }


    public function notifyUsers(){
        $notifyUsers = StockAlert::where(['email_sent' => 'No'])->limit(10)->get();
        $notifyUsers = json_decode(json_encode($notifyUsers));
        //echo "<pre>"; print_r($notifyUsers); die;
        foreach($notifyUsers as $notify){
            $stockDetails = ProductsAttribute::where(['product_id' => $notify->product_id, 'size' => $notify->product_size])->first();
            $productDetails = Product::select('product_name')->where('id',$notify->product_id)->first();
            if($stockDetails->stock>0){

                $product_link = strtolower(str_replace(" ","-",$productDetails->product_name));
                $notify_product_link = url("product/".$product_link."/".$notify->product_id);

                
                echo "Email Send to ".$notify->user_email;
                echo "<br>";

                // Update Notify Record
                StockAlert::where(['product_id' => $notify->product_id, 'product_size' => $notify->product_size, 'user_email' => $notify->user_email])->update(['email_sent' => 'Yes']);   
            }
        }
    }

    public function ajaxSearchOld(Request $request)
    {
        $query = $request->get('q');
        $products = Product::with('product_image')
            ->where('product_name', 'like', '%' . $query . '%')
            ->where('status', 1)
            ->where('stock', '>', 0)
            ->limit(10)
            ->get();

        $output = '';
        if ($products->count()) {
            foreach ($products as $product) {
                $image = !empty($product->product_image) && !empty($product->product_image->image)
                    ? asset('front/images/products/medium/' . $product->product_image->image)
                    : asset('front/images/no-image-found.jpg');

                $output .= '
                    <a href="' . url('product/' . $product->id) . '">
                        <div class="searcProd">
                            <div class="searchProdImg">
                                <img src="' . $image . '" alt="">
                            </div>
                            <div class="prodInfoSearch">
                                <h2>' . $product->product_name . '</h2>
                                <h3>₹ ' . $product->final_price;
                                if ($product->product_discount > 0) {
                                    $output .= '<span class="strike">' . $product->product_price . '</span>';
                                }
                                $output .= '</h3>
                            </div>
                        </div>
                    </a>';
            }
        } else {
            $output = '<div align="center">No results found</div>';
        }

        return $output;
    }


    public function ajaxSearchOld1(Request $request)
    {
        $query = $request->get('q');
        $terms = explode(' ', str_replace(['-', '_'], ' ', $query)); // Split terms and normalize spaces

        $products = Product::with('product_image')
            ->where('status', 1)
            ->where('stock', '>', 0)
            ->where(function ($queryBuilder) use ($terms) {
                foreach ($terms as $term) {
                    if (!empty($term)) {
                        $queryBuilder->whereRaw("REPLACE(product_name, '-', '') LIKE ?", ['%' . $term . '%'])
                                     ->orWhereRaw("REPLACE(product_code, '-', '') LIKE ?", ['%' . $term . '%']);
                    }
                }
            })
            ->limit(10) // Limit to 10 results
            ->get();

        $output = '';
        if ($products->count()) {
            foreach ($products as $product) {
                $getProductURL = Product::productURL($product->product_name);
                $image = !empty($product->product_image) && !empty($product->product_image->image)
                    ? asset('front/images/products/medium/' . $product->product_image->image)
                    : asset('front/images/no-image-found.jpg');

                $output .= '
                    <a href="' . url('product/'.$getProductURL.'/'.$product->id) . '">
                        <div class="searcProd">
                            <div class="searchProdImg">
                                <img src="' . $image . '" alt="">
                            </div>
                            <div class="prodInfoSearch">
                                <h2>' . $product->product_name . '</h2>
                                <h3>₹ ' . $product->final_price;
                                if ($product->product_discount > 0) {
                                    $output .= '<span class="strike">' . $product->product_price . '</span>';
                                }
                                $output .= '</h3>
                            </div>
                        </div>
                    </a>';
            }

            // 👉 "View All" button added here
            $output .= '
            <div class="vwBtn">
                <a href="' . url('results?q=' . urlencode($query)) . '" class="icButton mx-0">View All</a>
            </div>';
        } else {
            $output = '<div align="center">No results found</div>';
        }

        return $output;
    }

    public function ajaxSearch(Request $request)
    {
        $query = $request->get('q');
        $terms = explode(' ', str_replace(['-', '_'], ' ', $query)); // Normalize search terms

        // Save Searches
        /*$string = $query;
        // Count Search Results
        $searchCount = SearchResult::where('query',$string)->count();
        // Insert Search Result
        $search = new SearchResult;
        $search->query = $string;
        if(Auth::check()){
            $search->user_id = Auth::user()->id;
        }
        $search->count = $searchCount+1;
        $search->save();*/

        // Check if query has more than 4 characters before saving
        if (strlen($query) >= 4) {
            $string = $query;
            $searchCount = SearchResult::where('query', $string)->count();

            $search = new SearchResult;
            $search->query = $string;
            if (Auth::check()) {
                $search->user_id = Auth::user()->id;
            }
            $search->count = $searchCount + 1;
            $search->save();
        }

        $products = Product::with('product_image')
            ->where('status', 1)
            ->where('stock', '>', 0)
            ->where(function ($queryBuilder) use ($terms) {
                foreach ($terms as $term) {
                    if (!empty($term)) {
                        $queryBuilder->whereRaw("REPLACE(product_name, '-', '') LIKE ?", ['%' . $term . '%'])
                            ->orWhereRaw("REPLACE(product_code, '-', '') LIKE ?", ['%' . $term . '%'])
                            ->orWhereRaw("REPLACE(search_keywords, '-', '') LIKE ?", ['%' . $term . '%']);
                    }
                }
            })
            ->limit(6) // 💡 Ensures only 6 results are returned
            ->get();

        $output = '';
        if ($products->count()) {

            foreach ($products as $product) {
                $getProductURL = Product::productURL($product->product_name);
                $image = !empty($product->product_image) && !empty($product->product_image->image)
                    ? asset('front/images/products/medium/' . $product->product_image->image)
                    : asset('front/images/no-image-found.jpg');

                $output .= '
                    <a href="' . url('product/'.$getProductURL.'/'.$product->id) . '">
                        <div class="searcProd">
                            <div class="searchProdImg">
                                <img src="' . $image . '" alt="">
                            </div>
                            <div class="prodInfoSearch">
                                <h2>' . $product->product_name . '</h2>
                                <h3>₹ ' . $product->final_price;
                if ($product->product_discount > 0) {
                    $output .= '<span class="strike">' . $product->product_price . '</span>';
                }
                $output .= '</h3>
                            </div>
                        </div>
                    </a>';
            }

            // Add "View All" option if more than 6 results exist
            if (Product::where('status', 1)->where('stock', '>', 0)->count() > 6) {
                $output .= '
                    <div class="vwBtn">
                        <a href="' . url('/results?q=' . urlencode($query)) . '" class="icButton mx-0">View All</a>
                    </div>';
            }
        } else {
            $output = '<div align="center">No results found</div>';
        }

        return $output;
    }


    public function getProductDetails(Request $request)
    {
        $id = $request->product_id;
        $response = Product::CheckProductQuickView($id);

        if (empty($response['status'])) {
            return response()->json(['success' => false, 'message' => 'Product not found']);
        }

        $product = $response['productdetails'];

        /*dd($product);*/

        // Adjust category discount if applicable
        if (!empty($product['discount_type']) && $product['discount_type'] == "category") {
            $product['product_discount'] = $product['category']['category_discount'];
        }

        // Generate the main product URL
        $product['product_url'] = url('product/' . Product::productURL($product['product_name']) . '/' . $product['id']);

        return response()->json([
            'success' => true,
            'productdetails' => $product
        ]);
    }

    public function checkPincode(Request $request){ 
        if($request->isMethod('post')){
            $data = $request->all();
            
			
			 $validator = Validator::make($request->all(), [
					'pincode'=>'bail|required|numeric|digits:6|exists:cities,pincode',
			 ],
			 [
					'pincode.required' => 'Enter the pincode',
					'pincode.digits' => 'Enter the 6 digit pincode', 
					'pincode.exists' => 'This pin code is not serviceable at all', 
			 ]
			 
			 
			 );
			
			if($validator->passes()) {
				$message = array('This pincode is servicable');
				return response()->json(['status'=>true,'message'=>$message]);
			}else{
				$error_message = [];
				$error_messages = json_decode(json_encode($validator->messages()));
				foreach($error_messages as $err){ 
					$error_message[] = $err[0];
				}
				return response()->json(['status'=>false,'type'=>'validation','errors'=>$error_message]);
			}
			
        }
    }
}
