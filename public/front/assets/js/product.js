	document.addEventListener("DOMContentLoaded", applyLazyLoading);
	$(document).ready(function () {
		var $myTextarea = $('#product_review_message');
		var $charCountDisplay = $('#charCountDisplay');
		var maxLength = $myTextarea.attr('maxlength'); 
		$charCountDisplay.text('Body of Review : (' + $myTextarea.val().length + ' / ' + maxLength + ')');
		$myTextarea.on('input', function () {
			var currentLength = $(this).val().length;
			$charCountDisplay.text('Body of Review : (' + currentLength + ' / ' + maxLength + ')');
			if (currentLength >= maxLength) {
				$charCountDisplay.css('color', 'red');
			} else {
				$charCountDisplay.css('color', 'black'); 
			}
		});
	});
	$(document).ready(function ($) {
		$(document).on('click', '.checkpincode', function (event) {
		$('.loadingDiv').show();
		var pincode = $("#txtZipCode").val();
		$.ajax({
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			url: $("#checkpincode_route").html(),
			type: 'POST',
			data: {
				pincode: pincode
			},
			success: function (data) {
				$('.loadingDiv').hide();
				if (!data.status) {
					if (data.type == "validation") {
						notifyMessage(data.errors, 'danger');
					}
				} else {
					$("#txtZipCode").val('');
					notifyMessage(data.message, 'success');
				}
			},
			error: function () {
				$('.loadingDiv').hide();
				alert("Error");
			}
		});
	});
	$(document).on('click', '#review-submit', function (event) {
		$('.loadingDiv').show();
		$.ajax({
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		url: $("#review-form").attr('data-action'),
		type: 'POST',
		data: $("#review-form").serialize(),
		success: function (data) {
			$('.loadingDiv').hide();
			$('.error_message').empty();
			if (!data.status) {
				$.each(data.errors, function (i, error) {
					$('#input-error-' + i).css({ 'color': 'red', 'display': 'block' });
					$('#input-error-' + i).html(error);
					setTimeout(function () { $('#input-error-' + i).hide(); }, 5000);
				});
			} else {
				$("#review-form").trigger("reset");
				sweetAlertMessage(data.message);
			}
		},
		error: function () {
			alert('Error');
			$('.loadingDiv').hide();
		}
		});
	});
	$(document).on('click', '#custom-fit-submit', function (event) {
		$('.loadingDiv').show();
		$.ajax({
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			url: $("#custom-fit-form").attr('data-action'),
			type: 'POST',
			data: $("#custom-fit-form").serialize(),
			success: function (data) {
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
						setTimeout(function () {$('#input-error-' + i).hide();}, 5000);
					});
				} else {
					$("#custom-fit-form").trigger("reset");
					sweetAlertMessage(data.message);
				}
			},
			error: function () {
				alert('Error');
				$('.loadingDiv').hide();
			}
		});
	});
		var queryStringObject = {};
		if ($('.filtertrue').length > 0) {
			RefreshFilters("no");
			popTriggerList();
		}
	$(document).on('click', ".filterAjax", function () {
		var name = $(this).attr('name');
		var val = $(this).val();
		var $tt = $('#AppenderLinks');
		if (name === "price" && $(document).find('.inlineFilterLink[id*="RemoveFilter-"][data-name="price"]').length > 0) {
			var targetToRemove = $('.inlineFilterLink[id*="RemoveFilter-"][data-name="price"]');
			targetToRemove.trigger('click');
		}
		$('.filterAjax').each(function () {
			var v = $(this).val();
			if (v === val && $(this).is(':checked')) {
				$tt.prepend('<a data-target="' + val + '" id="RemoveFilter-' + val + '" href="javascript:void(0);" class="inlineFilterLink" data-name="' + name + '">' + val + '<span class="ion-close"></span></a>');
			} else if (v === val && !$(this).is(':checked')) {
				$('.inlineFilterLink[data-target="' + val + '"][id*="RemoveFilter-"]').remove();
				RefreshFilters("yes");
			}
		});
		if (name === "category" && $(this).is(':checked')) {
			var v = $(this).val();
			$('.inlineFilterLink[data-name="category"]').each(function (key) {
				var $this = $(this);
				var id = "Category-" + v;
				$('#RemoveFilter-' + v).html($('#' + id).siblings('span.ccLabel').text() + '<span class="ion-close"></span></a>');
			});
		}
		if (name === "brand" && $(this).is(':checked')) {
			var v = $(this).val();
			$('.inlineFilterLink[data-name="brand"]').each(function (key) {
				var $this = $(this);
				var id = "brand-" + v;
				$('#RemoveFilter-' + v).html($('#' + id).siblings('span.ccLabel').text() + '<span class="ion-close"></span></a>');
			});
		}
		if ($('#AppenderLinks .inlineFilterLink').length > 0 && $('#AppenderLinks .absClear').length <= 0) {
			$tt.append('<a href="javascript:void(0);" class="absClear">Clear All</a>');
		}
		if ($('#AppenderLinks .inlineFilterLink').length <= 0) {
			$('#AppenderLinks').html(' ');
		}
		queryStringObject[name] = [];
			$.each($("input[name='" + $(this).attr('name') + "']:checked"), function () {
			queryStringObject[name].push($(this).val());
		});
		if (queryStringObject[name].length == 0) {
			delete queryStringObject[name];
		}
		RefreshFilters("yes");
	});
	$(document).on('click', 'a[id*="RemoveFilter-"]', function (e) {
		e.preventDefault();
		var tar = $(this).attr('data-target');
		if ($('input[value="' + tar + '"]:checked')) {
			$('input[value="' + tar + '"]').removeAttr('checked');
			$(this).remove();
			if ($('#AppenderLinks .inlineFilterLink').length <= 0) {
				$('#AppenderLinks').html(' ');
			}
			RefreshFilters("yes");
		}
	});
	$(document).on('click', '.absClear', function (e) {
		$('.filterAjax').each(function () {
			$(this).prop('checked', false);
		});
		$('.getsort ').val('');
		$('#appendProductListing').empty();
		RefreshFilters("clear-all");
	});
	$(document).on('click', '#ApplyPrice', function () {
		var price_from = $("#price_from").val();
		var price_to = $("#price_to").val();
		if (price_from != '' && price_to != '') {
			$("#ApplyPrice").addClass('price-filter-active');
			$("#filter_price").val(price_from + '-' + price_to)
			$("#filter_price").prop('checked', true);
			var value = $("#filter_price").val();
			var name = $("#filter_price").attr('name');
			queryStringObject[name] = [value];
			if (value == "") {
				delete queryStringObject[name];
			}
			RefreshFilters("yes");
		}
	});
	$(document).on('change', '.getsort', function () {
		var value = $(this).val();
		var name = $(this).attr('name');
		queryStringObject[name] = [value];
		if (value == "") {
			delete queryStringObject[name];
		}
		RefreshFilters("yes");
	});
	$(document).on('click', '#pricesort', function () {
		var minprice = parseInt($('#from_range').val());
		var maxprice = parseInt($('#to_range').val());

		queryStringObject["price"] = [minprice + "-" + maxprice];
		if (minprice == "" && maxprice == "") {
			delete queryStringObject["price"];
		}
		$("#priceRange").val(minprice + "-" + maxprice);
		debounce(function () {
			$("input[name='price']").val($("#priceRange").val()).click();
		}, 100)();
		RefreshFilters("yes");
	});

	$(function () {
		$(".pm-range-slider").slider({
			range: true,
			min: 0,
			max: 10000,
			values: [100, 2000],
			slide: function (event, ui) {
				$("#amount").val("â‚¹ " + ui.values[0] + " - â‚¹ " + ui.values[1]);
				$('#minprice').val(ui.values[0]);
				$('#maxprice').val(ui.values[1]);
				RefreshFilters("yes");
			}
		});
		$("#amount").val("â‚¹ " + $(".pm-range-slider").slider("values", 0) +" - â‚¹ " + $(".pm-range-slider").slider("values", 1));
	});
	$(document).on('click', '.pagination a', function (event) {
		event.preventDefault();
		if ($('.filtertrue').length > 0) {
			$(".filterAjax").each(function () {
				var name = $(this).attr('name');
				queryStringObject[name] = [];
				$.each($("input[name='" + $(this).attr('name') + "']:checked"), function () {
					queryStringObject[name].push($(this).val());
				});
				if (queryStringObject[name].length == 0) {
					delete queryStringObject[name];
				}
			});
			var value = $('.getsort option:selected').val();
			var name = $('.getsort').attr('name');
			queryStringObject[name] = [value];
			if (value == "") {
				delete queryStringObject[name];
			}
		}
		var page = $(this).attr('href').split('page=')[1];
		var query = {};
		queryStringObject['page'] = page;
		filterproducts(queryStringObject);
	});

	$(document).on("click", ".copyBtn", function () {
		var promoCode = $('.coupon-key'+$(this).attr('data-key')).text(); 
		navigator.clipboard.writeText(promoCode).then(() => {
			var copyMsg = $(this).closest(".couponCard").find(".copyMsg");
			copyMsg.fadeIn();
			setTimeout(() => {
				copyMsg.fadeOut();
			}, 2000);
			$(this)
			.html('<i class="fa fa-check"></i>') 
			.addClass("btn-success") 
			.prop("disabled", true); 
		}).catch(err => {
			console.error("Error copying text: ", err);
			alert("Failed to copy the coupon code. Please try again.");
		});
		$("#couponCode").val(promoCode);
		$("#CouponBtn").trigger('click');
	});
	$(document).on('click', '#CouponBtn', function (e) {
		e.preventDefault();
		$('.loadingDiv').show();
		var coupon = $("#couponCode").val();
		$.ajax({
		headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
		type: 'post',
		data: {
			coupon: coupon
		},
		url: '/apply-coupon',
		success: function (resp) {
			$('.loadingDiv').hide();
			if (!resp.status) {
				$('#couponInput').val('');
				var clasName = "danger";
			} else {
				var clasName = "success";
			}
			notifyMessage(resp.message, clasName);
			if (resp.type == "login") {
				window.location.href = $("#signin_route").html();
				return false;
			}
			$("#appendCartItems").html(resp.view);
		},
		error: function () {
			alert('Error');
			$('.loadingDiv').hide();
		}
		});
	});
	$('#PlaceOrder').click(function () {
		$(".loadingDiv").show();
		var formError = '';
		var error_message = {};
		
		if (!$("input:checkbox[name='agree']").is(":checked")) {
			 error_message[1] = 'Please accept Terms & Conditions.';
			 formError = true;
		}
		
		if (!$("input:radio[name='paymentMode']").is(":checked")) {
			error_message[2] = 'Please select Payment Method.';
			formError = true;
		} 
		
		if (!$("input:radio[name='default_shipping_address']").is(":checked")) {
            error_message[3] = 'Please add your Delivery Address!.';
			formError = true;			
		}
		
		if (!$("input:radio[name='billing_address']").is(":checked")) {
            error_message[4] =  'Please add your Billing Address!.';
			formError = true;			
		}
		
		
		$(".loadingDiv").hide();
		
        if(formError != ''){ 
			var explodedArray = Object.values(error_message).join(',');
		    notifyMessage(explodedArray, 'danger');
			return false;
		}else{ 
		    return true;
		}
       		
		
	});
});
function filterproducts(queryStringObject) {
	$('.loadingDiv').show();
	$('body').css({
	'overflow': 'hidden'
	});
	let searchParams = new URLSearchParams(window.location.search);
	if (searchParams.has('q')) {
		let parameterQuery = searchParams.get('q');
		var queryString = "?q=" + parameterQuery;
	} else {
		var queryString = "";
	}
	for (var key in queryStringObject) {
		if (queryString == '') {
			queryString += "?" + key + "=";
		} else {
			queryString += "&" + key + "=";
		}
		var queryValue = "";
		for (var i in queryStringObject[key]) {
			if (queryValue == '') {
				queryValue += queryStringObject[key][i];
			} else {
				queryValue += "~" + queryStringObject[key][i];
			}
		}
		queryString += queryValue;
	}
	if (history.pushState) {
		var newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + queryString;
		newurl = newurl.replace("&undefined=undefined", "");
		newurl = newurl.replace("?undefined=undefined", "");
		window.history.pushState({
			path: newurl
		}, '', newurl);
	}
	if (newurl.indexOf("?") >= 0) {
		newurl = newurl + "&json=";
	} else {
		newurl = newurl + "?json=";
	}
	$.ajax({
		url: newurl,
		type: 'get',
		dataType: 'json',
		success: function (resp) {
			$('.loadingDiv').hide();
			$('body').attr('style', ' ');
			$("#appendProductListing").html(resp.view);
			$('body').attr('style', ' ');
			if (resp.total_products > 1) {
				var no_of_products = '(' + resp.total_products + ' items )';
			} else {
				var no_of_products = '(' + resp.total_products + ' item )';
			}
			$("#no_of_products").html(no_of_products);
			return false;
			$("#productCount").html(resp.countproducts);
			$('html, body').stop().animate({
				scrollTop: 150
				}, 0, 'easeInOutQuad', function () {
			});
			setTimeout(applyLazyLoading, 300);
		},
		error: function () {
			$('.loadingDiv').hide();
			$('body').attr('style', ' ');
		}
	});
}
function RefreshFilters(type) {
	var queryStringObject = {};
	if (type != "clear-all") {
		$(".filterAjax").each(function () {
			var name = $(this).attr("name");
			queryStringObject[name] = [];
			$.each($("input[name='" + name + "']:checked"), function () {
				queryStringObject[name].push($(this).val());
			});
			if (queryStringObject[name].length == 0) {
				delete queryStringObject[name];
			}
		});
		var value = $(".getsort option:selected").val();
		var name = $(".getsort").attr("name");
		queryStringObject[name] = [value];
		if (value == "") {
			delete queryStringObject[name];
		}
		if (type === "yes") {
			filterproducts(queryStringObject, function () {
			setTimeout(applyLazyLoading, 300); 
		});
		}
	} else {
		filterproducts(queryStringObject, function () {
		setTimeout(applyLazyLoading, 100); 
		});
	}
}

