<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\WidgetService;
use App\Http\Requests\Admin\StoreWidgetRequest;
use App\Http\Requests\Admin\UpdateWidgetRequest;
use Illuminate\Support\Facades\View;
use App\Models\HomeWidget;
use Redirect;
use Session;

class WidgetController extends Controller
{
    protected $widgetService;
    public function __construct(WidgetService $widgetService){
        $this->widgetService = $widgetService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        Session::put('page', 'widgets');
        $result = $this->widgetService->getAll();
        //echo "<pre>"; print_r($result['widgets']); die;
        if($result['status']=="error"){
            return redirect('admin/dashboard')->with('error_message',$result['message']);    
        }else{
            $widgets = $result['widgets'];
            $widgetModule = $result['widgetModule'];
            $title = "Home Widgets";
            return view('admin.widgets.index')->with(compact('title','widgets','widgetModule'));    
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $products       = products();  
        $categories     = rootCategories(); 
        $allCategories  = categories(); 
        $brands         = getbrands();
        $title = "Create Widget";
        return view('admin.widgets.create')->with(compact('title','products','categories','allCategories','brands'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreWidgetRequest $request)
    {
        $data = $request->all();
        $this->widgetService->createOrUpdate($request);
        $message = __('Widget has been created successfully');
        \Session::flash('flash_message_success',$message);
        $result['url'] = url('/admin/widgets');
        return response()->json(ajaxSuccessResponse($message,$result),200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $widget = $this->widgetService->find($id);
        //echo "<pre>"; print_r($widget); die;
        $products       = products();  
        $categories     = rootCategories(); 
        $allCategories  = categories(); 
        $brands         = getbrands();
        $title = "Edit Widget";
        return view('admin.widgets.edit')->with(compact('title','widget','products','categories','allCategories','brands'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateWidgetRequest $request, $id)
    {
        //
        $this->widgetService->createOrUpdate($request,$id);
        $message = __('Widget has been updated successfully');
        \Session::flash('flash_message_success',$message);
        $result['url'] = url('/admin/widgets');
        return response()->json(ajaxSuccessResponse($message,$result),200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $this->widgetService->destroy($id);
        $message = __('Widget has been deleted successfully');
        \Session::flash('flash_message_success',$message);
        return redirect::to('admin/widgets');
    }

    public function appendWidget(Request $request){
        $data = $request->all();
        $products       = products();  
        $categories     = rootCategories(); 
        $brands         = getbrands();
        $allCategories  = categories();
        $widgetType = $data['widgetType'];
        $getMultipleBannerParents = array();
        if($widgetType == "MULTIPLE_BANNERS"){
            $getMultipleBannerParents = HomeWidget::where('type','MULTIPLE_BANNERS')->wherenull('parent_id')->get()->toArray();
        }
        return response()->json([
            'view' => (String)View::make('admin.widgets.partials.widgets')->with(compact('widgetType','products','categories','brands','getMultipleBannerParents','allCategories'))
        ]);
    }

    public function updateSort(Request $request)
{
    $orderData = $request->input('order');

    //\Log::info('Sort Order Data:', $orderData);

    foreach ($orderData as $parent) {
        // Update the parent widget's sort order
        $parentWidget = HomeWidget::find($parent['id']);
        if ($parentWidget) {
            $parentWidget->sort = $parent['sort'];
            $parentWidget->save();
        }

        // Update the child widgets' sort order
        if (!empty($parent['children'])) {
            foreach ($parent['children'] as $child) {
                $childWidget = HomeWidget::find($child['id']);
                if ($childWidget) {
                    /*\Log::info('Updating child widget', [
                        'id' => $child['id'],
                        'sort' => $child['sort'],
                        'parent_id' => $parent['id']
                    ]);*/

                    // Update the child widget with new sort order and parent_id
                    $childWidget->sort = $child['sort'];
                    $childWidget->parent_id = $parent['id'];
                    $childWidget->save();
                }
            }
        }
    }

    return response()->json(['success' => true, 'message' => 'Sort order updated successfully.']);
}



}
