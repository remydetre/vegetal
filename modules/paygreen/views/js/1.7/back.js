/**
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
*/

$(document).ready(function() {
    var selectList = document.querySelectorAll('[name=executedAt]');
    for (var k = 0;k < selectList.length; k++) {
        checkExecutedAt(selectList[k]);
        selectList[k].onchange = function() {
            checkExecutedAt(
                this
            );
        }
    }

    $('.ps_paygreen_inputimage input.input-checkbox').each(function() {
        disable($(this), $(this).parent().parent().next('input[type="file"]'));

        $(this).click(function() {
            disable($(this), $(this).parent().parent().next('input[type="file"]'));
        });
    });
});

const CASH = 0;
const SUB = 1;
const REC = 3;
const DELIVERY = -1;

function checkExecutedAt(select) {
    var temp = document.querySelectorAll('[name=executedAt]');

    var labelPaymentDue = $(".labelnbPayment");
    var paymentDue = document.querySelectorAll('[name=nbPayment]');

    var labelPercent = $(".labelPercent");

    var labelSubOption = $(".labelSubOption");
    var checkbox = document.querySelectorAll('[name=subOption]');

    var labelReport = $(".labelReport");
    var reportPayment = document.querySelectorAll('[name=reportPayment]');

    var n;
    for (var i = 0; i < temp.length; ++i) {
        if (temp[i] == select) {
            n = i;
        }
    }
    if (select.value == 1) {
        displayAllPayment(labelPaymentDue[n], labelReport[n], "");
        displayPerCentPayment(labelPercent[n], "none");
        displaySubOption(labelSubOption[n], "");
    } else if (select.value == 3) {
        displayPaymentReport(labelReport[n], "none");
        displayPaymentDue(labelPaymentDue[n], "");
        displayPerCentPayment(labelPercent[n], "");
        displaySubOption(labelSubOption[n], "none");
        reportPayment[n].value = 0;
        checkbox[n].checked = false;
    } else {
        displayAllPayment(labelPaymentDue[n], labelReport[n], "none");
        displayPerCentPayment(labelPercent[n], "none");
        displaySubOption(labelSubOption[n], "none");
        paymentDue[n].value = 1;
        reportPayment[n].value = 0;
        checkbox.checked = false;
    }
}

function displaySubOption(labelSubOption, mode) {
    labelSubOption.parentNode.style.display = mode;
}

function displayPerCentPayment(labelPercent, mode) {
    labelPercent.parentNode.style.display = mode;
}

function displayPaymentDue(labelPaymentDue, mode) {
    labelPaymentDue.parentNode.style.display = mode;
}

function displayPaymentReport(labelReport, mode) {
    labelReport.parentNode.style.display = mode;
}

function displayAllPayment(labelPaymentDue, labelReport, mode) {
    displayPaymentDue(labelPaymentDue, mode);
    displayPaymentReport(labelReport, mode);
}


function disable(input, disabled) {
    if(input.is(':checked')) {
        disabled.prop('disabled', true);
    }

    else if(input.not(':checked')) {
        disabled.prop('disabled', false);
    }
}