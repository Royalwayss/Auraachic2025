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
                            <td align='left' valign='top' class='style3'>&nbsp;</td>
                        </tr>
						
                        <tr>
                            <td align='left' valign='top' class='style3'>
							@if($returnhistory['return_status'] == 'Accepted')
							  We are pleased to inform you that your return request for #{{$orderDetails['id'] }} has been approved.
							@else
							  We regret to inform you that your return request for {{$order_product['product_name'] }} (Order ID: {{ $orderDetails['id'] }}) has been rejected.
							@endif
							</td>
                        </tr>
                       
                        
						<tr>
                            <td align='left' valign='top' class='style3'>&nbsp;</td>
                        </tr>
                        <tr>
                            <td align='left' valign='top' class='style3'>Order ID: {{ @$orderDetails['id'] }}</td>
                        </tr>
                        <tr>
                            <td align='left' valign='top' class='style3'>&nbsp;</td>
                        </tr>
                        <tr>
                            <td align='left' valign='top' class='style3'>Product Name: {{ @$order_product['product_name'] }}</td>
                        </tr>
						 <tr>
                            <td align='left' valign='top' class='style3'>&nbsp;</td>
                        </tr>
                        <tr>
                            <td align='left' valign='top' class='style3'>Product Code: {{ @$order_product['product_code'] }}</td>
                        </tr>
                        <tr>
                            <td align='left' valign='top' class='style3'>&nbsp;</td>
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
    </body>
</html>
