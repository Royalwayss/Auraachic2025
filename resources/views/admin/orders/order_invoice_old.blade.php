<?php use App\GiftOffer; ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <!-- <link rel="icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon"> -->
        <title>User Invoice</title>
        <style type="text/css">
          @font-face {
              font-family: 'junicoderegular';
              src: url('../../css/fonts/junicode-webfont.eot');
              src: url('../../css/fonts/junicode-webfont.eot?#iefix') format('embedded-opentype'),
                   url('../../css/fonts/junicode-webfont.woff2') format('woff2'),
                   url('../../css/fonts/junicode-webfont.woff') format('woff'),
                   url('../../css/fonts/junicode-webfont.ttf') format('truetype'),
                   url('../../css/fonts/junicode-webfont.svg#junicoderegular') format('svg');
              font-weight: normal;
              font-style: normal;
          }

          @font-face {
              font-family: 'open_sans';
              src: url('../../css/fonts/opensans-regular-webfont.eot');
              src: url('../../css/fonts/opensans-regular-webfont.eot?#iefix') format('embedded-opentype'),
                   url('../../css/fonts/opensans-regular-webfont.woff2') format('woff2'),
                   url('../../css/fonts/opensans-regular-webfont.woff') format('woff'),
                   url('../../css/fonts/opensans-regular-webfont.ttf') format('truetype'),
                   url('../../css/fonts/opensans-regular-webfont.svg#open_sansregular') format('svg');
              font-weight: normal;
              font-style: normal;
          }

          .Btnsdiv__Invoice{margin-bottom: 30px; margin-top: 30px; text-align: center;}
          .Btnsdiv__Invoice input{background-color: #121212; color: #fff; opacity: 0.75;text-decoration: none;outline: none; border-width: 1px; border-color: transparent; border-style: solid; display: inline-block; padding: 8px 12px; margin-bottom: 0; margin-right: 15px; margin-left: 0; border-radius: 4px; -webkit-border-radius: 4px; -moz-border-radius: 4px; background-clip: padding-box; float: none; cursor: pointer;}
          .Btnsdiv__Invoice input:focus,
          .Btnsdiv__Invoice input:hover,
          .Btnsdiv__Invoice input:active{color: #fff; background-color: #999; opacity: 1; text-decoration: none;outline: none; border-width: 1px; border-color: transparent; border-style: solid; cursor: pointer;}
          .fullWidth{width: 100%; float: left; display: inline-block; position: relative;}
          .InvoiceTable p{margin-bottom: 0; line-height: 1.257em;letter-spacing: 0; color: #828282;}
          .InvoiceTable p.barcode:not(:last-of-type){margin-bottom: 10px;}
          .InvoiceTable > tbody > tr > td:only-child{width: 100%;}
          .InvoiceTable p:not(:last-of-type){margin-bottom: 3px; margin-top: 0; display: inline-block; width: 100%; float: left;text-align: left;}
          .InvoiceTable h6{margin-bottom: 0;margin-top: 0;display: inline-block; width: 100%; float: left;text-align: left;font-family: 'junicoderegular', sans-serif; letter-spacing: 1px; font-weight: 600; font-size: 14px; line-height: 1.1428em;}
          .InvoiceTable h6 span{display: inline-block; float: none; font-family: "open_sans", sans-serif; font-weight: normal;}
          .InvoiceTable h1{margin-bottom: 10px;margin-top: 0;display: inline-block; width: 100%; float: left;text-align: left;font-family: 'junicoderegular', sans-serif; font-size: 23px; letter-spacing: 1px;font-weight: 600;}
          .InvoiceTable h1.para{margin-bottom: 0;margin-top: 0;display: inline-block; width: 100%; float: left;text-align: left;font-family: 'open_sans', sans-serif; font-size: 23px; letter-spacing: 1px;font-weight: 600;}
          .InvoiceTable h6:not(:last-of-type){ margin-bottom: 6px;}
          .InvoiceTable h6:only-of-type{margin-bottom: 6px !important;}
          html{font-size-adjust: 100%; -webkit-text-size-adjust: 100%; font-size: 14px;}
          body{margin: 0; padding: 0; font-family: 'open_sans', 'Arial', 'Helvetica', sans-serif; font-size: 14px; font-weight: normal; font-style: normal; text-align: center;}
          /* .barcode {font-family: 'basawa_3_of_9_mhrregular';font-size:48px;} */
          .InvoiceTable h6{font-size: 14px; font-weight: bold;}
          h6.para{font-family: "open_sans", sans-serif; font-size: 12px;}
          table.InvoiceTable h6.para{margin-bottom: 0 !important;}
          *,*:after,*:before{box-sizing: border-box; -webkit-box-sizing: border-box; -moz-box-sizing: border-box;}
          table.InvoiceTable{float: none; width: 100%; border: 1px solid #ddd; table-layout: fixed; text-align: left; border-collapse: collapse;vertical-align: top;font-family: 'open_sans', 'Arial', 'Helvetica', sans-serif; font-size: 14px; }
          table.InvoiceTable > tbody > tr:not(:last-of-type){border-bottom: 1px solid #ddd;}
          table.InvoiceTable > tbody > tr > td:not(:last-of-type){}
          table.InvoiceTable td,
          table.InvoiceTable th{padding: 8px;vertical-align: top;}
          table:not(.InvoiceTable){table-layout: fixed; width: 100%; float: left;border-collapse: collapse; vertical-align: top;font-family: 'open_sans', 'Arial', 'Helvetica', sans-serif; font-size: 14px; }
          table td,
          table th{padding: 3px; vertical-align: top;}
          img{max-width: 100%;}

          .invoice-logo
            {   
                width:100%;
                max-width:125px;
                margin-left:20px;
            }
        </style>
    </head>
    <body>
        <div class="fullWidth Btnsdiv__Invoice" id="ButtonDiv" style="height: 5px;">
            <!-- <input value="Print Invoice" onclick="javascript:window.print();" type="button">
            <input value="Close Window" onclick="window.close();" type="button"> -->
        </div>
        <div style="max-width: 780px; text-align: center; float: none; display: inline-block; width: 780px;">
            <table width="778px" class="InvoiceTable" border="0" cellspacing="0" cellpadding="0" style="text-align: left;">
                <tr>
                  <td colspan="3" align="center"><h2 style="height: 10px;">Order Invoice</h2></td>
                </tr>
                <tr>
                    <td style="width:50px; vertical-align: middle; "><img class="invoice-logo" src="{{ asset('front/images/logo.png') }}"></td>
                    <td>
                        <p>{{config('constants.return_address')}}</p>
                        <p><strong>{{config('constants.invoice_name')}}</strong></p>
                        <!-- <p>GST Number: {{config('constants.gst')}} </p> -->
                    </td>
                    <td>
                        <!-- <p>Toll Free No. : {{config('constants.project_mobile')}}</p> -->
                        <p>Toll Free No. :</p>
                        <p>Email: {{config('constants.project_email')}}</p>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <h6>DELIVER TO :</h6>
                        <p>
                            {{$orderDetails['order_address']['shipping_name'] }}
                        </p>
                        <p>
                            {{$orderDetails['order_address']['shipping_address'] }}
                        </p>
                        <p>  
                            {{$orderDetails['order_address']['shipping_city'] }}
                        </p>
                        <p>
                            {{$orderDetails['order_address']['shipping_state'] }}
                        </p>
                        <p>
                            {{$orderDetails['order_address']['shipping_postcode'] }}
                        </p>
                        <p><b>Mobile:</b> {{$orderDetails['order_address']['shipping_mobile'] }}<br>
                        </p>
                    </td>
                    <td>
                        <h1>{{ ($orderDetails['payment_method'] == "COD") ? "COD" : 'Prepaid' }} Order</h1>
                        <p><strong>Order ID: </strong>{{$orderDetails['id']}}</p>
                        <p><strong>Order Date: </strong>{{date('d F Y h:ia',strtotime($orderDetails['created_at']))}}</p>
                        <p><strong>Quantity: </strong>{{$orderDetails['total_items']}}</p>
                        <p><strong>Grand Total: </strong>₹{{round($orderDetails['grand_total'],2)}}</p>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        @if($orderDetails['payment_method'] =="COD")
                            <h1>Amount To be Collected: ₹{{round($orderDetails['grand_total'],2) }}</h1>
                        @else
                            <h1>Amount: ₹{{round($orderDetails['grand_total'],2) }}</h1>
                        @endif
                    </td>
                </tr> 
                <tr>
                    <td colspan="3">
                        <h3 class="para">Grand Total in Words: &nbsp; {{ucwords($numberWords)}} Rupees Only</h3>   
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <table cellspacing="0" cellpadding="0">
                            <tbody>
                                <tr style="background-color:#ddd;">
                                    <td style="width:30%;" >
                                        <h6 class="para">Product Details </h6>
                                    </td>
                                    <td >
                                        <h6 class="para">Product Size</h6>
                                    </td>
                                    <td>
                                        <h6 class="para">MRP (₹)</h6>
                                    </td>
                                    <td>
                                        <h6 class="para">Discount (₹)</h6>
                                    </td>
                                    <td>
                                        <h6 class="para">Price (₹)</h6>
                                    </td>
                                    <td>
                                        <h6 class="para">Quantity</h6>
                                    </td>
                                    <td >
                                        <h6 class="para">Sub Total (₹)</h6>
                                    </td>
                                </tr>
                                @foreach($orderDetails['order_products'] as $product)
                                    <tr style="border-bottom: 1px solid #ddd;">
                                        <td>
                                            <p style="color: #8B9931; font-weight: bold; font-size: 14px;">{{$product['product_name']}} ({{$product['product_code']}})<br>HSN Code: {{$product['product_code']}}</p>
                                        </td>
                                        <td>
                                            <p>{{$product['product_size']}}</p>
                                        </td>
                                        <td>
                                            <p>
                                              {{round($product['mrp'],2)}}
                                            </p>
                                        </td>
                                        <td>
                                            <p>{{round($product['discount'])}}</p>
                                        </td>
                                        <td>
                                            <p>{{round($product['product_price'],2)}}</p>
                                        </td>
                                        <td>
                                            <p>{{$product['product_qty']}}</p>
                                        </td>
                                        <td>
                                            <p>{{round($product['sub_total'],2)}}</p>
                                        </td>
                                    </tr>
                                @endforeach
                                <tr style="border-bottom: 1px solid #ddd;">
                                    <td colspan="5"></td>
                                    <td>Coupon Discount (-):</td>
                                    <td>₹{{round($orderDetails['coupon_discount'],2)}}</td>
                                </tr>
                                <tr style="border-bottom: 1px solid #ddd;">
                                    <td colspan="5"></td>
                                    <td>Credit Amount (-):</td>
                                    <td>₹{{round($orderDetails['credit'],2)}}</td>
                                </tr>
                                <?php
                                    $gst_on_shipping = $orderDetails['shipping_charges'] - $orderDetails['shipping_charges']/118*100; 
                                ?>
                                <!-- <tr style="border-bottom: 1px solid #ddd;">
                                    <td colspan="5"></td>
                                    <td>Shipping Charges:</td>
                                    <td>₹{{round($orderDetails['shipping_charges']-$gst_on_shipping,2)}}</td>
                                </tr> -->
                                <tr style="border-bottom: 1px solid #ddd;">
                                    <td colspan="5"></td>
                                    <td>Shipping Charges (+):</td>
                                    <td>₹{{round($orderDetails['shipping_charges'],2)}}</td>
                                </tr>
                                <!-- <tr style="border-bottom: 1px solid #ddd;">
                                    <td colspan="5"></td>
                                    <td>GST on Shipping Charges:</td>
                                    <td>₹{{round($gst_on_shipping,2)}}</td>
                                </tr> -->
                                <!-- <tr style="border-bottom: 1px solid #ddd;">
                                    <td colspan="5"></td>
                                    <td>COD Charges:</td>
                                    <td>₹{{round($orderDetails['cod_charges'],2)}}</td>
                                </tr> -->
                                <tr style="border-bottom: 1px solid #ddd;">
                                    <td colspan="5"></td>
                                    <td>Taxes (+):</td>
                                    <td>₹{{round($orderDetails['taxes'],2)}}</td>
                                </tr>
                                <!-- @if($orderDetails['order_address']['shipping_state']=="Maharashtra")
                                <tr style="border-bottom: 1px solid #ddd;">
                                    <td colspan="5"></td>
                                    <td>CGST (Included):</td>
                                    <td>₹{{round($orderDetails['taxes']/2,2)}}</td>
                                </tr>
                                <tr style="border-bottom: 1px solid #ddd;">
                                    <td colspan="5"></td>
                                    <td>SGST (Included):</td>
                                    <td>₹{{round($orderDetails['taxes']/2,2)}}</td>
                                </tr>
                                @else
                                <tr style="border-bottom: 1px solid #ddd;">
                                    <td colspan="5"></td>
                                    <td>IGST (Included):</td>
                                    <td>₹{{round($orderDetails['taxes'],2)}}</td>
                                </tr>
                                @endif -->

                                <tr>
                                    <td colspan="5"></td>
                                    <td><b>Grand Total:</b></td>
                                    <td><b>₹{{round($orderDetails['grand_total'],2)}}</b></td>
                                </tr>
                                <tr style="background-color:#F5F5F5;">
                                    <td colspan="5">
                                    </td>
                                    <td style="text-align: left; vertical-align: middle;">
                                        <p style="margin-top: 0">
                                            Order Status:
                                        </p>
                                    </td>
                                    <td style="text-align: left; vertical-align: middle;">
                                        <p style="margin-top: 0">
                                        <strong>{{$orderDetails['order_status']}}</strong>
                                        </p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <p>
                            <b>This is a computer generated invoice and does not require signature</b>
                        </p>
                        <p>If you receive an open or a tampered package at the time of delivery, please do not accept it.</p>
                    </td>
                </tr>
            </table>
        </div>
    </body>
</html>