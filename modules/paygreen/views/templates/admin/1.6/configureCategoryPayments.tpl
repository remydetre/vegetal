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


{function name=display_categories}
    {assign var="depth" value=$category->getDepth()}

    <tr data-depth="{$depth|escape:'htmlall':'UTF-8'}" data-name="{$category->getName()|escape:'htmlall':'UTF-8'}" data-id="{$category->getId()|escape:'htmlall':'UTF-8'}">
        <td class="dec_{$depth|escape:'htmlall':'UTF-8'}">{$category->getName()|escape:'htmlall':'UTF-8'}</td>

        {foreach from=$paymentTypes key=key item=paymentType}
            {if $category->hasPaymentMode($paymentType)}
                {assign var="checked" value='checked="checked"'}
            {else}
                {assign var="checked" value=''}
            {/if}

            <td><input type="checkbox" name="wcpaygreen_category_payments[{$category->getId()|escape:'htmlall':'UTF-8'}][]" value="{$paymentType|escape:'htmlall':'UTF-8'}" {$checked|escape:'htmlall':'UTF-8'} /></td>
        {/foreach}

        {foreach from=$category->getChildren() key=key item=child}
            {display_categories category=$child paymentTypes=$paymentTypes}
        {/foreach}

    </tr>
{/function}


<div class="panel">
    <div class="panel-heading">
        <i class="icon-credit-card"></i> {l s='Activation des moyens de paiement par catégorie' mod='paygreen'}
    </div>

    <div class="ps_paygreen_buttonssetting">
        <form id="paygreen_category_payments_form" class="panel" action="#" method="post" enctype="multipart/form-data">
            <div class="wc_paygreen_categories">
                <p>
                    {l s='Activez vos moyens de paiement en fonction de la catégorie des produits ajoutés au panier. Exemple : n\'activez Connecs que pour les catégories contenant exclusivement des produits alimentaires éligibles.' mod='paygreen'}
                </p>

                <table>
                    <thead>
                    <tr>
                        <td><input id="category-filter" type="text" placeholder="{l s='Catégorie...' mod='paygreen'}" /></td>
                        {foreach from=$paymentTypes key=key item=paymentType}
                        <td>{$paymentType|upper|escape:'html':'UTF-8'}</td>
                        {/foreach}
                    </tr>
                    </thead>
                    <tbody id="categories">
                    {foreach from=$categories key=key item=category}
                        {display_categories category=$category paymentTypes=$paymentTypes}
                    {/foreach}
                    </tbody>
                </table>
            </div>


            <!-- Validate, Cancel, Delete -->
            <div class="ps_paygreen_submit">
                <label class="control-label" for="resetBtn"></label>

                <button type="submit" id="validCategoryPayments" name="submitCategoryPayments" class="btn btn-success pull-right">
                    {l s='Validate' mod='paygreen'}
                </button>

                <button type="reset" id="resetCategoryPayments" name="resetCategoryPayments" class="btn btn-danger pull-right">
                    {l s='Cancel' mod='paygreen'}
                </button>
            </div>
        </form>
    </div>
</div>
