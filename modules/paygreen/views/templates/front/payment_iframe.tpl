{*
* 2014 - 2015 Watt Is It
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
*  @author    PayGreen <contact@paygreen.fr>
*  @copyright 2014-2014 Watt It Is
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*
*}
{capture name=path}
	<a href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'html':'UTF-8'}" title="{l s='Go back to the Checkout' mod='paygreen'}">{l s='Checkout' mod='paygreen'}</a><span class="navigation-pipe">{$navigationPipe}</span>{l s='Paygreen payment' mod='paygreen'}
{/capture}
<h3>{l s='Paygreen payment' mod='paygreen'}</h3>

{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}

<div class="row">
    <div id="center_column" class="center_column col-xs-12 col-sm-12">
        <iframe id="pgIframe{$id|escape:'html':'UTF-8'}"
            class="pg-iframe"
            frameBorder="0"
            src="{$url|escape:'html':'UTF-8'}"
            style="height:{$minHeightIframe|escape:'html':'UTF-8'}px; width:{$minWidthIframe|escape:'html':'UTF-8'}px;">
        </iframe>
    </div>
</div>
