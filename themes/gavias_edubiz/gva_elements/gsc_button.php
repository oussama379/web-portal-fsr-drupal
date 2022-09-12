<?php 
namespace Drupal\gavias_blockbuilder\shortcodes;
if(!class_exists('gsc_button')):
   class gsc_button{
      
      public static function gsc_button_id($length=12){
         $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
         $randomString = '';
         for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
         }
         return $randomString;
      }

      public function render_form(){
         $fields =array(
            'type' => 'gsc_button',
            'title' => ('Button'), 
            'size' => 3,
            'fields' => array(
               array(
                  'id'        => 'title',
                  'type'      => 'text',
                  'title'     => t('Title'),
                  'class'     => 'display-admin'
               ),
               array(
                  'id'        => 'size',
                  'type'      => 'select',
                  'title'     => t('Size'),
                  'options'   => array(
                        'mini'         => 'Mini',
                        'small'        => 'Small',
                        'medium'       => 'Medium',
                        'large'        => 'Large',
                        'extra-large'  => 'Extra Large',
                  )
               ),
               array(
                  'id'        => 'color',
                  'type'      => 'text',
                  'title'     => t('Text color'),
                  'desc'      => 'Sample: #ccc',
                  'std'       => '#000'
               ),
               array(
                  'id'        => 'border_color',
                  'type'      => 'text',
                  'title'     => t('Border Color'),
                  'std'       => '#000'
               ),
               array(
                  'id'        => 'background_color',
                  'type'      => 'text',
                  'title'     => t('Background Color'),
                  'std'       => ''
               ),
               array(
                  'id'        => 'border_radius',
                  'type'      => 'select',
                  'title'     => t('Border radius'),
                  'options'   => array(
                     ''          => 'None',
                     'radius-2x' => 'Border radius 2x',
                     'radius-5x' => 'Border radius 5x',
                  )
               ),
               array(
                  'id'        => 'link',
                  'type'      => 'text',
                  'title'     => t('Link'),
               ),

               array(
                  'id'        => 'color_hover',
                  'type'      => 'text',
                  'title'     => t('Text Color Hover'),
                  'desc'      => 'Sample: #ccc'
               ),
               array(
                  'id'        => 'border_color_hover',
                  'type'      => 'text',
                  'title'     => t('Border Color Hover'),
               ),
               array(
                  'id'        => 'background_color_hover',
                  'type'      => 'text',
                  'title'     => t('Background Color Hover'),
               ),
               array(
                  'id'        => 'animate',
                  'type'      => 'select',
                  'title'     => t('Animation'),
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
         print self::sc_button( $item['fields'] );
      }

      public static function sc_button( $attr, $content = null ){
         global $base_url;
         extract(shortcode_atts(array(
            'content'               => '',
            'title'          => 'Read more',
            'size'                  => 'mini',
            'color'                 => '#000',
            'border_color'          => '#000',
            'background_color'      => '',
            'border_radius'         => '',
            'link'                  => '',
            'background_color_hover'=> '',
            'color_hover'           => '',
            'border_color_hover'    => '',
            'animate'               => '',
            'el_class'              => ''
         ), $attr));
         $_id = 'button-' . self::gsc_button_id(12);
        
         $classes = array();
         $classes[] = "{$el_class} ";

         if($border_radius){
            $classes[] = "{$border_radius} ";
         }

         $classes[] = " {$size} ";

         $styles = array();
         if($background_color){
            $styles[] = "background:{$background_color};";
         }
         if($color){
            $styles[] = "color:{$color};";
         }
         if($border_color){
            $styles[] = "border-color:{$border_color};";
         }

         $styles_hover = array();
         if($background_color_hover){
            $styles_hover[] = "background:{$background_color_hover};";
         }
         if($color_hover){
            $styles_hover[] = "color:{$color_hover};";
         }
         if($border_color_hover){
            $styles_hover[] = "border-color:{$border_color_hover};";
         }

         ?>

         <?php ob_start() ?>

         <style rel="stylesheet">
            <?php print "#{$_id}{".implode('', $styles)."}" ?>
            <?php print "#{$_id}:hover{".implode('', $styles_hover)."}" ?>
         </style>

         <div class="clearfix"></div>
         <a href="<?php print $link ?>" class="gsc-button <?php print implode('', $classes) ?>" id="<?php print $_id; ?>" <?php print gavias_print_animate_aos($animate) ?>>
            <?php print $title ?>
         </a> 

         <?php return ob_get_clean() ?>

         <?php       
      }

      public function load_shortcode(){
         add_shortcode( 'gsc_button', array($this, 'sc_button') );
      }
   }
endif;   




