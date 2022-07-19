<?php
					
	$client_order_count = array();

    if(!empty($orders))
	foreach($orders as $key=>$val)
	{
		if(!empty($client_order_count))
		{
			if( array_key_exists($orders[$key]->client_id,$client_order_count) )
			{
			   $count = $client_order_count[$orders[$key]->client_id]+1;
			   $client_order_count[$orders[$key]->client_id] = $count;
			}
			else
			{
			   $client_order_count[$orders[$key]->client_id] = 1;
			}
		}
		else
		{
			$client_order_count[$orders[$key]->client_id] = 1;
		}
	}

?>
<?php
					
	$client_order_count_n = array();

    if(!empty($orders_n))
	foreach($orders_n as $key=>$val)
	{
		if(!empty($client_order_count_n))
		{
			if( array_key_exists($orders_n[$key]->client_id,$client_order_count_n) )
			{
			   $count = $client_order_count_n[$orders_n[$key]->client_id]+1;
			   $client_order_count_n[$orders_n[$key]->client_id] = $count;
			}
			else
			{
			   $client_order_count_n[$orders_n[$key]->client_id] = 1;
			}
		}
		else
		{
			$client_order_count_n[$orders_n[$key]->client_id] = 1;
		}
	}

?>
<?php 
    
	$msg_set = false;
	
	if(!empty($client_order_count))
	foreach($client_order_count as $c)
	{
	  if($c==1)
	  {  
	     $msg_set = true;
		 break;
	  }
    }
	
	if(!empty($client_order_count_n))
	foreach($client_order_count_n as $c)
	{
	  if($c==1)
	  {  
	     $msg_set = true;
		 break;
	  }
    }
?>
<script type="text/javascript">
jQuery(document).ready(function($){
   $('#chk_all').click(function(){
       
	    if( document.getElementById('chk_all').checked == true )
		{
		   $('.chk_all').attr('checked', true);
		}
		else
		{
		   $('.chk_all').attr('checked', false);
		}
	   
   });

   $('.number_update').on('click',function(){
	   var client_id = $(this).parent().find('#client_id').val();
	   var company_id = $(this).parent().find('#company_id').val();
	   var c_number = $(this).parent().find('#client_number').val();
	   var div_id = $(this).parent().find('#div_id').val();
	   $.post('<?php echo base_url();?>cp/cdashboard/update_client_number',
			   {'client_id':client_id,'company_id':company_id,'client_number':c_number},
			   function(response){
				   	alert(response);
				   },
			   '');
	   $(this).parent().find('#client_number').val(c_number);
	   //alert(client_id);
	   //alert(company_id);
	});
});
</script>
<style>
	#TB_window #TB_ajaxContent{
		height: 450px !important;
	}
