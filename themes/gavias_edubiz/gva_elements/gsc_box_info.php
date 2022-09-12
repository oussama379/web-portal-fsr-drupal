<?php 
namespace Drupal\gavias_blockbuilder\shortcodes;
if(!class_exists('gsc_box_info')):
   class gsc_box_info{
      public function render_form(){
         return array(
           'type'          => 'gsc_box_info',
            'title'        => t('Box Information'),
            'size'         => 3,
            'fields' => array(
               array(
                  'id'        => 'title',
                  'type'      => 'text',
                  'title'     => t('Title'),
                  'class'     => 'display-admin'
               ),
               array(
                  'id'        => 'address',
                  'type'      => 'text',
                  'title'     => t('Address'),
               ),
               array(
                  'id'        => 'email',
                  'type'      => 'text',
                  'title'     => t('Email'),
               ),
               array(
                  'id'        => 'phone',
                  'type'      => 'text',
                  'title'     => t('Phone'),
               ),
               array(
                  'id'        => 'content',
                  'type'      => 'textarea',
                  'title'     => t('Content'),
                  'desc'      => t('Content for box info'),
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
            print self::sc_box_info( $item['fields'], $item['fields']['content'] );
      }

      public static function sc_box_info( $attr, $content = null ){
         global $base_url;
         extract(shortcode_atts(array(
            'title'              => '',
            'address'            => '',
            'email'              => '',
            'phone'              => '',
            'content'            => '',
            'el_class'           => '',
            'animate'            => '',
            'skin_text'          => ''
         ), $attr));

         if($animate){
            $el_class .= ' wow';
            $el_class .= ' '. $animate;
         }
         if($skin_text){
             $el_class .= ' '. $skin_text;
         }
         ?>
            <?php ob_start() ?>
            <div class="widget gsc-box-info <?php print $el_class ?>">
               <?php if($title){?><div class="widget-title title"><?php print $title ?></div> <?php } ?>
               <?php if($address){?><div class="address"><?php print $address ?></div> <?php } ?>
               <?php if($email){?><div class="email"><?php print $email ?></div> <?php } ?>
               <?php if($phone){?><div class="phone"><?php print $phone ?></div> <?php } ?>
               <?php if($content){?><div class="address"><?php print $content ?></div> <?php } ?>
           </div>
           <?php return ob_get_clean() ?>
      <?php
      } 

      public function load_shortcode(){
         add_shortcode( 'box_info', array('gsc_box_info', 'sc_box_info'));
      }
   }
endif;   
