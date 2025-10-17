<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CmsController;
use App\Http\Controllers\Admin\RatingController;
use App\Http\Controllers\Admin\CreditController;
use App\Http\Controllers\Admin\WidgetController;
use App\Http\Controllers\Admin\EnquiryController;
use App\Http\Controllers\Admin\ReturnController;
use App\Http\Controllers\Admin\ExchangeController;
use App\Http\Controllers\Admin\CustomfitController;
use App\Http\Controllers\Front\IndexController;
use App\Http\Controllers\Front\ProductController as ProductFrontController;
use App\Http\Controllers\Front\UserController as UserFrontController;
use App\Http\Controllers\Front\AddressController;
use App\Http\Controllers\Front\CmsController as CmsFrontController;
use App\Http\Controllers\Front\PaymentController;
use App\Http\Controllers\Front\CancelController;
use App\Http\Controllers\Front\RazorpayController;
use App\Http\Controllers\Front\ReturnController as ReturnFrontController;
use App\Http\Controllers\Front\ExchangeController as ExchangeFrontController;
use App\Models\Brand;
use App\Models\Category;
use App\Models\CmsPage;

/*Route::get('/', function () {
    return view('welcome');
});*/

Route::prefix('/admin')->namespace('App\Http\Controllers\Admin')->group(function(){
    Route::get('login',[AdminController::class,'login']);
    Route::match(['get','post'],'login/request',[AdminController::class,'loginRequest']);
    Route::group(['middleware'=>['admin']],function(){
        Route::match(['get','post'],'dashboard',[AdminController::class,'dashboard']);
        Route::get('update-password',[AdminController::class,'updatePassword']);
        Route::post('update-password/request',[AdminController::class,'updatePasswordRequest']);
        Route::post('current-password/verify',[AdminController::class,'currentPasswordVerify']);
        Route::get('update-details',[AdminController::class,'updateDetails']);
        Route::post('update-details/request',[AdminController::class,'updateDetailsRequest']);
        Route::get('logout',[AdminController::class,'logout']);

        // CMS Pages
        Route::get('cms-pages',[CmsController::class,'index']);
        Route::post('update-cms-page-status',[CmsController::class,'update']);
        Route::match(['get','post'],'add-edit-cms-page/{id?}',[CmsController::class,'edit']);
        Route::get('delete-cms-page/{id}','CmsController@destroy');

        // Sub-Admins
        Route::get('subadmins',[AdminController::class,'subadmins']);
        Route::post('update-subadmin-status',[AdminController::class,'updateSubadminStatus']);
        Route::get('delete-subadmin/{id}',[AdminController::class,'deleteSubadmin']);
        Route::get('add-edit-subadmin/{id?}',[AdminController::class,'addEditSubadmin']);
        Route::post('add-edit-subadmin/request',[AdminController::class,'addEditSubadminRequest']);
        Route::get('/update-role/{id}',[AdminController::class,'updateRole']);
        Route::post('/update-role/request',[AdminController::class,'updateRoleRequest']);
        Route::match(['get','post'],'website-settings',[AdminController::class,'websiteSettings']);
        Route::match(['get','post'],'save-website-settings',[AdminController::class,'saveWebsiteSettings']);

        // Categories
        Route::get('categories',[CategoryController::class,'categories']);
        Route::post('update-category-status',[CategoryController::class,'updateCategoryStatus']);
        Route::get('delete-category/{id}',[CategoryController::class,'deleteCategory']);
        Route::get('add-edit-category/{id?}',[CategoryController::class,'addEditCategory']);
        Route::post('add-edit-category/request',[CategoryController::class,'addEditCategoryRequest']);
        Route::get('delete-category-image/{id}',[CategoryController::class,'deleteCategoryImage']);
        Route::get('delete-size-chart-image/{id}',[CategoryController::class,'deleteSizeChartImage']);

        // Products
        Route::get('products',[ProductController::class,'products']);
        Route::post('update-product-status',[ProductController::class,'updateProductStatus']);
        Route::get('delete-product/{id}',[ProductController::class,'deleteProduct']);
        Route::get('add-edit-product/{id?}',[ProductController::class,'addEditProduct']);
        Route::post('add-edit-product/request',[ProductController::class,'addEditProductRequest']);
        Route::get('delete-product-video/{id}',[ProductController::class,'deleteProductVideo']);
        Route::get('delete-product-image/{id?}',[ProductController::class,'deleteProductImage']);
        Route::get('delete-product-banner/{id?}',[ProductController::class,'deleteProductBanner']);
        Route::get('delete-product-icon/{id?}',[ProductController::class,'deleteProductIcon']);
        Route::get('delete-product-main-image/{id?}',[ProductController::class,'deleteProductMainImage']);
        Route::get('delete-product-video-thumbnail/{id?}',[ProductController::class,'deleteProductVideoThumbnail']);

        //Import Mangement
        Route::match(['get', 'post'], '/import-data', 'ImportController@importData');
        Route::match(['get', 'post'], '/import-file-data', 'ImportController@importFileData');

        // Attributes
        Route::post('update-attribute-status',[ProductController::class,'updateAttributeStatus']);
        Route::get('delete-attribute/{id?}',[ProductController::class,'deleteAttribute']);

        // Brands
        Route::resource('brands', BrandController::class);
        Route::post('update-brand-status',[BrandController::class,'updateStatus']);
        /*Route::get('brands',[BrandController::class,'brands']);
        Route::get('delete-brand/{id?}',[BrandController::class,'deleteBrand']);
        Route::match(['get','post'],'add-edit-brand/{id?}',[BrandController::class,'addEditBrand']);*/
        Route::get('delete-brand-image/{id?}',[BrandController::class,'deleteBrandImage']);
        Route::get('delete-brand-logo/{id?}',[BrandController::class,'deleteBrandLogo']);

        // Banners
        Route::get('banners',[BannerController::class,'banners']);
        Route::post('update-banner-status',[BannerController::class,'updateBannerStatus']);
        Route::get('delete-banner/{id}',[BannerController::class,'deleteBanner']);
        Route::get('add-edit-banner/{id?}',[BannerController::class,'addEditBanner']);
        Route::post('add-edit-banner/request',[BannerController::class,'addEditBannerRequest']);

        // Coupons
        Route::get('coupons',[CouponController::class,'coupons']);
        Route::post('update-coupon-status',[CouponController::class,'updateCouponStatus']);
        Route::get('delete-coupon/{id}',[CouponController::class,'deleteCoupon']);
        Route::match(['get','post'],'add-edit-coupon/{id?}',[CouponController::class,'addEditCoupon']);

        // Orders
        Route::get('orders',[OrderController::class,'orders']);
        Route::get('orders/{id}',[OrderController::class,'orderDetails']);
        Route::get('orders/invoice/{id}',[OrderController::class,'viewOrderInvoice']);
        Route::get('orders/courier-invoice/{id}',[OrderController::class,'viewCourierInvoice']);
        Route::get('orders/print-invoice/{id}',[OrderController::class,'printInvoice']);
        Route::post('update-order-status',[OrderController::class,'updateOrdersStatus']);
        Route::get('update_product_sale',[OrderController::class,'update_product_sale']);
        Route::get('/search-orders', [OrderController::class, 'searchOrders'])->name('search-orders');

        // Export Orders
        Route::match(['get', 'post'], '/export-orders',[ReportController::class,'exportorders']);

        // Users
        Route::get('users',[UserController::class,'users']);
        Route::post('update-user-status',[UserController::class,'updateUserStatus']);

        // Credits
        Route::match(['get', 'post'], '/credits', [CreditController::class,'credits']);
        Route::match(['get', 'post'], '/credits/{userid}', [CreditController::class,'userCredits']);
        Route::match(['get', 'post'], '/add-credit',[CreditController::class,'addCredit']);
        Route::post('/checkUserEmail',[CreditController::class,'checkUserEmail']);
        Route::get('/search-emails', [CreditController::class, 'searchEmails'])->name('search-emails');

        // Searches
        Route::match(['get', 'post'], '/search-enquiries', [UserController::class, 'searchResults'])->name('admin.search-results');

        // Export Users
        Route::match(['get', 'post'], '/export-users', 'ReportController@exportusers');

        // Export
        Route::get('export-brands', [ReportController::class,'exportbrands']);
        Route::get('export-products',[ReportController::class,'exportproducts']);
        Route::get('export-categories',[ReportController::class,'exportcategories']);

        // Subscribers
        Route::get('subscribers',[UserController::class,'subscribers']);
        Route::post('update-subscriber-status',[UserController::class,'updateSubscriberStatus']);

        // Export Subscribers
        Route::match(['get', 'post'], '/export-subscribers',[ReportController::class,'exportsubscribers']);

        // Export Enquiries
        Route::match(['get', 'post'], '/export-enquiries',[ReportController::class,'exportEnquiries']);
        Route::match(['get', 'post'], '/export-business-enquiries',[ReportController::class,'exportBusinessEnquiries']);

        // Taxes
        Route::get('taxes',[ProductController::class,'taxes']);
        Route::match(['get','post'],'update-taxes',[ProductController::class,'updateTaxes']);

        // Reviews/Ratings
        Route::get('ratings',[RatingController::class,'ratings']);
        Route::post('update-rating-status',[RatingController::class,'updateRatingStatus']);
        Route::get('delete-rating/{id}',[RatingController::class,'deleteRating']);


        Route::get('custom-fit',[CustomfitController::class,'customfit']); 


        // Warranties
        Route::get('warranties',[OrderController::class,'warranties']);

        //Widget Routes
        Route::resource('widgets', WidgetController::class);
        Route::match(['get', 'post'], '/widgets/append',[WidgetController::class,'appendWidget']);
        Route::match(['get', 'post'], '/widgets/update-sort',[WidgetController::class,'updateSort'])->name('admin.widgets.updateSort');

        // Update Products Stock
        Route::match(['get', 'post'], '/update-stock',[ProductController::class,'updateStock']);

        // Enquiries Routes
        Route::match(['get', 'post'], '/contact-enquiries',[EnquiryController::class,'enquiries']);
        Route::match(['get', 'post'], '/business-enquiries',[EnquiryController::class,'business_enquiries']);

        // Return Requests
        Route::get('return-requests',[ReturnController::class,'returnRequests']);
        Route::post('return-requests/update',[ReturnController::class,'returnRequestUpdate']);

        // Exchange Requests
        Route::get('exchange-requests',[ExchangeController::class,'exchangeRequests']);
        Route::post('exchange-requests/update',[ExchangeController::class,'exchangeRequestUpdate']);


    });
});

