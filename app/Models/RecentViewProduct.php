<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecentViewProduct extends Model
{
    //
    public function product(){
    	return $this->belongsTo('App\Models\Product')->with('product_image');
    }
}
