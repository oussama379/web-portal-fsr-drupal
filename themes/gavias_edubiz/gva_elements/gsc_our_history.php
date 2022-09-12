<?php 
namespace Drupal\gavias_blockbuilder\shortcodes;
if(!class_exists('gsc_our_history')):
   class gsc_our_history{

      public function render_form(){
         $fields = array(
            'type' => 'gsc_our_history',
            'title' => t('Our history'),
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
               array(
                  'id'            => 'style',
                  'type'          => 'select',
                  'options'       => array(
                     'carousel'        => t('Carousel'),
                     'timeline'        => t('Time Line'),
                  ),
                  'title'  => t('Icon Position'),
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
               'id'     => "year_${i}",
               'type'   => 'text',
               'title'   => "Year {$i}"
            );
            $fields['fields'][] = array(
               'id'        => "title_{$i}",
               'type'      => 'text',
               'title'     => t("Title {$i}")
            );
            $fields['fields'][] = array(
               'id'        => "content_{$i}",
               'type'      => 'textarea',
               'title'     => t("Content {$i}")
            );
         }
         return $fields;
      }

      public function render_content( $item ) {
         print self::sc_our_history( $item['fields'] );
      }

      public static function sc_our_history( $attr, $content = null ){
         global $base_url;
         $default = array(
            'title'      => '',
            'el_class'   => '',
            'animate'    => '',
            'style'      => 'carousel'
         );

         for($i=1; $i<=10; $i++){
            $default["year_{$i}"] = '';
            $default["title_{$i}"] = '';
            $default["content_{$i}"] = '';
         }

         extract(shortcode_atts($default, $attr));

         $_id = gavias_blockbuilder_makeid();
         
         ?>
         <?php ob_start() ?>
         <?php if($style == 'carousel'){ ?>
            <div class="gsc-our-history-carousel <?php echo $el_class ?>" <?php print gavias_print_animate_aos($animate) ?>> 
               <div class="owl-carousel init-carousel-owl owl-loaded owl-drag" data-items="3" data-items_lg="3" data-items_md="3" data-items_sm="2" data-items_xs="2" data-loop="1" data-speed="500" data-auto_play="1" data-auto_play_speed="2000" data-auto_play_timeout="5000" data-auto_play_hover="1" data-navigation="1" data-rewind_nav="0" data-pagination="0" data-mouse_drag="1" data-touch_drag="1">
                  <?php for($i=1; $i<=10; $i++){ ?>
                     <?php 
                        $title = "title_{$i}";
                        $year = "year_{$i}";
                        $content = "content_{$i}";
                     ?>
                     <?php if($$title){ ?>
                        <div class="item"><div class="content-inner">
                           <?php if($$year){ ?><div class="year"><?php print $$year ?></div><?php } ?>         
                           <?php if($$title){ ?><div class="title"><?php print $$title ?></div><?php } ?>
                           <?php if($$content){ ?><div class="description"><?php print $$content ?></div><?php } ?>
                        </div></div>
                     <?php } ?>    
                  <?php } ?>
               </div> 
            </div>   
         <?php } elseif($style == 'timeline'){ ?>
            <div class="gsc-our-history-timeline <?php echo $el_class ?>" <?php print gavias_print_animate_aos($animate) ?>> 
               <div class="content-inner">
                  <?php for($i=1; $i<=10; $i++){ ?>
                     <?php 
                        $title = "title_{$i}";
                        $year = "year_{$i}";
                        $content = "content_{$i}";
                     ?>
                     <?php if($$title){ ?>
                        <div class="item"><div class="item-inner">
                           <?php if($$year){ ?><div class="year"><?php print $$year ?></div><?php } ?>         
                           <div class="content-right">
                              <?php if($$title){ ?><div class="title"><?php print $$title ?></div><?php } ?>
                              <?php if($$content){ ?><div class="description"><?php print $$content ?></div><?php } ?>
                           </div>
                        </div></div>
                     <?php } ?>    
                  <?php } ?>
               </div> 
            </div>   
         <?php } ?>   
         <?php return ob_get_clean();
      }

      public function load_shortcode(){
         add_shortcode( 'our_history', array($this, 'sc_our_history') );
      }
   }
 endif;  



