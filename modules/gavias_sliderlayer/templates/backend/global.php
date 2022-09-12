<div class="gavias-sliderlayer-groupsetting">
  <fieldset class="form-wrapper g-wrapper">
    <div class="gavias-heading">Global Settings</div>
    <input type="hidden" name="gid" value="<?php print $gid; ?>" />
    <div class="form-global-setting-item">
      <label>Delay</label>
      <input type="number" name="delay" class="form-text slidergroup-settings"/>
      <div class="description">The time one slide stays on the screen in Milliseconds. Global Setting. You can set per Slide extra local delay time via the data-delay in the slide element (Default: 9000)</div>
    </div>
    <div class="form-global-setting-item">
      <label>Slide width</label>
      <input type="number" name="gridwidth" class="form-text slidergroup-settings"/>
      <div class="description">This Width of the Grid.</div>
    </div>
    <div class="form-global-setting-item">
      <label>Slide height</label>
      <input type="number" name="gridheight" class="form-text slidergroup-settings"/>
      <div class="description">This Height of the Grid.</div>
    </div>
     <div class="form-global-setting-item">
      <label>Min Height</label>
      <input type="number" name="minheight" class="form-text slidergroup-settings"/>
    </div>
    <div class="form-global-setting-item">
      <label>Dotted Overlay</label>
      <select name="dotted_overlay" class="form-select slidergroup-settings">
        <option value="none">none</option>
        <option value="twoxtwo">2 x 2 Black</option>
        <option value="twoxtwowhite">2 x 2 White</option>
        <option value="threexthree">3 x 3 Black</option>
        <option value="threexthreewhite">3 x 3 White</option>
      </select>
    </div>
    <div class="form-global-setting-item">
      <label>Parallax Scroll</label>
      <select name="parallax_scroll" class="form-select slidergroup-settings">
        <option value="off">OFF</option>
        <option value="mouse+scroll">ON</option>
      </select>
    </div>        
  </fieldset>

  <fieldset class="form-wrapper g-wrapper">
    <div class="gavias-heading">Layout Style</div>
    <div class="form-global-setting-item">
      <label>Slider Layout</label>
      <select name="sliderlayout" class="form-select slidergroup-settings">
        <option value="auto">Auto</option>
        <option value="fullwidth">Fullwidth</option>
        <option value="fullscreen">Fullscreen</option>
      </select>
    </div>
    <div class="form-global-setting-item">
      <label>Shadow</label>
       <select name="shadow" class="form-select slidergroup-settings">
        <option value="0">0</option>
        <option value="1">1</option>
        <option value="2">2</option>
        <option value="3">3</option>
      </select>
      <div class="description"> Possible values: 0,1,2,3  (0 == no Shadow, 1,2,3 - Different Shadow Types)</div>
    </div>
  </fieldset>

  <fieldset class="form-wrapper g-wrapper">
    <div class="gavias-heading">Navigation Settings</div>
    <div class="form-global-setting-item">
      <label>Pause on hover</label>
      <select name="onhoverstop" class="form-select slidergroup-settings">
        <option value="on">Yes</option>
        <option value="off">No</option>
      </select>
    </div>
   
   <!-- Arrow -->
    <fieldset class="form-wrapper g-wrapper">
      <div class="gavias-heading">Arrow</div>
      <div class="form-global-setting-item">
        <label>Navigation Arrows Enable</label>
        <select name="arrow_enable" class="form-select slidergroup-settings">
          <option value="true">On</option>
          <option value="false">Off</option>
        </select>
      </div>

      <div class="form-global-setting-item">
        <label>Arrow Left Horizonal Align</label>
        <select name="navigationLeftHAlign" class="form-select slidergroup-settings">
          <option value="left">Left</option>
          <option value="center">Center</option>
          <option value="right">Right</option>
        </select>
      </div>

      <div class="form-global-setting-item">
        <label>Arrow Left Vertical Align</label>
        <select name="navigationLeftVAlign" class="form-select slidergroup-settings">
          <option value="top">Top</option>
          <option value="center">Center</option>
          <option value="bottom">Bottom</option>
        </select>
      </div>

      <div class="form-global-setting-item">
        <label>Arrow Left Horizonal Offset</label>
        <input class="form-text slidergroup-settings" name="navigationLeftHOffset" type="number" />
      </div>

      <div class="form-global-setting-item">
        <label>Arrow Left Vertical Offset</label>
        <input class="form-text slidergroup-settings" name="navigationLeftVOffset" type="number" />
      </div>

     <!--  -->

     <div class="form-global-setting-item">
        <label>Arrow Right Horizonal Align</label>
        <select name="navigationRightHAlign" class="form-select slidergroup-settings">
          <option value="left">Left</option>
          <option value="center">Center</option>
          <option value="right">Right</option>
        </select>
      </div>

      <div class="form-global-setting-item">
        <label>Arrow Right Vertical Align</label>
        <select name="navigationRightVAlign" class="form-select slidergroup-settings">
          <option value="top">Top</option>
          <option value="center">Center</option>
          <option value="bottom">Bottom</option>
        </select>
      </div>

      <div class="form-global-setting-item">
        <label>Arrow Right Horizonal Offset</label>
        <input class="form-text slidergroup-settings" name="navigationRightHOffset" type="number" />
      </div>

      <div class="form-global-setting-item">
        <label>Arrow Right Vertical Offset</label>
        <input class="form-text slidergroup-settings" name="navigationRightVOffset" type="number" />
      </div>
    </fieldset>  

    <fieldset class="form-wrapper g-wrapper">
      <div class="gavias-heading">Bullets</div>
      <div class="form-global-setting-item">
        <label>Bullets Enable</label>
        <select name="bullets_enable" class="form-select slidergroup-settings">
          <option value="true">On</option>
          <option value="false">Off</option>
        </select>
      </div>
    </fieldset> 
      
    <fieldset class="form-wrapper g-wrapper">
      <div class="gavias-heading">Disable Progress Bar</div>
      <div class="form-global-setting-item">
          <select name="progressbar_disable" class="form-select slidergroup-settings">
            <option value="on">On</option>
            <option value="off">Off</option>
          </select>
        </div>
    </fieldset>    

    <input type="button" id="save" class="button button-action button--primary button--small" value="Save"/>

  </fieldset>
</div>  
