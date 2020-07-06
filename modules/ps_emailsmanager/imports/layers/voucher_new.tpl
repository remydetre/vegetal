<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
  <title>${{ lang.message_from_shop_name }}$</title>
  <!--[if !mso]><!-- -->
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <!--<![endif]-->
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style type="text/css">
  #outlook a { padding: 0; }
  .ReadMsgBody { width: 100%; }
  .ExternalClass { width: 100%; }
  .ExternalClass * { line-height:100%; }
  body { margin: 0; padding: 0; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
  table, td { border-collapse:collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
  img { border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; }
  p { display: block; margin: 13px 0; }
</style>
<!--[if !mso]><!-->
<style type="text/css">
  @media only screen and (max-width:480px) {
    @-ms-viewport { width:320px; }
    @viewport { width:320px; }
  }
</style>
<!--<![endif]-->
<!--[if mso]>
<xml>
  <o:OfficeDocumentSettings>
    <o:AllowPNG/>
    <o:PixelsPerInch>96</o:PixelsPerInch>
  </o:OfficeDocumentSettings>
</xml>
<![endif]-->
<!--[if lte mso 11]>
<style type="text/css">
  .outlook-group-fix {
    width:100% !important;
  }
</style>
<![endif]-->

<!--[if !mso]><!-->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700" rel="stylesheet" type="text/css">
<link href="https://fonts.googleapis.com/css?family=Ubuntu:300,400,500,700" rel="stylesheet" type="text/css">
    <style type="text/css">

        @import url(https://fonts.googleapis.com/css?family=Roboto:300,400,500,700);
  @import url(https://fonts.googleapis.com/css?family=Ubuntu:300,400,500,700);

    </style>
  <!--<![endif]--><style type="text/css">
  @media only screen and (min-width:480px) {
    .mj-column-per-100 { width:100%!important; }
  }
</style>
<style>/**** Global ****/

div a, td a{color: {{$link_color}};}
div span {}
div strong {color: {{$strong_color}};}

.mj-inline-links a, .mj-inline-links a span{color: #ffffff !important;}

/**** Download Product ****/

ul{font-size: 14px; margin: 0; padding: 0 0 0 25px}
ul li{padding-bottom: 10px}
ul li:last-child{padding-bottom: 0}
ul li a{display: block;}

/**** Order Conf ***/
th{font-weight: bold;word-break: normal;color: {{$title_color}} !important; width: 20% !important; /*font-family: Arial !important;*/}

table.table-list{font-family: Arial !important;}
table.table-list tr{}
table.table-list tr td{width: 20% !important; color: {{$font_color}} !important; padding-left: 10px !important;}
table.table-list tr td:first-child{text-align: left !important;}
table.table-list tr td:last-child{text-align: right !important;padding:7px !important;}
table.table-list tr td:last-child p, table.table-recap td:last-child{color: {{$font_color}};}
table.table-list tr td:last-child p, table.table-recap td:last-child strong{color: {{$font_color}};font-weight:bold;}

table.table-list tr.conf_body td{border: none !important; color: {{$font_color}} !important;}


/**** New Order conf responsive table ****/

table.table-recap-resp {font-family: Roboto, Helvetica, Arial, sans-serif !important; width: 100%; font-size: 14px;}
table.table-recap-resp th {color: {{$title_color}} !important; text-align: left;}
table.table-recap-resp th:last-child, table.table-recap-resp td:last-child, .resume td:first-child {text-align: right; padding-right: 0px !important;}
table.table-recap-resp td {color: {{$font_color}} !important; padding: 10px 10px 10px 0px!important;}
@media screen and (min-width: 481px) {
  table.table-recap-resp tr.resume td {padding-top: 5px !important; padding-bottom: 5px !important;}
}
@media screen and (max-width: 480px) {
{{if !empty($order_conf_resp_mob) && $order_conf_resp_mob == 'yes'}}
table.table-recap-resp thead {/*display: none;*/}
table.table-recap-resp thead tr {display: block;}
table.table-recap-resp thead tr th {display: block; text-align: center !important; width: 100% !important;}
  table.table-recap-resp tr td.mob-high {font-size: 16px}
  table.table-recap-resp tbody tr {display: block; padding: 10px 0px 15px 0px !important;}
  table.table-recap-resp tbody tr.resume {padding: 0px !important;}
  table.table-recap-resp tbody td {display: block;  text-align:center; padding: 3px 0px !important;width: 100% !important;}
  table.table-recap-resp tbody td:last-child, .resume td:first-child {padding-right: 10px !important; }
  table.table-recap-resp tbody td:before {
      /*content: attr(data-th);
      display: block;
      text-align:center;
      font-weight: bold;
      color: {{$title_color}} !important;*/
  }
  table.table-recap-resp th:last-child, table.table-recap-resp td:last-child {text-align: center;}
  .resume td:first-child {text-align:center; font-weight: bold; padding-bottom:0px !important;}
  .resume td:last-child {padding-top:0px !important;}
  .order-total {}
{{/if}}
}


/**** Media Query ****/

@media only screen and (max-width: 768px) {
	/*.mj-inline-links a{
		display: block !important;
		padding: 15px 10px 5px !important;
	}
	.mj-inline-links a span{margin-right: 0 !important}*/
}

@media only screen and (min-width: 301px) and (max-width: 500px) {
		.table-recap tr th{font-size: 10px !important}
		.table-recap tr td{font-size: 12px !important}
	}
@media only screen and (max-device-width: 480px) {
	.table tr th{text-align:center!important; padding: 15px 10px !important}
	.table td, .table th{width: auto!important;}
	.table table, .table thead, .table tbody, .table th, .table td, .table tr {
	}
	table.table-list tr td:first-child{text-align: left !important;}
	table.table-list tr td:last-child{text-align: right !important;}
	}
@media only screen and (max-width: 300px){
	.table{width: 200px !important; margin: auto;}
	.table td{text-align:center !important;}
	.table tr td{font-size: 12px !important}
	.table table, .table thead, .table tbody, .table th, .table td, .table tr {
		display: block !important;
	}
	table.table-list tr td {width: 100% !important}
	table.table-list tr td:first-child{text-align: center !important;}
	table.table-list tr td:last-child{text-align: center !important;}
}


.fallback-text {
font-family: Arial, sans-serif;
}
</style></head>
<body style="background: {{$body_color}};">
  <div style="background-color:{{$body_color}};"><!--[if mso | IE]>
      <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="650" align="center" style="width:650px;">
        <tr>
          <td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;">
      <![endif]--><div style="margin:0px auto;max-width:650px;background:{{$section_back_color}};"><table role="presentation" cellpadding="0" cellspacing="0" style="font-size:0px;width:100%;background:{{$section_back_color}};" align="center" border="0"><tbody><tr><td style="text-align:center;vertical-align:top;border:1px solid {{$body_border_color}};direction:ltr;font-size:0px;padding:0px;"><!--[if mso | IE]>
      <table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td style="width:650px;">
      <![endif]--><div style="margin:0px auto;max-width:650px;background:{{$logo_back_color}};"><table role="presentation" cellpadding="0" cellspacing="0" style="font-size:0px;width:100%;background:{{$logo_back_color}};" align="center" border="0"><tbody><tr><td style="text-align:center;vertical-align:top;border-bottom:15px solid {{$sub_logo_back_color}};direction:ltr;font-size:0px;padding:20px 20px;"><!--[if mso | IE]>
      <table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td style="vertical-align:bottom;width:650px;">
      <![endif]--><div class="mj-column-per-100 outlook-group-fix" style="vertical-align:bottom;display:inline-block;direction:ltr;font-size:13px;text-align:left;width:100%;"><table role="presentation" cellpadding="0" cellspacing="0" style="vertical-align:bottom;" width="100%" border="0"><tbody><tr><td style="word-wrap:break-word;font-size:0px;padding:10px 25px;" align="left"><table cellpadding="0" cellspacing="0" style="cellspacing:0px;color:#000;font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:13px;line-height:inherit;table-layout:auto;" width="100%" border="0"><tr>
        <td align="center">
          <a href="{shop_url}" target="_blank">
            <img alt="{shop_name}" title="{shop_name}" height="auto" src="{shop_logo}" style="border:none;border-radius:;display:block;outline:none;text-decoration:none;height:auto;max-width:350px;"></a>
          </td>
        </tr></table></td></tr></tbody></table></div><!--[if mso | IE]>
      </td></tr></table>
      <![endif]--></td></tr></tbody></table></div><!--[if mso | IE]>
      </td></tr><tr><td style="width:650px;">
      <![endif]--><div style="margin:0px auto;max-width:650px;"><table role="presentation" cellpadding="0" cellspacing="0" style="font-size:0px;width:100%;" align="center" border="0"><tbody><tr><td style="text-align:center;vertical-align:top;direction:ltr;font-size:0px;padding:20px 30px;"><!--[if mso | IE]>
      <table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td style="vertical-align:top;width:650px;">
      <![endif]--><div class="mj-column-per-100 outlook-group-fix" style="vertical-align:top;display:inline-block;direction:ltr;font-size:13px;text-align:left;width:100%;"><table role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0"><tbody>{{if !empty($icon_size) && $icon_size > 0}}{{if !empty($icon_color) && $icon_color == 'light'}}<tr><td style="word-wrap:break-word;font-size:0px;padding:5px;" align="center"><table role="presentation" cellpadding="0" cellspacing="0" style="border-collapse:collapse;border-spacing:0px;" align="center" border="0"><tbody><tr><td style="width:{{$icon_size}}px;"><img alt="${{ lang.hi_firstname_lastname }}$" title="" height="auto" src="{{$mails_img_url}}cash_l.png" style="border:none;border-radius:0px;display:block;outline:none;text-decoration:none;width:100%;height:auto;" width="{{$icon_size}}"></td></tr></tbody></table></td></tr>{{else}}<tr><td style="word-wrap:break-word;font-size:0px;padding:5px;" align="center"><table role="presentation" cellpadding="0" cellspacing="0" style="border-collapse:collapse;border-spacing:0px;" align="center" border="0"><tbody><tr><td style="width:{{$icon_size}}px;"><img alt="${{ lang.hi_firstname_lastname }}$" title="" height="auto" src="{{$mails_img_url}}cash.png" style="border:none;border-radius:0px;display:block;outline:none;text-decoration:none;width:100%;height:auto;" width="{{$icon_size}}"></td></tr></tbody></table></td></tr>{{/if}}{{/if}}</tbody></table></div><!--[if mso | IE]>
      </td></tr></table>
      <![endif]--></td></tr></tbody></table></div><!--[if mso | IE]>
      </td></tr><tr><td style="width:650px;">
      <![endif]--><div style="margin:0px auto;max-width:650px;"><table role="presentation" cellpadding="0" cellspacing="0" style="font-size:0px;width:100%;" align="center" border="0"><tbody><tr><td style="text-align:center;vertical-align:top;direction:ltr;font-size:0px;padding:0px 20px 20px 20px;"><!--[if mso | IE]>
      <table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td style="vertical-align:top;width:650px;">
      <![endif]--><div class="mj-column-per-100 outlook-group-fix" style="vertical-align:top;display:inline-block;direction:ltr;font-size:13px;text-align:left;width:100%;"><table role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0"><tbody><tr><td style="word-wrap:break-word;font-size:0px;padding:0px 15px 25px 15px;" align="center"><div class="" style="cursor:auto;color:{{$title_color}};font-family:Roboto, Helvetica, Arial, sans-serif;font-size:30px;font-weight:700;line-height:30px;text-align:center;">${{ lang.hi_firstname_lastname }}$</div></td></tr></tbody></table></div><!--[if mso | IE]>
      </td></tr></table>
      <![endif]--></td></tr></tbody></table></div><!--[if mso | IE]>
      </td></tr><tr><td style="width:650px;">
      <![endif]--><p style="font-size:1px;margin:0px auto;border-top:2px solid {{$divider_color}};width:50%;"></p><!--[if mso | IE]><table role="presentation" align="center" border="0" cellpadding="0" cellspacing="0" style="font-size:1px;margin:0px auto;border-top:2px solid {{$divider_color}};width:50%;" width="325"><tr><td style="height:0;line-height:0;"> </td></tr></table><![endif]--><!--[if mso | IE]>
      </td></tr><tr><td style="width:650px;">
      <![endif]--><div style="margin:0px auto;max-width:650px;"><table role="presentation" cellpadding="0" cellspacing="0" style="font-size:0px;width:100%;" align="center" border="0"><tbody><tr><td style="text-align:center;vertical-align:top;direction:ltr;font-size:0px;padding:20px 30px;"><!--[if mso | IE]>
      <table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td style="vertical-align:top;width:650px;">
      <![endif]--><div class="mj-column-per-100 outlook-group-fix" style="vertical-align:top;display:inline-block;direction:ltr;font-size:13px;text-align:left;width:100%;"><table role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0"><tbody><tr><td style="word-wrap:break-word;font-size:0px;padding:15px;" align="left"><div class="" style="cursor:auto;color:{{$title_color}};font-family:Roboto, Helvetica, Arial, sans-serif;font-size:16px;font-weight:700;line-height:22px;text-align:left;">${{ lang.this_is_to_inform_you_about_the_creation_of_a_voucher }}$</div></td></tr><tr><td style="word-wrap:break-word;font-size:0px;padding:2px 15px;" align="left"><div class="" style="cursor:auto;color:{{$font_color}};font-family:Roboto, Helvetica, Arial, sans-serif;font-size:14px;line-height:22px;text-align:left;">${{ lang.here_is_the_code_of_your_voucher }}$<strong>{voucher_num}</strong></div></td></tr><tr><td style="word-wrap:break-word;font-size:0px;"><div style="font-size:1px;line-height:20px;white-space:nowrap;"> </div></td></tr><tr><td style="word-wrap:break-word;font-size:0px;padding:2px 15px;" align="left"><div class="" style="cursor:auto;color:{{$font_color}};font-family:Roboto, Helvetica, Arial, sans-serif;font-size:14px;line-height:22px;text-align:left;">${{ lang.simply_copypaste_this_code_during_the_payment_process_for_your_next_order }}$</div></td></tr></tbody></table></div><!--[if mso | IE]>
      </td></tr></table>
      <![endif]--></td></tr></tbody></table></div><!--[if mso | IE]>
      </td></tr><tr><td style="width:650px;">
      <![endif]--><div style="margin:0px auto;max-width:650px;background:{{$sl_back_color}};"><table role="presentation" cellpadding="0" cellspacing="0" style="font-size:0px;width:100%;background:{{$sl_back_color}};" align="center" border="0"><tbody><tr><td style="text-align:center;vertical-align:top;border-bottom:15px solid {{$sub_sl_back_color}};direction:ltr;font-size:0px;padding:20px 0px 15px 0px;"><!--[if mso | IE]>
      <table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td style="vertical-align:top;width:650px;">
      <![endif]--><div class="mj-column-per-100 outlook-group-fix" style="vertical-align:top;display:inline-block;direction:ltr;font-size:13px;text-align:left;width:100%;"><table role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0"><tbody><tr><td style="word-wrap:break-word;font-size:0px;" align="center"><div><div class="mj-inline-links" style="width:100%;text-align:center;"><!--[if gte mso 9]>
          <table role="presentation" border="0" cellpadding="0" cellspacing="0" align="center">
            <tr>
        <![endif]-->{{if $facebook_url}}<!--[if gte mso 9]>
          <td style="padding: 0">
        <![endif]--><a href="{{$facebook_url}}" style="display:inline-block;text-decoration:none;text-transform:uppercase;color:#000000;font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:13px;font-weight:normal;line-height:22px;padding:0px;"><img src="{{$mails_img_url}}sc_fb.png" alt="Link Facebook" width="64" height="64"></a><!--[if gte mso 9]>
          </td>
        <![endif]-->{{/if}}{{if $twitter_url}}<!--[if gte mso 9]>
          <td style="padding: 0">
        <![endif]--><a href="{{$twitter_url}}" style="display:inline-block;text-decoration:none;text-transform:uppercase;color:#000000;font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:13px;font-weight:normal;line-height:22px;padding:0px;"><img src="{{$mails_img_url}}sc_tt.png" alt="Link Twitter" width="64" height="64"></a><!--[if gte mso 9]>
          </td>
        <![endif]-->{{/if}}{{if $google_url}}<!--[if gte mso 9]>
          <td style="padding: 0">
        <![endif]--><a href="{{$google_url}}" style="display:inline-block;text-decoration:none;text-transform:uppercase;color:#000000;font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:13px;font-weight:normal;line-height:22px;padding:0px;"><img src="{{$mails_img_url}}sc_gp.png" alt="Link Google Plus" width="64" height="64"></a><!--[if gte mso 9]>
          </td>
        <![endif]-->{{/if}}{{if $youtube_url}}<!--[if gte mso 9]>
          <td style="padding: 0">
        <![endif]--><a href="{{$youtube_url}}" style="display:inline-block;text-decoration:none;text-transform:uppercase;color:#000000;font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:13px;font-weight:normal;line-height:22px;padding:0px;"><img src="{{$mails_img_url}}sc_yt.png" alt="Link Youtube" width="64" height="64"></a><!--[if gte mso 9]>
          </td>
        <![endif]-->{{/if}}{{if $vimeo_url}}<!--[if gte mso 9]>
          <td style="padding: 0">
        <![endif]--><a href="{{$vimeo_url}}" style="display:inline-block;text-decoration:none;text-transform:uppercase;color:#000000;font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:13px;font-weight:normal;line-height:22px;padding:0px;"><img src="{{$mails_img_url}}sc_vm.png" alt="Link Vimeo" width="64" height="64"></a><!--[if gte mso 9]>
          </td>
        <![endif]-->{{/if}}{{if $instagram_url}}<!--[if gte mso 9]>
          <td style="padding: 0">
        <![endif]--><a href="{{$instagram_url}}" style="display:inline-block;text-decoration:none;text-transform:uppercase;color:#000000;font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:13px;font-weight:normal;line-height:22px;padding:0px;"><img src="{{$mails_img_url}}sc_in.png" alt="Link Instagram" width="64" height="64"></a><!--[if gte mso 9]>
          </td>
        <![endif]-->{{/if}}{{if $pinterest_url}}<!--[if gte mso 9]>
          <td style="padding: 0">
        <![endif]--><a href="{{$pinterest_url}}" style="display:inline-block;text-decoration:none;text-transform:uppercase;color:#000000;font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:13px;font-weight:normal;line-height:22px;padding:0px;"><img src="{{$mails_img_url}}sc_pt.png" alt="Link Pinterest" width="64" height="64"></a><!--[if gte mso 9]>
          </td>
        <![endif]-->{{/if}}{{if $linkedin_url}}<!--[if gte mso 9]>
          <td style="padding: 0">
        <![endif]--><a href="{{$linkedin_url}}" style="display:inline-block;text-decoration:none;text-transform:uppercase;color:#000000;font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:13px;font-weight:normal;line-height:22px;padding:0px;"><img src="{{$mails_img_url}}sc_lk.png" alt="Link Linkedin" width="64" height="64"></a><!--[if gte mso 9]>
          </td>
        <![endif]-->{{/if}}<!--[if gte mso 9]>
            </tr>
          </table>
        <![endif]--></div></div></td></tr></tbody></table></div><!--[if mso | IE]>
      </td></tr></table>
      <![endif]--></td></tr></tbody></table></div><!--[if mso | IE]>
      </td></tr><tr><td style="width:650px;">
      <![endif]-->{{if !empty($display_shop_details) && $display_shop_details == 'yes'}}<div style="margin:0px auto;max-width:650px;"><table role="presentation" cellpadding="0" cellspacing="0" style="font-size:0px;width:100%;" align="center" border="0"><tbody><tr><td style="text-align:center;vertical-align:top;direction:ltr;font-size:0px;padding:20px 30px;"><!--[if mso | IE]>
      <table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td style="vertical-align:top;width:650px;">
      <![endif]--><div class="mj-column-per-100 outlook-group-fix" style="vertical-align:top;display:inline-block;direction:ltr;font-size:13px;text-align:left;width:100%;"><table role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0"><tbody><tr><td style="word-wrap:break-word;font-size:0px;padding:0px 0px 10px 0px;" align="center"><div class="" style="cursor:auto;color:{{$title_color}};font-family:Roboto, Helvetica, Arial, sans-serif;font-size:14px;font-weight:700;line-height:22px;text-align:center;">{shop_name}</div></td></tr><tr><td style="word-wrap:break-word;font-size:0px;padding:0px;" align="center"><div class="" style="cursor:auto;color:{{$font_color}};font-family:Roboto, Helvetica, Arial, sans-serif;font-size:14px;line-height:22px;text-align:center;">{{$shop_addr1}} {{$shop_addr2}}</div></td></tr><tr><td style="word-wrap:break-word;font-size:0px;padding:0px 0px 10px 0px;" align="center"><div class="" style="cursor:auto;color:{{$font_color}};font-family:Roboto, Helvetica, Arial, sans-serif;font-size:14px;line-height:22px;text-align:center;">{{$shop_zipcode}} {{$shop_city}} {{$shop_country}}</div></td></tr>{{if $shop_phone}}<tr><td style="word-wrap:break-word;font-size:0px;padding:0px;" align="center"><div class="" style="cursor:auto;color:{{$font_color}};font-family:Roboto, Helvetica, Arial, sans-serif;font-size:14px;line-height:22px;text-align:center;">{{$shop_phone}}</div></td></tr>{{/if}}{{if $shop_fax}}<tr><td style="word-wrap:break-word;font-size:0px;padding:0px;" align="center"><div class="" style="cursor:auto;color:{{$font_color}};font-family:Roboto, Helvetica, Arial, sans-serif;font-size:14px;line-height:22px;text-align:center;">{{$shop_fax}}</div></td></tr>{{/if}}{{if isset($shop_details)}}<tr><td style="word-wrap:break-word;font-size:0px;padding:10px 0px 0px 0px;" align="center"><div class="" style="cursor:auto;color:{{$font_color}};font-family:Roboto, Helvetica, Arial, sans-serif;font-size:14px;line-height:22px;text-align:center;">{{$shop_details}}</div></td></tr>{{/if}}</tbody></table></div><!--[if mso | IE]>
      </td></tr></table>
      <![endif]--></td></tr></tbody></table></div><!--[if mso | IE]>
      </td></tr><tr><td style="width:650px;">
      <![endif]-->{{/if}}{{if $custom_text}}{{if !empty($display_shop_details) && $display_shop_details == 'yes'}}<p style="font-size:1px;margin:0px auto;border-top:2px solid {{$divider_color}};width:50%;"></p><!--[if mso | IE]><table role="presentation" align="center" border="0" cellpadding="0" cellspacing="0" style="font-size:1px;margin:0px auto;border-top:2px solid {{$divider_color}};width:50%;" width="325"><tr><td style="height:0;line-height:0;"> </td></tr></table><![endif]--><!--[if mso | IE]>
      </td></tr><tr><td style="width:650px;">
      <![endif]-->{{/if}}<div style="margin:0px auto;max-width:650px;"><table role="presentation" cellpadding="0" cellspacing="0" style="font-size:0px;width:100%;" align="center" border="0"><tbody><tr><td style="text-align:center;vertical-align:top;direction:ltr;font-size:0px;padding:20px 30px;"><!--[if mso | IE]>
      <table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td style="vertical-align:top;width:650px;">
      <![endif]--><div class="mj-column-per-100 outlook-group-fix" style="vertical-align:top;display:inline-block;direction:ltr;font-size:13px;text-align:left;width:100%;"><table role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0"><tbody><tr><td style="word-wrap:break-word;font-size:0px;padding:0px;" align="center"><div class="" style="cursor:auto;color:{{$font_color}};font-family:Roboto, Helvetica, Arial, sans-serif;font-size:14px;line-height:22px;text-align:center;">{{$custom_text|nl2br}}</div></td></tr></tbody></table></div><!--[if mso | IE]>
      </td></tr></table>
      <![endif]--></td></tr></tbody></table></div><!--[if mso | IE]>
      </td></tr><tr><td style="width:650px;">
      <![endif]-->{{/if}}{{if !empty($display_copy) && $display_copy == 'yes'}}{{if (!empty($display_shop_details) && $display_shop_details == 'yes') || $custom_text}}<p style="font-size:1px;margin:0px auto;border-top:2px solid {{$divider_color}};width:50%;"></p><!--[if mso | IE]><table role="presentation" align="center" border="0" cellpadding="0" cellspacing="0" style="font-size:1px;margin:0px auto;border-top:2px solid {{$divider_color}};width:50%;" width="325"><tr><td style="height:0;line-height:0;"> </td></tr></table><![endif]--><!--[if mso | IE]>
      </td></tr><tr><td style="width:650px;">
      <![endif]-->{{/if}}<div style="margin:0px auto;max-width:650px;background:none;"><table role="presentation" cellpadding="0" cellspacing="0" style="font-size:0px;width:100%;background:none;" align="center" border="0"><tbody><tr><td style="text-align:center;vertical-align:top;direction:ltr;font-size:0px;padding:10px;"><!--[if mso | IE]>
      <table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td style="vertical-align:top;width:650px;">
      <![endif]--><div class="mj-column-per-100 outlook-group-fix" style="vertical-align:top;display:inline-block;direction:ltr;font-size:13px;text-align:left;width:100%;"><table role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0"><tbody><tr><td style="word-wrap:break-word;font-size:0px;padding:2px 15px;" align="center"><div class="" style="cursor:auto;color:{{$font_color}};font-family:Roboto, Helvetica, Arial, sans-serif;font-size:14px;line-height:22px;text-align:center;">${{ lang.shop_name_powered_by_prestashop }}$</div></td></tr></tbody></table></div><!--[if mso | IE]>
      </td></tr></table>
      <![endif]--></td></tr></tbody></table></div>{{/if}}<!--[if mso | IE]>
      </td></tr></table>
      <![endif]--></td></tr></tbody></table></div><!--[if mso | IE]>
      </td></tr></table>
      <![endif]--></div>
</body>
</html>