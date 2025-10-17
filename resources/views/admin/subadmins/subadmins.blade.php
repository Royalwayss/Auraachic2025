@extends('admin.layout.layout')
@section('content')
<style>
  /* Table Header Styling */
#subadmins thead input {
  width: 100%;
  box-sizing: border-box;
  margin-top: 5px;
}

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

/* Adjust column widths */
#subadmins {
  table-layout: fixed;
  width: 100%;
}

#subadmins th, 
#subadmins td {
  word-wrap: break-word;
}

#subadmins th:nth-child(1), 
#subadmins td:nth-child(1) {
  width: 10%; /* Reduced width for ID column */
}

#subadmins th:last-child, 
#subadmins td:last-child {
  width: 10%; /* Increased width for Actions column */
}

</style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Admin Management</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{'/admin/dashboard'}}">Home</a></li>
            <li class="breadcrumb-item active">Subadmins</li>
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
                <h3 class="card-title">Sub Admins</h3>
                <a style="max-width: 150px; float:right; display: inline-block;" href="{{ url('admin/add-edit-subadmin') }}" class="btn btn-block btn-primary">Add Sub Admin</a>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="subadmins" class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Name</th>
                      <th>Mobile</th>
                      <th>Email</th>
                      <th>Type</th>
                      <th>Created on</th>
                      <th>Actions</th>
                    </tr>
                    <?php /* <tr>
                      <!-- Add search inputs for each column -->
                      <th><input type="text" placeholder="Search ID" class="form-control"></th>
                      <th><input type="text" placeholder="Search Name" class="form-control"></th>
                      <th><input type="text" placeholder="Search Mobile" class="form-control"></th>
                      <th><input type="text" placeholder="Search Email" class="form-control"></th>
                      <th><input type="text" placeholder="Search Type" class="form-control"></th>
                      <th><input type="text" placeholder="Search Date" class="form-control"></th>
                      <th></th>
                    </tr> */ ?>
                  </thead>
                  <tbody>
                    @foreach($subadmins as $subadmin)
                    <tr>
                      <td>{{ $subadmin->id }}</td>
                      <td>{{ $subadmin->name }}</td>
                      <td>{{ $subadmin->mobile }}</td>
                      <td>{{ $subadmin->email }}</td>
                      <td>{{ $subadmin->type }}</td>
                      <td>{{ date("F j, Y, g:i a", strtotime($subadmin->created_at)) }}</td>
                      <td>
                        @if($subadmin->status==1)
                        <a class="updateSubadminStatus" id="subadmin-{{ $subadmin->id }}" subadmin_id="{{ $subadmin->id }}" style='color:#3f6ed3' href="javascript:void(0)"><i class="fas fa-toggle-on" status="Active"></i></a>
                        @else
                        <a class="updateSubadminStatus" id="subadmin-{{ $subadmin->id }}" subadmin_id="{{ $subadmin->id }}" style="color:grey" href="javascript:void(0)"><i class="fas fa-toggle-off" status="Inactive"></i></a>
                        @endif
                        &nbsp;&nbsp;
                        <a style='color:#3f6ed3;' href="{{ url('admin/add-edit-subadmin/'.$subadmin->id) }}"><i class="fas fa-edit"></i></a>
                        &nbsp;&nbsp;
                        <a style='color:#3f6ed3;' class="confirmDelete" name="Subadmin" title="Delete Subadmin" href="javascript:void(0)" record="subadmin" recordid="{{ $subadmin->id }}"><i class="fas fa-trash"></i></a>
                        &nbsp;&nbsp;
                        <a style='color:#3f6ed3;' href="{{ url('admin/update-role/'.$subadmin->id) }}"><i class="fas fa-unlock"></i></a>
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