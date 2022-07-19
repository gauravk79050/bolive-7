<div style="padding: 0px; display: block;" class="inside">
        <div class="inside">
          <div class="box">
            <div class="table">
              <form action="<?php echo base_url()?>cp/settings/pickupsettings" enctype="multipart/form-data" method="post" id="frm_pickup_settings" name="frm_pickup_settings" onsubmit="return validateForm2()">
                <input type="hidden" value="settings" name="OBS BEstelsysteem - SiteMatic BVBA_REF_VIEW">
                <table cellspacing="0">
                  <tbody>
                    <tr>
                      <td class="textlabel"><?php echo _(' Order Settings')?></td>
                      <td>
                        <table class="override" width="100%" cellspacing="0" cellpadding="0" border="0">
                          <tr>
                            <td width="3%">
                              <input type="checkbox" onClick="samedayPickup();" value="1" class="checkbox" id="same_day_orders_pickup" name="same_day_orders_pickup" <?php if($order_settings && $order_settings[0]->same_day_orders_pickup):?>checked="checked"<?php endif;?>>
                            </td>
                            <td width="30%">
                              <?php echo _('Allow orders for the day itself for ')?>
                            </td>
                            <td width="67%">
                              <?php $allowed_days_pickup = explode("," , $order_settings[0]->allowed_days_pickup); ?>
                              <select id="allowed_days_pickup" name="allowed_days_pickup[]" multiple>
                                <option value="1" <?php if(in_array("1",$allowed_days_pickup)){?>selected="selected"<?php }?>><?php echo _("Monday");?></option>
                                <option value="2" <?php if(in_array("2",$allowed_days_pickup)){?>selected="selected"<?php }?>><?php echo _("Tuesday");?></option>
                                <option value="3" <?php if(in_array("3",$allowed_days_pickup)){?>selected="selected"<?php }?>><?php echo _("Wednesday");?></option>
                                <option value="4" <?php if(in_array("4",$allowed_days_pickup)){?>selected="selected"<?php }?>><?php echo _("Thursday");?></option>
                                <option value="5" <?php if(in_array("5",$allowed_days_pickup)){?>selected="selected"<?php }?>><?php echo _("Friday");?></option>
                                <option value="6" <?php if(in_array("6",$allowed_days_pickup)){?>selected="selected"<?php }?>><?php echo _("Saturday");?></option>
                                <option value="7" <?php if(in_array("7",$allowed_days_pickup)){?>selected="selected"<?php }?>><?php echo _("Sunday");?></option>
                              </select>
                            </td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                    <tr>
                      <td class="textlabel">&nbsp;</td>
                      <td style="padding-left:35px"><table border="0" class="override" id="samedayPickup" style="display:none">
                          <tbody>
                            <tr>
                                <td width="280" align="left"><?php echo _('minimum time between order and pickup')?></td>
                                <td>
                                  <?php $hr = 0; $min = 0;?>
                                  <select style="margin-bottom:0px" class="select" type="select" id="time_diff_pickup" name="time_diff_pickup">
                                      <option value="0">-- <?php echo _('Select')?> --</option>
                                      <?php while(!($hr == 5 && $min == 30)){?>
                                      <?php $min = $min+30;?>
                                      <?php if($min == 60){ $hr = $hr + 1; $min = 0;}?>
                                      <?php $hr_str = ((strlen($hr) == 1)?"0".$hr:$hr);?>
                                      <?php $min_str = ((strlen($min) == 1)?"0".$min:$min);?>
                                      <option value="<?php echo $hr_str.':'.$min_str;?>" <?php if($order_settings && $order_settings[0]->time_diff_pickup == $hr_str.":".$min_str):?>selected=""<?php endif;?>><?php echo $hr_str;?> : <?php echo $min_str;?></option>
                                      <?php }?>
                                  </select>
                              </td>
                            </tr>
                          </tbody>
                        </table></td>
                    </tr>
                    <tr>
                      <td class="textlabel">&nbsp;</td>
                      <td style="padding-left:35px"><table border="0" class="override" id="samedayPickupTime" style="display:none">
                          <tbody>
                            <tr>
                                <td width="242" align="left"><?php echo _('Customers can order the same day till')?></td>
                                <td width="38"><?php echo _('from');?></td>
                                <td width="109">
	                                 <?php $hr = 0; $min = 0;?>
	                                 <select style="margin-bottom:0px" class="select" type="select" id="same_day_time_pickup_start" name="same_day_time_pickup_start">
	                                      <option value="0">-- <?php echo _('Select')?> --</option>
	                                      <?php while(!($hr == 23 && $min == 60)){?>
	                                      <?php if($min == 60){ $hr = $hr + 1; $min = 0;}?>
	                                      <?php $hr_str = ((strlen($hr) == 1)?"0".$hr:$hr);?>
	                                      <?php $min_str = ((strlen($min) == 1)?"0".$min:$min);?>
	                                      <option value="<?php echo $hr_str.':'.$min_str;?>" <?php if($order_settings && $order_settings[0]->same_day_time_pickup_start == $hr_str.":".$min_str):?>selected=""<?php endif;?>><?php echo $hr_str;?> : <?php echo $min_str;?></option>
	                                      <?php $min = $min+30;?>
	                                      <?php }?>
	                                  </select>
                            	</td>
                            	<td width="38"><?php echo _('to');?></td>
                            	<td>
	                                 <?php $hr = 0; $min = 0;?>
	                                 <select style="margin-bottom:0px" class="select" type="select" id="same_day_time_pickup" name="same_day_time_pickup">
	                                      <option value="0">-- <?php echo _('Select')?> --</option>
	                                      <?php while(!($hr == 23 && $min == 60)){?>
	                                      <?php if($min == 60){ $hr = $hr + 1; $min = 0;}?>
	                                      <?php $hr_str = ((strlen($hr) == 1)?"0".$hr:$hr);?>
	                                      <?php $min_str = ((strlen($min) == 1)?"0".$min:$min);?>
	                                      <option value="<?php echo $hr_str.':'.$min_str;?>" <?php if($order_settings && $order_settings[0]->same_day_time_pickup == $hr_str.":".$min_str):?>selected=""<?php endif;?>><?php echo $hr_str;?> : <?php echo $min_str;?></option>
	                                      <?php $min = $min+30;?>
	                                      <?php }?>
	                                  </select>
                            	</td>
                            </tr>
                          </tbody>
                        </table></td>
                    </tr>
                    <tr>
                     	<td class="textlabel"><?php echo _('Exceptions')?></td>
                     	<td>
                     	  <?php $allow_order_date_array = explode("#",$order_settings[0]->allow_order_date);?>
	                      <?php $date_time_diff_pickup_array = explode("#",$order_settings[0]->date_time_diff_pickup);?>
	                      <?php $same_date_start_time_pickup_array = explode("#",$order_settings[0]->same_date_start_time_pickup);?>
	                      <?php $same_date_end_time_pickup_array = explode("#",$order_settings[0]->same_date_end_time_pickup);?>
	                      <?php $total_rows_allow_order_date = count($allow_order_date_array);?>
	                      <?php if (!empty($order_settings[0]->allow_order_date)){?>
	                       		<table border="0" class="override">
	                       		 <?php for($i=0;$i<$total_rows_allow_order_date;$i++){?>
		                          <tbody>
			                          	<tr>
		                               		<td>
												<div style="float:left"><input type="text" class="text" readonly="readonly" name="same_day_excep[]" id="start_date_exp_<?php echo $i;?>" value="<?php echo $allow_order_date_array[$i];?>"></div>
									  			<div style="float:left"><input type="button" value="..." onclick="displayCalendar(document.frm_pickup_settings.start_date_exp_<?php echo $i;?>,'dd/mm/yyyy',this)" ></div>
											</td>
											<td>
					                        	<img width="18" border="0" onClick="javascript:addNewbodyToTableexp(this)" src="<?php echo base_url(); ?>assets/cp/images/add.gif">
				                        		<img width="18" border="0" onClick="javascript:deletebodyFromTableexp(this)" src="<?php echo base_url(); ?>assets/cp/images/delete.gif">
					                        </td>
			                          	</tr>
			                          	<tr>
			                          		<td width="238" align="left" style="padding-left: 27px" colspan="2"><?php echo _('minimum time between order and pickup')?></td>
			                                <td>
			                                  <?php $hr = 0; $min = 0;?>
			                                  <select style="margin-bottom:0px" class="select" type="select" name="date_time_diff_pickup[]">
			                                      <option value="0">-- <?php echo _('Select')?> --</option>
			                                      <?php while(!($hr == 5 && $min == 30)){?>
			                                      <?php $min = $min+30;?>
			                                      <?php if($min == 60){ $hr = $hr + 1; $min = 0;}?>
			                                      <?php $hr_str = ((strlen($hr) == 1)?"0".$hr:$hr);?>
			                                      <?php $min_str = ((strlen($min) == 1)?"0".$min:$min);?>
			                                      <option value="<?php echo $hr_str.':'.$min_str;?>" <?php if($date_time_diff_pickup_array && $date_time_diff_pickup_array[$i] == $hr_str.":".$min_str):?>selected=""<?php endif;?>><?php echo $hr_str;?> : <?php echo $min_str;?></option>
			                                      <?php }?>
			                                  </select>
			                              </td>
			                          	</tr>
			                          	<tr>
			                                <td width="244" align="left" style="padding-left: 27px"><?php echo _('Customers can order the same day till')?></td>
			                                <td width="42"><?php echo _('from');?></td>
			                                <td width="106">
				                                 <?php $hr = 0; $min = 0;?>
				                                 <select style="margin-bottom:0px;margin-top:4px;" class="select" type="select"  name="same_date_time_pickup_start_exp[]">
				                                      <option value="0">-- <?php echo _('Select')?> --</option>
				                                      <?php while(!($hr == 23 && $min == 60)){?>
				                                      <?php if($min == 60){ $hr = $hr + 1; $min = 0;}?>
				                                      <?php $hr_str = ((strlen($hr) == 1)?"0".$hr:$hr);?>
				                                      <?php $min_str = ((strlen($min) == 1)?"0".$min:$min);?>
				                                      <option value="<?php echo $hr_str.':'.$min_str;?>" <?php if($same_date_start_time_pickup_array && $same_date_start_time_pickup_array[$i] == $hr_str.":".$min_str):?>selected=""<?php endif;?>><?php echo $hr_str;?> : <?php echo $min_str;?></option>
				                                      <?php $min = $min+30;?>
				                                      <?php }?>
				                                  </select>
			                            	</td>
			                            	<td width="38"><?php echo _('to');?></td>
			                            	<td>
				                                 <?php $hr = 0; $min = 0;?>
				                                 <select style="margin-bottom:0px" class="select" type="select"  name="same_date_time_pickup_end_exp[]">
				                                      <option value="0">-- <?php echo _('Select')?> --</option>
				                                      <?php while(!($hr == 23 && $min == 60)){?>
				                                      <?php if($min == 60){ $hr = $hr + 1; $min = 0;}?>
				                                      <?php $hr_str = ((strlen($hr) == 1)?"0".$hr:$hr);?>
				                                      <?php $min_str = ((strlen($min) == 1)?"0".$min:$min);?>
				                                      <option value="<?php echo $hr_str.':'.$min_str;?>" <?php if($same_date_end_time_pickup_array && $same_date_end_time_pickup_array[$i] == $hr_str.":".$min_str):?>selected=""<?php endif;?>><?php echo $hr_str;?> : <?php echo $min_str;?></option>
				                                      <?php $min = $min+30;?>
				                                      <?php }?>
				                                  </select>
			                            	</td>
			                            </tr>
		                            </tbody>
		                           <?php }?>
		                            <tbody class="img_hide" style="display: none;">
			                       		<tr>
			                       		<td>
			                     	 		<img width="18" border="0" onClick="javascript:addNewbodyToTableexp(this)" src="<?php echo base_url(); ?>assets/cp/images/add.gif">
			                     	 	</td>
			                     	 	</tr>
			                      </tbody>
		                        </table>
		                 <?php }else{?>
		                 	<table class="override" border="0">
			                    <tbody class="img_hide">
		                       		<tr>
			                       		<td>
				                     	 	<img width="18" border="0" onClick="javascript:addNewbodyToTableexp(this)" src="<?php echo base_url(); ?>assets/cp/images/add.gif">
				                     	</td>
			                     	</tr>
			                    </tbody>
		                   </table>
		                 <?php }?>
		                 <input type="hidden" value="<?php if (isset($total_rows_allow_order_date)){ echo $total_rows_allow_order_date; }else{ ?>0<?php } ?>" name="same_date_day_order_sett_count" id="same_date_day_order_sett_count">
		                 </td>
                    </tr>

                    <tr>
                      <td class="textlabel">&nbsp;</td>
                      <td><table width="100%" cellspacing="0" cellpadding="0" border="0" class="override">
                          <tbody>
                            <?php //$custom_pickup_array = explode("#",$order_settings[0]->customise_pickup); //print_r($custom_pickup_array);?>
                            <?php $custom_time_pickup_array = explode("#",$order_settings[0]->custom_time_pickup);?>
                            <?php $custom_days_pickup_array = explode("#",$order_settings[0]->custom_days_pickup);?>
                            <?php for($i = 0 ; $i < 7 ; $i++){?>
                            <tr style="height: 30px;">
                                <td width="2%"><?php /*?><input type="checkbox" value="<?php echo $i+1;?>" class="checkbox" id="customise_pickup" name="customise_pickup[]"<?php if(isset($custom_pickup_array[$i]) && $custom_pickup_array[$i] == $i+1):?>checked="checked"<?php endif;?> /><?php */?></td>
                                <td width="23%">&nbsp;&nbsp;<?php echo _('If the customer orders before')?></td>
                                <td width="10%">&nbsp;&nbsp;
                                <?php
                                if(isset($_COOKIE['locale']) && $_COOKIE['locale'] == 'nl_NL')
			                  	{
			                     if( $i == 0 ){
			                        echo 'Maandag';
			                     }
			                     if( $i == 1 )
			                      echo 'Dinsdag';
			                     if( $i == 2 )
			                      echo 'Woensdag';
			                     if( $i == 3 )
			                       echo 'Donderdag';
			                     if( $i == 4 )
			                      echo 'Vrijdag';
			                     if( $i == 5 )
			                      echo 'Zaterdag';
			                     if( $i == 6 )
			                      echo 'Zondag';
			                  }elseif(isset($_COOKIE['locale']) && $_COOKIE['locale'] == 'fr_FR')
			                  {
			                    if( $i == 0 )
			                      echo 'Lundi';
			                    if( $i == 1 )
			                      echo 'Mardi';
			                    if( $i == 2 )
			                      echo 'Mercredi';
			                    if( $i == 3 )
			                      echo 'Jeudi';
			                    if( $i == 4 )
			                      echo 'Vendredi';
			                    if( $i == 5 )
			                      echo 'Samedi';
			                    if( $i == 6 )
			                      echo 'dimanche';
			                  }else{
								if( $i == 0 )
									echo 'Monday';
								if( $i == 1 )
									echo 'Tuesday';
								if( $i == 2 )
									echo 'Wednesday';
								if( $i == 3 )
									echo 'Thursday';
								if( $i == 4 )
									echo 'friday';
								if( $i == 5 )
									echo 'Saturday';
								if( $i == 6 )
									echo 'Sunday';
			                  }
			                ?>
                                </td>
                                <td width="15%">
                                  <?php $hr = 0; $min = 0;?>
                                  <select style="margin-bottom:0px" class="select" type="select" id="custom_time_pickup[]" name="custom_time_pickup[]">
                                      <option value="0">-- <?php echo _('Select')?> --</option>
                                      <?php while(!($hr == 23 && $min == 60)){?>
                                      <?php if($min == 60){ $hr = $hr + 1; $min = 0;}?>
                                      <?php $selected = false;?>
                                      <?php
                                        $hr_str = ((strlen($hr) == 1)?"0".$hr:$hr);
                                        $min_str = ((strlen($min) == 1)?"0".$min:$min);
                                        if($order_settings && isset($custom_time_pickup_array[$i])){
				                        if($min_str == 0 && ($custom_time_pickup_array[$i] == $hr_str.":".$min_str || $custom_time_pickup_array[$i] == $hr_str)){
				                          $selected = true;
				                        }elseif($custom_time_pickup_array[$i] == $hr_str.":".$min_str){
				                          $selected = true;
				                        }
				                      }
                                      ?>
                                      <option value="<?php echo $hr_str.':'.$min_str;?>" <?php if($selected){?>selected=""<?php }?>><?php echo $hr_str;?> : <?php echo $min_str;?></option>
                                      <?php $min = $min+30;?>
                                      <?php }?>
                                  </select>
                                </td>
                                <td width="40%"><?php echo _('hours, he can pick the products after')?></td>
                                <td width="10%">
                                  <?php $day = 1;?>
                                  <select style="margin-bottom:0px" class="select" type="select" id="custom_days_pickup[]" name="custom_days_pickup[]">
                                      <option value="0">-- <?php echo _('Select ')?>--</option>
                                      <option value="<?php echo $day;?>" <?php if($order_settings && isset($custom_days_pickup_array[$i]) && $custom_days_pickup_array[$i] == $day):?>selected=""<?php endif;?>><?php echo _('Next day')?></option>
                                      <?php $day++;?>
                                      <?php while($day < 14){?>
                                      <option value="<?php echo $day;?>" <?php if($order_settings && isset($custom_days_pickup_array[$i]) && $custom_days_pickup_array[$i] == $day):?>selected=""<?php endif;?>><?php echo $day.' '._('Days')?></option>
                                      <?php $day++?>
                                      <?php }?>
                                  </select>
                              </td>
                            </tr>
                            <?php }?>
                            <tr><td colspan=6>&nbsp;</td></tr>
                            <tr>
                              <td ><input type="checkbox" value="1" class="checkbox" id="customise_pickup_holiday" name="custom_pickup_holiday"<?php if($order_settings && $order_settings[0]->custom_pickup_holiday ):?>checked="checked"<?php endif;?> /></td>
                              <td colspan=3><?php echo _("If holiday or closing day overule above settings with");?></td>
                              <td colspan=2>
                                <?php $day = 1;?>
                                <select style="margin-bottom:0px" class="select" type="select" id="custom_holidays_pickup" name="custom_holidays_pickup">
                                      <option value="0">-- <?php echo _('Select ')?>--</option>
                                      <option value="<?php echo $day;?>" <?php if($order_settings && $order_settings[0]->custom_holidays_pickup == $day):?>selected=""<?php endif;?>><?php echo _('1')?></option>
                                      <?php $day++;?>
                                      <?php while($day < 14){?>
                                      <option value="<?php echo $day;?>" <?php if($order_settings && $order_settings[0]->custom_holidays_pickup == $day):?>selected=""<?php endif;?>><?php echo $day.' '._('Days')?></option>
                                      <?php $day++?>
                                      <?php }?>
                                  </select>
                                </td>
                            </tr>
                          </tbody>
                        </table></td>
                    </tr>
                    <tr>
                        <td class="textlabel">&nbsp;</td>
                        <td>
                          <table border="0" class="override">
                              <tbody>
                                <tr>
                                    <td width="250px" align="left"><strong> <?php echo _('Opening Hours')?>:</strong><?php echo _('ALL DAYS means from')?>&nbsp;</td>
                                    <td width="150">
                                      <?php $hr = 0; $min = 0;?>
                                      <select style="margin-bottom:0px" class="select" type="select" id="all_day_starttime_p" name="all_day_starttime_p">
                                          <option value="0">--<?php echo _('Select')?>--</option>
                                          <?php while(!($hr == 23 && $min == 60)){?>
                                        <?php if($min == 60){ $hr = $hr + 1; $min = 0;}?>
                                        <?php $selected = false;?>
                                        <?php
                                          $hr_str = ((strlen($hr) == 1)?"0".$hr:$hr);
                                          $min_str = ((strlen($min) == 1)?"0".$min:$min);
                                          if($order_settings){
				                            if($min_str == 0 && ($order_settings[0]->all_day_starttime_p == $hr_str.":".$min_str || $order_settings[0]->all_day_starttime_p == $hr_str)){
				                              $selected = true;
				                            }elseif($order_settings[0]->all_day_starttime_p == $hr_str.":".$min_str){
				                              $selected = true;
				                            }
				                          }
                                      ?>
                                        <option value="<?php echo $hr_str.':'.$min_str;?>" <?php if($selected){?>selected=""<?php }?>><?php echo $hr_str;?> : <?php echo $min_str;?></option>
                                        <?php $min = $min+30;?>
                                        <?php }?>
                                      </select>
                                    </td>
                                    <td width="30" align="center">&nbsp;&nbsp;<?php echo _('to')?>&nbsp;</td>
                                    <td>
                                      <?php $hr = 0; $min = 0;?>
                                      <select style="margin-bottom:0px" class="select" type="select" id="all_day_endtime_p" name="all_day_endtime_p">
                                          <option value="0">-- <?php echo _('Select')?>--</option>
                                          <?php while(!($hr == 23 && $min == 60)){?>
                                        <?php if($min == 60){ $hr = $hr + 1; $min = 0;}?>
                                        <?php $selected = false;?>
                                        <?php
                                          $hr_str = ((strlen($hr) == 1)?"0".$hr:$hr);
                                          $min_str = ((strlen($min) == 1)?"0".$min:$min);
                                          if($order_settings){
				                            if($min_str == 0 && ($order_settings[0]->all_day_endtime_p == $hr_str.":".$min_str || $order_settings[0]->all_day_endtime_p == $hr_str)){
				                              $selected = true;
				                            }elseif($order_settings[0]->all_day_starttime_p == $hr_str.":".$min_str){
				                              $selected = true;
				                            }
				                          }
				                        ?>
                                        <option value="<?php echo $hr_str.':'.$min_str;?>" <?php if($selected){?>selected=""<?php }?>><?php echo $hr_str;?> : <?php echo $min_str;?></option>
                                        <?php $min = $min+30;?>
                                        <?php }?>
                                      </select>
                                    </td>
                                </tr>
                              </tbody>
                          </table>
                        </td>
                    </tr>

                    <tr>
                      <td class="textlabel"><?php echo _('Exceptions')?>
                      </td>
                      <td>

	                      <?php $custom_days_pickup_array = explode("#",$custom_order_settings[0]->custom_days_pickup);?>
	                      <?php $custom_date_time_pickup_array = explode("#",$custom_order_settings[0]->custom_date_time_pickup);?>
	                      <?php $custom_date_days_pickup_array = explode("#",$custom_order_settings[0]->custom_date_days_pickup);?>
	                      <?php $total_rows_custom_pickup = count($custom_date_days_pickup_array);?>
	                      <?php if (!empty($custom_order_settings[0]->custom_days_pickup)){?>
	                       <table border="0" class="override">
		                      <?php for($i=0;$i<$total_rows_custom_pickup;$i++){?>
		                          <tbody>
		                          	<tr>
	                               		<td colspan="2">
											<div style="float:left"><input type="text" class="text" readonly="readonly" name="custom_new_days_pickup[]" id="start_date_<?php echo $i;?>" value="<?php echo $custom_days_pickup_array[$i]?>"></div>
								  			<div style="float:left"><input type="button" value="..." onclick="displayCalendar(document.frm_pickup_settings.start_date_<?php echo $i;?>,'dd/mm/yyyy',this)" ></div>
										</td>
										<td>
				                        	<img width="18" border="0" onClick="javascript:addNewbodyToTable(this)" src="<?php echo base_url(); ?>assets/cp/images/add.gif">
			                        		<img width="18" border="0" onClick="javascript:deletebodyFromTable(this)" src="<?php echo base_url(); ?>assets/cp/images/delete.gif">
				                        </td>
		                          	</tr>
		                          	<tr>
		                          	<td width="23%">&nbsp;&nbsp;<?php echo _('If the customer orders before')?></td>
	                                <td width="10%"></td>
		                                <td width="15%">
		                                  <?php $hr = 0; $min = 0;?>
		                                  <select style="margin-bottom:0px" class="select" type="select" name="custom_date_time_pickup[]">
		                                      <option value="0">--<?php echo _('Select')?>--</option>
		                                      <?php while(!($hr == 23 && $min == 60)){?>
		                                      <?php if($min == 60){ $hr = $hr + 1; $min = 0;}?>
		                                      <?php $selected = false;?>
		                                      <?php
		                                        $hr_str = ((strlen($hr) == 1)?"0".$hr:$hr);
		                                        $min_str = ((strlen($min) == 1)?"0".$min:$min);
		                                        if($custom_order_settings && isset($custom_date_time_pickup_array[$i])){
							                        if($min_str == 0 && ($custom_date_time_pickup_array[$i] == $hr_str.":".$min_str || $custom_date_time_pickup_array[$i] == $hr_str)){
							                          $selected = true;
							                        }elseif($custom_date_time_pickup_array[$i] == $hr_str.":".$min_str){
							                          $selected = true;
							                        }
							                      }
		                                      ?>

		                                      <option value="<?php echo $hr_str.':'.$min_str;?>" <?php if($selected){?>selected=""<?php }?>><?php echo $hr_str;?> : <?php echo $min_str;?></option>
		                                      <?php $min = $min+30;?>
		                                      <?php }?>
		                                  </select>
		                                </td>
		                                <td width="40%"><?php echo _('hours, he can pick the products after')?></td>
		                                <td width="10%">
		                                  <?php $day = 1;?>
		                                  <select style="margin-bottom:0px" class="select" type="select" name="custom_date_days_pickup[]">
		                                      <option value="0">-- <?php echo _('Select ')?>--</option>
		                                      <option value="<?php echo $day;?>" <?php if($custom_order_settings && isset($custom_date_days_pickup_array[$i]) && $custom_date_days_pickup_array[$i] == $day):?>selected=""<?php endif;?>><?php echo _('Next day')?></option>
		                                      <?php $day++;?>
		                                      <?php while($day < 14){?>
		                                      <option value="<?php echo $day;?>" <?php if($custom_order_settings && isset($custom_date_days_pickup_array[$i]) && $custom_date_days_pickup_array[$i] == $day):?>selected=""<?php endif;?>><?php echo $day.' '._('Days')?></option>
		                                      <?php $day++?>
		                                      <?php }?>
		                                  </select>
		                              </td>
		                          	</tr>
		                          </tbody>
		                            <?php }?>
		                            <tbody class="img_hide" style="display: none;">
			                       		<tr>
			                       		<td>
			                     	 		<img width="18" border="0" onClick="javascript:addNewbodyToTable(this)" src="<?php echo base_url(); ?>assets/cp/images/add.gif">
			                     	 	</td>
			                     	 	</tr>
			                      </tbody>
		                           </table>
		                       <?php }else{?>
		                       <table class="override" border="0">
			                       <tbody class="img_hide">
			                       		<tr>
			                       		<td>
			                     	 		<img width="18" border="0" onClick="javascript:addNewbodyToTable(this)" src="<?php echo base_url(); ?>assets/cp/images/add.gif">
			                     	 	</td>
			                     	 	</tr>
			                      </tbody>
		                      </table>
		                       <?php }?>
	                          <input type="hidden" value="<?php if (isset($total_rows_custom_pickup)){ echo $total_rows_custom_pickup; }else{ ?>0<?php } ?>" name="custom_order_sett_count" id="custom_order_sett_count">
                        </td>
                    </tr>

                    <tr>
                      <td width="20%" class="textlabel"><?php echo _('Order restriction time frame (in minutes)')?></td>
                        <td>
                          <?php $day = 5;?>
                          <select style="margin-bottom:0px;" class="select" type="select" id="time_restriction_p" name="time_restriction_p" >
                          <option value="0">-- <?php echo _('Select')?> --</option>
                          <?php while($day < 55){?>
                          <option value="<?php echo $day;?>" <?php if($order_settings && $order_settings[0]->time_restriction_p == $day):?>selected="selected"<?php endif;?>><?php echo $day;?></option>
                          <?php $day = $day + 5;?>
                          <?php }?>
                          </select>
                        </td>
                    </tr>

                    <tr>
                        <td width="20%" class="textlabel">&nbsp;</td>
                        <td><strong class="textlabel"><?php echo _('Pickup hours')?></strong></td>
                    </tr>

                    <tr>
                        <td class="textlabel">&nbsp;</td>
                        <td>
                          <table width="100%" cellspacing="0" cellpadding="0" border="0" class="override">
                              <tbody>
                                <?php if($pickup_settings):?>
                                <?php foreach($pickup_settings as $pickup_setting):?>

                    <?php if($pickup_setting->pickup1!="NONE"&&$pickup_setting->pickup1!="ALL DAY"&&$pickup_setting->pickup1!="CLOSED"):?>
                                  <script type="text/javascript">
                      jQuery('document').ready(function(){
                        show_hide('<?php echo $pickup_setting->day_id?>','pickup',this.value,'p1');
                      });
                    </script>
                     <?php endif;?>
                  <?php

                  $day_name = $pickup_setting->name;

		                  if(isset($_COOKIE['locale']) && $_COOKIE['locale'] == 'nl_NL')
		                  {
		                      if( $day_name == 'Monday' )
		                      $day_name = 'Maandag';
		                     if( $day_name == 'Tuesday' )
		                     $day_name = 'Dinsdag';
		                     if( $day_name == 'Wednesday' )
		                     $day_name = 'Woensdag';
		                     if( $day_name == 'Thursday' )
		                     $day_name = 'Donderdag';
		                     if( $day_name == 'Friday' )
		                     $day_name = 'Vrijdag';
		                     if( $day_name == 'Saturday' )
		                     $day_name = 'Zaterdag';
		                     if( $day_name == 'Sunday' )
		                     $day_name = 'Zondag';
		                  }
		                  if(isset($_COOKIE['locale']) && $_COOKIE['locale'] == 'fr_FR')
		                  {
		                  	if( $day_name == 'Monday' )
		                  		$day_name = 'Lundi';
		                  	if( $day_name == 'Tuesday' )
		                  		$day_name = 'Mardi';
		                  	if( $day_name == 'Wednesday' )
		                  		$day_name = 'Mercredi';
		                  	if( $day_name == 'Thursday' )
		                  		$day_name = 'Jeudi';
		                  	if( $day_name == 'Friday' )
		                  		$day_name = 'Vendredi';
		                  	if( $day_name == 'Saturday' )
		                  		$day_name = 'Samedi';
		                  	if( $day_name == 'Sunday' )
		                  		$day_name = 'dimanche';
		                  }
		                  ?>

                                <tr>
                                    <td width="100" height="30" style="text-align:right;padding-right:20px"><strong><?php echo $day_name; ?></strong></td>
                                    <td>
                                      <?php $hr = 0; $min = 0;?>
                                      <select onChange="show_hide('<?php echo $pickup_setting->day_id?>','pickup',this.value,'p1');" style="margin-bottom:0px" class="select" type="select" id="p1[<?php echo $pickup_setting->day_id ?>]" name="p1[<?php echo $pickup_setting->day_id ?>]">
                                          <option value="0" <?php if($pickup_setting->pickup1=="0"):?>selected=""<?php endif;?>>-- <?php echo _('Select')?>--</option>
                                          <option value="ALL DAY" <?php if($pickup_setting->pickup1=="ALL DAY"):?>selected=""<?php endif;?>><?php echo _('ALL DAY')?></option>
                                          <option value="CLOSED" <?php if($pickup_setting->pickup1=="CLOSED"):?>selected=""<?php endif;?>><?php echo _('CLOSED')?></option>
                                          <option value="NONE" <?php if($pickup_setting->pickup1=="NONE"):?>selected=""<?php endif;?>><?php echo _('NONE')?></option>
                                          <?php while(!($hr == 23 && $min == 60)){?>
                                        <?php if($min == 60){ $hr = $hr + 1; $min = 0;}?>
                                        <?php $selected = false;?>
                                        <?php
                                          $hr_str = ((strlen($hr) == 1)?"0".$hr:$hr);
                                          $min_str = ((strlen($min) == 1)?"0".$min:$min);
                          if($min_str == 0 && ($pickup_setting->pickup1 == $hr_str.":".$min_str || $pickup_setting->pickup1 == $hr_str)){
                            $selected = true;
                          }elseif($pickup_setting->pickup1 == $hr_str.":".$min_str){
                            $selected = true;
                          }
                                        ?>
                                        <option value="<?php echo $hr_str.':'.$min_str;?>" <?php if($selected){?>selected=""<?php }?>><?php echo $hr_str;?> : <?php echo $min_str;?></option>
                                        <?php $min = $min+30;?>
                                        <?php }?>
                                      </select>
                                    </td>
                                    <td>
                                      <table border="0" class="override">
                                          <tbody>
                                            <tr style="display:none" id="pickup_<?php echo $pickup_setting->day_id?>">
                                                <td style="text-align:center"><strong> <?php echo _('to')?>&nbsp;&nbsp;&nbsp;</strong></td>
                                                <td>
                                                  <?php $hr = 0; $min = 0;?>
                                                  <select style="margin-bottom:0px" class="select" type="select" id="p2[<?php echo $pickup_setting->day_id?>]" name="p2[<?php echo $pickup_setting->day_id?>]">
                                                  <option value="0"  <?php if($pickup_setting->pickup2=="0"):?>selected=""<?php endif;?>>-- <?php echo _('Select')?> --</option>
                                <option value="NONE"  <?php if($pickup_setting->pickup2=="NONE"):?>selected=""<?php endif;?>><?php echo _('NONE')?></option>
                                                  <?php while(!($hr == 23 && $min == 60)){?>
                                              <?php if($min == 60){ $hr = $hr + 1; $min = 0;}?>
                                              <?php $selected = false;?>
                                              <?php
                                                $hr_str = ((strlen($hr) == 1)?"0".$hr:$hr);
                                                $min_str = ((strlen($min) == 1)?"0".$min:$min);
                                  if($min_str == 0 && ($pickup_setting->pickup2 == $hr_str.":".$min_str || $pickup_setting->pickup2 == $hr_str)){
                                    $selected = true;
                                  }elseif($pickup_setting->pickup2 == $hr_str.":".$min_str){
                                    $selected = true;
                                  }
                                              ?>
                                              <option value="<?php echo $hr_str.':'.$min_str;?>" <?php if($selected){?>selected=""<?php }?>><?php echo $hr_str;?> : <?php echo $min_str;?></option>
                                              <?php $min = $min+30;?>
                                              <?php }?>
                                                  </select>
                                                </td>
                                                <td><strong>&nbsp;&nbsp;&nbsp;<?php echo _('and')?>&nbsp;&nbsp;&nbsp;</strong></td>
                                                <td>
                                                  <?php $hr = 0; $min = 0;?>
                                                  <select style="margin-bottom:0px" class="select" type="select" id="p3[<?php echo $pickup_setting->day_id?>]" name="p3[<?php echo $pickup_setting->day_id?>]">
                                                    <option value="0" <?php if($pickup_setting->pickup3=="0"):?>selected=""<?php endif;?>>-- <?php echo _('Select')?>--</option>
                                                    <option value="NONE" <?php if($pickup_setting->pickup3=="NONE"):?>selected=""<?php endif;?>><?php echo _('NONE')?></option>
                                                    <?php while(!($hr == 23 && $min == 60)){?>
                                              <?php if($min == 60){ $hr = $hr + 1; $min = 0;}?>
                                              <?php $selected = false;?>
                                              <?php
                                                $hr_str = ((strlen($hr) == 1)?"0".$hr:$hr);
                                                $min_str = ((strlen($min) == 1)?"0".$min:$min);
                                  if($min_str == 0 && ($pickup_setting->pickup3 == $hr_str.":".$min_str || $pickup_setting->pickup3 == $hr_str)){
                                    $selected = true;
                                  }elseif($pickup_setting->pickup3 == $hr_str.":".$min_str){
                                    $selected = true;
                                  }
                                              ?>
                                              <option value="<?php echo $hr_str.':'.$min_str;?>" <?php if($selected){?>selected=""<?php }?>><?php echo $hr_str;?> : <?php echo $min_str;?></option>
                                              <?php $min = $min+30;?>
                                              <?php }?>
                                                  </select>
                                                </td>
                                                <td><strong>&nbsp;&nbsp;&nbsp;<?php echo _('to')?> &nbsp;&nbsp;&nbsp;</strong></td>
                                                <td>
                                                  <?php $hr = 0; $min = 0;?>
                                                  <select style="margin-bottom:0px" class="select" type="select" id="p4[<?php echo $pickup_setting->day_id?>]" name="p4[<?php echo $pickup_setting->day_id?>]">
                                                    <option value="0" <?php if($pickup_setting->pickup4=="0"):?>selected=""<?php endif;?>>--<?php echo _(' Select')?>--</option>
                                                  <option value="NONE" <?php if($pickup_setting->pickup4=="NONE"):?>selected=""<?php endif;?>><?php echo _('NONE')?></option>
                                                    <?php while(!($hr == 23 && $min == 60)){?>
                                              <?php if($min == 60){ $hr = $hr + 1; $min = 0;}?>
                                              <?php $selected = false;?>
                                              <?php
                                                $hr_str = ((strlen($hr) == 1)?"0".$hr:$hr);
                                                $min_str = ((strlen($min) == 1)?"0".$min:$min);
                                  if($min_str == 0 && ($pickup_setting->pickup4 == $hr_str.":".$min_str || $pickup_setting->pickup4 == $hr_str)){
                                    $selected = true;
                                  }elseif($pickup_setting->pickup4 == $hr_str.":".$min_str){
                                    $selected = true;
                                  }
                                              ?>
                                              <option value="<?php echo $hr_str.':'.$min_str;?>" <?php if($selected){?>selected=""<?php }?>><?php echo $hr_str;?> : <?php echo $min_str;?></option>
                                              <?php $min = $min+30;?>
                                              <?php }?>
                                                  </select>
                                                </td>
                                            </tr>
                                          </tbody>
                                      </table>
                                    </td>
                                </tr>
                                <?php endforeach;?>
                                <?php endif;?>
                              </tbody>
                          </table>
                        </td>
                    </tr>
                     <tr>
                      <td class="textlabel"><?php echo _('Exceptions')?></td>
                      <td>

                       <?php $custom_pickup_days_array = explode("#",$custom_pickup_settings[0]->pickup_days);?>
                       <?php $custom_pickup1_pickup_array = explode("#",$custom_pickup_settings[0]->pickup1);?>
                       <?php $custom_pickup2_pickup_array = explode("#",$custom_pickup_settings[0]->pickup2);?>
                       <?php $custom_pickup3_pickup_array = explode("#",$custom_pickup_settings[0]->pickup3);?>
                       <?php $custom_pickup4_pickup_array = explode("#",$custom_pickup_settings[0]->pickup4);?>

                      <?php $total_custom_pickup_days_array = count($custom_pickup_days_array);?>
                      <?php if (!empty($custom_pickup_settings[0]->pickup_days)){?>
                       <?php $custom_pickup_settings = $custom_pickup_settings[0];?>
                      <table border="0" class="override">
                      <?php for($i=0;$i<$total_custom_pickup_days_array;$i++){?>
	                          <tbody>
	                          	<tr>
                               		<td style="padding-top: 10px;width: 259px">
										<div style="float:left"><input type="text" class="text" readonly="readonly" name="custom_new_pickup_hours[]" id="start_date_or_<?php echo $i;?>" value="<?php echo $custom_pickup_days_array[$i];?>"></div>
					  					<div style="float:left"><input type="button" value="..." onclick="displayCalendar(document.frm_pickup_settings.start_date_or_<?php echo $i;?>,'dd/mm/yyyy',this)"></div>
									</td>
									<td>
	                        			<img width="18" border="0" onClick="javascript:addNewpickday(this)" src="<?php echo base_url(); ?>assets/cp/images/add.gif">
                        				<img width="18" border="0" onClick="javascript:deleteNewpickday(this)" src="<?php echo base_url(); ?>assets/cp/images/delete.gif">
		                        	</td>
                          		</tr>
                          		<tr>
                          			<td>
                                      <?php $hr = 0; $min = 0;?>
                                      <select onChange="show_hide_custom_pickup('<?php echo $i;?>','pickup',this.value,this);" style="margin-bottom:0px" class="select" type="select" name="q1[]">
                                          <option value="0" <?php if($custom_pickup1_pickup_array[$i] == '0'):?>selected=""<?php endif;?>>-- <?php echo _('Select')?>--</option>
                                          <option value="ALL DAY" <?php if($custom_pickup1_pickup_array[$i]=="ALL DAY"):?>selected=""<?php endif;?>><?php echo _('ALL DAY')?></option>
                                          <option value="CLOSED" <?php if($custom_pickup1_pickup_array[$i]=="CLOSED"):?>selected=""<?php endif;?>><?php echo _('CLOSED')?></option>
                                          <option value="NONE" <?php if($custom_pickup1_pickup_array[$i]=="NONE"):?>selected=""<?php endif;?>><?php echo _('NONE')?></option>
                                          <?php while(!($hr == 23 && $min == 60)){?>
                                        <?php if($min == 60){ $hr = $hr + 1; $min = 0;}?>
                                        <?php $selected = false;?>
                                        <?php
                                          $hr_str = ((strlen($hr) == 1)?"0".$hr:$hr);
                                          $min_str = ((strlen($min) == 1)?"0".$min:$min);
				                          if($min_str == 0 && ($custom_pickup1_pickup_array[$i] == $hr_str.":".$min_str || $custom_pickup1_pickup_array[$i] == $hr_str)){
				                            $selected = true;
				                          }elseif($custom_pickup1_pickup_array[$i] == $hr_str.":".$min_str){
				                            $selected = true;
				                          }
                                        ?>
                                        <option value="<?php echo $hr_str.':'.$min_str;?>" <?php if($selected){?>selected=""<?php }?>><?php echo $hr_str;?> : <?php echo $min_str;?></option>
                                        <?php $min = $min+30;?>
                                        <?php }?>
                                      </select>
                                  </td>
                                  <td>
                                      <table border="0" class="override">
                                          <tbody>
                                            <tr id="pickup_<?php echo $i;?>"  <?php if(($custom_pickup1_pickup_array[$i]=="CLOSED") || ($custom_pickup1_pickup_array[$i]=="ALL DAY")){?> style="display: none;" <?php }?>>
                                                <td style="text-align:center"><strong> <?php echo _('to')?>&nbsp;&nbsp;&nbsp;</strong></td>
                                                <td>
                                                  <?php $hr = 0; $min = 0;?>
                                                  <select style="margin-bottom:0px" class="select" type="select"  name="q2[]">
                                                  <option value="0"  <?php if($custom_pickup2_pickup_array[$i]=="0"):?>selected=""<?php endif;?>>-- <?php echo _('Select')?> --</option>
                                				  <option value="NONE"  <?php if($custom_pickup2_pickup_array[$i]=="NONE"):?>selected=""<?php endif;?>><?php echo _('NONE')?></option>
                                                  <?php while(!($hr == 23 && $min == 60)){?>
	                                              <?php if($min == 60){ $hr = $hr + 1; $min = 0;}?>
	                                              <?php $selected = false;?>
	                                              <?php
	                                                $hr_str = ((strlen($hr) == 1)?"0".$hr:$hr);
	                                                $min_str = ((strlen($min) == 1)?"0".$min:$min);
					                                  if($min_str == 0 && ($custom_pickup2_pickup_array[$i] == $hr_str.":".$min_str || $custom_pickup2_pickup_array[$i] == $hr_str)){
					                                    $selected = true;
					                                  }elseif($custom_pickup2_pickup_array[$i] == $hr_str.":".$min_str){
					                                    $selected = true;
					                                  }
	                                              ?>
                                                 <option value="<?php echo $hr_str.':'.$min_str;?>" <?php if($selected){?>selected=""<?php }?>><?php echo $hr_str;?> : <?php echo $min_str;?></option>
                                                 <?php $min = $min+30;?>
                                                 <?php }?>
                                                  </select>
                                                </td>
                                                <td><strong>&nbsp;&nbsp;&nbsp;<?php echo _('and')?>&nbsp;&nbsp;&nbsp;</strong></td>
                                                <td>
                                                  <?php $hr = 0; $min = 0;?>
                                                  <select style="margin-bottom:0px" class="select" type="select" name="q3[]">
                                                    <option value="0" <?php if($custom_pickup3_pickup_array[$i]=="0"):?>selected=""<?php endif;?>>-- <?php echo _('Select')?>--</option>
                                                    <option value="NONE" <?php if($custom_pickup3_pickup_array[$i]=="NONE"):?>selected=""<?php endif;?>><?php echo _('NONE')?></option>
                                                    <?php while(!($hr == 23 && $min == 60)){?>
	                                              <?php if($min == 60){ $hr = $hr + 1; $min = 0;}?>
	                                              <?php $selected = false;?>
	                                              <?php
	                                                  $hr_str = ((strlen($hr) == 1)?"0".$hr:$hr);
	                                                  $min_str = ((strlen($min) == 1)?"0".$min:$min);
					                                  if($min_str == 0 && ($custom_pickup3_pickup_array[$i] == $hr_str.":".$min_str || $custom_pickup3_pickup_array[$i] == $hr_str)){
					                                    $selected = true;
					                                  }elseif($custom_pickup3_pickup_array[$i] == $hr_str.":".$min_str){
					                                    $selected = true;
					                                  }
	                                              ?>
	                                              <option value="<?php echo $hr_str.':'.$min_str;?>" <?php if($selected){?>selected=""<?php }?>><?php echo $hr_str;?> : <?php echo $min_str;?></option>
	                                              <?php $min = $min+30;?>
	                                              <?php }?>
	                                              </select>
                                                </td>
                                                <td><strong>&nbsp;&nbsp;&nbsp;<?php echo _('to')?> &nbsp;&nbsp;&nbsp;</strong></td>
                                                <td>
	                                                  <?php $hr = 0; $min = 0;?>
	                                                  <select style="margin-bottom:0px" class="select" type="select" name="q4[]">
	                                                    <option value="0" <?php if($custom_pickup4_pickup_array[$i]=="0"):?>selected=""<?php endif;?>>--<?php echo _(' Select')?>--</option>
	                                                  <option value="NONE" <?php if($custom_pickup4_pickup_array[$i]=="NONE"):?>selected=""<?php endif;?>><?php echo _('NONE')?></option>
		                                               <?php while(!($hr == 23 && $min == 60)){?>
		                                              <?php if($min == 60){ $hr = $hr + 1; $min = 0;}?>
		                                              <?php $selected = false;?>
		                                              <?php
		                                                $hr_str = ((strlen($hr) == 1)?"0".$hr:$hr);
		                                                $min_str = ((strlen($min) == 1)?"0".$min:$min);
					                                  if($min_str == 0 && ($custom_pickup4_pickup_array[$i] == $hr_str.":".$min_str || $custom_pickup4_pickup_array[$i] == $hr_str)){
					                                    $selected = true;
					                                  }elseif($custom_pickup4_pickup_array[$i] == $hr_str.":".$min_str){
					                                    $selected = true;
					                                  }
		                                              ?>
		                                              <option value="<?php echo $hr_str.':'.$min_str;?>" <?php if($selected){?>selected=""<?php }?>><?php echo $hr_str;?> : <?php echo $min_str;?></option>
		                                              <?php $min = $min+30;?>
		                                              <?php }?>
                                              		</select>
                                                </td>
                          					</tr>
                          				</tbody>
                          			</table>
                          		</td>
                          	</tr>
                         </tbody>

                       <?php }?>
	                        <tbody class="img_hide" style="display: none;">
	                       		<tr>
	                       		<td>
	                     	 		<img width="18" border="0" onClick="javascript:addNewpickday(this)" src="<?php echo base_url(); ?>assets/cp/images/add.gif">
	                     	 	</td>
	                     	 	</tr>
			                 </tbody>
                        </table>
                      <?php }else{?>
	                      <table class="override" border="0">
		                       <tbody class="img_hide">
		                       		<tr>
		                       		<td>
		                     	 		<img width="18" border="0" onClick="javascript:addNewpickday(this)" src="<?php echo base_url(); ?>assets/cp/images/add.gif">
		                     	 	</td>
		                     	 	</tr>
		                      </tbody>
		                    </table>
		             <?php }?>
		                     <input type="hidden" value="<?php if (isset($total_custom_pickup_days_array)){echo $total_custom_pickup_days_array; }else{?>0<?php }?>" name="custom_order_pickup_sett_count" id="custom_order_pickup_sett_count">
                        </td>
                    </tr>
                    <tr>
                      <td class="save_b" colspan="2"><input type="submit"  value="<?php echo _('UPDATE');?>" class="submit" id="btn_update" name="btn_update">
                      <!-- onClick="setHiddenVar('pickup');" -->
                      </td>
                      <input type="hidden" value="edit_pickup_settings" id="act" name="act">
                    </tr>
                  </tbody>
                </table>
              </form>

  <script type="text/javascript">
		 var before_order = "<?php echo _('If the customer orders before');?>";
		          function validateForm2() {

		                var x = document.forms["frm_pickup_settings"]["all_day_starttime_p"].value;
		                var y=document.forms["frm_pickup_settings"]["all_day_endtime_p"].value;

		                      if(y== 0){
		                       alert("<?php echo _("End Select All DAY")?>");
		                          return false;
		                      }

		                      if (x == 0) {
		                          alert("<?php echo _('Select Start Time ALL DAY')?>");
		                          return false;
		                      }
		            }


          function addNewbodyToTable(obj){
              if($(obj).closest('tbody').hasClass('img_hide'))
              {
            	  $(obj).closest('tbody').hide();
              }
        	  var length = $('#custom_order_sett_count').val();

        	  	length = parseInt(length)+1;
	            var html = '';
	        	html+='<tbody>';
	        	html+='<tr>';
	        	html+='<td colspan="2">';
	        	html+='<div style="float:left"><input type="text" class="text" readonly="readonly" name="custom_new_days_pickup[]" id="start_date_'+length+'"></div>';
	        	html+='<div style="float:left"><input type="button" value="..." onclick="displayCalendar(document.frm_pickup_settings.start_date_'+length+',\'dd/mm/yyyy\',this)"  id="button_'+length+'"></div>';
	        	html+='</td>';
	        	html+='<td>';
	        	html+='<img width="18" border="0" onClick="javascript:addNewbodyToTable(this)" src="'+base_url+'assets/cp/images/add.gif">';
	        	html+='<img width="18" border="0" onClick="javascript:deletebodyFromTable(this)" src="'+base_url+'assets/cp/images/delete.gif">';
	        	html+='</td>';
	        	html+='</tr>';
	        	html+='<tr>';
	        	html+='<td width="23%">&nbsp;&nbsp;'+before_order+'</td>';
	        	html+='<td width="10%"></td>';
	        	html+='<td width="15%">';
	            <?php $hr = 0; $min = 0;?>
	            html+='<select style="margin-bottom:0px" class="select" type="select" name="custom_date_time_pickup[]">';
	            html+='<option value="0">--<?php echo _('Select')?>--</option>';
	            <?php while(!($hr == 23 && $min == 60)){?>
	            <?php if($min == 60){ $hr = $hr + 1; $min = 0;}?>
	            <?php
	                $hr_str = ((strlen($hr) == 1)?"0".$hr:$hr);
	                $min_str = ((strlen($min) == 1)?"0".$min:$min);
	             ?>

            	html+="<option value='<?php echo $hr_str.":".$min_str;?>'><?php echo $hr_str;?> : <?php echo $min_str;?></option>";
                        <?php $min = $min+30;?>
                        <?php }?>
            	html+='</select>';
	            html+='</td>';
	            html+="<td width='40%'><?php echo _('hours, he can pick the products after')?></td>";
	            html+=' <td width="10%">';
	            <?php $day = 1;?>
	            html+=' <select style="margin-bottom:0px" class="select" type="select" name="custom_date_days_pickup[]">';
	            html+=" <option value='0'>-- <?php echo _('Select ')?>--</option>";
	            html+=" <option value='<?php echo $day;?>'><?php echo _('Next day')?></option>";
	            <?php $day++;?>
	            <?php while($day < 14){?>
	            html+="  <option value='<?php echo $day;?>'><?php echo $day.' '._('Days')?></option>";
	            <?php $day++?>
	            <?php }?>
	            html+='</select>';
	            html+='</td>';
	            html+='</tr>';
	            html+='</tbody>';
	            $(obj).closest('table').append(html);
           	 	$('#custom_order_sett_count').val(length);
          }

      	function deletebodyFromTable(obj){
      		if($(obj).closest('table').children('tbody').length == '2' ){
  				$(obj).closest('table').find('.img_hide').show();
      			$(obj).closest('tbody').remove();
			}
			else
			{
				$(obj).closest('tbody').remove();
			}
      	}

		function addNewpickday(obj){
			 if($(obj).closest('tbody').hasClass('img_hide'))
             {
           	  $(obj).closest('tbody').hide();
             }
       	  	var length = $('#custom_order_pickup_sett_count').val();

       	  	length = parseInt(length)+1;
	            var html = '';
	        	html+='<tbody>';
	        	html+='<tr>';
	        	html+='<td style="padding-top: 10px;width: 259px">';
	        	html+='<div style="float:left"><input type="text" class="text" readonly="readonly" name="custom_new_pickup_hours[]" id="start_date_or_'+length+'"></div>';
				html+='<div style="float:left"><input type="button" value="..." onclick="displayCalendar(document.frm_pickup_settings.start_date_or_'+length+',\'dd/mm/yyyy\',this)"></div>';
				html+='</td>';
			    html+='<td>';
				html+='<img width="18" border="0" onClick="javascript:addNewpickday(this)" src="'+base_url+'assets/cp/images/add.gif">';
				html+='<img width="18" border="0" onClick="javascript:deleteNewpickday(this)" src="'+base_url+'assets/cp/images/delete.gif">';
			    html+='</td>';
			    html+='</tr>';
			    html+='<tr>';
			    html+='<td style="padding: 5px;">';
                        <?php $hr = 0; $min = 0;?>
                html+='<select onChange="show_hide_custom_pickup('+length+',\'pickup_new\',this.value,this);" style="margin-bottom:0px" class="select" type="select" name="q1[]">';
                html+='<option value="0">-- <?php echo _('Select')?>--</option>';
                html+='<option value="ALL DAY"><?php echo _('ALL DAY')?></option>';
                html+='<option value="CLOSED"><?php echo _('CLOSED')?></option>';
                html+='<option value="NONE"><?php echo _('NONE')?></option>';
                <?php while(!($hr == 23 && $min == 60)){?>
                <?php if($min == 60){ $hr = $hr + 1; $min = 0;}?>
                <?php
                  $hr_str = ((strlen($hr) == 1)?"0".$hr:$hr);
                  $min_str = ((strlen($min) == 1)?"0".$min:$min);
                ?>
                html+='<option value="<?php echo $hr_str.':'.$min_str;?>"><?php echo $hr_str;?> : <?php echo $min_str;?></option>';
                <?php $min = $min+30;?>
                <?php }?>
                html+='</select>';
                html+='</td>';

                html+='<td>';
                html+='<table border="0" class="override">';
                html+='<tbody>';
                html+='<tr id="pickup_new_'+length+'">';
                html+='<td style="text-align:center"><strong> <?php echo _('to')?>&nbsp;&nbsp;&nbsp;</strong></td>';
                html+='<td>';
               <?php $hr = 0; $min = 0;?>
                html+='<select style="margin-bottom:0px" class="select" type="select" name="q2[]">';
                html+='<option value="0">-- <?php echo _('Select')?> --</option>';
                html+='<option value="NONE"><?php echo _('NONE')?></option>';
               <?php while(!($hr == 23 && $min == 60)){?>
               <?php if($min == 60){ $hr = $hr + 1; $min = 0;}?>
               <?php
                 $hr_str = ((strlen($hr) == 1)?"0".$hr:$hr);
                 $min_str = ((strlen($min) == 1)?"0".$min:$min);
              ?>
              html+='<option value="<?php echo $hr_str.':'.$min_str;?>"><?php echo $hr_str;?> : <?php echo $min_str;?></option>';
          	  <?php $min = $min+30;?>
              <?php }?>
              html+='</select>';
              html+='</td>';
              html+='<td><strong>&nbsp;&nbsp;&nbsp;<?php echo _('and')?>&nbsp;&nbsp;&nbsp;</strong></td>';
              html+='<td>';
                            <?php $hr = 0; $min = 0;?>
              html+='<select style="margin-bottom:0px" class="select" type="select" name="q3[]">';
              html+='<option value="0">-- <?php echo _('Select')?>--</option>';
              html+='<option value="NONE"><?php echo _('NONE')?></option>';
              <?php while(!($hr == 23 && $min == 60)){?>
              <?php if($min == 60){ $hr = $hr + 1; $min = 0;}?>
              <?php
              $hr_str = ((strlen($hr) == 1)?"0".$hr:$hr);
              $min_str = ((strlen($min) == 1)?"0".$min:$min);
          	  ?>
          	  html+='<option value="<?php echo $hr_str.':'.$min_str;?>"><?php echo $hr_str;?> : <?php echo $min_str;?></option>';
              <?php $min = $min+30;?>
              <?php }?>
              html+='</select>';
           	  html+='</td>';
           	  html+='<td><strong>&nbsp;&nbsp;&nbsp;<?php echo _('to')?> &nbsp;&nbsp;&nbsp;</strong></td>';
           	  html+='<td>';
              <?php $hr = 0; $min = 0;?>
              html+='<select style="margin-bottom:0px" class="select" type="select" name="q4[]">';
              html+='<option value="0">--<?php echo _(' Select')?>--</option>';
              html+='<option value="NONE"><?php echo _('NONE')?></option>';
              <?php while(!($hr == 23 && $min == 60)){?>
              <?php if($min == 60){ $hr = $hr + 1; $min = 0;}?>
              <?php
              $hr_str = ((strlen($hr) == 1)?"0".$hr:$hr);
              $min_str = ((strlen($min) == 1)?"0".$min:$min);
           	  ?>
           	  html+='<option value="<?php echo $hr_str.':'.$min_str;?>"><?php echo $hr_str;?> : <?php echo $min_str;?></option>';
              <?php $min = $min+30;?>
           	  <?php }?>
           	  html+='</select>';
           	  html+='</td>';
              html+='</tr>';
              html+='</tbody>';
              html+='</table>';
           	  html+='</td>';
	          html+='</tr>';
           	  html+='</tbody>';
           	  $(obj).closest('table').append(html);
           	  $('#custom_order_pickup_sett_count').val(length);
		}

		function deleteNewpickday(obj){
      			if($(obj).closest('table').children('tbody').length == '2' ){
      				$(obj).closest('table').find('.img_hide').show();
	      			$(obj).closest('tbody').remove();
				}
				else
				{
					$(obj).closest('tbody').remove();
				}
		}

		function show_hide_custom_pickup(trid,section,value,obj){
	    	var id = section+"_"+trid;
			if(value == "CLOSED" || value == "ALL DAY"){
				$(obj).parent().next().find("#"+id+"").hide();
			}else{
				$(obj).parent().next().find("#"+id+"").show();
	    	}
		}

		function addNewbodyToTableexp(obj){
			if($(obj).closest('tbody').hasClass('img_hide'))
            {
          	  $(obj).closest('tbody').hide();
            }
      	  	var length = $('#same_date_day_order_sett_count').val();
      	  	length = parseInt(length)+1;
      	  	var html = '';
      	  	html+='<tbody>';
      	 	html+='<tr>';
      	  	html+='<td>';
      	  	html+='<div style="float:left"><input type="text" class="text" readonly="readonly" name="same_day_excep[]" id="start_date_exp_'+length+'"></div>';
      	  	html+='<div style="float:left"><input type="button" value="..." onclick="displayCalendar(document.frm_pickup_settings.start_date_exp_'+length+',\'dd/mm/yyyy\',this)" ></div>';
      	  	html+='</td>';
      		html+='	<td>';
      		html+='<img width="18" border="0" onClick="javascript:addNewbodyToTableexp(this)" src="'+base_url+'assets/cp/images/add.gif">';
      		html+='<img width="18" border="0" onClick="javascript:deletebodyFromTableexp(this)" src="'+base_url+'assets/cp/images/delete.gif">';
      		html+='</td>';
      		html+='</tr>';
      		html+='<tr>';
      		html+='<td width="238" align="left" style="padding-left: 27px" colspan="2"><?php echo _('minimum time between order and pickup')?></td>';
      		html+='<td>';
            <?php $hr = 0; $min = 0;?>
           	html+=' <select style="margin-bottom:0px" class="select" type="select"  name="date_time_diff_pickup[]">';
            html+=' <option value="0">--<?php echo _('Select')?>--</option>';
            <?php while(!($hr == 5 && $min == 30)){?>
            <?php $min = $min+30;?>
            <?php if($min == 60){ $hr = $hr + 1; $min = 0;}?>
            <?php $hr_str = ((strlen($hr) == 1)?"0".$hr:$hr);?>
            <?php $min_str = ((strlen($min) == 1)?"0".$min:$min);?>
            html+=' <option><?php echo $hr_str;?>:<?php echo $min_str;?></option>';
            <?php }?>
            html+='</select>';
            html+='</td>';
            html+='</tr>';
            html+='<tr>';
        	html+=' <td width="244" align="left" style="padding-left: 27px"><?php echo _('Customers can order the same day till')?></td>';
        	html+=' <td width="42"><?php echo _('from');?></td>';
        	html+='<td width="106">';
            <?php $hr = 0; $min = 0;?>
            html+=' <select style="margin-bottom:0px;margin-top:4px;" class="select" type="select" name="same_date_time_pickup_start_exp[]">';
            html+='<option value="0">-- <?php echo _('Select')?> --</option>';
           	<?php while(!($hr == 23 && $min == 60)){?>
            <?php if($min == 60){ $hr = $hr + 1; $min = 0;}?>
            <?php $hr_str = ((strlen($hr) == 1)?"0".$hr:$hr);?>
            <?php $min_str = ((strlen($min) == 1)?"0".$min:$min);?>
            html+='<option><?php echo $hr_str;?>:<?php echo $min_str;?></option>';
            <?php $min = $min+30;?>
            <?php }?>
            html+='</select>';
            html+='</td>';
            html+='<td width="38"><?php echo _('to');?></td>';
            html+='<td>';
            <?php $hr = 0; $min = 0;?>
            html+='<select style="margin-bottom:0px" class="select" type="select" name="same_date_time_pickup_end_exp[]">';
            html+=' <option value="0">-- <?php echo _('Select')?> --</option>';
            <?php while(!($hr == 23 && $min == 60)){?>
            <?php if($min == 60){ $hr = $hr + 1; $min = 0;}?>
            <?php $hr_str = ((strlen($hr) == 1)?"0".$hr:$hr);?>
            <?php $min_str = ((strlen($min) == 1)?"0".$min:$min);?>
            html+=' <option><?php echo $hr_str;?>:<?php echo $min_str;?></option>';
            <?php $min = $min+30;?>
            <?php }?>
            html+='</select>';
            html+='</td>';
            html+='</tr>';
            html+='</tbody>';
            $(obj).closest('table').append(html);
         	$('#same_date_day_order_sett_count').val(length);
		}

		function deletebodyFromTableexp(obj){
			if($(obj).closest('table').children('tbody').length == '2' ){
  				$(obj).closest('table').find('.img_hide').show();
      			$(obj).closest('tbody').remove();
			}
			else
			{
				$(obj).closest('tbody').remove();
			}
		}
    </script>

              <!--<script type="text/javascript" language="javascript">
        var frmValidator = new Validator("frm_pickup_settings");
        frmValidator.EnableMsgsTogether();
        frmValidator.setCallBack(validate_mess);
        frmValidator.addValidation("all_day_starttime_p","dontselect=0","<?php //echo _('Select Start Time ALL DAY')?>");
        frmValidator.addValidation("all_day_endtime_p","dontselect=0","<?php //echo _('End Select All DAY')?>");
      </script>-->
            </div>
          </div>
        </div>
      </div>