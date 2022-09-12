<?php 
  $_id=gavias_sliderlayer_makeid();
?>
<div <?php print $attributes;?>>
   <div id="<?php print $_id; ?>" class="rev_slider fullwidthabanner" data-version="5.4.1">
      <ul>
         <?php print $content;?>
      </ul>
      <div class="tp-bannertimer tp-top"></div>
   </div>
</div>
<script type="text/javascript">

  jQuery(document).ready(function($){
    jQuery('#<?php print $_id ?>').show().revolution(<?php print $ss ?>);
  });

</script>