<?php

//prohibit unauthorized access
require("core/access.php");


echo"\n <form id='editpage' action='$_SERVER[PHP_SELF]?tn=pages&sub=edit&editpage=$editpage' method='POST'>\n";

// fancytabs
echo'<div id="tabsBlock">';

/* tab_info */
echo'<h4 title="' . $lang[tab_info_description] . '">' . $lang[tab_info] . '</h4>';

echo'<div class="tab-content">'; // tabs content

echo'<div class="form-line">
		<label>' . $lang[f_page_position] . '</label>
		<div class="form-controls">';
	
$dbh = new PDO("sqlite:".CONTENT_DB);
$sql = "SELECT page_linkname, page_sort FROM fc_pages
		    WHERE page_sort != 'portal'
		    ORDER BY page_sort ASC	";
$all_pages = $dbh->query($sql)->fetchAll();

$dbh = null;

echo'<select name="page_position">';

echo'<option value="null">' . $lang[legend_unstructured_pages] . '</option>';
echo'<option value="portal">' . $lang[f_homepage] . '</option>';

if($page_sort == "portal") {
	echo'<option value="portal" selected>' . $lang[f_homepage] . '</option>';
}

if(ctype_digit($page_sort)) {
	echo"<option value='mainpage' selected>$lang[f_mainpage]</option>";
} else {
	echo"<option value='mainpage'>$lang[f_mainpage]</option>";
}

echo"<optgroup label='$lang[f_subpage]'>";
for($i=0;$i<count($all_pages);$i++) {

	if($all_pages[$i][page_sort] == $page_sort) {
		continue;
	}
	
	if($all_pages[$i][page_sort] == "") {
		continue;
	}
	
	if($pos = strripos($page_sort,".")) {
		$string = substr($page_sort,0,$pos);
	}
		 
		 $parent_string = $all_pages[$i][page_sort];
		 
		 unset($selected);
		 if($parent_string != "" && $parent_string == "$string") {
		 	$selected = "selected";
		 }
		 
 
	$indent = str_repeat("-",substr_count($parent_string,'.'));
	echo"<option value='$parent_string' $selected> $indent " . $all_pages[$i][page_linkname] . "</option>";
	
}
echo'</optgroup>';
echo'</select>';
	
echo'</div></div>';

if($page_sort != "portal") {

$page_order = substr (strrchr ($page_sort, "."), 1);

if(ctype_digit($page_sort)) {
	$page_order = $page_sort;
}


echo"<div class='form-line'>
		<label>$lang[f_page_order]</label>
		<div class='form-controls'><input class='span2' type='text' name='page_order' value='$page_order'></div>
	</div>";
}
	
		
echo"<div class='form-line'>
		<label>$lang[f_page_linkname]</label>
		<div class='form-controls'><input class='span5' type='text' name='page_linkname' value='$page_linkname'></div>
	</div>";
		
echo"<div class='form-line'>
		<label>$lang[f_page_permalink]</label>
		<div class='form-controls'><input class='span5' type='text' name='page_permalink' value='$page_permalink'></div>
	</div>";

echo"<div class='form-line form-line-last'>
		<label>$lang[f_page_title]</label>
		<div class='form-controls'><input class='span5' type='text' name='page_title' value='$page_title'></div>
	</div>";


echo'</div>'; // eo tabs content


/* EOL tab_info ### ### ### */


/* tab_content */
echo"<h4 title='$lang[tab_content_description]'>$lang[tab_content]</h4>";

echo'<div class="tab-content">'; // tabs content

echo"<textarea name='page_content' class='mceEditor'>
	 $page_content
	 </textarea>";

echo"</div>";
/* EOL tab_content ### ### ### */


/* tab_extracontent */
echo"<h4 title='$lang[tab_extracontent_description]'>$lang[tab_extracontent]</h4>";

echo'<div class="tab-content">'; // tabs content

echo"<textarea name='page_extracontent' class='mceEditor_small'>
	 $page_extracontent
	 </textarea>";

echo"</div>";
/* EOL tab_extracontent ### ### ### */



/* tab_meta */
echo"<h4 title='$lang[tab_meta_description]'>$lang[tab_meta]</h4>";

echo'<div class="tab-content">'; // tabs content

if($page_meta_author == "") {
	$page_meta_author = "$_SESSION[user_firstname] $_SESSION[user_lastname]";
}


echo"<div class='form-line'>
		<label>$lang[f_meta_author]</label>
		<div class='form-controls'><input class='span5' type='text' name='page_meta_author' value='$page_meta_author'></div>
		</div>";
		
