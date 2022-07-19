<!------this page is being shown in a thickbox called in sidebar.php --------->
<!----------this page will show two option 1.holiday and 2.closed when a date is being clicked--------------->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type='text/javascript' src='<?php echo base_url()?>assets/cp/js/jquery-1.5.2.min.js?version=<?php echo version;?>'></script>
<script type="text/javascript">
	function set_closed(date){
		jQuery.post("<?php echo  base_url();?>cp/calender/set_closed",
				{'close_date':date},
				function(data){
					if(data!="successsfully_updated"){
						alert("<?php echo _('error occured ');?>");
					}
					parent.location.reload();
				}
		);
	}
	
	function set_holiday(date){
		jQuery.post("<?php echo  base_url();?>cp/calender/set_holiday",
				{'holiday_date':date},
				function(data){
					if(data!="successsfully_updated"){
						alert("<?php echo _('error occured')?>");
					}
					parent.location.reload();
				}
		);

	}

</script>				  
<title><?php echo _('choose date options')?></title>
</head>
<body>
	<ul>
    	<li style="text-align:left;"><a href="javascript:void(0)" onClick="set_closed('<?php echo $date;?>')"><?php echo _('Closed')?></a></li>
        <li style="text-align:left;"><a href="javascript:void(0)"  onclick="set_holiday('<?php echo $date;?>')"><?php echo _('Holiday')?></a></li>
   </ul>
                
</body>
</html>				  
