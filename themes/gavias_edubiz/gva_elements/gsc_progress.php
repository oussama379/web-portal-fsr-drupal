<?php 
namespace Drupal\gavias_blockbuilder\shortcodes;
if(!class_exists('gsc_progress')):
   class gsc_progress{

      public function render_form(){
         $fields = array(
            'type'   => 'gsc_progress',
            'title'  => t('Progress'),
            'size'   => 3,
            'icon'   => 'fa fa-bars',
            'fields' => array(
              array(
                  'id'        => 'title',
                  'type'      => 'text',
                  'title'     => t('Title'),
                  'class'     => 'display-admin'
               ),
               array(
                  'id'        => 'percent',
                  'type'      => 'text',
                  'title'     => t('Percent'),
                  'desc'      => t('Number between 0-100'),
               ),
               array(
                  'id'        => 'background',
                  'type'      => 'text',
                  'title'     => t('Background Color'),
                  'desc'      => 'Background color for progress'
               ),
               array(
                  'id'        => 'skin_text',
                  'type'      => 'select',
                  'title'     => 'Skin Text for box',
                  'options'   => array(
                     'text-light' => t('Text Light'),
                     'text-dark'  => t('Text Dark') 
                  ) 
               ),
               array(
                  'id'        => 'animate',
                  'type'      => 'select',
                  'title'     => t('Animation'),
                  'desc'      => t('Entrance animation for element'),
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
         print self::sc_progress( $item['fields'] );
      }


      public static function sc_progress( $attr, $content = null ){
         extract(shortcode_atts(array(
            'title'        => '',
            'percent'      => '',
            'background'   => '',
            'skin_text'    => '',
            'animate'      => '',
            'el_class'     => ''
         ), $attr));
         $style = '';
         if($background) $style = 'style="background-color: ' . $background . '"';
         $class_array = array();
         $class_array[] = $el_class;
         $class_array[] = $skin_text;

         ?>
         <?php ob_start() ?>
         <div class="widget gsc-progress<?php if(count($class_array)) print (' ' . implode(' ', $class_array)) ?>" <?php print gavias_print_animate_aos($animate) ?>>
            <div class="progress-label"><?php print $title ?></div>
             <div class="progress">
               <div class="progress-bar" <?php if($style) print $style; ?> data-progress-animation="<?php print $percent ?>%">
                  <span class="percentage"><span></span><?php print $percent ?>%</span>
               </div>
            </div>
         </div>   
         <?php return ob_get_clean() ?>
      <?php
      }

      public function load_shortcode(){
         add_shortcode( 'gsc_progress', array($this, 'sc_progress') );
      }
   }
 endif;  



