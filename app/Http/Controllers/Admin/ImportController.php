<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Session;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Redirect;
use App\Models\ProductsAttribute;
use App\Models\ProductsImage;
use App\Models\User;
use App\Models\Rating;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use Illuminate\Support\Facades\Schema;

class ImportController extends Controller
{
    //
    public function importData(Request $request){
    	Session::put('active','imports');
    	if($request->isMethod('post')){
    		$data = $request->all();
    		$filename = $_FILES['file']['name'];
		    $ext = pathinfo($filename, PATHINFO_EXTENSION);
		    if($ext == 'xls'){

			}else{
				return redirect::to('/admin/import-data')->with('flash_message_error','Please import xls file format only');
			}
    		$fileResp = $this->uploadFile($request,$data['type']);
    		if($data['type']=="products"){
    			if($fileResp['status']){
	    			$response = $this->validateProductFile($fileResp);
	    			if($response['status']){
	    				return redirect::to('/admin/import-data?&type=products&filename='.$fileResp['filename'])->with('flash_message_success','File has been validated successfully. Please click below button to import');
	    			}else{
	    				return redirect()->back()->with('flash_message_error',$response['message']);
	    			}
	    		}
    		}else if($data['type']=="reviews"){
    			if($fileResp['status']){
	    			$response = $this->ValidateReviewFile($fileResp);
	    			//echo "<pre>"; print_r($response); die;
	    			if($response['status']){
	    				return redirect::to('/admin/import-data?&type=reviews&filename='.$fileResp['filename'])->with('flash_message_success','File has been validated successfully. Please click below button to import');
	    			}else{
	    				return redirect()->back()->with('flash_message_error',$response['message']);
	    			}
	    		}
    		}
    	}
    	$title="Import Data";
    	return view('admin.imports.import_data')->with(compact('title'));
    }

    public function uploadFile($request,$type){
		if($request->hasFile('file')){
			if ($request->file('file')->isValid()) {
			    $file = $request->file('file');
			    $destination = public_path('imports/'.$type);
			    $ext= $file->getClientOriginalExtension();
			    $mainFilename = $type."-".rand(12,5554).time().date('i').".".$ext;
			    $file->move($destination, $mainFilename);
			    if($type=="products"){
			    	$filepath = 'imports/products/'.$mainFilename;
			    }else if($type=="reviews"){
			    	$filepath = 'imports/reviews/'.$mainFilename;
			    }else{

			    }
			    return array('status'=>true,'filename'=>$mainFilename,'filepath'=>public_path($filepath));
			}
		}
	}

