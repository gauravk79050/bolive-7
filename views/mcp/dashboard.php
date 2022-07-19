<link rel="stylesheet" href="<?php echo base_url().'assets/mcp/thickbox/css/thickbox.css'?>" type="text/css"/>
 <script type="text/javascript" src="<?php echo base_url().'assets/mcp/thickbox/javascript/thickbox.js'?>">
 </script>
 <script type="text/javascript">
 jQuery(document).ready(function(){
   jQuery(".approve").click(function(){   
   		if( confirm('<?php echo _('On approving a company, it will be added . Do you wish to continue ?'); ?>') ){
   			jQuery.ajax({
   				type: 'POST',
   				url: "<?php echo (base_url().'mcp/dashboard/approve');?>",
   				data: {'id':jQuery(this).attr('rel'),'ac_type_id': jQuery(this).attr('data-type')},
   				success: function(data){
					if(data.message.trim() == 'success'){ 
				 		alert ('<?php echo _('Selected Company has been approved successfully.'); ?>');   
						window.location = window.location.href;             
	             	}else{
				 		alert("<?php echo _('Sorry ! Selected Company could not be approved.'); ?>");	
					}
           		},
   				dataType: 'json',
   				async:false
   			});
		   return false;
	}	   
	return false;
  });//end of click event
 
  //function to disapprove//
   jQuery(".disapprove").click(function(){
	
    if( confirm('<?php echo _('On disapproving a company, all its data would be deleted. Do you wish to continue ?'); ?>') )
    {
    	jQuery.ajax({
			type: 'POST',
			url: "<?php echo (base_url().'mcp/dashboard/disapprove');?>",
			data: {'id':jQuery(this).attr('rel')},
			success: function(data){
		   		if(data.trim() == 'deleted'){
					alert ('<?php echo _('The data for selected disapproved company has been deleted successfully.'); ?>');
					window.location = window.location.href; 
	            }else{
	             	alert("<?php echo _('Sorry ! Selected company could not be disapproved.'); ?>");
				}
			},
			async:false
		});
    	return false;
    }
    return false;
  });//end of click event
});

 function disapprove(id){
		jQuery.ajax({
			url: '<?php echo base_url();?>mcp/dashboard/disapprove_suggestion',
			type: 'post',
			async:false,
			data: { 'id':id},
			dataType: 'json',
			success: function(response){
				alert(response.RESULT);
			}
		});
		window.parent.tb_remove();
		window.location.reload();

	}

	function show_company_data(id){

		tb_show('Details','<?php echo base_url();?>mcp/dashboard/suggested_corrections_detail/'+id+'?height=500&width=700','');	

	}

	function take_backup(){
		
		jQuery.ajax({
			url: base_url+'mcp/dashboard/tables_backup',
			type: 'post',
			data: { },
			success: function(response){
				if(response)
					alert("<?php echo _('DB cleaned succesfully');?>");
				else
					alert("<?php echo _('DB cannot be cleaned succesfully');?>");
			}
		});
	}


	function update_sortname(){
		jQuery('#update_shortnames').html('<?php echo _("Updating")?>...');
		jQuery('#update_shortnames').attr('disabled','disabled');

		jQuery.ajax({
			url: base_url+'mcp/excel_import/update_shortname',
			type: 'post',
			data: { },
			success: function(response){
				if(response)
					alert("<?php echo _('Short Name updated successfully');?>");
				else
					alert("<?php echo _('Short Name can not be updated successfully');?>");
			},
			async:false
		});
		
		jQuery('#update_shortnames').html('<?php echo _("Update Short Name")?>');
		jQuery('#update_shortnames').removeAttr('disabled');
	}
 </script>
 
<div style="width:100%">

