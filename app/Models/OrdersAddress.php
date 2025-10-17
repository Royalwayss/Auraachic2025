<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdersAddress extends Model
{
    //
    protected $fillable = ['id','order_id','billing_name','billing_first_name','billing_last_name','billing_company','billing_mobile','billing_postcode','billing_address','billing_address_line2','billing_country','billing_state','billing_city','shipping_name','shipping_first_name','shipping_last_name','shipping_company','shipping_mobile','shipping_postcode','shipping_address','shipping_address_line2','shipping_apartment','shipping_country','shipping_state','shipping_city','created_at','updated_at'];
}
