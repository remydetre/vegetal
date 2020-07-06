/*
* 2017 Singleton software
*
*  @author Singleton software <info@singleton-software.com>
*  @copyright 2017 Singleton software
*/
$(document).ready(function() {
	$("#text_size_pl, #text_size_pd, .mColorPickerInput").prop("readonly", true);
	$("#text_size_pl").after("<div class='fontSlider_pl'></div>");
	$("#text_size_pd").after("<div class='fontSlider_pd'></div>");
	$(".fontSlider_pl").slider({
		value: $( "#text_size_pl" ).val(),
		min: 8,
		max: 20,
		slide: function( event, ui ) {
			$( "#text_size_pl" ).val( ui.value );
			$(".notAuthorizedBoxText_pl").css({
				"font-size":ui.value + "px"
			});
			$(".notAuthorizedBoxText_pl a").css({
				"font-size":ui.value + "px"
			});
		}
	});
	$(".fontSlider_pd").slider({
		value: $( "#text_size_pd" ).val(),
		min: 8,
		max: 20,
		slide: function( event, ui ) {
			$( "#text_size_pd" ).val( ui.value );
			$(".notAuthorizedBoxText_pd").css({
				"font-size":ui.value + "px"
			});
			$(".notAuthorizedBoxText_pd a").css({
				"font-size":ui.value + "px"
			});
		}
	});
	$(".form-group input[type='checkbox']").last().parents(".form-group").css("margin-top","20px");
	$(".productDetailPosition:eq(0)").last().parents(".form-group").css({"border-bottom":"1px solid black","padding-bottom":"20px"});
	$(".productDetailPosition:eq(0)").parents(".form-group").css("margin-top","40px");
	
	$(".verifycustomer .form-group:eq(0)").before("<div class='form-group main_title'>" + translate.mainSettingsTitle + "</div>");
	$(".form-group input[name='show_product_detail_box']").parents(".form-group:eq(0)").before("<div class='form-group notAuthorizedBox_pd'></div>");
	$(".notAuthorizedBox_pd").before("<div class='form-group notAuthorizedBox_pd_title'>" + translate.productDetailTitle + "</div>");
	$(".productDetailPosition:eq(0)").parents(".form-group:eq(0)").after("<div class='form-group notAuthorizedBox_pl'></div>");
	$(".notAuthorizedBox_pl").before("<div class='form-group notAuthorizedBox_pl_title'>" + translate.productListTitle + "</div>");
	$(".notAuthorizedBox_pd_title").css({"border-top" : "1px solid black", "padding-top": "20px"});
	
	$(".notAuthorizedBox_pl_title, .notAuthorizedBox_pd_title, .main_title").css({
		"margin":"30px auto 40px",
		"text-align": "center",
	    "font-size": "15px",
		"font-weight": "bold"
	});
	if ($("input[name=show_borders_pl]:checked", ".verifycustomer").val() == 1) {
		$(".notAuthorizedBox_pl").css("border", "1px solid black");
	}
	if ($("input[name=border_radius_pl]:checked", ".verifycustomer").val() == 1) {
		$(".notAuthorizedBox_pl").css({
			"-webkit-border-radius": "8px",
			"-moz-border-radius":"8px",
			"border-radius":"8px"
		});
	}

	$(".notAuthorizedBox_pl").css({
		"width": "240px",
		"margin":"30px auto 40px",
		"height":"60px",
		"background-color":$(".backgroundColor_pl").val()
	});
	if ($("input[name=show_borders_pd]:checked", ".verifycustomer").val() == 1) {
		$(".notAuthorizedBox_pd").css("border", "1px solid black");
	}
	if ($("input[name=border_radius_pd]:checked", ".verifycustomer").val() == 1) {
		$(".notAuthorizedBox_pd").css({
			"-webkit-border-radius": "8px",
			"-moz-border-radius":"8px",
			"border-radius":"8px"
		});
	}
	$(".notAuthorizedBox_pd").css({
		"width":"300px",
		"margin":"30px auto 40px",
		"height":"60px",
		"background-color":$(".backgroundColor_pd").val()
	});
	refreshNotAuthorizedBox("pl");
	refreshNotAuthorizedBox("pd");
	
	$(".text_not_authorized_pl, .link_text_pl").bind("keyup change", function(e) {
		refreshNotAuthorizedBox("pl");
	});
	$(".text_not_authorized_pd, .link_text_pd").bind("keyup change", function(e) {
		refreshNotAuthorizedBox("pd");
	});
	
	var origHideOtherLanguage = hideOtherLanguage;
	hideOtherLanguage = function(param1){
		origHideOtherLanguage(param1);
		refreshNotAuthorizedBox("pl");
		refreshNotAuthorizedBox("pd");
	};
	
	$("input[name=show_borders_pd]").click(function(){
		if ($("input[name=show_borders_pd]:checked", ".verifycustomer").val() == 1) {
			$(".notAuthorizedBox_pd").css("border", "1px solid black");
		} else {
			$(".notAuthorizedBox_pd").css("border", "none");
		}
	});
	$("input[name=border_radius_pd]").click(function(){
		if ($("input[name=border_radius_pd]:checked", ".verifycustomer").val() == 1) {
			$(".notAuthorizedBox_pd").css({
				"-webkit-border-radius": "8px",
				"-moz-border-radius":"8px",
				"border-radius":"8px"
			});
		} else {
			$(".notAuthorizedBox_pd").css({
				"-webkit-border-radius": "0px",
				"-moz-border-radius":"0px",
				"border-radius":"0px"
			});
		}
	});
	$("input[name=show_borders_pl]").click(function(){
		if ($("input[name=show_borders_pl]:checked", ".verifycustomer").val() == 1) {
			$(".notAuthorizedBox_pl").css("border", "1px solid black");
		} else {
			$(".notAuthorizedBox_pl").css("border", "none");
		}
	});
	$("input[name=border_radius_pl]").click(function(){
		if ($("input[name=border_radius_pl]:checked", ".verifycustomer").val() == 1) {
			$(".notAuthorizedBox_pl").css({
				"-webkit-border-radius": "8px",
				"-moz-border-radius":"8px",
				"border-radius":"8px"
			});
		} else {
			$(".notAuthorizedBox_pl").css({
				"-webkit-border-radius": "0px",
				"-moz-border-radius":"0px",
				"border-radius":"0px"
			});
		}
	});
	$(".backgroundColor_pl").bind("change", function(e) {
		$(".notAuthorizedBox_pl").css({
			"background-color":$(".backgroundColor_pl").val(),
		});
	});
	$(".backgroundColor_pd").bind("change", function(e) {
		$(".notAuthorizedBox_pd").css({
			"background-color":$(".backgroundColor_pd").val(),
		});
	});
	$(".textColor_pl").bind("change", function(e) {
		$(".notAuthorizedBoxText_pl, .notAuthorizedBoxText_pl a").css({
			"color":$(".textColor_pl").val(),
		});
	});
	$(".textColor_pd").bind("change", function(e) {
		$(".notAuthorizedBoxText_pd, .notAuthorizedBoxText_pd a").css({
			"color":$(".textColor_pd").val(),
		});
	});
	$(".verifycustomer .checkbox").last().css("margin-bottom", "35px");
	$( ".textSize_pl, .textSize_pd" ).css({ "width":"35px", "float":"left" });
	
	$(".fontSlider_pl, .fontSlider_pd").css({"width":"40%", "margin-left":"50px", "margin-top":"10px"});
});

