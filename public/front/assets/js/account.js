$(document).ready(function() {
	$(document).on('click', '.update-profile', function(event) { 
		$('.loadingDiv').show();
		$.ajax({
			headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			url: $("#profile-form").attr('data-action'),
			type: 'POST',
			data: $("#profile-form").serialize(),
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
						$("#profile-" + i).focus();
					}
					setTimeout(function () {
						$('#input-error-' + i).hide();
					}, 5000);
					});
				} else { 
					notifyMessage(data.message,'success'); 
				}
			},
			error: function () {
				alert('Error');
				$('.loadingDiv').hide();
			}
		});
	});
	$(document).on('click', '.update-profile', function(event) { 
		$('.loadingDiv').show();
		$.ajax({
			headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			url: $("#profile-form").attr('data-action'),
			type: 'POST',
			data: $("#profile-form").serialize(),
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
						$("#profile-" + i).focus();
					}
					setTimeout(function () {
						$('#input-error-' + i).hide();
					}, 5000);
					});
				} else { 
					notifyMessage(data.message,'success'); 
				}
			},
			error: function () {
				alert('Error');
				$('.loadingDiv').hide();
			}
			});
		});
	$(document).on('click', '.action-addtocart', function(event) {
		$('.loadingDiv').show();
		var product_id = $(this).attr('data-product_id'); 
		var size = $(this).attr('data-size'); 
		var wishlist_id = $(this).attr('data-wishlist_id'); 
		var qty = 1;
		$.ajax({
			headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			url: $('#addtocart_route').html(),
			type: 'POST',
			data: {product_id:product_id,size:size,qty:qty,wishlist_id:wishlist_id},
			success: function(data) {
				$('.loadingDiv').hide();
				if (!data.status) {
					if (data.type == "validation") {
						notifyMessage(data.errors, 'danger'); 
					}
				} else {
					$('.totalItems').html(data.totalitems);
					$('#drawer-cart').empty();
					$('#drawer-cart').append(data.cart_popup);	
					$("#wishlist_list").html(data.wishlist.html); 
					$(".totalWishlistItems").html(data.wishlist.count); 
					notifyMessage(data.message, 'success');
				}
			},
			error: function() {
				$('.loadingDiv').hide();
				alert("Error");
			}
		});
	});
	$(document).on('click', '.wishlist-delete', function(event) {
		Swal.fire({
			title: "Are you sure you want to delete this Wishlist Item?",
			showDenyButton: true,
			denyButtonText: 'No',
			confirmButtonText: "Yes"
		}).then((result) => {
			if (result.isConfirmed) {	
				$('.loadingDiv').show(); 
				var id = $(this).attr('data-id'); 
				$.ajax({
					headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
					url: $('#removewishlist_route').html(),
					type: 'POST',
					data: {id:id},
					success: function(data) {
						$('.loadingDiv').hide();
							if (!data.status) {
							notifyMessage(data.errors, 'danger'); 
						} else {
							$("#wishlist_list").html(data.html); 
							$(".totalWishlistItems").html(data.count); 
							notifyMessage(data.message, 'success');
						}
					},
					error: function() {
						$('.loadingDiv').hide();
						alert("Error");
					}
				});
			}else if (result.isDenied) {
			}
		});
	});
	$(document).on('change', '.pincode', function(){
		$('.loadingDiv').show();
		var pincode = $(this).val();  
		$.ajax({
		headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		url: $("#getstatecity_route").html(),
		type: 'POST',
		data: {pincode:pincode},
		success: function(data) {
			$('.loadingDiv').hide();
			$('.error_message').empty();
			if (data.status) { 
				$(".state_list").val(data.state);
				$(".city").val(data.city);
			}
		},
		error: function () {
			alert('Error');
			$('.loadingDiv').hide();
		}
		});
	});
	$(document).on('click', '.orderAddress', function(event) { 
		$('.loadingDiv').show();
		var type=  $(this).attr('data-type');
		var id=  $(this).attr('data-id'); 			
		$.ajax({
			headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			url: $("#orderaddressform_route").html(),
			type: 'POST',
			data: {id:id,type:type},
			success: function(data) {
				$('.loadingDiv').hide();
				if (data.status) { 
					$('#Modal_Popup_Button').attr("data-bs-target","#OrderAddressModal");
					$('#OrderAddressModalContent').html(data.html);
					$('#Modal_Popup_Button').trigger("click");
				} 
			},
			error: function () {
				alert('Error');
				$('.loadingDiv').hide();
			}
		});
	});
	$(document).on('click', '#order-address-btn', function(event) {
		$('.loadingDiv').show();
		var formdata = $("#order_address_form").serialize(); 
		$.ajax({
			headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			url: $("#saveorderaddress_route").html(),
			type: 'POST',
			data: formdata,
			dataType: "JSON",
			success: function(data) {
				$('.loadingDiv').hide();
				$('.error_message').empty();
				if (!data.status) {
					var err_no = 0;
					if (data.type == "validation") {
						$.each(data.errors, function (i, error) { 
						err_no = err_no + 1; 
						$('#input-error-'+i).attr('style', 'color:red'); 
						$('#input-error-'+i).html(error);
						if(err_no == 1) { 
							$("#addr-"+i).filter(':visible').focus();
						} 
						}); 
					} 
				} else {
					$("#order_address").html(data.html);
					$('#Modal_Popup_Button').trigger("click");
					notifyMessage(data.message, 'success');
				}
			},
			error: function () {
				alert('Error');
				$('.loadingDiv').hide();
			}
		});
	});
	$(document).on('change', '[name=default_shipping_address]', function(){
		$('.loadingDiv').show();
		var addressid = $(this).val(); 
		$.ajax({
			headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			type:'post',
			url:$('#setdefaultaddress_route').html(),
			data:{addressid:addressid},
			success:function(resp){
				$('.loadingDiv').hide();
				$("#order_address").html(resp.html);
				notifyMessage(resp.messages, 'success');
			},
			error:function(){
				alert('error');
				$('.loadingDiv').hide();
			}
		});
	});
	$(document).on('click','.deleteaddress',function(){
		var id = $(this).attr('data-id'); 
		Swal.fire({
			title: "Are you sure you want to delete this shipping address?",
			showDenyButton: true,
			denyButtonText: 'No',
			confirmButtonText: "Yes"
		}).then((result) => {
			if (result.isConfirmed) {
				$('.loadingDiv').show();
				$.ajax({
					headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
					type:'post',
					url:$('#deleteaddress_route').html(),
					data:{id:id},
					success:function(resp){
						$('.loadingDiv').hide();
						$("#order_address").html(resp.html);
						notifyMessage(resp.messages, 'success');
					},
					error:function(){
						alert('error');
						$('.loadingDiv').hide();
					}
				});
			} else if (result.isDenied) {
			}
		});
	});
	$('#login_type').on('change', function() { 
	   var login_type = $('#login_type').val(); 
	   if(login_type == 'password'){
		   $('.login-otp-btn').hide();
		   $('.login-password').show();
		   $('.otp-field').hide();
		   $('#login-email').attr('style','width:100%');
		   $('#login-action').val('');
	   }else{
		   $('.login-otp-btn').show();
		   $('.login-password').hide();
		   $('#login-email').attr('style','width:75%');
	   }
   }); 
   $('#login-password').change(function(){
	$('#login-action').val('');		
   }); 
   $(".login-btn").click(function (e) {
	   $('.loadingDiv').show();
	   e.preventDefault();
	   var action = $(this).attr('data-action'); 
	   if(action != ''){
		$('#login-action').val(action);
	   }
	   var actionUrl = $("#login-form").attr('data-action'); 
	   var formdata = $("#login-form").serialize();
	   $.ajax({
		   url: actionUrl,
		   type: 'POST',
		   data: formdata,
		   success: function (data) {
		   $(".loadingDiv").hide();
		   if (!data.status) {
			   if (data.type == 'validation') {
				   var err_no = 0;
				   $.each(data.errors, function (i, error) {
					   err_no = err_no + 1;
					   $('#msg-login-' + i).css({ 'color': 'red','display': 'block'});
					   $('#msg-login-' + i).html(error);
					   if (err_no == 1) {
							$("#login-" + i).focus();
					   }
					   setTimeout(function () {$('#msg-login-' + i).css({'display': 'none'});}, 5000); 
				   });
			   } else {
				   $('#alert-danger').html(data.errors);
				   setTimeout(function () {$('.alert-danger').css({'display': 'none' });}, 5000);
			   }
		   } else {
			   if(data.action =='done'){
				   $("#login-form")[0].reset();
				   window.location.href = data.url;
			   }else{
				   if(action == 'sent_otp'){
					   $('#sendOtpBtn').prop('disabled', true);
					   $('#msg-login-email').css({'color': '#008000','display': 'block' });
					   $('#msg-login-email').html(data.message);
					   $('.otp-field').show();
					   $('#login-action').val('verify_otp');
					   let timeLeft = 30;
					   let countdown = setInterval(() => {
						   $('#countdown').text(`Please wait ${timeLeft} seconds...`);
						   timeLeft--;
						   if (timeLeft < 0) {
							   clearInterval(countdown);
							   $('#sendOtpBtn').prop('disabled', false); 
							   $('#sendOtpBtn').text('Resend OTP'); 
							   $('#msg-login-email').html('');
							   $('#countdown').text(''); 
						   }
					   }, 1000);
				   }
			   }
		   }
		   }
	   });
   }); 
   $(".forgot-password-btn").click(function (e) {
	   $('.loadingDiv').show();
	   e.preventDefault();
	   var actionUrl = $("#forgot-password-form").attr('data-action'); 
	   var formdata = $("#forgot-password-form").serialize();
	   $.ajax({
		   url: actionUrl,
		   type: 'POST',
		   data: formdata,
		   success: function (data) {
			   $(".loadingDiv").hide();
			   if (!data.status) {
				   if (data.type == 'validation') {
					   var err_no = 0;
					   $.each(data.errors, function (i, error) {
						   err_no = err_no + 1;
						   $('#msg-forgotpassword-' + i).css({'color': 'red', 'display': 'block' });
						   $('#msg-forgotpassword-' + i).html(error);
						   if (err_no == 1) {
								$("#msg-forgotpassword-email").focus();
						   }
						   setTimeout(function () {$('#msg-forgotpassword-email').css({'display': 'none' });}, 5000);
					   });
				   }else {
					   $('#alert-danger').html(data.errors);
					   setTimeout(function () { $('.alert-danger').css({'display': 'none'});}, 5000);
				   }
			   } else {
				   $('#forgot-password-email').val('');
				   $('#forgotpassword-alert-message').css({'color': 'green'});
				   $('#forgotpassword-alert-message').html(data.message);
				   setTimeout(function () {
					   $('#forgotpassword-alert-message').css({'display': 'none'});
					   $("#ForgotModal").modal('hide');
					}, 5000);		   
			   }
		   }
		   });
	   });
	   $(".btn-signin").click(function (e) {
			$('.loadingDiv').show();
			e.preventDefault();
			var action = $(this).attr('data-action');
			$("#signup-action").val(action);
			var actionUrl = $("#signup-form").attr('data-action'); 
			var formdata = $("#signup-form").serialize();
			$.ajax({
				url: actionUrl,
				type: 'POST',
				data: formdata,
				success: function (data) {
				$(".loadingDiv").hide();
				if (!data.status) {
					if (data.type == 'validation') {
						var err_no = 0;
						$.each(data.errors, function (i, error) {
							err_no = err_no + 1;
							$('#msg-signup-' + i).css({'color': 'red','display': 'block'});
							$('#msg-signup-' + i).html(error);
							if (err_no == 1) {
									$("#signup-" + i).focus();
							}
							setTimeout(function () {$('#msg-signup-' + i).css({'display': 'none'});}, 5000);					
						});
					} else {
							$('#alert-danger').html(data.errors);
							setTimeout(function () {$('.alert-danger').css({'display': 'none'});}, 5000);
					}
				} else {
					if(data.action =='done'){
						/*let timerInterval;
						Swal.fire({
							icon: "success",
							html: data.message,
							timer: 5000,  
							timerProgressBar: true,
							didOpen: () => {
								Swal.showLoading();
								const timer = Swal.getPopup().querySelector("b");
								timerInterval = setInterval(() => {
								const timeLeft = Swal.getTimerLeft();
								timer.textContent = `${Math.ceil(timeLeft / 1000)} seconds`;
								}, 1000);  
							},
							willClose: () => {
								clearInterval(timerInterval);
							}
						}).then((result) => {
							if (result.dismiss === Swal.DismissReason.timer) {						
								window.location.href = data.redirectTo;
							}
						}); */
						window.location.href = data.redirectTo;
					}else{
						if(action == 'send_otp'){
							$('#sendOtpBtn').prop('disabled', true);
							$('#msg-signup-email').css({'color': '#008000','display': 'block'});
							$('#msg-signup-email').html(data.message);
							$('#otp-fields').show();
							$('#signup-action').val('verify_otp');
							let timeLeft = 30;
							let countdown = setInterval(() => {
								$('#countdown').text(`Please wait ${timeLeft} seconds...`);
								timeLeft--;
								if (timeLeft < 0) {
									clearInterval(countdown);
									$('#sendOtpBtn').prop('disabled', false); 
									$('#sendOtpBtn').text('Resend OTP'); 
									$('#msg-signup-email').html('');
									$('#countdown').text(''); 
								}
							}, 1000);
						}
					}
				}
				}
				});
		});
	
});