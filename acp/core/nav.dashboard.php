<?php

require("core/access.php");

echo '<div class="panel-group panel-sidemenu" id="dashboard-collapse">';

echo '<div class="panel">';
echo '<div class="panel-heading">';
echo '<a class="sidebar-nav dashboard-toggle active" data-toggle="collapse" data-parent="#dashboard-collapse" href="#pages">'.$lang['tn_pages'].'<span class="tri-left"></span></a>';
echo '</div>';

echo '<div id="pages" class="panel-collapse collapse in">';
echo '<a class="sidebar-sub" href="acp.php?tn=pages&sub=list">'.$lang['page_list'].'</a>';
echo '<a class="sidebar-sub" href="acp.php?tn=pages&sub=new">'.$lang['new_page'].'</a>';
echo '<a class="sidebar-sub" href="acp.php?tn=pages&sub=snippets">'.$lang['snippets'].'</a>';
echo '<a class="sidebar-sub" href="acp.php?tn=pages&sub=rss">RSS</a>';
echo '</div>';
echo '</div>';


echo '<div class="panel panel-default">';
echo '<div class="panel-heading">';
echo '<a class="sidebar-nav dashboard-toggle" data-toggle="collapse" data-parent="#dashboard-collapse" href="#files">'.$lang['tn_filebrowser'].'<span class="tri-left"></span></a>';
echo '</div>';

echo '<div id="files" class="panel-collapse collapse">';
echo '<a class="sidebar-sub" href="acp.php?tn=filebrowser&sub=browse">'.$lang['manage_files'].'</a>';
echo '<a class="sidebar-sub" data-toggle="modal" data-target="#uploadModal" href="#">'.$lang['go_to_upload'].'</a>';
echo '</div>';
echo '</div>';


echo '<div class="panel panel-default">';
echo '<div class="panel-heading">';
echo '<a class="sidebar-nav dashboard-toggle" data-toggle="collapse" data-parent="#dashboard-collapse" href="#user">'.$lang['tn_usermanagement'].'<span class="tri-left"></span></a>';
echo '</div>';

echo '<div id="user" class="panel-collapse collapse">';
echo '<a class="sidebar-sub" href="acp.php?tn=user&sub=list">'.$lang['list_user'].'</a>';
echo '<a class="sidebar-sub" href="acp.php?tn=user&sub=new">'.$lang['new_user'].'</a>';
echo '<a class="sidebar-sub" href="acp.php?tn=user&sub=groups">'.$lang['edit_groups'].'</a>';
echo '</div>';
echo '</div>';


echo '<div class="panel panel-default">';
echo '<div class="panel-heading">';
echo '<a class="sidebar-nav dashboard-toggle" data-toggle="collapse" data-parent="#dashboard-collapse" href="#system">'.$lang['tn_system'].'<span class="tri-left"></span></a>';
echo '</div>';

echo '<div id="system" class="panel-collapse collapse">';
echo '<a class="sidebar-sub" href="acp.php?tn=system&sub=sys_pref">'.$lang['system_preferences'].'</a>';
echo '<a class="sidebar-sub" href="acp.php?tn=system&sub=stats">'.$lang['system_statistics'].'</a>';
echo '<a class="sidebar-sub" href="acp.php?tn=system&sub=backup">'.$lang['system_backup'].'</a>';
echo '<a class="sidebar-sub" href="acp.php?tn=system&sub=update">'.$lang['system_update'].'</a>';
echo '</div>';
echo '</div>';


echo '<div class="panel panel-default">';
echo '<div class="panel-heading">';
echo '<a class="sidebar-nav dashboard-toggle" data-toggle="collapse" data-parent="#dashboard-collapse" href="#moduls">'.$lang['tn_moduls'].'<span class="tri-left"></span></a>';
echo '</div>';

echo '<div id="moduls" class="panel-collapse collapse">';

$arr_iMods = get_all_moduls();
$nbrModuls = count($arr_iMods);

if($nbrModuls < 1) {
	echo '<a class="sidebar-sub" href="acp.php?tn=moduls">'.$lang['alert_no_modules'].'</a>';
}

for($i=0;$i<$nbrModuls;$i++) {
	$modFolder = $arr_iMods[$i]['folder'];
	unset($modnav);
	$mod_info_file = "../modules/$modFolder/info.inc.php";
		if(is_file("$mod_info_file")) {
			include("$mod_info_file");
			echo '<a class="sidebar-sub" href="acp.php?tn=moduls&sub='.$modFolder.'&a=start">'.$mod['name'].'</a>';
		}
}
echo '</div>';
echo '</div>';



$arr_lang = get_all_languages();
$current_lang_icon = '<img src="../lib/lang/'.$languagePack.'/flag.png" style="vertical-align: baseline; width:18px; height:auto;">';
echo '<div class="panel panel-default">';
echo '<div class="panel-heading">';
echo '<a class="sidebar-nav dashboard-toggle" data-toggle="collapse" data-parent="#dashboard-collapse" href="#lang">'.$current_lang_icon.' '.$lang['f_page_language'].'<span class="tri-left"></span></a>';
echo '</div>';

echo '<div id="lang" class="panel-collapse collapse">';

for($i=0;$i<count($arr_lang);$i++) {
	$lang_icon = '<img src="../lib/lang/'.$arr_lang[$i]['lang_folder'].'/flag.png" style="vertical-align: baseline; width:18px; height:auto;">';
	echo '<a class="sidebar-sub" href="acp.php?set_lang='.$arr_lang[$i]['lang_folder'].'">'.$lang_icon.' '.$arr_lang[$i]['lang_desc'].'</a>';
}

echo '</div>';
echo '</div>';

echo '</div>';










?>