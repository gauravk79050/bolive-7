<?php
$messages = $this->messages->get();
if(!empty($messages)):
	foreach($messages as $key => $val):
		if(!empty($val)):
			echo '<script type="text/javascript">alert("'.$val[0].'");</script>';
		endif;
	endforeach;
endif;
?>
   <!-- start of main body -->
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td height="175" align="center"></td>
      </tr>
      <tr>
        <td align="center"><table width="430" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td width="128" height="164" bgcolor="#003366"><img src="<?php echo base_url('')?>assets/mcp/images/password.png" width="128" height="128" /></td>
              <td width="302" valign="top" bgcolor="#8992AD" style="padding-left:5px">
			   <form action="<?php echo base_url();?>mcp/mcplogin/forgot_password" name = "forgot_password" id = "forgot_password" method="post">

                <table width="293" border="0" cellspacing="0" cellpadding="0">
                  <tr>

                    <td height="26" colspan="2" align="left" valign="top" class="whiteMediumBold" style="padding-top:2px; border-bottom:1px solid #000000"><?php echo _('Enter your email address to Get your password');?> </td>
                  </tr>
                  <tr>
                    <td height="30" align="left" class="whileSmallBold">&nbsp;</td>
                    <td align="left" class="whileSmallBold">&nbsp;</td>
                  </tr>
                  <tr>
                    <td width="80" height="24" align="left" class="whiteSmallBold"><?php echo _('Email')?></td>

                    <td width="215" align="left"><?php
					 
					 echo form_input(array('name'=>"email",'id'=>"email",'class'=>"textbox"));?></td>
                  </tr>
                   <tr>
                    <td height="10" colspan="2" align="left"></td>

                  </tr>
                  <tr>
                    <td align="left">&nbsp;</td>
                    <td align="left"><?php echo form_submit(array('name'=>'reset_password','id'=>'reset_password','class'=>'btnWhiteBack'),"Reset Passord");?></td>
					<?php $js='onclick="history.back()"'?>
					<td align="left"><?php echo form_submit(array('name'=>'back','id'=>'back','class'=>'btnWhiteBack'),"Back",$js);?></td>
                  </tr>
                  <tr>
                    <td height="22">&nbsp;</td>
                  </tr>
                </table>
               </form>
				<script type="text/javascript">
					var frmvalidator = new Validator("forgot_password");
					frmvalidator.EnableMsgsTogether();
					frmvalidator.addValidation("email","req","<?php echo _('Please enter your Email address');?>");
					frmvalidator.addValidation("email","email","<?php echo _('Please enter valid Email address');?>");
				</script>              
              </td>
            </tr>
          </table></td>
      </tr>
    </table>

    <!-- end of main body -->
  </div>
