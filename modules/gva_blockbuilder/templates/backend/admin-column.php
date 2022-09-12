<div class="gbb-element gbb-columns <?php if($column_type) print ('type-' . $column_type); ?> <?php if($column_size){print 'wb-' . $column_size;} ?>">
   <div class="gavias-blockbuilder-content">
      <input type="hidden" class="gbb-column-id" name="gbb-column-id[]" value="<?php print  $column_id  ?>" />
      <div class="gbb-el-header header-column">
          <div class="gbb-add-element">
            <a class="bb-drap action" ><i class="fa fa-arrows"></i></a>
            <a class="gbb-add action" ><i class="fa fa-plus"></i></a>
            <div class="gbb-items">
              <div class="hedding">
                <span>Add Element</span>
                <a class="bb-btn close-gbb-items"><i class="fa fa-times"></i></a>
              </div>
              <ul class="clearfix">
                  <?php foreach( $item_std as $item ){ ?>
                    <li><a class="<?php print  $item['type']  ?>" ><?php print  $item['title']  ?></a></li>
                  <?php } ?>
              </ul>
            </div>
          </div>
          <div class="element-size">
            <a class="bb-btn gbb-minus" >-</a>
            <span class="item-w-label"><?php print $column_size ?></span>
            <a class="bb-btn gbb-plus" >+</a>
            
          </div>
          <div class="gbb-el-action">
            <a class="bb-btn gbb-edit border-none" title="Edit" ><i class="fa fa-pencil"></i></a>
            <a class="bb-btn gbb-column-clone gbb-item-clone" title="Clone" ><i class="fa fa-clone"></i></a>
            <a class="bb-btn gbb-column-delete" title="Delete"><i class="fa fa-times"></i></a>

          </div>
      </div>
         
      <div class="gbb-droppable gbb-sortable gbb-droppable-column clearfix">
        <?php 
          if( $column && isset($column['items']) && is_array($column['items']) ){
               $i=0;
               foreach( $column['items'] as $item ){
                  if(isset($item_std[$item['type']]) && $item_std[$item['type']]){
                    gavias_admin_element( $item_std[$item['type']], $item, $column_id, $row_id );
                  }
                  $i++;
               }
            }
        ?>
      </div>
      <input type="hidden" class="column-size" name="gbb-columns[size][]" value="<?php print $column_size ?>">
      <input type="hidden" class="column-parent" name="column-parent[]" value="<?php print  $row_id ?>" />
      <input type="hidden" class="column-type" name="gbb-columns[type][]" value="<?php print  $column_type ?>" />
   </div>
      
     <div class="gbb-el-meta">
        <div class="gbb-form">
            <?php    
              foreach( $column_std as $field ){
              $val = false;
                  if( $column && isset($column['attr'][$field['id']]) && $field['type'] !='info'){
                     $val = $column['attr'][$field['id']];
                  }
                  if( !isset($field['std']) ) $field['std'] = false;
                  $val = ( $val || $val=='0' ) ? $val : stripslashes(htmlspecialchars(( $field['std']), ENT_QUOTES ));
                  $field['id'] = 'gbb-columns['. $field['id'] .']';
                  if( $field['type'] != 'tabs' ){
                     $field['id'] .= '[]';
                  }
                  
                  gavias_single_field( $field, $val );
              }
            ?> 
        </div>
     </div>
      
  </div>