<!-- start of main body -->

    <table width="100%" cellspacing="0" cellpadding="0" border="0">
    <tr>
       <td valign="top" align="center">
	      <table width="98%" cellspacing="0" cellpadding="0" border="2">            
          <tr>
              <td valign="top" align="center" style="border:#003366 1px solid; padding:15px 0px 0px 0px">
	 	         <table width="98%" cellspacing="0" cellpadding="0" border="0">
                 <tr>
                     <td align="center" style="padding-bottom:10px">
					    <table width="100%" cellspacing="0" cellpadding="0" border="0" style="background:url(<?php echo base_url(); ?>assets/mcp/images/bg.jpg) left top repeat-x;" class="page_caption">
                        <tr>
                            <td height="30" align="left"><h3><?php echo _('DASHBOARD');?></h3></td>
                            <td height="30" align="right">
                            	<button style="cursor:pointer;float: right;" id="update_shortnames" class="btnWhiteBack" onclick="update_sortname();"><?php echo _('Update Short Name');?></button>
                            	<button style="margin-right: 15px; cursor:pointer;float: right;" id="tk_bckup" class="btnWhiteBack" onclick="take_backup();"><?php echo _('Clean Database');?></button>
                            </td>
                        </tr>
                        </table>
					 </td>
                 </tr>

                 <tr>
                     <td align="center" style="padding-bottom:10px">
					    <table style="width:500px" cellspacing="0" cellpadding="0" border="0" class="box">                            
                        <tr>
                            <td width="50%">
							    <table width="100%" cellspacing="0" cellpadding="0" border="0">
								
                                <tr>
                                    <td height="30" class="blackMediumNormal" align="left">
 								      <strong><?php echo _('Total Companies');?> = <?php echo sizeof($all_companies); ?></strong>
									</td>
                                </tr>
								  <?php if(!empty($all_company_types)){?>
                                  <?php foreach ($all_company_types as $company_types){
								          
								  ?>
                                <tr> 
                                     <td height="30" class="blackMediumNormal" align="left">
										<strong>Total&nbsp;<?php echo $company_types->company_type_name; ?> = 
										<?php $comp = $this->Mcompanies->get_company(array('type_id'=>$company_types->id));
										      echo count($comp);
										?>
										</strong>
									 </td>
									 
                                </tr>
                               <?php }} ?>     
                              
                                </table>
							</td>

                            <td width="50%">
							    <table width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                     <td height="30" class="blackMediumNormal" align="left">
									     <strong>Bestellingen vandaag = <?php echo $todays_order;?></strong>
									 </td>

                                </tr>

                                <!-- <tr>
                                     <td height="30" class="blackMediumNormal" align="left">
									     <strong><?php echo _('Orders This Week'); ?> = 0</strong>
								     </td>
                                </tr> -->
								
                                <tr>
                                    <!-- <td height="30" class="blackMediumNormal" align="left">
									  <strong><?php echo _('Earnings per Year'); ?> =
									 <?php 
									     $total_earnings=0;
									     foreach($all_companies as $companies){?>
									     <?php $total_earnings +=$companies->earnings_year; ?>
										 <?php } echo $total_earnings; ?>&nbsp;&nbsp;&euro;</strong>
								     </td> -->
                                </tr>
                                
                                <!-- <tr>
                                     <td height="30" class="blackMediumNormal" align="left">
									     <strong><?php echo _('Earnings This Month'); ?> = 0&nbsp;&nbsp;&euro;</strong>
									 </td>
                                </tr>

                                <tr>
                                     <td height="30" class="blackMediumNormal" align="left">
									     <strong><?php echo _('Earnings Photo Script'); ?> = 45.00&nbsp;&nbsp;&euro;</strong>
									 </td>
                                </tr> -->   
								                            
                                </table>
							</td>
                        </tr>
                        </table>
					</td>

                </tr>

                <!-- Companies Pending Start --->
                <tr>
                    <td align="center">
					    <table width="100%" cellspacing="0" cellpadding="0" border="0">
                        <tr>
                            <td width="50%" height="30" align="left" class="home_subhead">
							   <?php echo _('Companies Pending('.count($pending).')')?>
							</td>
                            
							<?php if(!count($pending))?>
							<td width="50%" align="right"></td>

                        </tr>

                        <tr>
                            <td bgcolor="#003366" colspan="2">
							   <table width="100%" cellspacing="0" cellpadding="0" border="0" style="background:url(<?php echo base_url(); ?>assets/mcp/images/pink_table_bg.jpg) left repeat;">                            
                               <tr>
                                  
								  <td width="4%" class="whiteSmallBold"><?php echo _('ID')?></td>

								  <td width="14%" class="whiteSmallBold"><?php echo _('Company Name')?></td>
								  
								  <td width="8%" class="whiteSmallBold"><?php echo _('Current Package')?></td>

								  <td width="11%" class="whiteSmallBold"><?php echo _('Type')?></td>

								  <td width="10%" class="whiteSmallBold"><?php echo _('First Name')?></td>

								  <td width="10%" class="whiteSmallBold"><?php echo _('Last Name')?></td>

								  <td width="16%" class="whiteSmallBold"><?php echo _('Email')?></td>

								  <td width="10%" class="whiteSmallBold"><?php echo _('Phone')?></td>

								  <td width="10%" class="whiteSmallBold"><?php echo _('City')?></td>
								  
								  <td width="16%" class="whiteSmallBold"><?php echo _('Action')?></td>
 
							   </tr>
                                      
                               <tr>

                                   <td valign="middle" bgcolor="#FFFFFF" style="border:#003366 1px solid" colspan="10">
								       
									   <table width="100%" cellspacing="0" cellpadding="0">
									   <?php if(count($pending) == 0){ ?>
									   <tr>										   
										  <td valign="middle" height="40" bgcolor="#FFFFFF" align="center" style="border:#003366 1px solid;" class="redMediumBold" colspan="9">
										     <strong><?php echo _('No Companies Pending For Approval'); ?> !!!</strong>
										  </td>
									   </tr>
									   <?php } else { ?>
										  
                                       <?php foreach($pending as $pendings):?>
									   
                                       <!-- division that get displayed on click of pending companies -->
									   
									   <tr>
									   <td>
									     <table width="600" cellspacing="0" cellpadding="0" border="0" class="override" id='company_info_<?php echo $pendings->id;?>' style="display:none;">
										 <tbody>
										 <tr>
										 	 <td width="100%" valign="top" align="center">
											     <table width="98%" cellspacing="0" cellpadding="0" border="0" style="border:1px solid #003366;background-color:#FFFFFF; margin:15px 10px 10px 10px">
												 <tbody>
												 <tr>
												    <td height="20" bgcolor="#003366" align="left" style="padding-left:10px;" class="whiteSmallBold"> 								       <?php echo _('Company Information'); ?>
							  					    </td>
												 </tr>
													
												 <tr>
													  <td height="10">&nbsp;</td>
												 </tr>
													
												 <tr>
													   <td>
													      <table width="100%" cellspacing="0" cellpadding="0" border="0" style="text-align:left">
														  <tbody>
														  <tr>
															 <td width="50%" height="30" class="wd_text">
															   <?php echo _('Website'); ?>
															 </td>
															 <td><?php echo $pendings->website;?></td>
														  </tr>
														  <tr>
															 <td height="30" class="wd_text">
															   <?php echo _('Address'); ?>
															 </td>
															 <td><?php echo $pendings->address?></td>
														  </tr>
														  <tr>
															 <td height="30" class="wd_text">
															   <?php echo _('Zipcode'); ?>
															 </td>
															 <td><?php echo $pendings->zipcode?></td>
														  </tr>
														  <tr>
															 <td height="30" class="wd_text">
															   <?php echo _('City'); ?>
															 </td>
															 <td><?php  echo $pendings->city?></td>
														  </tr>
														  <tr>
															 <td height="30" class="wd_text">
															   <?php echo _('Country'); ?>
															 </td>
															 <td><?php  echo $pendings->country_id?></td>
														  </tr>
														  <tr>
															 <td height="30" class="wd_text">
															   <?php echo _('Username'); ?>
															 </td>
															 <td><?php echo $pendings->username?></td>
														  </tr>
														  <tr>
															 <td height="30" class="wd_text">
															   <?php echo _('Password'); ?>
															 </td>
															 <td><?php echo $pendings->password?></td>
														  </tr>
														  <tr>
															 <td height="30" class="wd_text">
															   <?php echo _('Expiry Date').' ('._('Every Year').')'; ?>
															 </td>
															 <td><?php echo $pendings->expiry_date?></td>
														  </tr>
														  <tr>
															 <td height="30" class="wd_text">
															   <?php echo _('Date Registration'); ?>
															 </td>
															 <td><?php echo $pendings->registration_date?></td>
														  </tr>
														  <tr>
															 <td height="30" class="wd_text">
															   <?php echo _('Earnings').'/'._('Year'); ?>
															 </td>
															 <td><?php echo $pendings->earnings_year?></td>
														  </tr>
														</tbody>
													    </table>
													 </td>
												  </tr>
												  </tbody>
												  </table>
											  </td>
										  </tr>
										  </tbody>
										  </table>
										</td>
										</tr>									   
										 
									   <!--end of the division-->
											
                                       <tr style="background:#fff;">

                                            <td width="4%" height="40" class="blackMediumNormal">
										      <?php echo $pendings->id ?>
										    </td>

											<td width="14%" class="blackMediumNormal"><a style="text-decoration: none;" target="_blank" href="<?php echo base_url(); ?>mcp/companies/update/<?php echo $pendings->id; ?>"><?php echo $pendings->company_name?></a>
											<?php 
											  
											  $imgs = img(
														   array(
																  'src'=>base_url()."assets/mcp/images/moreinfo.jpeg",
																  'width'=>"35",
																  'border'=>"0",
																  'height'=>"25"
																 )
														 );    
																	  
											  $js=array('class'=>"thickbox",'title'=>_('Company Information'));
											  
											  echo anchor( base_url()."mcp/dashboard#TB_inline?height=350&width=400&inlineId=company_info_".$pendings->id, $imgs, $js );
											?>
											</td>

                                            <td width="8%" class="blackMediumNormal"><?php echo $pendings->ac_title?></td>
											<td width="11%" class="blackMediumNormal"><?php echo $pendings->company_type_name?></td>

											<td width="10%" class="blackMediumNormal"><?php echo $pendings->first_name?></td>

											<td width="10%" class="blackMediumNormal"><?php echo $pendings->last_name?></td>

											<td width="16%" class="blackMediumNormal"><?php echo $pendings->email?></td>

											<td width="10%" class="blackMediumNormal"><?php echo $pendings->phone?></td>

											<td width="10%" class="blackMediumNormal"><?php echo $pendings->city?></td>
												
											<td width="16%" class="blackMediumNormal">
											  <a href="javascript:void(0);" class="approve" name="approve" rel="<?php echo $pendings->id; ?>" data-type="<?php echo $pendings->ac_type_id;?>" ><?php echo _('Approve'); ?></a>
											  <a href="javascript:void(0);" class="disapprove" name="disapprove" rel="<?php echo $pendings->id; ?>"><?php echo _('Disapprove'); ?></a>												
											</td>

                                          </tr>
                                               
                                          <?php endforeach;?>
									
									      <?php } ?>
									 
                                          </table>
									  </td>
								  </tr>
                                  </table>
							 </td>
					     </tr>  
								                     
                         <tr>
                            <td colspan="2">&nbsp;</td>
                         </tr>                           
                         </table>
					</td>
                </tr>
				<!-- Companies Pending Ends --->
				
				
				
				<!-- Companies Expiring This Month Start -->
				<tr>
                    <td align="center">
					    <table width="100%" cellspacing="0" cellpadding="0" border="0">
                        <tr>
                            <td width="50%" height="30" align="left" class="home_subhead">
							   <?php echo _('Companies Expiring This Month ('.count($expiring_this_month).')')?>
							</td>
                            
							<?php if(!count($expiring_this_month))?>
							<td width="50%" align="right"></td>

                        </tr>

                        <tr>
                            <td bgcolor="#003366" colspan="2">
							  <table width="100%" cellspacing="0" cellpadding="0" border="0" style="background:url(<?php echo base_url(); ?>assets/mcp/images/pink_table_bg.jpg) left repeat;">                            
                          					   
							  <tr>
                                <td width="4%" class="whiteSmallBold"><?php echo _('ID'); ?></td>
                                <td width="14%" class="whiteSmallBold"><?php echo _('Company Name'); ?></td>
								<td width="10%" class="whiteSmallBold"><?php echo _('Expire Date'); ?></td>
                                <td width="11%" class="whiteSmallBold"><?php echo _('Type')?></td>
                                <td width="10%" class="whiteSmallBold"><?php echo _('First Name')?></td>
                                <td width="10%" class="whiteSmallBold"><?php echo _('Last Name')?></td>
                                <td width="16%" class="whiteSmallBold"><?php echo _('Email')?></td>
                                <td width="10%" class="whiteSmallBold"><?php echo _('Phone')?></td>
                                <!-- <td width="15%" class="whiteSmallBold" align="center"><?php echo _('Invoice Made'); ?></td> -->
                              </tr>
                                      
                               <tr>

                                   <td valign="middle" bgcolor="#FFFFFF" style="border:#003366 1px solid" colspan="9">
								       
									   <table width="100%" cellspacing="0" cellpadding="0">
									   <?php if(count($expiring_this_month) == 0){ ?>
									   <tr>										   
										  <td valign="middle" height="40" bgcolor="#FFFFFF" align="center" style="border:#003366 1px solid;" class="redMediumBold" colspan="9">
										     <strong><?php echo _('No Companies Expiring This Month'); ?> !!!</strong>
										  </td>
									   </tr>
									   <?php } else { ?>
										  
                                       <?php foreach($expiring_this_month as $exp):?>
									   
									   <tr>
										  <td width="4%" height="40" class="blackMediumNormal"><?php echo $exp->id; ?></td>
										  <td width="14%" class="blackMediumNormal"><a style="text-decoration: none;" target="_blank" href="<?php echo base_url(); ?>mcp/companies/update/<?php echo $exp->id; ?>"><?php echo $exp->company_name; ?></a></td>
										  <td width="10%" class="blackMediumNormal"><?php echo $exp->expiry_date; ?></td>
										  <td width="11%" class="blackMediumNormal"><?php echo $exp->company_type_name; ?></td>
										  <td width="10%" class="blackMediumNormal"><?php echo $exp->first_name; ?></td>
										  <td width="10%" class="blackMediumNormal"><?php echo $exp->last_name; ?></td>
										  <td width="16%" class="blackMediumNormal"><?php echo $exp->email; ?></td>
										  <td width="10%" class="blackMediumNormal"><?php echo $exp->phone; ?></td>
										  <!-- <td width="15%" align="center" class="blackMediumNormal">
											   <input type="checkbox" class="textbox" id="invoice_exp_this" name="invoice_exp_this" onclick="window.location='<?php echo base_url().'mcp/dashboard/renewal_info/'.$exp->id; ?>';">
										 </td> -->
									   </tr>        
                                       
									   <?php endforeach;?>
									   <?php } ?>
									 
                                       </table>
									 </td>
								  </tr>
                                  </table>
							 </td>
					     </tr>  
								                     
                         <tr>
                            <td colspan="2">&nbsp;</td>
                         </tr>                           
                         </table>
					</td>
                </tr>
				<!-- Companies Expiring This Month Ends -->
				
				<!-- Companies Expiring Next Month Start -->
				<tr>
                    <td align="center">
					    <table width="100%" cellspacing="0" cellpadding="0" border="0">
                        <tr>
                            <td width="50%" height="30" align="left" class="home_subhead">
							   <?php echo _('Companies Expiring Next Month ('.count($expiring_next_month).')')?>
							</td>
                            
							<?php if(!count($expiring_next_month))?>
							<td width="50%" align="right"></td>

                        </tr>
                        <tr>
                            <td bgcolor="#003366" colspan="2">
							   <table width="100%" cellspacing="0" cellpadding="0" border="0" style="background:url(<?php echo base_url(); ?>assets/mcp/images/pink_table_bg.jpg) left repeat;">                            
                          					   
							   <tr>
                                <td width="4%" class="whiteSmallBold"><?php echo _('ID'); ?></td>
                                <td width="14%" class="whiteSmallBold"><?php echo _('Company Name'); ?></td>
								<td width="10%" class="whiteSmallBold"><?php echo _('Expire Date'); ?></td>
                                <td width="11%" class="whiteSmallBold"><?php echo _('Type')?></td>
                                <td width="10%" class="whiteSmallBold"><?php echo _('First Name')?></td>
                                <td width="10%" class="whiteSmallBold"><?php echo _('Last Name')?></td>
                                <td width="16%" class="whiteSmallBold"><?php echo _('Email')?></td>
                                <td width="10%" class="whiteSmallBold"><?php echo _('Phone')?></td>
                                <td width="15%" align="center" class="whiteSmallBold"><?php echo _('Invoice Made'); ?></td>
                              </tr>
                               <tr>
                                   <td valign="middle" bgcolor="#FFFFFF" style="border:#003366 1px solid" colspan="9">
									   <table width="100%" cellspacing="0" cellpadding="0">
									   <?php if(count($expiring_next_month) == 0){ ?>
									   <tr>										   
										  <td valign="middle" height="40" bgcolor="#FFFFFF" align="center" style="border:#003366 1px solid;" class="redMediumBold" colspan="9">
										     <strong><?php echo _('No Companies Expiring Next Month'); ?> !!!</strong>
										  </td>
									   </tr>
									   <?php } else { ?>
										  
                                       <?php foreach($expiring_next_month as $exp):?>
									   
									   <tr>
										  <td width="4%" height="40" class="blackMediumNormal"><?php echo $exp->id; ?></td>
										  <td width="14%" class="blackMediumNormal"><a style="text-decoration: none;" target="_blank" href="<?php echo base_url(); ?>mcp/companies/update/<?php echo $exp->id; ?>"><?php echo $exp->company_name; ?></a></td>
										  <td width="10%" class="blackMediumNormal"><?php echo $exp->expiry_date; ?></td>
										  <td width="11%" class="blackMediumNormal"><?php echo $exp->company_type_name; ?></td>
										  <td width="10%" class="blackMediumNormal"><?php echo $exp->first_name; ?></td>
										  <td width="10%" class="blackMediumNormal"><?php echo $exp->last_name; ?></td>
										  <td width="16%" class="blackMediumNormal"><?php echo $exp->email; ?></td>
										  <td width="10%" class="blackMediumNormal"><?php echo $exp->phone; ?></td>
										  <td width="15%" align="center" class="blackMediumNormal">
											   <input type="checkbox" class="textbox" id="invoice_exp_this" name="invoice_exp_this" onclick="window.location='<?php echo base_url().'mcp/dashboard/renewal_info/'.$exp->id; ?>';">
										 </td>
									   </tr>    
                                       
									   <?php endforeach;?>
									   <?php } ?>
									 
                                       </table>
									 </td>
								  </tr>
                                  </table>
							 </td>
					     </tr>  
								                     
                         <tr>
                            <td colspan="2">&nbsp;</td>
                         </tr>                           
                         </table>
					</td>
                </tr>
				<!-- Companies Expiring Next Month Ends -->
				
				
				<!-- Upgrade Requests Start -->
				<tr>
                    <td align="center">
					    <table width="100%" cellspacing="0" cellpadding="0" border="0">
                        <tr>
                            <td width="50%" height="30" align="left" class="home_subhead">
							   <?php echo _('Upgrade Requests').' ('.count($upgrade_requests).')'; ?>
							</td>
                            
							<td width="50%" align="right"></td>

                        </tr>

                        <tr>
                            <td bgcolor="#003366" colspan="2">
							   <table width="100%" cellspacing="0" cellpadding="0" border="0" style="background:url(<?php echo base_url(); ?>assets/mcp/images/pink_table_bg.jpg) left repeat;">                            
                          	   
							   <tr>
                                <td width="4%" class="whiteSmallBold"><?php echo _('ID'); ?></td>
                                <td width="14%" class="whiteSmallBold"><?php echo _('Company Name'); ?></td>
								<td width="15%" class="whiteSmallBold"><?php echo _('New Package'); ?></td>
                                <td width="15%" class="whiteSmallBold"><?php echo _('Name'); ?></td>
                                <td width="27%" class="whiteSmallBold"><?php echo _('Email'); ?></td>
                                <td width="10%" class="whiteSmallBold"><?php echo _('Phone'); ?></td>
								<td width="10%" class="whiteSmallBold"><?php echo _('City'); ?></td>
                                <td width="15%" align="center" class="whiteSmallBold"><?php echo _('Action'); ?></td>
                               </tr>
                                      
                               <tr>							   
                                   <td valign="middle" bgcolor="#FFFFFF" style="border:#003366 1px solid" colspan="8">
								       
                                       <?php //print_r($upgrade_requests); ?>
                                                                     
                                       <table width="100%" cellspacing="0" cellpadding="0">
									   <?php if( empty($upgrade_requests) ) { ?>
									   <tr>										   
										  <td valign="middle" height="40" bgcolor="#FFFFFF" align="center" style="border:#003366 1px solid;" class="redMediumBold" colspan="9">
										     <strong><?php echo _('No upgrade requests'); ?> !!!</strong>
										  </td>
									   </tr>
									   <?php } else { ?>
										  
                                       <?php foreach($upgrade_requests as $req):?>
									   
									   <tr>
										  <td width="4%" height="40" class="blackMediumNormal"><?php echo $req->id; ?></td>
										  <td width="14%" class="blackMediumNormal"><a style="text-decoration: none;" target="_blank" href="<?php echo base_url(); ?>mcp/companies/update/<?php echo $req->company_id; ?>"><?php echo $req->company_name; ?></a></td>                                          
                                          <td width="15%" class="blackMediumNormal"><?php echo $req->ac_title; ?></td>
                                          <td width="15%" class="blackMediumNormal"><?php echo $req->first_name.' '.$req->last_name; ?></td>
                                          <td width="27%" class="blackMediumNormal"><?php echo $req->email; ?></td>
                                          <td width="10%" class="blackMediumNormal"><?php echo $req->phone; ?></td>
                                          <td width="10%" class="blackMediumNormal"><?php echo $req->city; ?></td>
                                          <td width="15%" class="blackMediumNormal"><a href="<?php echo base_url(); ?>mcp/dashboard/approve_req/<?php echo $req->id; ?>" title="<?php echo _('Approve Request'); ?>"><?php echo _('Approve'); ?></a></td>
									   </tr>    
                                       
									   <?php endforeach;?>
									   <?php } ?>
									 
                                       </table>
                                   
                                   
								   </td>
							   </tr>
                               </table>
							   
							 </td>
					     </tr>  						 
						 		                     
                         <tr>
                            <td colspan="2">&nbsp;</td>
                         </tr>                           
                         </table>
					</td>
                </tr>
				<!-- Upgrade Requests Ends -->
				
				<!-- Suggestions Start -->
				<tr>
                    <td align="center">
					    <table width="100%" cellspacing="0" cellpadding="0" border="0">
                        <tr>
                            <td width="50%" height="30" align="left" class="home_subhead">
							   <?php echo _('Suggested Corrections'); ?>&nbsp;(<?php echo count($suggestions); ?>)
							</td>
							<td width="50%" align="right"></td>

                        </tr>

                        <tr>
                            <td bgcolor="#003366" colspan="2">
							   <table width="100%" cellspacing="0" cellpadding="0" border="0" style="background:url(<?php echo base_url(); ?>assets/mcp/images/pink_table_bg.jpg) left repeat;">                            
                          	   
							   <tr>
                                <td width="4%" class="whiteSmallBold"><?php echo _('ID'); ?></td>
                                <td width="15%" class="whiteSmallBold"><?php echo _('Company Name'); ?></td>
								<td width="16%" class="whiteSmallBold"><?php echo _('Subject'); ?></td>
                                <td width="40%" class="whiteSmallBold"><?php echo _('Remark'); ?></td>
                                <td width="10%" class="whiteSmallBold"><?php echo _('IP'); ?></td>
                                <td width="15%" align="center" class="whiteSmallBold"><?php echo _('Action'); ?></td>
                               </tr>
                        
                               
                               <tr>
                                   <td valign="middle" bgcolor="#FFFFFF" style="border:#003366 1px solid" colspan="6">
								                                     
                                       <table width="100%" cellspacing="0" cellpadding="0">
									   <?php if( empty($suggestions) ) { ?>
									   <tr>										   
										  <td valign="middle" height="40" bgcolor="#FFFFFF" align="center" style="border:#003366 1px solid;" class="redMediumBold" colspan="9">
										     <strong><?php echo _('No correction suggestions are been posted yet'); ?> !!!</strong>
										  </td>
									   </tr>
									   <?php } else { ?>
										  
                                       <?php foreach($suggestions as $sug):?>
									   
									   <tr>
										  <td width="4%" height="40" class="blackMediumNormal"><?php echo $sug['id']; ?></td>
										  <td width="15%" class="blackMediumNormal"><a style="text-decoration: none;" target="_blank" href="<?php echo base_url(); ?>mcp/companies/update/<?php echo $sug['company_id']; ?>"><?php echo $sug['company_name']; ?></a></td>
										  <td width="16%" class="blackMediumNormal"><?php echo $sug['subject']; ?></td>
										  <td width="40%" class="blackMediumNormal"><?php echo $sug['remark']; ?></td>
										  <td width="10%" class="blackMediumNormal"><?php echo $sug['ip_address']; ?></td>
										  <td width="15%" align="center" class="blackMediumNormal">
                                            <a onclick="show_company_data(<?php echo $sug['id'];?>)" href="javascript: void(0);"><?php echo _("View"); ?></a>
										  </td>
									   </tr>    
                                       
									   <?php endforeach;?>
									   <?php } ?>
									 
                                       </table>
                                   
                                   
								   </td>
							   </tr>
                               
                               </table>
							   
							 </td>
					     </tr>  
						 		                     
                         <tr>
                            <td colspan="2">&nbsp;</td>
                         </tr>                           
                         </table>
					</td>
                </tr>
				<!-- Suggestions Ends -->
				
				
				<!-- Notification from Portal -->
				<tr>
                    <td align="center">
					    <table width="100%" cellspacing="0" cellpadding="0" border="0">
                        <tr>
                            <td width="50%" height="30" align="left" class="home_subhead">
							   <?php echo _('Notifications to admin'); ?>
							</td>
							<td width="50%" align="right"></td>

                        </tr>

                        <tr>
                            <td bgcolor="#003366" colspan="2">
							   <table width="100%" cellspacing="0" cellpadding="0" border="0" style="background:url(<?php echo base_url(); ?>assets/mcp/images/pink_table_bg.jpg) left repeat;">                            
                          	   
							   <tr>
                                <td width="4%" class="whiteSmallBold"><?php echo _('ID'); ?></td>
                                <td width="15%" class="whiteSmallBold"><?php echo _('Company Name'); ?></td>
								<td width="16%" class="whiteSmallBold"><?php echo _('City Company'); ?></td>
                                <td width="20%" class="whiteSmallBold"><?php echo _('Entered Name'); ?></td>
                                <td width="20%" class="whiteSmallBold"><?php echo _('Entered City'); ?></td>
                                <td width="10%" class="whiteSmallBold"><?php echo _('IP'); ?></td>
                                <td width="15%" align="center" class="whiteSmallBold"><?php echo _('Action'); ?></td>
                               </tr>
                        
                               
                               <tr>
                                   <td valign="middle" bgcolor="#FFFFFF" style="border:#003366 1px solid" colspan="7">
								                                     
                                       <table width="100%" cellspacing="0" cellpadding="0">
									   <?php if( empty($notifications) ) { ?>
									   <tr>										   
										  <td valign="middle" height="40" bgcolor="#FFFFFF" align="center" style="border:#003366 1px solid;" class="redMediumBold" colspan="9">
										     <strong><?php echo _('No notifications are been posted yet'); ?> !!!</strong>
										  </td>
									   </tr>
									   <?php } else { ?>
										  
                                       <?php foreach($notifications as $notification):?>
									   
									   <tr>
										  <td width="4%" height="40" class="blackMediumNormal"><?php echo $notification->company_id; ?></td>
										  <td width="15%" class="blackMediumNormal"><a style="text-decoration: none;" target="_blank" href="<?php echo base_url(); ?>mcp/companies/update/<?php echo $notification->company_id; ?>" target="_blank"><?php echo $notification->company_name; ?></a></td>
										  <td width="16%" class="blackMediumNormal"><?php echo $notification->city; ?></td>
										  <td width="20%" class="blackMediumNormal"><?php echo $notification->clientname; ?></td>
										  <td width="20%" class="blackMediumNormal"><?php echo $notification->clientcity; ?></td>
										  <td width="10%" class="blackMediumNormal"><?php echo $notification->ip_address; ?></td>
										  <td width="15%" align="center" class="blackMediumNormal">
											<a href="<?php echo base_url(); ?>mcp/dashboard/send_notification/<?php echo $notification->id; ?>"><?php echo _("SEND");?></a>
                                            &nbsp;|&nbsp;
                                            <a href="<?php echo base_url(); ?>mcp/dashboard/delete_notification/<?php echo $notification->id; ?>" ><?php echo _("DELETE")?></a>
										  </td>
									   </tr>    
                                       
									   <?php endforeach;?>
									   <?php } ?>
									 
                                       </table>
                                   
                                   
								   </td>
							   </tr>
                               
                               </table>
							   
							 </td>
					     </tr>  
						 		                     
                         <tr>
                            <td colspan="2">&nbsp;</td>
                         </tr>                           
                         </table>
					</td>
                </tr>
				<!-- ------------------------ -->
				
				<!-- Contact Requests from Portal -->
				<tr>
                    <td align="center">
					    <table width="100%" cellspacing="0" cellpadding="0" border="0">
                        <tr>
                            <td width="50%" height="30" align="left" class="home_subhead">
							   <?php echo _('Contact Requests for companies'); ?>
							</td>
							<td width="50%" align="right"></td>

                        </tr>

                        <tr>
                            <td bgcolor="#003366" colspan="2">
							   <table width="100%" cellspacing="0" cellpadding="0" border="0" style="background:url(<?php echo base_url(); ?>assets/mcp/images/pink_table_bg.jpg) left repeat;">                            
                          	   
							   <tr>
                                <td width="3%" class="whiteSmallBold"><?php echo _('ID'); ?></td>
                                <td width="15%" class="whiteSmallBold"><?php echo _('Company Name'); ?></td>
								<td width="10%" class="whiteSmallBold"><?php echo _('Sender Name'); ?></td>
								<td width="20%" class="whiteSmallBold"><?php echo _('Email'); ?></td>
                                <td width="22%" class="whiteSmallBold"><?php echo _('Feedback Message'); ?></td>
                                <td width="10%" class="whiteSmallBold"><?php echo _('IP'); ?></td>
                                <td width="20%" align="center" class="whiteSmallBold"><?php echo _('Action'); ?></td>
                               </tr>
                        
                               
                               <tr>
                                   <td valign="middle" bgcolor="#FFFFFF" style="border:#003366 1px solid" colspan="7">
								                                     
                                       <table width="100%" cellspacing="0" cellpadding="0">
									   <?php if( empty($contact_requests) ) { ?>
									   <tr>										   
										  <td valign="middle" height="40" bgcolor="#FFFFFF" align="center" style="border:#003366 1px solid;" class="redMediumBold" colspan="9">
										     <strong><?php echo _('No requests are been made yet'); ?> !!!</strong>
										  </td>
									   </tr>
									   <?php } else { ?>
										  
                                       <?php foreach($contact_requests as $request):?>
									   
									   <tr>
										  <td width="3%" height="40" class="blackMediumNormal"><?php echo $request->id; ?></td>
										  <td width="15%" class="blackMediumNormal"><a style="text-decoration: none;" target="_blank" href="<?php echo base_url(); ?>mcp/companies/update/<?php echo $request->company_id; ?>"><?php echo $request->company_name; ?></a></td>
										  <td width="10%" class="blackMediumNormal"><?php echo $request->first_name.' '.$request->last_name; ?></td>
										  <td width="20%" class="blackMediumNormal"><?php echo $request->email; ?></td> 
										  <td width="22%" class="blackMediumNormal"><?php echo $request->feedback_msg; ?></td>
										  <td width="10%" class="blackMediumNormal"><?php echo $request->ip_address; ?></td>
										  <td width="20%" align="center" class="blackMediumNormal">
											<a href="<?php echo base_url(); ?>mcp/dashboard/appr_contact_req/<?php echo $request->id; ?>"><?php echo _("APPROVE");?></a>
                                            &nbsp;|&nbsp;
                                            <a href="<?php echo base_url(); ?>mcp/dashboard/disappr_contact_req/<?php echo $request->id; ?>" ><?php echo _("DISAPPROVE")?></a>
                                             &nbsp;|&nbsp;
                                            <a href="<?php echo base_url(); ?>mcp/dashboard/block_cr_ip/<?php echo $request->id; ?>" ><?php echo _("BLOCK IP")?></a>
										  </td>
									   </tr>    
                                       
									   <?php endforeach;?>
									   <?php } ?>
									 
                                       </table>
                                   
                                   
								   </td>
							   </tr>
                               
                               </table>
							   
							 </td>
					     </tr>  
						 		                     
                         <tr>
                            <td colspan="2">&nbsp;</td>
                         </tr>                           
                         </table>
					</td>
                </tr>
				<!-- ------------------------ -->
				
				<!-- Pending Comments -->
				<tr>
                    <td align="center">
					    <table width="100%" cellspacing="0" cellpadding="0" border="0">
                        <tr>
                            <td width="50%" height="30" align="left" class="home_subhead">
							   <?php echo _('Pendings comments for approval'); ?>
							</td>
							<td width="50%" align="right"></td>

                        </tr>

                        <tr>
                            <td bgcolor="#003366" colspan="2">
							   <table width="100%" cellspacing="0" cellpadding="0" border="0" style="background:url(<?php echo base_url(); ?>assets/mcp/images/pink_table_bg.jpg) left repeat;">                            
                          	   
							   <tr>
                                <td width="3%" class="whiteSmallBold"><?php echo _('ID'); ?></td>
                                <td width="14%" class="whiteSmallBold"><?php echo _('Company Name'); ?></td>
								<td width="10%" class="whiteSmallBold"><?php echo _('Sender Name'); ?></td>
								<td width="17%" class="whiteSmallBold"><?php echo _('Email'); ?></td>
								<td width="4%" class="whiteSmallBold"><?php echo _('Star'); ?></td>
                                <td width="22%" class="whiteSmallBold"><?php echo _('Comment'); ?></td>
                                <td width="10%" class="whiteSmallBold"><?php echo _('IP'); ?></td>
                                <td width="20%" align="center" class="whiteSmallBold"><?php echo _('Action'); ?></td>
                               </tr>
                        
                               
                               <tr>
                                   <td valign="middle" bgcolor="#FFFFFF" style="border:#003366 1px solid" colspan="8">
								                                     
                                       <table width="100%" cellspacing="0" cellpadding="0">
									   <?php if( empty($pending_comments) ) { ?>
									   <tr>										   
										  <td valign="middle" height="40" bgcolor="#FFFFFF" align="center" style="border:#003366 1px solid;" class="redMediumBold" colspan="9">
										     <strong><?php echo _('No pending comments'); ?> !!!</strong>
										  </td>
									   </tr>
									   <?php } else { ?>
										  
                                       <?php foreach($pending_comments as $pending_comment):?>
									   
									   <tr>
										  <td width="3%" height="40" class="blackMediumNormal"><?php echo $pending_comment->company_id; ?></td>
										  <td width="14%" class="blackMediumNormal"><a style="text-decoration: none;" target="_blank" href="<?php echo base_url(); ?>mcp/companies/update/<?php echo $pending_comment->company_id; ?>"><?php echo $pending_comment->company_name; ?></a></td>
										  <td width="10%" class="blackMediumNormal"><?php echo $pending_comment->username; ?></td>
										  <td width="17%" class="blackMediumNormal"><?php echo $pending_comment->email; ?></td>
										  <td width="4%" class="blackMediumNormal"><?php echo $pending_comment->rate; ?></td>
										  <td width="22%" class="blackMediumNormal"><?php echo $pending_comment->comment; ?></td>
										  <td width="10%" class="blackMediumNormal"><?php echo $pending_comment->user_ip; ?></td>
										  <td width="20%" align="center" class="blackMediumNormal">
											<a href="<?php echo base_url(); ?>mcp/dashboard/approve_comment/<?php echo $pending_comment->comment_id; ?>"><?php echo _("APPROVE");?></a>
                                            &nbsp;|&nbsp;
                                            <a href="<?php echo base_url(); ?>mcp/dashboard/disapprove_comment/<?php echo $pending_comment->comment_id; ?>" ><?php echo _("DISAPPROVE")?></a>
                                             <!-- &nbsp;|&nbsp;
                                            <a href="<?php echo base_url(); ?>mcp/dashboard/block_comment_ip/<?php echo $pending_comment->comment_id; ?>" ><?php echo _("BLOCK IP")?></a>
                                             -->
										  </td>
									   </tr>    
                                       
									   <?php endforeach;?>
									   <?php } ?>
									 
                                       </table>
                                   
                                   
								   </td>
							   </tr>
                               
                               </table>
							   
							 </td>
					     </tr>  
						 		                     
                         <tr>
                            <td colspan="2">&nbsp;</td>
                         </tr>                           
                         </table>
					</td>
                </tr>
				<!-- ------------------------ -->
				
				
				</table>
				
				
			 </td>
		  </tr>
		  </table>
	  </td>
  </tr>
  </table>
</div>              
<br />				