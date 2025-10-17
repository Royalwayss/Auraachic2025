@extends('front.layout.layout')
@section('content')
	<div class="breadcrumb">
		<div class="container">
			<ul class="list-unstyled d-flex align-items-center m-0">
				<li><a href="{{ route('home') }}">Home</a></li>
				<li><i class="fa-solid fa-angle-right"></i></li>
				<li><?php echo $details->title; ?></li>
			</ul>
		</div>
	</div>
	<main id="MainContent" class="content-for-layout">
		<div class="about-page">
			<div class="container privacy-content mt-5">
				<div class="row">
					<div class="section-header text-left">
						<h2 class="section-heading"><?php echo $details->title; ?></h2>
						<?php echo $details->description; ?>
					</div>
				</div>
			</div>
		</div>            
	</main>
@endsection