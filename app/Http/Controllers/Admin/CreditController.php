<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Models\UsersCredit;
use App\Models\User;
use App\Models\AdminsRole;
use Session;
use DB;
use Hash;
use Auth;
use Carbon\Carbon;

class CreditController extends Controller
{

    public function credits(){
        Session::put('page','credits');
        if(isset($_GET['email'])){
            $credits = UsersCredit::where('user_email',$_GET['email'])->get()->toArray(); 
        }else{
            $credits = UsersCredit::get()->toArray();    
        }
        

        foreach($credits as $key => $credit) {
            // Set color based on transaction type
            $textColor = ($credit['type'] === "debit") ? "#721c24" : "#155724"; // Red for debit, Green for credit

            // User ID Link
            $credits[$key]['user_id_link'] = "<a style='color:{$textColor}' href='/admin/credits/".$credit['user_id']."'>".$credit['user_id']."</a>";

            // User Email Link
            $credits[$key]['user_email_link'] = "<a style='color:{$textColor}' href='/admin/credits/".$credit['user_id']."'>".$credit['user_email']."</a>";

            // Order ID Link
            if ($credit['order_id'] != "" && $credit['order_id'] != 0) {
                $credits[$key]['user_order_link'] = "<a style='color:{$textColor}' href='/admin/orders/".$credit['order_id']."'>".$credit['order_id']."</a>";
            } else {
                $credits[$key]['user_order_link'] = "";
            }
        }


        /*dd($credits);*/

        // Set Admin/Subadmins Permissions for Users Credits
        $creditsModuleCount = AdminsRole::where(['subadmin_id'=>Auth::guard('admin')->user()->id,'module'=>'credits'])->count();
        $creditsModule = array();
        if(Auth::guard('admin')->user()->type=="admin"){
            $creditsModule['view_access'] = 1;
            $creditsModule['edit_access'] = 1;
            $creditsModule['full_access'] = 1;
        }else if($creditsModuleCount==0){
            $message = "This feature is restricted for you!";
            return redirect('admin/dashboard')->with('error_message',$message);
        }else{
            $creditsModule = AdminsRole::where(['subadmin_id'=>Auth::guard('admin')->user()->id,'module'=>'credits'])->first()->toArray();
        }

        return view('admin.credits.credits')->with(compact('credits','creditsModule'));
    }

    public function searchEmails(Request $request)
    {
        $term = $request->input('term'); // Get search term

        // Use a paginated query to avoid large data responses
        $emails = User::where('email', 'LIKE', "%{$term}%")
                    ->select('email')
                    ->limit(10) // Reduce the number of results
                    ->get();

        return response()->json($emails->pluck('email')); // Return only emails
    }

