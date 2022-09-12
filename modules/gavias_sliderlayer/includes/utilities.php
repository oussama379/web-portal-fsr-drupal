<?php
function gavias_sliderlayer_datatransition(){
    return $datatransition = array(
        'random' => 'Random Flat and Premium',
        'random-static' => 'Random Flat',
        'random-premium' => 'Random Premium',
        'slideup' => 'Slide To Top',
        'slidedown' => 'Slide To Bottom',
        'slideright' => 'Slide To Right',
        'slideleft' => 'Slide To Left',
        'slidehorizontal' => 'Slide Horizontal',
        'slidevertical' => 'Slide Vertical',
        'boxslide' => 'Slide Boxes',
        'slotslide-horizontal' => 'Slide Slots Horizontal',
        'slotslide-vertical' => 'Slide Slots Vertical',
        'boxfade' => 'Fade Boxes',
        'slotfade-horizontal' => 'Fade Slots Horizontal',
        'slotfade-vertical' => 'Fade Slots Vertical',
        'fadefromright' => 'Fade and Slide from Right',
        'fadefromleft' => 'Fade and Slide from Left',
        'fadefromtop' => 'Fade and Slide from Top',
        'fadefrombottom' => 'Fade and Slide from Bottom',
        'fadetoleftfadefromright' => 'Fade To Left and Fade From Right',
        'fadetorightfadefromleft' => 'Fade To Right and Fade From Left',
        'fadetotopfadefrombottom' => 'Fade To Top and Fade From Bottom',
        'fadetobottomfadefromtop' => 'Fade To Bottom and Fade From Top',
        'parallaxtoright' => 'Parallax to Right',
        'parallaxtoleft' => 'Parallax to Left',
        'parallaxtotop' => 'Parallax to Top',
        'parallaxtobottom' => 'Parallax to Bottom',
        'scaledownfromright' => 'Zoom Out and Fade From Right',
        'scaledownfromleft' => 'Zoom Out and Fade From Left',
        'scaledownfromtop' => 'Zoom Out and Fade From Top',
        'scaledownfrombottom' => 'Zoom Out and Fade From Bottom',
        'zoomout' => 'ZoomOut',
        'zoomin' => 'ZoomIn',
        'slotzoom-horizontal' => 'Zoom Slots Horizontal',
        'slotzoom-vertical' => 'Zoom Slots Vertical',
        'fade' => 'Fade',
        'curtain-1' => 'Curtain from Left',
        'curtain-2' => 'Curtain from Right',
        'curtain-3' => 'Curtain from Middle',
        '3dcurtain-horizontal' => '3D Curtain Horizontal',
        '3dcurtain-vertical' => '3D Curtain Vertical',
        'cube' => 'Cube Vertical',
        'cube-horizontal' => 'Cube Horizontal',
        'incube' => 'In Cube Vertical',
        'incube-horizontal' => 'In Cube Horizontal',
        'turnoff' => 'TurnOff Horizontal',
        'turnoff-vertical' => 'TurnOff Vertical',
        'papercut' => 'Paper Cut',
        'flyin' => 'Fly In');
    }

