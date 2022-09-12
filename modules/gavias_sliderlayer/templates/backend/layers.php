<?php 
  global $base_url;
  $width = '1170px'; $height = '600px';
  if( isset($group_settings_decode->gridwidth) && $group_settings_decode->gridwidth ){
    $width = $group_settings_decode->gridwidth . 'px';
  }
  if(isset($group_settings_decode->gridheight) && $group_settings_decode->gridheight){
    $height = ($group_settings_decode->gridheight) . 'px';
  }
  
  $_id = gavias_sliderlayer_makeid(10);

?>
<div id="gavias_slider_single" style="background-size: cover; margin:0 auto;width:<?php print $width ?>; height: <?php print $height ?>; border: 1px solid #ccc; list-style: none;position: relative;">
  
</div>

<div class="vertical-tabs clearfix">
  <div class="vertical-tabs-list">
  <ul id="gavias_list_layers">    
  </ul>
  <div class="clearfix"></div>
  <a href="#" id="add_layer">Add Layer</a>
  </div>
  <div class="vertical-tabs-panes vertical-tabs-processed" id="layeroptions" style="display: none">
    

  <fieldset class="form-wrapper g-wrapper">
      <div class="gavias-heading">Layer Transition Effects</div>

      <table>
        <tr>
          <td colspan="3">
            <div class="margin-tb-20 margin-bottom-50" style="float: left; width: 75%;">
              <label class="margin-bottom-20">Data Time (Start - End)</label>
              <div id="g-slider"></div>
            </div>  
            <div style="float: right; width: 20%;margin-top:55px;">
              <input name="data_time_start" id="g_data_start" class="layer-option" type="text" style="width: 40%!important;" />
              <input name="data_time_end" id="g_data_end" class="layer-option" type="text" style="width: 40%!important;" />
            </div>
          </td>
        </tr>
        <tr>
          <td width="25%"><label>Incoming Effect</label>
            <select name="incomingclasses" class="layer-option">
              <?php foreach (gaviasGetArrAnimations() as $key => $value) { ?>
                <option value="<?php print $key ?>" <?php if(isset($value['disable']) && ($value['disable']==true)) print 'disabled' ?>><?php print $value['handle']; ?></option>
              <?php } ?>
            </select>
          </td>  
          <td>
            <label>Easing</label>
            <?php print gavias_defined_select('data_easing', gavias_sliderlayer_dataeasing(),'layer-option'); ?></td>
          </td>
          <td  width="25%">
            <label>Speed Start</label>
            <input type="text" name="data_speed" class="layer-option form-text range-slider-single"/>
          </td>
        </tr>
        
        <tr>
          <td><label>Outgoing Effect</label>
            <select name="outgoingclasses" class="layer-option">
                <?php foreach (gaviasGetArrEndAnimations() as $key => $value) { ?>
                    <option value="<?php print $key ?>" <?php if(isset($value['disable']) && ($value['disable']==true)) print 'disabled' ?>><?php print $value['handle']; ?></option>
               <?php } ?>
            </select>
          </td>
          <td>
            <label>End Easing</label>
            <?php print gavias_defined_select('data_endeasing', gavias_sliderlayer_dataendeasing(),'layer-option'); ?>
          </td>
          <td width="25%">
              <div class="margin-top-30">
                <label> Speed End</label>
                <input type="text" name="data_end" class="layer-option form-text range-slider-single"/>
                </div>
          </td>
        </tr>
      </table>
    </fieldset>

  <fieldset class="fieldset-wrapper g-wrapper">
      <div class="gavias-heading">Layer Style Setting</div>
  		
      <table>
          <tr>
            <td width="50%">
              <div class="g-label">Layer style</div> 
             <select class="select-content-type layer-option" name="select_content_type">
                <option value="text">Text</option>
                <option value="image">Image</option>
              </select>
            </td>
            <td width="50%">
              <div class="g-label">Title</div> 
                <input type="text" name="title" class="form-text layer-option"/>
                <input type="hidden" class="layer-option" name="content" value=""/>
            </td>
          </tr>

          <tr>
            <td colspan="3">
              <div id="content-type">
                <div id="content-text" class="g-content-setting">
                    <table>
                      <tr>
                        <td width="50%">
                          <div class="g-label">Text style</div> 
                          <?php print gavias_defined_select('text_style', gavias_sliderlayer_captionclasses(),'layer-option'); ?>
                        </td>
                        <td width="50%">
                          <textarea class="layer-option form-textarea" name="text" id="layer-text"></textarea>
                        </td>
                      </tr>
                    </table>
                </div>

                <div id="content-image" class="g-content-setting filed-upload-layer-content" style="display: none;">
                  <?php print gavias_sliderlayer_field_upload_layer(); ?>
                  <input name="width" type="hidden" class="form-text layer-option" />
                  <input name="height" type="hidden" class="form-text layer-option" />
                </div>

              </div> 
            </td>
          </tr>
      </table>
      
    </fieldset>

    <fieldset class="fieldset-wrapper g-wrapper">
      <div class="gavias-heading">Layer Setting</div>
      <table>
        <tr>
          <td width="33%">
            <div class="g-label">Custom class</div> 
            <input type="text" name="custom_class" class="form-text layer-option" style="width:400px;"/>
          </td> 
          <?php if(gavias_sliderlayer_styles()){ 
            $styles = gavias_sliderlayer_styles(); 
          ?>
            <td>
              <div class="g-label">Styles available </div> 
              <select name="custom_style" class="layer-option">
                  <?php foreach ($styles as $key => $value) { ?>
                    <option value="<?php print $key ?>"><?php print $value; ?></option>
                 <?php } ?>
              </select>
            </td>
          <?php } ?>
        </tr>
        <tr>
          <td width="33%">
            <div class="g-label small">Top</div> 
            <input type="text" name="top" class="form-text layer-option">
          </td>
          <td width="33%">
            <div class="g-label small">Left</div> 
            <input type="text" name="left" class="form-text layer-option">
          </td>
          <td width="33%">
            <div class="g-label small">Link</div> 
            <input type="text" name="link" class="form-text layer-option">
          </td>
        </tr>
        <tr>
          <td colspan="2">
            <div class="g-label">Custom css</div> 
            <textarea name="custom_css" class="form-textarea layer-option"></textarea>
          </td>
        </tr>
      </table>
    </fieldset>
  </div>
  <div style="clear: both;"></div>
</div>