<?php
/*
Plugin Name: Blogger Posts
Description: Echoes last blogger feed entries
Version: 0.1
Author: Antonino Liconti
Author URI: 
*/

# get correct id for plugin
$thisfile=basename(__FILE__, ".php");
# add the plugin's language file
i18n_merge('blogger') || i18n_merge('blogger', 'en_US');

# register plugin
register_plugin(
	$thisfile, //Plugin id
	'Blogger Posts', 	//Plugin name
	'0.1', 		//Plugin version
	'Antonino Liconti',  //Plugin author
	'http://www.cagintranet.com/', //author website
	'Echoes last blogger feed entries', //Plugin description
	'plugins', //page type - on which admin tab to display
	'blogger_main'  //main function (administration)
);

# admin hooks 
add_action('plugins-sidebar','createSideMenu',array($thisfile, 'Blogger feed configuration'));

# definitions
define('BLGGR_INCLUDE', GSPLUGINPATH . 'blogger/');
define('BLGGR_DATADIR', GSDATAOTHERPATH . 'blogger/');
define('BLGGR_DATAFILE', BLGGR_DATADIR . 'bloggerSettings.xml');
define('BLGGR_CACHEDIR', GSDATAOTHERPATH . 'blogger/');
define('BLGGR_CACHEFILE', BLGGR_CACHEDIR . 'bloggerCache');
# includes
require_once(BLGGR_INCLUDE . 'blogger_functions.php');
require_once(BLGGR_INCLUDE . 'blogger_cfgpanel.php');

# get config file path
$bloggercfg_file=GSDATAOTHERPATH .'blogger/bloggerSettings.xml';
$blogger_cache=GSDATAOTHERPATH .'blogger/bloggerCache';

# main function
function blogger_main() {
  if (isset($_POST['save'])) {
    blogger_save_cfg();
    blogger_cfg_panel();
  } else {
    blogger_cfg_panel();
  }
}

?>