	public function validateProductFile($fileResp){
		$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($fileResp['filepath']);
    	foreach ($spreadsheet->getWorksheetIterator() as $worksheet) {
		    $worksheets[$worksheet->getTitle()] = $worksheet->toArray();
		}
		$sheetsdata = array_values($worksheets);
		if(isset($sheetsdata[0]) && !empty($sheetsdata[0])){
			$sheetsdata = $sheetsdata[0];
			if(isset($sheetsdata[0]) && !empty($sheetsdata[0])){
				$excelCols = $sheetsdata[0];
				$excelColumns = array();
				foreach($excelCols as $ckey=> $col){
					if(empty($col)){
						$previousKey = $this->getPrevKey($ckey,$excelCols);
						$errorcolumn =  $excelCols[$previousKey];
						unlink($fileResp['filepath']);
						if(empty($errorcolumn)){
							$message ="Please remove empty columns from sheet and try again";
						}else{
							$message = 'Please remove empty column after '.$errorcolumn." column and try again.";
						}
	            		return array('status'=>false,'message'=>$message);
					}else{
						$excelColumns[] =  strtolower($col);
					}
				}
				$headings = array_shift($sheetsdata);
				array_walk(
				    $sheetsdata,
				    function (&$row) use ($headings) {
				        $row = array_combine($headings, $row);
				    }
				);
				$table = "products";
	    		$columns = DB::getSchemaBuilder()->getColumnListing($table);
	    		$columns = array_diff( $columns, ['id','created_at','updated_at','product_discount_amount','final_price','discount_type','product_weight','more_detail_image','stock','authentic_code','is_featured','no_of_sales']);
	    		$otherColumns = array('other_cat_ids','attr_sku_0','attr_size_0','attr_stock_0','attr_price_0','image_0');
	    		$allcolumns = array_merge($columns,$otherColumns);
	    		$response =true;
	    		$columnarr = array();
				foreach($allcolumns as $key => $column){
	            	if(!in_array($column,$excelColumns)){
	            		$response=false;
	            		$columnarr[] = $column;
	            	}
	            }
	            if(!$response){
	            	$columnnames = implode(',',$columnarr);
	            	unlink($fileResp['filepath']);
	            	return array('status'=>false,'message'=> 'Your xls file having unmatched Columns to our database...Missing columns are:- '.$columnnames);
	            }else{
	            	//Check for Data Sets
	            	$countNoOfRows = count($sheetsdata);
	            	if($countNoOfRows>300){
	            		unlink($fileResp['filepath']);
	            		return array('status'=>false,'message'=> 'Please import maximum of 300 products at a time');
	            	}else{
	            		$lineNumber = 1;
	            		foreach($sheetsdata as $pkey => $prodata){
	            			$lineNumber = $lineNumber +1;
	            			$resp = $this->checkProductRowValid($prodata,$lineNumber);
	            			if(!$resp['status']){
	            				unlink($fileResp['filepath']);
	            				return array('status'=>false,'message'=> $resp['message']);
	            			}else{
	            				$products[] = $resp['products'];
	            			}
	            		}
	            		// Final Check Points
	            		$proCodes = array_column($products, 'product_code');
						if($proCodes != array_unique($proCodes)){
							unlink($fileResp['filepath']);
							return array('status'=>false,'message'=> 'There are duplicates product_code in sheet. Please fix and upload it again');
						}
						return array('status'=>true);
	            	}
	            }
			}else{
				unlink($fileResp['filepath']);
				return array('status'=>false,'message'=>'No data for import');
			}
		}else{
			unlink($fileResp['filepath']);
			return array('status'=>false,'message'=>'No data for import');
		}
	}

	public function ValidateReviewFile($fileResp){
		//echo "test"; die;
		$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($fileResp['filepath']);
    	foreach ($spreadsheet->getWorksheetIterator() as $worksheet) {
		    $worksheets[$worksheet->getTitle()] = $worksheet->toArray();
		}
		$sheetsdata = array_values($worksheets);
		if(isset($sheetsdata[0]) && !empty($sheetsdata[0])){
			$sheetsdata = $sheetsdata[0];
			if(isset($sheetsdata[0]) && !empty($sheetsdata[0])){
				$excelCols = $sheetsdata[0];
				$excelColumns = array();
				foreach($excelCols as $ckey=> $col){
					if(empty($col)){
						$previousKey = $this->getPrevKey($ckey,$excelCols);
						$errorcolumn =  $excelCols[$previousKey];
						unlink($fileResp['filepath']);
						if(empty($errorcolumn)){
							$message ="Please remove empty columns from sheet and try again";
						}else{
							$message = 'Please remove empty column after '.$errorcolumn." column and try again.";
						}
	            		return array('status'=>false,'message'=>$message);
					}else{
						$excelColumns[] =  strtolower($col);
					}
				}
				$headings = array_shift($sheetsdata);
				array_walk(
				    $sheetsdata,
				    function (&$row) use ($headings) {
				        $row = array_combine($headings, $row);
				    }
				);
				
	    		$allcolumns = array('product_id','user_id','name','email','review','rating','status');
	    		$response =true;
	    		$columnarr = array();
				foreach($allcolumns as $key => $column){
	            	if(!in_array($column,$excelColumns)){
	            		$response=false;
	            		$columnarr[] = $column;
	            	}
	            }
	            if(!$response){
	            	$columnnames = implode(',',$columnarr);
	            	unlink($fileResp['filepath']);
	            	return array('status'=>false,'message'=> 'Your xls file having unmatched Columns to our database...Missing columns are:- '.$columnnames);
	            }else{
	            	//Check for Data Sets
	            	$countNoOfRows = count($sheetsdata);
	            	if($countNoOfRows>2000){
	            		unlink($fileResp['filepath']);
	            		return array('status'=>false,'message'=> 'Please import maximum of 2000 reviews at a time');
	            	}else{
	            		$all_users = User::pluck('id')->toArray();
	            		$lineNumber = 1;
	            		foreach($sheetsdata as $pkey => $userdata){
	            			$lineNumber = $lineNumber +1;
	            			$resp = $this->checkReviewRowValid($userdata,$lineNumber,$all_users);
	            			//echo "<pre>"; print_r($resp); die;
	            			if(!$resp['status']){
	            				unlink($fileResp['filepath']);
	            				return array('status'=>false,'message'=> $resp['message']);
	            			}
	            		}
						return array('status'=>true);
	            	}
	            }
			}else{
				unlink($fileResp['filepath']);
				return array('status'=>false,'message'=>'No data for import');
			}
		}else{
			unlink($fileResp['filepath']);
			return array('status'=>false,'message'=>'No data for import');
		}
	}

