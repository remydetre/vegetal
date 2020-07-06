/*
* 2017 Singleton software
*
*  @author Singleton software <info@singleton-software.com>
*  @copyright 2017 Singleton software
*/
$(document).ready(function(){
	hidePriceConfigData = JSON.parse(hidePriceConfig);
	if (notShowPrice && hidePriceConfigData.show_product_detail_box == 1) {
		displayNotAuthorizedBlockDetail("#main");
	}
});