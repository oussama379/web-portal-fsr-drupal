<?php
function variable_get($name, $default = NULL) {
  global $conf;
  return isset($conf[$name]) ? $conf[$name] : $default;
}

function gavias_blockbuilder_makeid($length = 5){
    $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

function gavias_blockbuilder_includes( $path, $ifiles=array() ){
    if( !empty($ifiles) ){
         foreach( $ifiles as $key => $file ){
            $file  = $path.'/'.$file; 
            if(is_file($file)){
                require($file);
            }
         }   
    }else {
        $files = glob($path);
        foreach ($files as $key => $file) {
            if(is_file($file)){
                require($file);
            }
        }
    }
}

/*================================================
                Block for theme
=================================================*/                
function gavias_blockbuilder_get_blocks_options() {
  static $_blocks_array = array();
    if (empty($_blocks_array)) {
      // Get default theme for user.
      $theme_default = \Drupal::config('system.theme')->get('default');
      // Get storage handler of block.
      $block_storage = \Drupal::entityManager()->getStorage('block');
      // Get the enabled block in the default theme.
      $entity_ids = $block_storage->getQuery()->condition('theme', $theme_default)->execute();
      $entities = $block_storage->loadMultiple($entity_ids);
      $_blocks_array = [];
      foreach ($entities as $block_id => $block) {
        $_blocks_array[$block_id] = $block->label();
      }
      asort($_blocks_array);
    }
    return $_blocks_array;
}

function gavias_blockbuilder_render_block($key) {
    $block = \Drupal\block\Entity\Block::load($key);
    if($block){
      $block_content = \Drupal::entityManager()
        ->getViewBuilder('block')
        ->view($block);
        $block = null;
      return drupal_render($block_content);
    }else{
      return '<div>Missing view, block "'.$key.'"</div>';
    }
  }

