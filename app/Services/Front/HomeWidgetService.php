<?php 

namespace App\Services\Front;
use App\Models\HomeWidget;
use App\Models\User;
use App\Models\Category;
use App\Models\Brand;
use App\Models\ProductCategory;
use App\Models\HomeWidgetContent;
use App\Models\ShopType;
use App\Models\Product;

class HomeWidgetService {

	public function getAllWidgetProducts($productIds=null){
		$userInfo = User::info(); // get user info if logged in
		if(!$productIds){
			$productIds = HomeWidgetContent::whereNotNULL('product_id')->pluck('product_id');
			$productIds = array_unique(json_decode(json_encode($productIds),true));
		}
    	$getproducts = Product::with(['productimages','product_image','brand','groups'])->where('products.stock','>',0)->where('products.status',1)->join('brands','brands.id','=','products.brand_id')->select('products.*')->whereIn('products.id',$productIds)->where('products.status',1)->get()->toArray();
        if(isset($getproducts)){
    	//echo "<pre>"; print_r($getproducts); die;
        	return $getproducts;
        }else{
        	return array();
        }
	}

	public function widgets($offset = NULL){
		$widgetProducts = $this->getAllWidgetProducts();
		$widgets = HomeWidget::with(['productInfo','categoryInfo','brandInfo','childWidgets'=>function($query){
			$query->with('productInfo','categoryInfo','brandInfo')->select('id','type','parent_id','heading','description','script_code','link_to','search_term','category_id','product_id','brand_id','section_type','alt_tag','title_tag','sort')->where('status',1)->orderby('sort','ASC')->orderby('sort','ASC')->whereDate('start_date','<=',date('Y-m-d'))->whereDate('end_date', '>=',date('Y-m-d'));
		},'widgetContent','widgetMedia'])->select('id','type','parent_id','heading','description','content','script_code','link_to','search_term','category_id','product_id','brand_id','section_type','alt_tag','title_tag','sort')->whereNULL('parent_id')->where('status',1)->whereDate('start_date','<=',date('Y-m-d'))->whereDate('end_date', '>=',date('Y-m-d'));
		if(isset($offset)){
			$limit = 1;
			$widgets = $widgets->orderby('sort','ASC')->offset($offset)->limit($limit)->get()->toArray();
		}else{
			$widgets = $widgets->orderby('sort','ASC')->get()->toArray();
		}
		foreach($widgets as $wkey=> $widget){
			if($widget['type'] == "MULTIPLE_BANNERS" ){
				$widgets[$wkey]['images'] =  $this->formatMultipleBanners($widget);
			}else if($widget['type'] == "SINGLE_BANNER"){
				$banner = $this->formatMultipleBanners($widget);
				$widgets[$wkey]['banner'] =  $banner[0];
			}else if($widget['type'] == "SINGLE_DESCRIPTIVE_BANNER"){
				$banner = $this->formatMultipleBanners($widget);
				$widgets[$wkey]['banner'] =  $banner[0];
			}else if($widget['type'] == "SINGLE_BANNER_WITH_MULTIPLE_PRODUCTS"){
				$banner = $this->formatMultipleBanners($widget);
				$widgets[$wkey]['banner'] =  $banner[0];
				$widgets[$wkey]['products'] =  $this->formatProducts($widget,$widgetProducts);
			}else if($widget['type'] == "SINGLE_VIDEO_WITH_MULTIPLE_PRODUCTS"){
				$video = $this->formatMultipleBanners($widget);
				$widgets[$wkey]['video'] =  $video[0];
				$widgets[$wkey]['products'] =  $this->formatProducts($widget,$widgetProducts);
			}else if($widget['type'] == "MULTIPLE_CATEGORIES"){
				$widgets[$wkey]['categories'] =  $this->formatCategories($widget);
			}else if($widget['type'] == "MULTIPLE_BRANDS"){
				$widgets[$wkey]['brands'] =  $this->formatBrands($widget);
			}else if($widget['type'] == "SHOP_BY_CONCERNS"){
				$widgets[$wkey]['concerns'] =  $this->formatShopByConcerns($widget);
			}else if($widget['type'] == "MULTIPLE_PRODUCTS" || $widget['type']=="PRODUCTS_FROM_CATEGORIES"){
				$widgets[$wkey]['products'] =  $this->formatProducts($widget,$widgetProducts);
			}else if($widget['type'] == "TAB_WISE_PRODUCTS"){
				foreach($widget['child_widgets'] as $ckey=> $chilwidget){
					$widgets[$wkey]['tabs'][$ckey]['info'] = $chilwidget;
					$widgets[$wkey]['tabs'][$ckey]['info']['products'] =  $this->formatProducts($chilwidget,$widgetProducts);
					unset($widgets[$wkey]['tabs'][$ckey]['info']['widget_content']);
					unset($widgets[$wkey]['tabs'][$ckey]['info']['widget_media']);
				}
			}
			unset($widgets[$wkey]['child_widgets']);
			unset($widgets[$wkey]['widget_content']);
			unset($widgets[$wkey]['widget_media']);
		}
		return $widgets;
	}

