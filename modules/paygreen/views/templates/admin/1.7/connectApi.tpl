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

<div class="panel">
    <div class="panel-heading">
        <i class="icon-user"></i> {l s='Configuration Paygreen Account' mod='paygreen'}
    </div>

    <div id="ps_paygreen_menuadmin">

        <div id="ps_paygreen_step0">
            <a href="https://paygreen.fr/login" target="_blank" class="ps_paygreen_logoadmin">
                <img class="img-responsive center-block" src="{$imgdir|escape:'html':'UTF-8'}paygreen_logo.png" />
            </a>

            {if $connected == false}
                <a href="{$urlBase|escape:'html':'UTF-8'}" class="btn btn-success" role="button">
                    {l s='Log in' mod='paygreen'}
                </a>

                <span class='text-success'>
                    OU
                </span>

                <a href="http://paygreen.fr/subscribe" class="btn btn-default" role="button">
                    {l s='Create an account' mod='paygreen'}
                </a>

            {else}
                <a href="{$urlBaseDeconnect|escape:'html':'UTF-8'}" class="btn btn-default" role="button">
                    {l s='Log out' mod='paygreen'}
                </a>

                <p class="text-danger">
                    {l s='Attention : si vous vous déconnectez, vous ne serez plus en mesure d\'accepter des paiements via le module PayGreen.' mod='paygreen'}
                </p>
            {/if}

            {if $infoShop==null && $connected == false}
                <p class="text-danger">
                    {l s='The unique ID and/or private key have not been informed or they are incorrect.' mod='paygreen'}
                </p>
            {/if}
        </div>

        {if $infoShop != null && $infoAccount != null}
            <div class="ps_paygreen_stepsbox">
                <div class="panel ps_paygreen_step" id="ps_paygreen_step1">
                    <h2>
                        {l s='Step' mod='paygreen'} 1
                    </h2>

                    <p>
                        {l s='To collect your first payment, thank you to fill in the required information by clicking on the link below' mod='paygreen'} :
                    </p>

                    <a href="http://paygreen.fr/shop/wizard-activation" target="_blank" class="btn btn-default ps_paygreen_margin10" role="button">
                        {l s='My information' mod='paygreen'}
                    </a>


                    <div>
                        <strong>
                            {l s='SIRET' mod='paygreen'} :
                        </strong>

                        {if !empty($infoAccount->siret)}
                            <span>
                                {$infoAccount->siret|escape:'html':'UTF-8'}
                            </span>
                        {else}
                            <span class='text-danger'>
                                {l s='Not specified' mod='paygreen'}
                            </span>
                        {/if}
                    </div>

                    <div>
                        <strong>
                            {l s='IBAN' mod='paygreen'} :
                        </strong>

                        {if !empty($infoAccount->IBAN)}
                            <span>
                                {$infoAccount->IBAN|escape:'html':'UTF-8'}
                            </span>
                        {else}
                            <span class='text-danger'>
                                {l s='Not specified' mod='paygreen'}
                            </span>
                        {/if}
                    </div>

                    <div>
                        <strong>
                            {l s='URL' mod='paygreen'} :
                        </strong>

                        {if !empty($infoAccount->url)}
                            <span>
                                <a href="{$infoAccount->url|escape:'html':'UTF-8'}">
                                    {$infoAccount->url|escape:'html':'UTF-8'}
                                </a>
                            </span>
                        {else}
                            <span class='text-danger'>
                                {l s='Not specified' mod='paygreen'}
                            </span>
                        {/if}
                    </div>
                </div>


                <div class="panel ps_paygreen_step" id="ps_paygreen_step2">
                    <h2>
                        {l s='Step' mod='paygreen'} 2
                    </h2>

                    {assign var='checkActiveAccountOn' value=''}
                    {assign var='checkActiveAccountOff' value=''}
                    {assign var='checkOnDisable' value=''}

                    {if $infoAccount->valide==true}
                        {if $infoShop->activate==1}
                            {$checkActiveAccountOn='checked="checked"'}
                        {else}
                            {$checkActiveAccountOff='checked="checked"'}
                        {/if}
                    {else}
                        {$checkOnDisable=disabled}
                    {/if}

                    {if $allowRefund==1}
                        {$checkAllowRefundOn='checked="checked"'}
                    {else}
                        {$checkAllowRefundOff='checked="checked"'}
                    {/if}

                    <form class="" action="#" method="post" enctype="multipart/form-data">
                        <div class="form-group" data-toggle="tooltip" data-placement="top">
                            <label>
                                {l s='Activate my account' mod='paygreen'}
                            </label>
                            <span class="switch prestashop-switch fixed-width-lg">
                                <input name="PS_PG_activate_account" id="PS_PG_activate_account_on" {$checkActiveAccountOn|escape:'html':'UTF-8'} {$checkOnDisable|escape:'html':'UTF-8'} value="1" type="radio" />

                                <label for="PS_PG_activate_account_on" class="radioCheck">
                                    {l s='Yes' mod='paygreen'}
                                </label>

                                <input name="PS_PG_activate_account" id="PS_PG_activate_account_off" {$checkActiveAccountOff|escape:'html':'UTF-8'} {$checkOnDisable|escape:'html':'UTF-8'} value="0" type="radio" />

                                <label for="PS_PG_activate_account_off" class="radioCheck">
                                    {l s='No' mod='paygreen'}
                                </label>

                                <a class="slide-button btn"></a>
                            </span>
                        </div>

                        <p class="help-block">
                            {l s='Activate account for payment (available only if SIRET, IBAN and URL are informed).' mod='paygreen'}
                            <span style="font-weight: bold;">
                                {l s='By selecting "YES", you submit to the PayGreen\'s pricing policy which you can find through our website' mod='paygreen'} (<a href="https://paygreen.io/" target="_blank">{l s='click here' mod='paygreen'}</a>).
                            </span>
                        </p>

                        <button type="submit" value="1" id="module_form_submit_btn_account" name="submitPaygreenModuleAccount" class="btn btn-success button pull-right">
                            {l s='Save' mod='paygreen'}
                        </button>
                    </form>
                </div>
            </div>
        {/if}
    </div>
</div>