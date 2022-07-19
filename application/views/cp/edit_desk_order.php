<style type="text/css">
#add_label{
	float:left;
	margin-top: 2px;
	padding-left:20px;
}
#category_id{
	float:left;
	margin-left:20px;
}
#subcategory_id{
	float:left;
	margin-left:20px;
}
#product_id{
	float:left;
	margin-left:20px;
}
#quantity{
	float:left;
	margin-left:20px;
}
#quantity_label{
	float: left;
    margin: 3px 0 0 5px;
}
#add_product_in_order{
	float: right;
	margin-left:20px;
}
td{vertical-align: top;}
</style>
<script type="text/javascript">
function get_subcat_n_prod( category_id )
{
	if( category_id )
	{
		$.post(base_url+'cp/desk/get_subcat_n_prod/'+category_id,
		{'category_id':category_id},
		function(data){
			
			var i = 0;
			var html_txt = '';
			
			//alert( data.length + '-' + data.subcategories.length + '-' + data.products.length );
			
			if( data.subcategories.length || data.products.length )
			{
			    if( data.subcategories && data.subcategories.length )
				{				   
				   html_txt = '';
				   
				   for( i=0; i<data.subcategories.length; i++ )
				   {
				       html_txt += '<option value="'+data.subcategories[i].id+'">'+data.subcategories[i].subname+'</option>';
				   }
				   
				   if( html_txt )
				      html_txt = '<option value=""> -- <?php echo _('Select Subcategory'); ?> -- </option>'+html_txt;
				   
				   $('#subcategory_id').html( html_txt );
				}
				else
				{
					html_txt = '<option value=""> -- <?php echo _('No Subcategory'); ?> -- </option>';				   
				    $('#subcategory_id').html( html_txt );
				}
				
				if( data.products && data.products.length )
				{
				   html_txt = '';
				   
				   for( i=0; i<data.products.length; i++ )
				   {
				       html_txt += '<option value="'+data.products[i].id+'">'+stripslashes(data.products[i].proname)+'</option>';
				   }
				   
				   if( html_txt )
				      html_txt = '<option value=""> -- <?php echo _('Select Product'); ?> -- </option>'+html_txt;
				   
				   $('#product_id').html( html_txt );
				   $('#product_data').html( data.products.toSource() );
				}
				else
				{
					html_txt = '<option value=""> -- <?php echo _('No Product'); ?> -- </option>';				   
				    $('#product_id').html( html_txt );
					$('#product_data').html( '' );
				}
			}
			else
			{
				html_txt = '<option value=""> -- <?php echo _('No Subcategory'); ?> -- </option>';				   
				$('#subcategory_id').html( html_txt );
				
				html_txt = '<option value=""> -- <?php echo _('No Product'); ?> -- </option>';				   
                $('#product_id').html( html_txt );
				
				$('#product_data').html( '' );
					
				return false;
			}
			
		},'json');
	}
	else
	  return false;
}

function get_prod( subcategory_id )
{
	if( subcategory_id )
	{
		$.post(base_url+'cp/desk/get_prod/'+( $('#category_id').val() )+'/'+subcategory_id,
		{'subcategory_id':subcategory_id},
		function(data){
		
			var i = 0;
			var html_txt = '';
			
			if( data.length )
			{				   
			   html_txt = '';
			   
			   for( i=0; i<data.length; i++ )
			   {
				   html_txt += '<option value="'+data[i].id+'">'+stripslashes(data[i].proname)+'</option>';
			   }
			   
			   if( html_txt )
				  html_txt = '<option value=""> -- <?php echo _('Select Product'); ?> -- </option>'+html_txt;
			   
			   $('#product_id').html( html_txt );
			   $('#product_data').html( data.toSource() );
			}
			else
			{
				html_txt = '<option value=""> -- <?php echo _('No Product'); ?> -- </option>';				   
				$('#product_id').html( html_txt );
				
				$('#product_data').html( '' );
			}
			
		},'json');
	}
	else
	{
		get_subcat_n_prod( $('#category_id').val() ); 
	}
}

