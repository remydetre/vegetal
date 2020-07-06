{*
 * Product Pictograms module
 *
 * @author Jonathan Gaudé
 * @copyright 2018
 * @license Commercial
*}
<div id="pureproductpictograms" class="{if version_compare($smarty.const._PS_VERSION_,'1.7','<')}panel {/if}product-tab">
	<input type="hidden" name="submitted_tabs[]" value="{l s='Product Pictograms' mod='pureproductpictograms'}">
	{if version_compare($smarty.const._PS_VERSION_,'1.7','<')}<h3 class="tab"> <i class="icon-info"></i> {l s='Product Pictograms' mod='pureproductpictograms'}</h3>{/if}
	<div class="alert alert-danger" style="margin-bottom: 0">
		{l s='Unable to create the following folder :' mod='pureproductpictograms'} <i><b>{$pictograms_img_dir|escape:'htmlall':'UTF-8'}</b></i>.
		<br><br>
		{l s='Please create it manually using an FTP client for example.' mod='pureproductpictograms'}
	</div>
</div>