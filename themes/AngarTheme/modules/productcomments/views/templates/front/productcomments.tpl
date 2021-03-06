{*
* 2007-2016 PrestaShop
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2016 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*
*  MODIFIED BY MYPRESTA.EU FOR PRESTASHOP 1.7 PURPOSES !
*
*}

{* AngarTheme *}

<div class="tab-pane fade in" id="productcomments" role="tabpanel">

<script type="text/javascript">
    var productcomments_controller_url = '{$productcomments_controller_url nofilter}';
    var confirm_report_message = '{l s='Are you sure that you want to report this comment?' mod='productcomments' js=1}';
    var secure_key = '{$secure_key}';
    var productcomments_url_rewrite = '{$productcomments_url_rewriting_activated}';
    var productcomment_added = '{l s='Your comment has been added!' mod='productcomments' js=1}';
    var productcomment_added_moderation = '{l s='Your comment has been submitted and will be available once approved by a moderator.' mod='productcomments' js=1}';
    var productcomment_title = '{l s='New comment' mod='productcomments' js=1}';
    var productcomment_ok = '{l s='OK' mod='productcomments' js=1}';
    var moderation_active = {$moderation_active};
</script>

<div id="productCommentsBlock">
    <div class="h5 text-uppercase index_title"><span>{l s='Reviews' mod='productcomments'}</span></div>
    <div class="tabs">

        <div id="new_comment_form_ok" class="alert alert-success" style="display:none;padding:15px 25px"></div>

        <div id="product_comments_block_tab">
            {if $comments}
                {foreach from=$comments item=comment}
                    {if $comment.content}
                        <div class="comment clearfix" itemprop="review" itemscope itemtype="http://schema.org/Review">
                            <div class="comment_author">
                                <span>{l s='Grade' mod='productcomments'}&nbsp</span>
                                <div class="star_content clearfix">
                                    {section name="i" start=0 loop=5 step=1}
                                        {if $comment.grade le $smarty.section.i.index}
                                            <div class="star"></div>
                                        {else}
                                            <div class="star star_on"></div>
                                        {/if}
                                    {/section}
                                </div>
                                <div class="comment_author_infos">
                                    <strong itemprop="author">{$comment.customer_name|escape:'html':'UTF-8'}</strong><br/>
                                    <em itemprop="datePublished" content="{dateFormat date=$comment.date_add|escape:'html':'UTF-8' full=0}">{dateFormat date=$comment.date_add|escape:'html':'UTF-8' full=0}</em>
                                </div>
                            </div>
                            <div class="comment_details">
                                <h4 class="title_block" itemprop="name">{$comment.title}</h4>
                                <p itemprop="description">{$comment.content|escape:'html':'UTF-8'|nl2br nofilter}</p>

