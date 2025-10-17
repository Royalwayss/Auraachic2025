<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-dark">
  <!-- Left navbar links -->
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
    </li>
    <li class="nav-item d-none d-sm-inline-block">
      <a href="#" class="nav-link">Welcome <strong><?php /* {{ Auth::guard('admin')->user()->name }} ({{ Auth::guard('admin')->user()->type }}) */ ?></strong></a>
    </li>
    <li class="nav-item d-none d-sm-inline-block">
      <a href="{{ url('admin/dashboard')}}" class="nav-link">Dashboard</a>
    </li>
    <li class="nav-item d-none d-sm-inline-block">
      <a href="{{ url('admin/logout')}}" class="nav-link">Logout</a>
    </li>
  </ul>

  <!-- Right navbar links -->
  <ul class="navbar-nav ml-auto">
    
    <li class="nav-item">
      <a class="nav-link" data-widget="fullscreen" href="#" role="button">
        <i class="fas fa-expand-arrows-alt"></i>
      </a>
    </li>
    
  </ul>
</nav>
<!-- /.navbar -->



<style>
  /* Sidebar Link Base Styles */
.nav-sidebar .nav-link {
  transition: all 0.3s ease;
  position: relative;
  padding-left: 15px; /* Adjust for new bullet design */
}

