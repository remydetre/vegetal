/*
* 2017 Singleton software
*
*  @author Singleton software <info@singleton-software.com>
*  @copyright 2017 Singleton software
*/

$(document).ready(function(){
	hidePriceConfigData = JSON.parse(hidePriceConfig);
	if (notShowPrice) {
		chooseDisplayNotAuthorizedBlockPositionAndShow(hidePriceConfigData);
	}
	// vzdy po zbehnuti akehokolvek filtru v katalogu produktov (blocklayered, paginacia, zoradenie), sa cez tento emit vykreslia nanovo message boxi
	prestashop.on('updateProductList', function (data) {
		if (notShowPrice) {
			chooseDisplayNotAuthorizedBlockPositionAndShow(hidePriceConfigData);
		}
	});
	if (parseInt(hidePriceConfigData.approve_customer) == 1 && pageName == 'authentication' && (defaultCustomerGroup == 1 || defaultCustomerGroup == 2)) {
		$("#columns .breadcrumb").after("<p class='alert alert-warning'>" + accountHaveToBeApprove + "</p>");
	}
	prestashop.on('clickQuickView', function (elm) {
		if (notShowPrice && hidePriceConfigData.show_product_detail_box == 1) {
			checkIfElementExistRepeatedly("#quickview-modal-" + elm.dataset.idProduct + "-" + elm.dataset.idProductAttribute, 200, 0, 20, "displayNotAuthorizedBlockDetail", "#quickview-modal-" + elm.dataset.idProduct + "-" + elm.dataset.idProductAttribute);
		}
	});
});

function chooseDisplayNotAuthorizedBlockPositionAndShow(hidePriceConfigData) {
	$(".notAuthorizedBoxGlobal").remove();
	
	// nahal som pre isotu skryvanie cien v sekciach ktore boli aj v preste 16, napriklad v pravom a lavom stlpci alebo sekciach na indexe - najpredavane, najnovsie, ...
	if ($("#left_column .price-box").length > 0) {
		$("#left_column .price-box").remove();
	}
	if ($("#right_column .price-box").length > 0) {
		$("#right_column .price-box").remove();
	}
	if ($("#blockspecials .content_price").length > 0) {
		$("#blockspecials .content_price").remove();
	}
	if ($("#blockspecials .ajax_add_to_cart_button").length > 0) {
		$("#blockspecials .ajax_add_to_cart_button").remove();
	}
	if ($("#homefeatured .content_price").length > 0) {
		$("#homefeatured .content_price").remove();
	}
	if ($("#homefeatured .ajax_add_to_cart_button").length > 0) {
		$("#homefeatured .ajax_add_to_cart_button").remove();
	}
	if ($("#blockbestsellers .content_price").length > 0) {
		$("#blockbestsellers .content_price").remove();
	}
	if ($("#blockbestsellers .ajax_add_to_cart_button").length > 0) {
		$("#blockbestsellers .ajax_add_to_cart_button").remove();
	}
	if ($("#blocknewproducts .content_price").length > 0) {
		$("#blocknewproducts .content_price").remove();
	}
	if ($("#blocknewproducts .ajax_add_to_cart_button").length > 0) {
		$("#blocknewproducts .ajax_add_to_cart_button").remove();
	}
	
	if (hidePriceConfigData.show_product_list_box == 1) {
		prepareMessageBoxToDisplayInProductsCatalog();
		displayNotAuthorizedBlockGlobal(hidePriceConfigData.text_not_authorized_pl[langId], hidePriceConfigData.link_text_pl[langId], hidePriceConfigData.text_color_pl, hidePriceConfigData.text_size_pl, hidePriceConfigData.background_color_pl, hidePriceConfigData.product_list_position, registrationLink, hidePriceConfigData.show_borders_pl, hidePriceConfigData.border_radius_pl);
	}
}

function prepareMessageBoxToDisplayInProductsCatalog() {
	if ($(".products").length > 0) {
		//$("article.product-miniature").css("height","420px");
		//$("article .thumbnail-container").css("height","370px");
		//$("article .product-description").css("height","150px");
		$("article .highlighted-informations").css("height","8.125rem");
		$(".products .product-description").append("<div class='notAuthorizedBoxGlobal'></div>");
	}
}

