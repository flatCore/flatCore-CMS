<?php
error_reporting(E_ALL ^E_NOTICE);
//prohibit unauthorized access
require("core/access.php");

$all_plugin_files = array_diff(scandir('../'.FC_CONTENT_DIR.'/plugins/'), array('..', '.','.DS_Store','index.html'));

if(count($all_plugin_files)<1) {
	/* no plugins */
	echo '<div class="alert alert-info">'.$lang['alert_no_plugins'].'</div>';
} else {

	$template_file = file_get_contents("templates/modlist.tpl");
	
	foreach($all_plugin_files as $plugin) {
		
		$id = md5($plugin);
		$plugin_src = file_get_contents('../'.FC_CONTENT_DIR.'/plugins/'.$plugin);
		$tokens = token_get_all($plugin_src);
		$plugin_info = get_include_contents('../'.FC_CONTENT_DIR.'/plugins/'.$plugin);
		$filesize = readable_filesize(filesize('../'.FC_CONTENT_DIR.'/plugins/'.$plugin));
		$lastedit = date('Y-m-d H:i:s',filemtime('../'.FC_CONTENT_DIR.'/plugins/'.$plugin));
		
		
		
		if($_SESSION['user_class'] == 'administrator') {
			$edit_btn = '<a href="/acp/core/ajax.plugins.php?plugin='.$plugin.'" class="fancybox-ajax btn btn-sm btn-default"><span class="glyphicon glyphicon-pencil"></span> '.$lang['edit'].'</a>';
		} else {
			$edit_btn = '<a class="btn btn-default btn-xs" data-toggle="modal" data-target="#myModal'.$id.'">Source</a>';
		}
		
		$tpl_icon = "images/plugin-icon.png";
		
		$tpl = $template_file;
		
		$tpl = str_replace("{\$MOD_NAME}", "$plugin","$template_file"); 
		$tpl = str_replace("{\$MOD_DESCRIPTION}", $plugin_info['description'],"$tpl");
		$tpl = str_replace("{\$MOD_VERSION}", $plugin_info['version'],"$tpl");
		$tpl = str_replace("{\$MOD_AUTHOR}", $plugin_info['author'],"$tpl");
		$tpl = str_replace("{\$MOD_ICON}", "$tpl_icon","$tpl");
		$tpl = str_replace("{\$MOD_LIVECODE}", "","$tpl");
		$tpl = str_replace("{\$MOD_CHECK_IN_OUT}", "","$tpl");
		
		
		$tpl = str_replace("{\$MOD_NAV}", "$edit_btn","$tpl");
		
		echo $tpl;
	
		/* Modal */
		echo '<div class="modal fade" id="myModal'.$id.'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">';
		echo '<div class="modal-dialog modal-lg" role="document">';
		echo '<div class="modal-content">';
		echo '<div class="modal-header">';
		echo '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
		echo '<h4 class="modal-title" id="myModalLabel'.$id.'">'.$plugin.'</h4>';
		echo '</div>';
		echo '<div class="modal-body">';
		echo '<pre class="form-control" style="height:400px;overflow:auto;">'.htmlentities($plugin_src).'</pre>';
		echo '</div>';
		echo '<div class="modal-footer">';
		echo '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>';
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

?>