/*
* 2017 Singleton software
*
*  @author Singleton software <info@singleton-software.com>
*  @copyright 2017 Singleton software
*/
isCategoryFiltered = false;
if (typeof reloadContent !== 'undefined'){
	var origReloadContent = reloadContent;
	reloadContent = function(str){
		origReloadContent(str);
		if(notShowPrice){
			checkIfCanBeSetNotAuthorizedBlock();
			isCategoryFiltered = true;
		}
	}
}
if (typeof display !== 'undefined'){
	var origDisplay = display;
	display = function(str){
		origDisplay(str);
		if(notShowPrice){
			checkIfCanBeSetNotAuthorizedBlock();
			isCategoryFiltered = true;
		}
	}
}
$(document).ready(function(){
	if(notShowPrice && !isCategoryFiltered){
		chooseDisplayNotAuthorizedBlockPositionAndShow();
	}
	hidePriceConfigData = JSON.parse(hidePriceConfig);
	if (parseInt(hidePriceConfigData.approve_customer) == 1 && pageName == 'authentication' && (defaultCustomerGroup == 1 || defaultCustomerGroup == 2)) {
		$("#columns .breadcrumb").after("<p class='alert alert-warning'>" + accountHaveToBeApprove + "</p>");
	}
});
function chooseDisplayNotAuthorizedBlockPositionAndShow(){
	hidePriceConfigData = JSON.parse(hidePriceConfig);
	$(".notAuthorizedBox").remove();
	if($("#left_column .price-box").length > 0){
		$("#left_column .price-box").remove();
	}
	if($("#right_column .price-box").length > 0){
		$("#right_column .price-box").remove();
	}
	if($("#blockspecials .content_price").length > 0){
		$("#blockspecials .content_price").remove();
	}
	if($("#blockspecials .ajax_add_to_cart_button").length > 0){
		$("#blockspecials .ajax_add_to_cart_button").remove();
	}
	if($("#homefeatured .content_price").length > 0){
		$("#homefeatured .content_price").remove();
	}
	if($("#homefeatured .ajax_add_to_cart_button").length > 0){
		$("#homefeatured .ajax_add_to_cart_button").remove();
	}
	if($("#blockbestsellers .content_price").length > 0){
		$("#blockbestsellers .content_price").remove();
	}
	if($("#blockbestsellers .ajax_add_to_cart_button").length > 0){
		$("#blockbestsellers .ajax_add_to_cart_button").remove();
	}
	if($("#blocknewproducts .content_price").length > 0){
		$("#blocknewproducts .content_price").remove();
	}
	if($("#blocknewproducts .ajax_add_to_cart_button").length > 0){
		$("#blocknewproducts .ajax_add_to_cart_button").remove();
	}
	if(hidePriceConfigData.product_list_position == 0){
		if($(".product_list .right-block .right-block-content").length > 0){
			$(".product_list .right-block .right-block-content").prepend("<div class='notAuthorizedBox'></div>");
		}else if($(".product_list .right-block").length > 0){
			$(".product_list .right-block").prepend("<div class='notAuthorizedBox'></div>");
		}
	}else{
		if($(".product_list .center-block .product-desc").length > 0){
			$(".product_list .center-block .product-desc").after("<div class='notAuthorizedBox'></div>");
		}else if($(".product_list .right-block").length > 0){
			$(".product_list .right-block").prepend("<div class='notAuthorizedBox'></div>");
		}
	}
	if (hidePriceConfigData.show_product_list_box == 1) {
		displayNotAuthorizedBlock(hidePriceConfigData.text_not_authorized_pl[langId], hidePriceConfigData.link_text_pl[langId], hidePriceConfigData.text_color_pl, hidePriceConfigData.text_size_pl, hidePriceConfigData.background_color_pl, hidePriceConfigData.product_list_position, registrationLink, hidePriceConfigData.show_borders_pl, hidePriceConfigData.border_radius_pl);
	}
}
function checkIfCanBeSetNotAuthorizedBlock(){
	if (typeof ajaxLoaderOn !== 'undefined'){ //ajaxLoaderOn indikuje ci uz je nacitany ajax z filtra kategorii
		if(ajaxLoaderOn == 0){
			chooseDisplayNotAuthorizedBlockPositionAndShow();
		}else{
			setTimeout(function(){
				checkIfCanBeSetNotAuthorizedBlock();
			},200);
		}
	}else{
		setTimeout(function(){
			chooseDisplayNotAuthorizedBlockPositionAndShow();
		},250);
	}
}
function displayNotAuthorizedBlock(textNotAuthorized, linkVal, textColor, fontSize, backgroundColor, position, registrationLink, hasBorder, hasBorderRadius){
	if (typeof textNotAuthorized !== 'undefined'){
		if(textNotAuthorized.indexOf("{REGISTRATION}") > -1){
			textNotAuthorized = textNotAuthorized.replace("{REGISTRATION}", "<strong><a href='" + registrationLink + "' target='_blank'>" + linkVal + "</a></strong>");
		}
		$(".notAuthorizedBox").empty().append("<p class='notAuthorizedBoxText'>" + textNotAuthorized + "</p>");
		if (hasBorder == 1) {
			$(".notAuthorizedBox").css("border", "1px solid black");
		}
		if (hasBorderRadius == 1) {
			$(".notAuthorizedBox").css({
				"-webkit-border-radius": "8px",
				"-moz-border-radius":"8px",
				"border-radius":"8px"
			});
		}
		$(".notAuthorizedBox").css({
			"width": "90%",
			"margin": ((position == 1) ? "0 auto 20px" : "0 auto 20px" ),
			"height":"60px",
			"background-color": backgroundColor
		});
		$(".notAuthorizedBoxText").css({
			"color": textColor,
			"font-size": fontSize + "px",
			"text-align":"center",
			"padding": "5px"
		});
		$(".notAuthorizedBoxText a").css({
			"color": textColor,
			"font-size": fontSize + "px"
		});
	}
}