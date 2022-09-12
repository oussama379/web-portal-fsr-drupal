 /**
  * $Desc
  *
  * @author     Gavias <gaviasthemes@gmail.com>
  * @copyright  Copyright (C) 2015. All Rights Reserved.
  * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
  * @addition this license does not allow theme provider using in themes to sell on marketplaces.
  * 
  */
!function(e){function t(t,i){this.element=t,this.options=e.extend({},n,i),this._defaults=n,this._name=r}var r="serializeObject",n={requiredFormat:{}};t.prototype.toJsonObject=function(){var t=this,r=t.replaceEmptyBracketWithNumericBracket(e(t.element).serializeArray()),n=t.options.requiredFormat;return e.each(r,function(r,i){var a=i.name.replace(/]/g,"").split("["),o=t.stringArrayKeyToVariable(a,i.value);n=e.extend(!0,{},n,o)}),n},t.prototype.replaceEmptyBracketWithNumericBracket=function(t){var r={},n=t;return e.each(n,function(e,t){var i=t.name,a=i.indexOf("[]");a>-1&&(r[i]="undefined"==typeof r[i]?0:++r[i],n[e].name=t.name.replace("[]","["+r[i]+"]"))}),n},t.prototype.stringArrayKeyToVariable=function(e,t){var r,n=this;if(e.length>0){var i=e.shift().trim();r=isNaN(i)&&""!=i?{}:[],r[i]=e.length>0?n.stringArrayKeyToVariable(e,t):t}return r},e.fn[r]=function(e){return new t(this,e).toJsonObject()}}(jQuery,window,document);

var keyString="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
base64Encode = function(c) {
  var a = "";
  var k, h, f, j, g, e, d;
  var b = 0;
  c = UTF8Encode(c);
  while (b < c.length) {
    k = c.charCodeAt(b++); 
    h = c.charCodeAt(b++);
    f = c.charCodeAt(b++);
    j = k >> 2;
    g = ((k & 3) << 4) | (h >> 4);
    e = ((h & 15) << 2) | (f >> 6);
    d = f & 63;
    if (isNaN(h)) {
      e = d = 64
    } else {
      if (isNaN(f)) {
        d = 64
      }
    }
    a = a + keyString.charAt(j)
    + keyString.charAt(g)
    + keyString.charAt(e)
    + keyString.charAt(d)
  }
  return a
};

UTF8Encode = function(b) {
  b = b.replace(/\x0d\x0a/g, "\x0a");
  var a = "";
  for ( var e = 0; e < b.length; e++) {
    var d = b.charCodeAt(e);
    if (d < 128) {
      a += String.fromCharCode(d)
    } else {
      if ((d > 127) && (d < 2048)) {
        a += String.fromCharCode((d >> 6) | 192);
        a += String.fromCharCode((d & 63) | 128)
      } else {
        a += String.fromCharCode((d >> 12) | 224);
        a += String.fromCharCode(((d >> 6) & 63) | 128);
        a += String.fromCharCode((d & 63) | 128)
      }
    }
  }
  return a
};

GaviasCompare = function(a, b){
   if (a.index < b.index)
     return -1;
   if (a.index > b.index)
     return 1;
   return 0;
};

function randomString(length) {
    var chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    var result = '';
    for (var i = length; i > 0; --i) result += chars[Math.floor(Math.random() * chars.length)];
    return result;
}

//========= Main script ====================
 
