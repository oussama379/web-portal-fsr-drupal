<?php 
namespace Drupal\gavias_blockbuilder\shortcodes;
if(!class_exists('gsc_image')):
   class gsc_image{
      
      public function render_form(){
         $fields =array(
            'type' => 'gsc_image',
            'title' => ('Image'), 
            'size' => 3,
            'fields' => array(
               array(
                  'id'        => 'image',
                  'type'      => 'upload',
                  'title'     => t('Image'),
               ),
               array(
                  'id'        => 'align',
                  'type'      => 'select',
                  'title'     => t('Align Image'),
                  'options'   => array( 
                     ''          => 'None', 
                     'left'      => 'Left', 
                     'right'     => 'Right', 
                     'center'    => 'Center', 
                  ),
               ),
               array(
                  'id'     => 'margin',
                  'type'      => 'text',
                  'title'  => t('Margin Top'),
                  'desc'      => t('example: 30px'),
               ),
               array(
                  'id'     => 'alt',
                  'type'      => 'text',
                  'title'  => t('Alternate Text'),
               ),
               array(
                  'id'     => 'link',
                  'type'      => 'text',
                  'title'  => t('Link')
               ),
               array(
                  'id'     => 'target',
                  'type'      => 'select',
                  'options'   => array( 'off' => 'No', 'on' => 'Yes' ),
                  'title'  => t('Open in new window'),
                  'desc'      => t('Adds a target="_blank" attribute to the link.'),
               ),
               array(
                  'id'     => 'animate',
                  'type'      => 'select',
                  'title'  => t('Animation'),
                  'sub_desc'  => t('Entrance animation'),
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
         return $fields;
      }

      public function render_content( $item ) {
         print self::sc_image( $item['fields'] );
      }

      public static function sc_image( $attr, $content = null ){
         global $base_url;
         extract(shortcode_atts(array(
            'image'        => '',
            'border'       => 'off',
            'alt'          => '',
            'margin'       => '',
            'align'        => 'none',
            'link'         => '',
            'target'       => 'off',
            'animate'      => '',
            'el_class'     => ''
         ), $attr));
            
         $image = $base_url . $image; 

         if( $align ) $align = 'text-'. $align;
         
         if( $target=='on' ){
            $target = 'target="_blank"';
         } else {
            $target = '';
         }
         
         if( $margin ){
            $margin = 'style="margin-top:'. intval( $margin ) .'px"';
         } else {
            $margin = '';
         }

         $class_array = array();
         $class_array[] = $align;
         $class_array[] = $el_class;
         ?>
         <?php ob_start() ?>
            <div class="widget gsc-image<?php if(count($class_array) > 0) print (' ' . implode($class_array, ' ')) ?>" <?php print $margin ?> <?php print gavias_print_animate_aos($animate) ?>>
               <div class="widget-content">
                  <?php if($link){ ?>
                     <a href="<?php print $link ?>" <?php print $target ?>>
                  <?php } ?> 
                    <img src="<?php print $image ?>" alt="<?php print $alt ?>" />
                  <?php if($link){print '</a>'; } ?>
               </div>
            </div>    
         <?php return ob_get_clean() ?>  
         <?php       
      }

      public function load_shortcode(){
         add_shortcode( 'image', array('gsc_image', 'sc_image') );
      }
   }
endif;   




