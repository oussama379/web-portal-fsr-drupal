<?php 
namespace Drupal\gavias_blockbuilder\shortcodes;
if(!class_exists('gsc_work_process')):
   class gsc_work_process{
      public function render_form(){
         $fields = array(
            'type'      => 'gsc_work_process',
            'title'  => t('Work Process'), 
            'size'      => 3, 
            
            'fields' => array(
               array(
                  'id'     => 'title',
                  'type'      => 'text',
                  'title'  => t('Title'),
                  'class'     => 'display-admin'
               ),
               array(
                  'id'     => 'tabs',
                  'type'      => 'tabs',
                  'title'  => t('Work Process'),
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
         print self::gsc_work_process( $item['fields'] );
      }

      public static function gsc_work_process( $attr, $content = null ){
         extract(shortcode_atts(array(
            'title'     => '',
            'tabs'      => '',
            'animate'   => '',
            'el_class'  => ''
         ), $attr));
         $_id = 'workprocess-' . gavias_blockbuilder_makeid();
         $classes = $el_class;
         if($animate){
            $classes .= ' wow';
            $classes .= ' '. $animate;
         }
         ?>
          <?php ob_start() ?>
         <div class="gsc-workprocess <?php print $classes ?>" id="<?php print $_id; ?>" <?php print gavias_print_animate_aos($animate) ?>>
            <ul class="service-timeline post-area">
              <?php
               if( is_array( $tabs ) ){ 
                  $i=0;
                  foreach( $tabs as $tab ): $i++;
               ?>
               <li class="entry-timeline clearfix">
                  <div class="hentry skrollable skrollable-between" data-bottom-top="opacity: 0;" data-center-bottom="opacity: 1;" data-top-bottom="opacity: 0;">
                     <div class="icon"><span class="<?php print $tab['icon']; ?>"></span></div>    
                     <div class="hentry-box clearfix">
                        <div class="content-inner">
                           <div class="title"><?php print $tab['title'] ?></div>
                           <div class="content"><?php print $tab['content'] ?></div>
                        </div>   
                    </div>
                 </div> 
               </li>
               <?php endforeach;  ?>  
               <?php } ?>   
            </ul>
         </div>
         <?php return ob_get_clean() ?>
      <?php    
      }
      
      public function load_shortcode(){
         add_shortcode( 'work_process', array($this, 'gsc_work_process'));
      }
   }

endif;