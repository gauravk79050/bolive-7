<script src="<?php echo base_url(); ?>assets/plugins/jquery-ui-1.12.0.custom/jquery-ui.min.js?version=<?php echo version;?>" type="text/javascript"></script>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/jquery-ui-1.12.0.custom/jquery-ui.theme.min.css">
<style type="text/css">
.inp_fld{
	background: none repeat scroll 0 0 #FCFCFC;
    border: 1px solid #CCCCCC;
    color: #333333;
    display: inline;
    padding: 5px;
}
#fdd_credits {
    color: red;
    text-align: right;
}

</style>
<script type="text/javascript">
   function delete_favourite_products( fdd_pro_id ) {
    if( confirm( 'Are you sure you want to delete this ?' ) ) {
      jQuery.ajax({
          type:'POST',
          url: base_url+'cp/products/delete_favourite_products',
          data:{
            fdd_pro_id : fdd_pro_id
          },
          success: function(response){
            if( response.trim() == 'success' ) {
              jQuery( document ).find( '#fdd_pro_id_'+fdd_pro_id ).remove();
            }
          }
      });
    }
   }
</script>
<!-- MAIN -->
<div id="main">
	<div id="main-header">
    	<h2><?php echo _('Favourite-Products');?></h2>
     	<span class="breadcrumb"><a href="<?php echo base_url();?>cp/products/"><?php echo _('Home')?></a> &raquo; <?php echo _('Favourite-Products');?></span>
	</div>

    <div id="content">
		<div id="content-container">
        	<div class="box">
          		<h3><?php echo _('Product details')?></h3>
          		<div class="table">
					<table>
                  		<tr>
                    		<td colspan="9" style="text-align:center"></td>
                  		</tr>
                   	</table>
                   	<form method="post" action="">
						<table cellspacing="0">
              <tbody>
                <tr id="notice"><td id="notice_msg"  colspan='2'><?php echo _("The section has been added in the new Controlepanel(2.0)" ); ?><?php echo "<br>"; ?><?php echo _("Please proceed with new Controlepanel(2.0)")?><?php echo "<br>"; ?><a target="_blank" style="color: #8c8c00;text-decoration: none;" href="<?php echo rtrim($this->config->item('new_obs_url'),'/') ?>"><?php echo rtrim($this->config->item('new_obs_url'),'/') ?></a></td></tr>
              </tbody>
						<!-- 	<thead>
                  				<tr>
                    				<th><?php echo _('Product Name')?></th>
                    				<th><?php echo _('Action')?></th>
                  				</tr>
                  				<tr>
                  					<td colspan="9"></td>
                  				</tr>

                			</thead>
                			<tbody class="sortable">
			                <?php
			                 if( isset( $products ) && !empty( $products )):?>
								          <?php foreach( $products as $key => $value ):?>
                          <tr id="fdd_pro_id_<?php echo $value['p_id']; ?>">
                                    <td>
                            <input type="hidden" name="ids[]" value="<?php echo $value['p_id']; ?>" />
                              <input type="text" name="proname_<?php echo $value['p_id']; ?>" value="<?php echo stripslashes($value['pro_name']);?>" size="20" class="inp_fld text medium" />
                            </td>
                          <td width="90px" >
                            <a onclick="return delete_favourite_products('<?php echo $value['p_id']; ?>');" href="#" class="delete"><img width="16" height="16" border="0" alt="remove" src="<?php echo base_url(); ?>assets/cp/images/delete.gif"></a>
                          </td>
                        </tr>
                        <?php endforeach;?>
				  			<?php else:?>
                  <tr>
                            <td colspan="9"><span class="field-error"><?php echo _('Product list is empty.')?></span></td>
                          </tr>
							<?php endif;?>
                		</tbody> -->
              		</table>
              		</form>
              		<div style="margin-left:50px"> </div>
				</div>
        	</div>
      	</div>
    </div>
    <!-- <div id="credit_require" style="display: none">
   		<p><?php echo _('Sorry! Currently You have no credit to use a FoodDESK product.');?></p>
   		<p><?php echo _('To buy credits, choose a package.');?></p>
   		<ul>
   			<li><a onclick="add_credit(100)" href="javascript:;"><?php echo _('100 products/credits for 10');?>&euro;</a></li>
   			<li><a onclick="add_credit(200)" href="javascript:;"><?php echo _('200 products/credits for 15');?>&euro;</a></li>
   		</ul>
    </div> -->
    <!-- /content -->