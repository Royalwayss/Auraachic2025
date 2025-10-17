<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;
class BillingAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id','name','first_name','last_name','company','mobile','country','state','city','postcode','address','apartment','is_default','email'
    ];
    
    public static function addresses(){
    	$addresses = BillingAddress::where('user_id',Auth::user()->id)->first();
    	return $addresses;
    }

    public static function addresscount(){
    	$count = BillingAddress::where('user_id',Auth::user()->id)->count();
    	return $count;
    }
}
