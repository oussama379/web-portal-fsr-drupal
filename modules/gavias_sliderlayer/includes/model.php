<?php
function gavias_sliderlayer_load($sid) {
  $result = db_select('{gavias_sliderlayers}', 'd')
      ->fields('d')
      ->condition('id', $sid, '=')
      ->orderBy('sort_index', 'ESC')
      ->execute()
      ->fetchObject();
  $sliderlayers = new stdClass();
  if($result){
    $sliderlayers->title =  $result->title;
    $sliderlayers->status =  $result->status;
    $sliderlayers->sort_index =  $result->sort_index;
    $json = base64_decode($result->layersparams);
    $sliderlayers->layers = json_decode($json);  
    $json = base64_decode($result->params);
    $sliderlayers->settings = json_decode($json);   
  }
  return $sliderlayers;
}

function gavias_sliders_by_group($gid=0) {
  $result = db_select('{gavias_sliderlayers}', 'd')
          ->fields('d')
          ->condition('group_id', $gid, '=')
          ->orderBy('sort_index', 'ESC')
          ->execute();
  return $result;
}

function gavias_slider_load_frontend($sid=0) {
  $group = db_select('{gavias_sliderlayergroups}', 'd')
          ->fields('d')
          ->condition('id', $sid, '=')
          ->execute()
          ->fetchObject();

  $slides = db_select('{gavias_sliderlayers}', 'd')
            ->fields('d')
            ->condition('group_id', $sid, '=')
            ->orderBy('sort_index', 'ESC')
            ->execute();     

  $slideshow = new stdClass();

  if(!$group) return false;
  if(!$slides) return false;
  $json = base64_decode($group->params);
  $slideshow->settings = json_decode($json);
   //print "<pre>" + $sid; print( $group->params ); die();
  //Setting layers
  $i=0;
  foreach ($slides as $slide) {
    $json_slide = base64_decode($slide->params);
    $slideparams = json_decode($json_slide);
    $slideshow->slides[$i] = $slideparams;
    if (!empty($slideshow->slides[$i]->background_image_uri)) {
      $slideshow->slides[$i]->background_image = ($slideshow->slides[$i]->background_image_uri);
    }

    $json_layers = base64_decode($slide->layersparams);
    $slidelayers = json_decode($json_layers);
    $slideshow->slides[$i]->layers = $slidelayers;
    if (!empty($slideshow->slides[$i]->background_image_uri)) {
      $slideshow->slides[$i]->background_image = ($slideshow->slides[$i]->background_image_uri);
    }

    for ($j = 0; $j < count($slideshow->slides[$i]->layers); $j++) {
      if ($slideshow->slides[$i]->layers[$j]->type == 'image' && !empty($slideshow->slides[$i]->layers[$j]->image_uri)) {
        $slideshow->slides[$i]->layers[$j]->image = ($slideshow->slides[$i]->layers[$j]->image_uri);
      }
    }
    $i++;
  }
  return $slideshow;
}

function getListSliderGroups(){
    $result = db_select('{gavias_sliderlayergroups}', 'd')
          ->fields('d')
          ->execute()
          ->fetchObject();
    return $result;
}

function getSliderGroup($gid){
    $result = db_select('{gavias_sliderlayergroups}', 'd')
          ->fields('d')
          ->condition('id', $gid, '=')
          ->execute()
          ->fetchObject();
    return $result;
}

function gavias_sliderlayer_export($gid){
  $result = new stdClass();
    $result->group = getSliderGroup($gid);
    $result->sliders = array();
    $sliders = gavias_sliders_by_group($gid);

    $i = 0;
    foreach ($sliders as $key => $slider) {
      $result->sliders[$i] = new stdClass();
      $result->sliders[$i]->title = $slider->title;
      $result->sliders[$i]->sort_index = $slider->sort_index;
      $result->sliders[$i]->group_id = $slider->group_id;
      $result->sliders[$i]->params = $slider->params;
      $result->sliders[$i]->layersparams =$slider->layersparams ;
      $result->sliders[$i]->status = $slider->status;
      $result->sliders[$i]->background_image_uri = $slider->background_image_uri;
      $i++;
    }

    $data = json_encode($result);
    $data = base64_encode($data);
    return $data;
}