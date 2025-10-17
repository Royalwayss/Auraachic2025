<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    //
    public static function getTax($state){
        $getTax = State::select('taxes')->where('name',$state)->first();
        return $getTax->taxes;
    }

    public static function getStateCode($state){
        $getStateCode = State::select('abbreviation')->where('name',$state)->first();
        return $getStateCode->abbreviation;
    }

}