    public function userCreditsOld(Request $Request,$user_email){
        if(Session::has('adminSession')){ 
            Session::put('active',22); 

            if($Request->ajax()){
                $_SESSION['active']=22;
                switch ($_REQUEST['order'][0]['column'])
                {
                    case '1' :
                                $orderby= "id";
                                break ;

                    default : $orderby="id";
                                  break;                
                }
                $dir = $_REQUEST['order'][0]['dir'];
                if ($_REQUEST['length'] !=-1){
                    $limit=$_REQUEST['length'];
                } 
                else{
                    $limit="all";
                }
                $conditions = array();
                $query  = DB::table('credits')->where($conditions)->count();
                $data = $Request->input();
                $querys = DB::table('credits'); 
                /*if($user_email=="k2h4kr3ml5flflmfl242l4"){
                    $user_email= "sunil_guptafca@yahoo.co.in";
                }else{
                    $user_email=convert_uudecode(base64_decode($user_email));
                }*/
                $user_email = User::getUserEmail($user_email);
                $querys = $querys->where('user_email','like','%'.$user_email.'%');
                $iTotalRecords=$query;
                $iDisplayLength = intval($_REQUEST['length']);
                $iDisplayStart = intval($_REQUEST['start']);
                $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength; 
                $iTotalRecords=$query;
                $querys =  $querys->where($conditions)
                    ->skip($iDisplayStart)->take($iDisplayLength)
                    ->OrderBy($orderby,'Desc')
                    ->get();
                $iTotalRecords = count($querys);
                $sEcho = intval($_REQUEST['draw']);
                $records = array();
                $records["data"] = array(); 
                $end = $iDisplayStart + $iDisplayLength;
                $end = $end > $iTotalRecords ? $iTotalRecords : $end;
                $i=0;
                $querys=json_decode( json_encode($querys), true);
                //echo "<pre>"; print_r($querys); die;
                foreach($querys as $credit){
                    $id= $credit['id']; 
                    $actionValues='';
                    $num = ++$i;
                    if($credit['remarks']!="Credit Availed"){
                        if(!empty($credit['remarks'])){
                            $remarks = "Credit Added (".$credit['remarks'].")";
                        }else{
                            $remarks = "Credit Added";    
                        }
                        $amountType = "+";
                    }else{
                        $remarks = $credit['remarks'];  
                        $amountType = "-";  
                    }
                    $records["data"][] = array(
                        $credit['id'],
                        $credit['user_email'],
                        $amountType.$credit['amount'],
                        $remarks,
                        $credit['order_id'],
                        $credit['created_by'],   
                        $credit['created_at']
                    );
                        if (isset($_REQUEST["customActionType"]) && $_REQUEST["customActionType"] == "group_action") {
                             $records["customActionStatus"] = "OK"; // pass custom message(useful for getting status of group actions)
                             $records["customActionMessage"] = "Group action successfully has been completed. Well done!"; // pass custom message(useful for getting status of group actions)
                        }
                }
                $records["draw"] = $sEcho;
                $records["recordsTotal"] = $iTotalRecords;
                $records["recordsFiltered"] = $iTotalRecords;
                return response()->json($records);
            }
            /*if($user_email=="k2h4kr3ml5flflmfl242l4"){
                $user_email= "sunil_guptafca@yahoo.co.in";
            }else{
                $user_email=convert_uudecode(base64_decode($user_email));
            }*/
            $user_email = User::getUserEmail($user_email);
            $title = "".$user_email." Credits";
            $remainingCreditsQry = User::select('credit')->where('email',$user_email)->first();
            if(isset($remainingCreditsQry->credit)){
                $remainingCredits = $remainingCreditsQry->credit;
            }else{
                $remainingCredits = 0; 
            }
            return View::make('admin.credits.user_credits')->with(compact('title','remainingCredits'));
        } else {
            return redirect()->action('CreditController@login')->with('flash_message_error', 'Please Login');
        }
         
    }

    public function userCredits($userid){
        Session::put('page','credits');

        $userCount = User::where('id',$userid)->count();
        if($userCount==0){
            return redirect()->back()->with('flash_message_error','User does not exists!');
        }else{
            $credits = UsersCredit::where('user_id',$userid)->get()->toArray();    
        }
        
        foreach($credits as $key => $credit){
            $credits[$key]['user_id_link'] = "<a style='color:#155724' href='/admin/users?email=".$credit['user_email']."'>".$credit['user_id']."</a>";
            $credits[$key]['user_email_link'] = "<a style='color:#155724' href='/admin/users?email=".$credit['user_email']."'>".$credit['user_email']."</a>";
            if($credit['order_id']!="" && $credit['order_id']!=0){
                $credits[$key]['user_order_link'] = "<a style='color:#155724' href='/admin/orders/".$credit['order_id']."'>".$credit['order_id']."</a>";    
            }else{
                $credits[$key]['user_order_link'] = "";
            }
            
        }

        //dd($credits);

        // Set Admin/Subadmins Permissions for Users Credits
        $creditsModuleCount = AdminsRole::where(['subadmin_id'=>Auth::guard('admin')->user()->id,'module'=>'credits'])->count();
        $creditsModule = array();
        if(Auth::guard('admin')->user()->type=="admin"){
            $creditsModule['view_access'] = 1;
            $creditsModule['edit_access'] = 1;
            $creditsModule['full_access'] = 1;
        }else if($creditsModuleCount==0){
            $message = "This feature is restricted for you!";
            return redirect('admin/dashboard')->with('error_message',$message);
        }else{
            $creditsModule = AdminsRole::where(['subadmin_id'=>Auth::guard('admin')->user()->id,'module'=>'credits'])->first()->toArray();
        }

        $userDetails = User::select('name','credit')->where('id',$userid)->first();
        if(isset($userDetails->credit)){
            $remainingCredits = $userDetails->credit;
        }else{
            $remainingCredits = 0; 
        }
        return view('admin.credits.user_credits')->with(compact('credits','creditsModule','userDetails','remainingCredits'));
    }

