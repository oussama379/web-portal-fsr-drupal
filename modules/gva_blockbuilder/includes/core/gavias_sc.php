<?php
namespace Drupal\gavias_blockbuilder\includes\core;
class gavias_sc{

  protected $gv_shortcodes = array();

  public function __construct(){
    
  }

  public function gsc_load_shortcodes($backend=true){
    if( empty($this->gv_shortcodes) ){
      $shortcodes = [];
      $theme_name = gavias_blockbuilder_theme_name();

      //Load element from themes
      if(file_exists(drupal_get_path('theme', $theme_name) . '/gva_elements/elements.php')){
        
        $shortcodes = [];
        if(function_exists('gavias_blockbuilder_get_elements')) $shortcodes = gavias_blockbuilder_get_elements();
        foreach( $shortcodes as $sc ){
          $sc_path = '';
          if(file_exists(drupal_get_path('theme', $theme_name) . '/gva_elements/' . $sc . '.php')){
            $sc_path = drupal_get_path('theme', $theme_name) . '/gva_elements/' . $sc . '.php';
          }
          if($sc_path){
            $class = $sc;
            $_class = '\\Drupal\gavias_blockbuilder\shortcodes\\'.$class;
            if( class_exists($_class) ){
              $s = new $_class;
              if($backend){ //Load form setting for shortcode backend
                if(method_exists($s, 'render_form')){
                  $this->gv_shortcodes[$class] = $s->render_form();
                }
              }
            }
          }
        }

      //Load elements from gavias_elements modules (financial, charityplus ,sanbro, carina)
      }else{

        if(function_exists('gavias_elements_list_shortcodes')){
          $shortcodes = gavias_elements_list_shortcodes();
        }
        foreach( $shortcodes as $sc ){
          $sc_path = '';
          if(file_exists(drupal_get_path('module', 'gavias_elements') . '/elements/' . $sc . '.php')){
            $sc_path = drupal_get_path('module', 'gavias_elements') . '/elements/' . $sc . '.php';
          }
          if($sc_path){
            $class = $sc;
            $_class = '\\Drupal\gavias_blockbuilder\shortcodes\\'.$class;
            if( class_exists($_class) ){
              $s = new $_class;
              if($backend){ //Load form setting for shortcode backend
                if(method_exists($s, 'render_form')){
                  $this->gv_shortcodes[$class] = $s->render_form();
                }
              }
            }
          }
        }

      }
    }
  }

  public function gsc_shortcodes_forms(){
    return $this->gv_shortcodes;
  }

