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
          <td width="30%" valign="top">
            <p class="order_number"><span class="bold small">'._("Order No.").'</span> <span>'.$order->id.'</span></p>
            <br>
            <span>'.$order->firstname_c.' '.$order->lastname_c.'</span>
            <br/>
            <span>'.$add.'</span> <span>'.$house.'</span>

            <br/>
            <span>'.$city.'</span>

            <br/>
            <span>'.$phone.'</span>

            <br />
            <span class="bold small">'.$order->email_c.'</span>
          </td>
          <td width="50%" valign="top" >';
		foreach($order->order_details as $order_detail)
		{
			$quantity='';
	  	 	if($order_detail->content_type != '1')
	  	 	{
			  	$quantity= ($order_detail->quantity);
			}
		  	else
		  	{
		  	 	$quantity=($order_detail->quantity/1000);
		  	}
		    $con_type='';
            if($order_detail->content_type == '1')
            {
             	$con_type=' '._("kg");
            }
            elseif($order_detail->content_type == '2')
            {
             	$con_type=' '._("Person");
            }
            $pro='';
			if($order_detail->pro_remark)
			{
                $pro='<br /><span>'.$order_detail->pro_remark.'</span>';
            }

            $price=_("Price");
          	$html1.='<span class="bold medium">'.$quantity.$con_type.' x '.$order_detail->proname.'</span> <span>'.$price.':'.$order_detail->quantity.' x '.defined_money_format($order_detail->default_price).' &euro; = '.defined_money_format($order_detail->total).'&euro;</span>'.$pro.'<br/>';
        }
       	$pic='';
       	$month='';
       	$dte='';
		if($order->order_pickupdate != "0000-00-00")
		{
			$pic="Pickup";
			$month = date("F",strtotime($order->order_pickupdate));
		    $dte=date("d",strtotime($order->order_pickupdate));
		}
		else
		{
			$pic="Delivery";
			$month = date("F",strtotime($order->delivery_date));
			$dte=date("d",strtotime($order->delivery_date));
		}
       $html1.='</td>
     			<td width="20%" valign="top">
      				<span class="bold medium underline">'.$pic.'
        			</span>
        			<p class="order_date"><span>'.$dte;
		if(isset($_COOKIE['locale']) && $_COOKIE['locale'] == 'nl_NL')
		{
		  	if( $month == "January" )
		  	{
			 	$month = 'Januari';
		 	}
		  	if( $month == "February" )
		  	{
			  	$month = 'Februari';
		  	}
		  	if( $month == "March" )
		  	{
			  	$month = 'Maart';
		  	}
		  	if( $month == "May" )
		  	{
		  		$month = 'Mei';
		 	}
		  	if( $month == "June" )
		  	{
			  	$month = 'Juni';
		  	}
		  	if( $month == "July" )
		  	{
			 	$month = 'Juli';
		  	}
		  	if( $month == "August" )
		  	{
			  	$month = 'Augustus';
		  	}
		  	if( $month == "October" )
		  	{
			  	$month = 'Oktober';
		  	}
		}
	  	$html1.= ' '.$month.' ';

	  	if($order->order_pickupdate != "0000-00-00")
	  	{
		 	$html1.= "om ".$order->order_pickuptime;
	  	}
	  	else
	  	{
		 	$html1.="om ".$order->delivery_hour.":".$order->delivery_minute;
	  	}

        $html1.='</span>
        </p>
        <br> <br>';
        $finalTotal= $finalTotal + $order->order_total;
        $t=_("Total");
        $html1.='<span class="bold large" style="display:block; text-align: right;">'.$t.' = '.defined_money_format($order->order_total).' &euro;</span></td></tr>';
         $mpdf->WriteHTML($html1);
    }

    $t=_('Total');
    $html2='<tr>
            	<td colspan="3" style="text-align:right;border-top: 2px solid #000000;">
            		<span class="bold large">'.$t.' : '.defined_money_format($finalTotal).' &euro;</span>
            	</td>
    		</tr>
		</table>
		</div>';
	$mpdf->WriteHTML($html2);
}
?>