function applyLazyLoading() {
	let products = document.querySelectorAll(".lazy-load");
	let batchSize = 8;
	let currentIndex = batchSize;
	if (products.length === 0) return; 
		products.forEach((product, index) => {
		if (index >= batchSize) {
			product.classList.add("hidden");
		} else {
			product.classList.remove("hidden");
		}
	});
	function loadMoreProducts() {
		for (let i = currentIndex; i < currentIndex + batchSize; i++) {
			if (products[i]) {
				products[i].classList.remove("hidden");
			}
		}
		currentIndex += batchSize;
		if (currentIndex >= products.length) {
			window.removeEventListener("scroll", onScroll);
		}
	}
	function onScroll() {
		let scrollPosition = window.scrollY + window.innerHeight;
		let documentHeight = document.documentElement.scrollHeight;
		if (scrollPosition >= documentHeight - 100) {
			loadMoreProducts();
		}
	}
	window.removeEventListener("scroll", onScroll);
	window.addEventListener("scroll", onScroll);
}
function popTriggerList() {
	if ($('input.filterAjax:checked').length > 0) {
		$('input.filterAjax:checked').each(function (key) {
			var $tt = $('#AppenderLinks');
			var value = $(this).val();
			var name = $(this).attr('name');
			var id = $(this).attr('id');
			$tt.prepend('<a data-target="' + value + '" id="RemoveFilter-' + value + '" href="javascript:void(0);" class="inlineFilterLink" data-name="' + name + '">' + value + '<span class="ion-close"></span></a>');
		});
		debounce(function () {
			$('input.filterAjax[name="category"]:checked').each(function (key) {
				var v = $(this).val();
				var id = "Category-" + v;
				$('#RemoveFilter-' + v).html($('#' + id).siblings('span.ccLabel').text() + '<span class="ion-close"></span></a>');
			});
			$('input.filterAjax[name="brand"]:checked').each(function (key) {
				var v = $(this).val();
				var id = "brand-" + v;
				$('#RemoveFilter-' + v).html($('#' + id).siblings('span.ccLabel').text() + '<span class="ion-close"></span></a>');
			});
		}, 100)();
	}
}