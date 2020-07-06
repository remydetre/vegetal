{*
* 2017 Singleton software
*
*  @author Singleton software <info@singleton-software.com>
*  @copyright 2017 Singleton software
*}
{if $groups|@count > 0}
	<script type="text/javascript">
	//<![CDATA[
		var customGroupPosition = parseInt({$configuration['custom_group_position']|escape:"javascript":"UTF-8"});
	//]]>
	</script>
	
	<div id="customerGroups" class="form-group row">
		<label class="col-md-3 form-control-label">
			{l s='Account Group' mod='verifycustomer'}
		</label>
		{if $configuration['custom_group_select_type'] == 1}
		    <div class="col-md-6 form-control-valign">
		    	{foreach from=$groups item=group name=group}
			    	<div class="clearfix" style="margin-top: 5px">
						<span class="custom-radio pull-xs-left">
							<input name="id_group" id="id_group{$group.id_group|escape:'htmlall':'UTF-8'}" value="{$group.id_group|escape:'htmlall':'UTF-8'}" type="radio"{if $selectedRadioGroup == $group.id_group} checked="checked"{/if}/>
							<span></span>
			            </span>
			            {$group.name|escape:'htmlall':'UTF-8'}
			        </div>
		        {/foreach}
		    </div>
	    {else}
	    	<div class="col-md-6 form-control-valign" style="margin-top: 7px">
		    	{foreach from=$groups item=group name=group}
			    	<div class="clearfix">
						<span class="custom-checkbox pull-xs-left">
							{assign var="isChecked" value=false}
							{foreach from=$selectedCheckboxGroups item=selectedGroup name=selectedGroup}
								{if $selectedGroup == $group.id_group}
									{assign var="isChecked" value=true}
								{/if}
							{/foreach}
							<input name="groups[]" id="groups" value="{$group.id_group|escape:'htmlall':'UTF-8'}" type="checkbox"{if $isChecked} checked="checked"{/if}/>
							<span><i class="material-icons checkbox-checked"></i></span>
							<label style="margin-top: -3px">{$group.name|escape:'htmlall':'UTF-8'}</label>
			            </span>
			        </div>
		        {/foreach}
		    </div>
	    {/if}
	    <div class="col-md-3 form-control-comment">{l s='You can select group, you want to be assigned to' mod='verifycustomer'}</div>
	</div>
{/if}