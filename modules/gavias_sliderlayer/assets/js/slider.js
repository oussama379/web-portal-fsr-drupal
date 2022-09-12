var $settings = drupalSettings.gavias_sliderlayer.settings;

var $group_settings = drupalSettings.gavias_sliderlayer.group_settings;

var $layers = drupalSettings.gavias_sliderlayer.layers_settings;

var $cxt = 0;

if($layers == 'null') $layers =  new Array();

if($settings == 'null') $settings =  {}; 

if($group_settings == 'null') $group_settings = { startwidth: 1170, startheight: 600 };

var delayer = drupalSettings.gavias_sliderlayer.delayer; 

var deslider = drupalSettings.gavias_sliderlayer.deslider;

var key = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
base64Encode = function(r) {
    var e, n, t, a, o, i, d, C = "",
        h = 0;
    for (r = UTF8Encode(r); h < r.length;) e = r.charCodeAt(h++), n = r.charCodeAt(h++), t = r.charCodeAt(h++), a = e >> 2, o = (3 & e) << 4 | n >> 4, i = (15 & n) << 2 | t >> 6, d = 63 & t, isNaN(n) ? i = d = 64 : isNaN(t) && (d = 64), C = C + key.charAt(a) + key.charAt(o) + key.charAt(i) + key.charAt(d);
    return C
}, UTF8Encode = function(r) {
    r = r.replace(/\x0d\x0a/g, "\n");
    for (var e = "", n = 0; n < r.length; n++) {
        var t = r.charCodeAt(n);
        128 > t ? e += String.fromCharCode(t) : t > 127 && 2048 > t ? (e += String.fromCharCode(t >> 6 | 192), e += String.fromCharCode(63 & t | 128)) : (e += String.fromCharCode(t >> 12 | 224), e += String.fromCharCode(t >> 6 & 63 | 128), e += String.fromCharCode(63 & t | 128))
    }
    return e
}, GaviasCompare = function(r, e) {
    return r.index < e.index ? -1 : r.index > e.index ? 1 : 0
};

