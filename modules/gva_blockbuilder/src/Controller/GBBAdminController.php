<?php
/**
 * @file
 * Contains \Drupal\gavias_blockbuilder\Controller\GBBAdminController.
 */

namespace Drupal\gavias_blockbuilder\Controller;
use  Drupal\Core\Cache\Cache;
use Drupal\Core\Url;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Response;

class GBBAdminController extends ControllerBase {

  public function gavias_blockbuilder_list(){

    $page['#attached']['library'][] = 'gavias_blockbuilder/gavias_blockbuilder.assets.admin';

    $header = array( 'ID', 'Title', 'Shortcode', 'Action');
    $results = db_select('{gavias_blockbuilder}', 'd')
            ->fields('d', array('id', 'title', 'body_class'))
            ->orderBy('title', 'ASC')
            ->execute();
    $rows = array();
    foreach ($results as $row) {

      $tmp =  array();
      $tmp[] = $row->id;
      $tmp[] = $row->title;
      $tmp[] = "[gbb name=\"{$row->body_class}\"]";
      $tmp[] = t('<a href="@link">Edit</a> | <a href="@link_2">Config</a> |  <a href="@link_3">Delete</a> | <a href="@link_4">Duplicate</a>', array(
            '@link' => \Drupal::url('gavias_blockbuilder.admin.add', array('bid' => $row->id)),
            '@link_2' => \Drupal::url('gavias_blockbuilder.admin.edit', array('bid' => $row->id)),
            '@link_3' => \Drupal::url('gavias_blockbuilder.admin.delete', array('bid' => $row->id)),
            '@link_4' => \Drupal::url('gavias_blockbuilder.admin.clone', array('bid' => $row->id)),
        ));
      $rows[] = $tmp;
    }
    $page['gbb-admin-list'] = array(
      '#theme' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#empty' => t('No Block builder block available. <a href="@link">Add Block Builder</a>.', array('@link' => \Drupal::url('gavias_blockbuilder.admin.add', array('bid'=>0)))),
    );
    return $page;
  }

