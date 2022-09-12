<?php
function gavias_blockbuilder_animate(){
    return array(
        ''          => '- Not Animated -',
        'fadeIn'      => 'Fade In',
        'fadeInUp'      => 'Fade In Up',
        'fadeInDown'    => 'Fade In Down ',
        'fadeInLeft'    => 'Fade In Left',
        'fadeInRight'     => 'Fade In Right ',
        'fadeInUpBig'   => 'Fade In Up Big',
        'fadeInDownBig'   => 'Fade In Down Big',
        'fadeInLeftBig'   => 'Fade In Left Big',
        'fadeInRightBig'  => 'Fade In Right Big',
        'zoomIn'      => 'Zoom In',
        'zoomInUp'      => 'Zoom In Up',
        'zoomInDown'    => 'Zoom In Down',
        'zoomInLeft'    => 'Zoom In Left',
        'zoomInRight'     => 'Zoom In Right',
        'bounceIn'      => 'Bounce In',
        'bounceInUp'    => 'Bounce In Up',
        'bounceInDown'    => 'Bounce In Down',
        'bounceInLeft'    => 'Bounce In Left',
        'bounceInRight'   => 'Bounce In Right',
    );
}

function gavias_blockbuilder_animate_aos(){
    return array(
        ''          => '- Not Animated -',
        'fade'=>'fade',
        'fade-up'=>'fade-up',
        'fade-down'=>'fade-down',
        'fade-left'=>'fade-left',
        'fade-right'=>'fade-right',
        'fade-up-right'=>'fade-up-right',
        'fade-up-left'=>'fade-up-left',
        'fade-down-right'=>'fade-down-right',
        'fade-down-left'=>'fade-down-left',
        'flip-up'=>'flip-up',
        'flip-down'=>'flip-down',
        'flip-left'=>'flip-left',
        'flip-right'=>'flip-right',
        'slide-up'=>'slide-up',
        'slide-down'=>'slide-down',
        'slide-left'=>'slide-left',
        'slide-right'=>'slide-right',
        'zoom-in'=>'zoom-in',
        'zoom-in-up'=>'zoom-in-up',
        'zoom-in-down'=>'zoom-in-down',
        'zoom-in-left'=>'zoom-in-left',
        'zoom-in-right'=>'zoom-in-right',
        'zoom-out'=>'zoom-out',
        'zoom-out-up'=>'zoom-out-up',
        'zoom-out-down'=>'zoom-out-down',
        'zoom-out-left'=>'zoom-out-left',
        'zoom-out-right'=>'zoom-out-right'
       
    );
}

function gavias_print_animate_aos($animate=''){
    if($animate){
        return 'data-aos="'.$animate.'"';
    }
}