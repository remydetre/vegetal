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
<div class="row" id="bms_connect">
    <div class="col-lg-12">
        <div class="panel">
            <div class="panel-heading">
                <i class="icon-truck "></i>
                Boostmyshop Connect
            </div>
            <div>
                <div class="section-title panel-heading">
                    Products
                    <span class="badge">{count($products)}</span>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th><span class="title_box">Sku</span></th>
                            <th><span class="title_box">Name</span></th>
                            <th><span class="title_box">Qty Ordered</span></th>
                            <th><span class="title_box">Qty Reserved</span></th>
                            <th><span class="title_box">Qty Shipped</span></th>
                            <th><span class="title_box">Warehouse</span></th>
                        </tr>
                        </thead>
                        <tbody>
                        {foreach from=$products item=product}
                            <tr>
                                <td>{$product['sku']}</td>
                                <td>{$product['product_name']}</td>
                                <td style="text-align: left;padding-left: 2%;">{$product['qty_ordered']|string_format:"%d"}</td>
                                <td style="text-align: left;padding-left: 2%;">{$product['qty_reserved']|string_format:"%d"}</td>
                                <td style="text-align: left;padding-left: 2%;">{$product['qty_shipped']|string_format:"%d"}</td>
                                <td>{$product['w_name']}</td>
                            </tr>
                        {/foreach}
                        </tbody>
                    </table>
                </div>
            </div>
            <div style="padding-top: 3%;">
                <div class="section-title panel-heading">
                    Shipments
                    <span class="badge">{count($shipments)}</span>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th><span class="title_box">Date</span></th>
                            <th><span class="title_box">Shipment #</span></th>
                            <th><span class="title_box">Shipping Method</span></th>
                            <th><span class="title_box">Tracking Number</span></th>
                        </tr>
                        </thead>
                        <tbody>
                        {foreach from=$shipments item=shipment}
                            <tr>
                                <td>{$shipment['created_at']|date_format}</td>
                                <td>{$shipment['increment_id']}</td>
                                <td>{$shipment['shipping_method']}</td>
                                <td style="text-align: left;padding-left: 2%;">{$shipment['tracking_number']}</td>
                            </tr>
                        {/foreach}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
