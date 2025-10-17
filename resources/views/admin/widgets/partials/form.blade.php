
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
            <h1>Widgets Management</h1>
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

                <form id="WidgetForm" role="form" class="form-horizontal" method="post" action="javascript:;" enctype="multipart/form-data" autocomplete="off"> 
                            @if(isset($widget['id']))
                                @method('PUT')
                            @endif
                            <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
                            <div class="form-body">
                                <div class="form-group col-md-6">
                                    <label>Heading <span class="asteric">*</span></label>
                                        <input  type="text" placeholder="Heading" name="heading" style="color:gray" class="form-control" value="{{(!empty($widget['heading']))?$widget['heading']: '' }}"/>
                                    
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Widget Type <span class="asteric">*</span></label>
                                    
                                        @if(isset($widget['type']))
                                            <p class="form-control">{{$widget['type']}}</p>
                                            <input type="hidden" name="type" value="{{$widget['type']}}">
                                        @else 
                                            <select class="form-control" name="type">
                                                <option value="">Please Select</option>
                                                @foreach(widgetTypes() as $type)
                                                    <option value="{{$type}}">{{$type}}</option>
                                                @endforeach
                                            </select>
                                        @endif
                                    
                                </div>
                                <span id="AppendWidget">
                                    @if(isset($widget['type']))
                                        @php $widgetType = $widget['type']; @endphp
                                        @include('admin.widgets.partials.widgets')
                                    @endif
                                </span>
                                <div class="form-group col-md-6">
                                    <label>Description</label>
                                        <textarea  type="text" placeholder="Enter Description..." name="description" style="color:gray" class="form-control">{{(!empty($widget['description']))?$widget['description']: '' }}</textarea>
                                    
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Content</label>
                                        <textarea  type="text" placeholder="Enter Content..." name="content" style="color:gray" class="form-control">{{(!empty($widget['content']))?$widget['content']: '' }}</textarea>
                                    
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Start Date <span class="asteric">*</span></label>
                                    
                                        <input  type="date" placeholder="Start Date" name="start_date" style="color:gray" class="form-control" value="{{(!empty($widget['start_date']))?$widget['start_date']: '' }}"/>
                                    
                                </div>
                                <div class="form-group col-md-6">
                                    <label>End Date <span class="asteric">*</span></label>
                                    
                                        <input  type="date" placeholder="End Date" name="end_date" style="color:gray" class="form-control" value="{{(!empty($widget['end_date']))?$widget['end_date']: '' }}"/>
                                    
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Status <span class="asteric">*</span></label>
                                    <div class="col-md-4" style="margin-top:8px;">
                                        <?php $statusArr = array('1'=>'Active','0'=>'Inactive') ?>
                                        <select class="form-control" name="status">
                                            @foreach($statusArr as $skey=> $status)
                                                <option value="{{$skey}}" @if(isset($widget['status']) && $widget['status'] == $skey) selected @endif>{{$status}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div> 
                                @if(isset($widget['type']) && $widget['type'] == "MULTIPLE_BANNERS" && !is_null($widget['parent_id']))
                                    <div class="form-group col-md-6">
                                        <label>Make First Banner</label>
                                        <div class="col-md-4" style="margin-top:8px;">
                                            <input type="checkbox" name="first_banner" value="1">
                                        </div>
                                    </div> 
                                @endif            
                            </div>
                            <div>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </form>
                <!-- /.form-group -->
              </div>
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

