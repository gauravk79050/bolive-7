<style>
.overlay {
    height: 0%;
    width: 100%;
    position: fixed;
    z-index: 1;
    top: 0;
    left: 0;
    background-color: rgb(0,0,0);
    background-color: rgba(0,0,0, 0.9);
    overflow-y: hidden;
    transition: 0.02s;
}

.overlay-content {
    position: relative;
    top: 35%;
    width: 100%;
    text-align: center;
    margin-top: 30px;
}

.overlay a {
    text-decoration: none;
}
.overlay span{
  margin-top: 20px;
  font-size: 16px;
  color: #b5b5b5;
  display: block;
}


@media screen and (max-height: 450px) {
  .overlay {overflow-y: auto;}
  .overlay a {font-size: 20px}
}
</style>
<script>
function openNav() {
    document.getElementById("myNav").style.height = "100%";
}
</script>

<div id="myNav" class="overlay">
  <div class="overlay-content">
    <a href="javascript:;"><svg width='70px' height='70px' xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" class="uil-default"><rect x="0" y="0" width="100" height="100" fill="none" class="bk"></rect><rect  x='46.5' y='40' width='7' height='20' rx='5' ry='5' fill='rgba(180,180,180,0.772)' transform='rotate(0 50 50) translate(0 -30)'>  <animate attributeName='opacity' from='1' to='0' dur='1s' begin='0s' repeatCount='indefinite'/></rect><rect  x='46.5' y='40' width='7' height='20' rx='5' ry='5' fill='rgba(180,180,180,0.772)' transform='rotate(30 50 50) translate(0 -30)'>  <animate attributeName='opacity' from='1' to='0' dur='1s' begin='0.08333333333333333s' repeatCount='indefinite'/></rect><rect  x='46.5' y='40' width='7' height='20' rx='5' ry='5' fill='rgba(180,180,180,0.772)' transform='rotate(60 50 50) translate(0 -30)'>  <animate attributeName='opacity' from='1' to='0' dur='1s' begin='0.16666666666666666s' repeatCount='indefinite'/></rect><rect  x='46.5' y='40' width='7' height='20' rx='5' ry='5' fill='rgba(180,180,180,0.772)' transform='rotate(90 50 50) translate(0 -30)'>  <animate attributeName='opacity' from='1' to='0' dur='1s' begin='0.25s' repeatCount='indefinite'/></rect><rect  x='46.5' y='40' width='7' height='20' rx='5' ry='5' fill='rgba(180,180,180,0.772)' transform='rotate(120 50 50) translate(0 -30)'>  <animate attributeName='opacity' from='1' to='0' dur='1s' begin='0.3333333333333333s' repeatCount='indefinite'/></rect><rect  x='46.5' y='40' width='7' height='20' rx='5' ry='5' fill='rgba(180,180,180,0.772)' transform='rotate(150 50 50) translate(0 -30)'>  <animate attributeName='opacity' from='1' to='0' dur='1s' begin='0.4166666666666667s' repeatCount='indefinite'/></rect><rect  x='46.5' y='40' width='7' height='20' rx='5' ry='5' fill='rgba(180,180,180,0.772)' transform='rotate(180 50 50) translate(0 -30)'>  <animate attributeName='opacity' from='1' to='0' dur='1s' begin='0.5s' repeatCount='indefinite'/></rect><rect  x='46.5' y='40' width='7' height='20' rx='5' ry='5' fill='rgba(180,180,180,0.772)' transform='rotate(210 50 50) translate(0 -30)'>  <animate attributeName='opacity' from='1' to='0' dur='1s' begin='0.5833333333333334s' repeatCount='indefinite'/></rect><rect  x='46.5' y='40' width='7' height='20' rx='5' ry='5' fill='rgba(180,180,180,0.772)' transform='rotate(240 50 50) translate(0 -30)'>  <animate attributeName='opacity' from='1' to='0' dur='1s' begin='0.6666666666666666s' repeatCount='indefinite'/></rect><rect  x='46.5' y='40' width='7' height='20' rx='5' ry='5' fill='rgba(180,180,180,0.772)' transform='rotate(270 50 50) translate(0 -30)'>  <animate attributeName='opacity' from='1' to='0' dur='1s' begin='0.75s' repeatCount='indefinite'/></rect><rect  x='46.5' y='40' width='7' height='20' rx='5' ry='5' fill='rgba(180,180,180,0.772)' transform='rotate(300 50 50) translate(0 -30)'>  <animate attributeName='opacity' from='1' to='0' dur='1s' begin='0.8333333333333334s' repeatCount='indefinite'/></rect><rect  x='46.5' y='40' width='7' height='20' rx='5' ry='5' fill='rgba(180,180,180,0.772)' transform='rotate(330 50 50) translate(0 -30)'>  <animate attributeName='opacity' from='1' to='0' dur='1s' begin='0.9166666666666666s' repeatCount='indefinite'/></rect></svg>
