<?php

//prohibit unauthorized access
require 'core/access.php';

if(count($all_plugins)<1) {
	/* no plugins */
	echo '<div class="alert alert-info">'.$lang['alert_no_plugins'].'</div>';
} else {

	$template_file = file_get_contents("templates/pluginlist.tpl");
	
	foreach($all_plugins as $plugin) {
		
		$pathinfo = pathinfo('/content/plugins/'.$plugin);
		if($pathinfo['extension'] != 'php') {
			continue;
		}
		
		$id = md5($plugin);
		$plugin_src = file_get_contents('../'.FC_CONTENT_DIR.'/plugins/'.$plugin);
		$comment = getfirstcommentblock($plugin_src);
		$plugin_info = get_include_contents('../'.FC_CONTENT_DIR.'/plugins/'.$plugin);
		$tpl_icon = "images/plugin-icon.png";
		//$filesize = readable_filesize(filesize('../'.FC_CONTENT_DIR.'/plugins/'.$plugin));
		//$lastedit = date('Y-m-d H:i:s',filemtime('../'.FC_CONTENT_DIR.'/plugins/'.$plugin));
		
		$edit_btn = '<a class="btn btn-fc btn-sm" data-toggle="modal" data-target="#myModal'.$id.'" href="javascript:;">Source</a>';
		
		/* show the first comment block */
		$help_btn = '';
		if($comment != '') {
			echo '<div id="help'.$id.'" style="display:none;"><pre>'.$comment.'</pre></div>';
			$help_btn = ' <a class="fancybox btn btn-fc btn-sm" href="#help'.$id.'">'.$icon['question'].'</a>';
		}
		
		$btn_group = '<div class="btn-group float-right" role="group">'.$edit_btn.$help_btn.'</div>';
		
		/* shorten the filename if needed */
		$plugin_name = basename($plugin,'.php');
		if(strlen($plugin_name) > 10) {
			$plugin_name = substr($plugin_name, 0,10);
		}
		
		/* show version information */
		$plugin_version = '<p class="text-muted">';
		if($plugin_info['version'] != '') {
			$plugin_version .= '<span class="">Version: '.$plugin_info['version'].'</span> · ';
		}
		/* show author information */
		$plugin_author = '';
		if($plugin_info['author'] != '') {
			$plugin_version .= '<span class="">Author: '.$plugin_info['author'].'</span>';
		}
		$plugin_version .= '</p>';
		
		
		$tpl = $template_file;
		$tpl = str_replace("{\$PLUGIN_NAME}", "$plugin_name","$template_file");
		$tpl = str_replace("{\$PLUGIN_TITLE}", $plugin_info['title'],"$tpl");
		$tpl = str_replace("{\$PLUGIN_DESCRIPTION}", $plugin_info['description'],"$tpl");
		$tpl = str_replace("{\$PLUGIN_VERSION}", $plugin_version,"$tpl");
		$tpl = str_replace("{\$PLUGIN_ICON}", "$tpl_icon","$tpl");
		$tpl = str_replace("{\$PLUGIN_NAV}", "$btn_group","$tpl");
		echo $tpl;

		/* Modal */
		echo '<div class="modal fade" id="myModal'.$id.'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">';
		echo '<div class="modal-dialog modal-lg" role="document">';
		echo '<div class="modal-content">';
		echo '<div class="modal-header">';
		echo '<h4 class="modal-title" id="myModalLabel'.$id.'">'.$plugin.'</h4>';
		echo '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
		echo '</div>';
		echo '<div class="modal-body">';
		echo '<pre class="form-control" style="height:400px;overflow:auto;">'.htmlentities($plugin_src,ENT_QUOTES,"UTF-8").'</pre>';
		echo '</div>';
		echo '<div class="modal-footer">';
		echo '<button type="button" class="btn btn-fc" data-dismiss="modal">Close</button>';
		echo '</div>';
		echo '</div>';
		echo '</div>';
		echo '</div>';
		
	}
}



function get_include_contents($filename) {
    if (is_file($filename)) {
        ob_start();
        include $filename;
        ob_get_clean();
        return $plugin;
    }
    return false;
}

function getfirstcommentblock($str) {
	$comments = array_filter(
  	token_get_all($str),function($entry) {
    	return $entry[0] == T_DOC_COMMENT;
    }
  );
  $comment = array_shift($comments);
  return $comment[1];
}


?>