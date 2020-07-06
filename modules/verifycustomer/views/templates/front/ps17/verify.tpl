{*
* 2017 Singleton software
*
*  @author Singleton software <info@singleton-software.com>
*  @copyright 2017 Singleton software
*}
{extends file='page.tpl'}
{block name='page_title'}
	{l s='Thanks you for your registration' mod='verifycustomer'}
{/block}
{block name="page_content"}
	<section class="page-verify-customer-box">
		<p class="alert alert-success" {if $willBeNotificated == 1}style="text-align: center"{/if}>
			{l s='Your account must be approved by an admin before you can login' mod='verifycustomer'}.
			{if $willBeNotificated == 1}{l s='We will notificated you by email, after your account will be approved' mod='verifycustomer'}.{/if}
		</p>
	</section>
	<a style="margin-top:10px" class="btn btn-defaul button button-small" href="{$urls.base_url|escape:"htmlall":"UTF-8"}">
		<span><i class="icon-chevron-left"></i>{l s='Back to Home' mod='verifycustomer'}</span>
	</a>
{/block}