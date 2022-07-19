<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
<title><?php echo _('Online bestellen met OBS - bestelsysteem voor Bakkers | Broodjeszaken | Traiteurs | ...')?></title>

<meta content="OBS, online bestellen, bestellingssysteem, bestelsysteem, bakker, bakkerij, brood en banket, broodjeszaak, broodjeszaken, online, beheren, beheersysteem, goedkoop" name="keywords">

<meta content="Online bestellen met OBS (online bestelsysteem) voor bakkers / broodjeszaken /Traiteurs / Groente- en fruitwinkels / etc... Wees de eerste in uw buurt, verwen uw klanten!" name="description">

<link href="<?php echo base_url(); ?>assets/mcp/css/style.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>assets/mcp/css/SpryMenuBarHorizontal.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>assets/mcp/new_css/date_pic/jscal2.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>assets/mcp/new_css/date_pic/border-radius.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>assets/mcp/new_css/date_pic/steel/steel.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>assets/mcp/new_css/colorbox.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">var base_url = '<?php echo base_url(); ?>';</script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/mcp/js/SpryMenuBar.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/mcp/js/general_functions.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/mcp/js/validator.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/mcp/new_js/jscal2.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/mcp/new_js/lang/en.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/mcp/new_js/jquery-1.4.2.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/mcp/new_js/jquery.colorbox.js"></script>

<script type="text/javascript">
function get_login(id,username,password)
{
   //alert( id+'--'+username+'--'+password );
   jQuery('#login_'+id).css('text-decoration','none');
   jQuery('#login_'+id).html('&nbsp;&nbsp;&nbsp;<img src="<?php echo base_url(); ?>assets/mcp/images/ajax-loader.gif">&nbsp;&nbsp;&nbsp;');
   
   jQuery.post('<?php echo base_url(); ?>cp/login/validate',
         {
		    'act':'do_login',
			'submit':'LOGIN',
			'username':username,
			'password':password
			
		 },function(data){
            
			window.open('<?php echo base_url(); ?>cp');
			jQuery('#login_'+id).css('text-decoration','underline');
			jQuery('#login_'+id).html('LOGIN');
   });
}
</script>

</head>

<body>

<div id="cboxOverlay" style="display: none;"></div>

<div id="colorbox" style="padding-bottom: 20px; padding-right: 0px; display: none;">

  <div id="cboxWrapper">

    <div>

      <div id="cboxTopLeft" style="float: left;"></div>

      <div id="cboxTopCenter" style="float: left;"></div>

      <div id="cboxTopRight" style="float: left;"></div>

    </div>

    <div>

      <div id="cboxMiddleLeft" style="float: left;"></div>

      <div id="cboxContent" style="float: left;">

        <div id="cboxLoadedContent" style="width: 0px; height: 0px;" class=""></div>

        <div id="cboxLoadingOverlay" class=""></div>

        <div id="cboxLoadingGraphic" class=""></div>

        <div id="cboxTitle" class=""></div>

        <div id="cboxCurrent" class=""></div>

        <div id="cboxSlideshow" class=""></div>

        <div id="cboxNext" class=""></div>

        <div id="cboxPrevious" class=""></div>

        <div id="cboxClose" class=""></div>

      </div>

      <div id="cboxMiddleRight" style="float: left;"></div>

    </div>

    <div>

      <div id="cboxBottomLeft" style="float: left;"></div>

      <div id="cboxBottomCenter" style="float: left;"></div>

      <div id="cboxBottomRight" style="float: left;"></div>

    </div>

  </div>

  <div style="position: absolute; top: 0pt; left: 0pt; width: 9999px; height: 0pt;"></div>

</div>

<div id="content">

  <style type="text/css">


.style2 {color: #667297}

.style3 {color: #667297; font-size:14px}

.style4 {

	color:#CCC;

	font-size:12px;

	font-weight: normal;

	margin-right: 50px;

	float: right;

}

</style>

  <div id="header">

    <div style="width:90%; height:30px; padding-left:5px">

      <h1><span class="style2"><?php echo _('OBS')?></span>&nbsp;&nbsp;<span class="style3"><?php echo _('AFFILIATE USER CP')?></span><span class="style4"><?php echo _('Server Date/Time &nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp; '.date('l , F j, Y, g:i a', time())); ?></span></h1>

    </div>

    <div style="width:100%; height:20px">

      <div style="width:30%; height:20px; float:left; padding-left:7px">

        <h3><?php echo _('Affiliate User Panel')?></h3>

      </div>
 <?php
		if(isset($hide_header_menu) && $hide_header_menu == true)
		{ 
		}
		else
		{
	  ?>
      <div style="width:30%; height:20px; float:right; text-align:right; padding-right:5px">
	  

        <h3><?php echo _('Welcome')?> <?php echo $this->session->userdata('ap_username'); ?></h3>

      </div>

    </div>

    <div id="menu" style="width:100%; height:25px; background-color:#003366">
      
        <ul class="MenuBarHorizontal" id="MenuBar1">

          <li><?php echo anchor( base_url()."ap/affiliate/companies",_('COMPANIES'),array('class'=>"MenuBarItemSubmenu")); ?></li>

          <li><?php echo anchor( base_url('')."ap/affiliate/settings",_('SETTINGS'),array('class'=>"MenuBarItemSubmenu"))?>

          <li><?php echo anchor( base_url('')."ap/aplogin/logout",_('LOGOUT'),array('class'=>"MenuBarItemSubmenu"))?></li>

        </ul>

        <div class="clear_all"></div>   
    <?php 

		} 
		
	?>
    </div>

  </div>