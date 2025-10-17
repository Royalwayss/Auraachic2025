<?php 

namespace App\Services\Admin;
use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Models\Banner;
use App\Models\AdminsRole;
use Auth;

class BannerService {

    public function banners(){
        $banners = Banner::get()->toArray();
        /*dd($banners); die;*/
        // Set Admin/Subadmins Permissions for Banners
        $bannersModuleCount = AdminsRole::where(['subadmin_id'=>Auth::guard('admin')->user()->id,'module'=>'banners'])->count();
        $bannersModule = array();
        $status = "success";
        $message = "";
        if(Auth::guard('admin')->user()->type=="admin"){
            $bannersModule['view_access'] = 1;
            $bannersModule['edit_access'] = 1;
            $bannersModule['full_access'] = 1;
        }else if($bannersModuleCount==0){
            $status = "error";
            $message = "This feature is restricted for you!";
        }else{
            $bannersModule = AdminsRole::where(['subadmin_id'=>Auth::guard('admin')->user()->id,'module'=>'banners'])->first()->toArray();
        }
        return array("bannersModule"=>$bannersModule,"status"=>$status,"banners"=>$banners,"message"=>$message);
    }

    public function updateBannerStatus($data){
        if($data['status']=="Active"){
            $status = 0;
        }else{
            $status = 1;
        }
        Banner::where('id',$data['banner_id'])->update(['status'=>$status]);
        return $status;
    }

    public function deleteBanner($id){
        // Get Banner Image
        $bannerImage = Banner::where('id',$id)->first();

        // Get Banner Image Path
        $banner_image_path = 'front/images/banner_images/';

        // Delete Banner Image if exists in Folder
        if(file_exists($banner_image_path.$bannerImage->image)){
            unlink($banner_image_path.$bannerImage->image);
        }

        // Delete Banner Image from banners table
        Banner::where('id',$id)->delete();

        $message = "Banner deleted successfully!";
        return $message;
    }

    public function addEditBanner($request){
        $data = $request->all();
        //echo "<pre>"; print_r($data); die;
        $status = "success";
        $message = "";
        $adminType = Auth::guard('admin')->user()->type;
        if($adminType=="vendor"){
            $message = "You have no right to access this functionality";
            $status = "error";
        }
        if(isset($data['id']) && $data['id']!=""){
            // Update Banner
            $banner = Banner::find($data['id']);
            $message = "Banner updated successfully!";
        }else{
            // Add Banner
            $banner = new Banner;
            $message = "Banner added successfully!";    
        }

        $banner->type = $data['type'];
        $banner->link = $data['link'];
        $banner->title = $data['title'];
        $banner->alt = $data['alt'];
        $banner->sort = $data['sort'];
        $banner->status = 1;

        if($data['type']=="Slider" && !empty($data['image'])){
            $width = "1920";
            $height = "1100";
        }else if($data['type']=="Slider" && !empty( $data['mobile_banner'] )){
            $width = "470";
            $height = "375";
        }else if($data['type']=="Fix"){
            $width = "1920";
            $height = "1100";
        }

        // Upload Banner Image
        if($request->hasFile('image')){
            $image_tmp = $request->file('image');
            if($image_tmp->isValid()){
                // create image manager with desired driver
                $manager = new ImageManager(new Driver());
                // read image from file system
                $image = $manager->read($image_tmp);
                // Get Image Extension
                $extension = $image_tmp->getClientOriginalExtension();
                // Generate New Image Name
                $imageName = rand(111,99999).'.'.$extension;
                $imagePath = 'front/images/banners/'.$imageName;
                // Upload the Image
                 $image->save($imagePath);
                $banner->image = $imageName;
            }
        }

        // Upload mobile banner
        if($request->hasFile('mobile_image')){
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
                $imagePath = 'front/images/banners/'.$imageName;
                // Upload the Image
                $image->save($imagePath);
                $banner->mobile_banner = $imageName;
            }
        }
        $banner->save();
        return array("status"=>$status,"message"=>$message);
    }
    
}

?>