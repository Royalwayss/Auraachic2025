$(document).ready(function(){

		// Select All Input Data
		$('.select-all').click(function () {
        let $select2 = $(this).siblings('.select2')
        $select2.find('option').prop('selected', 'selected')
        $select2.trigger('change')
    });

		// Clear All Input Data
    $('.deselect-all').click(function () {
        let $select2 = $(this).siblings('.select2')
        $select2.find('option').prop('selected', '')
        $select2.trigger('change')
    });

	// Check for Partial Payment Refund
	$(document).on("click","#refundAmount",function(){
		if(confirm('Are you sure to refund this Payment?')){
			return true;
		}
		return false;
	})


	// Check Admin Password is correct or not
	$("#current_pwd").keyup(function(){
		var current_pwd = $("#current_pwd").val();
		//alert(current_pwd);
		$.ajax({
			headers: {
		        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		    },
			type:'post',
			url:'/admin/current-password/verify',
			data:{current_pwd:current_pwd},
			success:function(resp){
				if(resp=="false"){
					$("#verifyCurrentPwd").html("<font color='red'>Current Password is incorrect</font>");
				}else if(resp=="true"){
					$("#verifyCurrentPwd").html("<font color='green'>Current Password is correct</font>");
				}
			},error:function(){
				alert("Error");
			}
		});
	});

	// Update CMS Page Status
	$(document).on("click",".updateCmsPageStatus",function(){
		var status = $(this).children("i").attr("status");
		var page_id = $(this).attr("page_id");
		$.ajax({
			headers: {
		        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		    },
		    type:'post',
		    url:'/admin/update-cms-page-status',
		    data:{status:status,page_id:page_id},
		    success:function(resp){
		    	if(resp['status']==0){
		    		$("#page-"+page_id).html("<i class='fas fa-toggle-off' style='color:grey' status='Inactive'></i>");
		    	}else if(resp['status']==1){
		    		$("#page-"+page_id).html("<i class='fas fa-toggle-on' style='color:#3f6ed3' status='Active'></i>");
		    	}
		    	
		    },error:function(){
		    	alert("Error");
		    }	
		})
	})

	// Update Category Status
	$(document).on("click",".updateCategoryStatus",function(){
		var status = $(this).children("i").attr("status");
		var category_id = $(this).attr("category_id");
		$.ajax({
			headers: {
		        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		    },
		    type:'post',
		    url:'/admin/update-category-status',
		    data:{status:status,category_id:category_id},
		    success:function(resp){
		    	if(resp['status']==0){
		    		$("#category-"+category_id).html("<i class='fas fa-toggle-off' style='color:grey' status='Inactive'></i>");
		    	}else if(resp['status']==1){
		    		$("#category-"+category_id).html("<i class='fas fa-toggle-on' style='color:#3f6ed3' status='Active'></i>");
		    	}
		    	
		    },error:function(){
		    	alert("Error");
		    }	
		})
	})

	// Update Brand Status
	$(document).on("click",".updateBrandStatus",function(){
		var status = $(this).children("i").attr("status");
		var brand_id = $(this).attr("brand_id");
		$.ajax({
			headers: {
		        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		    },
		    type:'post',
		    url:'/admin/update-brand-status',
		    data:{status:status,brand_id:brand_id},
		    success:function(resp){
		    	if(resp['status']==0){
		    		$("#brand-"+brand_id).html("<i class='fas fa-toggle-off' style='color:grey' status='Inactive'></i>");
		    	}else if(resp['status']==1){
		    		$("#brand-"+brand_id).html("<i class='fas fa-toggle-on' style='color:#3f6ed3' status='Active'></i>");
		    	}
		    	
		    },error:function(){
		    	alert("Error");
		    }	
		})
	})

	// Update User Status
	$(document).on("click",".updateUserStatus",function(){
		var status = $(this).children("i").attr("status");
		var user_id = $(this).attr("user_id");
		$.ajax({
			headers: {
		        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		    },
		    type:'post',
		    url:'/admin/update-user-status',
		    data:{status:status,user_id:user_id},
		    success:function(resp){
		    	if(resp['status']==0){
		    		$("#user-"+user_id).html("<i class='fas fa-toggle-off' style='color:grey' status='Inactive'></i>");
		    	}else if(resp['status']==1){
		    		$("#user-"+user_id).html("<i class='fas fa-toggle-on' style='color:#3f6ed3' status='Active'></i>");
		    	}
		    	
		    },error:function(){
		    	alert("Error");
		    }	
		})
	})

	// Update Subscriber Status
	$(document).on("click",".updateSubscriberStatus",function(){
		var status = $(this).children("i").attr("status");
		var subscriber_id = $(this).attr("subscriber_id");
		$.ajax({
			headers: {
		        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		    },
		    type:'post',
		    url:'/admin/update-subscriber-status',
		    data:{status:status,subscriber_id:subscriber_id},
		    success:function(resp){
		    	if(resp['status']==0){
		    		$("#subscriber-"+subscriber_id).html("<i class='fas fa-toggle-off' style='color:grey' status='Inactive'></i>");
		    	}else if(resp['status']==1){
		    		$("#subscriber-"+subscriber_id).html("<i class='fas fa-toggle-on' style='color:#3f6ed3' status='Active'></i>");
		    	}
		    	
		    },error:function(){
		    	alert("Error");
		    }	
		})
	})

	// Update Coupon Status
	$(document).on("click",".updateCouponStatus",function(){
		var status = $(this).children("i").attr("status");
		var coupon_id = $(this).attr("coupon_id");
		$.ajax({
			headers: {
		        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		    },
		    type:'post',
		    url:'/admin/update-coupon-status',
		    data:{status:status,coupon_id:coupon_id},
		    success:function(resp){
		    	if(resp['status']==0){
		    		$("#coupon-"+coupon_id).html("<i class='fas fa-toggle-off' style='color:grey' status='Inactive'></i>");
		    	}else if(resp['status']==1){
		    		$("#coupon-"+coupon_id).html("<i class='fas fa-toggle-on' style='color:#3f6ed3' status='Active'></i>");
		    	}
		    	
		    },error:function(){
		    	alert("Error");
		    }	
		})
	})

	// Update Banner Status
	$(document).on("click",".updateBannerStatus",function(){
		var status = $(this).children("i").attr("status");
		var banner_id = $(this).attr("banner_id");
		$.ajax({
			headers: {
		        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		    },
		    type:'post',
		    url:'/admin/update-banner-status',
		    data:{status:status,banner_id:banner_id},
		    success:function(resp){
		    	if(resp['status']==0){
		    		$("#banner-"+banner_id).html("<i class='fas fa-toggle-off' style='color:grey' status='Inactive'></i>");
		    	}else if(resp['status']==1){
		    		$("#banner-"+banner_id).html("<i class='fas fa-toggle-on' style='color:#3f6ed3' status='Active'></i>");
		    	}
		    	
		    },error:function(){
		    	alert("Error");
		    }	
		})
	})

	// Update Product Status
	$(document).on("click",".updateProductStatus",function(){
		var status = $(this).children("i").attr("status");
		var product_id = $(this).attr("product_id");
		$.ajax({
			headers: {
		        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		    },
		    type:'post',
		    url:'/admin/update-product-status',
		    data:{status:status,product_id:product_id},
		    success:function(resp){
		    	if(resp['status']==0){
		    		$("#product-"+product_id).html("<i class='fas fa-toggle-off' style='color:grey' status='Inactive'></i>");
		    	}else if(resp['status']==1){
		    		$("#product-"+product_id).html("<i class='fas fa-toggle-on' style='color:#3f6ed3' status='Active'></i>");
		    	}
		    	
		    },error:function(){
		    	alert("Error");
		    }	
		})
	})

	// Update Attribute Status
	$(document).on("click",".updateAttributeStatus",function(){
		var status = $(this).children("i").attr("status");
		var attribute_id = $(this).attr("attribute_id");
		$.ajax({
			headers: {
		        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		    },
		    type:'post',
		    url:'/admin/update-attribute-status',
		    data:{status:status,attribute_id:attribute_id},
		    success:function(resp){
		    	if(resp['status']==0){
		    		$("#attribute-"+attribute_id).html("<i class='fas fa-toggle-off' style='color:grey' status='Inactive'></i>");
		    	}else if(resp['status']==1){
		    		$("#attribute-"+attribute_id).html("<i class='fas fa-toggle-on' style='color:#3f6ed3' status='Active'></i>");
		    	}
		    	
		    },error:function(){
		    	alert("Error");
		    }	
		})
	})

	// Update Rating Status
	$(document).on("click",".updateRatingStatus",function(){
		var status = $(this).children("i").attr("status");
		var rating_id = $(this).attr("rating_id");
		$.ajax({
			headers: {
		        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		    },
		    type:'post',
		    url:'/admin/update-rating-status',
		    data:{status:status,rating_id:rating_id},
		    success:function(resp){
		    	if(resp['status']==0){
		    		$("#rating-"+rating_id).html("<i class='fas fa-toggle-off' style='color:grey' status='Inactive'></i>");
		    	}else if(resp['status']==1){
		    		$("#rating-"+rating_id).html("<i class='fas fa-toggle-on' style='color:#3f6ed3' status='Active'></i>");
		    	}
		    	
		    },error:function(){
		    	alert("Error");
		    }	
		})
	})

	// Update Subadmin Status
	$(document).on("click",".updateSubadminStatus",function(){
		var status = $(this).children("i").attr("status");
		var subadmin_id = $(this).attr("subadmin_id");
		$.ajax({
			headers: {
		        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		    },
		    type:'post',
		    url:'/admin/update-subadmin-status',
		    data:{status:status,subadmin_id:subadmin_id},
		    success:function(resp){
		    	if(resp['status']==0){
		    		$("#subadmin-"+subadmin_id).html("<i class='fas fa-toggle-off' style='color:grey' status='Inactive'></i>");
		    	}else if(resp['status']==1){
		    		$("#subadmin-"+subadmin_id).html("<i class='fas fa-toggle-on' style='color:#3f6ed3' status='Active'></i>");
		    	}
		    	
		    },error:function(){
		    	alert("Error");
		    }	
		})
	})

	// Update Rating Status
	$(document).on("click",".updateRatingStatus",function(){
		var status = $(this).children("i").attr("status");
		var rating_id = $(this).attr("rating_id");
		$.ajax({
			headers: {
		        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		    },
		    type:'post',
		    url:'/admin/update-rating-status',
		    data:{status:status,rating_id:rating_id},
		    success:function(resp){
		    	if(resp['status']==0){
		    		$("#rating-"+rating_id).html("<i class='fas fa-toggle-off' style='color:grey' status='Inactive'></i>");
		    	}else if(resp['status']==1){
		    		$("#rating-"+rating_id).html("<i class='fas fa-toggle-on' style='color:#3f6ed3' status='Active'></i>");
		    	}
		    	
		    },error:function(){
		    	alert("Error");
		    }	
		})
	})


	// Confirm the deletion of CMS Page
	/* $(document).on("click",".confirmDelete",function(){
		var name = $(this).attr('name');
		if(confirm('Are you sure to delete this '+name+'?')){
			return true;
		}
		return false;
	}); */

	// Confirm Deletion with SweetAlert
	$(document).on("click",".confirmDelete",function(){
		var record = $(this).attr('record');
		var recordid = $(this).attr('recordid');
		Swal.fire({
		  title: 'Are you sure?',
		  text: "You won't be able to revert this!",
		  icon: 'warning',
		  showCancelButton: true,
		  confirmButtonColor: '#3085d6',
		  cancelButtonColor: '#d33',
		  confirmButtonText: 'Yes, delete it!'
		}).then((result) => {
		  if (result.isConfirmed) {
		    Swal.fire(
		      'Deleted!',
		      'Your file has been deleted.',
		      'success'
		    )
		    window.location.href = "/admin/delete-"+record+"/"+recordid;
		  }
		})
	});

	// Confirm the deletion of Module with Resource Controller
	 $(document).on("click",".confirmDeleteModule",function(){
		var module = $(this).attr('module');
		if(confirm('Are you sure to delete this '+module+'?')){
			return true;
		}
		return false;
	}); 

	// Products Attributes Add/Remove Script
	var maxField = 10; //Input fields increment limitation
    var addButton = $('.add_button'); //Add button selector
    var wrapper = $('.field_wrapper'); //Input field wrapper
    var fieldHTML = '<div ><div style="height:10px;"></div><input type="text" class="form-control attr-input" name="sku[]" style="width:120px" placeholder="SKU" />&nbsp;<input type="text" class="form-control attr-input" name="size[]" style="width:120px" placeholder="Size" />&nbsp;<input type="text" class="form-control attr-input" name="price[]" style="width:120px" placeholder="Price" />&nbsp;<input type="text" class="form-control attr-input" name="stock[]" style="width:120px" placeholder="Stock" />&nbsp;<input type="number" class="form-control attr-input" name="sort[]" placeholder="Sort" style="width:120px;"><a href="javascript:void(0);" class="remove_button btn">Delete</a></div>'; //New input field html 
    var x = 1; //Initial field counter is 1
    
    //Once add button is clicked
    $(addButton).click(function(){
        //Check maximum number of input fields
        if(x < maxField){ 
            x++; //Increment field counter
            $(wrapper).append(fieldHTML); //Add field html
        }
    });
    
    //Once remove button is clicked
    $(wrapper).on('click', '.remove_button', function(e){
        e.preventDefault();
        $(this).parent('div').remove(); //Remove field html
        x--; //Decrement field counter
    });

  $("#couponForm").submit(function(event) {
      if ($("#ManualCoupon").is(":checked") && $("#coupon_code").val().trim() === "") {
          alert("Coupon Code is required when Manual option is selected.");
          event.preventDefault(); // Prevent form submission
      }
  });  

  // Show/Hide Coupon Field for Manual/Automatic
	$("#ManualCoupon").click(function(){
		$("#couponField").show();
	});

	$("#AutomaticCoupon").click(function(){
		$("#couponField").hide();
	});

	$('#order_status').change(function() {    
        var order_status = $(this).val();
        if(order_status=="Shipped"){
        	$("#delivery_method").show();
        	$("#awb_number").show();
        }else{
        	$("#delivery_method").hide();
        	$("#awb_number").hide();
        }
    });

	$("#e1").select2();
    $("#cat_checkbox").click(function(){
      if($("#cat_checkbox").is(':checked') ){
          $("#e1 > option").prop("selected","selected");
          $("#e1").trigger("change");
          $("#cat_checkbox").hide();
          $(".selectall").hide();
      }else{
          $("#e1 > option").removeAttr("selected");
           $("#e1").trigger("change");
           $("#e1").val("");
           $(this).val('');
           $('#e1').attr("value", "");
      }
    });

    $("#brand_checkbox").click(function(){
      if($("#brand_checkbox").is(':checked') ){
          $("#b1 > option").prop("selected","selected");
          $("#b1").trigger("change");
          $("#brand_checkbox").hide();
          $(".brandselectall").hide();
      }else{
          $("#b1 > option").removeAttr("selected");
           $("#b1").trigger("change");
           $("#b1").val("");
           $(this).val('');
           $('#b1').attr("value", "");
      }
    });

    $("#user_checkbox").click(function(){
      if($("#user_checkbox").is(':checked') ){
          $("#u1 > option").prop("selected","selected");
          $("#u1").trigger("change");
          $("#user_checkbox").hide();
          $(".userselectall").hide();
      }else{
          $("#u1 > option").removeAttr("selected");
           $("#u1").trigger("change");
           $("#u1").val("");
           $(this).val('');
           $('#u1').attr("value", "");
      }
    });




});