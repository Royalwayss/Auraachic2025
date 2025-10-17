<?php
namespace App\Models;

use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Hash;
use Validator;
use Auth;
class WebSetting extends Model{
	
    
    protected $table = 'website_settings';
	
	public static function settings(){
		$websettings['site_logo'] = asset('front/assets/img/logo.png');
		$websettings['site_name'] = config('constants.project_name');
		$websettings['site_email'] = 'auraachicin@gmail.com';
		$websettings['admin_emails'] =array('rwpttech@gmail.com'); 
		$websettings['admin_bcc_emails'] = [];
		return $websettings;
	}

	
	
	
}