  public function gavias_blockbuilder_save_element($data) {
    $gbb_els = array();
    //$data['gbb-items'] = $data['gbb-items'];
    $new = '';
    // row
    if( isset($data['gbb-row-id']) && is_array($data['gbb-row-id'])){
      foreach( $data['gbb-row-id'] as $rowID_k => $rowID ){
        $row = array();
        if( isset($data['gbb-rows']) && is_array($data['gbb-rows'])){
          foreach ( $data['gbb-rows'] as $row_attr_k => $row_attr ){
            $row['attr'][$row_attr_k] = $row_attr[$rowID_k];
          }
        }
        $row['columns'] = [];
        $gbb_els[] = $row;
      }
    
      $array_rows_id = array_flip( $data['gbb-row-id'] );

    } 
  //print_r($gbb_els);die();
    $col_row_id = array();
   // print_r($data['gbb-column-id']);die();
    if( isset($data['gbb-column-id']) && is_array($data['gbb-column-id'])){
      foreach( $data['gbb-column-id'] as $column_id_key => $column_id ){
        if($column_id){
          $column = array();
          if( isset($data['gbb-columns']) && is_array($data['gbb-columns'])){
            foreach ( $data['gbb-columns'] as $col_attr_k => $col_attr ){
              $column['attr'][$col_attr_k] = $col_attr[$column_id_key];
            }
          }
          $column['items'] = [];

          $parent_row_id = $data['column-parent'][$column_id_key];
          $new_parent_row_id = $array_rows_id[$parent_row_id];
          if(isset($gbb_els[$new_parent_row_id])){
            $gbb_els[$new_parent_row_id]['columns'][$column_id] = $column;
          }
          $col_row_id[$column_id] = $new_parent_row_id;
        }
      }  
    } 

    // items 
    if( key_exists('element-type', $data) && is_array($data['element-type'])){
      $count = array();
      $count_tabs = array();
      
      foreach( $data['element-type'] as $type_k => $type ){ 
        $item = array();
        $item['type'] = $type;
        $item['size'] = 12;
        if(isset($data['element-size'][$type_k]) && $data['element-size'][$type_k]){
          $item['size'] = $data['element-size'][$type_k];
        }

        if( ! key_exists($type, $count) ) $count[$type] = 0;
        if( ! key_exists($type, $count_tabs) ) $count_tabs[$type] = 0;

        if( key_exists($type, $data['gbb-items']) ){ 
          foreach(  $data['gbb-items'][$type] as $attr_k => $attr ){

            if( $attr_k == 'tabs'){
              // field tabs fields
              $item['fields']['count'] = $attr['count'][$count[$type]];
              if( $item['fields']['count'] ){
                for ($i = 0; $i < $item['fields']['count']; $i++) {
                  $tab = array();
                  $tab['icon'] = stripslashes($attr['icon'][$count_tabs[$type]]);
                  $tab['title'] = stripslashes($attr['title'][$count_tabs[$type]]);
                  $tab['content'] = stripslashes($attr['content'][$count_tabs[$type]]);
                  $item['fields']['tabs'][] = $tab;
                  $count_tabs[$type]++;
                }
              }
            }else if(array_key_exists('textlangs', $attr)){ //textlangs
              $languages =  \Drupal::languageManager()->getLanguages();
              $tmp = stripslashes($attr['textlangs']['language__gvalangdefault'][$count[$type]]);
              $item['fields'][$attr_k]['textlangs']['language__gvalangdefault'] = $tmp;
              foreach ($languages as $key => $language) {
                $tmp = stripslashes($attr['textlangs']['language__' . $language->getId()][$count[$type]]);
                $item['fields'][$attr_k]['textlangs']['language__' . $language->getId()] = $tmp;
              }
            }else if(array_key_exists('htmllangs', $attr)){ //textarealangs
              $languages =  \Drupal::languageManager()->getLanguages();
              $tmp = stripslashes($attr['htmllangs']['language__gvalangdefault'][$count[$type]]);
              $item['fields'][$attr_k]['htmllangs']['language__gvalangdefault'] = $tmp;
              foreach ($languages as $key => $language) {
                $tmp = stripslashes($attr['htmllangs']['language__' . $language->getId()][$count[$type]]);
                $item['fields'][$attr_k]['htmllangs']['language__' . $language->getId()] = $tmp;
              }
            }
            else {
              $item['fields'][$attr_k] = stripslashes($attr[$count[$type]]);            
            }
          }
        }
        $count[$type] ++;
        $column_id = $data['element-parent'][$type_k];
        $parent_row_id = $data['element-row-parent'][$type_k];

        $new_parent_row_id = $array_rows_id[$parent_row_id];
        $new_column_id = $column_id;
        $gbb_els[$new_parent_row_id]['columns'][$new_column_id]['items'][] = $item;
      }
    }

    // save
    if( $gbb_els ){
      $new = base64_encode(json_encode($gbb_els));    
    }
    return $new;
  }


  public function gavias_blockbuilder_edit($bid) {
    require_once GAVIAS_BLOCKBUILDER_PATH . '/includes/utilities.php';
    require_once GAVIAS_BLOCKBUILDER_PATH .'/includes/backend.php';
    
    $page['#attached']['library'][] = 'gavias_blockbuilder/gavias_blockbuilder.assets.admin';
    $page['#attached']['drupalSettings']['gavias_blockbuilder']['check_imce'] = 'off';
    if(gavias_blockbuilder_imce()) {
      $page['#attached']['library'][] = 'imce/drupal.imce.input';
      $page['#attached']['drupalSettings']['gavias_blockbuilder']['check_imce'] = 'on';
    }

    $page['#attributes']['classes_array'][] = 'form-blockbuilder';

    $abs_url_config = \Drupal::url('gavias_blockbuilder.admin.save', array(), array('absolute' => FALSE)); 
    
    $page['#attached']['drupalSettings']['gavias_blockbuilder']['saveConfigURL'] = $abs_url_config;

    $abs_url_config = \Drupal::url('gavias_blockbuilder.admin.get_images_upload', array(), array('absolute' => FALSE));

    $page['#attached']['drupalSettings']['gavias_blockbuilder']['get_images_upload_url'] = $abs_url_config;

    $page['#attached']['drupalSettings']['gavias_blockbuilder']['base_path'] = base_path();


    $destination = false;
    
    if(isset($_GET['destination']) && $_GET['destination']){
      $url_redirect = $_GET['destination'];
      $destination = true;
    }else{
      $url_redirect = \Drupal::url('gavias_blockbuilder.admin.edit', array('bid' => $bid));
    }

    $page['#attached']['drupalSettings']['gavias_blockbuilder']['url_redirect'] = $url_redirect;
    
    $page['#attached']['drupalSettings']['gavias_blockbuilder']['destination'] = $destination;

    ob_start();
    include drupal_get_path('module', 'gavias_blockbuilder') . '/templates/backend/form.php';
    $content = ob_get_clean();
    $page['admin-form'] = array(
      '#theme' => 'admin-form',
      '#content' => $content
    );
    return $page;
  }

