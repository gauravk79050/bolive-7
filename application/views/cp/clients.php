<script type="text/javascript" src="<?php echo base_url()?>/assets/mcp/js/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">


</script>
<script type="text/javascript">
	tinyMCE.init({
		theme : "advanced",
		mode : "exact",	
		elements : "message",				
		script_url : '<?php echo base_url()?>/assets/mcp/js/tinymce/jscripts/tiny_mce/tiny_mce.js',
		convert_urls : false,
					
					 
		plugins : "spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

        // Theme options
        theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
        theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
        theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
        theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage",
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        theme_advanced_statusbar_location : "bottom",
        theme_advanced_resizing : true,

        // Skin options
        skin : "o2k7",
        skin_variant : "silver",

        // Example content CSS (should be your site CSS)
        content_css : "css/example.css",

        // Drop lists for link/image/media/template dialogs
        template_external_list_url : "js/template_list.js",
        external_link_list_url : "js/link_list.js",
        external_image_list_url : "js/image_list.js",
        media_external_list_url : "js/media_list.js",

        // Replace values for the template plugin
        template_replace_values : {
                username : "Some User",
                staffid : "991234"
        }
   }); 
</script>

<!-------this section is for jquery pagination --------------------------------------->
<link rel="stylesheet" href="<?php echo base_url()?>assets/cp/new_js/pagination/pagination.css"/>

