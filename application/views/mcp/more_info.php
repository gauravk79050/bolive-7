<!-- start of main body -->
<?php echo validation_errors(); ?>

<link rel="stylesheet" href="<?php echo base_url(); ?>assets/cp/js/thickbox/css/thickbox.css?version=<?php echo version; ?>" type="text/css" />
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/cp/new_css/jquery.Jcrop.css?version=<?php echo version; ?>" type="text/css" />
<script type="text/javascript" src="<?php echo base_url() ?>assets/cp/js/thickbox/javascript/thickbox.js?version=<?php echo version; ?>"></script>
<script type="text/javascript" src="<?php echo base_url() ?>assets/cp/new_js/jquery.Jcrop.js?version=<?php echo version; ?>"></script>
<script type="text/javascript" src="<?php echo base_url() ?>assets/cp/new_js/jquery.form.js?version=<?php echo version; ?>"></script>
<!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script> -->
<style type="text/css">
  .preview_title {
    display: block;
    font-size: 20px;
    font-weight: bold;
    margin: 10px auto;
    text-align: center;
    text-decoration: underline;
  }

  .jcrop-holder #preview-pane {
    display: block;
    position: absolute;
    /*z-index: 2000;*/
    top: -2px;
    right: -260px;
    padding: 6px;
    border: 1px rgba(0, 0, 0, .4) solid;
    background-color: white;

    -webkit-border-radius: 6px;
    -moz-border-radius: 6px;
    border-radius: 6px;

    -webkit-box-shadow: 1px 1px 5px 2px rgba(0, 0, 0, 0.2);
    -moz-box-shadow: 1px 1px 5px 2px rgba(0, 0, 0, 0.2);
    box-shadow: 1px 1px 5px 2px rgba(0, 0, 0, 0.2);
  }

  .sys_comp select {
    float: left;
    margin-right: 5px;
  }

  /* The Javascript code will set the aspect ratio of the crop
   area based on the size of the thumbnail preview,
   specified here */
  #preview-pane .preview-container {
    width: 220px;
    height: 209px;
    overflow: hidden;
  }

  /*#TB_window{
  top: 80% !important;
  z-index: 999 !important;
  }*/
  #crop_button {
    background-color: #007a96;
    padding: 12px 26px;
    color: #fff;
    font-size: 14px;
    border-radius: 2px;
    cursor: pointer;
    display: inline-block;
    line-height: 1;
    border: none;
  }

  .crop_div {
    margin-top: 30px;
  }

  .brightness_slider {
    clear: left;
    width: 40%;
    display: inline-block;
    margin-top: 30px;
  }

  span.brightness_percent {
    margin-left: 10px;
  }

  .wd_text.fdd-color {
    text-align: left;
    padding-left: 48px !important;
    background-color: #efefef;
    padding: 2px;
  }

  .move_rit {
    font-size: 12px;
    margin-left: 50px;
    color: #d5bcbce6;
    padding: 5px 10px;
  }

  .move_rit_light {
    margin-left: 50px;
    color: #000;
    padding: 35px 10px;
  }

  .move_rit_all {
    padding: 5px 10px;
  }

  .fdd-more-info-table table tr {
    padding-bottom: 10px !important;
    display: flow-root;
  }

  .fdd-more-info-table table tr input {
    margin-left: 10px;
  }

  .style_haccp .move_rit_light span .radio {
    margin: 0px 10px;
  }

  .move_rit {
    margin: 0px 24px;
  }
</style>
</script>
<script type="text/javascript">
  jQuery(function($) {
    $(document).on('click', '.textbox_select_all', function() {
      if ($(this).is(':checked')) {
        $('.chk_cost_price_status_parameter').prop('checked', true);
      } else {
        $('.chk_cost_price_status_parameter').prop('checked', false);
      }
    });
  });
  jQuery(function($) {
    $(document).on('click', '.chk_cost_price_status_parameter', function() {
      if ($(this).is(':checked')) {
        $('.textbox_select_all').prop('checked', true);
      }
    });
  });

  jQuery( document ).on( 'click', '.add_fav_list', function($) {
  var $this     = jQuery(this),
    repeatable  = $this.closest('tr').clone();
    repeatable.find( '.remove_fav_list' ).show();
    repeatable.find( 'input[type="text"]' ).val('');

  jQuery(document).find('.additional_fav_input').last().after( repeatable );
});

jQuery( document ).on( 'click', '.remove_fav_list', function($) {
  if( jQuery( document ).find( '.additional_fav_input' ).length > 1 ) {
    jQuery(this).closest('tr').remove();
  }else if( jQuery( document ).find( '.additional_fav_input' ).length == 1 ) {
    jQuery(document).find( 'input[name="additional_fav_list[]"]' ).val('');
  }
});
</script>
<script type="text/javascript">
  <?php
  if ($content[0]->shop_version) {
    if ($this->session->userdata('action')) {
  ?>
      var action = '<?php echo $this->session->userdata('action'); ?>';
      var shop_version = <?php echo $content[0]->shop_version; ?>;
      var company_id = <?php echo $content[0]->id; ?>;
      var company_role = "<?php echo $content[0]->role; ?>";
      var company_parent_id = <?php echo $content[0]->parent_id; ?>;
      jQuery(document).ready(function() {
        if (shop_version == 2 || shop_version == 3) {
          jQuery.post(
            "<?php echo base_url(); ?>cp/shop_all/update_json_files/" + shop_version + "/" + company_id + "/" + company_role + "/" + company_parent_id, {
              'action': action
            },
            function(data) {},
            'json'
          );
        }
      });

  <?php
      $this->session->unset_userdata('action');
    }
  } ?>
  // var check2="<?php if (!empty($meattime_setting) && $meattime_setting['status'] == 1) echo 1; ?>";
  var upload = "<?php echo _('Upload Image'); ?>";

  jQuery(document).ready(function($) {
    // if(check2=='1')
    // {
    //  jQuery("#show_meat_type").show();
    //  jQuery("#show_meat_slot").show();
    // }
    // 


    $("#bluecherry_reg").click(function() {
      var comp_id = $('#company_id').val();
      if (comp_id && comp_id != '') {
        jQuery.ajax({
          url: base_url + 'mcp/companies/reg_bluecherry',
          data: {
            'comp_id': comp_id
          },
          type: 'POST',
          dataType: 'json',
          success: function(response) {
            if (response && response != '') {
              alert("<?php echo _('Registered successfull'); ?>");
            } else {
              alert("<?php echo _('Something went wrong'); ?>");
            }
          }
        });
      }
    });


    $(".thickboxed_label").click(function() {
      //alert('abc');
      var num = $(this).attr('attr_id');
      var width = $(this).attr('attr_width');
      //alert(base_url+"cp/image_upload/ajax_img_upload/cp/"+num+"?height=400&width=600");
      tb_show(upload, base_url + "cp/image_upload/ajax_img_upload/cp/" + num + '/' + width + "?height=0&width=600", "true");
      //alert('ab');
    });
  });
  var cropping = "<?php echo _('Cropping'); ?>";

  function updateCoords(c) {
    jQuery('#x').val(c.x);
    jQuery('#y').val(c.y);
    jQuery('#w').val(c.w);
    jQuery('#h').val(c.h);
  }

  function checkCoords() {
    if (parseInt(jQuery('#w').val())) return true;
    alert("<?php echo _('Please select a crop region then press submit.'); ?>");
    return false;
  }

  function updatePreview(c) {
    if (parseInt(c.w) > 0) {
      var rx = xsize / c.w;
      var ry = ysize / c.h;

      jQuerypimg.css({
        width: Math.round(rx * boundx) + 'px',
        height: Math.round(ry * boundy) + 'px',
        marginLeft: '-' + Math.round(rx * c.x) + 'px',
        marginTop: '-' + Math.round(ry * c.y) + 'px'
      });
    }
  }

  function crop(i) {
    if (i == 1) {
      jQuery("#uploaded_img").append('<img src="<?php echo base_url(); ?>assets/cp/images/loader.gif" alt="<?php echo _("Cropping"); ?>...."/>');
    } else {
      jQuery("#uploaded_image").append('<img src="<?php echo base_url(); ?>assets/cp/images/loader.gif" alt="<?php echo _("Cropping"); ?>...."/>');
    }

    jQuery.ajax({
      url: base_url + 'cp/image_upload/crop_image',
      data: {
        'image_name': jQuery("#image_name").val(),
        'x': jQuery("#x").val(),
        'y': jQuery("#y").val(),
        'w': jQuery("#w").val(),
        'h': jQuery("#h").val()
      },
      type: 'POST',
      success: function(response) {
        if (i == 1) {
          jQuery("#uploaded_img").html(response);
          jQuery("#uploaded_img").focus();
        } else {
          jQuery("#uploaded_image").html(response);
          jQuery("#uploaded_image").focus();
        }
      }
    });
  }

  function gal_crop(obj) {

    var cur_id = jQuery(obj).parent().parent().attr('id');
    var ord = cur_id.replace('uploaded_image', '');

    if (ord == 5) {
      var width = 1920;
    } else if (ord == 7) {
      var width = 250;
    } else {
      var width = '';
    }

    jQuery("#" + cur_id).append('<img src="' + base_url + 'assets/cp/images/loader.gif" alt="' + cropping + '"/>');
    jQuery.ajax({
      url: base_url + 'cp/image_upload/crop_image/' + ord + '/' + width,
      data: {
        'image_name': jQuery("#image_name" + ord).val(),
        'x': jQuery("#x").val(),
        'y': jQuery("#y").val(),
        'w': jQuery("#w").val(),
        'h': jQuery("#h").val()
      },
      type: 'POST',
      success: function(response) {
        jQuery("#" + cur_id).html(response);
        jQuery("#" + cur_id).focus();
        if (ord == 5) {
          var width = jQuery(document).find('#img_width').val(),
            height = jQuery(document).find('#img_height').val();
          jQuery(document).find('#uploaded_image5').css({
            'width': width + 'px',
            'height': height + 'px',
            'max-width': '733px',
            'max-height': '300px',
            'margin': '5px'
          });
          jQuery(document).find('#uploaded_image5 img').css({
            'z-index': '-1',
            'position': 'relative'
          });
          brightness_slider();
        }
      }
    });
  };

  function brightness_slider() {
    jQuery('.brightness_slider').slider({
      min: 0,
      max: 100,
      step: 1,
      slide: function(event, ui) {
        var brightness = ui.value / 100;

        jQuery(document).find('#transparency').val(brightness);
        jQuery(document).find('.brightness_percent').html(ui.value + ' %');
        jQuery(document).find('#uploaded_image5').css({
          'background': 'linear-gradient( rgba(0, 0, 0, ' + brightness + ' ), rgba(0, 0, 0, ' + brightness + ' ))'
        });

      }
    });
  };

  function show_ing_sys(assoc) {
    if (assoc == 'k') {
      if (jQuery("#k_assoc").is(':checked')) {
        jQuery("#ing_sys").show();
        jQuery("#i_assoc").attr('checked', false);
      } else {
        jQuery("#ing_sys").hide();
      }
    } else if (assoc == 'i') {
      if (jQuery("#i_assoc").is(':checked')) {
        jQuery("#ing_sys").show();
        jQuery("#k_assoc").attr('checked', false);
      } else {
        jQuery("#ing_sys").hide();
      }
    }
  }

  function show_hide_credit(obj) {

    var i = jQuery(obj).val();
    if (i == 4 || i == 5 || i == 6) {
      jQuery("#fdd_credits_tr").show();
    } else {
      jQuery("#fdd_credits_tr").hide();
    }

  }

  function change_acc_type_sub(comp_id, obj) {
    var status = obj.options[obj.selectedIndex].value;
    var comp_id = comp_id;

    if (status != '' && comp_id != '') {

      jQuery.ajax({
        type: "POST",
        url: base_url + "mcp/companies/update_subcompany",
        dataType: 'json',
        data: {
          'comp_id': comp_id,
          'acc_type': status
        },
        success: function(response) {
          if (response.RESULT == "success") {
            alert("Company Account Type updated successfully.");
          } else if (response.RESULT == "fail") {
            alert("Error occurred while updating Company`s Account Type.Please Try Again.");
          }
        }
      });
    }
  }

  function company_type(comp_id, obj) {

    var types = jQuery(obj).val();

    jQuery.ajax({
      type: "POST",
      url: base_url + "mcp/companies/update_subcompany",
      dataType: 'json',
      data: {
        'comp_id': comp_id,
        'type_id': types
      },
      success: function(response) {
        // if(response.RESULT == "success"){
        // alert("Company Account Type updated successfully.");  
        // }else if(response.RESULT == "fail"){
        // alert("Error occurred while updating Company`s Account Type.Please Try Again."); 
        // }
      }
    });

  }

  function change_haccp_status(comp_id, obj) {

    var status = jQuery(obj).val();
    jQuery.ajax({
      type: "POST",
      url: base_url + "mcp/companies/update_subcompany",
      dataType: 'json',
      data: {
        'comp_id': comp_id,
        'haccp_status': status,
        'haccp': '1'
      },
      success: function(response) {
        if (response.RESULT == "success") {
          alert("Company Haccp Status updated successfully.");
        } else if (response.RESULT == "fail") {
          alert("Error occurred while updating Company`s Haccp Status.Please Try Again.");
        }
      }
    });
  }

  var successfully_updated_txt = "<?php echo _('Successfully updated') ?>"
  var error_update_txt = "<?php echo _('Error occurred while updating. Please Try Again') ?>"

  $(document).on('click', '.show_lead', function() {
    var comp_id = jQuery('#company_id').val();
    var is_sho_lead = '0';
    if (jQuery(this).is(":checked")) {
      is_sho_lead = '1';
    }

    if (comp_id != '') {
      jQuery.ajax({
        type: "POST",
        url: base_url + "mcp/companies/update_sho_leads",
        dataType: 'json',
        data: {
          'comp_id': comp_id,
          'is_sho_lead': is_sho_lead
        },
        success: function(response) {
          if (response) {
            alert(successfully_updated_txt);
          } else {
            alert(error_update_txt);
          }
        }
      });
    }
  });


  $(document).on('change', '#type_id_cat', function() {
    var html = '<option value="-1">--Select Company Type--</option>';
    jQuery('#type_id').html('');
    jQuery.ajax({
      type: "POST",
      url: base_url + "mcp/companies/get_types",
      dataType: 'json',
      data: {
        'grp_id': jQuery(this).val()
      },
      success: function(response) {
        if (response.length > 0) {
          jQuery.each(response, function(index, value) {
            html += '<option value="' + value.id + '">' + value.company_type_name + '</option>';
          });
        }
        jQuery('#type_id').append(html);
      }
    })
  });
</script>

