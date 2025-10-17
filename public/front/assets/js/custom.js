$(document).ready(function() {
	$(document).on('click', '.notifyCross', function() {  
		$(this).closest("div.notifymsg" ).hide();
	});
	$(document).on('click','.changeqty',function(){   
	if($(".cartpage").length){
		var cartpage = 1;
	}else{
		var cartpage = 0;
	}
	$('.loadingDiv').show();
	var qtyInput = $(this).parent().find('input');
	if ($(this).hasClass('inc-qty')) {
		qtyInput.val(parseInt(qtyInput.val()) + 1);
	} else if (qtyInput.val() >= 1) {
		qtyInput.val(parseInt(qtyInput.val()) - 1);
	}
	var cartid = $(this).attr("data-cartid");
	var productid = $(this).attr("data-proid");
	var size = $(this).attr("data-size");
	var qtyInput = $(this).parent().find('input'); 
	var qty = qtyInput.val();
	$.ajax({
		type:'post',
		headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
		data:{cartid:cartid,size:size,productid:productid,qty:qty,cartpage:cartpage},
		url: $("#updatecartitem_route").html(),
		success:function(resp){
			$('.loadingDiv').hide();
			$(".totalItems").html(resp.totalCartItems);
			$('#appendCartItems').empty();
			$('#appendCartItems').append(resp.view); 
			$('#drawer-cart').empty();
			$('#drawer-cart').append(resp.cart_popup); 
			if(resp.status == true){
				var clasName = 'success';
			}else{
				var clasName = 'danger';
			}
			notifyMessage(resp.message,clasName);  
		},  
		error:function(){
			$('.loadingDiv').hide();
			alert("Error");
		}
		});
	}); 
	$(document).on('click', '.product_size', function(event) {
		$('.loadingDiv').show();
		var type = $(this).attr("data-type");  
		var key = $(this).attr("data-key"); 
		$("#"+type+"-size-"+key).prop("checked", true);
		var size = $(this).attr("data-size"); 
		$("#"+type+"_size").val(size);
		var product_id = $(this).attr("data-product_id"); 
		$.ajax({
			type: 'post',
			headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
			data: { product_id: product_id, size: size },
			url: $("#getproductprice_route").html(),
			success: function(resp) { 
			$('.loadingDiv').hide();
			if (resp.status) { 
				$("#"+type+"_price_details").html(resp.product_price_details);
				if (resp.wishlist_check === 1) {
					$("."+type+"-wishlist-btn").html('<i class="fa-solid fa-heart"></i>');
				} else if (resp.wishlist_check === 0) {
					$("."+type+"-wishlist-btn").html('<i class="fa-regular fa-heart"></i>');
				}
			}
			},
			error: function() {
				$('.loadingDiv').hide();
				alert("Error");
			}
		});
	});
	$(document).on('click', '.add-to-cart', function(event) {
	$('.loadingDiv').show();
	var cart_type = $(this).attr('data-cart-type'); 
	var btn_type = $(this).attr('data-type');
	if(cart_type == 'page'){
		var formdata = $("#AddtoCart").serialize();
	}else{
		var formdata = $("#QucikViewAddtoCart").serialize();
	}
	var actionType = $(document.activeElement).attr('id');
	$.ajax({
		headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		url: $('#addtocart_route').html(),
		type: 'POST',
		data: formdata,
		success: function(data) {
			$('.loadingDiv').hide();
			if (!data.status) {
				if (data.type == "validation") {
					notifyMessage(data.errors, 'danger'); 
				}
			} else {
				if(btn_type != 'buy'){
					$('.totalItems').html(data.totalitems);
					$('#drawer-cart').empty();
					$('#drawer-cart').append(data.cart_popup);	
					notifyMessage(data.message, 'success');
				}else{
					window.location.href=$("#checkout_route").html();
				}
			}
		},
		error: function() {
			$('.loadingDiv').hide();
			alert("Error");
		}
	 });
	});
	$(document).on('click','.deleteCartItem',function(){
		Swal.fire({
			title: "Are you sure you want to delete this Cart Item?",
			showDenyButton: true,
			denyButtonText: 'No',
			confirmButtonText: "Yes"
		}).then((result) => {
			if (result.isConfirmed) {
				$('.loadingDiv').show();
				var cartid = $(this).attr('data-cartid');
				$.ajax({
					headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
					data : {cartid:cartid},
					url : $("#deletecartitem_route").html(), 
					type : 'post',
					success:function(resp){
						$('.loadingDiv').hide(); 
						$('.totalItems').html(resp.totalCartItems); 
						$('#appendCartItems').empty();
						$('#appendCartItems').append(resp.view);
						$('#drawer-cart').empty();
						$('#drawer-cart').append(resp.cart_popup);					
						if(resp.status == true){
							var clasName = 'success';
						}else{
							var clasName = 'danger';
						}
						notifyMessage(resp.message,clasName);    
					},  
					error:function(){
						$('.loadingDiv').hide();
						alert("Error");
					}
				})
			} else if (result.isDenied) {
			}
	    });
	});
	$(document).on('click', '.product-wishlist', function () {
		$('.loadingDiv').show();
		var type = $(this).attr('data-cart-type'); 
		var proid = $(this).attr('data-product-id'); 
		if(type == 'page'){
			var qty = $('input[name="qty"]').val(); 
			var size = $("#product_size").val(); 
		}else{
			var qty = $("#pro_qty").val(); 
			var size = $("#pro_size").val(); 
		}
		var _this = this;
		$.ajax({
		headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		data: { proid: proid,size:size,qty:qty},
		type: 'post',
		url: $("#addtowishlist_route").html(),
		success: function (resp) {
			$('.loadingDiv').hide();
			$('.totalWishlistItems').html(resp.totalWishlistItems);
			if (resp.status) {
				if (resp.mode === 'set') {
					$(_this).html('<i class="fa-solid fa-heart"></i>');
				} else if (resp.mode === 'unset') {
					$(_this).html('<i class="fa-regular fa-heart"></i>');
				}
				notifyMessage(resp.message, 'success');
			} else {
				if (resp.login) {
				  notifyMessage(resp.message, 'danger'); 
				}else{
				  window.location.href=resp.url;
				}
			}
		},
		error: function () {
			$('.loadingDiv').hide();
		}
		});
	});
    $(document).on('click', '.newsletter-submit-btn', function(event) {
		$('.loadingDiv').show();
		$.ajax({
			headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
			url: $('#newsletter-form').attr('data-action'),
			type: 'POST',
			data: { email:$('#newsletter-email').val()},
			success: function(resp) {
				$('.loadingDiv').hide();
				if (!resp.status) {    
					sweetAlertMessage(resp.message, 'danger'); 
				} else {
					$('#newsletter-email').val('');
					sweetAlertMessage(resp.message, 'success');
				}
			},
			error: function() {
				$('.loadingDiv').hide();
				alert("Error");
			}
		});
    });
	$(document).on('click', '.product-quick-view', function (e) {   
	$('.loadingDiv').show(); 
	var id = $(this).attr('data-id'); 
	var is_modal_open = $(this).attr('data-is_modal_open');
	$.ajax({
		headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
		url: $("#productquickview_route").html(),
		type: 'POST',
		data: {id:id},
		success: function(data) {
			$('.loadingDiv').hide();
			if (data.status) { 					
				if(is_modal_open == '1'){ 
					$('#Modal_Popup_Button').trigger("click");
				}
				$('#Modal_Popup_Button').attr("data-bs-target","#quickview-modal");
				$('#ProductQuickViewModalContent').empty();
				$('#ProductQuickViewModalContent').append(data.html);
				if(is_modal_open == '1'){ 
					setTimeout(function () { $('#Modal_Popup_Button').trigger("click");}, 200);
				}else{
					$('#Modal_Popup_Button').trigger("click");
				}
			} 
		},
		error: function () {
			alert('Error');
			$('.loadingDiv').hide();
		}
		});
	});
});
	function printErrorMsg(msg) {
		"use strict";
		$(".print-error-msg").stop(true, true).css({
			display: "block", 
			opacity: 1       
		});
		$(".print-error-msg").find("ul").html('');
		$.each(msg, function(key, value) {
			$(".print-error-msg").find("ul").append('<li>' + value + '</li>');
		});
	}
	function printSuccessMsg(msg) {
		"use strict";
		$(".print-success-msg").stop(true, true).css({
			display: "block", 
			opacity: 1      
		});
		$(".print-success-msg").find("ul").html('<li>' + msg + '</li>');
		$('.print-success-msg').delay(3000).fadeOut('slow');
	}
	function makeid(length) {
		var result           = '';
		var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
		var charactersLength = characters.length;
		for ( var i = 0; i < length; i++ ) {
			result += characters.charAt(Math.floor(Math.random() * charactersLength));
		}
		return result;
	}
	function notifyMessage(response, alertType, timeOutSeconds='5000') { 
		if(!$(".offcanvas-end1").hasClass("show")){
			var rendomStr = makeid(5);
			var messageArray = response.toString().split(",");
			var notifyStr = '<div class="notifymsg alert alert-'+alertType+'" role="alert" id="'+rendomStr+'"> <button type="button" class="position-absolute close notifyCross"><span aria-hidden="true">&times;</span></button>';
			   for(var i = 0; i < messageArray.length; i++) {
				notifyStr += ' <div class="col-sm-12 p-0"><i class="fas fa-asterisk fa-xs"></i> '+messageArray[i]+'</div>';
			   }
				notifyStr += '</div>';
			jQuery(".notify").append(notifyStr).show();
			setTimeout(function(){
				jQuery("#"+rendomStr).remove();
			}, timeOutSeconds);
		}else{
				var message_string = response.toString(); 
				if(alertType =='success'){
					sweetAlertMessage(message_string);
				}else{
					sweetAlertMessage(message_string,'danger');
				}
		}
	}
	function sweetAlertMessage(message,className='success'){ 
		if(className == 'success'){
			Swal.fire({
				icon: "success",
				text: message,
				showDenyButton: false,
				confirmButtonText: "Ok"
			});
		}else{
			swal.fire({
				icon: "error",
				text: message,
				type: "error",
				confirmButtonText: "Ok"
			}); 
		}
	}
	
	function isDivClassNotVisible(className) {
		const divs = document.getElementsByClassName(className);

		if (divs.length === 0) {
			// No elements with this class found, so they are not "visible" in the sense of existing.
			return true; 
		}

		for (let i = 0; i < divs.length; i++) {
			const div = divs[i];
			const computedStyle = window.getComputedStyle(div);

			// Check for display: none, visibility: hidden, or opacity: 0
			// Note: Elements with visibility: hidden or opacity: 0 still occupy space in the layout.
			if (computedStyle.display !== 'none' && computedStyle.visibility !== 'hidden' && 
				parseFloat(computedStyle.opacity) > 0) {
				// At least one element with the class is visible
				return false; 
			}
		}

		// All elements with the class are either display: none, visibility: hidden, or opacity: 0
		return true;
}

		
	