<div class="gbb-element gbb-row">
   <div class="gavias-blockbuilder-content">
      <input type="hidden" class="gbb-row-id" <?php print  $name_row_id ?> value="<?php print  $row_id  ?>" />
      <div class="gbb-el-header header-row">
          <div class="gbb-add-element">
            <a class="bb-drap action" ><i class="fa fa-arrows"></i></a>
            
            <div class="action-add-column">
               <a class="btn-hover"><?php print t('Add column') ?></a>
               <div class="hover-content">
                  <a class="gbb-add-column action" data-width="2">1/6</a>
                  <a class="gbb-add-column action" data-width="3">1/4</a>
                  <a class="gbb-add-column action" data-width="4">1/3</a>
                  <a class="gbb-add-column action" data-width="5">5/12</a>
                  <a class="gbb-add-column action" data-width="6">1/2</a>
                  <a class="gbb-add-column action" data-width="7">7/12</a>
                  <a class="gbb-add-column action" data-width="8">2/3</a>
                  <a class="gbb-add-column action" data-width="9">3/4</a>
                  <a class="gbb-add-column action" data-width="10">5/6</a>
                  <a class="gbb-add-column action" data-width="11">11/12</a>
                  <a class="gbb-add-column action" data-width="12">1/1</a>
               </div>
            </div>

            
            <a class="gbb-add-clear">Add Clear</a>
           
          </div>
         <div class="gbb-el-action">
            <a class="bb-btn gbb-edit big action" title="Edit" ><i class="fa fa-pencil"></i></a>
            <a class="bb-btn gbb-el-clone gbb-row-clone big action" title="Clone" ><i class="fa fa-clone"></i></a>
            <a class="bb-btn gbb-row-delete big action" title="Delete" ><i class="fa fa-times"></i></a>
         </div>
      </div>
         
      <div class="gbb-droppable gbb-sortable gbb-droppable-row clearfix">
         <?php 
            if( $row && isset($row['columns']) && is_array($row['columns']) ){
               $i=0;
               foreach( $row['columns'] as $column ){
                    gavias_admin_column( $item_std, $column_std, $column, $i+1, $row_id);
                  $i++;
               }
            }
         ?>
      </div>

   </div>
      
     <div class="gbb-el-meta">
        <div class="gbb-form">
            <?php    
              foreach( $row_std as $field ){
				      $val = false;
                  if( $row && isset($row['attr'][$field['id']]) && $field['type'] !='info'){
                     $val = $row['attr'][$field['id']];
                  }
                  if( !isset($field['std']) ) $field['std'] = false;
                  $val = ( $val || $val=='0' ) ? $val : stripslashes(htmlspecialchars(( $field['std']), ENT_QUOTES ));
                  $field['id'] = 'gbb-rows['. $field['id'] .']';
                  if( $field['type'] != 'tabs' ){
                     $field['id'] .= '[]';
                  }
                  
                  gavias_single_field( $field, $val );
              }
            ?> 
        </div>
     </div>
      
  </div>