</style>
<div id="main">
	<div id="main-header" style="padding-bottom:5px;">
 		<h2><?php echo _('IBSoft - OBS Module'); ?></h2>
      	<span class="breadcrumb"><a href="<?php echo base_url()?>cp/cdashboard"><?php echo _('Home'); ?></a> &raquo; <?php echo _('Orders'); ?></span>
		
		<?php if($msg_set) { ?>
		   <br /><br />
		   <div id="succeed"><strong><?php echo _('A NEW CLIENT')?></strong>&nbsp;<?php echo _('has registered - Please download client list first and implement in your IB Software.'); ?></div>
		<?php } ?>
		
		<?php $messages = $this->messages->get();?>
		<?php if($messages != array()): foreach($messages as $type => $message):?>
			
			<?php if($type == 'success' && $message != array()):?>
				<br /><br />
				<div id="succeed"><strong><?php echo _('Succeed')?></strong>:<?php echo $message[0];?></div>
			<?php elseif($type == 'error' && $message != array()):?>	
				<br /><br />
				<div id="error"><strong><?php echo _('Error')?></strong>:<?php echo $message[0];?></div>	
			<?php endif;?>
			
		<?php endforeach; endif;?>
		
	</div>
	
	<div id="content">
    	<div id="content-container">
        	<div class="box">
          		<h3><?php echo _("Search Orders");?></h3>
				<div class="table">
            		<form name="frm_search" id="frm_search" action="<?php echo base_url()?>cp/cdashboard/ibsoft_module" method="post">
            			<table cellspacing="0" border="0" width="90%" cellpadding="0">
	            			<tbody>
	                			<tr>
	                  				<td colspan="2" width="22%"><strong><?php echo _('Display all orders for')?></strong></td>
	                  				<td valign="bottom" align="justify" width="30%">
										<div style="float:left"><input type="text" class="text" readonly="readonly" name="start_date" id="start_date"></div>
					  					<div style="float:left"><input type="button" value="..." onclick="displayCalendar(document.frm_search.start_date,'dd/mm/yyyy',this)" name="button1" id="button1"></div>
									</td>
	                   				<!-- <td valign="bottom" align="justify" width="30%">
										<div style="float:left"><input type="text" class="text" readonly="readonly" name="end_date" id="end_date"/></div>
										<div style="float:left"><input type="button" value="..." onclick="displayCalendar(document.frm_search.end_date,'dd/mm/yyyy',this)" name="button2" id="button2"/></div>
	                  				</td> -->
	                 				<td valign="middle" width="20%"><input type="submit" class="submit" value="<?php echo _('Search')?>" name="btn_search" id="btn_search"/>
		                    			<input type="button" class="submit" value="<?php echo _('Reset')?>" onclick="this.form.reset();" name="btn_reset" id="btn_resset"/>
		                    			<input type="hidden" value="do_filter" name="act" id="act"/>
		                    			<input type="hidden" value="orders" name="view" id="view"/>
									</td>
	                  				<td width="0%">&nbsp;</td>
	                			</tr >
	              			</tbody>
              			</table>
            		</form>
            		<script language="JavaScript" type="text/javascript">
						var frmvalidator = new Validator("frm_search");
						frmvalidator.EnableMsgsTogether();
						frmvalidator.addValidation("start_date","req","<?php echo _('Please enter a date')?>");
						//frmvalidator.addValidation("end_date","req","<?php echo _('Please enter end date')?>");
					</script>
					<form name="frm_set" id="frm_set" action="<?php echo base_url()?>cp/cdashboard/ibsoft_module" method="post">
					    <table cellspacing="0" border="0" width="90%" cellpadding="0">
							<tbody>
	                			<tr>
								   <td><strong><?php echo _('Or - Print all ordered products for')?></strong></td>
								   <td>
								     <input type="hidden" name="date_tomorrow" value="<?php echo date('Y-m-d',strtotime( date('Y-m-d H:i:s',time())." +1 day" )); ?>">
								     <input type="submit" name="tomorrow" value="<?php echo _('Tomorrow'); ?> (<?php echo date('d/m/y',strtotime( date('Y-m-d H:i:s',time())." +1 day" )); ?>)">
								   </td>
								   <td> <strong><?php echo _('OR'); ?></strong> </td>
								   <td>
								     <input type="hidden" name="date_after_tomorrow" value="<?php echo date('Y-m-d',strtotime( date('Y-m-d H:i:s',time())." +2 day" )); ?>">
								     <input type="submit" name="day_after_tomorrow" value="<?php echo _('Day After Tomorrow'); ?> (<?php echo date('d/m/y',strtotime( date('Y-m-d H:i:s',time())." +2 day" )); ?>)">
								   </td>
								</tr>
							</tbody>
						</table>
					</form>
          		</div><!------END OF TABLE DIV------>
        	</div><!--------END OF BOX DIV--------->
			<?php if(!$first_load){?>
			<div class="box">
				<h3><?php echo _("Orders Information")?></h3>
				<div class="table">
				
				    <form name="export_all" id="export_all" action="<?php echo base_url()?>cp/cdashboard/ibsoft_module" method="post">
					
					<?php  if(count($orders)>0) { ?>
					<table cellspacing="0" border="0" id="order_content">
						<thead>
						   <tr>
						      <th><input type="checkbox" name="chk_alls" id="chk_all" value="" /></th>
							  <th><?php echo _('Date'); ?></th>
							  <th><?php echo _('Name'); ?></th>
							  <th><?php echo _('Total'); ?></th>
							  <th><?php echo _('Take Away'); ?></th>
							  <th><?php echo _('Delivery'); ?></th>
							  <th><?php echo _('Status'); ?></th>
						   </tr>
						</thead>
					<tbody>					
					<?php  for($i=0; $i<count($orders); $i++) { ?>
					   
					<?php 
					          $red_dot = (!empty($client_order_count) && isset($client_order_count[$orders[$i]->client_id]) && $client_order_count[$orders[$i]->client_id]==1)?1:0;
							  
							  $order_total = ((float)$orders[$i]->order_total)+((float)$orders[$i]->delivery_cost);
							  
							  if($orders[$i]->order_pickuptime != ""){
		
								$pickup_content = date("d / m / y",strtotime($orders[$i]->order_pickupdate))." ".$orders[$i]->order_pickuptime;
								
								$pickup_day = date("D",strtotime($orders[$i]->order_pickupdate));
								
								if(isset($_COOKIE['locale']) && $_COOKIE['locale'] == 'nl_NL')
								{
								   if( $pickup_day == 'Mon' )
									 $pickup_day = 'Ma';
								   if( $pickup_day == 'Tue' )
									 $pickup_day = 'Di';
								   if( $pickup_day == 'Wed' )
									 $pickup_day = 'Wo';
								   if( $pickup_day == 'Thu' )
									 $pickup_day = 'Da';
								   if( $pickup_day == 'Fri' )
									 $pickup_day = 'Vr';
								   if( $pickup_day == 'Sat' )
									 $pickup_day = 'Za';
								   if( $pickup_day == 'Sun' )
									 $pickup_day = 'Zo';
								}
								
								$pickup_content = $pickup_day.' '.$pickup_content;
								
							  }else{
								  $pickup_content = "--";
							  }
							  
							  /*===================================*/
							
							/*=======delivery_content============*/
							if($orders[$i]->delivery_streer_address!= ""){
																
								$delivery_address_content = _('Address').' : '.$orders[$i]->delivery_streer_address.'<br />';
								if(isset($orders[$i]->delivery_area_name) || isset($orders[$i]->delivery_city_name)){
									$delivery_address_content .= _('Area').' : '.$orders[$i]->delivery_city_name.', '.$orders[$i]->delivery_area_name.'<br />';
								}	
								
								if($orders[$i]->delivery_zip)
								{
									$delivery_address_content .= _('Zipcode').' : '.$orders[$i]->delivery_zip.'<br />';
								
									$delivery_date_content =date("d/m",strtotime($orders[$i]->delivery_date))." ".$orders[$i]->delivery_hour.":".$orders[$i]->delivery_minute;
									
									$delivery_day = date("D",strtotime($orders[$i]->delivery_date));
								
									if(isset($_COOKIE['locale']) && $_COOKIE['locale'] == 'nl_NL')
									{
									   if( $delivery_day == 'Mon' )
										 $delivery_day = 'Ma';
									   if( $delivery_day == 'Tue' )
										 $delivery_day = 'Di';
									   if( $delivery_day == 'Wed' )
										 $delivery_day = 'Wo';
									   if( $delivery_day == 'Thu' )
										 $delivery_day = 'Da';
									   if( $delivery_day == 'Fri' )
										 $delivery_day = 'Vr';
									   if( $delivery_day == 'Sat' )
										 $delivery_day = 'Za';
									   if( $delivery_day == 'Sun' )
										 $delivery_day = 'Zo';
									}
									
									$delivery_address_content = $delivery_address_content.' '.$delivery_day.' '.$delivery_date_content;
								}
							
							}else{
								$delivery_address_content =  "--"; 
							}
					   ?>					   
					   <tr>
					      <td><input type="checkbox" name="chk_all[]" class="chk_all" value="<?php echo $orders[$i]->id; ?>" /></td>
						  <td><?php echo date("d/m/y",strtotime($orders[$i]->created_date)); ?></td>
						  <td>
						  	<a href="#TB_inline?height=500&width=500&inlineId=detail_<?php echo $i.'_'.$orders[$i]->id; ?>" title="Account Details" class="thickbox"><?php echo $orders[$i]->firstname_c.' '.$orders[$i]->lastname_c; ?></a><?php if($red_dot==1) { echo '&nbsp;<img src="'.base_url().'assets/cp/images/red_dot.gif" width="5px" title="'._('New Client Ordered').'">'; } ?>
						  	<div id="detail_<?php echo $i.'_'.$orders[$i]->id; ?>" style="display: none;">
						  		<div class="table">
				            		<table border="0">
				              			<tbody>
							     			<tr>
				                  				<td class="textlabel"><span style="padding-left:20px"><?php echo _('Created Date')?> </span></td>
				                  				<td><?php echo mdate('%d-%m-%Y',human_to_unix($orders[$i]->created_c));?></td>
				
				                			</tr>
				                			<tr>
				                  				<td class="textlabel"><span style="padding-left:20px"><?php echo _('Created Time')?></span></td>
				                  				<td><?php echo mdate('%h:%i %a',human_to_unix($orders[$i]->created_c));?></td>
				                			</tr>
				                			<tr>
				                  				<td class="textlabel"><span style="padding-left:20px"><?php echo _('First Name')?></span></td>
				                 				<td><?php echo $orders[$i]->firstname_c;?></td>
				                			</tr>
				                			<tr>
				                  				<td class="textlabel"><span style="padding-left:20px"><?php echo _('Lasr Name')?></span></td>
				                  				<td><?php echo $orders[$i]->lastname_c;?></td>
				                			</tr>
				                			<tr>
							                  <td class="textlabel"><span style="padding-left:20px"><?php echo _('Business')?></span></td>
				            			      <td><?php echo $orders[$i]->company_c;?></td>
				                			</tr>
				                            <tr>
				            			    	<td class="textlabel"><span style="padding-left:20px"><?php echo _('Address')?></span></td>
				                  				<td><?php echo $orders[$i]->address_c?></td>
				                			</tr>
				
				                			<tr>
				                  				<td class="textlabel"><span style="padding-left:20px"><?php echo _('House number')?></span></td>
				                  				<td><?php echo $orders[$i]->housenumber_c?></td>
				                			</tr>
				                             <tr>
				                  				<td class="textlabel"><span style="padding-left:20px"><?php echo _('Postal Code')?></span></td>
								                <td><?php echo $orders[$i]->postcode_c?></td>
				                			</tr>
				
				                			<tr>
				                  				<td class="textlabel"><span style="padding-left:20px"><?php echo _('City')?></span></td>
				                  				<td><?php echo $orders[$i]->city_c?></td>
				
				                			</tr>
				                			<tr>
				                  				<td class="textlabel"><span style="padding-left:20px"><?php echo _('Country')?></span></td>
				                  				<td><?php echo $orders[$i]->country_name?></td>
				                			</tr>
				                			<tr>
				                  				<td class="textlabel"><span style="padding-left:20px"><?php echo _('Telephone')?></span></td>
				                  				<td><?php echo $orders[$i]->phone_c?></td>
				                			</tr>
				                			<tr>
								                <td class="textlabel"><span style="padding-left:20px"><?php echo _('GSM')?></span></td>
				                				<td><?php echo $orders[$i]->mobile_c?></td>
				                			</tr>
				                			<tr>
				                  				<td class="textlabel"><span style="padding-left:20px"><?php echo _('FAX')?></span></td>
				                  				<td><span style="padding-right:250px"><?php echo $orders[$i]->fax_c;?></span></td>
				                			</tr>
				                			<?php //if($is_set_discount_card_setting){?>
				                			<!-- <tr>
				                  				<td class="textlabel"><span style="padding-left:20px"><?php echo _('Discount Card Number')?></span></td>
				                  				<td><span style="padding-right:250px"><?php echo $orders_n[$i]->discount_card_number;?></span></td>
				                			</tr> -->
				                			<?php //}?>
											<tr>
								                <td class="textlabel"><span style="padding-left:20px"><?php echo _('Client Number')?></span></td>
				                				<td>
												<input type="text" name="client_number" id="client_number" value="<?php echo $orders[$i]->client_number; ?>" />
												&nbsp;&nbsp;
												<input type="hidden" name="client_id" id="client_id" value="<?php echo $orders[$i]->client_id; ?>" />
												<input type="hidden" name="div_id" id="div_id" value="<?php echo $i; ?>" />
												<input type="hidden" name="company_id" id="company_id" value="<?php echo $orders[$i]->company_id; ?>" />
												<input type="button" name="add_client_no" id="add_client_no" class="number_update" value="<?php echo _('Update'); ?>" />
												</td>
				                			</tr>
				              			</tbody>
						            </table>
				          		</div>
						  	</div>
						  </td>
						  <td><?php echo $order_total; ?></td>
						  <td><?php echo $pickup_content; ?></td>
						  <td><?php echo $delivery_address_content; ?></td>
						  <td><?php if($red_dot==1){ echo 'New Client'; }else{ echo '--'; } ?></td>
					   </tr>
					   <?php } ?>
					</tbody>
					</table>
					<div style="background: none repeat scroll 0 0 #FAFAFA;border-top: 1px solid #E3E3E3;padding: 5px 10px;">
					  <input type="submit" name="export_and_send" id="export_and_send" value="<?php echo _('EXPORT AND SEND'); ?>" />
					</div>
					<?php } else { ?>	
					<p style="padding:10px;color:red;"><strong><?php echo _('No orders.'); ?></strong></p>
					<?php } ?>
					</form>
					<div style="clear:both"></div>
			     </div>
		      </div>

			  <div class="box">
				<h3><?php echo _("Orders With Invoice")?></h3>
				<div class="table">
					
					<?php  if(count($orders_n)>0) { ?>
					<table cellspacing="0" border="0" id="order_content">
					<thead>
					   <tr>
					      <!--<th><input type="checkbox" name="chk_all[]" id="chk_all" value="" /></th>-->
						  <th><?php echo _('Date'); ?></th>
						  <th><?php echo _('Name'); ?></th>
						  <th><?php echo _('Total'); ?></th>
						  <th><?php echo _('Take Away'); ?></th>
						  <th><?php echo _('Delivery'); ?></th>
						  <th><?php echo _('Status'); ?></th>
					   </tr>
					</thead>
					<tbody>				
					<?php  for($i=0; $i<count($orders_n); $i++) { ?>					   
					<?php 
					          $red_dot = (!empty($client_order_count_n) && isset($client_order_count_n[$orders_n[$i]->client_id]) && $client_order_count_n[$orders_n[$i]->client_id]==1)?1:0;
							  
							  $order_total = ((float)$orders_n[$i]->order_total)+((float)$orders_n[$i]->delivery_cost);
							  
							  if($orders_n[$i]->order_pickuptime != ""){
		
								$pickup_content = date("d / m / y",strtotime($orders_n[$i]->order_pickupdate))." ".$orders_n[$i]->order_pickuptime;
								
								$pickup_day = date("D",strtotime($orders_n[$i]->order_pickupdate));
								
								if(isset($_COOKIE['locale']) && $_COOKIE['locale'] == 'nl_NL')
								{
								   if( $pickup_day == 'Mon' )
									 $pickup_day = 'Ma';
								   if( $pickup_day == 'Tue' )
									 $pickup_day = 'Di';
								   if( $pickup_day == 'Wed' )
									 $pickup_day = 'Wo';
								   if( $pickup_day == 'Thu' )
									 $pickup_day = 'Da';
								   if( $pickup_day == 'Fri' )
									 $pickup_day = 'Vr';
								   if( $pickup_day == 'Sat' )
									 $pickup_day = 'Za';
								   if( $pickup_day == 'Sun' )
									 $pickup_day = 'Zo';
								}
								
								$pickup_content = $pickup_day.' '.$pickup_content;
								
							  }else{
								  $pickup_content = "--";
							  }
							  
							/*===================================*/
							
							/*=======delivery_content============*/
							if($orders_n[$i]->delivery_streer_address!= ""){
																
								$delivery_address_content = _('Address').' : '.$orders_n[$i]->delivery_streer_address.'<br />';
								if(isset($orders_n[$i]->delivery_area_name) || isset($orders_n[$i]->delivery_city_name)){
									$delivery_address_content .= _('Area').' : '.$orders_n[$i]->delivery_city_name.', '.$orders_n[$i]->delivery_area_name.'<br />';
								}	
								
								if($orders_n[$i]->delivery_zip)
								{
									$delivery_address_content .= _('Zipcode').' : '.$orders_n[$i]->delivery_zip.'<br />';
								
									$delivery_date_content =date("d/m",strtotime($orders_n[$i]->delivery_date))." ".$orders_n[$i]->delivery_hour.":".$orders_n[$i]->delivery_minute;
									
									$delivery_day = date("D",strtotime($orders_n[$i]->delivery_date));
								
									if(isset($_COOKIE['locale']) && $_COOKIE['locale'] == 'nl_NL')
									{
									   if( $delivery_day == 'Mon' )
										 $delivery_day = 'Ma';
									   if( $delivery_day == 'Tue' )
										 $delivery_day = 'Di';
									   if( $delivery_day == 'Wed' )
										 $delivery_day = 'Wo';
									   if( $delivery_day == 'Thu' )
										 $delivery_day = 'Da';
									   if( $delivery_day == 'Fri' )
										 $delivery_day = 'Vr';
									   if( $delivery_day == 'Sat' )
										 $delivery_day = 'Za';
									   if( $delivery_day == 'Sun' )
										 $delivery_day = 'Zo';
									}
									
									$delivery_address_content = $delivery_address_content.' '.$delivery_day.' '.$delivery_date_content;
								}
							
							}else{
								$delivery_address_content =  "--"; 
							}
					   ?>					   
					   <tr>
					      
					      <!--<td><input type="checkbox" name="chk_all[]" class="chk_all" value="<?php echo $orders_n[$i]->id; ?>" /></td>-->
						  <td><?php echo date("d/m/y",strtotime($orders_n[$i]->created_date)); ?></td>
						  <td>
						  	<a href="#TB_inline?height=500&width=500&inlineId=detail_<?php echo $i.'_'.$orders_n[$i]->id; ?>" title="Account Details" class="thickbox"><?php echo $orders_n[$i]->firstname_c.' '.$orders_n[$i]->lastname_c; ?></a><?php if($red_dot==1) { echo '&nbsp;<img src="'.base_url().'assets/cp/images/red_dot.gif" width="5px" title="'._('New Client Ordered').'">'; } ?>
						  	<div id="detail_<?php echo $i.'_'.$orders_n[$i]->id; ?>" style="display: none;">
						  		<div class="table">
				            		<table border="0">
				              			<tbody>
							     			<tr>
				                  				<td class="textlabel"><span style="padding-left:20px"><?php echo _('Created Date')?> </span></td>
				                  				<td><?php echo mdate('%d-%m-%Y',human_to_unix($orders_n[$i]->created_c));?></td>
				
				                			</tr>
				                			<tr>
				                  				<td class="textlabel"><span style="padding-left:20px"><?php echo _('Created Time')?></span></td>
				                  				<td><?php echo mdate('%h:%i %a',human_to_unix($orders_n[$i]->created_c));?></td>
				                			</tr>
				                			<tr>
				                  				<td class="textlabel"><span style="padding-left:20px"><?php echo _('First Name')?></span></td>
				                 				<td><?php echo $orders_n[$i]->firstname_c;?></td>
				                			</tr>
				                			<tr>
				                  				<td class="textlabel"><span style="padding-left:20px"><?php echo _('Lasr Name')?></span></td>
				                  				<td><?php echo $orders_n[$i]->lastname_c;?></td>
				                			</tr>
				                			<tr>
							                  <td class="textlabel"><span style="padding-left:20px"><?php echo _('Business')?></span></td>
				            			      <td><?php echo $orders_n[$i]->company_c;?></td>
				                			</tr>
				                            <tr>
				            			    	<td class="textlabel"><span style="padding-left:20px"><?php echo _('Address')?></span></td>
				                  				<td><?php echo $orders_n[$i]->address_c?></td>
				                			</tr>
				
				                			<tr>
				                  				<td class="textlabel"><span style="padding-left:20px"><?php echo _('House number')?></span></td>
				                  				<td><?php echo $orders_n[$i]->housenumber_c?></td>
				                			</tr>
				                             <tr>
				                  				<td class="textlabel"><span style="padding-left:20px"><?php echo _('Postal Code')?></span></td>
								                <td><?php echo $orders_n[$i]->postcode_c?></td>
				                			</tr>
				
				                			<tr>
				                  				<td class="textlabel"><span style="padding-left:20px"><?php echo _('City')?></span></td>
				                  				<td><?php echo $orders_n[$i]->city_c?></td>
				
				                			</tr>
				                			<tr>
				                  				<td class="textlabel"><span style="padding-left:20px"><?php echo _('Country')?></span></td>
				                  				<td><?php echo $orders_n[$i]->country_name?></td>
				                			</tr>
				                			<tr>
				                  				<td class="textlabel"><span style="padding-left:20px"><?php echo _('Telephone')?></span></td>
				                  				<td><?php echo $orders_n[$i]->phone_c?></td>
				                			</tr>
				                			<tr>
								                <td class="textlabel"><span style="padding-left:20px"><?php echo _('GSM')?></span></td>
				                				<td><?php echo $orders_n[$i]->mobile_c?></td>
				                			</tr>
				                			<tr>
				                  				<td class="textlabel"><span style="padding-left:20px"><?php echo _('FAX')?></span></td>
				                  				<td><span style="padding-right:250px"><?php echo $orders_n[$i]->fax_c;?></span></td>
				                			</tr>
				                			<?php //if($is_set_discount_card_setting){?>
				                			<!-- <tr>
				                  				<td class="textlabel"><span style="padding-left:20px"><?php echo _('Discount Card Number')?></span></td>
				                  				<td><span style="padding-right:250px"><?php echo $orders_n[$i]->discount_card_number;?></span></td>
				                			</tr> -->
				                			<?php //}?>
											<tr>
								                <td class="textlabel"><span style="padding-left:20px"><?php echo _('Client Number')?></span></td>
				                				<td>
												<input type="text" name="client_number" id="client_number" value="<?php echo $orders_n[$i]->client_number; ?>" />
												&nbsp;&nbsp;
												<input type="hidden" name="client_id" id="client_id" value="<?php echo $orders_n[$i]->client_id; ?>" />
												<input type="hidden" name="div_id" id="div_id" value="<?php echo $i; ?>" />
												<input type="hidden" name="company_id" id="company_id" value="<?php echo $orders_n[$i]->company_id; ?>" />
												<input type="button" name="add_client_no" id="add_client_no" class="number_update" value="<?php echo _('Update'); ?>" />
												</td>
				                			</tr>
				              			</tbody>
						            </table>
				          		</div>
						  	</div>
						  </td>
						  <td><?php echo $order_total; ?></td>
						  <td><?php echo $pickup_content; ?></td>
						  <td><?php echo $delivery_address_content; ?></td>
						  <td><input type="submit" name="export_and_send" value="<?php echo _('EXPORT AND SEND'); ?>" onclick="window.location='<?php echo base_url()?>cp/cdashboard/ibsoft_module/<?php echo $orders_n[$i]->id; ?>/invoice/<?php echo $orders_n[$i]->company_id; ?>';" /></td>				      
					   </tr>					       
					   <?php } ?>
					</tbody>
					</table>
					<?php } else { ?>	
					<p style="padding:10px;color:red;"><strong><?php echo _('No orders.'); ?></strong></p>
				 	<?php } ?>			
					<div style="clear:both"></div>
			     </div>
		      </div>
			  <?php }?>
	       </div>
        </div>  