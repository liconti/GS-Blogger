<?php if (!defined('IN_GS')) {die('you cannot load this page directly.');}

/**
 * Configuration panel used by the GetSimple Last Tweet Plugin.
 */
 
 function blogger_cfg_panel() {
  $config = get_blogger_config_data();
  ?>
  
  <h3><?php i18n('blogger/BLOGGER_TITLE'); ?></h3>
	<p><?php i18n('blogger/BLOGGER_DESC_LONG'); ?></p>
  
  <form method="post" action="<?php	echo $_SERVER ['REQUEST_URI']?>">
	<p><label for="blogger" ><?php i18n('blogger/BLOGGER_ID'); ?></label>
  <input id="blogger" name="accountid" class="text" style="width: 100px;" value="<?php echo $config['id']; ?>" type="text" /></p>
  <p><label for="num_posts" ><?php i18n('blogger/NUM_POSTS'); ?></label>
  <input id="num_posts" name="num_posts" class="text" style="width: 100px;" value="<?php echo $config['numposts']; ?>" type="text" /></p>  
  <p><label for="excerpt_len" ><?php i18n('blogger/POST_EXCERPT_LENGTH'); ?></label>
  <input id="excerpt_len" name="excerpt_len" class="text" style="width: 100px;" value="<?php echo $config['excerptlen']; ?>" type="text" /></p>
  <p><label for="max_cachetime" ><?php i18n('blogger/MAX_CACHETIME'); ?></label>
  <input id="max_cachetime" name="max_cachetime" class="text" style="width: 100px;" value="<?php echo $config['cachetime']; ?>" type="text" /></p>
	<p><input type="submit" id="submit" class="submit" value="<?php i18n('BTN_SAVESETTINGS'); ?>" name="save" /></p>
    <p>  
		 <h3>sample output:</h3>
		 <?php show_blogger_posts(false); ?>
		</p>
	</form>
  
  <?php
  }



?>