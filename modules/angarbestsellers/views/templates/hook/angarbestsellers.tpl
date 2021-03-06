{*
* @author	Krzysztof Pecak
* @copyright	2017 Krzysztof Pecak
* @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}
{* AngarTheme *}
<section class="best-products tab-pane fade" id="angarbest">
  <div class="h1 products-section-title text-uppercase index_title">
    <a href="{$allBestSellers2}">{l s='Best Sellers' d='Shop.Theme.Catalog'}</a>
  </div>
  <div class="products">
    {foreach from=$products item="product"}
      {include file="catalog/_partials/miniatures/product.tpl" product=$product}
    {/foreach}
  </div>
  <a class="all-product-link float-xs-left float-md-right h4" href="{$allBestSellers2}">
    {l s='All best sellers' d='Shop.Theme.Catalog'}<i class="material-icons">&#xE315;</i>
  </a>
  <div class="clearfix"></div>
</section>