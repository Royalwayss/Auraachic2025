<?php
use App\Models\ProductsFilter;
?>
@extends('admin.layout.layout')
@section('content')
<style>
  input[type="file"] {
  display: block;
}
.imageThumb {
  max-height: 75px;
  border: 2px solid;
  padding: 1px;
  cursor: pointer;
}
.pip {
  display: inline-block;
  margin: 10px 10px 0 0;
}
.remove {
  display: block;
  background: #444;
  border: 1px solid black;
  color: white;
  text-align: center;
  cursor: pointer;
}
.remove:hover {
  background: white;
  color: black;
}
.size_chart_table, th, td {
  border: 1px solid black!important;
  border-collapse: collapse;
  vertical-align: top!important;
  font-weight:bold;
}
.size_chart_table{ overflow: scroll;width:100%; }
.size_chart_table td{ width:50px!important; }
.attr-input{width: 118px !important; float: inline-start; padding:4px;margin-left: 3px; }
.add_button{ background: blue;color: white; width: 71px;margin-top: 10px;}
.remove_button { background: red;color: white; }   
   
.btn-animated-link {
  display: inline-flex;
  align-items: center;
  text-decoration: none;
  transition: all 0.3s ease;
}

.btn-animated-link i {
  margin-right: 5px;
  transition: transform 0.3s ease;
}

.btn-animated-link:hover {
  background-color: #0056b3;
  color: white;
}

.btn-animated-link:hover i {
  transform: translateX(-5px);
}

.btn-animated-link:last-child i {
  margin-left: 5px;
  margin-right: 0;
}

.btn-animated-link:last-child:hover i {
  transform: translateX(5px);
}    