function gavias_sliderlayer_dataeasing_slide(){
    return $dataeasing_slide = array(
        'default'=>'Default',
        'Linear.easeNone'=>'Linear.easeNone',
        'Power0.easeIn'=>'Power0.easeIn (linear)',
        'Power0.easeInOut'=>'Power0.easeInOut (linear)',
        'Power0.easeOut'=>'Power0.easeOut (linear)',
        'Power1.easeIn'=>'Power1.easeIn',
        'Power1.easeInOut'=>'Power1.easeInOut',
        'Power1.easeOut'=>'Power1.easeOut',
        'Power2.easeIn'=>'Power2.easeIn',
        'Power2.easeInOut'=>'Power2.easeInOut',
        'Power2.easeOut'=>'Power2.easeOut',
        'Power3.easeIn'=>'Power3.easeIn',
        'Power3.easeInOut'=>'Power3.easeInOut',
        'Power3.easeOut'=>'Power3.easeOut',
        'Power4.easeIn'=>'Power4.easeIn',
        'Power4.easeInOut'=>'Power4.easeInOut',
        'Power4.easeOut'=>'Power4.easeOut',
        'Back.easeIn'=>'Back.easeIn',
        'Back.easeInOut'=>'Back.easeInOut',
        'Back.easeOut'=>'Back.easeOut',
        'Bounce.easeIn'=>'Bounce.easeIn',
        'Bounce.easeInOut'=>'Bounce.easeInOut',
        'Bounce.easeOut'=>'Bounce.easeOut',
        'Circ.easeIn'=>'Circ.easeIn',
        'Circ.easeInOut'=>'Circ.easeInOut',
        'Circ.easeOut'=>'Circ.easeOut',
        'Elastic.easeIn'=>'Elastic.easeIn',
        'Elastic.easeInOut'=>'Elastic.easeInOut',
        'Elastic.easeOut'=>'Elastic.easeOut',
        'Expo.easeIn'=>'Expo.easeIn',
        'Expo.easeInOut'=>'Expo.easeInOut',
        'Expo.easeOut'=>'Expo.easeOut',
        'Sine.easeIn'=>'Sine.easeIn',
        'Sine.easeInOut'=>'Sine.easeInOut',
        'Sine.easeOut'=>'Sine.easeOut',
        'SlowMo.ease'=>'SlowMo.ease',
    );
}
function gavias_sliderlayer_dataeasing(){
    return array(
        'Linear.easeNone' => 'Linear.easeNone',
        'Power0.easeIn' => 'Power0.easeIn',
        'Power0.easeInOut' => 'Power0.easeInOut',
        'Power0.easeOut' => 'Power0.easeOut',
        'Power1.easeIn' => 'Power1.easeIn',
        'Power1.easeInOut' => 'Power1.easeInOut',
        'Power1.easeOut' => 'Power1.easeOut',
        'Power2.easeIn' => 'Power2.easeIn',
        'Power2.easeInOut' => 'Power2.easeInOut',
        'Power2.easeOut' => 'Power2.easeOut',
        'Power3.easeIn' => 'Power3.easeIn',
        'Power3.easeInOut' => 'Power3.easeInOut',
        'Power3.easeOut' => 'Power3.easeOut',
        'Power4.easeIn' => 'Power4.easeIn',
        'Power4.easeInOut' => 'Power4.easeInOut',
        'Power4.easeOut' => 'Power4.easeOut',
        'Quad.easeIn' => 'Quad.easeIn',
        'Quad.easeInOut' => 'Quad.easeInOut',
        'Quad.easeOut' => 'Quad.easeOut',
        'Cubic.easeIn' => 'Cubic.easeIn',
        'Cubic.easeInOut' => 'Cubic.easeInOut',
        'Cubic.easeOut' => 'Cubic.easeOut',
        'Quart.easeIn' => 'Quart.easeIn',
        'Quart.easeInOut' => 'Quart.easeInOut',
        'Quart.easeOut' => 'Quart.easeOut',
        'Quint.easeIn' => 'Quint.easeIn',
        'Quint.easeInOut' => 'Quint.easeInOut',
        'Quint.easeOut' => 'Quint.easeOut',
        'Strong.easeIn' => 'Strong.easeIn',
        'Strong.easeInOut' => 'Strong.easeInOut',
        'Strong.easeOut' => 'Strong.easeOut',
        'Back.easeIn' => 'Back.easeIn',
        'Back.easeInOut' => 'Back.easeInOut',
        'Back.easeOut' => 'Back.easeOut',
        'Bounce.easeIn' => 'Bounce.easeIn',
        'Bounce.easeInOut' => 'Bounce.easeInOut',
        'Bounce.easeOut' => 'Bounce.easeOut',
        'Circ.easeIn' => 'Circ.easeIn',
        'Circ.easeInOut' => 'Circ.easeInOut',
        'Circ.easeOut' => 'Circ.easeOut',
        'Elastic.easeIn' => 'Elastic.easeIn',
        'Elastic.easeInOut' => 'Elastic.easeInOut',
        'Elastic.easeOut' => 'Elastic.easeOut',
        'Expo.easeIn' => 'Expo.easeIn',
        'Expo.easeInOut' => 'Expo.easeInOut',
        'Expo.easeOut' => 'Expo.easeOut',
        'Sine.easeIn' => 'Sine.easeIn',
        'Sine.easeInOut' => 'Sine.easeInOut',
        'Sine.easeOut' => 'Sine.easeOut',
        'SlowMo.ease' => 'SlowMo.ease',
        'easeOutBack' => 'easeOutBack',
        'easeInQuad' => 'easeInQuad',
        'easeOutQuad' => 'easeOutQuad',
        'easeInOutQuad' => 'easeInOutQuad',
        'easeInCubic' => 'easeInCubic',
        'easeOutCubic' => 'easeOutCubic',
        'easeInOutCubic' => 'easeInOutCubic',
        'easeInQuart' => 'easeInQuart',
        'easeOutQuart' => 'easeOutQuart',
        'easeInOutQuart' => 'easeInOutQuart',
        'easeInQuint' => 'easeInQuint',
        'easeOutQuint' => 'easeOutQuint',
        'easeInOutQuint' => 'easeInOutQuint',
        'easeInSine' => 'easeInSine',
        'easeOutSine' => 'easeOutSine',
        'easeInOutSine' => 'easeInOutSine',
        'easeInExpo' => 'easeInExpo',
        'easeOutExpo' => 'easeOutExpo',
        'easeInOutExpo' => 'easeInOutExpo',
        'easeInCirc' => 'easeInCirc',
        'easeOutCirc' => 'easeOutCirc',
        'easeInOutCirc' => 'easeInOutCirc',
        'easeInElastic' => 'easeInElastic',
        'easeOutElastic' => 'easeOutElastic',
        'easeInOutElastic' => 'easeInOutElastic',
        'easeInBack' => 'easeInBack',
        'easeOutBack' => 'easeOutBack',
        'easeInOutBack' => 'easeInOutBack',
        'easeInBounce' => 'easeInBounce',
        'easeOutBounce' => 'easeOutBounce',
        'easeInOutBounce' => 'easeInOutBounce'
    );
}

