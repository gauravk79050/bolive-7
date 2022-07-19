<!-- <link rel='stylesheet' type='text/css' href='<?php echo base_url()?>assets/mcp/cupertino/theme.css' />
<link rel='stylesheet' type='text/css' href='<?php echo base_url()?>assets/mcp/css/fullcalendar.css' /> -->
<link rel='stylesheet' type='text/css' href='<?php echo base_url()?>assets/cp/css/theme.css'/>
<link rel='stylesheet' type='text/css' href='<?php echo base_url()?>assets/mcp/css/fullcalendar.css'/>
<script type='text/javascript' src='<?php echo base_url()?>assets/mcp/new_js/jquery-ui-1.8.17.custom.min.js'></script>
<script type='text/javascript' src='<?php echo base_url()?>assets/mcp/js/fullcalendar.min.js'></script>
<script type='text/javascript'>
    
	//---1.this ias to add holiday class on the dates which are declared as holiday---//	
	function jscalls(calendarId){
 		jQuery.post("<?php echo base_url()?>mcp/calendar/get_holiday",
					{'month':jQuery('#'+calendarId+' .fc-header-title h2').text().split(" ")[0],'year':jQuery('#'+calendarId+' .fc-header-title h2').text().split(" ")[1],'country':calendarId},
					function(data){
  						jQuery('#'+calendarId+' .fc-day-number').each(function(){
							if(jQuery(this).hasClass('holiday')){
								jQuery(this).removeClass('holiday');
				
							}
							for(var i=0;i<data['holidays'].length;i++){
								if(data['holidays'][i]==jQuery(this).text()&&!jQuery(this).parents('td').hasClass('fc-other-month')){
									jQuery(this).addClass('holiday');
								}
							}
					});
 	},'json');
   //-----------------------------------------------------------------------------//
   
	jQuery('.fc-day-number').each(function(){
		if(jQuery(this).parents('.ui-widget-content').hasClass('fc-today')){
			//alert(jQuery(this).parents('.ui-widget-content').attr('class'));
			jQuery(this).parents('.ui-widget-content').addClass('today');
		}else if(jQuery(this).parents('.ui-widget-content').hasClass('today')){
			jQuery(this).parents('.ui-widget-content').removeClass('today');
		}
	
	});
	
//----------------------------------------------------------------------//

}

