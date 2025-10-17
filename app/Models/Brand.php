<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'brand_name',
        'brand_discount',
        'description',
        'url',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'status',
        'brand_image', // Add this
    ];

    public static function getBrands(){
        $getBrands = Brand::where('status',1)->get()->toArray();
        return $getBrands;
    }

    public static function countBrand($url){
        $countBrand = Brand::where('url',$url)->where('status',1)->count();
        return $countBrand;
    }
}