function gavias_sliderlayer_dataendeasing(){
    return $dataendeasing = array(
        '' => '-None-',
        'Linear.easeNone' => 'Linear.easeNone',
        
        'Power0.easeIn' => 'Power0.easeIn',
        'Power0.easeInOut' => 'Power0.easeInOut',
        'Power0.easeOut' => 'Power0.easeOut',
        'Power1.easeIn' => 'Power1.easeIn',
        'Power1.easeInOut' => 'Power1.easeInOut',
        'Power1.easeOut' => 'Power1.easeOut',
        'Power2.easeIn' => 'Power2.easeIn',
        'Power2.easeInOut' => 'Power2.easeInOut',
        'Power2.easeOut' => 'Power2.easeOut',
        'Power3.easeIn' => 'Power3.easeIn',
        'Power3.easeInOut' => 'Power3.easeInOut',
        'Power3.easeOut' => 'Power3.easeOut',
        'Power4.easeIn' => 'Power4.easeIn',
        'Power4.easeInOut' => 'Power4.easeInOut',
        'Power4.easeOut' => 'Power4.easeOut',
        'Quad.easeIn' => 'Quad.easeIn',
        'Quad.easeInOut' => 'Quad.easeInOut',
        'Quad.easeOut' => 'Quad.easeOut',
        'Cubic.easeIn' => 'Cubic.easeIn',
        'Cubic.easeInOut' => 'Cubic.easeInOut',
        'Cubic.easeOut' => 'Cubic.easeOut',
        'Quart.easeIn' => 'Quart.easeIn',
        'Quart.easeInOut' => 'Quart.easeInOut',
        'Quart.easeOut' => 'Quart.easeOut',
        'Quint.easeIn' => 'Quint.easeIn',
        'Quint.easeInOut' => 'Quint.easeInOut',
        'Quint.easeOut' => 'Quint.easeOut',
        'Strong.easeIn' => 'Strong.easeIn',
        'Strong.easeInOut' => 'Strong.easeInOut',
        'Strong.easeOut' => 'Strong.easeOut',
        'Back.easeIn' => 'Back.easeIn',
        'Back.easeInOut' => 'Back.easeInOut',
        'Back.easeOut' => 'Back.easeOut',
        'Bounce.easeIn' => 'Bounce.easeIn',
        'Bounce.easeInOut' => 'Bounce.easeInOut',
        'Bounce.easeOut' => 'Bounce.easeOut',
        'Circ.easeIn' => 'Circ.easeIn',
        'Circ.easeInOut' => 'Circ.easeInOut',
        'Circ.easeOut' => 'Circ.easeOut',
        'Elastic.easeIn' => 'Elastic.easeIn',
        'Elastic.easeInOut' => 'Elastic.easeInOut',
        'Elastic.easeOut' => 'Elastic.easeOut',
        'Expo.easeIn' => 'Expo.easeIn',
        'Expo.easeInOut' => 'Expo.easeInOut',
        'Expo.easeOut' => 'Expo.easeOut',
        'Sine.easeIn' => 'Sine.easeIn',
        'Sine.easeInOut' => 'Sine.easeInOut',
        'Sine.easeOut' => 'Sine.easeOut',
        'SlowMo.ease' => 'SlowMo.ease',
        'easeOutBack' => 'easeOutBack',
        'easeInQuad' => 'easeInQuad',
        'easeOutQuad' => 'easeOutQuad',
        'easeInOutQuad' => 'easeInOutQuad',
        'easeInCubic' => 'easeInCubic',
        'easeOutCubic' => 'easeOutCubic',
        'easeInOutCubic' => 'easeInOutCubic',
        'easeInQuart' => 'easeInQuart',
        'easeOutQuart' => 'easeOutQuart',
        'easeInOutQuart' => 'easeInOutQuart',
        'easeInQuint' => 'easeInQuint',
        'easeOutQuint' => 'easeOutQuint',
        'easeInOutQuint' => 'easeInOutQuint',
        'easeInSine' => 'easeInSine',
        'easeOutSine' => 'easeOutSine',
        'easeInOutSine' => 'easeInOutSine',
        'easeInExpo' => 'easeInExpo',
        'easeOutExpo' => 'easeOutExpo',
        'easeInOutExpo' => 'easeInOutExpo',
        'easeInCirc' => 'easeInCirc',
        'easeOutCirc' => 'easeOutCirc',
        'easeInOutCirc' => 'easeInOutCirc',
        'easeInElastic' => 'easeInElastic',
        'easeOutElastic' => 'easeOutElastic',
        'easeInOutElastic' => 'easeInOutElastic',
        'easeInBack' => 'easeInBack',
        'easeOutBack' => 'easeOutBack',
        'easeInOutBack' => 'easeInOutBack',
        'easeInBounce' => 'easeInBounce',
        'easeOutBounce' => 'easeOutBounce',
        'easeInOutBounce' => 'easeInOutBounce'
    );
}

$linktaget = array('_self' => '_self', '_blank' => '_blank');

function gavias_sliderlayer_incomingclasse(){
    return $incomingclasse = array(
        'sft' => 'Short from Top',
        'sfb' => 'Short from Bottom',
        'sfr' => 'Short from Right',
        'sfl' => 'Short from Left',
        'lft' => 'Long from Top',
        'lfb' => 'Long from Bottom',
        'lfr' => 'Long from Right',
        'lfl' => 'Long from Left',
        'skewfromleft' => 'Skew from Left',
        'skewfromright' => 'Skew from Right',
        'skewfromleftshort' => 'Skew Short from Left',
        'skewfromrightshort' => 'Skew Short from Right',
        'fade' => 'fading',
        'randomrotate' => 'Fade in, Rotate from a Random position and Degree',
        'customin' => 'Custom Incoming Animation - see below all data settings',
    );
}    

function gavias_sliderlayer_outgoingclasses(){
    return $outgoingclasses = array(
        '' => '-None-',
        'stt' => 'Short to Top',
        'stb' => 'Short to Bottom',
        'str' => 'Short to Right',
        'stl' => 'Short to Left',
        'ltt' => 'Long to Top',
        'ltb' => 'Long to Bottom',
        'ltr' => 'Long to Right',
        'ltl' => 'Long to Left',
        'skewtoleft' => 'Skew to Left',
        'skewtoright' => 'Skew to Right',
        'skewtoleftshort' => 'Skew Short to Left',
        'skewtorightshort' => 'Skew Short to Right',
        'fadeout' => 'fading',
        'randomrotateout' => 'Fade in, Rotate from a Random position and Degree',
        'customout' => 'Custom Outgoing Animation - see below all data settings',
    );
}    

