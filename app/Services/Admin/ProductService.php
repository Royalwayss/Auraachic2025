<?php 

namespace App\Services\Admin;
use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Models\Category;
use App\Models\Product;
use App\Models\AdminsRole;
use App\Models\ProductsAttribute;
use App\Models\ProductsCategory;
use App\Models\ProductsImage;
use App\Models\Brand;
use Auth;
use DB;

class ProductService {

    public function products(){
        $products = Product::with('category')->orderby('id','Desc')->get()->toArray();
        /*dd($products);*/
        // Set Admin/Subadmins Permissions for Products
        $productsModuleCount = AdminsRole::where(['subadmin_id'=>Auth::guard('admin')->user()->id,'module'=>'products'])->count();
        $status = "success";
        $message = "";
        $productsModule = array();
        if(Auth::guard('admin')->user()->type=="admin"){
            $productsModule['view_access'] = 1;
            $productsModule['edit_access'] = 1;
            $productsModule['full_access'] = 1;
        }else if($productsModuleCount==0){
            $status = "error";
            $message = "This feature is restricted for you!";
        }else{
            $productsModule = AdminsRole::where(['subadmin_id'=>Auth::guard('admin')->user()->id,'module'=>'products'])->first()->toArray();
        }
        return array("products"=>$products,"productsModule"=>$productsModule,"status"=>$status,"message"=>$message);
    }    

    public function updateProductStatus($data)
    {
        if($data['status']=="Active"){
            $status = 0;
        }else{
            $status = 1;
        }
        Product::where('id',$data['product_id'])->update(['status'=>$status]);
        return $status;
    }

    public function updateAttributeStatus($data){
        if($data['status']=="Active"){
            $status = 0;
        }else{
            $status = 1;
        }
        ProductsAttribute::where('id',$data['attribute_id'])->update(['status'=>$status]);
        return $status;
    }

    public function deleteAttribute($id){
        // Delete Attribute
        ProductsAttribute::where('id',$id)->delete();
        $message = "Product Attribute deleted successfully!";
        return $message;
    }

    public function deleteProduct($id){
        // Delete Product
        Product::where('id',$id)->delete();
        $message = "Product deleted successfully!";
        return $message;
    }

