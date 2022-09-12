<?php 
    global $theme_root, $base_url; 
    $_id = gavias_sliderlayer_makeid(10);

?>
<link rel="stylesheet" type="text/css" href="<?php print $base_url . '/' . $style_fontend ?>">

<div id="gavias_sliderlayer">
  <input type="hidden" value="<?php print $sid ?>" name="sid"/>
  <input type="hidden" value="<?php print $gid ?>" name="gid"/>
  <div>
    <ul id="slideslist" class="ui-tabs-nav ui-helper-reset hidden ui-helper-clearfix ">
    </ul>
    <div class="clearfix"></div>
    <div id="gavias_sliderlayer_main">
      <div class="g-wrapper">
       <div class="gavias-heading">Slide options</div>
        <table>
          <tr>
            <td width="33%">
              <label>Slider title</label>
              <input name="title" class="slide-option form-text" type="text" style="width: 100%;"/>
            </td>
            <td width="33%">
              <label>Status</label>
              <select name="status" class="slide-option form-select">
                  <option value="1">Enable</option>
                  <option value="0">Disable</option>
              </select>
            </td>
            <td width="33%">
              <label>Sort index</label>
              <input name="sort_index" type="number" class="slide-option form-text"/>
            </td>
          </tr>

          <tr>
            <td>
              <label>Background image</label>
              <?php print gavias_sliderlayer_field_upload_slider(); ?>
            </td>
            
            <td>
             <label>Background color(example: #f5f5f5;)</label>
              <input type="text" name="background_color" class="slide-option"/>
            </td>
            <td width="33%">
              <label>Background Position </label>
              <select name="background_position" class="slide-option form-select">
                  <option value="center top">center top</option>
                  <option value="center right">center right</option>
                  <option value="center bottom">center bottom</option>
                  <option value="center center">center center</option>
                  <option value="left top">left top</option>
                  <option value="left center">left center</option>
                  <option value="left bottom">left bottom</option>
                  <option value="right top">right top</option>
                  <option value="right center">right center</option>
                  <option value="right bottom">right bottom</option>
              </select>
            </td>
          </tr>
          <tr>
            <td width="33%">
              <label>Background Repeat</label>
              <select name="background_repeat" class="slide-option form-select">
                <option value="no-repeat">no-repeat</option>
                <option value="repeat">repeat</option>
                <option value="repeat-x">repeat-x</option>
                <option value="repeat-y">repeat-y</option>
              </select>
            </td>
            <td>
              <label>Background Fit</label>
              <select name="background_fit" class="slide-option form-select">
                <option value="cover">cover</option>
                <option value="contain">contain</option>
                <option value="normal">normal</option>
              </select>
            </td>
            <td>
              <label>Show Overlay</label>
              <select name="overlay_enable" class="slide-option form-select">
                <option value="on">on</option>
                <option value="off">off</option>
              </select>
            </td>
          </tr>

          <tr>
            <td width="33%">
              <label>Video Source</label>
              <select name="video_source" class="slide-option form-select">
                <option value="">None</option>
                <option value="youtube">Youtube</option>
                <option value="vimeo">Vimeo</option>
                <option value="html5">HTML 5</option>
              </select>
            </td>
            <td>
              <label>Video Start At</label>
              <input name="video_start_at" type="text" class="slide-option form-text"/>
            </td>
            <td>
             <label>Video End At</label>
              <input name="video_end_at" type="text" class="slide-option form-text"/>
            </td>
            
          </tr>
          
          <tr>
            <td>
              <label>Youtube Video(example: T8--OggjJKQ)</label>
              <input name="youtube_video" type="text" class="slide-option form-text"/>
              <label>Video Arguments Youtube</label>
              <input name="video_youtube_args" type="text" class="slide-option form-text"/>
            </td>
            <td>
              <label>Vimeo Video(example: 30300114)</label>
              <input name="vimeo_video" type="text" class="slide-option form-text"/>
              <label>Video Arguments Vimeo</label>
              <input name="video_vimeo_args" type="text" class="slide-option form-text"/>
            </td>
            <td>
              <label>MP4 Video Link:</label>
              <input name="html5_mp4" type="text" class="slide-option form-text"/>
              <label>MP4 Video Next Slide End:</label>
              <select name="mp4_nextslideatend" class="slide-option form-select">
                <option value="true">True</option>
                <option value="false">False</option>
              </select>
              <label>MP4 Video Loop:</label>
              <select name="mp4_videoloop" class="slide-option form-select">
                <option value="loopandnoslidestop">Loop And No Slide Stop</option>
                <option value="loop">Loop</option>
                <option value="none">None</option>
              </select>
            </td>
          </tr>

          <tr>
            <td>
              <label>Slide transition</label>
              <?php print gavias_defined_select('data_transition', gavias_sliderlayer_datatransition(),'slide-option'); ?>
            </td>
            <td>
              <label>Slide Easing In</label>
              <?php print gavias_defined_select('slide_easing_in', gavias_sliderlayer_dataeasing_slide(),'slide-option'); ?>
            </td>
            <td>
              <label>Slide Easing Out</label>
              <?php print gavias_defined_select('slide_easing_out', gavias_sliderlayer_dataeasing_slide(),'slide-option'); ?>
            </td>
          </tr>

          <tr>
            <td>
              <label>Slide Delay</label>
              <input type="text" name="delay" class="form-text slide-option">
              <label>Data Parallax</label>
              <select name="data_parallax" class="slide-option form-select">
                  <option value="1">1</option>
                  <option value="2">2</option>
                  <option value="3">3</option>
                  <option value="4">4</option>
                  <option value="5">5</option>
                  <option value="6">6</option>
                  <option value="7">7</option>
                  <option value="8">8</option>
                  <option value="9">9</option>
                  <option value="10">10</option>
              </select>
            </td>
            <td>
              <label>Scale-Start (eg: 120)</label>
              <input name="scalestart" type="text" class="slide-option form-text"/>
            </td>
            <td>
              <label>Scale-End (eg: 100)</label>
              <input name="scaleend" type="text" class="slide-option form-text"/>
            </td>
          </tr>
        </table>
      </div>
        
       <div class="clearfix">
        <?php include drupal_get_path('module', 'gavias_sliderlayer') . '/templates/backend/layers.php'; ?>
      </div>
      </div>
    </div>
  </div>
<div>
  <input type="button" id="save" class="button button-action button--primary button--small" value="Save"/>
</div>
