<?php date_default_timezone_set( 'Europe/Brussels' ); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
<title><?php echo _('Online bestellen met OBS - bestelsysteem voor Bakkers | Broodjeszaken | Traiteurs | ...')?></title>

<meta content="OBS, online bestellen, bestellingssysteem, bestelsysteem, bakker, bakkerij, brood en banket, broodjeszaak, broodjeszaken, online, beheren, beheersysteem, goedkoop" name="keywords">

<meta content="Online bestellen met OBS (online bestelsysteem) voor bakkers / broodjeszaken /Traiteurs / Groente- en fruitwinkels / etc... Wees de eerste in uw buurt, verwen uw klanten!" name="description">
<?php if( strripos( $_SERVER[ 'REQUEST_URI' ], 'autocontrole/addedit_pasteur_group' ) !== false ){ ?>
 <link href="<?php echo base_url(); ?>assets/css/bootstrap.css" rel="stylesheet" type="text/css" />
 <?php } ?>
<link href="<?php echo base_url(); ?>assets/mcp/css/style.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>assets/mcp/css/SpryMenuBarHorizontal.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>assets/mcp/new_css/date_pic/jscal2.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>assets/mcp/new_css/date_pic/border-radius.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>assets/mcp/new_css/date_pic/steel/steel.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>assets/mcp/new_css/colorbox.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url();?>assets/css/data_table.css" rel="stylesheet" type="text/css"/>

<style type="text/css">
  .menu_arrow {
    margin-left: 10px;
    width: 0;
    float: right;
    height: 0;
    border-top: 4px solid transparent;
    border-left: 8px solid #e4e4e4;
    border-bottom: 4px solid transparent;
  }
 
  #menu ul.MenuBarHorizontal li > ul li:hover > ul {
    left: 130px !important;
    display: block !important;
    top: 0px !important;
  }
  #menu ul.MenuBarHorizontal li > ul {
      display: none !important;
  }
  #menu ul.MenuBarHorizontal li:hover > ul {
      display: block !important;
  }
  #menu ul.MenuBarHorizontal li:hover ul {
    top: 23px !important;
  }
  
</style>

<?php if( $this->session->userdata( 'rp_user_id' ) == '10' && $this->router->fetch_class( ) == 'autocontrole' ) { ?>
 <script type="text/javascript">
 var  section = 'rp';
 var delete_obj_cat_txt = "<?php echo _( "Do you want to delete this object category ? If yes all object assign to this category will also be deleted" );?>";
</script>
<?php } ?>
<script type="text/javascript">
  var  section = 'mcp';
  var base_url = '<?php echo base_url(); ?>';
