<link rel="stylesheet" href="<?php echo base_url();?>assets/css/ui/jquery.ui.all.css">
<script src="<?php echo base_url();?>assets/js/ui/jquery.ui.core.js?version=<?php echo version;?>"></script>


<script src="<?php echo base_url();?>assets/js/ui/jquery.ui.position.js?version=<?php echo version;?>"></script>
<script>
	/* FOR SORTABLE */
	var fixHelper = function(e, ui) {
		ui.children().each(function() {
			$(this).width($(this).width());
		});
		return ui;
	};

	jQuery(document).ready(function($) {
		$(".sortable").sortable({
			helper: fixHelper,
			handle: '.handle',
			cursor: "move"/*,
			update: function( event, ui ) {alert(ui.placeholder.toSource());}*/
		});
  	});
</script>
<!-- MAIN -->
<div id="main">
	<div id="main-header">

    	<h2><?php echo _('View Subcategories');?></h2>

      	<span class="breadcrumb"><a href="<?php echo base_url()?>cp"><?php echo _('Home');?></a>&raquo;<?php echo _('Subcategories');?></span>
	  
	</div>
	
	<div style="display:none"  id="result" class="notification"><?php echo _('Error Occured');?></div>
	
	<div style="display:none"  id="succeed_order_update"><?php echo _('Sub-category Order Updated Successfully');?></div>
   		
	<div style="display:none" id="error_order_update"><?php echo _('Error occurred while updating order');?></div>
		  
	<div style="display:none" id="succeed_status"><?php echo _('Status successfully updated.');?></div>
  
	<div style="display:none" id="error_status"><?php echo _('Error occurred while updating status');?></div>

	<div style="display:none" id="succeed_tool_tip"><?php echo _('Tooltip setting successfully.');?></div>

	<div style="display:none" id="error_tool_tip"><?php echo _('Problem while saving the ToolTip setting.');?></div>
  	<?php $messages = $this->messages->get();  ?>
	
	<div id="messages">
	<?php
	// display all messages
	if (is_array($messages)):
		foreach ($messages as $type => $msgs):
			if (count($msgs > 0)):
				foreach ($msgs as $message):
					echo ('<span class="' .  $type .'">' . $message . '</span>');
			   endforeach;
		   endif;
		endforeach;
	endif;
	?>
	</div>

	<div id="content">

		<div id="content-container">

			<div class="box">

          		<h3><?php echo _('Subcategories Data');?></h3>

          		<div class="table">

	            	<table cellspacing="0">
	              		 <thead>
	              		 	<tr></tr>
	                		<!-- <tr>
	                		
	                  			<td class="notice_text" colspan="7" style="text-align:center"><?php echo _('** You can choose in which order the subcategories to be displayed on the website. Number 1 is very high on the list.')?>
								</td>
	                		
	                		</tr> -->
	                	</thead>
	                	<tbody>

	                		
			                <tr>
			
			                	<td align="right" colspan="7">
								    
									<table width="100%" cellspacing="10" cellpadding="0" border="0" class="override">
			
			                     		<tbody>
			
			                      	  		<tr>
			
			                        	  		<td width="40%" style="text-align:right"><?php echo _('Select Category')?></td>
			
												<td width="3%"></td>
			
			                         	 		<td>
													 <form action="<?php echo base_url()?>cp/subcategories/subcategories_addedit/add" name="category_select" id="category_select" method="post">
													 <select style="margin:0px" onChange="inCategory(this.value);" type="select" id="categories_id" name="categories_id">
						
						                              <option value="-1">-- <?php echo _('Select Category');?> --</option>
														<?php if($category_data):?>
													 		<?php foreach($category_data as $category):?>
						                             			 <option value="<?php echo $category->id;?>"<?php if($category->id==$cat_id):?> selected="selected"<?php endif;?>><?php echo $category->name;?></option>
															<?php endforeach;?>
														<?php else:?>
														<option value="-2">-- <?php echo ('No Category Available');?> --</option>
						                            	 <?php endif;?>
													  </select>
													  </form>
												</td>
			
			                       			</tr>
			
			                      		</tbody>
			
			                    	</table>
			                    </td>
			
			                </tr>
	
			                <tr>
			
			                	<td colspan="7" style="text-align:right"><a href="<?php echo base_url()?>cp/subcategories/subcategories_addedit/add" onclick="document.category_select.submit()"><?php echo _('Add Subcategory')?></a></td>
			
			                </tr>
						</tbody>
					</table>
					
					<form id="subcat_list" method="post" action="<?php echo current_url();?>">
					<table cellspacing="0">
						<thead>
			                <tr>
			
			                  <th><?php echo _('ord.')?></th>
			                  <th><?php echo _('Subcategory')?></th>
			                  <th><?php echo _('Description')?></th>
			                  <th style="text-align:center"><?php echo _('Popup')?></th>
			                  <th><?php echo _('Status')?></th>
			                  <!-- <th><?php echo _('Action')?></th> -->
			
			                </tr>
	              		</thead>
	
			            <tbody class="sortable">
							<?php if($subcategory_data):?>
							<?php foreach($subcategory_data as $subcategory):?>
							<tr>
			
			                	<td>
			                		<img width="18" border="0" class="handle" src="<?php echo base_url(); ?>assets/cp/images/move.png" style="cursor: pointer;" title="<?php echo _("Drag me");?>"/>
			                		<input type="hidden" name="sub_cat_ids[]" class="sub_cat_ids" value="<?php echo $subcategory->id; ?>" />
			                    </td>
			
			                  	<td><?php echo $subcategory->subname?></td>
			
								<td><?php echo $subcategory->subdescription?></td>
			
				                <td style="text-align:center"><?php if($subcategory->display_tool_tip): ?>
									<img id="display_tool_tip_<?php echo $subcategory->id; ?>" style="cursor:pointer" onClick="display_tool_tip(<?php echo $subcategory->id; ?>, 'subcategories')" src="<?php echo base_url(); ?>assets/cp/images/chk_on.png">
									<?php else: ?>
									<img src="<?php echo base_url(); ?>assets/cp/images/chk_off.png" onClick="display_tool_tip(<?php echo $subcategory->id; ?>, 'subcategories')" style="cursor: pointer;" id="display_tool_tip_<?php echo $subcategory->id; ?>">
									<?php endif; ?>
								</td>
			
			                  	<td>
			                  		<div class="select-popup-container">
			                      		<div style="display: none; position:absolute" id="subcategory_option_div_<?php echo $subcategory->id; ?>" class="popup-tools cm-popup-box hidden"> <img width="13" height="13" border="0" onClick="close_option(<?php echo $subcategory->id; ?>, 'subcategory')" src="<?php echo base_url(); ?>assets/cp/images/icon_close.gif" alt="" class="close-icon no-margin cm-popup-switch">
			                        		<ul class="cm-select-list">
										  		<li>
										  			<a id="show_<?php echo $subcategory->id; ?>" class="status-link-a cm-active" href="javascript:void(0)" onClick="return update_status(<?php echo $subcategory->id; ?>, '1', '<?php echo _('Show'); ?>', 'subcategory');"><?php echo _('Show'); ?></a>
										  		</li>
												<li>
													<a id="hide_<?php echo $subcategory->id; ?>" class="status-link-d cm-ajax" href="javascript:void(0)" onClick="return update_status(<?php echo $subcategory->id; ?>, '0', '<?php echo _('Hide'); ?>', 'subcategory');"><?php echo _('Hide'); ?></a>
												</li>
			                        		</ul>
			                      		</div>
			                      		<div onClick="open_option(<?php echo $subcategory->id; ?>, 'subcategory')" id="subcategory_div_<?php echo $subcategory->id; ?>" class="selected-status status-a cm-combination cm-combo-on"> <a id="status_value_<?php echo $subcategory->id; ?>" class="cm-combo-on cm-combination"> <?php if($subcategory->status=='0'): ?><?php echo _('Hide'); ?><?php else:?><?php echo _('Show'); ?><?php endif;?></a> </div>
			                    	</div>
			                    </td>
			
								<!-- <td width="90px"><a href="<?php echo base_url().'cp/subcategories/subcategories_addedit/update/'.$subcategory->id?>" class="edit"> <img width="16" height="16" border="0" alt="Edit" src="<?php echo base_url(); ?>assets/cp/images/edit.gif"></a> | <a onClick="return confirmation('<?php echo $subcategory->id; ?>');" href="#" class="delete"><img width="16" height="16" border="0" alt="remove" src="<?php echo base_url(); ?>assets/cp/images/delete.gif"></a></td> -->
			
			                </tr>
			                
							<?php endforeach;?>
							<tr>
			                	<td colspan="6" style="padding: 10px 60px 10px 20px;text-align: right;"><input type="submit" id="update" name="update" value="<?php echo _('Update');?>" /></td>
			                </tr>
							<?php endif;?>
			                <tr>
							<?php if(!$subcategory_data):?>
			                  <td colspan="7"><span class="field-error"><?php echo _('Subcategy list is empty')?></span></td>
							<?php endif;?>
			                </tr> 
			
			            </tbody>
	
	            	</table>
	            	</form>
	
	            	<!-- <ul class="pagination">
						<?php echo $links; ?>
	            	</ul> -->

          		</div>

        	</div>

      	</div>

     </div>
    <!-- /content -->
