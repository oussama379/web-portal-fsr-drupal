<?php
use Drupal\gavias_blockbuilder\includes\core\gavias_sc;

function gavias_blockbuilder_frontend( $params ) {
	global $base_url, $base_path;
	$gsc = new gavias_sc();
	//$gsc->gsc_load_shortcodes(false);
	$classes = array(
		'1' => 'col-lg-1 col-md-1 col-sm-2 col-xs-12',
		'2' => 'col-lg-2 col-md-2 col-sm-4 col-xs-12',
		'3' => 'col-lg-3 col-md-3 col-sm-6 col-xs-12',
		'4' => 'col-lg-4 col-md-4 col-sm-12 col-xs-12',
		'5' => 'col-lg-5 col-md-5 col-sm-12 col-xs-12',
		'6' => 'col-lg-6 col-md-6 col-sm-12 col-xs-12',
		'7' => 'col-lg-7 col-md-7 col-sm-12 col-xs-12',
		'8' => 'col-lg-8 col-md-8 col-sm-12 col-xs-12',
		'9' => 'col-lg-9 col-md-9 col-sm-12 col-xs-12',
		'10' => 'col-lg-10 col-md-10 col-sm-12 col-xs-12',
		'11' => 'col-lg-11 col-md-11 col-sm-12 col-xs-12',
		'12' => 'col-lg-12 col-md-12 col-sm-12 col-xs-12',
	);
	$gbb_els = $params;
	$gbb_els = base64_decode($params);
	$gbb_els = json_decode($gbb_els, true);
	$content = '';
	if( is_array( $gbb_els ) ){
		// Row
		foreach( $gbb_els as $row ){
			if(isset($row['attr'])){
				$row_attr = $row['attr'];
				//Class
				$array_class 		= array();
				$array_class[]	= $row_attr['class'];
				$array_class[]   = 'gbb-row';
				
				$array_style = array();
				
				//Padding for row
				if(isset($row_attr['padding_top']) && $row_attr['padding_top']){
					$array_style[] 	= 'padding-top:'. intval( $row_attr['padding_top'] ) .'px';
				}
				if(isset($row_attr['padding_bottom']) && $row_attr['padding_bottom']){
					$array_style[] 	= 'padding-bottom:'. intval( $row_attr['padding_bottom'] ) .'px';
				}	

				//Margin for row
				if(isset($row_attr['margin_top']) && $row_attr['margin_top']){
					$array_style[] 	= 'margin-top:'. intval( $row_attr['margin_top'] ) .'px';
				}
				if(isset($row_attr['margin_bottom']) && $row_attr['margin_bottom']){
					$array_style[] 	= 'margin-bottom:'. intval( $row_attr['margin_bottom'] ) .'px';
				}	

				// Background for row
				if(isset($row_attr['bg_color']) && $row_attr['bg_color']){
					$array_style[] 	= 'background-color:'. $row_attr['bg_color'];
				}
				 $attr_parallax = "";
				if( $row_attr['bg_image'] ){
					$array_style[] 	= 'background-image:url(\''. substr($base_path, 0, -1) . $row_attr['bg_image'] .'\')';
					$array_style[] 	= 'background-repeat:' . $row_attr['bg_repeat'];
					$array_style[] 	= 'background-position:' . $row_attr['bg_position'];
					if(isset($row_attr['bg_attachment']) && $row_attr['bg_attachment']=='fixed'){
						$array_class[] = 'gva-parallax-background ';
					}
				}
				
				$row_bg_size = 'bg-size-cover';
				if(isset($row_attr['bg_size']) && $row_attr['bg_size']){
					$row_bg_size = 'bg-size-' . $row_attr['bg_size'];
				}

				$array_class[] = $row_bg_size;

				if(isset($row_attr['equal_height']) && $row_attr['equal_height']){
					$array_class[] = $row_attr['equal_height'];
				}

				$data_bg_video = "";
				if(isset($row_attr['bg_video']) && $row_attr['bg_video']){
					$array_class[] = 'youtube-bg';
					$data_bg_video ="data-property=\"{videoURL: '{$row_attr['bg_video']}',
				      containment: 'self', startAt: 0,  stopAt: 0, autoPlay: true, loop: true, mute: true, showControls: false, 
				      showYTLogo: false, realfullscreen: true, addRaster: false, optimizeDisplay: true, stopMovieOnBlur: true}\"";
				}

				$row_class = implode($array_class, ' ');
				$row_style 	= implode('; ', $array_style );
			}	

			ob_start();
		  	include GAVIAS_BLOCKBUILDER_PATH . '/templates/frontend/print-builder.php';
		  	$content .= ob_get_clean();		
		}
	}
	return $content;
}