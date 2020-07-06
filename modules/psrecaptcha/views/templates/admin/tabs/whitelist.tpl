{*
* 2007-2019 PrestaShop
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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2019 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<!-- WHITELIST -->
<form id="recaptcha_whitelist_form">
    <div class="panel col-lg-10 left-panel form-horizontal">

            <h3>
                <i class="material-icons materialIcons-fontSize">event_note</i> {l s='Whitelist' mod='psrecaptcha'}
            </h3>

            <!-- IP Address - Sentence -->
            <div class="form-group ">
                <div class="control-label whitelistform col-lg-12 col-md-4 col-xs-10">
                    <label class="labelbutton">{l s='Please enter below the IP Addresses for which reCaptcha will be disabled and the associated names to theses addresses:' mod='psrecaptcha'}</label>
                </div>
            </div>

            <!-- IP Address - Textfield -->
            <div class="form-group row">
                <div class="form-group">
                    <div class="col-lg-1 col-md-7 col-sm-12 col-xs-12">
                        <div>
                            <label for="buylater_button_title_text" class="control-label pad-left">
                                {l s='IP Addresses:' mod='psrecaptcha'}
                            </label>
                        </div>
                    </div>
                    <div class="my_ip col-lg-11">
                        <div class="one_ip row">
                            {if $recaptcha_whitelist}
                                {foreach from=$recaptcha_whitelist key=k item=v}
                                    <div class="row">
                                        <div class="col-lg-1 col-md-7 col-sm-12 col-xs-12">
                                            <label class="control-label add_circle"><i class="material-icons hover add-button" for="add_circle">add_circle</i></label>
                                            <label class="control-label remove_circle"><i class="material-icons hover remove-button" for="remove_circle">remove_circle</i></label>
                                        </div>
                                        <div class="col-lg-2 col-md-7 col-sm-12 col-xs-12">
                                            <input type="text" name="recaptcha_ipaddressname" placeholder="{l s='Name' mod='psrecaptcha'}" value="{if !empty($k)}{$k}{/if}">
                                        </div>
                                        <div class="col-lg-2 col-md-7 col-sm-12 col-xs-12">
                                            <input type="text" name="recaptcha_ipaddress" placeholder="000.000.000.000" value="{if !empty($v)}{$v}{/if}">
                                        </div>
                                    </div>
                                {/foreach}
                            {/if}
                            <div class="row" >
                                <div class="col-lg-1 col-md-7 col-sm-12 col-xs-12">
                                    <label class="control-label add_circle"><i class="material-icons hover add-button" for="add_circle">add_circle</i></label>
                                    <label class="control-label remove_circle"><i class="material-icons hover remove-button" for="remove_circle">remove_circle</i></label>
                                </div>
                                <div class="col-lg-2 col-md-7 col-sm-12 col-xs-12">
                                    <input type="text" name="recaptcha_ipaddressname" placeholder="{l s='Name' mod='psrecaptcha'}" value="{if $recaptcha_config.RECAPTCHA_IPADDRESSNAME != ""}{/if}">
                                </div>
                                <div class="col-lg-2 col-md-7 col-sm-12 col-xs-12">
                                    <input type="text" name="recaptcha_ipaddress" placeholder="000.000.000.000" value="{if $recaptcha_config.RECAPTCHA_IPADDRESS != ""}{/if}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Panel footer -->
            <div class="panel-footer">
                <button type="submit" value="1" id="module_form_submit_btn" name="submitRecapWhitelist" class="btn btn-default pull-right"><i class="process-icon-save"></i>{l s='Save' mod='psrecaptcha'}</button>
            </div>
    </div>
</form>
