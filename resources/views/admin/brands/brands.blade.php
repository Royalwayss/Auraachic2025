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
          <h1 class="m-0">Brands Management</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{'/admin/dashboard'}}">Home</a></li>
            <li class="breadcrumb-item active">Brands</li>
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
              @if($brandsModule['edit_access']==1 || $brandsModule['full_access']==1)
              <div class="card-header">
                <h3 class="card-title">Brands</h3>
                <!-- <a style="max-width: 150px; float:right; display: inline-block;" href="{{ url('admin/export-brands') }}" class="btn btn-block btn-primary">Export Brands</a>&nbsp; -->
                <a style="max-width: 150px; margin-top: 0px ; display: inline-block; float:right; margin-right: 10px;" href="{{ url('admin/brands/create') }}" class="btn btn-block btn-primary">Add Brand</a>

              </div>
              @endif
              <!-- /.card-header -->
              <div class="card-body">
                <table id="brands" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>URL</th>
                    <th>Created on</th>
                    <th>Actions</th>
                  </tr>
                  </thead>
                  <tbody>
                    @foreach($brands as $brand)
                  <tr>
                    <td>{{ $brand['id'] }}</td>
                    <td>{{ $brand['brand_name'] }}</td>
                    <td>{{ $brand['url'] }}</td>
                    <td>{{ date("F j, Y, g:i a", strtotime($brand['created_at'])); }}</td>
                    <td>
                      @if($brandsModule['edit_access']==1 || $brandsModule['full_access']==1)
                      @if($brand['status']==1)
                          <a class="updateBrandStatus" id="brand-{{ $brand['id'] }}" brand_id="{{ $brand['id'] }}" style='color:#3f6ed3' href="javascript:void(0)"><i class="fas fa-toggle-on" status="Active"></i></a>
                        @else
                          <a class="updateBrandStatus" id="brand-{{ $brand['id'] }}" brand_id="{{ $brand['id'] }}" style="color:grey" href="javascript:void(0)"><i class="fas fa-toggle-off" status="Inactive"></i></a>
                        @endif
                      @endif
                      @if($brandsModule['edit_access']==1 || $brandsModule['full_access']==1)
                        &nbsp;&nbsp;
                        <a style='color:#3f6ed3;' href="{{ url('admin/brands/'.$brand['id'].'/edit') }}"><i class="fas fa-edit"></i></a>
                        &nbsp;&nbsp;
                        @endif
                        @if($brandsModule['full_access']==1)
                        <!-- <a style='color:#3f6ed3;' class="confirmDelete" title="Delete Brand" href="javascript:void(0)" record="brand" recordid="{{ $brand['id'] }}"><i class="fas fa-trash"></i></a>   -->
                        <!-- With Resource Controller -->
                        <!-- <form class="confirmDeleteModule" action="{{ route('brands.destroy', $brand->id) }}" method="POST" style="display:inline;" module="brand">@csrf @method('DELETE')
                          <button type="submit" style="border:0px;"><i style='color:#3f6ed3;' class="fas fa-trash"></i></button>
                        </form> -->
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