{*
 * Product Pictograms module
 *
 * @author Jonathan Gaudé
 * @copyright 2018
 * @license Commercial
*}
<div id="pureproductpictograms" class="{if version_compare($smarty.const._PS_VERSION_,'1.7','<')}panel {/if}product-tab">
	<input type="hidden" name="submitted_tabs[]" value="{l s='Pictograms' mod='pureproductpictograms'}">
	{if version_compare($smarty.const._PS_VERSION_,'1.7','<')}<h3 class="tab"> <i class="icon-info"></i> {l s='Pictograms' mod='pureproductpictograms'}</h3>{/if}
	<script type="text/javascript">
	$(function() {
		$.getScript("{$link->protocol_link}{$shop->domain}{$shop->physical_uri}js/jquery/ui/jquery.ui.sortable.min.js", function() {
			$( "#selectedPictograms" ).sortable({
			  stop: function(event, ui) {
				onSelectedPictogramsChange();
			  }
			});
			$( "#selectedPictograms" ).disableSelection();
			
			if (typeof tooltip == 'function')
				$(".label-tooltip").tooltip();
			
			$('#selectedPictograms > div > a').on('click', function() {
				onRemovePictogramEvent($(this).parent('div'));
			});
			
			$('.pictograms').on('click', function() {
				var $this = $(this);
				var pos = $this.position();
				var newPos = $('#selectedPictograms');
				var selectedPos = $('#selectedPictograms').position();
				
				var previouslySelected = $('#selectedPictograms div:last-child');
				previouslySelectedPos = previouslySelected.position();
				
				$('#selectedPictograms')
					.append('<div style="display: none" data-filename="' + $this.data('filename') + '" data-picid="' + $this.data('picid') + '">'
					+ '<a title="{l s='Remove' mod='pureproductpictograms'}" class="deletePictogram" href="javascript:;">'
					+ '{if version_compare($smarty.const._PS_VERSION_,'1.7','>=')}<i class="material-icons">close</i>{else}<i class="icon icon-close"></i>{/if}'
					+ '</a>'
					+ '<img src="{$pictograms_img_dir|escape:'htmlall':'UTF-8'}/' + $this.data('filename') + '" />'
					+ '</div>');
				var lastChild = $('#selectedPictograms div:last-child');
				onSelectedPictogramsChange();
				
				var newTop = selectedPos.top + newPos.outerHeight();
				var newLeft = selectedPos.left + newPos.width();
				if (newPos.outerHeight() >= $this.height()) newTop -= $this.height();
				
				if (typeof previouslySelectedPos != 'undefined') {
					newTop = previouslySelectedPos.top;
					newLeft = previouslySelectedPos.left;
				}
				
				$this.css({
						'position' : 'absolute',
						'top' : pos.top,
						'left' : pos.left
					})
					.add($this.find("img"))
					.animate({
						top: newTop,
						left: newLeft,
						opacity: 0
					}, 500, function(){
						lastChild.fadeIn(300);
						$this.hide();
						lastChild.children('a').on('click', function() {
							onRemovePictogramEvent(lastChild);
						});
					});
			});
			
			function onRemovePictogramEvent (elem) {
				var $this = elem;
				var pos = $this.position();
				var newElem = $('div[id=picto_' + $this.data('picid') + ']');
				var newPos = newElem.position();
				
				$this.css({
						'position' : 'absolute',
						'top' : pos.top,
						'left' : pos.left
					})
					.add($this.find("img"))
					.animate({
						top: newPos.top,
						left: newPos.left,
						opacity: 0
					}, 500, function(){
						$this.hide().remove();
						onSelectedPictogramsChange();
						newElem.children('.pictograms')
						 .add(newElem.find('img'))
						 .css({
							'position': 'static',
							'top': 'initial',
							'left': 'initial',
							'opacity': 1
						}).fadeIn(300).show();
					});
			}
			
			function onSelectedPictogramsChange() {
				var pictograms = '';
				$('#selectedPictograms > div').each(function() {
					if($(this).attr('data-picid')) {
						if (pictograms != '') pictograms += ',';
						pictograms += $(this).data('picid');
					}
				});
				$('input[id=pictograms_list]').val(pictograms);
			}
			
			$(window).resize(function() {
				$('#selectedPictograms > div').css({
					'display': 'inline-block'
				});
			});
		});
	});
	</script>
	<div class="form-group">
		<div class="alert alert-info">
			<p class="alert-text">
				{l s='Select the pictograms you want to add to this product' mod='pureproductpictograms'}.<br>
				{l s='The pictograms are stored in the following folder' mod='pureproductpictograms'} : <b>{$pictograms_img_dir|escape:'htmlall':'UTF-8'}</b>.
			</p>
		</div>
		<div id="selectedPictograms">
		{foreach from=$pureproductpictograms item=pictogram_chosen name=pictograms_chosen}
			<div data-filename="{$pictogram_chosen.file_name|escape:'htmlall':'UTF-8'}" data-picid="{$pictogram_chosen.id_pictogram}">
				<a title="{l s='Remove' mod='pureproductpictograms'}" class="deletePictogram" href="javascript:;">
					{if version_compare($smarty.const._PS_VERSION_,'1.7','>=')}<i class="material-icons">close</i>{else}<i class="icon icon-close"></i>{/if}
				</a>
				<img src="{$pictograms_img_dir|escape:'htmlall':'UTF-8'}/{$pictogram_chosen.file_name|escape:'htmlall':'UTF-8'}">
			</div>
		{/foreach}
		</div>
		<hr />
		{foreach from=$pictograms_available item=pictogram_available}
			<div class="pictogram_container" id="picto_{$pictogram_available.id_pictogram|escape:'htmlall':'UTF-8'}">
				<div class="pictograms" data-filename="{$pictogram_available.file_name|escape:'htmlall':'UTF-8'}" data-picid="{$pictogram_available.id_pictogram|escape:'htmlall':'UTF-8'}"{if in_array($pictogram_available.id_pictogram, $ids_pictograms_for_this_product)} style="opacity: 0;"{/if}>
					<img src="{$pictograms_img_dir|escape:'htmlall':'UTF-8'}/{$pictogram_available.file_name|escape:'htmlall':'UTF-8'}">
					<br>
					<label for="available_for_order">{$pictogram_available.title|truncate:19:"...":true|escape:'htmlall':'UTF-8'}</label>
				</div>
			</div>
		{/foreach}
		<input type="hidden" id="pictograms_list" name="pureproductpictograms[{$current_language|escape:'htmlall':'UTF-8'}]" value="{foreach from=$pureproductpictograms item=pictogram_chosen name=pictograms_chosen}{$pictogram_chosen.id_pictogram}{if !$smarty.foreach.pictograms_chosen.last},{/if}{/foreach}">
	</div>
	{if version_compare($smarty.const._PS_VERSION_,'1.7','<')}
	<div class="panel-footer">
		<a href="{$link->getAdminLink('AdminProducts')|escape:'htmlall':'UTF-8'}" class="btn btn-default"><i class="process-icon-cancel"></i> {l s='Cancel' mod='pureproductpictograms'}</a>
		<button type="submit" name="submitAddproduct" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l s='Save' mod='pureproductpictograms'}</button>
		<button type="submit" name="submitAddproductAndStay" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l s='Save and stay' mod='pureproductpictograms'}</button>
	</div>
	{/if}
</div>