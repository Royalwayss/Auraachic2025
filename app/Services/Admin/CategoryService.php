<?php 

namespace App\Services\Admin;
use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Models\Category;
use App\Models\Product;
use App\Models\AdminsRole;
use Auth;
use DB;

class CategoryService {

    public function categories(){
        $categories = Category::with('parentcategory')->get();
        // Set Admin/Subadmins Permissions for Categories
        $categoriesModuleCount = AdminsRole::where(['subadmin_id'=>Auth::guard('admin')->user()->id,'module'=>'categories'])->count();
        $status = "success";
        $message = "";
        $categoriesModule = array();
        if(Auth::guard('admin')->user()->type=="admin"){
            $categoriesModule['view_access'] = 1;
            $categoriesModule['edit_access'] = 1;
            $categoriesModule['full_access'] = 1;
        }else if($categoriesModuleCount==0){
            $status = "error";
            $message = "This feature is restricted for you!";
        }else{
            $categoriesModule = AdminsRole::where(['subadmin_id'=>Auth::guard('admin')->user()->id,'module'=>'categories'])->first()->toArray();
        }
        return array("categories"=>$categories,"categoriesModule"=>$categoriesModule,"status"=>$status,"message"=>$message);
    }

    public function updateCategoryStatus($data){
        if($data['status']=="Active"){
            $status = 0;
        }else{
            $status = 1;
        }
        Category::where('id',$data['category_id'])->update(['status'=>$status]);
        return $status;
    }

    public function deleteCategory($id){
        // Delete Category
        Category::where('id',$id)->delete();
        $message = "Category deleted successfully!";
        return $message;
    }

    public function addEditCategory($request){
        $getCategories = Category::getCategories($type='Admin');
        $data = $request->all();
        /*echo "<pre>"; print_r($data); die;*/
        if(isset($data['id']) && $data['id']!=""){
            // Edit Category
            $category = Category::find($data['id']);
            $message = "Category updated successfully!";
        }else{
            // Add Category
            $category = new Category;
            $message = "Category added successfully!";    
        }
        // Upload Category Image
        if($request->hasFile('category_image')){
            $image_tmp = $request->file('category_image');
            if($image_tmp->isValid()){
                // create image manager with desired driver
                $manager = new ImageManager(new Driver());
                // read image from file system
                $image = $manager->read($image_tmp);
                // Get Image Extension
                $extension = $image_tmp->getClientOriginalExtension();
                // Generate New Image Name
                $imageName = rand(111,99999).'.'.$extension;
                $image_path = 'front/images/categories/'.$imageName;
                // Upload the Category Image
                $image->save($image_path);
                $category->category_image = $imageName;
            }
        }

        // Upload Size Chart
        if($request->hasFile('size_chart')){
            $sizechart_tmp = $request->file('size_chart');
            if($sizechart_tmp->isValid()){
                // create image manager with desired driver
                $manager = new ImageManager(new Driver());
                // read image from file system
                $image = $manager->read($sizechart_tmp);
                // Get Image Extension
                $sizechart_extension = $sizechart_tmp->getClientOriginalExtension();
                // Generate New Image Name
                $sizechartimageName = rand(111,99999).'.'.$sizechart_extension;
                $sizechart_image_path = 'front/images/sizecharts/'.$sizechartimageName;
                // Upload the Size Chart
                $image->save($sizechart_image_path);
                $category->size_chart = $sizechartimageName;
            }
        }else{
            if(isset($data['size_chart']) && $data['size_chart']!=""){
                $category->size_chart = $data['size_chart'];
            }
        }

        

        if(empty($data['category_discount'])){
            $data['category_discount'] = 0;
            if(isset($data['id']) && $data['id']!=""){
                $categoryProducts = Product::where('category_id',$data['id'])->get()->toArray();
                foreach ($categoryProducts as $key => $product) {
                    /*echo "<pre>"; echo "<pre>"; print_r($product); die;*/
                    if($product['discount_type']=="category"){
                        Product::where('id',$product['id'])->update(['discount_type'=>'','product_discount'=>'','final_price'=>$product['product_price']]);    
                    }
                }
            }
        }else{
            // Add Category Discount to all products belongs to the specific Category
            Product::wherein('discount_type',['','category'])->where('category_id',$data['id'])
                ->update(array(
                    'discount_type' =>'category',
                    'product_discount' => $data['category_discount'],
                    'final_price' => DB::raw('product_price - (product_price * '.$data['category_discount'].' / 100.0)')
                ));
        }

        $data['category_name'] = str_replace("-"," ",ucwords(strtolower($data['category_name'])));
        $data['url'] = str_replace(" ","-",strtolower($data['url']));

        /*echo "<pre>"; print_r($data); die;*/

        $category->category_name = $data['category_name'];
        $category->parent_id = $data['parent_id'];
        $category->category_discount = $data['category_discount'];
        $category->description = $data['description'];
        /*$category->size_chart = $data['size_chart'];*/
        $category->url = $data['url'];
        $category->meta_title = $data['meta_title'];
        $category->meta_description = $data['meta_description'];
        $category->meta_keywords = $data['meta_keywords'];

        /*if(isset($data['filters'])){
            $category->filters =  implode(',', $data['filters']);
        }*/
        
        $category->status = 1;

        if(!empty($data['menu_status'])){
            $category->menu_status = 1;
        }else{
            $category->menu_status = 0;
        }

        $category->save();
        return $message;
        
    }

    
}

?>