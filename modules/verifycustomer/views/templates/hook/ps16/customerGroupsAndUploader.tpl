{*
* 2017 Singleton software
*
*  @author Singleton software <info@singleton-software.com>
*  @copyright 2017 Singleton software
*}
{if $configuration['custom_group_position'] == $positionID && $groups|@count > 0}
	<div class="groupSelect" style="margin-bottom: 20px;">
		{if $configuration['custom_group_select_type'] == 1}
			<div class="cleafix group-line">
				<h3 class="page-subheading">{l s='Account Group' mod='verifycustomer'}</h3>
				{foreach from=$groups item=group name=group}
					<div class="radio-inline">
						<label for="id_group{$group.id_group|escape:'htmlall':'UTF-8'}" class="top">
							<input type="radio" name="id_group" id="id_group{$group.id_group|escape:'htmlall':'UTF-8'}" value="{$group.id_group|escape:'htmlall':'UTF-8'}"{if $selectedRadioGroup == $group.id_group} checked="checked"{/if}/>
							{$group.name|escape:'htmlall':'UTF-8'}
						</label>
					</div>
				{/foreach}
			</div>
			<span class="form_info">{l s='You can select group, you want to be assigned to' mod='verifycustomer'}</span>
		{else}
			<div class="cleafix group-line">
				<h3 class="page-subheading">{l s='Account Groups' mod='verifycustomer'}</h3>
				{foreach from=$groups item=group name=group}
					<div class="checkbox">
						<label for="group{$group.id_group|escape:'htmlall':'UTF-8'}">
							{assign var="isChecked" value=false}
							{foreach from=$selectedCheckboxGroups item=selectedGroup name=selectedGroup}
								{if $selectedGroup == $group.id_group}
									{assign var="isChecked" value=true}
								{/if}
							{/foreach}
							{$group.name|escape:'htmlall':'UTF-8'}<input type="checkbox" name="groups[]" id="groups" value="{$group.id_group|escape:'htmlall':'UTF-8'}"{if $isChecked} checked="checked"{/if}/>
						</label>
					</div>
				{/foreach}
			</div>
			<span class="form_info">{l s='You can select groups, you want to be assigned to' mod='verifycustomer'}</span>
		{/if}
	</div>
{/if}
{if $configuration['upload_file_position'] == $positionID && $configuration['show_upload_button']|intval == 1}
	<div class="uploadFileSection" style="margin-bottom: 20px;">
		<h3 class="page-subheading">{$configuration['upload_file_label'][$langID]|escape:'htmlall':'UTF-8'}{if $configuration['upload_file_required'] == true} *{/if}</h3>
		<p class="form-group">
			<input type="file" name="uploadFile" id="uploadFile" />
		</p>
		<span class="form_info">{$configuration['upload_file_description'][$langID]|escape:'htmlall':'UTF-8'}</span>
	</div>
	<script type="text/javascript">
		$('#account-creation_form').attr('enctype', 'multipart/form-data');
		$("#uploadFile").uniform();
	</script>
{/if}