	public function checkReviewRowValid($prodata,$lineNumber){
		foreach($prodata as $proKey=> $prod){
			$proRow[trim(strtolower($proKey))] = $prod;
		}
		//Check Brand is Valid
		$columnChecks = array(
					'product_id'=>'product_id',
					'user_id'=>'user_id',
					'name'=>'name',
					'email' => 'email',
					'review' => 'review',
					'rating' => 'rating',
					'status' => 'status',
				);
		foreach($columnChecks as $ckey => $colcheck){
			$resp = checkReviewFile($proRow,$ckey,$proRow[$colcheck]);
			if(!$resp['status']){
				return array('status'=>false,'message'=> $resp['message']. ' at line number '.$lineNumber);
			}
		}
		return array('status'=>true,'products'=>$proRow);
	}

	public function checkProductRowValid($prodata,$lineNumber){
		foreach($prodata as $proKey=> $prod){
			$proRow[trim(strtolower($proKey))] = $prod;
		}
		//Check Brand is Valid
		$columnChecks = array(
					'brand'=>'brand_id',
					'category'=>'category_id',
					'product_code'=>'product_code',
					'product_price' => 'product_price',
					'product_discount' => 'product_discount',
					'main_image' => 'main_image',
					'other_cat_ids' => 'other_cat_ids',
					'is_new' => 'is_new',
					'is_featured' => 'is_featured',
					'image_0' => 'image_0',
					'attr_sku_0' => 'attr_sku_0',
					'status' => 'status',
				);
		foreach($columnChecks as $ckey => $colcheck){
			$resp = checkProFile($proRow,$ckey,$proRow[$colcheck]);
			if(!$resp['status']){
				return array('status'=>false,'message'=> $resp['message']. ' at line number '.$lineNumber);
			}
		}
		return array('status'=>true,'products'=>$proRow);
	}

	public function getPrevKey($key, $hash = array()) {
	    $keys = array_keys($hash);
	    $found_index = array_search($key, $keys);
	    if ($found_index === false || $found_index === 0)
	        return false;
	    return $keys[$found_index-1];
	}