<style type="text/css">
#message_tbl{width:auto !important;}
.defaultSkin .mceIframeContainer { border-right: 1px solid #CCCCCC; }
.success{color: #333333;}

table th, table td{
   padding:5px;
}
</style>

<script type="text/javascript" src="<?php echo base_url()?>assets/cp/new_js/pagination/jquery.pagination.js"></script>
<script type="text/javascript">
var members = new Array();
<?php 

$script_code = ''; 

if( $this->company_role != 'super' )
{
	for($i=0; $i<count($clients);$i++): 	
		if( !empty($clients[$i]) )
		{
			$client_id = $clients[$i]->id;
			
			$date_created = date('d-m-Y',strtotime($clients[$i]->created_c));
			
			$client_name = $clients[$i]->firstname_c." ".$clients[$i]->lastname_c;
			
			if($this->company->ac_type_id != 1)
				$email = $clients[$i]->email_c;
			else
				$email = '';
			
			//$password = $clients[$i]->password_c;
			
			$house_no =  $clients[$i]->housenumber_c;
			
			$address = $clients[$i]->address_c;
			
			$city = $clients[$i]->city_c;
			
			$phone_no = $clients[$i]->phone_c;	
			
			$notifications = ($clients[$i]->notifications == 'subscribe')?1:0;	
			
			$script_code .= "members[".$i."] = ['".$client_id."','".$date_created."','".stripslashes($client_name)."','".$email ."','','".$house_no."','".stripslashes($address)."','".stripslashes($city)."','".$phone_no."','".$this->company_id."','".$notifications."'];"; 
			
		}
	endfor; 
}
else
{
    $i = 0;
	foreach( $clients as $index => $c_arr )
	{
		if( $i == 0 )
		{
		   $script_code .= "members[".$i."] = ['super_comp_clients'];"; 
		   $compnay_id = $this->company_id;
		}
		else
		{
		   $script_code .= "members[".$i."] = ['".$index."'];"; 
		   if( !empty($sub_companies) )
		   {
		      foreach( $sub_companies as $sc )
			  {
			     if( $sc->company_name == $index )
				   $compnay_id = $sc->company_id;
			  }
		   }
		}
		
		if( !empty( $c_arr ) )
		{
		    foreach( $c_arr as $c )
			{
			    $i++;
				
				$date_created = date('d-m-Y',strtotime($c->created_c));
			    $client_name = $c->firstname_c." ".$c->lastname_c;
				$notifications = ($c->notifications == 'subscribe')?1:0;	
				
				$script_code .= "members[".$i."] = ['".$c->id."','".$date_created."','".stripslashes($client_name)."','".$c->email_c."','','".$c->housenumber_c."','".stripslashes($c->address_c)."','".stripslashes($c->city_c)."','".$c->phone_c."','".$compnay_id."','".$notifications."'];"; 
			}
		}
		else
		{
		    $i++;			
			$script_code .= "members[".$i."] = ['no_results'];"; 
		}
		
		$i++;			
	}
}
?>

<?php echo $script_code;?>	

/* -------------------- FUNCTION TO STRIP SLASHES ------------------------------------------ */
function stripslashes(str) {
	//        example 1: stripslashes('Kevin\'s code');
	//        returns 1: "Kevin's code"
	//        example 2: stripslashes('Kevin\\\'s code');
	//        returns 2: "Kevin\'s code"

	return (str + '')
	    .replace(/\\(.?)/g, function(s, n1) {
	      switch (n1) {
	        case '\\':
	          return '\\';
	        case '0':
	          return '\u0000';
	        case '':
	          return '';
	        default:
	          return n1;
	     }
	});
}

function pageselectCallback(page_index, jq){
	// Get number of elements per pagionation page from form
	var items_per_page = 10;
	var max_elem = Math.min((page_index+1) * items_per_page, members.length);
	var newcontent = '';
	// Iterate through a selection of the content and build an HTML string
	
	for(var i=page_index*items_per_page;i<max_elem;i++)
	{
		if( members[i][0] == 'super_comp_clients' )
		{
		}
		else
		if( members[i][0] == 'no_results' )
		{
		    newcontent += '<tr>';
            newcontent += '<td colspan="8"><strong>Sorry ! No Clients.</strong></td>';
			newcontent += '</tr>';	
		}
		else
		if( members[i][0] != '' && !members[i][1] )
		{
		    newcontent += '<tr>';
            newcontent += '<th colspan="8">'+members[i][0]+' Clients</th>';
			newcontent += '</tr>';	
		}
		else
		{		
			newcontent += '<tr>';
			
			newcontent += '<td width=\"10%\">'+members[i][1]+'</td>';
			
			if( members[i][10] == '1' )
			newcontent += '<td><a href=\"<?php echo base_url()?>cp/clients/lijst/client_details/'+members[i][0]+'/'+members[i][9]+'\" class=\"edit\">'+stripslashes( members[i][2] )+'</a>&nbsp;<img src=\"<?php echo base_url()?>assets/cp/images/checked_invoice_64.gif\" height=\"18\" width=\"18\"></td>';
			else
			newcontent += '<td><a href=\"<?php echo base_url()?>cp/clients/lijst/client_details/'+members[i][0]+'/'+members[i][9]+'\" class=\"edit\">'+stripslashes( members[i][2] )+'</a></td>';
			
			
			newcontent += '<td width=\"80px\">'+members[i][3]+'&nbsp;</td>';
			/*newcontent += '<td>'+members[i][4]+'</td>';*/
			newcontent += '<td>'+stripslashes( members[i][6] )+'</td>';
			newcontent += '<td>'+stripslashes( members[i][5] )+'</td>';
			newcontent += '<td>'+stripslashes( members[i][7] )+'</td>';
			newcontent += '<td>'+members[i][8]+'</td>';
			newcontent += '<td style=\"text-align:center\">';
			newcontent += '<a onclick=\"return confirmation('+members[i][0]+','+members[i][9]+');\" href=\"#\" class=\"delete\"><img width=\"16\" height=\"16\" border=\"0\" alt=\"delete\" src=\"<?php echo base_url()?>assets/cp/images/delete.gif\"></a></td>';
			newcontent += '</tr>';	
		
		}

	}
	// Replace old content with new content
	$('#client_content').html(newcontent);
	
	// Prevent click event propagation
	return false;
}
function getOptionsFromForm(){
	var opt = {callback: pageselectCallback};

	opt['items_per_page'] = 10;

	opt['num_display_entries'] =3;

	opt['num_edge_entries'] = 1;

	opt['prev_text'] = '&laquo; Prev';

	opt['next_text'] = 'Next &raquo;';

	return opt;

}

 $(document).ready(function(){
// Create pagination element with options from form
	var optInit =  $(document).ready(function(){
// Create pagination element with options from form
		var optInit = getOptionsFromForm();
		$("#pagination").pagination(members.length, optInit);
	});
     $("#pagination").pagination(members.length, optInit);
 });
</script>

<!------------------------------------------------------------------------------------->
  <!-- MAIN -->
  <div id="main">
    <div id="main-header">
      <h2><?php echo _('Client Management')?></h2>
      <span class="breadcrumb"><a href="<?php echo base_url()?>cp"><?php echo _('Home')?></a> &raquo; <?php echo('Customer')?></span>
	</div>
    <?php $messages = $this->messages->get();?>
	<?php if(is_array($messages)):?>
	<?php foreach($messages as $key=>$val):?>
		<?php if($val != array()):?>
		<div id="succeed_order_update" class="<?php echo $key;?>"><?php echo $val[0];?></div>
		<?php endif;?>
    <?php endforeach;?>
	<?php endif;?>
	<div id="content">
      <div id="content-container">
      
        <?php if( $this->company_role != 'super' ) { ?>
        
        <div class="boxed">
          <h3><?php echo _('Search Client')?></h3>
          <div class="table" style="display: none;">
            <form action="<?php echo base_url()?>cp/clients/lijst/search_client" enctype="multipart/form-data" method="post" id="frm_search" name="frm_search">
              <input type="hidden" value="clients" name="OBS BEstelsysteem - SiteMatic BVBA_REF_VIEW">
              <table cellspacing="0">
                <tbody>
                  <tr>
                    <td style="border:none;"><strong><?php echo _('Search')?></strong></td>
                    <td align="justify" style="border:none;"><select class="select" id="search_by" name="search_by">
                        <option value="0"><?php echo _('- - Select - -')?></option>
                        <option value="Name"><?php echo _('Name')?></option>
                        <option value="Email"><?php echo _('Email')?></option>
                      </select></td>
                    <td style="border:none;"><strong><?php echo _('Keyword')?></strong></td>
                    <td valign="baseline" style="border:none;"><input type="text" class="text" id="search_keyword" name="search_keyword"></td>
                    <td valign="middle" style="border:none;"><input type="submit" value="<?php echo _('Search')?>" class="submit" id="btn_search" name="btn_search">
                      <input type="button" onclick="this.form.search_by.selectedIndex=0; this.form.search_keyword.value='';" value="Reset" class="submit" id="btn_reset" name="btn_reset">
                      <input type="hidden" value="do_filter" id="act" name="act">
                      <input type="hidden" value="clients" id="view" name="view"></td>
                    <td width="20%">&nbsp;</td>
                  </tr>
                </tbody>
              </table>
            </form>
            <script type="text/javascript" language="JavaScript">
				var frmvalidator = new Validator("frm_search");
				frmvalidator.EnableMsgsTogether();
				frmvalidator.addValidation("search_by","dontselect=0","<?php echo _('Please enter a search item, the dropdown list')?>");
				frmvalidator.addValidation("search_keyword","req","<?php echo _('Please provide a keyword please')?>");
			 </script>
          </div>
        </div>
        
        <?php } ?>
        
        
        
        <div class="box">
          <h3><?php echo _('Clients')?></h3>
          <div class="table" oncontextmenu="return false">
            <table cellspacing="0">
              <thead>
              	<tr>
                	<td style="text-align:right" colspan="8"><a href="<?php echo base_url();?>cp/clients/lijst/add"><?php echo _('Add Client');?></a></td>
                </tr>
                <tr>
                  <th><?php echo _('Date')?></th>
                  <th><?php echo _('Name')?></th>
                  <th width="80px"><?php echo _('E-mail')?></th>
                  <th><?php echo _('Adress')?></th>
                  <th><?php echo _('House number')?></th>
                  <th><?php echo _('City')?></th>
                  <th><?php echo _('TelePhone')?></th>
                  <th><?php echo _('Action')?></th>
                </tr>
              </thead>
              <tbody id="client_content">
             </tbody>
            </table>
            <div class="pagination" id="pagination" style="font-size: 12px;">
           	<!---pagination will get appened here--------->
			</div>
            
            <?php if( $this->company_role != 'super' ) { ?>
            
            <table>
              <tbody>
                <tr>
                  <td>
				  <a href="<?php echo base_url()?>cp/clients/downlaod_customer_info" style="text-decoration:none;">
				    <img border="0" src="<?php echo base_url()?>assets/cp/images/download_excel.jpg">
				  </a>
				  </td>
                </tr>
              </tbody>
            </table>
            
            <?php } ?>
            
          </div>
        </div>
      </div>
    </div>
    <!-- /content -->