{*
                                <ul>
                                    {if $comment.total_advice > 0}
                                        <li>{l s='%1$d out of %2$d people found this review useful.' sprintf=[$comment.total_useful,$comment.total_advice] mod='productcomments'}</li>
                                    {/if}
                                    {if $logged}
                                        {if !$comment.customer_advice}
                                            <li>{l s='Was this comment useful to you?' mod='productcomments'}
                                                <button class="usefulness_btn" data-is-usefull="1" data-id-product-comment="{$comment.id_product_comment}">{l s='yes' mod='productcomments'}</button>
                                                <button class="usefulness_btn" data-is-usefull="0" data-id-product-comment="{$comment.id_product_comment}">{l s='no' mod='productcomments'}</button>
                                            </li>
                                        {/if}
                                        {if !$comment.customer_report}
                                            <li><span class="report_btn" data-id-product-comment="{$comment.id_product_comment}">{l s='Report abuse' mod='productcomments'}</span></li>
                                        {/if}
                                    {/if}
                                </ul>
*}

                                {hook::exec('displayProductComment', $comment) nofilter}
                            </div>
                        </div>
                    {/if}
                {/foreach}
            {else}
                {if (!$too_early AND ($logged OR $allow_guests))}
                    <p class="align_center alert alert-info">
                        <a id="new_comment_tab_btn" class="open-comment-form" href="#new_comment_form">{l s='Be the first to write your review' mod='productcomments'} !</a>
                    </p>
                {else}
                    <p class="align_center">{l s='No customer reviews for the moment.' mod='productcomments'}</p>
                {/if}
            {/if}
        </div>

        <div class="clearfix comments_openform">
            {if ($too_early == false AND ($logged OR $allow_guests))}
                <a class="open-comment-form btn btn-primary" href="#new_comment_form">{l s='Write your review' mod='productcomments'}</a>
            {/if}
        </div>


    </div>

    {if isset($product) && $product}
        <!-- Fancybox -->
        <div style="display:none">
            <div id="new_comment_form">
                <form id="id_new_comment_form" action="#">
                    <h3 class="title">{l s='Write your review' mod='productcomments'}</h3>
                    {if isset($product) && $product}
                        <div class="product clearfix">
                            <div class="product_desc">
								<img src="{$productcomment_cover_image}" height="{$mediumSize.height}" width="{$mediumSize.width}" alt="{if isset($product->name)}{$product->name}{elseif isset($product.name)}{$product.name}{/if}" />
                                <p class="product_name"><strong>{if isset($product->name)}{$product->name}{elseif isset($product.name)}{$product.name}{/if}</strong></p>
                                {if isset($product->description_short)}{$product->description_short nofilter}{elseif isset($product.description_short)}{$product.description_short nofilter}{/if}
                            </div>
                        </div>
                    {/if}
                    <div class="new_comment_form_content">
                        <div class="h3">{l s='Write your review' mod='productcomments'}</div>
                        <div id="new_comment_form_error" class="error" style="display:none;padding:15px 25px">
                            <ul></ul>
                        </div>
                        {if $criterions|@count > 0}
                            <ul id="criterions_list">
                                {foreach from=$criterions item='criterion'}
                                    <li>
                                        <label>{$criterion.name|escape:'html':'UTF-8'}</label>
                                        <div class="star_content">
                                            <input class="star" type="radio" name="criterion[{$criterion.id_product_comment_criterion|round}]" value="1"/>
                                            <input class="star" type="radio" name="criterion[{$criterion.id_product_comment_criterion|round}]" value="2"/>
                                            <input class="star" type="radio" name="criterion[{$criterion.id_product_comment_criterion|round}]" value="3"/>
                                            <input class="star" type="radio" name="criterion[{$criterion.id_product_comment_criterion|round}]" value="4"/>
                                            <input class="star" type="radio" name="criterion[{$criterion.id_product_comment_criterion|round}]" value="5" checked="checked"/>
                                        </div>
                                        <div class="clearfix"></div>
                                    </li>
                                {/foreach}
                            </ul>
                        {/if}

                        {if $allow_guests == true && !$logged}
                            <label>{l s='Your name' mod='productcomments'}<sup class="required">*</sup></label>
                            <input id="commentCustomerName" name="customer_name" type="text" value=""/>
                        {/if}

                        <label for="comment_title">{l s='Title for your review' mod='productcomments'}<sup class="required">*</sup></label>
                        <input id="comment_title" name="title" type="text" value=""/>

                        <label for="content">{l s='Your review' mod='productcomments'}<sup class="required">*</sup></label>
                        <textarea id="content" name="content"></textarea>

                        <div id="new_comment_form_footer">
                            <input id="id_product_comment_send" name="id_product" type="hidden" value='{$id_product_comment_form}'/>
                            <p class="fr">
                                <button class="btn btn-primary" id="submitNewMessage" name="submitMessage" type="submit">{l s='Send' mod='productcomments'}</button>&nbsp;
                                {l s='or' mod='productcomments'}&nbsp;<a href="#" onclick="$.fancybox.close();">{l s='Cancel' mod='productcomments'}</a>
                            </p>
                            <p class="fl required"><sup>*</sup> {l s='Required fields' mod='productcomments'}</p>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </form><!-- /end new_comment_form_content -->
            </div>
        </div>
        <!-- End fancybox -->
    {/if}
</div>


</div>


