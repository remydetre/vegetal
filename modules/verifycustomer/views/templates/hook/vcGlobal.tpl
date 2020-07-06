{*
* 2017 Singleton software
*
*  @author Singleton software <info@singleton-software.com>
*  @copyright 2017 Singleton software
*}
<script type="text/javascript">
//<![CDATA[
	{if !$psVersion17}
		var notShowPrice = '{$PS_CATALOG_MODE|escape:"javascript":"UTF-8"}';
		var pageName = '{$page_name|escape:"javascript":"UTF-8"}';
	{else}
		var notShowPrice = !Boolean({$configuration.show_prices|escape:"javascript":"UTF-8"});
		var pageName = '{$page.page_name|escape:"javascript":"UTF-8"}';
	{/if}
	var psVersion17 = Boolean({$psVersion17|escape:"javascript":"UTF-8"});
	var hidePriceConfig = '{$hidePriceConfig|escape:"javascript":"UTF-8" nofilter}';
	var langId = '{$langId|escape:"javascript":"UTF-8"}';
	var registrationLink = '{$link->getPageLink("authentication", true)|escape:"html":"UTF-8"}?create_account=1';
	var defaultCustomerGroup = '{$defaultCustomerGroup|escape:"javascript":"UTF-8"}';
	var accountHaveToBeApprove = '{$accountHaveToBeApprove|escape:"javascript":"UTF-8"}';
//]]>
</script>