<?php 
namespace Drupal\gavias_blockbuilder\shortcodes;
if(!class_exists('gsc_socials')):
   class gsc_socials{
      public function render_form(){
         $fields = array(
            'type'      => 'gsc_socials',
            'title'     => t('Socials'), 
            'size'      => 3, 
            
            'fields'    => array(
               array(
                  'id'        => 'title',
                  'type'      => 'text',
                  'title'     => t('Title for admin'),
                  'class'     => 'display-admin'
               ),
               array(
                  'id'        => 'style',
                  'type'      => 'select',
                  'options'   => array(
                     'style-1'      => t('Style 1'), 
                     'style-2'      => t('Style 2'), 
                  ),
                  'title'  => t('Style'),
               ),
               array(
                  'id'        => 'el_class',
                  'type'      => 'text',
                  'title'     => t('Extra class name'),
                  'desc'      => t('Style particular content element differently - add a class name and refer to it in custom CSS.'),
               ),
               array(
                  'id'        => 'animate',
                  'type'      => 'select',
                  'title'     => t('Animation'),
                  'desc'      => t('Entrance animation'),
                  'options'   => gavias_blockbuilder_animate_aos(),
               ),
            ),                                       
         );
         for($i=1; $i<=10; $i++){
            $fields['fields'][] = array(
               'id'     => "info_${i}",
               'type'   => 'info',
               'desc'   => "Information for item {$i}"
            );
            $fields['fields'][] = array(
               'id'        => "icon_{$i}",
               'type'      => 'text',
               'title'     => t("Icon {$i}"),
               'desc'     => t('Use class icon font <a target="_blank" href="http://fontawesome.io/icons/">Icon Awesome</a> or <a target="_blank" href="http://gaviasthemes.com/icons/">Custom icon</a>'),
            );
            $fields['fields'][] = array(
               'id'           => "link_{$i}",
               'type'         => 'text',
               'title'        => t("Link {$i}")
            );
         }
         return $fields;
      } 
      
      public function render_content( $item ) {
         if( ! key_exists('content', $item['fields']) ) $item['fields']['content'] = '';
         print self::sc_socials( $item['fields'], $item['fields']['content'] );
      }

      public static function sc_socials( $attr, $content = null ){
         $default = array(
            'title'     => '',
            'style'      => 'style-1',
            'el_class'  => '',
            'animate'   => ''
         );

         for($i=1; $i<=10; $i++){
            $default["icon_{$i}"] = '';
            $default["link_{$i}"] = '';
         }
         extract(shortcode_atts($default, $attr));

         $class = array();
         $class[] = $el_class;
         $class[] = $style;

         ?>
         <?php ob_start() ?>
         <div class="widget gsc-socials <?php print implode($class, ' ') ?>" <?php print gavias_print_animate_aos($animate) ?>>
            <?php for($i=1; $i<=10; $i++){ ?>
               <?php $icon = "icon_{$i}"; $link = "link_{$i}"; ?>
               <?php if($$icon && $$link){ ?>
                  <a href="<?php print $$link ?>"><i class="<?php print $$icon ?>" /></i></a>
               <?php } ?>
            <?php } ?>
         </div>
         <?php return ob_get_clean() ?>
         <?php
      }

      public function load_shortcode(){
         add_shortcode( 'socials', array('gsc_socials', 'sc_socials') );
      }
   }
endif;

