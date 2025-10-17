
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
            <td class='style2'>Dear Customer,</td>
         </tr>
         <tr>
            <td class='style2'>
               Your OTP is {{ $data['otp'] }}.<br>
               Please use this code to complete your verification.
            </td>
         </tr>
         <tr>
            <td>&nbsp;</td>
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
