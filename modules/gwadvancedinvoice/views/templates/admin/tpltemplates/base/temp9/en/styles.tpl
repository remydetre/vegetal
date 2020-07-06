{*
* Do not edit the file if you want to upgrade in future.
* 
* @author    Globo Software Solution JSC <contact@globosoftware.net>
* @copyright 2016 Globo ., Jsc
* @link	     http://www.globosoftware.net
* @license   please read license in file license.txt
*/
*}
{if !isset($bodytextcolor) || $bodytextcolor == ''}{assign var=bodytextcolor value="#000000"}{/if}
{if !isset($bodyblockground) || $bodyblockground == ''}{assign var=bodyblockground value="#ffffff"}{/if}
{if !isset($headingtextcolor) || $headingtextcolor == ''}{assign var=headingtextcolor value="#000000"}{/if}
{if !isset($bodytextfont) || $bodytextfont == ''}{assign var=bodytextfont value="helvetica"}{/if}
{if !isset($headingblockbackground) || $headingblockbackground == ''}{assign var=headingblockbackground value="#000000"}{/if}

{if !isset($color_border) || $color_border == ''}{assign var=color_border value="#345543"}{/if}
{if !isset($color_line_even)|| $color_line_even == ''}{assign var=color_line_even value="#FFFFFF"}{/if}
{if !isset($color_line_odd)|| $color_line_odd == ''}{assign var=color_line_odd value="#F9F9F9"}{/if}

{if !isset($font_size_text)|| $font_size_text == ''}{assign var=font_size_text value="9pt"}{/if}
{if !isset($font_size_header)|| $font_size_header == ''}{assign var=font_size_header value="9pt"}{/if}
{if !isset($font_size_product)|| $font_size_product == ''}{assign var=font_size_product value="9pt"}{/if}
{if !isset($color_product)|| $color_product == ''}{assign var=color_product value="#474241"}{/if}

{if !isset($font_shoptitle)|| $font_shoptitle == ''}{assign var=font_shoptitle value="helvetica"}{/if}
{if !isset($size_shoptitle)|| $size_shoptitle == ''}{assign var=size_shoptitle value="20pt"}{/if}
{if !isset($color_shoptitle)|| $color_shoptitle == ''}{assign var=color_shoptitle value="20pt"}{/if}

{if !isset($font_thank_for)|| $font_thank_for == ''}{assign var=font_thank_for value="helvetica"}{/if}
{if !isset($size_thank_for)|| $size_thank_for == ''}{assign var=size_thank_for value="15pt"}{/if}
{if !isset($color_thank_for)|| $color_thank_for == ''}{assign var=color_thank_for value="$000000"}{/if}

{if !isset($font_title)|| $font_title == ''}{assign var=font_title value=$bodytextfont}{/if}
{if !isset($size_title)|| $size_title == ''}{assign var=size_title value="8pt"}{/if}
{if !isset($color_title)|| $color_title == ''}{assign var=color_title value="#000000"}{/if}
{if !isset($color_strong)|| $color_strong == ''}{assign var=color_strong value="#000000"}{/if}

{if !isset($footer_background)|| $footer_background == ''}{assign var=footer_background value="#64625f"}{/if}
{if !isset($table_padding)|| $table_padding == ''}{assign var=table_padding value="0pt"}{/if}

