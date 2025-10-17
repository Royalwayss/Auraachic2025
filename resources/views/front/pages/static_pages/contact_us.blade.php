@extends('front.layout.layout')
@section('content')
<!-- breadcrumb start -->
<div class="breadcrumb">
    <div class="container">
        <ul class="list-unstyled d-flex align-items-center m-0">
            <li><a href="{{ route('home') }}">Home</a></li>
            <li><i class="fa-solid fa-angle-right"></i></li>
            <li>Contact Us</li>
        </ul>
    </div>
</div>
<!-- breadcrumb end -->

<main id="MainContent" class="content-for-layout">
    <div class="contact-page">

        <!-- contact box start -->
        <div class="contact-box mt-100">
            <div class="contact-box-wrapper">
                <div class="container">
                    <div class="row justify-content-center">
                        <!-- Contact Information Section -->
                        <div class="col-md-5 col-12">
                            <h2 class="section-heading">Contact Us</h2>
                            <p>For More Details</p>
                            <div class="row">
                                <!-- Mail Address -->
                                <div class="col-12">
                                    <div class="contact-item">
                                        <div class="contact-details">
                                            <h2 class="contact-title">Mail Address</h2>
                                            <a class="contact-info" href="mailto:auraachicin@gmail.com">auraachicin@gmail.com</a>
                                        </div>
                                    </div>
                                </div>

                                <!-- Office Location -->
                                <div class="col-12">
                                    <div class="contact-item">
                                        <div class="contact-details">
                                            <h2 class="contact-title">Office Location</h2>
                                            <p class="contact-info">Ludhiana, Punjab, India</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Phone Number -->
                                <div class="col-12">
                                    <div class="contact-item">
                                        <div class="contact-details">
                                            <h2 class="contact-title">Phone Number</h2>
                                            <a class="contact-info" href="tel:+91 83329 83000">+91 83329 83000</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Form Section -->
                        <div class="col-md-7 col-12 contact-form-section">
                            <div class="section-spacing" style="background: url('front/assets/img/about/banner.jpg') no-repeat fixed bottom center/cover">
                                <div class="contact-form-area">
                                    <div class="section-header mb-4 text-center">
                                        <h2 class="section-heading">Drop us a line</h2>
                                        <p class="section-subheading">We would like to hear from you.</p>
                                    </div>
                                    <div class="contact-form--wrapper">
                                        <form action="javascript:;" data-action="{{ route('savecontact')  }}" id="contact-form" class="contact-form">
                                            <div class="row">
                                                <div class="col-md-6 col-12">
                                                    <fieldset>
                                                        <input type="text" placeholder="Full name" name="name" id="contact-name" />
                                                    </fieldset>
													@php echo from_input_error_message('name') @endphp 
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <fieldset>
                                                        <input type="email" placeholder="Email Address*" name="email" id="contact-email" />
                                                    </fieldset>
													@php echo from_input_error_message('email') @endphp 
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <fieldset>
                                                        <input type="text" placeholder="Type a subject" name="subject" id="contact-subject" />
                                                    </fieldset>
													@php echo from_input_error_message('subject') @endphp 
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <fieldset>
                                                        <input type="text" placeholder="Mobile Number" name="mobile"  id="contact-mobile" />
                                                    </fieldset>
													@php echo from_input_error_message('mobile') @endphp 
                                                </div>
                                                <div class="col-md-12 col-12">
                                                    <fieldset>
                                                        <textarea cols="20" rows="6" placeholder="Write your message here*" name="message" id="contact-message"></textarea>
                                                    </fieldset>
													@php echo from_input_error_message('message') @endphp 
                                                    <button type="submit" class="position-relative review-submit-btn contact-submit-btn">SEND MESSAGE</button>
                                                </div>
                                            </div>                                    
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Contact Form Section -->

                    </div>
                </div>
            </div>
        </div>
        <!-- contact box end -->

    </div>
</main>
<script src="{{ asset('front/assets/js/ajax_jquery.min.js') }}"></script>
<script>
   $(document).ready(function ($)  {
     
    $(document).on('click', '.contact-submit-btn', function(event) {
     	    $('.loadingDiv').show();
           
   	       $.ajax({
                 headers: {
                     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                 },
                 url: $("#contact-form").attr('data-action'),
                 type: 'POST',
                 data: $("#contact-form").serialize(),
                 success: function(data) {
					$('.loadingDiv').hide();
					$('.error_message').empty();
					if (!data.status) { 
						var err_no = 0;
						$.each(data.errors, function (i, error) {
							err_no = err_no + 1;
							$('#input-error-' + i).css({'color': 'red','display': 'block'});
							$('#input-error-' + i).html(error);
							if (err_no == 1) {
								$("#contact-" + i).focus();
							}
							setTimeout(function () {
								$('#input-error-' + i).hide();
							}, 5000);
						});
					
					   
				   
                } else { 
				    $("#contact-form")[0].reset();
					sweetAlertMessage(data.message); 
				}
            },
     			error: function() {
                     $('.loadingDiv').hide();
     				   alert("Error");
                 }
             });
         
     	
     	});
       
   
   });	
   
   /*
   Swal.fire({
   		  title: "Are you sure you want to delete this Wishlist Item?",
   		  showDenyButton: true,
   		  denyButtonText: 'No',
   		  confirmButtonText: "Yes"
   		}).then((result) => {
   		 if (result.isConfirmed) {
			 }else if (result.isDenied) { } 
			 */
</script>
@endsection