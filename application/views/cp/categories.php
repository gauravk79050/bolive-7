
<script type="text/javascript">
jQuery(document).ready(function($) {

	var fixHelper = function(e, ui) {
		ui.children().each(function() {
			$(this).width($(this).width());
		});
		return ui;
	};

	$(".categories_sort").sortable({
		helper: fixHelper,
		cursor: "move"
	});
});
</script>
<!-- MAIN -->
<div id="main">
	<div id="main-header">
    	<h2><?php echo _('Manage Categories'); ?></h2>
      	<span class="breadcrumb"><a href="<?php echo base_url()?>cp"><?php echo _('Home')?></a> &raquo; <?php echo _('Categories')?></span>
	</div>
    	<div style="display:none" id="succeed_order_update"><?php echo _('Category Order Updated Successfully')?></div>
   		<div style="display:none" id="error_order_update"><?php echo _('Error occurred while updating order')?></div>
		<div style="display:none" id="succeed_status"><?php echo _('Status successfully updated')?></div>
   		<div style="display:none" id="error_status"><?php echo _('Error occurred while updating status')?></div>
		<div style="display:none" id="succeed_tool_tip"><?php echo _('Tooltip setting successfully saved')?></div>
    	<div style="display:none" id="error_tool_tip"><?php echo _('Problem while storing the Tooltip setting')?></div>
    
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
          		<h3><?php echo _('Category Data'); ?></h3>
          		<div class="table">
          		<form action="<?php echo base_url()?>cp/categories" enctype="multipart/form-data" method="post">
            		<table cellspacing="0">
              			<thead>
                			<!-- <tr>
                  				<td class="notice_text" colspan="8" style="text-align:center">** <?php echo _('You can choose in which order the categories are shown on the website. Number 1 is very high on the list.'); ?></td>
                			</tr> -->
                			<tr></tr>
                			<!-- <tr>
                  				<td colspan="8" style="text-align:right"><a href="<?php echo base_url(); ?>cp/categories/categories_addedit"><?php echo _('Add Category'); ?></a></td>
                			</tr> -->
                			<tr>
                  				<th><?php echo _('ord.'); ?></th>
                  				<th><?php echo _('Category'); ?></th>
                  				<th><?php echo _('Description'); ?></th>
                  				<th><?php echo _('Select Service'); ?></th>
                  				<th style="text-align:center"><?php echo _('Display Tooltip'); ?></th>
                  				<th><?php echo _('Status'); ?></th>
                  				<!-- <th><?php echo _('Action'); ?></th> -->
                			</tr>
						</thead>
						  
              			 <tbody class="categories_sort">
                			<?php if(sizeof($categories) > 0): ?>
							<?php foreach($categories as $category): ?>
							<tr>
                  				<td>
                  					<input type="hidden" value="<?php echo $category->id;?>" name="category_sort[]">
                    				<img src="<?php echo base_url();?>assets/cp/images/move.png" width="16" style="vertical-align: middle; cursor: pointer;"/>
                    			</td>
                  				<td width="130px"><?php echo $category->name; ?></td>
                  				<td><?php echo $category->description; ?></td>
                  				<td><select onchange="setServiceType(this.value,<?php echo $category->id; ?>)" style="width:80px; margin:0;" class="select" type="select" id="service_type" name="service_type">
										<option value="0" <?php if($category->service_type == 0): ?>selected="selected"<?php endif;?>><?php echo _('Both'); ?></option>
										<option value="1" <?php if($category->service_type == 1): ?>selected="selected"<?php endif;?>><?php echo _('Pickup'); ?></option>
										<option value="2" <?php if($category->service_type == 2): ?>selected="selected"<?php endif;?>><?php echo _('Delivery'); ?></option>
                    				</select></td>
                  				<td style="text-align:center">
									<?php if($category->display_tool_tip): ?>
									<img id="display_tool_tip_<?php echo $category->id; ?>" style="cursor:pointer" onclick="display_tool_tip(<?php echo $category->id; ?>, 'categories')" src="<?php echo base_url(); ?>assets/cp/images/chk_on.png">
									<?php else: ?>
									<img src="<?php echo base_url(); ?>assets/cp/images/chk_off.png" onclick="display_tool_tip(<?php echo $category->id; ?>, 'categories')" style="cursor: pointer;" id="display_tool_tip_<?php echo $category->id; ?>">
									<?php endif; ?>
				  				</td>
                  				<td><div class="select-popup-container">
                      					<div style="display: none; position:absolute" id="category_option_div_<?php echo $category->id; ?>" class="popup-tools cm-popup-box hidden"> <img width="13" height="13" border="0" onclick="close_option(<?php echo $category->id; ?>, 'category')" src="<?php echo base_url(); ?>assets/cp/images/icon_close.gif" alt="" class="close-icon no-margin cm-popup-switch">
                        					<ul class="cm-select-list">
						  						<li><a id="show_<?php echo $category->id; ?>" class="status-link-a cm-active" href="javascript:void(0)" onclick="return update_status(<?php echo $category->id; ?>, '1', '<?php echo _('Show'); ?>', 'category');">
						  <?php echo _('Show'); ?></a></li>
                          						<li><a id="hide_<?php echo $category->id; ?>" class="status-link-d cm-ajax" href="javascript:void(0)" onclick="return update_status(<?php echo $category->id; ?>, '0', '<?php echo _('Hide'); ?>', 'category');">
						  <?php echo _('Hide'); ?></a></li>
                        					</ul>
                      					</div>
                      					<div onclick="open_option(<?php echo $category->id; ?>, 'category')" id="category_div_<?php echo $category->id; ?>" class="selected-status status-a cm-combination cm-combo-on"> <a id="status_value_<?php echo $category->id; ?>" class="cm-combo-on cm-combination"> <?php if($category->status=='0'): ?><?php echo _('Hide'); ?><?php else:?><?php echo _('Show'); ?><?php endif;?></a> </div>
                    					</div></td>
                  				<!-- <td width="90px"><a href="<?php echo base_url().'cp/categories/categories_addedit/update/'.$category->id?>" class="edit"> <img width="16" height="16" border="0" alt="Edit" src="<?php echo base_url(); ?>assets/cp/images/edit.gif"></a> | <a onclick="return confirmation('<?php echo $category->id; ?>');" href="#" class="delete"><img width="16" height="16" border="0" alt="remove" src="<?php echo base_url(); ?>assets/cp/images/delete.gif"></a></td> -->
                			</tr>
							<?php endforeach; ?>
							<?php else: ?>
							<tr>
								<td colspan="8"><?php echo _('There is no category available.')?></td>
							</tr>				
							<?php endif; ?>
							<tr>
								<td colspan="8"><input type="submit" value="<?php echo _('UPDATE');?>" class="submit" id="btn_update" name="btn_update">
                    			<input type="hidden" value="update_categories" id="act" name="act"></td>
                    		</tr>
						</tbody>
						
						</form>
            		</table>
          		</div>
        	</div>
      	</div>
	</div>
