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

<div style="text-align: center;">
	<p style="text-align: center; font-size: 1.5em; margin-bottom: 10px;">{$title|escape:'html':'UTF-8'}</p>
	<iframe id="pgIframe{$id|escape:'html':'UTF-8'}"
			frameBorder="0"
			src="{$url|escape:'html':'UTF-8'}"
			style="height:{$minHeightIframe|escape:'html':'UTF-8'}px; width:{$minWidthIframe|escape:'html':'UTF-8'}px; display: block; margin: auto;">
	</iframe>
	<div style="text-align: center;"><a href="{$return_url|escape:'html':'UTF-8'}">Annuler mon paiement</a></div>
	<p style="text-align: center; font-size: 0.6em; margin-bottom: 10px;">Service proposé par <strong>Paygreen&copy;&reg;</strong></p>
</div>