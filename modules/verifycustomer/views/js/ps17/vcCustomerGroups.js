/*
* 2017 Singleton software
*
*  @author Singleton software <info@singleton-software.com>
*  @copyright 2017 Singleton software
*/

$(document).ready(function(){
	if (typeof customGroupPosition != "undefined") {
		if (customGroupPosition == 1) {
			$("#customerGroups").detach().prependTo("#customer-form section:eq(0)");
		} else if (customGroupPosition == 2) {
			if ($("input[name=siret]").length > 0) {
				$("input[name=siret]").parents('.form-group:eq(0)').after($("#customerGroups").detach());
			}
		} else if (customGroupPosition == 3) {
			$("input[name=birthday]").parents('.form-group:eq(0)').after($("#customerGroups").detach());
		} else {
			$("#customerGroups").detach().appendTo("#customer-form section:eq(0)");
		}
	}
});