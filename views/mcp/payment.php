  <div style="width:100%">

    <!-- start of main body -->

    <table width="100%" cellspacing="0" cellpadding="0" border="0">

      <tbody>

        <tr>

          <td valign="top" align="center"><table width="98%" cellspacing="0" cellpadding="0" border="0">

              <tbody>

                <tr>

                  <td valign="top" align="center" style="border:#003366 1px solid; padding:15px 0px 0px 0px"><table width="98%" cellspacing="0" cellpadding="0" border="0">

                      <tbody>

                        <tr>

                          <td align="center" style="padding-bottom:5px"><table width="100%" cellspacing="0" cellpadding="0" border="0" style="background:url(<?php echo base_url(); ?>assets/mcp/images/bg.jpg) left top repeat-x;" class="page_caption">
                         
                              <tbody>

                                <tr>

                                  <td width="50%" align="left"><h3><?php echo _('Payment Methods'); ?></h3></td>
								</tr>
								
                              </tbody>

                            </table></td>

                        </tr>
						<?php if(!empty($msg)) : ?>
								<tr>
								<td><div style="padding:10px 15px;color:green;font-weight:bold;" class="success">
								<p style="margin:0px;">///<?php echo _('Success');?> - <?php echo $msg;?> </p></div> </td>
								</tr>
                        <?php endif;?>
                        <tr>

                          <td align="center"><table width="100%" cellspacing="0" cellpadding="0" border="0">

                              <tbody>

                                <tr>

                                  <td style="padding-top:5px"><table width="100%" cellspacing="0" cellpadding="0" border="0" style="background:url(<?php echo base_url(); ?>assets/mcp/images/pink_table_bg.jpg) left repeat; text-align:left;">

                                      <tbody>

                                        <tr>

                                          <td valign="middle" height="20" bgcolor="#003366" align="left" style="border:#003366 1px solid; padding-left:5px" class="whiteSmallBold"><?php echo _('Select Methods:'); ?></td>
										  
                                        </tr>
										<tr>

                                          <td valign="middle" bgcolor="#FFFFFF" colspan="5" style="border:#003366 1px solid">
										     <form id="available_payment_method" method="post" action="">
											 <table width="100%" cellspacing="10" cellpadding="" border="0">
											 <tbody>
											 
                                              <?php foreach($available_methods as $method):?>
			                                    <tr>
			                                   
			                                    <td class="blackMediumNormal"> 
			                                    	<input type="checkbox" 
			                                    			id="<?php echo $method['id'];?>" 
			                                    			name="payment_method[]" 
			                                    			value="<?php echo $method['id'];?>" 
			                                    			<?php if($method['available'] == 1) {echo 'checked';} ?>> 
			                                        <?php echo $method['payment_method'];?> 
			                                    </td>
			                                    
			                                     </tr>
			                                    <?php endforeach;?>
			                                 
                                         	 </tbody>
                                         	 <tfoot>
											     <tr>
											       <td><input style="left:29px;position:relative; margin:10px 0 10px 0;padding: 0 30px;" type="submit" class="btnWhiteBack" value="<?php echo _('Set'); ?>" id="submit" name="submit"></td>
											     </tr>
										  	</tfoot>
                                             </table>
                                              </form>
										  </td>

                                        </tr>
                                    
                                    
                                    
                                   

                                      </tbody>

                                    </table></td>

                                </tr>

                                <tr>

                                  <td height="22" align="right"><div style="float:right; width:80%"> </div></td>

                                </tr>


                                <tr>

                                  <td>&nbsp;</td>

                                </tr>

                              </tbody>

                            </table></td>

                        </tr>

                      </tbody>

                    </table></td>

                </tr>

              </tbody>

            </table></td>

        </tr>

      </tbody>

    </table>

    <!-- end of main body -->

  </div>