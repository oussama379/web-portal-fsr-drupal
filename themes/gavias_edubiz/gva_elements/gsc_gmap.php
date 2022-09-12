<?php 
namespace Drupal\gavias_blockbuilder\shortcodes;
if(!class_exists('gsc_gmap')):
   class gsc_gmap{

      public function render_form(){
         $fields = array(
            'type' => 'gsc_gmap',
            'title' => t('Google Map'),
            'size' => 3,
            'fields' => array(
               array(
                  'id'     => 'title',
                  'type'      => 'text',
                  'title'  => t('Title for Admin'),
                  'class'     => 'display-admin'
               ),
               array(
                  'id'           => 'map_type',
                  'type'         => 'select',
                  'title'        => t('Map Type'),
                  'options'   => array(
                     'ROADMAP'      => 'ROADMAP',
                     'HYBRID'       => 'HYBRID',
                     'SATELLITE'    => 'SATELLITE',
                     'TERRAIN'      => 'TERRAIN'
                  )
               ), 
               array(
                  'id'        => 'link',
                  'type'      => 'text',
                  'title'     => t('Latitude, Longitude for map'),
                  'desc'         => 'eg: 21.0173222,105.78405279999993',
               ),
               array(
                  'id'           => 'height',
                  'type'         => 'text',
                  'title'        => 'Map height',
                  'desc'         => 'Enter map height (in pixels or leave empty for responsive map), eg: 400px',
                  'std'          => '400px'
               ),
               array(
                  'id'           => 'content',
                  'type'         => 'text',
                  'title'        => 'Text Address',
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
         print self::sc_gmap( $item['fields'] );
      }


      public static function sc_gmap( $attr, $content = null ){
         global $base_url;
         extract(shortcode_atts(array(
            'title'              => '',
            'map_type'           => 'ROADMAP',
            'link'               => '',
            'height'             => '',
            'info'               =>  '',
            'el_class'           => '',
            'animate'            => '',
            'content'            => ''
         ), $attr));

         $zoom = 14;
         $bubble = true;
         $_id = gavias_blockbuilder_makeid();
         $style = '[{"featureType":"water","elementType":"geometry","stylers":[{"color":"#e9e9e9"},{"lightness":17}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#f5f5f5"},{"lightness":20}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#ffffff"},{"lightness":17}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#ffffff"},{"lightness":29},{"weight":0.2}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#ffffff"},{"lightness":18}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#ffffff"},{"lightness":16}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#f5f5f5"},{"lightness":21}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#dedede"},{"lightness":21}]},{"elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#ffffff"},{"lightness":16}]},{"elementType":"labels.text.fill","stylers":[{"saturation":36},{"color":"#333333"},{"lightness":40}]},{"elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#f2f2f2"},{"lightness":19}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#fefefe"},{"lightness":20}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#fefefe"},{"lightness":17},{"weight":1.2}]}]';
      ?>

      <script type="text/javascript" src="//maps.google.com/maps/api/js?sensor=true&key=AIzaSyDWg9eU2MO9E0PF1ZMw9sFVJoPVU4Z6s3o"></script>
      <script type="text/javascript" src="<?php print (base_path() . drupal_get_path('theme', 'gavias_edubiz')) ?>/vendor/gmap3.js"></script>
      <script type="text/javascript" src="<?php print (base_path() . drupal_get_path('theme', 'gavias_edubiz')) ?>/vendor/jquery.ui.map.min.js"></script>
      <div id="map_canvas_<?php echo $_id; ?>" class="map_canvas" style="width:100%; height:<?php echo $height; ?>;"></div>

      <script type="text/javascript">
         jQuery(document).ready(function($) {
            var stmapdefault = '<?php echo $link; ?>';
            var marker = {position:stmapdefault}
            var content = '<?php print $content ?>';
        
            jQuery('#map_canvas_<?php echo $_id; ?>').gmap({
               'scrollwheel':false,
               'zoom': <?php echo  $zoom;  ?>  ,
               'center': stmapdefault,
               'mapTypeId':google.maps.MapTypeId.<?php echo ( $map_type ); ?>,
               'styles': <?php echo $style; ?>,
               'callback': function() {
                  var self = this;
                  self.addMarker(marker).click(function(){
                     if(content){
                        self.openInfoWindow({'content': content}, self.instance.markers[0]);
                     }                     
                  });
               },
               panControl: true
            });
         });
      </script>
      <?php
      }

      public function load_shortcode(){
         add_shortcode( 'gmap', array($this, 'sc_gmap') );
      }
   }
 endif;  



