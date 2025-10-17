<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeWidgetContent extends Model
{
    use HasFactory;
    protected $appends = ['desktop_image_url','mobile_image_url','video_url'];

    public function getDesktopImageUrlAttribute(){
        if($this->desktop_image  !=""){
            return config('constants.media.base_url').config('constants.media.widgets_path.images.desktop').$this->desktop_image;
        }else{
            return NULL;
        }
    }

    public function getMobileImageUrlAttribute(){
        if($this->mobile_image  !=""){
            return config('constants.media.base_url').config('constants.media.widgets_path.images.mobile').$this->mobile_image;
        }else{
            return NULL;
        }
    }

    public function getVideoUrlAttribute(){
        if($this->video  !=""){
            return config('constants.media.base_url').config('constants.media.widgets_path.videos').$this->video;
        }else{
            return NULL;
        }
    }
}
