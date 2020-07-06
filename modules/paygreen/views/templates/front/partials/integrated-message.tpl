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

<div class="clearfix">
	<div class="box box-small">
		{if isset($title) && !empty($title)}
			<h3>{$title|escape:'htmlall':'UTF-8'}</h3>
		{/if}

		{if isset($message) && !empty($message)}
			<p>{$message|escape:'htmlall':'UTF-8'}</p>
		{/if}

		{if isset($errors) && !empty($errors)}
			<ul class="alert alert-danger">
				{foreach from=$errors item='error'}
					<li>{$error|escape:'htmlall':'UTF-8'}.</li>
				{/foreach}
			</ul>
		{/if}

		{if isset($url) && !empty($url)}
			<div style="text-align: center">
				<a href="{$url.link|escape:'html':'UTF-8'}" id="redirect_link" class="btn btn-primary btn-lg">{$url.text|escape:'htmlall':'UTF-8'}</a>
				{if $url.reload == true}
					<script type="text/javascript">
						setTimeout(function() {
							let url = document.getElementById('redirect_link').attributes['href'].value;
							window.location.replace(url);
						}, 5000);
					</script>
				{/if}
			</div>
		{/if}
	</div>

	{if ($env === "DEV") && isset($exceptions) && !empty($exceptions)}
		{foreach from=$exceptions item='exception'}
			<hr />
			<div class="box box-small clearfix">
				<h4>Exception of type {get_class($exception)|escape:'htmlall':'UTF-8'}</h4>
				<p>In <strong>{$exception->getFile()|escape:'htmlall':'UTF-8'}</strong> at line <strong>{$exception->getLine()|escape:'htmlall':'UTF-8'}</strong></p>
				<p style="font-size: 2em;">{$exception->getMessage()|escape:'htmlall':'UTF-8'}</p>
				<ol class="text-monospace">
					{foreach from=$exception->getTrace() item='trace'}
						<li><strong>{$trace['function']|escape:'htmlall':'UTF-8'}</strong> in {$trace['file']|escape:'htmlall':'UTF-8'} at line {$trace['line']|escape:'htmlall':'UTF-8'}</li>
					{/foreach}
				</ol>
			</div>
		{/foreach}
	{/if}
</div>
