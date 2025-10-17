<?php

namespace App\Http\Controllers\Admin;

use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OrdersProduct;
use App\Models\Order;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductsCategory;
use App\Models\ProductsAttribute;
use App\Models\User;
use App\Models\Subscriber;
use App\Models\Contact;
use App\Models\BusinessContact;
use Session;
use DB;

class ReportController extends Controller
{
    public function exportorders(Request $request){
        Session::put('page','orders');
        if($request->isMethod('post')){
            $data = $request->all();
            $headers = array(
                'Content-Type'        => 'text/csv',
                'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
                'Content-Disposition' => 'attachment; filename=orders.csv',
                'Expires'             => '0',
                'Pragma'              => 'public',
            );
            $response = new StreamedResponse(function() use($data){
                // Open output stream
                $handle = fopen('php://output', 'w');
                // Add CSV headers
                fputcsv($handle, ["OrderId","UserId","Email","Name","Address","City","State","Postcode","Mobile","Shipping Name","Shipping Address","Shipping City","Shipping State","Shipping Postcode","Shipping Mobile",'Coupon Code','Order Status','Payment Method','Order Date','Product Name','Code','SKU','Product Qty','MRP','Product Discount (%)','Product Discount Amount','Product Price','Product Subtotal','Coupon Discount','Credit Amount','Taxes','Grand Total','Brand',"AWB Number","Courier Partner"]);
                    if($data['type'] =="Orders"){
                        $exportOrders  = OrdersProduct::join('orders','orders.id','=','orders_products.order_id')->join('orders_addresses','orders_addresses.order_id','=','orders.id')->join('users','users.id','=','orders.user_id')->join('products','products.id','=','orders_products.product_id')->join('brands','brands.id','=','products.brand_id')->select('orders_products.order_id','orders.awb_number','orders.delivery_method','orders.user_id','orders_products.product_id','orders_addresses.shipping_name','orders_addresses.shipping_address','orders_addresses.shipping_city','orders_addresses.shipping_state','orders_addresses.shipping_postcode','orders_addresses.shipping_mobile','orders.user_id','orders.comments','orders.shipping_charges','orders.coupon_code','orders.coupon_discount','orders.credit','orders.order_status','orders.payment_method','orders.created_at','orders.sub_total as order_subtotal','orders.grand_total','orders_products.product_name','orders_products.product_code','orders_products.product_sku','orders_products.product_qty','orders_products.product_price','orders_products.discount as prodiscount','orders_products.product_discount_amount','orders_products.mrp','orders_products.discount_type','orders_products.sub_total as pro_subtotal','users.email','users.name as user_name','users.mobile as user_mobile','users.city as user_city','users.state as user_state','users.pincode as user_pincode','users.address as user_address','orders.taxes','brands.brand_name')->orderBy('orders_products.id','DESC');
                    if(isset($data['status']) && !empty($data['status'])){
                        $exportOrders = $exportOrders->wherein('orders.order_status',$data['status']);
                    }
                    if(!empty($data['from_date'])){
                        $exportOrders = $exportOrders->whereDate('orders.created_at','>=',$data['from_date']);
                    }
                    if(!empty($data['to_date'])){
                        $exportOrders = $exportOrders->whereDate('orders.created_at','<=',$data['to_date']);
                    }
                    $exportOrders = $exportOrders->chunk(500, function($orderPro) use($handle) {
                        foreach ($orderPro as $order) {
                            $product_qty = $order->product_qty;    
                            $disRate = $order->discount_rate;
                            $lineDis = $order->line_discount;
                            $unitPrice = $order->unit_price;
                            $LineAmount = $order->line_amount;
                            // Add a new row with data
                            fputcsv($handle, [
                                $order->order_id,
                                $order->user_id,
                                $order->email,
                                $order->user_name,
                                substr($order->user_address, 0, 100),
                                $order->user_city,
                                $order->user_state,
                                $order->user_pincode,
                                $order->shipping_mobile,
                                $order->shipping_name,
                                substr($order->shipping_address, 0, 100),
                                $order->shipping_city,
                                $order->shipping_state,
                                $order->shipping_postcode,
                                $order->shipping_mobile,
                                $order->coupon_code,
                                $order->order_status,
                                $order->payment_method,
                                date('d/m/Y h:ia',strtotime($order->created_at)),
                                $order->product_name,
                                $order->product_code,
                                $order->product_sku,
                                $product_qty,
                                $order->mrp,
                                $order->prodiscount,
                                $order->product_discount_amount,
                                $order->product_price,
                                $order->pro_subtotal,
                                $order->coupon_discount,
                                $order->credit,
                                $order->taxes,
                                $order->grand_total,
                                $order->brand_name,
                                $order->awb_number,
                                $order->delivery_method,
                            ]);
                        }
                    });
                    // Close the output stream
                    fclose($handle);
                }else{}
                
            }, 200, $headers);
            return $response->send();
        }else{
            $orderstatuses =  DB::table('orders_statuses')->get();
            $orderstatuses = json_decode(json_encode($orderstatuses),true); 
            $title = "Export Orders";
            return view('admin.orders.export_orders')->with(compact('title','orderstatuses'));
        }
    }

