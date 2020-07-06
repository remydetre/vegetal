/**
 * 2007-2018 PrestaShop
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
 * @author    DPD France S.A.S. <support.ecommerce@dpd.fr>
 * @copyright 2018 DPD France S.A.S.
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

$(document).ready(function()
{
    $("input[name*='delivery_option[']").change(function() {
        dpdfrance_display();
    });
    dpdfrance_display();
});

/* Call AJAX to push Pudo selection */
function dpdfrance_registerPudo(pudo_id)
{
    if (pudo_id) {
        $.ajax({
            type : 'POST',
            url : dpdfrance_base_dir+'/ajax.php',
            data: {
                'action_ajax_dpdfrance' : 'ajaxRegisterPudo',
                'dpdfrance_cart_id'     : dpdfrance_cart_id,
                'pudo_id'               : pudo_id,
                'dpdfrance_token'       : dpdfrance_token,
            },
            dataType: 'json',
            error : function(er) {
                dpdfrance_allowOrder(false);
                alert('Votre relais Pickup n\'a pas été sauvegardé, merci d\'en sélectionner un autre.');
            }
        });
        dpdfrance_allowOrder(true);
    } else {
        dpdfrance_allowOrder(false);
        alert('Votre relais Pickup n\'a pas été sauvegardé, merci d\'en sélectionner un autre.');
    }
}

/* Check Pudo selection */
function dpdfrance_checkPudo()
{
    if ($("[name=dpdfrance_relay_id]:checked") && $(".dpdfrance_relais_error").length == 0) {
        dpdfrance_registerPudo($("[name=dpdfrance_relay_id]:checked").val());
        dpdfrance_allowOrder(true);
        return true;
    } else {
        dpdfrance_allowOrder(false);
        return false;
    }
}

/* Call AJAX to push GSM number */
function dpdfrance_registerGsm(phone)
{
    if (phone) {
        $.ajax({
            type : 'POST',
            url : dpdfrance_base_dir+'/ajax.php',
            data: {
                'action_ajax_dpdfrance' : 'ajaxRegisterGsm',
                'dpdfrance_cart_id'     : dpdfrance_cart_id,
                'gsm_dest'              : phone,
                'dpdfrance_token'       : dpdfrance_token,
            },
            dataType: 'json',
            error : function(er) {
                dpdfrance_allowOrder(false);
                alert('Votre numéro de téléphone n\'a pas été sauvegardé, merci de rééssayer.');
            }
        });
        dpdfrance_allowOrder(true);
    } else {
        dpdfrance_allowOrder(false);
        $('#input_dpdfrance_predict_gsm_dest').css('border', '2px solid red');
    }
}

/* In_array JS function implementation */
function dpdfrance_in_array(search, array)
{
    for (i = 0; i < array.length; i++) {
        if (array[i] == search)
            return true;
    }
    return false;
}

/* Check European GSM validity */
function dpdfrance_checkGSM()
{
    if (document.getElementById('input_dpdfrance_predict_gsm_dest')) {
        var gsmDest = document.getElementById('input_dpdfrance_predict_gsm_dest');

        var gsm_fr = new RegExp(/^((\+33|0)[67])(?:[ _.-]?(\d{2})){4}$/);
        var gsm_de = new RegExp(/^(\+|00)49(15|16|17)(\s?\d{7,8})$/);
        var gsm_be = new RegExp(/^(\+|00)324([56789]\d)(\s?\d{6})$/);
        var gsm_at = new RegExp(/^(\+|00)436([56789]\d)(\s?\d{4})$/);
        var gsm_uk = new RegExp(/^(\+|00)447([3456789]\d)(\s?\d{7})$/);
        var gsm_nl = new RegExp(/^(\+|00)316(\s?\d{8})$/);
        var gsm_pt = new RegExp(/^(\+|00)3519(\s?\d{7})$/);
        var gsm_ei = new RegExp(/^(\+|00)3538(\s?\d{8})$/);
        var gsm_es = new RegExp(/^(\+|00)34(6|7)(\s?\d{8})$/);
        var gsm_it = new RegExp(/^(\+|00)393(\s?\d{9})$/);

        var numbers = gsmDest.value.substr(-6);
        var pattern = new Array('000000','111111','222222','333333','444444','555555','666666','777777','888888','999999', '123456', '234567', '345678', '456789');

        if ((gsm_fr.test(gsmDest.value)
            || gsm_it.test(gsmDest.value)
            || gsm_es.test(gsmDest.value)
            || gsm_ei.test(gsmDest.value)
            || gsm_pt.test(gsmDest.value)
            || gsm_nl.test(gsmDest.value)
            || gsm_uk.test(gsmDest.value)
            || gsm_at.test(gsmDest.value)
            || gsm_de.test(gsmDest.value)
            || gsm_be.test(gsmDest.value))
            && !dpdfrance_in_array(numbers, pattern)) {
            // GSM OK
            $("#dpdfrance_predict_gsm_button").css('background-color', '#34a900');
            $("#dpdfrance_predict_gsm_button").html('&#10003');
            $("#dpdfrance_predict_error").hide();
            dpdfrance_registerGsm(gsmDest.value);
            dpdfrance_allowOrder(true);
            return true;
        } else {
            // GSM NOK
            $('#dpdfrance_predict_gsm_button').css('background-color','#424143');
            $("#dpdfrance_predict_gsm_button").html('>');
            $("#dpdfrance_predict_error").show();
            dpdfrance_allowOrder(false);
            return false;
        }
    }
}

