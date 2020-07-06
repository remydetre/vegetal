{**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    DPD France S.A.S. <support.ecommerce@dpd.fr>
 * @copyright 2018 DPD France S.A.S.
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *}

{literal}
<script type='text/javascript'>
    $(document).ready(function(){
        $('.page-title').prepend('<img src="../modules/dpdfrance/views/img/admin/admin.png"/>')
        $('.marquee').marquee({
            duration: 20000,
            gap: 50,
            delayBeforeStart: 0,
            direction: 'left',
            duplicated: true,
            pauseOnHover: true
        });
    $('a.popup').fancybox({             
            'hideOnContentClick': true,
            'padding'           : 0,
            'overlayColor'      :'#D3D3D3',
            'overlayOpacity'    : 0.7,
            'width'             : 1024,
            'height'            : 640,
            'type'              :'iframe'
            });
        jQuery.expr[':'].contains = function(a, i, m) { 
            return jQuery(a).text().toUpperCase().indexOf(m[3].toUpperCase()) >= 0; 
        };
    $("#tableFilter").keyup(function () {
        //split the current value of tableFilter
        var data = this.value.split(";");
        //create a jquery object of the rows
        var jo = $("#fbody").find("tr");
        if (this.value == "") {
            jo.show();
            return;
        }
        //hide all the rows
        jo.hide();

        //Recusively filter the jquery object to get results.
        jo.filter(function (i, v) {
            var t = $(this);
            for (var d = 0; d < data.length; ++d) {
                if (t.is(":contains('" + data[d] + "')")) {
                    return true;
                }
            }
            return false;
        })
        //show the rows that match.
        .show();
        }).focus(function () {
            this.value = "";
            $(this).css({
                "color": "black"
            });
            $(this).unbind('focus');
        }).css({
            "color": "#C0C0C0"
        });
    });
    function checkallboxes(ele) {
        var checkboxes = $("#fbody").find(".checkbox:visible");
        if (ele.checked) {
            for (var i = 0; i < checkboxes.length; i++) {
                if (checkboxes[i].type == 'checkbox') {
                    checkboxes[i].checked = true;
                }
            }
        } else {
            for (var i = 0; i < checkboxes.length; i++) {
                if (checkboxes[i].type == 'checkbox') {
                    checkboxes[i].checked = false;
                }
            }
        }
    }
</script>
{/literal}

{if !isset($stream.error) || (isset($stream.error) && !$stream.error)}
    <fieldset id="fieldset_rss"><legend><a href="javascript:void(0)" onclick="$(&quot;#zonemarquee&quot;).toggle(&quot;fast&quot;, function() {literal}{}{/literal});"><img src="../modules/dpdfrance/views/img/admin/rss_icon.png" />{l s='DPD News (show/hide)' mod='dpdfrance'}</a></legend>
    <div id="zonemarquee"><div id="marquee" class="marquee">
    {foreach from=$stream item=item key=key}
        <strong style="color:red;">{$item.category|escape:'htmlall':'UTF-8'} > {$item.title|escape:'htmlall':'UTF-8'} : </strong> {$item.description|escape:'htmlall':'UTF-8'} 
    {/foreach}
    </div></div></fieldset><br/><br/>
{/if}

{$msg|escape:'quotes':'UTF-8'}

