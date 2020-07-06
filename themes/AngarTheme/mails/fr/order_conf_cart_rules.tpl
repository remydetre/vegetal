{foreach $list as $cart_rule}
	<tr>
		<td colspan="3">{$cart_rule['voucher_name']}</td>
		<td colspan="2" align="right">{$cart_rule['voucher_reduction']}</td>
	</tr>
{/foreach}
