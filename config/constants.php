<?php
	return [
		'project_url' => 'https://auraachic.rtpltech.in/',
		'project_name' => 'Auraachic',
		'company_name' => 'Auraachic',
		'invoice_name' => '',
		'project_email' => 'customercare@auraachic.com',
		'project_mobile' => '+91-123-4567898',
		'gst' => '',
		'iec' => '',
		'pan' => '',
		'cin' => '',
		'tin' => '',
		'pincode'=> '',
		'return_address' => 'Village Rajgarh, Near Doraha Canal Bridge, GT Road, Doraha, Ludhiana, Punjab, India 141421',
		//MEDIA_BASE_URL is using aws url
		'media' => [
			'base_url' =>env('MEDIA_BASE_URL').'/',
			'static' => 'front/static/',
			'banners_path' => [
				'desktop' => 'front/images/banners/desktop/',
				'mobile' => 'front/images/banners/mobile/',
			],
			'brands_path' => [
				'logos' => 'front/images/brands/logos/',
				'covers' =>[
					'desktop' => 'front/images/brands/covers/desktop/',
					'mobile' => 'front/images/brands/covers/mobile/',
				],
				'homepage' =>[
					'desktop' => 'front/images/brands/covers/desktop/',
					'mobile' => 'front/images/brands/covers/mobile/',
				]
			],
			'categories_path' => [
				'images' => 'front/images/categories/images/',
				'covers' =>[
					'desktop' => 'front/images/categories/covers/desktop/',
					'mobile' => 'front/images/categories/covers/mobile/',
				]
			],
			'products_path' => [
				'products' => 'front/images/products/',
				'grid_path' => 'grids/',
				'images_path' => 'images/',
				'videos_path' => 'videos/',
				'reviews_path' => [
					'images' => 'front/images/reviews/images/',
					'videos' => 'front/images/reviews/videos/'
				],
				'sections_path' => 'media/sections/images/'
			],
			'widgets_path' => [
				'images' =>[
					'desktop' => 'front/images/widgets/banners/desktop/',
					'mobile' => 'front/images/widgets/banners/mobile/',
				],
				'videos' => 'front/widgets/videos/'
			],
			'default_image' => env('MEDIA_BASE_URL').'/media/static/default.png',
		]
	];
?>