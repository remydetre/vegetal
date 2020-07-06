<div id="wrap">
<table style="margin-bottom: 20px; width: 100%; float: left;">
<tbody>
<tr>
<td style="float: left; width: 40%;">{$logo}</td>
<td style="width: 20%;">
<table style="width: 100%;">
<tbody>
<tr>
<td>
<h4 style="font-size: 7pt;" class="header_title">TELEPHONE & CONTACT</h4>
</td>
</tr>
<tr>
<td style="font-size: 7pt;">+33 (0)1 88 32 76 55</td>
</tr>
<tr>
<td style="font-size: 7pt;">contact@grossiste-vegan.com</td>
</tr>
</tbody>
</table>
</td>
<td style="width: 20%;">
<table style="width: 100%;">
<tbody>
<tr>
<td>
<h4 style="font-size: 7pt;" class="header_title">SIEGE SOCIAL</h4>
</td>
</tr>
<tr>
<td style="font-size: 7pt;">10 rue de la Marne</td>
</tr>
<tr>
<td style="font-size: 7pt;">94170 Le Perreux, France</td>
</tr>
</tbody>
</table>
</td>
<td style="width: 20%;">
<table style="width: 100%;">
<tbody>
<tr>
<td>
<h4 style="font-size: 7pt;" class="header_title">COMMANDEZ EN LIGNE</h4>
</td>
</tr>
<tr>
<td style="font-size: 7pt;">www.Grossiste-Vegan.com</td>
</tr>
<tr>
<td style="font-size: 7pt;">#grossistevegan</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
<br />
<table style="width: 100%; float: left;">
<tbody>
<tr>
<td style="width: 37%;">
<h4 class="invoice_title">FACTURE</h4>
</td>
<td style="width: 21%;">
<h2 class="header_title"><br />FACTURATION</h2>
<p>{$billing_firstname} {$billing_lastname} <br />{$billing_address1} {$billing_address2} <br />{$billing_city} - {$billing_postcode} {$billing_state} <br />{$billing_phone} <br />{$billing_phone_mobile}</p>
</td>
<td style="width: 21%;">
<h2 class="header_title"><br />LIVRAISON</h2>
<p>{$delivery_firstname} {$delivery_lastname} <br />{$delivery_address1} {$delivery_address2} <br />{$delivery_city} - {$delivery_postcode} {$delivery_state} <br />{$delivery_phone} <br />{$delivery_phone_mobile}</p>
</td>
<td style="width: 21%;">
<p><strong class="strong_item"><br />Facture: </strong>{$invoice_number}  <br /><strong class="strong_item">Date : </strong>{$invoice_date} <br /><strong class="strong_item">Transporteur: </strong>{$order_carrier_name}</p>
</td>
</tr>
</tbody>
</table>
<br />{$products_list}
<table style="width: 100%;" cellpadding="7" cellspacing="0">
<tbody>
<tr>
<td style="float: right; width: 60%;"></td>
<td style="float: right; width: 40%;">
<table style="width: 100%;">
<tbody>
<tr>
<td style="text-align: right; width: 60%;"><strong class="strong_item">Montant total HT: </strong></td>
<td style="text-align: center; width: 40%;">{displayPrice currency=$order->id_currency price=$total_products}</td>
</tr>
<tr>
<td style="text-align: right; width: 60%;"><strong class="strong_item">Remise: </strong></td>
<td style="text-align: center; width: 40%;">-{displayPrice currency=$order->id_currency price=$total_discounts_tax_excl}</td>
</tr>
<tr>
<td style="text-align: right; width: 60%;"><strong class="strong_item">Cout transport : </strong></td>
<td style="text-align: center; width: 40%;">{displayPrice currency=$order->id_currency price=$total_shipping_tax_excl}</td>
</tr>
<tr>
<td style="text-align: right; width: 60%;"><strong class="strong_item">TVA : </strong></td>
<td style="text-align: center; width: 40%;">{displayPrice currency=$order->id_currency price=$footer.total_taxes}</td>
</tr>
</tbody>
</table>
</td>
</tr>
<tr class="total_wp">
<td style="float: right; width: 60%;"></td>
<td style="float: right; width: 40%;">
<table style="width: 100%;">
<tbody>
<tr>
<td style="text-align: right; width: 60%;" class="total_text"><strong>Montant total TTC: </strong></td>
<td style="text-align: center; width: 40%;" class="total_text">{displayPrice currency=$order->id_currency price=$total_paid}</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
<br />
<table style="width: 100%;">
<tbody>
<tr>
<td style="width: 50%;">
<table style="width: 100%;">
<tbody>
<tr>
<td style="text-align: left;"><span style="color: #474241;"><b>Methode de Paiement</b></span> : {$payment}</td>
</tr>
<tr>
<td>
<p><strong>En cas de paiement par Chèque: </strong></p>
<p><strong>Termes et Conditions</strong>:</p>
<p>Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt dolore magnam aliquam quaerat.</p>
</td>
</tr>
</tbody>
</table>
</td>
<td style="text-align: center; width: 10%;"></td>
<td style="text-align: center; width: 40%;">
<table style="width: 80%; margin-left: auto; margin-right: auto;">
<tbody>
<tr>
<td><img src="http://vegetalfood.fr/modules/gwadvancedinvoice/views/img/imgtemplates/founder.png" alt="" width="314" height="71" /></td>
</tr>
<tr>
<td>
<p>Nguyen Van Nham</p>
<p><strong class="strong_item">CEO & Founder</strong></p>
</td>
</tr>
</tbody>
</table>
</td>
</tr>
<tr>
<td style="text-align: center;" colspan="3"><br /><br />
<h4 class="thanksfor">Thank for your bussiness !</h4>
</td>
</tr>
</tbody>
</table>
</div>