/* Displays/hide DPD France FO blocks */
function dpdfrance_display() {
    if ($("input[name*='delivery_option[']:checked").length != 0) {
        checkedCarrier = $("input[name*='delivery_option[']:checked").val().substr(0,$("input[name*='delivery_option[']:checked").val().indexOf(','));
        if (checkedCarrier == dpdfranceRelaisCarrierId) {
            $("#dpdfrance_relais_point_table").detach().appendTo($("[id^=delivery_option]:checked").parents('.delivery-option').children('label'));
            $(".dpdfrance_fo").hide();
            $("#dpdfrance_relais_point_table").fadeIn('fast');
            $('#dpdfrance_relais_point_table').children('#dpdfrance_relais_point_table').css('display', '');
            dpdfrance_checkPudo();
            return true;
        }
        if (checkedCarrier == dpdfrancePredictCarrierId) {
            $("#div_dpdfrance_predict_block").detach().appendTo($("[id^=delivery_option]:checked").parents('.delivery-option').children('label'));
            $(".dpdfrance_fo").hide();
            $("#div_dpdfrance_predict_block").fadeIn('fast');
            dpdfrance_checkGSM();
            return true;
        }
        $(".dpdfrance_fo").hide();
        dpdfrance_allowOrder(true);
    }
}

/* Block/Unblock Order button */
function dpdfrance_allowOrder($status)
{
    if ($status == true) {
        $('[name=confirmDeliveryOption]').removeAttr('disabled');
    } else {
        $('[name=confirmDeliveryOption]').attr('disabled', 'disabled');
    }
}

/* Google Maps */
function initializeDpdfranceGM(mapid,lat,longti,baseurl) {
    var latlng = new google.maps.LatLng(lat, longti);

    var myOptions = {
        zoom      : 16,
        center    : latlng,
        mapTypeId : google.maps.MapTypeId.ROADMAP,
        styles:[{"featureType":"landscape","stylers":[{"visibility":"on"},{"color":"#e6e7e7"}]},{"featureType":"poi.sports_complex","stylers":[{"visibility":"on"}]},{"featureType":"poi.attraction","stylers":[{"visibility":"off"}]},{"featureType":"poi.government","stylers":[{"visibility":"on"}]},{"featureType":"poi.medical","stylers":[{"visibility":"on"}]},{"featureType":"poi.place_of_worship","stylers":[{"visibility":"on"}]},{"featureType":"poi.school","stylers":[{"visibility":"on"}]},{"featureType":"water","elementType":"geometry","stylers":[{"visibility":"on"},{"color":"#d2e4f3"}]},{"featureType":"water","elementType":"labels","stylers":[{"visibility":"on"}]},{"featureType":"transit","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"geometry.fill","stylers":[{"visibility":"on"},{"color":"#ffffff"}]},{"featureType":"road","elementType":"geometry.stroke","stylers":[{"visibility":"on"},{"color":"#e6e7e7"}]},{"elementType":"labels.text.fill","stylers":[{"visibility":"on"},{"color":"#666666"}]},{"featureType":"poi.business","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"poi","elementType":"geometry.fill","stylers":[{"visibility":"on"},{"color":"#dbdbdb"}]},{"featureType":"administrative.locality","elementType":"labels.text.fill","stylers":[{"visibility":"on"},{"color":"#808285"}]},{"featureType":"transit.station","stylers":[{"visibility":"on"}]},{"featureType":"transit","elementType":"geometry","stylers":[{"visibility":"on"},{"color":"#dbdbdb"}]},{"elementType":"labels.icon","stylers":[{"visibility":"on"},{"saturation":-100}]},{"featureType":"road","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"elementType":"labels.text","stylers":[{"visibility":"on"}]},{"featureType":"transit.line","elementType":"labels.text","stylers":[{"visibility":"off"}]}]
    };

    var map = new google.maps.Map(document.getElementById(mapid), myOptions);

    var marker = new google.maps.Marker({
        icon         : baseurl+"/views/img/front/relais/logo-max-png.png",
        position     : latlng,
        animation    : google.maps.Animation.DROP,
        map          : map
    });
}

function openDpdfranceDialog(id,mapid,lat,longti,baseurl) {
    $("#header").css('z-index', 0);
    $("#dpdfrance_relais_filter").fadeIn(150, function() {$("#"+id).fadeIn(150);});
    window.setTimeout(function () {initializeDpdfranceGM(mapid,lat,longti,baseurl)},200);
}