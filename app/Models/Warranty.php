<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warranty extends Model
{
    use HasFactory;

    protected $fillable = ['id','user_id','name','first_name','last_name','email','phone','country','street','city_state','postal_code','order_id','order_product_id','date_purchased','purchased_from','receipt'];

    public function product(){
        return $this->hasOne('App\Models\OrdersProduct','id','order_product_id');
    }
}
