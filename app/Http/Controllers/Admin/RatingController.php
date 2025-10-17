<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdminsRole;
use App\Models\Rating;
use App\Models\Product;
use Image;
use Session;
use Auth;
use DB;

class RatingController extends Controller
{
    public function ratings(){
        Session::put('page','ratings');
        $ratings = Rating::with('product')->get();
        /*$ratings = json_decode(json_encode($ratings));
        echo "<pre>"; print_r($ratings); die;*/

        // Set Admin/Subadmins Permissions for Ratings
        $ratingsModuleCount = AdminsRole::where(['subadmin_id'=>Auth::guard('admin')->user()->id,'module'=>'ratings'])->count();
        $ratingsModule = array();
        if(Auth::guard('admin')->user()->type=="admin"){
            $ratingsModule['view_access'] = 1;
            $ratingsModule['edit_access'] = 1;
            $ratingsModule['full_access'] = 1;
        }else if($ratingsModuleCount==0){
            $message = "This feature is restricted for you!";
            return redirect('admin/dashboard')->with('error_message',$message);
        }else{
            $ratingsModule = AdminsRole::where(['subadmin_id'=>Auth::guard('admin')->user()->id,'module'=>'ratings'])->first()->toArray();
        }

        return view('admin.ratings.ratings')->with(compact('ratings','ratingsModule'));
    }

    public function updateRatingStatus(Request $request){
        if($request->ajax()){
            $data = $request->all();
            /*echo "<pre>"; print_r($data); die;*/
            if($data['status']=="Active"){
                $status = 0;
            }else{
                $status = 1;
            }
            Rating::where('id',$data['rating_id'])->update(['status'=>$status]);
            return response()->json(['status'=>$status,'rating_id'=>$data['rating_id']]);
        }
    }

    public function deleteRating($id){
        // Delete Rating
        Rating::where('id',$id)->delete();
        return redirect()->back()->with('success_message','Rating/Review deleted successfully!');
    }

}
