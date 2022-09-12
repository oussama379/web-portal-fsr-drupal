<?php
class gavias_bb_fields{
	
	public function render_field_text($field = array(), $value = ''){
		$output = '';
		if(is_array($value)) $value = '';
		$class = ( isset( $field['class']) ) ? $field['class'] : '';
		$output .= '<input autocomplete="off" type="text" name="'. $field['id'] .'" value="'.htmlentities($value).'" class="'.$class.'" />';
		$output .= (isset($field['desc']) && !empty($field['desc']))?' <span class="description '.$class.'">'.$field['desc'].'</span>':'';	
		return $output;
	}

	public function render_field_textlangs($field = array(), $value = ''){
		$_id = gavias_blockbuilder_makeid();
		$languages =  \Drupal::languageManager()->getLanguages();
		if($value && !is_array($value)){
			$value =  array(
				'textlangs' => array(
					'language__gvalangdefault' => $value
				)
			);		
		}
		$output = '';
		$class = ( isset( $field['class']) ) ? $field['class'] : '';
		ob_start();

		print '<div class="multi-language"><ul class="swith-language nav nav-tabs">';
			print '<li><a class="active" href="#' . $_id . 'gvalangdefault">' . t('Default') . '</a></li>';
			if(count($languages) > 1){ 
				foreach ($languages as $language) { 
					print '<li><a href="#' . $_id . $language->getId() . '">'.$language->getName().'</a></li>';
				}
			 }
		print '</ul>';

		print '<div class="tab-content swith-languages">';
			?>
			<div class="tab-pane active" id="<?php echo ($_id . 'gvalangdefault') ?>">
		       <input class="<?php echo $class ?>" name="<?php echo $field['id'] ?>[textlangs][language__gvalangdefault][]" value="<?php echo (isset($value['textlangs']['language__gvalangdefault']) && $value['textlangs']['language__gvalangdefault'] ? $value['textlangs']['language__gvalangdefault'] : ''); ?>"/>
			</div>
			<?php
			if(count($languages) > 1){
			foreach ($languages as $language) { ?>
				<div class="tab-pane" id="<?php echo ($_id . $language->getId()) ?>">
		       	<input class="<?php echo $class ?>" name="<?php echo $field['id'] ?>[textlangs][language__<?php echo $language->getId() ?>][]" value="<?php echo (isset($value['textlangs']['language__'.$language->getId()]) && $value['textlangs']['language__'.$language->getId()] ? $value['textlangs']['language__'.$language->getId()] : ''); ?>" />
				</div>
			<?php } }
		print '</div></div>';

		$content = ob_get_clean(); 
		return $content;
	}
	
	public function render_field_select($field = array(), $value = ''){
		$output = '';
		$class = ( isset( $field['class']) ) ? 'class="'.$field['class'].'" ' : '';
		$output .= '<select autocomplete="off" name="'. $field['id'] .'" '.$class.'rows="6" >';
			if( is_array( $field['options'] ) ){
				foreach( $field['options'] as $k => $v ){
					$output .= '<option ' . (($value && $value == $k) ? 'selected="selected"' : '') . ' value="'.$k.'" '.'>'.$v.'</option>';
				}
			}
		$output .= '</select>';
		$output .= (isset($field['desc']) && !empty($field['desc']))?' <span class="description">'.$field['desc'].'</span>':'';
		return $output;
	}
	
	public function render_field_textarea($field = array(), $value = ''){
		$output = '';
		$class 	= isset( $field['class'] ) ? $field['class'] : '';
		if(isset( $field['shortcodes'] ) && $field['shortcodes']) $class .= 'gerenal_sc';
		$param 	= isset( $field['param'] ) ? $field['param'] : '';
		ob_start();
		?>
		<div class="textarea-wrapper <?php print $class ?>">
			
			<textarea class="code_html code_html_tiny" name="<?php print $field['id'] ?>" class="<?php print $param ?>" rows="8"><?php print($value) ?></textarea>
			<?php if( isset($field['desc']) && !empty($field['desc']) ){ ?>
				<br/><span class="description"><?php print $field['desc'] ?></span> 
			<?php } ?>	
		</div>
		<?php
		$content = ob_get_clean(); 
		return $content;
	}

