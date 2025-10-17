<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;
class ShippingAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id','name','first_name','last_name','company','mobile','country','state','city','postcode','address','apartment','is_default','email'
    ];
    
    public static function addresses(){
    	$addresses = ShippingAddress::where('user_id',Auth::user()->id)->orderby('is_default','desc')->get()->toArray();
    	return $addresses;
    }

    public static function addresscount(){
    	$count = ShippingAddress::where('user_id',Auth::user()->id)->count();
    	return $count;
    }
}