echo"<div class='form-line'>
		<label>$lang[f_meta_keywords]</label>
		<div class='form-controls'><input class='span5' type='text' name='page_meta_keywords' value='$page_meta_keywords'></div>
		</div>";

echo"<div class='form-line'>
			<label>$lang[f_meta_description]</label>
			<div class='form-controls'>
				<textarea name='page_meta_description' class='span5' rows='5'>$page_meta_description</textarea>
			</div>
		</div>";
		
echo"<div class='form-line form-line-last'>
		<label>$lang[f_meta_robots]</label>
		<div class='form-controls'>";

$selvar = "sel$page_meta_robots";
${$selvar} = "selected";

echo"<select name='page_meta_robots'>";
	echo"<option value='all' $selall>all</option>";
	echo"<option value='noindex' $selnoindex>noindex</option>";
	echo"<option value='nofollow' $selnofollow>nofollow</option>";
	echo"<option value='noodp' $selnoodp>noodp</option>";
	echo"<option value='noydir' $selnoydir>noydir</option>";
echo"</select>";
	
echo"	</div>
		</div>";



echo'</div>'; // eo tabs content

/* EOL tab_meta ### ### ### */



/* tab_head */
echo"<h4 title='$lang[tab_head_description]'>$lang[tab_head]</h4>";

echo'<div class="tab-content">'; // tabs content

echo"<div class='form-line'>
		<label>$lang[f_head_styles]</label>
		<div class='form-controls'>
			<span class='silent'>&lt;style type=&quot;text/css&quot;&gt;</span><br/>
				<textarea name='page_head_styles' class='span5' rows='6'>$page_head_styles</textarea><br />
			<span class='silent'>&lt;/styles&gt;</span>
		</div>
		</div>";

echo"	<div class='form-line form-line-last'>
		<label>$lang[f_head_enhanced]</label>
		<div class='form-controls'>
			<span class='silent'>&lt;head&gt;</span><br />
				<textarea name='page_head_enhanced' class='span5' rows='6'>$page_head_enhanced</textarea><br />
			<span class='silent'>&lt;/head&gt;</span>
		</div>
		</div>";

echo"<div style='clear:both;'></div>";

echo'</div>'; // eo tabs content

/* EOL tab_head ### ### ### */



/* tab_preferences */
echo"<h4 title='$lang[tab_page_preferences_description]'>$lang[tab_page_preferences]</h4>";

echo'<div class="tab-content">'; // tabs content



echo"<div class='form-line'>
		<label>$lang[f_page_language]</label>
		<div class='form-controls'>";

$arr_lang = get_all_languages();

echo"<select id='select_lang' name='page_language'>";

for($i=0;$i<count($arr_lang);$i++) {

	$selected = "";

	$lang_sign = $arr_lang[$i][lang_sign];
	$lang_desc = $arr_lang[$i][lang_desc];
	$lang_folder = $arr_lang[$i][lang_folder];

	if($lang_folder == "$page_language") {
		$selected = "selected";
	}

	echo"<option value='$lang_folder' $selected>$lang_sign ($lang_desc)</option>";

} // eo $i

echo"</select>";


echo"</div></div>"; // eo lang




echo"<div class='form-line'>
		<label>$lang[f_page_template]</label>
		<div class='form-controls'>";
		
$arr_Styles = get_all_templates();

echo"<select id='select_template' name='select_template'>";

if($page_template == "") {
	$selected_standard = "selected";
}

echo "<option value='use_standard<|-|>use_standard' $selected_standard>$lang[use_standard]</option>";

/* templates list */
foreach($arr_Styles as $template) {


$arr_layout_tpl = glob("../styles/$template/templates/layout*.tpl");

echo"<optgroup label='$template'>";

foreach($arr_layout_tpl as $layout_tpl) {
	$layout_tpl = basename($layout_tpl);


	$selected = "";
	if($template == "$page_template" && $layout_tpl == "$page_template_layout") {
		$selected = "selected";
	}


	
	echo "<option $selected value='$template<|-|>$layout_tpl'>$template » $layout_tpl</option>";
}

echo"</optgroup>";
    



} // eo foreach template list

echo"</select>";


echo"</div></div>";








echo"<div class='form-line'>
	<label>$lang[f_page_modul]</label>
	<div class='form-controls'>";



$arr_iMods = get_all_moduls();


echo"<select name='page_modul'>";

echo"<option value=''>Kein Modul</option>";

