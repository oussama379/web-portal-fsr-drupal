<?php 
namespace Drupal\gavias_blockbuilder\shortcodes;
if(!class_exists('gsc_icon_box')):
   class gsc_icon_box{

      public function render_form(){
         $fields = array(
            'type' => 'gsc_icon_box',
            'title' => ('Icon Box'), 
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
                  'id'        => 'icon',
                  'type'      => 'text',
                  'title'     => t('Icon class'),
                  'std'       => '',
                  'desc'     => t('Use class icon font <a target="_blank" href="http://fontawesome.io/icons/">Icon Awesome</a> or <a target="_blank" href="http://gaviasthemes.com/icons/">Custom icon</a>'),
               ),
               array(
                  'id'        => 'image',
                  'type'      => 'upload',
                  'title'     => t('Image Icon'),
                  'desc'      => t('Use image icon instead of icon class'),
               ),
               array(
                  'id'            => 'icon_position',
                  'type'          => 'select',
                  'options'       => array(
                     'top-center'            => 'Top Center', 
                     'top-left'              => 'Top Left',
                     'top-right'             => 'Top Right',
                     'right'                 => 'Right',
                     'left'                  => 'Left',
                     'top-left-title'        => 'Top Left Title',
                     'top-right-title'       => 'Top Right Title',
                  ),
                  'title'  => t('Icon Position'),
                  'std'    => 'top',
               ),
               array(
                  'id'        => 'link',
                  'type'      => 'text',
                  'title'     => t('Link'),
                  'desc'      => t('Link for text')
               ),
               array(
                  'id'        => 'box_background',
                  'type'      => 'text',
                  'title'     => t('Box Background'),
                  'desc'      => t('Box Background, e.g: #f5f5f5')
               ),
              
               array(
                  'id'        => 'icon_background',
                  'type'      => 'select',
                  'title'     => 'Background icon',
                  'options'   => array(
                     ''          => t('--None--'),
                     'bg-theme'  => t('Background of theme'), 
                     'bg-white'  => t('Background White'),
                     'bg-black'  => t('Background Black'),
                     'bg-dark'   => t('Background Dark'),
                  ) 
               ),
               array(
                  'id'        => 'icon_color',
                  'type'      => 'select',
                  'title'     => t('Icon Color'),
                  'options'   => array(
                     'text-theme'  => t('Text theme'),
                     'text-white'  => t('Text white'), 
                     'text-black'  => t('Text black')
                  )
               ),
               array(
                  'id'        => 'icon_width',
                  'type'      => 'select',
                  'title'     => t('Icon Width'),
                  'options'   => array(
                     'fa-1x'  => t('Fa 1x small'), 
                     'fa-2x'  => t('Fa 2x'), 
                     'fa-3x'  => t('Fa 3x'),
                     'fa-4x'  => t('Fa 4x'),
                  )
               ),
               array(
                  'id'        => 'icon_radius',
                  'type'      => 'select',
                  'title'     => t('Icon Radius'),
                  'options'   => array(
                     ''           => t('--None--'), 
                     'radius-1x'  => t('Radius 1x'), 
                     'radius-2x'  => t('Radius 2x'),
                     'radius-5x'  => t('Radius 5x'),
                  )
               ),
               array(
                  'id'        => 'icon_border',
                  'type'      => 'select',
                  'title'     => t('Icon Border'),
                  'options'   => array(
                     ''           => t('--None--'), 
                     'border-1'  => t('Border 1px'), 
                     'border-2'  => t('Border 2px'),
                     'border-3'  => t('Border 3px'),
                     'border-4'  => t('Border 4px'),
                     'border-5'  => t('Border 5px'),
                  )
               ),
               array(
                  'id'        => 'skin_text',
                  'type'      => 'select',
                  'title'     => 'Skin Text for box',
                  'options'   => array(
                     'text-dark'  => t('Text Dark'), 
                     'text-light' => t('Text Light')
                  ) 
               ),
               array(
                  'id'        => 'target',
                  'type'      => 'select',
                  'options'   => array( 'on' => 'No', 'off' => 'Yes' ),
                  'title'     => t('Open in new window'),
                  'desc'      => t('Adds a target="_blank" attribute to the link.'),
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
         print self::sc_icon_box( $item['fields'], $item['fields']['content'] );
      }

      public static function sc_icon_box( $attr, $content = null ){
         global $base_url;
         extract(shortcode_atts(array(
            'title'              => '',
            'icon'               => '',
            'image'              => '',
            'icon_position'      => 'top',
            'box_background'     => '',
            'icon_color'         => 'text-theme',
            'icon_background'    => '',
            'icon_radius'        => '',
            'icon_border'        => '',
            'icon_width'         => 'fa-2x',
            'link'               => '',
            'skin_text'          => '',
            'target'             => '',
            'animate'            => '',
            'min_height'         => '',
            'el_class'           => ''
         ), $attr));
         
         if($image) $image = $base_url . $image; 

         // target
         if( $target ){
            $target = 'target="_blank"';
         } else {
            $target = false;
         }

         $class = array();
         $class[] = $icon_position;
         if($el_class) $class[] = $el_class;
         if($skin_text) $class[] = $skin_text;

         if($box_background) $class[] = 'box-background';
         if($icon_border) $class[] = 'icon-border';
         if($icon_background) $class[] = 'icon-background';

         $icon_class = "{$icon_width} {$icon_radius} {$icon_border} {$icon_color} {$icon_background}";
         if($icon_border || $icon_background) $icon_class .= ' fa-stack';


         $style = array(); // Style box
         if($min_height) $style[] = "min-height:{$min_height};";
         if($box_background) $style[] = "background-color:{$box_background};";
         
         $style_icon = ''; // Style icon
         if($style_icon) $style_icon = "style=\"{$style_icon}\"";

         ?>
         <?php ob_start() ?>
         <?php if($icon_position=='top-center' || $icon_position=='top-left' || $icon_position=='top-right' || $icon_position=='right' || $icon_position=='left'){ ?>
            <div class="widget gsc-icon-box <?php if(count($class)>0) print implode($class, ' ') ?>" <?php if(count($style) > 0) print 'style="'.implode($style, ';').'"' ?> <?php print gavias_print_animate_aos($animate) ?>>
               
               <?php if(($icon || $image) && $icon_position != 'right'){ ?>
                  <div class="highlight-icon">
                     <span class="icon-container <?php print $icon_class ?>" <?php print $style_icon ?>>
                        <?php if($icon){ ?><span class="icon <?php print $icon ?>"></span> <?php } ?>
                        <?php if($image){ ?><span class="icon"><img src="<?php print $image ?>"/> </span> <?php } ?>
                     </span>
                  </div>
               <?php } ?>

               <div class="highlight_content">
                  <div class="title">
                     <?php if($link){ ?><a href="<?php print $link ?>"> <?php } ?><?php print $title; ?><?php if($link){ ?> </a> <?php } ?>
                  </div>
                  <?php if($content){ ?>
                     <div class="desc"><?php print do_shortcode($content); ?></div>
                  <?php } ?>   
               </div>

                <?php if(($icon || $image) && $icon_position == 'right'){ ?>
                  <div class="highlight-icon">
                     <span class="icon-container <?php print $icon_class ?>" <?php print $style_icon ?>>
                        <?php if($icon){ ?><span class="icon <?php print $icon ?>"></span> <?php } ?>
                        <?php if($image){ ?><span class="icon"><img src="<?php print $image ?>"/> </span> <?php } ?>
                     </span>
                  </div>
               <?php } ?>

            </div> 
         <?php } ?>   

         <?php if($icon_position == 'top-left-title' || $icon_position == 'top-right-title'){ ?>
            <div class="widget gsc-icon-box <?php if(count($class)>0) print implode($class, ' ') ?>" <?php if(count($style) > 0) print 'style="'.implode($style, ';').'"' ?> <?php print gavias_print_animate_aos($animate) ?>>
               
               <div class="highlight_content">
                  <div class="title-inner">
                     
                     <?php if(($icon || $image) && $icon_position=='top-left-title'){ ?>
                        <div class="highlight-icon">
                           <span class="icon-container <?php print $icon_class ?>"  <?php print $style_icon ?>>
                              <?php if($icon){ ?><span class="icon <?php print $icon ?>"></span> <?php } ?>
                              <?php if($image){ ?><span class="icon"><img src="<?php print $image ?>"/> </span> <?php } ?>
                           </span>
                        </div>
                     <?php } ?>
                     
                     <div class="title">
                        <?php if($link){ ?><a href="<?php print $link ?>"> <?php } ?><?php print $title; ?><?php if($link){ ?> </a> <?php } ?>
                     </div>

                     <?php if(($icon || $image) && $icon_position=='top-right-title'){ ?>
                        <div class="highlight-icon">
                           <span class="icon-container <?php print $icon_class ?>"  <?php print $style_icon ?>>
                              <?php if($icon){ ?><span class="icon <?php print $icon ?>"></span> <?php } ?>
                              <?php if($image){ ?><span class="icon"><img src="<?php print $image ?>"/> </span> <?php } ?>
                           </span>
                        </div>
                     <?php } ?>

                  </div>
                  <?php if($content){ ?>
                     <div class="desc"><?php print do_shortcode($content); ?></div>
                  <?php } ?>   
               </div>

            </div> 
         <?php } ?>   

         <?php return ob_get_clean() ?>
       <?php
      }

      public function load_shortcode(){
         add_shortcode( 'icon_box', array($this, 'sc_icon_box') );
      }
   } 
endif;   
