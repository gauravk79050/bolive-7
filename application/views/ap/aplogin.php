<?
$messages = $this->messages->get();
if($messages && !empty($messages)):
foreach($messages as $key => $val):
if(!empty($val)):
echo '<script type="text/javascript">alert("'.$val[0].'");</script>';
endif;
endforeach;
endif;
?>
<?php if(isset($message)){echo $message;}?>
<style>
   #header{
		border-bottom:25px solid #003366;
		height:auto;
  }
  .whiteSmallBold { 
	vertical-align:top;
  }
</style>
 <div style="width:100%">
    <!-- start of main body -->
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td height="175" align="center"></td>
      </tr>
      <tr>
        <td align="center"><table width="430" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td width="128" height="164" bgcolor="#003366"><img src="<?php echo base_url('')?>assets/mcp/images/login-icon.png" width="128" height="128" /></td>
              <td width="302" valign="top" bgcolor="#8992AD" style="padding-left:5px">
			   <?php echo form_open( base_url()."ap/aplogin/",array('name'=>"frm_login",'id'=>"frm_login",'method'=>"post"));?>

                <table width="293" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td height="26" colspan="2" align="left" valign="top" class="whiteMediumBold" style="padding-top:2px; border-bottom:1px solid #000000"><?php echo _('Please login to proceed');?> </td>
                  </tr>
                  <tr>
                    <td height="30" align="left" class="whileSmallBold">&nbsp;</td>
                    <td align="left" class="whileSmallBold">&nbsp;</td>
                  </tr>
                  <tr>
                    <td width="80" height="24" align="left" class="whiteSmallBold"><?php echo _('Username')?></td>

                    <td width="215" align="left"><?php echo form_input(array('name'=>"a_username",'id'=>"a_username",'class'=>"textbox"));?></td>
                  </tr>
                  <tr>
                    <td height="24" align="left" class="whiteSmallBold"><?php echo _('Password')?></td>
                    <td align="left"><?php echo form_password(array('name'=>"a_password",'id'=>"a_password",'class'=>"textbox"));?></td>
                  </tr>
                  <tr>
                    <td height="10" colspan="2" align="left"></td>
                  </tr>
                  <tr>
                    <td align="left">&nbsp;</td>
                    <td align="left" class="login_btn"><?php echo form_submit("btn_submit","Login");?></td>
                  </tr>
                  <tr>
                    <td height="22">&nbsp;</td>
                    <td align="right" valign="bottom">&nbsp;</td>

                  </tr>
                </table>
                <?php echo form_close();?>  
				<script type="text/javascript">
					var frmvalidator = new Validator("frm_login");
					frmvalidator.EnableMsgsTogether();
					frmvalidator.addValidation("a_username","req","<?php echo _('Please enter your login Username');?>");
					frmvalidator.addValidation("a_password","req","<?php echo _('Please enter your login Password');?>");
				</script>             
              </td>
            </tr>
          </table></td>
      </tr>
    </table>

    <!-- end of main body -->
  </div>