Route::namespace('App\Http\Controllers\Front')->group(function(){
    
    // Coming Soon Page
    //Route::get('/', [IndexController::class,'comingSoon'])->name('comingSoon');
    Route::get('/', [IndexController::class,'index'])->name('home');
    /*Route::get('/', [IndexController::class,'comingSoon']);*/

    // Home Page
    //Route::get('/index', [IndexController::class,'index']);
   // Route::get('/index2', [IndexController::class,'index2']);

    // Product Search
    Route::match(['get','post'],'/results',[ProductFrontController::class,'searchResults']);

    Route::get('/search-products', [ProductFrontController::class, 'searchproduct'])->name('searchproduct');
    //Route::get('/search-products', [ProductFrontController::class, 'ajaxSearch']);

    // Brand Routes
    if (Schema::hasTable('brands')) {
        //Brand Routes
        $brandSlugs = Brand::where('status',1)->get()->pluck('url')->toArray();
        foreach($brandSlugs as $brand){
            Route::get('/'.$brand,[ProductFrontController::class,'listing']);
        }
    }

    // Category Routes
    if (Schema::hasTable('categories')) {
        //Category Routes
        $catUrls = Category::where('status',1)->get()->pluck('url')->toArray();
        foreach ($catUrls as $key => $url) {
            Route::get('/'.$url,[ProductFrontController::class,'listing'])->name('listing');
        }
    }
    Route::get('/new-arrival',[ProductFrontController::class,'listing'])->name('newarrival');
	
    Route::get('/featured-collection',[ProductFrontController::class,'listing'])->name('featuredcollection');


    if (Schema::hasTable('cms_pages')) {
        //CMS ROUTES
        $cmsArray = CmsPage::Where('status',1)->get()->pluck('url')->toArray();
        foreach($cmsArray as $cms){
            Route::get('/'.$cms,[CmsFrontController::class,'staticpages']);
        }
    }

    // Contact Us
    Route::get('/contact-us',[IndexController::class,'contactus'])->name('contactus');
    Route::get('/about-us',[IndexController::class,'aboutus'])->name('aboutus');
    Route::get('/business-enquiry',[CmsFrontController::class,'businessenquiry']);
    Route::post('/save-contact',[CmsFrontController::class,'saveContact'])->name('savecontact');
    Route::post('/business-save-contact',[CmsFrontController::class,'businessSaveContact']);

   // Route::get('/about-us',[CmsFrontController::class,'aboutus']);

    // Product Detail Page
    Route::get('/product/{name}/{id}',[ProductFrontController::class,'detail'])->name('product');
    Route::post('product-quick-view',[ProductFrontController::class,'productquickview'])->name('productquickview');

    Route::post('/product/ProPrice',[ProductFrontController::class,'detailProPrice'])->name('getproductprice');

    // Update Stock Alert List
    Route::post('/product/stock-alert', [ProductFrontController::class, 'storeStockAlert'])->name('product.stock.alert');

    // Expire Credits API
    Route::get('/expire-credits', [CreditController::class,'expireCredits']);

    // Add to Cart
    Route::post('/add-to-cart',[ProductFrontController::class,'addtoCart'])->name('addtocart');

    // Add to Cart (from Listing Page)
    Route::post('/direct-add-to-cart',[ProductFrontController::class,'directAddtoCart']);

    // Quick Add to Cart
    Route::get('/get-product-details', [ProductFrontController::class, 'getProductDetails']);

    // Update Cart Item Quantity
    Route::post('update-cart-item',[ProductFrontController::class,'updateCartItem'])->name('updatecartitem');

    // Cart Route
    Route::any('/cart',[ProductFrontController::class,'cart'])->name('cart');

    // Delete Cart Item
    Route::post('delete-cart-item',[ProductFrontController::class,'deleteCartItem'])->name('deletecartitem');

    // Show login form
    Route::get('user/login',[UserFrontController::class,'showLoginForm'])->name('signin');

    // Handle login
    Route::post('user/login', [UserFrontController::class, 'login']);

    // Handle OTP Login
    Route::post('/send-otp', [UserFrontController::class, 'sendOtp']);

    // Verify OTP Route
    Route::post('/verify-otp', [UserFrontController::class, 'verifyOtp']);

    Route::post('/check-email', [UserFrontController::class, 'checkEmail']);
    Route::post('/check-mobile', [UserFrontController::class, 'checkMobile']);

    Route::post('/check-mobile-exists', [UserFrontController::class, 'checkMobileExists']);

    // Register User
    Route::match(['get','post'],'user/register',[UserFrontController::class,'register'])->name('signup');

    // Forgot Password
    Route::post('/forgot-password',[UserFrontController::class,'forgotPassword'])->name('forgotpassword');

    // Get State/City from Pincode
    Route::post('/get-state-city',[UserFrontController::class,'getStateCity'])->name('getstatecity');
    Route::post('/order-address-form', [AddressController::class,'orderaddressform'])->name('orderaddressform');
    Route::post('/save-order-address', [AddressController::class,'saveorderaddress'])->name('saveorderaddress');
    // Login to Apply Coupon
    Route::post('/apply-coupon',[ProductFrontController::class,'applyCoupon']);

    // Add to Wishlist
    Route::match(['get','post'],'/add-to-wishlist', [ProductFrontController::class,'addtoWishlist'])->name('addtowishlist');
   
   
     

   
   
    // User Invoice
    Route::get('user/invoice/{id}',[OrderController::class,'viewUserOrderInvoice']);

    //Subscriber routes
    Route::post('add-subscriber',[IndexController::class,'addSubscriber'])->name('addsubscriber');

    // Check Pincode Route
    Route::post('/check-pincode', [ProductFrontController::class, 'checkPincode'])->name('checkpincode');

    //Write a Review Page
    Route::post('write-a-review',[ProductFrontController::class,'writeReview'])->name('savereview');
    //Custom Fit
	Route::post('save-custom-fit',[ProductFrontController::class,'savecustomfit'])->name('savecustomfit');

    /*Cron Jobs*/

    // Notify Users about Stock
    Route::get('/notify-users', [ProductFrontController::class, 'notifyUsers']);

    // Verify Razorpay Payment
    Route::get('/verify-razorpay-payment/{id?}',[RazorpayController::class,'verifyRazorpayPayment']);

    

    Route::group(['middleware' => ['auth','preventBackHistory']], function () {

        // User Account
        Route::get('account/{slug}',[UserFrontController::class,'account'])->name('account');

        // User Account Update
        Route::post('user/account/update',[UserFrontController::class,'saveAccount'])->name('saveaccount');
        Route::post('get-state-city',[UserFrontController::class,'getStateCity'])->name('getstatecity');

        // User Wishlist
        Route::get('/user/wish-list',[UserFrontController::class,'userWishlist']);

        // Remove from Wishlist
        Route::post('/remove-wishlist',[UserFrontController::class,'removeWishlist'])->name('removewishlist');
        
        //Order Invoice
        Route::get('order-invoice/{id}',[UserFrontController::class,'orderinvoice'])->name('orderinvoice'); 
		
		
        //Route::get('order-cancel/{id}',[UserFrontController::class,'ordercancel'])->name('ordercancel'); 

        // User Password Update
        Route::post('user/password/update',[UserFrontController::class,'userUpdatePassword'])->name('changepassword');

        // Logout User
        Route::get('user/logout',[UserFrontController::class,'logout'])->name('signout');

        // Order Checkout
        Route::get('/checkout',[ProductFrontController::class,'orderCheckout'])->name('checkout');
       

        // Place Order
        Route::post('/place-order',[ProductFrontController::class,'placeOrder'])->name('placeOrder');

        //Address Routes
        Route::match(['get','post'],'/save-address',[AddressController::class,'saveAddress']);
        Route::get('/get-delivery-address',[AddressController::class,'getDeliveryAddress']);
        Route::post('/set-default-address',[AddressController::class,'setDefaultAddress'])->name('setdefaultaddress');
        Route::post('/remove-delivery-address',[AddressController::class,'removeDeliveryAddress'])->name('deleteaddress');

        // Order Thanks Page
        Route::get('/thanks',[ProductFrontController::class,'thanks']);

        // CCAvenue Wallet
        Route::any('/ccavenue/{id}',[PaymentController::class,'ccavenue']);
        Route::any('/ccavenue-response',[PaymentController::class,'ccavenueresponse']);
        Route::any('/ccavenue-fail',[PaymentController::class,'ccavenuefail']);

        // Razorpay
        Route::match(['get', 'post'], '/razorpay-payment', [RazorpayController::class, 'razorpayPayment'])->name('razorpay-payment');
        Route::post('dopayment', [RazorpayController::class, 'dopayment'])->name('dopayment');
        Route::get('cancel', [RazorpayController::class, 'cancel']);

        // Cancel Order from User
        Route::get('/cancel-order/{id}',[CancelController::class,'cancelOrder'])->name('cancelOrder');

        // Return Product
        Route::post('user/return-product',[ReturnFrontController::class,'verifyReturnProduct']);

        // Exchange Product
        Route::post('user/exchange-product',[ExchangeFrontController::class,'verifyExchangeProduct']);
    });

});
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home1');