(function ($) {
  $(window).load(function(){
    if(!$('input[name*="video_youtube_args"').val()){
      $('input[name*="video_youtube_args"').val('version=3&enablejsapi=1&html5=1&hd=1&wmode=opaque&showinfo=0&ref=0;origin=http://server.local;autoplay=1;');
    }
    if(!$('input[name*="video_vimeo_args"').val()){
      $('input[name*="video_vimeo_args"').val('title=0&byline=0&portrait=0&api=1');
    }
  })

  $(document).ready(function () {

  //=========== Ranger ==================
   var delay = 9000;
    if($group_settings['delay']){
      delay = $group_settings['delay'];
    }

  var slider = document.getElementById('g-slider');
    noUiSlider.create(slider, {
      start: [ 20, parseInt(delay) - 20 ],
      //step: 1000, 
      margin: 20, 
      connect: true, 
      behaviour: 'tap-drag',
      range: {
        'min': 0,
        'max': parseInt(delay)
      },
      pips: { 
        mode: 'steps',
        density: 2
      }
    });

    var end = document.getElementById('g_data_end'),
    start = document.getElementById('g_data_start');

    slider.noUiSlider.on('update', function( values, handle ) {
      if ( handle ) {
        end.value = values[handle];
      } else {
        start.value = values[handle];
      }
    });

   $('#g_data_end').on('change', function() {
      slider.noUiSlider.set([null, this.value]);
    });

    $('#g_data_start').on('change', function() {
      slider.noUiSlider.set([ this.value, null]);
    });

    //Option input ===
    var previous_val;

    $('select[name=text_style]').on('focus', function () {
      previous_val = this.value;
    }).change(function () {
      $('.layer[id=layer' + '-' + $cxt + '] .inner').removeClass(previous_val);
      
      $('.layer[id=layer' + '-' + $cxt + '] .inner').addClass($(this).val());
      var left = $layers[$cxt].left;
      if(left =='center'){
          $('#layer-' + $cxt).css({
            left: '50%',
            'margin-left' : -($('#layer-' + $cxt).width()/2)
          });
      }else if(left=='left'){
          $('#layer-' + $cxt).css({
            left: 0
          });
      }else if(left=='right'){
          $('#layer-' + $cxt).css({
            right: 0
          });
      }
      previous_val = $(this).val();
    });

    $('#content-type').find('#layer-text').keyup(function () {
        $layers[$cxt].text = $(this).val();
        $('#layer-' + $cxt).find('.inner').html($(this).val());
        var left = $layers[$cxt].left;
        if(left =='center'){
            $('#layer-' + $cxt).css({
              left: '50%',
              'margin-left' : -($('#layer-' + $cxt).width()/2)
            });
        }else if(left=='left'){
            $('#layer-' + $cxt).css({
              left: 0
            });
        }else if(left=='right'){
            $('#layer-' + $cxt).css({
              right: 0
            });
        }
    })

    $('[name=custom_css]').keyup(function(){
      $layers[$cxt].custom_css = $(this).val();
      $('#layer-' + $cxt).find('.inner').attr('style',$(this).val());
    });

    $('[name=custom_class]').change(function(){
      $layers[$cxt].custom_class = $(this).val();
      $('#layer-' + $cxt).find('.inner').attr('class', 'inner');
      $('#layer-' + $cxt).find('.inner').addClass($(this).val());
      $('#layer-' + $cxt).find('.inner').addClass($(this).parents('.fieldset-wrapper').find('[name=custom_style]').val());
    });
    
    $('[name=custom_style]').change(function(){
      $layers[$cxt].custom_style = $(this).val();
      $('#layer-' + $cxt).find('.inner').attr('class', 'inner');
      $('#layer-' + $cxt).find('.inner').addClass($(this).val());
      $('#layer-' + $cxt).find('.inner').addClass($(this).parents('.fieldset-wrapper').find('[name=custom_class]').val());
    });

    $('#gavias_slider_single').width($group_settings.startwidth).height($group_settings.startheight);

    $('input[name=top]').change(function () {
      $('#layer-' + $cxt).css({
        top: $(this).val() + 'px'
      });
    });

    $('input[name=left]').on('change', function () {
      if($(this).val()=='center'){
        $('#layer-' + $cxt).css({
          left: '50%',
          'margin-left' : -($('#layer-' + $cxt).width()/2)
        });
      }else if($(this).val()=='left'){
        $('#layer-' + $cxt).css({
          left: 0,
          'margin-left': 0
        });
      }else if($(this).val()=='right'){
        $('#layer-' + $cxt).css({
          right: 0,
          'margin-left': 0
        });
      }else{
        $('#layer-' + $cxt).css({
          left: $(this).val() + 'px',
          'margin-left': 0
        });
      }
    });

    $('input[name=width]').change(function () {
      $('#layer-' + $cxt).css({
        width: $(this).val() + 'px'
      });
    });

    $('input[name=height]').change(function () {
      $('#layer-' + $cxt).css({
        height: $(this).val() + 'px'
      });
    });

    //=========== End Ranger ==================  
    load_slider();

    $('#add_layer').click(function () {
      add_layer();
      return false;
    })

    $('#save').click(function () {
      $(this).attr('disabled', 'true');
      save_layer_slider();
    })
 
    $('input#g-image-layer').on('onchange', function(){
        $url = drupalSettings.gavias_sliderlayer.base_url + $(this).val();
        insertImageToLayer($url);
    });

    $('input#background-image').on('onchange', function(){
      $url = drupalSettings.gavias_sliderlayer.base_url + $(this).val();
      setSlideSackground($url);
    });

  function insertImageToLayer(url){
    var layerid = 'layer-' + $cxt;
    var img = $('<img>').attr('src', url);
    $('#'+layerid).find('.inner').html(img);
    var image = new Image();
    image.onload = function() {
      $('#'+layerid).width(this.width);
      $('#'+layerid).height(this.height);
      $('input[name=width]').val(this.width);
      $('input[name=height]').val(this.height);
    }
    image.src = url;
  }

  function setSlideSackground(url){
    jQuery('#gavias_slider_single').css({
      backgroundImage:'url('+url+')'
    });
  }

});

//========================================
  function notify(style, text) {
    $.notify({
        title: 'Notification',
        text: text,
        image: '<i class="fa fa-bell" style="font-size: 30px;color: #fff;"></i>',
        hideAnimation: 'slideUp',
    }, {
        style: 'metro',
        className: style,
        autoHide: true,
        clickToHide: true,
    });
  }

  function load_slider() {
    $settings = $.extend(true, deslider, $settings);
    if ($settings.background_image_uri != '') {
      $('#gavias_slider_single').css({
        'background-image': 'url(' + drupalSettings.gavias_sliderlayer.base_url  + $settings.background_image_uri + ')'
      })
    } else {
      $('#gavias_slider_single').css({
        backgroundImage: 'none'
      })
    }

    jQuery('.slide-option').each(function (index) {
      if (typeof $settings[jQuery(this).attr('name')] != "undefined") {
        jQuery(this).val($settings[jQuery(this).attr('name')]);
      } else {
        jQuery(this).val('');
      }
    });
    load_layers();
  }

  function save_slider() {
    $('input.slide-option, select.slide-option').each(function (index) {
      $settings[$(this).attr('name')] = $(this).val();
    });
  }

  function gva_sl_append_config_layer(l, i, t){
    l.title = l.title || 'Layer ' + (i + 1);
    var title = $('<span>').text($layers[i].title);
    var remove = $('<span>').text('').addClass('remove-layer fa fa-times');
    var clone = $('<span>').text('').addClass('fa fa-clone');
    var move = $('<span>').text('').addClass('move fa fa-arrows');
    title.click(function () {
      save_layer();
      load_layer(i);
    })
    clone.click(function () {
      save_layer();
      duplicate_layer(i);
    })
    remove.click(function () {
      remove_layer(i);
    })
    t.append(title).append(remove).append(clone).append(move);
  }

  function load_layers() {
    $('#gavias_slider_single').find('div').remove();
    $('#gavias_list_layers').find('li').remove();
    
    if (typeof $layers == 'undefined') {
      $layers = new Array();
    }
    $($layers).each(function (i) {
      if($layers[i])
        if($layers[i].removed != 1){
          gva_sl_add_tab_layer(i);
        }
    })
    $('.layer-option').val('');
      if ((typeof $layers[0] != 'undefined')) {
        load_layer(0);
      }
    }

  function add_layer() {
    save_layer();
    var j = $layers.length;
    $layers[j] = {};
    $start_layer = 0;
    $.each($layers, function(index, layer){
      if(parseInt(layer['data_time_start']) > $start_layer){
        $start_layer = layer['data_time_start'];
      }
    });

    delayer['data_time_start'] = parseInt($start_layer) + 500;
    $.extend(true, $layers[j], delayer);
    gva_sl_add_tab_layer(j);
    load_layer(j);
  }

  function gva_sl_add_tab_layer(i) {
     var layertype = $layers[i].type;
     var t = $('<li>').attr('index', i).addClass(layertype);
        
     gva_sl_append_config_layer($layers[i], i, t);

     $('ul#gavias_list_layers').append(t);

     var newdelayer = $('<div>').addClass('layer tp-caption').attr('id', 'layer-' + i);
     newdelayer.addClass('caption');
     if (typeof $layers[i].text_style == 'undefined') {
        $layers[i].text_style = 'text';
     }
     if ($layers[i].type == 'text') {
        newdelayer.addClass($layers[i].text_style);
     }
     var content = '';
     switch ($layers[i].type) {
        case 'image':
        var url = drupalSettings.gavias_sliderlayer.base_url  + $layers[i].image_uri;
          content = '<img src="' + url + '" />';
          var img = new Image();
          img.onload = function() {
            newdelayer.width($layers[i].width || this.width);
            newdelayer.height($layers[i].height || this.height);
          }
          img.src = drupalSettings.gavias_sliderlayer.base_url  + $layers[i].image_uri;
          break;
        case 'text':
          content = $layers[i].text;
     }
     var inner = $('<div>').addClass('inner');
     if($layers[i].custom_css){
        inner.attr('style',$layers[i].custom_css);
     };
     if($layers[i].custom_class){
        inner.addClass($layers[i].custom_class);
     };
     if($layers[i].custom_style){
      inner.addClass($layers[i].custom_style);
    }
     inner.html(content);
     newdelayer.append(inner);
     var zIndex = 99 - $layers[i].index;
     newdelayer.mousedown(function () {
        save_layer();
        load_layer(i);
     }).draggable({
        containment: "parent",
        drag: function (event, ui) {
            ui.helper.width('auto');
            ui.helper.height('auto');
            var left = $('input[name=left]').val();
            if(left=='center'){
                $('input[name=left]').val('center');
                $('input[name=top]').val(ui.position.top);
                ui.position.left = '50%';
                set_layer_position(i, ui.position.top, ui.position.left);
                ui.helper.css('margin-left', ui.helper.width/2)
            }else if(left=='left'){
                $('input[name=left]').val('left');
                $('input[name=top]').val(ui.position.top);
                ui.position.left = '10';
                ui.position.right = 'auto';
                set_layer_position(i, ui.position.top, ui.position.left);
            }else if(left=='right'){
                $('input[name=left]').val('right');
                $('input[name=top]').val(ui.position.top);
                ui.position.left = 'auto';
                ui.position.right = '0';
                set_layer_position(i, ui.position.top, ui.position.left);
            }else{
                $('input[name=left]').val(ui.position.left);
                $('input[name=top]').val(ui.position.top);
                set_layer_position(i, ui.position.top, ui.position.left);
            }
        },
        grid: [5, 5]
     })
     .resizable({
        aspectRatio: $layers[i].type=='image',
        resize: function (event, ui) {
          $('input[name=width]').val(ui.size.width);
          $('input[name=height]').val(ui.size.height);
        }
     });
     $('#gavias_slider_single').append(newdelayer);
     var left = $layers[i].left;
     var layer_style = $layers[i].type;
     var _width = newdelayer.width();
     if(layer_style== 'image') {
        _width = $layers[i].width;
     }
     if(left=='center'){
        newdelayer.css({
          'left': '50%',
          'margin-left' : -(_width/2),
        })
     }else if(left=='left'){
        newdelayer.css({
          'left': 0
        })
     }else if(left=='right'){
        newdelayer.css({
          'right': 0
        })
     }else{
        newdelayer.css({
          'left': left + 'px'
        })
     }
     newdelayer.css({
        top: $layers[i].top + 'px',
        zIndex: zIndex
     });

     $('#layeroptions').show(0);

     $('#gavias_list_layers').sortable({
           handle: '.move',
           update: function (event, ui) {
              $('#gavias_list_layers').find('li').each(function (index) {
                    var lindex = $(this).attr('index');
                    $layers[lindex].index = index;
                    $('#layer-'+lindex).css({zIndex:(99-index)});
                    save_layer();
              });
              $layers.sort(GaviasCompare);
              save_slider();
              load_slider();
           }
     });
  }

  function duplicate_layer(i) {
    save_layer();
    var j = $layers.length;
    $layers[j] = {};
    $start_layer = 0;
    $.each($layers, function(index, layer){
      if(parseInt(layer['data_time_start']) > $start_layer){
        $start_layer = layer['data_time_start'];
      }
    });
    
    $.extend(true, $layers[j], $layers[i]);
    gva_sl_add_tab_layer(j);
    load_layer(j);
  }

 function load_type_layer(type){
    $('.g-content-setting').each(function(){
      $(this).css('display','none');
    });
    $('#content-'+ type).css('display','block');
    $layers[$cxt].type = type;
    $('ul#gavias_list_layers li.active').removeClass('image text').addClass(type);
    if (type == 'image') {
      var op = $('#0-' + $cxt).resizable("option");
      $('#layer-' + $cxt).resizable( "destroy");
      op.aspectRatio = true;
      $('#layer-' + $cxt).resizable(op);
    } else if (type == 'text') {
      $('#content-'+ type).find('textarea[id=layer-text]').trigger('keyup');
      var op2 = $('#layer-' + $cxt).resizable( "option");
      $('#layer-' + $cxt).resizable( "destroy");
      op2.aspectRatio = false;
      $('#layer-' + $cxt).resizable(op2);
    } 
  }

  function load_layer(i) {
    $cxt = i;
    $('.layer').removeClass('selected');
    $('#layer-' + i).addClass('selected');
    $('ul#gavias_list_layers').find('li').removeClass('active');
    $('ul#gavias_list_layers').find('li[index=' + i + ']').addClass('active');

    $('.layer-option').each(function (index) {
      if (typeof $layers[i][$(this).attr('name')] != 'undefined') {
        if($(this).attr('name')=='data_time_end' || $(this).attr('name')=='data_time_start'){
          $(this).val($layers[i][$(this).attr('name')]).trigger('change');
        }else{
          $(this).val($layers[i][$(this).attr('name')]);
        }
      } else {
        $(this).val('');
      }
    });
    var type = $layers[i]['select_content_type'];
    load_type_layer(type);

    $('.select-content-type').change(function(){
        var type = $(this).val();
        load_type_layer(type);
    });
  }

  function set_layer_position($i, top, left) {
    $layers[$i].top = top;
    $layers[$i].left = left;
  }
  window.set_layer_position = set_layer_position;

  function save_layer() {
    if ($layers.length == 0) {
      return;
    }
    $('.layer-option').each(function (index) {
      if($(this) != 'undefined'){
        $layers[$cxt][$(this).attr('name')] = $(this).val();
      }
    })
  }

  function remove_layer(i) {
    $('#layer-' + i).remove();
    $layers[i]['removed'] = 1;
    $('ul#gavias_list_layers').find('li[index=' + i + ']').remove();
    if (i == $cxt) {
      if($('ul#gavias_list_layers li').length > 0){
        var f = parseInt($('ul#gavias_list_layers').find('li:first').attr('index'));
        load_layer(f);
      }
    }
  }

  function save_layer_slider() {
    save_slider();
    save_layer();
    var layers = [];

    $layers.sort(GaviasCompare);
    $.each($layers,function(index, layer){
      if(layer.removed == 0){
        layers[layers.length] = layer;
      }
    });
    $layers = layers;
    var settings = $.extend(true, {}, $settings);
    
    //console.log($settings);
    //console.log($layers);
    //console.log($layers); return;
    var datasettings = base64Encode(JSON.stringify(settings));

    var datalayers = base64Encode(JSON.stringify(layers));

    var gid = $('input[name=gid]').val();
    var sid = $('input[name=sid]').val();
    var title = $('input[name=title]').val();
    var sort_index = $('input[name=sort_index]').val();
    var status = $('select[name=status]').val();
    var background_image_uri = $('input[name=background_image_uri]').val();
    var data = {
      sort_index: sort_index,
      status: status,
      title: title,
      sid: sid,
      gid: gid,
      background_image_uri: background_image_uri,
      datalayers: datalayers,
      settings: datasettings
    };
    $.ajax({
      url: drupalSettings.gavias_sliderlayer.save_url,
      type: 'POST',
      data: data,
      dataType: 'json',
      success: function (data) {
        $('#save').val('Save');
        notify('success', 'Slider updated');
        $('#save').removeAttr('disabled');
        window.location = data['url_edit'];
       
      },
      error: function (jqXHR, textStatus, errorThrown) {
        notify('black', 'Slider not updated');
        $('#save').removeAttr('disabled');
      }
    });
  }
})(jQuery);
