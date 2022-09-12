<div id="gavias-blockbuilder-setting">

   <script src="<?php echo base_path() . drupal_get_path('module', 'gavias_blockbuilder') . '/vendor/tinymce/tinymce.min.js' ?>"></script>

    <?php  print gavias_blockbuilder_admin($bid); ?>
    <input type="hidden" value="<?php print $bid ?>" id="gavias_blockbuilder_id" name="gavias_blockbuilder_id" />
    <input type="button" id="save" class="button button-action button--primary button--small" value="Save"/>
  </fieldset>
</div>  
