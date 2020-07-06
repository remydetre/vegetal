{if $params.type|escape:'quotes':'UTF-8' == 'text'}
	{if isset($params.lang) && $params.lang == true}
		<div style="overflow:hidden">
			<label for="{$params.name|escape:'quotes':'UTF-8'}">{$params.label|escape:'quotes':'UTF-8'}</label>
			<div class="margin-form">
				<div style="float:left">
					{foreach $languages as $language}
						<div>
							<input type="text" name="{$params.name|escape:'quotes':'UTF-8'}_{$language['id_lang']|escape:'quotes':'UTF-8'}" value="{$fields_value[$params.name|escape:'quotes':'UTF-8'][$language['id_lang']]|escape:'quotes':'UTF-8'}" />
							<img src="{$THEME_LANG_DIR|escape:'quotes':'UTF-8'}{$language['id_lang']|escape:'quotes':'UTF-8'}.jpg" alt="{$language['iso_code']|escape:'quotes':'UTF-8'}" title="{$language['name']|escape:'quotes':'UTF-8'}" />
						</div>
					{/foreach}
				</div>
			</div>
		</div>
		<br />
	{else}
		<div style="overflow:hidden">
			<label for="{$params.name|escape:'quotes':'UTF-8'}">{$params.label|escape:'quotes':'UTF-8'}</label>
			<div class="margin-form">
				<input type="text" name="{$params.name|escape:'quotes':'UTF-8'}" value="{$fields_value[$params.name|escape:'quotes':'UTF-8']|escape:'quotes':'UTF-8'}" />
			</div>
		</div>
		<br />
	{/if}
{elseif $params.type|escape:'quotes':'UTF-8' == 'switch' || $params.type|escape:'quotes':'UTF-8' == 'radio'}
	<div style="overflow:hidden">
		<label for="{$params.name|escape:'quotes':'UTF-8'}">{$params.label|escape:'quotes':'UTF-8'}</label>
		<div class="margin-form">
			{foreach $params.values as $value}
				<input type="radio" name="{$params.name|escape:'quotes':'UTF-8'}" id="{$value.id|intval}" value="{$value.value|escape:'quotes':'UTF-8'}"
						{if $fields_value[$params.name] == $value.value}checked="checked"{/if}
						{if isset($params.disabled) && $params.disabled}disabled="disabled"{/if} />
				<label class="t" for="{$value.id|intval}">
				 {if isset($params.is_bool) && $params.is_bool == true}
					{if $value.value == 1}
						<img src="../img/admin/enabled.gif" alt="{$value.label|escape:'quotes':'UTF-8'}" title="{$value.label|escape:'quotes':'UTF-8'}" />
					{else}
						<img src="../img/admin/disabled.gif" alt="{$value.label|escape:'quotes':'UTF-8'}" title="{$value.label|escape:'quotes':'UTF-8'}" />
					{/if}
				 {else}
					{$value.label|escape:'quotes':'UTF-8'}
				 {/if}
				</label>
				{if isset($params.br) && $params.br}<br />{/if}
				{if isset($value.p) && $value.p}<p>{$value.p|escape:'quotes':'UTF-8'}</p>{/if}
			{/foreach}
		</div>
	</div>
	<br />
{elseif $params.type|escape:'quotes':'UTF-8' == 'submit'}
	<div style="overflow:hidden">
		<center>
			<input class="button" type="submit" name="{$params.name|escape:'quotes':'UTF-8'}" />
		</center>
	</div>
{elseif $params.type|escape:'quotes':'UTF-8' == 'select'}
	<div style="overflow:hidden">
		<label for="{$params.name|escape:'quotes':'UTF-8'}">{$params.label|escape:'quotes':'UTF-8'}</label>
		<div class="margin-form">
			{assign var=index value=$params.options.id}
			{assign var=value value=$params.options.name}
			<select name="{$params.name|escape:'quotes':'UTF-8'}">
			{foreach $params.options.query as $option}
				<option value="{$option.$index|escape:'quotes':'UTF-8'}" {if $fields_value[$params.name] == $option.$index}selected{/if}>{$option.$value|escape:'quotes':'UTF-8'}</option>
			{/foreach}
			</select>
		</div>
	</div>
	<br />
{elseif $params.type|escape:'quotes':'UTF-8' == 'file'}
	<div style="overflow:hidden">
		<label for="{$params.name|escape:'quotes':'UTF-8'}">{$params.label|escape:'quotes':'UTF-8'}</label>
		<div class="margin-form">
			<input type="file" name="{$params.name|escape:'quotes':'UTF-8'}" />
		</div>
	</div>
	{if isset($params.name) && $params.name != ''}
			<label>{l s='Current image' mod='groupinc'}:</label>
			<div class="margin-form">
				<img src="{$params.name|escape:'htmlall':'UTF-8'}" class="payment_image">
				<img id="delete_image" src="{$this_path|escape:'htmlall':'UTF-8'}views/img/delete.gif" alt="{l s='Delete image' mod='groupinc'}" title="{l s='Delete image' mod='groupinc'}" class="payment_img payment_image" />
				<input type="submit" name="delete_image_submit" id="delete_image_submit" style="display:none" />
			</div>
		{/if}
	<br />
{elseif $params.type|escape:'quotes':'UTF-8' == 'checkbox'}
	<div style="overflow:hidden">
		<label for="{$params.name|escape:'quotes':'UTF-8'}">{$params.label|escape:'quotes':'UTF-8'}</label>
		<div class="margin-form">
			{foreach $params.values.query as $value}
				<input type="checkbox" name="{$params.name|escape:'quotes':'UTF-8'}_{$value.id|escape:'quotes':'UTF-8'}" id="{$params.name|escape:'quotes':'UTF-8'}_{$value.id|escape:'quotes':'UTF-8'}" {if isset($fields_value[$params.name|escape:'quotes':'UTF-8'|cat:_|cat:$value.id|escape:'quotes':'UTF-8']) && $fields_value[$params.name|escape:'quotes':'UTF-8'|cat:_|cat:$value.id|escape:'quotes':'UTF-8'] == 'on'}checked="checked"{/if}>{$value.name|escape:'quotes':'UTF-8'}</option>
				<br />
			{/foreach}
		</div>
	</div>
	<br />
{elseif $params.type|escape:'quotes':'UTF-8' == 'color'}
	<div style="overflow:hidden">
		<label for="{$params.name|escape:'quotes':'UTF-8'}">{$params.label|escape:'quotes':'UTF-8'}</label>
		<div class="margin-form">
			<input type="text" name="{$params.name|escape:'quotes':'UTF-8'}" value="{$fields_value[$params.name|escape:'quotes':'UTF-8']|escape:'quotes':'UTF-8'}" />
		</div>
	</div>
	<br />
{elseif $params.type|escape:'quotes':'UTF-8' == 'free'}
	{if $fields_value[$params.name]}{$fields_value[$params.name]|escape:'htmlall':'UTF-8'}{/if}
{/if}

{literal}
<script type="text/javascript">
	$('#delete_image').click(function() {
		$('#delete_image_submit').click();
	});
</script>
{/literal}