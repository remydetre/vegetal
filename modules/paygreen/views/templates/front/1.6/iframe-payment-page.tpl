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

{capture name=path}
	<a href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'html':'UTF-8'}" title="{l s='Go back to the Checkout' mod='paygreen'}">{l s='Checkout' mod='paygreen'}</a><span class="navigation-pipe">{$navigationPipe}</span>{l s='Paygreen payment' mod='paygreen'}
{/capture}
<h3>{l s='Paygreen payment' mod='paygreen'}</h3>

{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}

<div class="row">
    <div id="center_column" class="center_column col-xs-12 col-sm-12" style="text-align: center;">
        {include file='../partials/iframe-payment.tpl' id=$id title=$title url=$url return_url=$return_url minHeightIframe=$minHeightIframe minWidthIframe=$minWidthIframe}
    </div>
</div>
