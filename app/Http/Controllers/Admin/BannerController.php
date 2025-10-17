<?php

namespace App\Http\Controllers\Admin;

use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdminsRole;
use App\Models\Banner;
use Session;
use Auth;
use App\Services\Admin\BannerService;

class BannerController extends Controller
{
    public function banners(){
        Session::put('page','banners');
        $service = new BannerService();
        $result = $service->banners();
        if($result['status']=="error"){
            return redirect('admin/dashboard')->with('error_message',$result['message']);    
        }else{
            $banners = $result['banners'];
            $bannersModule = $result['bannersModule'];
            return view('admin.banners.banners')->with(compact('banners','bannersModule'));    
        }
    }

    public function updateBannerStatus(Request $request){
        if($request->ajax()){
            $data = $request->all();
            /*echo "<pre>"; print_r($data); die;*/
            $service = new BannerService();
            $status = $service->updateBannerStatus($data);
            return response()->json(['status'=>$status,'banner_id'=>$data['banner_id']]);
        }
    }

    public function deleteBanner($id){
        $service = new BannerService();
        $message = $service->deleteBanner($id);
        return redirect('admin/banners')->with('success_message',$message);
    }

    public function addEditBanner($id=null){
        Session::put('page','banners');
        if($id==""){
            // Add Banner
            $banner = array();
            $title = "Add Banner Image";
            // Next & Prev Banner
            $prevId = 0; 
            $nextId = 0;
        }else{
            // Update Banner
            $banner = Banner::find($id);
            $title = "Edit Banner Image";

            /*// Next & Prev Banner
            $prevBannerId = $id-1; 
            $nextBannerId = $id+1;
            $prevBannerCount = Banner::where('id',$prevBannerId)->count();
            if($prevBannerCount==0){
                $prevBannerId = 0;    
            }
            $nextBannerCount = Banner::where('id',$nextBannerId)->count();
            if($nextBannerCount==0){
                $nextBannerId = 0;    
            }*/

            $model = 'Banner'; // Fully qualified model name
            $prevId = findPreviousId($id, $model); // Start checking with $id - 1
            $nextId = findNextId($id, $model);  // Start checking with $id + 1
        }
        return view('admin.banners.add_edit_banner')->with(compact('title','banner','prevId','nextId'));

    }

    public function addEditBannerRequest(Request $request,$id=null){
        ini_set('memory_limit','256M');
        if($request->isMethod('post')){
            $service = new BannerService();
            $result = $service->addEditBanner($request);
            if($result['status']=="error"){
                return redirect('admin/dashboard')->with('error_message',$result['message']);
            }else{
                return redirect('admin/banners')->with('success_message',$result['message']);    
            }   
        }
    }
}