</a>
   <span><?php echo _('Please wait while we prepare your files for download...');?></span>
  </div>
</div>
<style type="text/css">
#sidebar, #sidebarSet{
  display:none;
}
.report_exp td a button{
  cursor:pointer;
}
#main-header h2 {
    font-size: 18px;
    float: unset!important;
}

#main-header h6 {
    font-weight: normal;
    margin-top: 10px;
}
</style>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css"/>
<!--
function print_all(){
  window.location = '<?php //echo base_url(); ?>cp/cdashboard/print_product_download/print_all';
}-->

<script type="text/javascript">

function closeNav() {
    document.getElementById("myNav").style.height = "0%";
}

var file_download = "<?php echo _("Request completed successfully.");?>";
$(document).ready(function(){
  $('.dat_format_export').on('click', function(){
    $.post('<?php echo base_url(); ?>cp/report_exp/digi_export/',{},
      function(response){ console.log(response);
      if(response !== '' ){
        window.location.href = '<?php echo base_url(); ?>cp/report_exp/download_dat_file/'+response;
        closeNav();
      }
      else{
        alert("<?php echo _('Please try again');?>");
        closeNav();
      }
    });
  });
  $('.zen_export').on('click', function(){
    $.post('<?php echo base_url(); ?>cp/report_exp/zenius_export/',{},
      function(response){
      if(response !== '' ){
        window.location.href = '<?php echo base_url(); ?>cp/report_exp/download_dat_file_zen/'+response;
        closeNav();
      }
      else{
        alert("<?php echo _('Please try again');?>");
        closeNav();
      }
    });
  });
  $('.label_export').on('click', function(){
    $.post('<?php echo base_url(); ?>cp/report_exp/label_export/',{},
      function(response){
      if(response !== '' ){
        window.location.href = '<?php echo base_url(); ?>cp/report_exp/download_dat_file_label/'+response;
        closeNav();
      }
      else{
        alert("<?php echo _('Please try again');?>");
        closeNav();
      }
    });
  });
  $('.bizerba_export').on('click', function(){
    $.post('<?php echo base_url(); ?>Test_cp/specific_product_get/',{},
      function(response){
      if(response !== '' ){
        window.location.href = '<?php echo base_url(); ?>cp/report_exp/download_dat_file_bizerba/'+response;
        closeNav();
      }
      else{
        alert("<?php echo _('Please try again');?>");
        closeNav();
      }
    });
  });
  $('.recipe_sheet_all_prod').on('click', function(){
    $.post('<?php echo base_url(); ?>cp/report_exp/all_recipe_sheets/',{},
      function(response){
      if(response !== '' ){
        window.location.href = '<?php echo base_url(); ?>cp/report_exp/download_dat_file_all_recipe_sheets/'+response;
        closeNav();
      }
      else{
        alert("<?php echo _('Please try again');?>");
        closeNav();
      }
    });
  });
  $('.technical_sheet_all_prod').on('click', function(){
    $.post('<?php echo base_url(); ?>cp/report_exp/all_technical_sheets/',{},
      function(response){
      if(response !== '' ){
        window.location.href = '<?php echo base_url(); ?>cp/report_exp/download_dat_file_all_technical_sheets/'+response;
        closeNav();
      }
      else{
        alert("<?php echo _('Please try again');?>");
        closeNav();
      }
    });
  });
  $('.pdf_all_prod').on('click', function(){
    $.post('<?php echo base_url(); ?>cp/report_exp/print_product/',{},
      function(response){
      if(response !== '' ){
        window.location.href = '<?php echo base_url(); ?>cp/report_exp/download_dat_file_all_pdf/'+response;
        closeNav();
      }
      else{
        alert("<?php echo _('Please try again');?>");
        closeNav();
      }
    });
  });
});
</script>

