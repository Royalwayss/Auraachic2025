<?php 

namespace App\Services\Admin;
use Carbon\Carbon;
use App\Models\HomeWidget;
use App\Models\HomeWidgetContent;
use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Models\AdminsRole;
use Auth;

class WidgetService {

    public function getAll(){

        // Set Admin/Subadmins Permissions for Products
        $widgetModuleCount = AdminsRole::where(['subadmin_id'=>Auth::guard('admin')->user()->id,'module'=>'widget'])->count();
        $status = "success";
        $message = "";
        $widgetModule = array();
        if(Auth::guard('admin')->user()->type=="admin"){
            $widgetModule['view_access'] = 1;
            $widgetModule['edit_access'] = 1;
            $widgetModule['full_access'] = 1;
        }else if($widgetModuleCount==0){
            $status = "error";
            $message = "This feature is restricted for you!";
        }else{
            $widgetModule = AdminsRole::where(['subadmin_id'=>Auth::guard('admin')->user()->id,'module'=>'widget'])->first()->toArray();
        }

        $widgets = HomeWidget::with(['childWidgets','widgetMedia'])->whereNULL('parent_id')->orderby('sort','ASC')->get()->toArray();

        return array("widgets"=>$widgets,"widgetModule"=>$widgetModule,"status"=>$status,"message"=>$message);
    }

    public function find($id){
        $widget = HomeWidget::with(['widgetContent','childWidgets'])->find($id)->toArray();
        return $widget;
    }

