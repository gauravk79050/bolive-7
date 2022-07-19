<div id="main">
	<div id="main-header">
    	<h2><?php echo _('Video-tutorials'); ?></h2>
	</div>
	<div id="content">
    	<div id="content-container">
        	<div class="box">
          		<h3><?php echo _('Video'); ?></h3>
          		<div class="table">
            		<table cellspacing="0">
              			<tbody>
              			<?php if($this->company->ac_type_id == 1){?>
              			<tr>
              			<td>
                   	01 - <a href="#TB_inline?inlineId=free_v" class="thickbox video_open"><?php echo _("Videotutorial INTRO");?></a>
					<div id="free_v" style="display: none;"><iframe class="tscplayer_inline" name="tsc_player_free" src="<?php echo base_url();?>videos/01-Free/Free_player.html" scrolling="no" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe></div>
					</td>
                    </tr>
                    <?php }else{?>
                    <tr>
                    <td>
                    01 - <a target="_blank" href="<?php echo $this->config->item('video_url').'video/view/'.get_video_token().'/1'; ?>">Rondleiding</a>
                    </td>
                    </tr>
                    <tr>
                    <td>
                    02 - <a target="_blank" href="<?php echo $this->config->item('video_url').'video/view/'.get_video_token().'/2'; ?>">De eerste stappen</a>
                    </td>
                    </tr>
                    <?php if($this->company->ac_type_id == 4 || $this->company->ac_type_id == 5 || $this->company->ac_type_id == 6){?>
		            <tr>
		            <td>
                    03 - <a target="_blank" href="<?php echo $this->config->item('video_url').'video/view/'.get_video_token().'/3'; ?>">Hoe recepturen toevoegen</a>
                    </td>
                    </tr>
                    <tr>
                    <td>
                    04 - <a target="_blank" href="<?php echo $this->config->item('video_url').'video/view/'.get_video_token().'/4'; ?>">Webwinkel algemeen</a>
                    </td>
                    </tr>
                    <tr>
                    <td>
                    05 - <a target="_blank" href="<?php echo $this->config->item('video_url').'video/view/'.get_video_token().'/5'; ?>">Webwinkel configuratie</a>
                    </td>
                    </tr>
		            <?php }?>
                    <?php }?>
						</tbody>	
            		</table>
          		</div><!-- /table -->
        	</div><!-- /box -->
      	</div><!-- /content-container -->
	</div><!-- /content -->