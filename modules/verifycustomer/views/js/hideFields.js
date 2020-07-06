/*
* 2017 Singleton software
*
*  @author Singleton software <info@singleton-software.com>
*  @copyright 2017 Singleton software
*/

$(document).ready(function() {
	showEmployyesAndGroupsLabels();
	displayFields("input[name='send_mail_after_approve_to_customer']", $("input[name='approve_customer']:checked").val() == 1);
	displaySendMailAfterRegToAdminSection();
	displayChooseCustomGroupToCustomerSection();
	displayUploadFileSection();
	if (!canUseGroups) {
		$("input[name='allow_choose_custom_group_to_customer']").parents(".switch:eq(0)").hide();
		$("input[name='allow_choose_custom_group_to_customer']").parents(".switch:eq(0)").siblings('p').css({'color' : '#CA6F6F', 'font-weight' : 'bold', 'margin-top' : '9px'}).html(translate.noGroups);
		displayFields(".customGroupPosition", false);
		displayFields(".customGroupSelectType", false);
		displayFields(".groups", false);
		displayFields(".autoApprovedGroups", false);
	}
	$("input[name='approve_customer']").click(function() {
		displayFields("input[name='send_mail_after_approve_to_customer']", $("input[name='approve_customer']:checked").val() == 1);
		showAutoApprovedGroups();
	});
	$("input[name='send_mail_after_reg_to_admin']").click(function() {
		displaySendMailAfterRegToAdminSection();
	});
	$("input[name='allow_choose_custom_group_to_customer']").click(function() {
		displayChooseCustomGroupToCustomerSection();
	});
	$("input[name='show_upload_button']").click(function() {
		displayUploadFileSection();
	});
	$("input.groups").click(function() {
		showAutoApprovedGroups();
	});
	
	if (psVersion17) {
		displayFields(".productListPosition", false);
	}
});

function displaySendMailAfterRegToAdminSection() {
	displayFields(".employees", $("input[name='send_mail_after_reg_to_admin']:checked").val() == 1);
}

function displayChooseCustomGroupToCustomerSection() {
	displayFields(".customGroupPosition", $("input[name='allow_choose_custom_group_to_customer']:checked").val() == 1);
	displayFields(".customGroupSelectType", $("input[name='allow_choose_custom_group_to_customer']:checked").val() == 1);
	displayFields(".groups", $("input[name='allow_choose_custom_group_to_customer']:checked").val() == 1);
	showAutoApprovedGroups();
}

function displayUploadFileSection() {
	displayFields(".upload_file_label", $("input[name='show_upload_button']:checked").val() == 1);
	displayFields(".upload_file_description", $("input[name='show_upload_button']:checked").val() == 1);
	displayFields("input[name='upload_file_required']", $("input[name='show_upload_button']:checked").val() == 1);
	displayFields(".upload_file_allowed_files", $("input[name='show_upload_button']:checked").val() == 1);
	displayFields(".upload_file_max_file_size", $("input[name='show_upload_button']:checked").val() == 1);
	displayFields(".uploadFilePosition:eq(0)", $("input[name='show_upload_button']:checked").val() == 1);
}

function showEmployyesAndGroupsLabels() {
	$(".employees").each(function(){
		$(this).parents('.checkbox:eq(0)').css("margin-left", "20px");
	});
	$(".employees:eq(0)").parents('.checkbox:eq(0)').css("clear", "both");
	$(".employees:eq(0)").parents('.checkbox:eq(0)').before("<span class='control-label' style='float: left; margin-bottom: 10px;'>" + translate.employeesTitle + ":</span>");
	
	$(".groups").each(function(){
		$(this).parents('.checkbox:eq(0)').css("margin-left", "20px");
	});
	$(".groups:eq(0)").parents('.checkbox:eq(0)').css("clear", "both");
	$(".groups:eq(0)").parents('.checkbox:eq(0)').before("<span class='control-label' style='float: left; margin-bottom: 10px;'>" + translate.groupsTitle + ":</span>");
	
	$(".autoApprovedGroups").each(function(){
		$(this).parents('.checkbox:eq(0)').css("margin-left", "20px");
	});
	$(".autoApprovedGroups:eq(0)").parents('.checkbox:eq(0)').css("clear", "both");
	$(".autoApprovedGroups:eq(0)").parents('.checkbox:eq(0)').before("<span class='control-label' style='float: left; margin-bottom: 10px;'>" + translate.autoApprovedGroupsTitle + ":</span>");
}

function showAutoApprovedGroups() {	
	if ($("input[name='approve_customer']:checked").val() == 1 && $("input[name='allow_choose_custom_group_to_customer']:checked").val() == 1) {
		checkedGroupsCount = 0;
		$('.groups').each(function() {
			if ($(this).prop('checked')) {
				checkedGroupsCount++;
				$('#auto_approve_' + $(this).attr('id')).parents('.checkbox:eq(0)').show();
			} else {
				$('#auto_approve_' + $(this).attr('id')).parents('.checkbox:eq(0)').hide();
				$('#auto_approve_' + $(this).attr('id')).prop('checked', false);
			}
		});
		if (checkedGroupsCount > 0) {
			displayFields(".autoApprovedGroups", true);
		} else {
			displayFields(".autoApprovedGroups", false);
		}
	} else {
		displayFields(".autoApprovedGroups", false);
	}
}

function displayFields(identification,isDisplay) {
	if(isDisplay) {
		$(identification).parents(".form-group").show();
	} else {
		$(identification).parents(".form-group").hide();
	}
}