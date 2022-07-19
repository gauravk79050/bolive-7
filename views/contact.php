<style>
.mainbanner{
background-image:url(<?php echo base_url();?>assets/images/online-bestellen3.jpg);
}
div.success{
    background: none repeat scroll 0 0 #D6F9D6;
    border: 1px dashed green;
    color: green;
    font-weight: bold;
    padding: 5px;
}
div.error{
    background: none repeat scroll 0 0 #FCE3E6;
    border: 1px dashed red;
    color: red;
    font-weight: bold;
    padding: 5px;
}
</style>
<div class="mainbanner">
	<div class="bannerrtxt">
    	<p><?php echo _('More income')?>: <?php echo _('customers familiar with this system will make ordering faster since they will experience many benefits');?>..<a target="_blank" href="/Docs/OBS-FLYER.pdf"></a></p>
	</div>
</div>	
<div class="midlftmain">
	<h4><?php echo _('Contact us');?></h4>
  	<p><strong><?php echo _('Are you the owner of a retail business and would like more information about this bestelysteem');?>?</strong></p>
  	<p><strong><?php echo _('Please');?> <a href="<?php echo base_url();?>welcome/register"><?php echo _('this entry form');?></a> <?php echo _('to be used');?>.</strong><br>
    <br>
  	<p></p>
  	<p><?php echo _('Do you have a general question, comment or are you interested in a collaboration? Let us know via the form below');?>!</p>
  	<p>&nbsp;</p>
	<p>
	<?php 
		$message = $this->messages->get(); 
		if($message != array()){
			if($message['success'] != array()){
				foreach($message['success'] as $msg){
					echo '<div class="success">'.$msg.'</div>';
				}
			}
			if($message['error'] != array()){
				foreach($message['error'] as $msg){
					echo '<div class="error">'.$msg.'</div>';
				}
			}
		}
	?>
	</p>
  	<div class="form">
    	<form id="contactform" method="post" action="<?php echo base_url();?>welcome/contact">
      		<input type="hidden" value="ONLINEBESTELSYSTEEM.NET" name="wat">
      		<p>
        	<label for="name"><?php echo _('company Name');?></label>
        	<input type="text" tabindex="1" id="company_name" class="input" name="company_name">
        
			<label for="name"><?php echo _('name');?> *</label>
        	<input type="text" tabindex="2" id="user_name" class="input" name="user_name">
        
			<label for="email">Email *</label>
        	<input type="text" tabindex="3" id="email" class="input" name="email">
        
			<label for="tel"><?php echo _('phone');?> *</label>
        	<input type="text" tabindex="4" id="phone" class="input" name="phone">
        
			<label for="url">website</label>
        	<input type="text" value="http://www." tabindex="5" id="url" class="input" name="url">
        
			<label for="wat"><?php echo _('Selection');?></label>
        	<select tabindex="6" id="subject" name="subject">
          		<option selected="selected" value = "-1">--- <?php echo _('Select');?> ---</option>
          		<option value="<?php echo _('I have a general question');?>"><?php echo _('I have a general question');?></option>
          		<option value="<?php echo _('I have a comment');?>"><?php echo _('I have a comment');?></option>
          		<option value="<?php echo _('Please more info');?>"><?php echo _('I want to be distributor of OBS')?> - <?php echo _('Please more info');?></option>
          		<option value="<?php echo _('remainder')?>"><?php echo _('remainder')?></option>
        	</select>
        
			<label for="message"><?php echo _('report');?> </label>
        	<textarea cols="25" rows="4" tabindex="6" id="message" name="message"></textarea>
        	
			<label for="message"><?php echo _('security code');?> </label>
			<?php echo $captcha['image'];?>
        	<br/>
			<label for="code"><?php echo _('Repeat the code');?>:</label>
        	<input type="text" size="20" id="captcha_field" class="input" name="captcha_field">
      
	  		</p>
      		<p>&nbsp;</p>
      		<p>&nbsp;</p>
      		<p>&nbsp;</p>
     		<p><input type="submit" tabindex="7" class="send3" value="<?php echo _('send');?>" name="submit"></p>
      		<br/>
      		<br/>
      		<p> </p>
      		<div align="center">(*) <?php echo _('required fields');?></div>
      		<p></p>
    	 </form>
	
		<script type="text/javascript" language="javascript">
		
			var frmValidator = new Validator("contactform");
			
			frmValidator.EnableMsgsTogether();
			
			frmValidator.addValidation("subject","dontselect=-1","<?php echo _('Select a subject');?>");
			frmValidator.addValidation("company_name","req","<?php echo _('Please enter a company name.'); ?>");
			frmValidator.addValidation("user_name","req","<?php echo _('Please enter your name.'); ?>");	
			frmValidator.addValidation("email","req","<?php echo _('Please give your email address.'); ?>");	
			frmValidator.addValidation("email","email","<?php echo _('Enter email address is not valid.'); ?>");	
			frmValidator.addValidation("phone","req","<?php echo _('Please enter a valid phone number'); ?>");
			frmValidator.addValidation("url","req","<?php echo _('Please give your website URL.'); ?>");	
			frmValidator.addValidation("message","req","<?php echo _('Please give your message.'); ?>");	
			frmValidator.addValidation("captcha_field","req","<?php echo _('Please enter security code.'); ?>");
				
		</script>
  	</div>
  <!-- WHEN LOGGED IN -->
</div>
