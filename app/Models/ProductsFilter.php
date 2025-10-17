<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
class ProductsFilter extends Model
{
    //
    protected $fillable = [
        'id','type','value','description','sort','status'
    ];

    

    public static function filterids($values){
        $ids = array();
        $filterids = ProductsFilter::whereIn('value',$values)->get()->pluck('id');
        if(count($filterids)>0){
           $ids =  $filterids->toArray();
        }
        return $ids;
    }

    public static function filterTypes(){
        $filterTypes = ProductsFilter::select('filter_name')->groupBy('filter_name')->where('status',1)->get();
        $filterTypes = \Arr::flatten(json_decode(json_encode($filterTypes),true));
        //$filterTypes = array('fabric'=>'Fabric','sleeve'=>'Sleeve','neck'=>'Neck','pattern'=>'Pattern','fit'=>'Fit','occasion'=>'Occasion','fastening'=>'Fastening');
        return $filterTypes;
    }

    public static function getfilters($catids){
        $ProductIDs = ProductsCategory::select('product_id')->whereIn('category_id',$catids)->get();
        $ProductIDs = \Arr::flatten(json_decode(json_encode($ProductIDs),true));
        $getFilterTypes = ProductsFilter::select('filter_name')->get();
        $getFilterTypes = \Arr::flatten(json_decode(json_encode($getFilterTypes),true));
        $filterColumns = array();
        if(!empty($getFilterTypes)){
            foreach ($getFilterTypes as $key => $ftype) {
                $filterColumns[] = $ftype;
            }
        }

        /*echo "<pre>"; print_r($filterColumns); die;*/

        if(count($filterColumns)>0){
            $getcatfilters = Product::select($filterColumns)->wherein('id',$ProductIDs)->where('status',1)->get();
        }else{
            $getcatfilters = Product::wherein('id',$ProductIDs)->where('status',1)->get();     
        }

        $getcatfilters = array_filter(array_unique(\Arr::flatten(json_decode(json_encode($getcatfilters),true))));

        $getfilters = ProductsFilter::select('filter_name')->whereIn('filter_value',$getcatfilters)->groupBy('filter_name')->orderby('sort','Asc')->where('status',1)->get();
        $getfilters = \Arr::flatten(json_decode(json_encode($getfilters),true));
        /*echo "<pre>"; print_r($getfilters); die;*/
        return $getfilters;
    }

    public static function topfilters($catids){
        $ProductIDs = ProductsCategory::select('product_id')->whereIn('category_id',$catids)->get();
        $ProductIDs = \Arr::flatten(json_decode(json_encode($ProductIDs),true));
        $getFilterTypes = ProductsFilter::select('filter_name')->get();
        $getFilterTypes = \Arr::flatten(json_decode(json_encode($getFilterTypes),true));
        $filterColumns = array();
        if(!empty($getFilterTypes)){
            foreach ($getFilterTypes as $key => $ftype) {
                $filterColumns[] = $ftype;
            }
        }

        /*echo "<pre>"; print_r($filterColumns); die;*/

        $getfilters = array();

        if(count($filterColumns)>0){
            $getcatfilters = Product::select($filterColumns)->wherein('id',$ProductIDs)->where('status',1)->get();

            $getcatfilters = (array_unique(\Arr::flatten(json_decode(json_encode($getcatfilters),true))));

            $getfilters = ProductsFilter::select('filter_name')->whereIn('filter_value',$getcatfilters)->groupBy('filter_name')->orderby('sort','Asc')->where('status',1)->get();
            $getfilters = \Arr::flatten(json_decode(json_encode($getfilters),true));
            /*echo "<pre>"; print_r($getfilters); die;*/
            
        }

        return $getfilters;
    }

    public static function profilters($filter_name){
        $profilters = ProductsFilter::where('filter_name',$filter_name)->where('status',1)->get();
        return $profilters;
    }

    public static function familycolors($catids){
        $colorsCategories = Product::select('family_color')->whereIn('category_id',$catids)->where('status',1)->where('stock','>',0)->where('family_color','!=','')->groupBy('family_color')->get();
        $colorsCategories = \Arr::flatten(json_decode(json_encode($colorsCategories),true));
        return $colorsCategories;
    }

    public static function selfilters($filter_name,$catids,$catseo=''){
        $profilters = Product::select($filter_name)->wherein('category_id',$catids);
		if($catseo == 'new-arrival'){
			$profilters = $profilters->where('products.is_new','Yes');
		}
		$profilters = $profilters->groupBy($filter_name)->get();
        $profilters = array_filter(\Arr::flatten(json_decode(json_encode($profilters),true)));
        /*echo "<pre>"; print_r($profilters); die;*/
        return $profilters;
    }

    public static function checkFilters($data){
        $getfiltertypes = ProductsFilter::select('filter_name')->get();
        $getfiltertypes = \Arr::flatten(json_decode(json_encode($getfiltertypes),true));
        $getfiltertypes[] = "size";
        $getfiltertypes[] = "price";
        $getfiltertypes[] = "category";
        $getfiltertypes[] = "brand";
        $getfiltertypes[] = "pack";
        /*echo "<pre>"; print_r($getfiltertypes); die;*/
        $getdata = array();
        foreach($data as $key => $values){
            if(in_array($key,$getfiltertypes)){
                if(!empty($values)){
                    $getdata[$key] = $values;
                }
            }
        }
        return $getdata;
    }
}