    public function createOrUpdate($request,$widgetId=null){
        $data = $request->all();
        if($widgetId){
            $widget = HomeWidget::find($widgetId);
            if(isset($data['first_banner'])){
                $getParentSliderBanner = HomeWidget::where('id',$widget->parent_id)->first();
                if(is_object($getParentSliderBanner)){
                    HomeWidget::where('parent_id',$getParentSliderBanner->id)->update(['parent_id'=>$widgetId]);
                    $getParentSliderBanner->parent_id = $widgetId;
                    $getParentSliderBanner->save();
                    $widget->parent_id = NULL;
                    $widget->sort = $getParentSliderBanner->sort;
                }
            }
        }else{
            $widget       = new HomeWidget;
            $widget->sort = $this->getSortNumber();
            $widget->section_type = $data['section_type'] ?? '';
            if($data['type'] =='MULTIPLE_BANNERS' && is_numeric($data['parent_id'])){
                $getParent = HomeWidget::where('id',$data['parent_id'])->first();
                if(is_object($getParent)){
                    $countChild =  HomeWidget::where('parent_id',$getParent->id)->count();
                   $widget->parent_id =  $getParent->id;
                   $widget->sort = $countChild + 1;
                   $widget->section_type = $getParent->section_type;
                }
            }
        }
        $widget->type        = $data['type'];
        $widget->heading     = $data['heading'];
        $widget->description = $data['description'];
        $widget->content = $data['content'];
        $widget->status      = $data['status'];
        $widget->script_code = $data['script_code'] ?? null;
        $widget->alt_tag     = $data['alt_tag'] ?? null;
        $widget->title_tag   = $data['title_tag'] ?? null;
        $widget->start_date  = $data['start_date'] ?? null;
        $widget->end_date    = $data['end_date'] ?? null;
        $widget->product_id  = NULL;
        $widget->category_id = NULL;
        $widget->brand_id    = NULL;
        $widget->search_term = "";
        $widget->link_to     = "";
        if(isset($data['link_to']) && !empty($data['link_to'])){
            $widget->link_to     = $data['link_to'];
            $widget->product_id  = $data['product_id'] ?? null;
            $widget->category_id = $data['category_id'] ?? null;
            $widget->brand_id    = $data['brand_id'] ?? null;
            $widget->search_term = $data['search_term'] ?? null;
        }
        $widget->save();
        if($widget->type == 'SINGLE_BANNER' || $widget->type == 'SINGLE_DESCRIPTIVE_BANNER' || $widget->type == 'SINGLE_BANNER_WITH_MULTIPLE_PRODUCTS' || $widget->type =='MULTIPLE_BANNERS'){
            if($request->hasFile('desktop_image') || $request->hasFile('mobile_image')){
                $widgetContent = HomeWidgetContent::where('home_widget_id',$widget->id)->whereNotNull('desktop_image')->first();
                if(!is_object($widgetContent)){
                    $widgetContent = new HomeWidgetContent;
                }
                $widgetContent->home_widget_id = $widget->id;
                if($request->hasFile('desktop_image')){
                    $filepath = config('constants.media.widgets_path.images.desktop');
                    if(isset($widgetContent->desktop_image) && !empty($widgetContent->desktop_image)){
                        // Delete Image if exists in Folder
                        if(file_exists($filepath.$widgetContent->desktop_image)){
                            unlink($filepath.$widgetContent->desktop_image);
                        }
                    }
                    // Upload Image
                    $image_tmp = $request->file('desktop_image');
                    if($image_tmp->isValid()){
                        // create image manager with desired driver
                        $manager = new ImageManager(new Driver());
                        // read image from file system
                        $image = $manager->read($image_tmp);
                        // Get Image Extension
                        $extension = $image_tmp->getClientOriginalExtension();
                        // Generate New Image Name
                        $imageName = rand(111,99999).'.'.$extension;
                        $imagePath = $filepath.$imageName;
                        // Upload the Image
                        $image->save($imagePath);
                    }
                    $widgetContent->desktop_image = $imageName;
                }
                if($request->hasFile('mobile_image')){
                    //mobile image
                    $filepath = config('constants.media.widgets_path.images.mobile');
                    if(isset($widgetContent->mobile_image) && !empty($widgetContent->mobile_image)){
                        // Delete Image if exists in Folder
                        if(file_exists($filepath.$widgetContent->mobile_image)){
                            unlink($filepath.$widgetContent->mobile_image);
                        }
                    }
                    // Upload Image
                    $image_tmp = $request->file('mobile_image');
                    if($image_tmp->isValid()){
                        // create image manager with desired driver
                        $manager = new ImageManager(new Driver());
                        // read image from file system
                        $image = $manager->read($image_tmp);
                        // Get Image Extension
                        $extension = $image_tmp->getClientOriginalExtension();
                        // Generate New Image Name
                        $imageName = rand(111,99999).'.'.$extension;
                        $imagePath = $filepath.$imageName;
                        // Upload the Image
                        $image->save($imagePath);
                    }
                    $widgetContent->mobile_image = $imageName;

                }
                $widgetContent->save();
            }
        }
        if($widget->type == 'SINGLE_VIDEO_WITH_MULTIPLE_PRODUCTS'){
            if($request->hasFile('video')){
                $widgetContent = HomeWidgetContent::where('home_widget_id',$widget->id)->whereNotNull('video')->first();
                if(!is_object($widgetContent)){
                    $widgetContent = new HomeWidgetContent;
                }
                $widgetContent->home_widget_id = $widget->id;
                $filepath = config('constants.media.widgets_path.videos');
                if(isset($widgetContent->video) && !empty($widgetContent->video)){
                    // Delete Video if exists in Folder
                    if(file_exists($filepath.$widgetContent->video)){
                        unlink($filepath.$widgetContent->video);
                    }
                }
                $file = $request->file('video');
                $resp = $this->saveMedia($file,$filepath,$request);
                $widgetContent->video = $resp['filename'];
                $widgetContent->save();
            }
        }
        if($widget->type == 'MULTIPLE_PRODUCTS' || $widget->type == 'SINGLE_BANNER_WITH_MULTIPLE_PRODUCTS' || $widget->type == 'SINGLE_VIDEO_WITH_MULTIPLE_PRODUCTS'){
            HomeWidgetContent::where('home_widget_id',$widget->id)->whereNULL('desktop_image')->whereNULL('video')->delete(); //delete while updating the record
            $bulkData = array();
            foreach($data['products'] as $key=> $productId){
                $bulkData[$key]['home_widget_id'] = $widget->id;
                $bulkData[$key]['product_id'] = $productId;
                $bulkData[$key]['created_at'] = date('Y-m-d H:i:s');
                $bulkData[$key]['updated_at'] = date('Y-m-d H:i:s');
            }
            HomeWidgetContent::insert($bulkData);
        }
        if($widget->type == 'MULTIPLE_CATEGORIES' || $widget->type == 'PRODUCTS_FROM_CATEGORIES'){
            HomeWidgetContent::where('home_widget_id',$widget->id)->delete(); //delete while updating the record
            $bulkData = array();
            foreach($data['categories'] as $key=> $categryId){
                $bulkData[$key]['home_widget_id'] = $widget->id;
                $bulkData[$key]['category_id'] = $categryId;
                $bulkData[$key]['created_at'] = date('Y-m-d H:i:s');
                $bulkData[$key]['updated_at'] = date('Y-m-d H:i:s');
            }
            HomeWidgetContent::insert($bulkData);
        }
        if($widget->type == 'MULTIPLE_BRANDS'){
            HomeWidgetContent::where('home_widget_id',$widget->id)->delete(); //delete while updating the record
            $bulkData = array();
            foreach($data['brands'] as $key=> $brandId){
                $bulkData[$key]['home_widget_id'] = $widget->id;
                $bulkData[$key]['brand_id'] = $brandId;
                $bulkData[$key]['created_at'] = date('Y-m-d H:i:s');
                $bulkData[$key]['updated_at'] = date('Y-m-d H:i:s');
            }
            HomeWidgetContent::insert($bulkData);
        }

        if($widget->type == 'TAB_WISE_PRODUCTS'){
            HomeWidget::where('parent_id',$widget->id)->delete();
            foreach($data['tab_title'] as $tabNo => $title){
                $childWidget = new HomeWidget;
                $childWidget->type        = $data['type'];
                $childWidget->parent_id   = $widget->id;
                $childWidget->heading     = $title;
                $childWidget->status      = 1;
                $childWidget->sort        = $tabNo;
                $childWidget->start_date  = $data['start_date'] ?? null;
                $childWidget->end_date    = $data['end_date'] ?? null;
                $childWidget->save();
                $bulkData = array();
                foreach($data['tab_products'][$tabNo] as $key=> $productId){
                    $bulkData[$key]['home_widget_id'] = $childWidget->id;
                    $bulkData[$key]['product_id'] = $productId;
                    $bulkData[$key]['created_at'] = date('Y-m-d H:i:s');
                    $bulkData[$key]['updated_at'] = date('Y-m-d H:i:s');
                }
                HomeWidgetContent::insert($bulkData);
            }
        }
    }

