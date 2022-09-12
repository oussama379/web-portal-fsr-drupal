<?php 
namespace Drupal\gavias_blockbuilder\shortcodes;
if(!class_exists('gsc_our_team')):
   class gsc_our_team{

      public function render_form(){
         $fields = array(
            'type'   => 'gsc_our_team',
            'title'  => t('Our Team'), 
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
                  'id'        => 'job',
                  'type'      => 'text',
                  'title'     => t('Job'),
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
                  'id'        => 'facebook',
                  'type'      => 'text',
                  'title'     => t('Link Facebook'),
               ),
               array(
                  'id'        => 'twitter',
                  'type'      => 'text',
                  'title'     => t('Link Twitter'),
               ),
               array(
                  'id'        => 'pinterest',
                  'type'      => 'text',
                  'title'     => t('Link Pinterest'),
               ),
               array(
                  'id'        => 'google',
                  'type'      => 'text',
                  'title'     => 'Link Google'
               ),
               array(
                  'id'        => 'style',
                  'type'      => 'select',
                  'options'   => array(
                     'vertical'           => 'Vertical',
                     'horizontal'         => 'Horizontal',
                     'circle'             => 'Circle'
                  ),
                  'title'     => t('Style'),
                  'std'       => 'vertical',
               ),
               array(
                  'id'        => 'content_align',
                  'type'      => 'select',
                  'title'     => t('Content Align'),
                  'desc'      => t('Align Content for box info'),
                  'options'   => array( 'left' => 'Left', 'center' => 'center', 'right' => 'Right' ),
                  'std'       => 'left'
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
         print self::sc_our_team( $item['fields'], $item['fields']['content'] );
      }

      public static function sc_our_team( $attr, $content = null ){
         global $base_url;
         extract(shortcode_atts(array(  
            'image'         => '',   
            'title'         => '',
            'job'           => '',
            'content'       => '',
            'facebook'      => '',
            'twitter'       => '',
            'pinterest'     => '',
            'google'        => '',
            'style'         => 'vertical',
            'content_align' => 'left',
            'link'          => '',
            'target'        => '',
            'animate'       => '',
            'el_class'     => ''
         ), $attr));
         
         $image = substr(base_path(), 0, -1) . $image;
         
         if( $target ){
            $target = 'target="_blank"';
         } else {
            $target = false;
         }

         ?>
         <?php ob_start() ?>
         <?php
         //Style display horizontal
          if($style=='horizontal'){ ?>
            <div class="widget gsc-team team-horizontal <?php print $el_class ?>" <?php print gavias_print_animate_aos($animate) ?>>
               <div class="row">
                  <div class="col-lg-6 col-md-6">
                     <div class="team-header">
                        <img alt="<?php print $title; ?>" src="<?php print $image; ?>" class="img-responsive"> 
                        <div class="box-hover">
                           <div class="content-inner">
                              <div class="social-list text-center">
                                 <?php if($facebook){ ?>
                                    <a href="<?php print $facebook ?>"><i class="fa fa-facebook"></i> </a>
                                 <?php } ?>
                                 <?php if($twitter){ ?>   
                                    <a href="<?php print $twitter ?>"><i class="fa fa-twitter"></i> </a>
                                 <?php } ?>
                                 <?php if($pinterest){ ?>   
                                    <a href="<?php print $pinterest ?>"><i class="fa fa-pinterest"></i> </a>
                                 <?php } ?>
                                 <?php if($google){ ?>   
                                    <a href="<?php print $google ?>"> <i class="fa fa-google"></i></a>  
                                 <?php } ?>                                             
                              </div>  
                           </div>   
                        </div>
                     </div> 
                  </div>
                  <div class="col-lg-6 col-md-6">
                     <div class="team-body">
                        <div class="team-body-content">
                           <h3 class="team-name">
                           <?php if($link){ ?>
                              <a href="<?php print $link; ?>" <?php print $target; ?> >
                           <?php } ?>   
                              <?php print $title ?>
                           <?php if($link){ ?> </a> <?php } ?>
                           </h3>
                           <p class="team-position"><?php print $job ?></p>
                        </div>  
                        <p class="team-info"><?php print $content ?></p>      
                                                 
                     </div>                            
                  </div>
               </div>
            </div>
         <?php } ?>
         
         <?php 
         //Style display vertical
         if($style=='vertical'){ ?>
            <div class="widget gsc-team team-vertical <?php print $el_class ?>">
               <div class="widget-content text-center">
                  <div class="team-header text-center">
                     <img alt="<?php print $title; ?>" src="<?php print $image ?>" class="img-responsive"> 
                     <div class="box-hover">
                        <div class="team-content"><?php print $content ?></div>     
                     </div>   
                  </div> 
                  <div class="team-body text-<?php print $content_align; ?>">
                     <div class="info">
                        <h3 class="team-name">
                           <?php if($link){ ?>
                              <a href="<?php print $link; ?>" <?php print $target; ?> >
                           <?php } ?>  
                           <?php print $title ?>
                           <?php if($link){ ?> </a> <?php } ?>
                        </h3>
                        <p class="team-position"><?php print $job ?></p>
                        <div class="social-list">
                           <?php if($facebook){ ?>
                              <a href="<?php print $facebook ?>"><i class="fa fa-facebook"></i> </a>
                           <?php } ?>
                           <?php if($twitter){ ?>   
                              <a href="<?php print $twitter ?>"><i class="fa fa-twitter"></i> </a>
                           <?php } ?>
                           <?php if($pinterest){ ?>   
                              <a href="<?php print $pinterest ?>"><i class="fa fa-pinterest"></i> </a>
                           <?php } ?>
                           <?php if($google){ ?>   
                              <a href="<?php print $google ?>"> <i class="fa fa-google"></i></a>  
                           <?php } ?>                                          
                        </div>
                     </div>
                                       
                  </div>                            
               </div>
            </div>
         <?php } ?>
         <?php 
         //circle
         if($style=='circle'){ ?>
            <div class="widget gsc-team team-vertical team-circle <?php print $el_class ?>">
               <div class="widget-content text-center">
                  <div class="team-header text-center">
                     <img alt="<?php print $title ?>" src="<?php print $image ?>" class="img-responsive"> 
                     <div class="box-hover">
                        <div class="team-content"><?php print $content ?></div>     
                     </div>   
                  </div> 
                  <div class="team-body text-<?php print $content_align; ?>">
                     <div class="info">
                        <h3 class="team-name">
                           <?php if($link){ ?>
                              <a href="<?php print $link; ?>" <?php print $target; ?> >
                           <?php } ?>  
                           <?php print $title ?>
                           <?php if($link){ ?> </a> <?php } ?>
                        </h3>
                        <p class="team-position"><?php print $job ?></p>
                        <div class="social-list">
                           <?php if($facebook){ ?>
                              <a href="<?php print $facebook ?>"><i class="fa fa-facebook"></i> </a>
                           <?php } ?>
                           <?php if($twitter){ ?>   
                              <a href="<?php print $twitter ?>"><i class="fa fa-twitter"></i> </a>
                           <?php } ?>
                           <?php if($pinterest){ ?>   
                              <a href="<?php print $pinterest ?>"><i class="fa fa-pinterest"></i> </a>
                           <?php } ?>
                           <?php if($google){ ?>   
                              <a href="<?php print $google ?>"> <i class="fa fa-google"></i></a>  
                           <?php } ?>                                          
                        </div>
                     </div>
                                       
                  </div>                            
               </div>
            </div>
         <?php } ?>

         <?php return ob_get_clean() ?>
         <?php
      }

      public function load_shortcode(){
         add_shortcode( 'our_team', array('gsc_our_team', 'sc_our_team' ));
      }
   }
endif;


