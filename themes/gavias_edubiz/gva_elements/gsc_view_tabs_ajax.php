<?php 
namespace Drupal\gavias_blockbuilder\shortcodes;
use Drupal\views\Views;
use Drupal\views\Element\View;
if(!class_exists('gsc_view_tabs_ajax')):
   class gsc_view_tabs_ajax{

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
            'type' => 'gsc_view_tabs_ajax',
            'title' => t('View Tabs Ajax'),
            'size' => 3,
            'fields' => array(
               array(
                  'id'        => 'title',
                  'type'      => 'text',
                  'title'     => t('Title For Admin'),
                  'class'     => 'display-admin'
               ),
               array(
                  'id'        => 'style',
                  'type'      => 'select',
                  'title'     => t('Style display'),
                  'options'   => array(
                     'style-1'   => 'Style #1', 
                     'style-2'   =>'Style #2'
                  )
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
            ),                                     
         );

         for($i=1; $i<=10; $i++){
            $fields['fields'][] = array(
               'id'     => "info_${i}",
               'type'   => 'info',
               'desc'   => "Information for tab item {$i}"
            );
            $fields['fields'][] = array(
               'id'        => "title_{$i}",
               'type'      => 'text',
               'title'     => t("Title {$i}")
            );
            $fields['fields'][] = array(
               'id'        => "view_{$i}",
               'type'      => 'select',
               'title'     => t("View {$i}"),
               'options'   => $view_display
            );
         }
         return $fields;
      }

      public function render_content( $item ) {
         print self::sc_tab_views( $item['fields'] );
      }

      public static function sc_tab_views( $attr, $content = null ){
         $default = array(
            'title'      => '',
            'el_class'   => '',
            'style'      => 'style-1',
            'animate'    => '',
         );

         for($i=1; $i<=10; $i++){
            $default["title_{$i}"] = '';
            $default["view_{$i}"] = '';
         }

         extract(shortcode_atts($default, $attr));

         if($animate){
            $el_class .= ' wow';
            $el_class .= ' ' . $animate;
         }
         $el_class .= ' ' . $style;

         $_id = gavias_blockbuilder_makeid();
         ?>
         <?php ob_start() ?>
         <div class="gsc-tab-views block widget gsc-tabs-views-ajax <?php echo $el_class ?>" <?php print gavias_print_animate_aos($animate) ?>>  
            <div class="block-content">
               <div class="list-links-tabs clearfix">
                  <ul class="nav nav-tabs links-ajax" data-load="ajax">
                     <?php 
                     for($i=1; $i<=10; $i++){ 
                        $title = "title_{$i}";
                        if(!empty($$title)){
                     ?>
                        <li class="<?php print ($i==1?'active':'') ?>"><a data-toggle="tab" href="#tab-item-<?php print ($_id . $i) ?>"><?php print $$title ?></a></li>
                     <?php 
                        }
                     } 
                     ?>
                  </ul>
               </div>  
               <div class="tab-content tab-content-view">
                  <?php for($i=1; $i<=10; $i++){ 
                     $output = '';
                     $view = "view_{$i}";
                     $view_name = $view;
                     $title = "title_{$i}";
                     if(!empty($$title)){
                        if($i==1){
                           if($$view){
                              $_view =  preg_split("/-----/", $$view);
                              if(isset($_view[0]) && isset($_view[1])){
                                 try{
                                    $view = Views::getView($_view[0]);
                                    if($view){
                                       $v_output = $view->buildRenderable($_view[1], [], FALSE);
                                       if($v_output){
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
                                 $view = null;
                                 $v_output = null;
                              }
                           }else{
                              $output .= '<div>Missing view, please choose view"</div>';
                           }
                           print '<div data-loaded="true" data-view="'.  $$view_name . '" class="tab-pane clearfix fade in '.(($i==1)?'active':'').'" id="tab-item-' . $_id . $i . '">'.$output.'</div>';     
                        }else{
                           print '<div data-loaded="false" data-view="'.  $$view_name . '" class="tab-pane clearfix fade in '.(($i==1)?'active':'').'" id="tab-item-' . $_id . $i . '">Loadding</div>';
                        }
                     }
                  } ?>
               </div>
            </div>   
         </div>   
         <?php return ob_get_clean();
      }

      public function load_shortcode(){
         add_shortcode( 'sc_tab_views', array($this, 'sc_tab_views') );
      }
   }
 endif;  



