<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

use File;
class DataFeedFile extends Model{
	
    /*create automatic Data Feed (.txt)  */
     public static function CreeateDataFeedFile(){
		 
		// echo 'ok'; exit;
		 
		 $query = Product::with(['product_image','product_attributes'])->select('products.*','categories.category_name')->join('categories','categories.id','products.category_id')->where('categories.status','1')->where('products.status', '1')->orderBy('products.id');
		// pd($query->get()->toArray());
		
		 $header = "id|title|description|link|image link|condition|availability|price|sale_price|brand|google product category"; 
         $lines[] = $header;

			$query->chunk(1000, function($products) use (&$lines) {
				foreach ($products as $p) {
					if(!empty($p->product_attributes)){
					$title = str_replace(["\r","\n","|"], ['','',' '], $p->product_name);
					$description = trim(str_replace(["\r","\n","|"], ['','',' '], $p->description)); 
					$link = route('product',[$p->product_url,$p->id]); 
					
					$condition = 'new'; 
					/*if(!empty($p->product_attributes)){
					      $availability = 'in stock';
					}else{
						 $availability = 'out of stock';
					} */
					$availability = 'in stock';
					$price = 'INR '.$p->product_price;
					$sale_price = 'INR '.$p->final_price;
					
					$brand  = 'Auraachic - '.$p->category_name;
					$google_product_category  = ' Apparel & Accessories > Clothing';
					 
					  $image_link = '';
					 
					 if(!empty($p['product_image'])){
					 
                        $image_link = 'front/images/products/large/'.$p['product_image']['image'];
					 
					 }
					
					  if(empty($image_link) || !File::exists(public_path($image_link))){
		                      $image_link = '';
                       }else{
						   $image_link = asset($image_link);
					   }
					 
 
					$lines[] = implode('|', [
						$p->id,
						$title,
						$description,
						$link,
						$image_link,
						$condition,
						$availability,
						$price,
						$sale_price,
						$brand,
						$google_product_category,
					]);
				}
				}
			});
			
		$content = implode("\n", $lines) . "\n";  

       $feedPath = public_path('feeds'); 

		// Create folder if not exists
		if (!file_exists($feedPath)) {
			mkdir($feedPath, 0775, true);
		}

		// Full file path inside public folder
		$fullPath = $feedPath . '/auraachic_products_feed_3214141.txt';

		// Save file
		file_put_contents($fullPath, $content);
         // Log::info('FEED FILE GENERATED: '.url($fullPath).' - '.date("Y-m-d H:i:s"));

        // Optionally: rotate old files, gzip, upload via sftp/ftp, etc.
        return true;
			

		 
		 
		 
		 
		 
		 
	 }

   
   
   
  public static function CreeateDataFacebookFeedFile() {
    // Query to get products with their attributes and images
    $query = Product::with(['product_image', 'product_attributes'])
                    ->select('products.*', 'categories.category_name')
                    ->join('categories', 'categories.id', '=', 'products.category_id')
                    ->where('categories.status', '1')
                    ->where('products.status', '1')
                    ->orderBy('products.id');
    
    // CSV file headers
    $header = [
        'id', 
        'title', 
        'description', 
        'availability', 
        'condition', 
		'price', 
        'sale_price', 
        'link', 
        'image_link',        
        'brand', 
        'google_product_category',
        'fb_product_category'
    ];

    // Create a file path for the feed
    $feedPath = public_path('feeds');
    if (!file_exists($feedPath)) {
        mkdir($feedPath, 0775, true);
    }

    // Full file path for the CSV
    $fullPath = $feedPath . '/auraachic_products_facebook_feed_356636.csv';

    // Open the file for writing
    $file = fopen($fullPath, 'w');
    
    // Write the header to the CSV
    fputcsv($file, $header);

    // Process products in chunks of 1000
    $query->chunk(1000, function($products) use ($file) {
        foreach ($products as $p) {
            if (!empty($p->product_attributes)) {
                $title = str_replace(["\r", "\n", "|"], ['', '', ' '], $p->product_name);
                $description = trim(str_replace(["\r", "\n", "|"], ['', '', ' '], $p->description));
                $link = route('product', [$p->product_url,$p->id]);
                $condition = 'new'; 
                $availability = 'in stock';  
                $price = 'INR ' . $p->product_price;
                $sale_price = 'INR ' . $p->final_price;
                $brand  = 'Auraachic - '.$p->category_name;
                $google_product_category = 'Apparel & Accessories > Clothing';
                $fb_product_category = 'Clothing & accessories > Clothing';
                $image_link = '';
                if (!empty($p->productimage) && File::exists(public_path('images/products/large/' . $p->productimage->image))) {
                    $image_link = asset('images/products/large/' . $p->productimage->image);
                }
                $row = [
                    $p->id,
                    $title,
                    $description,
					$availability,
					$condition,
                    $price,
                    $sale_price,
					$link,
					$image_link,
                    $brand,
                    $google_product_category,
                    $fb_product_category,
                ];

               
                fputcsv($file, $row);
            }
        }
    });
    fclose($file);

   
   // Log::info('FACEBOOK FEED FILE GENERATED: ' . url($fullPath) . ' - ' . date("Y-m-d H:i:s"));

    return true;
}

   
   
   
	
}
