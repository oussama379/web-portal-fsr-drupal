<?php 
namespace Drupal\gavias_blockbuilder\shortcodes;
if(!class_exists('gsc_instagram')):
   class gsc_instagram{

      public function render_form(){
         $fields = array(
            'type' => 'gsc_instagram',
            'title' => ('Instagram'), 
            'size' => 3,'fields' => array(
               array(
                  'id'        => 'title',
                  'type'      => 'text',
                  'title'     => t('Title'),
                  'class'     => 'display-admin'
               ),
               array(
                  'id'        => 'content',
                  'type'      => 'textarea',
                  'title'     => t('Content'),
                  'desc'      => t('Some Shortcodes and HTML tags allowed'),
               ),
               array(
                  'id'        => 'username',
                  'type'      => 'text',
                  'title'     => t('Username'),
               ),
               array(
                  'id'        => 'number',
                  'type'      => 'text',
                  'title'     => t('Number'),
                  'std'       => '6'
               ),
               array(
                  'id'        => 'columns',
                  'type'      => 'text',
                  'title'     => t('Columns'),
                  'std'       => '5'
               ),
               array(
                  'id'        => 'columns_md',
                  'type'      => 'text',
                  'title'     => t('Columns for medium screen'),
                  'std'       => '5'
               ),
               array(
                  'id'        => 'columns_sm',
                  'type'      => 'text',
                  'title'     => t('Columns for small screen'),
                  'std'       => '5'
               ),
               array(
                  'id'        => 'link',
                  'type'      => 'select',
                  'title'     => t('Show link use'),
                  'options'   => array(
                     1     =>  t('Enable'),
                     0     =>  t('Disable')               
                  )
               ),
               array(
                  'id'        => 'style',
                  'title'     => t('Style'),
                  'type'      => 'select',
                  'options'   => array(
                     'style-1'        => 'default',
                     'style-2'        => 'style #2: no padding',
                  ),
                  'std'    => 'icon-left',
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
         if( ! key_exists('content', $item['fields']) ) $item['fields']['content'] = '';
         print self::sc_instagram( $item['fields'], $item['fields']['content'] );
      }


      public static function sc_instagram( $attr, $content = null ){
         extract(shortcode_atts(array(
            'title'           => '',
            'username'        => '',
            'number'          => '6',
            'columns'         => '5',
            'columns_sm'      => '4',
            'columns_md'      => '5', 
            'link'            => 0,
            'style'           => 'style-1',
            'animate'         => '',
            'el_class'        => ''
         ), $attr));
         
         $el_class .= ' ' . $style;

         $results_array = false;
         if($username){
            $results_array = scrape_insta_hash($username);
         }
         if($link && $results_array){
            $link = $results_array['hostname'] . '/' . $username;
         }

         ?>
         <?php ob_start() ?>
         <div class="widget gsc-instagram <?php print $el_class; ?>">
            <div class="widget-heading">
               <?php if($title){ ?>
                  <div class="title"><?php print $title ?> <?php if($link) print "<a href=\"{$link}\">{$username}</a>"; ?></div>
               <?php } ?>
               <?php if($content){ ?>
                  <div class="desc"><?php print $content; ?></div>
               <?php } ?>
            </div>
            <div class="widget-content">
               <div class="owl-carousel init-carousel-owl" data-items="<?php print $columns ?>" data-items_lg="<?php print $columns ?>" data-items_md="<?php print $columns_md ?>" data-items_sm="<?php print $columns_sm ?>" data-items_xs="2" data-loop="1" data-speed="500" data-auto_play="1" data-auto_play_speed="2000" data-auto_play_timeout="3000" data-auto_play_hover="1" data-navigation="0" data-rewind_nav="0" data-pagination="0" data-mouse_drag="1" data-touch_drag="1">
                  <?php 
                     if($results_array){
                        $images = $results_array['entry_data']['ProfilePage'][0]['user']['media']['nodes'];
                        $images = array_slice( $images, 0, $number );
                        foreach ($images as $image) {
                           print '<div class="instagram-image item"><a data-rel="prettyPhoto[g_gal]" href="'.$image['display_src'].'"><img src="'.$image['thumbnail_src'].'" alt=""/></a></div>';
                        }
                     }
                  ?>
               </div>
            </div>
         </div>
         <?php return ob_get_clean() ?>
       <?php
      }

      public function load_shortcode(){
         add_shortcode( 'icon_instagram', array($this, 'sc_instagram') );
      }
   } 
endif;   
