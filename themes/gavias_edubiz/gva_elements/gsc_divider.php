<?php 
namespace Drupal\gavias_blockbuilder\shortcodes;
if(!class_exists('gsc_divider')):
  class gsc_divider{
    
     public function render_form(){
        $fields = array(
           'type'  => 'gsc_divider',
           'title' => ('Divider'), 
           'size'  => 12,
           'fields'   => array(
              array(
                  'id'        => 'el_class',
                  'type'      => 'text',
                  'title'     => t('Extra class name'),
                  'desc'      => t('Style particular content element differently - add a class name and refer to it in custom CSS.'),
               ),
           ),                                       
        );
      return $fields;
    }

    public function render_content( $item ) {
       print self::sc_divider( $item['fields'] );
    }

    public static function sc_divider( $attr, $content = null ){
      extract(shortcode_atts(array(
        'el_class'      => '' 
      ), $attr));
      ?>
      <?php ob_start() ?>
        <div class="widget clearfix gsc-divider"></div>
        <?php return ob_get_clean() ?>    
      <?php 
     }

     public function load_shortcode(){
      add_shortcode( 'divider', 'gsc_divider', 'sc_divider' );
     }
  }
endif;  



