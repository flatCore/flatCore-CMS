<?php

//prohibit unauthorized access
require("core/access.php");

echo '<div class="accordion" id="dashboard-collapse">';

echo '<div class="accordion-group">';
echo '<div class="accordion-heading">';
echo '<a class="accordion-toggle" data-toggle="collapse" data-parent="#dashboard-collapse" href="#pages">'.$lang['tn_pages'].'</a>';
echo '</div>';

echo '<div id="pages" class="accordion-body collapse in">';
echo "<a class='submenu' href='$_SERVER[PHP_SELF]?tn=pages&sub=list'>$lang[page_list]</a>";
echo "<a class='submenu' href='$_SERVER[PHP_SELF]?tn=pages&sub=new'>$lang[new_page]</a>";
echo "<a class='submenu' href='$_SERVER[PHP_SELF]?tn=pages&sub=snippets'>$lang[snippets]</a>";
echo "<a class='submenu' href='$_SERVER[PHP_SELF]?tn=pages&sub=rss'>RSS</a>";
echo '</div>';
echo '</div>';


echo '<div class="accordion-group">';
echo '<div class="accordion-heading">';
echo '<a class="accordion-toggle" data-toggle="collapse" data-parent="#dashboard-collapse" href="#files">'.$lang['tn_filebrowser'].'</a>';
echo '</div>';

echo '<div id="files" class="accordion-body collapse">';
echo "<a class='submenu' href='$_SERVER[PHP_SELF]?tn=filebrowser&sub=browse'>$lang[manage_files]</a>";
echo "<a class='submenu' href='$_SERVER[PHP_SELF]?tn=filebrowser&sub=upload'>$lang[go_to_upload]</a>";
echo '</div>';
echo '</div>';


echo '<div class="accordion-group">';
echo '<div class="accordion-heading">';
echo '<a class="accordion-toggle" data-toggle="collapse" data-parent="#dashboard-collapse" href="#user">'.$lang['tn_usermanagement'].'</a>';
echo '</div>';

echo '<div id="user" class="accordion-body collapse">';
echo "<a class='submenu' href='$_SERVER[PHP_SELF]?tn=user&sub=list'>$lang[list_user]</a>";
echo "<a class='submenu' href='$_SERVER[PHP_SELF]?tn=user&sub=new'>$lang[new_user]</a>";
echo "<a class='submenu' href='$_SERVER[PHP_SELF]?tn=user&sub=groups'>$lang[edit_groups]</a>";
echo '</div>';
echo '</div>';


echo '<div class="accordion-group">';
echo '<div class="accordion-heading">';
echo '<a class="accordion-toggle" data-toggle="collapse" data-parent="#dashboard-collapse" href="#system">'.$lang['tn_system'].'</a>';
echo '</div>';

echo '<div id="system" class="accordion-body collapse">';
echo "<a class='submenu' href='$_SERVER[PHP_SELF]?tn=system&sub=sys_pref'>$lang[system_preferences]</a>";
echo "<a class='submenu' href='$_SERVER[PHP_SELF]?tn=system&sub=sys_textlib'>$lang[system_textlib]</a>";
echo "<a class='submenu' href='$_SERVER[PHP_SELF]?tn=system&sub=stats'>$lang[system_statistics]</a>";
echo "<a class='submenu' href='$_SERVER[PHP_SELF]?tn=system&sub=backup'>$lang[system_backup]</a>";
echo '</div>';
echo '</div>';


echo '<div class="accordion-group">';
echo '<div class="accordion-heading">';
echo '<a class="accordion-toggle" data-toggle="collapse" data-parent="#dashboard-collapse" href="#moduls">'.$lang['tn_moduls'].'</a>';
echo '</div>';

echo '<div id="moduls" class="accordion-body collapse">';

$arr_iMods = get_all_moduls();
$nbrModuls = count($arr_iMods);


for($i=0;$i<$nbrModuls;$i++) {

	$modFolder = $arr_iMods[$i][folder];
	unset($modnav);
	$mod_info_file = "../modules/$modFolder/info.inc.php";
		if(is_file("$mod_info_file")) {
			include("$mod_info_file");
			echo "<a class='submenu' href='$_SERVER[PHP_SELF]?tn=moduls&sub=$modFolder&a=start'>$mod[name]</a>";
		}
}
echo '</div>';
echo '</div>';



echo '<div class="accordion-group">';
echo '<div class="accordion-heading">';
echo '<a class="accordion-toggle" data-toggle="collapse" data-parent="#dashboard-collapse" href="#lang">'.$lang['f_page_language'].'</a>';
echo '</div>';

echo '<div id="lang" class="accordion-body collapse">';
$arr_lang = get_all_languages();

for($i=0;$i<count($arr_lang);$i++) {

	$lang_sign = $arr_lang[$i]['lang_sign'];
	$lang_desc = $arr_lang[$i]['lang_desc'];
	$lang_folder = $arr_lang[$i]['lang_folder'];

	echo "<a class='submenu'href='$_SERVER[PHP_SELF]?set_lang=$lang_folder'>$lang_desc</a>";

}
echo '</div>';
echo '</div>';





echo "</div>";





?>