<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\AdminsRole;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Brand;
use App\Models\User;
use Session;
use Auth;

class CouponController extends Controller
{
    public function coupons(){
        Session::put('page','coupons');
        $coupons = Coupon::get();
        /*$coupons = json_decode(json_encode($coupons));
        echo "<pre>"; print_r($coupons); die;*/

        // Set Admin/Subadmins Permissions for Coupons
        $couponsModuleCount = AdminsRole::where(['subadmin_id'=>Auth::guard('admin')->user()->id,'module'=>'coupons'])->count();
        $couponsModule = array();
        if(Auth::guard('admin')->user()->type=="admin"){
            $couponsModule['view_access'] = 1;
            $couponsModule['edit_access'] = 1;
            $couponsModule['full_access'] = 1;
        }else if($couponsModuleCount==0){
            $message = "This feature is restricted for you!";
            return redirect('admin/dashboard')->with('error_message',$message);
        }else{
            $couponsModule = AdminsRole::where(['subadmin_id'=>Auth::guard('admin')->user()->id,'module'=>'coupons'])->first()->toArray();
        }

        return view('admin.coupons.coupons')->with(compact('coupons','couponsModule'));
    }

    public function updateCouponStatus(Request $request){
        if($request->ajax()){
            $data = $request->all();
            /*echo "<pre>"; print_r($data); die;*/
            if($data['status']=="Active"){
                $status = 0;
            }else{
                $status = 1;
            }
            Coupon::where('id',$data['coupon_id'])->update(['status'=>$status]);
            return response()->json(['status'=>$status,'coupon_id'=>$data['coupon_id']]);
        }
    }

    public function deleteCoupon($id){
        // Delete Coupon
        Coupon::where('id',$id)->delete();
        $message = "Coupon has been deleted successfully!";
        return redirect()->back()->with('success_message',$message);
    }

    public function addEditCoupon(Request $request,$id=null){
        Session::put('page','coupons');
        if($id==""){
            // Add Coupon
            $coupon = new Coupon;
            $selCats = array();
            $selUsers = array();
            $selBrands = array();
            $title = "Add Coupon";
            $message = "Coupon added successfully!";
            // Next & Prev Coupon
            $prevId = 0; 
            $nextId = 0;
        }else{
            // Update Coupon
            $coupon = Coupon::find($id);
            $selCats = explode(',',$coupon['categories']);
            $selUsers = explode(',',$coupon['users']);
            $selBrands = explode(',',$coupon['brands']);
            $title = "Edit Coupon";
            $message = "Coupon updated successfully!";

            // Next & Prev Coupon
            $model = 'Coupon'; // Fully qualified model name
            $prevId = findPreviousId($id, $model); // Start checking with $id - 1
            $nextId = findNextId($id, $model);  // Start checking with $id + 1
        }

        if($request->isMethod('post')){
            $data = $request->all();
            /*echo "<pre>"; print_r($data); die;*/

            // Coupon Validations
            $rules = [
                'categories' => 'required',
                /*'brands' => 'required',*/
                'coupon_option' => 'required',
                'coupon_type' => 'required',
                'amount_type' => 'required',
                'amount' => 'required|numeric|gt:0',
                'start_date' => 'required',
                'expiry_date' => 'required',
                'coupon_code' => 'required_if:coupon_option,Manual', // Require coupon_code if Manual is selected
                'users' => [
                    'nullable',           // users field is optional
                    'array',              // Ensure it is an array
                    'max:10',             // Maximum 10 emails allowed
                ],
                'users.*' => 'email',    // Validate each email in the array
            ];
            $customMessages = [
                'categories.required' => 'Select Categories',
                /*'brands.required' => 'Select Brands',*/
                'coupon_option.required' => 'Select Coupon Option',
                'coupon_type.required' => 'Select Coupon Type',
                'amount_type.required' => 'Select Amount Type',
                'amount.required' => 'Enter Amount',
                'amount.numeric' => 'Enter Valid Amount',
                'start_date.required' => 'Enter Start Date',
                'expiry_date.required' => 'Enter Expiry Date',
                'coupon_code.required_if' => 'Coupon Code is required when Manual option is selected.',
                'users.array' => 'Emails must be an array.',
                'users.max' => 'You can only select a maximum of 10 emails.',
                'users.*.email' => 'Each email must be valid.',
            ];
            $this->validate($request,$rules,$customMessages);

            if(isset($data['users'])){
                $users = implode(',', $data['users']);
            }else{
                $users = "";
            }
            if(isset($data['categories'])){
                $categories = implode(',', $data['categories']);
            }else{
                $categories = "";
            }
            if(isset($data['brands'])){
                $brands = implode(',', $data['brands']);
            }else{
                $brands = "";
            }
            if($id=="" && $data['coupon_option']=="Automatic"){
                $coupon_code = Str::random(8);
            }else{
                $coupon_code = $data['coupon_code'];
            }
            if(!isset($data['min_qty'])){
                $data['min_qty'] = 1;    
            }
            if(!isset($data['max_qty'])){
                $data['max_qty'] = 100;    
            }
            if(!isset($data['min_amount'])){
                $data['min_amount'] = 1;    
            }
            if(!isset($data['max_amount'])){
                $data['max_amount'] = 100000;    
            }

            /*$adminType = Auth::guard('admin')->user()->type;
            $vendor_id = Auth::guard('admin')->user()->vendor_id;*/
            /*$admin_id = Auth::guard('admin')->user()->id;*/
            /*$coupon->admin_type = $adminType;*/
            /*$coupon->admin_id = $admin_id;*/

            /*if($adminType=="vendor"){
                $coupon->vendor_id = $vendor_id;
            }else{
                $coupon->vendor_id = 0;    
            }*/

            $coupon->coupon_option = $data['coupon_option'];
            $coupon->coupon_code = $coupon_code;
            $coupon->categories = $categories;
            $coupon->brands = $brands;
            $coupon->min_qty = $data['min_qty'];
            $coupon->max_qty = $data['max_qty'];
            $coupon->min_amount = $data['min_amount'];
            $coupon->max_amount = $data['max_amount'];
            $coupon->users = $users;
            $coupon->coupon_type = $data['coupon_type'];
            $coupon->amount_type = $data['amount_type'];
            $coupon->amount = $data['amount'];
            $coupon->start_date = $data['start_date'];
            $coupon->expiry_date = $data['expiry_date'];
            if($id==""){
                $coupon->status = 1;
            }
            if(isset($data['visible'])){
                $coupon->visible = 1;
            }else{
                $coupon->visible = 0;
            }
            $coupon->save();
            return redirect('admin/coupons')->with('success_message',$message);
        }

        // Get Categories and their Sub Categories
        $categories = Category::getCategories($type='Admin');
        /*dd($categories);*/

        // Get All Brands
        $brands = Brand::where('status',1)->get()->toArray();

        // Users
        $users = User::select('email')->where('status',1)->get()->toArray();

        return view('admin.coupons.add_edit_coupon')->with(compact('title','coupon','categories','brands','users','selCats','selUsers','selBrands','prevId','nextId'));
    }
}
