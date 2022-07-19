<link href="<?php echo base_url(); ?>assets/cp/new_js/pagination/pagination.css" rel="stylesheet">
<script src="<?php echo base_url(); ?>assets/cp/new_js/pagination/jquery.pagination.js" type="text/javascript"></script>
<script type="text/javascript">
   var base_url = '<?php echo base_url(); ?>';
</script>
<link href="<?php echo base_url(); ?>assets/mcp/thickbox/css/thickbox.css" rel="stylesheet">
<style type="text/css">
/*29/05/2017*/
#overview_table_paginate {
    background: #003366;
    padding: 10px 0px;
    width: 100%;
    margin-bottom: 10px;
    display: inline-block;
    text-align: right;
}
a.paginate_button.previous,a#overview_table_previous {
    border: solid 1px #fff;
    display: inline-block;
    cursor: pointer;
    padding: 3px 10px;
    border-radius: 3px;
    color: #fff;
}
a.paginate_button {
    padding: 0px 10px;
    color: #fff;
}
a.paginate_button.next,a#overview_table_next {
    border: solid 1px #ffffff;
    display: inline-block;
    cursor: pointer;
    padding: 3px 10px;
    border-radius: 3px;
    color: #fff;
    margin-right: 20px;
}
div#overview_table_filter {
    margin: 7px 0px;
    display: inline-block;
    float: right;
}
div#overview_table_length {
    display: inline-block;
    float: left;
}
div#overview_table_filter {
    margin: 7px 0px;
    display: inline-block;
    float: right;
}
.dataTables_paginate.paging_simple_numbers {
    background: #003366;
    padding: 10px 10px;
}
div#overview_table_info {
    margin: 10px 0px;
}
span.ellipsis {
    color: #fff;
}
#overview_table td {
  padding-left: 12px;
  padding-top: 5px;
}

#overview_table_processing > img {
  bottom: 0;
  left: 0;
  margin: 0 auto;
  position: absolute;
  right: 0;
  top: 50%;
  width: 55px;
}
.dataTables_processing {
  background-color: rgba(0, 0, 0, 0.70);
  bottom: 0;
  height: 100%;
  left: 0;
  position: fixed;
  right: 0;
  top: 0;
  width: 100%;
}

</style>
<script src="<?php echo base_url(); ?>assets/mcp/thickbox/javascript/thickbox.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/mcp/js/jquery.dataTables-new.min.js" type="text/javascript"></script>
<script type="text/javascript">
jQuery.noConflict();


function stripslashes(str) {
	  //       discuss at: http://phpjs.org/functions/stripslashes/
	  //      original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	  //      improved by: Ates Goral (http://magnetiq.com)
	  //      improved by: marrtins
	  //      improved by: rezna
	  //         fixed by: Mick@el
	  //      bugfixed by: Onno Marsman
	  //      bugfixed by: Brett Zamir (http://brett-zamir.me)
	  //         input by: Rick Waldron
	  //         input by: Brant Messenger (http://www.brantmessenger.com/)
	  // reimplemented by: Brett Zamir (http://brett-zamir.me)
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

var members = new Array();

</script>

<!-- -----this section is for jquery pagination ------------------------------------- -->
<script type="text/javascript">
var dataLength = <?php echo $company_count;?>;

function urldecode(str) 
{
     if(str)
	 return unescape(str.replace(/\+/g, " "));
	 else
	 return '';
}

jQuery(document).ready(function(){

	//sorting
	jQuery('#overview_table').dataTable({

	 	language: {
      processing: "<img src='<?php echo base_url();?>assets/images/loading2.gif'> Loading...",
    },
    "processing": true,
	 	"searchable":true,
        "serverSide": true,
        "type":"POST",
     	"ajax": 'overview/ajax_companies/',
     	"deferLoading": <?php echo $company_count; ?>,
        "iDisplayLength": 10, 	   
        stateSave: true,
        "sDom": '<"top"lfp<"clear">>rt<"bottom"ip<"clear">>',
        "stateDuration": -1
 		
    });
    jQuery.fn.dataTableExt.oApi.fnStandingRedraw = function(oSettings) {
    jQuery( '#print_load' ).show();
	    if(oSettings.oFeatures.bServerSide === false){
		    var before = oSettings._iDisplayStart;
	        oSettings.oApi._fnReDraw(oSettings);
	        oSettings._iDisplayStart = before;
	        oSettings.oApi._fnCalculateEnd(oSettings);
	    }

	    //draw the 'current' page
	    oSettings.oApi._fnDraw(oSettings);
       jQuery( '#print_load' ).hide();
  };
	var oTable1 = jQuery('#overview_table').dataTable();
	oTable1.fnStandingRedraw();


 });
</script>


<div style="width:100%">
  
  <!-- start of main body -->
  <table width="100%" cellspacing="0" cellpadding="0" border="0">
    <tbody>
      <tr>
        <td valign="top" align="center"><table width="98%" cellspacing="0" cellpadding="0" border="0">
            <tbody>
              <tr>
                <td valign="top" align="center" style="border:#003366 1px solid; padding:15px 0px 0px 0px"><table width="98%" cellspacing="0" 
				 cellpadding="0" border="0">
                    <tbody>
                      <tr>
                        <td align="center" style="padding-bottom:10px">
						
						<table width="100%" cellspacing="0" cellpadding="0" border="0" style="background
						  :url(<?php echo base_url(); ?>assets/mcp/images/bg.jpg) left top repeat-x;" class="page_caption">
                            <tbody>
                              <tr height="26">
                                <td width="50%" align="left"><h3><?php echo _('Overview Clients'); ?></h3></td>
                                <td width="50%" align="right">
								  	<a href="<?php echo base_url();?>mcp/overview/download_client_overview">
         								<?php echo _( "Export in excel" );?>
         							</a>
								  	
								  </a>
								</td>
                              </tr>
                            </tbody>
                          </table></td>
                      </tr>
                      <tr>
                        <td align="center"><table width="100%" cellspacing="0" cellpadding="0" border="0" class="blackMediumNormal">
                            <tbody>
                              <tr>
                                <td><table id="overview_table" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:url(<?php echo base_url(); ?>assets/mcp/images/pink_table_bg.jpg) left repeat;border:1px solid #003366;">
                                    <thead> 
                                      <tr style="background:#003366;">
                                        <td width="3%" class="whiteSmallBold"><?php echo _('ID');?></td>
                                        <td width="15%" class="whiteSmallBold"><?php echo _('Company Name');?></td>
                                        <td width="10%" class="whiteSmallBold"><?php echo _('Company Type');?></td>
                                        <td width="8%" class="whiteSmallBold"><?php echo _('City');?></td>
                                        <td width="9%" class="whiteSmallBold"><?php echo _('Phone');?></td>
                                        <td width="12%" class="whiteSmallBold"><?php echo _('Mail');?></td>
										<td width="8%" class="whiteSmallBold"><?php echo _('Recipes').' / '._( "Products" );?></td>
                                        <td width="7%" class="whiteSmallBold"><?php echo '% '. _('Completed');?></td>
                                      </tr>
									</thead>
									
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
      <tr>
        <td>&nbsp;</td>
      </tr>
    </tbody>
  </table>
<img id="print_load" src="<?php echo base_url();?>assets/cp/images/loading-circle.gif" style="display: none;" />
					   
  <!-- end of main body -->
</div>