</style>
<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Products Management</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{'/admin/dashboard'}}">Home</a></li>
              <li class="breadcrumb-item active">{{ $title }}</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">

        <div class="card card-default">
          <div class="card-header">
            <h3 class="card-title">{{ $title }}</h3>
            

            <div class="card-tools">
              <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
              </button>
              <button type="button" class="btn btn-tool" data-card-widget="remove">
                <i class="fas fa-times"></i>
              </button>
            </div>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <div class="row">
              <div class="col-12">
                <div style="float:right;">
                @if($prevId!=0)
                  <a href="{{ url('admin/add-edit-product/'.$prevId) }}" class="btn btn-primary btn-animated-link"><i class="fas fa-arrow-left"></i>&nbsp;&nbsp;Previous Product</a>
                @endif
                @if($nextId!=0)
                  <a href="{{ url('admin/add-edit-product/'.$nextId) }}" class="btn btn-primary btn-animated-link"> Next Product  <i class="fas fa-arrow-right"></i> </a>
                @endif
                </div>
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if(Session::has('success_message'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                  <strong>Success:</strong> {{ Session::get('success_message') }}
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                @endif

                <form name="productForm" id="productForm" action="{{ url('admin/add-edit-product/request') }}" method="post" enctype="multipart/form-data">@csrf
                @if(!empty($product['id']))
                  <input type="hidden" name="id" value="{{ $product['id'] }}">
                @endif

                <div class="card-body">
                  <div class="row">
                  <div class="form-group col-md-6">
                    <label for="category_id">Select Category*</label>
                    <select name="category_id" class="form-control" required>
                      <option value="">Select</option>
                      @foreach($getCategories as $cat)
                        <option @if(!empty(@old('category_id')) && $cat['id']==@old('category_id')) selected="" @elseif(!empty($product['category_id']) && $product['category_id']==$cat['id']) selected="" @endif value="{{ $cat['id'] }}">{{ $cat['category_name'] }}</option>
                        @if(!empty($cat['subcategories']))
                          @foreach($cat['subcategories'] as $subcat)
                            <option @if(!empty(@old('category_id')) && $subcat['id']==@old('category_id')) selected="" @elseif(!empty($product['category_id']) && $product['category_id']==$subcat['id']) selected="" @endif value="{{ $subcat['id'] }}">&nbsp;&nbsp;&nbsp;&nbsp;&raquo;&raquo;{{ $subcat['category_name'] }}</option> 
                            @if(!empty($subcat['subcategories']))
                              @foreach($subcat['subcategories'] as $subsubcat)
                                <option @if(!empty(@old('category_id')) && $subsubcat['id']==@old('category_id')) selected="" @elseif(!empty($product['category_id']) && $product['category_id']==$subsubcat['id']) selected="" @endif value="{{ $subsubcat['id'] }}">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&raquo;&raquo;{{ $subsubcat['category_name'] }}</option> 
                              @endforeach
                            @endif
                          @endforeach
                        @endif
                      @endforeach
                    </select>
                  </div>
                  <div class="form-group col-md-6">
                    <label for="cats">Select Other Categories*</label>
                    <select name="cats[]" id="e1" class="form-control selectbox MultipleSelect select2" required multiple size="15" style="height: 200px;">
                      @foreach($getCategories as $cat)
                        <option @if(in_array($cat['id'],$productCats)) selected @endif value="{{ $cat['id'] }}">{{ $cat['category_name'] }}</option>
                        @if(!empty($cat['subcategories']))
                          @foreach($cat['subcategories'] as $subcat)
                            <option @if(in_array($subcat['id'],$productCats)) selected @endif value="{{ $subcat['id'] }}">&nbsp;&nbsp;&nbsp;&nbsp;&raquo;&raquo;{{ $subcat['category_name'] }}</option> 
                            @if(!empty($subcat['subcategories']))
                              @foreach($subcat['subcategories'] as $subsubcat)
                                <option @if(in_array($subsubcat['id'],$productCats)) selected @endif value="{{ $subsubcat['id'] }}">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&raquo;&raquo;{{ $subsubcat['category_name'] }}</option> 
                              @endforeach
                            @endif
                          @endforeach
                        @endif
                      @endforeach
                    </select>
                    <!-- <span class="selectall"><input type="checkbox" id="cat_checkbox" >Select All</span> -->
                    <span class="btn btn-success btn-sm select-all" style="margin-top:8px;">{{ __('Select all') }}</span>
                    <span class="btn btn-danger btn-sm deselect-all" style="margin-top:8px;">{{ __('Deselect all') }}</span>
                  </div>
                  <div class="form-group col-md-6" style="display:none">
                    <label for="category_id">Select Brand*</label>
                    <select id="brand_id" name="brand_id" class="form-control" style="width: 100%;" >
                      <option value="">Select</option>
                        @foreach($brands as $brand)
                          <option value="{{ $brand['id'] }}" @if(!empty($product['brand_id']) && $product['brand_id'] == $brand['id']) selected @endif>{{ $brand['brand_name'] }}</option>
                        @endforeach
                    </select>
                  </div>
                  <div class="form-group col-md-6">
                    <label for="product_name">Product Name*</label>
                    <input type="text" class="form-control" id="product_name" name="product_name" placeholder="Enter Product Name" @if(!empty($product['product_name'])) value="{{ $product['product_name'] }}" @else value="{{ @old('product_name') }}" @endif required>
                  </div>
				  <div class="form-group col-md-6">
                    <label for="product_name">Product URL*</label>
                    <input type="text" class="form-control" id="product_url" name="product_url" placeholder="Enter Product URL" @if(!empty($product['product_url'])) value="{{ $product['product_url'] }}" @else value="{{ @old('product_url') }}" @endif required>
                  </div>
                  <div class="form-group col-md-6">
                    <label for="product_code">Product Code*</label>
                    <input type="text" class="form-control" id="product_code" name="product_code" placeholder="Enter Product Code" @if(!empty($product['product_code'])) value="{{ $product['product_code'] }}" @else value="{{ @old('product_code') }}" @endif required>
                  </div>
                  <div class="form-group col-md-6">
                    <label for="product_color">Product Color*</label>
                    <input type="text" class="form-control" id="product_color" name="product_color" placeholder="Enter Product Color" @if(!empty($product['product_color'])) value="{{ $product['product_color'] }}" @else value="{{ @old('product_color') }}" @endif required>
                  </div>
                  @php $familyColors = \App\Models\Color::colors() @endphp
                  <div class="form-group col-md-6">
                    <label for="family_color">Family Color*</label>
                    <select name="family_color" class="form-control" required="">
                      <option value="">Select</option>
                      @foreach($familyColors as $color)
                        <option value="{{$color['color_name']}}" @if(!empty(@old('family_color')) && @old('family_color')==$color['color_name']) selected="" @elseif(!empty($product['family_color']) && $product['family_color']==$color['color_name']) selected="" @endif>{{$color['color_name']}}</option>
                      @endforeach
                    </select>
                  </div>
				  
				  @foreach($product_filters as $product_filter) 
				  <div class="form-group col-md-3">
                    <label for="{{ $product_filter['key'] }}">{{ $product_filter['label'] }}*</label>
                    <select name="{{ $product_filter['key'] }}" class="form-control" required="">
                      <option value="">Select</option>
                      @foreach($product_filter['list'] as $filter_value)
                        <option value="{{ $filter_value }}" @if(!empty(@old($product_filter['key'])) && @old($product_filter['key'])==$filter_value) selected="" @elseif(!empty($product[$product_filter['key']]) && $product[$product_filter['key']]==$filter_value) selected="" @endif>{{ $filter_value }}</option>
                      @endforeach
                    </select>
                  </div>
				  @endforeach
				  
				  
				  
				  
				  
                  <div class="form-group col-md-6">
                    <label for="group_code">Group Code</label>
                    <input type="text" class="form-control" id="group_code" name="group_code" placeholder="Enter Group Code" @if(!empty($product['group_code'])) value="{{ $product['group_code'] }}" @else value="{{ @old('group_code') }}" @endif>
                  </div>
                 
                  <div class="form-group col-md-6">
                    <label for="product_price">Product Price*</label>
                    <input type="text" class="form-control" id="product_price" name="product_price" placeholder="Enter Product Price" @if(!empty($product['product_price'])) value="{{ $product['product_price'] }}" @else value="{{ @old('product_price') }}" @endif required="">
                  </div>
                  @if(!empty($product['id']))
				          <div class="form-group col-md-6">
                    <label for="discount_type">Discount Type</label>
                    <input type="text" class="form-control" id="discount_type" name="discount_type"   value="{{(!empty($product['discount_type']))?$product['discount_type']: '' }}" disabled>
                  </div>
				          @endif 
				  
				          <div class="form-group col-md-6">
                    <label for="product_discount">Product Discount (%)</label>
                    <input type="text" class="form-control" id="product_discount" name="product_discount" placeholder="Enter Product Discount (%)" @if(!empty($product)  && ( $product['discount_type']=="category" || $product['discount_type']=="brand")) value="" @else value="{{(!empty($product['product_discount']))?$product['product_discount']: '' }}" @endif>
                  </div>
				  
                  <div class="form-group col-md-6">
                    <label for="product_weight">Product Weight (in grams)</label>
                    <input type="text" class="form-control" id="product_weight" name="product_weight" placeholder="Enter Product Weight" @if(!empty($product['product_weight'])) value="{{ $product['product_weight'] }}" @else value="{{ @old('product_weight') }}" @endif>
                  </div>
                  <div class="form-group col-md-6">
                    <label for="product_sort">Product Sort</label>
                    <input type="number" class="form-control" id="product_sort" name="product_sort" placeholder="Enter Product Sort" @if(!empty($product['product_sort'])) value="{{ $product['product_sort'] }}" @else value="{{ @old('product_sort') }}" @endif>
                  </div>

                  <div class="form-group col-md-6">
                    <label for="main_image">Product Image (for Listing/Cart Pages, Recommend Size: 1000 x 1288)</label>
                    <input type="file" class="form-control" id="main_image" name="main_image" accept="image/*">
                    @if(!empty($product['main_image']))
                      <a target="_blank" href="{{ url('front/images/products/small/'.$product['main_image']) }}"><img style="width:50px; margin: 10px;" src="{{ asset('front/images/products/small/'.$product['main_image']) }}"></a>
                      <a style='color:#3f6ed3;' class="confirmDelete" title="Delete Product Image" href="javascript:void(0)" record="product-main-image" recordid="{{ $product['id'] }}"><i style="color:#3F6ED3" class="fas fa-trash"></i></a>
                    @endif
                  </div>
                  
                  <div class="form-group col-md-6">
                      <label for="product_images">Product Images Recommend Size: 1000 x 1500 <br>Medium - 500 x 750,Small 120 x 180 </label>
                      <input type="file" class="form-control" id="files" name="product_images[]" multiple accept="image/*">
                      <table cellpadding="4" cellspacing="4" border="1" style="margin: 5px;">
                        <tr style="margin: 5px; padding: 5px;">
                      @foreach($product['images'] as $image)
                        <td style="background-color:#f9f9f9; margin: 5px; padding: 5px;">
                          <a target="_blank" href="{{ url('front/images/products/small/'.$image['image']) }}"><img style="width:60px;" src="{{ asset('front/images/products/small/'.$image['image']) }}"></a>&nbsp;
                          <input type="hidden" name="image[]" value="{{ $image['image'] }}">
                          <input style="width:30px;" type="text" placeholder="Sort" name="image_sort[]" value="{{ $image['image_sort'] }}">  
                          <a style='color:#3f6ed3;' class="confirmDelete" title="Delete Product Image" href="javascript:void(0)" record="product-image" recordid="{{ $image['id'] }}"><i class="fas fa-trash"></i></a>
                        </td>
                      @endforeach
                      </tr>
                    </table>
                  </div>

                  <div class="form-group col-md-6">
                    <label for="product_video">Product Video (Recommend Size: *.mp4 with less than 2 MB)</label>
                    <input type="file" class="form-control" id="product_video" name="product_video" accept="video/*">
                    @if(!empty($product['product_video']))
                      <a target="_blank" href="{{ url('front/videos/products/'.$product['product_video']) }}" style="color:#ccc">View</a> |
                      <a class="confirmDelete" title="Delete Product Video" href="javascript:void(0)" record="product-video" recordid="{{ $product['id'] }}" style="color:#ccc">Delete</a>
                    @endif
                  </div>

                  <div class="form-group col-md-6">
                    <label for="video_thumbnail">Video Thumbnail(Recommend Size: 1000 x 1288)</label>
                    <input type="file" class="form-control" id="video_thumbnail" name="video_thumbnail" accept="image/*">
                    @if(!empty($product['video_thumbnail']))
                      <a target="_blank" href="{{ url('front/videos/thumbnails/'.$product['video_thumbnail']) }}"><img style="width:50px; margin: 10px;" src="{{ asset('front/videos/thumbnails/'.$product['video_thumbnail']) }}"></a>
                      <a style='color:#3f6ed3;' class="confirmDelete" title="Delete Video Thumbnail" href="javascript:void(0)" record="product-video-thumbnail" recordid="{{ $product['id'] }}"><i style="color:#3F6ED3" class="fas fa-trash"></i></a>
                    @endif
                  </div>

                  @if(count($product['attributes'])>0)
                  <div class="form-group">
                    <label>Attributes</label>
                    <table style="width: 80%;" cellpadding="5">
                      
                        @foreach($product['attributes'] as $attribute)
                          <tr>
                        <th>ID</th>
                        <th>Size</th>
                        <th>SKU</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Sort</th>
                        <th>Actions</th>
                      </tr>
						            <input style="display: none;" type="text" name="attrId[]" value="{{ $attribute['id'] }}">
                          <tr>
                            <td>{{ $attribute['id'] }}</td>
                            <td>{{ $attribute['size'] }}</td>
                            <td>{{ $attribute['sku'] }}</td>
                            <td>
                              <input style="width:100px;" class="form-control" type="number" name="price[]" value="{{ $attribute['price'] }}" required="">
                            </td>
                            <td>
                              <input style="width:100px;" class="form-control" type="number" name="stock[]" value="{{ $attribute['stock'] }}" required="">
                            </td>
							              <td>
                              <input style="width:100px;" class="form-control" type="number" name="edit-sort[]" value="{{ $attribute['sort'] }}" required="">
                            </td>
                            <td>
                            @if($attribute['status']==1)
                              <a class="updateAttributeStatus" id="attribute-{{ $attribute['id'] }}" attribute_id="{{ $attribute['id'] }}" style='color:#3f6ed3' href="javascript:void(0)"><i class="fas fa-toggle-on" status="Active"></i></a>
                            @else
                              <a class="updateAttributeStatus" id="attribute-{{ $attribute['id'] }}" attribute_id="{{ $attribute['id'] }}" style="color:grey" href="javascript:void(0)"><i class="fas fa-toggle-off" status="Inactive"></i></a>
                            @endif
                             &nbsp;&nbsp;
                            <a title="Delete Attribute" href="javascript:void(0)" class="confirmDelete" record="attribute" recordid="{{ $attribute['id'] }}"><i class="fas fa-trash"></i></a> 
                            </td>
                          </tr>
                          
                        @endforeach
                    </table>
                  </div>
                  @endif
                  <div class="form-group">
                    <label for="product_video">Add Attributes</label>
                    <div class="field_wrapper">
                      <input type="text" class="form-control attr-input" name="sku[]" id="sku" placeholder="SKU" style="width:120px;">
                      <input type="text" class="form-control attr-input" name="size[]" id="size" placeholder="Size" style="width:120px;">
                      <input type="text" class="form-control attr-input" name="price[]" id="price" placeholder="Price" style="width:120px;"> 
                      <input type="text" class="form-control attr-input" name="stock[]" id="stock" placeholder="Stock" style="width:120px;">
                      <input type="number" class="form-control attr-input" name="sort[]"  placeholder="Sort" style="width:120px;">
                      <a href="javascript:void(0);" class="add_button btn btn-primary" style="width: 71px;margin-top: 2px; margin-left:20px;" title="Add field">Add</a>
				  
                    </div>
                  </div>
				  
                  <?php $filterTypes = ProductsFilter::filterTypes(); ?>
                  @foreach($filterTypes as $fkey=> $ftype)
                      <?php $filterOptions = ProductsFilter::profilters($ftype); ?>
                      <div class="form-group col-md-6">
                          <label for="{{$ftype}}">
                          <?php $fiType = str_replace("_"," ",$ftype); ?>
                          {{ucwords($fiType)}} :</label>
                            <select name="{{$ftype}}" class="form-control">
                                <option value="">Please Select</option>
                                @foreach($filterOptions as $filterVal)
                                    <option value="{{$filterVal->filter_value}}" @if(isset($product[$ftype]) && $product[$ftype] ==$filterVal->filter_value) selected @endif>{{$filterVal->filter_value}}</option>
                                @endforeach
                            </select>
                      </div>
                  @endforeach
                 <?php /* <div class="form-group">
                    <label for="fabric">Fabric</label>
                    <select name="fabric" class="form-control">
                      <option value="">Select</option>
                      @foreach($productsFilters['fabricArray'] as $fabric)
                        <option value="{{$fabric}}" @if(!empty(@old('fabric')) && @old('fabric')==$fabric) selected="" @elseif(!empty($product['fabric']) && $product['fabric']==$fabric) selected="" @endif>{{$fabric}}</option>
                      @endforeach
                    </select>
                  </div> */ ?>
                  
                  <div class="form-group col-md-12">
                    <label for="description">Product Description</label>
                    <textarea class="form-control" rows="3" id="summernote_desc" name="description" placeholder="Enter Product Description">@if(!empty($product['description'])) {{ $product['description'] }} @else {{ @old('description') }} @endif</textarea>
                  </div>
                  <div class="form-group col-md-12">
                    <label for="description">Key Features</label>
                    <textarea class="form-control" rows="3" id="summernote_features" name="key_features" placeholder="Enter Product Key Features">@if(!empty($product['key_features'])) {{ $product['key_features'] }} @else {{ @old('key_features') }} @endif</textarea>
                  </div>
                  <div class="form-group col-md-12">
                    <label for="search_keywords">Search Keywords</label>
                    <textarea class="form-control" rows="3" id="search_keywords" name="search_keywords" placeholder="Enter Product Search Keywords">@if(!empty($product['search_keywords'])) {{ $product['search_keywords'] }} @else {{ @old('search_keywords') }} @endif</textarea>
                  </div>
                  
				          <div class="form-group col-md-4">
                    <label for="product_gst">Product GST</label>
                    <input type="number" class="form-control" id="product_gst" name="product_gst" placeholder="Enter Product Gst" @if(!empty($product['product_gst'])) value="{{ $product['product_gst'] }}" @else value="{{ @old('product_gst') }}" @endif>
                  </div>
				          
				          <div class="form-group col-md-4">
                    <label for="is_featured">Trending Product(Featured)</label>
                    <input type="checkbox" name="is_featured" value="Yes" @if(!empty($product['is_featured']) && $product['is_featured']=="Yes") checked="" @endif>
                  </div>
                  
                  
                   <div class="form-group col-md-4">
                    <label for="is_new">New Arrival</label>
                    <input type="checkbox" name="is_new" value="Yes" @if(!empty($product['is_new']) && $product['is_new']=="Yes") checked="" @endif>
                  </div>
                  
                  
                  
                  
                  
                  
                  
				          
				        <!-- <div class="form-group col-md-6">
                    <label for="status">Status</label>
                    <input type="checkbox" name="status" value="1" @if(!empty($product['status']) && $product['status']=="1") checked="" @endif>
                  </div>
                </div> -->
                <!-- /.card-body -->
                <div>
                  <button type="submit" class="btn btn-primary">Submit</button>
                </div>
              </form>
                <!-- /.form-group -->
              </div>
              <!-- /.col -->
            </div>
            </div>
            <!-- /.row -->
          </div>
          <!-- /.card-body -->
          <div class="card-footer">
          </div>
        </div>
        <!-- /.card -->

        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
<script src="{{ asset('front/assets/js/vendor.js') }}"></script>
<script>
$(document).ready(function () {
	$('#product_name').on('keyup', function() {
	  var productName = $(this).val();
	  var product_url = generateSlug(productName); 
	  $("#product_url").val(product_url);
	});
});

function generateSlug(productName) {
        let slug = productName.toLowerCase();
        slug = slug.replace(/[^a-z0-9\s-]/g, ''); // Remove special characters except spaces and hyphens
        slug = slug.replace(/\s+/g, '-'); // Replace spaces with hyphens
        slug = slug.replace(/^-+|-+$/g, ''); // Remove leading/trailing hyphens
        slug = slug.replace(/-+/g, '-'); // Replace multiple hyphens with a single hyphen
        return slug;
    }
</script>
@endsection