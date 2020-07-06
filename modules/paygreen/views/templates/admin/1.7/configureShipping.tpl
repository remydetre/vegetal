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


{function name=display_shipping}
    {assign var="checked" value=''}

    {if in_array($paymentType, $shipping_payments)}
        {assign var="checked" value='checked="checked"'}
    {/if}

    <td><input type="checkbox" name="paygreen_shipping_deactivated_payment_modes[]" value="{$paymentType|escape:'html':'UTF-8'}" {$checked|escape:'html':'UTF-8'} /></td>
{/function}


<div class="panel">
    <div class="panel-heading">
        <i class="icon-credit-card"></i> {l s='Gestion des frais de livraison par moyen de paiement' mod='paygreen'}
    </div>

    <div class="ps_paygreen_buttonssetting">
        <form id="paygreen_shipping_payments_form" class="panel" action="#" method="post" enctype="multipart/form-data">
            <div class="wc_paygreen_categories">
                <p>
                    {l s='Si certains moyens de paiement ne peuvent pas prendre en charge les frais de livraison (exemple : Connecs), cochez la case correspondante.' mod='paygreen'}
                </p>

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
