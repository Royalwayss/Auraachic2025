<?php use App\Models\Product; ?>
@extends('admin.layout.layout')
@section('content')

<style>
/* Action Icon Hover Effect */
a .fas {
  transition: color 0.3s ease, transform 0.3s ease;
}

a:hover .fas {
  color: #1E90FF; /* Vibrant blue color on hover */
  transform: scale(1.2); /* Slightly enlarge icon */
}

/* Action Icons for Better Visibility */
a .fas.fa-toggle-on {
  color: #28a745; /* Green for Active */
}

a .fas.fa-toggle-off {
  color: #dc3545; /* Red for Inactive */
}

a .fas.fa-edit {
  color: #ffc107; /* Yellow for Edit */
}

a .fas.fa-trash {
  color: #dc3545; /* Red for Delete */
}

a .fas.fa-unlock {
  color: #17a2b8; /* Teal for Update Role */
}

</style>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Products Management</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{'/admin/dashboard'}}">Home</a></li>
            <li class="breadcrumb-item active">Products</li>
          </ol>
        </div><!-- /.col -->
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content-header -->

  <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            @if(Session::has('success_message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              <strong>Success:</strong> {{ Session::get('success_message') }}
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            @endif
            <div class="card">
              
              <div class="card-header">
                <h3 class="card-title">Products</h3>
                <div class="text-right">
                @if($productsModule['edit_access']==1 || $productsModule['full_access']==1)
                <a style="max-width: 150px; margin-top: 0px ;  display: inline-block;" href="{{ url('admin/export-products') }}" class="btn btn-block btn-primary">Export Products</a>
                <!--<a style="max-width: 150px; margin-top: 0px ; margin-left:5px; display: inline-block;" href="{{ url('admin/import-data') }}" class="btn btn-block btn-primary">Import Products</a>-->
                <a style="max-width: 150px; margin-top: 0px ; display: inline-block;" href="{{ url('admin/add-edit-product') }}" class="btn btn-block btn-primary">Add Product</a>
                <a style="max-width: 150px; margin-top: 0px ; display: inline-block;" href="{{ url('admin/update-stock') }}" class="btn btn-block btn-primary">Update Stock</a>
                @endif
                </div>
              </div>
              
              <!-- /.card-header -->
              <div class="card-body">
                <table id="products" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>ID</th>
                    <th>Product Name</th>
                    <th>Product Code</th>
                    <th>Product Color</th>
                    <th>Category</th>
                    <th>Parent Category</th>
                    <th>Actions</th>
                  </tr>
                  </thead>
                  <tbody>
                    @foreach($products as $product)
                  <tr>
                    <td>{{ $product['id'] }}</td>
                    <td>
                      <?php $getProductURL = $product['product_url']; ?>
                      <a target="_blank" style="color:#000;" href="{{ route('product',[$getProductURL,$product['id']]) }}" class="p-a-link">{{ $product['product_name'] }}</a>
                    </td>
                    <td>{{ $product['product_code'] }}</td>
                    <td>{{ $product['product_color'] }}</td>
                    <td>{{ $product['category']['category_name'] }}</td>
                    <td>
                      @if(isset($product['category']['parentcategory']['category_name']))
                        {{ $product['category']['parentcategory']['category_name'] }}
                      @endif
                    </td>
                     <td>
                       @if($productsModule['edit_access']==1 || $productsModule['full_access']==1)
                        @if($product['status']==1)
                            <a class="updateProductStatus" id="product-{{ $product['id'] }}" product_id="{{ $product['id'] }}" style='color:#3f6ed3' href="javascript:void(0)"><i class="fas fa-toggle-on" status="Active"></i></a>
                          @else
                            <a class="updateProductStatus" id="product-{{ $product['id'] }}" product_id="{{ $product['id'] }}" style="color:grey" href="javascript:void(0)"><i class="fas fa-toggle-off" status="Inactive"></i></a>
                          @endif
                      @endif
                      @if($productsModule['edit_access']==1 || $productsModule['full_access']==1)
                       
                       <?php /* <br> <label for="is_new" style="font-size: 13px;color:#3f6ed3;">Is New?</label>
                       <input type="checkbox" style="cursor:pointer;" onclick="product_attr_status_update(this)" data-attr="is_new"  data-id="{{ $product['id'] }}"  @if($product['is_new']=='Yes') checked @endif >
                         
                       <label for="is_new" style="font-size: 13px;color:#3f6ed3;">Is Featured?</label> 
                       <input type="checkbox" style="cursor:pointer;" onclick="product_attr_status_update(this)" data-attr="is_featured"  data-id="{{ $product['id'] }}"  @if($product['is_featured']=='Yes') checked @endif > */ ?>
                           
                      @endif
                      
                     
                      @if($productsModule['edit_access']==1 || $productsModule['full_access']==1)
                        &nbsp;&nbsp;
                        <a style='color:#3f6ed3;' href="{{ url('admin/add-edit-product/'.$product['id']) }}"><i class="fas fa-edit"></i></a>
                        &nbsp;&nbsp;
                      @endif
                      @if($productsModule['full_access']==1)
                        <!-- <a style='color:#3f6ed3;' class="confirmDelete" title="Delete Product" href="javascript:void(0)" record="product" recordid="{{ $product['id'] }}"><i class="fas fa-trash"></i></a> -->
                      @endif  

                    </td>
                  </tr>
                  @endforeach
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

@endsection