function set_prod_unit( product_id )
{
	var product_data = $('#product_data').html();
	//product_data = JSON && JSON.parse(product_data) || $.parseJSON(product_data);
	
	product_data = eval('('+product_data+')');
	
	var i = 0;
	var sell_product_option = 'per_unit';
	
	if( product_data && product_data.length )
	{
	   for( i=0; i<product_data.length; i++ )
	   {
	      if( product_data[i].id == product_id )
		  {
		     sell_product_option = product_data[i].sell_product_option;
			 			 
			 if( sell_product_option == 'per_unit' )
			 {
			 	$('#content_type').val( 0 );
				$('#quantity_label').html( '<?php echo _('Unit'); ?>' );
			 }
			 else 
			 if( sell_product_option == 'weight_wise' )
			 {
			 	$('#content_type').val( 1 );
				$('#quantity_label').html( '<?php echo _('Grams'); ?>' );
			 }
			 else
			 if( sell_product_option == 'per_person' )
			 {
			 	$('#content_type').val( 2 );
				$('#quantity_label').html( '<?php echo _('Person'); ?>' );
			 }
			 else
			 if( sell_product_option == 'client_may_choose' )
			 {
			    var html_txt = '';
			 
			    html_txt += '<select name="new_content_type" id="new_content_type" onchange="document.getElementById(\'content_type\').value=this.value;">';
				html_txt += '<option value="0"><?php echo _('Per Piece'); ?></option>';
				html_txt += '<option value="1"><?php echo _('Grams'); ?></option>';
				html_txt += '</select>';
			 
			 	$('#content_type').val( 0 );
				$('#quantity_label').html( html_txt );
			 }			 
			 
		     break;
		  }
	   }
	}
}

function edit_order_product( index, product_id, content_type )
{
    $.post( base_url+'cp/desk/get_product_groups',
	{ 'product_id' : product_id, 'content_type' : content_type },
	function(data){
	     		 
		 if( data && data.length )
		 {
		     var i=0;
			 var j=0;
			 var grp_html = ''; 
			 
			 for( i=0; i<data.length; i++ )
			 {
				 if( data[i].option_arr.length )
				 {
				    grp_html += '<b style="float:left;">'+data[i].group_name+' : </b>';
                    grp_html += '<select id="grp_'+data[i].id+'" name="group['+data[i].id+']" style="float:left;">';
					grp_html += '<option value=""> -- <?php echo _('Select'); ?> -- </option>';
					for( j=0; j<data[i].option_arr.length; j++ )
			        {
						grp_html += '<option value="'+data[i].group_name+'_'+data[i].option_arr[j].attribute_name+'_'+data[i].option_arr[j].attribute_value+'">'+data[i].option_arr[j].attribute_name+' ('+data[i].option_arr[j].attribute_value+')'+'</option>';
					}
					grp_html += '</select>';
					grp_html += '<div style="clear:both;"></div>';
					grp_html += '<br />';
				 }
			 }
			 
			 $('#grp_html_'+index).html(grp_html);
			 
			 for( i=0; i<data.length; i++ )
			   if( data[i].option_arr.length )
			     $('#grp_'+data[i].id).live('click',function(){});
		 }
		 
		 $('#org_'+index).fadeOut('slow');
		 $('#edit_'+index).fadeIn('fast');
		 
	},'json');
}

