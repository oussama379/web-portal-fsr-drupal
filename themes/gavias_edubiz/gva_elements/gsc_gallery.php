<?php 
namespace Drupal\gavias_blockbuilder\shortcodes;
if(!class_exists('gsc_gallery')):
   class gsc_gallery{

      public function render_form(){
         $fields = array(
            'type' => 'gsc_gallery',
            'title' => t('Gallery'),
            'size' => 3,
            'fields' => array(
               array(
                  'id'        => 'title',
                  'type'      => 'text',
                  'title'     => t('Title For Admin'),
                  'class'     => 'display-admin'
               ),
               array(
                  'id'        => 'animate',
                  'type'      => 'select',
                  'title'     => ('Animation'),
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

         for($i=1; $i<=10; $i++){
            $fields['fields'][] = array(
               'id'     => "info_${i}",
               'type'   => 'info',
               'desc'   => "Information for item {$i}"
            );
            $fields['fields'][] = array(
               'id'        => "title_{$i}",
               'type'      => 'text',
               'title'     => t("Title {$i}")
            );
            $fields['fields'][] = array(
               'id'        => "image_{$i}",
               'type'      => 'upload',
               'title'     => t("Image {$i}")
            );
         }
         return $fields;
      }

      public function render_content( $item ) {
         print self::sc_gallery( $item['fields'] );
      }

      public static function sc_gallery( $attr, $content = null ){
         global $base_url;
         $default = array(
            'title'      => '',
            'el_class'   => '',
            'animate'    => '',
         );

         for($i=1; $i<=10; $i++){
            $default["title_{$i}"] = '';
            $default["image_{$i}"] = '';
         }

         extract(shortcode_atts($default, $attr));

         $_id = gavias_blockbuilder_makeid();
         
         ?>
         <?php ob_start() ?>
         <div class="gsc-our-gallery <?php echo $el_class ?>" <?php print gavias_print_animate_aos($animate) ?>> 
            <div class="owl-carousel init-carousel-owl owl-loaded owl-drag" data-items="1" data-items_lg="1" data-items_md="1" data-items_sm="2" data-items_xs="1" data-loop="1" data-speed="500" data-auto_play="1" data-auto_play_speed="2000" data-auto_play_timeout="5000" data-auto_play_hover="1" data-navigation="1" data-rewind_nav="0" data-pagination="0" data-mouse_drag="1" data-touch_drag="1">
               <?php for($i=1; $i<=10; $i++){ ?>
                  <?php 
                     $title = "title_{$i}";
                     $image = "image_{$i}";
                  ?>
                  <?php if($$title){ ?>
                     <div class="item"><div class="content-inner">
                        <?php if($$title){ ?><div class="title"><?php print $$title ?></div><?php } ?>         
                        <?php if($$image){ ?><div class="image"><img src="<?php echo ($base_url . $$image) ?>" /></div><?php } ?>
                     </div></div>
                  <?php } ?>    
               <?php } ?>
            </div> 
         </div>   
         <?php return ob_get_clean();
      }

      public function load_shortcode(){
         add_shortcode( 'sc_gallery', array($this, 'sc_gallery') );
      }
   }
 endif;  



