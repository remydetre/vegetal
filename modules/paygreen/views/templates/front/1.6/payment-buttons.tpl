{*
 * 2014 - 2019 Watt Is It
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons BY-ND 4.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://creativecommons.org/licenses/by-nd/4.0/fr/
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@paygreen.fr so we can send you a copy immediately.
 *
 * @author    PayGreen <contact@paygreen.fr>
 * @copyright 2014 - 2019 Watt Is It
 * @license   https://creativecommons.org/licenses/by-nd/4.0/fr/ Creative Commons BY-ND 4.0
 * @version   2.7.6
 *}

	{foreach from=$buttons item=button}
	<span  id="paygreen_button_{$button['id']|escape:'html':'UTF-8'}">


		{if $button['displayType'] !== 'BLOC'}
		<div class="row">
			<div class="col-xs-12{if $button['displayType'] === 'HALF'} col-md-6{/if}">
				{/if}
				<p class="payment_module">
					<a href="javascript:document.getElementById('paygreen_form_button_{$button['id']|escape:'html':'UTF-8'}').submit()" title="{$button['text']|escape:'html':'UTF-8'}" class="paygreen_button{if $button['displayType'] !== 'BLOC'} bloc{/if}">
						<img src="{$button['image']|escape:'html':'UTF-8'}"{if $button['height'] > 0} height="{$button['height']|escape:'html':'UTF-8'}"{/if} alt="">
						{$button['text']|escape:'html':'UTF-8'}
					</a>
				</p>
				{if $button['displayType'] !== 'BLOC'}
			</div>
		</div>
		{/if}
		<form id="paygreen_form_button_{$button['id']|escape:'html':'UTF-8'}" action="{$action|escape:'html':'UTF-8'}" method="post">
			<input type="hidden" name="id" value="{$button['id']|escape:'html':'UTF-8'}" />
		</form>
	</span>
	{/foreach}
	<style type="text/css">
	p.payment_module a.paygreen_button.bloc {
		padding-left:10px;
		background-image:none;
	}
	</style>
