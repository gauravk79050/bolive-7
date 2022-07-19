
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/css/data_table.css" media="screen">

<script src="<?php echo base_url();?>assets/js/jqtable.js"></script>


<script type="text/javascript">
$(function() {
    $('#log_table').dataTable({
    	"aaSorting": [[0,'desc']],
    	"oLanguage": {
    		"sProcessing": "Bezig...",
    	    "sLengthMenu": "_MENU_ resultaten weergeven",
    	    "sZeroRecords": "Geen resultaten gevonden",
    	    "sInfo": "_START_ tot _END_ van _TOTAL_ resultaten",
    	    "sInfoEmpty": "Geen resultaten om weer te geven",
    	    "sInfoFiltered": " (gefilterd uit _MAX_ resultaten)",
    	    "sInfoPostFix": "",
    	    "sSearch": "Zoeken:",
    	    "sEmptyTable": "Geen resultaten aanwezig in de tabel",
    	    "sInfoThousands": ".",
    	    "sLoadingRecords": "Een moment geduld aub - bezig met laden...",
    	    "oPaginate": {
    	        "sFirst": "Eerste",
    	        "sLast": "Laatste",
    	        "sNext": "Volgende",
    	        "sPrevious": "Vorige"
    	    }
        }
    });

});
</script>

<style>
	.fc-first th {
	    background: none repeat scroll 0 0 black !important;
	    border: medium none !important;
	}

	.dataTables_filter input {
	border: 1px solid #ccccff;
    border-radius: 0;
    padding: 5px;
    margin-right: 20px;
	}
	.dataTables_length select{
	display:inline;

    padding:3px;
    margin: 0;
    -webkit-border-radius:4px;
    -moz-border-radius:4px;
    border-radius:4px;
    -webkit-box-shadow: 0 3px 0 #ccc, 0 -1px #fff inset;
    -moz-box-shadow: 0 3px 0 #ccc, 0 -1px #fff inset;
    box-shadow: 0 3px 0 #ccc, 0 -1px #fff inset;
    background: #f8f8f8;
    color:#888;
    border:none;
    outline:none;
    display: inline-block;
    -webkit-appearance:none;
    -moz-appearance:none;
    appearance:none;
    cursor:pointer;

	}
	.dataTables_length label {
    margin-left: 20px;
	}

	.dataTable th{
	border-top:1px solid #9999FF;
	}
</style>


 <!-- MAIN -->
<div id="main">
	<div id="main-header">
    	<h2><?php echo _('Recently added FoodDESK products'); ?></h2>
      	<span class="breadcrumb"><a href="<?php echo base_url()?>cp/cdashboard"><?php echo _('Home')?></a> &raquo; <?php echo _('Products')?></span>
	</div>

	<div id="content">
    	<div id="content-container">
        	<div class="box">
          		<h3><?php echo _('Products'); ?></h3>
          		<div class="table">
            		<table id="log_table" cellspacing="0">
              			<thead>
                			<tr>
                  				<th style="width:20%" ><?php echo _('Date added'); ?></th>
                  				<th style="width:35%"><?php echo _('Product Name'); ?></th>
                  				<th style="width:15%"><?php echo _('Producer Name'); ?></th>
                  				<th style="width:15%"><?php echo _('supplier Name'); ?></th>
                  				<th style="width:15%"><?php echo _('EAN Code'); ?></th>
                			</tr>
						</thead>
              			<tbody>
                			<?php if(sizeof($products) > 0): ?>
								<?php foreach($products as $product):
									$pro_name = "";
									if($language==2)
									{
										$pro_name = $product->p_name_dch;
									}
									elseif($language==3)
									{
										$pro_name = $product->p_name_fr;
									}
									elseif($language==1)
									{
										$pro_name = $product->p_name;
									}

								?>

								<tr >
									<td style="color:rgba(0,0,0,0.6)" style="width:20%">
										<span  class="pro_name">&nbsp;<?php echo $product->approval_date_time; ?></span>
									</td>
									<td style="width:35%">
										<strong>&nbsp;<?php echo $pro_name; ?></strong>
									</td style="width:15%">
									<td>
										&nbsp;<?php echo $product->s_name; ?>
									</td>
									<td style="width:15%">
										&nbsp;<?php echo $product->rs_name; ?>
									</td>
									<td style="width:15%">
										&nbsp;<?php echo $product->barcode; ?>
									</td>

	                			</tr>
								<?php endforeach; ?>

							<?php endif; ?>
						</tbody>
            		</table>

          		</div><!-- /table -->
        	</div><!-- /box -->
      	</div><!-- /content-container -->
	</div><!-- /content -->


	<div id="my_tb_holder">

	</div>


	<div id="rename_master" style="display: none">

	</div>