	public function formatMultipleBanners($widget){
		$childWidgets = $widget['child_widgets'];
		$widgetMedia  = $widget['widget_media'];
		unset($widget['child_widgets']);
		unset($widget['widget_media']);
		unset($widget['widget_content']);
		$firstBanner[0] = $widget;
		$firstBanner[0]['desktop_image'] = $widgetMedia['desktop_image_url'];
		$firstBanner[0]['mobile_image_url'] = $widgetMedia['mobile_image_url'];
		$firstBanner[0]['video_url'] = $widgetMedia['video_url'];
		foreach($childWidgets as $key=> $childWidget){
			$widgetMedia = $childWidget['widget_media'];
			unset($childWidget['widget_content']);
			unset($childWidget['widget_media']);
			$banners[$key] = $childWidget;
			$banners[$key]['desktop_image'] = $widgetMedia['desktop_image_url'];
			$banners[$key]['mobile_image_url'] = $widgetMedia['mobile_image_url'];
		}
		if(isset($banners)){
			return array_merge($firstBanner,$banners);
		}
		return $firstBanner;
	}

	public function formatProducts($widget,$widgetProducts){
		if($widget['type'] == "MULTIPLE_PRODUCTS" || $widget['type'] == "TAB_WISE_PRODUCTS" || $widget['type'] =="SINGLE_BANNER_WITH_MULTIPLE_PRODUCTS" || $widget['type'] =="SINGLE_VIDEO_WITH_MULTIPLE_PRODUCTS"){
			$productIdsToSearch = array_column($widget['widget_content'],'product_id');
			$products = array_values(collect($widgetProducts)->whereIn('id', $productIdsToSearch)->toArray());
		}else if($widget['type'] == "PRODUCTS_FROM_CATEGORIES"){
			//random products from categories
			$categoryIds =  array_column($widget['widget_content'],'category_id');
			$count = ProductCategory::wherein('category_id',$categoryIds)->count();
			$limit = 10;
			$randomOffset = rand(0, max($count - $limit, 0));
			$productIds = ProductCategory::wherein('category_id',$categoryIds)->skip($randomOffset)->take($limit)->pluck('product_id')->toArray();
			$products = $this->getAllWidgetProducts($productIds); 
		}
		return $products;
	}

	public function formatCategories($widget){
		$categoryIds =  array_column($widget['widget_content'],'category_id');
		$categories = Category::wherein('id',$categoryIds)->get();
		return $categories;
	}

	public function formatBrands($widget){
		$brandIds =  array_column($widget['widget_content'],'brand_id');
		$brands =Brand::wherein('id',$brandIds)->get();
		return $brands;
	}

	public function formatShopByConcerns($widget){
		$concernIds =  array_column($widget['widget_content'],'shop_type_id');
		$concerns = ShopType::wherein('id',$concernIds)->get();
		return $concerns;
	}
}