<?php

namespace App\Http\Controllers\Admin;

use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductsCategory;
use App\Models\ProductsImage;
use App\Models\ProductsAttribute;
use App\Models\AdminsRole;
use App\Models\Category;
use App\Models\Brand;
use App\Models\ReturnRequest;
use App\Models\OrdersProduct;
use App\Models\User;
use App\Models\State;
use Session;
use DB;
use Auth;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Services\Admin\ProductService;
use App\Http\Requests\Admin\ProductRequest;

class ProductController extends Controller
{
    public function products(){

        Session::put('page','products');
        $service = new ProductService();
        $result = $service->products();
        if($result['status']=="error"){
            return redirect('admin/dashboard')->with('error_message',$result['message']);    
        }else{
            $products = $result['products'];
            $productsModule = $result['productsModule'];
            return view('admin.products.products')->with(compact('products','productsModule'));    
        }
    }

    public function updateProductStatus(Request $request)
    {
        if($request->ajax()){
            $data = $request->all();
            /*echo "<pre>"; print_r($data); die;*/
            $service = new ProductService();
            $status = $service->updateProductStatus($data);
            return response()->json(['status'=>$status,'product_id'=>$data['product_id']]);
        }
    }

    public function updateAttributeStatus(Request $request){
        if($request->ajax()){
            $data = $request->all();
            /*echo "<pre>"; print_r($data); die;*/
            $service = new ProductService();
            $status = $service->updateAttributeStatus($data);
            return response()->json(['status'=>$status,'attribute_id'=>$data['attribute_id']]);
        }
    }

    public function deleteAttribute($id){
        $service = new ProductService();
        $message = $service->deleteAttribute($id);
        return redirect()->back()->with('success_message',$message);
    }

    public function deleteProduct($id)
    {
        $service = new ProductService();
        $message = $service->deleteProduct($id);
        return redirect()->back()->with('success_message',$message);
    }

    public function addEditProduct(Request $request,$id=null){
        ini_set('memory_limit','256M');
        Session::put('page','products');
        if($id==""){
            // Add Product
            $title = "Add Product";
            $product = new Product;
            $productCats = array();
            // Next & Prev Products
            $prevId = 0; 
            $nextId = 0;
        }else{
            // Edit Product
            $title = "Edit Product";
            $product = Product::with(['images','attributes'])->find($id);
            /*dd($product['attributes'][0]['sku']);*/
            $productCats = ProductsCategory::where('product_id',$id)->select('category_id')->pluck('category_id')->toArray();

            // Next & Prev Products
            $model = 'Product'; // Fully qualified model name
            $prevId = findPreviousId($id, $model); // Start checking with $id - 1
            $nextId = findNextId($id, $model);  // Start checking with $id + 1
        }

        // Get Categories and their Sub Categories
        $getCategories = Category::getCategories($type='Admin');

        // Product Filters
        $productsFilters = Product::productsFilters();
       
        // Get All Brands
        $brands = Brand::where('status',1)->get();
        $brands = json_decode(json_encode($brands),true);
        $product_filters = Product::product_filters();   
        return view('admin.products.add_edit_product')->with(compact('title','getCategories','product_filters','product','brands','productCats','prevId','nextId'));
    }

    public function addEditProductRequest(Request $request,$id=null){
        if($request->isMethod('post')){
            $service = new ProductService();
            $result = $service->addEditProduct($request);
            return redirect('admin/products')->with('success_message',$result['message']);
        }
    }

    public function deleteProductVideo($id){
        $service = new ProductService();
        $message = $service->deleteProductVideo($id);
        return redirect()->back()->with('success_message',$message);
    }

    public function deleteProductImage($id){
        $service = new ProductService();
        $message = $service->deleteProductImage($id);
        return redirect()->back()->with('success_message',$message);
    }

    public function deleteProductBanner($id){
        $service = new ProductService();
        $message = $service->deleteProductBanner($id);
        return redirect()->back()->with('success_message',$message);
    }