  public  function row_opts(){
   return array(
      array(
        'id'        => 'info',
        'type'      => 'info',
        'desc'      => 'Setting background for row'
      ),
      array(
        'id'       => 'bg_image',
        'type'     => 'upload',
        'title'    => ('Background Image'),
      ),
      array(
        'id'          => 'bg_color',
        'type'        => 'text',
        'title'       => ('Background Color'),
        'desc'        => ('Use color name (eg. "gray") or hex (eg. "#808080").'),
        'class'       => 'small-text',
        'std'         => '',
      ),
      array(
        'id'         => 'bg_position',
        'type'       => 'select',
        'title'      => t('Background Position'),
        'options'    => array(
          'center top' => 'center top',
          'center right' => 'center right',
          'center bottom' => 'center bottom',
          'center center' => 'center center',
          'left top' => 'left top',
          'left center' => 'left center',
          'left bottom' => 'left bottom',
          'right top' => 'right top',
          'right center' => 'right center',
          'right bottom' => 'right bottom',
        )
      ),
      array(
        'id'         => 'bg_repeat',
        'type'       => 'select',
        'title'      => t('Background Position'),
        'options'    => array(
          'no-repeat' => 'no-repeat',
          'repeat' => 'repeat',
          'repeat-x' => 'repeat-x',
          'repeat-y' => 'repeat-y',
          )
      ),
      array(
        'id'         => 'bg_attachment',
        'type'       => 'select',
        'title'      => t('Background Attachment'),
        'options'    => array(
          'scroll' => 'Scroll',
          'fixed'  => 'Parallax',
          ),
        'std'         => 'scroll'
      ),

      array(
        'id'         => 'bg_size',
        'type'       => 'select',
        'title'      => t('Background Size'),
        'options'    => array(
            'cover'      => 'cover',
            'contain'    => 'contain',
            'default'    => 'default'
          ),
        'std'         => 'cover'
      ),

      array(
        'id'          => 'bg_video',
        'type'        => 'text',
        'title'       => ('Background video (url video)'),
        'desc'        => ('Use video youtube.'),
        'std'         => '',
      ),

      array(
        'id'        => 'info',
        'type'      => 'info',
        'desc'      => 'Setting padding, margin for row'
      ),
      array(
        'id'        => 'style_space',
        'type'      => 'select',
        'title'     => 'Style Space',
        'options'   => array(
          'default'                           => 'Default',
          'remove_padding_top'                => 'Remove padding top',
          'remove_padding_bottom'             => 'Remove padding bottom',
          'remove_padding'                    => 'Remove padding for row',
          'remove_padding_col'                => 'Remove padding for colums of row',
          'remove_margin remove_padding remove_padding_col' => 'Remove padding for [colums & row]'
        )
      ),

      array(
        'id'        => 'padding_top',
        'type'      => 'text',
        'title'     => ('Padding Top'),
        'desc'      => ('Set value padding top for row (e.g. 30)'),
        'class'     => 'small-text',
        'std'       => '0',
      ),
      
      array(
        'id'          => 'padding_bottom',
        'type'        => 'text',
        'title'       => ('Padding Bottom'),
        'desc'        => ('Set value padding bottom for row (e.g. 30)'),
        'class'       => 'small-text',
        'std'         => '0',
      ),

      array(
        'id'          => 'margin_top',
        'type'        => 'text',
        'title'       => ('Margin Top'),
        'desc'        => ('Set value margin top for row (e.g. 30)'),
        'class'       => 'small-text',
        'std'         => '0',
      ),
      
      array(
        'id'          => 'margin_bottom',
        'type'        => 'text',
        'title'       => ('Margin Bottom'),
        'desc'        => ('Set value margin bottom for row (e.g. 30)'),
        'class'       => 'small-text',
        'std'         => '0',
      ),
      
      array(
        'id'        => 'info',
        'type'      => 'info',
        'desc'      => 'Setting layout, style for row'
      ),

      array(
        'id'            => 'layout',
        'type'          => 'select',
        'title'         => 'Layout',
        'options'       => array( 'container' => 'Box', 'container-fw' => 'Full Width', 'full-screen' => 'Full Screen' )
      ),

      array(
        'id'            => 'equal_height',
        'type'          => 'select',
        'title'         => 'Columns Equal Height',
        'options'       => array( '' => 'Disable', 'gsc-equal-height' => 'Enable' )
      ),
      
      array(
        'id'        => 'info',
        'type'      => 'info',
        'desc'      => 'Setting class, id for row'
      ),

     array(
        'id'    => 'icon',
        'type'    => 'text',
        'title'   => ('Icon for row'),
        'desc'     => t('Use class icon font <a target="_blank" href="http://fontawesome.io/icons/">Icon Awesome</a>'),
      ),

      array(
        'id'    => 'class',
        'type'    => 'text',
        'title'   => ('Custom CSS classes'),
        'desc'    => ('Multiple classes should be separated with SPACE.<br />'),
      ),
      
      array(
        'id'    => 'row_id',
        'type'    => 'text',
        'title'   => ('Custom ID'),
        'desc'    => ('Use this option to create One Page sites.<br/>For example: Your Custom ID is <strong>offer</strong> and you want to open this section, please use link: <strong>your-url/#offer-2</strong>'),
        'class'   => 'small-text',
      ),
    );
  }

