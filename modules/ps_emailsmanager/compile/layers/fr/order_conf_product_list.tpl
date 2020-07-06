{foreach $list as $product}

	<tr>
			<td width="18%">{$product['reference']}</td>
			<td class="mob-high" width="37%"><span style="font-weight:bold;">{$product['name']}</span>
				{if count($product['customization']) == 1}
				{foreach $product['customization'] as $customization}
					<span style="display:block;">{$customization['customization_text']}</span>
				{/foreach}
			{/if}</td>
			<td width="15%" style="white-space: nowrap;">{$product['unit_price']}</td>
			<td width="15%">{$product['quantity']}</td>
			<td align="right" width="15%" style="white-space: nowrap;">{$product['price']}</td>
	</tr>

	{if count($product['customization']) > 1}
		{foreach $product['customization'] as $customization}
			<tr>
				<td colspan="3">{$customization['customization_text']}</td>
				<td colspan="2" align="right">
						{if count($product['customization']) > 1}
							{$customization['customization_quantity']}
						{/if}
					</font>
				</td>
			</tr>
		{/foreach}
	{/if}

{/foreach}
