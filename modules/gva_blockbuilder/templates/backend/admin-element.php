<div class="gbb-element gbb-item wb-12 gbb-type-<?php print $item_std['type'] ?>">            
  <div class="gavias-blockbuilder-content">
    <div class="gbb-el-header">
      <div class="gbb-el-action">
        <a class="bb-btn gbb-el-delete" title="Delete" ><i class="fa fa-times"></i></a>
      </div>
	  <div class="element-size">
        <a class="bb-btn bb-el-drap action"><i class="fa fa-arrows"></i></a>
        <a class="bb-btn gbb-edit border-none" title="Edit" ><i class="fa fa-pencil"></i></a>
        <a class="bb-btn gbb-el-clone gbb-item-clone" title="Clone" ><i class="fa fa-clone"></i></a>
      </div>
    </div>

    <?php if($item_std['type'] == 'gsc_code'){?>
       <div class="gbb-item-colums gbb-droppable" style="min-height:200px;border: 1px solid #ccc;">
       </div>
    <?php }else{ ?>
     <div class="gbb-item-content">
        <span class="gbb-item-title"><?php print $item_std['title'] ?></span>
        <span class="item-bb-title"><?php print strip_tags($label); ?></span>
      </div>
    <?php } ?>  

    <input type="hidden" class="element-type" <?php print  $element_type ?> value="<?php print $item_std['type'] ?>">
    <input type="hidden" class="element-parent" <?php print $element_parent ?> value="<?php print  $column_id ?>" />
    <input type="hidden" class="element-row-parent" <?php print $element_row_parent ?> value="<?php print  $row_id ?>" />
  </div>
    
    <div class="gbb-el-meta">
      <div class="gbb-form">  
        <?php
          foreach( $item_std['fields'] as $field ){
			 $val = false;
            if( $item && key_exists( 'fields', $item ) && key_exists( $field['id'], $item['fields'] ) ){
              $val = $item['fields'][$field['id']];
            }
            if( ! isset($field['std']) ) $field['std'] = false;
            $val = ( $val || $val=='0' ) ? $val : stripslashes(htmlspecialchars(( $field['std']), ENT_QUOTES ));
            $field['id'] = 'gbb-items['. $item_std['type'] .']['. $field['id'] .']';
            if( $field['type'] != 'tabs' && $field['type'] != 'textlangs' && $field['type'] != 'textarealangs' ){
              $field['id'] .= '[]';         
            }
            gavias_single_field( $field, $val );
          }
      ?>
    </div>
  </div>
</div>