  public  function column_opts(){
   return array(
      array(
        'id'        => 'info',
        'type'      => 'info',
        'desc'      => 'Setting background for column'
      ),
      array(
        'id'       => 'bg_image',
        'type'     => 'upload',
        'title'    => ('Background Image'),
      ),
      array(
        'id'          => 'bg_color',
        'type'        => 'text',
        'title'       => ('Background Color'),
        'desc'        => ('Use color name (eg. "gray") or hex (eg. "#808080").'),
        'class'       => 'small-text',
        'std'         => '',
      ),
      array(
        'id'         => 'bg_position',
        'type'       => 'select',
        'title'      => t('Background Position'),
        'options'    => array(
          'center top' => 'center top',
          'center right' => 'center right',
          'center bottom' => 'center bottom',
          'center center' => 'center center',
          'left top' => 'left top',
          'left center' => 'left center',
          'left bottom' => 'left bottom',
          'right top' => 'right top',
          'right center' => 'right center',
          'right bottom' => 'right bottom',
        )
      ),
      array(
        'id'         => 'bg_repeat',
        'type'       => 'select',
        'title'      => t('Background Position'),
        'options'    => array(
          'no-repeat' => 'no-repeat',
          'repeat' => 'repeat',
          'repeat-x' => 'repeat-x',
          'repeat-y' => 'repeat-y',
          )
      ),

      array(
        'id'         => 'bg_attachment',
        'type'       => 'select',
        'title'      => t('Background Attachment'),
        'options'    => array(
          'scroll' => 'Scroll',
          'fixed'  => 'Fixed - Parallax',
          ),
        'std'         => 'scroll'
      ),
      
      array(
        'id'         => 'bg_size',
        'type'       => 'select',
        'title'      => t('Background Size'),
        'options'    => array(
            'cover'      => 'cover',
            'contain'    => 'contain',
            'default'    => 'default'
          ),
        'std'         => 'cover'
      ),

      array(
        'id'        => 'info',
        'type'      => 'info',
        'desc'      => 'Setting class, id for columns'
      ),

      array(
        'id'    => 'class',
        'type'    => 'text',
        'title'   => ('Custom CSS classes'),
        'desc'    => ('Multiple classes should be separated with SPACE.'),
      ),

      array(
        'id'    => 'class_inner',
        'type'    => 'text',
        'title'   => ('Custom for element inner CSS classes'),
        'desc'    => ('Multiple classes should be separated with SPACE.'),
      ),
      
      array(
        'id'    => 'column_id',
        'type'    => 'text',
        'title'   => ('Custom ID'),
        'desc'    => ('For example: Your Custom ID for column'),
        'class'   => 'small-text',
      ),
      
      array(
        'id'        => 'info',
        'type'      => 'info',
        'desc'      => 'Setting Responsive Visibility for Columns'
      ),
      array(
        'id' => 'hidden_lg',
        'type'    => 'select',
        'title'   => ('Hide on large screen (hidden-lg)'),
        'options'   => array(
          'show'        => 'Show',   
          'hidden'   => 'Hidden'
        )
      ),
      array(
        'id' => 'hidden_md',
        'type'    => 'select',
        'title'   => ('Hide on medium screen (hidden-md)'),
        'options'   => array(
          'show'        => 'Show',   
          'hidden'         => 'Hidden'
        )
      ),
      array(
        'id' => 'hidden_sm',
        'type'    => 'select',
        'title'   => ('Hide on small screen (hidden-sm)'),
        'options'   => array(
          'show'        => 'Show',   
          'hidden'         => 'Hidden'
        )
      ),
      array(
        'id' => 'hidden_xs',
        'type'    => 'select',
        'title'   => ('Hide on extra small screen (hidden-xs)'),
        'options'   => array(
          'show'        => 'Show',   
          'hidden'         => 'Hidden'
        )
      ),

    );
  }
}