function refreshNotAuthorizedBox(type) {
	textNotAuthorized = $(".text_not_authorized_" + type).val();
	linkVal = $(".link_text_" + type).val();
	$("textarea.text_not_authorized_" + type).each(function(){
		if ($(this).parents(".translatable-field:eq(0)").css("display") != "none") {
			textNotAuthorized = $(this).val();
		}
	});
	$("input.link_text_" + type).each(function(){
		if ($(this).parents(".translatable-field:eq(0)").css("display") != "none") {
			linkVal = $(this).val();
		}
	});
				
	if (textNotAuthorized.indexOf("{REGISTRATION}") > -1) {
		textNotAuthorized = textNotAuthorized.replace("{REGISTRATION}", "<strong><a href='javascript:void(0)'>" + linkVal + "</a></strong>");
	}
	$(".notAuthorizedBox_" + type).empty().append("<p class='notAuthorizedBoxText_" + type + "'>" + textNotAuthorized + "</p>");
	$(".notAuthorizedBoxText_" + type).css({
		"color": $(".textColor_" + type).val(),
		"font-size": $("#text_size_" + type).val() + "px",
		"text-align": "center",
		"padding": ((type == "pl" ? "5px" : "7px"))
	});
	$(".notAuthorizedBoxText_" + type + " a").css({
		"color": $(".textColor_" + type).val(),
		"font-size": $("#text_size_" + type).val() + "px"
	});
}