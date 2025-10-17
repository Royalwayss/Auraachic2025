<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Subscriber;
use App\Models\Banner;
use App\Models\Product;
use App\Models\Visitor;
use App\Models\Mails;
use Validator;
use App\Services\Front\HomeWidgetService;

class IndexController extends Controller
{

    protected $homeWidgetService;
    public function __construct(HomeWidgetService $homeWidgetService){
        $this->homeWidgetService = $homeWidgetService;
    }

    public function comingSoon(){
        return view('front.pages.website_under_construction.coming_soon');
    }
   
    public function index(){ 
        $this->checkVistor();	
        $top_banners =Banner::where('type','Slider')->where('status','1')->orderby('sort','asc')->get()->toArray(); 
        $category_banners =Banner::where('type','Category')->where('status','1')->orderby('sort','asc')->get()->toArray(); 
        $instagram_banners =Banner::where('type','Instagram')->where('status','1')->orderby('sort','asc')->get()->toArray(); 
        $single_banner = Banner::where('type','Single')->where('status','1')->first(); 
        $featured_products = Product::getFeaturedProducts(); 
        $newarrival_products = Product::getNewArrivalProducts(); 
		return view('front.pages.home.index')->with(compact('top_banners','category_banners','instagram_banners','single_banner','featured_products','newarrival_products'));
    }

    public function index2(){
        // Get Home Page Slider Banners
        $homeSliderBanners = Banner::where('type','Slider')->orderby('sort','ASC')->where('status',1)->get()->toArray();

        $title = "Mens Sports Wear - Buy Men Designer Round Neck T Shirts, Trendy Polo T Shirts, Polyester Sando, Cargo Pants, Shorts, Tracksuits, Light Jackets, Sweaters, Pullovers, Gloves, Bags & Thermal Online in India";
        $meta_title = "Mens Sports Wear - Buy Men Designer Round Neck T Shirts, Trendy Polo T Shirts, Polyester Sando, Cargo Pants, Shorts, Tracksuits, Light Jackets, Sweaters, Pullovers, Gloves, Bags & Thermal Online in India";
        $meta_description = "On-Vers is one of the leading gym wear, sports clothing & fitness clothing brand in India offers online shopping for men sports t shirts, cotton t shirts, fitness t shirts, round neck t-shirts, printed polo t shirts, sandos, sports shorts, half pants, tracksuits, jogging suits, rain jackets, rain wear, wind cheater, light jackets, fleece jackets, soft shell jackets, quilted jackets, thermal wear, athletic wear, gym wear, shirts, scarf, beanies caps, gloves, bags & latest design active wear. Designed for comfort and performance. Unbeatable prices on premium sportswear. Shop Now.";
        $meta_keyword = "sports t shirts, sportswear online, fitness clothes online, gym clothes online, sports clothing, active wear, men sports t shirts, cotton t shirts, fitness t shirts, round neck t-shirts, printed polo t shirts, sandos, sports shorts, half pants, tracksuits, jogging suits, rain jackets, rain wear, wind cheater, light jackets, fleece jackets, soft shell jackets, quilted jackets, thermal wear, athletic wear, gym wear, shirts, scarf, beanies caps, gloves, bags";

        $result['widgets'] = !empty($this->homeWidgetService->widgets())?$this->homeWidgetService->widgets():null;
        //echo "<pre>"; print_r($result); die;
        //dd($result);

        return view('front.index2')->with(compact('homeSliderBanners','meta_title','meta_description','meta_keyword','result'));
    }

    public function collection(){
        
    }
	
	public function contactus(){
        $meta_title = "";
        $meta_description = "";
        $meta_keyword = ""; 
        return view('front.pages.static_pages.contact_us')->with(compact('meta_title','meta_description','meta_keyword'));
    }
	public function aboutus(){
        $meta_title = "";
        $meta_description = "";
        $meta_keyword = ""; 
        return view('front.pages.static_pages.about_us')->with(compact('meta_title','meta_description','meta_keyword'));
    }

    public function addSubscriber(Request $request){
        if($request->ajax()){
            $data = $request->all();
            $validator = Validator::make($request->all(), [
                   'email' => 'required|string|regex:/^\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b$/i|max:255|unique:subscribers',
                ],
                [
					'email.required' => 'This email is not a valid email address',
					'email.regex' => 'This email is not a valid email address',
					'email.unique' => 'This email is already in our subscription list!'
				]
            );

            if($validator->passes()) {
                $subscriber = new Subscriber;
				$subscriber->email = $data['email'];
				$subscriber->status = 1;
				$subscriber->save();
				Mails::subscriber_mail($data['email']); 
				return response()->json([  'status' => true, 'message' => array('Thank you for subscribing! Stay tuned for updates and offers.')]);
            }else{
				$message = '';
				$errors = $validator->messages();
				$errors = json_decode(json_encode($errors), true);
				foreach($errors as $err){
					$message = $err[0];
				}
                return response()->json([ 'status' => false, 'message' => 'Invalid email format!','message' =>array($message)]);
			}
            
        }
    }
	
	
	 public function checkVistor() {
        $ip = $_SERVER['REMOTE_ADDR']; 
        $checkVisitor = Visitor::where('user_ip',$ip)->count();
        if(empty($checkVisitor)){
            $user_ip_address_info = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=".$ip));
			$visitor = new Visitor;
			if(!empty($user_ip_address_info)){
				$user_ip_address_info_array = [];
				foreach($user_ip_address_info as $key=>$info){
					$user_ip_address_info_array[$key] = $info;
				}
				$visitor->user_info = json_encode($user_ip_address_info_array);
			}
			
			
            $visitor->user_ip  = $ip;
            $visitor->visit_date = date('Y-m-d');
            $visitor->save();
        }
      }

}
