<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Mail;
use Session;
use Auth;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'address',
        'city',
        'state',
        'country',
        'pincode',
        'mobile',
        'email',
        'password',
        'credit',
        'remember_token',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public static function info($cart_session_id=null){
        if(Auth::check()){
            //for Website
            $data['user_id'] = Auth::user()->id;
            $data['user_details'] = Auth::user();
        }/*else if(Auth::guard('api')->check()){
            //for Apis
            $data['user_id'] = Auth::guard('api')->user()->id;
            $data['user_details'] = Auth::guard('api')->user();
        }*/else{
            if(isset($cart_session_id) && !empty($cart_session_id)){
                //it will come only in case of Apis
                $data['cart_session_id'] = $cart_session_id;
            }else if(!Session::has('cartsessionId')){
                //it will come only in case of Website 
                $session_id = Session::getId();
                Session::put('cartsessionId',$session_id);
                $data['cart_session_id'] = $session_id;
            }else{
                $data['cart_session_id'] = Session::get('cartsessionId');
            }
        }
        return $data;
    }

    public static function getUserDetails($user_id){
        $getUserDetails = User::select('name','mobile','email')->where('id',$user_id)->first()->toArray();
        return $getUserDetails;
    }
}
