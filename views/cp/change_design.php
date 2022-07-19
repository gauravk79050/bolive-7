<script type="text/javascript" src="<?php echo base_url()?>assets/cp/new_js/colorpicker/jquery.miniColors.js"></script>
<link type="text/css" rel="stylesheet" href="<?php echo base_url()?>assets/cp/new_css/colorpicker/jquery.miniColors.css" />

<link type="text/css" href="<?php echo base_url()?>assets/cp/css/ui-lightness/jquery-ui-1.8.16.custom.css" rel="stylesheet" />	
<script type="text/javascript" src="<?php echo base_url()?>assets/cp/js/jquery-ui-1.8.16.custom.min.js"></script>
		
<script type="text/javascript">
	jQuery.noConflict();
	jQuery(document).ready(function($){
		$(".colors").miniColors();
		$('#tabs').tabs();	
	});
</script>

<style type="text/css">
	select{ 
		border: 1px solid #CCCCCC !important;
		width:150px !important
	}

  ul#theme-list{
  	list-style:none;
	margin:0px;
	padding:0px;
  }
  
  ul#theme-list li{
    float: left;
    list-style: none outside none;
    margin-bottom: 10px;
    margin-left: 20px;
    width: 220px;
  }
  
  ul#theme-list li strong{
  	font-weight: bold;
	font-size:14px;
  }
  
  ul#theme-list li img{
  	margin:0 auto;
	height: 160px;
    width: 190px;
  }
</style>

  <!-- MAIN -->
