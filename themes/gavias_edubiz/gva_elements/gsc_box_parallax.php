<?php 
namespace Drupal\gavias_blockbuilder\shortcodes;
if(!class_exists('gsc_box_parallax')):
   class gsc_box_parallax{
      public function render_form(){
         return array(
           'type'          => 'gsc_box_parallax',
            'title'        => t('Box Parallax'),
            'size'         => 3,
            'fields' => array(
               array(
                  'id'        => 'title',
                  'type'      => 'textlangs',
                  'title'     => t('Title'),
                  'class'     => 'display-admin'
               ),
                array(
                  'id'        => 'subtitle',
                  'type'      => 'textlangs',
                  'title'     => t('Sub Title')
               ),
               array(
                  'id'        => 'image',
                  'type'      => 'upload',
                  'title'     => t('Image'),
                  'desc'      => t('Image for box info'),
               ),
               array(
                  'id'        => 'content',
                  'type'      => 'textarealangs',
                  'title'     => t('Content'),
                  'desc'      => t('Content for box info'),
               ),
               array(
                  'id'        => 'content_align',
                  'type'      => 'select',
                  'title'     => t('Content Align'),
                  'desc'      => t('Align Content for box info'),
                  'options'   => array( 'left' => 'Left', 'right' => 'Right' ),
                  'std'       => 'left'
               ),
               array(
                  'id'        => 'content_bg',
                  'type'      => 'text',
                  'title'     => t('Background content'),
                  'desc'      => t('Background color for content. e.g. #f5f5f5'),
               ),
               array(
                  'id'        => 'content_color',
                  'type'      => 'text',
                  'title'     => t('Content color'),
                  'desc'      => t('Color for content. e.g. #f5f5f5. default color-text for theme'),
               ),
               array(
                  'id'        => 'link',
                  'type'      => 'textlangs',
                  'title'     => t('Link'),
               ),
               array(
                  'id'        => 'link_title',
                  'type'      => 'textlangs',
                  'title'     => t('Link Title'),
                  'std'       => 'Read more'
               ),
               array(
                  'id'        => 'target',
                  'type'      => 'select',
                  'title'     => t('Open in new window'),
                  'desc'      => t('Adds a target="_blank" attribute to the link'),
                  'options'   => array( 'off' => 'No', 'on' => 'Yes' ),
                  'std'       => 'on'
               ),
               array(
                  'id'        => 'el_class',
                  'type'      => 'text',
                  'title'     => t('Extra class name'),
                  'desc'      => t('Style particular content element differently - add a class name and refer to it in custom CSS.'),
               ),
            ),                                     
         );
      }

      public function render_content( $item ) {
         if( ! key_exists('content', $item['fields']) ) $item['fields']['content'] = '';
            print self::sc_box_parallax( $item['fields'], $item['fields']['content'] );
      }

      public static function sc_box_parallax( $attr, $content = null ){
         global $base_url;
         extract(shortcode_atts(array(
            'title'              => '',
            'subtitle'           => '',
            'image'              => '',
            'height'             => '1px',
            'content_align'      => '',
            'content_bg'         => '',
            'content_color'      => 'dark',
            'link'               => '',
            'link_title'         => 'Readmore',
            'target'             => '',
            'el_class'           => '',
            'animate'            => ''
         ), $attr));

         // target
         if( $target ){
            $target = 'target="_blank"';
         } else {
            $target = false;
         }
         if($image) $image = $base_url . $image;

         $style_content = '';
         if($content_bg){
            $style_content = 'style="background-color: ' . $content_bg . '"';
         }
         if($animate){
            $el_class .= ' wow';
            $el_class .= ' '. $animate;
         }

         ?>
            <?php ob_start() ?>
            <div class="widget gsc-box-parallax <?php print $el_class ?> content-align-<?php print $content_align ?>">
               <div class="clearfix">
                  <div class="col-first">
                     <div class="image"><img src="<?php print $image ?>" alt="<?php print gavias_render_textlangs($title) ?>"/></div>   
                  </div>
                  <div class="col-second skrollable skrollable-between" data-bottom-top="top: -10%;" data-top-bottom="top: 30%;">
                     <div class="content-inner"  <?php print $style_content ?>>
                        <div class="content-bg" <?php print $style_content ?>></div>
                        <div class="content-inner">
                           <?php if(gavias_render_textlangs($subtitle)){ ?>
                              <div class="subtitle"><span><?php print gavias_render_textlangs($subtitle); ?></span></div>
                           <?php } ?>  
                           <?php if(gavias_render_textlangs($title)){ ?>
                              <div class="title"><h3><?php print gavias_render_textlangs($title); ?></h3></div>
                            <?php } ?>    
                           <?php if(gavias_render_textarealangs($content)){ ?>
                              <div class="desc"><?php print do_shortcode(gavias_render_textarealangs($content)); ?></div>
                           <?php } ?>   
                           <?php if(gavias_render_textlangs($link)){ ?>
                              <div class="readmore"><a class="btn-theme" href="<?php print gavias_render_textlangs($link) ?>"><?php print gavias_render_textlangs($link_title) ?></a></div>
                           <?php } ?>
                        </div>
                     </div>
                  </div> 
               </div>   
           </div>
           <?php return ob_get_clean() ?>
      <?php
      } 

      public function load_shortcode(){
         add_shortcode( 'box_parallax', array($this, 'sc_box_parallax'));
      }
   }
endif;   
