<?php 
$html='';
if(!empty($orderData))
{ 
  $finalTotal = 0;
  $html.='<div class="summary">';
  $html.='<table cellspacing="0" border="0" id="prod_list" width="100%">';
  $mpdf->WriteHTML($html);
	foreach($orderData as $order) 
  {
    $add='';
    $house='';
    if($order->address_c != '')
    {
            $add=$order->address_c;
            $house=$order->housenumber_c;
    }
    $city='';
    if($order->city_c != '')
    {
        $city=$order->city_c;
    }
    $phone='';
    if($order->mobile_c != '' || $order->phone_c != '')
    {
      $phone=(($order->mobile_c)?$order->mobile_c:$order->phone_c);
    }
    $html1='<tr>
              <td width="20%" valign="top">
                <p class="order_number"><span class="bold small">'._("Order No.").'</span> <span>'.$order->id.'</span></p>
                <span>'.date("d-m-Y", strtotime($order->created_date)).'</span>
              </td>
              <td width="20%">
              	<span>'.$order->firstname_c.' '.$order->lastname_c.'</span>
                <br/>
                <span>'.$add.'</span> <span>'.$house.'</span>
              </td>
              <td width="20%">
              	<span>'.$order->company_name.'</span>
              	<br>
              	
              </td>
              <td width="20">
                <span>'.$phone.'</span>
                <br />
                <span>'.$order->email_c.'</span>
              </td>
              <td wisth="20%">';
    $finalTotal = $finalTotal + $order->order_total;
    $html1.='<span valign="top">'.defined_money_format($order->order_total).' &euro;<br>(incl btw)</span>
              </td>
            </tr>';
    $mpdf->WriteHTML($html1);
  } 
  $html2=' <tr>
  	       <td colspan="5" style="text-align:right;border-top: 2px solid #000000;">
  		     <span class="bold large">'._("Total").' : '.defined_money_format($finalTotal).' &euro; (incl btw)</span>
  	       </td>
           </tr>
	     </table>
      </div>';
  $mpdf->WriteHTML($html2);
}