<!DOCTYPE html>
<html>
    <head>
        <title>Cardgate Payment Demo</title>

        <meta charset="utf-8">
        <meta http-equiv="content-type"    content="text/html; charset=utf-8"/>
        <meta name="author"                content="Card Gate B.V." />
        <meta name="description"        content=""  />
        <meta name="keywords"            content="" />

        <script type="text/javascript">


            function show_bank(pay_method) {
                var o = document.getElementById("cgp_option");
                var oSub = document.getElementById("cgp_suboption");
                if (pay_method == 'ideal') {
                    oSub.style.visibility = 'visible';
                    oSub.focus();
                    o.style.width = '97px';
                } else {
                    if (pay_method == '' || pay_method == 'onbekend') {
                        o.style.width = '180px';
                    } else {
                        o.style.width = '97px';
                    }
                    oSub.style.visibility = 'hidden';
                }
            }

            function cgval(oForm) {

          
                if (oForm.input_amount.value == '' || Math.valueOf(oForm.input_amount.value) <= 0) {
                    alert("<?php echo _('Invalid amount!'); ?>");
                    return false;
                }
                else if (oForm.cgp_option.selectedIndex == 0) {
                    alert("<?php echo _('Select a payment option!'); ?>");
                    return false;
                }
                else {
                	selected = document.getElementById('cgp_option').value;
                    if( selected == 'ideal' && oForm.cgp_suboption.selectedIndex == -1) {
                    alert("<?php echo _('Select your bank!'); ?>");
                    return false;
                    }
                } 
            }
        </script>
        <style type="text/css">
            <!--
           
            #input_amount {width:80px;}
            #cgp_suboption {width:149px;}
            .tr {height:30px;}
            -->
        </style>
       <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/css/cardgate.css">
    </head>
    <body>
    <div id="wrapper">
    	<header id="mainheader">
			<div class="container"></div>	
		</header>
		<div class="container">
		
	        <form name="cgp_payment" method="post" action="<?php echo $_FormAction ?>" target="<?php echo $_FormTarget ?>" onsubmit="return cgval(this);">    
	            <input type="hidden" name="siteid" value="<?php echo $iSiteID ?>">
	            <input type="hidden" name="hash" value="<?php echo $sHash ?>">
	            <input type="hidden" name="test" value="<?php echo $sPrefix == 'TEST' ? 1 : 0 ?>">
	            <input type="hidden" name="language" value="nl">
	            <input type="hidden" name="return_url" value="<?php echo $return_url ?>">
	            <input type="hidden" name="return_url_failed" value="<?php echo $return_url ?>">
	            <input type="hidden" namr="control_url" value="<?php echo $sControlURL;?>">
	           <span class="select_title"><?php echo _('Select your payment method'); ?></span>
	            <div class="table_wrap">
		            <table class="select_payment">
		                
		                <tr>
		                    <td class="payment_title"><?php echo _('Your reference'); ?></td>
		                    <td><input type="text" class="wpcf7-form-control wpcf7-text" name="ref" value="<?php echo $ref?>" readonly></td>
		                </tr>
		                <tr>
		                    <td class="payment_title"><?php echo _('Amount'); ?></td>
		                    <td><input type="hidden" id="amount" name="amount" value="<?php echo $sAmount ?>">
		                        &euro; <input type="text" class="wpcf7-form-control wpcf7-text" id="input_amount" name="input_amount" value="<?php echo $pay_amount ?>" readonly>
		                    </td>
		                </tr>
		                 <tr>
		                    <td class="payment_title"><?php echo _('Email'); ?></td>
		                    <td><input type="text" class="wpcf7-form-control wpcf7-text" id="email" name="email" value="<?php echo $email;?>" readonly >
		               <!--       <input type="hidden" id="phone_number" name="phone_number" value="<?php echo $custom_data;?>" readonly > --> 
		                    </td>
		                </tr>
		                <tr>
		                    <td class="payment_title"><?php echo _('Payment method'); ?></td>
		                    <td>
		                        <select id="cgp_option" name="option" class="payment_methods" onchange="show_bank(this.options[this.selectedIndex].value)">
		                            <option name="unknown" value="">Kies uw betaalmethode</option>
		                          		<?php foreach($payment_gateway as $gateways):?>
		                            	<?php foreach($gateways as $gateway):?>
		                            		
		                            	<option name="<?php echo $gateway['value'];?>" value="<?php echo $gateway['value'];?>"><?php echo $gateway['payment_method'];?></option>
		                            	<?php endforeach;?>
		                            	<?php endforeach;?>
		                          		
		                        </select>
		                        <select id="cgp_suboption" name="suboption" style="visibility:hidden">
		                            <?php echo $sBankOptions; ?>
		                        </select>
		                    </td>
		                </tr>
		               
		            </table>
		             <input type="submit" class="proceed" name="send" value="<?php echo _('Proceed to payment'); ?>">
	            </div>
	           
	        </form>
	      </div>
      </div>
    </body>
</html>