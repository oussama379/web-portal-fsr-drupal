<?php
/**
 * Helper function for including theme files.
 *
 * @param string $theme
 *   Name of the theme to use for base path.
 * @param string $path
 *   Path relative to $theme.
 */
function gavias_edubiz_include($theme, $path) {
  static $themes = array();
  if (!isset($themes[$theme])) {
    $themes[$theme] = drupal_get_path('theme', $theme);
  }
  if ($themes[$theme] && ($file = DRUPAL_ROOT . '/' . $themes[$theme] . '/' . $path) && file_exists($file)) {
    include_once $file;
  }
}

function gavias_edubiz_render_block($key) {
  $block = \Drupal\block\Entity\Block::load($key);
  if($block){
  $block_content = \Drupal::entityManager()
    ->getViewBuilder('block')
    ->view($block);
    return drupal_render($block_content);
  }  
  return '';
}

function gavias_edubiz_makeid($length = 5){
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

/**
* THEME_add_regions_to_node
*/
function gavias_edubiz_add_regions_to_node($allowed_regions, &$variables) {
  // Retrieve active theme
  $theme = \Drupal::theme()->getActiveTheme()->getName();
 
  // Retrieve theme regions
  $available_regions = system_region_list($theme, 'REGIONS_ALL');
 
  // Validate allowed regions with available regions
  $regions = array_intersect(array_keys($available_regions), $allowed_regions);
 
  // For each region
  foreach ($regions as $key => $region) {
 
    // Load region blocks
    $blocks = entity_load_multiple_by_properties('block', array('theme' => $theme, 'region' => $region));
 
    // Sort ‘em
    uasort($blocks, 'Drupal\block\Entity\Block::sort');
 
    // Capture viewable blocks and their settings to $build
    $build = array();
    foreach ($blocks as $key => $block) {
      if ($block->access('view')) {
        $build[$key] = entity_view($block, 'block');
      }
    }
 
    // Add build to region
    $variables[$region] = $build;
  }
}