    public function addEditProduct($request){
        $data = $request->all();
        if(isset($data['id']) && $data['id']!=""){
            // Edit Product
            $product = Product::with(['images','attributes'])->find($data['id']);
            /*dd($product['attributes'][0]['sku']);*/
            $productCats = ProductsCategory::where('product_id',$data['id'])->select('category_id')->pluck('category_id')->toArray();
            $message = 'Product updated successfully!';
        }else{
            // Add Product
            $product = new Product;
            $productCats = array();
            $message = 'Product added successfully!';    
        }

            DB::beginTransaction();

            // Upload Product Video
            if($request->hasFile('product_video')){
                $video_tmp = $request->file('product_video');
                if($video_tmp->isValid()){
                    // Upload Video
                    /*$videoName = $video_tmp->getClientOriginalName();*/
                    $videoExtension = $video_tmp->getClientOriginalExtension();
                    $videoName = rand().'.'.$videoExtension;
                    $videoPath = "front/videos/products/";
                    $video_tmp->move($videoPath,$videoName);
                    // Save Video name in products table
                    $product->product_video = $videoName;
                }
            }

            if(!isset($data['product_discount'])){
                $data['product_discount'] = 0;
            }

            if(!isset($data['product_weight'])){
                $data['product_weight'] = 0;
            }

            $product->brand_id = $data['brand_id'];
            $product->category_id = $data['category_id'];
            $product->product_name = $data['product_name'];
            $product->product_url = $data['product_url'];
            $product->product_code = $data['product_code'];
            $product->product_color = $data['product_color'];
            $product->family_color = $data['family_color'];
            $product->group_code = $data['group_code'];
            $product->product_sort = $data['product_sort'];
            $product->product_price = $data['product_price'];
            $product->product_discount = $data['product_discount'];
            $product->product_gst = $data['product_gst'];

            if(isset($data['fabric']) && !empty($data['fabric'])){
                $product->fabric = $data['fabric'];    
            }
            if(isset($data['sleeve']) && !empty($data['sleeve'])){
                $product->sleeve = $data['sleeve'];    
            }
            if(isset($data['fit']) && !empty($data['fit'])){
                $product->fit = $data['fit'];    
            }
            if(isset($data['occasion']) && !empty($data['occasion'])){
                $product->occasion = $data['occasion'];    
            }
            if(isset($data['neck']) && !empty($data['neck'])){
                $product->neck = $data['neck'];    
            }

            if(!empty($data['product_discount'])&&$data['product_discount']>0){
                $product->discount_type = 'product';
                $product->final_price = $data['product_price'] - ($data['product_price'] * $data['product_discount'])/100;
                $product->product_discount_amount = $data['product_price'] * $data['product_discount']/100;
            }else{
                $getCategoryDiscount = Category::select('category_discount')->where('id',$data['category_id'])->first();
                if($getCategoryDiscount->category_discount == 0){
                    $product->discount_type = "";
                    $product->final_price = $data['product_price'];
                    $product->product_discount = 0;
                }else{
                    $product->discount_type = 'category';
                    $product->product_discount = $getCategoryDiscount->category_discount;
                    $product->product_discount_amount = $data['product_price'] * $getCategoryDiscount->category_discount/100;
                    $product->final_price = $data['product_price'] - ($data['product_price'] * $getCategoryDiscount->category_discount)/100;
                }
            }

            $product->product_weight = $data['product_weight'];
            $product->description = $data['description'];
            $product->key_features = $data['key_features'];
            $product->wash_care = $data['wash_care'];
            $product->search_keywords = $data['search_keywords'];                  
            if(!empty($data['is_featured'])){
                $product->is_featured = $data['is_featured'];
            }else{
                $product->is_featured = "No";
            }
            
             if(!empty($data['is_new'])){
                $product->is_new = $data['is_new'];
            }else{
                $product->is_new = "No";
            }
            
            
            
            


            // Upload Product Image
            if($request->hasFile('main_image')){
                $image_tmp = $request->file('main_image');
                if($image_tmp->isValid()){
                    // create image manager with desired driver
                    $manager = new ImageManager(new Driver());
                    // read image from file system
                    $image = $manager->read($image_tmp);
                    // Get Image Extension
                    $extension = $image_tmp->getClientOriginalExtension();
                    // Generate New Image Name
                    $imageName = 'product-'.rand(1111,999999).'.'.$extension;
                    $largeImagePath = 'front/images/products/large/'.$imageName;
                    $mediumImagePath = 'front/images/products/medium/'.$imageName;
                    $smallImagePath = 'front/images/products/small/'.$imageName;
                    // Upload the Large, Medium and Small Images after Resize
                    $image->resize(1000,1500)->save($largeImagePath);
                    $image->resize(500,750)->save($mediumImagePath);
                    $image->resize(120,180)->save($smallImagePath);
                    $product->main_image = $imageName;
                }
            }


            // Upload Product Video Thumbnail
            if($request->hasFile('video_thumbnail')){
                $thumbnail_tmp = $request->file('video_thumbnail');
                if($thumbnail_tmp->isValid()){
                    // create image manager with desired driver
                    $manager = new ImageManager(new Driver());
                    // read image from file system
                    $thumbnail = $manager->read($thumbnail_tmp);
                    // Get Image Extension
                    $extension = $thumbnail_tmp->getClientOriginalExtension();
                    // Generate New Image Name
                    $thumbnailName = 'thumbnail-'.rand(1111,999999).'.'.$extension;
                    $thumbnailImagePath = 'front/videos/thumbnails/'.$thumbnailName;
                    // Upload the Large, Medium and Small Images after Resize
                    $thumbnail->save($thumbnailImagePath);
                    $product->video_thumbnail = $thumbnailName;
                }
            }

            /*
            // Code to convert image to webp
            // Path to the input JPG file
            $inputImage = 'front/images/products/large/'.$imageName;

            // Path to the output WebP file
            $outputImage = 'front/images/products/large/'.$imageName.'.webp';

            // Create an image resource from the JPG file
            $image = imagecreatefromjpeg($inputImage);

            // Check if the image resource was created successfully
            if ($image === false) {
                die('Error: Could not create image from the JPEG file.');
            }

            // Convert the image to WebP format
            $quality = 80; // WebP quality (0-100)
            $result = imagewebp($image, $outputImage, $quality);

            // Check if the conversion was successful
            if ($result) {
                echo 'Image successfully converted to WebP!';
            } else {
                echo 'Error: Failed to convert image to WebP.';
            }

            // Free up memory
            imagedestroy($image); */


            /*if(!empty($data['status'])){
                $product->status =1;
            }else{
                $product->status = 0;
            }*/
            $product->status =1;
            $product->save();

            if(isset($data['id']) && $data['id']!=""){
                $product_id = $data['id'];
                $product->products_categories()->sync($data['cats']);
            }else{
                $product_id = DB::getPdo()->lastInsertId();
                $product->products_categories()->attach($data['cats']); 
            }

            // Upload Product Alt Images
            if($request->hasFile('product_images')){
                $images = $request->file('product_images');
                /*echo "<pre>"; print_r($images); die;*/
                foreach ($images as $key => $image_tmp) {
                    // create image manager with desired driver
                    $manager = new ImageManager(new Driver());
                    // read image from file system
                    $image = $manager->read($image_tmp);
                    // Get Image Name
                    /*$image_name = $image->getClientOriginalName();*/
                    // Get Image Extension
                    $extension = $image_tmp->getClientOriginalExtension();
                    // Generate New Image Name
                    $imageName = 'product-'.rand(1111,999999).'.'.$extension;
                    $largeImagePath = 'front/images/products/large/'.$imageName;
                    $mediumImagePath = 'front/images/products/medium/'.$imageName;
                    $smallImagePath = 'front/images/products/small/'.$imageName;
                    // Upload the Large, Medium and Small Images after Resize
                    $image->resize(1000,1500)->save($largeImagePath);
                    $image->resize(400,600)->save($mediumImagePath);
                    $image->resize(80,120)->save($smallImagePath);
                    // Insert Image Name in products table
                    $image = new ProductsImage; 
                    $image->image = $imageName;
                    $image->product_id = $product_id;
                    $image->status = 1;
                    $image->save();
                }
            }

            if(isset($data['id']) && $data['id']!=""){
                if(isset($data['image'])){
                    foreach ($data['image'] as $ikey => $image) {
                        ProductsImage::where(['product_id'=>$data['id'],'image'=>$image])->update(['image_sort'=>$data['image_sort'][$ikey]]);
                    }
                }
            }

            $total_stock = 0;

            // Add Product Attributes
            foreach ($data['sku'] as $key => $value) {
 
                if(!empty($value)&&!empty($data['size'][$key])&&!empty($data['price'][$key])){

                    // SKU already exists check
                    $attrCountSKU = ProductsAttribute::join('products','products.id','=','products_attributes.product_id')->where('sku',$value)->count();
                    if($attrCountSKU>0){
                        $message = "SKU already exists. Please add another SKU!";
                        return redirect()->back()->with('success_message',$message);
                    }

                    // Size already exists check
                    $attrCountSize = ProductsAttribute::join('products','products.id','=','products_attributes.product_id')->where(['product_id'=>$product_id,'size'=>$data['size'][$key]])->count();
                    if($attrCountSize>0){
                        $message = "Size already exists. Please add another Size!";
                        return redirect()->back()->with('success_message',$message);
                    }
                    if(empty($data['stock'][$key])){
                         $data['stock'][$key] = 0;
                    }
                    $attribute = new ProductsAttribute;
                    $attribute->product_id = $product_id;
                    $attribute->sku = $value;
                    $attribute->size = $data['size'][$key];
                    $attribute->price = $data['price'][$key];
                    if(!empty($data['stock'][$key])){
                    $attribute->stock = $data['stock'][$key];
                    }
                    $attribute->sort = $data['sort'][$key];
                    $attribute->status = 1;
                    $attribute->save();
                    $total_stock = $total_stock + $data['stock'][$key];    
                }
            }

            // Edit Product Attributes
            if(isset($data['id']) && $data['id']!="" && isset($data['attrId'])){  
                foreach ($data['attrId'] as $key => $attr) {
                    if(!empty($attr)){
                        $update_attr = [
                             'price'=>$data['price'][$key],
                             'stock'=>$data['stock'][$key],
                             'sort'=>$data['edit-sort'][$key]
                        ];  
                        ProductsAttribute::where(['id'=>$data['attrId'][$key]])->update($update_attr);
                    }
                }
            }

            //Update Stock on Edit Product
            if(isset($data['attrId'])){
                foreach($data['attrId'] as $attrKeyId => $attrIdDetails){
                    
                    $proAttrUpdate = ProductsAttribute::find($attrIdDetails);
                    $proAttrUpdate->stock = $data['stock'][$attrKeyId];  
                    $total_stock = $total_stock + $data['stock'][$attrKeyId];
                }
            }

            /*echo $total_stock; die;*/

            // Update total stock of Product
            
            
            Product::where('id',$product_id)->update(['stock'=>$total_stock]);

            DB::Commit();

            return array("message"=>$message,'product'=>$product,'productCats'=>$productCats);

    }

