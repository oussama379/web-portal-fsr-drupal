<?php 
namespace Drupal\gavias_blockbuilder\shortcodes;
if(!class_exists('gsc_tabs')):
   global $tabs_array, $tabs_count;
   class gsc_tabs{

      public function render_form(){
         $fields = array(
            'type'   => 'gsc_tabs',
            'title'  => t('Tabs'), 
            'size'   => 3, 
            
            'fields' => array(
         
               array(
                  'id'        => 'title',
                  'type'      => 'text',
                  'title'     => t('Title for admin'),
                  'class'     => 'display-admin'
               ),
            
               array(
                  'id'        => 'tabs',
                  'type'      => 'tabs',
                  'title'     => t('Tabs'),
                  'sub_desc'  => t('To add an <strong>icon</strong> in Title field, please use the following code:<br/><br/>&lt;i class=" icon-lamp"&gt;&lt;/i&gt; Tab Title'),
                  'desc'      => t('You can use Drag & Drop to set the order.'),
               ),
               
               array(
                  'id'        => 'type',
                  'type'      => 'select',
                  'options'   => array(
                     'horizontal'   => 'Horizontal',
                     'horizontal_icon'   => 'Horizontal Icon',
                     'vertical'     => 'Vertical', 
                  ),
                  'title'  => t('Style'),
                  'desc'      => t('Vertical tabs works only for column widths: 1/2, 3/4 & 1/1'),
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
         print self::sc_tabs( $item['fields'] );
      }

      public static function sc_tabs( $attr, $content = null ){
         global $tabs_array, $tabs_count;
         
         extract(shortcode_atts(array(
            'title'     => '',
            'uid'       => 'tab-',
            'tabs'      => '',
            'type'      => '',
            'el_class'  => '',
            'animate'   => ''
         ), $attr)); 
         do_shortcode( $content );
         $_id = gavias_blockbuilder_makeid();
         $uid .= $_id;

         if($tabs) $tabs_array = $tabs;
         
         $output = '<div class="gsc-tabs '.$el_class.'" ' . gavias_print_animate_aos($animate) . '>';
         if( is_array( $tabs_array ) ){
            $output .= '<div class="tabs_wrapper tabs_'. $type .'">';
     
               $output .= '<ul class="nav nav-tabs">';
                  $i = 1;
                  $output_tabs = '';
                  foreach( $tabs_array as $tab ){
                     $icon = '';
                     if(isset($tab['icon']) && $tab['icon']){
                        $icon = '<span><i class="fa ' . $tab['icon'] . '"></i></span>';
                     }
                     $output .= '<li '.($i==1?'class="active"':'').'><a data-toggle="tab" href="#'. $uid .'-'. $i .'">' . $icon . $tab['title'] .'</a></li>';
                     $output_tabs .= '<div id="'. $uid .'-'. $i .'" class="tab-pane fade in '.($i==1?'active':'').'">'. do_shortcode( $tab['content'] ) .'</div>';
                     $i++;
                  }
               $output .= '</ul>';
               $output .= '<div class="tab-content">';
                  $output .= $output_tabs;
               $output .= '</div>';
            $output .= '</div>';
            
            $tabs_array = array();
            $tabs_count = 0;  
         }
         $output .= '</div>';
         return $output;
      }



      public static function sc_tab( $attr, $content = null ){
         global $tabs_array, $tabs_count;
         
         extract(shortcode_atts(array(
            'title' => 'Tab title',
         ), $attr));
         
         $tabs_array[] = array(
            'title' => $title,
            'content' => do_shortcode( $content )
         ); 
         $tabs_count++;
      
         return true;
      }

      public function load_shortcode(){
         add_shortcode( 'tabs', array($this, 'sc_tabs') );
         add_shortcode( 'tab', array($this, 'sc_tab') );
      }
   }
endif;


