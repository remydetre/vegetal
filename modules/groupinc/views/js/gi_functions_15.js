/**
* Price increment/discount by groups, categories and prices
*
* NOTICE OF LICENSE
*
* This product is licensed for one customer to use on one installation (test stores and multishop included).
* Site developer has the right to modify this module to suit their needs, but can not redistribute the module in
* whole or in part. Any other use of this module constitues a violation of the user agreement.
*
* DISCLAIMER
*
* NO WARRANTIES OF DATA SAFETY OR MODULE SECURITY
* ARE EXPRESSED OR IMPLIED. USE THIS MODULE IN ACCORDANCE
* WITH YOUR MERCHANT AGREEMENT, KNOWING THAT VIOLATIONS OF
* PCI COMPLIANCY OR A DATA BREACH CAN COST THOUSANDS OF DOLLARS
* IN FINES AND DAMAGE A STORES REPUTATION. USE AT YOUR OWN RISK.
*
*  @author    idnovate
*  @copyright 2018 idnovate
*  @license   See above
*/

jQuery('document').ready(function() {
	if (jQuery('#groupinc_configuration_form #type').val() == 1) {
		jQuery('#price_calculation').parent().slideDown();
		jQuery('#price_calculation').parent().prev().slideDown();

		jQuery('#percentage').parent().slideDown();
		jQuery('#percentage').parent().prev().slideDown();

		jQuery('#fix').parent().slideUp();
		jQuery('#fix').parent().prev().slideUp();
		jQuery('#fix').val('');
	} else if (jQuery('#groupinc_configuration_form #type').val() == 0) {
		jQuery('#price_calculation').parent().slideUp();
		jQuery('#price_calculation').parent().prev().slideUp();

		jQuery('#percentage').parent().slideUp();
		jQuery('#percentage').parent().prev().slideUp();
		jQuery('#percentage').val('');

		jQuery('#fix').parent().slideDown();
		jQuery('#fix').parent().prev().slideDown();
	} else {
		jQuery('#price_calculation').parent().slideDown();
		jQuery('#price_calculation').parent().prev().slideDown();

		jQuery('#percentage').parent().slideDown();
		jQuery('#percentage').parent().prev().slideDown();

		jQuery('#fix').parent().slideDown();
		jQuery('#fix').parent().prev().slideDown();
	}

	jQuery('#groupinc_configuration_form #type').change(function() {
		if (jQuery(this).val() == 1) {
			jQuery('#price_calculation').parent().slideDown();
		jQuery('#price_calculation').parent().prev().slideDown();

		jQuery('#percentage').parent().slideDown();
		jQuery('#percentage').parent().prev().slideDown();

		jQuery('#fix').parent().slideUp();
		jQuery('#fix').parent().prev().slideUp();
		jQuery('#fix').val('');
		} else if (jQuery(this).val() == 0) {
			jQuery('#price_calculation').parent().slideUp();
			jQuery('#price_calculation').parent().prev().slideUp();

			jQuery('#percentage').parent().slideUp();
			jQuery('#percentage').parent().prev().slideUp();
			jQuery('#percentage').val('');

			jQuery('#fix').parent().slideDown();
			jQuery('#fix').parent().prev().slideDown();
		}
		else {
			jQuery('#price_calculation').parent().slideDown();
			jQuery('#price_calculation').parent().prev().slideDown();

			jQuery('#percentage').parent().slideDown();
			jQuery('#percentage').parent().prev().slideDown();

			jQuery('#fix').parent().slideDown();
			jQuery('#fix').parent().prev().slideDown();
		}
	});

	jQuery('.multiple_select').multiselect();

});

jQuery('document').ready(function() {
	$("input:radio[name=filter_prices], input:radio[name=filter_stock], input:radio[name=filter_store]").click(function() {
		toggleFields($(this).attr('name'));
	});

	toggleFields('filter_prices');
	toggleFields('filter_stock');
});

function toggleFields(fieldName)
{
    if ($('#'+fieldName+'_on').is(':checked')) {
        $('.form-group').each(function() {
            if ($(this).find('.toggle_'+fieldName).length > 0) {
                if (!$(this).hasClass('translatable-field')) {
                    $(this).slideDown('slow');
                }

                if (id_language) {
                    if ($(this).hasClass('lang-'+id_language)) {
                        $(this).slideDown('slow');
                    }
                } else {
                    if ($(this).hasClass('lang-1')) {
                        $(this).slideDown('slow');
                    }
                }
            }
        });
        if (fieldName == 'filter_store') {
    		$('.tree-panel-heading-controls').closest('.panel').closest('.form-group').slideDown('slow');
    	}
    } else {
        $('.form-group').each(function() {
            if ($(this).find('.toggle_'+fieldName).length > 0) {
                $(this).slideUp();
            }
        });
		if (fieldName == 'filter_store') {
    		$('.tree-panel-heading-controls').closest('.panel').closest('.form-group').slideUp();
    	}
    }
}