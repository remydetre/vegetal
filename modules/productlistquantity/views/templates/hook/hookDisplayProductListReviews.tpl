{*
* 2007-2018 PrestaShop
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2019 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

    <form action="{$page_cart|escape:'htmlall':'UTF-8'}" method="post" class="quantity_form">
        <input type="hidden" name="token" value="{$static_token|escape:'htmlall':'UTF-8'}">
        <input type="hidden" value="{$product.id_product|intval}" name="id_product">
        <input type="hidden" value="{$product.quantity|intval}" name="product_quantity">
        {if $product.available_for_order}
            <input type="number" min="{if isset($product.product_attribute_minimal_quantity) && $product.product_attribute_minimal_quantity >= 1}{$product.product_attribute_minimal_quantity|intval}{else}{$product.minimal_quantity|intval}{/if}" class="input-group form-control number-quantity" name="qty" value="{if isset($product.product_attribute_minimal_quantity) && $product.product_attribute_minimal_quantity >= 1}{$product.product_attribute_minimal_quantity|intval}{else}{$product.minimal_quantity|intval}{/if}" data_allow_order="{if $product.allow_oosp || !$is_stock_management}1{/if}">
	    {if ($product.allow_oosp || $product.quantity > 0)}
                <button data-button-action="add-to-cart" class="btn btn-primary button-quantity">{l s='Add to cart' mod='productlistquantity'}</button>
	    {else}
                <button class="btn btn-primary button-quantity" disabled >{l s='Add to cart' mod='productlistquantity'}</button>
	    {/if}
        {/if}
    </form>
