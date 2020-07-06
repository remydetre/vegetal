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

{function name=display_shipping}
    {assign var="checked" value=''}

    {if in_array($paymentType, $shipping_payments)}
        {assign var="checked" value='checked="checked"'}
    {/if}

    <td><input type="checkbox" name="paygreen_shipping_deactivated_payment_modes[]" value="{$paymentType|escape:'html':'UTF-8'}" {$checked|escape:'html':'UTF-8'} /></td>
{/function}

{assign var="paymentTypes" value=$shippingTab['paymentTypes']}
{assign var="shipping_payments" value=$shippingTab['shipping_payments']}

<div class="panel">
    <div class="panel-heading">
        <i class="icon-truck"></i> {'eligible_amounts.actions.save_shipping_payments.title'|pgtrans}
    </div>

    <div class="ps_paygreen_buttonssetting">
        <form id="paygreen_shipping_payments_form" class="panel" action="#" method="post" enctype="multipart/form-data">
            <div class="wc_paygreen_categories">
                {'eligible_amounts.actions.save_shipping_payments.header'|pgtranslines}
                <table>
                    <thead>
                        <tr>
                            <td>&nbsp;</td>
                            {foreach from=$paymentTypes item=paymentType}
                            <td>{$paymentType|upper|escape:'html':'UTF-8'}</td>
                            {/foreach}
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{l s='Frais de livraison non applicables' mod='paygreen'}</td>
                            {foreach from=$paymentTypes item=paymentType}
                                {display_shipping paymentType=$paymentType shipping_payments=$shipping_payments}
                            {/foreach}
                        </tr>
                    </tbody>
                </table>
                <p>&nbsp;</p>
                {'eligible_amounts.actions.save_shipping_payments.footer'|pgtranslines}
            </div>

            <!-- Validate, Cancel, Delete -->
            <div class="ps_paygreen_submit">
                <label class="control-label" for="resetBtn"></label>

                <button type="submit" id="validShippingPayments" name="submitShippingPayments" class="btn btn-success pull-right">
                    {l s='Validate' mod='paygreen'}
                </button>

                <button type="reset" id="resetShippingPayments" name="resetShippingPayments" class="btn btn-danger pull-right">
                    {l s='Cancel' mod='paygreen'}
                </button>
            </div>
        </form>
    </div>
</div>
