<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\WidgetService;
use App\Http\Requests\Admin\StoreWidgetRequest;
use App\Http\Requests\Admin\UpdateWidgetRequest;
use Illuminate\Support\Facades\View;
use App\Models\DataFeedFile;
use Redirect;
use Session;

class FeedFileController extends Controller
{
   
    public function createFeedFile()
    {
       DataFeedFile::CreeateDataFeedFile();
       DataFeedFile::CreeateDataFacebookFeedFile();
	   
	   
	   
	   $response = [
        'status' => 'success',
        'message' => 'Feed file created successfully.',
       ];
	   
	   echo json_encode($response);

	   
	   
	   
	   
    }



}
