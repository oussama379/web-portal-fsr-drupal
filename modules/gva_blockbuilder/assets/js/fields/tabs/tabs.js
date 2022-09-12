jQuery(document).ready(function($){

	// sortable init
	$('.tabs-ul').hover(function(){
		$('.tabs-ul').sortable({ 
			cursor	: 'move',
			opacity	: 0.9
		});
	});
	
	
	// add
	$('.gbb-add-tab').click(function(){
		
		// increase tabs counter
		var tabs_counter = $(this).siblings('.gbb-tabs-count');
		tabs_counter.val(tabs_counter.val()*1 + 1);
		
		var name = $(this).attr('rel-name');
		var tabs_wrapper = $(this).siblings('.tabs-ul');
		var new_tab = tabs_wrapper.children('li.tabs-default').clone(true);

		new_tab.removeClass('tabs-default');	
		new_tab.children('input.title').attr('name',name+'[title][]');
		new_tab.children('input.icon').attr('name',name+'[icon][]');
		new_tab.children('textarea').attr('name',name+'[content][]');
		new_tab.children('textarea').addClass('code_html_tiny code_html_tiny_after_' + (tabs_counter.val()*1 + 1));

		tabs_wrapper
			.append( new_tab )
			.children('li:last')
				.fadeIn(500);

		tinymce.init({
        	selector: '#gbb-form-setting textarea.code_html_tiny_after_' + (tabs_counter.val()*1 + 1),
        	height: 300,
        	plugins: [
          'advlist autolink lists link image charmap anchor pagebreak media searchreplace code fullscreen',
          'emoticons textcolor textpattern colorpicker'
        	],
        	toolbar1: 'insertfile undo redo | styleselect | bullist numlist outdent indent | link image | forecolor backcolor emoticons',
      });		

	});
	
	// delete
	$('.gbb-remove-tab').click(function(e){
		e.preventDefault();
		
		// decrease tabs counter
		var tabs_counter = $(this).parents('td').children('.gbb-tabs-count');
		tabs_counter.val(tabs_counter.val()*1 - 1);
		
		$(this).parent().fadeOut(300, function(){$(this).remove();});
	});
	
});