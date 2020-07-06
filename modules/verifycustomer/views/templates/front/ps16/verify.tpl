{*
* 2017 Singleton software
*
*  @author Singleton software <info@singleton-software.com>
*  @copyright 2017 Singleton software
*}
{capture name=path}{l s='Thanks for your registration' mod='verifycustomer'}{/capture}
<section class="page-verify-customer-box">
	<h3 class="page-verify-customer-heading">
		{l s='Thank you for your registration' mod='verifycustomer'}
	</h3>
	<p class="alert alert-success" {if $willBeNotificated == 1}style="text-align: center"{/if}>
		{l s='Your account must be approved by an admin before you can login' mod='verifycustomer'}.
		{if $willBeNotificated == 1}{l s='We will notificated you by email, after your account will be approved' mod='verifycustomer'}.{/if}
	</p>
</section>
<a style="margin-top:10px" class="btn btn-defaul button button-small" href="{if isset($force_ssl) && $force_ssl}{$base_dir_ssl|escape:'htmlall':'UTF-8'}{else}{$base_dir|escape:'htmlall':'UTF-8'}{/if}">
	<span><i class="icon-chevron-left"></i>{l s='Back to Home' mod='verifycustomer'}</span>
</a>