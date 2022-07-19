<table width="900" cellspacing="2" cellpadding="2" border="0">
  {OrderMessage}
  <tr>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="2">
        <tr>
          <td align="left">&nbsp;</td>
        </tr>
          <td align="left">Hieronder een overzicht van uw bestelling: <br /><br /></td>
        </tr>
        <tr>
          <td align="left"><strong>Bestelling Nr.</strong> : {order_no}</td>
        </tr>
        <tr>
          <td align="left"><strong>Datum Bestelling</strong> : {date_created} <br />
            <br /></td>
        </tr>
        <tr>
          <td width="52%" valign="top">
		     <table width="100%" border="0" cellspacing="0" cellpadding="2">
              <tr>
                <td bgcolor="#F0F0F0"><b>Klantengegevens:</b></td>
              </tr>
              <tr>
                <td>{client_company}<br />
				  {last_name}
                  &nbsp;
                  {first_name} <br />
                  {address}
                  &nbsp;-
                  {house_no} <br />
                  {postcode}
				  &nbsp;{city}<br/>
                  {country}<br />
				  GSM&nbsp;&nbsp;&nbsp; :&nbsp;{mobile}<br />
				  Telefoon :&nbsp;{phone}<br />
                  <br/></td>
              </tr>
			</table></td>
        </tr>
		{Options}
      </table></td>
  </tr>
  <tr>
    <td align="left">&nbsp;</td>
  </tr>
  <tr>
    <td><table border="0" cellpadding="2" cellspacing="2" width="100%">
        <tr>
          <td width="350" align="left" bgcolor="#EAEAEA"><strong>Product</strong></td>
          <td align="center" bgcolor="#EAEAEA"><strong>Aantal</strong></td>
		  <td align="center" bgcolor="#EAEAEA"><strong>Extra</strong></td>
        </tr>
       	{MailBody}
        <!--<tr>
          <td valign="top" align="right"><div align="right">&nbsp;</div></td>
          <td valign="top" align="right"><div align="right">&nbsp;</div></td>
          <td align="right" valign="top"><div align="right">&nbsp;</div></td>
		  <td align="right" valign="top"><div align="right">&nbsp;</div></td>
          <td align="right" valign="top"><div align="right"><strong>Order Totaal:</strong></div></td>
          <td align="left" valign="top"><div align="left">&euro;{total}</div></td>
        </tr>-->
        {Options2}
      </table></td>
  </tr>
  <tr>
    <td align="left">&nbsp;</td>
  </tr>
  <tr>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="2">
        <tr>
          <td align="left">
            Met vriendelijke groeten,<br>
            <br>
            {CompanyName} <br />
            <br /></td>
        </tr>
		 <tr>
          <td align="left">{Options3}</td>
        </tr>
      </table></td>
  </tr>
</table>
