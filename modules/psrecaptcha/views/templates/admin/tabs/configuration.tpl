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

<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

<!-- ACCOUNT SETTINGS -->
<form id="recaptcha_form">
    <div class="panel col-lg-10 left-panel form-horizontal">       
        <h3>
            <i class="material-icons materialIcons-fontSize">link</i> {l s='Account Settings' mod='psrecaptcha'}
        </h3>

        <div class="form-group row">
    
            <!-- Site key - Textfield -->
            <div class="form-group">
                <div class="col-xs-12 col-sm-12 col-md-5 col-lg-2">
                    <div class="text-right">
                        <label for="buylater_button_title_text" class="control-label">
                                {l s='Site key' mod='psrecaptcha'}
                        </label>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-7 col-lg-6">
                    <input class="" type="text" value="{if $recaptcha_config.RECAPTCHA_SITEKEY != ""}{$recaptcha_config.RECAPTCHA_SITEKEY}{/if}" name="RECAPTCHA_SITEKEY">
                </div>
            </div>

            <!-- Secret key - Textfield -->
            <div class="form-group">
                <div class="col-xs-12 col-sm-12 col-md-5 col-lg-2">
                    <div class="text-right">
                        <label for="buylater_button_title_text" class="control-label">
                            {l s='Secret key' mod='psrecaptcha'}
                        </label>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-7 col-lg-6">
                    <input class="" type="text" value="{if $recaptcha_config.RECAPTCHA_SECRETKEY != ""}{$recaptcha_config.RECAPTCHA_SECRETKEY}{/if}" name="RECAPTCHA_SECRETKEY">
                    <div class="help-block">
                        {l s='Please ' mod='psrecaptcha'}
                        <a href="https://www.google.com/recaptcha/admin#list" target="_blank">{l s='click here ' mod='psrecaptcha'}</a>
                        {l s='to know how to obtain these Google reCaptcha keys.' mod='psrecaptcha'}
                    </div>
                </div>

            </div>

            <!-- ReCaptcha type - Radio buttons -->
            <div class="form-group">
                <!-- Alert Message - Warning -->
                <div class="col-xs-12 col-sm-12 col-md-7 col-lg-offset-2 col-lg-6 alert alert-warning" role="alert">
                    <i class="material-icons"></i> {l s='Please select the reCaptcha V2 you have choosen on Google platform before:' mod='psrecaptcha'}
                </div>         
                <div style="margin-left: 40px">
                    <div class="col-xs-12 col-sm-12 col-md-7 col-lg-offset-2 col-lg-10 form-check form-check-inline">
                        <label>
                            <input type="radio" class="input_img js-show-all" name="RECAPTCHA_TYPE" value="1" {if $recaptcha_config.RECAPTCHA_TYPE == 1}checked{/if}/>
                            <img src="{$img_path}/content/reCaptchaV2Checkbox.png" width="300" height="80" alt="checkboxV2">
                            <div class="help-block center-text">
                                <p>{l s='\"I\'m not a robot\" Checkbox' mod='psrecaptcha'}</p>
                            </div>
                        </label>
                        <label>
                            <input type="radio" class="input_img" name="RECAPTCHA_TYPE" value="0" {if $recaptcha_config.RECAPTCHA_TYPE == 0}checked{/if}/>
                            <img src="{$img_path}/content/reCaptchaV2Invisible.png" width="300" height="80" alt="invisibleV2">
                            <div class="help-block center-text">
                                <p>{l s='Invisible reCAPTCHA badge' mod='psrecaptcha'}</p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel footer -->
        <div class="panel-footer">
            <button type="submit" value="1" name="submitRecapConfig" class="btn btn-default pull-right"><i class="process-icon-save"></i>{l s='Save' mod='psrecaptcha'}</button>
        </div>
    </div>

    <!-- RECAPTCHA CONFIGURATION -->
    <div class="panel col-lg-10 col-lg-offset-2 form-horizontal">
            
    <!-- Enable recaptcha - Switch-->
        <h3>
            <i class="material-icons materialIcons-fontSize">settings</i>
            {l s='Recaptcha Configuration' mod='psrecaptcha'}
        </h3>
        <div class="form-group row">
            <div class="form-group">
                <div class="control-label col-lg-2 col-md-4 col-xs-10">
                    <label class="labelbutton fontweight-std">{l s='Enable reCaptcha' mod='psrecaptcha'}</label>
                </div>
                <div>
                    <div class="input-group fixed-width-lg">
                        <span class="switch prestashop-switch fixed-width-lg">
                            <input class="yes" type="radio" name="RECAPTCHA_ACTIVE" id="recaptcha_enable_on" value="1" {if $recaptcha_config.RECAPTCHA_ACTIVE eq 1}checked="checked"{/if}>
                            <label for="recaptcha_enable_on" class="radioCheck">{l s='Yes' mod='psrecaptcha'}</label>
                            <input class="no" type="radio" name="RECAPTCHA_ACTIVE" id="recaptcha_enable_off" value="0" {if $recaptcha_config.RECAPTCHA_ACTIVE eq 0}checked="checked"{/if}>
                            <label for="recaptcha_enable_off" class="radioCheck">{l s='No' mod='psrecaptcha'}</label>
                            <a class="slide-button btn"></a>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div id="displayRecaptchaConfiguration"
        style="{if $recaptcha_config.RECAPTCHA_ACTIVE eq 0} display:none; {/if}">

            <!-- Activation - Sentence -->
            <div class="form-group row fontweight-bold col-xs-12 col-sm-12 col-md-12 col-lg-12" id="sentencesMarginLeft">
                {l s='Please activate below the forms in wich you want to insert reCaptcha' mod='psrecaptcha'}
            </div>            

            <div class="form-group row">
                <!-- Registration form - Switch -->
                <div class="form-group">
                    <div class="control-label col-lg-2 col-md-4 col-xs-10">
                        <label class="labelbutton fontweight-std">{l s='Registration form' mod='psrecaptcha'}</label>
                    </div>
                    <div>
                        <div class="input-group fixed-width-lg">
                            <span class="switch prestashop-switch fixed-width-lg">
                            <input class="yes" type="radio" name="RECAPTCHA_REGISTRATIONFORM" id="recaptcha_registration_form_on" value="1" {if $recaptcha_config.RECAPTCHA_REGISTRATIONFORM eq 1}checked="checked"{/if}>
                            <label for="recaptcha_registration_form_on" class="radioCheck">{l s='Yes' mod='psrecaptcha'}</label>
                            <input class="no" type="radio" name="RECAPTCHA_REGISTRATIONFORM" id="recaptcha_registration_form_off" value="0" {if $recaptcha_config.RECAPTCHA_REGISTRATIONFORM eq 0}checked="checked"{/if}>
                            <label for="recaptcha_registration_form_off" class="radioCheck">{l s='No' mod='psrecaptcha'}</label>
                            <a class="slide-button btn"></a>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Contact form - Switch -->
                <div class="form-group">
                    <div class="control-label col-lg-2 col-md-4 col-xs-10">
                        <label class="labelbutton fontweight-std">{l s='Contact form' mod='psrecaptcha'}</label>
                    </div>
                    <div>
                        <div class="input-group fixed-width-lg">
                            <span class="switch prestashop-switch fixed-width-lg">
                            <input class="yes" type="radio" name="RECAPTCHA_CONTACTFORM" id="recaptcha_contact_form_on" value="1" {if $recaptcha_config.RECAPTCHA_CONTACTFORM eq 1}checked="checked"{/if}>
                            <label for="recaptcha_contact_form_on" class="radioCheck">{l s='Yes' mod='psrecaptcha'}</label>
                            <input class="no" type="radio" name="RECAPTCHA_CONTACTFORM" id="recaptcha_contact_form_off" value="0" {if $recaptcha_config.RECAPTCHA_CONTACTFORM eq 0}checked="checked"{/if}>
                            <label for="recaptcha_contact_form_off" class="radioCheck">{l s='No' mod='psrecaptcha'}</label>
                            <a class="slide-button btn"></a>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Login form - Switch -->
                <div class="form-group">
                    <div class="control-label col-lg-2 col-md-4 col-xs-10">
                        <label class="labelbutton fontweight-std">{l s='Login form' mod='psrecaptcha'}</label>
                    </div>
                    <div>
                        <div class="input-group fixed-width-lg">
                            <span class="switch prestashop-switch fixed-width-lg">
                            <input class="yes" type="radio" name="RECAPTCHA_LOGINFORM" id="recaptcha_login_form_on" value="1" {if $recaptcha_config.RECAPTCHA_LOGINFORM eq 1}checked="checked"{/if}>
                            <label for="recaptcha_login_form_on" class="radioCheck">{l s='Yes' mod='psrecaptcha'}</label>
                            <input class="no" type="radio" name="RECAPTCHA_LOGINFORM" id="recaptcha_login_form_off" value="0" {if $recaptcha_config.RECAPTCHA_LOGINFORM eq 0}checked="checked"{/if}>
                            <label for="recaptcha_login_form_off" class="radioCheck">{l s='No' mod='psrecaptcha'}</label>
                            <a class="slide-button btn"></a>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Number of fail - Dropdown list -->
                <div id="displayLoginFailConfiguration">
                    <div class="form-group" style="{if $recaptcha_config.RECAPTCHA_TYPE eq 0} display:none; {/if}">
                        <div class="control-label col-lg-2 col-md-4 col-xs-10">
                            <label class="labelbutton fontweight-std">{l s='Number of login fail before reCaptcha display' mod='psrecaptcha'}</label>
                        </div>
                        <div>
                            <div class="input-group fixed-width-lg">
                                <select class="c-select" name="RECAPTCHA_NUMBERLOGINFAIL">
                                    <option value="0" {if $recaptcha_config.RECAPTCHA_NUMBERLOGINFAIL == 0}selected{/if}>0</option>
                                    <option value="1" {if $recaptcha_config.RECAPTCHA_NUMBERLOGINFAIL == 1}selected{/if}>1</option>
                                    <option value="2" {if $recaptcha_config.RECAPTCHA_NUMBERLOGINFAIL == 2}selected{/if}>2</option>
                                    <option value="3" {if $recaptcha_config.RECAPTCHA_NUMBERLOGINFAIL == 3}selected{/if}>3</option>
                                    <option value="4" {if $recaptcha_config.RECAPTCHA_NUMBERLOGINFAIL == 4}selected{/if}>4</option>
                                    <option value="5" {if $recaptcha_config.RECAPTCHA_NUMBERLOGINFAIL == 5}selected{/if}>5</option>
                                    <option value="6" {if $recaptcha_config.RECAPTCHA_NUMBERLOGINFAIL == 6}selected{/if}>6</option>
                                    <option value="7" {if $recaptcha_config.RECAPTCHA_NUMBERLOGINFAIL == 7}selected{/if}>7</option>
                                    <option value="8" {if $recaptcha_config.RECAPTCHA_NUMBERLOGINFAIL == 8}selected{/if}>8</option>
                                    <option value="9" {if $recaptcha_config.RECAPTCHA_NUMBERLOGINFAIL == 9}selected{/if}>9</option>
                                    <option value="10"> {if $recaptcha_config.RECAPTCHA_NUMBERLOGINFAIL == 10}selected{/if}10</option>
                                </select>
                            </div>
                            <!-- Alert Message - Warning -->
                            <div class="col-xs-12 col-sm-12 col-md-7 col-lg-offset-2 col-lg-4 alert alert-warning" role="alert">
                                <i class="material-icons"></i> {l s='For instant display of the reCaptcha, please select "0"' mod='psrecaptcha'}
                            </div>
                        </div>
                    </div>
                </div>
            
        <!-- Activation sentence - Radio buttons -->
        <div id="customRecaptcha" style="{if $recaptcha_config.RECAPTCHA_TYPE eq 0} display:none; {/if}">
        
            <div class="form-group row">        
                <div class="title fontweight-bold col-xs-12 col-sm-12 col-md-12 col-lg-12" id="sentencesMargin2Left">
                    {l s='Customize the design of the reCaptcha' mod='psrecaptcha'}
                </div>
            </div>

            <!-- ReCaptcha theme - Radio buttons -->
            <div class="form-group">
                <div class="noPad control-label col-lg-2 col-md-4 col-xs-10">
                    <label class="labelbutton fontweight-std">{l s='reCaptcha theme' mod='psrecaptcha'}</label>
                </div>
                <div>
                    <div class="form-check form-check-inline col-lg-2">
                        <input class="form-check-input" type="radio" name="RECAPTCHA_THEME" id="light" value="1" {if $recaptcha_config.RECAPTCHA_THEME == 1}checked{/if}/>
                        <label class="form-check-label" for="light">{l s='Light' mod='psrecaptcha'}</label>
                    </div>
                    <div class="form-check form-check-inline col-lg-1">
                        <input class="form-check-input" type="radio" name="RECAPTCHA_THEME" id="dark" value="0" {if $recaptcha_config.RECAPTCHA_THEME == 0}checked{/if}/>
                        <label class="form-check-label" for="dark">{l s='Dark' mod='psrecaptcha'}</label>
                    </div>
                </div>
            </div>

            <!-- ReCaptcha size - Radio buttons -->
            <div class="form-group form-check-inline">
                <div class="noPad control-label col-lg-2 col-md-4 col-xs-10">
                    <label class="labelbutton fontweight-std">{l s='reCaptcha size' mod='psrecaptcha'}</label>
                </div>
                <div>
                    <div class="form-check form-check-inline col-lg-2">
                        <input class="form-check-input" type="radio" name="RECAPTCHA_SIZE" id="normal" value="1" {if $recaptcha_config.RECAPTCHA_SIZE == 1}checked{/if}/>
                        <label class="form-check-label" for="normal">{l s='Normal' mod='psrecaptcha'}</label>
                    </div>
                    <div class="form-check form-check-inline col-lg-1">
                        <input class="form-check-input" type="radio" name="RECAPTCHA_SIZE" id="compact" value="0" {if $recaptcha_config.RECAPTCHA_SIZE == 0}checked{/if}/>
                        <label class="form-check-label" for="compact">{l s='Compact' mod='psrecaptcha'}</label>
                    </div>
                </div>
            </div>

            <!-- ReCaptcha language - Radio buttons -->
            <div class="form-group">
                <div class="noPad control-label col-lg-2 col-md-4 col-xs-10">
                    <label class="labelbutton fontweight-std">{l s='reCaptcha language' mod='psrecaptcha'}</label>
                </div>
                <div>
                    <div class="form-check form-check-inline col-lg-2">
                        <input class="form-check-input" type="radio" name="RECAPTCHA_LANGUAGE" id="shopLanguage" value="1" {if $recaptcha_config.RECAPTCHA_LANGUAGE == 1}checked{/if}/>
                        <label class="form-check-label" for="shopLanguage">{l s='Shop Language' mod='psrecaptcha'}</label>
                    </div>
                    <div class="form-check form-check-inline col-lg-2">
                        <input class="form-check-input" type="radio" name="RECAPTCHA_LANGUAGE" id="browserLanguage" value="0" {if $recaptcha_config.RECAPTCHA_LANGUAGE == 0}checked{/if}/>
                        <label class="form-check-label" for="browserLanguage">{l s='Browser Language' mod='psrecaptcha'}</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- Panel footer -->
    <div class="panel-footer">
        <button type="submit" value="1" name="submitRecapConfig" class="btn btn-default pull-right"><i class="process-icon-save"></i>{l s='Save' mod='psrecaptcha'}</button>
    </div>
</div>
</form>
