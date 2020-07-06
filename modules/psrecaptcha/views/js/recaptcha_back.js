/**
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
*/

// Save : ReCaptcha configuration

$(document).ready(function() {

    let getValueOfFirstInput = $(".my_ip .one_ip").children(".row").find("input[name=recaptcha_ipaddressname]").eq(1).val();

    if (getValueOfFirstInput != "") {
        if ($(".my_ip .one_ip").children(".row").find("input[name=recaptcha_ipaddressname]").eq(0).val() == "") {
            $(".one_ip .remove_circle").hide();
        }
        let getAllInput = $(".my_ip .one_ip").parent().parent().find("input[name^=recaptcha_ipaddressname]").toArray()
        for (var i = 0; i < getAllInput.length; i++) {
            if ($(getAllInput[i]).val() == "") {
                $(".one_ip .row .add_circle:last").show();  
            } else {
                $(".one_ip .row .add_circle").hide();
            }
        }
    } else {
        $(".one_ip .add_circle:first").hide();
    }

    $('button[name=submitRecapConfig]').on('click', function(e){
        e.preventDefault();
        form_values = $('#recaptcha_form').serializeArray();
        $.ajax({
            type: 'POST',
            url: recaptcha_controller,
            data: {
                controller: 'AdminPsRecaptcha',
                action: 'SaveConfig',
                ajax: true,
                form_values: form_values
            },
            success: function (feedback) {
                if (feedback == 'true')
                    Swal(psrecap_SA_sucess_title, psrecap_SA_sucess_message, 'success');
            }
        });
    });

    // Save : ReCaptcha whitelist
    $('button[name=submitRecapWhitelist]').on('click', function (e) {
        e.preventDefault();
        form_whitelist_values = $('#recaptcha_whitelist_form').serializeArray();
        $.ajax({
            type: 'POST',
            url: recaptcha_controller,
            data: {
                controller: 'AdminPsRecaptcha',
                action: 'SaveWhitelist',
                ajax: true,
                form_whitelist_values: form_whitelist_values
            },
            success: function (feedback) {
                    if (feedback == 'true'){
                        Swal(psrecap_SA_sucess_title, psrecap_SA_sucess_message, 'success');
                        showSuccessMessage(psrecap_PS_succes_added);
                    }
                    else{
                        showErrorMessage(psrecap_PS_error_added);
                    }
                }
        });
    });
    
    // Add : New IP Address' row
    $(document).on('click', '.add_circle', function (e)  {
        $(".remove_circle:first").show();
        
        let newEl = $(this).parent().parent().clone().find("input:text").val("").end();
        let getValueFromChildrenInput = $(".my_ip .one_ip").children(".row").find("input[name=recaptcha_ipaddressname]").val();
        $(newEl).hide();
        $('.my_ip').append(newEl);
        
        // General
        $(".row").css("margin", "inherit");
        $('.add_circle').hide();

        if (getValueFromChildrenInput != "" && getValueFromChildrenInput != undefined ) {
            $('.add_circle:last').show();
        }
        
        // last element
        $(".row .col-lg-1").css("text-align","right");
        $('.add_circle:last').show()
        $('.remove_circle:last').show();
        
        $(newEl).slideDown('fast');

    });
    // Remove : IP Address' row
    $(document).on('click', '.remove_circle', function (e) {

        // SA : Modal
        Swal({
            title: 'Warning',
            text: "Are you sure you want to delete that ip address?",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Confirm'
        }).then((result) => {
            if (result.value){
                // Remove row in Back-Office
                let newEl = $(this).parent().parent();
                $(newEl).slideUp('Fast');
                $(newEl).remove();
                
                // Remove row in DataBase
                let currentIP = $(this).parent().parent().find("input:text").eq(1).val()
                let getValueFromLastInput = $(this).parent().parent().find(" input[name^=recaptcha_ipaddressname]").last().val();
                let getLenghtOfParentDiv = $(".my_ip .one_ip").parent().parent().find("input[name^=recaptcha_ipaddressname]").length
                removeUserFromWhiteList(currentIP);
                showSuccessMessage(psrecap_PS_succes_deleted);

                if (getValueFromLastInput != "" && getValueFromLastInput != undefined) {
                        if (getLenghtOfParentDiv == 1) {
                            $(".remove_circle:first").hide();
                        }
                    $('.add_circle:last').show()
                } else {
                    if ($(".my_ip .one_ip").children(".row").length <= 0) {
                       if ($(".my_ip > .one_ip ~ .row .remove_circle").length <= 1) {
                           $(".my_ip > .one_ip ~ .row .remove_circle").eq(0).hide();
                           $(".my_ip > .one_ip ~ .row .add_circle").eq(0).show();
                        } else {
                            $('.add_circle:last').show()
                        }
                    } else if ($(".my_ip > .one_ip ~ .row")[0]) {
                        $('.add_circle:last').show()
                        $(".remove_circle:first").show()
                    }
                    else {
                        if (getLenghtOfParentDiv == 1) {
                            $(".remove_circle:first").hide();
                            $('.add_circle:last').show()
                        } 
                    }
                    $('.add_circle:last').show()
                }

            }
        });
    });
    
    function updateWhiteList(){
        form_whitelist_values = $('#recaptcha_whitelist_form').serializeArray();
        $.ajax({
            type: 'POST',
            url: recaptcha_controller,
            data: {
                controller: 'AdminPsRecaptcha',
                action: 'SaveWhitelist',
                ajax: true,
                form_whitelist_values: form_whitelist_values
            },
        });
    }

    function removeUserFromWhiteList(currentIP){
        form_whitelist_values = currentIP;
        $.ajax({
            type: 'POST',
            url: recaptcha_controller,
            data: {
                controller: 'AdminPsRecaptcha',
                action: 'DeleteUserFromWhitelist',
                ajax: true,
                form_whitelist_values: form_whitelist_values
            },
        });
    }

    // Hide reCaptcha configuration if reCaptcha is disable in BO
    $(document).on('change', 'input[name="RECAPTCHA_ACTIVE"]', (e) => {
        if ($(e.target).val() == 0) {
            $('#displayRecaptchaConfiguration').slideUp('slow');
        } else {
            $('#displayRecaptchaConfiguration').slideDown('slow');
        }
   })

   $(document).ready(function() {

       if ($('input[name="RECAPTCHA_ACTIVE"]:checked').val() == 0) {
           $("#displayLoginFailConfiguration").addClass("checked");
       } else {
           $("#displayLoginFailConfiguration").show()
       }

   })
    // Hide reCaptcha login fail configuration if Login form is disable in BO
    $(document).on('change', 'input[name="RECAPTCHA_LOGINFORM"]', (e) => {
        if ($(e.target).val() == 0) {
            $('#displayLoginFailConfiguration').slideUp('fast');
        } else {
            $('#displayLoginFailConfiguration').slideDown('fast');
        }
    })

    $(document).on('change', 'input[name="RECAPTCHA_TYPE"]', (e) => {
        if ($(e.target).val() == 0) {
            $('#displayLoginFailConfiguration').slideUp('fast');
            $("#displayLoginFailConfiguration > .form-group").slideUp('fast')
            $("#customRecaptcha").slideUp('fast');
        } else {
            $('#displayLoginFailConfiguration').slideDown('fast');
            $("#displayLoginFailConfiguration > .form-group").slideDown('fast')
            $("#customRecaptcha").slideDown('fast');
        }
    })
});
