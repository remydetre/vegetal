/*
* 2017 Singleton software
*
*  @author Singleton software <info@singleton-software.com>
*  @copyright 2017 Singleton software
*/
$(document).ready(function() {
	if (typeof linkToFile != 'undefined') {
		$("#customer_form .form-wrapper").after('<a href="' + linkToFile + '" target="_blank">' + fileTypeText + '</a>');
	}
});