function gavias_sliderlayer_captionclasses(){
    return $captionclasses = array(
        'whitedivider3px' => 'whitedivider3px',
        'finewide_large_white' => 'finewide_large_white',
        'whitedivider3px' => 'whitedivider3px',
        'finewide_medium_white' => 'finewide_medium_white',
        'boldwide_small_white' => 'boldwide_small_white',
        'whitedivider3px_vertical' => 'whitedivider3px_vertical',
        'finewide_small_white' => 'finewide_small_white',
        'finewide_verysmall_white_mw' => 'finewide_verysmall_white_mw',
        'lightgrey_divider' => 'lightgrey_divider',
        'finewide_large_white' => 'finewide_large_white',
        'finewide_medium_white' => 'finewide_medium_white',
        'huge_red' => 'huge_red',
        'middle_yellow' => 'middle_yellow',
        'huge_thin_yellow' => 'huge_thin_yellow',
        'big_dark' => 'big_dark',
        'medium_dark' => 'medium_dark',
        'medium_grey' => 'medium_grey',
        'small_text' => 'small_text',
        'medium_text' => 'medium_text',
        'large_bold_white_25' => 'large_bold_white_25',
        'medium_text_shadow' => 'medium_text_shadow',
        'large_text' => 'large_text',
        'medium_bold_grey' => 'medium_bold_grey',
        'very_large_text' => 'very_large_text',
        'very_big_white' => 'very_big_white',
        'very_big_black' => 'very_big_black',
        'modern_medium_fat' => 'modern_medium_fat',
        'modern_medium_fat_white' => 'modern_medium_fat_white',
        'modern_medium_light' => 'modern_medium_light',
        'modern_big_bluebg' => 'modern_big_bluebg',
        'modern_big_redbg' => 'modern_big_redbg',
        'modern_small_text_dark' => 'modern_small_text_dark',
        'boxshadow' => 'boxshadow',
        'black' => 'black',
        'noshadow' => 'noshadow',
        'thinheadline_dark' => 'thinheadline_dark',
        'thintext_dark' => 'thintext_dark',
        'smoothcircle' => 'smoothcircle',
        'largeblackbg' => 'largeblackbg',
        'largepinkbg' => 'largepinkbg',
        'largewhitebg' => 'largewhitebg',
        'largegreenbg' => 'largegreenbg',
        'excerpt' => 'excerpt',
        'large_bold_grey' => 'large_bold_grey',
        'medium_thin_grey' => 'medium_thin_grey',
        'small_thin_grey' => 'small_thin_grey',
        'lightgrey_divider' => 'lightgrey_divider',
        'large_bold_darkblue' => 'large_bold_darkblue',
        'medium_bg_darkblue' => 'medium_bg_darkblue',
        'medium_bold_red' => 'medium_bold_red',
        'medium_light_red' => 'medium_light_red',
        'medium_bg_red' => 'medium_bg_red',
        'medium_bold_orange' => 'medium_bold_orange',
        'medium_bg_orange' => 'medium_bg_orange',
        'grassfloor' => 'grassfloor',
        'large_bold_white' => 'large_bold_white',
        'medium_light_white' => 'medium_light_white',
        'mediumlarge_light_white' => 'mediumlarge_light_white',
        'mediumlarge_light_white_center' => 'mediumlarge_light_white_center',
        'medium_bg_asbestos' => 'medium_bg_asbestos',
        'medium_light_black' => 'medium_light_black',
        'large_bold_black' => 'large_bold_black',
        'mediumlarge_light_darkblue' => 'mediumlarge_light_darkblue',
        'small_light_white' => 'small_light_white',
        'roundedimage' => 'roundedimage',
        'large_bg_black' => 'large_bg_black',
        'mediumwhitebg' => 'mediumwhitebg',
        'medium_bg_orange_new1' => 'medium_bg_orange_new1',
        'boxshadow' => 'boxshadow',
        'black' => 'black',
        'noshadow' => 'noshadow',
        'frontcorner' => 'frontcorner',
        'backcorner' => 'backcorner',
        'frontcornertop' => 'frontcornertop',
        'backcornertop' => 'backcornertop',
        'large_bolder_white' => 'large_bolder_white',
        'text' => 'text',
    );
}

function parseCustomAnimationByArray($animArray){
    $retString = '';
    if(isset($animArray['movex']) && $animArray['movex'] !== '') $retString.= 'x:'.$animArray['movex'].';';
    if(isset($animArray['movey']) && $animArray['movey'] !== '') $retString.= 'y:'.$animArray['movey'].';';
    if(isset($animArray['movez']) && $animArray['movez'] !== '') $retString.= 'z:'.$animArray['movez'].';';

    if(isset($animArray['rotationx']) && $animArray['rotationx'] !== '') $retString.= 'rotationX:'.$animArray['rotationx'].';';
    if(isset($animArray['rotationy']) && $animArray['rotationy'] !== '') $retString.= 'rotationY:'.$animArray['rotationy'].';';
    if(isset($animArray['rotationz']) && $animArray['rotationz'] !== '') $retString.= 'rotationZ:'.$animArray['rotationz'].';';

    if(isset($animArray['scalex']) && $animArray['scalex'] !== ''){
        $retString.= 'scaleX:';
        $retString.= (intval($animArray['scalex']) == 0) ? 0 : $animArray['scalex'];
        $retString.= ';';
    }
    if(isset($animArray['scaley']) && $animArray['scaley'] !== ''){
        $retString.= 'scaleY:';
        $retString.= (intval($animArray['scaley']) == 0) ? 0 : $animArray['scaley'];
        $retString.= ';';
    }

    if(isset($animArray['skewx']) && $animArray['skewx'] !== '') $retString.= 'skewX:'.$animArray['skewx'].';';
    if(isset($animArray['skewy']) && $animArray['skewy'] !== '') $retString.= 'skewY:'.$animArray['skewy'].';';

    if(isset($animArray['captionopacity']) && $animArray['captionopacity'] !== ''){
        $retString.= 'opacity:';
        $retString.= (intval($animArray['captionopacity']) == 0) ? 0 : $animArray['captionopacity'] / 100;
        $retString.= ';';
    }

    if(isset($animArray['captionperspective']) && $animArray['captionperspective'] !== '') $retString.= 'transformPerspective:'.$animArray['captionperspective'].';';

    if(isset($animArray['originx']) && $animArray['originx'] !== '' && isset($animArray['originy']) && $animArray['originy'] !== ''){
        $retString.= "transformOrigin:".$animArray['originx'].'% '.$animArray['originy']."%;";
    }

    return $retString;
}

