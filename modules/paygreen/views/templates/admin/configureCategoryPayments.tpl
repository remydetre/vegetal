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

{function name=display_categories}
    {assign var="depth" value=$category->getDepth()}

    <tr data-depth="{$depth|escape:'htmlall':'UTF-8'}" data-name="{$category->getName()|escape:'htmlall':'UTF-8'}" data-id="{$category->id()|escape:'htmlall':'UTF-8'}">
        <td class="dec_{$depth|escape:'htmlall':'UTF-8'}">{$category->getName()|escape:'htmlall':'UTF-8'}</td>

        {foreach from=$paymentTypes key=key item=paymentType}
            {if $category->hasPaymentMode($paymentType)}
                {assign var="checked" value='checked="checked"'}
            {else}
                {assign var="checked" value=''}
            {/if}

            <td><input type="checkbox" name="wcpaygreen_category_payments[{$category->id()|escape:'htmlall':'UTF-8'}][]" value="{$paymentType|escape:'htmlall':'UTF-8'}" {$checked|escape:'htmlall':'UTF-8'} /></td>
        {/foreach}

        {foreach from=$category->getChildren() key=key item=child}
            {display_categories category=$child paymentTypes=$paymentTypes}
        {/foreach}

    </tr>
{/function}

{assign var="paymentTypes" value=$categoryPaymentsTab['paymentTypes']}
{assign var="categories" value=$categoryPaymentsTab['categories']}

<div class="panel">
    <div class="panel-heading">
        <i class="icon-tags"></i> {'eligible_amounts.actions.save_category_payments.title'|pgtrans}
    </div>

    <div class="ps_paygreen_buttonssetting">
        <form id="paygreen_category_payments_form" class="panel" action="#" method="post" enctype="multipart/form-data">
            <div class="wc_paygreen_categories">
                {'eligible_amounts.actions.save_category_payments.header'|pgtranslines}
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
                <p>&nbsp;</p>
                {'eligible_amounts.actions.save_category_payments.footer'|pgtranslines}
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