	public function render_field_textarealangs($field = array(), $value = ''){
		$_id = gavias_blockbuilder_makeid();
		$languages =  \Drupal::languageManager()->getLanguages();
		if($value && !is_array($value)){
			$value =  array(
				'htmllangs' => array(
					'language__gvalangdefault' => $value
				)
			);		
		}
		$output = '';
		$class = ( isset( $field['class']) ) ? $field['class'] : '';
		ob_start();

		print '<div class="multi-language"><ul class="swith-language nav nav-tabs">';
			print '<li><a class="active" href="#' . $_id . 'gvalangdefault">' . t('Default') . '</a></li>';
			if(count($languages) > 1){ 
				foreach ($languages as $language) { 
					print '<li><a href="#' . $_id . $language->getId() . '">'.$language->getName().'</a></li>';
				}
			 }
		print '</ul>';

		print '<div class="tab-content swith-languages">';
			?>
			<div class="tab-pane active" id="<?php echo ($_id . 'gvalangdefault') ?>">
		       <textarea class="code_html code_html_tiny" name="<?php echo $field['id'] ?>[htmllangs][language__gvalangdefault][]"><?php echo (isset($value['htmllangs']['language__gvalangdefault']) && $value['htmllangs']['language__gvalangdefault'] ? $value['htmllangs']['language__gvalangdefault'] : ''); ?></textarea>
			</div>
			<?php
			if(count($languages) > 1){
			foreach ($languages as $language) { ?>
				<div class="tab-pane" id="<?php echo ($_id . $language->getId()) ?>">
		       	<textarea class="code_html code_html_tiny" name="<?php echo $field['id'] ?>[htmllangs][language__<?php echo $language->getId() ?>][]"><?php echo (isset($value['htmllangs']['language__'.$language->getId()]) && $value['htmllangs']['language__'.$language->getId()] ? $value['htmllangs']['language__'.$language->getId()] : ''); ?></textarea>
				</div>
			<?php } }
		print '</div></div>';

		$content = ob_get_clean(); 
		return $content;
	}

	public function render_field_textarea_no_html($field = array(), $value = ''){
		$output = '';
		$class 	= isset( $field['class'] ) ? $field['class'] : '';
		if(isset( $field['shortcodes'] ) && $field['shortcodes']) $class .= 'gerenal_sc';
		$param 	= isset( $field['param'] ) ? $field['param'] : '';
		//$general_shortcodes = gavias_blockbluider_general_shortcode();
		ob_start();
		?>
		<div class="textarea-wrapper <?php print $class ?>">
			
			<textarea name="<?php print $field['id'] ?>" class="<?php print $param ?>" rows="8"><?php print htmlentities($value) ?></textarea>
			<?php if( isset($field['desc']) && !empty($field['desc']) ){ ?>
				<br/><span class="description"><?php print $field['desc'] ?></span> 
			<?php } ?>	
		</div>
		<?php
		$content = ob_get_clean(); 
		return $content;
	}
	
	public function render_field_upload($field = array(), $value = ''){
	 	if(gavias_blockbuilder_imce()) {
	 		return $this->render_field_upload_imce($field, $value);
	 	}else{
	 		return $this->render_field_upload_non_imce($field, $value);
	 	}
	}

	public function render_field_upload_imce($field = array(), $value = ''){
		global $base_url;
		$_id = gavias_blockbuilder_makeid(10);
		$default_image = base_path() . GAVIAS_BLOCKBUILDER_PATH . '/assets/images/default.png';
		if( $value ){
			$path_image_demo = substr(base_path(), 0 , -1) . $value;
		}else{
			$path_image_demo = $default_image;
		} 
		$class = ( isset($field['class']) ) ? $field['class'] : '';
		ob_start();
		?> 

		<div class="gva-upload-input">
			<input data-id="<?php print $_id; ?>" id="gva-upload-<?php print $_id; ?>" type="text" name="<?php print $field['id'] ?>" value="<?php print $value ?>" class="<?php print $class ?> imce-url-input" data-imce-type="link"/>
			
			<img class="gavias-image-demo" id="gva-upload-<?php print $_id; ?>-demo" src="<?php print $path_image_demo ?>" />
			<a class="gavias-field-upload-remove btn-delete" data-src="<?php print $default_image ?>" style="<?php print (($value) ? 'display:inline-block;' : 'display:none;') ?>">Remove</a>
			
			<div class="clearfix"></div>
			<?php if(isset($field['desc']) && ! empty($field['desc'])){?>
				<span class="description"><?php print $field['desc'] ?></span>
			<?php } ?>
			<div class="clearfix"></div>
		</div>	
			
		<?php
		$content = ob_get_clean(); 
		return $content;
	}

