<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\Brand;
use App\Models\Category;
class UploadProduct extends Model
{
    

    
 public static function getproductcolumns(){
	$productcoumns = array( 	
							'A'=>'brand_id',
							'B'=>'category_id',
							'C'=>'product_for',
							'D'=>'product_name',
							'E'=>'product_color',
							'F'=>'family_color',
							'G'=>'brand_color',
							'H'=>'product_code',
							'I'=>'group_code',
							'J'=>'gender',
							'K'=>'hsn_code',
							'L'=>'product_weight',
							'M'=>'description',
							'N'=>'status',
							'O'=>'product_price',
							'P'=>'product_discount',
							'Q'=>'b2b_product_price',
							'R'=>'product_gst',
							'S'=>'b2b_product_gst',
							'T'=>'sku',
							'U'=>'b2b_sku',
							'V'=>'size',
							'W'=>'attr_price',
							'X'=>'attr_b2b_price',
							'Y'=>'stock',
							'Z'=>'b2b_stock',
							'AA'=>'sort',
							'AB'=>'status',
							'AC'=>'round_chest',
							'AD'=>'top_length',
							'AE'=>'shoulder',
							'AF'=>'sleeve_length',
							'AG'=>'bottom_length',
							'AH'=>'waist',
							'AI'=>'tofit_waist',
							'AJ'=>'inseam',
							'AK'=>'waistcoat_length',
							'AL'=>'waistcoat_chest',
							'AM'=>'circumference',
							'AN'=>'length',
							'AO'=>'kurta_waist',
							'AP'=>'kurta_hip',
							'AQ'=>'is_new',
							'AR'=>'is_featured',
							'AS'=>'is_returnable',
							'AT'=>'sleeve',
							'AU'=>'neck',
							'AV'=>'fit',
							'AW'=>'pattern',
							'AX'=>'fabric',
							'AY'=>'print',
							'AZ'=>'closure',
							'BA'=>'style',
							'BB'=>'occasion',
							'BC'=>'clothing_set',
							'BD'=>'age',
							'BE'=>'season',
							'BF'=>'product_video',
							'BG'=>'wash_care',
							'BH'=>'search_keywords',
							'BI'=>'package_length',
							'BJ'=>'package_width',
							'BK'=>'package_height',
							'BL'=>'package_weight',
							'BM'=>'manufactured_by',
							'BN'=>'packed_by',
							'BO'=>'add_ons',
							'BP'=>'units',
							'BQ'=>'package_content',
							'BR'=>'saree_blouse',
							'BS'=>'saree_ornamentation',
							'BT'=>'saree_fabric',
							'BU'=>'saree_blouse_fabric',
							'BV'=>'saree_length',
							'BW'=>'saree_width',
							'BX'=>'saree_blouse_length',
							'BY'=>'kurta_neck',
							'BZ'=>'kurta_top_fabric',
							'CA'=>'kurta_design_styling',
							'CB'=>'kurta_oranamentation',
							'CC'=>'kurta_sleeve_styling',
							'CD'=>'kurta_stitch',
							'CE'=>'kurta_length',
							'CF'=>'kurta_sets_bottom_fabric',
							'CG'=>'kurta_sets_bottom_type',
							'CH'=>'kurta_sets_dupatta',
							'CI'=>'kurta_sets_dupatta_fabric',
							'CJ'=>'meta_title',
							'CK'=>'meta_description',
							'CL'=>'meta_keywords',
							'CM'=>'other_cat_ids',
							'CN'=>'image_0',
							'CO'=>'image_1',
							'CP'=>'image_2',
							'CQ'=>'image_3',
							'CR'=>'image_4',
							'CS'=>'image_5',
						);
							
	return $productcoumns;					

 }
 
 public static function check_columns($columns){
	 $getproductcoumns = UploadProduct::getproductcolumns();
	 $result=array_diff($columns,$getproductcoumns);
	 if(!empty($result)){
		$err_message = 'Some column is mismatching.<br>';
		 foreach($result as $row){
			 $err_message .= '<br>'.$row;
		 }
		 return $err_message; 
	 }else{
		 if(count($getproductcoumns) !=  count($columns)){
			 $err_message = 'Some column is mismatching.<br>';
			 return $err_message;
		 }
		
	 }
 }
 
 
 public static  function get_product_list($data){
	$get_products_array = array();
	$line = 0;
	foreach($data as $product_key => $product){ 
		
			$get_products = UploadProduct::get_products($product,$line);
			
			$get_products_array[] = $get_products['data'];
			$line = $get_products['lineNo'];
		
		
		 
	}
	return $get_products_array;
 }
 
 
 
