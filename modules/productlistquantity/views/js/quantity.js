/*
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 *  @author PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2019 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
    $(document).ready(function () {
        /** version 1.6 **/
		if(version != 'last'){
            AddContent();
	    }
    });


	if(version == 'last'){
		/** version 1.7 **/

        $(document).on('change', 'input.number-quantity', function(e){
            var quantity_wanted = parseInt($(this).val());
			var min_quantity = parseInt($(this).attr('min'));
			var quantity = $(this).parents(".quantity_form").find('input[name=product_quantity]').val();
			var allow_order = $(this).attr('data_allow_order');
            if(quantity_wanted > quantity && allow_order != 1){
				$(this).val(min_quantity);
				$.fancybox.open([
							{
								type: 'inline',
								autoScale: true,
								minHeight: 30,
								content: '<p class="fancybox-error">' + stock + '</p>'
							}
						], {
							padding: 0
				});
			}else{
                $(this).next().attr("disabled", false);
			}
			if(quantity_wanted < min_quantity){
				$(this).val(min_quantity);
				$.fancybox.open([
							{
								type: 'inline',
								autoScale: true,
								minHeight: 30,
								content: '<p class="fancybox-error">' + error_quantity + ' ' + min_quantity + ' '+error_min +'</p>'
							}
						], {
							padding: 0
				});
			}
	    });

    }else{
        /** version 1.6 **/
		$(document).on('click', '.ajax_add_to_cart_quantity', function(e){
			e.preventDefault();
			var isDisabled = $(this).is(':disabled');
			var idProduct =  parseInt($(this).data('id-product'));
			var idProductAttribute =  parseInt($(this).data('id-product-attribute'));
			var qty = $(this).prev("input").val();
		    if (window.ajaxCart != undefined){
			    if (isDisabled == false){
			        ajaxCart.add(idProduct, idProductAttribute, false, this, qty);
		        }
		    }
	    });
		$(document).ajaxComplete(function( event, request, settings ) {
            if(document.getElementById("quantity_wanted") == null){
                AddContent();
			}
        });

    }
    function AddContent(){
	    	$('.ajax_add_to_cart_button').each(function() {
                $(this).removeClass("ajax_add_to_cart_button").addClass("ajax_add_to_cart_quantity");
            });
	        $('.button-container').each(function() {
		        if($(this).find('a.ajax_add_to_cart_quantity').length > 0 ){
		            var id_product = $(this).find('a.ajax_add_to_cart_quantity').attr('data-id-product');
				    var minimal_quantity =$(this).find('a.ajax_add_to_cart_quantity').attr('data-minimal_quantity');
                    if(minimal_quantity){
                        var qte = minimal_quantity;
				    }else{
					    var qte = 1;
				    }
                    var content ='<input type="number" min="'+qte+'" name="qty" id="quantity_wanted" data_id_product ="'+id_product +'" class="text number-quantity" value="'+qte+'" >';
			        $(this).prepend(content);
			    }
            });
	}