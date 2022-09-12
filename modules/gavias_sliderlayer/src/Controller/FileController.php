<?php

/**
 * @file
 * Contains \Drupal\gavias_sliderlayer\Controller\FileController.
 */

namespace Drupal\gavias_sliderlayer\Controller;

use Drupal\Core\Url;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Response;


class FileController extends ControllerBase {

  
  public function gavias_sliderlayer_upload_file(){
    // A list of permitted file extensions
    global $base_url;
    $allowed = array('png', 'jpg', 'gif','zip');
    $_id = gavias_sliderlayer_makeid(6);
    if(isset($_FILES['upl']) && $_FILES['upl']['error'] == 0){

      $extension = pathinfo($_FILES['upl']['name'], PATHINFO_EXTENSION);

      if(!in_array(strtolower($extension), $allowed)){
        echo '{"status":"error extension"}';
        exit;
      }  
      $path_folder = \Drupal::service('file_system')->realpath(file_default_scheme(). "://gva-sliderlayer-upload");
    
      //$file_path = $path_folder . '/' . $_id . '-' . $_FILES['upl']['name'];
      //$file_url = str_replace($base_url, '',file_create_url(file_default_scheme(). "://gva-sliderlayer-upload") . '/' .  $_id .'-'. $_FILES['upl']['name']); 
      
      $ext = end(explode('.', $_FILES['upl']['name']));
      $image_name =  basename($_FILES['upl']['name'], ".{$ext}");

      $file_path = $path_folder . '/' . $image_name . "-{$_id}" . ".{$ext}";
      $file_url = str_replace($base_url, '',file_create_url(file_default_scheme(). "://gva-sliderlayer-upload"). '/' .  $image_name . "-{$_id}" . ".{$ext}"); 

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

    $file_path = \Drupal::service('file_system')->realpath(file_default_scheme(). "://gva-sliderlayer-upload");

    $file_url = file_create_url(file_default_scheme(). "://gva-sliderlayer-upload"). '/';
    $list_file = glob($file_path . '/*.{jpg,png,gif}', GLOB_BRACE);

    $files = array();
    $data = '';
    foreach ($list_file as $key => $file) {
      if(basename($file)){
        $file_url = str_replace($base_url, '', file_create_url(file_default_scheme(). "://gva-sliderlayer-upload"). '/' .  basename($file)); 
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
