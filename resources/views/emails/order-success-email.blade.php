
<html>
    <head>
        <style type='text/css'>
             

                .style1 {

                    color: #FFFFFF

                }

                .style2 {

                    font-size: 11px;

                    font-weight: bold;

                    text-decoration: none;

                    font-family: Verdana, Arial, Helvetica, sans-serif;

                    color:#666666;

                }

                .style3 {

                    text-decoration: none;

                    font-family: Verdana, Arial, Helvetica, sans-serif;

                    font-size: 11px;

                    color:#666666;

                }

        

        </style>
    </head>
    <body>
         <table width='80%' border='0' cellpadding='3' cellspacing='3' style='border:#EFEFEF 5px solid; padding:5px;'>
   
			<tr>
                <td colspan='3'></td>
            </tr>
            <tr>
                 <td  align='center' valign='middle'>
				<a target="_black" href="{{ url(route('home')) }}">
				<img border='0' class="site_logo" src="{{ $websettings['site_logo'] }}"  style="width: 75px!important;" alt='logo' />
				</a>
				</td>
			</tr>
            <tr>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td height='70' align='right' valign='top'>
                    <table width='93%' border='0' align='right' cellpadding='3' cellspacing='0' >
                        <tr>
                            <td align='left' valign='top' class='style3'><span class='style2'>Date :</span> {{ date("d-m-Y", strtotime(date('Y-m-d'))) }}</td>
                        </tr>
                        <tr>
                            <td align='left' valign='top' class='style3'>&nbsp;</td>
                        </tr>
                        <tr>
                            <td align='left' valign='top' class='style3'>Dear {{ $orderDetails['getuser']['name'] }},</td>
                        </tr>
                        <tr>
                            <td align='left' valign='top' class='style3'>&nbsp;</td>
                        </tr>
						
                        <tr>
                            <td align='left' valign='top' class='style3'>Thank you for shopping at <a target="_black" style="color:#AA7450;text-decoration:none" href="{{ route('home') }}">{{ route('home') }}</a> the delivery of the book will take upto 7 working days. For any query, write to us at <a style="color:#AA7450;text-decoration:none" href="mailto:{{ $websettings['site_email'] }}"> {{ $websettings['site_email'] }}</a></a></td>
                        </tr>
                        <tr>
                            <td align='left' valign='top' class='style3'>&nbsp;</td>
                        </tr>
                       
                        <tr>
                            <td align='left' valign='top' class='style3'>Payment Method: @if($orderDetails['payment_method'] == 'cod') Cash on Delivery @else {{ $orderDetails['payment_method'] }} @endif  </td>
                        </tr>
						<tr>
                            <td align='left' valign='top' class='style3'>&nbsp;</td>
                        </tr>
                        <tr>
                            <td align='left' valign='top' class='style3'>Order Status: {{$orderDetails['order_status']}}</td>
                        </tr>
                        <tr>
                            <td align='left' valign='top' class='style3'>&nbsp;</td>
                        </tr>
                        <tr>
                            <td align='left' valign='top' class='style3'><strong>Your order has been placed with the following details<strong>: </td>
                        </tr>
                        <tr>
                            <td align='left' valign='top' class='style3'>&nbsp;</td>
                        </tr>
						 <tr>
                            <td align='left' valign='top' class='style3'><strong>Order No</strong>: {{$orderDetails['id']}}</td>
                        </tr>
						<tr>
                            <td align='left' valign='top' class='style3'></td>
                        </tr>
						
                        <tr>
                            <td align='left' valign='top' class='style3'>
                                <table width='95%' border='0' align='left' cellpadding='3' cellspacing='1' bgcolor='ACA899'>
                                    <tr>
                                        <td width='20%' align='center' valign='top' class='style2' bgcolor='#cccccc'>Product Name</td>
                                        <td width='15%' align='center' valign='top' class='style2' bgcolor='#cccccc'>Image</td>
                                       
                                        <td width='15%' align='center' valign='top' class='style2' bgcolor='#cccccc'>Size</td>
                                        <td width='15%' align='center' valign='top' class='style2' bgcolor='#cccccc'>Quantity</td>
                                        <td width='20%' align='center' valign='top' class='style2' bgcolor='#cccccc'>Price</td>
                                        <td width='20%' align='center' valign='top' class='style2' bgcolor='#cccccc'>Subtotal</td>
                                    </tr> 
                                    @foreach($orderDetails['order_products'] as $pro)  
                                    <tr>
                                        <td align='center' valign='top' class='style3' bgcolor='#F7F7F7'>
										<a target="_block" style="text-decoration: none;color: #666666;" href="{{ route('product',[$pro['productdetail']['product_url'],$pro['productdetail']['id']]) }}">
										{{ ucfirst($pro['product_name']) }}
										</a>
                                        </td>
                                        <td align='center' valign='top' class='style3' bgcolor='#F7F7F7'>
										 
										 @if($pro['productdetail']['product_image']  != '')
										<a  target="_block" href="{{ route('product',[$pro['productdetail']['product_url'],$pro['productdetail']['id']]) }}">
										<img src="{{ asset('front/images/products/small/'.$pro['productdetail']['product_image']['image']) }}">
										</a>
										 @endif
										</td>
                                        <td align='center' valign='top' class='style3' bgcolor='#F7F7F7'>{{ $pro['product_size'] }}</td>
                                        
										<td align='center' valign='top' class='style3' bgcolor='#F7F7F7'>{{ $pro['product_qty'] }}</td>
                                        <td align='center' valign='top' class='style3' bgcolor='#F7F7F7'> {{ amount_format($pro['product_price']) }}</td>
                                        <td align='center' valign='top' class='style3' bgcolor='#F7F7F7'>{{ amount_format($pro['sub_total']) }}</td>
                                    </tr>
                                    @endforeach
                                    @if($orderDetails['coupon_discount']>0)
                                         <tr>
                                            <td colspan='5' align='right' valign='top' class='style3' bgcolor='#F7F7F7'>Coupon Code</td>
                                            <td align='right' valign='top' class='style3' bgcolor='#F7F7F7'> <p style="color: red;">{{ $orderDetails['coupon_code'] }} </p></td>
                                        </tr>
                                    @endif
                                    @if($orderDetails['coupon_discount']>0)
                                        <tr>
                                            <td colspan='5' align='right' valign='top' class='style3' bgcolor='#F7F7F7'>Discount</td>
                                            <td align='right' valign='top' class='style3' bgcolor='#F7F7F7'> {{ amount_format($orderDetails['coupon_discount']) }}</td>
                                        </tr>
                                    @endif
                                    @if($orderDetails['payment_method'] == "cod")
                                        <tr>
                                            <td colspan='5' align='right' valign='top' class='style3' bgcolor='#F7F7F7'>COD Charges (If Any)</td>
                                            <td align='right' valign='top' class='style3' bgcolor='#F7F7F7'> {{ amount_format($orderDetails['cod_charges']) }}</td>
                                        </tr>
                                    @endif
                                    @if($orderDetails['shipping_charges']>0)
                                        <tr>
                                            <td colspan='5' align='right' valign='top' class='style3' bgcolor='#F7F7F7'>Shipping Charges</td>
                                            <td align='right' valign='top' class='style3' bgcolor='#F7F7F7'> {{ amount_format($orderDetails['shipping_charges']) }}</td>
                                        </tr>
                                    @endif
                                   
                                    <tr>
                                        <td colspan='5' align='right' valign='top' class='style3' bgcolor='#F7F7F7'>Grand Total</td>
                                        <td align='right' valign='top' class='style3' bgcolor='#F7F7F7'><strong> {{ amount_format($orderDetails['grand_total']) }} </strong></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        
						<tr>
                            <td>
                                <table width='100%'>
                                    <tr>
                                        <td width='50%'>
                                            <table width='100%' border='0' align='left' cellpadding='3' cellspacing='0'>
                                                <tr class='shop'>
                                                    <td colspan='2' align='left' valign='middle' class='style3' ><span class='top_text1'><strong>Bill To: -</strong></span></td>
                                                </tr>
                                                <tr>
                                                    <td colspan='2' align='left' valign='middle' class='style3'>{{ $orderDetails['order_address']['billing_name'] }}</td>
                                                </tr>
                                                <tr>
                                                    <td colspan='2' align='left' valign='middle' class='style3'>{{ $orderDetails['order_address']['billing_address'] }}</td>
                                                </tr>
												@if($orderDetails['order_address']['billing_address_line2'] !='')
												<tr>
                                                    <td colspan='2' align='left' valign='middle' class='style3'>{{ $orderDetails['order_address']['billing_address_line2'] }}</td>
                                                </tr>
												@endif
                                                <tr>
                                                    <td colspan='2' align='left' valign='middle' class='style3'>{{ $orderDetails['order_address']['billing_city'] }}</td>
                                                </tr>
                                                <tr>
                                                    <td colspan='2' align='left' valign='middle' class='style3'>{{ $orderDetails['order_address']['billing_state'] }}</td>
                                                </tr>
                                                <tr>
                                                    <td colspan='2' align='left' valign='middle' class='style3'>{{ $orderDetails['order_address']['billing_postcode'] }}</td>
                                                </tr>
                                                <tr>
                                                    <td align='left' valign='middle' class='style3'>{{ $orderDetails['order_address']['billing_country'] }}</td>
                                                </tr>
                                                <tr>
                                                    <td align='left' valign='middle' class='style3'>{{ $orderDetails['order_address']['billing_mobile'] }}</td>
                                                </tr>
                                                <tr>
                                                    <td align='left' valign='middle' class='top_text1'></td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td width='39%'>
                                            <table width='100%' border='0' align='left' cellpadding='3' cellspacing='0'>
                                                <tr class='shop'>
                                                    <td colspan='2' align='left' valign='middle' class='style3' ><span class='style2'><strong>Ship To: -</strong></span></td>
                                                </tr>
                                                <tr>
                                                    <td colspan='2' align='left' valign='middle' class='style3'>{{ $orderDetails['order_address']['shipping_name'] }}</td>
                                                </tr>
                                                <tr>
                                                    <td colspan='2' align='left' valign='middle' class='style3'>{{ $orderDetails['order_address']['shipping_address'] }}</td>
                                                </tr>
												@if($orderDetails['order_address']['shipping_address_line2'] !='')
												<tr>
                                                    <td colspan='2' align='left' valign='middle' class='style3'>{{ $orderDetails['order_address']['shipping_address_line2'] }}</td>
                                                </tr>
												@endif
                                                <tr>
                                                    <td colspan='2' align='left' valign='middle' class='style3'>{{ $orderDetails['order_address']['shipping_city'] }}</td>
                                                </tr>
                                                <tr>
                                                    <td colspan='2' align='left' valign='middle' class='style3'>{{ $orderDetails['order_address']['shipping_state'] }}</td>
                                                </tr>
                                                <tr>
                                                    <td colspan='2' align='left' valign='middle' class='style3'>{{ $orderDetails['order_address']['shipping_postcode'] }}</td>
                                                </tr>
                                                <tr>
                                                    <td align='left' valign='middle' class='style3'>{{ $orderDetails['order_address']['shipping_country'] }}</td>
                                                </tr>
                                                <tr>
                                                    <td align='left' valign='middle' class='style3'>{{ $orderDetails['order_address']['shipping_mobile'] }}</td>
                                                </tr>
                                                <tr>
                                                    <td height='5' align='left' valign='middle' class='top_text1'></td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td align='left' valign='top' class='style3'>
                                <table width='90%' border='0' align='left' cellpadding='3' cellspacing='0'>
                                    <tr>
                                    </tr>
                                    <tr>
                                        <td colspan='3' class='style3'>&nbsp;</td>
                                    </tr>
									
									
									 <tr>
                                        <td colspan='3' class='style3'>Please keep this email for future reference & don’t forget to add us to your favourites to make it easier to come back and view our latest product range and offers.</td>
                                    </tr>

                                     <tr>
                                        <td height='16' colspan='3' class='style3'>
                                            &nbsp;
                                        </td>
                                    </tr>
									
									
                                    <tr>
                                        <td colspan='3' class='style3'>
                                            If you have any enquiries or wish to contact us please use the following link: <br><br> <a href="mailto:{{ $websettings['site_email'] }}"> {{ $websettings['site_email'] }}</a> 
                                        </td>
                                    </tr>
                                    <tr>
                                        <td height='16' colspan='3' class='style3'>
                                            &nbsp;
                                        </td>
                                    </tr>
                                    <tr>
                                        <td height='16' colspan='3' class='style3'>
                                            <div align='left'>   
                                                Regards {{ $websettings['site_name'] }}
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
</html>
