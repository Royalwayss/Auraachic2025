<?php

namespace App\Http\Controllers\Admin;

use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\AdminsRole;
use Session;
use Auth;
use DB;
use App\Services\Admin\CategoryService;
use App\Http\Requests\Admin\CategoryRequest;


class CategoryController extends Controller
{
    public function categories(){
        Session::put('page','categories');
        $service = new CategoryService();
        $result = $service->categories();
        $categories = $result['categories'];
        if($result['status']=="error"){
            return redirect('admin/dashboard')->with('error_message',$result['message']);    
        }else{
            $categories = $result['categories'];
            $categoriesModule = $result['categoriesModule'];
            return view('admin.categories.categories')->with(compact('categories','categoriesModule'));    
        }
    }

    public function updateCategoryStatus(Request $request)
    {
        if($request->ajax()){
            $data = $request->all();
            /*echo "<pre>"; print_r($data); die;*/
            $service = new CategoryService();
            $status = $service->updateCategoryStatus($data);
            return response()->json(['status'=>$status,'category_id'=>$data['category_id']]);
        }
    }

    public function deleteCategory($id)
    {
        $service = new CategoryService();
        $message = $service->deleteCategory($id);
        return redirect('admin/categories')->with('success_message',$message);
    }

    public function addEditCategory($id=null){
        $getCategories = Category::getCategories($type='Admin');
        if($id==""){
            // Add Category
            $title = "Add Category";
            $category = new Category;
            // Next & Prev Category
            $prevId = 0; 
            $nextId = 0;
        }else{
            // Edit Category
            $title = "Edit Category";
            $category = Category::find($id);

            // Next & Prev Categories
            $model = 'Category'; // Fully qualified model name
            $prevId = findPreviousId($id, $model); // Start checking with $id - 1
            $nextId = findNextId($id, $model);  // Start checking with $id + 1

        }
        return view('admin.categories.add_edit_category')->with(compact('title','getCategories','category','prevId','nextId'));
    }

    public function addEditCategoryRequest(CategoryRequest $request){
        if($request->isMethod('post')){
            $data = $request->all();
            /*echo "<pre>"; print_r($data); die;*/
            $service = new CategoryService();
            $message = $service->addEditCategory($request);
            return redirect('admin/categories')->with('success_message',$message);
        }
        return view('admin.categories.add_edit_category')->with(compact('title','getCategories','category'));
    }

    public function deleteCategoryImage($id){
        // Get Category Image
        $categoryImage = Category::select('category_image')->where('id',$id)->first();

        // Get Category Image Path
        $category_image_path = 'front/images/categories/';

        // Delete Category Image from categories folder if exists
        if(file_exists($category_image_path.$categoryImage->category_image)){
            unlink($category_image_path.$categoryImage->category_image);
        }

        // Delete Category Image from categories table
        Category::where('id',$id)->update(['category_image'=>'']);

        return redirect()->back()->with('success_message','Category image deleted successfully!');
    }

    public function deleteSizeChartImage($id){
        
        // Get Size Chart
        $sizechartImage = Category::select('size_chart')->where('id',$id)->first();

        // Get Size Chart Path
        $size_chart_image_path = 'front/images/sizecharts/';

        // Delete Size Chart from categories folder if exists
        if(file_exists($size_chart_image_path.$sizechartImage->size_chart)){
            unlink($size_chart_image_path.$sizechartImage->size_chart);
        }

        // Delete Size Chart from categories table
        Category::where('id',$id)->update(['size_chart'=>'']);

        return redirect()->back()->with('success_message','Size Chart deleted successfully!');
    }
}