    public function addCredit(Request $request){

        if($request->isMethod('post')){
            $data = $request->all();
            $data = json_decode(json_encode($data));
            /*echo "<pre>"; print_r($data); die;*/

            $userCount = User::select('email')->where('email',$data->user_email)->count();
            if($userCount==0){
                return redirect()->back()->with('flash_message_error','User does not exists!');
            }else{
                $userDetails = User::select('id')->where('email',$data->user_email)->first();
            }

            if($data->amount==0){
                return redirect()->back()->with('flash_message_error','Credit Amount must be more or less than 0 but not 0');
            }

            $credit = new UsersCredit;
            $credit->user_email = $data->user_email;
            $credit->user_id = $userDetails->id;
            $credit->amount = $data->amount;
            if($data->amount>0){
                $credit->type = 'credit';
                if($data->order_id!=""){
                    $credit->action = "₹".$data->amount." Credit Amount added for Order #".$data->order_id;   
                }else{
                    $credit->action = "₹".$data->amount." Credit Amount added";
                }
            }else{
                $credit->type = 'debit';
                if($data->order_id!=""){
                    $credit->action = "Credit Amount subtracted for Order #".$data->order_id;   
                }else{
                    $credit->action = "₹".$data->amount." Credit Amount subtracted";
                }
            }
            $credit->remarks = $data->remarks;
            $credit->order_id = $data->order_id;
            $credit->expiry_date = $data->expiry_date;
            $credit->created_by = Auth::guard('admin')->user()->name;
            $credit->ip_address = $_SERVER['REMOTE_ADDR'];
            $credit->status = 1;
            $credit->save();

            User::where(['email' => $data->user_email])->update(['credit' => DB::raw('credit + '.$data->amount.'')]);

            $userDetails = User::where(['email' => $data->user_email])->first();

            /*if(!empty($data->sendsms) && $data->sendsms == 1){*/

                if(empty($data->order_id)){
                    $text = "Dear ".$userDetails->name.", Your ".config('constants.project_name')." Wallet has been credited with Rs. ".$data->amount." T&C apply. For more information, write us to ".config('constants.project_email')."";
                }else{
                    $text = "Dear ".$userDetails->name.", Your ".config('constants.project_name')." Wallet has been credited with Rs. ".$data->amount." against order no.".$data->order_id.". T&C apply. For more information, write us to ".config('constants.project_email')."";
                }
 
                /*Code for SMS Script Start*/
                /*$smsdetails['message'] = $text;
                $smsdetails['mobile'] = $userDetails->mobile;
                SMS::sendSms($smsdetails);*/
                /*Code for SMS Script Ends*/

            /*}*/

            

            if($data->amount>0){

                if(env('MAIL_MODE') == "live"){
                    $email = $data->user_email;
                    $messageData = [
                        'email' => $data->user_email,
                        'name' => $userDetails['name'],
                        'mobile' => $userDetails['mobile'],
                        'credit_amount' => $data->amount
                    ];

                    Mail::send('emails.credit', $messageData, function($message) use ($email){
                        $message->to($email)->subject('Credit Added '.config('constants.project_name'));
                    });
                }    
            }

            
            return redirect('admin/credits')->with('flash_message_success','Credit has been added successfully');
        }

        return view('admin.credits.add_credit');
    }

    public function checkUserEmail(Request $request){
        $data = $request->all();
        print_r($data); die;
        $user_email = $data['user_email'];
        $check_email = DB::table('users')
                       ->where('email', $user_email)
                       ->first();
        $count = count($check_email);
        if($count == 0) {
            echo '{"valid":false}';die;
        } else {
            echo '{"valid":true}';die;;
        }
    }

    public function expireCredits(){
        $now = Carbon::now();
        $now = $now->toDateString();
        $getCredits = UsersCredit::select('id','user_id','user_email','amount')->where('is_expired','No')->where('expiry_date','=', $now)->get();
        $getCredits = json_decode(json_encode($getCredits));
        /*echo "<pre>"; print_r($getCredits); die;*/
        foreach ($getCredits as $key => $val) {
            /*$credit = new UsersCredit;
            $credit->user_email = $val->user_email;
            $credit->user_id = $val->user_id;
            $credit->amount = -($val->amount);
            $credit->type = "debit";
            $credit->action = "Credit of Amount ".$credit->amount." has been expired";
            $credit->remarks = "";
            $credit->order_id = "";
            $credit->expiry_date = "";
            $credit->created_by = "Automatically updated by Credits API";
            $credit->ip_address = $_SERVER['REMOTE_ADDR'];
            $credit->is_expired = 'Yes';
            $credit->status = 1;
            $credit->save();*/

            User::where(['id' => $val->user_id])->update(['credit' => DB::raw('credit - '.$val->amount.'')]);

            UsersCredit::where(['id' => $val->id])->update(['is_expired'=>'Yes','status'=>0]);
        }
         
    }
}
