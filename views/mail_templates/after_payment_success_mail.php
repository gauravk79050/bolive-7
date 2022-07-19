<table width="100%" cellspacing="2" cellpadding="2" border="0">
  {Message}
  <tr>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="2">
	    <!--<tr>
            <td align="left">Overzicht bestelling: <br /><br /></td>
        </tr>-->
        <tr>
          <td align="left"><strong>{order_no_text}</strong> : {order_no}</td>
        </tr>
         <tr>
          <td align="left"><strong>{transaction_no_text}</strong> : {transaction_no}</td>
        </tr>
      
        <tr>
          <td width="52%" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="2">
              <tr>
                <td bgcolor="#F0F0F0"><b>{customer_data_text}:</b></td>
                <td bgcolor="#F0F0F0" style="text-align: right;" ><span style="color:#FF0004"> {payment_option_selected} </span></td>
              </tr>
              <tr>
                <td>
				  {client_company}<br />
				  {last_name}&nbsp;{first_name}<br />
                  {address}&nbsp;{house_no}<br />
                  {postcode} - {city}<br/>
                  {invoice_and_vat}
                  {country}<br />
				  {mobile_text} :&nbsp;{mobile}<br />
				  {phone_text} :&nbsp;{phone}<br />
				  {email_text} :&nbsp;{email}<br /><br/>
			    </td>
              </tr>
			</table></td>
        </tr>
      </table>
  
      </td>
  </tr>
  
  <tr>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="2">
	    <tr><td align="left"><br /><!--<br />{Options3}<br /> --><br /></td></tr>
        <tr>
          <td align="left"><br />
             {regard_text},<br /><br />
            {CompanyName}<br /><br />
		  </td>
        </tr>
		 <tr>
          <td align="left">{Options4}</td>
        </tr>
      </table></td>
  </tr>
</table>