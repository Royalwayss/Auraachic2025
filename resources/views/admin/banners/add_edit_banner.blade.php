@extends('admin.layout.layout')
@section('content')

<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Banners Management</h1>
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
                  <a href="{{ url('admin/add-edit-banner/'.$prevId) }}" class="btn btn-primary btn-animated-link"><i class="fas fa-arrow-left"></i>&nbsp;&nbsp;Previous Banner</a>
                @endif
                @if($nextId!=0)
                  <a href="{{ url('admin/add-edit-banner/'.$nextId) }}" class="btn btn-primary btn-animated-link"> Next Banner  <i class="fas fa-arrow-right"></i> </a>
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
                <form class="forms-sample" action="{{ url('admin/add-edit-banner/request') }}" method="post" enctype="multipart/form-data">@csrf
                    @if(!empty($banner['id']))
                      <input type="hidden" name="id" value="{{ $banner['id'] }}">
                    @endif
                    <div class="form-group col-md-6">
                      <label for="link">Banner Type</label>
                      <select class="form-control" id="type" name="type" required="">
                        <option value="">Select</option>
                        <!-- <option @if(!empty($banner['type'])&& $banner['type']=="Slider (Desktop)") selected="" @endif value="Slider (Desktop)">Slider (Desktop) 1362x500</option> -->
                        <!-- <option @if(!empty($banner['type'])&& $banner['type']=="Slider (Mobile)") selected="" @endif value="Slider (Mobile)">Slider (Mobile) 1500x800</option> -->
                        <option @if(!empty($banner['type'])&& $banner['type']=="Slider") selected="" @endif value="Slider">Top Slider</option>
                        <option @if(!empty($banner['type'])&& $banner['type']=="Category") selected="" @endif value="Category">Category Banner</option>
                        <option @if(!empty($banner['type'])&& $banner['type']=="Single") selected="" @endif value="Single">Single banner</option>
                        <option @if(!empty($banner['type'])&& $banner['type']=="Instagram") selected="" @endif value="Instagram">Instagram banner</option>
                        <option @if(!empty($banner['type'])&& $banner['type']=="Featured") selected="" @endif value="Featured">Featured banner</option>
                      </select>
                    </div>
                    <div class="form-group col-md-6">
                      <label for="admin_image">Desktop Banner Image<br>
					     <small><strong> Desktop Top Slider - 1920x700</strong></small> <br>
					      <small><strong> Desktop Category Banner - 1090x560</strong></small> <br>
					      <small><strong> Desktop Single Banner - 1920x740</strong></small> <br>
					      <small><strong> Desktop Instagram Banner - 510x760</strong></small>  <br>
					      <small><strong> Desktop Instagram Banner - 200x322</strong></small> 
					  </label>
                      <input type="file" class="form-control" id="image" name="image">
                      @if(!empty($banner['image']))
                        <a target="_blank" href="{{ url('front/images/banners/'.$banner['image']) }}">View Image</a>
                      @endif
                    </div>

                    <div class="form-group col-md-6">
                      <label for="admin_image">Mobile Banner Image<br>
					 <small> <strong>Desktop Top Slider - 770x660</strong></small></label>
                      <input type="file" class="form-control" id="mobile_image" name="mobile_image">
                      
                      @if(!empty($banner['mobile_banner']))
                        <a target="_blank" href="{{ url('front/images/banners/'.$banner['mobile_banner']) }}">View Image</a>
                      @endif
                    </div>

                    <div class="form-group col-md-6">
                      <label for="link">Banner Link</label>
                      <input type="text" class="form-control" id="link" placeholder="Enter Banner Link" name="link" @if(!empty($banner['link'])) value="{{ $banner['link'] }}" @else value="{{ old('link') }}" @endif>
                    </div>
                    <div class="form-group col-md-6">
                      <label for="title">Banner Title</label>
                      <input type="text" class="form-control" id="title" placeholder="Enter Banner Title" name="title" @if(!empty($banner['title'])) value="{{ $banner['title'] }}" @else value="{{ old('title') }}" @endif>
                    </div>
                    <div class="form-group col-md-6">
                      <label for="alt">Banner Alternate Text</label>
                      <input type="text" class="form-control" id="alt" placeholder="Enter Banner Alternate Text" name="alt" @if(!empty($banner['alt'])) value="{{ $banner['alt'] }}" @else value="{{ old('alt') }}" @endif>
                    </div>
                    <div class="form-group col-md-6">
                      <label for="sort">Banner Sort</label>
                      <input type="number" class="form-control" id="sort" placeholder="Enter Banner Sort" name="sort" @if(!empty($banner['sort'])) value="{{ $banner['sort'] }}" @else value="{{ old('sort') }}" @endif>
                    </div>
                    <button type="submit" class="btn btn-primary mr-2">Submit</button>
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

@endsection