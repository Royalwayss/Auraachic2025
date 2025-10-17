<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeWidget extends Model
{
    use HasFactory;

    public function widgetContent(){
        return $this->hasMany('App\Models\HomeWidgetContent');
    }

    public function widgetMedia(){
        return $this->hasOne('App\Models\HomeWidgetContent');
    }

    public function childWidgets(){
        return $this->hasMany('App\Models\HomeWidget','parent_id','id')->orderby('sort','ASC')->with(['widgetContent','widgetMedia']);
    }

    public function productInfo(){
        return $this->belongsTo('App\Models\Product','product_id','id')->select('id','product_name');
    }

    public function categoryInfo(){
        return $this->belongsTo('App\Models\Category','category_id','id')->select('id','category_name','url');
    }

    public function brandInfo(){
        return $this->belongsTo('App\Models\Brand','brand_id','id')->select('id','brand_name','url');
    }
}
