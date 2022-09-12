<?php

/**
 * @file
 * Contains \Drupal\gavias_sliderlayer\Controller\GroupSliderController.
 */

namespace Drupal\gavias_sliderlayer\Controller;

use Drupal\Core\Url;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Response;


class GroupSliderController extends ControllerBase {

  public function gavias_sl_group_list(){
  
    if(!db_table_exists('gavias_sliderlayergroups')){
      return "";
    }

    $header = array( 'ID', 'Name', 'Action');
    
    $results = db_select('{gavias_sliderlayergroups}', 'd')
            ->fields('d', array('id', 'title'))
            ->execute();
    $rows = array();

    foreach ($results as $row) {

      $tmp =  array();
      $tmp[] = $row->id;
      $tmp[] = $row->title;
      $tmp[] = t('<a href="@link_1">Edit Name</a> | <a href="@link_2">List Silders</a> | <a href="@link_3">Config General</a> | <a href="@link_5">Clone</a> | <a href="@link_6">Export</a> | <a href="@link_7">Import</a> | <a href="@link_4">Delete</a>', array(
            '@link_1' => \Drupal::url('gavias_sl_group.admin.add', array('sid' => $row->id)),
            '@link_2' => \Drupal::url('gavias_sl_sliders.admin.list', array('gid' => $row->id)),
            '@link_3' => \Drupal::url('gavias_sl_group.admin.config', array('gid' => $row->id)),
            '@link_5' => \Drupal::url('gavias_sl_group.admin.clone', array('sid' => $row->id)),
            '@link_6' => \Drupal::url('gavias_sl_group.admin.export', array('gid' => $row->id)),
            '@link_7' => \Drupal::url('gavias_sl_group.admin.import', array('gid' => $row->id)),
            '@link_4' => \Drupal::url('gavias_sl_group.admin.delete', array('gid' => $row->id, 'sid' => '0', 'action' => 'group'))
        ));
      $rows[] = $tmp;
    }
    return array(
      '#theme' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#empty' => t('No Slider available. <a href="@link">Add Slider</a>.', array('@link' => \Drupal::url('gavias_sl_group.admin.add', array('sid'=>0)))),
    );
  }

  public function gavias_sl_group_config($gid){
    global $base_url;
    $page['#attached']['library'][] = 'gavias_sliderlayer/gavias_sliderlayer.assets.config_global';
    $slideshow = getSliderGroup($gid);
    $settings = ((isset($slideshow->params) && $slideshow->params) ? json_decode(base64_decode($slideshow->params)):'{}');
    
    $save_url = \Drupal::url('gavias_sl_group.admin.config_save', array(), array('absolute' => FALSE));
    $page['#attached']['drupalSettings']['gavias_sliderlayer']['base_url'] = $base_url;
    $page['#attached']['drupalSettings']['gavias_sliderlayer']['save_url'] = $save_url;
    $page['#attached']['drupalSettings']['gavias_sliderlayer']['settings'] = $settings;

    ob_start();
    include GAV_SLIDERLAYER_PATH . '/templates/backend/global.php';
    $content = ob_get_clean();
    $page['admin-global'] = array(
      '#theme' => 'admin-global',
      '#content' => $content
    );
    return $page;
  }

  public function gavias_sl_group_config_save(){
    header('Content-type: application/json');
    $gid = $_REQUEST['gid'];
    $settings = $_REQUEST['settings'];
    
    db_update("gavias_sliderlayergroups")->fields(array(
        'params'  => $settings,
    ))->condition('id', $gid, '=')->execute();
    $result = array(
      'data' => 'update saved'
    );
    
    // Clear all cache
    \Drupal::service('plugin.manager.block')->clearCachedDefinitions();     
    $module_handler = \Drupal::moduleHandler();
    $module_handler->invokeAll('rebuild');

    drupal_set_message("Group Slider has been updated");
    print json_encode($result);
    exit(0);
  }

  public function gavias_sl_group_export($gid){
    $data = gavias_sliderlayer_export($gid);
    //print"<pre>"; print_r(json_decode(base64_decode($data)));die();

    $title = 'sliderlayer_' . date('Y_m_d_h_i_s'); 
    header("Content-Type: text/txt");
    header("Content-Disposition: attachment; filename={$title}.txt");
    //header("Content-Length: " . strlen($data));
    print $data;
    exit;
  }
}
