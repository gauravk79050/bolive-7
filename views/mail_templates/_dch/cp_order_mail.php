<table width="100%" cellspacing="2" cellpadding="2" border="0">
  {OrderMessage}
  <tr>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="2">
	    <!--<tr>
            <td align="left">Overzicht bestelling <br /><br /></td>
        </tr>-->
        <tr>
          <td align="left"><strong>{order_no_text}</strong> : {order_no}</td>
        </tr>
        <tr>
          <td align="left"><strong>{date_created_text}</strong> : {date_created} <br />
            <br /></td>
        </tr>
        <tr>
          <td width="52%" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="2">
              <tr>
                <td bgcolor="#F0F0F0"><b>{customer_data_text}:</b></td>
              </tr>
              <tr>
                <td>
				  {client_company}<br />
				  {last_name}&nbsp;{first_name}<br />
                  {address}&nbsp;{house_no}<br />
                  {postcode} - {city}<br/>
                  {country}<br />
				  {mobile_text} :&nbsp;{mobile}<br />
				  {phone_text} :&nbsp;{phone}<br />
				  {email_text} :&nbsp;{email}<br /><br/>
			    </td>
              </tr>
			</table></td>
        </tr>
		{Options}
      </table></td>
  </tr>
  <tr>
    <td><table border="0" cellpadding="2" cellspacing="2" width="100%">
        <tr>
          <td width="30%" bgcolor="#EAEAEA"><strong>{product_text}</strong></td>
          <td width="10%" align="left" bgcolor="#EAEAEA"><strong>{qauntity_text}</strong></td>
          <td width="15%" align="left" bgcolor="#EAEAEA"><strong>{price_text}</strong></td>
		  <td width="35%" align="left" bgcolor="#EAEAEA"><strong>{extra_text}</strong></td>
          <!--<td width="2%" align="right" bgcolor="#EAEAEA">&nbsp;</td>-->
          <td width="8%" align="left" bgcolor="#EAEAEA"><strong>{total_text}</strong></td>
        </tr>
       	{MailBody}

      <!--  {Options2} -->
    </table></td>
  </tr>
  <tr>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="2">
        <tr>
          <td align="left"><br />{regard_text}, <br />
            <br />
{CompanyName}<br /><br />
		  </td>
        </tr>
		 
      </table></td>
  </tr>
</table>