<div id="fieldset_grid">
{if !isset($order_info.error) || (isset($order_info.error) && !$order_info.error)}
    <input id="tableFilter" placeholder="{l s='Search something, separate values with ; ' mod='dpdfrance'}"/><img id="filtericon" src="../modules/dpdfrance/views/img/admin/search.png"/><br/><br/>
        <form id="exportform" action="index.php?tab=AdminDPDFrance&token={$token|escape:'htmlall':'UTF-8'}" method="POST" enctype="multipart/form-data">
        <body><table>
                <thead>
                    <tr>
                        <th class="hcheckexport"><input type="checkbox" onchange="checkallboxes(this)"/></th>
                        <th class="hid">ID</th>
                        <th class="href">{l s='Reference' mod='dpdfrance'}</th>
                        <th class="hdate">{l s='Date of order' mod='dpdfrance'}</th>
                        <th class="hnom">{l s='Recipient' mod='dpdfrance'}</th>
                        <th class="htype">{l s='Service' mod='dpdfrance'}</th>
                        <th class="hpr">{l s='Destination' mod='dpdfrance'}</th>
                        <th class="hpoids">{l s='Weight' mod='dpdfrance'}</th>
                        <th colspan="2" class="hprix" align="right">{l s='Amount' mod='dpdfrance'}<br/><span style="font-size:10px;">{l s='(tick to insure<br/>this parcel)' mod='dpdfrance'}</span></th>
                        {if $dpdfrance_retour_option !== 0}<th class="hretour">{l s='Allow return' mod='dpdfrance'}</th>{/if}
                        <th class="hstatutcommande" align="center">{l s='Order status' mod='dpdfrance'}</th>
                        <th class="hstatutcolis" align="center">{l s='Parcel trace' mod='dpdfrance'}</th>
                    </tr>
                </thead><tbody id="fbody">

        {foreach from=$order_info item=order}
            <tr>
                <td><input class="checkbox" type="checkbox" name="checkbox[]" {$order.checked|escape:'htmlall':'UTF-8'} value="{$order.id|escape:'htmlall':'UTF-8'}"></td><td class="id">{$order.id|escape:'htmlall':'UTF-8'}</td>
                <td class="ref">{$order.reference|escape:'htmlall':'UTF-8'}</td>
                <td class="date">{$order.date|escape:'htmlall':'UTF-8'}</td>
                <td class="nom">{$order.nom|escape:'htmlall':'UTF-8'}</td>
                <td class="type">{$order.type|escape:'quotes':'UTF-8'}</td>
                <td class="pr">{$order.address|escape:'quotes':'UTF-8'}</td>
                <td class="poids"><input name="parcelweight[{$order.id|escape:'htmlall':'UTF-8'}]" type="text" value="{$order.poids|escape:'htmlall':'UTF-8'}" /> {$order.weightunit|escape:'htmlall':'UTF-8'}</td>
                <td class="prix" align="right">{$order.prix|escape:'htmlall':'UTF-8'}</td>
                <td class="advalorem"><input class="advalorem" type="checkbox" name="advalorem[]" {$order.advalorem_checked|escape:'htmlall':'UTF-8'} value="{$order.id|escape:'htmlall':'UTF-8'}"></td>
                {if $dpdfrance_retour_option !== 0}<td class="retour"><input class="retour" type="checkbox" name="retour[]" {$order.retour_checked|escape:'htmlall':'UTF-8'} value="{$order.id|escape:'htmlall':'UTF-8'}"></td>{/if}
                <td class="statutcommande" align="center">{$order.statut|escape:'quotes':'UTF-8'}</td>
                <td class="statutcolis" align="center"><a href="javascript:void(0)" onclick="window.open('http://www.dpd.fr/tracex_{$order.reference|escape:'htmlall':'UTF-8'}_{$order.depot_code|escape:'htmlall':'UTF-8'}{$order.shipper_code|escape:'htmlall':'UTF-8'}','','width=1024,height=768,top=30,left=20')">{$order.dernier_statut_colis|escape:'quotes':'UTF-8'}</a></td>
            </tr>
        {/foreach}
    </tbody></table>
    <p>
        <input type="submit" class="button" name="exportOrders" value="{l s='Export selected orders to DPD Station' mod='dpdfrance'}" />
        <input type="submit" class="button" name="updateShippedOrders" value="{l s='Update shipped orders' mod='dpdfrance'}" />
        <input type="submit" class="button" name="updateDeliveredOrders" value="{l s='Update delivered orders' mod='dpdfrance'}" />
    </p>
    </form></div>
{else}
    <div class="alert warn">{l s='There are no orders' mod='dpdfrance'}</div>
{/if}