/* Hover Effect with Gradient */
.nav-sidebar .nav-link:hover {
  background: linear-gradient(90deg, #ff7e5f, #feb47b); /* Gradient hover background */
  color: #fff !important; /* White text */
  border-radius: 5px;
  padding-left: 20px; /* Indentation on hover for dynamic look */
  box-shadow: 0px 4px 8px rgba(255, 126, 95, 0.4); /* Subtle glow */
}

/* Custom Bullet/Icon on Hover */
.nav-sidebar .nav-link:hover::before {
  /*content: '\2022';*/ /* Custom bullet (circle) */
  content: '';
  color: #fff; /* White bullet */
  font-size: 20px; /* Larger size for prominence */
  position: absolute;
  left: 5px; /* Position near the text */
  top: 50%;
  transform: translateY(-50%);
}

/* Active Tab Style */
.nav-sidebar .nav-link.active {
  background: linear-gradient(90deg, #00c6ff, #0072ff); /* Blue gradient for active tab */
  color: #fff !important; /* White text for active */
  border-radius: 5px;
  box-shadow: 0px 4px 8px rgba(0, 198, 255, 0.4); /* Glow effect */
  padding-left: 20px;
}

/* Custom Bullet/Icon for Active Tab */
.nav-sidebar .nav-link.active::before {
  /*content: '\2714';*/ /* Checkmark icon for active tab */
  content: ''; /* Remove the checkmark icon */
  color: #fff; /* White bullet/icon */
  font-size: 20px;
  position: absolute;
  left: 5px;
  top: 50%;
  transform: translateY(-50%);
}

/* Animated Underline Effect on Hover */
.nav-sidebar .nav-link::after {
  content: '';
  position: absolute;
  bottom: 3px;
  left: 15px;
  right: 15px;
  height: 2px;
  background: transparent;
  transition: all 0.3s ease;
}

/* Show Underline on Hover */
.nav-sidebar .nav-link:hover::after {
  /*background: #fff;*/ /* White underline */
}

/* Submenu Items Hover Effect */
.nav-sidebar .nav-treeview .nav-item .nav-link:hover {
  background-color: #ff9e80; /* Soft orange for submenu hover */
  color: #fff !important;
  border-radius: 5px;
}

/* Submenu Active Style */
.nav-sidebar .nav-treeview .nav-item .nav-link.active {
  background-color: #ff7043; /* Deeper orange for active submenu */
  color: #fff !important;
  border-radius: 5px;
}
</style>
<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <!-- Brand Logo -->
  <a href="{{url('admin/dashboard')}}" class="brand-link" style="background-color:#fff; border:0px !important;">
    <img src="{{ asset('admin/images/logo.png') }}" alt="AdminLTE Logo" class="brand-image" style="width:120px; height:auto; max-height:100% !important; border:0px !important;">
    <!-- <span class="brand-text font-weight-light">Admin Panel</span> -->
  </a>

  <!-- Sidebar -->
  <div class="sidebar">
    <!-- Sidebar user panel (optional) -->
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
      <div class="image">
       
          <img src="{{ asset('front/assets/img/logo.png') }}" class="img-circle elevation-2" alt="logo"> 
        
      </div>
      <div class="info">
        <a href="#" class="d-block">{{ Auth::guard('admin')->user()->name }}</a>
      </div>
    </div>

    <!-- Sidebar Menu -->
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <!-- Add icons to the links using the .nav-icon class
             with font-awesome or any other icon font library -->
        @if(Session::get('page')=="dashboard")
            @php $active="active" @endphp
        @else
            @php $active = "" @endphp
        @endif
        <li class="nav-item">
          <a href="{{ url('admin/dashboard') }}" class="nav-link {{ $active }}">
            <i class="nav-icon fas fa-th"></i>
            <p>
              Dashboard
            </p>
          </a>
        </li>
         @if(Session::get('page')=="categories" || Session::get('page')=="products" || Session::get('page')=="brands")
            @php $active="active" @endphp
          @else
              @php $active = "" @endphp
          @endif
        <li class="nav-item">
            <a href="#" class="nav-link {{$active}}">
              <i class="nav-icon fas fa-th"></i>
              <p style="font-size:15.5px !important">
                Catalogue Management
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">

              @if(Session::get('page')=="categories")
                  @php $active="active" @endphp
              @else
                  @php $active = "" @endphp
              @endif
              <li class="nav-item">
                <a href="{{ url('admin/categories')}}" class="nav-link {{$active}}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Categories</p>
                </a>
              </li>
              <?php /*
              @if(Session::get('page')=="brands")
                  @php $active="active" @endphp
              @else
                  @php $active = "" @endphp
              @endif
              <li class="nav-item">
                <a href="{{ url('admin/brands')}}" class="nav-link {{$active}}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Brands</p>
                </a>
              </li>  */ ?>

              @if(Session::get('page')=="products")
                  @php $active="active" @endphp
              @else
                  @php $active = "" @endphp
              @endif
              <li class="nav-item">
                <a href="{{ url('admin/products')}}" class="nav-link {{$active}}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Products</p>
                </a>
              </li>

              @if(Session::get('page')=="ratings")
                  @php $active="active" @endphp
              @else
                  @php $active = "" @endphp
              @endif
              <li class="nav-item">
                <a href="{{ url('admin/ratings')}}" class="nav-link {{$active}}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Reviews</p>
                </a>
              </li>

            </ul>
          </li>
		  
		  
		    @if(Session::get('page')=="orders" || Session::get('page')=="orders" || Session::get('page')=="custom-fit")
            @php $active="active" @endphp
          @else
              @php $active = "" @endphp
          @endif
        <li class="nav-item">
            <a href="#" class="nav-link {{$active}}">
              <i class="nav-icon fas fa-tshirt"></i>
              <p style="font-size:15.5px !important">
                Orders Management
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              @if(Session::get('page')=="orders")
                  @php $active="active" @endphp
              @else
                  @php $active = "" @endphp
              @endif
              <li class="nav-item">
                <a href="{{ url('admin/orders')}}" class="nav-link {{$active}}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Orders</p>
                </a>
              </li>

              @if(Session::get('page')=="return_requests")
                  @php $active="active" @endphp
              @else
                  @php $active = "" @endphp
              @endif
              <li class="nav-item">
                <a href="{{ url('admin/return-requests')}}" class="nav-link {{$active}}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Return Requests</p>
                </a>
              </li>

              @if(Session::get('page')=="exchange_requests")
                  @php $active="active" @endphp
              @else
                  @php $active = "" @endphp
              @endif
              <li class="nav-item">
                <a href="{{ url('admin/exchange-requests')}}" class="nav-link {{$active}}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Exchange Requests</p>
                </a>
              </li>
			  
			  
			   
			   @if(Session::get('page')=="custom-fit")
                  @php $active="active" @endphp
              @else
                  @php $active = "" @endphp
              @endif
              <li class="nav-item">
                <a href="{{ url('admin/custom-fit')}}" class="nav-link {{$active}}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Custom Fit</p>
                </a>
              </li>
			  
			  
			  
			  
			  
			  
			  
			  
            </ul>
        </li>

       
            @if(Session::get('page')=="users" || Session::get('page')=="subscribers")
            @php $active="active" @endphp
          @else
              @php $active = "" @endphp
          @endif
        <li class="nav-item">
            <a href="#" class="nav-link {{$active}}">
              <i class="nav-icon fas fa-users"></i>
              <p style="font-size:15.5px !important">
                Users Management
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              @if(Session::get('page')=="users")
                  @php $active="active" @endphp
              @else
                  @php $active = "" @endphp
              @endif
              <li class="nav-item">
                <a href="{{ url('admin/users')}}" class="nav-link {{$active}}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Users</p>
                </a>
              </li>

              @if(Session::get('page')=="subscribers")
                  @php $active="active" @endphp
              @else
                  @php $active = "" @endphp
              @endif
              <li class="nav-item">
                <a href="{{ url('admin/subscribers')}}" class="nav-link {{$active}}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Subscribers</p>
                </a>
              </li>
             
            </ul>
        </li> 

          @if(Session::get('page')=="coupons" || Session::get('page')=="coupons")
            @php $active="active" @endphp
          @else
              @php $active = "" @endphp
          @endif
        <li class="nav-item">
            <a href="#" class="nav-link {{$active}}">
              <i class="nav-icon fas fa-rupee-sign"></i>
              <p style="font-size:15.5px !important">
                Coupons Management
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              @if(Session::get('page')=="coupons")
                  @php $active="active" @endphp
              @else
                  @php $active = "" @endphp
              @endif
              <li class="nav-item">
                <a href="{{ url('admin/coupons')}}" class="nav-link {{$active}}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Coupons</p>
                </a>
              </li>

            </ul>
        </li>  

        @if(Session::get('page')=="banners" || Session::get('page')=="banners")
            @php $active="active" @endphp
          @else
              @php $active = "" @endphp
          @endif
       

        @if(Auth::guard('admin')->user()->type=="admin")

          @if(Session::get('page')=="update-password" || Session::get('page')=="update-details" || Session::get('page')=="subadmins")
            @php $active="active" @endphp
          @else
              @php $active = "" @endphp
          @endif
          <li class="nav-item">
            <a href="#" class="nav-link {{$active}}">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Admin Management
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              @if(Session::get('page')=="update-password")
                  @php $active="active" @endphp
              @else
                  @php $active = "" @endphp
              @endif
              <li class="nav-item">
                <a href="{{ url('admin/update-password')}}" class="nav-link {{$active}}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Update Admin Password</p>
                </a>
              </li>
              @if(Session::get('page')=="update-details")
                  @php $active="active" @endphp
              @else
                  @php $active = "" @endphp
              @endif
              <li class="nav-item">
                <a href="{{ url('admin/update-details')}}" class="nav-link {{$active}}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Update Admin Details</p>
                </a>
              </li>
              </li>
              @if(Session::get('page')=="subadmins")
                  @php $active="active" @endphp
              @else
                  @php $active = "" @endphp
              @endif
              <li class="nav-item">
                <a href="{{ url('admin/subadmins')}}" class="nav-link {{ $active }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
                    Subadmins
                  </p>
                </a>
              </li>
             
            </ul>

        @endif
       
	    @if(Session::get('page')=="pages" || Session::get('page')=="cms-pages")
            @php $active="active" @endphp
          @else
              @php $active = "" @endphp
          @endif
        <li class="nav-item">
            <a href="#" class="nav-link {{$active}}">
              <i class="nav-icon fas fa-copy"></i>
              <p style="font-size:15.5px !important">
                Content Management
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              @if(Session::get('page')=="cms-pages")
                  @php $active="active" @endphp
              @else
                  @php $active = "" @endphp
              @endif
              <li class="nav-item">
                <a href="{{ url('admin/banners')}}" class="nav-link {{$active}}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Banners</p>
                </a>
              </li>
			  <li class="nav-item">
                <a href="{{ url('admin/cms-pages')}}" class="nav-link {{$active}}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>CMS Pages</p>
                </a>
              </li>
             
            </ul>
        </li>  

       
      
      </ul>
    </nav>
    <!-- /.sidebar-menu -->
  </div>
  <!-- /.sidebar -->
</aside>





