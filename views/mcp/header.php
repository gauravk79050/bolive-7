<?php date_default_timezone_set('Europe/Brussels'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  <meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
  <title><?php echo _('Online bestellen met OBS - bestelsysteem voor Bakkers | Broodjeszaken | Traiteurs | ...') ?></title>

  <meta content="OBS, online bestellen, bestellingssysteem, bestelsysteem, bakker, bakkerij, brood en banket, broodjeszaak, broodjeszaken, online, beheren, beheersysteem, goedkoop" name="keywords">

  <meta content="Online bestellen met OBS (online bestelsysteem) voor bakkers / broodjeszaken /Traiteurs / Groente- en fruitwinkels / etc... Wees de eerste in uw buurt, verwen uw klanten!" name="description">
  <?php if (strripos($_SERVER['REQUEST_URI'], 'autocontrole/addedit_pasteur_group') !== false) { ?>
    <link href="<?php echo base_url(); ?>assets/css/bootstrap.css" rel="stylesheet" type="text/css" />
  <?php } ?>
  <link href="<?php echo base_url(); ?>assets/mcp/css/style.css" rel="stylesheet" type="text/css" />
  <link href="<?php echo base_url(); ?>assets/mcp/css/SpryMenuBarHorizontal.css" rel="stylesheet" type="text/css" />
  <link href="<?php echo base_url(); ?>assets/mcp/new_css/date_pic/jscal2.css" rel="stylesheet" type="text/css" />
  <link href="<?php echo base_url(); ?>assets/mcp/new_css/date_pic/border-radius.css" rel="stylesheet" type="text/css" />
  <link href="<?php echo base_url(); ?>assets/mcp/new_css/date_pic/steel/steel.css" rel="stylesheet" type="text/css" />
  <link href="<?php echo base_url(); ?>assets/mcp/new_css/colorbox.css" rel="stylesheet" type="text/css" />
  <link href="<?php echo base_url(); ?>assets/mcp/css/jquery-ui.css" rel="stylesheet" type="text/css" />

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

    #menu ul.MenuBarHorizontal li>ul li:hover>ul {
      left: 130px !important;
      display: block !important;
      top: 0px !important;
    }

    #menu ul.MenuBarHorizontal li>ul {
      display: none !important;
    }

    #menu ul.MenuBarHorizontal li:hover>ul {
      display: block !important;
    }

    #menu ul.MenuBarHorizontal li:hover ul {
      top: 23px !important;
    }
  </style>

  <?php if ($this->router->fetch_class() == 'autocontrole') { ?>
    <script type="text/javascript">
      var delete_obj_cat_txt = "<?php echo _("Do you want to delete this object category ? If yes all object assign to this category will also be deleted"); ?>";
    </script>
  <?php } ?>
  <script type="text/javascript">
    var section = 'mcp';
    var base_url = '<?php echo base_url(); ?>';
  </script>
  <script type="text/javascript" src="<?php echo base_url(); ?>assets/mcp/new_js/jquery-1.7.1.min.js"></script>
  <script type="text/javascript" src="<?php echo base_url(); ?>assets/mcp/js/jquery-ui.js"></script>
  <script type="text/javascript" src="<?php echo base_url(); ?>assets/mcp/js/SpryMenuBar.js"></script>
  <script type="text/javascript" src="<?php echo base_url(); ?>assets/mcp/js/general_functions.js"></script>
  <script type="text/javascript" src="<?php echo base_url(); ?>assets/mcp/js/validator.js"></script>
  <script type="text/javascript" src="<?php echo base_url(); ?>assets/mcp/new_js/jscal2.js"></script>
  <script type="text/javascript" src="<?php echo base_url(); ?>assets/mcp/new_js/lang/en.js"></script>
  <script type="text/javascript" src="<?php echo base_url(); ?>assets/mcp/new_js/jquery.colorbox.js"></script>
  <script src='<?php echo base_url() ?>assets/cp/js/lib/moment.min.js'></script>
  <?php if ($this->router->fetch_class() == 'autocontrole') { ?>
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/mcp/new_js/autocontrole.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/mcp/js/jquery-1.12.4.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/mcp/js/select2.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/mcp/new_js/bootstrap.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/mcp/new_js/bootstrap-multiselect.js"></script>
  <?php } ?>

  <script type="text/javascript">
    /*var cal = Calendar.setup({     jquery.ui.sortable.js  jquery-1.12.4.js,jquery.tablesorter.min.js ,jquery-ui.js
  onSelect: function(cal) { cal.hide() }
  });
  cal.manageFields("date_picker", "trial_date", "%Y-%m-%d");*/
    function get_login(id, username, password, redirect_bestelpunt, redirect_mail_manager) {

      jQuery('#login_' + id).html('&nbsp;&nbsp;&nbsp;<img src="<?php echo base_url(); ?>assets/mcp/images/ajax-loader.gif">&nbsp;&nbsp;&nbsp;');

      jQuery.post('<?php echo base_url(); ?>cp/login/validate', {
        'act': 'do_login',
        'submit': 'LOGIN',
        'via': 'mcp',
        'username': username,
        'password': password

      }, function(data) {

        if (redirect_mail_manager == 'true') {
          window.open('<?php echo base_url(); ?>cp/mail_manager');
        } else if (redirect_bestelpunt == 'true') {
          window.open('<?php echo base_url(); ?>cp/bestelonline/bp_settings');
        } else {
          window.open('<?php echo base_url(); ?>cp');
        }

        if (redirect_mail_manager == 'true') {
          jQuery('#login_' + id).html('<?php echo _("LOGIN MAIL MANAGER"); ?>');
        } else {
          jQuery('#login_' + id).html('LOGIN');
        }


      });
    }

    function get_login_fdd(comp_id) {
      jQuery('#fdd2_login_' + comp_id).css('text-decoration', 'none');
      jQuery('#fdd2_login_' + comp_id).html('&nbsp;&nbsp;&nbsp;<img src="<?php echo base_url(); ?>assets/mcp/images/ajax-loader.gif">&nbsp;&nbsp;&nbsp;');

      jQuery.post('<?php echo base_url(); ?>mcp/mcplogin/login_fdd2_via_mcp', {
        'comp_id': comp_id,
      }, function(data) {
        if (data) {
          jQuery('#fdd2_login_' + comp_id).html('LOGIN 20');
          window.open('<?php echo $this->config->item('new_obs_url'); ?>' + "login/loggen_via_mcp_oldobs/" + data + "/" + comp_id, "_blank");
        }
      });
    }

    function get_login_tv(comp_id, tv_id) {
      jQuery('#tv_login_' + comp_id).css('text-decoration', 'none');
      jQuery('#tv_login_' + comp_id).html('&nbsp;&nbsp;&nbsp;<img src="<?php echo base_url(); ?>assets/mcp/images/ajax-loader.gif">&nbsp;&nbsp;&nbsp;');

      jQuery.post('<?php echo base_url(); ?>mcp/mcplogin/login_tv_via_mcp', {
        'comp_id': comp_id
      }, function(data) {
        if (data) {
          window.open('<?php echo $this->config->item('tv_login'); ?>' + "cp/login/login_via_obs_mcp/" + data + "/" + comp_id + "/" + tv_id, "_blank");
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

  <script type="text/javascript">
    function approve_disapprove(id, action) {

      window.location.href = 'index.php?view=home&amp;id=' + id + '&amp;action=' + action;

    }


    function setInvoice(Oid) {

      var ans = confirm("<?php echo _('Sure, You want to Set Invoice Status as Paid ?'); ?>");

      if (ans) {

        window.location = "index.php?view=home&amp;act=invoice_status&amp;id=" + Oid;

      }

    }

    <?php if ($this->router->fetch_class() != 'mail_manager') {
      if ($this->router->fetch_class() != 'autocontrole') { ?>

        $(document).ready(function() {

          var $jq = jQuery.noConflict();

          //Examples of how to assign the ColorBox event to elements

          $jq(".colorbox").colorbox({

            width: '630px',

            height: '400px',

            scrolling: false

          });
          if (!($jq.browser.msie && $jq.browser.version == 9)) { // take away IE6
            $jq("#TB_window").css({
              'margin-top': '-200px'
            });
          }

        });
    <?php }
    } ?>
  </script>

  <div id="content">

    <style type="text/css">
      .style2 {
        color: #667297
      }

      .style3 {
        color: #667297;
        font-size: 14px
      }

      .style4 {

        color: #CCC;

        font-size: 12px;

        font-weight: normal;

        margin-right: 50px;

        float: right;

      }
    </style>

    <div id="header" class="edit_obj">

      <div style="width:90%; height:30px; padding-left:5px">

        <h1><span class="style2"><?php echo _('OBS') ?></span>&nbsp;&nbsp;<span class="style3"><?php echo _('MASTER CP') ?></span><span class="style4"><?php echo _('Server Date/Time &nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp; ' . date('l , F j, Y, g:i a', time())); ?></span></h1>

      </div>

      <div style="width:100%; height:20px">

        <div style="width:30%; height:20px; float:left; padding-left:7px">

          <h3><?php echo _('Master Administrator Panel') ?></h3>

        </div>
        <?php
        if (isset($hide_header_menu) && $hide_header_menu == true) {
        } else {
        ?>
          <div style="width:30%; height:20px; float:right; text-align:right; padding-right:5px">


            <h3><?php echo _('Welcome Admin') ?> </h3>

          </div>

      </div>

      <div id="menu" style="width:100%; height:25px; background-color:#003366">

        <ul class="MenuBarHorizontal" id="MenuBar1">

          <li style="display:none;"><?php echo anchor(base_url() . "mcp/dashboard", _('HOME'), array('class' => "MenuBarItemSubmenu")); ?></li>

          <li><?php echo anchor(base_url() . "mcp/companies", _('COMPANIES'), array('class' => "MenuBarItemSubmenu")); ?></li>

          <li>
            <?php echo anchor(base_url() . "mcp/easybutler", _('EB leads ') . '(' . get_eb_leads_count() . ')', array('class' => "MenuBarItemSubmenu")); ?></li>
          <?php if ('mcp' == $this->session->userdata('admin_role')) { ?>

            <li><?php echo anchor("/mcp/country/#", _('SETTINGS'), array('class' => "MenuBarItemSubmenu")) ?>

              <ul>

                <li><?php echo anchor(base_url() . "mcp/country", _('COUNTRIES')) ?></li>

                <li><?php echo anchor(base_url() . "mcp/languages", _('LANGUAGES')) ?></li>

                <li><?php echo anchor(base_url() . "mcp/email_message", _('MAIL MESSAGES')) ?></li>

                <?php /*?><li><?php echo anchor(base_url()."mcp/web_designers",_('WEB DESIGNERS'))?></li><?php */ ?>

                <li><?php echo anchor(base_url() . "mcp/calendar", _('CALENDAR')) ?></li>

                <li><?php echo anchor(base_url() . "mcp/company_type", _('TYPE')) ?></li>

                <li><?php echo anchor(base_url() . "mcp/ads", _('ADS')) ?></li>

                <li><?php echo anchor(base_url() . "mcp/competitor", _('COMPETITOR')) ?></li>

                <li><?php echo anchor(base_url() . "mcp/email_verification", _('EMAIL VERIFICATIONS')) ?></li>

                <li><?php echo anchor(base_url() . "mcp/bestelonline", _('BESTELONLINE BANNER')) ?></li>

                <li><?php echo anchor(base_url() . "mcp/allergenenchecker", _('ALLERGENEN BANNER')) ?></li>

                <li><?php echo anchor(base_url() . "mcp/payment", _('PAYMENT')) ?></li>

                <li><?php echo anchor(base_url() . "mcp/allergenswords", _('ALLERGENS WORDS')) ?></li>
              </ul>

            </li>
          <?php } ?>
          <?php if ('mcp' == $this->session->userdata('admin_role')) { ?>
            <li>
              <?php echo anchor(base_url() . "mcp/package", _('PACKAGE MANAGER'), array('class' => "MenuBarItemSubmenu")) ?>
              <ul>
                <li><?php echo anchor(base_url() . "mcp/profile", _('ADMIN MANAGER')); ?></li>
                <li><?php echo anchor(base_url() . "mcp/addon", _('ADDON MANAGER')); ?></li>
                <li><?php echo anchor(base_url('') . "mcp/api", _('API MANAGER')); ?></li>
                <li><?php echo anchor(base_url('') . "mcp/mail_manager", _('MAIL MANAGER')); ?></li>
              </ul>
            </li>
          <?php } else { ?>
            <li>
              <?php echo anchor(base_url() . "mcp/package", _('PACKAGE MANAGER'), array('class' => "MenuBarItemSubmenu")) ?>
              <ul>
                <li><?php echo anchor(base_url('') . "mcp/api", _('API MANAGER')); ?></li>
              </ul>
            </li>
          <?php } ?>
          <?php if ('mcp' == $this->session->userdata('admin_role')) { ?>
            <li><?php echo anchor(base_url('') . "mcp/partners/manage_partners", _('PARTNERS'), array('class' => "MenuBarItemSubmenu")) ?>
              <ul>
                <li><?php echo anchor(base_url('') . "mcp/affiliates/manage_affiliates", _('AFFILIATES')); ?></li>
              </ul>
            </li>
          <?php } else { ?>
            <li>
              <?php echo anchor(base_url('') . "mcp/partners/manage_partners", _('PARTNERS'), array('class' => "MenuBarItemSubmenu")) ?>
            </li>
          <?php  } ?>
          <?php if ('mcp' == $this->session->userdata('admin_role')) { ?>
            <li><?php echo anchor(base_url('') . "mcp/upgrade/update_client_files", _('FILE UPGRADE'), array('class' => "MenuBarItemSubmenu")) ?></li>

            <li><?php echo anchor(base_url('') . "mcp/excel_import", _('EXCEL IMPORT'), array('class' => "MenuBarItemSubmenu")) ?></li>

            <li><?php echo anchor(base_url('') . "mcp/dep/companies", _('COMPANIES FROM PORTAL') . '(' . get_dep_company_count() . ')', array('class' => "MenuBarItemSubmenu")) ?></li>

            <li><?php echo anchor(base_url('') . "mcp/mail_manager/companies", _('MAIL MANAGER'), array('class' => "MenuBarItemSubmenu")) ?>
              <ul>
                <li><?php echo anchor(base_url() . "mcp/mail_manager/companies", _('Clients')); ?></li>
                <li><?php echo anchor(base_url() . "mcp/mail_manager/templates", _('Templates')); ?></li>
                <li><?php echo anchor(base_url('') . "mcp/mail_manager/newsletters", _('Newsletters')); ?></li>
                <li><?php echo anchor(base_url('') . "mcp/mail_manager/image_manager", _('Image Manager')); ?></li>
                <li><?php echo anchor(base_url('') . "mcp/mail_manager/doc_manager", _('Doc Manager')); ?></li>
              </ul>
            </li>

            <li><?php echo anchor(base_url('') . "mcp/dashboard/faq_new_admin", _('FAQ Admin'), array('class' => "MenuBarItemSubmenu")) ?></li>

            <li><?php echo anchor(base_url('') . "mcp/stats", _('STATS'), array('class' => "MenuBarItemSubmenu")) ?></li>

            <li><?php echo anchor(base_url('') . "mcp/notifications", _('NOTIFICATIONS'), array('class' => "MenuBarItemSubmenu")) ?></li>

            <li><?php echo anchor(base_url('') . "mcp/autocontrole/categories", _('AUTOCONTROLE'), array('class' => "MenuBarItemSubmenu")) ?>
              <ul>
                <li class="MenuBarItem_BE"><a href="javascript:;"><?php echo _('BELGIUM'); ?><span class="menu_arrow"></span></a>
                  <ul class="MenuBarSubItem_BE">
                    <li><?php echo anchor(base_url('') . "mcp/autocontrole/temperature_group/be", _('TEMPERATURE GROUP')); ?></li>
                    <li><?php echo anchor(base_url('') . "mcp/autocontrole/pasteurization_group/be", _('PASTEURISATION GROUP')); ?></li>
                    <li><?php echo anchor(base_url('') . "mcp/autocontrole/ccp_pva_ghp/be", _('CCP PVA GHP')); ?></li>
                  </ul>
                </li>
                <li class="MenuBarItem_NL"> <a href="javascript:;"><?php echo _('NETHERLANDS'); ?><span class="menu_arrow"></span></a>
                  <ul class="MenuBarSubItem_NL">
                    <li><?php echo anchor(base_url('') . "mcp/autocontrole/temperature_group/nl", _('TEMPERATURE GROUP')); ?></li>
                    <li><?php echo anchor(base_url('') . "mcp/autocontrole/pasteurization_group/nl", _('PASTEURISATION GROUP')); ?></li>
                    <li><?php echo anchor(base_url('') . "mcp/autocontrole/ccp_pva_ghp/nl", _('CCP PVA GHP')); ?></li>
                  </ul>
                </li>
                <li><?php echo anchor(base_url() . "mcp/autocontrole/categories", _('CATEGORIES')); ?></li>
                <li><?php echo anchor(base_url() . "mcp/autocontrole/domains", _('DOMAINS')); ?></li>
                <li><?php echo anchor(base_url() . "mcp/autocontrole/predifined", _('PREDIFINED')); ?></li>
                <li><?php echo anchor(base_url() . "mcp/autocontrole/object_categories", _('OBJECT TYPES')); ?></li>
                <li><?php echo anchor(base_url() . "mcp/autocontrole/objects", _('OBJECTS')); ?></li>
                <li><?php echo anchor(base_url('') . "mcp/autocontrole/location", _('LOCATION')); ?></li>
                <li><?php echo anchor(base_url('') . "mcp/autocontrole/procedure", _('PROCEDURE')); ?></li>
                <li><?php echo anchor(base_url('') . "mcp/autocontrole/checklist", _('CHECKLIST')); ?></li>

                <li><?php echo anchor(base_url('') . "mcp/autocontrole/temperature_workroom", _('TEMPERATURE WORKROOM')); ?></li>
                <li><?php echo anchor(base_url('') . "mcp/autocontrole/autocontrole_calibration", _('CALIBRATION')); ?></li>

                <li><?php echo anchor(base_url('') . "mcp/autocontrole/module", _('MODULE')); ?></li>
                <li><?php echo anchor(base_url('') . "mcp/autocontrole/autocontrole_ph_groups", _('PH GROUPS')); ?></li>
              </ul>
            </li>
            <li><?php echo anchor(base_url('') . "mcp/overview", _('OVERVIEW'), array('class' => "MenuBarItemSubmenu")) ?></li>
          <?php } ?>
          <?php if ($this->session->userdata('admin_role') ==  'manager' && ($this->session->userdata('admin_id') == 18 || $this->session->userdata('admin_id') == 22 || $this->session->userdata('admin_id') == 14 || $this->session->userdata('admin_id') == 17 || $this->session->userdata('admin_id') == 38)) { ?>

            <li><?php echo anchor(base_url('') . "mcp/autocontrole/categories", _('AUTOCONTROLE'), array('class' => "MenuBarItemSubmenu")) ?>
              <ul>
                <li class="MenuBarItem_BE"><a href="javascript:;"><?php echo _('BELGIUM'); ?><span class="menu_arrow"></span></a>
                  <ul class="MenuBarSubItem_BE">
                    <li><?php echo anchor(base_url('') . "mcp/autocontrole/temperature_group/be", _('TEMPERATURE GROUP')); ?></li>
                    <li><?php echo anchor(base_url('') . "mcp/autocontrole/pasteurization_group/be", _('PASTEURISATION GROUP')); ?></li>
                    <li><?php echo anchor(base_url('') . "mcp/autocontrole/ccp_pva_ghp/be", _('CCP PVA GHP')); ?></li>
                  </ul>
                </li>
                <li class="MenuBarItem_NL"> <a href="javascript:;"><?php echo _('NETHERLANDS'); ?><span class="menu_arrow"></span></a>
                  <ul class="MenuBarSubItem_NL">
                    <li><?php echo anchor(base_url('') . "mcp/autocontrole/temperature_group/nl", _('TEMPERATURE GROUP')); ?></li>
                    <li><?php echo anchor(base_url('') . "mcp/autocontrole/pasteurization_group/nl", _('PASTEURISATION GROUP')); ?></li>
                    <li><?php echo anchor(base_url('') . "mcp/autocontrole/ccp_pva_ghp/nl", _('CCP PVA GHP')); ?></li>
                  </ul>
                </li>
                <li><?php echo anchor(base_url() . "mcp/autocontrole/categories", _('CATEGORIES')); ?></li>
                <li><?php echo anchor(base_url() . "mcp/autocontrole/domains", _('DOMAINS')); ?></li>
                <li><?php echo anchor(base_url() . "mcp/autocontrole/predifined", _('PREDIFINED')); ?></li>
                <li><?php echo anchor(base_url() . "mcp/autocontrole/object_categories", _('OBJECT TYPES')); ?></li>
                <li><?php echo anchor(base_url() . "mcp/autocontrole/objects", _('OBJECTS')); ?></li>
                <li><?php echo anchor(base_url('') . "mcp/autocontrole/location", _('LOCATION')); ?></li>
                <li><?php echo anchor(base_url('') . "mcp/autocontrole/procedure", _('PROCEDURE')); ?></li>
                <li><?php echo anchor(base_url('') . "mcp/autocontrole/checklist", _('CHECKLIST')); ?></li>
                <li><?php echo anchor(base_url('') . "mcp/autocontrole/temperature_workroom", _('TEMPERATURE WORKROOM')); ?></li>
                <li><?php echo anchor(base_url('') . "mcp/autocontrole/autocontrole_calibration", _('CALIBRATION')); ?></li>
                <li><?php echo anchor(base_url('') . "mcp/autocontrole/module", _('MODULE')); ?></li>
                <li><?php echo anchor(base_url('') . "mcp/autocontrole/autocontrole_ph_groups", _('PH GROUPS')); ?></li>
              </ul>
            </li>
          <?php } ?>
          <li><?php echo anchor(base_url('') . "mcp/assignee/manage_assignee", _('ASSIGNEE'), array('class' => "MenuBarItemSubmenu")) ?></li>
          <li><?php echo anchor(base_url('') . "mcp/mcplogin/logout", _('LOGOUT'), array('class' => "MenuBarItemSubmenu")) ?></li>
        </ul>
        <div class="clear_all"></div>
      <?php
        }
      ?>
      </div>
    </div>