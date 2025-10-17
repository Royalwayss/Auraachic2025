<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    public function parentcategory(){
        return $this->hasOne('App\Models\Category','id','parent_id')->select('id','category_name','parent_id','url')->where('status',1);
    }

    public function subcategories(){
        return $this->hasMany('App\Models\Category','parent_id')->where('status',1);
    }

    public function subcat(){
        return $this->belongsTo('App\Models\Category','parent_id');
    }

    public static function getCategories($type){
        $getCategories = Category::with(['subcategories'=>function($query){
            $query->with('subcategories');
        }])->where('parent_id',0)->where('status',1);
        if($type=="Front"){
            $getCategories = $getCategories->where('menu_status',1);    
        }
        $getCategories = $getCategories->get()->toArray();
        return $getCategories;
    }

    public static function getSelectedCategories($type,$catId){
        $getCategories = Category::select('id','parent_id','category_name','url')->with(['subcategories'=>function($query){
            $query->select('id','parent_id','category_name','url');
        }])->where('parent_id',0)->where('status',1);
        if($type=="Front"){
            $getCategories = $getCategories->where('menu_status',1); 
            $getCategories = $getCategories->where('id',$catId);   
        }
        $getCategories = $getCategories->get()->toArray();
        return $getCategories;
    }

    public static function getMainCategories(){
        $getMainCategories = Category::where('parent_id',0)->where('status',1)->get()->toArray();
        return $getMainCategories;
    }

    public static function getcatdetails($catseo){ 
        $getCatdetail = Category::with(['subcategories'=>function($query){
                $query->with('subcategories');
            }]);
			
		if($catseo	!= 'new-arrival' && $catseo	!= 'featured-collection' ){
			$getCatdetail =	$getCatdetail->where('url',$catseo);
		}
			
			
		$getCatdetail =	$getCatdetail->where('status',1)->select('id','category_name','category_image','description','url','meta_title','meta_keywords','meta_description','parent_id','category_image','size_chart')->first();
        $getCatdetail = json_decode(json_encode($getCatdetail),true);
        if(empty($getCatdetail)){
            $resp = array('status'=>false);
            return $resp;
        }
		
		if(!empty($getCatdetail['parent_id'])){
			$parentCategoryCount = Category::where('id',$getCatdetail['parent_id'])->where('status',1)->count();
		    if(empty($parentCategoryCount)){
					$resp = array('status'=>false);
					return $resp;
            }
		}
		
        $catids =array();
        $catids[] = $getCatdetail['id'];

        
		if($catseo	!= 'new-arrival' && $catseo	!= 'featured-collection' ){
			if($getCatdetail['parent_id']==0){
				
				$breadcrumbs = '<li><a href="'.url($getCatdetail['url']).'" class="active">'.$getCatdetail['category_name'].'</a></li>';
			}else{
				
				$parentCategory = Category::select('category_name','url')->where('id',$getCatdetail['parent_id'])->first()->toArray();
				$breadcrumbs = '
					<li><a href="'.url($parentCategory['url']).'">'.$parentCategory['category_name'].'</a></li> <li><i class="fa-solid fa-angle-right"></i></li> 
					<li>'.$getCatdetail['category_name'].'</li>';
			}
		}else{
			if($catseo	== 'new-arrival'){
				$cat_url = route('newarrival');
				$cat_name = 'New Arrival';
			}else{
				$cat_url = route('featuredcollection');
				$cat_name = 'Featured Collection';
			}
			$breadcrumbs = '<li><a href="'.$cat_url.'" class="active">'.$cat_name.'</a></li>';
			
		}

        foreach($getCatdetail['subcategories'] as $subcat){
            $catids[] = $subcat['id'];
            foreach($subcat['subcategories'] as $subsubcat){
                $catids[] = $subsubcat['id'];
            }
        }
        $resp = array('status'=>true,'catids'=>$catids,'catdetail'=>$getCatdetail,'breadcrumbs'=>$breadcrumbs);
        /*echo "<pre>"; print_r($resp); die;*/
        return $resp;
    }

    public static function getParentCategory($catid){
        $getParentId = Category::select('parent_id')->where('id',$catid)->first();
        $getCatName = Category::select('category_name')->where('id',$getParentId->parent_id)->first();
        return $getCatName->category_name; 
    }
}