	public function importFileData(Request $request){
		if($request->isMethod('post')){
			$data = $request->all();
			if($data['type']=="products"){
				$filename = public_path('imports/products/'.$data['filename']);
				$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filename);
		    	foreach ($spreadsheet->getWorksheetIterator() as $worksheet) {
				    $worksheets[$worksheet->getTitle()] = $worksheet->toArray();
				}
				$sheetsdata = array_values($worksheets);
				if(isset($sheetsdata[0]) && !empty($sheetsdata[0])){
					$sheetsdata = $sheetsdata[0];
					if(isset($sheetsdata[0]) && !empty($sheetsdata[0])){
						$headings = array_shift($sheetsdata);
						array_walk(
						    $sheetsdata,
						    function (&$row) use ($headings) {
						        $row = array_combine($headings, $row);
						    }
						);
						foreach ($sheetsdata as $key => $proRow) {
							foreach($proRow as $proKey=> $prod){
								$productinfo[trim(strtolower($proKey))] = $prod;
							}
							//Create Product
							$product = new Product;
							$product->brand_id = $productinfo['brand_id'];
							$product->category_id = $productinfo['category_id'];
							$product->product_name = $productinfo['product_name'];
							$product->product_url = $this->generate_seo_url($productinfo['product_name']);
							$product->product_code = $productinfo['product_code'];
							$product->product_color = $productinfo['product_color'];
							$product->product_video = $productinfo['product_video'];
							$product->video_thumbnail = $productinfo['video_thumbnail'];
							$product->family_color = $productinfo['family_color'];
							$product->main_image = $productinfo['main_image'];
							/*$product->gender = (!empty($productinfo['gender'])?$productinfo['gender']:'');*/
							$product->fabric = (!empty($productinfo['fabric'])?$productinfo['fabric']:'');
							/*$product->pattern = (!empty($productinfo['pattern'])?$productinfo['pattern']:'');*/
							$product->sleeve = (!empty($productinfo['sleeve'])?$productinfo['sleeve']:'');
							$product->fit = (!empty($productinfo['fit'])?$productinfo['fit']:'');
							$product->occasion = (!empty($productinfo['occasion'])?$productinfo['occasion']:'');
							/*$product->length = (!empty($productinfo['length'])?$productinfo['length']:'');*/
							/*$product->style = (!empty($productinfo['style'])?$productinfo['style']:'');*/
							$product->neck = (!empty($productinfo['neck'])?$productinfo['neck']:'');
							$product->product_sort = (!empty($productinfo['product_sort'])?$productinfo['product_sort']:'');
							$product->product_weight = (!empty($productinfo['product_weight'])?$productinfo['product_weight']:'');
							$product->description = (!empty($productinfo['description'])?$productinfo['description']:'');
							$product->key_features = (!empty($productinfo['key_features'])?$productinfo['key_features']:'');
							$product->wash_care = (!empty($productinfo['wash_care'])?$productinfo['wash_care']:'');
							$product->search_keywords = (!empty($productinfo['search_keywords'])?$productinfo['search_keywords']:'');
							/*$product->units = (!empty($productinfo['units'])?$productinfo['units']:'');*/
							/*$product->product_for = (!empty($productinfo['product_for'])?$productinfo['product_for']:'');*/
							$product->group_code = (!empty($productinfo['group_code'])?$productinfo['group_code']:'');
							$product->is_featured = $productinfo['is_featured'];
							$product->is_new = $productinfo['is_new'];
							$product->status = $productinfo['status'];
							$product->product_price = $productinfo['product_price'];
							$product->group_code = (!empty($productinfo['group_code'])?$productinfo['group_code']:'');
							$product->product_gst = (!empty($productinfo['product_gst'])?$productinfo['product_gst']:'');
							/*$product->meta_title = (!empty($productinfo['meta_title'])?$productinfo['meta_title']:'');*/
							/*$product->meta_description = (!empty($productinfo['meta_description'])?$productinfo['meta_description']:'');*/
							/*$product->meta_keywords = (!empty($productinfo['meta_keywords'])?$productinfo['meta_keywords']:'');*/
							if($productinfo['product_discount'] >0){
								$product->discount_type = "product";
								$product->product_discount = $productinfo['product_discount'];
								$product->product_discount_amount = $productinfo['product_price'] * $productinfo['product_discount']/100;
								$product->final_price = $productinfo['product_price'] - ($productinfo['product_price'] * $productinfo['product_discount'])/100;
							}else{
								$product->product_discount = 0;
								$product->product_discount_amount = 0;
								$product->final_price =$productinfo['product_price'];
							}
							$product->save();
							$cats = explode(',',$productinfo['other_cat_ids']);
							$product->products_categories()->attach($cats);
							$sort = 1;
							for($img = 0; $img <=9; $img ++){
    							$imgKey = "image_".$img;
    							if(isset($productinfo[$imgKey]) && !empty($productinfo[$imgKey])){
    								$productImage =  new ProductsImage;
    								$productImage->product_id =  $product->id;
    								$productImage->image =  $productinfo[$imgKey];
    								$productImage->image_sort = $sort;
    								$productImage->status = 1;
    								$productImage->save();
    								$sort ++;
    							}
    						}
    						//Adding Product Attributes
    						$attrSort = 1;$totalStock=0;
    						for($attr = 0; $attr <=9; $attr ++){
    							if(isset($productinfo['attr_sku_'.$attr]) && isset($productinfo['attr_size_'.$attr]) && !empty($productinfo['attr_sku_'.$attr]) &&   !empty($productinfo['attr_size_'.$attr])){
    								$totalStock += $productinfo['attr_stock_'.$attr];
    								$proAttr =  new ProductsAttribute;
    								$proAttr->product_id  = $product->id;
    								$proAttr->sku  = $productinfo['attr_sku_'.$attr];
    								if(isset($productinfo['b2b_attr_sku_'.$attr])&&!empty($productinfo['b2b_attr_sku_'.$attr])){
    									$proAttr->b2b_sku  = $productinfo['b2b_attr_sku_'.$attr];	
    								}
    								$proAttr->size  = $productinfo['attr_size_'.$attr];
    								$proAttr->stock  = $productinfo['attr_stock_'.$attr];
    								if(isset($productinfo['b2b_attr_stock_'.$attr])&&!empty($productinfo['b2b_attr_stock_'.$attr])){
	    								$proAttr->b2b_stock  = $productinfo['b2b_attr_stock_'.$attr];
	    							}
    								$proAttr->price  = $productinfo['attr_price_'.$attr];
    								if(isset($productinfo['b2b_attr_price_'.$attr])&&!empty($productinfo['b2b_attr_price_'.$attr])){
    									$proAttr->b2b_price  = $productinfo['b2b_attr_price_'.$attr];
    								}
    								$proAttr->sort = $attrSort;
    								$proAttr->status  = 1;
    								$proAttr->save();
    								$attrSort++;
    							}
    						}
    						Product::where('id',$product->id)->update(['stock'=> $totalStock]);
						}
						return redirect::to('/admin/import-data')->with('flash_message_success','Product has been imported successfully');
					}
				}else{
					return redirect::to('/admin/import-data')->with('flash_message_error','Import failed. Please try after sometime');
				}
			}

			if($data['type']=="reviews"){
				$filename = public_path('imports/reviews/'.$data['filename']);
				$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filename);
		    	foreach ($spreadsheet->getWorksheetIterator() as $worksheet) {
				    $worksheets[$worksheet->getTitle()] = $worksheet->toArray();
				}
				$sheetsdata = array_values($worksheets);
				if(isset($sheetsdata[0]) && !empty($sheetsdata[0])){
					$sheetsdata = $sheetsdata[0];
					if(isset($sheetsdata[0]) && !empty($sheetsdata[0])){
						$headings = array_shift($sheetsdata);
						array_walk(
						    $sheetsdata,
						    function (&$row) use ($headings) {
						        $row = array_combine($headings, $row);
						    }
						);
						foreach ($sheetsdata as $key => $proRow) {
							foreach($proRow as $proKey=> $prod){
								$productinfo[trim(strtolower($proKey))] = $prod;
							}
							//Create Review
							$review = new Rating;
							$review->product_id = $productinfo['product_id'];
							$review->user_id = $productinfo['user_id'];
							$review->name = $productinfo['name'];
							$review->email = $productinfo['email'];
							$review->review = $productinfo['review'];
							$review->rating = $productinfo['rating'];
							$review->status = $productinfo['status'];
							$review->save();
						}
						return redirect::to('/admin/import-data')->with('flash_message_success','Reviews has been imported successfully');
					}
				}else{
					return redirect::to('/admin/import-data')->with('flash_message_error','Import failed. Please try after sometime');
				}
			}
		}
	}

	public function ValidateUpdateProductFile($fileResp,$type){
		$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($fileResp['filepath']);
    	foreach ($spreadsheet->getWorksheetIterator() as $worksheet) {
		    $worksheets[$worksheet->getTitle()] = $worksheet->toArray();
		}
		$sheetsdata = array_values($worksheets);
		if(isset($sheetsdata[0]) && !empty($sheetsdata[0])){
			$sheetsdata = $sheetsdata[0];
			if(isset($sheetsdata[0]) && !empty($sheetsdata[0])){
				$excelCols = $sheetsdata[0];
				$excelColumns = array();
				foreach($excelCols as $ckey=> $col){
					if(empty($col)){
						$previousKey = $this->getPrevKey($ckey,$excelCols);
						$errorcolumn =  $excelCols[$previousKey];
						unlink($fileResp['filepath']);
						if(empty($errorcolumn)){
							$message ="Please remove empty columns from sheet and try again";
						}else{
							$message = 'Please remove empty column after '.$errorcolumn." column and try again.";
						}
	            		return array('status'=>false,'message'=>$message);
					}else{
						$excelColumns[] =  strtolower($col);
					}
				}
				$headings = array_shift($sheetsdata);
				array_walk(
				    $sheetsdata,
				    function (&$row) use ($headings) {
				        $row = array_combine($headings, $row);
				    }
				);
				$importType = explode('-',$type);
				$allcolumns = array('id',$importType[1]);
				$response =true;
				$columnarr = array();
				foreach($allcolumns as $key => $column){
	            	if(!in_array($column,$excelColumns)){
	            		$response=false;
	            		$columnarr[] = $column;
	            	}
	            }
	            if(!$response){
	            	$columnnames = implode(',',$columnarr);
	            	unlink($fileResp['filepath']);
	            	return array('status'=>false,'message'=> 'Your xls file having unmatched Columns to our database...Missing columns are:- '.$columnnames);
	            }else{
	            	$sheetproids = array_filter(array_column($sheetsdata,'id'));
	            	$allproids = Product::pluck('id')->toArray();
					$not_found = array();
					foreach($sheetproids as $key => $proid) {
					    if (!in_array($proid,$allproids)) {
					    	$not_found[$key] = $proid;
					    } 
					}
					if(!empty($not_found)){
						$firstKey = array_key_first($not_found);
						$lineNumber = $firstKey +2;
						return array('status'=>false,'message'=>'Invalid Product id at line number '.$lineNumber);
					}
	            	foreach($sheetsdata as $pkey => $prodata){
	            		if(!empty($prodata['id'])){
	            			$sku = $prodata['id'];
			            	$successSkus[] = $sku;
			                $id = "'".$prodata["id"]."'";
			                $cases[] = "WHEN {$id} then ?";
			                $params[] = $prodata[$importType[1]];
			                $ids[] = $id;
	            		}
            		}
            		$ids = implode(',', $ids);
		            $cases = implode(' ', $cases);
		            if (!empty($ids)) {
		                DB::update("UPDATE products SET `$importType[1]` = CASE `id` {$cases} END WHERE `id` in ({$ids})", $params);
		            }
		            unlink($fileResp['filepath']);
		            return array('status'=>true,'message'=>'Records has been updated successfully');
	            }
			}
		}
	}
	public static function generate_seo_url($string){
    	$separator = '-'; $wordLimit = 0;
    
		if($wordLimit != 0){
			$wordArr = explode(' ', $string);
			$string = implode(' ', array_slice($wordArr, 0, $wordLimit));
		}

		$quoteSeparator = preg_quote($separator, '#');

		$trans = array(
			'&.+?;'                    => '',
			'[^\w\d _-]'            => '',
			'\s+'                    => $separator,
			'('.$quoteSeparator.')+'=> $separator
		);

		$string = strip_tags($string);
		foreach ($trans as $key => $val){
			$string = preg_replace('#'.$key.'#i'.(''), $val, $string);
		}

		$string = strtolower($string);
		$seo_url =   trim(trim($string, $separator));
		return $seo_url;
    }
}
