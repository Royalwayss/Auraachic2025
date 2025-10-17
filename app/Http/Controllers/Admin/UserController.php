<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use App\Models\SearchResult;
use App\Models\AdminsRole;
use App\Models\Subscriber;
use App\Models\User;
use Session;
use Auth;

class UserController extends Controller
{
    public function usersOld(){
        Session::put('page','users');
        if(isset($_GET['email'])){
            $users = User::where('email',$_GET['email'])->get()->toArray(); 
        }else{
            $users = User::get()->toArray();    
        }
        /*dd($users);*/

        // Set Admin/Subadmins Permissions for Users
        $usersModuleCount = AdminsRole::where(['subadmin_id'=>Auth::guard('admin')->user()->id,'module'=>'users'])->count();
        $usersModule = array();
        if(Auth::guard('admin')->user()->type=="admin"){
            $usersModule['view_access'] = 1;
            $usersModule['edit_access'] = 1;
            $usersModule['full_access'] = 1;
        }else if($usersModuleCount==0){
            $message = "This feature is restricted for you!";
            return redirect('admin/dashboard')->with('error_message',$message);
        }else{
            $usersModule = AdminsRole::where(['subadmin_id'=>Auth::guard('admin')->user()->id,'module'=>'users'])->first()->toArray();
        }

        return view('admin.users.users')->with(compact('users','usersModule'));
    }

    public function users(Request $request)
    {
        Session::put('page', 'users');

        // Check Admin/Subadmin Permissions for Users
        $usersModuleCount = AdminsRole::where([
            'subadmin_id' => Auth::guard('admin')->user()->id,
            'module' => 'users'
        ])->count();

        $usersModule = [];
        if (Auth::guard('admin')->user()->type == "admin") {
            $usersModule = [
                'view_access' => 1,
                'edit_access' => 1,
                'full_access' => 1
            ];
        } elseif ($usersModuleCount == 0) {
            return redirect('admin/dashboard')->with('error_message', 'This feature is restricted for you!');
        } else {
            $usersModule = AdminsRole::where([
                'subadmin_id' => Auth::guard('admin')->user()->id,
                'module' => 'users'
            ])->first()->toArray();
        }

        // Handle AJAX request from DataTable
        if ($request->ajax()) {
            // Check for single user fetch
            if ($request->has('id') && is_numeric($request->id)) {
                $user = User::find($request->id);
                if (!$user) {
                    return response()->json(['error' => 'User not found'], 404);
                }
                $query = collect([$user]); // wrap single user in a collection
            } else {
                $query = User::query();

                // Optional filter by email if present
                if ($request->has('email')) {
                    $query->where('email', $request->email);
                }
            }

            return DataTables::of($query)
                ->addColumn('registered_on', function ($user) {
                    return date("F j, Y, g:i a", strtotime($user->created_at));
                })
                ->addColumn('actions', function ($user) use ($usersModule) {
                    $toggleStatus = $user->status == 1 ? 'fa-toggle-on' : 'fa-toggle-off';
                    $toggleColor = $user->status == 1 ? '#3f6ed3' : 'grey';
                    $statusText = $user->status == 1 ? 'Active' : 'Inactive';

                    return '<a class="updateUserStatus" id="user-' . $user->id . '" 
                        user_id="' . $user->id . '" style="color:' . $toggleColor . '" 
                        href="javascript:void(0)">
                        <i class="fas ' . $toggleStatus . '" status="' . $statusText . '"></i></a>';
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return view('admin.users.users', compact('usersModule'));
    }


    public function updateUserStatus(Request $request){
        if($request->ajax()){
            $data = $request->all();
            /*echo "<pre>"; print_r($data); die;*/
            if($data['status']=="Active"){
                $status = 0;
            }else{
                $status = 1;
            }
            User::where('id',$data['user_id'])->update(['status'=>$status]);
            return response()->json(['status'=>$status,'user_id'=>$data['user_id']]);
        }
    }

    public function subscribers(){
        Session::put('page','subscribers');
        if(isset($_GET['email'])){
            $subscribers = Subscriber::where('email',$_GET['email'])->get()->toArray(); 
        }else{
            $subscribers = Subscriber::get()->toArray();    
        }
        /*dd($subscribers);*/

        // Set Admin/Subadmins Permissions for Subscribers
        $subscribersModuleCount = AdminsRole::where(['subadmin_id'=>Auth::guard('admin')->user()->id,'module'=>'subscribers'])->count();
        $subscribersModule = array();
        if(Auth::guard('admin')->user()->type=="admin"){
            $subscribersModule['view_access'] = 1;
            $subscribersModule['edit_access'] = 1;
            $subscribersModule['full_access'] = 1;
        }else if($subscribersModuleCount==0){
            $message = "This feature is restricted for you!";
            return redirect('admin/dashboard')->with('error_message',$message);
        }else{
            $subscribersModule = AdminsRole::where(['subadmin_id'=>Auth::guard('admin')->user()->id,'module'=>'subscribers'])->first()->toArray();
        }

        return view('admin.subscribers.subscribers')->with(compact('subscribers','subscribersModule'));
    }

    public function updateSubscriberStatus(Request $request){
        if($request->ajax()){
            $data = $request->all();
            /*echo "<pre>"; print_r($data); die;*/
            if($data['status']=="Active"){
                $status = 0;
            }else{
                $status = 1;
            }
            Subscriber::where('id',$data['subscriber_id'])->update(['status'=>$status]);
            return response()->json(['status'=>$status,'subscriber_id'=>$data['subscriber_id']]);
        }
    }

    public function searchResults(Request $request) { 
        Session::put('page','search-enquiries'); 
		if ($request->ajax()) {
            $query = SearchResult::select(
                        'search_results.*',
                        'users.name as user_name',
                        'users.email as user_email'
                    )
                    ->leftJoin('users', 'search_results.user_id', '=', 'users.id');

            // ✅ Global Search (Include user fields)
            if ($request->input('search')['value']) {
                $search = $request->input('search')['value'];
                $query->where(function ($q) use ($search) {
                    $q->where('search_results.query', 'like', "%{$search}%")
                      ->orWhere('search_results.count', 'like', "%{$search}%")
                      ->orWhere('users.name', 'like', "%{$search}%")
                      ->orWhere('users.email', 'like', "%{$search}%");
                });
            }

            // ✅ Debug the SQL Query
            \Log::info($query->toSql());

            return DataTables::of($query)
                ->addColumn('user_name', function($search) {
                    return $search->user_name ?? 'Guest';
                })
                ->addColumn('email', function($search) {
                    return $search->user_email ?? '-';
                })
                ->addColumn('user_type', function($search) {
                    return $search->user_name ? 'Registered User' : 'Guest';
                })
                ->addColumn('searched_on', function($search) {
                    return date('F j, Y, g:i a', strtotime($search->created_at));
                })
                ->rawColumns(['user_name', 'email', 'user_type', 'searched_on'])
                ->make(true);
        }

       

        // ✅ Set Admin/Subadmin Permissions for Search Results
        $searchesModule = [];
        $user = Auth::guard('admin')->user();

        if ($user->type == "admin") {
            $searchesModule = [
                'view_access' => 1,
                'edit_access' => 1,
                'full_access' => 1
            ];
        } else {
            $searchModule = AdminsRole::where([
                'subadmin_id' => $user->id,
                'module' => 'search_results'
            ])->first();

            if (!$searchModule) {
                return redirect('admin/dashboard')->with('error_message', 'This feature is restricted for you!');
            }

            $searchesModule = $searchModule->toArray();
        }

        return view('admin.users.search_results')->with(compact('searchesModule'));
    }
}
