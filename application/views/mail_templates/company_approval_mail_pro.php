<table width="900" cellspacing="2" cellpadding="2">
  <tr>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="2">
        <tr>
          <td align="left">&nbsp;</td>
        </tr>
        <tr>
          <td align="left"><?php echo $this->lang->line('order_online_dear_client');?></td>
        </tr>
        <tr>
          <td align="left">&nbsp;</td>
        </tr>
        <tr>
          <td align="left"><?php echo $this->lang->line('c_a_m_c_1');?>.</td>
        </tr>
        <tr>
          <td align="left">&nbsp;</td>
        </tr>
	 <tr>
          <td align="left"><?php echo $this->lang->line('c_a_m_f_2');?> <a href="<?php echo base_url();?>cp"><?php echo base_url();?>cp</a> <?php echo $this->lang->line('c_a_m_f_3');?></td>
        </tr>
        <tr>
          <td align="left">&nbsp;</td>
        </tr>
        <tr>
          <td align="left"><?php echo $this->lang->line('c_a_m_f_4');?>: <b><?php echo $username;?></b></td>
        </tr>
        <tr>
          <td align="left"><?php echo $this->lang->line('c_a_m_f_5');?>:<b> <?php echo $password;?></b></td>
        </tr>
	 <tr>
          <td align="left">&nbsp;</td>
        </tr>
        <tr>
          <td align="left"><?php echo $this->lang->line('c_a_m_c_2');?> <?php echo $trialEndDate;?></td>
        </tr>
        <tr>
          <td align="left">&nbsp;</td>
        </tr>
        <tr>
          <td align="left"><?php echo $this->lang->line('c_a_m_c_3');?></td>
        </tr>
        <tr>
          <td align="left">&nbsp;</td>
        </tr>
        <tr>
          <td align="left"><?php echo $this->lang->line('c_a_m_c_4'); ?></td>
        </tr>
        <tr>
          <td align="left">&nbsp;</td>
        </tr>
        <tr>
          <td align="left"><?php echo $this->lang->line('c_a_m_p_1');?>: <a href="<?php echo $this->config->item("portal_url").$company_type_slug.'/'.$company_slug;?>/testdrive"><?php echo $this->config->item("portal_url").$company_type_slug.'/'.$company_slug;?>/testdrive</a> <br />
<br />
<?php echo $this->lang->line('c_a_m_p_2');?> (<a href="<?php echo $this->config->item("portal_url");?>"><?php echo $this->config->item("portal_url");?></a>).</td>
        </tr>
		<tr>
          <td align="left">&nbsp;</td>
        </tr>
        <tr>
          <td align="left"><?php echo $this->lang->line('c_a_m_c_5');?> </td>
        </tr>
		<tr>
          <td align="left">&nbsp;</td>
        </tr>
        <tr>
          <td align="left"><?php echo $this->lang->line('c_a_m_c_6');?>.</td>
        </tr>
      </table></td>
  </tr>
</table>