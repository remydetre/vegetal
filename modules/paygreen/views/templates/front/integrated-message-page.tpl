{*
* 2007-2019 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2019 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{extends file='page.tpl'}

{block name='content'}
	<div>
		<h3>{$title|escape:'htmlall':'UTF-8'}</h3>

		{if !empty($message)}
			<p>{$message|escape:'htmlall':'UTF-8'}</p>
		{/if}

		{if !empty($errors)}
		<ul class="alert alert-danger">
			{foreach from=$errors item='error'}
				<li>{$error|escape:'htmlall':'UTF-8'}.</li>
			{/foreach}
		</ul>
		{/if}

		{if !empty($url)}
			<a href="{$url.link|escape:'html':'UTF-8'}" id="redirect_link">{$url.text|escape:'htmlall':'UTF-8'}</a>
			{if $url.reload == true}
				<script type="text/javascript">
					setTimeout(function() {
						let url = document.getElementById('redirect_link').attributes['href'].value;
						window.location.replace(url);
					}, 5000);
				</script>
			{/if}
		{/if}

		{if !empty($exceptions) && $env=="DEV"}
			{foreach from=$exceptions item='exception'}
				<h4>{$exception->getMessage()|escape:'htmlall':'UTF-8'}</h4>
				{$exception|@debug_print_var|escape:'htmlall':'UTF-8'}
			{/foreach}
		{/if}
	</div>
{/block}