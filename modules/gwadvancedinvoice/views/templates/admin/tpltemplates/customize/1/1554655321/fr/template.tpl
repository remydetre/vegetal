<table style="margin-bottom: 20px; width: 100%; float: left;">
<tbody>
<tr>
<td style="width: 31%;">{$logo}</td>
<td style="width: 23%;">
<table style="width: 100%;">
<tbody>
<tr>
<td style="width: 15%;"><img src="http://vegetalfood.fr/modules/gwadvancedinvoice/views/img/imgtemplates/phone.png" alt="" width="25" height="25" /></td>
<td style="width: 85%;">
<table style="width: 100%;">
<tbody>
<tr>
<td style="font-size: 7pt;">+33 (0)1 88 33 53 46</td>
</tr>
<tr>
<td style="font-size: 7pt;">+33 (0)6 95 23 28 66</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
</td>
<td style="width: 23%;">
<table style="width: 100%;">
<tbody>
<tr>
<td style="width: 15%;"><img src="http://vegetalfood.fr/modules/gwadvancedinvoice/views/img/imgtemplates/location.png" alt="" width="25" height="25" /></td>
<td style="width: 85%;">
<table style="width: 100%;">
<tbody>
<tr>
<td style="font-size: 7pt;">10 rue de la Marne</td>
</tr>
<tr>
<td style="font-size: 7pt;">94170 Le Perreux, France</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
</td>
<td style="width: 23%;">
<table style="width: 100%;">
<tbody>
<tr>
<td style="width: 15%;"><img src="http://vegetalfood.fr/modules/gwadvancedinvoice/views/img/imgtemplates/email.png" alt="" width="25" height="25" /></td>
<td style="width: 85%;">
<table style="width: 100%;">
<tbody>
<tr>
<td style="font-size: 7pt;">contact@vegetalfood.fr</td>
</tr>
<tr>
<td style="font-size: 7pt;">www.vegetalfood.fr</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
<h6></h6>
<table style="width: 100%; float: left;" cellpadding="5" cellspacing="0">
<tbody>
<tr>
<td style="width: 25%;">
<h4 class="header_title">ADRESSE DE FACTURATION</h4>
<p>{$billing_company}<br />{$billing_firstname} {$billing_lastname} <br />{$billing_address1} {$billing_address2} <br />{$billing_city} - {$billing_postcode} {$billing_state} <br />{$billing_phone} <br />{$billing_phone_mobile}</p>
</td>
<td style="width: 25%;">
<h4 class="header_title">ADRESSE DE LIVRAISON</h4>
<p><span style="color: #666666; font-family: 'Open Sans', Helvetica, Arial, sans-serif;">{$delivery_company}</span><br />{$delivery_firstname} {$delivery_lastname} <br />{$delivery_address1} {$delivery_address2} <br />{$delivery_city} - {$delivery_postcode} {$delivery_state} <br />{$delivery_phone} <br />{$delivery_phone_mobile}</p>
</td>
<td style="width: 10%;"></td>
<td style="width: 40%;">
<table style="width: 100%;">
<tbody>
<tr>
<td colspan="2" class="invoice_title">FACTURE</td>
</tr>
<tr>
<td colspan="2" class="border_bottom"></td>
</tr>
<tr>
<td colspan="2"></td>
</tr>
<tr>
<td style="width: 50%;">Facture numéro:</td>
<td style="width: 50%;">{$invoice_number}</td>
</tr>
<tr>
<td style="width: 50%;">Date de facturation:</td>
<td style="width: 50%;">{$invoice_date}</td>
</tr>
<tr>
<td style="width: 50%;">Commande numéro:</td>
<td style="width: 50%;">{$reference}</td>
</tr>
<tr>
<td style="width: 50%;">Date de commande:<br /><br /></td>
<td style="width: 50%;">{$date_add}<br /><br /></td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
<h6><br />{$products_list}</h6>
<table style="width: 100%;" cellpadding="0" cellspacing="0">
<tbody>
<tr>
<td style="width: 60%; text-align: justify;"><br />
<table style="width: 70%;" cellpadding="5" cellspacing="0">
<tbody>
<tr>
<td class="box_color">Methode et conditions de Paiement:</td>
<td class="box_color" style="text-align: center;">{$payment}</td>
</tr>
</tbody>
</table>
<p><span style="text-decoration: underline;"><strong>Reglement par virement:</strong></span><br />Banque : Caisse d'Epargne <br />Ordre: SASU VEGETAL FOOD<br />IBAN : FR7617515900000801391023029<br />BIC / SWIFT: CEPAFRPP751</p>
<p><span style="text-decoration: underline;"><strong>Règlement par chèque:</strong></span><br />Ordre: SASU VEGETAL FOOD <br />10 rue de la Marne<br />94170, Le Perreux sur Marne<br />France</p>
<br />Aucun escompte consenti pour règlement anticipé.<br />Tout incident de paiement est passible d'intérêt de retard.<br />Le montant des pénalités résulte de l'application aux <br />sommes restant dues d'un taux d'intérêt égal en vigeur <br />au moment de l'incident.<br />Indemnité forfaitaire pour frais de recouvrement due <br />au créancier en cas de retard de paiement : 40€</td>
<td style="width: 40%;">
<table style="width: 100%;" cellpadding="5" cellspacing="0">
<tbody>
<tr class="color_line_even">
<td style="text-align: right; width: 50%;"><strong class="strong_item">Montant Total: </strong></td>
<td style="text-align: center; width: 50%;">{displayPrice currency=$order->id_currency price=$total_products}</td>
</tr>
<tr class="color_line_odd">
<td style="text-align: right; width: 50%;"><strong class="strong_item">Remises: </strong></td>
<td style="text-align: center; width: 50%;">-{displayPrice currency=$order->id_currency price=$total_discounts_tax_excl}</td>
</tr>
<tr class="color_line_even">
<td style="text-align: right; width: 50%;"><strong class="strong_item">Frais d'éxpédition : </strong></td>
<td style="text-align: center; width: 50%;">{displayPrice currency=$order->id_currency price=$total_shipping_tax_excl}</td>
</tr>
<tr class="color_line_odd">
<td style="text-align: right; width: 50%;"><strong class="strong_item">Total TVA : </strong></td>
<td style="text-align: center; width: 50%;">{displayPrice currency=$order->id_currency price=$footer.total_taxes}</td>
</tr>
<tr class="total_wp">
<td style="text-align: right; width: 50%;" class="total_text"><strong>TOTAL TTC: </strong></td>
<td style="text-align: center; width: 50%;" class="total_val">{displayPrice currency=$order->id_currency price=$total_paid}</td>
</tr>
<tr>
<td colspan="2"><br /><br />
<table style="width: 100%; text-align: center; margin-left: auto; margin-right: auto;" cellpadding="5">
<tbody>
<tr>
<td style="text-align: center; margin-left: auto; margin-right: auto;"></td>
</tr>
</tbody>
<tbody>
<tr class="thanksfor_bottom">
<td colspan="2" class="thanksfor" style="text-align: left;">Merci pour votre commande!</td>
</tr>
<tr>
<td colspan="2" class="color_footer" style="text-align: left;"><br />Le saviez vous ? Nous reversons 2% de vos achats HT à des associations caritatives pour la défense des animaux, la préservation de l'environement ou la lutte contre la faim.<br />+ d'infos sur vegetalfood.fr/associations</td>
</tr>
<tr>
<td style="width: 10%;" colspan="2">
<p></p>
</td>
</tr>
</tbody>
<tbody>
<tr>
<td>
<p></p>
</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>