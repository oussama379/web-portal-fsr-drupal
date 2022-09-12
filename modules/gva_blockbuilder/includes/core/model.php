<?php
function gavias_blockbuilder_load($pid) {
  $result = db_select('{gavias_blockbuilder}', 'd')
          ->fields('d')
          ->condition('id', $pid, '=')
          ->execute()
          ->fetchObject();
  $page = new stdClass();
  if($result){
    $page->title =  $result->title;
    $page->body_class = $result->body_class;  
    $page->params = $result->params;  
  }else{
    return false;
  }
  return $page;
}

function gavias_blockbuilder_load_by_machine($mid) {
  $result = db_select('{gavias_blockbuilder}', 'd')
          ->fields('d', array('id', 'title', 'params', 'body_class'))
          ->condition('body_class', $mid, '=')
          ->execute()
          ->fetchObject();
  $page = new stdClass();
  if($result){
    $page->id = $result->id;
    $page->title = $result->title;
    $page->body_class = $result->body_class;  
    $page->params = $result->params;  
  }else{
    return false;
  }
  $result = null;
  return $page;
}

function gavias_blockbuilder_check_machine($id, $mid){
  $result = db_select('{gavias_blockbuilder}', 'd')
          ->fields('d')
          ->condition('id', $id , '<>')
          ->condition('body_class', $mid, '=')
          ->execute()
          ->fetchObject();
  if($result && $result->body_class){
    return true;
  }   
  return false;
      
}

function gavias_blockbuilder_get_list(){
    $result = db_select('{gavias_blockbuilder}', 'd')
          ->fields('d')
          ->execute();
    return $result;
}