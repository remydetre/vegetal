{**
 * 2007-2019 boostmyshop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
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
 * @copyright 2007-2019 boostmyshop
 * International Registered Trademark & Property of PrestaShop SA
 *}
{*{include file=_PS_MODULE_DIR_ . 'bmserpcloud/views/views/css/bmserpcloud.css'}*}
{*_PS_MODULE_DIR_ . 'bmserpcloud/views/views/css/bmserpcloud.css';*}
<html>
    <head>
        <style>
            .tab {
                overflow: hidden;
                border: 1px solid #ccc;
                background-color: #f1f1f1;
            }
            .tab span.tablinks {
                background-color: inherit;
                float: left;
                border: none;
                outline: none;
                cursor: pointer;
                padding: 14px 16px;
                transition: 0.3s;
                font-size: small;
            }
            .tab span:hover {
                background-color: #ddd;
            }
            .tab span.active {
                background-color: #ccc;
            }
            .tabcontent {
                display: none;
                padding: 6px 12px;
                border: 1px solid #ccc;
                border-top: none;
            }
        </style>
    </head>
    <body>
        {if count($ws_data)>1}
            <div class="tab">
                {foreach from=$ws_data item=data}
                    <span class="tablinks {$data['product_name']|replace:' ':''}" onclick="openContent(event, '{$data['product_name']|replace:' ':''}')">{$data['product_name']}</span>
                {/foreach}
            </div>
        {/if}

        {foreach from=$ws_data key=datakey item=data}
            <div class="row tabcontent" id="{$data['product_name']|replace:' ':''}">
                <div class="col-md-12">
                    <section>
                        <h2>{l s='Stocks' d='Stocks'}</h2>
                        <table class="table table-condensed table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Warehouse</th>
                                    <th>On Hand</th>
                                    <th>To ship</th>
                                    <th>Reserved</th>
                                    <th>Available</th>
                                </tr>
                            </thead>
                            <tbody>
                            {foreach from=$data['stocks'] item=stock}
                                <tr>
                                    <td>{$stock['w_name']}</td>
                                    <td style="text-align: left;padding-left: 3%;">{$stock['wi_physical_quantity']}</td>
                                    <td style="text-align: left;padding-left: 3%;">{$stock['wi_quantity_to_ship']}</td>
                                    <td style="text-align: left;padding-left: 3%;">{$stock['wi_reserved_quantity']}</td>
                                    <td style="text-align: left;padding-left: 3%;">{$stock['wi_available_quantity']}</td>
                                </tr>
                            {/foreach}
                            </tbody>
                        </table>
                    </section>
                    <section>
                        <h2>{l s='Expected purchase orders' mod='bmserpcloud'}</h2>
                        <table class="table table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Supplier</th>
                                    <th>ETA</th>
                                    <th>PO#</th>
                                    <th>Status</th>
                                    <th>Qty ordered</th>
                                    <th>Qty received</th>
                                    <th>Buying price</th>
                                    <th>Buying price with costs</th>
                                    <th>Warehouse</th>
                                </tr>
                            </thead>
                            <tbody>
                            {foreach from=$data['purchase_orders'] key=key item=purchaseOrder}
                                <tr>
                                    <td>{$purchaseOrder['po_created_at']|date_format}</td>
                                    <td>{$purchaseOrder['sup_name']}</td>
                                    <td>{$purchaseOrder['po_eta']|date_format}</td>
                                    <td>{$purchaseOrder['po_reference']}</td>
                                    <td>{$purchaseOrder['po_status']}</td>
                                    <td align="center">{$purchaseOrder['pop_qty']}</td>
                                    <td align="center">{$purchaseOrder['pop_qty_received']}</td>
                                    <td align="center">{$purchaseOrder['pop_price']|string_format:"%.2f"}</td>
                                    <td align="center">{$purchaseOrder['pop_price_with_cost_base']|string_format:"%.2f"}</td>
                                    <td align="center">{$purchaseOrder['po_warehouse']}</td>
                                </tr>
                            {/foreach}
                            </tbody>
                        </table>
                    </section>
                    <section>
                        <h2>{l s='Orders to ship' mod='bmserpcloud'}</h2>
                        <table class="table table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Order</th>
                                    <th>Status</th>
                                    <th>Customer</th>
                                    <th>Qty to ship</th>
                                    <th>Qty reserved</th>
                                    <th>Shipping warehouse</th>
                                </tr>
                            </thead>
                            <tbody>
                            {foreach from=$data['orders_to_ship'] key=key item=orderstoship}
                                <tr>
                                    <td>{$orderstoship['created_at']}</td>
                                    <td>{$orderstoship['order_increment_id']}</td>
                                    <td>{$orderstoship['order_status']}</td>
                                    <td>{$orderstoship['order_customer_name']}</td>
                                    <td style="text-align: left;padding-left: 3%;">{$orderstoship['qty_to_ship']}</td>
                                    <td style="text-align: left;padding-left: 3%;">{$orderstoship['esfoi_qty_reserved']}</td>
                                    <td>{$orderstoship['esfoi_warehouse']}</td>
                                </tr>
                            {/foreach}
                            </tbody>
                        </table>
                    </section>
                    <section>
                        <h2>{l s='Associated supplier' mod='bmserpcloud'}</h2>
                        <table class="table table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Supplier</th>
                                    <th>Supplier Sku</th>
                                    <th>buying price</th>
                                    <th>MOQ</th>
                                    <th>Pack</th>
                                    <th>Is primary</th>
                                </tr>
                            </thead>
                            <tbody>
                            {foreach from=$data['suppliers'] key=key item=supplier}
                                <tr>
                                    <td>{$supplier['sup_name']}</td>
                                    <td>{$supplier['sup_sku']}</td>
                                    <td >{$supplier['sup_buying_price']}</td>
                                    <td style="text-align: left;padding-left: 3%;">{$supplier['sup_moq']}</td>
                                    <td style="text-align: left;padding-left: 3%;">{$supplier['sup_pack']}</td>
                                    <td>{$supplier['sup_primary']}</td>
                                </tr>
                            {/foreach}
                            </tbody>
                        </table>
                    </section>
                </div>
            </div>
        {/foreach}
        <script>
            $( document ).ready(function() {
                console.log( "document loaded" );
                $('.tablinks').first().addClass('active');
                $('.tabcontent').first().show();
            });
            function openContent(evt, productName) {
                $( ".tabcontent" ).each(function( index ) {
                    $(this).hide();
                });
                $( ".tablinks" ).each(function( index ) {
                    $(this).removeClass("active");
                });
                $('.'+ productName).addClass("active");
                $("#"+ productName).show();
                }
        </script>
    </body>
</html>