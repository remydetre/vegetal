{*
 * 2014 - 2019 Watt Is It
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
 * @copyright 2014 - 2019 Watt Is It
 * @license   https://creativecommons.org/licenses/by-nd/4.0/fr/ Creative Commons BY-ND 4.0
 * @version   2.7.6
 *}

{if $color == 'green'} {$colorText='#25b373'} {else} {$colorText = $color } {/if}

<div id="block_paygreen_infos">
    <img src="{$imgdir|escape:'html':'UTF-8'}footer/lock-{$color|escape:'html':'UTF-8'}.png" />
    <span>{l s='Secured payment by' mod='paygreen'} : </span>

    <a href="{$backlink|escape:'html':'UTF-8'}" target="_blank" title="paygreen" >
        <img src="{$imgdir|escape:'html':'UTF-8'}footer/paygreen-{$color|escape:'html':'UTF-8'}.png"
        alt="{l s='Secured Payment with Paygreen' mod='paygreen'}" />
    </a>
</div>

<style type="text/css">
    #block_paygreen_infos {
        padding: 5px 0;
    }
    #block_paygreen_infos > img {
        max-height: 15px !important;
        margin-top:-5px;
    }
    #block_paygreen_infos > span {
        color:{$colorText|escape:'html':'UTF-8'};
        font-style:italic;
    }
    #block_paygreen_infos > a > img {
        display: block;
        max-width: 150px !important;
        height: auto;
        max-height: 50px !important;
        width: 100% !important;
    }
</style>
