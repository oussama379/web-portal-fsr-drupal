<?php
function gavias_sliderlayer_block_content($sid) {
  global $base_url;
  $slideshow = gavias_slider_load_frontend($sid);
  if(!$slideshow) return 'No slider selected';
  $module_path = drupal_get_path('module', 'gavias_sliderlayer');

  //Setting 
  $settings = $slideshow->settings;

  $ss = new stdClass();
  $ss->delay = isset($settings->delay) ? (int)$settings->delay : 9000;
  $ss->gridheight = isset($settings->gridheight) ? (int)$settings->gridheight : 600;
  $ss->gridwidth = isset($settings->gridwidth) ? (int)$settings->gridwidth : 1170;
  $ss->minHeight = isset($settings->minheight) ? (int)$settings->minheight : 0;
  $ss->autoHeight = 'off';
  $ss->sliderType = "standard";
  $ss->sliderLayout = isset($settings->sliderlayout) ? $settings->sliderlayout : 'auto';  // auto, fullwidth, fullscreen      
  $ss->fullScreenAutoWidth="on";       
  $ss->fullScreenAlignForce="off";
  $ss->fullScreenOffsetContainer="";      
  $ss->fullScreenOffset="0";
  $ss->hideCaptionAtLimit=0;               
  $ss->hideAllCaptionAtLimit=0;            
  $ss->hideSliderAtLimit=0;                                    
  $ss->disableProgressBar= isset($settings->progressbar_disable) ? $settings->progressbar_disable : "on";             
  $ss->stopAtSlide=-1;                    
  $ss->stopAfterLoops=-1;                  
  $ss->shadow= isset($settings->shadow) ? $settings->shadow : 0;                       
  $ss->dottedOverlay = isset($settings->dottedOverlay) ? $settings->dottedOverlay : 'none';        
  $ss->startDelay=0;                                
  $ss->lazyType = "none";                
  $ss->spinner = "spinner0";
  $ss->shuffle = "off"; 
  $ss->debugMode = 0;
  //print "<pre>"; print_r($settings); die();

  $ss->parallax = new stdClass();
  $ss->parallax->type = 'off';
  if(isset($settings->parallax_scroll)){
    if($settings->parallax_scroll == 'mouse+scroll'){
      $ss->parallax->type = 'mouse+scroll';
      $ss->parallax->bgparallax = 'on';
    }
  }

  $ss->navigation = new stdClass();

  $ss->navigation->onHoverStop = isset($settings->onhoverstop) ? $settings->onhoverstop : 'on';

  $ss->navigation->touch = new stdClass();
  $ss->navigation->touch->touchenabled = 'on';

  $ss->navigation->arrows = new stdClass();
  $ss->navigation->arrows->enable = (isset($settings->arrow_enable) && $settings->arrow_enable =='true') ? true : false;

  $ss->navigation->arrows->left = new stdClass();
  $ss->navigation->arrows->left->h_align = isset($settings->navigationLeftHAlign) ? $settings->navigationLeftHAlign : 'left';
  $ss->navigation->arrows->left->v_align = isset($settings->navigationLeftVAlign) ? $settings->navigationLeftVAlign : 'center';
  $ss->navigation->arrows->left->h_offset = isset($settings->navigationLeftHOffset) ? (int)$settings->navigationLeftHOffset : 0;
  $ss->navigation->arrows->left->v_offset = isset($settings->navigationLeftVOffset) ? (int)$settings->navigationLeftVOffset : 20;

  $ss->navigation->arrows->right = new stdClass();
  $ss->navigation->arrows->right->h_align = isset($settings->navigationRightHAlign) ? $settings->navigationRightHAlign : 'right';
  $ss->navigation->arrows->right->v_align = isset($settings->navigationRightVAlign) ? $settings->navigationRightVAlign : 'center';
  $ss->navigation->arrows->right->h_offset = isset($settings->navigationRightHOffset) ? (int)$settings->navigationRightHOffset : 0;
  $ss->navigation->arrows->right->v_offset = isset($settings->navigationRightVOffset) ? (int)$settings->navigationRightVOffset : 20;

  $ss->navigation->bullets = new stdClass();
  $ss->navigation->bullets->enable = (isset($settings->bullets_enable) && $settings->bullets_enable =='true') ? true : false;
  $ss->navigation->bullets->v_align = 'bottom';
  $ss->navigation->bullets->h_align = 'center';
  $ss->navigation->bullets->v_offset = 20;
  $ss->navigation->bullets->space = 10;
  $ss->navigation->bullets->tmp = '';
  
  $ss->parallax = new stdClass();
  $ss->parallax->type = "mouse";
  $ss->parallax->origo = "slidercenter";
  $ss->parallax->speed = 2000;
  $ss->parallax->levels = [4,5,6,7,12,16,10,50,46,47,48,49,50,55];

  $ss = json_encode($ss);
  $slide_settings['id'] = $sid;
  $slide_settings['slides'] = $slideshow->slides;
  $slide_settings['settings'] = $slideshow->settings;

  $slide_settings['scount'] = count($slideshow->slides);
  $slide_settings['ss'] = $ss;

  return gavias_sliderlayer_slides($slide_settings);

}

