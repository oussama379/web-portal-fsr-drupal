<?php 
namespace Drupal\gavias_blockbuilder\shortcodes;
if(!class_exists('gsc_image_content')):
   class gsc_image_content{
      public function render_form(){
         return array(
           'type'          => 'gsc_image_content',
            'title'        => t('Image content'),
            'size'         => 3,
            'icon'         => 'fa fa-bars',
            'fields' => array(
            
               array(
                  'id'        => 'title',
                  'type'      => 'text',
                  'title'     => t('Title'),
                  'class'     => 'display-admin'
               ),
               array(
                  'id'        => 'background',
                  'type'      => 'upload',
                  'title'     => t('Background images')
               ),
               array(
                  'id'        => 'content',
                  'type'      => 'textarea',
                  'title'     => t('Content'),
                  'desc'      => t('Some HTML tags allowed'),
               ),
               array(
                  'id'        => 'link',
                  'type'      => 'text',
                  'title'     => t('Link'),
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
                  'id'        => 'skin',
                  'type'      => 'select',
                  'title'     => t('Skin'),
                  'options'   => array( 
                     'skin-v1' => t('Skin #1'), 
                     'skin-v2' => t('Skin #2'), 
                  ),
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
      }

      public function render_content( $item ) {
         if( ! key_exists('content', $item['fields']) ) $item['fields']['content'] = '';
            print self::sc_image_content( $item['fields'], $item['fields']['content'] );
      }

      public static function sc_image_content( $attr, $content = null ){
         global $base_url;
         extract(shortcode_atts(array(
            'title'              => '',
            'icon'               => '',
            'background'         => '',
            'link'               => '',
            'target'             => '',
            'skin'               => 'skin-v1',
            'el_class'           => '',
            'animate'            => ''
         ), $attr));

         // target
         if( $target =='on' ){
            $target = 'target="_blank"';
         } else {
            $target = false;
         }
         
         if($background) $background = $base_url . $background; 

         if($skin) $el_class .= ' ' . $skin;

         ?>
         <?php ob_start() ?>

         <div class="gsc-image-content <?php print $el_class; ?>" <?php print gavias_print_animate_aos($animate) ?>>
            <div class="image"><?php if($link){ ?><a <?php print $target ?> href="<?php print $link ?>"><?php } ?><img src="<?php print $background ?>" alt="<?php print $title ?>" /><?php if($link){ ?></a><?php } ?></div>
            <div class="content">
               <?php if($title){ ?><h4 class="title"><?php print $title ?></h4><?php } ?>   
               <div class="desc"><?php print $content; ?></div>
               <?php if($link){ ?>
                  <div class="action"><a <?php print $target ?> href="<?php print $link ?>"><?php print t('Read more') ?></a></div>
               <?php } ?>  
            </div>  
         </div>

         <?php return ob_get_clean() ?>
        <?php            
      } 

      public function load_shortcode(){
         add_shortcode( 'image_content', array($this, 'sc_image_content'));
      }
   }
endif;   
