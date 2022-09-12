<?php 
namespace Drupal\gavias_blockbuilder\shortcodes;
if(!class_exists('gsc_testimonial_single')):
   class gsc_testimonial_single{

      public function render_form(){
         $fields = array(
            'type' => 'gsc_testimonial_single',
            'title' => ('Testimonial single'), 
            'size' => 3,'fields' => array(
               array(
                  'id'        => 'title',
                  'type'      => 'textlangs',
                  'title'     => t('Name'),
                  'class'     => 'display-admin'
               ),
               array(
                  'id'        => 'job',
                  'type'      => 'textlangs',
                  'title'     => t('Job'),
               ),
               array(
                  'id'        => 'video',
                  'type'      => 'text',
                  'title'     => t('Video Link'),
               ),
               array(
                  'id'        => 'content',
                  'type'      => 'textarealangs',
                  'title'     => t('Content'),
                  'desc'      => t('Some Shortcodes and HTML tags allowed'),
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
         print self::sc_testimonial_single( $item['fields'], $item['fields']['content'] );
      }


      public static function sc_testimonial_single( $attr, $content = null ){
         extract(shortcode_atts(array(
            'title'           => '',
            'job'             => '',
            'video'           => '',
            'skin_text'       => '',
            'el_class'        => '',
            'animate'         => ''
         ), $attr));

         $class = array();
         if($el_class) $class[] = $el_class;
         if($skin_text) $class[] = $skin_text;

         ?>
         <?php ob_start() ?>
        
         <div class="widget gsc-testimonial-single <?php if(count($class)>0) print implode($class, ' ') ?>" <?php print gavias_print_animate_aos($animate) ?>>
            <div class="box-content">
               <?php if($video){ ?> 
                  <div class="video"><a class="popup-video" href="<?php print $video ?>"><?php print t('Play video') ?></a></div>
               <?php } ?>
               <?php if(gavias_render_textarealangs($content)){ ?>
                  <div class="quote"><?php print gavias_render_textarealangs($content); ?></div>
               <?php } ?> 
               <div class="info">
                  <?php if(gavias_render_textlangs($title)){ ?>
                     <div class="title"><?php print gavias_render_textlangs($title); ?></div>
                  <?php } ?>  
                  <?php if(gavias_render_textlangs($job)){ ?>
                     <div class="job"><?php print gavias_render_textlangs($job); ?></div>
                  <?php } ?>   
               </div>    
            </div>
         </div> 

         <?php return ob_get_clean() ?>
       <?php
      }

      public function load_shortcode(){
         add_shortcode( 'testimonial_single', array($this, 'sc_testimonial_single') );
      }
   } 
endif;   
