<script type="text/javascript">
	$(document).ready(function(){
		$(document).on('change','[name=type]',function(e){
			var widgetType = $(this).val();
			$('.loadingDiv').show();
	        e.preventDefault();
	        $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
	            url: "{{url('/admin/widgets/append')}}",
	            type:'POST',
	            data: {widgetType:widgetType},
	            success: function(resp) {
	                $('.loadingDiv').hide();
	                $('#AppendWidget').html(resp.view);
	                select2();
	            }
	        });
		});

        $(document).on('change','[name=parent_id]',function(){
            var parent = $(this).val();
            var SectionType = '';
            if(parent ==='new'){
                var SectionType = `<div class="form-group col-md-6">
                            <label>Section Type <span class="asteric">*</span></label>
                            <select class="form-control" name="section_type" required>
                                <option value="slider">Slider</option>
                                <option value="each_row">Each Row (1 Banner)</option>
                                <option value="each_row_3banners">Each Row (3 Banners)</option>
                            </select>
                        </div>`;
            }
            $('#SectionType').html(SectionType);
        })

        @if(isset($widget['child_widgets']) && !empty($widget['child_widgets']))
            let tabIndex = "<?php echo count($widget['child_widgets']) ?>"; // Start with the next index after initial tabs
        @else
            let tabIndex = 1; // Start with the next index after initial tabs
        @endif


        // Add more tabs on button click
        $(document).on('click', '#AddMoreTabs',function () {

            tabIndex++; // Increment the tab index
            if(tabIndex >= 4){
            	alert("You can not add more then 3 tabs");
            	$('#AddMoreTabs').remove();
            	return false;
            }else{
            	// Create a new tab section
    	        let newTab = `
    	            <div class="tab-section" data-index="${tabIndex}">
    	                <div class="form-group col-md-6">
    	                    <label>Tab ${tabIndex} Title <span class="asteric">*</span></label>
    	                    <input type="text" name="tab_title[${tabIndex}]" class="form-control" placeholder="Enter Title">
    	                </div>
    	                <div class="form-group col-md-6">
    	                    <label>Tab ${tabIndex} Products <span class="asteric">*</span></label>
	                        <select name="tab_products[${tabIndex}][]" class="select2" multiple required>
	                            @foreach (products() as $key => $product) 
	                                <option value="{{$product['id']}}">{{$product['product_name']}} ({{$product['product_code']}})</option>
	                            @endforeach
	                        </select>
    	                </div>
    	                <hr>
    	            </div>
    	        `;

    	        // Append the new tab section to the container
    	        $('#AppendTabProducts').append(newTab);

    	        // Reinitialize Select2 for the new select element
    	        $(`select[name="products[${tabIndex}][]"]`).select2();
    	        select2();
            }

        });

        $(document).on('change','[name=link_to]',function(){
            var linkTo = $(this).val();
            widgetLinking(linkTo);
        });

        @if(isset($widget['link_to']) && !empty($widget['link_to']))
            widgetLinking(`{{$widget['link_to']}}`);
        @endif
    });