<div id="main">
	<div id="main-header">
	
    	<h2><?php echo _('Change the Design')?></h2>
		<span class="breadcrumb"><a href="<?php echo base_url()?>cp/cdashboard"><?php echo _('Home')?></a> &raquo; <?php echo('change design')?></span> 
	</div>
	
	<?php $messages = $this->messages->get();  ?>
	
	<div id="messages">
	<?php
	// display all messages
	if (is_array($messages)):
		foreach ($messages as $type => $msgs):
			if (count($msgs > 0)):
				foreach ($msgs as $message):
					echo ('<span class="' .  $type .'">' . $message . '</span>');
			   endforeach;
		   endif;
		endforeach;
	endif;
	?>
	</div>
	
	
	<div id="content">
	<div id="content-container">
	<div class="box">
		<h3> <?php echo _('Themes Section')?></h3>
      	<div style="padding: 0px; display: block;" class="inside">
        	<div class="table">
          		<form action="<?php echo base_url()?>cp/cdashboard/settings" enctype="multipart/form-data" method="post" id="frm_template" name="frm_template">
            	
            		<br>

					<?php $theme_id = $general_settings[0]->theme_id; ?>
					
					<?php if(!empty($themes)) { ?>
				       <ul id="theme-list">
					<?php foreach($themes as $theme) { //if($theme->id!=1) { ?>
					      <li>
						      <input type="radio"id="default" name="theme_id"  value="<?php echo $theme->id; ?>"  <?php if($general_settings[0]->theme_id == $theme->id): echo 'checked="checked"'; endif;?>/>
							  &nbsp;
							  <strong><?php echo $theme->theme_name;?></strong>
							  <br /><br />
							  <img src="<?php echo $theme->theme_img;?>" class="theme_img"/>
							  <br /><br />
							  <p><?php echo $theme->theme_description;?></p>
						  </li>
					<?php } //} ?>
					      <div style="clear:both;"></div>
					   </ul>
					   <input type="submit" value="UPDATE" class="submit" id="btn_update" name="btn_update" style="margin-bottom: 20px; margin-left: 20px;">
                  	   <input type="hidden" name="act" id="act" value="edit_theme_settings" />
					<?php } else { ?>
					   <p>Sorry ! No themes available.</p>
					<?php } ?>
				</form>
          		
        	</div><!--- /table --->
		</div><!--- /inside ---->
    </div>
	
	<?php 
		  if( $theme_id ==0 )
		  {
			 $background_1 = 'F6F6F6';
			 $color_1 = '333333';
			 $width_1 = '230';
			 $font_family_1 = 'arial';
			 $font_size_1 = '14';
			 $font_style_1 = 'normal';
			 $font_weight_1 = 'normal';
			 $text_decoration_1 = 'none';
			 
			 $background_2 = 'F6F6F6';
			 $color_2 = '333333';
			 $width_2 = '230';
			 $font_family_2 = 'arial';
			 $font_size_2 = '14';
			 $font_style_2 = 'normal';
			 $font_weight_2 = 'normal';
			 $text_decoration_2 = 'none';
			 
			 $background_3 = 'FFFFFF';
			 $color_3 = '333333';
			 $width_3 = '630';
			 $font_family_3 = 'arial';
			 $font_size_3 = '14';
			 $font_style_3 = 'normal';
			 $font_weight_3 = 'normal';
			 $text_decoration_3 = 'none';
		  }
		  else
		  if( $theme_id ==2 )
		  {
			 $background_1 = 'F6F6F6';
			 $color_1 = '333333';
			 $width_1 = '230';
			 $font_family_1 = 'arial';
			 $font_size_1 = '14';
			 $font_style_1 = 'normal';
			 $font_weight_1 = 'normal';
			 $text_decoration_1 = 'none';
			 
			 $background_2 = 'F6F6F6';
			 $color_2 = '333333';
			 $width_2 = '230';
			 $font_family_2 = 'arial';
			 $font_size_2 = '14';
			 $font_style_2 = 'normal';
			 $font_weight_2 = 'normal';
			 $text_decoration_2 = 'none';
			 
			 $background_3 = 'FFFFFF';
			 $color_3 = '333333';
			 $width_3 = '630';
			 $font_family_3 = 'arial';
			 $font_size_3 = '14';
			 $font_style_3 = 'normal';
			 $font_weight_3 = 'normal';
			 $text_decoration_3 = 'none';
		  }
		  else
		  if( $theme_id ==3 )
		  {
			 $background_1 = 'F6F6F6';
			 $color_1 = '333333';
			 $width_1 = '230';
			 $font_family_1 = 'arial';
			 $font_size_1 = '14';
			 $font_style_1 = 'normal';
			 $font_weight_1 = 'normal';
			 $text_decoration_1 = 'none';
			 
			 $background_2 = 'F6F6F6';
			 $color_2 = '333333';
			 $width_2 = '230';
			 $font_family_2 = 'arial';
			 $font_size_2 = '14';
			 $font_style_2 = 'normal';
			 $font_weight_2 = 'normal';
			 $text_decoration_2 = 'none';
			 
			 $background_3 = 'FFFFFF';
			 $color_3 = '333333';
			 $width_3 = '630';
			 $font_family_3 = 'arial';
			 $font_size_3 = '14';
			 $font_style_3 = 'normal';
			 $font_weight_3 = 'normal';
			 $text_decoration_3 = 'none';
		  }
		  else
		  if( $theme_id ==4 )
		  {
		 	 $background_1 = 'F6F6F6';
			 $color_1 = '333333';
			 $width_1 = '230';
			 $font_family_1 = 'arial';
			 $font_size_1 = '14';
			 $font_style_1 = 'normal';
			 $font_weight_1 = 'normal';
			 $text_decoration_1 = 'none';
			 
			 $background_2 = 'F6F6F6';
			 $color_2 = '333333';
			 $width_2 = '230';
			 $font_family_2 = 'arial';
			 $font_size_2 = '14';
			 $font_style_2 = 'normal';
			 $font_weight_2 = 'normal';
			 $text_decoration_2 = 'none';
			 
			 $background_3 = 'FFFFFF';
			 $color_3 = '333333';
			 $width_3 = '630';
			 $font_family_3 = 'arial';
			 $font_size_3 = '14';
			 $font_style_3 = 'normal';
			 $font_weight_3 = 'normal';
			 $text_decoration_3 = 'none';
	  	  }
	  	  else
  		  if( $theme_id ==5 )
  		  {
  			 $background_1 = 'F6F6F6';
			 $color_1 = '333333';
			 $width_1 = '230';
			 $font_family_1 = 'arial';
			 $font_size_1 = '14';
			 $font_style_1 = 'normal';
			 $font_weight_1 = 'normal';
			 $text_decoration_1 = 'none';
			 
			 $background_2 = 'F6F6F6';
			 $color_2 = '333333';
			 $width_2 = '230';
			 $font_family_2 = 'arial';
			 $font_size_2 = '14';
			 $font_style_2 = 'normal';
			 $font_weight_2 = 'normal';
			 $text_decoration_2 = 'none';
			 
			 $background_3 = 'FFFFFF';
			 $color_3 = '333333';
			 $width_3 = '630';
			 $font_family_3 = 'arial';
			 $font_size_3 = '14';
			 $font_style_3 = 'normal';
			 $font_weight_3 = 'normal';
			 $text_decoration_3 = 'none';
  		  }
  		  else
  		  if( $theme_id ==6 )
  		  {
  			 $background_1 = 'F6F6F6';
			 $color_1 = '333333';
			 $width_1 = '230';
			 $font_family_1 = 'arial';
			 $font_size_1 = '14';
			 $font_style_1 = 'normal';
			 $font_weight_1 = 'normal';
			 $text_decoration_1 = 'none';
			 
			 $background_2 = 'F6F6F6';
			 $color_2 = '333333';
			 $width_2 = '230';
			 $font_family_2 = 'arial';
			 $font_size_2 = '14';
			 $font_style_2 = 'normal';
			 $font_weight_2 = 'normal';
			 $text_decoration_2 = 'none';
			 
			 $background_3 = 'FFFFFF';
			 $color_3 = '333333';
			 $width_3 = '630';
			 $font_family_3 = 'arial';
			 $font_size_3 = '14';
			 $font_style_3 = 'normal';
			 $font_weight_3 = 'normal';
			 $text_decoration_3 = 'none';
    	  }
	?>
	
	<div class="box">
		<h3> <?php echo _('Style Settings')?></h3>
      	<div style="padding: 0px; display: block;" class="inside">
		
		<div id="tabs">
			<ul>
				<li><a href="#tabs-1"><?php echo _('Edit Stylesheet : Simple Mode')?></a></li>
				<li><a href="#tabs-2"><?php echo _('Edit Stylesheet : Advanced Mode')?></a></li>
			</ul>
			
			<!--Tab 1 : Starts -->
		
			<div id="tabs-1">
			<div id="content-container">
				<form name="section_designs" id="section_designs" action="<?php echo base_url()?>cp/cdashboard/section_designs" method="post">
					<div class="boxed">
						<h3><?php echo _('Login Section')?></h3>
						<div class="inside" style="display: none;">
							<div class="box">
								<div class="table">
									<table cellspacing="0">
										<tbody>
											<tr>
												<td width="20%" class="textlabel"><?php echo _('Background')?></td>
												<td style="padding-top:10px; vertical-align:bottom">
												<input type="text" class="text short colors" id="background_1" name="background_1" value="<?php if($login_design && $login_design->background){ echo $login_design->background; }else{ echo '#'.$background_1; } ?>"></td>
											</tr>
											<tr>
												<td class="textlabel"><?php echo _('Color')?></td>
												<td><input type="text" class="text short colors" id="color_1" name="color_1" value="<?php if($login_design && $login_design->color): echo $login_design->color; else: echo '#'.$color_1; endif; ?>"></td>
											</tr>
											<tr>
												<td class="textlabel"><?php echo _('width')?></td>
												<td><input type="text" class="text short" id="width_1" name="width_1" value="<?php if($login_design && $login_design->width): echo $login_design->width; else: echo $width_1; endif; ?>">&nbsp;px</td>
											</tr>
											<tr>
												<td class="textlabel"><?php echo _('Font-family')?></td>
												<td><select name="font-family_1" id="font-family_1" class="text short">
														<option value="0"><?php echo _('select')?></option>
														<option value="Verdana" <?php if($login_design&&$login_design->{'font-family'}=="Verdana"):?>selected="selected"<?php endif;?>><?php echo _('Verdana')?></option>
														<option value="Helvetica" <?php if($login_design&&$login_design->{'font-family'}=="Helvetica"):?>selected="selected"<?php endif;?>><?php echo _('Helvetica')?></option>
														<option value="Georgia" <?php if($login_design&&$login_design->{'font-family'}=="Georgia"):?>selected="selected"<?php endif;?>><?php echo _('Georgia')?></option>
														<option value="arial" <?php if($login_design && $login_design->{'font-family'} ) { if($login_design->{'font-family'}=="arial"): echo 'selected="selected"'; endif; } elseif($font_family_1=='arial') { echo 'selected="selected"'; } ?> ><?php echo _('Arial')?></option>
														<option value="new times roman" <?php if($login_design&&$login_design->{'font-family'}=="new times roman"):?>selected="selected"<?php endif;?>><?php echo _('New Times Roman')?></option>
														<option value="comic sence" <?php if($login_design&&$login_design->{'font-family'}=="comic sence"):?>selected="selected"<?php endif;?>><?php echo _('Comic Sence')?></option>
														<option value="wingdings" <?php if($login_design&&$login_design->{'font-family'}=="wingdings"):?>selected="selected"<?php endif;?>><?php echo _('Wingdings')?></option>
													</select>
												</td>
												
											</tr>
											<tr>
												<td class="textlabel"><?php echo _('Font-size')?></td>
												<td><select id="font-size_1" name="font-size_1" class="text short">
														<option value="0"><?php echo _('Select')?></option>
														<option value="10" <?php if($login_design&&$login_design->{'font-size'}=="10"):?>selected="selected"<?php endif;?>>10</option>
														<option value="12" <?php if($login_design&&$login_design->{'font-size'}=="12"):?>selected="selected"<?php endif;?>>12</option>
														<option value="14" <?php if($login_design&&$login_design->{'font-size'}) {if($login_design&&$login_design->{'font-size'}=="14"):echo 'selected="selected"'; endif; } elseif($font_size_1==14) {echo 'selected="selected"';} ?>>14</option>
														<option value="16" <?php if($login_design&&$login_design->{'font-size'}=="16"):?>selected="selected"<?php endif;?>>16</option>
														<option value="20" <?php if($login_design&&$login_design->{'font-size'}=="20"):?>selected="selected"<?php endif;?>>20</option>
														<option value="24" <?php if($login_design&&$login_design->{'font-size'}=="24"):?>selected="selected"<?php endif;?>>24</option>
														<option value="30" <?php if($login_design&&$login_design->{'font-size'}=="30"):?>selected="selected"<?php endif;?>>30</option>
													</select>
												</td>
												
											</tr>
											<tr>
												<td class="textlabel"><?php echo _('Font-style')?></td>
												<td><select id="font-style_1" name="font-style_1" class="text short">
														<option value="0"><?php echo _('Select')?></option>
														<option value="bold" <?php if($login_design&&$login_design->{'font-style'}=="bold"):?>selected="selected"<?php endif;?>><?php echo _('bold')?></option>
														<option value="normal" <?php if($login_design&&$login_design->{'font-style'}) {if($login_design&&$login_design->{'font-style'}=="normal"):echo 'selected="selected"'; endif; } elseif($font_style_1=='normal') {echo 'selected="selected"';} ?>><?php echo _('normal')?></option>
														<option value="italic" <?php if($login_design&&$login_design->{'font-style'}=="italic"):?>selected="selected"<?php endif;?>><?php echo _('italic')?></option>
														<option value="bold italic" <?php if($login_design&&$login_design->{'font-style'}=="bold italic"):?>selected="selected"<?php endif;?>><?php echo _('bold italic')?></option>
													</select>
												</td>
												
											</tr>
											<tr>
												<td class="textlabel"><?php echo _('Font-weight')?></td>
												<td><select id="font-weight_1" name="font-weight_1" class="text short">
														<option value="0"><?php echo _('select')?></option>
														<option value="bold" <?php if($login_design&&$login_design->{'font-weight'}=="bold"):?>selected="selected"<?php endif;?>><?php echo _('bold')?></option>
														<option value="bolder" <?php if($login_design&&$login_design->{'font-weight'}=="bolder"):?>selected="selected"<?php endif;?>><?php echo _('bolder')?></option>														
														<option value="normal" <?php if($login_design&&$login_design->{'font-weight'}) {if($login_design&&$login_design->{'font-weight'}=="normal"):echo 'selected="selected"'; endif; } elseif($font_weight_1=='normal') {echo 'selected="selected"';} ?>><?php echo _('normal')?></option>												
														<option value="lighter" <?php if($login_design&&$login_design->{'font-weight'}=="lighter"):?>selected="selected"<?php endif;?>><?php echo _('lighter')?></option>
														<option value="inherit" <?php if($login_design&&$login_design->{'font-weight'}=="inherit"):?>selected="selected"<?php endif;?>><?php echo _('inherit')?></option>
													</select>
												</td>
											</tr>
											<tr>
											<td class="textlabel"><?php echo _('Text Decoration')?></td>
											<td><select id="text-decoration_1" name="text-decoration_1" class="text short">
													<option value="0"><?php echo _('select')?></option>
													<option value="none" <?php if($login_design&&$login_design->{'text-decoration'}){ if($login_design&&$login_design->{'text-decoration'}=="none"):echo 'selected="selected"'; endif; } elseif($text_decoration_1=='none') {echo 'selected="selected"';} ?>><?php echo _('none')?></option>
													<option value="underline" <?php if($login_design&&$login_design->{'text-decoration'}=="underline"):?>selected="selected"<?php endif;?>><?php echo _('underline')?></option>
													<option value="overline" <?php if($login_design&&$login_design->{'text-decoration'}=="overline"):?>selected="selected"<?php endif;?>><?php echo _('overline')?></option>
													<option value="blink" <?php if($login_design&&$login_design->{'text-decoration'}=="blink"):?>selected="selected"<?php endif;?>><?php echo _('blink')?></option>
												</select>
											</td>
											
											</tr>
							  
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
					<div class="boxed">
						<h3><?php echo _('Shopping Cart')?></h3>
						<div class="inside" style="display: none;">
							<div class="box">
								<div class="table">
									<table cellspacing="0">
										<tbody>
											<tr>
												<td width="20%" class="textlabel"><?php echo _('Background')?></td>
												<td style="padding-top:10px; vertical-align:bottom">											                                                   <input type="text" class="text short colors" id="background_2" name="background_2" value="<?php if($cart_design && $cart_design->background){ echo $cart_design->background; }else{ echo '#'.$background_2; } ?>">
												</td>
											</tr>
											<tr>
												<td class="textlabel"><?php echo _('Color')?></td>
												<td><input type="text" class="text short colors" id="color_2" name="color_2" value="<?php if($cart_design && $cart_design->color): echo $cart_design->color; else: echo '#'.$color_2; endif; ?>"></td>
											</tr>
											<tr>
												<td class="textlabel"><?php echo _('width')?></td>
												<td><input type="text" class="text short" id="width_2" name="width_2" value="<?php if($cart_design && $cart_design->width): echo $cart_design->width; else: echo $width_2; endif; ?>">&nbsp;px</td>
											</tr>
											<tr>
												<td class="textlabel"><?php echo _('Font-family')?></td>
												<td><select name="font-family_2" id="font-family_2" class="text short">
														<option value="0"><?php echo _('select')?></option>
														<option value="Verdana" <?php if($cart_design&&$cart_design->{'font-family'}=="Verdana"):?>selected="selected"<?php endif;?>><?php echo _('Verdana')?></option>
														<option value="Helvetica" <?php if($cart_design&&$cart_design->{'font-family'}=="Helvetica"):?>selected="selected"<?php endif;?>><?php echo _('Helvetica')?></option>
														<option value="Georgia" <?php if($cart_design&&$cart_design->{'font-family'}=="Georgia"):?>selected="selected"<?php endif;?>><?php echo _('Georgia')?></option>
														<option value="arial" <?php if($login_design && $login_design->{'font-family'} ) { if($login_design->{'font-family'}=="arial"): echo 'selected="selected"'; endif; } elseif($font_family_2=='arial') { echo 'selected="selected"'; } ?> ><?php echo _('Arial')?></option>
														<option value="new times roman" <?php if($cart_design&&$cart_design->{'font-family'}=="new times roman"):?>selected="selected"<?php endif;?>><?php echo _('new times roman')?></option>
														<option value="comic sence" <?php if($cart_design&&$cart_design->{'font-family'}=="comic sence"):?>selected="selected"<?php endif;?>><?php echo _('comic sence')?></option>
														<option value="wingdings" <?php if($cart_design&&$cart_design->{'font-family'}=="wingdings"):?>selected="selected"<?php endif;?>><?php echo _('wingdings')?></option>
													</select>
												</td>
												
											</tr>
											<tr>
												<td class="textlabel"><?php echo _('Font-size')?></td>
												<td><select id="font-size_2" name="font-size_2" class="text short">
														<option value="0"><?php echo _('Select')?></option>
														<option value="10" <?php if($cart_design&&$cart_design->{'font-size'}=="10"):?>selected="selected"<?php endif;?>>10</option>
														<option value="12" <?php if($cart_design&&$cart_design->{'font-size'}=="12"):?>selected="selected"<?php endif;?>>12</option>
														<option value="14" <?php if($cart_design&&$cart_design->{'font-size'}) {if($cart_design&&$cart_design->{'font-size'}=="14"):echo 'selected="selected"'; endif; } elseif($font_size_2==14) {echo 'selected="selected"';} ?>>14</option>
														<option value="16" <?php if($cart_design&&$cart_design->{'font-size'}=="16"):?>selected="selected"<?php endif;?>>16</option>
														<option value="20" <?php if($cart_design&&$cart_design->{'font-size'}=="20"):?>selected="selected"<?php endif;?>>20</option>
														<option value="24" <?php if($cart_design&&$cart_design->{'font-size'}=="24"):?>selected="selected"<?php endif;?>>24</option>
														<option value="30" <?php if($cart_design&&$cart_design->{'font-size'}=="30"):?>selected="selected"<?php endif;?>>30</option>
													</select>
												</td>
												
											</tr>
											<tr>
												<td class="textlabel"><?php echo _('Font-style')?></td>
												<td><select id="font-style_2" name="font-style_2" class="text short">
														<option value="0"><?php echo _('Select')?></option>
														<option value="bold" <?php if($cart_design&&$cart_design->{'font-style'}=="bold"):?>selected="selected"<?php endif;?>><?php echo _('bold')?></option>
														<option value="normal" <?php if($cart_design&&$cart_design->{'font-style'}) {if($cart_design&&$cart_design->{'font-style'}=="normal"):echo 'selected="selected"'; endif; } elseif($font_style_2=='normal') {echo 'selected="selected"';} ?>><?php echo _('normal')?></option>
														<option value="italic" <?php if($cart_design&&$cart_design->{'font-style'}=="italic"):?>selected="selected"<?php endif;?>><?php echo _('italic')?></option>
														<option value="bold italic" <?php if($cart_design&&$cart_design->{'font-style'}=="bold italic"):?>selected="selected"<?php endif;?>><?php echo _('bold italic')?></option>
													</select>
												</td>
												
											</tr>
											<tr>
												<td class="textlabel"><?php echo _('Font-weight')?></td>
												<td><select id="font-weight_2" name="font-weight_2" class="text short">
														<option value="0"><?php echo _('Select')?></option>
														<option value="bold" <?php if($cart_design&&$cart_design->{'font-weight'}=="bold"):?>selected="selected"<?php endif;?>><?php echo _('bold')?></option>
														<option value="bolder" <?php if($cart_design&&$cart_design->{'font-weight'}=="bolder"):?>selected="selected"<?php endif;?>><?php echo _('bolder')?></option>														
														<option value="normal" <?php if($cart_design&&$cart_design->{'font-weight'}) {if($cart_design&&$cart_design->{'font-weight'}=="normal"):echo 'selected="selected"'; endif; } elseif($font_weight_2=='normal') {echo 'selected="selected"';} ?>><?php echo _('normal')?></option>												
														<option value="lighter" <?php if($cart_design&&$cart_design->{'font-weight'}=="lighter"):?>selected="selected"<?php endif;?>><?php echo _('lighter')?></option>
														<option value="inherit" <?php if($cart_design&&$cart_design->{'font-weight'}=="inherit"):?>selected="selected"<?php endif;?>><?php echo _('inherit')?></option>
													</select>
												</td>
												
											</tr>
											<tr>
												<td class="textlabel"><?php echo _('Text Decoration')?></td>
												<td><select id="text-decoration_2" name="text-decoration_2" class="text short">
														<option value="0"><?php echo _('Select')?></option>
														<option value="none" <?php if($cart_design&&$cart_design->{'text-decoration'}){ if($cart_design&&$cart_design->{'text-decoration'}=="none"):echo 'selected="selected"'; endif; } elseif($text_decoration_2=='none') {echo 'selected="selected"';} ?>><?php echo _('none')?></option>
														<option value="underline" <?php if($cart_design&&$cart_design->{'text-decoration'}=="underline"):?>selected="selected"<?php endif;?>><?php echo _('underline')?></option>
														<option value="overline" <?php if($cart_design&&$cart_design->{'text-decoration'}=="overline"):?>selected="selected"<?php endif;?>><?php echo _('overline')?></option>
														<option value="blink" <?php if($cart_design&&$cart_design->{'text-decoration'}=="blink"):?>selected="selected"<?php endif;?>><?php echo _('blink')?></option>
													</select>
												</td>
												
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
					<div class="boxed">
						<h3><?php echo _('Main Section')?></h3>
						<div class="inside" style="display: none;">
							<div class="box">
								<div class="table">
									<table cellspacing="0">
										<tbody>
											<tr>
												<td width="20%" class="textlabel"><?php echo _('Background')?></td>
												<td style="padding-top:10px; vertical-align:bottom">
												<input type="text" class="text short colors" id="background_3" name="background_3" value="<?php if($main_section_design && $main_section_design->background){ echo $main_section_design->background; }else{ echo '#'.$background_3; } ?>">
												</td>
											</tr>
											<tr>
												<td class="textlabel"><?php echo _('Color')?></td>
												<td><input type="text" class="text short colors" id="color_3" name="color_3" value="<?php if($main_section_design && $main_section_design->color): echo $main_section_design->color; else: echo '#'.$color_3; endif; ?>"></td>
											</tr>
											<tr>
												<td class="textlabel"><?php echo _('width')?></td>
												<td><input type="text" class="text short" id="width_3" name="width_3" value="<?php if($main_section_design && $main_section_design->width): echo $main_section_design->width; else: echo $width_3; endif; ?>">&nbsp;px</td>
											</tr>
											<tr>
												<td class="textlabel"><?php echo _('Font-family')?></td>
												<td><select name="font-family_3" id="font-family_3" class="text short">
														<option value="0"><?php echo _('select')?></option>
														
														<option value="Verdana" <?php if($main_section_design&&$main_section_design->{'font-family'}=="Verdana"):?>selected="selected"<?php endif;?>><?php echo _('Verdana')?></option>
														<option value="Helvetica" <?php if($main_section_design&&$main_section_design->{'font-family'}=="Helvetica"):?>selected="selected"<?php endif;?>><?php echo _('Helvetica')?></option>
														<option value="Georgia" <?php if($main_section_design&&$main_section_design->{'font-family'}=="Georgia"):?>selected="selected"<?php endif;?>><?php echo _('Georgia')?></option>
														<option value="arial" <?php if($main_section_design && $main_section_design->{'font-family'} ) { if($main_section_design->{'font-family'}=="arial"): echo 'selected="selected"'; endif; } elseif($font_family_3=='arial') { echo 'selected="selected"'; } ?> ><?php echo _('Arial')?></option>
														<option value="new times roman" <?php if($main_section_design&&$main_section_design->{'font-family'}=="new times roman"):?>selected="selected"<?php endif;?>><?php echo _('new times roman')?></option>
														<option value="comic sence" <?php if($main_section_design&&$main_section_design->{'font-family'}=="comic sence"):?>selected="selected"<?php endif;?>><?php echo _('comic sence')?></option>
														<option value="wingdings" <?php if($main_section_design&&$main_section_design->{'font-family'}=="wingdings"):?>selected="selected"<?php endif;?>><?php echo _('wingdings')?></option>
													</select>
												</td>
												<!--<td><input type="text" class="text short" id="font-family_3" name="font-family_3" value="<?php if($main_section_design): echo $main_section_design->{'font-family'};endif;?>"></td>-->
											</tr>
											<tr>
												<td class="textlabel"><?php echo _('Font-size')?></td>
												
												<td><select id="font-size_3" name="font-size_3" class="text short">
														<option value="0"><?php echo _('Select')?></option>
														<option value="10" <?php if($main_section_design&&$main_section_design->{'font-size'}=="10"):?>selected="selected"<?php endif;?>>10</option>
														<option value="12" <?php if($main_section_design&&$main_section_design->{'font-size'}=="12"):?>selected="selected"<?php endif;?>>12</option>
														<option value="14" <?php if($main_section_design&&$main_section_design->{'font-size'}) {if($main_section_design&&$main_section_design->{'font-size'}=="14"):echo 'selected="selected"'; endif; } elseif($font_size_3==14) {echo 'selected="selected"';} ?>>14</option>
														<option value="16" <?php if($main_section_design&&$main_section_design->{'font-size'}=="16"):?>selected="selected"<?php endif;?>>16</option>
														<option value="20" <?php if($main_section_design&&$main_section_design->{'font-size'}=="20"):?>selected="selected"<?php endif;?>>20</option>
														<option value="24" <?php if($main_section_design&&$main_section_design->{'font-size'}=="24"):?>selected="selected"<?php endif;?>>24</option>
														<option value="30" <?php if($main_section_design&&$main_section_design->{'font-size'}=="30"):?>selected="selected"<?php endif;?>>30</option>
													</select>
												</td>
										
											</tr>
											<tr>
												<td class="textlabel"><?php echo _('Font-style')?></td>
												
												<td><select id="font-style_3" name="font-style_3" class="text short">
														<option value="0"><?php echo _('Select')?></option>
														<option value="bold" <?php if($main_section_design&&$main_section_design->{'font-style'}=="bold"):?>selected="selected"<?php endif;?>><?php echo _('bold')?></option>
														<option value="normal" <?php if($main_section_design&&$main_section_design->{'font-style'}) {if($main_section_design&&$main_section_design->{'font-style'}=="normal"):echo 'selected="selected"'; endif; } elseif($font_style_3=='normal') {echo 'selected="selected"';} ?>><?php echo _('normal')?></option>
														<option value="italic" <?php if($main_section_design&&$main_section_design->{'font-style'}=="italic"):?>selected="selected"<?php endif;?>><?php echo _('italic')?></option>
														<option value="bold italic" <?php if($main_section_design&&$main_section_design->{'font-style'}=="bold italic"):?>selected="selected"<?php endif;?>><?php echo _('bold italic')?></option>
													</select>
												</td>
												
											</tr>
											<tr>
												<td class="textlabel"><?php echo _('Font-weight')?></td>
												
												<td><select id="font-weight_3" name="font-weight_3" class="text short">
														<option value="0"><?php echo _('Select')?></option>
														<option value="bold" <?php if($main_section_design&&$main_section_design->{'font-weight'}=="bold"):?>selected="selected"<?php endif;?>><?php echo _('bold')?></option>
														<option value="bolder" <?php if($main_section_design&&$main_section_design->{'font-weight'}=="bolder"):?>selected="selected"<?php endif;?>><?php echo _('bolder')?></option>														
														<option value="normal" <?php if($main_section_design&&$main_section_design->{'font-weight'}) {if($main_section_design&&$main_section_design->{'font-weight'}=="normal"):echo 'selected="selected"'; endif; } elseif($font_weight_3=='normal') {echo 'selected="selected"';} ?>><?php echo _('normal')?></option>												
														<option value="lighter" <?php if($main_section_design&&$main_section_design->{'font-weight'}=="lighter"):?>selected="selected"<?php endif;?>><?php echo _('lighter')?></option>
														<option value="inherit" <?php if($main_section_design&&$main_section_design->{'font-weight'}=="inherit"):?>selected="selected"<?php endif;?>><?php echo _('inherit')?></option>
													</select>
												</td>
												
											</tr>
											<tr>
												<td class="textlabel"><?php echo _('Text Decoration')?></td>
												<td><select id="text-decoration_3" name="text-decoration_3" class="text short">
														<option value="0"><?php echo _('Select')?></option>
														<option value="none" <?php if($main_section_design&&$main_section_design->{'text-decoration'}){ if($main_section_design&&$main_section_design->{'text-decoration'}=="none"):echo 'selected="selected"'; endif; } elseif($text_decoration_3=='none') {echo 'selected="selected"';} ?>><?php echo _('none')?></option>
														<option value="underline" <?php if($main_section_design&&$main_section_design->{'text-decoration'}=="underline"):?>selected="selected"<?php endif;?>><?php echo _('underline')?></option>
														<option value="overline" <?php if($main_section_design&&$main_section_design->{'text-decoration'}=="overline"):?>selected="selected"<?php endif;?>><?php echo _('overline')?></option>
														<option value="blink" <?php if($main_section_design&&$main_section_design->{'text-decoration'}=="blink"):?>selected="selected"<?php endif;?>><?php echo _('blink')?></option>
													</select>
												</td>
	
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
						<table>
							<tr style="padding:20px margin:auto">
								<td colspan="3">
								<input type="reset" class="submit" value="<?php echo _('RESET'); ?>" name="reset" id="reset"/>
								<input type="submit" class="submit" value="<?php echo _('SAVE'); ?>" name="change_designs" id="change_designs"/>
								</td>
						
							</tr>
						</table>
					</div>
				</form>
			</div>
			<div style="clear:both;"></div>
			</div>
			
			<!--Tab 1 : Ends -->
			
			<!--Tab 2 : Starts -->
			
			<div id="tabs-2">
			<form method="post" action="">
			   
			   <input type="hidden" name="theme_id" id="theme_id" value="<?php print_r($adv_css->theme_id); ?>" />
			   <input type="hidden" name="company_id" id="company_id" value="<?php print_r($adv_css->company_id); ?>" />
			   
			   <div style="float:left;width:70%;">
			   <input type="checkbox" name="use_own_css" id="use_own_css" value="1" <?php if($adv_css->use_own_css){ echo 'checked="checked"'; } ?> />&nbsp;<?php echo _('Use this custom css for my current theme.'); ?>
			   </div>
			   
			   <div style="float:right;width:30%;">
				   <input type="submit" name="update" id="update" value="<?php echo _('Save changes'); ?>" />
				   &nbsp;&nbsp;&nbsp;&nbsp;
				   <input type="submit" name="restore" id="restore" value="<?php echo _('Restore default'); ?>" />
			   </div>
			   
			   <div style="clear:both;"></div>
			   
			   <br /><br />
			   
			   <textarea name="theme_custom_css" id="theme_custom_css" rows="100" style="width:100%;"><?php print_r($adv_css->theme_custom_css); ?></textarea>
			   
			</form>
			</div>
			
			<!--Tab 2 : Ends -->
			
		</div>
		
	    </div>
	</div>
		
	</div>

    </div>
    <!-- /content -->
