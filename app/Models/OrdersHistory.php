<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdersHistory extends Model
{
    //
    protected $fillable = [
    	'id','order_id','order_status','comments','updated_by','awb_number','invoice_no','invoice_date','shipped_by'
    ];
}
