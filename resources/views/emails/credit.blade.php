<?php use App\CustomFunction;?>
<html>
    <head>
        <style>
            html,
            body {
            margin: 0 auto !important;
            padding: 0 !important;
            height: 100% !important;
            width: 100% !important;
            background: #f1f1f1;
            }
            @font-face {
            font-family: 'Indian Rupee';
            src: url(data:application/font-woff2;charset=utf-8;base64,d09GMgABAAAAAALgAA0AAAAABogAAAKLAAEAAAAAAAAAAAAAAAAAAAAAAAAAAAAAP0ZGVE0cGh4GYACCShEICoE4gVILEAABNgIkAxwEIAWEIgc3G6wFyJ4FduNLw0/kGJEvi5ehV3Q8PG9X/bmvqrpn7VpbkT/g1IohZZt+OBr4D9d8V5R66++LBorCgVxEA9uQCbfChOBib4B/g4D/c7nnN0k+0y3wTWvxvbMDWgsPZBRIGGAvHPN5WuCJBJZohCG0O8e0XMztkmaU17dyKTch4NtH+9fABye3/dNdd7zBoCnaIwmhIuRIyPIVcaeCYn6JwC9udxpr7x6FIgdEMJX8zWAGsuPd6rvFA9VX4woCsj+LF1AKZMjQVYA5OIGucgNQkQtJaUehtL0arjQ8JgCiQgAiIQHugsxvywvwkGhIAlXQHAljsAg0RVuQABrgjBH5uVa+adv0y7XN7vNN48bfrMF/MJfPNv4/osO74m3rH4nmN8f3TZrFd+u+W+P4/kJE5N9++1Zac/WTsbT1htYzss03pZeLeVN3r3S+i1tmXU6NZz3aMPrO7Lq27W9Tlnzy5RQ+33ZTei/1W3Z8vbzTjHph840tt9CgZaF1oYKGX2l5xbf4lgLBw7eu2qmrU/6raVLjgBXZdmokEBydUbzVIf8dNRY4VM1/0ESkDTnMf0AotLKBqjGSB/BKhEBzzwtAFxMECE0dw4Ck6hYBMu29KUCulVoBCs1ikAAVbbIDJ/JGGEeF0NEaSJo7B5kh7oFcLz9AoXOaP7miXyZ4yigjjDRCaRHTiKxgajlGpsRGaZVWweXaOSiFp8z2B353jrRiZ+uMZrIpf2cL190dPAoslHGETJlOheUTRPuBVVy+VsJUwce0nnkVGapUNOctS0Npwnrz5DLNGP8gmgTWcVXqiOTxSMOMKKqbLRi5O4Tm6joN2O4mBH1LdsKU9W84Sqtass1muS1V9h8Zi+AKAA==) format('woff2'),
            url(data:application/font-woff;charset=utf-8;base64,d09GRgABAAAAAASUAA0AAAAABogAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAABGRlRNAAAEeAAAABwAAAAcZobmSUdERUYAAARcAAAAHAAAAB4AJwANT1MvMgAAAZwAAABMAAAAYECCPs5jbWFwAAACBAAAAE8AAAFKALgLoGdhc3AAAARUAAAACAAAAAj//wADZ2x5ZgAAAmQAAACuAAAAuJzLaRtoZWFkAAABMAAAADMAAAA2AJMKEmhoZWEAAAFkAAAAIAAAACQYSPgZaG10eAAAAegAAAAcAAAAHBj8/8Rsb2NhAAACVAAAABAAAAAQAOwBQG1heHAAAAGEAAAAGAAAACAADQBpbmFtZQAAAxQAAAEXAAACIhWzT6xwb3N0AAAELAAAACgAAAA3ddajLHjaY2BkYGAA4lU9fqnx/DZfGbg5GEDgRGHcDwid4PH/DQODwDLWq0AuBwMTSBQAPiALkwB42mNgZGBgk/0nyzid9dD/N1+MBJYxAEVQADsAnOwGbnjaY2BkYGBgZzBhYAJjVMDIwAgACqwAd3jaY2BmCWCcwMDKwMDqzurOwMBwB0IzmTKkMi4B8hlYWUAkA4REAgpAwODAoMCQwCb7T5ZxOvsWxsdAYUaQHPMH1vUgJQyMANaCCsYE2P/sAAAAAAJYAAACWAAABNgAAAXC/+wE2P/seNpjYGBgZoBgGQZGBhBwAfIYwXwWBg0gzQakGRmYGBQYEv7/B/IVGOL/////+P9SqHogYGRjgHMYmYAEEwMqYIRYQQFgYWUY3AAAMj8JXQAAAABMAEwATABMAEwAVABceNpjYPr/hoFBYBnrVQZ9BmMGBmFFUTZ2RTFxRTFRNm1GNnYgVBYVYVRmsGU0FzNWMDczVVQyNTNWNFVTV9NmNDVjtWTyZT4Uyv3vv6kwoxBrDNMmNrsg754GJuf/f88wGTkz2vjy/D3CEs/EeEy9IMDhX4xADOtVpmZOu0hG5i73lFQ5Zyauv9FME5lbWdnPyimwdDH560ixdkr8/w9zlwATAxAg+EJgPgCuNi7KAAB42o2PQUrDQBSGv2nTiqAFEUR0M7hwIU2YdCE9QaFdlSJdO5ChBGJSUrMIHsGlp+jGO3gLD+PC13TEVhTMEN735v3/PzNAj1cU2++SoWfFEbnnFgc8e25zxpvngBM+PHc4Vjeeu5yqe1Gq4FC628a1YcU5d55bcu6T5zZ9XjwHXPHuucOF6nnucq2GrBlgiOXXTLDUUqdSKxJhSyZJmpn0S5ysiJQHWA9MbPTE1npqq6S2WV/PqqVzUSrDsbwzEZ2VuutlnCepzbdKZOBYyDATYSmtW1SZLf+2a8Km33PtRupQ/yNkRCF7jz+soyKXrbkoSlbiKxpfLO81zWLuylVa5DqOjDH7+d/p4e/Xa44Iv+72Cd7AW3oAeNpjYGIAg3/bGXIZsAF2IGZkYGJgZnBicGZkYkvPqSzIMAUAcpEFGQAAAAH//wACeNpjYGRgYOABYjEgZmJgBEI2IGYB8xgAA+AANQAAAAEAAAAA1aQnCAAAAADIcV74AAAAAMhxYEg=) format('woff');
            font-weight: normal;
            font-style: normal;
            }
            .rupee{
            font-family: 'Indian Rupee';
            }
            * {
            -ms-text-size-adjust: 100%;
            -webkit-text-size-adjust: 100%;
            }
            div[style*="margin: 16px 0"] {
            margin: 0 !important;
            }
            table,
            td {
            mso-table-lspace: 0pt !important;
            mso-table-rspace: 0pt !important;
            word-break: break-word;
            }
            table {
            border-spacing: 0 !important;
            border-collapse: collapse !important;
            table-layout: fixed !important;
            margin: 0 auto !important;
            }
            td:before,
            td:after {
            content:"";
            display:table;
            clear:both;
            }
            img {
            -ms-interpolation-mode:bicubic;
            image-rendering: crisp-edges;
            }
            a {
            text-decoration: none;
            }
            *[x-apple-data-detectors],
            .unstyle-auto-detected-links *,
            .aBn {
            border-bottom: 0 !important;
            cursor: default !important;
            color: inherit !important;
            text-decoration: none !important;
            font-size: inherit !important;
            font-family: inherit !important;
            font-weight: inherit !important;
            line-height: inherit !important;
            }
            .a6S {
            display: none !important;
            opacity: 0.01 !important;
            }
            .im {
            color: inherit !important;
            }
            img.g-img + div {
            display: none !important;
            }
            @media only screen and (min-device-width: 320px) and (max-device-width: 374px) {
            u ~ div .email-container {
            min-width: 320px !important;
            }
            }
            /* iPhone 6, 6S, 7, 8, and X */
            @media only screen and (min-device-width: 375px) and (max-device-width: 413px) {
            u ~ div .email-container {
            min-width: 375px !important;
            }
            }
            /* iPhone 6+, 7+, and 8+ */
            @media only screen and (min-device-width: 414px) {
            u ~ div .email-container {
            min-width: 414px !important;
            }
            }
        </style>
        <style>
            .primary{
            background: #f85e9f;
            }
            .bg_white{
            background: #ffffff;
            }
            .bg_light{
            background: #fafafa;
            }
            .bg_black{
            background: #000000;
            }
            .bg_dark{
            background: rgba(0,0,0,.8);
            }
            .email-section{
            padding:2.5em;
            }
            /*BUTTON*/
            .btn{
            padding: 5px 15px;
            display: inline-block;
            }
            .btn.btn-primary{
            border-radius: 5px;
            background: #f85e9f;
            color: #ffffff;
            }
            .btn.btn-white{
            border-radius: 5px;
            background: #ffffff;
            color: #000000;
            }
            .btn.btn-white-outline{
            border-radius: 5px;
            background: transparent;
            border: 1px solid #fff;
            color: #fff;
            }
            .btn.btn-black-outline{
            border-radius: 0px;
            background: transparent;
            border: 2px solid #000;
            color: #000;
            font-weight: 700;
            }
            h1,h2,h3,h4,h5,h6{
            font-family: 'Source Sans Pro', sans-serif;
            color: #000000;
            letter-spacing: 0.5px;
            margin-top: 0;
            font-weight: 500;
            }
            p{font-weight: 400; letter-spacing: 0.5px; margin-top: 0; margin-bottom: 0; line-height: 1.8;}
            body{
            font-family: 'Source Sans Pro', sans-serif;
            font-weight: 400;
            font-size: 16px;
            line-height: 1.575;
            color: rgba(0,0,0,.4);
            }
            a{
            color: #ed217c;
            }
            table{
            }
            /*LOGO*/
            .logo h1{
            margin: 0;
            }
            .logo h1 a{
            color: #000000;
            font-size: 20px;
            font-weight: 700;
            text-transform: uppercase;
            font-family: 'Source Sans Pro', sans-serif;
            border: 2px solid #000;
            padding: .2em 1em;
            }
            .navigation{
            padding: 0;
            padding: 1em 0;
            /*background: rgba(0,0,0,1);*/
            border-top: 1px solid rgba(0,0,0,.05);
            border-bottom: 1px solid rgba(0,0,0,.05);
            }
            .navigation li{
            list-style: none;
            display: inline-block;;
            margin-left: 5px;
            margin-right: 5px;
            font-size: 13px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 2px;
            }
            .navigation li a{
            color: rgba(0,0,0,1);
            }
            /*HERO*/
            .hero{
            position: relative;
            z-index: 0;
            }
            .hero .text{
            color: rgba(0,0,0,.3);
            }
        </style>
    </head>
    <body width="100%" style="margin: 0; padding: 0 !important; mso-line-height-rule: exactly; background-color: #222222;">
        <center style="width: 100%; background-color: #f1f1f1;">
            <div style="display: none; font-size: 1px;max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all; font-family: sans-serif;">
                &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
            </div>
            <div style="max-width: 600px; margin: 0 auto; width: 100%; background-color: #fff;" class="email-container">
                <!-- BEGIN BODY -->
                <table align="center" role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: auto;">
                    <tr>
                        <td valign="top" class="bg_white" style="padding: 1em 2.5em 0 2.5em;">
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td class="logo" style="text-align: center;">
                                        <a href="{{ url(config('constants.project_url'))}}" target="_blank" href="" style="width: 100%; float: left; text-align: center; display: inline-block;">
                                        <img src="{{ asset(config('constants.project_url').'front/images/logo-scrolled.png') }}" alt="" style="width: 120px; display: inline-block; float: none;"><br><br>
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td valign="middle" class="hero hero-2 bg_white" style="padding: 2em 0 2em 0;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td style="width: 100%;">
                                        <div class="text" style="padding: 0 2.5em; text-align: center;">
                                            <h2 style="width: 100%; float: left; display: inline-block; text-align: center; margin-bottom: 20px;">CREDIT ADDED</h2>
                                            <p style="float: left; width: 100%; text-align: center; color: #acacac; margin-bottom: 0;">Hey <span>{{ $name }}</span>,<br />Your account has been credited with INR <span>{{ $credit_amount }}</span>. You can avail this credit while placing order on <span>{{config('constants.project_name')}} Website</span>.</p>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <!-- end:tr -->
                    <tr>
                        <td class="bg_white" style="padding: 10px 25px 10px; width: 100%; border-top: 1px solid #ddd;">
                            <p style="float: left; width: 100%; text-align: center; line-height: 1.575; color: #acacac; margin-bottom: 0; line-height: 1 padding: 0">
                                If you need any help you can<br /> drop us an email: <a href="{{config('constants.project_email')}}" style="color: #000;">{{config('constants.project_email')}}</a>
                            </p>
                            <p style="float: left; width: 100%; text-align: center; line-height: 1.575; color: #acacac; margin-bottom: 0; line-height: 1 padding: 0">Team {{config('constants.project_name')}}</p>
                        </td>
                    </tr>
                </table>
            </div>
        </center>
    </body>
</html>