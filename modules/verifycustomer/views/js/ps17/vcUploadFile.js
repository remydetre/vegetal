/*
* 2017 Singleton software
*
*  @author Singleton software <info@singleton-software.com>
*  @copyright 2017 Singleton software
*/
$(document).ready(function() {
	$("#customer-form").attr('enctype','multipart/form-data');
	$('input[type=file]').removeClass('form-control').addClass('filestyle');
	$('.filestyle').filestyle();
	$('input[type=file]').parents('.form-group:eq(0)').find('.form-control-comment').css('padding','0').html(fileDescText);
	if (uploadFilePosition == 1) {
		$(".filestyle").parents(".form-group:eq(0)").detach().prependTo("#customer-form section:eq(0)");
	} else if (uploadFilePosition == 2) {
		if ($("input[name=siret]").length > 0) {
			$("input[name=siret]").parents('.form-group:eq(0)').after($(".filestyle").parents(".form-group:eq(0)").detach());
		}
	} else if (uploadFilePosition == 3) {
		$("input[name=birthday]").parents('.form-group:eq(0)').after($(".filestyle").parents(".form-group:eq(0)").detach());
	}
});