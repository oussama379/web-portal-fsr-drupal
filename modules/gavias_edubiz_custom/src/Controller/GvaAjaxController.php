<?php
/**
 * @file
 * Contains \Drupal\gavias_hook_themer\Controller\GvaAjaxController.
 */

namespace Drupal\gavias_hook_themer\Controller;
use  Drupal\Core\Cache\Cache;
use Drupal\Core\Url;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Response;
use Drupal\views\Views;
use Drupal\views\Element\View;

class GvaAjaxController extends ControllerBase {

   public function gavias_ajax_view() {
      $view = $_REQUEST['view'];
      if($view){
         $_view =  preg_split("/-----/", $view);
         if(isset($_view[0]) && isset($_view[1])){
            $output .= '<div class="ajax-view-content clearfix">';
            try{
               $view = Views::getView($_view[0]);
               if($view && is_object($view)){
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
            $output .= '</div>';
         }
      }
      print $output;
      exit(0);
   }
}