<?php 
namespace Drupal\gavias_blockbuilder\shortcodes;
if(!class_exists('gsc_our_partners')):
   class gsc_our_partners{

      public function render_form(){
         $fields = array(
            'type'   => 'gsc_our_partners',
            'title'  => t('Our Partners'), 
            'size'   => 3,
            'icon'   => 'fa fa-bars',
            'fields' => array(
               array(
                  'id'        => 'title',
                  'type'      => 'text',
                  'title'     => t('Name'),
                  'class'     => 'display-admin'
               ),
               array(
                  'id'        => 'image',
                  'type'      => 'upload',
                  'title'     => t('Photo'),
               ),
               array(
                  'id'        => 'content',
                  'type'      => 'textarea',
                  'title'     => t('Content'),
               ),
               array(
                  'id'        => 'address',
                  'type'      => 'text',
                  'title'     => t('Address'),
               ),
               array(
                  'id'        => 'category',
                  'type'      => 'text',
                  'title'     => t('Category'),
               ),
               array(
                  'id'        => 'link',
                  'type'      => 'text',
                  'title'     => t('Link'),
               ),
               array(
                  'id'        => 'target',
                  'type'      => 'select',
                  'title'     => ('Open in new window'),
                  'desc'      => ('Adds a target="_blank" attribute to the link.'),
                  'options'   => array( 0 => 'No', 1 => 'Yes' ),
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
                  'sub_desc'  => t('Entrance animation'),
                  'options'   => gavias_blockbuilder_animate_aos()
               ),
            ),                                      
         );
         return $fields;
      }

      public function render_content( $item ) {
         if( ! key_exists('content', $item['fields']) ) $item['fields']['content'] = '';
         print self::sc_our_partners( $item['fields'], $item['fields']['content'] );
      }

      public static function sc_our_partners( $attr, $content = null ){
         global $base_url;
         extract(shortcode_atts(array(  
            'title'         => '',
            'image'         => '', 
            'content'       => '',
            'address'       => '',
            'category'      => '',
            'link'          => '',
            'target'        => '',
            'animate'       => '',
            'el_class'     => ''
         ), $attr));
         
         if($image){
            $image = $base_url . $image;
         }
         if( $target ){
            $target = 'target="_blank"';
         } else {
            $target = false;
         }
         ?>
         <?php ob_start() ?>
        
            <div class="widget gsc-our-partners <?php print $el_class ?>" <?php print gavias_print_animate_aos($animate) ?>>
               <?php if($image){ ?>
                  <div class="image"><img src="<?php print $image ?>" alt="<?php ?>"/></div>
               <?php } ?>

               <div class="content-inner">
                  <?php if($title){ ?>
                     <div class="title">
                        <?php if($link){ ?><a href="<?php $link ?>" <?php print $target ?>><?php } ?> 
                           <?php print $title ?>
                        <?php if($link){print '</a>'; } ?>
                     </div>
                  <?php } ?>    
                  <div class="info">
                     <?php if($category){ ?>
                        <span class="category"><?php print $category ?>,</span>
                     <?php } ?>
                     <?php if($address){ ?>
                        <span class="address"><?php print $address ?></span>
                     <?php } ?>
                  </div>
                  <?php if($content){ ?>
                     <div class="content"><?php print $content ?></div>
                  <?php } ?>                       
               </div>

            </div>

         <?php return ob_get_clean() ?>
         <?php
      }

      public function load_shortcode(){
         add_shortcode( 'our_team', array('gsc_our_partners', 'sc_our_partners' ));
      }
   }
endif;