<div id="main" style="text-transform:none;">
    <div id="main-header">
     <h2><?php echo _('Report - Export')?></h2>
     <h6><?php echo _('Click on a download button and you will receive an email from us that notifies you when the files is ready to download. This can take a while')?></h6>
    </div>
  <div id="content" style="width: 100%;">
      <div id="content-container">

     <div class="box">
      <h3><?php echo _('EXPORT Files')?></h3>
      <div class="table">
          <table  cellspacing="0" cellpadding="0" border="0">
             <tbody>
                <!-- <tr>
                   <td><?php echo _('DIGI '._('Export ').'(XML)');?></td>
                   <td><?php echo _('This file can be imported via DIGI scales');?></td>
                   <td><a class="dat_format_export" href ="javascript:;" data-id = "digi_exp">
                    <span style="cursor:pointer" onclick="openNav()"><button><?php echo _('Download');?></button></span></td>
                </tr>

                <tr>
                   <td><?php echo _('Zenius Export (XLS)');?></td>
                   <td><?php echo _('This file can be imported into your software Zenus');?></td>
                   <td><a class="zen_export" href = "javascript:;" data-id = "zen_exp">
                   <span style="cursor:pointer" onclick="openNav()"><button><?php echo _('Download');?></button></span></a></td>
                </tr>

                <tr>
                   <td><?php echo _('Export Labels (XLS)');?></td>
                   <td><?php echo _('This file can be imported into labeling software');?></td>
                   <td><a class="label_export" href ="javascript:;" data-id = "labels_exp">
                   <span style="cursor:pointer" onclick="openNav()"><button><?php echo _('Download');?></button></span></td>
                </tr>

                <?php if( $this->company_id == '87' ){ ?>
                  <tr>
                     <td><?php echo _('BIZERBA (CSV)');?></td>
                     <td><?php echo _('This file can be sent via mail to bizerba');?></td>
                     <td><a class="bizerba_export" href ="javascript:;" data-id = "bizerba_exp">
                     <span style="cursor:pointer" onclick="openNav()"><button><?php echo _('Download');?></button></span></td>
                  </tr>
                <?php } ?>
                <div style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(102, 102, 102); z-index: 30001; opacity: 0.8; display: none;" class='loadimg'>
                  <img style="position: absolute; color: White; top: 50%; left: 45%;" src="<?php echo base_url();?>assets/cp/images/ajax-loading.gif">
                </div> -->
                 <tr id="notice"><td id="notice_msg"  colspan='2'><?php echo _("The section has been added in the new Controlepanel(2.0)" ); ?><?php echo "<br>"; ?><?php echo _("Please proceed with new Controlepanel(2.0)")?><?php echo "<br>"; ?><a target="_blank" style="color: #8c8c00;text-decoration: none;" href="<?php echo rtrim($this->config->item('new_obs_url'),'/') ?>"><?php echo rtrim($this->config->item('new_obs_url'),'/') ?></a></td></tr>
              </tbody>
            </table>
        </div>
     </div>
     <div class="box">
      <h3><?php echo _('REPORT')?></h3>
      <div class="table">
                   <table id="ftp-settings" class="report_exp" cellspacing="0" cellpadding="0" border="0">
             <tbody>
                     <!--  <tr>
                         <td width="40%"><?php echo _('Print all products (pdf)');?></td>
                         <td><a class="pdf_all_prod" href ="javascript:;" data-id = "print_all_rep" onclick="openNav()"><button><?php echo _('Download');?></button></td>
                      </tr>
                      <?php if(($this->session->userdata('menu_type') == 'fdd_light') || ($this->session->userdata('menu_type') == 'fdd_pro') || ($this->session->userdata('menu_type') == 'fdd_premium')){?>
                        <tr>
                           <td><?php echo _('Download Technical sheets of all products (zip)');?></td>
                           <td>
                        <a class="technical_sheet_all_prod" href ="javascript:;" data-id = "tech_rep" onclick="openNav()">
                        <button><?php echo _('Download');?></button></a></td>
                        </tr>
                        <?php if( isset($show_recipe) && $show_recipe == 1 ){ ?>
                          <tr>
                            <td><?php echo _('Download Recipe sheets of all products (zip)');?></td>
                            <td><a class="recipe_sheet_all_prod" href ="javascript:;" data-id = "recipe_rep" onclick="openNav()"><button><?php echo _('Download');?></button></td>
                          </tr>
                        <?php }
                          }
                        ?> -->
                         <tr id="notice"><td id="notice_msg"  colspan='2'><?php echo _("The section has been added in the new Controlepanel(2.0)" ); ?><?php echo "<br>"; ?><?php echo _("Please proceed with new Controlepanel(2.0)")?><?php echo "<br>"; ?><a target="_blank" style="color: #8c8c00;text-decoration: none;" href="<?php echo rtrim($this->config->item('new_obs_url'),'/') ?>"><?php echo rtrim($this->config->item('new_obs_url'),'/') ?></a></td></tr>
                   </tbody>
                   </table>
            </div>
     </div>



      <div class="box">
      <h3><?php echo _('Files')?></h3>
      <div class="table">
                   <table  cellspacing="0" cellpadding="0" border="0">
             <tbody><!-- 
                      <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>File</th>
                        <th>Remove</th>
                      </tr>
                      <?php //if(isset($download_links)){
                          foreach($download_links as $links){
                      ?>
                      <tr>
                          <td>
                            <?php echo $links->date;?>
                          </td>
                          <td>
                            <?php echo $links->type;?>
                          </td>
                          <?php $name = $links->report_export_name;
                                if($name == 'digi_export'){
                          ?>
                          <td width="27%" valign="top">
                            <a  href="<?php echo base_url();?>cp/fooddesk/download_dat_file/<?php echo $links->filename; ?>"><?php echo $links->filename; ?></a>
                          </td>
                          <?php } elseif($name == 'zenius_export'){?>
                             <td width="27%" valign="top">
                                <a  href="<?php echo base_url();?>cp/fooddesk/zenius_export_download_link/<?php echo $links->filename; ?>"><?php echo $links->filename; ?></a>
                             </td>
                          <?php } elseif($name == 'label_export'){ ?>
                              <td width="27%" valign="top">
                                <a  href="<?php echo base_url();?>cp/fooddesk/label_export_download_link/<?php echo $links->filename; ?>"><?php echo $links->filename; ?></a>
                             </td>
                          <?php } elseif($name == 'print_all_import'){ ?>
                               <td width="27%" valign="top">
                                <a  href="<?php echo base_url();?>cp/fooddesk/print_product_download_link/<?php echo $links->filename; ?>"><?php echo $links->filename; ?></a>
                             </td>

                          <?php } elseif($name == 'tech_import'){ ?>
                              <td width="27%" valign="top">
                                <a  href="<?php echo base_url();?>cp/fooddesk/tech_import_download_link/<?php echo $links->filename; ?>"><?php echo $links->filename; ?></a>
                              </td>
                          <?php } elseif($name == 'recipe_import'){ ?>
                              <td width="27%" valign="top">
                                <a  href="<?php echo base_url();?>cp/fooddesk/recipe_import_download_link/<?php echo $links->filename; ?>"><?php echo $links->filename; ?></a>
                              </td>
                          <?php } ?>
                          <td>
                          <a class="remove_row" href ="javascript:;" data-id = "<?php echo $links->download_id; ?>"><img src="<?php echo base_url(); ?>assets/cp/images/delete.png"></a>
                          </td>
                        </tr>
                      <?php }
                      //} ?> -->
                       <tr id="notice"><td id="notice_msg"  colspan='2'><?php echo _("The section has been added in the new Controlepanel(2.0)" ); ?><?php echo "<br>"; ?><?php echo _("Please proceed with new Controlepanel(2.0)")?><?php echo "<br>"; ?><a target="_blank" style="color: #8c8c00;text-decoration: none;" href="<?php echo rtrim($this->config->item('new_obs_url'),'/') ?>"><?php echo rtrim($this->config->item('new_obs_url'),'/') ?></a></td></tr>
                   </tbody>
                   </table>
            </div>
     </div>


    </div>