(function ($) {
  $.fn.extend({insertcaret:function(t){return this.each(function(){if(document.selection){this.focus();var e=document.selection.createRange();e.text=t,this.focus()}else if(this.selectionStart||"0"==this.selectionStart){var s=this.selectionStart,i=this.selectionEnd,n=this.scrollTop;this.value=this.value.substring(0,s)+t+this.value.substring(i,this.value.length),this.focus(),this.selectionStart=s+t.length,this.selectionEnd=s+t.length,this.scrollTop=n}else this.value+=t,this.focus()})}});

   $(document).ready(function () {
      var isInIFrame = (window.location != window.parent.location);
      if(isInIFrame==true){
        $('#toolbar-administration').remove();
        $('.region-breadcrumb').hide();
        $('header.content-header').hide();
        $('#gavias-blockbuilder-setting').css('padding-top', '20px');
      }
      $('#gbb-form-setting').delegate(".input_datetime", 'click', function(e){
          e.preventDefault();
          $(this).datepicker({
               defaultDate: "",
               dateFormat: "yy-mm-dd",
               numberOfMonths: 1,
               showButtonPanel: true,
          });
           $(this).datepicker('show');
     });


      $('#save').removeAttr('disabled');
      $('#gavias-blockbuilder-setting').delegate('#save', 'click', function(){
        $(this).attr('disabled', 'true');
         gavias_save_blockbuilder();
         return false;
      })

      $('#gbb-form-setting').delegate('.gsc_display_view', 'change', function(){
        var $label = $(this).find(':selected').text();
        $(this).parents('#gbb-form-setting').find('.display-admin').val($label);
      });
   });

   function notify(style, text) {
      $.notify({
          title: 'Notification',
          text: text,
          image: '<i class="fa fa-bell" style="font-size: 30px; color: #fff;"></i>',
          hideAnimation: 'slideUp',

      }, {
          style: 'metro',
          className: style,
          autoHide: true,
          clickToHide: true,
          globalPosition: 'top right'
      });
   }

  function gavias_save_blockbuilder(){
      var result = $('#gavias-blockbuilder-setting input:not(.input-file-upload), #gavias-blockbuilder-setting select, #gavias-blockbuilder-setting textarea').serializeObject();
      result = $.extend({}, result);
      result =base64Encode(JSON.stringify(result));
        //console.log(result);
      var pid = $("input[name=gavias_blockbuilder_id]").val();
      var data = {
         data: result,
         pid: pid
      };
      notify('info', 'Please waiting Block Builder process !');
      $.ajax({
         url: drupalSettings.gavias_blockbuilder.saveConfigURL,
         type: 'POST',
         data: data,
         dataType: 'json',
         success: function (data) {
          $('#save').val('Save');
          notify('success', 'Block Builder setting updated');
          $('#save').removeAttr('disabled');
          if(drupalSettings.gavias_blockbuilder.destination==true){
            window.location = drupalSettings.gavias_blockbuilder.url_redirect;
          }

          // var isInIFrame = (window.location != window.parent.location);
          // if(!isInIFrame){
          //   window.location = drupalSettings.gavias_blockbuilder.url_redirect;
          // }else{
          //   $('#save').removeAttr('disabled');
          // }

         },
         error: function (jqXHR, textStatus, errorThrown) {
           alert(textStatus + ":" + jqXHR.responseText);
           notify('black', 'Block Builder setting not updated');
           window.location = drupalSettings.gavias_blockbuilder.url_redirect;
         }
      });
   }

   function init_sortable_find(wrap, find, items){
      wrap.find(find).sortable({ 
         start: function(e, ui){
           ui.placeholder.width(ui.item.find('.gavias-blockbuilder-content').first().width() - 20);
           ui.placeholder.height(ui.item.height() - 20);
         },
         connectWith          : find,
         cursor               : 'move',
         forcePlaceholderSize : true, 
         placeholder          : 'gbb-placeholder',
         items                : items,
         opacity              : 1,
         handle               : '.bb-drap',
          receive              : function(event, ui){ 
            gbb_sort_rows();
         },
      }); 
      return wrap;  
   }

  function gbb_sortable_elements(){
    var wrap = $('#gbb-admin-wrap');
    wrap.find('.gbb-droppable-column').sortable({ 
      start: function(e, ui){
           ui.placeholder.width(150);
           ui.item.width(150);
           ui.placeholder.height(ui.item.height() - 20);
        },
        connectWith           : '.gbb-droppable-column',
        cursor                : 'move',
        forcePlaceholderSize  : true, 
        placeholder           : 'gbb-placeholder',
        items                 : '.gbb-item',
        opacity               : 0.9 ,  
        handle                : '.bb-el-drap',
        receive              : function(event, ui){ 
            var target_sid = jQuery(this).siblings('.gbb-column-id').val(); 
            var target_rid = jQuery(this).parents('.gbb-row').find('.gbb-row-id').val(); 
            ui.item.find('.element-parent').val(target_sid);
            ui.item.find('.element-row-parent').val(target_rid);
         }
    });
   }

   function gbb_sort_columns(){
    var wrap = $('#gbb-admin-wrap');
    return wrap.find('.gbb-row').each(function(){
      id = 0;
      $(this).find('.gbb-columns').each(function(){
        id++;
        $(this).find('input.gbb-column-id').val(id);
        $(this).find('input.element-parent').val(id);
      })
    })
   }

   function gbb_sort_rows(){
    var wrap = $('#gbb-admin-wrap');
    row_id = 0;
    wrap.find('.gbb-row').each(function(){
      row_id++;
      col_id = 0;
      $(this).find('input.gbb-row-id').val(row_id);
      $(this).find('.gbb-columns').each(function(){
        col_id++;
        $(this).find('input.column-parent').val(row_id);
        $(this).find('input.element-row-parent').val(row_id);
      })
    })
    $('input#gbb-row-id').val(row_id);
    return wrap;
   }

   function gbb_sortable_columns(){
     var wrap = $('#gbb-admin-wrap');
     var id=0;
      wrap.find('.gbb-droppable-row').sortable({ 
      start: function(e, ui){
           ui.placeholder.width(ui.item.find('.gavias-blockbuilder-content').first().width() - 20);
           ui.placeholder.height(ui.item.height() - 20);
        },
        connectWith           : '.gbb-droppable-row',
        cursor                : 'move',
        forcePlaceholderSize  : true, 
        placeholder           : 'gbb-placeholder',
        items                 : '.gbb-columns',
        opacity               : 0.9 ,
        handle                : '.bb-drap',
        receive              : function(event, ui){ 
            var target_rid = jQuery(this).siblings('.gbb-row-id').val(); 
            ui.item.find('.column-parent').val(target_rid);
            ui.item.find('.element-row-parent').val(target_rid);
         },
         update: function(){
          gbb_sort_columns();
         }
      });
   }

   function add_column(el, width, type){
    var blockbuilder_admin = $('#gbb-admin-wrap');
      var clone = $('#gbb-columns .gbb-columns').clone(true);
      clone.hide();
      var row_id = el.parents('.gbb-row').find('.gbb-row-id').val(); 
      var col_id = -1;
      var col_width = width;
      clone.removeClass('wb-4').addClass('wb-' + col_width);
      clone.find('input.column-size').val(col_width);
      clone.find('item-w-label').html(col_width);
      //Set value max column id
      el.parents('.gbb-row').find('input.gbb-column-id').each(function(){
        if( $(this).val() > col_id){
          col_id = $(this).val();
        }
      });
      if(col_id==-1) col_id = 0;

      $('#gbb-column-id').val($('#gbb-column-id').val()*1 + 1);
      clone.addClass('type-' + type);
      clone.find('.item-w-label').html(col_width);
      clone.find('.gbb-column-id').val(col_id*1 + 1);
      clone.find('input.column-parent').val(row_id);
      clone.find('input.column-type').val(type);
      el.parents('.gbb-row').find('.gbb-droppable-row').append(clone).find(".gbb-columns").fadeIn(500);
      gbb_sortable_elements();
      gbb_sortable_columns();
   }

   var IMCE_WINDOW = null;
   function gavias_block_builder(){
   		
   	var blockbuilder_admin = $('#gbb-admin-wrap');
     
      var _bb_sortable = '.gbb-sortable';
      var _bb_item = '.gbb-item';
      var _bb_items = '#gbb-items';
      var _bb_row = '.gbb-row';
      var _bb_column = '.gbb-columns';

   	if( ! blockbuilder_admin.length ) return false;	
   	
    blockbuilder_admin.sortable({ 
      start: function(e, ui){
           ui.placeholder.width(ui.item.find('.gavias-blockbuilder-content').first().width() - 20);
           ui.placeholder.height(ui.item.height() - 20);
        },
        cursor          : 'move',
        forcePlaceholderSize  : true, 
        placeholder       : 'gbb-placeholder',
        items           : '.gbb-row',
        opacity         : 0.9,
        handle               : '.bb-drap',
        update              : function(event, ui){ 
            gbb_sort_rows();
         },
    });
   	
    gbb_sortable_columns();
    gbb_sortable_elements();

      //Size element
      $('.gbb-plus').click(function(){
         var item = $(this).closest('.gbb-columns');
         var _isize = [ '1', '2', '3', '4', '5', '6', '7','8','9','10','11', '12' ];
         for( var i = 0; i < _isize.length-1; i++ ){
            var classes = 'wb-' + _isize[i].replace('/', '-');  //classes width bootstrap eg. wb-6
            var classes_new = 'wb-' + _isize[i+1].replace('/', '-'); //classes width bootstrap eg. wb-6
            if( ! item.hasClass( classes ) ) continue;
            item.removeClass( classes ).addClass( classes_new ).find('.column-size').val( _isize[i+1] );
            item.find('.item-w-label').text( _isize[i+1] );
            break;
         }  
      });
      
      $('.gbb-minus').click(function(){
         var item = $(this).closest('.gbb-columns');
          var _isize = [ '1', '2', '3', '4', '5', '6', '7','8','9','10','11', '12' ];
         
         for( var i = 1; i < _isize.length; i++ ){
            var classes = 'wb-' + _isize[i].replace('/', '-'); //classes width bootstrap eg. wb-6
            var classes_new = 'wb-' + _isize[i-1].replace('/', '-'); //classes width bootstrap eg. w-6
            if( ! item.hasClass( classes ) ) continue;
            
            item.removeClass( classes )
               .addClass( classes_new )
               .find('.column-size').val( _isize[i-1]);
            
            item.find('.item-w-label').text( _isize[i-1] );
            
            break;
         }     
      });

    // add row 
    var rowid = $('#gbb-row-id');
   	$('.bb-btn-row-add').click(function(){
   		var clone = $('#gbb-rows .gbb-row').clone(true);
         
        init_sortable_find(clone, _bb_sortable, _bb_item);

   		clone.hide().find('.gavias-blockbuilder-content > input').each(function() {
   				$(this).attr('name',$(this).attr('class')+'[]');
   			});
   		
   		rowid.val(rowid.val()*1+1);
   		clone.find('.gbb-row-id').val(rowid.val());
   		
   		blockbuilder_admin.append(clone).find(".gbb-row").fadeIn(500);
      gbb_sort_rows();
   	});

    // add column
    $('.gbb-add-column').click(function(){
      var col_width = $(this).attr('data-width');
      add_column($(this), col_width, '');
    });
    
    // add clear
    $('.gbb-add-clear').click(function(){
      add_column($(this), '12', 'column-clearfix');
    });

   	// clone row
   	$('.gbb-row .gbb-row-clone').click(function(){
   		var element = $(this).closest('.gbb-element');
   		element.find(_bb_sortable).sortable('destroy');
   		var clone = element.clone(true);
   		element.after(clone);
      gbb_sort_rows();
      gbb_sortable_columns();
      gbb_sortable_elements();
   	});

    // clone columns
    $('.gbb-columns .gbb-column-clone').click(function(){
      var wrap = $('#gbb-admin-wrap');
      var element = $(this).closest('.gbb-element');
      element.find(_bb_sortable).sortable('destroy');
      row_id = $(this).parents('gbb-row').find('input.gbb-row-id').val();
      var clone = element.clone(true);
      clone.find('input.element-parent').val(row_id);
      element.after(clone);
      gbb_sort_columns();
      gbb_sortable_columns();
      gbb_sortable_elements();
    }); 

    
   	// add item list toggle
   	$('.gbb-add').click(function(){
   		var parent = $(this).parent();
   		
   		if( parent.hasClass('focus') ){
   			parent.removeClass('focus');
        $(this).parents('.gbb-columns').css('z-index', '');
   		} else {
   			$('.gbb-add-element').removeClass('focus');
   			parent.addClass('focus');
        $(this).parents('.gbb-columns').css('z-index', 9);
   		}
   	});
   	
   	// add item 
   	$('.gbb-items a').click(function(){
   		$(this).closest('.gbb-add-element').removeClass('focus');
       $(this).parents('.gbb-columns').css('z-index', 9);
   		var parent = $(this).parents('.gbb-columns').find('.gbb-droppable');
   		var column_id = $(this).parents('.gbb-columns').find('.gbb-column-id').val(); 
      var row_id = $(this).parents('.gbb-row').find('.gbb-row-id').val(); 
      
   		var item = $(this).attr('class');
   		var clone = $(_bb_items).find('div.gbb-type-'+ item ).clone(true);
      
   		clone.hide().find('.gavias-blockbuilder-content input').each(function() {
   			$(this).attr('name',$(this).attr('class')+'[]');
   		});

   		clone.find('.element-parent').val(column_id);
      clone.find('.element-row-parent').val(row_id);
   		parent.append(clone).find(".gbb-item").fadeIn(500);
      gbb_sortable_elements();
      gbb_sortable_columns();
   	});
   	

      // delete el 
      $('.gbb-el-delete').click(function(){
         var item = $(this).closest('.gbb-element');
         if( confirm( "You are sure delete this element ! Continue?" ) ){
            item.fadeOut(500, function(){$(this).remove()});
          } else {
            return false;
          }
      });

      // delete column 
      $('.gbb-column-delete').click(function(){
         var item = $(this).closest('.gbb-columns');
         if( confirm( "You are sure delete this column ! Continue?" ) ){
            item.fadeOut(500, function(){
              $(this).remove()
              gbb_sort_columns();
            });
          } else {
            return false;
          }
      });

      //Delete row
      $('.gbb-row-delete').click(function(){
         var item = $(this).closest('.gbb-row');
         if( confirm( "You are sure delete this row ! Continue?" ) ){
            item.fadeOut(500, function(){
              $(this).remove();
              gbb_sort_rows();
            });
          } else {
            return false;
          }
      });

   	// clone el
   	$('.gbb-item .gbb-item-clone').click(function(){
   		var element = $(this).closest('.gbb-element');
   		var _clone = element.clone(true);
   		element.after(_clone);
   	});

    // form
   	var iresult = ''; var oresult = '';
   
   	$('.gbb-edit').click(function(){

      oresult = '';
      $('#gbb-form-setting').parent().find('.gavias-overlay').first().addClass('active').parents('body').addClass('gavias-overflow-hidden');
   		iresult = $(this).closest('.gbb-element');    
   		var _clone = iresult.children('.gbb-el-meta').clone(true);
   	  oresult = iresult.children('.gbb-el-meta').clone(true);
      //_clone.find('.CodeMirror').remove();

      $('#gbb-form-setting').prepend(_clone).show();
      $('#gbb-form-setting .gbb-el-meta').animate({scrollTop: 0}, 500);

   		iresult.children('.gbb-el-meta').remove();
      //runRender('html');
      tinymce.init({
        selector: '#gbb-form-setting textarea.code_html_tiny',
        height: 200,
        plugins: [
          'advlist autolink lists link image charmap anchor contextmenu pagebreak media searchreplace code fullscreen table',
          'emoticons textcolor textpattern colorpicker'
        ],
        toolbar1: 'insertfile undo redo | styleselect | bullist numlist outdent indent | link image | forecolor backcolor emoticons',
      });

      if (typeof(tinyMCE) != "undefined") {
        if (tinyMCE.activeEditor == null || tinyMCE.activeEditor.isHidden() != false) {
          tinyMCE.editors=[]; // remove any existing references
        }
      }
      if(drupalSettings.gavias_blockbuilder.check_imce=='on'){
        load_imce();
      }
      return;
   	});

    $('#gbb-form-setting .gbb-form-setting-save').click(function(){
        tinymce.remove(); 
        if(drupalSettings.gavias_blockbuilder.check_imce=='on'){
          close_imce();
        }  

        $('.tabs-ul.ui-sortable').sortable('destroy');
        var popup = $('#gbb-form-setting');
        var _clone = popup.find('.gbb-el-meta').clone(true);
        iresult.append(_clone);
        iresult.find('>.gavias-blockbuilder-content>.gbb-item-content>.item-bb-title').html(iresult.find('.display-admin').first().val());
        popup.fadeOut(50, function(){
          $(this).parent().find('.gavias-overlay').first().removeClass('active').parents('body').removeClass('gavias-overflow-hidden');
        })
       
        setTimeout(function(){
          popup.find('.gbb-el-meta').remove();
        }, 150);

      iresult = ''; oresult = '';
      return;
    });  

    $('#gbb-form-setting .gbb-form-setting-cancel').click(function(){
      tinymce.remove(); 
      if(drupalSettings.gavias_blockbuilder.check_imce=='on'){
        close_imce();
      }  
      $('.tabs-ul.ui-sortable').sortable('destroy');
      var popup = $('#gbb-form-setting');
      iresult.append(oresult);
      popup.fadeOut(50).parent().find('.gavias-overlay').first().removeClass('active').parents('body').removeClass('gavias-overflow-hidden');
      setTimeout(function(){
        popup.find('.gbb-el-meta').remove();
      }, 150);
      iresult = ''; oresult = '';
    });

  }

  (function (o) {
      jQuery.fn.clone = function () {
          var result = o.apply (this, arguments),
          old_input = this.find('textarea, select'),
          new_input = result.find('textarea, select');
          //set random id upload field
          result.find('.imce-url-input').each(function(){
            var random = randomString(10)
            $(this).attr('id', 'gva-upload-' + random);
            $(this).attr('id', 'gva-upload-' + random);
            $(this).attr('data-id', random);
            $(this).parents('.gva-upload-input').attr('data-id', '' + random);
          });
          
          //set random id upload field
          result.find('.gva-upload-image').each(function(){
            var random = randomString(10)
            $(this).attr('id', 'gva-upload-' + random);
            $(this).find('form.upload').attr('id', 'upload-' + random);
          });

          for (var i = 0, l = old_input.length; i < l; ++i)
            jQuery(new_input[i]).val( jQuery(old_input[i]).val() );
          
          return result;
      };
  }) (jQuery.fn.clone);

  $(document).ready(function(){
    gavias_block_builder();
  });

  $(document).mouseup(function(e){
   	if ($(".gbb-add-element").has(e.target).length === 0){
   		$(".gbb-add-element").removeClass('focus');
      $('.gbb-columns').css('z-index', '');
   	} 
   	if ($(".gbb-sc-add").has(e.target).length === 0){
   		$(".gbb-sc-add").removeClass('focus');
      $('.gbb-columns').css('z-index', '');
   	}
  });

  $(document).ready(function(e){
    $(".close-gbb-items").click(function(){
      $(".gbb-add-element").removeClass('focus');
      $('.gbb-columns').css('z-index', '');
    });
  });

  function getByClass(sClass){
    var aResult=[];
    var aEle=document.getElementsByTagName('*');
    for(var i=0;i<aEle.length;i++){
      /*foreach className*/
      var arr=aEle[i].className.split(/\s+/);
      for(var j=0;j<arr.length;j++){
        /*check class*/
        if(arr[j]==sClass){
          aResult.push(aEle[i]);
        }
      }
    }
    return aResult;
  };


  function runRender(type){
    var aBox=getByClass("code_"+type);
    for(var i=0;i < aBox.length; i++){
      var editor = false;
      if(!editor){
        editor = CodeMirror.fromTextArea(aBox[i], {
          lineNumbers: true,
          mode: "text/html",
        });
      }
      editor.on("blur", function() {editor.save()});
    }
  };


  $('.swith-language.nav-tabs li a').click(function(e){
    e.preventDefault();
    var id = $(this).attr('href');
    var w = $(this).parents('.multi-language');
    w.find('.swith-language.nav-tabs li a').removeClass('active');
    $(this).addClass('active');
    w.find('.swith-languages.tab-content .tab-pane').removeClass('active');
    w.find('.swith-languages.tab-content .tab-pane' + id).addClass('active');
  });

  function load_imce(){
    $('.imce-url-button').click(function(e){
      e.preventDefault();
      var url = Drupal.url('imce');
      var inputID = 'gva-upload-' + $(this).parents('.gva-upload-input').attr('data-id');
      url += (url.indexOf('?') === -1 ? '?' : '&') + 'sendto=gvaImceInput.urlSendto&inputId=' + inputID + '&type=link';
      $('#gva-upload-' + inputID).focus();
      if(IMCE_WINDOW == null || IMCE_WINDOW.closed){
        IMCE_WINDOW = window.open(url, '', 'width=' + Math.min(1000, parseInt(screen.availWidth * 0.8, 10)) + ',height=' + Math.min(800, parseInt(screen.availHeight * 0.8, 10)) + ',resizable=1');
      }
      return false;
    })
  }

  function close_imce(){
    try {
      if (IMCE_WINDOW.document.location.href == "about:blank") {
        IMCE_WINDOW.close();
        IMCE_WINDOW = undefined;
      }
    } catch (e) { }
  }

  var gvaImceInput = window.gvaImceInput = window.gvaImceInput || {
     urlSendto: function(File, win) {
      var url = File.getUrl();
      var el = $('#' + win.imce.getQuery('inputId'))[0];
      win.close();
      if (el) {
        var base_path = drupalSettings.gavias_blockbuilder.base_path;
        var url_new = '/' + url.replace(base_path, '');
        $(el).val(url_new);
        $(el).parents('.gva-upload-input').find('.gavias-image-demo').attr('src', url);
      }
    }
  }
  

})(jQuery);