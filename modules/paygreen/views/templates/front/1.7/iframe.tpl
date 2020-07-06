{*
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
*}
<input name="pgIdToSelect" id="pgIdToSelect" value="{$id|escape:'html':'UTF-8'}" type="hidden">
<iframe id="pgIframe{$id|escape:'html':'UTF-8'}"
		frameBorder="0"
		src="{$url|escape:'html':'UTF-8'}"
		style="height:{$iframeSize['minHeight']|escape:'html':'UTF-8'}px; width:{$iframeSize['minWidth']|escape:'html':'UTF-8'}px;max-width:100%;">
</iframe>
<script type="text/javascript" id="plugin{$id|escape:'html':'UTF-8'}">
// allow to show additionnal content of payment option for iframe
(function() {
	var pgIdToSelect = document.getElementById('pgIdToSelect');
    // Identify by form name
    var paymentForm = document.getElementById('pgIframe'+pgIdToSelect.value);
    var parent = paymentForm.parentNode;

    if (!parent) {
        return;
    }
    // Find the button linked to iframe
    var splittedPaymentOption = parent.id.split('-');
    var idToSelect = splittedPaymentOption[2];

	if (idToSelect !== null && idToSelect !== '' && !isNaN(idToSelect)) {
		var pgRadio = document.getElementById('payment-option-'+ idToSelect);
		var additionalContent = document.getElementById('payment-option-'+idToSelect+'-additional-information');
		var iframeName = 'pgIframe'+idToSelect;
        if (pgRadio){
            pgRadio.click();
        }
		// show only if html iframe is loaded
		paymentForm.onload=function(){
            if (pgRadio){
                pgRadio.click();
            }
			// force if the click not show additionnal part
			if (additionalContent) {
                additionalContent.style.display = 'block';
            }
		};
	}
})();
</script>