//----------3.this is to show calender using jquery calender--------------//
	jQuery(document).ready(function() {
	
		var date = new Date();
		//alert(date);
		var d = date.getDate();
		//alert(d);
		var m = date.getMonth();
		var y = date.getFullYear();
		jQuery('#calendar_belgium').fullCalendar({
			theme: true,
			header: {
				left: 'prev',
				center: 'title',
				right: 'next'
			},
			editable: true,
			monthNames:['Januari','Februari','Maart','April','Mei','Juni','Juli','Augustus','September','Oktober','November','December'],
			monthNamesShort: ['Jan','Feb','Maa','Apr','Mei','Jun','Jul','Aug','Sep','Okt','Nov','Dec'],
			dayNames: ['Zondag','Maandag','Dinsdag','Woensdag','Donderdag','Vrijdag','Zaterdag'],
			dayNamesShort: ['Zo','Ma','Di','Wo','Do','Vr','Za']					
		
		});

	  jQuery('#calendar_netherland').fullCalendar({
			theme: true,
			header: {
				left: 'prev',
				center: 'title',
				right: 'next'
			},
			editable: true,
			monthNames:['Januari','Februari','Maart','April','Mei','Juni','Juli','Augustus','September','Oktober','November','December'],
			monthNamesShort: ['Jan','Feb','Maa','Apr','Mei','Jun','Jul','Aug','Sep','Okt','Nov','Dec'],
			dayNames: ['Zondag','Maandag','Dinsdag','Woensdag','Donderdag','Vrijdag','Zaterdag'],
			dayNamesShort: ['Zo','Ma','Di','Wo','Do','Vr','Za']					
	
		});
		
		jscalls('calendar_belgium');
		jscalls('calendar_netherland');
		<!--calling this function to show holidays when sidebar gets loaded -->
		
		jQuery('.ui-widget-content').each(function(){
		   jQuery(this).children('div').css('position','relative');
		});
		
        //-------4.this will show the thickbox when a date is clicked--------//
		jQuery('.fc-day-number').click(function(){

			//alert(jQuery(this).parents('div.calendar').attr('id'));
			var calendar_id = jQuery(this).parents('div.calendar').attr('id');
		    //alert(jQuery(this).hasClass());
		    //alert("fiu");
			if(jQuery(this).parents('td').hasClass('fc-other-month')){ 
   			  alert("Can\'t mark holiday.");
			}else{
			
			    jQuery('.fc-day-content').fadeOut();
				
				var date=jQuery(this).text();
				
				//alert(date);
				//date1 = unescape(date);//this command to remove space(cming in internet explorer)
				date = date.replace(/^[\s]+/,'').replace(/[\s]+$/,'').replace(/[\s]{2,}/,'').replace(/&nbsp;/g,'');
				//alert(escape(date));
				//alert(unescape(date));
				//alert(jQuery('.fc-header-title h2').text());
				var month=jQuery('#'+calendar_id+' .fc-header-title h2').text().split(" ")[0];
				var year=jQuery('#'+calendar_id+' .fc-header-title h2').text().split(" ")[1];
				
				var months=new Array(12);
				months["January"]="01";
				months["February"]='02';
				months["March"]="03";
				months["April"]="04";
				months["May"]="05";
				months["June"]="06";
				months["July"]="07";
				months["August"]="08";
				months["September"]="09";
				months["October"]="10";
				months["November"]="11";
				months["December"]="12";
				
				months["Januari"]="01";
				months["Februari"]='02';
				months["Maart"]="03";
				months["April"]="04";
				months["Mei"]="05";
				months["Juni"]="06";
				months["Juli"]="07";
				months["Augustus"]="08";
				months["September"]="09";
				months["Oktober"]="10";
				months["November"]="11";
				months["December"]="12";
				
				var date_str = date+'/'+months[month]+'/'+year;				

				set_holiday(date_str,jQuery(this),calendar_id);
				/*var html_txt = '<a href="javascript:hide();" style="float:right;"><img src="<?php echo base_url()?>assets/cp/images/icon_close.gif"></a><br style="clear:both;"><ul><li style="text-align:left;"><a href="javascript:void(0)" onClick="set_closed(\''+date_str+'\',\''+date+'\')"><?php echo _('Closed')?></a></li><li style="text-align:left;"><a href="javascript:void(0)" onclick="set_holiday(\''+date_str+'\',\''+date+'\')"><?php echo _('Holiday')?></a></li></ul>';
				
				jQuery(this).next('.fc-day-content').html(html_txt);
				jQuery(this).next('.fc-day-content').fadeIn();*/
				
				//tb_show('choose option',"<?php echo site_url('cp/calender/get_option'); ?>/"+date+"/"+month+"/"+year+"/?keepThis=true&TB_iframe=true&height=100&width=100",null);
			}		
		});

		jQuery('.fc-button-prev').click(function(){jscalls(jQuery(this).parents('div.calendar').attr('id'));});<!--jscalls() to show holidays on the previous month-->
		jQuery('.fc-button-next').click(function(){jscalls(jQuery(this).parents('div.calendar').attr('id'));});<!--jscalls() to show holidays in next month-->
	});
	
	//function hide(){jQuery('.fc-day-content').fadeOut();}
	
	function set_holiday(date,obj,calendar_id){
	 
		jQuery.post("<?php echo  base_url();?>mcp/calendar/set_holiday",
				{'holiday_date':date,'country':calendar_id},
				function(data){
					if(data.trim()!="successsfully_updated"){
						alert("<?php echo _('Error Occured')?>");						
					}
					else
					{
					    //parent.location.reload();
						
						jQuery('#'+calendar_id+' .fc-day-content').css('display','none');
						
						if( jQuery(obj).hasClass('holiday') ){
							jQuery(obj).removeClass('holiday');
				            alert("<?php echo _('Holiday removed successfully.'); ?>");
						}
						else
						{
				            jQuery(obj).addClass('holiday');
						    alert("<?php echo _('Holiday set successfully.'); ?>");
						}

						jscalls(calendar_id);
					}
				}
		);

	}
	