</script>
<script>

    function widgetLinking(linkTo){
        if(linkTo ==="Product"){
                var redirectHtml = `<div class="form-group col-md-6">
                    <label>Select Product <span class="asteric">*</span></label>
                    <select name="product_id" class="select2">
                        <option></option>
                        @foreach ($products as $key => $product) 
                            <option value="{{$product['id']}}" @if(isset($widget['product_id']) && !empty($widget['product_id']) && $widget['product_id'] == $product['id']) selected @endif>{{$product['product_name']}} ({{$product['product_code']}})</option>
                        @endforeach
                    </select>
                </div>`;
            }else if(linkTo ==="Brand"){
                var redirectHtml = `<div class="form-group col-md-6">
                        <label>Select Brand <span class="asteric">*</span></label>
                        <select name="brand_id" class="select2">
                            <option></option>
                            @foreach ($brands as $key => $brand) 
                                <option value="{{$brand['id']}}" @if(isset($widget['brand_id']) && !empty($widget['brand_id']) && $widget['brand_id'] == $brand['id']) selected @endif >{{$brand['brand_name']}}</option>
                            @endforeach
                        </select>
                    </div>`;
            }else if(linkTo ==="Category"){
                var redirectHtml = `<div class="form-group col-md-6">
                    <label>Select Category <span class="asteric">*</span></label>
                    <select name="category_id" class="select2">
                        <option></option> 
                        @foreach ($allCategories as $key => $category)
                        <option value="{{$category['id']}}" @if(isset($widget['category_id']) && !empty($widget['category_id']) && $widget['category_id'] == $category['id']) selected @endif>&#9679;&nbsp;{{$category['category_name']}}</option>
                            @foreach ($category['subcategories'] as $key => $subcat)
                                <option value="{{$subcat['id']}}" @if(isset($widget['category_id']) && !empty($widget['category_id']) && $widget['category_id'] == $subcat['id']) selected @endif>&nbsp;&nbsp;&nbsp;&nbsp;&raquo; &nbsp;{{$subcat['category_name']}}</option>
                                @foreach ($subcat['subcategories'] as $key => $subsubcat)
                                    <option value="{{$subsubcat['id']}}" @if(isset($widget['category_id']) && !empty($widget['category_id']) && $widget['category_id'] == $subsubcat['id']) selected @endif>&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&raquo; &raquo; &nbsp;{{$subsubcat['category_name']}}</option>
                                @endforeach
                            @endforeach
                        @endforeach
                    </select>
                </div>`;
            } else if(linkTo ==="Search Page"){
                var redirectHtml =`<div class="form-group col-md-6">
                                    <label>Search Term <span class="asteric">*</span></label>
                                    <input  type="text" placeholder="Enter Search Term" name="search_term" style="color:gray" class="form-control" value="{{(!empty($widget['search_term']))?$widget['search_term']: '' }}"/>
                                </div>`;
            }else{
                var redirectHtml = ``;
            }
            $('#AppendRedirections').html(redirectHtml);
            select2();
    }

    function ajaxFormRequest(url, requestType, formdata) {
        $.ajax({
            url: url,
            type: requestType,
            data: formdata,
            processData: false,
            contentType: false,
            beforeSend: function () {
               $('.loadingDiv').show();
            },
            success: function (resp) {
                if (resp.status) {
                    window.location.href = resp.data.url || '/';
                } else {
                    alert(resp.message);
                }
            },
            error: function (error) {
                ajaxError(error);
                $('.loadingDiv').hide();
            },
            complete: function () {
                $('.loadingDiv').hide();
            }
        });
    }

    function ajaxError(error) {
        // Remove all existing error messages before appending new ones
        $('.error-message').remove();

        // Iterate over the errors from the server response
        $.each(error.responseJSON.errors, function (key, value) {
            console.log("Error Key:", key); // Debugging line
            console.log("Error Value:", value); // Debugging line

            let name;
            if (key.includes('.')) {
                // Split the key to handle indexed fields like 'title.1'
                let parts = key.split('.');
                let baseName = parts[0]; // e.g., "title"
                let index = parts[1];   // e.g., "1"

                // Use the array syntax to target the specific input field
                name = $("input[name='" + baseName + "[" + index + "]']");
            } else if (key === 'desktop_images' || key === 'mobile_images') {
                // Handle file inputs
                name = $("input[name='" + key + "[]']");
            } else {
                // Handle other input fields or select fields
                name = $("input[name='" + key + "'], select[name='" + key + "'], select[name='" + key + "[]'], textarea[name='" + key + "']");
            }

            // If a matching field is found, append the error message
            if (name && name.length) {
                var errorHtml = '<div class="error-message text-danger pt-3" data-key="' + key + '">' + value[0] + '</div>';
                name.after(errorHtml);

                // Remove the error message when the user interacts with the field
                name.on('input change', function () {
                    // Only remove the error message related to the interacted field
                    $(this).next('.error-message').fadeOut(500).remove();
                });
            }
        });

        // Scroll to the first error message
        if ($('.error-message').length) {
            $('html, body').animate({
                scrollTop: $('.error-message').first().offset().top - 200
            }, 1000);
        }
    }
</script>