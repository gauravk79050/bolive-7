 <!-- MAIN -->
 <script type="text/javascript">
 	jQuery(document).ready(function(){
 		jQuery(".make_order").live("click",function(){
 			jQuery.post(
 						'<?php echo base_url();?>cp/gouden_tips/make_order',
 						{'tip_id':jQuery(this).attr("rel")},
 						function(response){
 							alert(response);
 							self.parent.tb_remove();
 						}
 					);
 		});

 		jQuery(".thickboxed").click(function(){
 			tb_show("<?php echo _("Confirm order"); ?>","#TB_inline?height=300&width=300&inlineId=order_div_"+jQuery(this).attr('rel'));
 		});
 	});
</script>
 
<style>
	.post {
 		border-bottom: 4px solid #CCCCCC;
 	}
 
 	.make_order{
 		background: none repeat scroll 0 0 #CCCCCC;
	    border: 1px solid #000000;
	    display: inline-block;
	    font-weight: bold;
	    height: 20px;
	    width: 100px;
	    text-decoration: none;
	    padding-top: 2px;
 	}
</style>

  <div id="main">
    <div id="main-header">
      <h2><?php echo _('Printings')?></h2>
      <span class="breadcrumb"><a href="index.php?view=orders"><?php echo _('Home')?></a> &raquo; <?php echo _('Printings')?></span> </div>
    <div id="content">
      <div id="content-container">
      <div class="box">
      	<h3><?php echo _('Printings')?></h3>
       	<div class="inside">
       		<?php if(!empty($flyers)){?>
        	<p></p>
        	<ul class="printings">
        		<?php foreach ($flyers as $flyer){?>
        		<li>
        			<div class="post_div">
        				<div class="post-img"><img width="160" height="245" src="<?php echo base_url();?>assets/mcp/images/flyers/<?php echo $flyer->image;?>"></div>
        				<div class="post-desc"><p><?php echo $flyer->description;?></p></div>
        				<div class="post-price"><span class="price"><?php echo $flyer->price;?> &euro;</span> (verzending inbegrepen)</div>
        				<div class="post-link">
        					<a href="javascript:void(0);" rel="<?php echo $flyer->id;?>" class="thickboxed">NU BESTELLEN</a>
        					<div id="order_div_<?php echo $flyer->id;?>" style="display: none;">
        						<?php echo _("After confirming the order we will keep you updated via mail.");?>
        						<br /><br />
        						<?php echo _("I want to order")?>: <u><?php echo $flyer->name;?></u>
        						<br />
        						<p style="text-align:center; margin:5px 0;">
        							<a href="javascript: void(0);" class="make_order" rel="<?php echo $flyer->id;?>"><?php echo _("Confirm order");?></a>
        						</p>
        					</div>
        				</div>
        			</div>
        		</li>
        		<?php }?>
        	</ul>
        	<?php }else{?>
        	<p class='error'><?php echo _("No item to print yet.");?></p>
        	<?php }?>   
        	<div class="clear"></div>             
      	</div>
      </div>
      </div>
      </div>
    <!-- /content -->