    public function deleteProductVideo($id){
        // Get Product Video 
        $productVideo = Product::select('product_video')->where('id',$id)->first();

        // Get Product Video Path
        $product_video_path = 'front/videos/products/';

        // Delete Product Video from folder if exists
        if(file_exists($product_video_path.$productVideo->product_video)){
            unlink($product_video_path.$productVideo->product_video);
        }

        // Delete Product Video Name from products table
        Product::where('id',$id)->update(['product_video'=>'']);

        $message = "Product Video has been deleted successfully!";
        return $message;
    }

    public function deleteProductImage($id){
        // Get Product Image
        $productImage = ProductsImage::select('image')->where('id',$id)->first();

        // Get Product Image Paths
        $small_image_path = 'front/images/products/small/';
        $medium_image_path = 'front/images/products/medium/';
        $large_image_path = 'front/images/products/large/';

        // Delete Product small image if exits in small folder
        if(file_exists($small_image_path.$productImage->image)){
            unlink($small_image_path.$productImage->image);
        } 

        // Delete Product medium image if exits in medium folder
        if(file_exists($medium_image_path.$productImage->image)){
            unlink($medium_image_path.$productImage->image);
        } 

        // Delete Product large image if exits in large folder
        if(file_exists($large_image_path.$productImage->image)){
            unlink($large_image_path.$productImage->image);
        } 

        // Delete Product Image from products table
        ProductsImage::where('id',$id)->delete();

        $message = "Product Image has been deleted successfully!";
        return $message;
    }

