{*
 * 2014 - 2020 Watt Is It
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
 * @copyright 2014 - 2020 Watt Is It
 * @license   https://creativecommons.org/licenses/by-nd/4.0/fr/ Creative Commons BY-ND 4.0
 * @version   3.0.1
 *}

<div class="pg-front">
	<div class="pg-payment-confirmation">
		<p>{$message|escape:'htmlall':'UTF-8'}</p>

		<div>
			<a href="{$url.link|escape:'html':'UTF-8'}" id="pg_redirect_link">{$url.text|pgtrans}</a>
		</div>
	</div>
</div>
<script type="text/javascript">

	setTimeout(function() {
		let url = document.getElementById('pg_redirect_link').attributes['href'].value;
		window.location.replace(url);
	}, 5000);

</script>
