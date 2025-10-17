@extends('front.layout.layout')
@section('content')
<div class="breadcrumb">
    <div class="container">
        <ul class="list-unstyled d-flex align-items-center m-0">
            <li><a href="{{ route('home') }}">Home</a></li>
            <li><svg class="svg-inline--fa fa-angle-right" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="angle-right" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" data-fa-i2svg=""><path fill="currentColor" d="M278.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-160 160c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L210.7 256 73.4 118.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l160 160z"></path></svg><!-- <i class="fa-solid fa-angle-right"></i> Font Awesome fontawesome.com --></li>
            <li>Profile</li>
        </ul>
    </div>
</div>

<main id="MainContent" class="content-for-layout">
    <section class="collection listing">
        <div class="accWrap container">
            <div class="row">
                @include('front.pages.account.account_tabs')
                <div class="col-lg-9 col-md-12 address-detail-area">
                    <h3>Profile</h3>
                    <hr>
                    <div class="accPage">
                    <form action="javascript:;" data-action="{{ route('saveaccount') }}" id="profile-form">@csrf
					<div class="row">
                            
							<div class="col-lg-6 col-md-12 mb-3">
                                <label for="">First Name*</label><br>
                                <input type="text"  class="w-100" value="{{ $user->first_name }}" name="first_name" id="profile-first_name" placeholder="Enter Your First Name*">
                                @php echo from_input_error_message('first_name') @endphp
							</div>
                            <div class="col-lg-6 col-md-12 mb-3">
                                <label for="">Last Name*</label><br>
                                <input type="text"  class="w-100" value="{{ $user->last_name }}" name="last_name" id="profile-last_name" placeholder="Enter Your Last Name*">
								@php echo from_input_error_message('last_name') @endphp
                            </div>
                            <div class="col-lg-6 col-md-12 mb-3">
                                <label for="">Email</label><br>
                                <input class="w-100" type="text"  value="{{ $user->email }}" placeholder="Enter Your Email" disabled>
								@php echo from_input_error_message('email') @endphp
                            </div>
                            <div class="col-lg-6 col-md-12 mb-3">
                                <label for="">Mobile</label><br>
                                <input class="w-100" type="text"  value="{{ $user->mobile }}" placeholder="Enter Your Mobile"  disabled>
								@php echo from_input_error_message('mobile') @endphp
                            </div>
                            <div class="col-lg-6 col-md-12 mb-3">
                                <label for="">Address*</label><br>
                                <input type="text"  class="w-100" value="{{ $user->address }}" name="address" id="profile-address" placeholder="Enter Your Address*">
								@php echo from_input_error_message('address') @endphp
                            </div>
                            <div class="col-lg-6 col-md-12 mb-3">
                                <label for="">Address Line2</label><br>
                                <input class="w-100" type="text" value="{{ $user->address_line2 }}" placeholder="" name="address_line2" >
                            </div>
                            <div class="col-lg-4 col-md-12 mb-3">
                                <label for="">Pincode*</label><br>
                                <input type="text"  class="w-100 pincode" value="{{ $user->pincode }}"  name="pincode"  id="profile-pincode" placeholder="Enter Your Pin Code*">
								@php echo from_input_error_message('pincode') @endphp 
                            </div>
							<div class="col-lg-4 col-md-12 mb-3">
                                <label for="">State*</label><br>
                                <select class="state_list" name="state" id="profile-state" >
                                <option value="">Select State</option>
                                @foreach($states as $state) 
								<option value="{{ $state }}" @if($state == $user->state) selected @endif >{{ $state }}</option>                               
                                @endforeach
								</select>
								@php echo from_input_error_message('state') @endphp 
                            </div>
                            <div class="col-lg-4 col-md-12 mb-3">
                                <label for="">City*</label><br>
                                <input type="text"  class="w-100 city" value="{{ $user->city }}" name="city" id="profile-city"  placeholder="Enter Your City*">
                                @php echo from_input_error_message('city') @endphp 
							</div>
                            
                            
                            <div class="col-12">
                            <button type="submit" class="icButton button2 update-profile">Update</button>
                            </div>
							</form>
                        
                    </div>
					</div>
                </div>

            </div>
        </div>
        <!-- Edit Address Modal -->
    </section>
</main>
@endsection