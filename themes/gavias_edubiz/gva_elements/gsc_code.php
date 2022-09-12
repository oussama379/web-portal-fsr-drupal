<?php 
namespace Drupal\gavias_blockbuilder\shortcodes;
if(!class_exists('gsc_code')):
   class gsc_code{
      public function render_form(){
         $fields = array(
            'type'      => 'gsc_code',
            'title'  => t('Code'), 
            'size'      => 3, 
            
            'fields' => array(
               array(
                  'id'     => 'content',
                  'type'      => 'textarea',
                  'title'  => t('Content'),
               ),
            ),                                       
         );
         return $fields;
      } 
      
      public function render_content( $item ) {
         if( ! key_exists('content', $item['fields']) ) $item['fields']['content'] = '';
         print self::sc_code( $item['fields'], $item['fields']['content'] );
      }

      public static function sc_code( $attr, $content = null ){
         $output  = '<pre>';
            $output .= do_shortcode($content);
         $output .= '</pre>'."\n";
         print $output;
      }

      public function load_shortcode(){
         add_shortcode( 'code',array($this, 'sc_code') );
      }
   }
endif;

