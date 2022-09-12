<li <?php print $attributes;?>>
   <?php if(isset($slide->video_source) && (isset($slide->youtube_video) || isset($slide->vimeo_video) || isset($slide->html5_mp4)) && $slide->video_source && ($slide->youtube_video || $slide->vimeo_video || $slide->html5_mp4)){ ?>
      <div <?php print $attributes_video;?>></div>
   <?php } ?>
	<?php print $content;?>
</li>

