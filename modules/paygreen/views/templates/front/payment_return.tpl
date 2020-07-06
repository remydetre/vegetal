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
 * @version   2.5.4
 *}

<div class="box box-small clearfix">
{if isset($error) AND $error}
	<p>
		<h3>{$config['_PG_PAIEMENT_REFUSED']|escape:'html':'UTF-8'}</h3><br />
	</p>
{else}
	<p>
		<h3>{$config['_PG_PAIEMENT_ACCEPTED']|escape:'html':'UTF-8'}</h3><br />
	</p>
{/if}
</div>