    public function exportusers(Request $request){
        Session::put('page','users');
            $headers = array(
                'Content-Type'        => 'text/csv',
                'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
                'Content-Disposition' => 'attachment; filename=users.csv',
                'Expires'             => '0',
                'Pragma'              => 'public',
            );
            $response = new StreamedResponse(function() {
                // Open output stream
                $handle = fopen('php://output', 'w');
                // Add CSV headers
                fputcsv($handle, ["Id","Name","First Name","Last Name","Address","City","State","Country","Pincode","Mobile","Email","Status","Registered on"]);
                        $exportUsers  = User::select('id','name','first_name','last_name','address','city','state','country','pincode','mobile','email','status','created_at')->orderBy('users.id','DESC');
                        $exportUsers = $exportUsers->chunk(500, function($users) use($handle) {
                        foreach ($users as $user) {
                            // Add a new row with data
                            fputcsv($handle, [
                                $user->id,
                                $user->name,
                                $user->first_name,
                                $user->last_name,
                                $user->address,
                                $user->city,
                                $user->state,
                                $user->country,
                                $user->pincode,
                                $user->mobile,
                                $user->email,
                                $user->status,
                                $user->created_at
                            ]);
                        }
                    });
                    // Close the output stream
                    fclose($handle);
            }, 200, $headers);
            return $response->send();
    }

    public function exportSubscribers(Request $request){
        Session::put('page','subscribers');
            $headers = array(
                'Content-Type'        => 'text/csv',
                'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
                'Content-Disposition' => 'attachment; filename=subscribers.csv',
                'Expires'             => '0',
                'Pragma'              => 'public',
            );
            $response = new StreamedResponse(function() {
                // Open output stream
                $handle = fopen('php://output', 'w');
                // Add CSV headers
                fputcsv($handle, ["Id","Email","Status","Subscribed on"]);
                        $exportSubscriber  = Subscriber::select('id','email','status','created_at')->orderBy('subscribers.id','DESC');
                        $exportSubscriber = $exportSubscriber->chunk(500, function($subscribers) use($handle) {
                        foreach ($subscribers as $subscriber) {
                            // Add a new row with data
                            fputcsv($handle, [
                                $subscriber->id,
                                $subscriber->email,
                                $subscriber->status,
                                $subscriber->created_at
                            ]);
                        }
                    });
                    // Close the output stream
                    fclose($handle);
            }, 200, $headers);
            return $response->send();
    }

