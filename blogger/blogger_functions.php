<?php if (!defined('IN_GS')) {die('you cannot load this page directly.');}

/**
 * Function file used by the GetSimple Blogger Plugin.
 */
 
 # main function to call the last blogger posts
function show_blogger_posts($overwrite_cache) {
  $posts = get_blogger_data($overwrite_cache);
  echo format_blogger_posts($posts);
}

# get file, if exists, get data
function get_blogger_config_data() {
  if (file_exists(BLGGR_DATAFILE)) {
	  $data = @getXML(BLGGR_DATAFILE);
	  $id = $data->id;
	  $num_posts = $data->numposts;
	  $excerpt_len = $data->excerptlen;
	  $max_cachetime = $data->max_cachetime;
  } else {
	  $id = '';
	  $num_posts = '';
	  $excerpt_len = '';
	  $max_cachetime = '';
  }
  return array('id'=>$id, 'numposts'=>$num_posts, 'excerptlen'=>$excerpt_len,
               'cachetime'=>$max_cachetime);
}
 
# save config data
function blogger_save_cfg() {
	$id = safe_slash_html($_POST['accountid']);
	# check blogger id
	$numposts = isset($_POST['num_posts']) ? intval($_POST['num_posts']) : 1;
	$excerptlen = isset($_POST['excerpt_len']) ? intval($_POST['excerpt_len']) : 200;
	$caching = isset($_POST['max_cachetime']) ? intval($_POST['max_cachetime']) : 60;
	$xml = @new SimpleXMLElement('<item></item>');
	$xml->addChild('id', $id);
	$xml->addChild('numposts', $numposts);
	$xml->addChild('excerptlen', $excerptlen);
	$xml->addChild('max_cachetime', $caching);
	#check if blogger plugin configuration directory exists
	if (!file_exists(BLGGR_DATADIR)) {
		if (defined('GSCHMOD')) { 
			$chmod_value = GSCHMOD; 
		} else {
			$chmod_value = 0755;
		}
		mkdir(BLGGR_DATADIR, $chmod_value);
	}
	if (! $xml->asXML(BLGGR_DATAFILE)) {
		echo '<p style="color:#cc0000;"><b>'. i18n_r('CHMOD_ERROR') .'</b></p>';
	} else {
		echo '<p style="color:#669933;"><b>'. i18n_r('SETTINGS_UPDATED') .'</b></p>';
	}
}
 
 
# return 1 if the cached file is still within $maxTime, else return 0
function checkBloggerCachedFile($maxTime) {
  # check if file $tweet_cache exist
  if (file_exists(BLGGR_CACHEFILE)) {
    $diff =  time() - filemtime(BLGGR_CACHEFILE);
    echo '<!-- age: ' . $diff . '-->';
    if ($diff < $maxTime) {
      echo '<!-- cached file is still valid. -->';
      return 1;
    }
    return 0;
  } else {
    return 0;
  }
}

function get_blogger_data($overwrite_cache) {
	$config = get_blogger_config_data();
	$blogger = array();

	  # check the datetime stamp of the cached file
	if ($overwrite_cache <> 1 AND checkBloggerCachedFile($config['cachetime'])) {
		echo '<!-- blogger information from cache -->';
		$xmldata = file_get_contents(BLGGR_CACHEFILE);
		$bloggerXML = new SimpleXMLElement($xmldata);
	} else {
		$url = 'http://www.blogger.com/feeds/'.$config['id'].'/posts/default?max-results='.$config['numposts'];
		$bloggerXML = simplexml_load_file($url);
		write_blogger_to_cache($bloggerXML);
	}
	foreach ($bloggerXML->entry as $entry) {
		$link = '';
		foreach ($entry->link as $clink) 
			if (($clink['rel'])=='alternate') $link=$clink['href'];
		$title = $entry->title;
		if ($title=='') $title = $bloggerXML->title;
		$content = $entry->content;
		$excerpt = substr( strip_tags($content), 0, intval($config['excerptlen']));
		$lastspace = strrpos($excerpt, " ");
		if ($lastspace>0) $excerpt = substr($excerpt, 0, $lastspace);
		$excerpt .= '... ';
		$blogger[] = array(
			'title' => $title, 
			'content' => $content, 
			'excerpt' => $excerpt, 
			'link' => $link
		);
	}
	//var_dump("<pre>",$blogger,"</pre>");
	return $blogger;
}

function format_blogger_posts($blogger, $excerpt=true) {
	$out = '';
	foreach($blogger as $entry) {
		$out .= '<div class="blogger_entry">';
		$out .= ' <a class="blogger_link" href="' . $entry['link'] . '">';
		$out .= '  <span class="blogger_title" >'. $entry['title'] .'</span>';
		$out .= ' </a>';
		if ($excerpt) {
			$out .= ' <p class="blogger_content" >'. $entry['excerpt'] .'</p>';
		}
		else{
			$out .= ' <p class="blogger_content" >'. $entry['content'] .'</p>';	
		}
		$out .= '</div>';  
	}
	$out = '<div class="blogger_entry">' . $out . '</div>'; 
	return $out;
}


# write back the new tweet data into cache, catch some possible errors
function write_blogger_to_cache($bloggerXML) {
  # check BLGGR_CACHEDIR directory existence
  if (!file_exists(BLGGR_CACHEDIR)) {
    echo '<p style="color:#cc0000;"><b>'. 'directory does not exist, trying to create' .'</b></p>';
    if (!mkdir(BLGGR_CACHEDIR)) {
      echo '<p style="color:#cc0000;"><b>'. 'unable to create cache directory' .'</b></p>';
    } else {
      echo '<p style="color:#669933;"><b>'. 'created ' . BLGGR_CACHEDIR .'</b></p>';
    }
  }
  # write
  if (!file_put_contents(BLGGR_CACHEFILE, $bloggerXML->asXML())) {
    echo '<p style="color:#cc0000;"><b>'. i18n_r('CHMOD_ERROR') .'</b></p>';
  }
}


?>