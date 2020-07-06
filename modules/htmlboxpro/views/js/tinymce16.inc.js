/**
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
 * @copyright 2010-9999 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
 */

function tinySetup(config) {
    if (typeof tinyMCE === 'undefined') {
        setTimeout(function () {
            tinySetup(config);
        }, 100);
        return;
    }

    if (!config)
        config = {};

    var editor_selector = 'rte';
    //if (typeof config['editor_selector'] !== 'undefined')
    //var editor_selector = config['editor_selector'];
    if (typeof config['editor_selector'] != 'undefined')
        config['selector'] = '.' + config['editor_selector'];

    //safari,pagebreak,style,table,advimage,advlink,inlinepopups,media,contextmenu,paste,fullscreen,xhtmlxtras,preview
    default_config = {
        selector: ".rte",
        plugins: "visualblocks, preview searchreplace print insertdatetime, hr charmap colorpicker anchor code link image paste pagebreak table contextmenu filemanager table code media autoresize textcolor emoticons",
        toolbar2: "newdocument,print,|,bold,italic,underline,|,strikethrough,superscript,subscript,|,forecolor,colorpicker,backcolor,|,bullist,numlist,outdent,indent",
        toolbar1: "styleselect,|,formatselect,|,fontselect,|,fontsizeselect,",
        toolbar3: "code,|,table,|,cut,copy,paste,searchreplace,|,blockquote,|,undo,redo,|,link,unlink,anchor,|,image,emoticons,media,|,inserttime,|,preview,|, visualblocks,charmap,hr,",

        external_filemanager_path: ad + "/filemanager/",
        filemanager_title: "File manager",
        external_plugins: {"filemanager": ad + "/filemanager/plugin.min.js"},
        extended_valid_elements: 'link[*], pre[*],script[*],style[*]',
        valid_children: "+body[meta|style|script|iframe|section|link],a[embed|sub|sup|textarea|strong|strike|small|em|form|frame|iframe|input|select|legend|button|div|img|h1|h2|h3|h4|h5|h6|h7|span|p|section|pre|b|u|i|a|ol|ul|li|table|td|tr|th|tbody|thead],pre[iframe|section|script|div|p|br|span|img|style|h1|h2|h3|h4|h5],*[*]",
        valid_elements: '*[*]',
        allow_html_in_named_anchor: true,
        force_p_newlines: false,
        cleanup: false,
        forced_root_block: false,
        force_br_newlines: true,
        relative_urls: true,
        statusbar: false,
        convert_urls: true,
        remove_script_host: false,

        menu: {
            edit: {title: 'Edit', items: 'undo redo | cut copy paste | selectall'},
            insert: {title: 'Insert', items: 'media image link | pagebreak'},
            view: {title: 'View', items: 'visualaid'},
            format: {title: 'Format', items: 'bold italic underline strikethrough superscript subscript | formats | removeformat'},
            table: {title: 'Table', items: 'inserttable tableprops deletetable | cell row column'},
            tools: {title: 'Tools', items: 'code'}
        }

    }

    $.each(default_config, function (index, el) {
        if (config[index] === undefined)
            config[index] = el;
    });

    tinyMCE.init(config);

}

$().ready(function () {
    tinySetup();
});