    public function deleteProductMainImage($id){
        $service = new ProductService();
        $message = $service->deleteProductMainImage($id);
        return redirect()->back()->with('success_message',$message);
    }

    public function deleteProductVideoThumbnail($id){
        $service = new ProductService();
        $message = $service->deleteProductVideoThumbnail($id);
        return redirect()->back()->with('success_message',$message);
    }

    public function returnRequests(){
        Session::put('page','return_requests');
        $service = new ProductService();
        $result = $service->returnRequests();
        if($result['status']=="error"){
            return redirect('admin/dashboard')->with('error_message',$result['message']);    
        }else{
            $returnRequests = $result['returnRequests'];
            $returnModule = $result['returnModule'];
            return view('admin.orders.return_requests')->with(compact('returnRequests','returnModule'));   
        }
    }

    public function returnRequestUpdate(Request $request){
        if($request->isMethod('post')){
            $service = new ProductService();
            $result = $service->returnRequestUpdate($request);
            if(isset($result['return_status'])){
                return redirect('admin/return-requests')->with('success_message', 'Return Request '.$result.'!');      
            }            
        }
    }

    public function updateAttributeStaus(Request $request){
        if($request->ajax()){
            $service = new ProductService();
            $status = $service->updateAttributeStaus($request);
        }
    }

    public function taxes(){
        Session::put('page','taxes');
        $state = null;
        if(isset($_GET['state'])){
            $state = $_GET['state'];
        }
        $service = new ProductService();
        $result = $service->taxes($state);
        if($result['status']=="error"){
            return redirect('admin/dashboard')->with('error_message',$result['message']);    
        }else{
            $taxes = $result['taxes'];
            $taxesModule = $result['taxesModule'];
            return view('admin.taxes.taxes')->with(compact('taxes','taxesModule'));   
        }
    }

    public function updateTaxes(Request $request){
        if($request->isMethod('post')){
            $service = new ProductService();
            $message = $service->updateTaxes($request);
            return redirect()->back()->with('success_message', $message);
        }
    }

    public function updateStock(Request $request){
        Session::put('active',4); 
        if($request->isMethod('post')){
            $data = $request->all();
            if($request->hasFile('stock_file')){
                $file=$request->file('stock_file');
                $file->getClientOriginalName();
                $file->move('file' , "stock.csv");

            }else{
                echo 'Not Uploaded';
            }

            $file = public_path('file/stock.csv');
            $handle = fopen($file,"r");
            $header = fgetcsv($handle, 0, ',');
            $productsArr = $this->csvToArray($file);

            $response ="true";
            $columns = array('SKU','Stock');
            foreach($columns as $key => $pcolumn){
                if(in_array($pcolumn,$header)){
                    //do nothing
                }else{
                    $response="failed";
                    $columnarr[] = $pcolumn;
                }
            }
            if($response=="failed"){
                $columnnames = implode(',',$columnarr);
                return redirect()->back()->with('flash_message_error', 'Your CSV files having unmatched Columns to our database...Missing columns are:- '.$columnnames.' (Case Sensitive)');
            }


            for ($i = 0; $i < count($productsArr); $i ++){
                ProductsAttribute::where(['sku' => $productsArr[$i]['SKU']])->update(['stock' => $productsArr[$i]['Stock']]);
            }

            return redirect()->back()->with('flash_message_success','Products Stock updated Successfully!');
        }
        return view('admin.products.update_stock');
    }

    public function csvToArray($filename = '', $delimiter = ','){
        if (!file_exists($filename) || !is_readable($filename))
            return false;
        $header = null;
        $data = array();
        if (($handle = fopen($filename, 'r')) !== false){
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false){
                if (!$header)
                    $header = $row;
                else
                    $data[] = array_combine($header, $row);
            }
            fclose($handle);
        }
        return $data;
    }

}