function gavias_sliderlayer_slides($vars){
  global $base_url;
  $slides = $vars['slides'];
  $scount = $vars['scount'];
  $settings = $vars['settings'];
  $vars['attributes_array']['class'] = 'gavias_sliderlayer rev_slider_wrapper fullwidthbanner-container';
  $style = array();
  $style[] = 'height:'. ((isset($settings->gridheight) && $settings->gridheight) ? $settings->gridheight : '700') . 'px';
  $vars['attributes_array']['style'] = implode($style, ';');
  
  $vars['content'] = '';
  $i = 1;
  foreach($slides as $slide){
    if($slide->status){
      $slide_settings['slide'] = $slide;
      $slide_settings['scount'] = $scount;
      $slide_settings['settings'] = $settings;
      $vars['content'] .= gavias_sliderlayer_slide($slide_settings, $i);
      $i = $i + 1;
    }  
  }

  $vars['attributes'] = '';
  foreach($vars['attributes_array'] as $key => $attr){
    $vars['attributes'] .= $key . '=' . '"' . $attr . '" ';
  }

  extract($vars);
  ob_start();
    include GAV_SLIDERLAYER_PATH . '/templates/frontend/slides.php';
  $output = ob_get_clean();
  return $output;
}

function gavias_sliderlayer_slide($vars, $index){
  global $base_url;
  $module_path = drupal_get_path('module', 'gavias_sliderlayer');
  $slide = $vars['slide'];
  $layers = $slide->layers;
  $scount =  $vars['scount'];
  $settings = $vars['settings'];
  $vars['attributes_array']['data-transition'] = $slide->data_transition;
  $vars['attributes_array']['data-easein'] = $slide->slide_easing_in;
  $vars['attributes_array']['data-easeout'] = $slide->slide_easing_out;
  $vars['attributes_array']['data-slotamount'] = '7';
  $vars['attributes_array']['data-kenburns'] = 'off';
  $vars['attributes_array']['data-masterspeed'] = 'default';
  $vars['attributes_array']['data-index'] = 'rs-' . $index;

  if(isset($slide->overlay_enable) && $slide->overlay_enable=='on'){
    $vars['attributes_array']['class'] = 'gavias-overlay ';
  }

  if(!isset($slide->scalestart)){$slide->scalestart=0;}
  if(!isset($slide->scaleend)){$slide->scaleend=0;}
  if(!isset($slide->data_parallax)){$slide->data_parallax=0;}
  if(!$slide->background_color){$slide->background_color='#f2f2f2';}
  $data_kenburns = 'off';
  if(isset($slide->scalestart) && $slide->scalestart && $slide->scalestart != 0){
    $data_kenburns = 'on';
  }
  $path_image = substr(base_path(), 0, -1);
  if(!isset($slide->delay)) $slide->delay = 300;
  if($slide->background_image_uri){
    $vars['content'] = "<img class=\"rev-slidebg\" src=\"{$path_image}{$slide->background_image_uri}\" alt=\"\"  data-duration=\"{$slide->delay}\" data-bgparallax=\"{$slide->data_parallax}\"  data-scalestart=\"{$slide->scalestart}\" data-scaleend=\"{$slide->scaleend}\" data-kenburns=\"{$data_kenburns}\"  data-bgrepeat=\"{$slide->background_repeat}\" style=\"background-color:{$slide->background_color}\" data-bgfit=\"{$slide->background_fit}\" data-bgposition=\"{$slide->background_position}\" />";
  }else{
    $vars['content'] = "<img class=\"rev-slidebg\" src=\"{$path_image}/{$module_path}/vendor/revolution/assets/transparent.png\" data-bgrepeat=\"repeat\" style=\"background-color:{$slide->background_color}\" />";
  }
  $zindex = count($layers) + 1;

  foreach($layers as $layer){
    $layer_settings['layer'] = $layer;
    $layer_settings['zindex'] = $zindex--;
    $layer_settings['scount'] = $scount;
    $layer_settings['settings'] = $settings;
    $vars['content'] .= gavias_sliderlayer_layer($layer_settings);
  }

  $vars['attributes'] = '';
  foreach($vars['attributes_array'] as $key => $attr){
    $vars['attributes'] .= $key . '=' . '"' . $attr . '" ';
  }


  // Array
  $vars['attributes_video_array'] = array();
  if(isset($slide->video_source) && (isset($slide->youtube_video) || isset($slide->vimeo_video) || isset($slide->html5_mp4))){
    if($slide->video_source &&($slide->youtube_video || $slide->vimeo_video || $slide->html5_mp4)){
      $vars['attributes_video_array']['data-forcerewind'] = 'on';
      $vars['attributes_video_array']['data-volume'] = 'mute';
      if(!isset($slide->video_youtube_args) && empty($slide->video_youtube_args)){
        $slide->video_youtube_args = 'version=3&enablejsapi=1&html5=1&hd=1&wmode=opaque&showinfo=0&ref=0;origin=http://server.local;autoplay=1;';
      }
      if(!isset($slide->video_vimeo_args) && empty($slide->video_vimeo_args)){
        $slide->video_vimeo_args = 'title=0&byline=0&portrait=0&api=1';
      }
      if($slide->video_source == 'youtube'){
        $vars['attributes_video_array']['data-videoattributes'] = $slide->video_youtube_args;
      }
      if($slide->video_source == 'vimeo'){
        $vars['attributes_video_array']['data-videoattributes'] = $slide->video_vimeo_args;
      }
      if($slide->video_source == 'html5'){
        $vars['attributes_video_array']['data-nextslideatend'] = isset($slide->mp4_nextslideatend) ? $slide->mp4_nextslideatend : true;
        $vars['attributes_video_array']['data-videoloop'] = isset($slide->mp4_videoloop) ? $slide->mp4_videoloop : 'loopandnoslidestop';
      }
      $vars['attributes_video_array']['data-videorate'] = '1.5';
      $vars['attributes_video_array']['data-videowidth'] = '100%';
      $vars['attributes_video_array']['data-videoheight'] = '100%';
      $vars['attributes_video_array']['data-videocontrols'] = 'none';
      if($slide->video_source == 'youtube' && $slide->youtube_video){
        $vars['attributes_video_array']['data-ytid'] = $slide->youtube_video;
      }
      if($slide->video_source == 'vimeo' && $slide->vimeo_video){
        $vars['attributes_video_array']['data-vimeoid'] = $slide->vimeo_video;
      }
      if($slide->video_source == 'html5' && $slide->html5_mp4){
        $vars['attributes_video_array']['data-videomp4'] = $slide->html5_mp4;
      }
      if(isset($slide->video_start_at) && $slide->video_start_at){
        $vars['attributes_video_array']['data-videostartat'] = $slide->video_start_at;
      }
      if(isset($slide->video_end_at) && $slide->video_end_at){
        $vars['attributes_video_array']['data-videoendat'] = $slide->video_end_at;
      }
      //$vars['attributes_video_array']['data-videoloop'] = 'loopandnoslidestop';
      $vars['attributes_video_array']['data-forceCover'] = '1';
      $vars['attributes_video_array']['data-aspectratio'] = '16:9';
      $vars['attributes_video_array']['data-autoplay'] = 'true';
      $vars['attributes_video_array']['data-autoplayonlyfirsttime'] = 'false';
      //$vars['attributes_video_array']['data-nextslideatend'] = 'false';

      $vars['attributes_video_array']['class'] = 'rs-background-video-layer';

      $vars['attributes_video'] = '';
      foreach($vars['attributes_video_array'] as $key => $attr){
        $vars['attributes_video'] .= $key . '=' . '"' . $attr . '" ';
      }
    }
  }


  extract($vars);
  ob_start();
    include GAV_SLIDERLAYER_PATH . '/templates/frontend/slide.php';
  $output = ob_get_clean();
  return $output;
}

