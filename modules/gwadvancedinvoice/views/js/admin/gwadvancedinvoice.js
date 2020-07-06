/**
* This is main js file. Don't edit the file if you want to update module in future.
* 
* @author    Globo Software Solution JSC <contact@globosoftware.net>
* @copyright 2016 Globo ., Jsc
* @link	     http://www.globosoftware.net
* @license   please read license in file license.txt
*/
$(document).ready(function(){
    $('.getnextnumber').click(function(){
        rel = $(this).attr('rel');
        start = parseInt($('.'+rel+'_start').val());
        if(isNaN(start)) start = 0;
        step = parseInt($('.'+rel+'_step').val());
        if(isNaN(step) || step < 1 ) step = 1;
        length = parseInt($('.'+rel+'_length').val());
        if(isNaN(length) || length < 1 ) length = 1;
        format = $('.'+rel+'_format').val();
        group = [];
        $('input.groupBox_'+rel+':checked').each(function(){
            group.push($(this).val());
        });
        group = group.join(';');
        data = 'action=getnextnumber&customunerajax=true&'+$(this).parents('.addnewrule').find('input').serialize();
        $.ajax({
          url: currentIndex+'&token='+token,
          type : 'POST', 
          dataType: "json",                     
          data: data,
            })
        .done(function(data) {
            if(data.success =='true' || data.success == true){
                $('.'+rel+'_addnewrule .nextnumber').html(data.numberformat);
            }else{
                showErrorMessage(data.warrning);
            }
        });
        
        return false;
    })
    $('.choose_shortcode a').click(function() {
            shortcode = $(this).data('shortcode');
            rel = $(this).parents('.choose_shortcode').attr('rel');
            $('.'+rel+'_format').val($('.'+rel+'_format').val()+'{'+shortcode+'}');
            return false;
        });
    $('.number_input').keyup(function(){
        val = $(this).val();
        _val = parseInt(val);
        if(isNaN(_val) && _val != ''){
            _val = ''; 
        } 
        if($(this).hasClass('unsigned_number') && _val < 1) _val = '';
        $(this).val(_val);
    });
    if($('.date_val').length){
        $('.date_val').each(function(){
            $(this).datetimepicker({
                dateFormat: 'yy-mm-dd',
                timeFormat: 'hh:mm:ss tt',
                changeMonth : true,
                changeYear : true,
            });
        })
    }
   
    $('.addgrouprule').click(function(){
        rel = $(this).attr('rel');
        start = parseInt($('.'+rel+'_start').val());
        if(isNaN(start)) start = 0;
        step = parseInt($('.'+rel+'_step').val());
        if(isNaN(step) || step < 1 ) step = 1;
        length = parseInt($('.'+rel+'_length').val());
        if(isNaN(length) || length < 1 ) length = 1;
        format = $('.'+rel+'_format').val();
        group = [];
        $('input.groupBox_'+rel+':checked').each(function(){
            group.push($(this).val());
        });
        group = group.join(';');
        data = 'action=addgrouprule&customunerajax=true&'+$(this).parents('.addnewrule').find('input').serialize();
        $.ajax({
          url: currentIndex+'&token='+token,
          type : 'POST', 
          dataType: "json",                     
          data: data,
            })
        .done(function(data) {
            if(data.success =='true' || data.success == true){
                showSuccessMessage(data.warrning);
                if($('.rule_'+data.id_rule).length > 0){
                    //update
                    $.each(data,function(key,val){
                        if(key !='success' && key !='warrning' && key !='groups_name'){
                            $('.rule_'+data.id_rule).data(key,val);
                        }
                    })
                    $('.rule_'+data.id_rule).find('.numberformat').html(data.numberformat);
                    $('.rule_'+data.id_rule).find('.groups').html(data.groups_name);
                }else{
                    //addnew
                    new_rule = $('.'+rel+'_wp').find('.demo_data').clone();
                    new_rule.removeClass('demo_data').removeAttr('style').addClass('rule_'+data.id_rule);
                    $.each(data,function(key,val){
                        if(key !='success' && key !='warrning' && key !='groups_name'){
                            new_rule.data(key,val);
                        }
                    })
                    new_rule.find('.delete_rule').attr('rel',data.id_rule).data('type',data.type);
                    new_rule.find('.edit_rule').attr('rel',data.id_rule).data('type',data.type);
                    new_rule.find('.numberformat').html(data.numberformat);
                    new_rule.find('.groups').html(data.groups_name);
                    new_rule.appendTo($('.'+rel+'_wp table'));
                }
                $('.'+rel+'_addnewrule').stop(true,true).slideUp(500);
            }else{
                showErrorMessage(data.warrning);
            }
        });
        
        return false;
    });
    $('.addnewrule_bt').click(function(){
        rel = $(this).attr('rel');
        parrent_wp_rel = $(this).parents('.allrule_box').attr('rel');
        $.each(allgroup,function(key,val){
            $('#groupBox_'+parrent_wp_rel+'_'+val.id_group).removeAttr('disabled');
        });
        $('.'+parrent_wp_rel+'_wp table tr').each(function(){
            if(!$(this).hasClass('demo_data')){
                _groups_choose = $(this).data('groups');
                _groups_choose+='';
                groups_choose = _groups_choose.split(',');
                if(groups_choose)
                    $.each(groups_choose,function(_key,_val){
                        $("input[name=\"groupBox_"+parrent_wp_rel+"[]\"][value="+_val+"]").attr('disabled','disabled');
                    });
            }
        })
        $('.'+parrent_wp_rel+'_addnewrule').find('input:text').val('');
        $('.'+parrent_wp_rel+'_addnewrule').find('input[name="id_rule"]').val('');
        $('.'+parrent_wp_rel+'_addnewrule').find('input:checkbox').prop("checked",false);
        $('.'+rel+'_addnewrule').slideToggle(500);
        return false;
    })
    $('#gwadvancedinvoice_tabs a.list-group-item').click(function(){
        id = $(this).attr('id');
        $('#gwadvancedinvoice_tabs a.list-group-item').stop(true,true).removeClass('active');
        $(this).stop(true,true).addClass('active');
        $('.gwadvancedinvoice_tab_content').stop(true,true).removeClass('active');
        $('#'+id+'_wp').stop(true,true).addClass('active');
        return false;
    });
    $('.delete_rule').live('click',function(){
        if(confirm(confirm_delete_text)){
            rel = $(this).attr('rel');
            type = $(this).data('type');
            data = 'action=removerule&customunerajax=true&id='+rel+'&type='+type;
            $.ajax({
              url: currentIndex+'&token='+token,
              type : 'POST', 
              dataType: "json",                     
              data: data,
                })
            .done(function(data) {
                if(data.success =='true' || data.success == true){
                    showSuccessMessage(data.warrning);
                    $('.rule_'+rel).remove();
                }else{
                    showErrorMessage(data.warrning);
                }
            });
        }
        return false;
    });
    $('.edit_rule').live('click',function(){
        rel = $(this).attr('rel');
        parrent_wp_rel = $(this).parents('.allrule_box').attr('rel');
        $.each(allgroup,function(key,val){
            $('#groupBox_'+parrent_wp_rel+'_'+val.id_group).removeAttr('disabled');
        });
        $('.'+parrent_wp_rel+'_wp table tr').each(function(){
            if(!$(this).hasClass('demo_data') && !$(this).hasClass('rule_'+rel)){
                _groups_choose = $(this).data('groups');
                _groups_choose+='';
                groups_choose = _groups_choose.split(',');
                if(groups_choose)
                    $.each(groups_choose,function(_key,_val){
                        $("input[name=\"groupBox_"+parrent_wp_rel+"[]\"][value="+_val+"]").attr('disabled','disabled');
                    });
            }
        })
        $('.'+parrent_wp_rel+'_addnewrule').stop(true,true).slideDown(500);
        $('.'+parrent_wp_rel+'_addnewrule').find('input:text').val('');
        $('.'+parrent_wp_rel+'_addnewrule').find('input[name="id_rule"]').val('');
        $('.'+parrent_wp_rel+'_addnewrule').find('input:checkbox').prop("checked",false);
        $.each($('.rule_'+rel).data(),function(key,val){
            if(key == 'groups'){
                if(val !=''){
                    val+='';
                    groups = val.split(',');
                    if(groups)
                        $.each(groups,function(_key,_val){
                            $("input[name=\"groupBox_"+parrent_wp_rel+"[]\"][value="+_val+"]").prop("checked",true);
                        });
                }
            }else if(key == 'resettype')
            {
                $("input[name="+parrent_wp_rel+"_reset][value='"+val+"']").prop("checked",true);
            }
            else
                $('.'+parrent_wp_rel+'_addnewrule input[name="'+key+'"]').val(val);
            
        })
        
        return false;
    });
    $('button[name="submitAddconfiguration"]').click(function(){
        data = 'action=saveActive&customunerajax=true&cus_delivery_active='+$('input[name="cus_delivery_active"]:checked').val()+'&cus_invoice_active='+$('input[name="cus_invoice_active"]:checked').val()+'&cus_order_active='+$('input[name="cus_order_active"]:checked').val();
        $.ajax({
          url: currentIndex+'&token='+token,
          type : 'POST', 
          dataType: "json",                     
          data: data,
            })
        .done(function(data) {
            if(data.success =='true' || data.success == true){
                showSuccessMessage(data.warrning);
            }else{
                showErrorMessage(data.warrning);
            }
        });
        return false;
    });
    if($("#popup_template").length > 0)
        $("#popup_template").fancybox();
    $('.choose_temp').live('click',function(){
        rel = $(this).attr('rel');
        $('#choose_design').val(rel);
        $('#templateChosse').html($(this).html());
        $('.fancybox-close').trigger('click');
    });
    columnsWidthResized = function(){
        productlist_width = $("#productlist").width();
		columns = $("#productlist").find(".title th");
        total = 100;
        t = columns.length;
		columns.each(function(){
		  if (t == 1) {
		      $(this).children('.widthtitle').val(total);
		  }else{
		      t--;
              widthtitle = Math.floor($(this).width()/productlist_width*100);
		      $(this).children('.widthtitle').val(widthtitle);
              total -=widthtitle;
		  }
        });
        
	};
    $('#new_column').click(function(){
        $("#productlist").colResizable({disable:true}); 
	    $(".title th:first").clone().appendTo('.title');
        $(".content td:first").clone().appendTo('.content');
        $("#productlist").colResizable({liveDrag:true,onResize:columnsWidthResized,gripInnerHtml:"<i class=\"icon-resize-horizontal column_resize\"></i>"});
        $( window ).resize();
        columnsWidthResized();
        return false;
    });
    $("#productlist").on("click", ".remove_column", function ( event ) {
        $("#productlist").colResizable({disable:true});
        var ndx = $(this).parents('th').index() + 1;
        $("th", event.delegateTarget).remove(":nth-child(" + ndx + ")");
        $("td", event.delegateTarget).remove(":nth-child(" + ndx + ")");
        $("#productlist").colResizable({liveDrag:true,onResize:columnsWidthResized,gripInnerHtml:"<i class=\"icon-resize-horizontal column_resize\"></i>"});
        $( window ).resize();
        columnsWidthResized();
        return false;
    });	
    if($("#productlist").length > 0)
	       $("#productlist").colResizable({liveDrag:true,onResize:columnsWidthResized,gripInnerHtml:"<i class=\"icon-resize-horizontal column_resize\"></i>"});
    $('button[name="previewTemplate"]').click(function(){
        tinyMCE.triggerSave();
        $('form#gwadvancedinvoicetemplate_form').attr('target', '_blank');
        $(this).val(id_language);
        return true;
    }
    );
    $('button[name="submitAddgwadvancedinvoicetemplateAndStay"]').click(function(){
        tinyMCE.triggerSave();
        $('form#gwadvancedinvoicetemplate_form').removeAttr('target');
    });
    $('button[name="saveTemplate"]').click(function(){
        tinyMCE.triggerSave();
        $('form#gwadvancedinvoicetemplate_form').removeAttr('target');
    });
    mcediv = 0;
    mceload = 0;
    if($('#activeheader_on').length > 0){
        activeheader_on = $('#activeheader_on').offset();
        mcediv = Math.round(activeheader_on.top - $(window).height()/2);
        $(window).scroll(function() {
            if (($(window).scrollTop() > mcediv && !mceload) || ($(window).scrollTop() < mcediv && mceload) ) {
                mceload = (mceload+1)%2;
                form_action = $('#gwadvancedinvoicetemplate_form').attr('action');
                data = 'submitAjaxCall=1&getstyle=true&choose_design='+$('input[name="choose_design"]').val()+'&id_language='+id_language;
                $.each($('.template_config'), function() {
                  data +='&'+$(this).attr('name')+'='+$(this).val();
                });
                $.ajax({
                  url: form_action,
                  type : 'POST',                      
                  data: data,
                    })
                .done(function(data) {
                    $.each($('iframe[id^="invoice_"]'), function() {
                        if($(this).contents().find('head').find('#customcss').length){
                            $(this).contents().find('head').find('#customcss').html(data+$('#customcss').val());
                        }else
                            $(this).contents().find('head').append('<style id="customcss">'+data+$('#customcss').val()+'</style>');
                    });
                    $.each($('iframe[id^="invoice_"]'), function() {
                        if($(this).contents().find('head').find('#customcss').length){
                            $(this).contents().find('head').find('#customcss').html(data+$('#customcss').val());
                        }else
                            $(this).contents().find('head').append('<style id="customcss">'+data+$('#customcss').val()+'</style>');
                    });
                    $.each($('iframe[id^="header_"]'), function() {
                        if($(this).contents().find('head').find('#customcss').length){
                            $(this).contents().find('head').find('#customcss').html(data+$('#customcss').val());
                        }else
                            $(this).contents().find('head').append('<style id="customcss">'+data+$('#customcss').val()+'</style>');
                    });
                    $.each($('iframe[id^="footer_"]'), function() {
                        if($(this).contents().find('head').find('#customcss').length){
                            $(this).contents().find('head').find('#customcss').html(data+$('#customcss').val());
                        }else
                            $(this).contents().find('head').append('<style id="customcss">'+data+$('#customcss').val()+'</style>');
                    });
                    
                });
            }
        });
        $(window).resize(function() {
            activeheader_on = $('#activeheader_on').offset();
            mcediv = Math.round(activeheader_on.top - $(window).height()/2);
            
        });
    }
    
    $("#GWADVANCEDINVOICE_TEMPLATE").change(function(){
        if($(this).val() == "")
            $("#configuration_form_submit_btn").attr("disabled","disabled");
        else
            $("#configuration_form_submit_btn").removeAttr("disabled");
    });
    if($("#GWADVANCEDINVOICE_TEMPLATE").length > 0)
        if($("#GWADVANCEDINVOICE_TEMPLATE").val() == "")
            $("#configuration_form_submit_btn").attr("disabled","disabled");
            
    
    if($(".pagesize.pagesize_ajaxcall").length > 0){
        $("#gwadvancedinvoicetemplate_form_submit_btn.chooseTemplate_ajaxcall").attr("disabled","disabled");
        form_action = $(this).parents('form').attr('action');
        if($(this).val() != '0' && $(this).val() !=''){
            $.ajax({
              url: form_action,
              data: "submitAjaxCall=1&pagesize="+$(this).val(),
                })
            .done(function(data) {
                var obj = jQuery.parseJSON(data);
                $("#templateChosse_box").html( obj.templates );
            });
        }
    }
    
    $(".pagesize").change(function(){
        if($(this).hasClass('pagesize_ajaxcall')){
            $("#gwadvancedinvoicetemplate_form_submit_btn.chooseTemplate_ajaxcall").attr("disabled","disabled");
            form_action = $(this).parents('form').attr('action');
            $.ajax({
              url: form_action,
              data: "submitAjaxCall=1&pagesize="+$(this).val(),
                })
            .done(function(data) {
                var obj = jQuery.parseJSON(data);
                $("#templateChosse_box").html( obj.templates );
            });
        }
    });
    if($(".pagesize.pagesize_ajaxcall").length > 0 && ($(".pagesize").val() == '0' || $(".pagesize").val() == '')){
        $(".pagesize.pagesize_ajaxcall").val('A4').change();
    }
    $("input[name=\'choose_design\']").live("change",function(){
        if ($("input[name=\'choose_design\']:checked").val() != "undefined"){
            $("#gwadvancedinvoicetemplate_form_submit_btn.chooseTemplate_ajaxcall").removeAttr("disabled").click();
            
        }
    });
    
})
$(document).ready(function(){
    if(typeof $.fancybox == 'function') {
         $('.popupimage').fancybox();
    }
});