// zobrazenie message boxu v katalogoch produktov (v liste)
function displayNotAuthorizedBlockGlobal(textNotAuthorized, linkVal, textColor, fontSize, backgroundColor, position, registrationLink, hasBorder, hasBorderRadius) {
	if (typeof textNotAuthorized !== 'undefined') {
		if (textNotAuthorized.indexOf("{REGISTRATION}") > -1) {
			textNotAuthorized = textNotAuthorized.replace("{REGISTRATION}", "<strong><a href='" + registrationLink + "' target='_blank'>" + linkVal + "</a></strong>");
		}
		$(".notAuthorizedBoxGlobal").empty().append("<p class='notAuthorizedBoxText'>" + textNotAuthorized + "</p>");
		if (hasBorder == 1) {
			$(".notAuthorizedBoxGlobal").css("border", "1px solid black");
		}
		if (hasBorderRadius == 1) {
			$(".notAuthorizedBoxGlobal").css({
				"-webkit-border-radius": "8px",
				"-moz-border-radius":"8px",
				"border-radius":"8px"
			});
		}
		$(".notAuthorizedBoxGlobal").css({
			"width": "90%",
			"margin": "20px auto 20px",
			"height":"60px",
			"background-color": backgroundColor
		});
		$(".notAuthorizedBoxGlobal .notAuthorizedBoxText").css({
			"color": textColor,
			"font-size": fontSize + "px",
			"text-align":"center",
			"padding": "5px"
		});
		$(".notAuthorizedBoxGlobal .notAuthorizedBoxText a").css({
			"color": textColor,
			"font-size": fontSize + "px"
		});
	}
}

function prepareMessageBoxToDisplayInProductDetail(hidePriceConfigData, parent) {
	if (hidePriceConfigData.product_detail_position == 0) {
		//In center
		if ($(parent).find(".product-actions").length > 0) {
			$(parent).find(".product-actions").before("<div class='notAuthorizedBoxDetail'></div>");
		}
	} else if (hidePriceConfigData.product_detail_position == 1) {
		//Instead of price
		if ($(parent).find(".product-information").length > 0) {
			// v pripade klasickeho zobrazenia , nie modalu
			$(parent).find(".product-information").before("<div class='notAuthorizedBoxDetail'></div>");
		} else if($(parent).find("#product-description-short").length > 0) {
			// v pripade modalu
			$(parent).find("#product-description-short").before("<div class='notAuthorizedBoxDetail'></div>");
		}
	} else {
		//Instead of "add to card" button
		if ($(parent).find(".product-add-to-cart").length > 0) {
			$(parent).find(".product-add-to-cart").after("<div class='notAuthorizedBoxDetail'></div>");
		}
	}
}

//zobrazenie message boxu v detaile produktov a v modale pre datail produktu
function displayNotAuthorizedBlockDetail(parent) {
	hidePriceConfigData = JSON.parse(hidePriceConfig);
	prepareMessageBoxToDisplayInProductDetail(hidePriceConfigData, parent);
	if (typeof hidePriceConfigData.text_not_authorized_pd[langId] !== 'undefined') {
		if (hidePriceConfigData.text_not_authorized_pd[langId].indexOf("{REGISTRATION}") > -1) {
			hidePriceConfigData.text_not_authorized_pd[langId] = hidePriceConfigData.text_not_authorized_pd[langId].replace("{REGISTRATION}", "<strong><a href='" + registrationLink + "' target='_blank'>" + hidePriceConfigData.link_text_pd[langId] + "</a></strong>");
		}
		$(parent).find(".notAuthorizedBoxDetail").empty().append("<p class='notAuthorizedBoxText'>" + hidePriceConfigData.text_not_authorized_pd[langId] + "</p>");
		if (hidePriceConfigData.show_borders_pd == 1) {
			$(parent).find(".notAuthorizedBoxDetail").css("border", "1px solid black");
		}
		if (hidePriceConfigData.border_radius_pd == 1) {
			$(parent).find(".notAuthorizedBoxDetail").css({
				"-webkit-border-radius": "8px",
				"-moz-border-radius":"8px",
				"border-radius":"8px"
			});
		}
		$(parent).find(".notAuthorizedBoxDetail").css({
			"margin": "20px auto 20px",
			"height":"60px",
			"background-color": hidePriceConfigData.background_color_pd
		});
		$(parent).find(".notAuthorizedBoxDetail .notAuthorizedBoxText").css({
			"color": hidePriceConfigData.text_color_pd,
			"font-size": hidePriceConfigData.text_size_pd + "px",
			"text-align":"center",
			"padding": "7px"
		});
		$(parent).find(".notAuthorizedBoxDetail .notAuthorizedBoxText a").css({
			"color": hidePriceConfigData.text_color_pd,
			"font-size": hidePriceConfigData.text_size_pd + "px"
		});
	}
}

//funkcia ktora po urcitom casovom intervale kontrolu existenciu DOM elementu a ked uz existuje vykona pozadovanu funkciu
function checkIfElementExistRepeatedly(element, checkAfterTime, actualRepeat, maxRepeat, functionName, functionArgs) {
	if (actualRepeat <= maxRepeat) {
		if ($(element).length > 0) {
			window[functionName](functionArgs);
		} else {
			setTimeout(function() {
				actualRepeat++;
				checkIfElementExistRepeatedly(element, checkAfterTime, actualRepeat, maxRepeat, functionName, functionArgs);
			}, checkAfterTime);
		}
	}
	return false;
}