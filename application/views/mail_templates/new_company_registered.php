<table width="900" cellspacing="2" cellpadding="2">
  <tr>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="2">
        <tr>
          <td align="left">&nbsp;</td>
        </tr>
        <tr>
          <td align="left"><strong><?php echo $company_name; ?></strong> <?php echo _('has registered via FoodDESK:'); ?> <br />
            <br /></td>
        </tr>
		<tr>
          <td align="left"><strong><?php echo _('Company');?>&nbsp;&nbsp;<?php echo _('ID');?></strong> : <?php echo $insert_id; ?></td>
        </tr>
        <tr>
          <td align="left"><strong><?php echo _('Company');?>&nbsp;<?php echo _('Name');?></strong> : <?php echo $company_name; ?></td>
        </tr>
        <tr>
          <td align="left"><strong><?php echo _('First Name');?></strong> : <?php echo $first_name; ?></td>
        </tr>
        <tr>
          <td align="left"><strong><?php echo _('Last Name');?></strong> : <?php echo $last_name; ?></td>
        </tr>
        <tr>
          <td align="left"><strong><?php echo _('Email');?></strong> : <?php echo $email; ?></td>
        </tr>
        <tr>
          <td align="left"><strong><?php echo _('Username');?></strong> : <?php echo $username; ?></td>
        </tr>
        <tr>
          <td align="left"><strong><?php echo _('Password');?></strong> : <?php echo $password; ?></td>
        </tr>
        <tr>
          <td align="left"><strong><?php echo _('Address');?></strong> : <?php echo $address; ?></td>
        </tr>
        <tr>
          <td align="left"><strong><?php echo _('Zipcode');?></strong> : <?php echo $zipcode; ?></td>
        </tr>
        <tr>
          <td align="left"><strong><?php echo _('City');?></strong> : <?php echo $city; ?></td>
        </tr>
        <tr>
          <td align="left"><strong><?php echo _('Country');?></strong> : <?php echo ( ($country_id == "21")?"BELGIE":"NEDERLAND" ); ?> <br />
            <br /></td>
        </tr>
		<tr>
          <td align="left"><strong><?php echo _('IP Address');?></strong> : <?php echo $REQ_IP_ADD; ?><br />
            <br /></td>
        </tr>
        <?php if(isset($as_supercompany)):?>
        <tr>
          <td align="left"><strong><?php echo _('Want to register as supercompany');?></strong><br />
            <br /></td>
        </tr>
        <?php endif;?>
        
        <?php if($have_website):?>
        <tr>
          <td align="left">
          	<strong><?php echo _('Already has website');?> : </strong><?php echo $website;?>
          </td>
        </tr>
        <?php elseif($domain != null) :?>
        <tr>
          <td align="left">
          	<strong><?php echo _('Domain to register');?> : </strong><?php echo $domain;?>
          </td>
        </tr>
        <?php endif;?>
        
      </table></td>
  </tr>
</table>