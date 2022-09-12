<?php 
namespace Drupal\gavias_blockbuilder\shortcodes;
use Drupal\views\Views;
use Drupal\views\Element\View;
if(!class_exists('gsc_view')):
   class gsc_view{
      
      public function render_form(){
         $view_options = Views::getViewsAsOptions(TRUE, 'all', NULL, FALSE, TRUE);
         $view_display = array();
         foreach ($view_options as $view_key => $view_name) {
            $view = Views::getView($view_key);
            $view_display[''] = '-- None --';
            foreach ($view->storage->get('display') as $name => $display) {
               if($display['display_plugin']=='block'){
                  $view_display[$view_key . '-----' . $name] = $view_name .' || '. $display['display_title'];
               }
            }
         }

         $fields = array(
            'type' => 'gsc_view',
            'title' => ('Drupal View'),
            'size' => 12,
            
            'fields' => array(
               array(
                  'id'        => 'title_admin',
                  'type'      => 'text',
                  'title'     => t('Title'),
                  'class'       => 'display-admin',
               ),
               array(
                  'id'        => 'title',
                  'type'      => 'text',
                  'title'     => t('Title'),
               ),
               array(
                  'id'        => 'view',
                  'type'      => 'select',
                  'title'     => t('View Name'),
                  'options'   => $view_display,
                  'class'     => 'gsc_display_view',
                  'std'       => '',
               ),
               array(
                  'id'        => 'view_arg',
                  'type'      => 'text',
                  'title'     => t('View Arg'),
                  'std'       => '',
               ),
               array(
                  'id'        => 'show_title',
                  'type'      => 'select',
                  'title'     => t('Show Title'),
                  'options'   => array('hidden' => 'Hidden', 'title_view'=>'Title View', 'title_block'=>'Title Block'),
                  'std'       => 'hidden',
                  'desc'      => t('Hidden title default for block')
               ),
               array(
                  'id'        => 'style_text',
                  'type'      => 'select',
                  'title'     => t('Skin Text for box'),
                  'options'   => array(
                     'text-dark'   => 'Text dark',
                     'text-light'   => 'Text light',
                  ),
                  'std'       => 'text-dark'
               ),
               array(
                  'id'        => 'align_title',
                  'type'      => 'select',
                  'title'     => t('Align title'),
                  'options'   => array('title-align-left' => 'Align Left', 'title-align-right'=>'Align Right', 'title-align-center' => 'Align Center'),
                  'std'       => 'title-align-center',
                  'desc'      => t('Align title default for block')
               ),
               array(
                  'id'        => 'remove_margin',
                  'type'      => 'select',
                  'title'     => ('Remove Margin'),
                  'options'   => array('off'=>'No', 'on' => 'Yes'),
                  'std'       => 'off',
                  'desc'      => t('Defaut block margin bottom 60px, You can remove margin for block')
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
         return $fields;
      }

      public function render_content( $item ) {
         print self::sc_drupal_block( $item['fields'] );
      }

      public function sc_drupal_block( $attr, $content = null ){
         extract(shortcode_atts(array(
            'title_admin'        => '',
            'title'              => '',
            'view'               => '',
            'view_arg'           => '',
            'show_title'         => 'hidden',
            'align_title'        => 'title-align-center',
            'style_text'         => '',
            'el_class'           => '',
            'remove_margin'      => 'off',
            'animate'            => ''
         ), $attr));
         
         if(!$view) return "None view choose";

         $output = '';
         $class = array();
         $class[] = $align_title; 
         $class[] = $el_class;
         $class[] = $style_text;
         $class[] = 'remove-margin-' . $remove_margin;

         $view_tmp = $view;
         $_view =  preg_split("/-----/", $view);

         if(isset($_view[0]) && isset($_view[1])){
            $output .= '<div>';
               $output .= '<div class="widget block gsc-block-view  gsc-block-drupal block-view '.implode($class, ' ') .'" ' . gavias_print_animate_aos($animate) .'>';
               if($title && $show_title == 'title_block'){
                  $output .= '<h2 class="block-title title-shortcode"><span>' . $title . '</span></h2>';
               }
               try{
                  $view = Views::getView($_view[0]);
                  if($view){
                     $v_output = $view->buildRenderable($_view[1], [], FALSE);
                     if($v_output){
                        if($view->getTitle() && $show_title == 'title_view'){
                           $output .= '<h2 class="block-title title-view"><span>' . $view->getTitle() . '</span></h2>';
                        }
                        $v_output['#view_id'] = $view->storage->id();
                        $v_output['#view_display_show_admin_links'] = $view->getShowAdminLinks();
                        $v_output['#view_display_plugin_id'] = $view->display_handler->getPluginId();
                        views_add_contextual_links($v_output, 'block', $_view[1]);
                        $v_output = View::preRenderViewElement($v_output);
                        if (empty($v_output['view_build'])) {
                          $v_output = ['#cache' => $v_output['#cache']];
                        }
                        if($v_output){
                          $output .= render($v_output);
                        }
                     }
                  }else{
                     $output .= '<div>Missing view, block "'.$view_tmp.'"</div>';
                  }
               }catch(PluginNotFoundException $e){
                     $output .= '<div>Missing view, block "'.$view_tmp.'"</div>';
               }
            $output .= '</div></div>';
            
            $view = null;
            $v_output = null;
         } 
         return $output;  
      }

    public function load_shortcode(){ }

   }
endif;
   