<style>
    th{
        color:{$headingtextcolor|escape:'htmlall':'UTF-8'};
        background-color:{$headingblockbackground|escape:'htmlall':'UTF-8'};
    }
    .box_color{
        background-color:{$headingblockbackground|escape:'htmlall':'UTF-8'};
        color: {$headingtextcolor|escape:'htmlall':'UTF-8'};
    }
    .total_head{
        font-size:{$size_shoptitle|escape:'htmlall':'UTF-8'};
    }
	table, th, td {
		margin: 0!important;
		padding: 0!important;
		vertical-align: middle;
		font-size: {$font_size_text|escape:'htmlall':'UTF-8'};
        color:{$bodytextcolor|escape:'htmlall':'UTF-8'};
        font-family:{$bodytextfont|escape:'htmlall':'UTF-8'};
	}
    .color_title{
        color:{$color_title|escape:'htmlall':'UTF-8'};
    }
    .total_val{
        color:{$headingtextcolor|escape:'htmlall':'UTF-8'};
        background-color:{$color_title|escape:'htmlall':'UTF-8'};
    }
    .heading{
        color:{$bodytextcolor|escape:'htmlall':'UTF-8'};
    }
    .alignleft{
        text-align:left;
    }
    .aligncenter{
        text-align:center;
    }
    .alignright{
        text-align:right;
    }
	table.product {
		border: 1px solid {$color_border|escape:'htmlall':'UTF-8'};
		border-collapse: collapse;
	}
    
    .total_wp{
         background-color:{$headingblockbackground|escape:'htmlall':'UTF-8'};
    }

	table#addresses-tab tr td {
		font-size: large;
	}

	table#summary-tab {
		padding: {$table_padding|escape:'htmlall':'UTF-8'};
		border: 1pt solid {$color_border|escape:'htmlall':'UTF-8'};
	}
	table#total-tab {
		padding: {$table_padding|escape:'htmlall':'UTF-8'};
		border: 1pt solid {$color_border|escape:'htmlall':'UTF-8'};
	}
	table#tax-tab {
		padding: {$table_padding|escape:'htmlall':'UTF-8'};
		border: 1pt solid {$color_border|escape:'htmlall':'UTF-8'};
	}
	table#payment-tab {
		padding: {$table_padding|escape:'htmlall':'UTF-8'};
		border: 1px solid {$color_border|escape:'htmlall':'UTF-8'};
	}

	th.product {
		border-bottom: 1px solid {$color_border|escape:'htmlall':'UTF-8'};
	}

	tr.discount th.header {
		border-top: 1px solid {$color_border|escape:'htmlall':'UTF-8'};
	}

	tr.product td {
		border-bottom: 4px solid {$color_border|escape:'htmlall':'UTF-8'};
        border-left: 1px solid {$color_border|escape:'htmlall':'UTF-8'};
        border-right: 1px solid {$color_border|escape:'htmlall':'UTF-8'};
        color:{$color_product|escape:'htmlall':'UTF-8'};
	}
    .product_name{
        color:{$color_product|escape:'htmlall':'UTF-8'};
    }
	tr.color_line_even {
		background-color: {$color_line_even|escape:'htmlall':'UTF-8'};
	}

	tr.color_line_odd {
		background-color: {$color_line_odd|escape:'htmlall':'UTF-8'};
	}

	tr.customization_data td {
	}

	td.product {
		vertical-align: middle;
		font-size: {$font_size_product|escape:'htmlall':'UTF-8'};
	}

	th.header {
		font-size: {$font_size_header|escape:'htmlall':'UTF-8'};
		color:{$headingtextcolor|escape:'htmlall':'UTF-8'};
        background-color:{$headingblockbackground|escape:'htmlall':'UTF-8'};
		vertical-align: middle;
	}

	th.header-right {
		font-size: {$font_size_header|escape:'htmlall':'UTF-8'};
		color:{$headingtextcolor|escape:'htmlall':'UTF-8'};background-color:{$headingblockbackground|escape:'htmlall':'UTF-8'};
		vertical-align: middle;
		text-align: right;
		font-weight: bold;
	}

	th.payment {
		color:{$headingtextcolor|escape:'htmlall':'UTF-8'};background-color:{$headingblockbackground|escape:'htmlall':'UTF-8'};
		vertical-align: middle;
		font-weight: bold;
	}

	th.tva {
		color:{$headingtextcolor|escape:'htmlall':'UTF-8'};background-color:{$headingblockbackground|escape:'htmlall':'UTF-8'};
		vertical-align: middle;
		font-weight: bold;
	}

	tr.separator td {
		border-top: 1px solid #000000;
	}

	.left {
		text-align: left;
	}

	.fright {
		float: right;
	}

	.right {
		text-align: right;
	}

	.center {
		text-align: center;
	}

	.bold {
		font-weight: bold;
	}

	.border {
		border: 1px solid black;
	}

	.no_top_border {
		border-top:hidden;
		border-bottom:1px solid black;
		border-left:1px solid black;
		border-right:1px solid black;
	}

	.grey {
		color:{$headingtextcolor|escape:'htmlall':'UTF-8'};background-color:{$headingblockbackground|escape:'htmlall':'UTF-8'};

	}
	.white {
		background-color: #FFFFFF;
	}

	.big,
	tr.big td{
		font-size: 110%;
	}
	
	.small, table.small th, table.small td {
		font-size:small;   
	}
    
    .thanksfor{
        font-family:{$font_thank_for|escape:'htmlall':'UTF-8'};
        font-size: {$size_thank_for|escape:'htmlall':'UTF-8'};
        color:{$color_thank_for|escape:'htmlall':'UTF-8'};
    }
    .shop_title{
        font-family:{$font_shoptitle|escape:'htmlall':'UTF-8'};
        font-size: {$size_shoptitle|escape:'htmlall':'UTF-8'};
        color:{$color_shoptitle|escape:'htmlall':'UTF-8'};
        margin:0;
    }
    .header_title{
        font-family:{$font_title|escape:'htmlall':'UTF-8'};
        font-size: {$size_title|escape:'htmlall':'UTF-8'};
        color:{$color_title|escape:'htmlall':'UTF-8'};
    }
    .border_bottom{
        border-bottom: 1px solid {$color_title|escape:'htmlall':'UTF-8'};
    }
    .total_text{
        color:{$headingtextcolor|escape:'htmlall':'UTF-8'};
        background-color:{$headingblockbackground|escape:'htmlall':'UTF-8'};
    }
    .strong_item{
        color:{$color_strong|escape:'htmlall':'UTF-8'};
    }
    {if isset($custom_style)}
        {$custom_style|escape:'htmlall':'UTF-8'}
    {/if}
</style>