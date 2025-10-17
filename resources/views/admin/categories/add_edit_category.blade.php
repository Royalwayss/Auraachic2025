<style>
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
@extends('admin.layout.layout')
@section('content')

<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Categories Management</h1>
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
                  <a href="{{ url('admin/add-edit-category/'.$prevId) }}" class="btn btn-primary btn-animated-link"><i class="fas fa-arrow-left"></i>&nbsp;&nbsp;Previous Category</a>
                @endif
                @if($nextId!=0)
                  <a href="{{ url('admin/add-edit-category/'.$nextId) }}" class="btn btn-primary btn-animated-link"> Next Category  <i class="fas fa-arrow-right"></i> </a>
                @endif
                </div>
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
                <form name="categoryForm" id="categoryForm" action="{{ url('admin/add-edit-category/request') }}" method="post" enctype="multipart/form-data">@csrf
                @if(!empty($category['id']))
                  <input type="hidden" name="id" value="{{ $category['id'] }}">
                @endif
                <div class="card-body">
                  <div class="row">
				  <div class="form-group col-md-6">
                    <label for="category_name">Category Name*</label>
                    <input type="text" class="form-control" id="category_name" name="category_name" placeholder="Enter Category Name" @if(!empty($category['category_name'])) value="{{ $category['category_name'] }}" @else value="{{ old('category_name') }}" @endif pattern="[-a-zA-Z0-9_\.]+">
                  </div>
                  
                  <div class="form-group col-md-6">
                    <label for="category_name">Category Level (Parent Category)*</label>
                    <select name="parent_id" class="form-control">
                      <option value="">Select</option>
                      <option value="0" @if($category['parent_id']==0) selected="" @endif>Main Category</option>
                      @foreach($getCategories as $cat)
                        <option @if(isset($category['parent_id'])&&$category['parent_id']==$cat['id']) selected @endif value="{{ $cat['id'] }}">{{ $cat['category_name'] }}</option>
                        @if(!empty($cat['subcategories']))
                          @foreach($cat['subcategories'] as $subcat)
                            <option value="{{ $subcat['id'] }}" @if(isset($category['parent_id'])&&$category['parent_id']==$subcat['id']) selected @endif>&nbsp;&nbsp;&nbsp;&nbsp;&raquo;&raquo;{{ $subcat['category_name'] }}</option> 
                            @if(!empty($subcat['subcategories']))
                              @foreach($subcat['subcategories'] as $subsubcat)
                                <option value="{{ $subsubcat['id'] }}">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&raquo;&raquo;{{ $subsubcat['category_name'] }}</option> 
                              @endforeach
                            @endif
                          @endforeach
                        @endif
                      @endforeach
                    </select>
                  </div>
                  <div class="form-group col-md-6">
                    <label for="category_image">Category Image</label>
                    <input type="file" class="form-control" id="category_image" name="category_image" accept="image/*">
                    @if(!empty($category['category_image']))
                      <a target="_blank" href="{{ url('front/images/categories/'.$category['category_image']) }}"><img style="width:50px; margin: 10px;" src="{{ asset('front/images/categories/'.$category['category_image']) }}"></a>
                      <a style='color:#3f6ed3;' class="confirmDelete" title="Delete Category Image" href="javascript:void(0)" record="category-image" recordid="{{ $category['id'] }}"><i style="color:#3F6ED3" class="fas fa-trash"></i></a>
                    @endif
                  </div>
                  <?php /* <div class="form-group col-md-6">
                    <label for="category_image_title">Category Image Title</label>
                    <input type="text" class="form-control" id="category_image_title" name="category_image_title" placeholder="Enter Category Image Title" @if(!empty($category['category_image_title'])) value="{{ $category['category_image_title'] }}" @else value="{{ old('category_image_title') }}" @endif>
                  </div> */ ?>
                  <?php /* <div class="form-group col-md-6">
                    <label for="category_image_status">Show on Home Page</label>
                    <input type="checkbox" name="category_image_status" value="1" @if(!empty($category['category_image_status']) && $category['category_image_status']==1) checked="" @endif>
                  </div> */ ?>
                  <div class="form-group col-md-6">
                    <label for="size_chart">Size Chart</label>
                    <select class="form-control" id="sizeChartOption" name="sizeChartOption">
                      <option value="">Select Image or Text Format</option>
                      <option value="Image">Image</option>
                      <option value="Text">Text</option>
                    </select>
                  </div>
                  <div class="form-group col-md-6 sizeChartImage">
                    <label for="size_chart">Size Chart Image</label>
                    <input type="file" class="form-control" id="size_chart" name="size_chart"  accept="image/*">
                    @if(!empty($category['size_chart']))
                      <a target="_blank" href="{{ url('front/images/sizecharts/'.$category['size_chart']) }}"><img style="width:50px; margin: 10px;" src="{{ asset('front/images/sizecharts/'.$category['size_chart']) }}"></a>
                      <a style='color:#3f6ed3;' class="confirmDelete" title="Delete Size Chart" href="javascript:void(0)" record="size-chart-image" recordid="{{ $category['id'] }}"><i style="color:#3F6ED3" class="fas fa-trash"></i></a>
                    @endif
                  </div>
                  <div class="form-group col-md-12 sizeChartText">
                    <label for="size_chart">Size Chart (in Text/Table Format)</label>
                    <textarea class="form-control" rows="3" id="summernote_size_chart" name="size_chart" placeholder="Enter Size Chart Details">@if(!empty($category['size_chart'])) {{ $category['size_chart'] }} @else {{ old('size_chart') }} @endif</textarea>
                  </div>
                  <?php /* <div class="form-group col-md-6">
                    <label for="size_chart">Size Chart</label>
                    <textarea class="form-control" rows="3" id="summernote_size_chart" name="size_chart" placeholder="Enter Size Chart">@if(!empty($category['size_chart'])) {{ $category['size_chart'] }} @else {{ @old('size_chart') }} @endif</textarea>
                  </div> */ ?>
                  <div class="form-group col-md-6">
                    <label for="category_discount">Category Discount</label>
                    <input type="text" class="form-control" id="category_discount" name="category_discount" placeholder="Enter Category Discount" @if(!empty($category['category_discount'])) value="{{ $category['category_discount'] }}" @else value="{{ old('category_discount') }}" @endif>
                  </div>
                  <div class="form-group col-md-6">
                    <label for="url">Category URL*</label>
                    <input type="text" class="form-control" id="url" name="url" placeholder="Enter Category URL" @if(!empty($category['url'])) value="{{ $category['url'] }}" @else value="{{ old('url') }}" @endif>
                  </div>
                  <div class="form-group col-md-6">
                    <label for="url">How to add Category URL ?</label>
                    <table class="table table-striped table-bordered table-hover">
                        <tr>
                            <th>Category Name</th>
                            <th>Category URL</th>
                        </tr>
                        <tr>
                            <td>T Shirts</td>
                            <td>t-shirts</td>
                      </tr>
                    </table>
                    <b>Note: Don't enter any Special Characters in Category URL except (-)</b>
                  </div>
                  <?php /* <div class="form-group col-md-6">
                    <label for="category_name">Filters*</label>
                    @php $selFilters = array() @endphp
                    @if(!empty($category['filters']))
                        @php $selFilters = explode(',',$category['filters']) @endphp
                    @endif
                    <select class="form-control selectpicker" name="filters[]" multiple="">
                        @php $filters = array('Price','Color','Categories','gender') @endphp
                        @foreach($filters as $filter)
                            @php $selFilter="" @endphp
                            @if(in_array($filter,$selFilters))
                                @php $selFilter ="selected"; @endphp
                            @endif
                            <option value="{{$filter}}" {{$selFilter}}>{{$filter}}</option>
                        @endforeach
                    </select>
                  </div> */ ?>

                  <div class="form-group col-md-6">
                    <label for="description">Category Description</label>
                    <textarea class="form-control" rows="3" id="description" name="description" placeholder="Enter Category Description">@if(!empty($category['description'])) {{ $category['description'] }} @else {{ old('description') }} @endif</textarea>
                  </div>
                  <div class="form-group col-md-6">
                    <label for="meta_title">Meta Title</label>
                    <input type="text" class="form-control" id="meta_title" name="meta_title" placeholder="Enter Meta Title" @if(!empty($category['meta_title'])) value="{{ $category['meta_title'] }}" @else value="{{ old('meta_title') }}" @endif>
                  </div>
                  <div class="form-group col-md-6">
                    <label for="meta_description">Meta Description</label>
                    <input type="text" class="form-control" id="meta_description" name="meta_description" placeholder="Enter Meta Description" @if(!empty($category['meta_description'])) value="{{ $category['meta_description'] }}" @else value="{{ old('meta_description') }}" @endif>
                  </div>
                  <div class="form-group col-md-6">
                    <label for="meta_keywords">Meta Keywords</label>
                    <input type="text" class="form-control" id="meta_keywords" name="meta_keywords" placeholder="Enter Meta Keywords" @if(!empty($category['meta_keywords'])) value="{{ $category['meta_keywords'] }}" @else value="{{ old('meta_keywords') }}" @endif>
                  </div>
                  <div class="form-group col-md-6">
                    <label for="menu_status">Show on Header Menu</label>
                    <input type="checkbox" name="menu_status" value="1" @if(!empty($category['menu_status']) && $category['menu_status']==1) checked="" @endif>
                  </div>
                  
                </div>
                </div>
                <!-- /.card-body -->

                <div>
                  <button type="submit" class="btn btn-primary">Submit</button>
                </div>
              </form>
                <!-- /.form-group -->
             
              <!-- /.col -->
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

@endsection