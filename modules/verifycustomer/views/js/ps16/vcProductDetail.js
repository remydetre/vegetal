/*
* 2017 Singleton software
*
*  @author Singleton software <info@singleton-software.com>
*  @copyright 2017 Singleton software
*/
$(document).ready(function(){
	if(notShowPrice){
		hidePriceConfigData = JSON.parse(hidePriceConfig);
		if(hidePriceConfigData.product_detail_position == 0){ //In center
			if($("#short_description_block").length > 0){
				$("#short_description_block").after("<div class='notAuthorizedBox'></div>");
			}else{
				$("#availability_statut").before("<div class='notAuthorizedBox'></div>");
			}
		}else if(hidePriceConfigData.product_detail_position == 1){ //Instead of price
			if($(".product_attributes").length > 0){
				$(".product_attributes").before("<div class='notAuthorizedBox'></div>");
			}else{
				$("#buy_block").append("<div class='notAuthorizedBox'></div>");
			}
		}else{ //Instead of "add to card" button 
			if($(".product_attributes").length > 0){
				$(".product_attributes").after("<div class='notAuthorizedBox'></div>");
			}else{
				$("#buy_block").append("<div class='notAuthorizedBox'></div>");
			}
		}
		if (hidePriceConfigData.show_product_detail_box == 1) {
			displayNotAuthorizedBlock(hidePriceConfigData.text_not_authorized_pd[langId], hidePriceConfigData.link_text_pd[langId], hidePriceConfigData.text_color_pd, hidePriceConfigData.text_size_pd, hidePriceConfigData.background_color_pd, hidePriceConfigData.product_detail_position, registrationLink, hidePriceConfigData.show_borders_pd, hidePriceConfigData.border_radius_pd);
		}
	}
});
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
			"width": ((position == 0) ? "100%" : "90%" ),
			"margin": ((position == 1) ? "0 auto 20px" : "0 auto 20px" ),
			"height":"60px",
			"background-color": backgroundColor
		});
		$(".notAuthorizedBoxText").css({
			"color": textColor,
			"font-size": fontSize + "px",
			"text-align":"center",
			"padding": "7px"
		});
		$(".notAuthorizedBoxText a").css({
			"color": textColor,
			"font-size": fontSize + "px"
		});
	}
}