<?php

namespace App\Http\Controllers\Admin;

use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdminsRole;
use App\Models\Product;
use App\Models\Brand;
use Session;
use Auth;
use DB;

class BrandController extends Controller
{
    public function index()
    {
        Session::put('page', 'brands');
        $brands = Brand::all();

        // Set Admin/Subadmins Permissions for Brands
        $brandsModuleCount = AdminsRole::where(['subadmin_id'=>Auth::guard('admin')->user()->id,'module'=>'brands'])->count();
        $brandsModule = array();
        if(Auth::guard('admin')->user()->type=="admin"){
            $brandsModule['view_access'] = 1;
            $brandsModule['edit_access'] = 1;
            $brandsModule['full_access'] = 1;
        }else if($brandsModuleCount==0){
            $message = "This feature is restricted for you!";
            return redirect('admin/dashboard')->with('error_message',$message);
        }else{
            $brandsModule = AdminsRole::where(['subadmin_id'=>Auth::guard('admin')->user()->id,'module'=>'brands'])->first()->toArray();
        }

        return view('admin.brands.brands', compact('brands', 'brandsModule'));
    }

    public function create()
    {
        $title = "Add Brand";
        $brand = new Brand;
        return view('admin.brands.add_edit_brand', compact('title', 'brand'));
    }

    public function store(Request $request)
    {
        return $this->saveBrand($request);
    }

    public function edit($id)
    {
        $title = "Edit Brand";
        $brand = Brand::findOrFail($id);
        return view('admin.brands.add_edit_brand', compact('title', 'brand'));
    }

    public function update(Request $request, $id)
    {
        return $this->saveBrand($request, $id);
    }

    public function destroy($id)
    {
        Brand::destroy($id);
        return redirect()->back()->with('success_message', 'Brand deleted successfully!');
    }

    public function updateStatus(Request $request)
    {
        if ($request->ajax()) {
            $status = $request->status === "Active" ? 0 : 1;
            Brand::where('id', $request->brand_id)->update(['status' => $status]);

            return response()->json(['status' => $status, 'brand_id' => $request->brand_id]);
        }
    }

    private function saveBrand(Request $request, $id = null)
    {
        $rules = [
            'brand_name' => 'required',
            'url' => $id ? 'required' : 'required|unique:brands',
        ];

        $customMessages = [
            'brand_name.required' => 'Brand Name is required',
            'url.required' => 'Brand URL is required',
            'url.unique' => 'Unique Brand URL is required',
        ];

        $this->validate($request, $rules, $customMessages);

        $brand = $id ? Brand::findOrFail($id) : new Brand;
        $message = $id ? 'Brand updated successfully!' : 'Brand added successfully!';

        if ($request->hasFile('brand_image')) {
            $brand->brand_image = $this->uploadFile($request->file('brand_image'));
        }

        $brand->brand_name = $request->brand_name;
        $brand->brand_discount = $request->brand_discount ?? 0;
        $brand->description = $request->description;
        $brand->url = $request->url;
        $brand->meta_title = $request->meta_title;
        $brand->meta_description = $request->meta_description;
        $brand->meta_keywords = $request->meta_keywords;
        $brand->status = 1;
        $brand->save();

        $this->updateProductDiscounts($brand->id, $brand->brand_discount);

        return redirect('admin/brands')->with('success_message', $message);
    }

    private function uploadFile($file)
    {
        $manager = new ImageManager(new Driver());
        $image = $manager->read($file);
        $extension = $file->getClientOriginalExtension();
        $imageName = rand(111, 99999) . '.' . $extension;
        $imagePath = 'front/images/brands/' . $imageName;
        $image->save($imagePath);

        return $imageName;
    }

    private function updateProductDiscounts($brandId, $discount)
    {
        Product::where('brand_id', $brandId)->update([
            'discount_type' => $discount ? 'brand' : '',
            'product_discount' => $discount,
            'final_price' => DB::raw('product_price - (product_price * ' . $discount . ' / 100.0)')
        ]);
    }

    public function deleteBrandImage($id){
        // Get Brand Image
        $brandImage = Brand::select('brand_image')->where('id',$id)->first();

        // Get Brand Image Path
        $brand_image_path = 'front/images/brands/';

        // Delete Brand Image from brands folder if exists
        if(file_exists($brand_image_path.$brandImage->brand_image)){
            unlink($brand_image_path.$brandImage->brand_image);
        }

        // Delete Brand Image from brands table
        Brand::where('id',$id)->update(['brand_image'=>'']);

        return redirect()->back()->with('success_message','Brand image deleted successfully!');
    }

    public function deleteBrandLogo($id){
        // Get Brand Logo
        $brandLogo = Brand::select('brand_logo')->where('id',$id)->first();

        // Get Brand Image Path
        $brand_logo_path = 'front/images/brands/';

        // Delete Brand Image from brands folder if exists
        if(file_exists($brand_logo_path.$brandLogo->brand_logo)){
            unlink($brand_logo_path.$brandLogo->brand_logo);
        }

        // Delete Brand Image from brands table
        Brand::where('id',$id)->update(['brand_logo'=>'']);

        return redirect()->back()->with('success_message','Brand logo deleted successfully!');
    }
}