    public function deleteProductBanner($id){

        // Get Product Image
        $bannerImage = Product::select('banner')->where('id',$id)->first();

        // Get Product Image Paths
        $banner_image_path = 'front/images/products/banners/';

        // Delete Product banner if exits in banners folder
        if(file_exists($banner_image_path.$bannerImage->banner)){
            unlink($banner_image_path.$bannerImage->banner);
        } 

        // Delete Product banner from products table
        Product::where('id',$id)->update(['banner'=>'']);

        $message = "Product Banner has been deleted successfully!";
        return $message;
    }

    public function deleteProductMainImage($id){

        // Get Product Image
        $productImage = Product::select('main_image')->where('id',$id)->first();

        // Get Product Image Paths
        $small_image_path = 'front/images/products/small/';
        $medium_image_path = 'front/images/products/medium/';
        $large_image_path = 'front/images/products/large/';

        // Delete Product small image if exits in small folder
        if(file_exists($small_image_path.$productImage->main_image)){
            unlink($small_image_path.$productImage->main_image);
        } 

        // Delete Product medium image if exits in medium folder
        if(file_exists($medium_image_path.$productImage->main_image)){
            unlink($medium_image_path.$productImage->main_image);
        } 

        // Delete Product large image if exits in large folder
        if(file_exists($large_image_path.$productImage->main_image)){
            unlink($large_image_path.$productImage->main_image);
        } 

        // Delete Product Image from products table
        Product::where('id',$id)->update(['main_image'=>'']);

        $message = "Product Image has been deleted successfully!";
        return $message;
    }

    public function deleteProductVideoThumbnail($id){

        // Get Product Video Thumbnail
        $productVideo = Product::select('video_thumbnail')->where('id',$id)->first();

        // Get Product Video Thumbnail Path
        $video_thumbnail_path = 'front/videos/thumbnails/';

        // Delete Product Video Thumbnail if exits in thumbnails folder
        if(file_exists($video_thumbnail_path.$productVideo->video_thumbnail)){
            unlink($video_thumbnail_path.$productVideo->video_thumbnail);
        } 

        // Delete Product Video Thumbnail from products table
        Product::where('id',$id)->update(['video_thumbnail'=>'']);

        $message = "Product Video Thumbnail has been deleted successfully!";
        return $message;
    }