    public function exportEnquiries(Request $request){
        Session::put('page','enquiries');
            $headers = array(
                'Content-Type'        => 'text/csv',
                'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
                'Content-Disposition' => 'attachment; filename=enquiries.csv',
                'Expires'             => '0',
                'Pragma'              => 'public',
            );
            $response = new StreamedResponse(function() {
                // Open output stream
                $handle = fopen('php://output', 'w');
                // Add CSV headers
                fputcsv($handle, ["Id","Name","Email","Mobile","Message","Enquired on"]);
                        $exportEnquiries  = Contact::select('id','name','email','mobile','message','created_at')->orderBy('contacts.id','DESC');
                        $exportEnquiries = $exportEnquiries->chunk(500, function($enquiries) use($handle) {
                        foreach ($enquiries as $enquiry) {
                            // Add a new row with data
                            fputcsv($handle, [
                                $enquiry->id,
                                $enquiry->name,
                                $enquiry->email,
                                $enquiry->mobile,
                                $enquiry->message,
                                $enquiry->created_at
                            ]);
                        }
                    });
                    // Close the output stream
                    fclose($handle);
            }, 200, $headers);
            return $response->send();
    }

    public function exportBusinessEnquiries(Request $request){
        Session::put('page','business-enquiries');
        
        $headers = array(
            'Content-Type'        => 'text/csv',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Content-Disposition' => 'attachment; filename=business-enquiries.csv',
            'Expires'             => '0',
            'Pragma'              => 'public',
        );

        $response = new StreamedResponse(function() {
            // Open output stream
            $handle = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv($handle, [
                "Id", "Name", "Company Name", "City", "State", "Email", "Mobile", "Interested Brand", "Business Type", "Estimated Order Quantity", "Message", "Enquired on"
            ]);

            // Fetch data in chunks
            $exportBusinessEnquiries = BusinessContact::select(
                'id', 'name', 'company_name', 'city', 'state', 'email', 'mobile', 
                'estimated_order_quantity', 'business_type', 'message', 'created_at'
            )->orderBy('id', 'DESC');

            $exportBusinessEnquiries->chunk(500, function($enquiries) use($handle) {
                foreach ($enquiries as $enquiry) {
                    // Add a new row with data
                    fputcsv($handle, [
                        $enquiry->id,
                        $enquiry->name,
                        $enquiry->company_name,
                        $enquiry->city,
                        $enquiry->state,
                        $enquiry->email,
                        $enquiry->mobile,
                        $enquiry->business_type ?? 'N/A', // Handle null value
                        $enquiry->estimated_order_quantity ?? 'N/A', // Handle null value
                        $enquiry->message,
                        $enquiry->created_at->format('F j, Y, g:i a'),
                    ]);
                }
            });

            // Close the output stream
            fclose($handle);
        }, 200, $headers);

        return $response->send();
    }

    public function exportbrands(Request $request){
        Session::put('page','brands');
            $headers = array(
                'Content-Type'        => 'text/csv',
                'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
                'Content-Disposition' => 'attachment; filename=brands.csv',
                'Expires'             => '0',
                'Pragma'              => 'public',
            );
            $response = new StreamedResponse(function() {
                // Open output stream
                $handle = fopen('php://output', 'w');
                // Add CSV headers
                fputcsv($handle, ["Id","Brand Name","Status"]);
                        $exportBrands  = Brand::select('id','brand_name','status')->orderBy('brands.id','DESC');
                        $exportBrands = $exportBrands->chunk(500, function($brands) use($handle) {
                        foreach ($brands as $brand) {
                            // Add a new row with data
                            fputcsv($handle, [
                                $brand->id,
                                $brand->brand_name,
                                $brand->status
                            ]);
                        }
                    });
                    // Close the output stream
                    fclose($handle);
            }, 200, $headers);
            return $response->send();
    }

