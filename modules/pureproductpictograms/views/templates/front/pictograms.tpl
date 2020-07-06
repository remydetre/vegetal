{*
 * Product Pictograms module
 *
 * @author Jonathan Gaudé
 * @copyright 2018
 * @license Commercial
*}
<!-- BEGIN Pure Pictograms -->
{foreach from=$pictograms item=pictogram}
	{if ($product.quantity > 0 and !$pictogram.show_when_stock) or ($product.quantity <= 0 and !$pictogram.show_when_no_stock)}
		{continue}
	{/if}
	<div class="pureproductpictogram">
		{if !empty($pictogram.link_to)}<a href="{$pictogram.link_to}">{/if}
		<img src="{$pictograms_img_dir|escape:'htmlall':'UTF-8'}/{$pictogram.file_name|escape:'htmlall':'UTF-8'}" alt="{$pictogram.title|escape:'htmlall':'UTF-8'}" title="{$pictogram.title|escape:'htmlall':'UTF-8'}" />
		{if !empty($pictogram.link_to)}</a>{/if}
	</div>
{/foreach}
<!-- END Pure Pictograms -->