<table width="100%" cellspacing="0" cellpadding="0" border="0">
  <tbody>
    <tr>
      <td valign="top" align="center">
        <table width="98%" cellspacing="0" cellpadding="0" border="0">
          <tbody>
            <tr>
              <td valign="top" align="center" style="border:#8F8F8F 1px solid">
                <table width="100%" cellspacing="0" cellpadding="0" border="0">
                  <tbody>
                    <tr>
                      <td align="center" style="padding:15px 0px 5px 0px">
                        <table width="98%" cellspacing="0" cellpadding="0" border="0" style="background:url(<?php echo base_url(''); ?>assets/mcp/images/bg.jpg) left top repeat-x;" class="page_caption">
                          <tbody>
                            <tr>
                              <td width="94%" align="left">
                                <h3><?php echo _('Update Company Details'); ?></h3>
                              </td>
                              <td width="3%" align="right"></td>
                              <td width="3%" align="left">
                                <div class="icon_button">
                                  <img width="16" height="16" border="0" style="cursor:pointer" onClick="javascript:history.back();" title="Go Back" alt="Go Back" src="<?php echo base_url(''); ?>assets/mcp/images/undo.jpg">
                                </div>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </td>
                    </tr>
                    <tr>
                      <td width="100%" valign="top" align="center">
                        <?php
                        if (!empty($content)) : ?>
                          <?php $cont = $content[0]; ?>
                          <script language="javascript" type="text/javascript">
                            function confirm_delete() {
                              var did = '<?php echo $cont->id; ?>';
                              var answer = confirm("Sure, You want to DELETE this company and all its Records ?");
                              if (answer) {
                                window.location = 'index.php?view=companies_add_edit&act=delete&DID=' + did;
                              }
                            }

                            function confirm_delete_this(cid) {
                              var answer = confirm("Sure, You want to DELETE this company and all its Records ?");
                              if (answer) {
                                window.location = '<?php echo base_url(); ?>mcp/companies/delete/' + cid;
                              }
                            }

                            function showSubAdmin(chkbox) {

                              var value = jQuery(chkbox).val();
                              if (value == 'super') {
                                // console.log(document.getElementById("superadmin"));

                                // document.getElementById("superadmin").style.display = "block";

                              } else {
                                // document.getElementById("superadmin").style.display = "none";
                              }


                              if (value == 'admin') {
                                // console.log(document.getElementById("superadmin"));

                                //document.getElementById("admin").style.display = "block";

                              } else {
                                //document.getElementById("admin").style.display = "none";
                              }


                            }

                            function showDivision(parent_id, comp_name = '') {

                              if (parent_id != '') {
                                var company_id = jQuery('.add_division_url').attr('data-sub');
                                var url = '<?php echo base_url(); ?>mcp/companies/get_division';

                                var div_url = '<?php echo base_url(); ?>mcp/companies/division_add_edit/company_id/' + company_id + '/subcomp_id/' + parent_id;
                                jQuery.ajax({
                                  url: url,
                                  data: {
                                    parent: parent_id,
                                    company_id: company_id
                                  },
                                  dataType: 'json',
                                  type: "POST",
                                  success: function(response) {

                                    if (response) {

                                      jQuery('.division_table_data tbody').html(response);
                                      jQuery('#subadmin_name').text('Subadmin ' + comp_name + '');
                                      jQuery('.add_division_url').attr('href', div_url);
                                      jQuery('.division_table').show();
                                    }
                                  }
                                });
                              }
                            }



                            /*Delete banner from database and from server*/
                            function delete_banner(banner_name, company_id, table_column) {
                              var url = '<?php echo base_url(); ?>mcp/companies/delete_banner';
                              if (confirm("Sure, You want to DELETE this ?")) {
                                jQuery.ajax({
                                  url: url,
                                  data: {
                                    banner_name: banner_name,
                                    company_id: company_id,
                                    table_column: table_column
                                  },
                                  type: "POST",
                                  success: function(response) {
                                    if (response.trim() == 'success') {
                                      if (table_column == 'aller_banner_sheet') {
                                        jQuery(".hide_aller_banner_div").remove();
                                        jQuery("#delete_aller_banner").hide();
                                      } else if (table_column.trim() == 'aller_upload_image') {
                                        jQuery(".hide_image_box").remove();
                                        jQuery("#delete_aller_image").hide();

                                      } else if (table_column.trim() == 'sheet_banner') {
                                        jQuery("#remove_sheet_banner").remove();
                                        jQuery("#delete_sheet_banner").hide();
                                      }

                                    }
                                  }
                                });
                              }
                            }
                          </script>

                          <script type="text/javascript">
                            jQuery(document).ready(function() {
                              jQuery("a[rel='open_ajax']").live('click', function() {
                                var locat = jQuery(this).attr('href');
                                jQuery(this).colorbox({
                                  onLoad: function() {
                                    jQuery("#colorbox").show();
                                  }
                                });
                                return false;
                              });
                              jQuery("#cboxClose").on("click", function() {
                                jQuery('.sub').removeClass('cboxElement');
                                jQuery('.add_division_url').removeClass('cboxElement');
                              });
                            });
                          </script>
                          <?php
                          $attributes = array('class' => 'email', 'id' => 'frm_companies_add_update', 'name' => 'frm_companies_add_update');
                          echo form_open_multipart('mcp/companies/update/' . $cont->id, $attributes);
                          ?>
                          <input type="hidden" name="company_id" value="<?php echo $cont->id; ?>" />
                          <table width="98%" cellspacing="0" cellpadding="0" border="0" style="border:1px solid #003366; text-align:left">
                            <tbody>
                              <tr>
                                <td height="20" bgcolor="#003366" align="left" style="padding-left:10px;" class="whiteSmallBold" colspan="5"><?php echo _('Company Information'); ?>
                                </td>
                              </tr>
                              <tr>
                                <td height="10" colspan="5">&nbsp;</td>
                              </tr>
                              <tr>
                                <td height="10" align="center" colspan="5"><span style="color:#FF0000" id="dup_msg"></span></td>
                              </tr>
                              <tr>
                                <td width="10%">
                                  &nbsp;
                                  <input type="hidden" value="companies_add_edit" name="OBS BEstelsysteem - SiteMatic BVBA_REF_VIEW">
                                </td>

                                <td width="37%">
                                  <table width="100%" cellspacing="0" cellpadding="0" border="0">
                                    <tbody>
                                      <tr>
                                        <td height="30" class="wd_text"><?php echo _('ID'); ?>&nbsp;&nbsp;</td>
                                        <td><?php echo $cont->id ?></td>
                                      </tr>
                                      <tr>
                                        <td height="30" class="wd_text">
                                          <?php echo _('Client Nbr'); ?>
                                        </td>
                                        <td>
                                          <input type="text" size="30" class="textbox" id="client_no" name="client_no" value="<?php echo $cont->client_no; ?>">
                                        </td>
                                      </tr>
                                      <tr>
                                        <td height="30" class="wd_text">
                                          <?php echo _('Company Name'); ?><span class="red_star">*</span>
                                        </td>
                                        <td>
                                          <input type="text" size="30" class="textbox" id="company_name" name="company_name" value="<?php echo $cont->company_name; ?>">
                                        </td>
                                      </tr>
                                      <tr>
                                        <td height="30" class="wd_text">
                                          <?php echo _('Company Type'); ?><span class="red_star">*</span>
                                        </td>
                                        <td>
                                          <select style="width:100px; margin-top: 0 !important;" class="textbox" type="select" id="type_id_cat" name="type_id_cat">
                                            <option value="-1">--<?php echo _('Select Company Group'); ?>--</option>

                                            <?php if (!empty($company_type_group)) {
                                              foreach ($company_type_group as $ctg) { ?>
                                                <option value="<?php echo $ctg['id']; ?>" <?php if ($cont->comp_grp == $ctg['id']) {
                                                                                            echo 'selected="selected"';
                                                                                          } ?>><?php echo $ctg['comp_grp_name']; ?></option>
                                            <?php }
                                            } ?>
                                          </select>
                                          <?php $company_types = explode("#", $cont->type_id); ?>
                                          <select style="width:200px" class="textbox" type="select" id="type_id" name="type_id[]" multiple="multiple">
                                            <option value="-1" style="background: none repeat scroll 0 0 #CCCCCC;">-- <?php echo _('Select Company Type'); ?> --</option>

                                            <?php if (!empty($company_type)) {
                                              foreach ($company_type as $ct) { ?>
                                                <option value="<?php echo $ct->id; ?>" <?php if (in_array($ct->id, $company_types)) {
                                                                                          echo 'selected="selected"';
                                                                                        } ?>><?php echo $ct->company_type_name; ?></option>
                                            <?php }
                                            } ?>

                                          </select>
                                        </td>
                                      </tr>
                                      <tr>
                                        <td height="30" class="wd_text">
                                          <?php echo _('First Name'); ?>
                                        </td>
                                        <td>
                                          <input type="text" size="30" class="textbox" id="first_name" name="first_name" value="<?php echo $cont->first_name; ?>">
                                        </td>
                                      </tr>
                                      <tr>
                                        <td height="30" class="wd_text">
                                          <?php echo _('Last Name'); ?>
                                        </td>
                                        <td>
                                          <input type="text" size="30" class="textbox" id="last_name" name="last_name" value="<?php echo $cont->last_name; ?>">
                                        </td>
                                      </tr>
                                      <tr>
                                        <td height="30" class="wd_text"><?php echo _('Email'); ?>
                                          <span class="red_star">*</span>
                                        </td>
                                        <td><input type="text" onChange="check_email(this.value,this.id);" size="30" class="textbox required" id="email" name="email" value="<?php echo $cont->email; ?>">
                                        </td>
                                      </tr>
                                      <!-- Additional Email -->
                                      <?php

                                      if ($cont->additional_email != '' && !empty(json_decode($cont->additional_email))) {
                                        $additional_email = (array) json_decode($cont->additional_email);
                                        $i = 0;
                                        foreach ($additional_email as $mail) { ?>
                                          <tr class="additional_email_row">
                                            <?php if ($i == 0) { ?>
                                              <td height="30" class="wd_text add_email_text"><?php echo _('Additional Email'); ?></td>
                                            <?php } else { ?>
                                              <td height="30" class="wd_text"></td>
                                            <?php } ?>
                                            <td>
                                              <input type="text" onChange="check_email(this.value,this.id);" size="30" class="textbox" name="additional_email[]" value="<?php echo $mail; ?>" id="additional_email<?php echo $i ?>">
                                            </td>
                                            <td>
                                              <span><input class="add_additional_email" value="+" style="border-radius: 0;height: 21px;width: 21px;" type="button"></span>
                                              <span><input class="remove_additional_email" value="-" style="border-radius: 0;height: 21px;width: 21px; <?php if ($i == 0) {
                                                                                                                                                          echo 'display: none;';
                                                                                                                                                        } ?>" type="button"></span>
                                            </td>
                                          </tr>
                                        <?php
                                          $i++;
                                        }
                                      } else { ?>
                                        <tr class="additional_email_row">
                                          <td height="30" class="wd_text add_email_text"><?php echo _('Additional Email'); ?></td>
                                          <td>
                                            <input type="text" onChange="check_email(this.value,this.id);" size="30" class="textbox" name="additional_email[]" value="" id="additional_email0">
                                          </td>
                                          <td>
                                            <span><input class="add_additional_email" value="+" style="border-radius: 0;height: 21px;width: 21px;" type="button"></span>
                                            <span><input class="remove_additional_email" value="-" style="border-radius: 0;height: 21px;width: 21px; display: none;" type="button"></span>
                                          </td>
                                        </tr>
                                      <?php } ?>
                                      <tr>
                                        <td height="30" class="wd_text"><?php echo _('Phone'); ?>
                                          <span class="red_star">*</span>
                                        </td>
                                        <td><input type="text" size="30" class="textbox" id="phone" name="phone" value="<?php echo $cont->phone; ?>">
                                        </td>
                                      </tr>
                                      <tr>
                                        <td height="30" class="wd_text"><?php echo _('Website'); ?>
                                        </td>
                                        <td><input type="text" size="30" class="textbox" id="website" name="website" value="<?php echo $cont->website; ?>"></td>
                                      </tr>
                                      <tr>
                                        <td class="wd_text">
                                          <input type="radio" id="mark_high_priority_client" value="<?php if (isset($cont->is_high_priority)) {
                                                                                                      echo $cont->is_high_priority;
                                                                                                    } ?>" name="is_high_priority" <?php if (isset($cont->is_high_priority) && ($cont->is_high_priority == '1')) {
                                                                                                                                    echo 'checked="checked"';
                                                                                                                                  } ?>>
                                        </td>
                                        <td height="30"><?php echo _('Priority client'); ?></td>
                                      </tr>
                                    </tbody>
                                  </table>
                                </td>
                                <td width="2%">&nbsp;</td>
                                <td width="37%">
                                  <table width="100%" cellspacing="0" cellpadding="0" border="0">
                                    <tbody>
                                      <tr>
                                        <td height="30" class="wd_text">&nbsp;</td>
                                        <td>&nbsp;</td>
                                      </tr>
                                      <tr>
                                        <td height="30" class="wd_text"><?php echo _('Address'); ?>
                                          <span class="red_star">*</span>
                                        </td>
                                        <td><input type="text" size="30" class="textbox" id="address" name="address" value="<?php echo $cont->address; ?>">
                                        </td>
                                      </tr>
                                      <tr>
                                        <td height="30" class="wd_text"><?php echo _('Zipcode'); ?><span class="red_star">*</span></td>
                                        <td><input type="text" size="30" class="textbox" id="zipcode" name="zipcode" value="<?php echo $cont->zipcode; ?>">
                                        </td>
                                      </tr>
                                      <tr>
                                        <td height="30" class="wd_text"><?php echo _('City'); ?><span class="red_star">*</span></td>
                                        <td><input type="text" size="30" class="textbox" id="city" name="city" value="<?php echo $cont->city; ?>">
                                        </td>
                                      </tr>
                                      <tr>
                                        <td height="30" class="wd_text"><?php echo _('Country'); ?><span class="red_star">*</span></td>
                                        <td>
                                          <select style="width:215px" class="textbox" type="select" id="country_id" name="country_id">
                                            <option value="-1">-- <?php echo _('Select Country'); ?> --</option>
                                            <?php if (!empty($country)) : ?>
                                              <?php foreach ($country as $cont1) : ?>
                                                <?php if ($cont1->id == 21 || $cont1->id == 150) { ?>
                                                  <option value="<?php echo $cont1->id; ?>" <?php if ($cont->country_id == $cont1->id) {
                                                                                              echo 'selected="selected"';
                                                                                            } ?>><?php echo $cont1->country_name; ?></option>
                                                <?php } ?>
                                              <?php endforeach; ?>
                                            <?php endif; ?>

                                          </select>
                                        </td>
                                      </tr>

                                      <tr>
                                        <td height="30" class="wd_text"><?php echo _('Existing Order Page'); ?></td>
                                        <td><input type="text" size="30" class="textbox" id="existing_order_page" name="existing_order_page" value="<?php echo $cont->existing_order_page; ?>">
                                        </td>
                                      </tr>

                                      <tr>
                                        <td height="30" class="wd_text">&nbsp;</td>
                                        <td>&nbsp;</td>
                                      </tr>
                                      <tr>
                                        <td height="30" class="wd_text"><?php echo _('Username'); ?><span class="red_star">*</span></td>
                                        <td><input type="text" onChange="check_username(this.value);" size="30" class="textbox" id="username" name="username" value="<?php echo $cont->username; ?>" readonly="true"></td>
                                      </tr>
                                      <tr>
                                        <td height="30" class="wd_text"><?php echo _('Reset Password'); ?><span class="red_star">*</span></td>
                                        <td><input type="button" name="reset-password" value="<?php echo _('Reset password'); ?>" /></td>
                                      </tr>
                                      <tr>
                                        <td colspan="2" align="right" style="padding: 7px;">
                                          <a href="javascript:void(0);" onclick="get_login('<?php echo $cont->id; ?>','<?php echo $cont->username; ?>');" class="btnWhiteBack"><?php echo _('LOGIN'); ?></a>
                                        </td>
                                      </tr>
                                      <tr>
                                        <td colspan="2" align="right" style="padding: 7px;">
                                          <a href="javascript:void(0);" onclick="get_login_fdd('<?php echo $cont->id; ?>','<?php echo $cont->username; ?>');" class="btnWhiteBack"><?php echo _('LOGIN 2.0'); ?></a>
                                        </td>
                                      </tr>
                                    </tbody>
                                  </table>
                                </td>
                                <td width="10%">&nbsp;</td>
                              </tr>

                              <tr>
                                <td valign="middle" colspan="5">


                                  <!-- table-wrap    -->
                                  <table width="100%" cellspacing="0" cellpadding="0" border="0">
                                    <tbody>
                                      <tr>
                                        <td height="10" colspan="4">&nbsp;</td>
                                      </tr>
                                      <tr>
                                        <td width="270" height="80">&nbsp;</td>
                                        <td class="wd_text"><?php echo _('Admin Remarks'); ?>&nbsp;&nbsp;</td>
                                        <td>
                                          <textarea cols="50" rows="5" class="textbox" type="textarea" id="admin_remarks" name="admin_remarks"><?php echo isset($cont->admin_remarks) && ($cont->admin_remarks != '0') ? $cont->admin_remarks : ''; ?></textarea>
                                        </td>
                                        <td width="281">&nbsp;</td>
                                      </tr>
                                      <tr>
                                        <td height="10" colspan="4">&nbsp;</td>
                                      </tr>
                                      <tr>
                                        <td width="270" height="80">&nbsp;</td>
                                        <td class="wd_text"><?php echo _('Resellers Remark'); ?>&nbsp;&nbsp;</td>
                                        <td>
                                          <textarea cols="50" rows="5" class="textbox" type="textarea" id="reseller_remarks" name="reseller_remarks"><?php echo isset($cont->reseller_remarks) && ($cont->reseller_remarks != '0') ? $cont->reseller_remarks : ''; ?></textarea>
                                        </td>
                                        <td width="281">&nbsp;</td>
                                      </tr>
                                      <tr>
                                        <td height="10" colspan="4">&nbsp;</td>
                                      </tr>

                                      <tr>
                                        <td width="270" height="30">&nbsp;</td>
                                        <td class="wd_text"><?php echo _('Account Type'); ?>&nbsp;&nbsp;
                                        </td>
                                        <td>
                                          <select style="width:250px" class="textbox" type="select" id="ac_type_id" name="ac_type_id" onchange="show_hide_credit(this)">
                                            <?php if (!empty($account_types)) : ?>
                                              <?php foreach ($account_types as $at) : ?>
                                                <option value="<?php echo $at->id ?>" <?php if ($cont->ac_type_id == $at->id) {
                                                                                        echo 'selected="selected"';
                                                                                      } ?>><?php echo $at->ac_title ?></option>
                                              <?php endforeach; ?>
                                            <?php endif; ?>

                                          </select>
                                        </td>
                                        <td width="281">&nbsp;</td>
                                      </tr>

                                      <tr id="fdd_credits_tr" style="display:<?php if ($cont->ac_type_id == 4 || $cont->ac_type_id == 5 || $cont->ac_type_id == 6) { ?>;<?php } else { ?> none;<?php } ?>">
                                        <td width="270" height="30">&nbsp;</td>
                                        <td class="wd_text"><?php echo _('FoodDESK Products credits'); ?>&nbsp;&nbsp;</td>
                                        <td>
                                          <input class="textbox required" type="text" name="fdd_credits" id="fdd_credits" value="<?php echo $cont->fdd_product_credit; ?>">
                                        </td>
                                        <td width="281">&nbsp;</td>
                                      </tr>

                                      <tr>
                                        <td width="270" height="30">&nbsp;</td>
                                        <td class="wd_text"><?php echo _('Package Preferred'); ?>&nbsp;&nbsp;</td>
                                        <td>
                                          <select style="width:250px" class="textbox" type="select" id="packages_id" name="packages_id">
                                            <option value="0">-- <?php echo _('Select Package'); ?> --</option>
                                            <?php if (!empty($package)) : ?>
                                              <?php foreach ($package as $cont2) : ?>
                                                <option value="<?php echo $cont2->id ?>" <?php if ($cont->packages_id == $cont2->id) {
                                                                                            echo 'selected="selected"';
                                                                                          } ?>><?php echo $cont2->package_name ?></option>
                                              <?php endforeach; ?>
                                            <?php endif; ?>

                                          </select>
                                        </td>
                                        <td width="281">&nbsp;</td>
                                      </tr>

                                      <tr>
                                        <td width="270" height="30">&nbsp;</td>
                                        <td class="wd_text"><?php echo _('Email Ads'); ?>&nbsp;&nbsp;</td>
                                        <td>
                                          <select type="select" id="email_ads" name="email_ads">
                                            <option value="0" <?php if ($cont->email_ads == 0) {
                                                                echo 'selected="selected"';
                                                              } ?>><?php echo _('No'); ?></option>
                                            <option value="1" <?php if ($cont->email_ads == 1) {
                                                                echo 'selected="selected"';
                                                              } ?>><?php echo _('Yes'); ?></option>
                                          </select>
                                        </td>
                                        <td width="281">&nbsp;</td>
                                      </tr>
                                      <tr>
                                        <td width="270" height="30">&nbsp;</td>
                                        <td class="wd_text"><?php echo _('Frontend Footer Text'); ?></td>
                                        <td>
                                          <select type="select" id="footer_text" name="footer_text">
                                            <option value="0" <?php if ($cont->footer_text == 0) {
                                                                echo 'selected="selected"';
                                                              } ?>><?php echo _('No'); ?></option>
                                            <option value="1" <?php if ($cont->footer_text == 1) {
                                                                echo 'selected="selected"';
                                                              } ?>><?php echo _('Yes'); ?></option>
                                          </select>
                                        </td>
                                        <td width="281">&nbsp;</td>
                                      </tr>

                                      <!-- #################### FOR SHOW/HIDE PORTAL LINK at SHOP CHECKOUT ###################### -->
                                      <!-- <tr>
                                    <td width="270" height="30">&nbsp;</td>
                                    <td class="wd_text"><?php echo _('Show Bestelonline at checkout (only PRO)'); ?></td>
                                    <td>
                                      <select type="select" id="show_bo_link_in_shop" name="show_bo_link_in_shop">
                                                            <option value="0" <?php if ($cont->show_bo_link_in_shop == 0) {
                                                                                echo 'selected="selected"';
                                                                              } ?> ><?php echo _('No'); ?></option>
                                                            <option value="1" <?php if ($cont->show_bo_link_in_shop == 1) {
                                                                                echo 'selected="selected"';
                                                                              } ?> ><?php echo _('Yes'); ?></option>
                                                        </select>
                                    </td>
                                    <td width="281">&nbsp;</td>
                                  </tr> -->
                                      <!-- #################### ------------------------------------------- ###################### -->

                                      <!--   <tr>
                                    <td height="10" colspan="4">&nbsp;</td>
                                  </tr>-->
                                      <!-- #################### For Labeler ################################### -->
                                      <!-- <tr>
                                    <td>&nbsp;</td>
                                    <td height="30" class="wd_text"><?php echo _('Activate Labeler'); ?></td>
                                    <td>
                      <input type="checkbox" value="1" class="textbox" id="activate_labeler" name="activate_labeler" <?php if ($labeler_settings['0']->activate_labeler == 1) {
                                                                                                                        echo 'checked="checked"';
                                                                                                                      } ?> >
                    </td>
                                    <td>&nbsp;</td>
                                  </tr> -->
                                      <!-- ------############################################################3------ -->
                                      <tr>
                                        <td height="10" colspan="4">&nbsp;</td>
                                      </tr>
                                      <!-- #################### For Intro and Next Step ################################### -->
                                      <tr>
                                        <td>&nbsp;</td>
                                        <td height="30" class="wd_text"><?php echo _('Hide tabs what is next and next step from intro'); ?></td>
                                        <td>
                                          <input type="checkbox" value="1" class="textbox" id="hide_next_step" name="hide_next_step" <?php if ($labeler_settings['0']->hide_next_step == 1) {
                                                                                                                                        echo 'checked="checked"';
                                                                                                                                      } ?>>
                                        </td>
                                        <td>&nbsp;</td>
                                      </tr>
                                      <!-- ------############################################################3------ -->
                                      <tr>
                                        <td height="10" colspan="4">&nbsp;</td>
                                      </tr>

                                      <!--  <tr>
                                    <td>&nbsp;</td>
                                    <td height="30" class="wd_text"><?php echo _('2 year Subscription'); ?></td>
                                    <td>
                                      <input type="checkbox" value="1" class="textbox" id="5year_subscription" name="5year_subscription" <?php if ($cont->{'5year_subscription'} == 1) {
                                                                                                                                            echo 'checked="checked"';
                                                                                                                                          } ?> >
                                    </td>
                                    <td>&nbsp;</td>
                                  </tr> -->
                                      <tr>
                                        <td>&nbsp;</td>
                                        <td height="30" class="wd_text">
                                          <?php echo _('Date Registration'); ?><span class="red_star">*</span>
                                        </td>
                                        <td>
                                          <!--<input type="text" value="<?php echo $cont->registration_date; ?>" size="10" id="registration_date" name="registration_date" onchange="get_expiry(this.value);">-->

                                          <input name="registration_date" id="registration_date" type="text" class="textbox" size="10" value="<?php echo $cont->registration_date; ?>" /><img border="0" src="<?php echo base_url(); ?>assets/mcp/images/cal.jpeg" width="30" height="30" name="date_picker" id="date_picker" style="vertical-align:bottom">

                                          <script type="text/javascript">
                                            var cal = Calendar.setup({
                                              onSelect: function(cal) {
                                                cal.hide()
                                              }
                                            });
                                            cal.manageFields("date_picker", "registration_date", "%Y-%m-%d");
                                          </script>
                                        </td>
                                        <td>&nbsp;</td>
                                      </tr>
                                      <tr>
                                        <td>&nbsp;</td>
                                        <td height="30" class="wd_text"><?php echo _('Expiry Date (Every 1 Year)'); ?></td>
                                        <td><input type="text" readonly="readonly" value="<?php echo $cont->expiry_date; ?>" size="10" class="textbox" id="expiry_date" name="expiry_date">
                                        </td>
                                        <td>&nbsp;</td>
                                      </tr>
                                      <tr>
                                        <td>&nbsp;</td>
                                        <td height="30" class="wd_text"><?php echo _('Earnings/Year'); ?><span class="red_star"> *</span></td>
                                        <td>
                                          <input type="text" size="10" class="textbox required" id="earnings_year" name="earnings_year" value="<?php echo $cont->earnings_year; ?>">
                                          &nbsp;&nbsp;<b>&euro;</b>
                                        </td>
                                        <td>&nbsp;</td>
                                      </tr>


                                      <!--=================================
                                =            inner-table            =
                                ==================================-->

                                      <tr>
                                        <td colspan="6">
                                          <table width="100%" style=" border-collapse: collapse;">
                                            <tbody>
                                              <tr>
                                                <td>
                                              <tr>

                                                <td height="30" class="wd_text">
                                                  <?php echo _('Only show ingredients in product detail page'); ?>
                                                  <span>
                                                    <input type="checkbox" value="1" class="textbox" id="ingredient_system" name="ingredient_system" <?php if ($cont->ingredient_system == 1) {
                                                                                                                                                        echo 'checked="checked"';
                                                                                                                                                      } ?>>
                                                  </span>
                                                </td>
                                              </tr>

                                              <tr>

                                                <td height="30" class="wd_text">
                                                  <?php echo _('Show Demo link webshop'); ?>
                                                  <span>
                                                    <input type="checkbox" value="1" class="textbox" name="show_demoshop_link" <?php if ($cont->show_demoshop_link == 1) {
                                                                                                                                  echo 'checked="checked"';
                                                                                                                                } ?>>
                                                  </span>
                                                </td>
                                              </tr>
                                              <tr>
                                                <td height="30" class="wd_text">
                                                  <?php echo _('Show Recipe'); ?>
                                                  <span>
                                                    <input type="checkbox" value="1" class="textbox" name="show_recipe" <?php if ($cont->show_recipe == 1) {
                                                                                                                          echo 'checked="checked"';
                                                                                                                        } ?>>
                                                  </span>
                                                </td>
                                              </tr>

                                              <tr>
                                                <td height="30" class="wd_text">
                                                  <?php echo _("This client uses"); ?>
                                                  <span>
                                                    <select name="client_system_name" id="client_system_name">
                                                      <option value="0" <?php if (!empty($cont->system_selected) && json_decode($cont->system_selected)[0] == 0) { ?>selected="selected" <?php } ?>>----</option>
                                                      <?php if (isset($system_info)) {
                                                        foreach ($system_info as $system_info_val) {
                                                      ?>
                                                          <option value="<?php echo $system_info_val['id'] ?>" <?php if (!empty($cont->system_selected) && json_decode($cont->system_selected)[0] == $system_info_val['id']) { ?>selected="selected" <?php } ?>><?php echo $system_info_val['system_name']; ?></option>
                                                      <?php }
                                                      } ?>
                                                    </select>
                                                    <select id="sub_comp" name="sub_comp_for_digi" style="display: none;float: right;">
                                                      <option <?php if (json_decode($cont->system_selected)[1] == "Old") {
                                                                echo "selected";
                                                              } ?> value="Old"><?php echo _("Old"); ?></option>
                                                      <option <?php if (json_decode($cont->system_selected)[1] == "New") {
                                                                echo "selected";
                                                              } ?> value="New"><?php echo _("New"); ?></option>
                                                    </select>
                                                  </span>
                                                </td>
                                              </tr>


                                              <tr id="ing_sys">

                                                <td height="30" class="wd_text">
                                                  <?php echo _("3rd Party connection"); ?>
                                                  <span>
                                                    <select name="third_pary_con">
                                                      <option value=""><?php echo _("Select"); ?></option>
                                                      <?php if (!empty($third_party_connections)) {
                                                        foreach ($third_party_connections as $key => $connections) { ?>
                                                          <option <?php if (isset($cont->third_pary_con) && $cont->third_pary_con == $connections['id']) {
                                                                    echo "selected='selected'";
                                                                  } ?> value="<?php echo $connections['id'] ?>"><?php echo $connections['name']; ?></option>
                                                      <?php }
                                                      } ?>
                                                    </select>
                                                  </span>

                                                </td>

                                                <td height="30" class="wd_text" style="text-align: left;">
                                                  <div <?php if (isset($cont->third_pary_con) && $cont->third_pary_con != '') { ?>style="background-color: #e7e7e7;display: inline-block;padding: 4px 8px;" <?php } ?>>
                                                    <?php echo _('Enable delete/add in production'); ?>
                                                    <span>
                                                      <input type="checkbox" size="10" class="textbox" <?php if (isset($cont->third_pary_con) && $cont->third_pary_con == '') {
                                                                                                          echo "checked='checked'";
                                                                                                        } ?>>
                                                    </span>
                                                  </div>
                                                </td>

                                              </tr>
                                              <tr>

                                                <td height="30" class="wd_text">
                                                  <?php echo _("Hide recipe section in admin CP"); ?>
                                                  <span>
                                                    <input type="checkbox" name="hide_recipe_in_cp" value="1" <?php if (isset($cont->hide_recipe_in_cp) && $cont->hide_recipe_in_cp == '1') {
                                                                                                                echo "checked='checked'";
                                                                                                              } ?>>
                                                  </span>
                                                </td>

                                                <td>&nbsp;</td>
                                              </tr>

                                              <tr>

                                                <td height="30" class="wd_text">
                                                  <?php echo _('Hide “Downloads” in Settings'); ?>
                                                  <span>
                                                    <input type="checkbox" class="textbox" id="hide_download" name="hide_download" value="1" <?php if (isset($cont->hide_download) && $cont->hide_download == 1) { ?> checked="checked" <?php } ?>>
                                                  </span>
                                                </td>

                                                <td>&nbsp;</td>
                                              </tr>

                                              <tr>
                                              <tr>

                                                <td height="30" class="wd_text">
                                                  <?php echo _('Enable products download in Settings'); ?>
                                                  <span>
                                                    <input type="checkbox" class="textbox" id="hide_product_download" name="hide_product_download" value="1" <?php if (isset($cont->hide_product_download) && $cont->hide_product_download == '1') { ?> checked="checked" <?php } ?>>
                                                  </span>
                                                </td>

                                                <td>&nbsp;</td>
                                              </tr>

                                              <tr>

                                                <td class="wd_text" height="30">
                                                  <?php echo _('Enable Select and Copy'); ?>
                                                  <span>
                                                    <input type="checkbox" size="10" class="textbox" id="copy_content" name="copy_content" <?php if (isset($cont->select_n_copy) && $cont->select_n_copy == 1) { ?> checked="checked" <?php } ?>>
                                                  </span>
                                                </td>
                                              </tr>

                                              <tr>

                                                <td class="wd_text">
                                                  <?php echo _('Enable Infodesk'); ?>
                                                  <span>
                                                    <input type="checkbox" size="10" value="1" class="textbox" id="enable_infodesk" name="enable_infodesk" <?php if (isset($cont->enable_infodesk) && $cont->enable_infodesk == 1) { ?> checked="checked" <?php } ?>>
                                                  </span>
                                                </td>


                                              </tr>

                                              <tr>
                                                <td class="wd_text" height="30">
                                                  <?php echo _('Activate Non-Food Setting'); ?>
                                                  <span>
                                                    <input type="checkbox" size="10" value="1" class="textbox" id="ingredient_article_status" name="ingredient_article_status" <?php if (isset($cont->ingredient_article_status) && $cont->ingredient_article_status == 1) { ?>checked="checked" <?php } ?>>
                                                  </span>
                                                </td>
                                              </tr>

                                              <tr>

                                                <td class="wd_text">
                                                  <?php echo _('Hide checkout for PWS'); ?>
                                                  <span>
                                                    <input type="checkbox" size="10" value="1" class="textbox" id="hide_checkout" name="hide_checkout" <?php if (isset($cont->hide_checkout) && $cont->hide_checkout == 1) { ?> checked="checked" <?php } ?>>
                                                  </span>
                                                </td>


                                              </tr>


                                              <tr>

                                                <!-- <td class="wd_text"> 
                                             <?php echo _('Show Menucard Maker'); ?>
                                             <span>
                                               <input type="checkbox" size="10" value="1" class="textbox" id="show_menukartt_maker" name="show_menukartt_maker"  <?php if (isset($cont->show_menukartt_maker) && $cont->show_menukartt_maker == 1) { ?> checked="checked"<?php } ?>>
                                             </span>
                                           </td> -->

                                              </tr>


                                              <tr>

                                                <td height="30" style="padding:15px; " class="wd_text">
                                                  <input type="radio" onChange="showSubAdmin(this);" value="admin" id="role" name="role" <?php if ($cont->role == 'master') {
                                                                                                                                            echo 'checked="checked"';
                                                                                                                                          } ?>>
                                                  <!-- </td> -->
                                                  <!--  <td style="padding-top:20px;padding-bottom:20px"> -->
                                                  <strong><?php echo _('\' ADMIN\''); ?></strong>
                                                </td>
                                                <td height="30" style="padding:20px; text-align: left;" class="wd_text">
                                                  <input type="radio" onChange="showSubAdmin(this);" value="super" id="role" name="role" <?php if ($cont->role == 'super') {
                                                                                                                                            echo 'checked="checked"';
                                                                                                                                          } ?>>
                                                  <!--    </td>
                 <td style="padding-top:20px;padding-bottom:20px"> -->
                                                  <strong><?php echo _('Activate As \'SUPER ADMIN\''); ?></strong>
                                                </td>

                                              </tr>


                                        </td>
                                      </tr>
                                    </tbody>
                                  </table>
                                </td>
                              </tr>
                              <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>
                                  <a href="<?php echo base_url() ?>mcp/companies/export_empty_recipes_xls/<?php echo $cont->id ?>" style="color: blue;"><?php echo _('Export empty recipes(xls)'); ?></a>
                                </td>
                                <td>&nbsp;</td>
                              </tr>
                              <tr>
                                <td valign="top" align="center" colspan="5">
                                  <table width="100%" cellspacing="0" cellpadding="0" border="0">
                                    <tbody>
                                      <tr>
                                        <td width="270">&nbsp;</td>
                                        <td valign="middle" height="60">
                                          <table width="100%" cellspacing="0" cellpadding="0" border="0">
                                            <tbody>
                                              <tr>
                                                <td width="31%" align="right">

                                                  <input type="submit" value="<?php echo _('UPDATE'); ?>" class="btnWhiteBack" id="btn_add_update" name="btn_add_update" />
                                                </td>
                                                <td style="padding-left:20px">

                                                  <input type="button" value="<?php echo _('DELETE THIS COMPANY AND ALL SETTINGS FROM DB'); ?>" class="btnWhiteBack" id="delete" name="delete" onclick="confirm_delete_this(<?php echo $cont->id; ?>);" />
                                                  <input type="hidden" value="add_edit" id="act" name="act">
                                                  <input type="hidden" value="" id="ID" name="ID">
                                                </td>
                                              </tr>

                                            </tbody>
                                          </table>
                                        </td>
                                        <td width="20%">&nbsp;</td>
                                      </tr>
                                    </tbody>
                                  </table>
                                </td>
                              </tr>
                            </tbody>
                          </table>
                      </td>
                    </tr>


                    <?php if ($cont->role == "super") {  ?>
                      <tr>
                        <td colspan="6" align="center">
                          <table width="100%" border="0" cellspacing="0" cellpadding="0" id="superadmin" <?php if ($cont->role != "super") : ?>style="display:none;" <?php endif; ?>>
                            <tr>
                              <td>
                                <table class="page_caption" style="background:url(<?php echo base_url(); ?>assets/mcp/images/bg.jpg) left top repeat-x; border:#CC0066; " border="0" cellspacing="0" width="100%" cellpadding="0">
                                  <tr>
                                    <td width="94%" align="left">
                                      <h3><?php echo _('Add') . '/' . _('Edit SubAdmins') ?></h3>
                                    </td>
                                    <td width="3%" align="right"><a class="sub" rel="open_ajax" href="<?php echo base_url() . 'mcp/companies/subadmin_add_edit/company_id/' . $cont->id; ?>">
                                        <div class="icon_button"> <img src="<?php echo base_url(); ?>assets/mcp/images/add.png" alt="Add Sub Admins" title="Add Sub Admins" width="16" height="16" border="0" style="cursor:pointer" /> </div>
                                      </a></td>
                                    <td width="3%" align="left">
                                      <div class="icon_button"> <img src="<?php echo base_url(); ?>assets/mcp/images/undo.jpg" alt="Go Back" title="Go Back" width="16" height="16" border="0" onClick="javascript:history.back();" style="cursor:pointer" /> </div>
                                    </td>
                                  </tr>
                                  <tr>
                                    <td colspan="3">&nbsp;</td>
                                  </tr>
                                  <tr>
                                    <td colspan="6">
                                      <style>
                                        .obs-table-responsive thead tr th:nth-child(1) {
                                          width: 110px !important;
                                        }

                                        .obs-table-responsive thead tr th {
                                          padding: 8px 10px;
                                          font-size: 14px;
                                        }

                                        .obs-table-responsive tbody tr td {
                                          padding: 0px 8px;
                                          word-break: break-word;
                                        }

                                        .obs-table-responsive thead tr th:nth-child(9) {
                                          width: 80px;
                                        }

                                        .obs-table-responsive thead tr th:nth-child(12) {
                                          width: 70px;
                                        }
                                      </style>
                                      <table class="obs-table-responsive" width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <thead>
                                          <tr bgcolor="#CCCCCC">
                                            <th><?php echo _('Company Name'); ?></th>
                                            <th><?php echo _('Contact Name'); ?></th>
                                            <th><?php echo _('Email'); ?></th>
                                            <th><?php echo _('Phone'); ?></th>
                                            <th><?php echo _('City'); ?></th>
                                            <th><?php echo _('Account Type'); ?></th>
                                            <th><?php echo _('Company Type'); ?></th>
                                            <th><?php echo _('HACCP Status'); ?></th>
                                            <th><?php echo _('EB Sett'); ?></th>
                                            <th><?php echo _('Show Menucard'); ?></th>
                                            <th><?php echo _('Status'); ?></th>
                                            <th><?php echo _('Actions'); ?></th>
                                          </tr>
                                        </thead>
                                        <?php if (count($cont->children) > 0) {
                                          for ($q = 0; $q < count($cont->children); $q++) {
                                            if ($cont->children[$q]->role == 'sub') {
                                        ?>
                                              <tr>
                                                <td><?= $cont->children[$q]->company_name ?></td>
                                                <td><?= $cont->children[$q]->first_name . " " . $cont->children[$q]->last_name; ?></td>
                                                <td><?= $cont->children[$q]->email; ?></td>
                                                <td><?= $cont->children[$q]->phone; ?></td>
                                                <td><?= $cont->children[$q]->city; ?></td>
                                                <td>
                                                  <select class="textbox" type="select" id="ac_type_id" name="selected_ac_type_id" onchange="change_acc_type_sub('<?php echo $cont->children[$q]->id; ?>',this)">
                                                    <?php if (!empty($account_types)) : ?>
                                                      <?php foreach ($account_types as $at) : ?>
                                                        <option value="<?php echo $at->id ?>" <?php if ($cont->children[$q]->ac_type_id == $at->id) {
                                                                                                echo 'selected="selected"';
                                                                                              } ?>><?php echo $at->ac_title ?>
                                                        </option>
                                                      <?php endforeach; ?>
                                                    <?php endif; ?>
                                                  </select>

                                                </td>
                                                <td class='select_type_id'>&nbsp;

                                                  <select class="textbox select_comp_type" type="select" id="sub_type_id" multiple onchange="company_type('<?php echo $cont->children[$q]->id; ?>',this)">
                                                    <option value="-1" style="background: none repeat scroll 0 0 #CCCCCC;">-- <?php echo _('Select Company Type'); ?> --</option>
                                                    <?php $company_types_sub = explode("#", $cont->children[$q]->type_id); ?>
                                                    <?php if (!empty($company_type)) {
                                                      foreach ($company_type as $ct) { ?>
                                                        <option value="<?php echo $ct->id ?>" <?php if (in_array($ct->id, $company_types_sub)) {
                                                                                                echo 'selected="selected"';
                                                                                              } ?>><?php echo $ct->company_type_name ?></option>
                                                    <?php }
                                                    } ?>
                                                  </select>

                                                </td>
                                                <td>
                                                  <select name="haccp_status" id="" onchange="change_haccp_status('<?php echo $cont->children[$q]->id; ?>',this)">
                                                    <option value='0' <?php if ($cont->children[$q]->haccp_status == 0) : ?>selected="selected" <?php endif; ?>>INACTIVE</option>
                                                    <option value="1" <?php if ($cont->children[$q]->haccp_status == 1) : ?>selected="selected" <?php endif; ?>>ACTIVE</option>
                                                  </select></td>
                                                <?php if (isset($cont->children[$q]->easybutler_status) && $cont->children[$q]->easybutler_status != '') {
                                                  $easy_stat = $cont->children[$q];
                                                } ?>
                                                <td>
                                                  <span>
                                                    <p><input type="checkbox" data-comp_id=<?php echo $cont->children[$q]->id; ?> class="activate_eb_sup" <?php if (isset($easy_stat->easybutler_status) && (json_decode($easy_stat->easybutler_status)->activate_easybutler == 1)) { ?> checked="checked" <?php } ?>><?php echo _('Act. EB'); ?></p>
                                                    <p><input type="checkbox" <?php if (isset($easy_stat->easybutler_status) && (json_decode($easy_stat->easybutler_status)->easybutler_order_app == 1)) { ?> checked="checked" <?php } ?> data-comp_id=<?php echo $cont->children[$q]->id; ?> class="activate_order_app_sup"><?php echo _('Ord. App'); ?></p>
                                                  </span>
                                                </td>

                                                <td>
                                                  <span>
                                                    <p><input type="checkbox" <?php if (isset($cont->children[$q]->show_menukartt) && !empty($cont->children[$q]->show_menukartt && $cont->children[$q]->show_menukartt == '1')) { ?> checked="checked" <?php } ?> data-comp_id=<?php echo $cont->children[$q]->id; ?> class="show_menucard"><?php echo _('Show Menucard'); ?></p>
                                                  </span>
                                                </td>
                                                <td>
                                                  <select name="status" id="status_<?php echo $cont->children[$q]->id; ?>" onchange="company_status('<?php echo $cont->children[$q]->id; ?>',this);">
                                                    <option value="0" <?php if ($cont->children[$q]->status == 0) : ?>selected="selected" <?php endif; ?>>INACTIVE</option>
                                                    <option value="1" <?php if ($cont->children[$q]->status == 1) : ?>selected="selected" <?php endif; ?>>ACTIVE</option>
                                                  </select>
                                                </td>
                                                <td>&nbsp;
                                                  <a class="" onclick="showDivision(<?= $cont->children[$q]->id; ?>,'<?= $cont->children[$q]->company_name ?>')"> <img src="<?php echo base_url(); ?>assets/mcp/images/add.png" alt="Add Division" title="Add Division" width="16" height="16" border="0" style="cursor:pointer" /></a>

                                                  <a class="sub" href="<?php echo base_url(); ?>mcp/companies/subadmin_add_edit/act/edit/company_id/<?= $cont->id; ?>/subid/<?= $cont->children[$q]->id; ?>"><img src="<?php echo base_url(); ?>assets/mcp/images/update.png" title="Update" width="16" height="16" border="0" style="cursor:pointer" /></a>&nbsp;&nbsp;<a href="<?php echo base_url(); ?>mcp/companies/delete/<?= $cont->children[$q]->id; ?>"><img src="<?php echo base_url(); ?>assets/mcp/images/delete1.png" title="Delete" width="16" height="16" border="0" style="cursor:pointer" /></a></td>
                                              </tr>

                                          <?php }
                                          }
                                        } else {
                                          ?>
                                          <tr>
                                            <td colspan="7" style="color:#FF0000">No Sub Admins added !!</td>
                                          </tr>
                                        <?php } ?>
                                      </table>
                                    </td>
                                  </tr>
                                </table>
                              </td>
                            </tr>
                          </table>
                        </td>
                      </tr>
                    <?php }
                          if ($cont->role == "master") {
                    ?>
                      <tr>
                        <td colspan="5" align="center">
                          <table width="100%" border="0" cellspacing="0" cellpadding="0" id="admin" <?php if ($cont->role != "master") : ?>style="display:none;" <?php endif; ?>>
                            <tr>
                              <td colspan="4" align="center">
                                <table width="100%" border="0" cellspacing="0" cellpadding="0" id="admin" <?php if ($cont->role != "master") : ?>style="display:none;" <?php endif; ?>>
                                  <tr>
                                    <td>
                                      <table class="page_caption obs-table-responsive" style="background:url(<?php echo base_url(); ?>assets/mcp/images/bg.jpg) left top repeat-x; border:#CC0066;" width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                          <td width="94%" align="left">
                                            <h3>Add/Edit Division</h3>
                                          </td>
                                          <td width="3%" align="right"> <a class="sub" rel="open_ajax" href="<?php echo base_url() . 'mcp/companies/division_add_edit/company_id/' . $cont->id; ?>">
                                              <div class="icon_button"> <img src="<?php echo base_url(); ?>assets/mcp/images/add.png" alt="Add Division" title="Add Division" width="16" height="16" border="0" style="cursor:pointer" /> </div>
                                            </a> </td>
                                          <td width="3%" align="left">
                                            <div class="icon_button"> <img src="<?php echo base_url(); ?>assets/mcp/images/undo.jpg" alt="Go Back" title="Go Back" width="16" height="16" border="0" onClick="javascript:history.back();" style="cursor:pointer" /> </div>
                                          </td>
                                        </tr>
                                        <tr>
                                          <td colspan="3">&nbsp;</td>
                                        </tr>
                                        <tr>
                                          <td colspan="3">
                                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                              <tr bgcolor="#CCCCCC">
                                                <th><?php echo _('Company Name'); ?></th>
                                                <th><?php echo _('Contact Name'); ?></th>
                                                <th><?php echo _('Email'); ?></th>
                                                <th><?php echo _('Phone'); ?></th>
                                                <th><?php echo _('City'); ?></th>
                                                <th><?php echo _('Account Type'); ?></th>
                                                <th><?php echo _('Company Type'); ?></th>
                                                <th><?php echo _('EB Sett'); ?></th>
                                                <th><?php echo _('Show Menucard'); ?></th>
                                                <th></th>
                                                <th><?php echo _('Status'); ?></th>
                                                <th><?php echo _('Actions'); ?></th>
                                              </tr>
                                              <?php if (($divisions)) {

                                                foreach ($divisions as $key => $division) {
                                              ?>
                                                  <tr>
                                                    <td width="150px" height="30"><?= $division->company_name ?></td>
                                                    <td width="175px" height="30"><?= $division->first_name . " " . $division->last_name; ?></td>
                                                    <td width="175px" height="30"><?= $division->email; ?></td>
                                                    <td width="130px" height="30"><?= $division->phone; ?></td>
                                                    <td width="120px" height="30"><?= $division->city; ?></td>

                                                    <td width="175px" height="30">
                                                      <select style="width:150px" class="textbox" type="select" id="ac_type_id" name="selected_ac_type_id" onchange="change_acc_type_sub('<?php echo $division->id; ?>',this)">

                                                        <?php if (!empty($account_types)) : ?>
                                                          <?php foreach ($account_types as $at) : ?>
                                                            <option value="<?php echo $at->id ?>" <?php if ($division->ac_type_id == $at->id) {
                                                                                                    echo 'selected="selected"';
                                                                                                  } ?>><?php echo $at->ac_title ?>
                                                            </option>
                                                          <?php endforeach; ?>
                                                        <?php endif; ?>
                                                      </select>

                                                    </td>

                                                    <td width="175px" class='select_type_id'>&nbsp;
                                                      <select style="width:190px" class="textbox select_comp_type" type="select" id="sub_type_id" multiple onchange="company_type('<?php echo $division->id; ?>',this)">
                                                        <option value="-1" style="background: none repeat scroll 0 0 #CCCCCC;">-- <?php echo _('Select Company Type'); ?> --</option>
                                                        <?php $company_types_sub = explode("#", $division->type_id); ?>
                                                        <?php if (!empty($company_type)) {
                                                          foreach ($company_type as $ct) { ?>
                                                            <option value="<?php echo $ct->id ?>" <?php if (in_array($ct->id, $company_types_sub)) {
                                                                                                    echo 'selected="selected"';
                                                                                                  } ?>><?php echo $ct->company_type_name ?></option>
                                                        <?php }
                                                        } ?>
                                                      </select>

                                                    </td>
                                                    <td style="padding-left:2px" width="150px">
                                                      <span>
                                                        <p><input type="checkbox" data-comp_id=<?php echo $division->id; ?> class="activate_eb_sup" <?php if (isset($division->easybutler_status) && (json_decode($division->easybutler_status)->activate_easybutler == 1)) { ?> checked="checked" <?php } ?>><?php echo _('Act. EB'); ?></p>
                                                        <p><input type="checkbox" <?php if (isset($division->easybutler_status) && (json_decode($division->easybutler_status)->easybutler_order_app == 1)) { ?> checked="checked" <?php } ?> data-comp_id=<?php echo $division->id; ?> class="activate_order_app_sup"><?php echo _('Ord. App'); ?></p>
                                                      </span>
                                                    </td>
                                                    <td style="padding-left:2px" width="150px">
                                                      <span>
                                                        <p><input type="checkbox" <?php if (isset($division->show_menukartt) && !empty($division->show_menukartt && $division->show_menukartt == '1')) { ?> checked="checked" <?php } ?> data-comp_id=<?php echo $division->id; ?> class="show_menukartt"><?php echo _('Show Menucard'); ?></p>
                                                      </span>
                                                    </td>
                                                    <td></td>

                                                    <td width="100px" height="30">
                                                      <select name="status" id="status_<?php echo $division->id; ?>" onchange="company_status('<?php echo $division->id; ?>',this);">
                                                        <option value="0" <?php if ($division->status == 0) : ?>selected="selected" <?php endif; ?>>INACTIVE</option>
                                                        <option value="1" <?php if ($division->status == 1) : ?>selected="selected" <?php endif; ?>>ACTIVE</option>
                                                      </select>
                                                    </td>
                                                    <td width="70px" height="30">&nbsp;<a class="sub" href="<?php echo base_url(); ?>mcp/companies/division_add_edit/act/edit/company_id/<?= $cont->id; ?>/division_id/<?= $division->id; ?>"><img src="<?php echo base_url(); ?>assets/mcp/images/update.png" title="Update" width="16" height="16" border="0" style="cursor:pointer" /></a>&nbsp;&nbsp;<a href="<?php echo base_url(); ?>mcp/companies/delete/<?= $division->id; ?>"><img src="<?php echo base_url(); ?>assets/mcp/images/delete1.png" title="Delete" width="16" height="16" border="0" style="cursor:pointer" /></a></td>
                                                  </tr>

                                                <?php  }
                                              } else {
                                                ?>
                                                <tr>
                                                  <td colspan="7" height="30" style="color:#FF0000">No Division added !!</td>
                                                </tr>
                                              <?php } ?>
                                            </table>
                                          </td>
                                        </tr>
                                      </table>
                                    </td>
                                  </tr>
                                </table>
                              </td>
                            </tr>
                          <?php

                          }
                          ?>
                          <tr>
                            <td colspan="6" align="center">
                              <table width="100%" class="division_table" border="0" cellspacing="0" cellpadding="0" id="" style="display:none;">
                                <tr>
                                  <td>&nbsp;</td>
                                </tr>
                                <tr>
                                  <td>&nbsp;</td>
                                </tr>
                                <tr style="background:url(<?php echo base_url(); ?>assets/mcp/images/bg.jpg) left top repeat-x;height: 40px">
                                  <td colspan="7">
                                    <h3 id="subadmin_name"></h3>
                                  </td>
                                </tr>
                                <tr>
                                  <td>&nbsp;</td>
                                </tr>
                                <tr>
                                  <td>
                                    <table class="" style="border: 1px solid #003366" width="100%" border="0" cellspacing="0" cellpadding="0">

                                      <tr>
                                        <td style="" class="whiteSmallBold" colspan="5" height="20" bgcolor="#003366" align="">Manage</td>
                                      </tr>

                                      <tr>
                                        <td>&nbsp;</td>
                                      </tr>

                                      <tr>
                                        <td>
                                          <table>
                                            <tbody>
                                              <tr style="background:url(<?php echo base_url(); ?>assets/mcp/images/bg.jpg) left top repeat-x;">
                                                <td width="100%" align="left">
                                                  <h3>Add/Edit Divisions</h3>
                                                </td>
                                                <td width="3%" align="right"> <a class="add_division_url" data-sub="<?= $cont->id; ?>" rel="open_ajax" href="<?php echo base_url() . 'mcp/companies/division_add_edit/company_id/' . $cont->id; ?>">
                                                    <div class="icon_button"> <img src="<?php echo base_url(); ?>assets/mcp/images/add.png" alt="Add Division" title="Add Division" width="16" height="16" border="0" style="cursor:pointer" /> </div>
                                                  </a> </td>
                                                <td width="3%" align="left">
                                                  <div class="icon_button"> <img src="<?php echo base_url(); ?>assets/mcp/images/undo.jpg" alt="Go Back" title="Go Back" width="16" height="16" border="0" onClick="javascript:history.back();" style="cursor:pointer" /> </div>
                                                </td>

                                              </tr>
                                            </tbody>
                                          </table>
                                        </td>

                                      </tr>
                                      <tr>
                                        <td colspan="3">&nbsp;</td>
                                      </tr>
                                      <tr>
                                        <td colspan="3">
                                          <table width="100%" class=" obs-table-responsive division_table_data" border="0" cellspacing="0" cellpadding="0">
                                            <thead>
                                              <tr bgcolor="#CCCCCC">
                                                <th><?php echo _('Company Name'); ?></th>
                                                <th><?php echo _('Contact Name'); ?></th>
                                                <th><?php echo _('Email'); ?></th>
                                                <th><?php echo _('Phone'); ?></th>
                                                <th><?php echo _('City'); ?></th>
                                                <th><?php echo _('Account Type'); ?></th>
                                                <th><?php echo _('Company Type'); ?></th>
                                                <th><?php echo _('EB Sett'); ?></th>
                                                <th><?php echo _('Show Menucard'); ?></th>
                                                <th></th>
                                                <th><?php echo _('Status'); ?></th>
                                                <th><?php echo _('Actions'); ?></th>
                                              </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                          </table>
                                        </td>
                                      </tr>
                                    </table>
                                  </td>
                                </tr>
                              </table>
                            </td>
                          </tr>
                  </tbody>
                </table>
                </form>
                <script type="text/javascript">
                  var frmValidator = new Validator("frm_companies_add_update");
                  frmValidator.EnableMsgsTogether();
                  frmValidator.addValidation("company_name", "req", "<?php echo _('Please enter the Company Name'); ?>");
                  frmValidator.addValidation("type_id", "dontselect=-1", "<?php echo _('Please enter the Company Type'); ?>");
                  frmValidator.addValidation("phone", "req", "<?php echo _('Please enter the Phone Number'); ?>");
                  frmValidator.addValidation("address", "req", "<?php echo _('Please enter the Address'); ?>");
                  frmValidator.addValidation("zipcode", "req", "<?php echo _('Please enter the Zipcode'); ?>");
                  frmValidator.addValidation("city", "req", "<?php echo _('Please enter the City'); ?>");
                  frmValidator.addValidation("country_id", "dontselect=-1", "<?php echo _('Please Select Country'); ?>");
                  frmValidator.addValidation("username", "req", "<?php echo _('Please enter Username'); ?>");
                  frmValidator.addValidation("password", "req", "<?php echo _('Please enter Password'); ?>");
                  frmValidator.addValidation("registration_date", "req", "<?php echo _('Please enter Date of Registration'); ?>");
                </script>

              <?php endif; ?>

              </td>
            </tr>

            <tr>
              <td style="padding:5px 0px 5px 0px" colspan="5">

                <table width="100%" cellspacing="0" cellpadding="0" border="0">
                  <tbody>
                    <?php if ($cont->role == 'super' || $cont->role == 'master') { ?>
                      <tr style="display: none">
                        <td width="50">&nbsp;</td>
                        <td align="center">
                          <br />
                          <hr>
                          <h3><?php echo _('Company\'s Webshop Footer Text'); ?></h3>
                          <hr>
                          <br />
                          <form method="post" action="<?php echo base_url(''); ?>mcp/companies/update/<?php echo $cont->id; ?>" id="footer_text_form" name="footer_text_form">

                            <input type="hidden" name="company_id" id="company_id" value="<?php echo $cont->id; ?>" />

                            <table border="0">
                              <tr>
                                <td class="wd_text"><?php echo _('Copyright Link Text'); ?> :</td>
                                <td><input type="text" name="company_footer_text" id="company_footer_text" value="<?php echo $cont->company_footer_text; ?>" size="80" class="textbox" /></td>
                                <td>&nbsp;</td>
                              </tr>

                              <tr>
                                <td height="30" align="center" colspan="3"></td>
                              </tr>

                              <tr>
                                <td class="wd_text"><?php echo _('Copyright Link URL'); ?> :</td>
                                <td><input type="text" name="company_footer_link" id="company_footer_link" value="<?php echo $cont->company_footer_link; ?>" size="80" class="textbox" /></td>
                                <td>OBS - Online bestelsysteem voor bakkers<br />http://www.onlinebestelsysteem.net</td>
                              </tr>

                              <tr>
                                <td class="wd_text"><?php echo _('Text Background Color'); ?> :</td>
                                <td colspan="2" style="vertical-align:middle;">
                                  <link rel="stylesheet" href="<?php echo base_url(''); ?>assets/mcp/new_js/colorpicker/css/colorpicker.css" type="text/css" />

                                  <script type="text/javascript" src="<?php echo base_url(''); ?>assets/mcp/new_js/colorpicker/js/colorpicker.js"></script>
                                  <script type="text/javascript" src="<?php echo base_url(''); ?>assets/mcp/new_js/colorpicker/js/eye.js"></script>
                                  <script type="text/javascript" src="<?php echo base_url(''); ?>assets/mcp/new_js/colorpicker/js/utils.js"></script>
                                  <script type="text/javascript" src="<?php echo base_url(''); ?>assets/mcp/new_js/colorpicker/js/layout.js?ver=1.0.2"></script>
                                  <script type="text/javascript">
                                    jQuery(document).ready(function($) {

                                      $('#colorSelector1').ColorPicker({
                                        color: '#<?php echo $cont->text_bg_color; ?>',
                                        onShow: function(colpkr) {
                                          $(colpkr).fadeIn(500);
                                          return false;
                                        },
                                        onHide: function(colpkr) {
                                          $(colpkr).fadeOut(500);
                                          return false;
                                        },
                                        onChange: function(hsb, hex, rgb) {
                                          $('#colorSelector1 div').css('backgroundColor', '#' + hex);
                                          $('#text_bg_color').val(hex);
                                        }
                                      });

                                      $('#colorSelector2').ColorPicker({
                                        color: '#<?php echo $cont->text_color; ?>',
                                        onShow: function(colpkr) {
                                          $(colpkr).fadeIn(500);
                                          return false;
                                        },
                                        onHide: function(colpkr) {
                                          $(colpkr).fadeOut(500);
                                          return false;
                                        },
                                        onChange: function(hsb, hex, rgb) {
                                          $('#colorSelector2 div').css('backgroundColor', '#' + hex);
                                          $('#text_color').val(hex);
                                        }
                                      });
                                    });
                                  </script>

                                  <input type="text" maxlength="6" size="6" id="text_bg_color" name="text_bg_color" value="<?php echo $cont->text_bg_color; ?>" style="float:left;margin-top:7px;" class="textbox" />

                                  <div id="colorSelector1" style="float:left;margin-left:5px;">
                                    <div style="background-color: #<?php echo $cont->text_bg_color; ?>;"></div>
                                  </div>

                                  <strong style="float:left;margin-top:7px;margin-left:10px;"><?php echo _('Text Color'); ?> :</strong>

                                  <input type="text" maxlength="6" size="6" id="text_color" name="text_color" value="<?php echo $cont->text_color; ?>" style="float:left;margin-top:7px;margin-left:5px;" class="textbox" />

                                  <div id="colorSelector2" style="float:left;margin-left:5px;">
                                    <div style="background-color: #<?php echo $cont->text_color; ?>"></div>
                                  </div>
                                  <div style="clear:both;"></div>
                                </td>
                              </tr>

                            </table>

                            <br /><br />
                            <input type="submit" name="save_text" id="save_text" value="<?php echo _('UPDATE'); ?>" class="btnWhiteBack" />
                          </form>

                        </td>
                        <td width="50">&nbsp;</td>
                      </tr>
                    <?php } ?>
                    <tr class="theme_tr">
                      <td width="50">&nbsp;</td>
                      <td align="center">
                        <br />
                        <hr>
                        <h3><?php echo _('THEME'); ?></h3>
                        <hr>
                        <br />
                        <form method="post" action="<?php echo base_url(''); ?>mcp/companies/update_theme/<?php echo $cont->id; ?>" id="footer_text_form" name="footer_text_form">
                          <input type="hidden" name="company_id" id="company_id" value="<?php echo $cont->id; ?>" />
                          <table border="0">
                            <tr>
                              <td class="wd_text"><?php echo _('Theme'); ?> :</td>
                              <td>
                                <select class="company_typetheme" name="theme">
                                  <option><?php echo _("Select"); ?></option>
                                  <option <?php if ((isset($cont->theme) &&  $cont->theme != '' && $cont->theme == '1') || (isset($theme_as_per_type_id) && $theme_as_per_type_id['theme'] == '1')) {
                                            echo "selected='selected'";
                                          } ?> value="1"><?php echo _("Retail"); ?></option>
                                  <option <?php if ((isset($cont->theme) &&  $cont->theme != '' && $cont->theme == '2') || (isset($theme_as_per_type_id) && $theme_as_per_type_id['theme'] == '2')) {
                                            echo "selected='selected'";
                                          } ?> value="2"><?php echo _("Catering"); ?></option>
                                  <option <?php if ((isset($cont->theme) &&  $cont->theme != '' && $cont->theme == '3') || (isset($theme_as_per_type_id) && $theme_as_per_type_id['theme'] == '3')) {
                                            echo "selected='selected'";
                                          } ?> value="3"><?php echo _("Medical"); ?></option>
                                  <option <?php if ((isset($cont->theme) &&  $cont->theme != '' && $cont->theme == '4') || (isset($theme_as_per_type_id) && $theme_as_per_type_id['theme'] == '4')) {
                                            echo "selected='selected'";
                                          } ?> value="4"><?php echo _("Horeca"); ?></option>

                                  <option <?php if ((isset($cont->theme) &&  $cont->theme != '' && $cont->theme == '5') || (isset($theme_as_per_type_id) && $theme_as_per_type_id['theme'] == '5')) {
                                            echo "selected='selected'";
                                          } ?> value="5"><?php echo _("SHO"); ?></option>

                                  <option <?php if ((isset($cont->theme) &&  $cont->theme != '' && $cont->theme == '6') || (isset($theme_as_per_type_id) && $theme_as_per_type_id['theme'] == '6')) {
                                            echo "selected='selected'";
                                          } ?> value="6"><?php echo _("Buurtsuper"); ?></option>
                                </select>
                              </td>
                              <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                              <td> <input type="submit" name="save_text" id="save_text" value="<?php echo _('CHANGE'); ?>" class="btnWhiteBack" /></td>
                            </tr>
                            <tr></tr>
                            <tr></tr>
                            <tr>
                              <td class="wd_text"><?php echo _('SHO-lead'); ?> :</td>
                              <td>
                                <input type="checkbox" class="show_lead" value="1" <?php if ($cont->show_sho_leads == '1') echo 'checked="checked"' ?>>
                              </td>
                            </tr>

                            <tr>
                              <td height="30" align="center" colspan="3"></td>
                            </tr>
                          </table>
                          <br />
                        </form>
                      </td>
                      <td width="50">&nbsp;</td>
                    </tr>

                    <tr>
                      <td width="50">&nbsp;</td>
                      <td>
                        <table width="100%" cellspacing="0" cellpadding="0" border="0">
                          <tbody>
                            <tr>
                              <td height="30" align="center" colspan="2">
                                <br /><br />
                                <hr>
                                <h3><?php echo _('Front End API Details'); ?></h3>
                                <hr>
                                <br />
                              </td>
                            </tr>
                            <?php  ?>
                            <?php if (!empty($api) && $api->api_id) { ?>
                              <tr>
                                <td height="50" class="wd_text" width="600"><?php echo _('API ID'); ?> : </td>
                                <td><?php echo $api->api_id; ?></td>
                              </tr>
                              <tr>
                                <td height="50" class="wd_text" width="600"><?php echo _('API Secret Key'); ?> : </td>
                                <td><?php echo $api->api_secret; ?></td>
                              </tr>
                              <tr>
                                <td height="50" class="wd_text" width="600"><?php echo _('API Domain'); ?> : </td>
                                <td><?php echo $api->domain; ?></td>
                              </tr>
                              <tr>
                                <td height="50" class="wd_text" width="600"><?php echo _('Download Files'); ?> : (<?php echo _('NEW Api Files'); ?>)</td>
                                <td>
                                  <a href="<?php echo base_url(''); ?>download.php?f=online-bestellen.zip">Online-Bestel(Zip)</a>
                                  <br>
                                  <a href="<?php echo base_url(''); ?>download.php?f=online-bestellen.rar">Online-Bestel(Rar)</a>
                                </td>
                              </tr>
                            <?php } else { ?>
                              <tr>
                                <td height="50" class="wd_text" colspan="2" style="text-align:center;">
                                  <?php echo _('No API issued to this company yet !'); ?>
                                </td>
                              </tr>
                            <?php } ?>
                          </tbody>
                        </table>
                      </td>
                      <td width="50">&nbsp;</td>
                    </tr>
                    <!-- ----------------------------<<<<<<<< START ACTIVATE ADDONS >>>>>>>>----------------------------- -->
                    <tr>
                      <td width="50">&nbsp;</td>
                      <td>
                        <form action="" method="POST">
                          <span height="30" align="center" colspan="2" style="display: block;">
                            <br /><br />
                            <hr>
                            <h3 style="text-align: center;"><?php echo _('Activate Addons'); ?></h3>
                            <hr>
                            <br />
                          </span>
                          <table width="100%" cellspacing="0" cellpadding="0" border="0">
                            <tbody>
                              <tr>
                                <td height="30" class="wd_text" width="600"><?php echo _('Show on Allergenchecker.net'); ?></td>
                                <td>
                                  <input type="checkbox" value="1" class="textbox" id="hide_bp_intro" name="hide_bp_intro" <?php if ($labeler_settings['0']->hide_bp_intro == 1) {
                                                                                                                              echo 'checked="checked"';
                                                                                                                            } ?> <?php if ((in_array('20', $company_types) || in_array('27', $company_types) || in_array('28', $company_types))) { ?> disabled="disabled" <?php } ?>>
                                  <input type="hidden" name="hiddenfield" value=<?php if ($labeler_settings['0']->hide_bp_intro == 1) {
                                                                                  echo '1';
                                                                                } else {
                                                                                  echo '0';
                                                                                } ?>>

                                </td>
                                <td class="move_rit"><i><?php echo _('(hidden for 20-27-28 / search results hidden for 1,3,8,9,12,13,23,24,25,26 / 20%)'); ?></i></td>
                              </tr>

                              <tr class="act_easy">
                                <td height="30" class="wd_text" width="600"><?php echo _('EasyBUTLER'); ?></td>
                                <td>
                                  <input type="checkbox" size="10" class="textbox" id="activate_easybutler" name="activate_easybutler" value="1" <?php if (isset($cont->easybutler_status) && !empty($cont->easybutler_status) && (json_decode($cont->easybutler_status)->activate_easybutler == 1)) { ?> checked="checked" <?php } ?> <?php if ($cont->ac_type_id == '1') { ?> disabled="disabled" <?php } ?>>
                                </td>
                                <td class="move_rit"><i><?php echo _('(Activated by default for all types besides 20-27 and FDD FREE)'); ?></i></td>
                              </tr>

                              <tr class="order_app">
                                <td height="30" class="wd_text" width="600"><?php echo _('EasyBUTLER Order App '); ?></td>
                                <td>
                                  <input type="checkbox" size="10" class="textbox" id="easybutler_order_app" name="easybutler_order_app" value="1" <?php if (isset($cont->easybutler_status) && !empty($cont->easybutler_status) && (json_decode($cont->easybutler_status)->easybutler_order_app == 1)) { ?> checked="checked" <?php } ?> <?php if ($cont->ac_type_id == '1') { ?> disabled="disabled" <?php } ?>>
                                </td>
                              </tr>

                              <tr class="show_menukartt">
                                <td height="30" class="wd_text" width="600"><?php echo _('Show Menucard'); ?></td>
                                <td>
                                  <input type="checkbox" size="10" class="textbox" id="show_menukartt" name="show_menukartt" value="1" <?php if (isset($cont->show_menukartt) && !empty($cont->show_menukartt && $cont->show_menukartt == '1')) { ?> checked="checked" <?php } ?> <?php if ($cont->ac_type_id == '1') { ?> disabled="disabled" <?php } ?>>
                                </td>
                                <td class="move_rit"><i><?php echo _('(Only requested by client manually)'); ?></i></td>
                              </tr>

                              <tr class="show_menukartt_maker">
                                <td height="30" class="wd_text" width="600"><?php echo _('Show Menucard Maker'); ?></td>
                                <td>
                                  <input type="checkbox" size="10" class="textbox" id="show_menukartt_maker" name="show_menukartt_maker" value="1" <?php if (isset($cont->show_menukartt_maker) && !empty($cont->show_menukartt_maker && $cont->show_menukartt_maker == 1)) { ?> checked="checked" <?php } ?> <?php if ($cont->ac_type_id == '1') { ?> disabled="disabled" <?php } ?>>
                                </td>
                                <td class="move_rit"><i><?php echo _('(Activated by default for all types besides 1,3,8,9,12,13,23,24,25,26)'); ?></i></td>
                              </tr>

                              <tr class="show_infodesk">
                                <td height="30" class="wd_text" width="600"><?php echo _('Show Infodesk'); ?></td>
                                <td>
                                  <input type="checkbox" size="10" class="textbox" id="show_infodesk" name="show_infodesk" value="1" <?php if (isset($cont->show_infodesk) && !empty($cont->show_infodesk && $cont->show_infodesk == '1')) { ?> checked="checked" <?php } ?>>
                                </td>
                                <td class="move_rit"><i><?php echo _('(Activated by default for type 20-27-28)'); ?></i></td>
                              </tr>

                              <tr class="show_week_menu">
                                <td height="30" class="wd_text" width="600"><?php echo _('Show Weekmenu'); ?></td>
                                <td>
                                  <input type="checkbox" size="10" class="textbox" id="show_week_menu" name="show_week_menu" value="1" <?php if (isset($cont->show_week_menu) && !empty($cont->show_week_menu && $cont->show_week_menu == '1')) { ?> checked="checked" <?php } ?>>
                                </td>
                                <td class="move_rit"><i><?php echo _('(Activated by default for type 20-27-28)'); ?></i></td>
                              </tr>

                              <tr class="style_haccp">
                                <td height="30" class="wd_text" width="600"><?php echo _('HACCP'); ?></td>
                                <td>
                                  <input type="checkbox" size="10" class="textbox" id="haccp_status" name="haccp_addon" value="1" <?php if ($cont->haccp_addon == 1) { ?> checked="checked" <?php } ?> <?php if ($cont->ac_type_id == '1') { ?> disabled="disabled" <?php } ?>>

                                <td class="move_rit_light" style="padding: 28px;">
                                  <span>
                                    <?php echo _('LIGHT'); ?><input type="radio" size="10" class="radio" name="easing_measure" value="1" <?php if ($labeler_settings['0']->easing_measure == 1) { ?> checked="checked" <?php } ?>>
                                  </span>
                                  <span>
                                    <?php echo _('PRO'); ?>
                                    <input type="radio" size="10" class="radio" name="easing_measure" value="0" <?php if ($labeler_settings['0']->easing_measure == 0) { ?> checked="checked" <?php } ?>>
                                  </span>
                                </td>


                                <!-- <b class="move_rit_light"><?php echo _('HACCP LIGHT'); ?></b><b class="move_rit_light">
               <input type="checkbox" size="10" class="textbox" name="easing_measure" value="1" <?php if ($labeler_settings['0']->easing_measure == 1) { ?> checked="checked"<?php } ?>>
             </b> -->
                      </td>
                    </tr>
                <form name="frm">
                    <tr style="vertical-align: top;" class="fdd-more-info-table">
                        <td height="30" class="wd_text" width="600"><?php echo _('Production');?>
                        <td style="width: 20px;">
                        <?php 
                        $cost_price_status = explode('#',$content[0]->cost_price_status);  ?>
                        <input type="checkbox" name="cost_price_status[]" value="1" size="10" class="textbox_select_all" id="Kitchenmgmt" <?php if (in_array("1", $cost_price_status)){ ?> checked="checked"<?php }?>>
                        </td>
                        </td>
                        <td style="padding: 0px 20px;">
                        <table>
                        <tr>
                        <td class="container" style="float: inline-end;"><?php echo _('Activate "Customers"');?>
                        <input type="checkbox" name="cost_price_status[]" value="6" size="10" class="chk_cost_price_status_parameter" <?php if (in_array("6", $cost_price_status)){ ?> checked="checked"<?php }?>>
                        </td>
                        </tr>
                        <tr>
                            <td class="container" style="float: inline-end;"><?php echo _('Stock');?> 
                                <input type="checkbox"  name="cost_price_status[]" value="5" size="10" class="chk_cost_price_status_parameter" <?php if (in_array("5", $cost_price_status)){ ?> checked="checked"<?php }?>> 
                            </td>
                        </tr>
                        <tr>
                        <td class="container" style="float: inline-end;"><?php echo _('Tracing');?> 
                        <input type="checkbox" name="cost_price_status[]" value="7" size="10" class="chk_cost_price_status_parameter" <?php if (in_array("7", $cost_price_status)){ ?> checked="checked"<?php }?>>
                        </td>
                        </tr>

                        <tr>
                        <td class="container" style="float: inline-end;"><?php echo _('Foodcost');?>
                        <input type="checkbox" name="cost_price_status[]" value="8" size="10" class="chk_cost_price_status_parameter" <?php if (in_array("8", $cost_price_status)){ ?> checked="checked"<?php }?>>
                        </td>
                        </tr>
                        <tr>
                        <td class="container" style="float: inline-end;"><?php echo _('Costprice');?>
                        <input type="checkbox" name="cost_price_status[]" value="4" size="10" class="chk_cost_price_status_parameter" <?php if (in_array("4", $cost_price_status)){ ?> checked="checked"<?php }?>>
                        </td>
                        </tr>
                        </table>

                        </td>
                        </tr>
                </form>
                <!-- //For production api  start  -->
                <tr class="enable_production_api">
                       <td height="30" class="wd_text" width="600"><?php echo _('Show Ids for production API'); ?></td>
                        <td>
                       <input type="checkbox" size="10" class="textbox" id="enable_production
                       _api" name="enable_production_api" value="1" <?php (isset($labeler_settings[0]->api_enable) && ($labeler_settings[0]->api_enable == 1)) ?print 'checked': '' ?>/>
                      </td>
                      <td class="move_rit"><i><?php echo _('(Activate for those who will use production API)'); ?></i></td>
                      
                </tr>
                <!-- //For production api  end -->
                    
            <tr>
              <td height="50" class="wd_text" colspan="3" style="text-align: center;">
                <input type="hidden" name="addon_company_id" id="addon_company_id" value="<?php echo $labeler_settings['0']->company_id; ?>" />
                <input type="submit" name="update_addon_cost" id="update_addon_cost" value="<?php echo _('UPDATE'); ?>" class="btnWhiteBack" />
              </td>
            </tr>
          </tbody>
        </table>
        </form>
      </td>
      <td width="50">&nbsp;</td>
    </tr>
    <!-- ----------------------------<<<<<<<< END ACTIVATE ADDONS >>>>>>>>>------------------------------- -->
    <tr>
      <td width="50">&nbsp;</td>
      <td>
        <table width="100%" cellspacing="0" cellpadding="10" border="0">
          <tbody>
            <tr>
              <td height="30" align="center" colspan="2">
                <br /><br />
                <hr>
                <h3><?php echo _('BLUECHERRY DATALOGGERS'); ?></h3>
                <hr>
                <br />
              </td>
            </tr>
            <?php
            if (!empty($datalogger) &&  $datalogger->bluecherry_activation == 1) { ?>
              <td>
                <div align="center">
                  <p><?php echo _('Activation Sent'); ?> : </p>
                  <input type="button" id="bluecherry_reg" name="bluecherry_reg" value="<?php echo _('Re-Register'); ?>" class="btnWhiteBack" />
                </div>
              </td>


            <?php
            } else if (!empty($datalogger) &&   $datalogger->bluecherry_activation == 2) { ?>
              <td>
                <div align="center">
                  <p><?php echo _('Activated'); ?> </p>
                </div>
              </td>
            <?php
            } else { ?>
              <td>
                <div align="center">
                  <p><?php echo _('No dataloggers registered'); ?> :</p>
                  <input type="button" name="bluecherry_reg" id="bluecherry_reg" value="<?php echo _('Register'); ?>" class="btnWhiteBack" />
                </div>
              </td>
            <?php
            }
            ?>

          </tbody>
        </table>
      </td>
    </tr>
    <tr>
      <td width="50">&nbsp;</td>
      <td>
        <table width="100%" cellspacing="0" cellpadding="10" border="0">
          <tbody>
            <tr>
              <td height="30" align="center" colspan="2">
                <br /><br />
                <hr>
                 <h3><?php echo _('FAVOURITE LIST'); ?></h3>
                <hr>
                <br />
              </td>
            </tr>
            <tr>
              <td colspan="2" align="center">
                <?php echo _('Show Multiple Favourite List'); ?>
                <span>
                  <input type="checkbox" size="10" value="1" class="textbox" id="show_fav_list" name="show_fav_list" <?php if (isset($cont->show_fav_list) && $cont->show_fav_list == 1) { ?>checked="checked" <?php } ?>>
                </span>
              </td>
            </tr>
            <?php if ($cont->fav_list != '' && !empty(json_decode($cont->fav_list))) {
              $fav_list = (array) json_decode($cont->fav_list);
              foreach ($fav_list as $list) { ?>
                <tr class="additional_fav_input">
                  <td colspan="2" align="center">
                      <input type="text" size="30" class="textbox" name="additional_fav_list[]" value="<?php echo $list; ?>" >
                      <span><input class="add_fav_list" value="+" style="border-radius: 0;height: 21px;width: 21px;" type="button"></span>
                      <span><input class="remove_fav_list" value="-" style="border-radius: 0;height: 21px;width: 21px;" type="button"></span>
                    </td>
                </tr>
            <?php 
              }
            }else{?>
                <tr class="additional_fav_input">
                  <td colspan="2" align="center">
                  <input type="text" size="30" class="textbox" name="additional_fav_list[]" value="" >
                  <span><input class="add_fav_list" value="+" style="border-radius: 0;height: 21px;width: 21px;" type="button"></span>
                  <span><input class="remove_fav_list" value="-" style="border-radius: 0;height: 21px;width: 21px;" type="button"></span>
                </td>
                </tr>
            <?php } ?>
            <tr>
              <td colspan="2" align="center">
                <input type="button" name="save_fav_setting" id="save_fav_setting" value="<?php echo _('UPDATE'); ?>" class="btnWhiteBack" />
              </td>
            </tr>
            </tbody>
          </table>
        </td>
      </tr>

    <?php if ($cont->role == 'super' || $cont->role == 'master') { ?>
      <tr>
        <td width="50">&nbsp;</td>
        <td>
          <form method="post" action="<?php echo base_url(); ?>mcp/companies/update/<?php echo $cont->id; ?>">
            <input type="hidden" name="p_company_id" id="p_company_id" value="<?php echo $cont->id; ?>" />
            <table width="100%" cellspacing="0" cellpadding="10" border="0">
              <tbody>
                <tr>
                  <td height="30" align="center" colspan="2">
                    <br /><br />
                    <hr>
                    <h3><?php echo _('Partner Settings'); ?></h3>
                    <hr>
                    <br />
                  </td>
                </tr>

                <?php if (!empty($partners)) {
                  $partner_ids = array();
                  if ($cont->partner_id != '') {
                    $partner_ids = json_decode($cont->partner_id, true);
                  } ?>
                  <tr class="gh">
                    <th align="right" width="600px padding-bottom:10px"><?php echo _('Assign to Partner'); ?> :</th>
                    <td align="left">
                      <select name="partner_id[]" id="partner_id" class="textbox" multiple="">
                        <option value="0"> -- <?php echo _('Select Partner'); ?> -- </option>
                        <?php foreach ($partners as $p) { ?>
                          <option value="<?php echo $p->id; ?>" <?php if (in_array($p->id, $partner_ids)) {
                                                                  echo 'selected="selected"';
                                                                } ?>>
                            <?php echo $p->p_first_name . ' ' . $p->p_last_name; ?>
                          </option>
                        <?php } ?>
                      </select>
                    </td>
                  </tr>
                  <!-- <tr>
                  <th align="right"><?php echo _('Status'); ?> :</th>
                <td align="left">
                   <select name="partner_status" id="partner_status">
                    <option value="0" <?php if ($cont->partner_status == 0) {
                                        echo 'selected="selected"';
                                      } ?>><?php echo _('UNPAID'); ?></option>
                    <option value="1" <?php if ($cont->partner_status == 1) {
                                        echo 'selected="selected"';
                                      } ?>><?php echo _('PAID'); ?></option>
                 </select>
                </td>
              </tr> -->
                  <!-- ************************************************* -->
                  <!-- <tr>
                  <th align="right" width="600px"><?php echo _('Total amount'); ?> :</th>
                <td align="left">
                   <input name="total_amount" id="total_amount" type="text" class="textbox" size="10" value="<?php echo $cont->partner_total_amount; ?>" />&nbsp;&nbsp;&euro;
                </td>
              </tr> -->
                  <!-- <tr>
                <th align="right"><?php echo _('Total commission'); ?> :</th>
                <td align="left">
                   <input name="total_commission" id="total_commission" type="text" class="textbox" size="10" value="<?php echo isset($cont->partner_total_commission) && ($cont->partner_total_commission > 0) ? $cont->partner_total_commission : ($cont->partner_total_amount / 3); ?>" />&nbsp;&nbsp;&euro;/mnth 
                   <input name="total_commission" id="total_commission" type="text" class="textbox" size="10" value="<?php echo isset($cont->partner_total_commission) ? (float) $cont->partner_total_commission : '0'; ?>" />&nbsp;&nbsp;&euro;/mnth
                </td>
              </tr> -->

                  <tr>
                    <th align="right"><?php echo _('Start date'); ?> :</th>
                    <td align="left">
                      <input name="invoive_date" id="invoive_date" type="text" class="textbox" size="10" value="<?php echo $cont->partner_invoice_date; ?>" /><img border="0" src="<?php echo base_url(); ?>assets/mcp/images/cal.jpeg" width="30" height="30" name="date_picker1" id="date_picker1" style="vertical-align:bottom">

                      <script type="text/javascript">
                        var cal = Calendar.setup({
                          onSelect: function(cal) {
                            cal.hide();
                            var start_date = cal.selection.sel[0];
                            console.log(typeof start_date);
                            var year = start_date.toString().substring(4, 0);
                            var month = start_date.toString().substring(6, 4);
                            var day = start_date.toString().substring(8, 6);
                            jQuery('#invoive_end_date').val((parseInt(year, 10) + 3) + '-' + month + '-' + day);
                          }
                        });
                        cal.manageFields("date_picker1", "invoive_date", "%Y-%m-%d");
                      </script>
                    </td>
                  </tr>
                  <tr>
                    <th align="right"><?php echo _('End date'); ?> :</th>
                    <td align="left">
                      <input name="invoive_end_date" id="invoive_end_date" type="text" class="textbox" size="10" value="<?php echo $cont->invoice_end_date; ?>" /><img border="0" src="<?php echo base_url(); ?>assets/mcp/images/cal.jpeg" width="30" height="30" name="date_picker2" id="date_picker2" style="vertical-align:bottom">

                      <script type="text/javascript">
                        var cal2 = Calendar.setup({
                          onSelect: function(cal2) {
                            cal2.hide()
                          }
                        });
                        cal2.manageFields("date_picker2", "invoive_end_date", "%Y-%m-%d");
                      </script>
                    </td>
                  </tr>
                  <tr>
                    <th align="right"><?php echo _('Message'); ?> :</th>
                    <td align="left">
                      <textarea cols="50" rows="5" class="textbox" type="textarea" id="message" name="message"><?php echo $cont->partner_message ?></textarea>
                    </td>
                  </tr>
                  <!-- ************************************************* -->
                  <tr>
                    <td colspan="2" align="center">
                      <input type="submit" name="assign_partner" id="assign_partner" value="<?php echo _('ASSIGN PARTNER'); ?>" class="btnWhiteBack" />
                    </td>
                  </tr>
                <?php } else { ?>
                  <tr>
                    <td colspan="2" style="color:red; font-weight:bold;">
                      <?php echo _('No Partner Added !!!'); ?>
                    </td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </form>
        </td>
        <td width="50">&nbsp;</td>
      </tr>
    <?php } ?>

    <?php if ($cont->role == 'super' || $cont->role == 'master') { ?>
      <tr>
        <td width="50">&nbsp;</td>
        <td>
          <form method="post" action="<?php echo base_url(); ?>mcp/companies/update/<?php echo $cont->id; ?>">
            <input type="hidden" name="a_company_id" id="a_company_id" value="<?php echo $cont->id; ?>" />
            <table width="100%" cellspacing="0" cellpadding="10" border="0">
              <tbody>
                <tr>
                  <td height="30" align="center" colspan="2">
                    <br /><br />
                    <hr>
                    <h3><?php echo _('Affiliate Settings'); ?></h3>
                    <hr>
                    <br />
                  </td>
                </tr>

                <?php if (!empty($affiliates)) { ?>
                  <tr>
                    <th align="right"><?php echo _('Assign to Affiliate'); ?> :</th>
                    <td align="left">
                      <select name="affiliate_id" id="affiliate_id">
                        <option value="0"> -- <?php echo _('Select Affiliate'); ?> -- </option>
                        <?php foreach ($affiliates as $a) { ?>
                          <option value="<?php echo $a->id; ?>" <?php if ($cont->affiliate_id == $a->id) {
                                                                  echo 'selected="selected"';
                                                                } ?>>
                            <?php echo $a->a_first_name . ' ' . $a->a_last_name; ?>
                          </option>
                        <?php } ?>
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <th align="right"><?php echo _('Status'); ?> :</th>
                    <td align="left">
                      <select name="affiliate_status" id="affiliate_status">
                        <option value="0" <?php if ($cont->affiliate_status == 0) {
                                            echo 'selected="selected"';
                                          } ?>><?php echo _('UNPAID'); ?></option>
                        <option value="1" <?php if ($cont->affiliate_status == 1) {
                                            echo 'selected="selected"';
                                          } ?>><?php echo _('PAID'); ?></option>
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <td colspan="2" align="center">
                      <input type="submit" name="assign_affiliate" id="assign_affiliate" value="<?php echo _('ASSIGN AFFILIATE'); ?>" class="btnWhiteBack" />
                    </td>
                  </tr>
                <?php } else { ?>
                  <tr>
                    <td colspan="2" style="color:red; font-weight:bold;">
                      <?php echo _('No Affiliate Added !!!'); ?>
                    </td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </form>
        </td>
        <td width="50">&nbsp;</td>
      </tr>
    <?php } ?>

    <?php if ($cont->role == 'super' || $cont->role == 'master') { ?>
      <tr>
        <td width="50">&nbsp;</td>
        <td>
          <form method="post" action="<?php echo base_url(); ?>mcp/companies/update/<?php echo $cont->id; ?>" enctype="multipart/form-data">
            <table width="100%" cellspacing="0" cellpadding="10" border="0">
              <tbody>
                <tr>
                  <td height="30" align="center" colspan="2">
                    <br />
                    <br />
                    <hr>
                    <h3><?php echo _('Sheet Banner Setting'); ?></h3>
                    <hr>
                    <br />
                  </td>
                </tr>
                <tr>
                  <th align="right"><?php echo _('Sheet Banner'); ?> :</th>
                  <td align="left">
                    <!-- <input type="file" name="sht_banner"/> -->
                    <a href="javascript:;" class="thickboxed_label" attr_id="8" attr_width="1920" style="text-decoration: none;"><input type="button" name="upload_img" id="upload_img" value="<?php echo _("Banner upload"); ?>" /></a>

                    <?php if ($labeler_settings && !empty($labeler_settings[0]->sheet_banner)) { ?>
                      <a href="javascript:;" onclick="delete_banner('<?php echo $labeler_settings[0]->sheet_banner; ?>', '<?php echo $labeler_settings[0]->company_id; ?>', 'sheet_banner');"><input type="button" id="delete_sheet_banner" value="<?php echo _("Delete"); ?>" /></a>
                    <?php } ?>

                  </td>
                <tr>
                  <td>&nbsp;</td>
                  <td>
                    <div id="uploaded_image8"></div>
                    <?php if ($labeler_settings && !empty($labeler_settings[0]->sheet_banner)) { ?>
                      <div class="hide_sheet_banner_div" style=" margin: 5px;">

                        <img class="hide_sheet_banner" style="z-index:-1; position: relative" src="<?php echo base_url() . 'assets/mcp/images/sheet_banner/' . $labeler_settings[0]->sheet_banner; ?>">
                      </div>
                    <?php } ?>
                    <input type="hidden" id="x" name="x" />
                    <input type="hidden" id="y" name="y" />
                    <input type="hidden" id="w" name="w" />
                    <input type="hidden" id="h" name="h" />
                  </td>
                </tr>
                <tr>
                  <td colspan="2" align="center">
                    <input type="submit" name="banner_upd" value="<?php echo _('Upload Sheet Banner'); ?>" class="btnWhiteBack" />
                  </td>
                </tr>
              </tbody>
            </table>
          </form>
        </td>
        <td width="50">&nbsp;</td>
      </tr>
      <tr>
        <td width="50">&nbsp;</td>
        <td>
          <form method="post" action="<?php echo base_url(); ?>mcp/companies/aller_upload_banner/<?php echo $cont->id; ?>" enctype="multipart/form-data">
            <table width="100%" cellspacing="0" cellpadding="10" border="0">
              <tbody>
                <tr>
                  <td height="30" align="center" colspan="2">
                    <br />
                    <br />
                    <hr>
                    <h3><?php echo _('Allergenchecker.net'); ?></h3>
                    <hr>
                    <br />
                  </td>
                </tr>
                <tr>
                  <th align="right"><?php echo _('Company Banner'); ?> :</th>
                  <td align="left">
                    <a href="javascript:;" class="thickboxed_label" attr_id="5" attr_width="1920" style="text-decoration: none;"><input type="button" name="upload_img" id="upload_img" value="<?php echo _("Banner upload"); ?>" /></a>
                    <?php if ($labeler_settings && !empty($labeler_settings[0]->aller_banner_sheet)) { ?>
                      <a href="javascript:;" onclick="delete_banner('<?php echo $labeler_settings[0]->aller_banner_sheet; ?>', '<?php echo $labeler_settings[0]->company_id; ?>', 'aller_banner_sheet');"><input type="button" id="delete_aller_banner" value="<?php echo _("Delete"); ?>" /></a>
                    <?php } ?>
                  </td>
                </tr>

                <tr>
                  <td colspan="2">
                    <div id="uploaded_image5"></div>
                    <?php if ($labeler_settings && !empty($labeler_settings[0]->aller_banner_sheet)) { ?>
                      <div class="hide_aller_banner_div" style=" margin: 5px; width:800px; height:300px; background: linear-gradient( rgba(0, 0, 0, <?php echo $labeler_settings[0]->transparency; ?> ), rgba(0, 0, 0, <?php echo $labeler_settings[0]->transparency; ?> ))">

                        <img class="hide_aller_banner" style="z-index:-1; position: relative" width="800px" height="300px" src="<?php echo base_url() . 'assets/aller_checker_banner/' . $labeler_settings[0]->aller_banner_sheet; ?>">
                      </div>
                    <?php } ?>
                    <input type="hidden" id="x" name="x" />
                    <input type="hidden" id="y" name="y" />
                    <input type="hidden" id="w" name="w" />
                    <input type="hidden" id="h" name="h" />
                  </td>
                </tr>


                <tr>
                  <td colspan="2" align="center">
                    <input type="submit" name="banner_upd" value="<?php echo _('Upload Company Banner'); ?>" class="btnWhiteBack" />
                  </td>
                </tr>
              </tbody>
            </table>
          </form>
        </td>
        <td width="50">&nbsp;</td>
      </tr>
      <tr>
        <td width="50">&nbsp;</td>
        <td>
          <form method="post" action="<?php echo base_url(); ?>mcp/companies/aller_upload_image/<?php echo $cont->id; ?>" enctype="multipart/form-data">
            <table width="100%" cellspacing="0" cellpadding="10" border="0">
              <tbody>
                <tr>
                  <td height="30" align="center" colspan="2">
                    <br />
                    <br />
                    <hr>
                    <h3><?php echo _('Image upload'); ?></h3>
                    <hr>
                    <br />
                  </td>
                </tr>
                <tr>
                  <th align="right"><?php echo _('Company Profile'); ?> :</th>
                  <td align="left">
                    <a href="javascript:;" class="thickboxed_label" attr_id="7" attr_width="250" style="text-decoration: none;"><input type="button" name="upload_img" id="upload_img" value="<?php echo _("Image upload"); ?>" /></a>
                    <?php if ($labeler_settings && !empty($labeler_settings[0]->aller_upload_image)) { ?>
                      <a href="javascript:;" onclick="delete_banner('<?php echo $labeler_settings[0]->aller_upload_image; ?>', '<?php echo $labeler_settings[0]->company_id; ?>', 'aller_upload_image');"><input type="button" id="delete_aller_image" value="<?php echo _("Delete"); ?>" /></a>
                    <?php } ?>
                  </td>
                </tr>

                <tr>
                  <td>&nbsp;</td>
                  <td>
                    <div id="uploaded_image7"></div>
                    <?php if ($labeler_settings && !empty($labeler_settings[0]->aller_upload_image)) { ?>
                      <img class="hide_image_box" width="250px" height="250px" style="margin: 5px;" src="<?php echo base_url() . 'assets/aller_upload_image/' . $labeler_settings[0]->aller_upload_image; ?>">
                    <?php } ?>
                    <input type="hidden" id="x" name="x" />
                    <input type="hidden" id="y" name="y" />
                    <input type="hidden" id="w" name="w" />
                    <input type="hidden" id="h" name="h" />
                  </td>
                </tr>


                <tr>
                  <td colspan="2" align="center">
                    <input type="submit" name="image_upd" value="<?php echo _('Upload Image'); ?>" class="btnWhiteBack" />
                  </td>
                </tr>
              </tbody>
            </table>
          </form>
        </td>
        <td width="50">&nbsp;</td>
      </tr>


    <?php } ?>
    <tr>
      <td style="padding:0px 70px 0px 70px" colspan="4">&nbsp;</td>
    </tr>
  </tbody>
</table>
</td>
</tr>
</tbody>
</table>
</td>
</tr>
<tr>
  <td height="10">&nbsp;</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
<!-- end of main body -->
</div>
<div id="push"></div>
</div>

<script type="text/javascript">
  var selected_txt = jQuery("#client_system_name option:selected").text();
  if (selected_txt == 'Digi') {
    jQuery('#sub_comp').css('display', 'block');
  }
  $(document).on('change', '#client_system_name', function($) {
    var selected_txt = jQuery("#client_system_name option:selected").text();
    if (selected_txt == 'Digi') {
      jQuery('#sub_comp').css('display', 'block');

    } else {
      jQuery('#sub_comp').css('display', 'none');

    }
  })


  $(document).on('click', '.activate_eb_sup,.activate_order_app_sup', function() {
    var company_id = jQuery(this).attr('data-comp_id');

    if (jQuery(this).hasClass('activate_eb_sup')) {
      if (jQuery(this).is(":checked")) {
        var activate_easybutler = 1;
      } else {
        var activate_easybutler = 0;
      }
      if (jQuery(this).closest('tr').find('.activate_order_app_sup').is(":checked")) {
        var easybutler_order_app = 1;
      } else {
        var easybutler_order_app = 0;
      }

    } else {
      if (jQuery(this).is(":checked")) {
        var easybutler_order_app = 1;
      } else {
        var easybutler_order_app = 0;
      }
      if (jQuery(this).closest('tr').find('.activate_eb_sup').is(":checked")) {
        var activate_easybutler = 1;
      } else {
        var activate_easybutler = 0;
      }

    }

    jQuery.ajax({
      type: "POST",
      url: base_url + "mcp/companies/update_easybutler_status",
      dataType: 'json',
      data: {
        'company_id': company_id,
        'activate_easybutler': activate_easybutler,
        'easybutler_order_app': easybutler_order_app
      },
      success: function(response) {
        alert(response.result);
      }
    });


  });


  $(document).on('click', '.show_menucard', function() {
    var company_id = jQuery(this).attr('data-comp_id');
    if (jQuery(this).closest('tr').find('.show_menucard').is(":checked")) {
      var show_menukartt = 1;
    } else {
      var show_menukartt = 0;
    }
    jQuery.ajax({
      type: "POST",
      url: base_url + "mcp/companies/update_show_menucard",
      dataType: 'json',
      data: {
        'company_id': company_id,
        'show_menukartt': show_menukartt
      },
      success: function(response) {
        alert(response.result);
      }
    });
  });

  $('input[name="reset-password"]').click(function() {
    jQuery.ajax({
      type: 'POST',
      url: base_url + '/mcp/companies/forget_password',
      data: {
        'id': <?php echo $content[0]->id; ?>
      },
      success: function() {
        alert('<?php echo _('Mail sent successfully!'); ?>');
      },
      error: function() {
        alert('<?php echo _('Error sending Mail'); ?>');
      }
    });
  });
  
  $(document).on("click", "#mark_high_priority_client", function() {
    if (this.getAttribute('checked')) { // check the presence of checked attr
      jQuery(this).removeAttr('checked'); // if present remove that
      jQuery(this).val('0');
    } else {
      jQuery(this).attr('checked', true); // if not then make checked
      jQuery(this).val('1');
    }
  });
  $(document).on("click", "#save_fav_setting", function() {
    var company_id = jQuery(document).find('#company_id').val();
    var fav_list = [];
    jQuery(document).find("input[name='additional_fav_list[]']").each(function(){
        if(jQuery(this).val().length){
          fav_list.push(jQuery(this).val());
        }
    });
     if (jQuery(document).find('#show_fav_list').is(":checked")) {
       var show_fav_list = 1;
     } else {
       var show_fav_list = 0;
     }
     jQuery.ajax({
        type: "POST",
        url: base_url + "mcp/companies/update_fav_setting",
        dataType: 'json',
        data: {
          'company_id': company_id,
          'show_fav_list': show_fav_list,
          'fav_list' : fav_list
        },
        success: function(response) {
          alert(response.result);
        }
      });
    });

  // $(document).on('click','#haccp_status',function(){
  //   if(jQuery("#haccp_status").prop("checked")){
  //     jQuery("#cost_price_status").prop("checked", false);
  //   }
  // });
  // $(document).on('click','#cost_price_status',function(){
  //   if(jQuery("#cost_price_status").prop("checked")){
  //     jQuery("#haccp_status").prop("checked", false);
  //   }
  // });

  // var type_ids = jQuery("#type_id").val(); 
  // var array1 = ["14","15","22"];
  // var intersection = array1.filter(value => -1 !== type_ids.indexOf(value));
  // if(intersection == ''){
  //  console.log($(document).find('.act_easy'));
  //  $(document).find('.act_easy').css('display', 'none');
  //  $(document).find('.order_app').css('display', 'none');
  // }


  // array1 = ["14","22"];
  // var intersection = array1.filter(value => -1 !== type_ids.indexOf(value));
  // if(type_ids.length == 2 && intersection.length == 2){
  //  jQuery("#enable_easybutler").prop("checked", true);
  // }


  //   jQuery(document).on('change','#type_id',function(){
  //    var type_ids = jQuery("#type_id").val(); 
  //    console.log(type_ids);
  //    array1 = ["14","22"];
  //    var intersection = array1.filter(value => -1 !== type_ids.indexOf(value));
  //    if(type_ids.length == 2 && intersection.length == 2){
  //      jQuery("#enable_easybutler").prop("checked", true);
  //    }else{
  //     jQuery("#enable_easybutler").prop("checked", false);
  //   }
  // });
</script>

<!-- <script type="text/javascript">
  jQuery( document ).ready(function($) {

    $(document).on('change','#type_id',function(){
       var value = $(this).val();
       var type_id = ['1', '8', '9', '10', '12', '23'];
       var type_id2 = ['20', '27', '28'];
           $.each(value , function(index, val) { 
            var check = type_id.includes(val);
            var check2 = type_id2.includes(val);
          if(check == false){
            $('#show_menukartt_maker').attr('checked','checked');
            if(check2 == true){
              $('#enable_infodesk').attr('checked','checked'); 
              return false;
            }else{
              $('#enable_infodesk').removeAttr('checked');
            }
            
          }
          else{
            $('#show_menukartt_maker').removeAttr('checked');
            }
        });
    });
   });
</script> -->