  public function gavias_blockbuilder_save(){
    header('Content-type: application/json');
    $data = $_REQUEST['data'];
    $pid = $_REQUEST['pid'];
    $params = '';
    if($data){
      $data = base64_decode($data);
      $data = json_decode($data, true);
      $params = $this->gavias_blockbuilder_save_element($data);
    } 
    if($params==null) $params = '';

    db_update("gavias_blockbuilder")
          ->fields(array(
              'params' => $params,
          ))
          ->condition('id', $pid)
          ->execute();
    
    \Drupal::service('plugin.manager.block')->clearCachedDefinitions();     
    foreach (Cache::getBins() as $service_id => $cache_backend) {
      if($service_id == 'render' || $service_id == 'page'){
        $cache_backend->deleteAll();
      }
    }

    $result = array(
      'data' => 'update saved'
    );
 
    print json_encode($result);
    exit(0);
  }

  public function gavias_blockbuilder_export($bid){
    $pbd_single = gavias_blockbuilder_load($bid);
    $data = $pbd_single->params;
    $title = date('Y_m_d_h_i_s') . '_bb_export'; 
    header("Content-Type: text/txt");
    header("Content-Disposition: attachment; filename={$title}.txt");
    print $data;
    exit;
  }

  public function gavias_upload_file(){
    // A list of permitted file extensions
    global $base_url;
    $allowed = array('png', 'jpg', 'gif','zip');
    $_id = gavias_blockbuilder_makeid(6);
    if(isset($_FILES['upl']) && $_FILES['upl']['error'] == 0){

      $extension = pathinfo($_FILES['upl']['name'], PATHINFO_EXTENSION);

      if(!in_array(strtolower($extension), $allowed)){
        echo '{"status":"error extension"}';
        exit;
      }  
      $path_folder = \Drupal::service('file_system')->realpath(file_default_scheme(). "://gbb-uploads");

      $ext = end(explode('.', $_FILES['upl']['name']));
      $image_name =  basename($_FILES['upl']['name'], ".{$ext}");

      //$file_path = $path_folder . '/' . $_id . '-' . $_FILES['upl']['name'];
      $file_path = $path_folder . '/' . $image_name . "-{$_id}" . ".{$ext}";

      $file_url = str_replace($base_url, '',file_create_url(file_default_scheme(). "://gbb-uploads"). '/' .  $image_name . "-{$_id}" . ".{$ext}"); 
      if (!is_dir($path_folder)) {
        @mkdir($path_folder); 
      }
      if(move_uploaded_file($_FILES['upl']['tmp_name'], $file_path)){
        $result = array(
          'file_url' => $file_url,
          'file_url_full' => $base_url . $file_url
        );
        print json_encode($result);
        exit;
        }
    }

    echo '{"status":"error"}';
    exit;
  }

  public function get_images_upload(){
    header('Content-type: application/json');
    global $base_url; 

    $file_path = \Drupal::service('file_system')->realpath(file_default_scheme(). "://gbb-uploads");

    $file_url = file_create_url(file_default_scheme(). "://gbb-uploads"). '/';
    $list_file = glob($file_path . '/*.{jpg,png,gif}', GLOB_BRACE);
    usort( $list_file, function( $a, $b ) { return filemtime($b) - filemtime($a); } );
    $files = array();
    $data = '';
    foreach ($list_file as $key => $file) {
      if(basename($file)){
        $file_url = str_replace($base_url, '', file_create_url(file_default_scheme(). "://gbb-uploads"). '/' .  basename($file)); 
        $files[$key]['file_url'] = $file_url;
        $files[$key]['file_url_full'] = $base_url . $file_url;
      }  
    }
    $result = array(
      'data' => $files
    );
    print json_encode($result);
    exit(0);
  }

}