</script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/mcp/new_js/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/mcp/js/jquery-ui.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/mcp/js/SpryMenuBar.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/mcp/js/general_functions.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/mcp/js/validator.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/mcp/new_js/jscal2.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/mcp/new_js/lang/en.js"></script>
<!-- <script type="text/javascript" src="<?php echo base_url(); ?>assets/mcp/new_js/jquery-1.4.2.js"></script> -->
<script type="text/javascript" src="<?php echo base_url(); ?>assets/mcp/new_js/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/mcp/js/jquery-ui.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/mcp/new_js/jquery.colorbox.js"></script>
<script src='<?php echo base_url()?>assets/cp/js/lib/moment.min.js'></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/js/jqtable.js"></script>
<?php if( $this->session->userdata( 'rp_user_id' ) == '10' && $this->router->fetch_class( ) == 'autocontrole' ) { ?>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/mcp/new_js/autocontrole.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/mcp/js/jquery-1.12.4.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/mcp/js/select2.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/mcp/new_js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/mcp/new_js/bootstrap-multiselect.js"></script>
<?php } ?>
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
function get_login_fdd( comp_id ) {
  jQuery('#fdd2_login_'+comp_id).css('text-decoration','none');
    jQuery('#fdd2_login_'+comp_id).html('&nbsp;&nbsp;&nbsp;<img src="<?php echo base_url(); ?>assets/mcp/images/ajax-loader.gif">&nbsp;&nbsp;&nbsp;');

    jQuery.post('<?php echo base_url(); ?>mcp/mcplogin/login_fdd2_via_mcp',
      {
      'comp_id' : comp_id,
    },function(data){
      if(data){
        jQuery('#fdd2_login_'+comp_id).html('LOGIN 20');
        window.open('<?php echo $this->config->item( 'new_obs_url' ); ?>'+"login/loggen_via_mcp_oldobs/"+data+"/"+comp_id,"_blank");
      }
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

      <h1><span class="style2"><?php echo _('OBS')?></span>&nbsp;&nbsp;<span class="style3"><?php echo _('RESELLER CP')?></span><span class="style4"><?php echo _('Server Date/Time &nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp; '.date('l , F j, Y, g:i a', time())); ?></span></h1>

    </div>

    <div style="width:100%; height:20px">

      <div style="width:30%; height:20px; float:left; padding-left:7px">

        <h3><?php echo _('Reseller Panel')?></h3>

      </div>
 <?php
		if(isset($hide_header_menu) && $hide_header_menu == true)
		{ 
		}
		else
		{
	  ?>
      <div style="width:30%; height:20px; float:right; text-align:right; padding-right:5px">
	  

        <h3><?php echo _('Welcome')?> <?php echo $this->session->userdata('rp_username'); ?></h3>

      </div>

    </div>

    <div id="menu" style="width:100%; height:25px; background-color:#003366">
      
        <ul class="MenuBarHorizontal" id="MenuBar1">

          <li><?php echo anchor( base_url()."rp/reseller/companies",_('COMPANIES'),array('class'=>"MenuBarItemSubmenu")); ?></li>

          <li><?php echo anchor( base_url('')."rp/reseller/settings",_('SETTINGS'),array('class'=>"MenuBarItemSubmenu"));?>
          <li><?php echo anchor( base_url('')."rp/assignee/manage_assignee",_('ASSIGNEE'),array('class'=>"MenuBarItemSubmenu"));?>
          <?php  if( $this->session->userdata( 'rp_user_id' ) == '10' ) { ?>
          <li><?php echo anchor(base_url('')."rp/autocontrole/categories",_('AUTOCONTROLE'),array('class'=>"MenuBarItemSubmenu"))?>
            <ul>
              <li class="MenuBarItem_BE"><a href="javascript:;"><?php echo _('BELGIUM');?><span class="menu_arrow"></span></a>
                <ul class="MenuBarSubItem_BE">
                  <li><?php echo anchor(base_url('')."rp/autocontrole/temperature_group/be",_('TEMPERATURE GROUP'));?></li>
                  <li><?php echo anchor(base_url('')."rp/autocontrole/pasteurization_group/be",_('PASTEURISATION GROUP'));?></li>
                  <li><?php echo anchor(base_url('')."rp/autocontrole/ccp_pva_ghp/be",_('CCP PVA GHP'));?></li>
                </ul>
              </li>
              <li class="MenuBarItem_NL"> <a href="javascript:;"><?php echo _('NETHERLANDS');?><span class="menu_arrow"></span></a>
                 <ul class="MenuBarSubItem_NL">
                  <li><?php echo anchor(base_url('')."rp/autocontrole/temperature_group/nl",_('TEMPERATURE GROUP'));?></li>
                  <li><?php echo anchor(base_url('')."rp/autocontrole/pasteurization_group/nl",_('PASTEURISATION GROUP'));?></li>
                  <li><?php echo anchor(base_url('')."rp/autocontrole/ccp_pva_ghp/nl",_('CCP PVA GHP'));?></li>
                </ul>
              </li>
              <li><?php echo anchor(base_url()."rp/autocontrole/categories",_('CATEGORIES'));?></li>
              <li><?php echo anchor(base_url()."rp/autocontrole/domains",_('DOMAINS'));?></li>
              <li><?php echo anchor(base_url()."rp/autocontrole/predifined",_('PREDIFINED'));?></li>
               <li><?php echo anchor(base_url()."rp/autocontrole/object_categories",_('OBJECT TYPES'));?></li>
              <li><?php echo anchor(base_url()."rp/autocontrole/objects",_('OBJECTS'));?></li>
              <li><?php echo anchor(base_url('')."rp/autocontrole/location",_('LOCATION'));?></li>
              <li><?php echo anchor(base_url('')."rp/autocontrole/procedure",_('PROCEDURE'));?></li>
              <li><?php echo anchor(base_url('')."rp/autocontrole/checklist",_('CHECKLIST'));?></li>
              <li><?php echo anchor(base_url('')."rp/autocontrole/temperature_workroom",_('TEMPERATURE WORKROOM'));?></li>
              <li><?php echo anchor(base_url('')."rp/autocontrole/autocontrole_calibration",_('CALIBRATION'));?></li>
              <li><?php echo anchor(base_url('')."rp/autocontrole/module",_('MODULE'));?></li>
              <li><?php echo anchor(base_url('')."rp/autocontrole/autocontrole_ph_groups",_('PH GROUPS'));?></li>
            </ul>
          </li>
          <?php  } ?>
          <!--<li><?php if($p_manager==1)
          			{
          			//	echo anchor( base_url()."rp/reseller/suggested_corrections", 'SUGGESTED CORRECTIONS('.count($company_suggested_corrections).')',  array('class'=>"MenuBarItemSubmenu")); ?>
          
          <li><?php //echo anchor(base_url()."rp/reseller/manage_companies",_('MANAGE COMPANIES'),array('class'=>"MenuBarItemSubmenu"));
          			}?>
-->
          <li><?php echo anchor( base_url('')."rp/rplogin/logout",_('LOGOUT'),array('class'=>"MenuBarItemSubmenu"));?></li>

        </ul>

        <div class="clear_all"></div>   
    <?php 

		} 
		
	?>
    </div>

  </div>