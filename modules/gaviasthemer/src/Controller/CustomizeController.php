<?php
/**
 * @file
 * Contains \Drupal\gaviasthemer\Controller\CustomizeController.
 */
namespace Drupal\gaviasthemer\Controller;

use Drupal\Core\Url;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Response;

class CustomizeController extends ControllerBase {

  public function save(){
    $user = \Drupal::currentUser();
    header('Content-type: application/json');
    $json = $_REQUEST['data'];
    $theme = $_REQUEST['theme_name'];
    $path_theme = drupal_get_path('theme', $theme);
    gaviasthemer_writecache(  $path_theme . '/css/', 'customize', $json, 'json' );
     // Clear all cache
    //$json = base64_encode($json);
    \Drupal::configFactory()->getEditable('gaviasthemer.settings')
      ->set('gavias_customize', $json)
      ->save();
  
     $result = array(
      'data' => 'update saved'
    );
    print json_encode($result);
    exit(0);
  }

  public function preview(){
    header('Content-type: application/json');
    $json = $_REQUEST['data'];
    $theme = $_REQUEST['theme_name'];
    $path_theme = drupal_get_path('theme', $theme);
    $styles = '';
    if($json){
      ob_start();
      require_once($path_theme . '/customize/preview.php');
      $styles = ob_get_clean();
    } 
    $styles = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $styles );
    $styles = str_replace( array( "\r\n", "\r", "\n", "\t", '  ', '   ', '    ' ), '', $styles );
    $return['style']= $styles;

    echo json_encode($return);
    exit(0);
  }  
  
}
