<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Contact;
use App\Models\BusinessContact;
use App\Models\AdminsRole;
use Session;
use Auth;

class EnquiryController extends Controller
{
    public function enquiries(){
        Session::put('page','contact-enquiries');
        if(isset($_GET['email'])){
            $enquiries = Contact::where('email',$_GET['email'])->get()->toArray(); 
        }else{
            $enquiries = Contact::get()->toArray();    
        }
        /*dd($enquiries);*/

        // Set Admin/Subadmins Permissions for Enquiries
        $enquiriesModuleCount = AdminsRole::where(['subadmin_id'=>Auth::guard('admin')->user()->id,'module'=>'enquiries'])->count();
        $enquiriesModule = array();
        if(Auth::guard('admin')->user()->type=="admin"){
            $enquiriesModule['view_access'] = 1;
            $enquiriesModule['edit_access'] = 1;
            $enquiriesModule['full_access'] = 1;
        }else if($enquiriesModuleCount==0){
            $message = "This feature is restricted for you!";
            return redirect('admin/dashboard')->with('error_message',$message);
        }else{
            $enquiriesModule = AdminsRole::where(['subadmin_id'=>Auth::guard('admin')->user()->id,'module'=>'enquiries'])->first()->toArray();
        }

        return view('admin.enquiries.enquiries')->with(compact('enquiries','enquiriesModule'));
    }

    public function business_enquiries(){
        Session::put('page','business_enquiries');
        if(isset($_GET['email'])){
            $enquiries = BusinessContact::where('email',$_GET['email'])->get()->toArray(); 
        }else{
            $enquiries = BusinessContact::get()->toArray();    
        }
        /*dd($enquiries);*/

        // Set Admin/Subadmins Permissions for Enquiries
        $enquiriesModuleCount = AdminsRole::where(['subadmin_id'=>Auth::guard('admin')->user()->id,'module'=>'enquiries'])->count();
        $enquiriesModule = array();
        if(Auth::guard('admin')->user()->type=="admin"){
            $enquiriesModule['view_access'] = 1;
            $enquiriesModule['edit_access'] = 1;
            $enquiriesModule['full_access'] = 1;
        }else if($enquiriesModuleCount==0){
            $message = "This feature is restricted for you!";
            return redirect('admin/dashboard')->with('error_message',$message);
        }else{
            $enquiriesModule = AdminsRole::where(['subadmin_id'=>Auth::guard('admin')->user()->id,'module'=>'enquiries'])->first()->toArray();
        }

        return view('admin.enquiries.business_enquiries')->with(compact('enquiries','enquiriesModule'));
    }
}
