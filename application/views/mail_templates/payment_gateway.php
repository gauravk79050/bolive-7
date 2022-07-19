<?php
/**
 * PHP Demo page for Card Gate Gateway
 * (C)2013 Card Gate B.V.
 **/

# Enter the correct Site info from https://merchants.cardgateplus.com/
# the Control URL can only be set in the backoffice and will be called automatically
# return_url en return_url_failed can be given dynamically per transaction
$iSiteID = 5272;
# Hash-check value
$sAPIKey = 'p7z3V_wh';

# If you leave these out, the system will use the defaults entered in the backoffice
// $sReturnURL = base_url().'cardgate/success';
//  $sReturnURL_Failed = base_url().'cardgate/failure';
$sReturnURL = $return_url;
$sReturnURL_Failed = $return_url;
$sControlURL = base_url().'cardgate/callback_url';
# Optional test-modus (=always uses creditcard demo-module at this time)
$sPrefix = 'TEST'; // 'TEST' for testing


$sAmount = $pay_amount*100;

$sHash = md5( $sPrefix . $iSiteID . $sAmount . $ref . $sAPIKey );


# Load banks for iDEAL
######################
$sBankOptions = file_get_contents( 'https://gateway.cardgateplus.com/cache/idealDirectoryCUROPayments.dat' );
if ( empty( $sBankOptions ) || $sBankOptions[ 0 ] != 'a' ) {
    # Fallback in case of an error
    $sBankOptions = 'a:11:{i:0;s:0:"";s:8:"ABNANL2A";s:8:"ABN Amro";s:8:"FRBKNL2L";s:14:"Friesland Bank";s:8:"INGBNL2A";s:3:"ING";s:8:"RABONL2U";s:8:"Rabobank";s:8:"SNSBNL2A";s:8:"SNS Bank";s:8:"ASNBNL21";s:8:"ASN Bank";s:8:"KNABNL2H";s:4:"Knab";s:8:"RBRBNL21";s:9:"RegioBank";s:8:"TRIONL2U";s:12:"Triodos Bank";s:8:"FVLBNL22";s:21:"Van Lanschot Bankiers";};';
}
$aBankOptions = unserialize( $sBankOptions );
unset( $aBankOptions[ 0 ] ); # Remove blank option
# Convert to HTML
$sBankOptions = '<optgroup label="Kies uw bank">';
foreach ( $aBankOptions as $k => $v ) {
    if ( $v[ 0 ] == '-' ) {
        $sBankOptions .= '<optgroup label="' . str_replace( '-', '', $v ) . '">';
    } else {
        $sBankOptions .= '<option value="' . $k . '">' . $v . '</option>';
    }
}
$sBankOptions.= '</optgroup>';


# Form target
$_FormAction = 'https://gateway.cardgateplus.com';
$_FormTarget = '';




?>
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

            function selectors() {
                var pay_method;
                if ("<?php echo $_REQUEST[ 'option' ] ?>" != "") {
                    var cgp_option = document.getElementById('cgp_option');
                    cgp_option.options["<?php echo $_REQUEST[ 'option' ] ?>"].selected = "selected";
                    document.getElementById('cgp_suboption').options["<?php echo $_REQUEST[ 'suboption' ] ?>"].selected = "selected";
                    document.getElementById('country').options["<?php echo $_REQUEST[ 'country' ] ?>"].selected = "selected";
                    pay_method = cgp_option.options[cgp_option.selectedIndex].value;
                    show_bank(pay_method);
                }
                document.getElementById("input_amount").focus();
            }

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

            // Lightweight Ajax
            function GetXmlHttpObject() {
                var xmlHttp = null;
                try {
                    // Firefox, Opera 8.0+, Safari
                    xmlHttp = new XMLHttpRequest();
                } catch (e) {
                    // Internet Explorer
                    try {
                        xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
                    } catch (e) {
                        xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
                    }
                }
                if (xmlHttp == null) {
                    alert("Your browser does not support Ajax!\nPlease use another browser.");
                }
                return xmlHttp;
            }
            var xreq = null;
            function AjaxRequest(url, handlers) {
                xreq = GetXmlHttpObject();
                if (xreq != null) {
                    xreq.handlers = handlers;
                    xreq.onreadystatechange = AjaxOnReadyStateChange;
                    xreq.open("GET", url, true);
                    xreq.send(null);
                }
            }
            function AjaxOnReadyStateChange() {
                if (xreq.readyState == 4) {
                    if (xreq.status == 200) {
                        if (xreq.handlers.onSuccess)
                            xreq.handlers.onSuccess(xreq);
                    } else if (xreq.handlers.onFailure)
                        xreq.handlers.onFailure(xreq);
                }
            }
            function cgpCalcHash(oForm) {
                if (oForm.input_amount.value == '' || Math.valueOf(oForm.input_amount.value) <= 0) {
                    alert("Invalid amount!");
                }
                else if (oForm.cgp_option.selectedIndex == 0) {
                    alert("Select a payment option!");
                }
                else if (oForm.cgp_option.selectedIndex == 1 && oForm.cgp_suboption.selectedIndex == -1) {
                    alert("Select your bank!");
                } else {
                    
                }
            }
        </script>
        <style type="text/css">
            <!--
            .field {width:250px;}
            #input_amount {width:80px;}
            #cgp_suboption {width:149px;}
            .tr {height:30px;}
            -->
        </style>
    </head>
    <body onload="">
        <form name="cgp_payment" method="post" action="<?php echo $_FormAction ?>" target="<?php echo $_FormTarget ?>" onsubmit="cgpCalcHash(this);
                return false">    
            <input type="hidden" name="siteid" value="<?php echo $iSiteID ?>">
            <input type="hidden" name="hash" value="<?php echo $sHash ?>">
            <input type="hidden" name="test" value="<?php echo $sPrefix == 'TEST' ? 1 : 0 ?>">
            <input type="hidden" name="language" value="nl">
            <input type="hidden" name="return_url" value="<?php echo $sReturnURL ?>">
            <input type="hidden" name="return_url_failed" value="<?php echo $sReturnURL_Failed ?>">
            <input type="hidden" namr="control_url" value="<?php echo $sControlURL;?>">
            <table>
                <tr class="tr">
                    <td colspan="2"><b>Payment Page:</b></td>
                </tr>
                <tr>
                    <td>Your reference</td>
                    <td><input type="text" class="field" name="ref" value="<?php echo $ref?>" readonly></td>
                </tr>
                <tr>
                    <td>Amount</td>
                    <td><input type="hidden" id="amount" name="amount" value="<?php echo $sAmount ?>">
                        &euro; <input type="text" id="input_amount" name="input_amount" value="<?php echo $pay_amount ?>" readonly>
                    </td>
                </tr>
                 <tr>
                    <td>Email</td>
                    <td><input type="text" id="email" name="email" value="<?php echo $email;?>" readonly >
                    <input type="hidden" id="phone_number" name="phone_number" value="<?php echo $order_id;?>" readonly >
                    </td>
                </tr>
                <tr>
                    <td>Payment method</td>
                    <td>
                        <select id="cgp_option" name="option" onchange="show_bank(this.options[this.selectedIndex].value)">
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
            <input type="submit" name="send" value="Proceed to payment">
        </form>
    </body>
</html>