function gavias_sliderlayer_layer($vars){
  global $base_url;
  $module_path = drupal_get_path('module', 'gavias_sliderlayer');
  $layer = $vars['layer'];
  $scount = $vars['scount'];
  $settings = $vars['settings'];
  $vars['attributes_array']['class'] = 'tp-caption ';
  
  if($layer->type=='text'){
    $vars['attributes_array']['class'] .= $layer->text_style . ' ';
  }
  if(isset($layer->custom_class) && $layer->custom_class){
    $vars['attributes_array']['class'] .= $layer->custom_class . ' ';
  }
  if(isset($layer->custom_style) && $layer->custom_style){
    $vars['attributes_array']['class'] .= $layer->custom_style . ' ';
  }
  
  $vars['attributes_array']['data-x'] = $layer->left;
  $vars['attributes_array']['data-y'] = $layer->top;

  $vars['attributes_array']['data-start'] = $layer->data_time_start;
 
  $settings_delay = (isset($settings->delay) && $settings->delay) ? $settings->delay : 9000;

  if($scount > 1 && ($layer->data_time_end + 20) < $settings_delay  && ((int)$layer->data_time_end > (int)$layer->data_time_start)){
    $vars['attributes_array']['data-end'] = $layer->data_time_end;
  }  

  $vars['attributes_array']['data-transform_idle'] ="o:1;";
  
  $transform_in = '';
  if($layer->incomingclasses){
    $tmp = gaviasGetArrAnimations()[$layer->incomingclasses];
    if($tmp){
      $params = (array)$tmp['params'];
      if(isset($params['split']) && $params['split'] && $params['split'] == 'chars') {
        $vars['attributes_array']['data-splitin'] = $params['split'];
        $vars['attributes_array']['data-elementdelay'] = 0.1;
       $vars['attributes_array']['class'] .='tp-resizeme splitted ';
      }
      $transform_in = parseCustomAnimationByArray($params);
      $search = array('opacity', 'scaleX', 'scaleY', 'skewX','skewY','rotationX', 'rotationY','rotationZ','pers');
      $replace = array('opacity', 'sX', 'sY', 'skX', 'skY', 'rX', 'rY', 'rZ', 'tP');
      $transform_in = str_replace($search, $replace, $transform_in);
    }  
  }
  $transform_in .= "s:{$layer->data_speed};e:{$layer->data_easing};";
  $vars['attributes_array']['data-transform_in'] = $transform_in;

  $transform_out = '';
  if($layer->outgoingclasses){
    $tmp_out = gaviasGetArrEndAnimations()[$layer->outgoingclasses];

    if($tmp_out){
      $params_out = (array)$tmp_out['params'];
      $transform_out = parseCustomAnimationByArray($params_out);
      $search = array('opacity', 'scaleX', 'scaleY', 'skewX','skewY','rotationX', 'rotationY','rotationZ','pers');
      $replace = array('opacity', 'sX', 'sY', 'skX', 'skY', 'rX', 'rY', 'rZ', 'tP');
      $transform_out = str_replace($search, $replace, $transform_out);
    }else{
      $transform_out .= 'auto:auto;';
    } 

    $transform_out .= "s:{$layer->data_end};e:{$layer->data_endeasing};";

    $vars['attributes_array']['data-transform_out'] = $transform_out;
  }

  if($layer->custom_css){
    $custom_css = trim(preg_replace('/\s+/', ' ', $layer->custom_css));
    $vars['attributes_array']['style'] = 'z-index:'.$vars['zindex'].';'.$custom_css;
  }else{
    $vars['attributes_array']['style'] = 'z-index:'.$vars['zindex'];
  }
  if($layer->incomingclasses == 'customin'){
    $vars['attributes_array']['data-customin'] = $layer->customin;
  }
  if($layer->outgoingclasses == 'customout'){
    $vars['attributes_array']['data-customout'] = $layer->customout;
  }

  switch($layer->type){
    case 'text':
      if($layer->link){
        $vars['content'] =  "<a href=\"{$layer->link}\">{$layer->text}</a>";
      }else{
        $vars['content'] = $layer->text;
      }
      break;
    case 'image':

      $width = $layer->width;
      $height = $layer->height;
      $image = $layer->image;
      $path_image = substr(base_path(), 0, -1);
      if($layer->link){
        $vars['content'] = "<a href=\"{$layer->link}\"><img alt=\"\" style=\"width: {$width}; height: auto;\" src=\"{$path_image}{$image}\"/></a>";
      }else{
        //$vars['content'] = "<div class=\"layer-style-image\" style=\"width: {$width}px; height: auto;\"><img alt=\"\" src=\"{$path_image}{$image}\"/></div>";
        $vars['content'] = "<img alt=\"\" src=\"{$path_image}{$image}\"/>";
      }
      break;
  }
    $vars['attributes'] = '';
    foreach($vars['attributes_array'] as $key => $attr){
      $vars['attributes'] .= $key . '=' . '"' . $attr . '" ';
    }

    extract($vars);
    ob_start();
      include GAV_SLIDERLAYER_PATH . '/templates/frontend/layer.php';
    $output = ob_get_clean();
    return $output;
}
