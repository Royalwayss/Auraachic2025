@extends('front.layout.layout')
@section('content')
<
<!-- breadcrumb start -->
<div class="breadcrumb">
    <div class="container">
        <ul class="list-unstyled d-flex align-items-center m-0">
            <li><a href="/">Home</a></li>
            <li>
                <i class="fa-solid fa-angle-right"></i>
            </li>
            <li>About Us</li>
        </ul>
    </div>
</div>
<!-- breadcrumb end -->


   <main id="MainContent" class="content-for-layout">

         <section class="about-section container">
            <div class="row">
                <!-- Left Column with Vertical Text -->
                <div class="col-md-3 col-12 d-flex justify-content-center align-items-center about-left">
                    <h2 class="vertical-text">ABOUT US</h2>
                </div>
 
                <!-- Right Column with Paragraph Content -->
                <div class="col-md-9 col-12 about-right">
                    <p>
                        Established in the year 1991 at Ludhiana (Punjab, India), we “R. K. Basil Hosiery”, are a
                        leading manufacturer and supplier of premium quality range of Ladies Woolen Kurties, Ladies
                        Woolen Coat, Ladies Cardigan, Ladies Pullover and Ladies Shrugs. The garments provided by us are
                        designed using high grade fabrics and other allied material that are sourced from the trusted
                        and honorable vendors of the industry. Offered garments are crafted with the aid of innovative
                        stitching machines in complete compliance with the current fashion trends by our dedicated
                        professionals.
                    </p>
                    <p>
                        The garments provided by us are widely acknowledged by our honorable clients for their salient
                        features such as flawless finish, alluring look, light weight, perfect fitting, mesmerizing
                        design, elegant pattern, skin–friendliness, smooth texture and captivating appearance. In
                        addition to this, we provide these garments at rock bottom prices to our clients within the
                        promised time span.
                    </p>
                </div>
            </div>
        </section>



                <!-- about banner start -->
                <div class="about-banner mt-100" data-aos="fade-up" data-aos-duration="700">
                    <div class="container">
                        <div class="about-banner-wrapper">
                            <div class="about-banner-content">
                                <p class="about-banner-text heading_48">Get in touch with us for your service related query</p>
                                <a href="{{ route('contactus') }}" class="about-banner-btn">CONTACT US</a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- about banner end -->
         
        </main>

@endsection