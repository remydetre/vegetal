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

<div class="panel col-lg-10 right-panel">

    <h3>
        <i class="fa fa-question-circle"></i> {l s='Help & contact' mod='psrecaptcha'} <small>{$module_display|escape:'htmlall':'UTF-8'}</small>
    </h3>
    
    <div class="helpContentParent">
        <div class="helpContentLeft">
            <div class="left">
                <img src="{$logo_path|escape:'htmlall':'UTF-8'}" alt=""/>
            </div>
            <div class="right">
                <p><span class="data_label" style="color:#00aff0;"><b>{l s='This module allows you to:' mod='psrecaptcha'}</b></span></p>
                <br>
                <div>
                    <div class="numberCircle">1</div>
                    <div class="numberCircleText">
                    <p class="numberCircleText">
                        {l s='Prevent your store from robot spams and abuses' mod='psrecaptcha'}
                    </p>
                    </div>
                </div>
                <div>
                    <div class="numberCircle">2</div>
                    <div class="numberCircleText">
                    <p class="numberCircleText">
                        {l s='Activate reCaptcha on the registration, contact and login form of your website' mod='psrecaptcha'}
                    </p>
                    </div>
                </div>
                <div>
                    <div class="numberCircle">3</div>
                    <div class="numberCircleText">
                    <p class="numberCircleText">
                        {l s='Decide the number of login fail before reCaptcha display on your forms' mod='psrecaptcha'}
                    </p>
                    </div>
                </div>
                <div>
                    <div class="numberCircle">4</div>
                    <div class="numberCircleText">
                    <p class="numberCircleText">
                        {l s='Personalize the design of the reCaptcha: its theme, size and language' mod='psrecaptcha'}
                    </p>
                    </div>
                </div>
                <div>
                    <div class="numberCircle">5</div>
                    <div class="numberCircleText">
                    <p class="numberCircleText">
                        {l s='Decide on the IP addresses in the whitelist that will be exempt from reCaptcha' mod='psrecaptcha'}
                    </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="helpContentRight">
            <div class="helpContentRight-sub">
                <b>{l s='Need help?' mod='psrecaptcha'}</b><br>
                {l s='Find here the documentation of this module' mod='psrecaptcha'}
                <a class="btn btn-primary" href="{$doc|escape:'htmlall':'UTF-8'}" target="_blank" style="margin-left:20px;" href="#">
                    <i class="fa fa-book"></i> {l s='Documentation' mod='psrecaptcha'}</a>
                </a>
                <br><br>
                <div class="tab-pane panel" id="faq">
                    <div class="panel-heading"><i class="icon-question"></i> {l s='FAQ' mod='psrecaptcha'}</div>
                    {foreach from=$apifaq item=categorie name='faq'}
                        <span class="faq-h1">{$categorie->title|escape:'htmlall':'UTF-8'}</span>
                        <ul>
                            {foreach from=$categorie->blocks item=QandA}
                                {if !empty($QandA->question)}
                                    <li>
                                        <span class="faq-h2"><i class="icon-info-circle"></i> {$QandA->question|escape:'htmlall':'UTF-8'}</span>
                                        <p class="faq-text hide">
                                            {$QandA->answer|escape:'htmlall':'UTF-8'|replace:"\n":"<br />"}
                                        </p>
                                    </li>
                                {/if}
                            {/foreach}
                        </ul>
                        {if !$smarty.foreach.faq.last}<hr/>{/if}
                    {/foreach}
                </div>
                {l s='You couldn\'t find any answer to your question ?' mod='psrecaptcha'}
                <b><a href="http://addons.prestashop.com/contact-form.php" target="_blank">{l s='Contact us on PrestaShop Addons' mod='psrecaptcha'}</a></b>
            </div>
        </div>
    </div>
</div>