for($i=0;$i<count($arr_iMods);$i++) {

	$selected = "";

	$mod_name = $arr_iMods[$i][name];
	$mod_folder = $arr_iMods[$i][folder];

	if($mod_folder == "$page_modul") {
		$selected = "selected";
	}

	echo"<option value='$mod_folder' $selected>$mod_name</option>";

} // eo $i


echo"</select>";



echo"</div></div>";





unset($checked_status);

if($page_status == "") {
	$page_status = "public";
}

$checked_status[$page_status] = "checked"; 


echo"<div class='form-line'>
	<label>$lang[f_page_status]</label>
	<div class='form-controls'>";

echo"<input type='radio' name='page_status' value='public' $checked_status[public]> <span class='label label-success'>$lang[f_page_status_puplic]</span> <br />";
echo"<input type='radio' name='page_status' value='private' $checked_status[private]> <span class='label label-important'>$lang[f_page_status_private]</span><br />";
echo"<input type='radio' name='page_status' value='draft' $checked_status[draft]> <span class='label'>$lang[f_page_status_draft]</span>";

echo"</div></div>";





echo"<div class='form-line'>
	<label>$lang[choose_usergroup]</label>
	<div class='form-controls'>";

$arr_groups = get_all_groups();
$arr_checked_groups = explode(",",$page_usergroup);

for($i=0;$i<count($arr_groups);$i++) {

	$group_id = $arr_groups[$i][group_id];
	$group_name = $arr_groups[$i][group_name];

if(in_array("$group_name", $arr_checked_groups)) {
	$checked = "checked";
} else {
	$checked = "";
}
	
	
	echo"<input type='checkbox' $checked name='set_usergroup[]' value='$group_name'> $group_name <br />";

} // eo $i


echo"</div>"; // .sectorRight
echo"</div>"; // .line




echo"<div class='form-line form-line-last'>
	<label>$lang[f_page_authorized_admins]</label>
	<div class='form-controls'>";

$arr_admins = get_all_admins();

$arr_checked_admins = explode(",", $page_authorized_users);

$cnt_admins = count($arr_admins);


for($i=0;$i<$cnt_admins;$i++) {

	$user_nick = $arr_admins[$i][user_nick];

    if(in_array("$user_nick", $arr_checked_admins)) {
			$checked_user = "checked";
		} else {
			$checked_user = "";
		}
	
 	echo"<input type='checkbox' $checked_user name='set_authorized_admins[]' value='$user_nick'> $user_nick<br />";

}



echo"</div>"; // .sectorRight
echo"</div>"; // .line



echo'</div>'; // eo tabs content

/* EOL tab_preferences ### ### ### */





$custom_fields = get_custom_fields();
sort($custom_fields);
$cnt_result = count($custom_fields);

if($cnt_result > 0) {

/* tab custom fields */
echo"<h4 title='$lang[legend_custom_fields]'>$lang[legend_custom_fields]</h4>";

echo'<div class="tab-content">'; // tabs content


	for($i=0;$i<$cnt_result;$i++) {
	
		

		if(substr($custom_fields[$i],0,10) == "custom_one") {
			$label = substr($custom_fields[$i],11);
			echo "<div class='form-line'>";
			echo "<label>$label</label>";
			echo "<div class='form-controls'><input type='text' class='span5' name='$custom_fields[$i]' value='" . $$custom_fields[$i] . "'></div>";
			echo "</div>";
		}
		
		if(substr($custom_fields[$i],0,11) == "custom_text") {
			$label = substr($custom_fields[$i],12);
			echo "<div class='form-line'>";
			echo "<label>$label</label>";
			echo "<div class='form-controls'><textarea class='span5' rows='4' name='$custom_fields[$i]'>" . $$custom_fields[$i] . "</textarea></div>";
			echo "</div>";
		}		
		
		if(substr($custom_fields[$i],0,14) == "custom_wysiwyg") {
		$label = substr($custom_fields[$i],15);
			echo "<div class='form-line'>";
			echo "<label>$label</label>";
			echo "<div class='form-controls'><textarea class='mceEditor_small' name='$custom_fields[$i]'>" . $$custom_fields[$i] . "</textarea></div>";
			echo "</div>";
		}		
		

	}



echo'</div>'; // eo tabs content

/* EOL tab custom fields ### ### ### */

}








echo"</div>"; // EOL fancytabs



//submit form to save data

if($page_sort == "portal") {
	// It's not a good idea to delete the portal
	// unset($delete_button);
}

echo"<div class='formfooter'>";
echo"<input type='hidden' name='page_version' value='$page_version'>";
echo"<div style='float:right;'>$submit_button $previev_button</div> $delete_button";
echo"<div style='clear:both;'></div>";
echo"</div>";



echo"</form>";



?>