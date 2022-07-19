<!-- start of main body -->
<script>
jQuery(document).ready(function(){
	  jQuery("#download_excel").click(function(){
	    var val=jQuery("#company_id_export").val();
	if(val=='')
	{
		alert("please select the company");
		return false;
	}
	else{
	}
	  });
	  
	});
</script>

<table width="100%" cellspacing="0" cellpadding="0" border="0">
<tbody>
<tr>
    <td valign="top" align="center">
	    <table width="98%" cellspacing="0" cellpadding="0" border="0">
        <tbody>
        <!-- >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> EXPORT EXCEL PRODUCT: START <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< -->
        <tr id="tr1">
            <td valign="top" align="center" style="border:#8F8F8F 1px solid">
	 	        <table width="100%" cellspacing="0" cellpadding="0" border="0">
                <tbody>
                <?php $messages = $this->messages->get();?>
				<?php if($messages != array()): ?>
                <tr>
                   <td>
                    <?php foreach($messages as $type => $message):?>
			
						<?php if($type == 'success' && $message != array()):?>
                            <div id="succeed" style="color: #FFF; font-weight: bold; background: green; margin: 10px 15px; padding: 5px 10px;"><strong><?php echo _('Succeed')?></strong> : <?php echo $message[0];?></div>
                        <?php elseif($type == 'error' && $message != array()):?>	
                            <div id="error" style="color: #FFF; font-weight: bold; background: #A32022; margin: 10px 15px; padding: 5px 10px;"><strong><?php echo _('Error')?></strong> : <?php echo $message[0];?></div>
                        <?php endif;?>
                        
                    <?php endforeach; ?>
                   </td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td align="center" style="padding:15px 0px 5px 0px">
					    <table width="98%" cellspacing="0" cellpadding="0" border="0" style="background:url(<?php echo base_url('');?>assets/mcp/images/bg.jpg) left top repeat-x;" class="page_caption">
                        <tbody>
                        <tr>
                            <td width="94%" align="left"><h3><?php echo _('PRODUCTS: EXCEL DOWNLOAD'); ?></h3></td>
                            <td width="3%" align="right"></td>
                            <td width="3%" align="left">
							  <div class="icon_button">
							      <img width="16" height="16" border="0" style="cursor:pointer" onClick="javascript:history.back();" title="Go Back" alt="Go Back" src="<?php echo base_url('');?>assets/mcp/images/undo.jpg">
							  </div>
							</td>
                        </tr>
                        </tbody>
                        </table>
					</td>
                </tr>
                <tr>
                	<td align="center" style="padding:15px 0px 5px 0px">
                 		<form method="post" action="<?php echo base_url();?>mcp/excel_import/download_file"  id="export_excel">
                          <table cellpadding="8">
                            <tr>
                               <td><strong style="color:#002EBC;"><?php echo _('Choose Company'); ?> : </strong></td>
                               <td>
                                   <select name="company_id_export" id="company_id_export">
                                     <?php if( !empty($companies) ){ ?>
                                     <option value=""> -- <?php echo _('Select Company'); ?> -- </option>
                                     <?php foreach($companies as $comp){ ?>
                                     <option value="<?php echo $comp->id; ?>"><?php echo $comp->company_name; ?></option>
                                     <?php } ?>
                                     <?php } else { ?>
                                     <option value=""> -- <?php echo _('No Companies'); ?> -- </option>
                                     <?php } ?>
                                   </select>
                               </td>
                             <td> <button type="submit" name="download_excel" id="download_excel" ><img src="<?php echo base_url(); ?>assets/mcp/images/download.png" width="180"></a></td>
                              <!--  <td><a href="javascript:void(0);" style="float:left;" onClick="if( document.getElementById('company_id').value=='' ){ alert('<?php echo _('Please select a company to download data !'); ?>'); return false; }else{ document.getElementById('import_excel').submit(); }"><img src="<?php echo base_url(); ?>assets/mcp/images/upload.png" width="180"></button></td> -->
                            </tr>
                          </table>
                    	</form>
                    </td>  
                    <!-- td width="100%" align="center" valign="middle">
                       <div style="width:550px; margin:0 auto;">
                       <span style="color: #4E7A00; display: block; float: left; font-size: 18px; font-weight: bold; margin-right: 10px; margin-top: 25px; text-align: right; width: 300px;"><?php echo _('Blank Excel-Sheet & Help Document'); ?> : </span>
                       <a href="<?php echo base_url(); ?>download.php?f=Sample_Excel_Help_Doc.zip" style="float:left;"><img src="<?php echo base_url(); ?>assets/mcp/images/download.png" width="180"></a>
                       </div>
                    </td> -->
                </tr>
               </tbody>
               </table>
            </td>
        </tr>
        <!-- >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> EXPORT EXCEL PRODUCT: END <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< -->
        
        <tr>
        	<td>&nbsp;</td>
        </tr>
        
        <!-- >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> IMPORT EXCEL PRODUCT: START <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< -->
        <tr id="tr2">
            <td valign="top" align="center" style="border:#8F8F8F 1px solid">
	 	        <table width="100%" cellspacing="0" cellpadding="0" border="0">
                <tbody>
               <tr>
                    <td align="center" style="padding:15px 0px 5px 0px">
					    <table width="98%" cellspacing="0" cellpadding="0" border="0" style="background:url(<?php echo base_url('');?>assets/mcp/images/bg.jpg) left top repeat-x;" class="page_caption">
                        <tbody>
                        <tr>
                            <td width="94%" align="left"><h3><?php echo _('PRODUCTS: EXCEL IMPORT'); ?></h3></td>
                            <td width="3%" align="right"></td>
                            <td width="3%" align="left"></td>
                        </tr>
                        </tbody>
                        </table>
					</td>
                </tr>
                <tr>
                    <td width="100%" align="center" valign="middle">
                       <p><?php echo _('Select a company from the dropdown, for which you want to import the data. Upload the excel file and click upload. Remember, the excel file should be in the same format, which was downloaded from the above !'); ?></p>
                       
                       <?php //print_r($companies); ?>
                       
                       <form method="post" action="" enctype="multipart/form-data" id="import_excel">
                          <table cellpadding="8">
                            <tr>
                               <td><strong style="color:#002EBC;"><?php echo _('Choose Company'); ?> : </strong></td>
                               <td>
                                   <select name="company_id" id="company_id">
                                     <?php if( !empty($companies) ){ ?>
                                     <option value=""> -- <?php echo _('Select Company'); ?> -- </option>
                                     <?php foreach($companies as $comp){ ?>
                                     <option value="<?php echo $comp->id; ?>"><?php echo $comp->company_name; ?></option>
                                     <?php } ?>
                                     <?php } else { ?>
                                     <option value=""> -- <?php echo _('No Companies'); ?> -- </option>
                                     <?php } ?>
                                   </select>
                               </td>
                           
                               <td><strong style="color:#002EBC;"><?php echo _('Upload Excel'); ?> : </strong></td>
                               <td><input type="file" name="upload_excel" id="upload_excel" /></td>
                               <td><a href="javascript:void(0);" style="float:left;" onClick="if( document.getElementById('company_id').value=='' ){ alert("<?php echo _('Please select a company to upload data !'); ?>"); return false; }else{ document.getElementById('import_excel').submit(); }"><img src="<?php echo base_url(); ?>assets/mcp/images/upload.png" width="180"></a></td>
                            </tr>
                          </table>
                       </form>
                       
                    </td>
                </tr>
               </tbody>
               </table>
            </td>
        </tr>
        <!-- >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> IMPORT EXCEL PRODUCT: START <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< -->
        
        <tr>
        	<td>&nbsp;</td>
        </tr>
        
        <!-- >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> EXPORT EXCEL CLIENTS: START <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< -->
        <tr id="tr3">
            <td valign="top" align="center" style="border:#8F8F8F 1px solid">
	 	        <table width="100%" cellspacing="0" cellpadding="0" border="0">
                <tbody>
                <tr>
                    <td align="center" style="padding:15px 0px 5px 0px">
					    <table width="98%" cellspacing="0" cellpadding="0" border="0" style="background:url(<?php echo base_url('');?>assets/mcp/images/bg.jpg) left top repeat-x;" class="page_caption">
                        <tbody>
                        <tr>
                            <td width="94%" align="left"><h3><?php echo _('CLIENTS: EXCEL DOWNLOAD'); ?></h3></td>
                            <td width="3%" align="right"></td>
                            <td width="3%" align="left">
							</td>
                        </tr>
                        </tbody>
                        </table>
					</td>
                </tr>
                <tr>
                	<td align="center" style="padding:15px 0px 5px 0px">
                 		<form method="post" action="<?php echo base_url();?>mcp/excel_import/download_client_info_file"  id="export_excel_client">
                          <table cellpadding="8">
                            <tr>
                               <td><strong style="color:#002EBC;"><?php echo _('Choose Company'); ?> : </strong></td>
                               <td>
                                   <select name="company_id_export_client" id="company_id_export_client">
                                     <?php if( !empty($companies) ){ ?>
                                     <option value=""> -- <?php echo _('Select Company'); ?> -- </option>
                                     <?php foreach($companies as $comp){ ?>
                                     <option value="<?php echo $comp->id; ?>"><?php echo $comp->company_name; ?></option>
                                     <?php } ?>
                                     <?php } else { ?>
                                     <option value=""> -- <?php echo _('No Companies'); ?> -- </option>
                                     <?php } ?>
                                   </select>
                               </td>
                             <td> <button type="submit" name="download_excel_client" id="download_excel_client" ><img src="<?php echo base_url(); ?>assets/mcp/images/download.png" width="180"></a></td>
                            </tr>
                          </table>
                    	</form>
                    </td>  
                </tr>
               </tbody>
               </table>
            </td>
        </tr>
        <!-- >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> EXPORT EXCEL CLIENTS: END <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< -->
        
        <tr>
        	<td>&nbsp;</td>
        </tr>
        
        <!-- >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> IMPORT EXCEL CLIENTS: START <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< -->
        <tr id="tr2">
            <td valign="top" align="center" style="border:#8F8F8F 1px solid">
	 	        <table width="100%" cellspacing="0" cellpadding="0" border="0">
                <tbody>
               <tr>
                    <td align="center" style="padding:15px 0px 5px 0px">
					    <table width="98%" cellspacing="0" cellpadding="0" border="0" style="background:url(<?php echo base_url('');?>assets/mcp/images/bg.jpg) left top repeat-x;" class="page_caption">
                        <tbody>
                        <tr>
                            <td width="94%" align="left"><h3><?php echo _('CLIENT: EXCEL IMPORT'); ?></h3></td>
                            <td width="3%" align="right"></td>
                            <td width="3%" align="left"></td>
                        </tr>
                        </tbody>
                        </table>
					</td>
                </tr>
                <tr>
                    <td width="100%" align="center" valign="middle">
                       <p><?php echo _('Select a company from the dropdown, for which you want to import the data. Upload the excel file and click upload. Remember, the excel file should be in the same format, which was downloaded from the above !'); ?></p>
                       
                       <?php //print_r($companies); ?>
                       
                       <form method="post" action="<?php echo base_url();?>mcp/excel_import/upload_client_info_file" enctype="multipart/form-data" id="client_import_excel">
                          <table cellpadding="8">
                            <tr>
                               <td><strong style="color:#002EBC;"><?php echo _('Choose Company'); ?> : </strong></td>
                               <td>
                                   <select name="company_id_client" id="company_id_client">
                                     <?php if( !empty($companies) ){ ?>
                                     <option value=""> -- <?php echo _('Select Company'); ?> -- </option>
                                     <?php foreach($companies as $comp){ ?>
                                     <option value="<?php echo $comp->id; ?>"><?php echo $comp->company_name; ?></option>
                                     <?php } ?>
                                     <?php } else { ?>
                                     <option value=""> -- <?php echo _('No Companies'); ?> -- </option>
                                     <?php } ?>
                                   </select>
                               </td>
                           
                               <td><strong style="color:#002EBC;"><?php echo _('Upload Excel'); ?> : </strong></td>
                               <td><input type="file" name="upload_excel_client" id="upload_excel_client" /></td>
                               <td><a href="javascript:void(0);" style="float:left;" onClick="if( document.getElementById('company_id_client').value=='' ){ alert("<?php echo _('Please select a company to upload data !'); ?>"); return false; }else{ document.getElementById('client_import_excel').submit(); }"><img src="<?php echo base_url(); ?>assets/mcp/images/upload.png" width="180"></a></td>
                            </tr>
                          </table>
                       </form>
                       
                    </td>
                </tr>
               </tbody>
               </table>
            </td>
        </tr>
        <!-- >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> IMPORT EXCEL CLIENTS: START <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< -->
        </tbody>
        </table>
    </td>
</tr>
</tbody>
</table>