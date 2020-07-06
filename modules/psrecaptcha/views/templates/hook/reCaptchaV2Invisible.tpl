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

<div>
    <script src='https://www.google.com/recaptcha/api.js?hl={$RECAPTCHA_LANG}' async defer></script>
    <button
        style="display: none"
        class="g-recaptcha"
        data-sitekey="{$RECAPTCHA_SITEKEY}"
        data-callback="correctCaptcha">
        SUBMIT
    </button>
</div>

<script>
var recap_ps_version = "{$recap_ps_version}";
if (recap_ps_version == "1") {
    // create account 1.7
    document.addEventListener("DOMContentLoaded",function() {
        $('#customer-form > footer.form-footer').before($('.g-recaptcha'));
        $(".form-control-submit").on('click', function(e){
            e.preventDefault();
            $(".g-recaptcha").click();
            recaptchaResponse = "";
            window.correctCaptcha = function(response) {
                recaptchaResponse = response;
                if (recaptchaResponse.length > 0 && recaptchaResponse != ''){
                    $(".form-control-submit").off('click').trigger('click');
                }
            };
        });

    // login 1.7
        $('#login-form > footer.form-footer').before($('.g-recaptcha'));
        $("#submit-login").on('click', function(e){
            e.preventDefault();
            $(".g-recaptcha").click();
            recaptchaResponse = "";
            window.correctCaptcha = function(response) {
                recaptchaResponse = response;
                if (recaptchaResponse.length > 0 && recaptchaResponse != ''){
                    $("#submit-login").off('click').trigger('click');
                }
            };
        });

    // contact form 1.7 - WIP
        $('.form-footer').before($('.g-recaptcha'));
        $('input[name="submitMessage"]').on('click', function(e){
            e.preventDefault();
            $(".g-recaptcha").click();
            recaptchaResponse = "";
            window.correctCaptcha = function(response) {
                recaptchaResponse = response;
                if (recaptchaResponse.length > 0 && recaptchaResponse != ''){
                    $('input[name="submitMessage"]').off('click').trigger('click');
                };
            }
        });
    });
} else {
    // create account 1.6
    document.addEventListener("DOMContentLoaded",function() {
       $('#SubmitCreate').on('click', function(){
            var temp = $('.g-recaptcha');
            var tmp_badge = $('.grecaptcha-badge');
            setTimeout(function(){
                $('body').find('#account-creation_form .account_creation').append(temp);
                $('body').find('.g-recaptcha').before(tmp_badge);
                reloadRecaptcha();
            }, 4000);
        });

        reloadRecaptcha = function(){
            $("#submitAccount").on('click', function(e){
                e.preventDefault();
                $(".g-recaptcha").click();
                recaptchaResponse = "";
                window.correctCaptcha = function(response) {
                    recaptchaResponse = response;
                    if (recaptchaResponse.length > 0 && recaptchaResponse != ''){
                        $("#submitAccount").off('click').trigger('click');
                    }
                };
            });
        }

        $('#account-creation_form').append($('.g-recaptcha'));
        $("#submitAccount").on('click', function(e){
            e.preventDefault();
            $(".g-recaptcha").click();
            recaptchaResponse = "";
            window.correctCaptcha = function(response) {
                recaptchaResponse = response;
                if (recaptchaResponse.length > 0 && recaptchaResponse != ''){
                    $("#submitAccount").off('click').trigger('click');
                }
            };
        });

    // login 1.6
        $('#SubmitLogin').before($('.g-recaptcha'));
        $("#SubmitLogin").on('click', function(e){
            e.preventDefault();
            $(".g-recaptcha").click();
            recaptchaResponse = "";
            window.correctCaptcha = function(response) {
                recaptchaResponse = response;
                if (recaptchaResponse.length > 0 && recaptchaResponse != ''){
                    $("#SubmitLogin").off('click').trigger('click');
                }
            };
        });

    // contact form 1.6
        $('#submitMessage').before($('.g-recaptcha'));
        $("#submitMessage").on('click', function(e){
            e.preventDefault();
            $(".g-recaptcha").click();
            recaptchaResponse = "";
            window.correctCaptcha = function(response) {
                recaptchaResponse = response;
                if (recaptchaResponse.length > 0 && recaptchaResponse != ''){
                    $("#submitMessage").off('click').trigger('click');
                }
            };
        });
    });
}
</script>

