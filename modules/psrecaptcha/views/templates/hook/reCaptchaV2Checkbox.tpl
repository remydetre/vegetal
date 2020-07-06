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

<script src="https://www.google.com/recaptcha/api.js?hl={$RECAPTCHA_LANG}" async defer></script>

<div class="form-group row g-recaptcha-row" style="display:none">
    <label class="col-md-3 form-control-label">
    </label>
    <div class="col-md-6">
        <div class="g-recaptcha"
            data-callback="correctCaptcha"
            style="text-align:center"
            data-sitekey="{$RECAPTCHA_SITEKEY}"
            data-theme="{$RECAPTCHA_THEME}"
            data-size="{$RECAPTCHA_SIZE}">
        </div>
        <div id="infoRecaptcha" style="padding: 10px;font-style: italic;width: 315px;"></div>
    </div>
</div>

{literal}
<script>
var recap_ps_version = "{/literal}{$recap_ps_version}{literal}";
var recap_create_account =  "{/literal}{$create_account_recaptcha}{literal}";
</script>
{/literal}

<script>

function getButtonInForm() {
    parentForm = $('.g-recaptcha').closest('form');
    let element = $('.g-recaptcha');
    let iLoopLimit = 0;

    while(0 === element.nextAll('[type="submit"]').length &&
        element.get(0) !== parentForm.get(0) &&
        element.length &&
        iLoopLimit != 1000) {
            element = element.parent();
            iLoopLimit++;
        }

    return (element.find('[type="submit"]'));
}


document.addEventListener("DOMContentLoaded",function() {
    if (recap_ps_version == "1") {

        $('.js-customer-form > section').append($('.g-recaptcha-row').show());

        var display = $(".g-recaptcha").data("size");
        
        if (display == "compact" && $("body").find('#login-form > section').length == "1") {
            $('#login-form > section').append($('.g-recaptcha-row').show().css({
                "width" : "100%",
                "padding-left" : "100px",
                })
            );
        } else {
            $('#login-form > section').append($('.g-recaptcha-row').show());
        }

        $('.form-fields').append($('.g-recaptcha-row').show());

        let button = getButtonInForm();
        let recaptchaResponse = "";

        $(button).on('click', function(e) {
            if (recaptchaResponse.length <= 0 && recaptchaResponse == '') {
                e.preventDefault()
                $("#infoRecaptcha").html("Please make sure you complete reCaptcha above.");
            }
        });

        window.correctCaptcha = function(response) {
            recaptchaResponse = response
        };

    } else {

        if (recap_create_account == true) {
            $('#account-creation_form .account_creation').append($('.g-recaptcha-row').show())
        }

        // create account - 1.6
        $('#SubmitCreate').on('click', function(){
            var temp = $('.g-recaptcha-row');
            setTimeout(function(){
                $('body').find('#account-creation_form .account_creation').append(temp);
                let childreanRecaptcha = $(".g-recaptcha-row").children().eq(1)
                childreanRecaptcha.css({
                    "width" : "100%",
                    "padding-left" : "5px",
                });
                $("#submitAccount").on('click', function(e) {
                    if (recaptchaResponse.length <= 0 && recaptchaResponse == '') {
                    e.preventDefault()
                        $("#infoRecaptcha").html("Please make sure you complete reCaptcha above.");
                    }
                });
            }, 2000);
        })

        // login 1.6
        $(".g-recaptcha-row").insertBefore('#login_form .submit').show();
        let childreanRecaptcha = $(".g-recaptcha-row").children().eq(1)
        childreanRecaptcha.css({
            "width" : "100%",
            "padding-left" : "0px",
        });

        // contact form 1.6
        $(".g-recaptcha-row").insertBefore('.contact-form-box .submit').show();
        if ($(".contact-form-box").length > 0) {
            let childreanRecaptcha = $(".g-recaptcha-row").children().eq(1)
            childreanRecaptcha.css({
                "width" : "100%",
                "padding-left" : "15px",
            });
        }
        
        let button = getButtonInForm();
        let recaptchaResponse = "";
        $(button).on('click', function(e) {
            if (recaptchaResponse.length <= 0 && recaptchaResponse == '') {
                e.preventDefault()
                $("#infoRecaptcha").html("Please make sure you complete reCaptcha above.");
            }
        });

        window.correctCaptcha = function(response) {
            recaptchaResponse = response
        };
    }
});
</script>