    public function exportcategories(Request $request){
        Session::put('page','categories');
            $headers = array(
                'Content-Type'        => 'text/csv',
                'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
                'Content-Disposition' => 'attachment; filename=categories.csv',
                'Expires'             => '0',
                'Pragma'              => 'public',
            );
            $response = new StreamedResponse(function() {
                // Open output stream
                $handle = fopen('php://output', 'w');
                // Add CSV headers
                fputcsv($handle, ["Id","Parent Id","Category Name","Category Discount","Category URL","Meta Title","Meta Description","Meta Keywords","Sort","Status","Menu Status"]);
                        $exportCategories  = Category::select('id','parent_id','category_name','category_discount','url','meta_title','meta_description','meta_keywords','sort','status','menu_status')->orderBy('categories.id','DESC');
                        $exportCategories = $exportCategories->chunk(500, function($categories) use($handle) {
                        foreach ($categories as $category) {
                            // Add a new row with data
                            fputcsv($handle, [
                                $category->id,
                                $category->parent_id,
                                $category->category_name,
                                $category->category_discount,
                                $category->url,
                                $category->meta_title,
                                $category->meta_description,
                                $category->meta_keywords,
                                $category->sort,
                                $category->status,
                                $category->menu_status
                            ]);
                        }
                    });
                    // Close the output stream
                    fclose($handle);
            }, 200, $headers);
            return $response->send();
    }

    public function exportproducts  (Request $request){

         $conditions = array();
            $data = array();
            $headers = array(
            'Content-Type'        => 'text/csv',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Content-Disposition' => 'attachment; filename=Products.csv',
            'Expires'             => '0',
            'Pragma'              => 'public',
            );
            
            $response = new StreamedResponse(function(){
            // Open output stream
            $handle = fopen('php://output', 'w');
             $product_columns = ["product_id","category_id","product_name","product_color","family_color","product_code","group_code","product_weight","product_sort","product_price","product_discount","discount_type","product_gst","final_price","description","key_features","wash_care","search_keywords","sleeve","fabric","neck","fit","occasion","status","sku","size","attr_price","stock","sort","status","is_featured","meta_title","meta_description","meta_keywords","other_cat_ids"];
            
            fputcsv($handle,$product_columns);
                $querys = Product::select('products.*','products_attributes.sku','products_attributes.size','products_attributes.price','products_attributes.status as attr_status','products_attributes.stock','products_attributes.sort as attr_sort')->join('products_attributes','products_attributes.product_id','=','products.id')->orderby('products.id','desc');
                $querys = $querys->chunk(1000, function($rows) use($handle,$product_columns) {
                $no = '1';
                foreach($rows as $row){
                   
                  $other_cat_ids = '';
                  $products_categories = ProductsCategory::where('product_id',$row['id'])->get();
                  foreach($products_categories as $cat){
                      if(!empty($other_cat_ids)){
                          $other_cat_ids .= ','.$cat['category_id'];
                      }else{
                          $other_cat_ids .= $cat['category_id'];
                      }
                      
                    }
                    
                    fputcsv($handle,[
                        $row['id'],
                        $row['category_id'],
                        $row['product_name'],
                        $row['product_color'],
                        $row['family_color'],
                        $row['product_code'],
                        $row['group_code'],
                        $row['product_weight'],
                        $row['product_sort'],
                        $row['product_price'],
                        $row['product_discount'],
                        $row['discount_type'],
                        $row['product_gst'],
                        $row['final_price'],
                        $row['description'],
                        $row['key_features'],
                        $row['wash_care'],
                        $row['search_keywords'],
                        $row['sleeve'],
                        $row['fabric'],
                        $row['neck'],
                        $row['fit'],
                        $row['occasion'],
                        $row['status'],
                        $row['sku'],
                        $row['size'],
                        $row['price'],
                        $row['stock'],
                        $row['attr_sort'],
                        $row['attr_status'],
                        $row['is_featured'],
                        $row['meta_title'],
                        $row['meta_description'],
                        $row['meta_keywords'],
                        $other_cat_ids,
                        ]);
                    
                    
                     $no++;
                }
            });
            fclose($handle);
        },200, $headers);
        return $response->send();
        
    }
}
