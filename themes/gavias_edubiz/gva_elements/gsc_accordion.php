<?php 
namespace Drupal\gavias_blockbuilder\shortcodes;
if(!class_exists('gsc_accordion')):
   class gsc_accordion{
      public function render_form(){
         $fields = array(
            'type'      => 'gsc_accordion',
            'title'  => t('Accordion'), 
            'size'      => 3, 
            
            'fields' => array(
               array(
                  'id'     => 'title',
                  'type'      => 'text',
                  'title'  => t('Title'),
                  'class'     => 'display-admin'
               ),
               array(
                  'id'        => 'style',
                  'type'      => 'select',
                  'title'     => t('Style'),
                  'options'   => array( 
                     'skin-white'         => 'Background White',
                     'skin-dark'          => 'Background Dark',
                     'skin-white-border'  => 'Background White Border',
                  ),
               ),
               array(
                  'id'     => 'tabs',
                  'type'      => 'tabs',
                  'title'  => t('Accordion'),
                  'desc'      => t('You can use Drag & Drop to set the order.'),
               ),
               array(
                  'id'        => 'animate',
                  'type'      => 'select',
                  'title'     => t('Animation'),
                  'desc'      => t('Entrance animation for element'),
                  'options'   => gavias_blockbuilder_animate_aos(),
               ),
               
               array(
                  'id'     => 'el_class',
                  'type'      => 'text',
                  'title'  => t('Extra class name'),
                  'desc'      => t('Style particular content element differently - add a class name and refer to it in custom CSS.'),
               ),
            ),                                           
         );
      return $fields;
      }

      public function render_content( $item ) {
         print self::sc_accordion( $item['fields'] );
      }

      public static function sc_accordion( $attr, $content = null ){
         global $tabs_array, $tabs_count;
         extract(shortcode_atts(array(
            'title'     => '',
            'style'     => '',
            'tabs'      => '',
            'animate'   => '',
            'el_class'  => ''
         ), $attr));
         
         do_shortcode( $content );

         $_id = 'accordion-' . gavias_blockbuilder_makeid();
         $classes = $style;
         
         if($el_class) $classes .= ' ' . $el_class;

         ?>
          <?php ob_start() ?>
         <div class="gsc-accordion">
            <div class="panel-group <?php print $classes ?>" id="<?php print $_id; ?>" role="tablist" aria-multiselectable="true" <?php print gavias_print_animate_aos($animate) ?>>
              <?php
               if( is_array( $tabs ) ){
                  $i=0;
                  foreach( $tabs as $tab ){ $i++;
               ?>
                  <div class="panel panel-default">
                     <div class="panel-heading" role="tab">
                        <h4 class="panel-title">
                          <a role="button" data-toggle="collapse" class="<?php print ($i == 1) ? '' : 'collapsed' ?>" data-parent="#<?php print $_id; ?>" href="#<?php print ($_id . '-' . $i) ?>" aria-expanded="true" aria-controls="collapseOne">
                            <?php print $tab['title'] ?>
                          </a>
                        </h4>
                     </div>
                     <div id="<?php print ($_id . '-' . $i) ?>" class="panel-collapse collapse<?php if($i==1) print ' in' ?>" role="tabpanel">
                        <div class="panel-body">
                           <?php print do_shortcode($tab['content']) ?>
                        </div>
                     </div>
                  </div>
               <?php }
               } 
             ?>   
            </div>
         </div>   
         <?php $tabs_array = null; return ob_get_clean() ?>
      <?php    
      }
      
      public static function sc_accordion_item( $attr, $content = null ){
         global $tabs_array, $tabs_count;
         extract(shortcode_atts(array(
            'title'  => '',
         ), $attr));

          $tabs_array[] = array(
            'title' => $title,
            'content' => do_shortcode( $content )
         ); 
         $tabs_count++;

         return true;
      }

      public function load_shortcode(){
         add_shortcode( 'accordion', array($this, 'sc_accordion'));
         add_shortcode( 'accordion_item', array($this, 'sc_accordion_item') );
      }
   }

endif;