</script>
<style type='text/css'>
	.calendar {
		width: 500px;
		margin:20px auto;
	}
	.holiday{
			background-image:url("<?php echo base_url()?>assets/cp/images/calBg.jpg");
			background-repeat: no-repeat;
			background-position: center top;
	}
	.today {
			background-color: transparent;
			background-image: url("<?php echo base_url()?>assets/cp/images/holiday.jpg");
			background-repeat: no-repeat;
			background-position: center top;
	}

    .fc-day-content {
	        display:none;
	        background: none repeat scroll 0 0 #FFFFFF;
			border: 1px solid #CCCCCC;
			box-shadow: 2px 2px 2px #CCCCCC;
			/*height: 40px;*/
			left: 20px;
			padding: 5px;
			position: absolute;
			top: 18px;
			width: 80px;
			z-index: 1000;
	}
	
	.fc-view{
	       overflow:visible;
	}
	.box, .boxed{
		   overflow:visible;
	}
</style>
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

                                  <td width="50%" align="left"><h3><?php echo _("Calendar Manager");?></h3></td>

                                  <td width="50%" align="right"><div "="" onclick="history.back();" title="Back" style="background-image:url(<?php echo base_url(); ?>assets/mcp/images/undo.jpg); cursor:pointer; float:right" class="icon_button"></div>

                                </tr>
                           
                              </tbody>

                            </table></td>

                        </tr>

                        <tr>

                          <td align="center"><table width="100%" cellspacing="0" cellpadding="0" border="0">

                              <tbody>

                                <tr>

                                  <td height="22" align="right"><div style="float:right; width:80%"> <?php /*?><span class="paging_nolink">&lt;&lt;Vorige</span>&nbsp;<span class="paging_selected">1</span>&nbsp;<span class="paging_nolink">Volgende&gt;&gt;</span><?php */?> </div></td>

                                </tr>

                                <tr>

                                  <td bgcolor="#003366"><table width="100%" cellspacing="0" cellpadding="0" border="0" style="background:url(<?php echo base_url(); ?>assets/mcp/images/pink_table_bg.jpg) left repeat; text-align:left;">

                                      <tbody>

                                        <tr>

                                          <td width="50%" class="whiteSmallBold"><?php echo _("BELGIUM");?></td>

                                          <td width="50%" align="right" style="padding-right:40px" class="whiteSmallBold"></td>

                                        </tr>

                                        <tr>

                                          <td valign="middle" bgcolor="#FFFFFF" style="border:#003366 1px solid;" colspan="5">
												<div id='calendar_belgium' class="calendar"></div>
										  </td>

                                        </tr>

                                      </tbody>

                                    </table></td>

                                </tr>
                                
                                <tr>

                                  <td height="22" align="right"><div style="float:right; width:80%"> <?php /*?><span class="paging_nolink">&lt;&lt;Vorige</span>&nbsp;<span class="paging_selected">1</span>&nbsp;<span class="paging_nolink">Volgende&gt;&gt;</span><?php */?> </div></td>

                                </tr>

                                <tr>

                                  <td bgcolor="#003366"><table width="100%" cellspacing="0" cellpadding="0" border="0" style="background:url(<?php echo base_url(); ?>assets/mcp/images/pink_table_bg.jpg) left repeat; text-align:left;">

                                      <tbody>

                                        <tr>

                                          <td width="50%" class="whiteSmallBold"><?php echo _("NETHERLANDS");?></td>

                                          <td width="50%" align="right" style="padding-right:40px" class="whiteSmallBold"></td>

                                        </tr>

                                        <tr>

                                          <td valign="middle" bgcolor="#FFFFFF" style="border:#003366 1px solid" colspan="5">
										     <div id='calendar_netherland' class="calendar"></div>
									      </td>
					
                                        </tr>

                                      </tbody>

                                    </table></td>

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