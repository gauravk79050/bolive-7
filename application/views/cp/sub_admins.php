<script type="text/javascript">
function get_login(id,username,password)
{
   //alert( id+'--'+username+'--'+password );
   
   var access_super = $('#access_super').val();
   
   if( access_super )
   {
	   $('#login_'+id).html('<img src="<?php echo base_url(''); ?>assets/mcp/images/ajax-loader.gif">');
	   
	   $.post('<?php echo base_url(''); ?>cp/login/validate',
			 {
				'act':'do_login',
				'submit':'LOGIN',
				'username':username,
				'password':password
				
			 },function(data){
				
				window.location = '<?php echo base_url(''); ?>cp';

	         });
   }
   else
   {
      alert("<?php echo _('Please set your code first, to return to super admin.'); ?>");
   }
}
</script>
<!-- MAIN -->
<div id="main">
<div id="main-header">
  <h2> <?php echo _('Manage Sub Admins')?></h2>
  <span class="breadcrumb"><a href="<?php echo base_url()?>cp/cdashboard"><?php echo _('Home')?> </a>&raquo;<?php echo _('Sub Admins')?> </span> </div>
  <?php $messages = $this->messages->get();?>
	<?php if($messages != array()):?>
		<?php foreach($messages as $key => $val):?>
			<?php foreach($val as $v):?>
				<div  class = "<?php echo $key;?>"><strong><?php echo $key;?> : </strong><?php echo $v;?></div>
			<?php endforeach;?>	
		<?php endforeach;?>
    <?php endif;?>
	
	
<div id="content">
  <div id="content-container">
  
  <div class="box">
  	<h3  id="r_holiday"> <?php echo _('Sub-Admins')?> </h3>
      <div style="padding: 0px; display:block;" class="inside">
        <div class="table">
			<?php //print_r($sub_companies); ?>
			<table cellspacing="0">
              <thead>
                <tr>
					<th><?php echo _('Company Name'); ?></th>
					<th><?php echo _('Contact Name'); ?></th>
					<th><?php echo _('Email'); ?></th>
					<th><?php echo _('Phone'); ?></th>
					<th><?php echo _('Address'); ?></th>
					<th><?php echo _('Shop Status'); ?></th>
					<th><?php echo _('Order Actions'); ?></th>
					<th>&nbsp;</th>
				</tr>
              </thead>
			  <tbody>
			  <?php if(!empty($sub_companies)) { ?>
			  <?php foreach($sub_companies as $sc) { ?>
			    <tr>
					<td><?php echo $sc->company_name; ?></td>
					<td><?php echo $sc->first_name; ?>&nbsp;<?php echo $sc->last_name; ?></td>
					<td><?php echo $sc->email; ?></td>
					<td><?php echo $sc->phone; ?></td>
					<td><?php echo $sc->address; ?>&nbsp;<?php echo $sc->city; ?>&nbsp;<?php echo $sc->zipcode; ?></td>
					<td><?php if($sc->approved==1 && $sc->status==1) { ?><span style="color:#009933"><?php echo _('Shop Online'); ?></span><?php } else if($sc->approved==0 || $sc->status==0) { ?><span style="color:red"><?php echo _('Shop Offline'); ?></span><?php } ?>
					</td>
					<td style="text-align:center"><?php if($sc->delivery_service && $sc->pickup_service) { ?><strong><?php echo _('Both'); ?></strong><?php } elseif($sc->delivery_service && !$sc->pickup_service) { ?><strong><?php echo _('Delivery'); ?></strong><?php } elseif(!$sc->delivery_service && $sc->pickup_service) { ?><strong><?php echo _('Pickup'); ?></strong><?php } else { ?><strong><?php echo _('Not Set'); ?></strong><?php } ?></td>
					<td style="text-align:center"><a class="div_<?php echo $sc->id; ?> thickbox" href="#TB_inline?height=200&width=300&inlineId=inline_<?php echo $sc->id; ?>" title="Login to <?php echo $sc->company_name; ?>"><img width="40" height="40" border="0" src="<?php echo base_url(''); ?>assets/cp/images/login-sub.png"></a>
			
					</td>
				</tr>
			
				<div style="display: none;">
					<div id="inline_<?php echo $sc->id; ?>" style="padding:20px; background:#fff; margin:3px;">
					<p style="height:30px; font-size:14px"><?php echo _('Username'); ?> : <strong><?php echo $sc->username; ?></strong></p>
					<p style="height:30px; font-size:14px"><?php echo _('Password'); ?> : <strong><?php echo $sc->password; ?></strong></p>
					<a style="display:block; text-align:center" href="javascript:void(0);" onclick="get_login('<?php echo $sc->id; ?>','<?php echo $sc->username; ?>','<?php echo $sc->password; ?>');" id="login_<?php echo $sc->id; ?>"><img border="0" src="<?php echo base_url(''); ?>assets/cp/images/ClientLogin2.jpg" height="30" width="60"></a>
					<p></p>
					</div>
				</div>
			 <?php } ?>
			 <?php } else { ?>
			    <tr>
				   <td colspan="8" style="padding:10px;color:red;font-weight:bold;">
				      <?php echo _('No sub-admins registered !'); ?>
				   </td>
				</tr>
			 <?php } ?>
				
				<tr>
					<td colspan="8">
					<form action="<?php echo base_url(''); ?>cp/cdashboard/sub_admins" method="post" id="frm_access_code" name="frm_access_code">
                        <input type="hidden" value="settings" name="OBS BEstelsysteem - SiteMatic BVBA_REF_VIEW">
						<table border="0">
						<tbody><tr>
							<td><?php echo _('Your code to return to Super Admin'); ?>&nbsp;:&nbsp;<input type="text" maxlength="4" style="width:70px" value="<?php echo $super_admin_login_code; ?>" class="short" id="access_super" name="access_super">&nbsp;&nbsp;<input type="submit" value="UPDATE" class="submit" id="access_update" name="access_update"><input type="hidden" value="edit_access_code" id="act" name="act"></td>
						</tr>	
						</tbody></table>
						
					</form>
					<script type="text/javascript" language="javascript">
						var frmValidator = new Validator("frm_access_code");
						frmValidator.EnableMsgsTogether();
						frmValidator.addValidation("access_super","req","<?php echo _('Please enter your access code. (4-Digit) '); ?>");	
					</script>
					</td>
				</tr>
			  </tbody>
            </table>
			
		</div>
	  </div>
  </div>
  
  </div><!---/content-container--->
</div><!-- /content -->  