    public function returnRequests(){
        $returnRequests = ReturnRequest::with('order','account')->orderby('id','Desc')->get()->toArray();
        //dd($returnRequests);
        // Set Admin/Subadmins Permissions for Return Requests
        $returnModuleCount = AdminsRole::where(['subadmin_id'=>Auth::guard('admin')->user()->id,'module'=>'return_requests'])->count();
        $status = "success";
        $message = "";
        $returnModule = array();
        if(Auth::guard('admin')->user()->type=="admin"){
            $returnModule['view_access'] = 1;
            $returnModule['edit_access'] = 1;
            $returnModule['full_access'] = 1;
        }else if($returnModuleCount==0){
            $status = "error";
            $message = "This feature is restricted for you!";
        }else{
            $returnModule = AdminsRole::where(['subadmin_id'=>Auth::guard('admin')->user()->id,'module'=>'return_requests'])->first()->toArray();
        }
        return array("returnRequests"=>$returnRequests,"returnModule"=>$returnModule,"status"=>$status,"message"=>$message);
    }

    public function returnRequestUpdate($request){
        $data = $request->all();
        /*echo "<pre>"; print_r($data); die;*/
        if(isset($data['return_status'])){
            $comment = "";
            $send_comment = "";
            if(isset($data['comment'])){
                $comment = $data['comment'];
                $comment_datetime = Carbon::now();
            }
            ReturnRequest::where('id',$data['return_id'])->update(['return_status'=>$data['return_status'],'comment'=>$comment,'comment_datetime'=>$comment_datetime]);
            $orderProductDetails = ReturnRequest::updateOrderPro($data['return_id'],$data['return_status']);
            $returnDetails = ReturnRequest::returnDetails($data['return_id']);
            $getUserDetails = User::getUserDetails($returnDetails['user_id']);

            /*Code for SMS Script Start*/
            /*$smsdetails['message'] = "Dear ".$getUserDetails['name'].", your return request at ".config('constants.project_name')." for order #".$returnDetails['order_id']." with Product Code ".$returnDetails['product_code']." has been ".$data['return_status'].". ".$send_comment;
            $smsdetails['mobile'] = $getUserDetails['mobile'];
            SMS::sendSms($smsdetails);*/
            /*Code for SMS Script Ends*/

            $return_status = $data['return_status'];
            $order_id = $returnDetails['order_id'];
            $product_code = $returnDetails['product_code'];
            $email = $getUserDetails['email'];
            $messageData = ['name'=>$getUserDetails['name'],'email'=>$getUserDetails['email'],'returnDetails'=>$returnDetails,'return_status'=>$return_status,'product_code'=>$product_code];
            Mail::send('emails.return_status',$messageData,function($message)use($email,$return_status,$order_id){
                $message->to($email)->subject('Return '.$return_status.' for Order #'.$order_id.' - '.config('constants.project_name').'');
            });
            return $data['return_status'];     
        }
    }

    public function updateAttributeStaus ($request){
        $data = $request->input();
        $product_id = $data['id'];
        $status = $data['status'];
        $attr = $data['attr'];
        if(!empty($product_id) && !empty($status)){
           if($attr == 'is_new') {
             Product::where('id',$product_id)->update([$attr=>$status]);
             $status = "true";
           } else if($attr == 'is_featured') {
             Product::where('id',$product_id)->update([$attr=>$status]);
             $status = "true";
           }
        }
        return $status;
    }

    public function taxes($state=null){
        if($state){
            $taxes = State::where('name',$state)->get()->toArray(); 
        }else{
            $taxes = State::where('country','India')->get()->toArray();    
        }
        /*dd($taxes);*/

        // Set Admin/Subadmins Permissions for Taxes
        $taxesModuleCount = AdminsRole::where(['subadmin_id'=>Auth::guard('admin')->user()->id,'module'=>'taxes'])->count();
        $status = "success";
        $message = "";
        $taxesModule = array();
        if(Auth::guard('admin')->user()->type=="admin"){
            $taxesModule['view_access'] = 1;
            $taxesModule['edit_access'] = 1;
            $taxesModule['full_access'] = 1;
        }else if($taxesModuleCount==0){
            $status = "error";
            $message = "This feature is restricted for you!";
        }else{
            $taxesModule = AdminsRole::where(['subadmin_id'=>Auth::guard('admin')->user()->id,'module'=>'taxes'])->first()->toArray();
        }

        return array("taxes"=>$taxes,"taxesModule"=>$taxesModule,"status"=>$status,"message"=>$message);
    }

    public function updateTaxes($request){
        $data = $request->all();
        /*echo "<pre>"; print_r($data); die;*/
        foreach ($data['state'] as $key => $value) {
            if(!empty($value)&&!empty($data['tax'][$key])){
                State::where('name',$value)->update(['taxes'=>$data['tax'][$key]]);
            }
        }
        $message = "Taxes for the Provinces updated!";
        return $message; 
    }

    
}

?>