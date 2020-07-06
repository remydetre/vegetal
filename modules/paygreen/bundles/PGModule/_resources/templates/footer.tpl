{*
 * 2014 - 2020 Watt Is It
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons BY-ND 4.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://creativecommons.org/licenses/by-nd/4.0/fr/
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@paygreen.fr so we can send you a copy immediately.
 *
 * @author    PayGreen <contact@paygreen.fr>
 * @copyright 2014 - 2020 Watt Is It
 * @license   https://creativecommons.org/licenses/by-nd/4.0/fr/ Creative Commons BY-ND 4.0
 * @version   3.0.1
 *}

<section id="block_paygreen_infos" class="col-xs-12 col-sm-12 col-md-12 col-lg-4">
    {assign var="target" value="APPfrontoffice:footer/lock-{$color|escape:'html':'UTF-8'}.png"}
    <img src="{$target|picture}" />
    <span class="{$color}">{'frontoffice.footer.securized.short'|pgtrans} : </span>

    <a href="{$backlink|escape:'html':'UTF-8'}" target="_blank" title="paygreen" >
        {assign var="target" value="APPfrontoffice:footer/paygreen-{$color|escape:'html':'UTF-8'}.png"}
        <img src="{$target|picture}" alt="{'frontoffice.footer.securized.long'|pgtrans}" />
    </a>
</section>