    public function getSortNumber(){
        $sort = HomeWidget::orderby('sort','DESC')->value('sort');
        if($sort){
            $sort = $sort +1;
            return $sort;
        }else{
            return 1;
        }
    }

    public function destroy($id){
        $widget = HomeWidget::with(['widgetContent'])->find($id)->toArray();
        foreach($widget['widget_content'] as $content){
            if(!empty($content['desktop_image'])){
                $filepath = config('constants.media.widgets_path.images.desktop');
                // Delete Image if exists in Folder
                if(file_exists($filepath.$content['desktop_image'])){
                    unlink($filepath.$content['desktop_image']);
                }
            }
            if(!empty($content['mobile_image'])){
                $filepath = config('constants.media.widgets_path.images.mobile');
                // Delete Image if exists in Folder
                if(file_exists($filepath.$content['mobile_image'])){
                    unlink($filepath.$content['mobile_image']);
                }
            }
            if(!empty($content['video'])){
                $filepath = config('constants.media.widgets_path.videos');
                // Delete Video if exists in Folder
                if(file_exists($filepath.$content['video'])){
                    unlink($filepath.$content['video']);
                }
            }
        }
        if($widget['type'] == "MULTIPLE_BANNERS" && is_null($widget['parent_id'])){
            $nextSliderBanners = HomeWidget::where('parent_id',$widget['id'])->orderby('sort','ASC')->get()->toArray();
            if(count($nextSliderBanners) >0){
                $nextParentId = $nextSliderBanners[0]['id'];
                HomeWidget::where('parent_id',$widget['id'])->update(['parent_id'=>$nextParentId]); //update new parent id
                HomeWidget::where('id',$nextParentId)->update(['parent_id'=> NULL,'sort'=> $widget['sort']]);
            }
        }
        HomeWidget::find($id)->delete();
        return 1;
    }

}

?>