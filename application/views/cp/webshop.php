<style>
.override.same_day_exp > tbody tr:nth-child(2) > td {
    border-top: 1px solid #e3e3e3;
}
.holiday_label {
    display: inline-block;
    margin-right: 10px;
    vertical-align: middle;
}
</style>
<script type="text/javascript">
var group_not_valid = "<?php echo _('Please fill group details !');?>";
</script>	
  	<div <?php if($this->session->flashdata('webshop')){?>class="box"<?php }else{?>class="boxed"<?php }?>>
      			<h3 id="webshop"> <?php echo _('Webshop')?></h3>
      			<div style="padding: 0px; display: block;" class="inside">
      			<form action="<?php echo base_url();?>cp/products/products_addedit" enctype="multipart/form-data" method="post" id="webshop_addedit" name="webshop_addedit">
        			<div class="table">
        				<table border="0">
			       			<tbody>
			       				<tr>
			                <td class="textlabel"><?php echo _('Allow customer to upload image')?></td>
			                <td style="padding-right:250px"><input type="checkbox" value="1" class="checkbox" id="allow_upload_image" name="allow_upload_image" <?php if($product_information&&$product_information['0']->allow_upload_image=="1"):?>checked="checked"<?php endif;?>></td>
			              </tr>			              
			              
			              <tr>
			                <td class="textlabel"><?php echo _('Sell Product'); ?></td>
			                <td>
			                	<input type="radio" name="sell_product_option" class="sell_pro" value="per_unit" <?php //if($product_information && ($product_information['0']->sell_product_option == 'per_unit' || $product_information['0']->sell_product_option == '') ){ echo 'checked="checked"'; } ?>checked="checked" />
			                  	&nbsp;<?php echo _('Per Unit'); ?><br />
			                  	<input type="radio" name="sell_product_option" class="sell_pro" value="per_person" <?php if($product_information && $product_information['0']->sell_product_option == 'per_person' ){ echo 'checked="checked"'; } ?> />
			                  	&nbsp;<?php echo _('Per Person'); ?><br />
			                  	<input type="radio" name="sell_product_option" class="sell_pro" value="weight_wise" <?php if($product_information && $product_information['0']->sell_product_option == 'weight_wise' ){ echo 'checked="checked"'; } ?> />
			                  	&nbsp;<?php echo _('Per Unit Weight'); ?><br />
			                  	<input type="radio" name="sell_product_option" class="sell_pro" value="client_may_choose" <?php if($product_information && $product_information['0']->sell_product_option == 'client_may_choose' ){ echo 'checked="checked"'; } ?> />
			                  	&nbsp;<?php echo _('Let client choose'); ?><br />
			                </td>
			              </tr>
			              
			              <tr>
			                <td class="textlabel"><?php echo _('Rate'); ?></td>
			                <td style="vertical-align:middle;">
			                	<div id="punit">
				                    <input type="text" name="price_per_unit" id="price_per_unit" value="<?php if($product_information):echo round($product_information['0']->price_per_unit,2); endif;?>" class="text medium" style="width:60px;" onchange="document.getElementById('rate_price').value=this.value;" />
				                    &nbsp;&euro;&nbsp;<i><?php echo _('Per unit price'); ?></i>&nbsp;				                    
			                    </div>
			                    <div id="pperson" style="display:none;">
				                    <input type="text" name="price_per_person" id="price_per_person" value="<?php if($product_information):echo round($product_information['0']->price_per_person,2); endif;?>" class="text medium" style="width:60px;" onchange="document.getElementById('rate_price_person').value=this.value;" />
				                    &nbsp;&euro;&nbsp;<i><?php echo _('Per p.'); ?></i><br />
				                    <input type="text" name="min_amount" id="min_amount" value="<?php if($product_information):echo round($product_information['0']->min_amount,2); endif;?>" class="text medium" style="width:60px;" />
				                    &nbsp;<i><?php echo _('Minimum amount they have to order'); ?></i><br />
				                    <input type="text" name="max_amount" id="max_amount" value="<?php if($product_information):echo round($product_information['0']->max_amount,2); endif;?>" class="text medium" style="width:60px;" />
				                    &nbsp;<i><?php echo _('Maximum amount they can order'); ?></i>&nbsp;
			                    </div>
			                  	<div id="pweight" style="display:none;"> <br />
				                    <input type="text" name="price_weight" id="price_weight" value="<?php if($product_information):echo round( ($product_information['0']->price_weight)*1000 ,2); endif;?>" class="text medium" style="width:60px;" onchange="document.getElementById('rate_price_wt').value=parseFloat((this.value).replace(',', '.'))/1000;" />
				                    &nbsp;&euro;&nbsp;/&nbsp;<i><?php echo _('kg');?></i>
				                    <input type="hidden" name="weight_unit" id="weight_unit" value="gm" /><br/>
				                    <input type="text" name="min_weight" id="min_weight" value="<?php if($product_information):echo round( ($product_information['0']->min_weight),2); endif;?>" class="text medium" style="width:60px;" />
				                    &nbsp;<i><?php echo _('kg');?></i>
				                    &nbsp;<i><?php echo _('Minimum amount they have to order'); ?></i><br />
				                    <input type="text" name="max_weight" id="max_weight" value="<?php if($product_information):echo round( ($product_information['0']->max_weight),2); endif;?>" class="text medium" style="width:60px;" />
				                    &nbsp;<i><?php echo _('kg');?></i>
				                    &nbsp;<i><?php echo _('Maximum amount they can order'); ?></i>&nbsp;
				                    <div style="clear:both;"></div>
			                  	</div>
			                </td>
			              </tr>
			            <tr>
			                <td id="w_td" class="textlabel"><?php echo _('Weight'); ?></td>
			                <td style="vertical-align:middle;">
			                	<div id="wunit">				                   
				                    <input type="text" name="weight_per_unit" id="weight_per_unit" value="<?php if($product_information):echo round($product_information['0']->weight_per_unit,2); endif;?>" class="text medium" style="width:60px;" onchange="document.getElementById('default_qty').value=this.value;" />
				                    &nbsp;<?php echo _('Kg')?>&nbsp;<i><?php echo _('Per unit'); ?></i>
			                    </div>			                    		                  	
			                </td>
			              </tr>
			              <tr>
			                <td class="textlabel"><?php echo _('New')?>&nbsp;&nbsp;&nbsp;<a title="<?php echo _('Want a product put in paint or is this a new product you can be the customer know by placing an icon behind.')?>" href="#" id="help-prod2"><img width="16" height="16" src="<?php echo base_url(); ?>assets/cp/images/help.png"></a></td>
			                <td style="padding-right:250px">
			                	<input type="checkbox"  value="1" class="checkbox" id="type" name="type"<?php if($product_information&&$product_information['0']->type=='1'):?>checked="checked"<?php endif;?>>
			                  	&nbsp;&nbsp;<img width="16" height="16" alt=""src="<?php echo base_url(); ?>assets/cp/images/new.png">
			                </td>
			              </tr>
			              
			              <tr id="group_table_row" style="display:none">
			              	<td class="textlabel"><?php echo _('Group')?>&nbsp;<?php echo _('Per unit')?>&nbsp;&nbsp;<a title="<?php echo _('This feature allows the customer to put together a product (eg cheese sandwich: small = &euro; -1.00 / +0.00 &euro; = medium / large &euro; = +1.00). The groups can be created via "Settings".')?>" href="#" class="help-prod"><img width="16" height="16" src="<?php echo base_url(); ?>assets/cp/images/help.png"></a><br /><p style="font-size:11px;"><?php echo _('Will be added on per unit purchase.'); ?></p></td>
			                <?php if($groups_products){?>
			                <!--this is for the updation form it will  show the updated value-->
			                <td style="padding-right:250px">
				                <table width="500px" border="0" class="override" id="outerBigTable">
				                    <tbody>
				                      <tr>
				                        <input type="hidden" name="count_groups" id="count_groups" value="<?php if($products_per_group):?><?php echo  count($products_per_group)-1; endif;?>">
				                      </tr>
				                      
				                      <tr>
				                        <td width="100%">
					                        <table width="100%" border="0" class="override" id="GroupsTable">
					                            <tbody>
					                              <?php $count=0;foreach($products_per_group as $key=>$product_per_group):?>
					                              <tr>                              	
					                                <td width="100%">
						                                <table width="70%" border="0" class="override" id="new_table<?php echo $count?>">
						                                    <tbody>
						                                      <tr>
						                                        <td width="30%">
							                                        <select class="select" type="select" id="m_select<?php echo $count?>" name="m_select<?php echo $count?>">
							                                            <option value="0">----<?php echo _('select')?>----</option>
							                                            <?php if($groups): foreach($groups as $group):?>
							                                            <option value=<?php echo $group->id;?><?php if($group->id==$key):?> selected="selected" <?php endif;?>><?php echo $group->group_name?>
							                                            <?php endforeach;?>
							                                            </option>
							                                            <?php else:?>
							                                            <option value="-1"><?php echo _('No groups available')?></option>
							                                            <?php endif;?>
							                                        </select>
						                                        </td>
						                                        <td width="35%">
							                                        <input type="checkbox" name="ms<?php echo $count;?>" id="ms<?php echo $count;?>" value="1" <?php if($product_per_group['0']['multiselect']){?>checked="checked"<?php }?> /><span id="chktxt<?php echo $count;?>" >&nbsp;<?php echo _("Multiselect Attributes");?></span>
						                                        </td>
						                                        <td width="25%">
							                                        <input type="checkbox" name="required_<?php echo $count;?>" id="required_<?php echo $count;?>" value="1" <?php if($product_per_group['0']['required']){?>checked="checked"<?php }?> /><span id="req_txt_<?php echo $count;?>">&nbsp;<?php echo _("Required");?></span>
						                                        </td>
						                                        <td width="10">
						                                        	<a href="javascript:addTable();"><img width="18" border="0" src="<?php echo base_url(); ?>assets/cp/images/add.gif" id="add_m<?php echo $count?>" name="add_m<?php echo $count?>"></a>
						                                        	&nbsp;<img width="18" border="0" onClick="javascript:deleteTable(this.id);" src="<?php echo base_url(); ?>assets/cp/images/delete.gif" id="delete_m<?php echo $count?>" name="delete_m<?php echo $count?>">
						                                        </td>
						                                      </tr>
						                                    </tbody>
						                                  </table>
						                             </td>
					                                <!--new_table-->
					                              </tr>
					                              
					                              <tr>
					                                <td width="100%" style="padding: 10px 0px 10px 30px">
						                                <table border="0" class="override" name="Attributes_Table<?php echo $count?>" id="Attributes_Table<?php echo $count?>">
						                                    <tbody class="sortable">
						                                      <input type="hidden" name="count_row_per_group<?php echo $count?>" id="count_row_per_group<?php echo $count?>" value="<?php echo (int)(count($product_per_group)-1);?>"/>
						                                      <?php $row_per_table=0; foreach($product_per_group as $key=>$value):?>
						                                      <tr>
						                                      	<td width="50%">
						                                      		<input type="text" id="att_text_<?php echo $row_per_table;?>_<?php echo $count?>" name="att_text_<?php echo $count?>[]" class="text verywidth grp_valid_check" value="<?php echo $product_per_group[$key]['attribute_name']?>" />
						                                      	</td>
						                                        <td width="20%" align="left" style="padding:0px 20px 0px 10px">
						                                        	<input type="text" class="text medium grp_valid_check" onblur="close_tip(this.id);" onfocus="open_tip(this.id);" size="5" id="att_price_<?php echo $row_per_table;?>_<?php echo $count?>" name="att_price_<?php echo $count?>[]" value="<?php echo $product_per_group[$key]['attribute_value']?>" />
						                                            <span class="myTooltipNone" id="tooltip_att_price_<?php echo $row_per_table;?>_<?php echo $count?>"><?php echo _('formaat 0.0000 (punt) , <br> Rate to begin with - or + sign and 4 digits after the dot (eg: -42.4823 +42.4823 or)')?></span>
						                                        </td>
						                                        <td width="5%">
						                                        	<img width="18" border="0" onclick="javascript:addNewRowToTable(this.id)" src="<?php echo base_url(); ?>assets/cp/images/add.gif" id="add_a_<?php echo $row_per_table;?>_<?php echo $count?>" name="add_a_<?php echo $row_per_table;?>_<?php echo $count?>" />
						                                        </td>
						                                        <td width="5%">
						                                        	<img width="18" border="0" onclick="javascript:deleteRowFromTable(this.id)" src="<?php echo base_url(); ?>assets/cp/images/delete.gif" name="delete_a_<?php echo $row_per_table;?>_<?php echo $count?>" id="delete_a_<?php echo $row_per_table;?>_<?php echo $count?>" />
						                                        </td>
						                                        <td width="10%" style="text-align:right">
						                                        	<img width="18" border="0" class="handle" src="<?php echo base_url(); ?>assets/cp/images/move.png" style="cursor: pointer;" title="<?php echo _("Drag me");?>"/>
						                                        </td>
						                                      </tr>
						                                      <?php $row_per_table++; endforeach;?>
						                                    </tbody>
						                                  </table>
						                              </td>
					                                </tr>
					                                  
					                              <?php  echo '<script type="text/javascript">iteration = '.$count.';</script>';?>
					                             <?php $count++;	endforeach;?>
					                            </tbody>
					                          </table>
				                          </td>
				                      </tr>
				                    </tbody>
				                  </table>
				                  <?php if($this->session->userdata('login_via') == 'mcp'){?>
				                  <div id="setting_div">
							         <a href= "javascript:;" onclick="apply_group_settings()">
							            <?php _('Take same settings for this category.');?>
							         </a>
						          </div>
						          <?php }?>
			                  </td>
				              <?php }else{?>
				              <!--for the addition of groups-->
				              <td style="padding-right:250px">
				              	<table width="300" border="0" class="override" id="outerBigTable">
				                    <tbody>
				                      <tr>
				                        <td width="100%">
					                        <table width="100%" border="0" class="override" id="GroupsTable">
					                            <tbody>
					                            <tr>
					                                <td width="100%">
					                                	<table width="70%" border="0" class="override" id="new_table0">
						                                    <tbody>
						                                      <tr>
						                                        <input type="hidden" value="0" name="count_groups" id="count_groups">
						                                        <td width="30%">
							                                        <select type="select" id="m_select0" name="m_select0">
							                                            <option value="0">---- <?php echo _('Select')?> ----</option>
							                                            <?php if($groups): foreach($groups as $group):?>
							                                            <option value=<?php echo $group->id?>><?php echo $group->group_name?>
							                                            <?php endforeach;?>
							                                            </option>
							                                            <?php else:?>
							                                            <option value="381"><?php echo _('No Groups Available')?></option>
							                                            <?php endif;?>
							                                        </select>
						                                        </td>
						                                        <td width="35%">
							                                        <input type="checkbox" name="ms0" id="ms0" value="1" /><span id="chktxt0">&nbsp;<?php echo _("Multiselect Attributes");?></span>
						                                        </td>
						                                        <td width="25%">
							                                        <input type="checkbox" name="required_0" id="required_0" value="1" /><span id="req_txt_0">&nbsp;<?php echo _("Required");?></span>
						                                        </td>
						                                        <td width="10">
						                                        	<a href="javascript:addTable();"><img width="18" border="0" src="<?php echo base_url(); ?>assets/cp/images/add.gif" id="add_m0" name="add_m0"></a>
						                                        	&nbsp;<img width="18" border="0" onClick="javascript:deleteRowFromTable(this.id);" src="<?php echo base_url(); ?>assets/cp/images/delete.gif" id="delete_m0" name="delete_m0">
						                                        </td>
						                                      </tr>
						                                    </tbody>
						                                  </table>
					                                  </td>
					                              </tr>
					                              
					                              <tr>
					                                <td width="100%" style="padding: 10px 0px 10px 30px">
					                                	<table border="0" class="override" name="Attributes_Table0" id="Attributes_Table0">
						                                    <tbody class="sortable">
						                                      <tr>
						                                        <input type="hidden" name="count_row_per_group0" id="count_row_per_group0" value="0"/>
						                                        <td width="50%">
						                                        	<input type="text" id="att_text_0_0" name="att_text_0[]" class="text verywidth grp_valid_check" >
						                                        </td>
						                                        <td width="20%" style="padding:0px 20px 0px 10px">
						                                        	<input type="text" onBlur="close_tip(this.id);" onFocus="open_tip(this.id);" size="5" id="att_price_0_0" name="att_price_0[]" class="text medium grp_valid_check">
							                                        <!--<span id="price" class="myTooltip">Enter price upto 2 decimal places</span>-->
							                                        <span class="myTooltipNone" id="tooltip_att_price_0_0"><?php echo _('formaat 0.0000 (punt')?> , <br>
							                                        <?php echo _('Rate to begin with - or + sign and 4 digits after the dot (eg: -42.4823 +42.4823 or')?> </span>
						                                        </td>
						                                        <td width="5%">
						                                        	<img width="18" border="0" onClick="javascript:addNewRowToTable(this.id)" src="<?php echo base_url(); ?>assets/cp/images/add.gif" id="add_a_0_0" name="add_a_0_0">
						                                        </td>
						                                        <td width="5%">
						                                        	<img width="18" border="0" onClick="javascript:deleteRowFromTable(this.id)" src="<?php echo base_url(); ?>assets/cp/images/delete.gif" name="delete_a_0_0" id="delete_a_0_0">
						                                       	</td>
						                                       	 <td width="10%" style="text-align:right">
						                                        	<img width="18" border="0" class="handle" src="<?php echo base_url(); ?>assets/cp/images/move.png" style="cursor: pointer;" title="<?php echo _("Drag me");?>"/>
						                                        </td>
						                                      </tr>
						                                    </tbody>
						                                </table>
						                            </td>
					                              </tr>
					                            </tbody>
					                          </table>
				                          </td>
				                      </tr>
				                    </tbody>
				                  </table>
				                  <?php if($this->session->userdata('login_via') == 'mcp'){?>
				                  <div id="setting_div">
							         <a href= "javascript:;" onclick="apply_group_settings()">
							            <?php echo _('Take same settings for this category.');?>
							         </a>
						          </div>
						          <?php }?>
				              </td>
				              <?php }?>
			              </tr>
						  
						  <!-- Options for PERSON -->
  			              <tr id="group_table_person" style="display:none">
			                <td class="textlabel"><?php echo _('Group')?>&nbsp;<?php echo _('Per person')?>&nbsp;&nbsp;<a title="<?php echo _('This feature allows the customer to put together a product (eg cheese sandwich: small = &euro; -1.00 / +0.00 &euro; = medium / large &euro; = +1.00). The groups can be created via "Settings".')?>" href="#" class="help-prod"><img width="16" height="16" src="<?php echo base_url(); ?>assets/cp/images/help.png"></a><br /><p style="font-size:11px;"><?php echo _('Will be added on per person purchase.'); ?></p></td>
			                <?php if($groups_products_person){?>
			                <!--this is for the updation form it will  show the updated value-->
			                <td style="padding-right:250px">
				                <table width="500px" border="0" class="override" id="outerBigTable">
				                    <tbody>
				                      <tr>
				                        <input type="hidden"  name="count_person_groups" id="count_person_groups" value="<?php if($products_per_group_person):?><?php echo  count($products_per_group_person)-1; endif;?>">
				                      </tr>
				                      
				                      <tr>
				                        <td width="100%">
					                        <table width="100%" border="0" class="override" id="GroupsPersonTable">
					                            <tbody>
					                              <?php $count=0;foreach($products_per_group_person as $key=>$products_per_group_person):?>
					                              <tr>                              	
					                                <td width="100%">
						                                <table width="70%" border="0" class="override" id="new_person_table<?php echo $count?>">
						                                    <tbody>
						                                      <tr>
						                                        <td width="30%">
							                                        <select class="select" type="select" id="m_person_select<?php echo $count?>" name="m_person_select<?php echo $count?>">
							                                            <option value="0">----<?php echo _('select')?>----</option>
							                                            <?php if($groups_person): foreach($groups_person as $groups_persons):?>
							                                            <option value=<?php echo $groups_persons->id;?><?php if($groups_persons->id==$key):?> selected="selected" <?php endif;?>><?php echo $groups_persons->group_name?>
							                                            <?php endforeach;?>
							                                            </option>
							                                            <?php else:?>
							                                            <option value="-1"><?php echo _('No groups available')?></option>
							                                            <?php endif;?>
							                                        </select>
						                                        </td>
						                                        <td width="35%">
							                                        <input type="checkbox" name="ms_p<?php echo $count;?>" id="ms_p<?php echo $count;?>" value="1" <?php if($products_per_group_person['0']['multiselect']){?>checked="checked"<?php }?> /><span id="chktxt_p<?php echo $count;?>" >&nbsp;<?php echo _("Multiselect Attributes");?></span>
						                                        </td>
						                                        <td width="25%">
							                                        <input type="checkbox" name="required_p_<?php echo $count;?>" id="required_p_<?php echo $count;?>" value="1" <?php if($products_per_group_person['0']['required']){?>checked="checked"<?php }?> /><span id="req_txt_p_<?php echo $count;?>">&nbsp;<?php echo _("Required");?></span>
						                                        </td>
						                                        <td width="10">
						                                        	<a href="javascript:addTablePerson();"><img width="18" border="0" src="<?php echo base_url(); ?>assets/cp/images/add.gif" id="add_m_p<?php echo $count?>" name="add_m_p<?php echo $count?>"></a>
						                                        	&nbsp;<img width="18" border="0" onClick="javascript:deleteTablePerson(this.id);" src="<?php echo base_url(); ?>assets/cp/images/delete.gif" id="delete_m_p<?php echo $count?>" name="delete_m_p<?php echo $count?>">
						                                        </td>
						                                      </tr>
						                                    </tbody>
						                                  </table>
						                             </td>
					                                <!--new_table-->
					                              </tr>
					                              
					                              <tr>
					                                <td width="100%" style="padding: 10px 0px 10px 30px">
						                                <table border="0" class="override" name="Attributes_Person_Table<?php echo $count?>" id="Attributes_Person_Table<?php echo $count?>">
						                                    <tbody class="sortable">
						                                        <input type="hidden" name="count_row_per_person_group<?php echo $count?>" id="count_row_per_person_group<?php echo $count?>" value="<?php echo (int)(count($products_per_group_person)-1);?>"/>
						                                      <?php $row_per_table=0; foreach($products_per_group_person as $key=>$value):?>
						                                      <tr>
						                                      	<td width="50%">
						                                      		<input type="text" id="att_person_text_<?php echo $row_per_table;?>_<?php echo $count?>" name="att_person_text_<?php echo $count;?>[]" class="text verywidth grp_valid_check" value="<?php echo $products_per_group_person[$key]['attribute_name']?>" />
						                                      	</td>
						                                        <td width="20%" align="left" style="padding:0px 20px 0px 10px">
						                                        	<input type="text" class="text medium grp_valid_check" onblur="close_tip(this.id);" onfocus="open_tip(this.id);" size="5" id="att_person_price_<?php echo $row_per_table;?>_<?php echo $count?>" name="att_person_price_<?php echo $count;?>[]" value="<?php echo $products_per_group_person[$key]['attribute_value']?>" />
						                                            <span class="myTooltipNone" id="tooltip_att_person_price_<?php echo $row_per_table;?>_<?php echo $count?>"><?php echo _('formaat 0.0000 (punt) , <br> Rate to begin with - or + sign and 4 digits after the dot (eg: -42.4823 +42.4823 or)')?></span>
						                                        </td>
						                                        <td width="5%">
						                                        	<img width="18" border="0" onclick="javascript:addNewRowToPersonTable(this.id)" src="<?php echo base_url(); ?>assets/cp/images/add.gif" id="add_p_a_<?php echo $row_per_table;?>_<?php echo $count?>" name="add_p_a_<?php echo $row_per_table;?>_<?php echo $count?>" />
						                                        </td>
						                                        <td width="5%">
						                                        	<img width="18" border="0" onclick="javascript:deleteRowFromPersonTable(this.id)" src="<?php echo base_url(); ?>assets/cp/images/delete.gif" name="delete_p_a_<?php echo $row_per_table;?>_<?php echo $count?>" id="delete_p_a_<?php echo $row_per_table;?>_<?php echo $count?>" />
						                                        </td>
						                                        <td width="10%" style="text-align:right">
						                                        	<img width="18" border="0" class="handle" src="<?php echo base_url(); ?>assets/cp/images/move.png" style="cursor: pointer;" title="<?php echo _("Drag me");?>"/>
						                                        </td>
						                                      </tr>
						                                      <?php $row_per_table++; endforeach;?>
						                                    </tbody>
						                                  </table>
						                              </td>
					                                </tr>
					                                  
					                              <?php  echo '<script type="text/javascript">iteration = '.$count.';</script>';?>
					                             <?php $count++;	endforeach;?>
					                            </tbody>
					                          </table>
				                          </td>
				                      </tr>
				                    </tbody>
				                  </table>
				                  <?php if($this->session->userdata('login_via') == 'mcp'){?>
				                  <div>
							         <a href= "javascript:;" onclick="apply_group_settings()">
							            <?php echo _('Take same settings for this category.');?>
							         </a>
						          </div>
						          <?php }?>
			                  </td>
				              <?php }else{?>
				              <!--for the addition of groups-->
				              <td style="padding-right:250px">
				              	<table width="300" border="0" class="override" id="outerBigTable">
				                    <tbody>
				                      <tr>
				                        <td width="100%">
					                        <table width="100%" border="0" class="override" id="GroupsPersonTable">
					                            <tbody>
					                              <tr>
					                                <td width="100%">
					                                	<table width="70%" border="0" class="override" id="new_person_table0">
						                                    <tbody>
						                                      <tr>
						                                        <input type="hidden" value="0" name="count_person_groups" id="count_person_groups">
						                                        <td width="30%">
							                                        <select type="select" id="m_person_select0" name="m_person_select0">
							                                            <option value="0">---- <?php echo _('Select')?> ----</option>
							                                            <?php if($groups_person): foreach($groups_person as $groups_persons):?>
							                                            <option value=<?php echo $groups_persons->id?>><?php echo $groups_persons->group_name?>
							                                            <?php endforeach;?>
							                                            </option>
							                                            <?php else:?>
							                                            <option value="381"><?php echo _('No Groups Available')?></option>
							                                            <?php endif;?>
							                                        </select>
						                                        </td>
						                                        <td width="35%">
							                                        <input type="checkbox" name="ms_p0" id="ms_p0" value="1" /><span id="chktxt_p0" >&nbsp;<?php echo _("Multiselect Attributes");?></span>
						                                        </td>
						                                        <td width="25%">
							                                        <input type="checkbox" name="required_p_0" id="required_p_0" value="1" /><span id="req_txt_p_0">&nbsp;<?php echo _("Required");?></span>						                                        </td>
						                                        <td width="10">
						                                        	<a href="javascript:addTablePerson()"><img width="18" border="0" src="<?php echo base_url(); ?>assets/cp/images/add.gif" id="add_m_p0" name="add_m_p0"></a>
						                                        	&nbsp;<img width="18" border="0" onClick="javascript:deleteRowFromTablePerson(this.id);" src="<?php echo base_url(); ?>assets/cp/images/delete.gif" id="delete_m_p0" name="delete_m_p0">
						                                        </td>
						                                      </tr>
						                                    </tbody>
						                                  </table>
					                                  </td>
					                              </tr>
					                              
					                              <tr>
					                                <td width="100%" style="padding: 10px 0px 10px 30px">
					                                	<table border="0" class="override" name="Attributes_Person_Table0" id="Attributes_Person_Table0">
						                                    <tbody class="sortable">
						                                      <tr>
						                                        <input type="hidden" name="count_row_per_person_group0" id="count_row_per_person_group0" value="0"/>
						                                        <td width="50%">
						                                        	<input type="text" id="att_person_text_0_0" name="att_person_text_0[]" class="text verywidth grp_valid_check" >
						                                        </td>
						                                        <td width="20%" style="padding:0px 20px 0px 10px">
						                                        	<input type="text" onBlur="close_tip(this.id);" onFocus="open_tip(this.id);" size="5" id="att_person_price_0_0" name="att_person_price_0[]" class="text medium grp_valid_check">
							                                        <span class="myTooltipNone" id="tooltip_att_person_price_0_0"><?php echo _('formaat 0.0000 (punt')?> , <br>
							                                        <?php echo _('Rate to begin with - or + sign and 4 digits after the dot (eg: -42.4823 +42.4823 or')?> </span>
						                                        </td>
						                                        <td width="5%">
						                                        	<img width="18" border="0" onClick="javascript:addNewRowToPersonTable(this.id)" src="<?php echo base_url(); ?>assets/cp/images/add.gif" id="add_p_a_0_0" name="add_p_a_0_0">
						                                        </td>
						                                        <td width="5%">
						                                        	<img width="18" border="0" onClick="javascript:deleteRowFromPersonTable(this.id)" src="<?php echo base_url(); ?>assets/cp/images/delete.gif" name="delete_p_a_0_0" id="delete_p_a_0_0">
						                                       	</td>
						                                       	<td width="10%" style="text-align:right">
						                                        	<img width="18" border="0" class="handle" src="<?php echo base_url(); ?>assets/cp/images/move.png" style="cursor: pointer;" title="<?php echo _("Drag me");?>"/>
						                                        </td>
						                                      </tr>
						                                    </tbody>
						                                </table>
						                            </td>
					                              </tr>
					                            </tbody>
					                          </table>
				                          </td>
				                      </tr>
				                    </tbody>
				                  </table>
				                  <?php if($this->session->userdata('login_via') == 'mcp'){?>
				                  <div>
							         <a href= "javascript:;" onclick="apply_group_settings()">
							            <?php echo _('Take same settings for this category.');?>
							         </a>
						          </div>
						          <?php }?>
				              </td>
				              <?php }?>
			              </tr>
						  
						  <!-- OPTION FOR WEIGHT WISE -->
			              <tr id="group_table_wt" style="display:none">
			                <td class="textlabel"><?php echo _('Group')?>&nbsp;<?php echo _('Per weight')?><strong></strong>&nbsp;&nbsp;<a title="<?php echo _('This feature allows the customer to put together a product .... ')?>" href="#" class="help-prod"><img width="16" height="16" src="<?php echo base_url(); ?>assets/cp/images/help.png"></a><br /><p style="font-size:11px;"><?php echo _('Will be added on per purchase.'); ?></p></td>		                
							<?php if($groups_products_wt):?>
			                <!--this is for the updation form it will  show the updated value-->
			                <td style="padding-right:250px">
				                <table width="500px" border="0" class="override" id="outerBigTable">
				                    <tbody>
				                      <tr>
				                        <input type="hidden"  name="count_wt_groups" id="wcount_groups" value="<?php if($products_per_group_wt):?><?php echo  count($products_per_group_wt)-1; endif;?>">
				                      </tr>
				                      <tr>
				                        <td width="100%">
					                        <table width="100%" border="0" class="override" id="WGroupsTable">
					                            <tbody>
					                              <?php $count=0;foreach($products_per_group_wt as $key=>$products_per_group_wt):?>
					                              <tr id="grow_<?php echo $count?>">
					                                <td width="100%">
					                                	<table width="70%" border="0" class="override" id="new_wt_table<?php echo $count?>">
						                                    <tbody>
						                                      <tr>
						                                        <td width="30%">
																	<select type="select" id="wm_select<?php echo $count?>" name="wm_select<?php echo $count?>">
							                                            <option value="0">----<?php echo _('select')?>----</option>
							                                            <?php if($groups_wt): foreach($groups_wt as $group):?>
							                                            <option value=<?php echo $group->id;?><?php if($group->id==$key):?> selected="selected" <?php endif;?>><?php echo $group->group_name?>
							                                            <?php endforeach;?>
							                                            </option>
							                                            <?php else:?>
							                                            <option value="-1"><?php echo _('no groups available')?></option>
							                                            <?php endif;?>
							                                        </select>
						                                        </td>
						                                        <td width="35%">
							                                        <input type="checkbox" name="ms_wt<?php echo $count;?>" id="ms_wt<?php echo $count;?>" value="1" <?php if($products_per_group_wt['0']['multiselect']){?>checked="checked"<?php }?> /><span id="chktxt_wt<?php echo $count;?>" >&nbsp;<?php echo _("Multiselect Attributes");?></span>
						                                        </td>
						                                        <td width="25%">
							                                        <input type="checkbox" name="required_wt_<?php echo $count;?>" id="required_wt_<?php echo $count;?>" value="1" <?php if($products_per_group_wt['0']['required']){?>checked="checked"<?php }?> /><span id="req_txt_wt_<?php echo $count;?>">&nbsp;<?php echo _("Required");?></span>
						                                        </td>
						                                        <td width="10">
						                                        	<a href="javascript:addTableWeight()"><img width="18" border="0" src="<?php echo base_url(); ?>assets/cp/images/add.gif" id="add_<?php echo ($count);?>" name="add_<?php echo ($count);?>" /></a>
																	&nbsp;<img width="18" border="0" src="<?php echo base_url(); ?>assets/cp/images/delete.gif" id="delete_<?php echo ($count);?>" name="delete_<?php echo ($count);?>" onclick="deleteTableWeight(this.id);" />
																</td>
						                                      </tr>
						                                    </tbody>
						                                </table>
						                             </td>
					                                <!--new_table-->
					                              </tr>
					                              
					                              <tr id="arow_<?php echo $count?>">
					                                <td width="100%" style="padding: 10px 0px 10px 30px">
						                                <table width="450" border="0" class="override" id="WAttributes_Table<?php echo $count?>" name="WAttributes_Table<?php echo $count?>">
						                                    <tbody class="sortable">
						                                      <input type="hidden" name="count_row_per_wt_group<?php echo $count?>" id="wcount_row_per_wt_group<?php echo $count?>" value="<?php echo (int)(count($products_per_group_wt)-1); ?>"/>
						                                      <?php $row_per_table=0; foreach($products_per_group_wt as $key=>$value):?>
						                                      <tr id="at_<?php echo $row_per_table;?>_<?php echo $count?>">
						                                        <td width="50%">
						                                        	<input type="text" id="watt_text_<?php echo $row_per_table;?>_<?php echo $count?>" name="watt_text_<?php echo $count?>[]" class="text verywidth grp_valid_check" value="<?php echo $products_per_group_wt[$key]['attribute_name']?>">
						                                        </td>
						                                        <td width="20%" align="left" style="padding:0px 20px 0px 10px">
						                                        	<input type="text" class="text medium grp_valid_check" onBlur="close_tip(this.id);" onFocus="open_tip(this.id);" size="5" id="watt_price_<?php echo $row_per_table;?>_<?php echo $count?>" name="watt_price_<?php echo $count?>[]" value="<?php echo $products_per_group_wt[$key]['attribute_value']?>">						                                          
						                                         	<span class="myTooltipNone" id="tooltip_watt_price_<?php echo $row_per_table;?>_<?php echo $count?>"><?php echo _('formaat 0.0000 (punt) , <br> Rate to begin with - or + sign and 4 digits after the dot (eg: -42.4823 +42.4823 or)')?> </span>
																</td>						                                        								
																<td width="5%">
																	<img width="18" border="0" onClick="addNewRowToWeightTable(this.id)" src="<?php echo base_url(); ?>assets/cp/images/add.gif" id="add_at_<?php echo $row_per_table;?>_<?php echo $count?>" name="add_at_<?php echo $row_per_table;?>_<?php echo $count?>">
																</td>
						                                        <td width="5%">
						                                        	<img width="18" border="0" onClick="deleteRowFromWeightTable(this.id)" src="<?php echo base_url(); ?>assets/cp/images/delete.gif" name="delete_at_<?php echo $row_per_table;?>_<?php echo $count?>" id="delete_at_<?php echo $row_per_table;?>_<?php echo $count?>">
						                                        </td>
						                                        <td width="10%" style="text-align:right">
						                                        	<img width="18" border="0" class="handle" src="<?php echo base_url(); ?>assets/cp/images/move.png" style="cursor: pointer;" title="<?php echo _("Drag me");?>"/>
						                                        </td>																
						                                      </tr>						                                      
						                                      <?php $row_per_table++; endforeach;?>						                                    
						                                    </tbody>
						                                </table>
						                            </td>
					                              </tr>
					                              <?php  echo '<script type="text/javascript">iteration_wt = '.$count.';</script>';?>
					                              <?php $count++;	endforeach;?>
					                            </tbody>
					                        </table>
					                    </td>
				                      </tr>
				                    </tbody>
				                  </table>
				                  <?php if($this->session->userdata('login_via') == 'mcp'){?>
				                  <div>
							         <a href= "javascript:;" onclick="apply_group_settings()">
							            Take same settings for this category.
							         </a>
						          </div>
						          <?php }?>
				             </td>
							  
			                <?php else:?>
							<!--for the addition of groups-->
			                <td style="padding-right:250px">
				                <table width="300" border="0" class="override" id="outerBigTable">
				                    <tbody>
				                      <tr>
				                        <td width="100%">
											<table width="100%" border="0" class="override" id="WGroupsTable">
					                            <tbody>
					                              <tr id="grow_0">
					                                <td width="100%">
						                                <table width="70%" border="0" class="override" id="new_wt_table0">
						                                    <tbody>
						                                      <tr>
						                                        <input type="hidden" value="0" name="count_wt_groups" id="wcount_groups">
						                                        <td width="30%">
																  <select type="select" id="wm_select0" name="wm_select0">
						                                            <option value="0"> ---- <?php echo _('Select')?> ---- </option>
						                                            <?php if($groups_wt): foreach($groups_wt as $group):?>
						                                            <option value=<?php echo $group->id?>><?php echo $group->group_name?>
						                                            <?php endforeach;?>
						                                            </option>
						                                            <?php else:?>
						                                            <option value="-1"><?php echo _('No Groups Available')?></option>
						                                            <?php endif;?>
						                                          </select>
																</td>
																<td width="35%">
							                                        <input type="checkbox" name="ms_wt0" id="ms_wt0" value="1" /><span id="chktxt_wt0" >&nbsp;<?php echo _("Multiselect Attributes");?></span>
						                                        </td>
						                                        <td width="25%">
							                                        <input type="checkbox" name="required_wt_0" id="required_wt_0" value="1" /><span id="req_txt_wt_0">&nbsp;<?php echo _("Required");?></span>
						                                        </td>
						                                        <td width="10%">
						                                        	<a href="javascript:addTableWeight()"><img width="18" border="0" src="<?php echo base_url(); ?>assets/cp/images/add.gif" id="add_0" name="add_0" /></a>
																	&nbsp;<img width="18" border="0" src="<?php echo base_url(); ?>assets/cp/images/delete.gif" id="delete_0" name="delete_0" onclick="deleteTableWeight(0);" />
						                                        </td>
						                                      </tr>
						                                    </tbody>
						                                </table>
						                            </td>
					                              </tr>
					                              
					                              <tr id="grow_0">
					                                <td width="100%" style="padding: 10px 0px 10px 30px">
						                                <table border="0" class="override" name="WAttributes_Table0" id="WAttributes_Table0">
						                                    <tbody class="sortable">
						                                      <tr id="at_0_0">
						                                        <input type="hidden" name="count_row_per_wt_group0" id="wcount_row_per_wt_group0" value="0"/>
						                                        <td width="50%">
						                                        	<input type="text" id="watt_text_0_0" name="watt_text_0[]" class="text verywidth grp_valid_check">
						                                        </td>
						                                        <td width="20%" style="padding:0px 20px 0px 10px">
						                                        	<input type="text" onBlur="close_tip(this.id);" onFocus="open_tip(this.id);" size="5" id="watt_price_0_0" name="watt_price_0[]" class="text medium grp_valid_check">						                                          
						                                          	<span class="myTooltipNone" id="tooltip_watt_price_0_0"><?php echo _('formaat 0.0000 (punt')?> , <br>
						                                          	<?php echo _('Rate to begin with - or + sign and 4 digits after the dot (eg: -42.4823 +42.4823 or')?> </span>
						                                        </td>
						                                        <td width="5%">
						                                        	<img width="18" border="0" onClick="addNewRowToWeightTable(this.id)" src="<?php echo base_url(); ?>assets/cp/images/add.gif" id="add_at_0_0" name="add_at_0_0">
						                                        </td>
						                                        <td width="5%">
						                                        	<img width="18" border="0" onClick="deleteRowFromWeightTable(this.id)" src="<?php echo base_url(); ?>assets/cp/images/delete.gif" name="delete_at_0_0" id="delete_at_0_0">
						                                        </td>
						                                        <td width="10%" style="text-align:right">
						                                        	<img width="18" border="0" class="handle" src="<?php echo base_url(); ?>assets/cp/images/move.png" style="cursor: pointer;" title="<?php echo _("Drag me");?>"/>
						                                        </td>
						                                      </tr>
						                                    </tbody>
						                                </table>
						                            </td>
					                              </tr>
					                              
					                            </tbody>
					                    	</table>
					                    </td>
				                      </tr>
				                    </tbody>
				                  </table>
				                  <?php if($this->session->userdata('login_via') == 'mcp'){?>
				                  <div>
							         <a href= "javascript:;" onclick="apply_group_settings()">
							            Take same settings for this category.
							         </a>
						          </div>
						          <?php }?>
							  </td>							  
							  <?php endif; ?>
			              </tr>
			              
			              <tr id="discount_table_row" style="display:none">
			                <td class="textlabel"><?php echo _('DISCOUNT')?>&nbsp;<?php echo _('Per unit')?>&nbsp;&nbsp;<a title="<?php echo _('You can also grant discounts per product. A "general reduction" when you give a particular product promotion is - "a quantity discount" is as follows: 1 apple = &euro; 1 / 2 apples = reduction of &euro; 0.20 = &euro; 0.80 / each ...')?>" href="#" id="help-prod3"><img width="16" height="16" src="<?php echo base_url(); ?>assets/cp/images/help.png"></a></td>
			                <td style="padding-right:50px">
				                <table width="100%" border="0" class="override" >
				                    <tbody>
				                      <tr>
				                        <td width="5%">
				                        	<input type="radio"  onclick="discount_show(this.value);" value="0" id="no_dis" name="dis" <?php //if($product_information&&$product_information['0']->discount==0){?>checked="true"<?php //}?>>
				                        </td>
				                        <td>
				                        	<strong><?php echo _('No Discount')?></strong>
				                        </td>
				                      </tr>
				                      
				                      <tr>
				                        <td>
				                        	<input type="radio" onclick="discount_show(this.value);" value="1" id="dis" name="dis" <?php if($product_information&&$product_information['0']->discount&&is_numeric($product_information['0']->discount)){?>checked="true"<?php }?>>
				                        </td>
				                        <td>
				                        	<strong><?php echo _('Discount on Add')?></strong>
				                        </td>
				                      </tr>
				                      
				                      <tr>
				                        <td>
				                        	<input type="radio"  onclick="discount_show(this.value);" value="2" id="multi_dis" name="dis" <?php if($product_information&&$product_information['0']->discount=='multi'){?>checked="true"<?php }?>>
				                        </td>
				                        <td>
				                        	<strong><?php echo _('Discount on per purchase in add')?></strong>
				                        </td>
				                      </tr>
				                      
				                      <tr>
				                        <td colspan="2">
					                        <table border="1" style="display:none" id="general" class="override">
					                            <tbody>
					                              <tr>
					                                <td height="50">
					                                	<input type="text" onBlur="close_tip(this.id);" onFocus="open_tip(this.id);" class="text medium" style="width:60px;" id="discount" name="discount" <?php if($product_information&&is_numeric($product_information['0']->discount)):?>value="<?php echo $product_information['0']->discount?>"<?php endif;?>>&nbsp;&euro;&nbsp;/&nbsp;<?php echo _('Unit')?>
					                                  	<span class="myTooltipNone" id="tooltip_discount"><?php echo _('formaat 0.0000 (punt)')?> , <br>
					                                  	<?php echo _('Discoun Ex: 12.8723');?> </span> &nbsp;&nbsp;
					                                  	<img width="16" height="16" alt="" src="<?php echo base_url();?>assets/cp/images/help.png">&nbsp;<?php echo _('example: 10.00 Or 10.5')?>
					                                </td>
					                              </tr>
					                            </tbody>
					                        </table>
					                    </td>
				                      </tr>
				                      
				                      <?php if($product_information&&$product_information['0']->discount=='multi'&&$product_discount):?>
				                      <tr>
				                        <td width="100%" align="left" style="padding-right:0px" colspan="2">				                        
					                        <table cellspacing="5" border="0" id="specific" class="myspan12" style="width:70%;">
					                            <tbody>
					                              <tr>
					                                <td width="500" align="left">
						                                <table border="0">
						                                    <tbody>
						                                      <tr>
						                                      <?php //print_r($product_information['0']->discount);?>
						                                        <td width="25%"><strong><?php echo('Number')?></strong></td>
						                                        <td width="25%"><strong><?php echo _('Discount/Piece')?></strong></td>
						                                        <td width="25%"><strong><?php echo _('Rate/piece')?></strong></td>
						                                        <td width="25%"><strong><?php echo _('Action')?></strong></td>
						                                      </tr>
						                                      
						                                      <tr>
						                                        <td width="30%"><input class="text" type="text" style="width:70px" readonly="readonly" value="1" id="default_qty" name="default_qty"></td>
						                                        <td width="30%"><input class="text" type="text" style="width:70px" readonly="readonly" value="0" id="default_discount" name="default_discount"></td>
						                                        <td width="30%"><input class="text" type="text" style="width:70px" readonly="readonly" value="" id="rate_price" name="rate_price">&nbsp;&euro;</td>
						                                        <td width="10%">&nbsp;</td>
						                                      </tr>
						                                      
						                                      <tr>
						                                        <input type="hidden"  name="count_discount_row" id="count_discount_row" value="<?php echo count($product_discount);?>"/>
						                                        <td colspan="4">
							                                        <table class="override" id="dynamic">
							                                            <tbody>
							                                              <?php for($i=0;$i<count($product_discount);$i++):?>
							                                              <tr id="rw">
							                                                <td width="30%">
							                                                	<input class="text" type="text" style="width:70px" id="<?php echo 'qty'.$i?>" name="<?php echo 'qty'.$i?>" value="<?php echo $product_discount[$i]->quantity?>"/>
							                                                </td>
							                                                <td width="30%">
							                                                	<input class="text" type="text" style="width:70px" onBlur="calcDiscPrice(this.id)" id="<?php echo 'dd'.$i?>" name="<?php echo 'dd'.$i?>" value="<?php echo $product_discount[$i]->discount_per_qty?>">
							                                                </td>
							                                                <td width="30%">
							                                                	<input class="text" type="text" style="width:70px" readonly="readonly" id="<?php echo 'dp'.$i?>" name="<?php echo 'dp'.$i?>" value="<?php echo $product_discount[$i]->price_per_qty?>">&nbsp;&euro;
							                                                </td>
							                                                <td width="10%" style="text-align:left"><img width="15" height="15" onClick="javascript:addNewRow(this.id)" title="<?php echo _('Add row')?>" name="img_add<?php echo $i?>" id="img_add<?php echo $i?>" src="<?php echo base_url();?>assets/cp/images/dis_add.png">&nbsp;<img width="15" height="15" onClick="javascript:deleteRow(this.id)" title="<?php echo _('Delete Row')?>" name="img_minus<?php echo $i?>" id="img_minus<?php echo $i?>" src="<?php echo base_url();?>assets/cp/images/dis_minus.jpg"></td>
							                                              </tr>
							                                              <?php endfor;?>
							                                            </tbody>
							                                        </table>
						                                        </td>
						                                      </tr>
						                                    </tbody>
						                                </table>
						                            </td>
					                              </tr>
					                            </tbody>
					                        </table>
					                    </td>
				                      </tr>
				                      
				                      <?php else:?>
				                      <tr>
				                        <td width="100%" align="left" style="padding-right:0px" colspan="2">
					                        <table cellspacing="5" border="0" style="display:none;width:70%;" id="specific" class="myspan12">
					                            <tbody>
					                              <tr>
					                                <td width="500" align="left">
						                                <table border="0">
						                                    <tbody>
						                                      <tr>
						                                        <td width="25%"><strong><?php echo('Number')?></strong></td>
						                                        <td width="25%"><strong><?php echo _('Discount/Piece')?></strong></td>
						                                        <td width="25%"><strong><?php echo _('Rate/piece')?></strong></td>
						                                        <td width="25%"><strong><?php echo _('Action')?></strong></td>
						                                      </tr>
						                                      
						                                      <tr>
						                                        <td width="30%">
						                                        	<input class="text" type="text" style="width:70px" readonly="readonly" value="1" id="default_qty" name="default_qty">
						                                        </td>
						                                        <td width="30%">
						                                        	<input class="text" type="text" style="width:70px" readonly="readonly" value="0" id="default_discount" name="default_discount">
						                                        </td>
						                                        <td width="30%">
						                                        	<input class="text" type="text" style="width:70px" readonly="readonly" value="" id="rate_price" name="rate_price">
						                                          	&nbsp;&euro;
						                                        </td>
						                                        <td width="10%">&nbsp;</td>
						                                      </tr>
						                                      
						                                      <tr>
						                                        <input type="hidden" value="1" name="count_discount_row" id="count_discount_row"/>
						                                        <td colspan="4">
							                                        <table class="override" id="dynamic">
							                                            <tbody>
							                                              <tr id="rw">
							                                                <td width="30%">
							                                                	<input class="text" type="text" style="width:70px" id="qty0" name="qty0"/>
							                                                </td>
							                                                <td width="30%">
							                                                	<input class="text" type="text" style="width:70px" onBlur="calcDiscPrice(this.id)" id="dd0" name="dd0">
							                                                </td>
							                                                <td width="30%">
							                                                	<input class="text" type="text" style="width:70px" readonly="readonly" id="dp0" name="dp0">
							                                           	        &nbsp;&euro;
							                                                </td>
							                                                <td width="10%" style="text-align:left"><img width="15" height="15" onClick="javascript:addNewRow(this.id)" title="<?php echo _('Add Row')?>" name="img_add0" id="img_add0" src="<?php echo base_url();?>assets/cp/images/dis_add.png">&nbsp;<img width="15" height="15" onClick="javascript:deleteRow(this.id)" title="<?php echo _('Delete Row');?>" name="img_minus0" id="img_minus0" src="<?php echo base_url();?>assets/cp/images/dis_minus.jpg"></td>
							                                              </tr>
							                                            
							                                            </tbody>
							                                        </table>
							                                    </td>
						                                      </tr>
						                                    </tbody>
						                                </table>
						                            </td>
					                              </tr>
					                            </tbody>
					                        </table>
					                    </td>
				                      </tr>
				                      <?php endif;?>
				                    </tbody>
				                </table>
				            </td>
			              </tr>
			              
			              <tr id="discount_table_person" style="display:none">
			                <td class="textlabel"><?php echo _('DISCOUNT')?>&nbsp;<?php echo _('Per p.')?>&nbsp;&nbsp;<a title="<?php echo _('You can also grant discounts per product. A "general reduction" when you give a particular product promotion is - "a quantity discount" is as follows: 1 apple = &euro; 1 / 2 apples = reduction of &euro; 0.20 = &euro; 0.80 / each ...')?>" href="#" id="help-prod3"><img width="16" height="16" src="<?php echo base_url(); ?>assets/cp/images/help.png"></a></td>
			                <td style="padding-right:50px">
				                <table width="100%" border="0" class="override" >
				                    <tbody>
				                      <tr>
				                        <td width="5%">
				                        	<input type="radio"  onclick="discount_show(this.value,false,true);" value="0" id="no_dis_p" name="dis_p" <?php //if($product_information&&$product_information['0']->discount_person==0):?>checked="true"<?php //endif;?>>
				                        </td>
				                        <td>
				                        	<strong><?php echo _('No Discount')?></strong>
				                        </td>
				                      </tr>
				                      
				                      <tr>
				                        <td>
				                        	<input type="radio" onclick="discount_show(this.value,false,true);" value="1" id="dis_p" name="dis_p"  <?php if($product_information&&$product_information['0']->discount_person&&is_numeric($product_information['0']->discount_person)):?>checked="true"<?php endif;?>>
				                        </td>
				                        <td>
				                        	<strong><?php echo _('Discount on Add')?></strong>
				                        </td>
				                      </tr>
				                      
				                      <tr>
				                        <td>
				                        	<input type="radio"  onclick="discount_show(this.value,false,true);" value="2" id="multi_dis_p" name="dis_p"  <?php if($product_information&&$product_information['0']->discount_person=='multi'):?>checked="true"<?php endif;?>>
				                        </td>
				                        <td>
				                        	<strong><?php echo _('Discount on per purchase in add')?></strong>
				                        </td>
				                      </tr>
				                      
				                      <tr>
				                        <td colspan="2">
					                        <table border="1" style="display:none" id="general_person" class="override">
					                            <tbody>
					                              <tr>
					                                <td height="50">					                                
					                                	<input type="text" onBlur="close_tip(this.id);" onFocus="open_tip(this.id);" class="text medium" style="width:60px;" id="discount_p" name="discount_p" <?php if($product_information&&is_numeric($product_information['0']->discount_person)):?>value="<?php echo $product_information['0']->discount_person?><?php endif;?>">&nbsp;&euro;&nbsp;/&nbsp;<?php echo _('p.')?>
					                                  	<span class="myTooltipNone" id="tooltip_discount"><?php echo _('formaat 0.0000 (punt)')?> , <br>
					                                  	<?php echo _('Discoun Ex: 12.8723');?> </span> &nbsp;&nbsp;
					                                  	<img width="16" height="16" alt="" src="<?php echo base_url();?>assets/cp/images/help.png">&nbsp;<?php echo _('example: 10.00 Or 10.5')?>
					                                </td>
					                              </tr>
					                            </tbody>
					                        </table>
					                    </td>
				                      </tr>
				                      
				                      <?php if($product_information&&$product_information['0']->discount_person=='multi'&&$product_discount_person):?>
				                      <tr>
				                        <td width="100%" align="left" style="padding-right:0px" colspan="2">
					                        <table cellspacing="5" border="0" id="specific_person" class="myspan12" style="width:70%;">
					                            <tbody>
					                              <tr>
					                                <td width="500" align="left">
						                                <table border="0">
						                                    <tbody>
						                                      <tr>
						                                        <td width="25%"><strong><?php echo('Number')?></strong></td>
						                                        <td width="25%"><strong><?php echo _('Discount/p.')?></strong></td>
						                                        <td width="25%"><strong><?php echo _('Rate/p.')?></strong></td>
						                                        <td width="25%"><strong><?php echo _('Action')?></strong></td>
						                                      </tr>
						                                      
						                                      <tr>
						                                        <td width="30%"><input class="text" type="text" style="width:70px" readonly="readonly" value="1" id="default_qty_p" name="default_qty_p"></td>
						                                        <td width="30%"><input class="text" type="text" style="width:70px" readonly="readonly" value="0" id="default_discount_p" name="default_discount_p"></td>
						                                        <td width="30%"><input class="text" type="text" style="width:70px" readonly="readonly" value="<?php echo round($product_information['0']->price_per_person,2);?>" id="rate_price_person" name="rate_price_person">&nbsp;&euro;</td>
						                                        <td width="10%">&nbsp;</td>
						                                      </tr>
						                                      
						                                      <tr>
						                                        <input type="hidden"  name="count_discount_row_person" id="count_discount_row_person" value="<?php echo count($product_discount_person);?>"/>
						                                        <td colspan="4">
							                                        <table class="override" id="dynamic_p">
							                                            <tbody>
							                                              <?php for($i=0;$i<count($product_discount_person);$i++):?>
							                                              <tr id="rw">
							                                                <td width="30%">
							                                                	<input class="text" type="text" style="width:70px" id="<?php echo 'qtyp'.$i?>" name="<?php echo 'qtyp'.$i?>" value="<?php echo $product_discount_person[$i]->quantity?>"/>
							                                                </td>
							                                                <td width="30%">
							                                                	<input class="text" type="text" style="width:70px" onBlur="calcDiscPrice(this.id,false,true)" id="<?php echo 'ddp'.$i?>" name="<?php echo 'ddp'.$i?>" value="<?php echo $product_discount_person[$i]->discount_per_qty?>">
							                                                </td>
							                                                <td width="30%">
							                                                	<input class="text" type="text" style="width:70px" readonly="readonly" id="<?php echo 'dp_p'.$i?>" name="<?php echo 'dp_p'.$i?>" value="<?php echo $product_discount_person[$i]->price_per_qty?>">&nbsp;&euro;
							                                                </td>
							                                                <td width="10%" style="text-align:left"><img width="15" height="15" onClick="javascript:addNewRow_p(this.id)" title="<?php echo _('Add row')?>" name="pimg_add<?php echo $i?>" id="pimg_add<?php echo $i?>" src="<?php echo base_url();?>assets/cp/images/dis_add.png">&nbsp;<img width="15" height="15" onClick="javascript:deleteRow_p(this.id)" title="<?php echo _('Delete Row')?>" name="pimg_minus<?php echo $i?>" id="pimg_minus<?php echo $i?>" src="<?php echo base_url();?>assets/cp/images/dis_minus.jpg"></td>
							                                              </tr>
							                                              <?php endfor;?>
							                                            </tbody>
							                                        </table>
						                                        </td>
						                                      </tr>
						                                    </tbody>
						                                </table>
						                            </td>
					                              </tr>
					                            </tbody>
					                        </table>
					                    </td>
				                      </tr>
				                      
				                      <?php else:?>
				                      <tr>
				                        <td width="100%" align="left" style="padding-right:0px" colspan="2">
					                        <table cellspacing="5" border="0" style="display:none;width:70%;" id="specific_person" class="myspan12">
					                            <tbody>
					                              <tr>
					                                <td width="500" align="left">
						                                <table border="0">
						                                    <tbody>
						                                      <tr>
						                                        <td width="25%"><strong><?php echo('Number')?></strong></td>
						                                        <td width="25%"><strong><?php echo _('Discount/Piece')?></strong></td>
						                                        <td width="25%"><strong><?php echo _('Rate/piece')?></strong></td>
						                                        <td width="25%"><strong><?php echo _('Action')?></strong></td>
						                                      </tr>
						                                      
						                                      <tr>
						                                        <td width="30%">
						                                        	<input class="text" type="text" style="width:70px" readonly="readonly" value="1" id="default_qty_p" name="default_qty_p">
						                                        </td>
						                                        <td width="30%">
						                                        	<input class="text" type="text" style="width:70px" readonly="readonly" value="0" id="default_discount_p" name="default_discount_p">
						                                        </td>
						                                        <td width="30%">
						                                        	<input class="text" type="text" style="width:70px" readonly="readonly" value="" id="rate_price_person" name="rate_price_person">
						                                          	&nbsp;&euro;
						                                        </td>
						                                        <td width="10%">&nbsp;</td>
						                                      </tr>
						                                      
						                                      <tr>
						                                        <input type="hidden" value="1" name="count_discount_row_person" id="count_discount_row_person"/>
						                                        <td colspan="4">
							                                        <table class="override" id="dynamic_p">
							                                            <tbody>
							                                              <tr id="rw_p">
							                                                <td width="30%">
							                                                	<input class="text" type="text" style="width:70px" id="qtyp0" name="qtyp0"/>
							                                                </td>
							                                                <td width="30%">
							                                                	<input class="text" type="text" style="width:70px" onBlur="calcDiscPrice(this.id,false,true)" id="ddp0" name="ddp0">
							                                                </td>
							                                                <td width="30%">
							                                                	<input class="text" type="text" style="width:70px" readonly="readonly" id="dp_p0" name="dp_p0">
							                                           	        &nbsp;&euro;
							                                                </td>
							                                                <td width="10%" style="text-align:left"><img width="15" height="15" onClick="javascript:addNewRow_p(this.id)" title="<?php echo _('Add Row')?>" name="pimg_add0" id="pimg_add0" src="<?php echo base_url();?>assets/cp/images/dis_add.png">&nbsp;<img width="15" height="15" onClick="javascript:deleteRow_p(this.id)" title="<?php echo _('Delete Row');?>" name="pimg_minus0" id="pimg_minus0" src="<?php echo base_url();?>assets/cp/images/dis_minus.jpg"></td>
							                                              </tr>
							                                            </tbody>
							                                        </table>
							                                    </td>
						                                      </tr>
						                                    </tbody>
						                                </table>
						                            </td>
					                              </tr>
					                            </tbody>
					                        </table>
					                    </td>
				                      </tr>
				                      <?php endif;?>
				                    </tbody>
				                </table>
				            </td>
			              </tr>
			              
			              <tr id="discount_table_wt" style="display:none">
			                <td class="textlabel"><?php echo _('DISCOUNT')?>&nbsp;<?php echo _('Per weight')?>&nbsp;&nbsp;<a title="<?php echo _('You can also grant discounts per product ....')?>" href="#" id="help-prod3"><img width="16" height="16" src="<?php echo base_url(); ?>assets/cp/images/help.png"></a></td>
			                
							<td style="padding-right:50px">
								<table width="100%" border="0" class="override" >
				                    <tbody>
				                      <tr>
				                        <td width="5%">
				                        	<input type="radio" onclick="discount_show(this.value,true,false);" value="0" id="no_dis_wt" name="dis_wt" <?php //if($product_information && $product_information['0']->discount_wt == '0'):?>checked="checked"<?php //endif;?> >
				                        </td>
				                        <td><strong><?php echo _('No Discount')?></strong></td>
				                      </tr>
				                      
				                      <tr>
				                        <td>
				                        	<input type="radio" onclick="discount_show(this.value,true,false);" value="1" id="dis_wt" name="dis_wt" <?php if($product_information && is_numeric($product_information['0']->discount_wt) && $product_information['0']->discount_wt!=0):?>checked="checked"<?php endif;?>>
				                        </td>
				                        <td><strong><?php echo _('Discount per Kg')?></strong></td>
				                      </tr>
				                      
				                      <tr>
				                        <td>
				                        	<input type="radio"  onclick="discount_show(this.value,true,false);" value="2" id="multi_dis_wt" name="dis_wt" <?php if($product_information && $product_information['0']->discount_wt == 'multi'):?>checked="checked"<?php endif;?>>
				                        </td>
				                        <td><strong><?php echo _('Multiple discount on per unit grams.')?></strong></td>
				                      </tr>
				                      
				                      <tr>
				                        <td colspan="2">
					                        <table border="1" style="display:none" id="general_wt" class="override">
					                            <tbody>
					                              <tr>
					                                <td height="50">
					                                	<input type="text" onBlur="close_tip(this.id);" onFocus="open_tip(this.id);" class="text medium" style="width:60px;" id="discount_wt" name="discount_wt" <?php if($product_information && is_numeric($product_information['0']->discount_wt)):?>value="<?php echo $product_information['0']->discount_wt?>"<?php endif;?>>
					                                	&nbsp;&euro;&nbsp;/&nbsp;kg
					                                  	<span class="myTooltipNone" id="tooltip_discount"><?php echo _('formaat 0.0000 (punt)')?> , <br>
					                                  	<?php echo _('Discoun Ex: 12.8723');?> </span> &nbsp;&nbsp;
					                                  	<img width="16" height="16" alt="" src="<?php echo base_url();?>assets/cp/images/help.png">&nbsp;<?php echo _('example: 10.00 Or 10.5')?>
					                                </td>
					                              </tr>
					                            </tbody>
					                        </table>
					                    </td>
				                      </tr>
				                      
				                      <?php if($product_information&&$product_information['0']->discount_wt=='multi'&&$product_discount_wt):?>
				                      <tr>
				                        <td width="100%" align="left" style="padding-right:0px" colspan="2">
					                        <table cellspacing="5" border="0" id="specific_wt" class="myspan12" style="width:70%;">
					                            <tbody>
					                              <tr>
					                                <td width="500" align="left">
						                                <table border="0">
						                                    <tbody>
						                                      <tr>
						                                        <td width="25%"><strong><?php echo('Grams')?></strong></td>
						                                        <td width="25%"><strong><?php echo _('Discount')?></strong></td>
						                                        <td width="25%"><strong><?php echo _('Rate')?></strong></td>
						                                        <td width="25%"><strong><?php echo _('Action')?></strong></td>
						                                      </tr>
						                                      
						                                      <tr>
						                                        <td width="30%">
						                                        	<input class="text" type="text" style="width:70px" readonly="readonly" value="1" id="default_qty_wt" name="default_qty_wt">
						                                        </td>
						                                        <td width="30%">
						                                        	<input class="text" type="text" style="width:70px" readonly="readonly" value="0" id="default_discount_wt" name="default_discount_wt">
						                                        </td>
						                                        <td width="30%">
						                                        	<input class="text" type="text" style="width:70px" readonly="readonly" value="<?php echo $product_information['0']->price_weight; ?>" id="rate_price_wt" name="rate_price_wt">
						                                          	&nbsp;&euro;
						                                        </td>
						                                        <td width="10%">&nbsp;</td>
						                                      </tr>
						                                      
						                                      <tr>
						                                        <input type="hidden"  name="count_discount_row_wt" id="count_discount_row_wt" value="<?php echo count($product_discount_wt);?>"/>
						                                        <td colspan="4">
							                                        <table class="override" id="dynamic_wt">
							                                            <tbody>
							                                              <?php for($i=0;$i<count($product_discount_wt);$i++):?>
							                                              <tr id="rw">
							                                                <td width="30%">
							                                                	<input class="text" type="text" style="width:70px" id="<?php echo 'qtyw'.$i?>" name="<?php echo 'qtyw'.$i?>" value="<?php echo $product_discount_wt[$i]->quantity?>"/>
							                                                </td>
							                                                <td width="30%">
							                                                	<input class="text" type="text" style="width:70px" onBlur="calcDiscPrice(this.id,true)" id="<?php echo 'ddw'.$i?>" name="<?php echo 'ddw'.$i?>" value="<?php echo $product_discount_wt[$i]->discount_per_qty?>">
							                                                </td>
							                                                <td width="30%">
							                                                	<input class="text" type="text" style="width:70px" readonly="readonly" id="<?php echo 'dpw'.$i?>" name="<?php echo 'dpw'.$i?>" value="<?php echo $product_discount_wt[$i]->price_per_qty?>">&nbsp;&euro;
							                                                </td>
							                                                <td width="10%" style="text-align:left">
							                                                	<img width="15" height="15" onClick="javascript:addNewRow_wt(this.id)" title="<?php echo _('Add row')?>" name="wimg_add<?php echo $i?>" id="wimg_add<?php echo $i?>" src="<?php echo base_url();?>assets/cp/images/dis_add.png">&nbsp;<img width="15" height="15" onClick="javascript:deleteRow_wt(this.id)" title="<?php echo _('Delete Row')?>" name="wmg_minus<?php echo $i?>" id="wimg_minus<?php echo $i?>" src="<?php echo base_url();?>assets/cp/images/dis_minus.jpg">
							                                                </td>
							                                              </tr>
							                                              <?php endfor;?>
							                                            </tbody>
							                                        </table>
						                                        </td>
						                                      </tr>
						                                    </tbody>
						                                </table>
					                                </td>
					                              </tr>
					                            </tbody>
					                        </table>
					                    </td>
				                      </tr>
				                      
				                      <?php else: ?>
				                      <tr>
				                        <td width="100%" align="left" style="padding-right:0px" colspan="2">
					                        <table cellspacing="5" border="0" style="display:none;width:70%;" id="specific_wt" class="myspan12">
					                            <tbody>
					                              <tr>
					                                <td width="500" align="left">
						                                <table border="0">
						                                    <tbody>
						                                      <tr>
						                                        <td width="25%"><strong><?php echo('Grams')?></strong></td>
						                                        <td width="25%"><strong><?php echo _('Discount')?></strong></td>
						                                        <td width="25%"><strong><?php echo _('Rate')?></strong></td>
						                                        <td width="25%"><strong><?php echo _('Action')?></strong></td>
						                                      </tr>
						                                      
						                                      <tr>
						                                        <td width="30%">
						                                        	<input class="text" type="text" style="width:70px" readonly="readonly" value="1" id="default_qty_wt" name="default_qty_wt">
						                                        </td>
						                                        <td width="30%">
						                                        	<input class="text" type="text" style="width:70px" readonly="readonly" value="0" id="default_discount_wt" name="default_discount_wt">
						                                        </td>
						                                        <td width="30%">
						                                        	<input class="text" type="text" style="width:70px" readonly="readonly" value="" id="rate_price_wt" name="rate_price_wt">&nbsp;&euro;
						                                        </td>
						                                        <td width="10%">&nbsp;</td>
						                                      </tr>
						                                      
						                                      <tr>
						                                        <input type="hidden" value="1" name="count_discount_row_wt" id="count_discount_row_wt"/>
						                                        <td colspan="4">
							                                        <table class="override" id="dynamic_wt">
							                                            <tbody>
							                                              <tr id="rw_wt">
							                                                <td width="30%">
							                                                	<input class="text" type="text" style="width:70px" id="qtyw0" name="qtyw0"/>
							                                                </td>
							                                                <td width="30%">
							                                                	<input class="text" type="text" style="width:70px" onBlur="calcDiscPrice(this.id,true)" id="ddw0" name="ddw0">
							                                                </td>
							                                                <td width="30%">
							                                                	<input class="text" type="text" style="width:70px" readonly="readonly" id="dpw0" name="dpw0">&nbsp;&euro;
							                                                </td>
							                                                <td width="10%" style="text-align:left">
							                                                	<img width="15" height="15" onClick="javascript:addNewRow_wt(this.id)" title="<?php echo _('Add Row')?>" name="wimg_add0" id="wimg_add0" src="<?php echo base_url();?>assets/cp/images/dis_add.png">&nbsp;<img width="15" height="15" onClick="javascript:deleteRow_wt(this.id,true)" title="<?php echo _('Delete Row');?>" name="wimg_minus0" id="wimg_minus0" src="<?php echo base_url();?>assets/cp/images/dis_minus.jpg">
							                                                </td>
							                                              </tr>
							                                            </tbody>
							                                        </table>
						                                       	</td>
						                                      </tr>
						                                    </tbody>
						                                </table>
					                                </td>
					                              </tr>
					                            </tbody>
					                        </table>
				                        </td>
				                      </tr>
				                      <?php endif;?>
				                    </tbody>
				                </table>
				            </td>
			              </tr>
			              
			              <tr>
			              	<td class="textlabel">
			              		<?php echo _("Available after");?> :
			              	</td>
			              	<td>
			              		<input type="text" class="text small" style="width: 80px;" id="available_after" name="available_after" value="<?php if($product_information && $product_information['0']->available_after){ echo $product_information['0']->available_after ; }?>" /> <?php echo _("Day(s)");?>
			              		
			              	</td>
			              </tr>
			              	<?php if( $general_settings && $general_settings[0]->hide_availability != 1 ) { ?>
                          
			              		<tr>
			                		<td class="textlabel">
										<?php echo _('Availability')?>
			                    		&nbsp;&nbsp;&nbsp;
			                    		<a title="<?php echo _('Want a product to be available days wise like all day or at any perticular day')?>" href="#" class="help-prod">
			                       			<img src="<?php echo base_url()?>assets/cp/images/help.png" />
			                    		</a>
			                		</td>
                            
			                		<td>
			                  			<label><br />
			                  		<?php echo _('All Day'); ?> :			                  
			                  			&nbsp;
			                  			<input type="checkbox" name="allday_availability" id="day0" value="1" <?php if($product_information && $product_information['0']->allday_availability == '1'):?>checked="checked"<?php endif;?> onchange="select_all(this.id,'day',7)" /></label>
			                  			<br/>
			                  		<?php //echo $product_information['0']->availability;?>
			                  		<?php 
										if($product_information && $product_information['0']->availability != ''):
											$day = json_decode($product_information['0']->availability);													
										else:
											$day = array();
										    
											if(!empty($pickup_delivery_timings))
											  for($d=1;$d<=7;$d++)
											    if(!in_array($d,$pickup_delivery_timings))
												  $day[] = $d;
										endif;
							  		?>
									  	<table width="150px" border="0">
										  	<tr>
										    	<td width="1%"> <lable><?php echo _('Monday');?>:</lable></td>
										    	<td>
										    		<input type="checkbox" name="day[]" id="day1" value="1"<?php if(!empty($day) && in_array("1", $day)):?>checked="checked"<?php endif;?> class="set_chk"/>
										    	</td>
										  	</tr>
										  
										  	<tr>
										    	<td><lable><?php echo _('Tuesday');?>:</lable></td>
										    	<td>
										    		<input type="checkbox" name="day[]" id="day2" value="2" <?php if(!empty($day) && in_array("2", $day)):?>checked="checked"<?php endif;?> class="set_chk"/>
										    	</td>
										  	</tr>
										  
										  	<tr>
										    	<td><lable><?php echo _('Wednesday');?>:</lable></td>
										    	<td>
										    		<input type="checkbox" name="day[]" id="day3" value="3" <?php if(!empty($day) && in_array("3", $day)):?>checked="checked"<?php endif;?> class="set_chk"/>
										    	</td>
										  	</tr>
										  
										  	<tr>
										    	<td><lable><?php echo _('Thursday');?>:</lable></td>
										    	<td>
										    		<input type="checkbox" name="day[]" id="day4" value="4" <?php if(!empty($day) && in_array("4", $day)):?>checked="checked"<?php endif;?> class="set_chk"/>
										    	</td>
										  	</tr>
										  
										  	<tr>
										    	<td><lable><?php echo _('Friday');?>:</lable></td>
										    	<td>
										    		<input type="checkbox" name="day[]" id="day5" value="5" <?php if(!empty($day) && in_array("5", $day)):?>checked="checked"<?php endif;?> class="set_chk"/>
										    	</td>
										  	</tr>
										  
										  	<tr>
										    	<td><lable><?php echo _('Saturday');?>:</lable></td>
										    	<td>
										    		<input type="checkbox" name="day[]" id="day6" value="6" <?php if(!empty($day) && in_array("6", $day)):?>checked="checked"<?php endif;?> class="set_chk"/>
										    	</td>
										  	</tr>
										  
										  	<tr>
										    	<td><lable><?php echo _('Sunday');?>:</lable></td>
										    	<td>
										    		<input type="checkbox" name="day[]" id="day7" value="7" <?php if(!empty($day) && in_array("7", $day)):?>checked="checked"<?php endif;?> class="set_chk"/>
										    	</td>
											</tr>
										</table>
									    <script type="text/javascript">
											  $(document).ready(function(){
												  $('.set_chk').click(function(){
												  
												      var id = $(this).attr('id');
												  
												      if( !$('#'+id).is(':checked') ){
													      $('input[name=allday_availability]').attr('checked',false);
													  };
													 
												  });										  		  
											  });
										</script>
										<br/>
										<br/>								
			                		</td>
			              		</tr>                         
                          	<?php } ?>
                          	
                          	 <tr>
			                      <td class="textlabel">
			                      	<?php echo _('Exceptions')?>
			                      	<a title="<?php echo _('It must take heigher priority than days in availability')?>" href="#" class="help-prod">
			                       		<img src="<?php echo base_url()?>assets/cp/images/help.png" />
			                    	</a>
			                      </td>
			                      <td>
			                      	<table class="override same_day_exp">
			                      	<tbody>
			                      	<tr>
				                      		<td style="padding-top: 10px;border-bottom: 1px solid #e3e3e3;"colspan="2">
	                  							<label class="holiday_label"><?php echo _('Holidays'); ?></label>
	                  							
				                      			<a title="<?php echo _('Check this checkbox for product available based on exceptions')?>" href="#" class="help-prod">
			                       					<img src="<?php echo base_url()?>assets/cp/images/help.png" />
			                    				</a>
			                    				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	                  							<input type="checkbox" name="Holiday_availability" value="1" <?php if($product_information && $product_information['0']->holiday_availability == '1'):?>checked="checked"<?php endif;?> />
				                      			
				                      		</td>
				                      	</tr>
			                      	</tbody>
				                      	<tbody>
					                      	<tr>
					                      		<td style="padding-top: 10px;width: 259px">
						                      		<div>
						                      			<label><?php echo _('Date that products are available');?></label>
													</div>
						                      	</td>
						                      	<td id="prod_avai">
								                    <?php $date_available = explode("#",$product_information[0]->date_available);?>
								                    <?php $total_date_available = count($date_available);?>
								                    
								                    <?php foreach ($date_available as $key=>$val){?>
									                    <table>
							                      			<tbody>
							                      				<tr>
										                      		<td style="padding-top: 10px;width: 259px;">
																		<div style="float:left"><input type="text" class="text" readonly="readonly" name="product_available[]" id="start_date_<?php echo $key;?>" value="<?php echo $val;?>"></div>
														  				<div style="float:left"><input type="button" value="..." onclick="displayCalendar(document.webshop_addedit.start_date_<?php echo $key;?>,'dd/mm/yyyy',this)"></div>
																	</td>
																	<td>
											                        	<img data_attr="available" width="18" border="0" onClick="javascript:addNewavaipro(this)" src="<?php echo base_url(); ?>assets/cp/images/add.gif">
										                        		<img data_attr="available" width="18" border="0" onClick="javascript:deleteavaipro(this)" src="<?php echo base_url(); ?>assets/cp/images/delete.gif">
											                        </td>
											                     </tr>
											                     </tbody>
									  					</table>
							                       <?php 
								  					}?> 
							  					</td>
						                    <input type="hidden" name="product_date_available" id="product_date_available" value="<?php echo $total_date_available;?>">    
					                      	</tr>
					                      	<tr>
					                      		<td style="padding-top: 10px;width: 259px">
					                      			<div>
					                      				<label><?php echo _('Date that products are unavailable');?></label>
													</div>	
					                      		</td>
					                      		<td id="prod_notavai">
							                    <?php $date_unavailable = explode("#",$product_information[0]->date_unavailable);?>
							                    <?php $total_date_unavailable = count($date_unavailable);?>
							                    <?php foreach ($date_unavailable as $key=>$val){?>
								                     <table>
								                      	<tbody>
								                      		<tr>
									                      		<td style="padding-top: 10px;width: 259px">
																	<div style="float:left"><input type="text" class="text" readonly="readonly" name="product_notavailable[]" id="start_date_un_<?php echo $key;?>" value="<?php echo $val;?>"></div>
												  					<div style="float:left"><input type="button" value="..." onclick="displayCalendar(document.webshop_addedit.start_date_un_<?php echo $key;?>,'dd/mm/yyyy',this)"></div>
																</td>
																<td>
										                        	<img data_attr="unavailable" width="18" border="0" onClick="javascript:addNewavaipro(this)" src="<?php echo base_url(); ?>assets/cp/images/add.gif">
									                        		<img data_attr="unavailable" width="18" border="0" onClick="javascript:deleteunavaipro(this)" src="<?php echo base_url(); ?>assets/cp/images/delete.gif">
										                        </td>
										                     </tr>
										                 </tbody>
										             </table>
						                        <?php 
							  						}?>
							  					</td> 
							  					<input type="hidden" name="product_date_unavailable" id="product_date_unavailable" value="<?php echo $total_date_unavailable;?>">
					                      	</tr>
				                      	</tbody>
			                      	</table>
				             	</td>
				             	<script>
				             	function addNewavaipro(obj){
					             	var html='';
					             	var length_a = $('#product_date_available').val();
					             	var length_n = $('#product_date_unavailable').val();
					             	if($(obj).attr('data_attr') == 'available')
					             	{
				             			length_a = parseInt(length_a)+1;
					             	}
					             	if($(obj).attr('data_attr') == 'unavailable')
					             	{
				             			length_n = parseInt(length_n)+1;
					             	}
				             		
				             		html+='<table>';
				             		html+='<tbody>';
				             		html+='<tr>';
				             		html+='<td style="padding-top: 10px;width: 259px">';
				             		if($(obj).attr('data_attr') == 'available')
				             		{
				             			html+='<div style="float:left"><input type="text" class="text" readonly="readonly" name="product_available[]" id="start_date_'+length_a+'"></div>';
				             			html+='<div style="float:left"><input type="button" value="..." onclick="displayCalendar(document.webshop_addedit.start_date_'+length_a+',\'dd/mm/yyyy\',this)"></div>';
				             		}
				             		else
				             		{
				             			html+='<div style="float:left"><input type="text" class="text" readonly="readonly" name="product_notavailable[]" id="start_date_un_'+length_n+'"></div>';
				             			html+='<div style="float:left"><input type="button" value="..." onclick="displayCalendar(document.webshop_addedit.start_date_un_'+length_n+',\'dd/mm/yyyy\',this)"></div>';
				             		}
					             	html+='</td>';
				             		html+='<td>';
				             		if($(obj).attr('data_attr') == 'available')
				             		{
				             			html+='<img data_attr="available" width="18" border="0" onClick="javascript:addNewavaipro(this)" src="'+base_url+'assets/cp/images/add.gif">';
				             			html+='<img data_attr="available" width="18" border="0" onClick="javascript:deleteavaipro(this)" src="'+base_url+'assets/cp/images/delete.gif">';
				             		}
				             		else
				             		{
				             			html+='<img data_attr="unavailable" width="18" border="0" onClick="javascript:addNewavaipro(this)" src="'+base_url+'assets/cp/images/add.gif">';
				             			html+='<img data_attr="unavailable" width="18" border="0" onClick="javascript:deleteunavaipro(this)" src="'+base_url+'assets/cp/images/delete.gif">';
				             		}
					             	html+='</td>';
				             		html+='</tr>';
				             		html+='</tbody>';
				             		html+='</table>';
									$(obj).parents().eq(4).append(html);
				             		$('#product_date_available').val(length_a);
				             		$('#product_date_unavailable').val(length_n);
				             	}

				             	
				             	
								function deleteavaipro(obj)
								{
									var length = $('#product_date_available').val();
									length = parseInt(length);
									
									if(length == 1)
									{
										$(obj).closest('tr').children('td').find('input[type=text]').val('');
										
									}
									else
									{
										$(obj).closest('table').remove();
										$('#product_date_available').val(length-1);
									}
								}

								function deleteunavaipro(obj)
								{
									var length = $('#product_date_unavailable').val();
									length = parseInt(length);
									
									if(length == 1)
									{
										$(obj).closest('tr').children('td').find('input[type=text]').val('');
										
									}
									else
									{
										$(obj).closest('table').remove();
										$('#product_date_unavailable').val(length-1);
									}
								}

				             	
				             	</script>
			                 </tr>
	                  
					            <tr>
						        	<td class="textlabel">
						               	<?php echo _('Recommend this')?><br/>
						               	(<?php echo _("only shown on bestelonline.nu");?>)
						            </td>
						            <td style="padding-right:250px">
						               	<input type="checkbox"  value="1" class="checkbox" id="recommend" name="recommend" <?php if($product_information&&$product_information['0']->recommend=='1'):?>checked="checked"<?php endif;?>>
						            </td>
					            </tr>
			              	<?php if(@$show_payment_setting == 'true'){?>
			              	<?php //if($general_settings && $general_settings[0]->online_payment==1) { ?>
			              		<tr>
			                		<td class="textlabel" valign="top"><?php echo _('Payment')?></td>
			                		<td>
			                			<input type="radio" name="advance_payment" value="1" <?php if($product_information && $product_information['0']->advance_payment==1):?>checked="checked"<?php endif;?>/>
			                  			&nbsp;<?php echo _('In advance'); ?> <br />
			                  			<input type="radio" name="advance_payment" value="0" <?php if($product_information && $product_information['0']->advance_payment==0):?>checked="checked"<?php endif;?> checked="checked"/>
			                  			&nbsp;<?php echo _('Client may choose'); ?>
			                		</td>
			              		</tr>
			              	<?php //} ?>
			              	<?php }?>
			              		<!-- start -->
			              				 <tr>
                    		<td class="textlabel">
                    			<?php echo _('Other Product Images Upload')?>&nbsp;
                    			<a title="<?php echo _('Please upload multiple rectangle image in jpg/gif/png format')?>" href="#" id="help-prod1">
                    				<img width="16" height="16" src="<?php echo base_url(); ?>assets/cp/images/help.png">
                    			</a>
                    		</td>
                   			<td style="padding-right:00px">
                   			<?php for($c = 1; $c <4; $c++){?>
                   				<div>
	                   				<div id="uploaded_image<?php echo $c;?>"></div>
	                   				<div>
	                   					<a href="javascript:;" class="thickboxed_gal" data-count="<?php echo $c;?>" style="text-decoration: none;">
	                   						<input type="button" name="upload_image<?php echo $c;?>" id="upload_image<?php echo $c;?>" value="<?php echo _("Upload Image Here");?>" />
	                   					</a>
	                   				</div>
                   				</div>
                   				<?php }?>
                   			</td>
                   			
			            </tr>
			            <?php if($product_information):?>
			              	<tr>
			                	<td class="textlabel"><?php echo _('Current other images')?></td>
			                	<td class="more_img" style="padding-right:250px">
			                	<?php if($product_information['0']->more_image != '') { ?>
			                		<?php $more_img = explode(':::', $product_information['0']->more_image);?>
			                		<?php foreach ($more_img as $key=>$img){?>
			                		<div>
				                  		<img src="<?php echo base_url(); ?>assets/cp/images/product/<?php echo $img;?>" alt="<?php echo _('Image not available.')?>" style="height:300px"/>
				                  		<a href="javascript:;" class="remove_image_more" rel="<?php echo $product_information['0']->id;?>"><?php echo _('Remove'); ?></a>
			                  		</div>
			                  		<?php }?>
			                  <?php } else { ?>
			                  		<img src="<?php echo base_url(''); ?>assets/cp/images/product/no_image.jpg" alt="<?php echo _('No image available.Please upload.')?>"/>
			                  <?php } ?>
			                	</td>
			              	</tr>
			              <?php endif;?>

			              			<!-- end -->
			              		<tr>
						        	<td class="textlabel">
						               	<?php echo _('Show this item related with')?>
						            </td>
						            <td style="padding-right:250px">
						            <div>
						            	<span class="rel_p_f">
						            		<select id="rel_cat">
						            			<option value="-1">-- <?php echo _('Select category')?> --</option>

				   		            		<?php foreach($category_data as $category):?>
				   		            			
				                    			<option value="<?php echo $category->id?>"><?php echo $category->name?></option>
						                    <?php endforeach;?></select>
						                </span>
						            	<span class="rel_p_f">
						            		<select id="rel_subcat">
						            			<option value="-1">-- <?php echo _('Select subcategory')?> --</option>
						            		</select>
						            	</span>
						            	<span class="rel_p_f">
						            		<select id="rel_prod">
						            			<option value="-1">-- <?php echo _('Select product');?> --</option>
						            		</select>
						            	</span>
						            	</div>
						            	<div>
						            	<input id="sel_prod" name="sel_prod">
						            	</div>
						            </td>
					            </tr>
					            <tr>
					            	<td class="textlabel">
						               	<?php echo _('Stock')?>
						               	<a title="<?php echo _('Max amount of articles that can be ordered')?>" href="#" id="help-prod1">
                    						<img width="16" height="16" src="<?php echo base_url(); ?>assets/cp/images/help.png">
                    					</a>
						            </td>
						             <td width="30%">
						                <input class="text" type="text" style="width:70px"  value="<?php if($product_information):echo stripslashes($product_information['0']->stock_qty);endif;?>" name="obs_stock_qty">
                                    </td>

					            </tr>
			       			</tbody>
			       		</table>
			       		<input type="hidden" name="proname" id="h_proname" <?php if($product_information):?>value="<?php echo stripslashes($product_information['0']->proname);?>"<?php endif;?>>
			       		<input type="hidden" name="categories_id" id="h_categories_id" <?php if(($product_information) && $product_information['0']->categories_id):?>value="<?php echo $product_information['0']->categories_id;?>"<?php endif;?>>
			       		<input type="hidden" name="subcategories_id" id="h_subcategories_id" <?php if(($product_information) && $product_information['0']->subcategories_id):?>value="<?php echo $product_information['0']->subcategories_id;?>"<?php endif;?>>
        			</div>
        			<?php if($product_information):?>
        			<input type="hidden" value="<?php echo $product_information['0']->id?>" name="prod_id" class="prod_id" >
					<div class="sub_div">
					    <div class="sub__div" colspan="2">
					        <input type="button" value="<?php echo _("Update");?>" class="submit" id="webshop_update" name="webshop_update">
					        <input type="hidden" value="add_edit" id="webshop_act" name="webshop_act">
					    	<input type="hidden" value="update" id="webshop_add_update" name="ajax_add_update">
					    	<input type="hidden" value="webshop" name="action">
					    	<input type="button" value="<?php echo _('Save & next')?>" class="submit" id="webshop_savenext" name="webshop_savenext">
						</div>
					</div>
					<?php else:?>
						<div class="sub_div">
						    <div class="sub__div" colspan="2">
						        <input type="button" value="<?php echo _('Send')?>" class="submit" id="webshop_add" name="webshop_add">
						      	<input type="hidden" value="add" id="webshop_add_update" name="ajax_add_update">
						      	<input type="hidden" value="webshop" name="action">
						      	<input type="button" value="<?php echo _('Save & next')?>" class="submit" id="webshop_savenext" name="webshop_savenext">
						   	</div>
						</div>
					<?php endif;?>
				</form>
				<script>
				var x = document.forms["webshop_addedit"]["weight_per_unit"].value;
				if (isNaN(x)) 
					  {
					    //alert("<?php echo _("Weight must be numeric")?>");
					   
					  } 
				</script>
        		</div>        		
        	</div>
			<input type="hidden" value="" id="hidden_fdds_quantity" name="hidden_fdds_quantity">
		    <input type="hidden" value="" id="hidden_own_pro_quantity" name="hidden_own_pro_quantity">
		    <input type="hidden" value="0" id="hidden_fdd_total" name="hidden_fdd_total">
			<input type="hidden" value="0" id="hidden_own_total" name="hidden_own_total">
		<!--</form>-->
	</div>
</div>