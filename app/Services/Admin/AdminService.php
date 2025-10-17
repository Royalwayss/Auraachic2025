<?php 

namespace App\Services\Admin;
use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Models\Admin;
use App\Models\AdminsRole;
use Auth;
use Hash;

class AdminService {

    public function login($data){
        if(Auth::guard('admin')->attempt(['email'=>$data['email'],'password'=>$data['password'],'status'=>1])){

            // Remember Admin Email and Password
            if(!empty($data["remember"])) {
                // Set Cookies
                setcookie ("email",$data["email"],time()+ 3600);
                setcookie ("password",$data["password"],time()+ 3600);
            } else {
                // Delete Cookies
                setcookie("email","");
                setcookie("password","");
            }

            $loginStatus = 1;
        }else{
            $loginStatus = 0;
        }
        return $loginStatus;
    }

    public function verifyPassword($data){
        if(Hash::check($data['current_pwd'], Auth::guard('admin')->user()->password)){
            return "true";
        }else{
            return "false";
        }
    }

    public function updatePassword($data){
        // Check if Current Password is correct
        if(Hash::check($data['current_pwd'],Auth::guard('admin')->user()->password)){
            // Check if new password and confirm password is same
            if($data['new_pwd']==$data['confirm_pwd']){
                Admin::where('email',Auth::guard('admin')->user()->email)->update(['password'=>bcrypt($data['new_pwd'])]);
                $status = "success";
                $message = "Password has been updated Successfully!";
            }else{
                $status = "error";
                $message = "New Passord and Retype Password not match!";
            }
        }else{
            $status = "error";
            $message = "Your current password is Incorrect!";
        }
        return array("status"=>$status,"message"=>$message);
    }

    public function updateDetails($request){
        $data = $request->all();
        // Upload Admin Image
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
                $image_path = 'admin/images/photos/'.$imageName;

                // save image in specified path
                $image->save($image_path);

                //Image::make($image_tmp)->save($image_path);
            }
        }else if(!empty($data['current_image'])){
            $imageName = $data['current_image'];
        }else{
            $imageName = "";
        }

        // Update Admin Details
        Admin::where('email',Auth::guard('admin')->user()->email)->update(['name'=>$data['name'],'mobile'=>$data['mobile'],'image'=>$imageName]);
        $message = "Admin Details has been updated Successfully!";
        return array("message"=>$message);
    }

    public function updateSubadminStatus($data){
        if($data['status']=="Active"){
            $status = 0;
        }else{
            $status = 1;
        }
        Admin::where('id',$data['subadmin_id'])->update(['status'=>$status]);
        return $status;
    }

    public function addEditSubadmin($request){
        $data = $request->all();
        /*echo "<pre>"; print_r($data); die;*/
        if(isset($data['id']) && $data['id']!=""){
            $subadmindata = Admin::find($data['id']);
            $message = "Subadmin updated successfully!";
        }else{
            $subadmindata = new Admin;
            $message = "Subadmin added successfully!";    
        }
        // Upload Admin Image
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
                $image_path = 'admin/images/photos/'.$imageName;

                // save image in specified path
                $image->save($image_path);

                //Image::make($image_tmp)->save($image_path);
            }
        }else if(!empty($data['current_image'])){
            $imageName = $data['current_image'];
        }else{
            $imageName = "";
        }
        $subadmindata->image = $imageName;
        $subadmindata->name = $data['name'];
        $subadmindata->mobile = $data['mobile'];
        if(!isset($data['id'])){
            $subadmindata->email = $data['email'];
            $subadmindata->type = 'subadmin';
            $subadmindata->status = 1;
        }
        if($data['password']!=""){
            $subadmindata->password = bcrypt($data['password']);
        }
        $subadmindata->save();
        return array("message"=>$message);
    }

    public function deleteSubadmin($id){
        // Delete Sub Admin
        Admin::where('id',$id)->delete();
        $message = 'Subadmin deleted successfully!';
        return array("message"=>$message);
    }

    public function updateRole($request){
        $data = $request->all();
        //echo "<pre>"; print_r($data); die;

        // Delete all earlier roles for Subadmin
        AdminsRole::where('subadmin_id',$data['subadmin_id'])->delete();

        // Add new roles for Subadmin Dynamically
        foreach ($data as $key => $value) {
            if(isset($value['view'])){
                $view = $value['view'];
            }else{
                $view = 0;
            }
            if(isset($value['edit'])){
                $edit = $value['edit'];
            }else{
                $edit = 0;
            }
            if(isset($value['full'])){
                $full = $value['full'];
            }else{
                $full = 0;
            }

            AdminsRole::where('subadmin_id',$data['subadmin_id'])->insert(['subadmin_id'=>$data['subadmin_id'],'module'=>$key,'view_access'=>$view,'edit_access'=>$edit,'full_access'=>$full]);
        }

        $message = "Subadmin Roles updated successfully!";
        return array("message"=>$message);
    }
}

?>