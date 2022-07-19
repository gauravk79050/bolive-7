<style>
label{display: inline;}
</style>
<div id="loadingmessage" style="display:none;margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(102, 102, 102); z-index: 30001; opacity: 0.8;">
  <img src="<?php echo base_url(''); ?>assets/cp/images/ajax-loading.gif" style="position: absolute; color: White; top: 50%; left: 45%;"/>
</div>
<!-- MAIN -->
<div id="main">
	<div id="main-header">
		<h2><?php echo _('LOGIN DETAILS');?></h2>
		<span class="breadcrumb"><a href="<?php echo base_url()?>cp/cdashboard/"><?php echo _('Home')?></a> &raquo; <a href="<?php echo base_url()?>cp/cdashboard/products"><?php echo _(' Products')?></a> &raquo;</span>
		<?php if($this->session->flashdata('login_details')){?>
			<div id="succeed"><strong><?php echo _('Succeed')?></strong> : <?php echo _('login details updated');?></div>
		<?php }?>
	</div>

	<div id="content">
		<div id="content-container">
			<!-- <div class="box" id="approve-box" <?php if($is_approved) echo 'style="display:none"';?>>
				<h3><?php echo _('Approve')?></h3>
				<div style="padding: 20px;">
					<?php echo _('Here comes a text that will get from our lawyer where they approve that we may use there login details to login and retrieve their sheets. Somehow the admin must approve this by sending us mail. Once approved the admin can add his login details.')?>
				</div>
				<div style="padding: 10px; text-align: center;">
					<input type="button" value="<?php echo _("Approve");?>" class="submit" id="approve_login" name="approve_login" style="color:green">
					<input type="button" value="<?php echo _("Reject");?>" class="submit" id="reject_login" name="reject_login" style="color:red">
				</div>
			</div> -->
			<div class="box" id="detail-box" <?php if(!$is_approved) echo 'style="display:none"';?>>
				<h3><?php echo _('Login Details')?></h3>
				<table>
					<tbody>
						 <tr id="notice"><td id="notice_msg"  colspan='2'><?php echo _("The section has been added in the new Controlepanel(2.0)" ); ?><?php echo "<br>"; ?><?php echo _("Please proceed with new Controlepanel(2.0)")?><?php echo "<br>"; ?><a target="_blank" style="color: #8c8c00;text-decoration: none;" href="<?php echo rtrim($this->config->item('new_obs_url'),'/') ?>"><?php echo rtrim($this->config->item('new_obs_url'),'/') ?></a></td></tr>
					</tbody>
				</table>
				<!-- <div style="padding: 10px;"><?php echo _("Please fill your login details and we will use this to retrieve your sheets without abusing it. PLEASE FILL IN THE EXACT LOGINCODE that you received from producer.")?></div>
				<form id="login_add" name="login_add" method="post" action="<?php echo base_url()?>cp/cdashboard/login_details">
				<?php if($login){?>
				<div class="table">
					<table border="0" id="login_table" class="override">
						<tbody>
							<input type="hidden" id="count_row" name="count_row" value="<?php if($login){echo count($login); }?>">
						<?php $count=0;foreach($login as $key=>$specific_login){?>
							<tr>
								<td>
									<select id="att_prod_<?php echo $count;?>" name="att_prod[]" type="select" style="width:175px">
										<option value="0"><?php echo _('Select producer'); ?></option>
									<?php foreach ($producers as $producer){?>
										<option value="<?php echo $producer['s_id']; ?>" <?php if($specific_login['fdd_producer_id'] == $producer['s_id']){ echo 'selected="selected"';}?>><?php echo stripslashes( str_replace( '"' ,"'", $producer['s_name'] ) ); ?></option>
									<?php }?>
									</select>
								</td>
								<td>
									&nbsp;<strong><?php echo _('OR');?></strong>
								</td>
								<td>
									<select id="att_supp_<?php echo $count;?>" name="att_supp[]" type="select" style="width:175px">
										<option value="0"><?php echo _('Select supplier'); ?></option>
									<?php foreach ($suppliers as $supplier){?>
										<option value="<?php echo $supplier['rs_id']; ?>" <?php if($specific_login['fdd_supplier_id'] == $supplier['rs_id']){ echo 'selected="selected"';}?>><?php echo stripslashes(  str_replace( '"' ,"'", $supplier['rs_name'] ) );?></option>
									<?php }?>
									</select>
								</td>
								<td>
									&nbsp;<label><?php echo _("Username/email:");?></label>
									<input type="text" class="text medium" name="att_name[]" id="att_name_<?php echo $count;?>" value="<?php echo $specific_login['username']?>" autocomplete="off">
								</td>
								<td>
									<label><?php echo _("Password:");?></label>
									<input type="text" class="text medium" name="att_pass[]" id="att_pass_<?php echo $count;?>" value="<?php echo $specific_login['password']?>" autocomplete="off">
								</td>
								<td style="width: 25px;">
									<img border="0" name="add_a_<?php echo $count;?>" id="add_a_<?php echo $count;?>" src="<?php echo base_url();?>assets/cp/images/add.gif" onclick="javascript:addNewLoginRow(this.id);">
								</td>
								<td style="width: 25px;">
									<img border="0" name="delete_a_<?php echo $count;?>" id="delete_a_<?php echo $count;?>" src="<?php echo base_url();?>assets/cp/images/delete.gif" onclick="javascript:deleteLoginRow(this.id);" style="width:18px">
								</td>
							</tr>
							<?php $count++;}?>
							<tr>
								<td>
									<select id="att_prod_<?php echo $count;?>" name="att_prod[]" type="select" style="width:175px">
										<option value="0"><?php echo _('Select producer'); ?></option>
									<?php foreach ($producers as $producer){?>
										<option value="<?php echo $producer['s_id']; ?>" ><?php echo stripslashes( str_replace( '"' ,"'", $producer['s_name'] ) ); ?></option>
									<?php }?>
									</select>
								</td>
								<td>
									&nbsp;<strong><?php echo _('OR');?></strong>
								</td>
								<td>
									<select id="att_supp_<?php echo $count;?>" name="att_supp[]" type="select" style="width:175px">
										<option value="0"><?php echo _('Select supplier'); ?></option>
									<?php foreach ($suppliers as $supplier){?>
										<option value="<?php echo $supplier['rs_id']; ?>" ><?php echo stripslashes( str_replace( '"' ,"'", $supplier['rs_name'] ) );?></option>
									<?php }?>
									</select>
								</td>
								<td>
									&nbsp;<label><?php echo _("Username/email:");?></label>
									<input type="text" class="text medium" name="att_name[]" id="att_name_<?php echo $count;?>" autocomplete="off">
								</td>
								<td>
									<label><?php echo _("Password:");?></label>
									<input type="text" class="text medium" name="att_pass[]" id="att_pass_<?php echo $count;?>" autocomplete="off">
								</td>
								<td style="width: 25px;">
									<img border="0" name="add_a_<?php echo $count;?>" id="add_a_<?php echo $count;?>" src="<?php echo base_url();?>assets/cp/images/add.gif" onclick="javascript:addNewLoginRow(this.id);">
								</td>
								<td style="width: 25px;">
									<img border="0" name="delete_a_<?php echo $count;?>" id="delete_a_<?php echo $count;?>" src="<?php echo base_url();?>assets/cp/images/delete.gif" onclick="javascript:deleteLoginRow(this.id);" style="width:18px">
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<?php }else{?>
					<div class="table">
					<table border="0" id="login_table" class="override">
						<tbody>
							<input type="hidden" id="count_row" name="count_row" value="0">
							<tr>
								<td>
									<select id="att_prod_0" name="att_prod[]" type="select" style="width:175px">
										<option value="0"><?php echo _('Select producer'); ?></option>
									<?php foreach ($producers as $producer){?>
										<option value="<?php echo $producer['s_id']; ?>" ><?php echo stripslashes( str_replace( '"' ,"'",  $producer['s_name'] ) ); ?></option>
									<?php }?>
									</select>
								</td>
								<td>
									&nbsp;<strong><?php echo _('OR');?></strong>
								</td>
								<td>
									<select id="att_supp_0" name="att_supp[]" type="select" style="width:175px">
										<option value="0"><?php echo _('Select supplier'); ?></option>
									<?php foreach ($suppliers as $supplier){?>
										<option value="<?php echo $supplier['rs_id']; ?>" ><?php echo stripslashes( str_replace( '"' ,"'",  $supplier['rs_name'] ) );?></option>
									<?php }?>
									</select>
								</td>
								<td>
									&nbsp;<label><?php echo _("Username/email:");?></label>
									<input type="text" class="text medium" name="att_name[]" id="att_name_0" autocomplete="off">
								</td>
								<td>
									<label><?php echo _("Password:");?></label>
									<input type="text" class="text medium" name="att_pass[]" id="att_pass_0" autocomplete="off">
								</td>
								<td style="width: 25px;">
									<img border="0" name="add_a_0" id="add_a_0" src="<?php echo base_url();?>assets/cp/images/add.gif" onclick="javascript:addNewLoginRow(this.id);">
								</td>
								<td style="width: 25px;">
									<img border="0" name="delete_a_0" id="delete_a_0" src="<?php echo base_url();?>assets/cp/images/delete.gif" onclick="javascript:deleteLoginRow(this.id);" style="width:18px">
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<?php }?>
			    <div colspan="5" style="padding: 10px; text-align: right;">
			        <input type="submit" value="<?php echo _("SAVE");?>" class="submit" id="save_login_details" name="save_login_details">
			        <input type="hidden" value="add_details" name="add_login_details">
				</div>
				</form> -->
			</div>
		</div>
	</div>
	<script type="text/javascript">
	jQuery(document).ready(function(){
		$("#approve_login").click(function(){
			var agree = confirm("<?php echo _('An email will be sent on behalf of admin to us as your approval. Are you sure you want to approve this?')?>");
			if(agree){
				$('#loadingmessage').show();
		    	$.post(
					"<?php echo base_url()?>cp/cdashboard/login_details",
				   	{"approve":1},
				   	function(data){
				   		$('#loadingmessage').hide();
				   		if(data){
				   			alert("<?php echo _('Mail sent successfully')?>");
				   			$("#approve-box").css("display","none");
				   			$("#detail-box").css("display","");
				   		}
					}
				);
			}
		});
		$("#reject_login").click(function(){
			 window.history.back();
		});
	});

	function addNewLoginRow(att_id){
		var AttRow_count = $("#login_table tr").length;
		var iteration2 = AttRow_count;

		$("#count_row").val(iteration2);

		var new_row = '';
		new_row += '<tr>';
		new_row += '	<td>';
		new_row += '		<select name="att_prod[]" id="att_prod_'+iteration2+'" style="width:175px">';
		new_row += '			<option value="0"><?php echo _("Select producer"); ?></option>';
							<?php foreach ($producers as $producer){?>
		new_row += "			<option value='<?php echo $producer['s_id'] ?>' ><?php echo stripslashes( str_replace( '"' ,"'", $producer['s_name'] ) ) ?></option>";
							<?php }?>
		new_row += '		</select>';
		new_row += '	</td>';
		new_row += '	<td>';
		new_row += "		&nbsp;<strong><?php echo _('OR');?></strong>";
		new_row += '	</td>';
		new_row += '	<td>';
		new_row += '		<select name="att_supp[]" id="att_supp_'+iteration2+'" style="width:175px">';
		new_row += "			<option value='0'><?php echo _("Select supplier"); ?></option>";
							<?php foreach ($suppliers as $supplier){?>
		new_row += "			<option value='<?php echo $supplier['rs_id'] ?>' ><?php echo stripslashes( str_replace( '"' ,"'", $supplier['rs_name'] ) ) ?></option>";
							<?php }?>
		new_row += '		</select>';
		new_row += '	</td>';
		new_row += '	<td>';
		new_row += "		&nbsp;<label><?php echo _('Username/email:');?></label>";
		new_row += '		<input type="text" name="att_name[]" id="att_name_'+iteration2+'" size="5" class="text medium" autocomplete="off">';
		new_row += '	</td>';
		new_row += '	<td>';
		new_row += "		<label><?php echo _('Password:');?></label>";
		new_row += '		<input type="text" name="att_pass[]" id="att_pass_'+iteration2+'" size="5" class="text medium">';
		new_row += '	</td>';
		new_row += '	<td style="width: 25px;">';
		new_row += '		<img border="0" src="'+base_url+'assets/cp/images/add.gif" onclick="javascript:addNewLoginRow(this.id)" name="add_a_'+iteration2+'" id="add_a_'+iteration2+'">';
		new_row += '	</td>';
		new_row += '	<td style="width: 25px;">';
		new_row += '		<img border="0" src="'+base_url+'assets/cp/images/delete.gif" onclick="javascript:deleteLoginRow(this.id)" name="delete_a_'+iteration2+'" id="delete_a_'+iteration2+'" style="width:18px">';
		new_row += '	</td>';
		new_row += '</tr>';

		$("#login_table").find('tr:last').after(new_row);
	}

	function deleteLoginRow(deleteid){
		var del_id = deleteid;
		var row_id = parseInt(del_id.charAt(9));
		var last_row_delete = $("#login_table tr").length;

		var count = $("#count_row").val();

		if(last_row_delete > 1){
			if(row_id > 0){
				$('#'+deleteid).parent('td').parent('tr').remove();
				$("#count_row").val(count-1);
			}
		}
	}
	</script>