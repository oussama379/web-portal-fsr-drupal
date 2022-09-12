<?php 
namespace Drupal\gavias_blockbuilder\shortcodes;
if(!class_exists('gsc_column')):
   class gsc_column{

      public function render_form(){
         $fields = array(
            'type' => 'gsc_column',
            'title' => t('Custom Text'),
            'size' => 3,
            'fields' => array(
               
               array(
                  'id'     => 'title',
                  'type'      => 'text',
                  'title'  => t('Title'),
                   'class'     => 'display-admin'
               ),
               array(
                  'id'           => 'content',
                  'type'         => 'textarea',
                  'title'        => t('Column content'),
                  'desc'         => t('Shortcodes and HTML tags allowed.'),
                  'shortcodes'   => 'on'
               ),
               array(
                  'id'        => 'style',
                  'type'      => 'select',
                  'title'     => t('Style display'),
                  'options'   => array(
                        ''   => 'Default',
                        'text-size-medium'  => 'Text medium (font-size: 30px, font-weight: 100)',
                  )
               ), 
               array(
                  'id'     => 'animate',
                  'type'      => 'select',
                  'title'  => ('Animation'),
                  'desc'  => t('Entrance animation for element'),
                  'options'   => gavias_blockbuilder_animate_aos(),
               ),
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
         print self::sc_column( $item['fields'] );
      }


      public static function sc_column( $attr, $content = null ){
         extract(shortcode_atts(array(
            'title'      => '',
            'content'    => '',
            'style'      => '',
            'el_class'   => '',
            'animate'    => ''
         ), $attr));
         $el_class .= ' ' . $style;

         $ouput = '';
         $ouput .= '<div class="column-content '.$el_class.'" ' . gavias_print_animate_aos($animate) . '>';
         $ouput .= do_shortcode( $content );
         $ouput .= '</div>';
         return $ouput;
      }

      public function load_shortcode(){
         add_shortcode( 'column', array('gsc_column', 'sc_column') );
      }
   }
 endif;  



