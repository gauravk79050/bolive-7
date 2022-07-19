<!-- FOOTER -->

<div id="footer">
	<?php if($this->session->userdata('menu_type') == 'free'){?>
  	<p><a title="Orders" href="<?php echo base_url()?>cp/cdashboard/page_not_found/orders"><?php echo _('Orders')?></a> | <a title="Categories" href="<?php echo base_url()?>cp/cdashboard/page_not_found/categories"> <?php echo _('Category')?></a> | <a title="Subcategories" href="<?php echo base_url()?>cp/cdashboard/page_not_found/subcategories"><?php echo _('Subcategory')?></a> | <a title="Products" href="<?php echo base_url()?>cp/cdashboard/page_not_found/products"><?php echo _('Products')?></a> | <a title="Clients" href="<?php echo base_url()?>cp/cdashboard/clients"><?php echo _('Clients')?></a> | <a title="Site Settings" href="<?php echo base_url()?>cp/cdashboard/page_not_found/settings"><?php echo _('Settings')?></a> <!--| <a title="Change Password" href="<?php //echo base_url()?>cp/cdashboard/changepassword"><?php //echo _('change password')?></a>--></p>
  	<?php }else{?>
  	<p><a title="Orders" href="<?php echo base_url()?>cp/orders"><?php echo _('Orders')?></a> | <a title="Categories" href="<?php echo base_url()?>cp/categories"> <?php echo _('Category')?></a> | <a title="Subcategories" href="<?php echo base_url()?>cp/subcategories"><?php echo _('Subcategory')?></a> | <a title="Products" href="<?php echo base_url()?>cp/products"><?php echo _('Products')?></a> | <a title="Clients" href="<?php echo base_url()?>cp/clients"><?php echo _('Clients')?></a> | <a title="Site Settings" href="<?php echo base_url()?>cp/settings"><?php echo _('Settings')?></a><!-- | <a title="Change Password" href="<?php //echo base_url()?>cp/cdashboard/changepassword"><?php //echo _('change password')?></a>--></p>
  	<?php }?>
  <p><?php echo _('Copyright &copy; 2010.')?></p>
  <style type="text/css">
  a.back-to-top {
	display: none;
	width: 40px;
	height: 40px;
	text-indent: -9999px;
	position: fixed;
	z-index: 999;
	right: 20px;
	bottom: 20px;
	background: #6699CC url("<?php echo base_url();?>/assets/cp/images/up-arrow.png") no-repeat center 43%;
	-webkit-border-radius: 20px;
	-moz-border-radius: 20px;
	border-radius: 30px;
}
a:hover.back-to-top {
	background-color: #6699CC;
}
</style>
  <script type="text/javascript">
	// create the back to top button
	jQuery('body').prepend('<a href="#" class="back-to-top">Back to Top</a>');

	var amountScrolled = 300;

	jQuery(window).scroll(function() {
		if ( jQuery(window).scrollTop() > amountScrolled ) {
			jQuery('a.back-to-top').fadeIn('slow');
		} else {
			jQuery('a.back-to-top').fadeOut('slow');
		}
	});

	jQuery('a.back-to-top, a.simple-back-to-top').click(function() {
		jQuery('html, body').animate({
			scrollTop: 0
		}, 700);
		return false;
	});
</script>
  <p><a href="#" class="simple-back-to-top"><?php echo _('Back to Top');?></a></p>
</div>
<!-- FOOTER -->
</div>

<style type="text/css">
#sidebarSet{
	text-decoration:none;
}
#sidebarController{
	background: none repeat scroll 0 0 #393939;
    color: #FFFFFF;
    font-size: 14px;
    font-weight: bold;
    padding: 5px 3px;
    position: fixed;
    right: 0;
    top: 200px;
    width: 20px;
	text-align:center;
	text-decoration:none;
	/*border:4px solid #fff;
	border-right:0px;*/
}
</style>


<script type="text/javascript">
jQuery(document).ready(function($){

   var display_sidebar = ( Get_Cookie('display_sidebar') == 'hide' ) ? 'hide' : 'show';

   if(display_sidebar == 'hide')
   {
       $('#content').attr('style','width:100%;');
	   $('#sidebar').hide();
	   $('#sidebarController').html('&laquo;<br /><br />S<br />I<br />D<br />E<br />B<br />A<br />R<br />');
   }

   $('#sidebarSet').click(function(){

	  //$('#sidebar').toggle();

	  if( $('#sidebar').css('display') == 'none')
      {
	     //$('#content').css('width','970px');
		 $('#content').attr('style','');
		 $('#sidebar').show();
		 $('#sidebarController').html('&raquo;<br /><br />S<br />I<br />D<br />E<br />B<br />A<br />R<br />');

		 Set_Cookie( 'display_sidebar', 'show', '', '/', '', '' );

	  }
	  else
	  {
	     //$('#content').css('width','100%');
		 $('#content').attr('style','width:100%;');
		 $('#sidebar').hide();
		 $('#sidebarController').html('&laquo;<br /><br />S<br />I<br />D<br />E<br />B<br />A<br />R<br />');

		 Set_Cookie( 'display_sidebar', 'hide', '', '/', '', '' );

	  }

   });
});

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
</script>

<a href="javascript:void(0);" id="sidebarSet">
<div id="sidebarController">
&raquo;<br /><br />S<br />I<br />D<br />E<br />B<br />A<br />R<br />
</div>
</a>
</body></html>