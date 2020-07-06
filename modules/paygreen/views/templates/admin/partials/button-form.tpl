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

<form id="paygreen_btn_form{$btn['id']|escape:'html':'UTF-8'}" class="panel" action="#" method="post" enctype="multipart/form-data">
    <input type="hidden" name="id" value="{$btn['id']|escape:'html':'UTF-8'}">

    <h2>
        {if $btn['id'] > 0}
            {$btn['label']|escape:'htmlall':'UTF-8'}
        {else}
            {'button.form.title_new_button'|pgtrans}
        {/if}
    </h2>

    {if !empty($btn['errors'])}
        {foreach from=$btn['errors'] item=error}
            <div class="alert alert-warning">
                <strong>{$error|pgtrans}</strong>
            </div>
        {/foreach}
    {/if}

    <div class="ps_paygreen_fields">

        <!-- Text input-->
        <div class="form-group">
            <label class="control-label ps_paygreen_label" for="label_{$btn['id']|escape:'html':'UTF-8'}">
                {'button.form.fields.label.label'|pgtrans}
            </label>

            <div class="ps_paygreen_input">
                <input id="label_{$btn['id']|escape:'html':'UTF-8'}" name="label" type="text" placeholder="{'button.form.fields.label.placeholder'|pgtrans}" class="form-control input-md" required="required" value="{$btn['label']|escape:'html':'UTF-8'}" />

                <span class="help-block">
                    {'button.form.fields.label.helper'|pgtrans}
                </span>
            </div>
        </div>

        <!-- Text input-->
        <div class="form-group">
            <label class="control-label ps_paygreen_label" for="minAmount_{$btn['id']|escape:'html':'UTF-8'}">
                {'button.form.fields.min_amount.label'|pgtrans}
            </label>

            <div class="ps_paygreen_input ps_paygreen_amount">
                <div>
                    <input id="minAmount_{$btn['id']|escape:'html':'UTF-8'}" name="minAmount" min="0" type="number" placeholder="" class="form-control input-md" value="{if $btn['minAmount']>0}{$btn['minAmount'|escape:'html':'UTF-8']}{/if}" />
                    <span class="help-block">
                        {'button.form.fields.min_amount.helper'|pgtrans}
                    </span>
                </div>
                <div class="ps_paygreen_to">
                    {'button.form.fields.max_amount.label'|pgtrans}
                </div>
                <div>
                    <input id="maxAmount_{$btn['id']|escape:'html':'UTF-8'}" name="maxAmount" min="0" type="number" placeholder="" class="form-control input-md" value="{if $btn['maxAmount']>0}{$btn['maxAmount'|escape:'html':'UTF-8']}{/if}" />
                    <span class="help-block">
                        {'button.form.fields.max_amount.helper'|pgtrans}
                    </span>
                </div>
            </div>
        </div>

        <!-- Select Basic -->
        <div class="form-group">
            <label class="control-label ps_paygreen_label" for="integration_{$btn['id']|escape:'html':'UTF-8'}">
                {'button.form.fields.integration.label'|pgtrans}
            </label>

            <div class="ps_paygreen_input">
                <select id="integration_{$btn['id']|escape:'html':'UTF-8'}" name="integration" class="form-control js-payment-integration-selector">
                    <option value="EXTERNAL"{if $btn['integration'] == 'EXTERNAL'} selected="selected"{/if}>
                        {'button.form.fields.integration.values.external'|pgtrans}
                    </option>
                    <option value="INSITE"{if $btn['integration'] == 'INSITE'} selected="selected"{/if}>
                        {'button.form.fields.integration.values.insite'|pgtrans}
                    </option>
                </select>
                <span class="help-block">
                    {'button.form.fields.integration.helper'|pgtrans}
                </span>
            </div>
        </div>

        <!-- File Button -->
        <div class="form-group">
            <label class="control-label ps_paygreen_label" for="image_{$btn['id']|escape:'html':'UTF-8'}">
                {'button.form.fields.image.label'|pgtrans}
            </label>

            <div class="ps_paygreen_input ps_paygreen_inputimage">
                <div class="checkbox">
                    <label for="defaultimg_{$btn['id']|escape:'html':'UTF-8'}">
                        <input type="checkbox" name="defaultimg" id="defaultimg_{$btn['id']|escape:'html':'UTF-8'}" class="input-checkbox" value="1" />
                        {'button.form.fields.image.default'|pgtrans}
                    </label>
                </div>

                <input id="image_{$btn['id']|escape:'html':'UTF-8'}" name="image" class="input-file" type="file" />
                <a href="{$btn['imageUrl']|escape:'html':'UTF-8'}"
                   target="_blank" title="{'button.form.fields.image.current'|pgtrans}"><img
                            src="{$btn['imageUrl']|escape:'html':'UTF-8'}"
                            style="max-height: {if $btn['height'] > 0}{$btn['height']|escape:'html':'UTF-8'}{else}60{/if}px;"/></a>
            </div>
        </div>

        {if $config['isHeightFieldDisplayed']}
        <!-- Number input-->
        <div class="form-group">
            <label class="control-label ps_paygreen_label" for="height_{$btn['id']|escape:'html':'UTF-8'}">
                {'button.form.fields.height.label'|pgtrans}
            </label>

            <div class="ps_paygreen_input">
                <input id="height_{$btn['id']|escape:'html':'UTF-8'}" name="height" min="1" type="number" placeholder="" class="form-control input-md" {if $btn['height'] > 0}value="{$btn['height']|escape:'html':'UTF-8'}"{/if} />
                <span class="help-block">
                    {'button.form.fields.height.helper'|pgtrans}
                </span>
            </div>
        </div>
        {/if}

        <!-- Select Basic -->
        <div class="form-group">
            <label class="control-label ps_paygreen_label" for="displayType_{$btn['id']|escape:'html':'UTF-8'}">
                {'button.form.fields.display_type.label'|pgtrans}
            </label>

            <div class="ps_paygreen_input">
                <select id="displayType_{$btn['id']|escape:'html':'UTF-8'}" name="displayType" class="form-control">
                    {foreach from=$config['displayTypes'] key=type item=text}
                    <option value="{$type}"{if $btn['displayType'] == $type} selected="selected"{/if}>
                        {$text|pgtrans}
                    </option>
                    {/foreach}
                </select>
                <span class="help-block">
                    {'button.form.fields.display_type.helper'|pgtrans}
                </span>
            </div>
        </div>

        <!-- Number input-->
        <div class="form-group">
            <label class="control-label ps_paygreen_label" for="position_{$btn['id']|escape:'html':'UTF-8'}">
                {'button.form.fields.position.label'|pgtrans}
            </label>

            <div class="ps_paygreen_input">
                <input id="position_{$btn['id']|escape:'html':'UTF-8'}" name="position" min="0" type="number" placeholder="" class="form-control input-md" value="{$btn['position'|escape:'html':'UTF-8']}" />
                <span class="help-block">
                    {'button.form.fields.position.helper'|pgtrans}
                </span>
            </div>
        </div>

        <!-- Select Basic -->
        <div class="form-group">
            <label class="control-label ps_paygreen_label" for="paymentMode_{$btn['id']|escape:'html':'UTF-8'}">
                {'button.form.fields.payment_mode.label'|pgtrans}
            </label>

            <div class="ps_paygreen_input">
                <select id="paymentMode_{$btn['id']|escape:'html':'UTF-8'}" name="paymentMode" class="form-control js-payment-mode-selector" data-button="{$btn['id']|escape:'html':'UTF-8'}" required>
                    <option value="">{'button.form.fields.payment_mode.empty'|pgtrans}</option>
                    {foreach from=$config['paymentModes'] item=paymentMode key=code}
                    <option value="{$code|escape:'htmlall':'UTF-8'}"{if $btn['paymentMode'] == $code} selected="selected"{/if}>
                        {$paymentMode|escape:'htmlall':'UTF-8'}
                    </option>
                    {/foreach}
                </select>
                <span class="help-block">
                    {'button.form.fields.payment_mode.helper'|pgtrans}
                </span>
            </div>
        </div>

        <!-- Select Basic -->
        <div class="form-group">
            <label class="control-label ps_paygreen_label" for="paymentType_{$btn['id']|escape:'html':'UTF-8'}">
                {'button.form.fields.payment_type.label'|pgtrans}
            </label>

            <div class="ps_paygreen_input">
                <select id="paymentType_{$btn['id']|escape:'html':'UTF-8'}" name="paymentType" class="form-control js-payment-type-selector" data-button="{$btn['id']|escape:'html':'UTF-8'}" required>
                    <option value="">{'button.form.fields.payment_type.empty'|pgtrans}</option>
                    {foreach from=$config['paymentTypes'] item=paymentType key=code}
                    <option value="{$code|escape:'htmlall':'UTF-8'}"{if $btn['paymentType'] == $code} selected="selected"{/if}>
                        {$paymentType|escape:'htmlall':'UTF-8'}
                    </option>
                    {/foreach}
                </select>
                <span class="help-block">
                    {'button.form.fields.payment_type.helper'|pgtrans}
                </span>
            </div>
        </div>

        <!-- Text input-->
        <div id="paymentNumberField-{$btn['id']|escape:'html':'UTF-8'}" class="form-group hidden-field js-hidden-field-togglable-{$btn['id']|escape:'html':'UTF-8'}">
            <label class="control-label ps_paygreen_label labelPaymentNumber" for="paymentNumber_{$btn['id']|escape:'html':'UTF-8'}">
                {'button.form.fields.payment_number.label'|pgtrans}
            </label>

            <div class="ps_paygreen_input">
                <input id="paymentNumber_{$btn['id']|escape:'html':'UTF-8'}" name="paymentNumber" type="number" min="1" max="24" class="form-control input-md" value="{$btn['paymentNumber'|escape:'html':'UTF-8']}" />
                <span class="help-block">
                    {'button.form.fields.payment_number.helper'|pgtrans}
                </span>
            </div>
        </div>

       <!-- Text input-->
        <div id="firstPaymentPartField-{$btn['id']|escape:'html':'UTF-8'}" class="form-group hidden-field js-hidden-field-togglable-{$btn['id']|escape:'html':'UTF-8'}">
            <label class="control-label ps_paygreen_label labelPercent" for="firstPaymentPart_{$btn['id']|escape:'html':'UTF-8'}">
                {'button.form.fields.first_payment_part.label'|pgtrans}
            </label>

            <div class="ps_paygreen_input">
                <div class="input-group">
                    <span class="input-group-addon">%</span>
                    <input id="firstPaymentPart_{$btn['id']|escape:'html':'UTF-8'}" name="firstPaymentPart" type="number" min="1" max="99" class="form-control input-md"
                    {if $btn['firstPaymentPart'] neq 0}
                        value="{$btn['firstPaymentPart'|escape:'html':'UTF-8']}"
                    {/if} />
                </div>

                <span class="help-block">
                    {'button.form.fields.first_payment_part.helper'|pgtrans}
                </span>
            </div>
        </div>

        <!-- CheckBox Basic -->
        <div id="orderRepeatedField-{$btn['id']|escape:'html':'UTF-8'}" class="form-group hidden-field js-hidden-field-togglable-{$btn['id']|escape:'html':'UTF-8'}">
            <label class="control-label ps_paygreen_label labelSubOption">
                {'button.form.fields.order_repeated.label'|pgtrans}
            </label>

            <div class="checkbox ps_paygreen_input">
                <label for="orderRepeated_{$btn['id']|escape:'html':'UTF-8'}">
                    <input type="checkbox" name="orderRepeated" id="orderRepeated_{$btn['id']|escape:'html':'UTF-8'}" value="1" {if $btn['orderRepeated'] == 1} checked {/if} />
                    {'button.form.fields.order_repeated.placeholder'|pgtrans}
                </label>

                <p class="help-block">
                    {'button.form.fields.order_repeated.helper'|pgtrans}
                </p>
            </div>
        </div>


        <!-- Select Basic -->
        <div id="paymentReportField-{$btn['id']|escape:'html':'UTF-8'}" class="form-group hidden-field js-hidden-field-togglable-{$btn['id']|escape:'html':'UTF-8'}">
            <label class="control-label ps_paygreen_label labelReport" for="paymentReport_{$btn['id']|escape:'html':'UTF-8'}">
                {'button.form.fields.payment_report.label'|pgtrans}
            </label>

            <div class="ps_paygreen_input">
                <select id="paymentReport_{$btn['id']|escape:'html':'UTF-8'}" name="paymentReport" class="form-control">
                    <option value="0"{if $btn['paymentReport'] == '0'} selected="selected"{/if}>
                        {'button.form.fields.payment_report.values.none'|pgtrans}
                    </option>
                    <option value="1 week"{if $btn['paymentReport'] == '1 week'} selected="selected"{/if}>
                        {'button.form.fields.payment_report.values.1week'|pgtrans}
                    </option>
                    <option value="2 weeks"{if $btn['paymentReport'] == '2 weeks'} selected="selected"{/if}>
                        {'button.form.fields.payment_report.values.2week'|pgtrans}
                    </option>
                    <option value="1 month"{if $btn['paymentReport'] == '1 month'} selected="selected"{/if}>
                        {'button.form.fields.payment_report.values.1month'|pgtrans}
                    </option>
                    <option value="2 months"{if $btn['paymentReport'] == '2 months'} selected="selected"{/if}>
                        {'button.form.fields.payment_report.values.2month'|pgtrans}
                    </option>
                    <option value="3 months"{if $btn['paymentReport'] == '3 months'} selected="selected"{/if}>
                        {'button.form.fields.payment_report.values.3month'|pgtrans}
                    </option>
                </select>
                <p class="help-block">
                    {'button.form.fields.payment_report.helper'|pgtrans}
                </p>
            </div>
        </div>

        <!-- Text input-->
        <!--
        <div class="form-group">
            <label class="control-label ps_paygreen_label" for="discount_{$btn['id']|escape:'html':'UTF-8'}">
                {'button.form.fields.discount.label'|pgtrans}
            </label>

            <div class="ps_paygreen_input">
                {$discountId="discount_`$btn['id']`"}
                {html_options name=discount id=$discountId options=$config['promoCode'] selected=$btn['discount']}
                <span class="help-block" id="spanReductionPayment">
                    {'button.form.fields.discount.helper'|pgtrans}
                </span>
            </div>
        </div>
        -->


        <!-- Validate, Cancel, Delete -->
        <div class="ps_paygreen_submit">
            <label class="control-label" for="resetBtn{$btn['id']|escape:'html':'UTF-8'}"></label>

            <button type="submit" id="validBtn{$btn['id']|escape:'html':'UTF-8'}" name="submitPaygreenModuleButton" class="btn btn-success pull-right">
                {'module.form.buttons.validate'|pgtrans}
            </button>

            {if $btn['id'] > 0}
                <button id="deleteBtn{$btn['id']|escape:'html':'UTF-8'}" name="submitPaygreenModuleButtonDelete" class="btn btn-danger pull-right" onclick="return confirm('{'button.actions.delete.confirmation'|pgtrans|escape:javascript}')">
                    {'module.form.buttons.delete'|pgtrans}
                </button>
            {else}
                <button type="reset" id="resetBtn{$btn['id']|escape:'html':'UTF-8'}" name="resetBtn" class="btn btn-danger pull-right">
                    {'module.form.buttons.cancel'|pgtrans}
                </button>
            {/if}
        </div>

    </div>
</form>