	public function render_field_upload_non_imce($field = array(), $value = ''){
		global $base_url;
		$_id = gavias_blockbuilder_makeid(10);
		$default_image = base_path() . GAVIAS_BLOCKBUILDER_PATH . '/assets/images/default.png';
		if( $value ){
			$path_image_demo = substr(base_path(), 0 , -1) . $value;
		}else{
			$path_image_demo = $default_image;
		} 
		$class = ( isset($field['class']) ) ? $field['class'] : 'image';
		ob_start();
		?> 
		<div class="gva-upload-image" id="gva-upload-<?php print $_id; ?>">
			<form class="upload" id="upload-<?php print $_id; ?>" method="post" action="<?php print (base_path() . 'admin/structure/gavias_blockbuilder/upload') ?>" enctype="multipart/form-data">
				<div class="drop">
					<input type="file" name="upl" multiple class="input-file-upload"/>
				</div>
			</form>
			<input readonly="true" type="text" name="<?php print $field['id'] ?>" value="<?php print $value ?>" class="<?php print $class ?> file-input" />
			<img class="gavias-image-demo" src="<?php print $path_image_demo ?>" />
			<a class="gavias-field-upload-remove btn-delete" data-src="<?php print $default_image ?>" style="<?php print (($value) ? 'display:inline-block;' : 'display:none;') ?>">Remove</a>
			<span class="loading">Loading....</span>
			<a class="btn-delete btn-get-images-upload">Choose image</a>
			<div class="clearfix"></div>
			<?php if(isset($field['desc']) && ! empty($field['desc'])){?>
				<span class="description"><?php print $field['desc'] ?></span>
			<?php } ?>
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
	
	public function render_field_info($field = array(), $value = ''){
		$output = '';
		if( key_exists('desc', $field) ){
			$output .= '<p class="info-des">'.$field['desc'].'</p>';
		}
		return $output;
	}
	
	public function render_field_tabs($field = array(), $value = ''){
		$class = ( isset($field['class']) ) ? $field['class'] : '';
		$name = $field['id'];
		$count = ($value) ? count($value) : 0;
		ob_start();
		?>
		
		<input type="hidden" name="<?php print $name ?>[count][]" class="gbb-tabs-count" value="<?php print $count ?>" />
		<br style="clear:both;" />
		<ul class="tabs-ul">
			<?php	
				if(isset($value) && is_array($value)){
					foreach($value as $k => $val){
			?>
					<li>
						<label>Title</label>
						<input type="text" name="<?php print $name ?>[title][]" value="<?php print htmlspecialchars(stripslashes($val['title'])) ?>" />
						<label>Icon tab</label>
						<input type="text" name="<?php print $name ?>[icon][]" value="<?php print htmlspecialchars(stripslashes($val['icon'])) ?>" />
						<label>Content</label>
						<textarea class="code_html_tiny" name="<?php print $name ?>[content][]" value="" ><?php print $val['content'] ?></textarea>
						<a href="" class="bb-btn-close gbb-remove-tab"><em>delete</em></a>
					</li>
			 <?php
				}
			}
			?>
			<li class="tabs-default gavias-hidden">
				<label>Title</label>
				<input class="title" type="text" name="" value="" />
				<label>Icon tab</label>
				<input class="icon" type="text" name="" value="" />
				<div class="description">This support display icon from FontAwsome, Please click here to <a href="http://fortawesome.github.io/Font-Awesome/icons/">see the list</a></div>
				<label>Content</label>
				<textarea class="" name="" value=""></textarea>
				<a href="" class="btn-delete bb-btn-close gbb-remove-tab">delete</a>
			</li>
		</ul>

		<?php if(isset($field['desc']) && ! empty($field['desc'])){?>
			<span class="description"><?php print $field['desc'] ?></span>
		<?php } ?>
		<a class="btn-add gbb-add-tab" rel-name="<?php print $name ?>">Add tab</a>
		<?php
		$content = ob_get_clean(); 
		return $content;
	}
}