function cancle_order_product( index )
{
    $('#edit_'+index).fadeOut('slow');
	$('#org_'+index).fadeIn('fast');
}
</script>
<div id="main">

    <!-- MAIN HEADER -->
    <div id="main-header">
        <h2><?php echo _('Orders'); ?></h2>
      	<span class="breadcrumb">
           <a href="<?php echo base_url(); ?>"><?php echo _('Home'); ?></a>
           &nbsp;&raquo;&nbsp;
		   <a href="<?php echo base_url(); ?>cp/desk/orders"><?php echo _('Desk orders'); ?></a>
           &nbsp;&raquo;&nbsp;
		   <?php echo _('Order details'); ?>
        </span>
     </div>
     <!-- /MAIN HEADER -->
     
     <!-- CONTENT -->
     <div id="content">
         
         <?php $this->messages->display_messages();  ?>
         
         <div id="content-container">
         
        	<div class="box">
          		<h3><?php echo _('Edit Order'); ?></h3>
				<div class="table">
                    <?php $order = $order[0]; ?>
					<table border="0">
                       <tbody>
                          <tr>
                			<td><span class="textlabel" style="padding-left:20px"><?php echo _('Order ID'); ?> : </span> <?php echo $order->id; ?></td>
                            <td><span class="textlabel" style="padding-left:20px"><?php echo _('Order Date'); ?> : </span> <?php echo date('d M, y', strtotime($order->created_date) ); ?></td>
              			  </tr>
                          <tr>
                			<td><span class="textlabel" style="padding-left:20px"><?php echo _('Order Counter'); ?> : </span> <?php echo $order->order_counter; ?></td>
                            <td><span class="textlabel" style="padding-left:20px"><?php echo _('Order Time'); ?> : </span> <?php echo date('H:i a', strtotime($order->created_date) ); ?></td>
              			  </tr>
                          <tr>
                			<td colspan="2">&nbsp;</td>
                          </tr>
                          <tr>
                            <td colspan="2">
                              <form method="post" action="">
                              <input type="hidden" name="order_id" id="order_id" value="<?php echo $order->id; ?>" />
                              <input type="hidden" name="order_total" id="order_total" value="<?php echo $order->order_total; ?>" />
                              <span class="textlabel" id="add_label"><?php echo _('Add New Product'); ?> : </span>
                              <select name="category_id" id="category_id" onchange="get_subcat_n_prod(this.value);">
                                 <?php if(!empty($categories)){ ?>
                                 <option value=""> -- <?php echo _('Select Category'); ?> -- </option>
                                 <?php foreach( $categories  as $cat ){ ?>
                                 <option value="<?php echo $cat->id; ?>"><?php echo $cat->name; ?></option>
								 <?php } ?>
                                 <?php } ?>
                              </select>
                              <select name="subcategory_id" id="subcategory_id" onchange="get_prod(this.value);">
                                 <option value=""> -- <?php echo _('No Subcategory'); ?> -- </option>
                              </select>
                              <select name="product_id" id="product_id" onchange="set_prod_unit(this.value);">
                                 <option value=""> -- <?php echo _('No Product'); ?> -- </option>
                              </select>
                              <input type="text" name="quantity" id="quantity" value="" size="5" />
                              <label id="quantity_label"><?php echo _('Unit'); ?></label>
                              <input type="hidden" name="content_type" id="content_type" value="0" />
                              <input type="submit" name="add_product_in_order" id="add_product_in_order" value="<?php echo _('Add Product'); ?>" />
                              </form>
                              <div id="product_data" style="display:none;"></div>
                            </td>
                          </tr>
                          <tr>
                            <td colspan="2">&nbsp;</td>
                          </tr>
                          <tr>
                			<td colspan="2"><span class="textlabel" style="padding-left:20px"><?php echo _('Order Details'); ?> : </span></td>
                          </tr>
                          <tr>
                			<td colspan="2">
                               <?php if( !empty($order_details) ) { ?>
                               <br />
                               <table>
                                  <thead>                                    
                                    <tr>
                                        <th width="20%"><?php echo _('Product Name'); ?></th>
                                        <th width="15%"><?php echo _('Quantity'); ?></th>
                                        <th width="8%"><?php echo _('Price'); ?></th>
                                        <th width="25%"><?php echo _('Extra'); ?></th>
                                        <th width="8%"><?php echo _('Sub-Total'); ?></th>
                                        <th width="25%"><?php echo _('Action'); ?></th>
                                    </tr>
                                  </thead>
                                  <tbody>                            
                                    <?php foreach( $order_details as $index=>$od ) { ?>
                                    <?php
											 $add_costs_txt = '';
											 $add_costs = $od->add_costs;
											 if( $add_costs )
											 {
												$add_costs = explode( '#', $add_costs );
												
												if( !empty($add_costs) ) 
												foreach( $add_costs as $ac )
												{
												   $ac = explode( '_', $ac );
												   
												   if( !empty($ac) && isset($ac[0]) && isset($ac[1]) && isset($ac[2]) )
													 $add_costs_txt .= '<span style="color:red;">'.$ac[0].' : </span>'.$ac[1].' = '.$ac[2].'<br />';
												}
											 }
											 
											 $discount_txt = '';					 
											 if( $od->discount && $od->discount != 'multi' && $od->discount != 0 )
											   $discount_txt = '<strong>'._('Extra Discount').' : </strong>'.$od->discount.'&nbsp;&euro;<br />';
											 
											 $remark_txt = '';
											 if( $od->pro_remark )
											   $remark_txt = '<strong>'._('Remark').' : </strong>'.$od->pro_remark;
									?>
                                    <tr id="org_<?php echo $od->ID; ?>">
                                        <td><?php echo stripslashes($od->proname); ?></td>
                                        <td>
                                        	<?php
                                        		$unit = '';
                                        		if($od->content_type==1)
                                        			$unit = ' Gr.';
                                        		if($od->content_type==2)
                                        			$unit = ' Person';
                                        	?>
                                        	<?php echo $od->quantity.( ($od->content_type==1)?' Gr.':'' ); ?>
                                        </td>
                                        <td><?php echo round($od->default_price,2); ?>&nbsp;&euro;</td>
                                        <td><?php echo $add_costs_txt.$discount_txt.$remark_txt; ?></td>
                                        <td><?php echo round($od->total,2); ?>&nbsp;&euro;</td>
                                        <td>
										    <a href="javascript:void(0);" title="<?php echo _('Edit product options.'); ?>" onclick="edit_order_product('<?php echo $od->ID; ?>','<?php echo $od->product_id; ?>','<?php echo $od->content_type; ?>');"> <img src="<?php echo base_url(); ?>assets/cp/images/edit.gif" /></a>&nbsp;|&nbsp;<a href="<?php echo base_url(); ?>cp/desk/remove_prod/<?php echo $od->ID; ?>/<?php echo $order->id; ?>/<?php echo $od->product_id; ?>" title="<?php echo _('Remove this product from order.'); ?>"><img src="<?php echo base_url(); ?>assets/cp/images/delete.gif" height="16" width="16" /></a>
                                        </td>
                                    </tr>
                                    <tr id="edit_<?php echo $od->ID; ?>" style="display:none;"><td colspan="6" style="padding:0px;">
                                    <form method="post" action="<?php echo base_url(); ?>cp/desk/edit_ordered_product" id="edit_ordered_product_<?php echo $od->ID; ?>">
                                        <input type="hidden" name="order_id" value="<?php echo $order->id; ?>" />
                                        <input type="hidden" name="order_index" value="<?php echo $od->ID; ?>" />
                                        <input type="hidden" name="product_id" value="<?php echo $od->product_id; ?>" />
                                        <input type="hidden" name="content_type" value="<?php echo $od->content_type; ?>" />
                                        <input type="hidden" name="current_total" value="<?php echo $od->total; ?>" />
                                        <table width="100%"><tr>
                                        <td width="20%" style="border:0px;"><?php echo stripslashes($od->proname); ?></td>
                                        <td width="15%"style="border:0px;"><input type="text" name="upd_quantity" value="<?php echo $od->quantity; ?>" size="5" />&nbsp;<?php echo ($od->content_type==1)?_('Grams'):(($od->content_type==2)?_('Person'):_('Unit'))?></td>
                                        <td width="8%" style="border:0px;"><?php echo round($od->default_price,2); ?>&nbsp;&euro;</td>
                                        <td width="25%" style="border:0px;">
										    <?php echo ($discount_txt)?($discount_txt.'<br />'):''; ?>                                            
                                            <div id="grp_html_<?php echo $od->ID; ?>"></div>
                                            <textarea name="remark" id="remark" rows="3" cols="20"><?php echo $od->pro_remark; ?></textarea>
                                        </td>
                                        <td width="8%" style="border:0px;"><?php echo round($od->total,2); ?>&nbsp;&euro;</td>
                                        <td width="25%" style="border:0px;">
										    <input type="submit" name="update_order_prod" value="<?php echo _('Update'); ?>" style="float:left;" />
                                            <input type="button" name="cancle_update_order_prod" value="<?php echo _('Cancle'); ?>" style="float:left;margin-left:10px;" onclick="cancle_order_product('<?php echo $od->ID; ?>');" />
                                        </td>
                                        </tr></table>
                                    </form>
                                    </td></tr>
                                    <?php } ?>
                                    <tr>
                                       <td colspan="4" style="text-align:right;">
                                          <strong><?php echo _('Total'); ?> : </strong>
                                       </td>
                                       <td colspan="2" align="left">
                                          <?php echo round($order->order_total,2); ?>&nbsp;&euro;
                                       </td>
                                    </tr>
                                    <tr>                                       
                                      <td colspan="4" style="text-align:right;"><strong><?php echo _('Order Status'); ?> : </strong></td>
                                      <td colspan="2">
                                      <form method="post" action="">
                                          <input type="hidden" name="order_id" id="order_id" value="<?php echo $order->id; ?>" />
                                          <select name="order_status" id="order_status" style="float:left;" >
                                             <option value="0" <?php if($order->completed==0){echo 'selected="selected"';} ?>><?php echo _('Incomplete'); ?></option>
                                             <option value="1" <?php if($order->completed==1){echo 'selected="selected"';} ?>><?php echo _('Completed'); ?></option>
                                          </select>
                                      	  <input type="submit" name="update_order" value="<?php echo _('UPDATE'); ?>" style="float:left; margin-left:20px; margin-top: -2px;" />
                                          <div style="clear:both;"></div>
                                      </form>
                                      </td>                                       
                                    </tr>
                                  </tbody>
                               </table>
                               <?php } ?>
                            </td>
                          </tr>
                       </tbody>
                    </table>
                    
                </div>
        	</div>
            
         </div>
     
     </div>
     <!-- /CONTENT -->
<!-- </div> -->