function gaviasGetArrAnimations(){
    $arrAnimations = array();
    $arrAnimations['disable-1'] = array('handle' => '-----------------------------------', 'disable'=>true);
    $arrAnimations['disable-2'] = array('handle' => '- VERSION 5.0 ANIMATIONS -', 'disable'=>true);
    $arrAnimations['disable-3'] = array('handle' => '-----------------------------------', 'disable'=>true);
    $arrAnimations['LettersFlyInFromBottom'] = array('handle' => 'LettersFlyInFromBottom','params' => '{"movex":"inherit","movey":"[100%]","movez":"0","rotationx":"inherit","rotationy":"inherit","rotationz":"-35deg","scalex":"1","scaley":"1","skewx":"0","skewy":"0","captionopacity":"inherit","mask":"true","mask_x":"0px","mask_y":"0px","easing":"Power4.easeInOut","speed":"2000","split":"chars","splitdelay":"5"}', 'settings' => array('version' => '5.0'));
    $arrAnimations['LettersFlyInFromLeft'] = array('handle' => 'LettersFlyInFromLeft','params' => '{"movex":"[-105%]","movey":"inherit","movez":"0","rotationx":"0deg","rotationy":"0deg","rotationz":"-90deg","scalex":"1","scaley":"1","skewx":"0","skewy":"0","captionopacity":"inherit","mask":"true","mask_x":"0px","mask_y":"0px","easing":"Power4.easeInOut","speed":"2000","split":"chars","splitdelay":"10"}', 'settings' => array('version' => '5.0'));
    $arrAnimations['LettersFlyInFromRight'] = array('handle' => 'LettersFlyInFromRight','params' => '{"movex":"[105%]","movey":"inherit","movez":"0","rotationx":"45deg","rotationy":"0deg","rotationz":"90deg","scalex":"1","scaley":"1","skewx":"0","skewy":"0","captionopacity":"inherit","mask":"true","mask_x":"0px","mask_y":"0px","easing":"Power4.easeInOut","speed":"2000","split":"chars","splitdelay":"5"}', 'settings' => array('version' => '5.0'));
    $arrAnimations['LettersFlyInFromTop'] = array('handle' => 'LettersFlyInFromTop','params' => '{"movex":"inherit","movey":"[-100%]","movez":"0","rotationx":"inherit","rotationy":"inherit","rotationz":"35deg","scalex":"1","scaley":"1","skewx":"0","skewy":"0","captionopacity":"inherit","mask":"true","mask_x":"0px","mask_y":"0px","easing":"Power4.easeInOut","speed":"2000","split":"chars","splitdelay":"5"}', 'settings' => array('version' => '5.0'));
    $arrAnimations['MaskedZoomOut'] = array('handle' => 'MaskedZoomOut','params' => '{"movex":"inherit","movey":"inherit","movez":"0","rotationx":"0deg","rotationy":"0","rotationz":"0","scalex":"2","scaley":"2","skewx":"0","skewy":"0","captionopacity":"0","mask":"true","mask_x":"0px","mask_y":"0px","easing":"Power2.easeOut","speed":"1000","split":"none","splitdelay":"10"}', 'settings' => array('version' => '5.0'));
    $arrAnimations['PopUpSmooth'] = array('handle' => 'PopUpSmooth','params' => '{"movex":"inherit","movey":"inherit","movez":"0","rotationx":"0","rotationy":"0","rotationz":"0","scalex":"0.9","scaley":"0.9","skewx":"0","skewy":"0","captionopacity":"0","mask":"false","mask_x":"0px","mask_y":"top","easing":"Power3.easeInOut","speed":"1500","split":"none","splitdelay":"10"}', 'settings' => array('version' => '5.0'));
    $arrAnimations['RotateInFromBottom'] = array('handle' => 'RotateInFromBottom','params' => '{"movex":"inherit","movey":"bottom","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"90deg","scalex":"2","scaley":"2","skewx":"inherit","skewy":"inherit","captionopacity":"inherit","mask":"false","mask_x":"0","mask_y":"0","easing":"Power3.easeInOut","speed":"1500","split":"none","splitdelay":"10"}', 'settings' => array('version' => '5.0'));
    $arrAnimations['RotateInFormZero'] = array('handle' => 'RotateInFormZero','params' => '{"movex":"inherit","movey":"bottom","movez":"inherit","rotationx":"-20deg","rotationy":"-20deg","rotationz":"0deg","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"inherit","mask":"false","mask_x":"0","mask_y":"0","easing":"Power3.easeOut","speed":"1500","split":"none","splitdelay":"10"}', 'settings' => array('version' => '5.0'));
    $arrAnimations['SlideMaskFromBottom'] = array('handle' => 'SlideMaskFromBottom','params' => '{"movex":"inherit","movey":"[100%]","movez":"0","rotationx":"0deg","rotationy":"0","rotationz":"0","scalex":"1","scaley":"1","skewx":"0","skewy":"0","captionopacity":"0","mask":"true","mask_x":"0px","mask_y":"[100%]","easing":"Power2.easeInOut","speed":"2000","split":"none","splitdelay":"5"}', 'settings' => array('version' => '5.0'));
    $arrAnimations['SlideMaskFromLeft'] = array('handle' => 'SlideMaskFromLeft','params' => '{"movex":"[-100%]","movey":"inherit","movez":"0","rotationx":"0deg","rotationy":"0","rotationz":"0","scalex":"1","scaley":"1","skewx":"0","skewy":"0","captionopacity":"inherit","mask":"true","mask_x":"0px","mask_y":"0px","easing":"Power3.easeInOut","speed":"1500","split":"none","splitdelay":"10"}', 'settings' => array('version' => '5.0'));
    $arrAnimations['SlideMaskFromRight'] = array('handle' => 'SlideMaskFromRight','params' => '{"movex":"[100%]","movey":"inherit","movez":"0","rotationx":"0deg","rotationy":"0","rotationz":"0","scalex":"1","scaley":"1","skewx":"0","skewy":"0","captionopacity":"inherit","mask":"true","mask_x":"0px","mask_y":"0px","easing":"Power3.easeInOut","speed":"1500","split":"none","splitdelay":"10"}', 'settings' => array('version' => '5.0'));
    $arrAnimations['SlideMaskFromTop'] = array('handle' => 'SlideMaskFromTop','params' => '{"movex":"inherit","movey":"[-100%]","movez":"0","rotationx":"0deg","rotationy":"0","rotationz":"0","scalex":"1","scaley":"1","skewx":"0","skewy":"0","captionopacity":"inherit","mask":"true","mask_x":"0px","mask_y":"0px","easing":"Power3.easeInOut","speed":"1500","split":"none","splitdelay":"10"}', 'settings' => array('version' => '5.0'));
    $arrAnimations['SmoothPopUp_One'] = array('handle' => 'SmoothPopUp_One','params' => '{"movex":"inherit","movey":"inherit","movez":"0","rotationx":"0","rotationy":"0","rotationz":"0","scalex":"0.8","scaley":"0.8","skewx":"0","skewy":"0","captionopacity":"0","mask":"false","mask_x":"0px","mask_y":"top","easing":"Power4.easeOut","speed":"1500","split":"none","splitdelay":"10"}', 'settings' => array('version' => '5.0'));
    $arrAnimations['SmoothPopUp_Two'] = array('handle' => 'SmoothPopUp_Two','params' => '{"movex":"inherit","movey":"inherit","movez":"0","rotationx":"0","rotationy":"0","rotationz":"0","scalex":"0.9","scaley":"0.9","skewx":"0","skewy":"0","captionopacity":"0","mask":"false","mask_x":"0px","mask_y":"top","easing":"Power2.easeOut","speed":"1000","split":"none","splitdelay":"10"}', 'settings' => array('version' => '5.0'));
    $arrAnimations['SmoothMaskFromRight'] = array('handle' => 'SmoothMaskFromRight','params' => '{"movex":"[-175%]","movey":"0px","movez":"0","rotationx":"0","rotationy":"0","rotationz":"0","scalex":"1","scaley":"1","skewx":"0","skewy":"0","captionopacity":"1","mask":"true","mask_x":"[100%]","mask_y":"0","easing":"Power3.easeOut","speed":"1500","split":"none","splitdelay":"10"}', 'settings' => array('version' => '5.0'));
    $arrAnimations['SmoothMaskFromLeft'] = array('handle' => 'SmoothMaskFromLeft','params' => '{"movex":"[175%]","movey":"0px","movez":"0","rotationx":"0","rotationy":"0","rotationz":"0","scalex":"1","scaley":"1","skewx":"0","skewy":"0","captionopacity":"1","mask":"true","mask_x":"[-100%]","mask_y":"0","easing":"Power3.easeOut","speed":"1500","split":"none","splitdelay":"10"}', 'settings' => array('version' => '5.0'));
    $arrAnimations['SmoothSlideFromBottom'] = array('handle' => 'SmoothSlideFromBottom','params' => '{"movex":"inherit","movey":"[100%]","movez":"0","rotationx":"0deg","rotationy":"0","rotationz":"0","scalex":"1","scaley":"1","skewx":"0","skewy":"0","captionopacity":"0","mask":"false","mask_x":"0px","mask_y":"[100%]","easing":"Power4.easeInOut","speed":"2000","split":"none","splitdelay":"5"}', 'settings' => array('version' => '5.0'));

    $arrAnimations['disable-4'] = array('handle' => '-----------------------------------', 'disable'=>true);
    $arrAnimations['disable-5'] = array('handle' => '- VERSION 4.0 ANIMATIONS -', 'disable'=>true);
    $arrAnimations['disable-6'] = array('handle' => '-----------------------------------', 'disable'=>true);       
    $arrAnimations['noanim'] = array('handle' => 'No-Animation','params' => '{"movex":"inherit","movey":"inherit","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"inherit","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
    $arrAnimations['tp-fade'] = array('handle' => 'Fade-In','params' => '{"movex":"inherit","movey":"inherit","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"0","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"0"}', 'settings' => array('version' => '5.0'));
    $arrAnimations['sft'] = array('handle' => 'Short-from-Top','params' => '{"movex":"inherit","movey":"-50px","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"0","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
    $arrAnimations['sfb'] = array('handle' => 'Short-from-Bottom','params' => '{"movex":"inherit","movey":"50px","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"0","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
    $arrAnimations['sfl'] = array('handle' => 'Short-From-Left','params' => '{"movex":"-50px","movey":"inherit","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"0","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
    $arrAnimations['sfr'] = array('handle' => 'Short-From-Right','params' => '{"movex":"50px","movey":"inherit","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"0","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
    $arrAnimations['lfr'] = array('handle' => 'Long-From-Right','params' => '{"movex":"right","movey":"inherit","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"inherit","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
    $arrAnimations['lfl'] = array('handle' => 'Long-From-Left','params' => '{"movex":"left","movey":"inherit","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"inherit","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
    $arrAnimations['lft'] = array('handle' => 'Long-From-Top','params' => '{"movex":"inherit","movey":"top","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"inherit","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
    $arrAnimations['lfb'] = array('handle' => 'Long-From-Bottom','params' => '{"movex":"inherit","movey":"bottom","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"inherit","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
    $arrAnimations['skewfromleft'] = array('handle' => 'Skew-From-Long-Left','params' => '{"movex":"left","movey":"inherit","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"45px","skewy":"inherit","captionopacity":"inherit","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
    $arrAnimations['skewfromright'] = array('handle' => 'Skew-From-Long-Right','params' => '{"movex":"right","movey":"inherit","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"-85px","skewy":"inherit","captionopacity":"inherit","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
    $arrAnimations['skewfromleftshort'] = array('handle' => 'Skew-From-Short-Left','params' => '{"movex":"-200px","movey":"inherit","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"85px","skewy":"inherit","captionopacity":"0","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
    $arrAnimations['skewfromrightshort'] = array('handle' => 'Skew-From-Short-Right','params' => '{"movex":"200px","movey":"inherit","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"-85px","skewy":"inherit","captionopacity":"0","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
    $arrAnimations['randomrotate'] = array('handle' => 'Random-Rotate-and-Scale','params' => '{"movex":"{-250,250}","movey":"{-150,150}","movez":"inherit","rotationx":"{-90,90}","rotationy":"{-90,90}","rotationz":"{-360,360}","scalex":"{0,1}","scaley":"{0,1}","skewx":"inherit","skewy":"inherit","captionopacity":"0","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
    foreach($arrAnimations as $key => $value){
        if(!isset($value['params'])) continue;
        $t = json_decode(str_replace("'", '"', $value['params']), true);
        if(!empty($t))
            $arrAnimations[$key]['params'] = $t;
    }
    return $arrAnimations;
}

 function gaviasGetArrEndAnimations(){
        $arrAnimations = array();
        $arrAnimations['disable-1'] = array('handle' => '-----------------------------------', 'disable'=>true);
        $arrAnimations['disable-2'] = array('handle' => '- VERSION 5.0 ANIMATIONS -', 'disable'=>true);
        $arrAnimations['disable-3'] = array('handle' => '-----------------------------------', 'disable'=>true);

        $arrAnimations['BounceOut'] = array('handle' => 'BounceOut','params' => '{"movex":"inherit","movey":"inherit","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"0deg","scalex":"0.7","scaley":"0.7","skewx":"inherit","skewy":"inherit","captionopacity":"0","mask":"true","mask_x":"0","mask_y":"0","easing":"Back.easeIn","speed":"500","split":"none","splitdelay":"10"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['Fade-Out-Long'] = array('handle' => 'Fade-Out-Long','params' => '{"movex":"inherit","movey":"inherit","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"0","mask":"false","mask_x":"0","mask_y":"0","easing":"Power2.easeIn","speed":"1000","split":"none","splitdelay":"10"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['SlideMaskToBottom'] = array('handle' => 'SlideMaskToBottom','params' => '{"movex":"inherit","movey":"[100%]","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"inherit","mask":"true","mask_x":"inherit","mask_y":"inherit","easing":"nothing","speed":"300","split":"none","splitdelay":"10"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['SlideMaskToLeft'] = array('handle' => 'SlideMaskToLeft','params' => '{"movex":"[-100%]","movey":"inherit","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"inherit","mask":"true","mask_x":"inherit","mask_y":"inherit","easing":"Power3.easeInOut","speed":"1000","split":"none","splitdelay":"10"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['SlideMaskToRight'] = array('handle' => 'SlideMaskToRight','params' => '{"movex":"[100%]","movey":"inherit","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"inherit","mask":"true","mask_x":"inherit","mask_y":"inherit","easing":"Power3.easeInOut","speed":"1000","split":"none","splitdelay":"10"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['SlideMaskToTop'] = array('handle' => 'SlideMaskToTop','params' => '{"movex":"inherit","movey":"[-100%]","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"inherit","mask":"true","mask_x":"inherit","mask_y":"inherit","easing":"nothing","speed":"300","split":"none","splitdelay":"10"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['SlurpOut'] = array('handle' => 'SlurpOut','params' => '{"movex":"inherit","movey":"[100%]","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"0deg","scalex":"0.7","scaley":"0.7","skewx":"inherit","skewy":"inherit","captionopacity":"inherit","mask":"true","mask_x":"0","mask_y":"0","easing":"Power3.easeInOut","speed":"1000","split":"none","splitdelay":"10"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['SmoothCropToBottom'] = array('handle' => 'SmoothCropToBottom','params' => '{"movex":"inherit","movey":"[175%]","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"inherit","mask":"true","mask_x":"inherit","mask_y":"inherit","easing":"Power2.easeInOut","speed":"1000","split":"none","splitdelay":"10"}', 'settings' => array('version' => '5.0'));
 
        $arrAnimations['disable-4'] = array('handle' => '-----------------------------------', 'disable'=>true);
        $arrAnimations['disable-5'] = array('handle' => '- VERSION 4.0 ANIMATIONS -', 'disable'=>true);
        $arrAnimations['disable-6'] = array('handle' => '-----------------------------------', 'disable'=>true);
        $arrAnimations['noanimout'] = array('handle' => 'No-Out-Animation','params' => '{"movex":"inherit","movey":"inherit","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"inherit","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['fadeout'] = array('handle' => 'Fade-Out','params' => '{"movex":"inherit","movey":"inherit","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"0","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"0"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['stt'] = array('handle' => 'Short-To-Top','params' => '{"movex":"inherit","movey":"-50px","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"0","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['stb'] = array('handle' => 'Short-To-Bottom','params' => '{"movex":"inherit","movey":"50px","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"0","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['stl'] = array('handle' => 'Short-To-Left','params' => '{"movex":"-50px","movey":"inherit","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"0","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['str'] = array('handle' => 'Short-To-Right','params' => '{"movex":"50px","movey":"inherit","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"0","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['ltr'] = array('handle' => 'Long-To-Right','params' => '{"movex":"right","movey":"inherit","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"inherit","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['ltl'] = array('handle' => 'Long-To-Left','params' => '{"movex":"left","movey":"inherit","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"inherit","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['ltt'] = array('handle' => 'Long-To-Top','params' => '{"movex":"inherit","movey":"top","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"inherit","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['ltb'] = array('handle' => 'Long-To-Bottom','params' => '{"movex":"inherit","movey":"bottom","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"inherit","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['skewtoleft'] = array('handle' => 'Skew-To-Long-Left','params' => '{"movex":"left","movey":"inherit","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"45px","skewy":"inherit","captionopacity":"inherit","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['skewtoright'] = array('handle' => 'Skew-To-Long-Right','params' => '{"movex":"right","movey":"inherit","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"-85px","skewy":"inherit","captionopacity":"inherit","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['skewtorightshort'] = array('handle' => 'Skew-To-Short-Right','params' => '{"movex":"200px","movey":"inherit","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"-85px","skewy":"inherit","captionopacity":"0","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['skewtoleftshort'] = array('handle' => 'Skew-To-Short-Left','params' => '{"movex":"-200px","movey":"inherit","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"85px","skewy":"inherit","captionopacity":"0","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['randomrotateout'] = array('handle' => 'Random-Rotate-Out','params' => '{"movex":"{-250,250}","movey":"{-150,150}","movez":"inherit","rotationx":"{-90,90}","rotationy":"{-90,90}","rotationz":"{-360,360}","scalex":"{0,1}","scaley":"{0,1}","skewx":"inherit","skewy":"inherit","captionopacity":"0","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
        foreach($arrAnimations as $key => $value){
            if(!isset($value['params'])) continue;
            
            $t = json_decode(str_replace("'", '"', $value['params']), true);
            if(!empty($t))
                $arrAnimations[$key]['params'] = $t;
        }
        return $arrAnimations;
    }    


function gavias_defined_select($name, $options, $class = null) {
  $html = '<select name="' . $name . '" class="form-select ' . $class . '">';
  foreach ($options as $key => $option) {
    $html .= '<option value="' . $key . '">' . $option . '</option>';
  }
  $html .= '</select>';
  return $html;
}

function gavias_sliderlayer_field_upload_slider(){
    global $base_url;
    $_id = gavias_sliderlayer_makeid(10);
    ob_start();
    ?> 
    <div class="gva-upload-image" id="gva-upload-<?php print $_id; ?>">
        <form class="upload" id="upload-<?php print $_id; ?>" method="post" action="<?php print ($base_url . '/admin/structure/gavias_sliderlayer/upload') ?>" enctype="multipart/form-data">
            <div class="drop">
                <input type="file" name="upl" multiple class="input-file-upload"/>
            </div>
        </form>
        <input readonly="true" type="text" name="background_image_uri" value="" class="slide-option file-input" />
        <span class="loading">Loading....</span>
        <a class="button button-action button--primary button--small btn-get-images-upload">Choose image</a>
        <div class="clearfix"></div>

        <div class="clearfix"></div>
        <div class="gavias-box-images">
            <div class="gavias-box-images-inner">
                <div class="header">
                    Images Upload
                    <a class="close">close</a>
                </div>
                <div class="list-images">

                </div>
            </div>
        </div>
    </div>  
        
    <?php
    $content = ob_get_clean(); 
    return $content;
}


function gavias_sliderlayer_field_upload_layer(){
    global $base_url;
    $_id = gavias_sliderlayer_makeid(10);
    ob_start();
    ?> 
    <div class="gva-upload-image gva-upload-image-layer" id="gva-upload-<?php print $_id; ?>">
        <form class="upload-image-layer" id="upload-<?php print $_id; ?>" method="post" action="<?php print ($base_url . '/admin/structure/gavias_sliderlayer/upload') ?>" enctype="multipart/form-data">
            <div class="drop">
                <input type="file" name="upl" multiple class="input-file-upload"/>
            </div>
        </form>
        <input readonly="true" type="text" name="image_uri" id="g-image-layer" value="" class="layer-option file-input" />
        <span class="loading">Loading....</span>
        <a class="button button-action button--primary button--small btn-get-images-upload-layer">Choose image</a>
        <div class="clearfix"></div>

        <div class="clearfix"></div>
        <div class="gavias-box-images">
            <div class="gavias-box-images-inner">
                <div class="header">
                    Images Upload
                    <a class="close">close</a>
                </div>
                <div class="list-images">

                </div>
            </div>
        </div>
    </div>  
        
    <?php
    $content = ob_get_clean(); 
    return $content;
}