<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AwbNumber extends Model
{
    //
	public static function getnumber(){
		$number = AwbNumber::where('flag','N')->first();
		if($number){
			return $number->awb_number;
		}else{
			return 0;
		}
	}

}