 public static  function get_products($product,$line){
	  //get product
	     $line++;
	     $product_row = $product[0];
		
		  unset($product_row['sku']);
		  unset($product_row['size']);
		  unset($product_row['stock']);
		  unset($product_row['attr_price']);
		  unset($product_row['attr_b2b_price']);
		  
		
		  
		  //get product_image
		   $product_images['image_0'] = $product_row['image_0'];
		   $product_images['image_1'] = $product_row['image_1'];
		   $product_images['image_2'] = $product_row['image_2'];
		   $product_images['image_3'] = $product_row['image_3'];
		   $product_images['image_4'] = $product_row['image_4'];
		   $product_images['image_5'] = $product_row['image_5'];
		   $product_images['line'] = $line;
		   
		   
		  unset($product_row['image_0']);
		  unset($product_row['image_1']);
		  unset($product_row['image_2']);
		  unset($product_row['image_3']);
		  unset($product_row['image_4']);
		  unset($product_row['image_5']);
		 
		  $product_row['line'] = $line;
		  $data = $product_row;
		  $data['product_images'] =$product_images;
		  
		  //get product_attributes
		  /*echo "<pre>"; print_r($product); die;*/
		  foreach($product as $attr_key=>$row){
			  if($attr_key != 0){
				$line++;
			  }
			  $attr['sku'] = $row['sku'];
			  $attr['b2b_sku'] = $row['b2b_sku'];
			  $attr['size'] = $row['size'];
			  $attr['attr_price'] = $row['attr_price'];
			  $attr['attr_b2b_price'] = $row['attr_b2b_price'];
			  $attr['stock'] = $row['stock'];
			  $attr['b2b_stock'] = $row['b2b_stock'];
			  $attr['sort'] = $row['sort'];
			  $attr['round_chest'] = $row['round_chest'];
			  $attr['top_length'] = $row['top_length'];
			  $attr['shoulder'] = $row['shoulder'];
			  $attr['sleeve_length'] = $row['sleeve_length'];
			  $attr['bottom_length'] = $row['bottom_length'];
			  $attr['waist'] = $row['waist'];
			  $attr['tofit_waist'] = $row['tofit_waist'];
			  $attr['inseam'] = $row['inseam'];
			  $attr['waistcoat_length'] = $row['waistcoat_length'];
			  $attr['waistcoat_chest'] = $row['waistcoat_chest'];
			  $attr['circumference'] = $row['circumference'];
			  $attr['length'] = $row['length'];
			  $attr['kurta_waist'] = $row['kurta_waist'];
			  $attr['kurta_hip'] = $row['kurta_hip'];
			  $attr['line'] = $line;
			  $data['product_attribute'][] = $attr;
		  }
	  $data['lineNo'] = $line;
	  $data['data'] = $data;
	  return $data;
	  
 }
 
 public static function checkProductField($values,$column_name){
	    $message = '';
		if(count(array_filter($values)) != count($values)) {
			if($column_name != 'other_cat_ids' && $column_name != 'status'){
				$message = $column_name.' is missing <br>';	
			}
		}else{
			$array_unique = array_unique($values);
			if($column_name == 'brand_id'){
				foreach($array_unique as $field_value){
					$count = Brand::where('id',$field_value)->count();
					if(empty($count)){
						$message .= $column_name.' - '.$field_value.' is invalid <br>';
					}
				}
			}else if($column_name == 'category_id'){
				foreach($array_unique as $field_value){
					$count = Category::where('id',$field_value)->where('status','1')->count();
					if(empty($count)){
						$message .= $column_name.' - '.$field_value.' is invalid <br>';
					}
				}
			}else if($column_name == 'product_for'){
				$options = array('Both','Customer','Dealer');
				foreach($array_unique as $field_value){
						if (!in_array($field_value, $options)){
						 
							$message .= $column_name.' - '.$field_value.' is invalid <br>';
						}  
							
				}
			}else if($column_name == 'other_cat_ids'){
				$other_cat_ids = implode(',',$values);
				$cat_ids = array_unique(explode(',',$other_cat_ids));
				foreach($cat_ids as $cat_id){
					$count = Category::where('id',$cat_id)->count();
					if(empty($count)){
						$message .= $column_name.' - '.$cat_id.' is invalid <br>';
					}
				}
			}else if($column_name == 'status'){
				$options = array('1','0');
				foreach($array_unique as $field_value){
						if (!in_array($field_value, $options)){
						 
							$message .= $column_name.' - '.$field_value.' is invalid <br>';
						}  
							
				}
			}else if($column_name == 'is_new'){
				$options = array('Yes','No');
				foreach($array_unique as $field_value){
						if (!in_array($field_value, $options)){
						 
							$message .= $column_name.' - '.$field_value.' is invalid <br>';
						}  
							
				}
			}
		}
		
		return $message;
 }
    
	public static function check_image_name($image_name){
		    if($image_name != ''){
				$image_format = '';
				$explde = explode('.',$image_name);
				if(!empty($explde) && count($explde) > 1){
					$image_format =  end($explde);
				}
				
				
			
				if($image_format == 'jpg' || $image_format == 'png' || $image_format == 'JPG' || $image_format == 'PNG'){
				$pattern = "/^[a-zA-Z0-9]+$/";
				$invalid_chars = array(
					' ',
					'/',
					'&',
					',',
					'"',
					'?',
				   
				);
				foreach($invalid_chars as $invalid_char){
					if (count(explode($invalid_char, $image_name)) > 1) {
						if( $invalid_char == ' '){ 
							return 'video_description_thumbnail_image not accept white space.';
						}else{
							return 'video_description_thumbnail_image not accept '.$invalid_char.' ';
						}
					}
				}
				}else{ 
					return 'only accept jpg,png<br>';
				}
			}
					
	}
}
