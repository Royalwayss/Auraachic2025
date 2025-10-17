<?php  
use App\Models\WebSetting;
$websettings = WebSetting::settings();
?> 
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

                <td  align='left' valign='middle'>
				<a target="_black" href="{{ url(route('home')) }}">
				<img border='0' class="site_logo" src="{{ $websettings['site_logo'] }}"  style="width: 75px!important;" alt='logo' />
				</a>
				</td>

            </tr>

            <tr>

                <td>&nbsp;</td>

            </tr>
			<tr>

                <td class='style2'>Hi admin,</td>

            </tr>
			 <tr>

                <td class='style2'>Bulk enquiry has been recived from '.config('constants.project_name').Enquiry details is as below :-</td>

            </tr>
			

            <tr>

                <td>&nbsp;</td>

            </tr>           

            <tr>

               <td align='left' valign='middle'>

                   <table width='98%' border='0' align='right' cellpadding='5' cellspacing='5' style='background-color:#F5F5F5'>

                        <tr>

                           <td width='30%' align='left' valign='top' class='style2'>Name:</td>

                           <td width='5%' align='left' valign='top' class='style2'>:</td>

                           <td width='65%' align='left' valign='top' class='style3'>{{ $data['name'] }}</td>

                        </tr>

                        <tr>

                           <td width='30%' align='left' valign='top' class='style2'>Email:</td>

                           <td width='5%' align='left' valign='top' class='style2'>:</td>

                           <td width='65%' align='left' valign='top' class='style3'>{{ $data['email'] }}</td>

                        </tr>
						
						<tr>

                           <td width='30%' align='left' valign='top' class='style2'>Mobile:</td>

                           <td width='5%' align='left' valign='top' class='style2'>:</td>

                           <td width='65%' align='left' valign='top' class='style3'>{{ $data['mobile'] }}</td>

                        </tr>
						
						<tr>

                           <td width='30%' align='left' valign='top' class='style2'>City:</td>

                           <td width='5%' align='left' valign='top' class='style2'>:</td>

                           <td width='65%' align='left' valign='top' class='style3'>{{ $data['city'] }}</td>

                        </tr>
						
						<tr>

                           <td width='30%' align='left' valign='top' class='style2'>Address:</td>

                           <td width='5%' align='left' valign='top' class='style2'>:</td>

                           <td width='65%' align='left' valign='top' class='style3'>{{ $data['address'] }}</td>

                        </tr>
						
						<tr>

                           <td width='30%' align='left' valign='top' class='style2'>Enquiry:</td>

                           <td width='5%' align='left' valign='top' class='style2'>:</td>

                           <td width='65%' align='left' valign='top' class='style3'>{{ $data['enquiry'] }}</td>

                        </tr>



                   </table>

               </td>

           </tr>
          
           <tr>

                <td>&nbsp;</td>

            </tr>
            
			
            <tr>

                <td  class='style2'>
                   
				   
				  If you need any help you can {{config('constants.project_name')}} <br>
				  
				  drop us an email:<a style="color:#AA7450;text-decoration:none" href="mailto:{{ $websettings['site_email']  }}">{{ $websettings['site_email']  }}</a> <br>

   				   

                </td>
             </tr>
             <tr>

                 <td  class='style2'> Regards<br />
                   Team {{ $websettings['site_name'] }} 
				   
				  </td>
			</tr>	  
				 

   				  
                    
                    

              






            </tr>
			
			
			
			

        </table>

    </body>

</html>
