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


<div class="panel">
    <div class="panel-heading">
        <i class="icon-credit-card"></i> {l s='Configuration Payment Butons' mod='paygreen'}
    </div>

    <div class="ps_paygreen_buttonssetting">
        {foreach from=$buttons key=key item=btn}

            <form id="paygreen_btn_form{$btn['id']|escape:'html':'UTF-8'}" class="panel" action="#" method="post" enctype="multipart/form-data">
                <input type="hidden" name="id" value="{$btn['id']|escape:'html':'UTF-8'}">

                <h2>
                    {if $btn['id'] > 0}
                        {$btn['label']|escape:'htmlall':'UTF-8'}
                    {else}
                        {l s='New button' mod='paygreen'}
                    {/if}
                </h2>

                {if isset($btn['error'])}
                    <div class="alert alert-warning">
                        <strong>{$btn['error']|escape:'htmlall':'UTF-8'}</strong>
                    </div>
                {/if}
                {if isset($btn['warning'])}
                    <div class="alert alert-warning">
                        <strong>{$btn['warning']|escape:'htmlall':'UTF-8'}</strong>
                    </div>
                {/if}

                <div class="ps_paygreen_fields">

                    <!-- Text input-->
                    <div class="form-group">
                        <label class="control-label ps_paygreen_label" for="label{$btn['id']|escape:'html':'UTF-8'}">
                            {l s='Label' mod='paygreen'}
                        </label>

                        <div class="ps_paygreen_input">
                            <input id="label{$btn['id']|escape:'html':'UTF-8'}" name="label" type="text" placeholder="{l s='Button label' mod='paygreen'}" class="form-control input-md" required="required" value="{$btn['label']|escape:'html':'UTF-8'}" />

                            <span class="help-block">
                                {l s='Text displayed to the right of the icon' mod='paygreen'}
                            </span>
                        </div>
                    </div>

                    <!-- Text input-->
                    <div class="form-group">
                        <label class="control-label ps_paygreen_label" for="minAmount{$btn['id']|escape:'html':'UTF-8'}">
                            {l s='Amount cart' mod='paygreen'}
                        </label>

                        <div class="ps_paygreen_input ps_paygreen_amount">
                            <div>
                                <input id="minAmount{$btn['id']|escape:'html':'UTF-8'}" name="minAmount" min="0" type="number" placeholder="" class="form-control input-md" value="{if $btn['minAmount']>0}{$btn['minAmount'|escape:'html':'UTF-8']}{/if}" />
                                <span class="help-block">
                                    {l s='Minimum' mod='paygreen'}
                                </span>
                            </div>
                            <div class="ps_paygreen_to">
                                {l s='to' mod='paygreen'}
                            </div>
                            <div>
                                <input id="maxAmount{$btn['id']|escape:'html':'UTF-8'}" name="maxAmount" min="0" type="number" placeholder="" class="form-control input-md" value="{if $btn['maxAmount']>0}{$btn['maxAmount'|escape:'html':'UTF-8']}{/if}" />
                                <span class="help-block">
                                    {l s='Maximum' mod='paygreen'}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Select Basic -->
                    <div class="form-group">
                        <label class="control-label ps_paygreen_label" for="integration{$btn['id']|escape:'html':'UTF-8'}">
                            {l s='Integration type' mod='paygreen'}
                        </label>

                        <div class="ps_paygreen_input">
                            <select id="integration{$btn['id']|escape:'html':'UTF-8'}" name="integration" class="form-control">
                                <option value="0"{if $btn['integration'] == '0'} selected="selected"{/if}>
                                    {l s='External Payment' mod='paygreen'}
                                </option>
                                <option value="1"{if $btn['integration'] == '1'} selected="selected"{/if}>
                                    {l s='IFrame Payment' mod='paygreen'}
                                </option>
                            </select>
                            <span class="help-block">
                                {l s='if SSL is disabled, all butons IFrame will be disabled' mod='paygreen'}
                            </span>
                        </div>
                    </div>

                    <!-- File Button -->
                    <div class="form-group">
                        <label class="control-label ps_paygreen_label" for="image{$btn['id']|escape:'html':'UTF-8'}">
                            {l s='icon' mod='paygreen'}
                        </label>

                        <div class="ps_paygreen_input ps_paygreen_inputimage">
                            <div class="checkbox">
                                <label for="defaultimg{$btn['id']|escape:'html':'UTF-8'}">
                                    <input type="checkbox" name="defaultimg" id="defaultimg{$btn['id']|escape:'html':'UTF-8'}" class="input-checkbox" value="1" {if $btn['defaultimg'] == 1 || $btn['id'] == 0} checked {/if} />
                                    {l s='Use default image' mod='paygreen'}
                                </label>
                            </div>

                            <input id="image{$btn['id']|escape:'html':'UTF-8'}" name="image" class="input-file" type="file" />

                            {if $btn['image'] > ""}
                                <a href="{$icondir|escape:'html':'UTF-8'}{$btn['image']|escape:'html':'UTF-8'}" target="_blank" title="{l s='Image used' mod='paygreen'}">
                                   <img src="{$icondir|escape:'html':'UTF-8'}{$btn['image']|escape:'html':'UTF-8'}" />
                               </a>
                            {else}
                                <a href="{$icondir|escape:'html':'UTF-8'}paygreen_paiement.png" target="_blank" title="{l s='Default image' mod='paygreen'}">
                                    <img src="{$icondir|escape:'html':'UTF-8'}paygreen_paiement.png" />
                                </a>
                            {/if}
                        </div>
                    </div>

                    <!-- Select Basic -->
                    <div class="form-group">
                        <label class="control-label ps_paygreen_label" for="displayType{$btn['id']|escape:'html':'UTF-8'}">
                            {l s='Display type' mod='paygreen'}
                        </label>

                        <div class="ps_paygreen_input">
                            <select id="displayType{$btn['id']|escape:'html':'UTF-8'}" name="displayType" class="form-control">
                                <option value="1"{if $btn['displayType'] == '1'} selected="selected"{/if}>
                                    {l s='Image' mod='paygreen'}
                                </option>
                                <option value="2"{if $btn['displayType'] == '2'} selected="selected"{/if}>
                                    {l s='Label' mod='paygreen'}
                                </option>
                                <option value="3"{if $btn['displayType'] == '3'} selected="selected"{/if}>
                                    {l s='Label + Image' mod='paygreen'}
                                </option>
                            </select>
                        </div>
                    </div>

                    <!-- Number input-->
                    <div class="form-group">
                        <label class="control-label ps_paygreen_label" for="position{$btn['id']|escape:'html':'UTF-8'}">
                            {l s='Display Order' mod='paygreen'}
                        </label>

                        <div class="ps_paygreen_input">
                            <input id="position{$btn['id']|escape:'html':'UTF-8'}" name="position" min="0" type="number" placeholder="" class="form-control input-md" value="{$btn['position'|escape:'html':'UTF-8']}" />
                            <span class="help-block">
                                {l s='if empty, the position will be automatic' mod='paygreen'}
                            </span>
                        </div>
                    </div>

                    <!-- Select Basic -->
                    <div class="form-group">
                        <label class="control-label ps_paygreen_label" for="executedAt{$btn['id']|escape:'html':'UTF-8'}">
                            {l s='Payment mode' mod='paygreen'}
                        </label>

                        <div class="ps_paygreen_input">
                            <select id="executedAt{$btn['id']|escape:'html':'UTF-8'}" name="executedAt" class="form-control">
                                <option value="0"{if $btn['executedAt'] == '0'} selected="selected"{/if}>
                                    {l s='Cash' mod='paygreen'}
                                </option>
                                <option value="1"{if $btn['executedAt'] == '1'} selected="selected"{/if}>
                                    {l s='Subscription' mod='paygreen'}
                                </option>
                                <option value="3"{if $btn['executedAt'] == '3'} selected="selected"{/if}>
                                    {l s='In installments' mod='paygreen'}
                                </option>
                                <option value="-1"{if $btn['executedAt'] == '-1'} selected="selected"{/if}>
                                    {l s='At the delivery' mod='paygreen'}
                                </option>
                            </select>
                        </div>
                    </div>

                    <!-- Select Basic -->
                    <div class="form-group">
                        <label class="control-label ps_paygreen_label" for="paymentType{$btn['id']|escape:'html':'UTF-8'}">
                            {l s='Payment type' mod='paygreen'}
                        </label>

                        <div class="ps_paygreen_input">
                            <select id="paymentType{$btn['id']|escape:'html':'UTF-8'}" name="paymentType" class="form-control">
                                {foreach from=$paymentTypes item=paymentType}
                                <option value="{$paymentType|escape:'htmlall':'UTF-8'}"{if $btn['paymentType'] == $paymentType} selected="selected"{/if}>
                                    {$paymentType|escape:'htmlall':'UTF-8'}
                                </option>
                                {/foreach}
                            </select>
                        </div>
                    </div>

                    <!-- Text input-->
                    <div class="form-group">
                        <label class="control-label ps_paygreen_label labelnbPayment" for="nbPayment{$btn['id']|escape:'html':'UTF-8'}">
                            {l s='Number of installments' mod='paygreen'}
                        </label>

                        <div class="ps_paygreen_input">
                            <input id="nbPayment{$btn['id']|escape:'html':'UTF-8'}" name="nbPayment" type="number" min="1" max="24" class="form-control input-md" value="{$btn['nbPayment'|escape:'html':'UTF-8']}" />
                            <span class="help-block">
                                {l s='Defines the payment duration in months' mod='paygreen'}
                            </span>
                        </div>
                    </div>

                   <!-- Text input-->
                    <div class="form-group">
                        <label class="control-label ps_paygreen_label labelPercent" for="perCentPayment{$btn['id']|escape:'html':'UTF-8'}">
                            {l s='Percentage of first installment' mod='paygreen'}
                        </label>

                        <div class="ps_paygreen_input">
                            <div class="input-group">
                                <span class="input-group-addon">%</span>
                                <input id="perCentPayment{$btn['id']|escape:'html':'UTF-8'}" name="perCentPayment" type="number" min="1" max="99" class="form-control input-md"
                                {if $btn['perCentPayment'] neq 0}
                                    value="{$btn['perCentPayment'|escape:'html':'UTF-8']}"
                                {/if} />
                            </div>

                            <span class="help-block">
                                {l s='if empty, the position will be automatic' mod='paygreen'}
                            </span>
                        </div>
                    </div>

                    <!-- CheckBox Basic -->
                    <div class="form-group">
                        <label class="control-label ps_paygreen_label labelSubOption">
                            {l s='Renew previous orders' mod='paygreen'}
                        </label>

                        <div class="checkbox ps_paygreen_input">
                            <label for="subOption{$btn['id']|escape:'html':'UTF-8'}">
                                <input type="checkbox" name="subOption" id="subOption{$btn['id']|escape:'html':'UTF-8'}" value="1" {if $btn['subOption'] == 1} checked {/if} />
                                {l s='Enable option' mod='paygreen'}
                            </label>

                            <p class="help-block">
                                {l s='At each due payment, the previous order is recreated automatically' mod='paygreen'}
                            </p>
                        </div>
                    </div>


                    <!-- Select Basic -->
                    <div class="form-group">
                        <label class="control-label ps_paygreen_label labelReport" for="reportPayment{$btn['id']|escape:'html':'UTF-8'}">
                            {l s='Payment deferment' mod='paygreen'}
                        </label>

                        <div class="ps_paygreen_input">
                            <select id="reportPayment{$btn['id']|escape:'html':'UTF-8'}" name="reportPayment" class="form-control">
                                <option value="0"{if $btn['reportPayment'] == '0'} selected="selected"{/if}>
                                    {l s='No deferment' mod='paygreen'}
                                </option>
                                <option value="1 week"{if $btn['reportPayment'] == '1 week'} selected="selected"{/if}>
                                    {l s='1 week' mod='paygreen'}
                                </option>
                                <option value="2 weeks"{if $btn['reportPayment'] == '2 weeks'} selected="selected"{/if}>
                                    {l s='2 weeks' mod='paygreen'}
                                </option>
                                <option value="1 month"{if $btn['reportPayment'] == '1 month'} selected="selected"{/if}>
                                    {l s='1 month' mod='paygreen'}
                                </option>
                                <option value="2 months"{if $btn['reportPayment'] == '2 months'} selected="selected"{/if}>
                                    {l s='2 months' mod='paygreen'}
                                </option>
                                <option value="3 months"{if $btn['reportPayment'] == '3 months'} selected="selected"{/if}>
                                    {l s='3 months' mod='paygreen'}
                                </option>
                            </select>
                        </div>
                    </div>

                    <!-- Text input-->
                    <div class="form-group">
                        <label class="control-label ps_paygreen_label" for="reductionPayment{$btn['id']|escape:'html':'UTF-8'}">
                            {l s='Discount' mod='paygreen'}
                        </label>

                        <div class="ps_paygreen_input">
                            {$reductionPaymentId="reductionPayment`$btn['id']`"}
                            {html_options name=reductionPayment id=$reductionPaymentId options=$promoCode selected=$btn['reductionPayment']}
                            <span class="help-block" id="spanReductionPayment">
                                {l s='Non visible Cart Rules' mod='paygreen'}
                            </span>
                        </div>
                    </div>


                    <!-- Validate, Cancel, Delete -->
                    <div class="ps_paygreen_submit">
                        <label class="control-label" for="resetBtn{$btn['id']|escape:'html':'UTF-8'}"></label>

                        <button type="submit" id="validBtn{$btn['id']|escape:'html':'UTF-8'}" name="submitPaygreenModuleButton" class="btn btn-success pull-right">
                            {l s='Validate' mod='paygreen'}
                        </button>

                        {if $btn['id'] > 0}
                            <button id="deleteBtn{$btn['id']|escape:'html':'UTF-8'}" name="submitPaygreenModuleButtonDelete" class="btn btn-danger pull-right">
                                {l s='Delete' mod='paygreen'}
                            </button>
                        {else}
                            <button type="reset" id="resetBtn{$btn['id']|escape:'html':'UTF-8'}" name="resetBtn" class="btn btn-danger pull-right">
                                {l s='Cancel' mod='paygreen'}
                            </button>
                        {/if}
                    </div>

                </div>
            </form>

        {/foreach}
    </div>

</div>