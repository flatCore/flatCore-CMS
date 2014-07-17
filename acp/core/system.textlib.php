<?php

//prohibit unauthorized access
require("core/access.php");

$sel_text = 'extra_content_text';
$sel_language = 'de';

if(isset($_POST['sel_text'])) {
	$sel_text = $_POST['sel_text'];
}

if(isset($_POST['sel_language'])) {
	$sel_language = $_POST['sel_language'];
}

/* Save Textsnippet */
if(isset($_POST['save_text'])) {

	// connect to database
	$db = new PDO("sqlite:".CONTENT_DB);
	
	if($_POST['modus'] == 'update_textlib') {
	
		$sql = "UPDATE fc_textlib
						SET textlib_content = :textlib_content
						WHERE textlib_name = :textlib_name AND textlib_lang = :textlib_lang ";
	
	} else {
		
		$sql = "INSERT INTO fc_textlib
						(textlib_content, textlib_name, textlib_lang)
						VALUES
						(:textlib_content, :textlib_name, :textlib_lang )";		
	}
	
	$sth = $db->prepare($sql);
	$sth->bindParam(':textlib_content', $_POST['textlib_content'], PDO::PARAM_STR);
	$sth->bindParam(':textlib_name', $sel_text, PDO::PARAM_STR);
	$sth->bindParam(':textlib_lang', $sel_language, PDO::PARAM_STR);
	
	$cnt_changes = $sth->execute();
	
	$db = null;
	
	
	if($cnt_changes == TRUE) {
		$sys_message = "{OKAY} $lang[db_changed] ($sel_text | $sel_language)";
		record_log("$_SESSION[user_nick]","edit textlib <b>$sel_text</b>","0");
	} else {
		$sys_message = "{ERROR} $lang[db_not_changed] ($text)";
	}
	
	print_sysmsg("$sys_message");

} // eol save text



switch ($sel_text) {

	case 'agreement_text':
		$selected2 = "selected";
		$desc = $lang['txtlib_agreement_desc'];
		break;

	case 'extra_content_text':
		$selected3 = "selected";
		$desc = $lang['txtlib_extra_content_desc'];
		break;

	case 'footer_text':
		$selected4 = "selected";
		$desc = $lang['txtlib_page_footer_desc'];
		break;

	case 'account_confirm':
		$selected5 = "selected";
		$desc = $lang['txtlib_account_confirm_mail_desc'];
		break;
		
	case 'account_confirm_mail':
		$selected6 = "selected";
		$desc = $lang['txtlib_account_confirm_mail_desc'];
		break;
		
	case 'no_access':
		$selected7 = "selected";
		$desc = $lang['txtlib_no_access_desc'];
		break;

}

$arr_lang = get_all_languages();
show_editor_switch($tn,$sub);

echo '<fieldset style="margin-bottom:0;">';
echo '<legend>'.$lang['system_textlib'].'</legend>';
echo '<form action="'.$_SERVER['PHP_SELF'].'?tn=system&sub=sys_textlib" method="POST" name="sel_snippet" class="form-">';



$select_textlib_language  = '<select name="sel_language" class="form-control">';
for($i=0;$i<count($arr_lang);$i++) {
	$lang_sign = $arr_lang[$i]['lang_sign'];
	$lang_desc = $arr_lang[$i]['lang_desc'];
	$lang_folder = $arr_lang[$i]['lang_folder'];
	$select_textlib_language .= "<option value='$lang_folder'".($_POST['sel_language'] == "$lang_folder" ? 'selected="selected"' :'').">$lang_sign</option>";	
}
$select_textlib_language .= '</select>';


echo '<div class="row">';

echo '<div class="col-md-3">';

echo '<select name="sel_text" class="form-control">';

echo "<option value='agreement_text' $selected2>$lang[txtlib_agreement]</option>";
echo "<option value='extra_content_text' $selected3>$lang[txtlib_extra_content]</option>";
echo "<option value='footer_text' $selected4>$lang[txtlib_page_footer]</option>";
echo "<option value='account_confirm' $selected5>$lang[txtlib_account_confirm]</option>";
echo "<option value='account_confirm_mail' $selected6>$lang[txtlib_account_confirm_mail]</option>";
echo "<option value='no_access' $selected7>$lang[txtlib_no_access]</option>";

echo '</select> ';

echo '</div>';

echo '<div class="col-md-2">';
echo $select_textlib_language;
echo '</div>';

echo '<div class="col-md-2">';
echo '<input type="submit" class="btn btn-default btn-block" name="sel_snippet" value="'.$lang['edit'].'">';
echo '</div>';

echo '<div class="col-md-5">';
echo '<div class="alert alert-info">'. $desc .'</div>';
echo '</div>';

echo '</div>'; // row

echo"</form>";
echo"</fieldset>";

/* open selected text */


$dbh = new PDO("sqlite:".CONTENT_DB);

$sql = "SELECT * FROM fc_textlib WHERE textlib_name = :textlib_name AND textlib_lang = :textlib_lang ";

$sth = $dbh->prepare($sql);
$sth->bindParam(':textlib_name', $sel_text, PDO::PARAM_STR);
$sth->bindParam(':textlib_lang', $sel_language, PDO::PARAM_STR);
$sth->execute();
$textlibData = $sth->fetch(PDO::FETCH_ASSOC);
$dbh = null;


foreach($textlibData as $k => $v) {
   $$k = stripslashes($v);
}




echo '<form action="'.$_SERVER['PHP_SELF'].'?tn=system&sub=sys_textlib" method="POST">';

echo '<textarea class="'.$editor_class.'" id="textEditor" name="textlib_content">'.$textlib_content.'</textarea>';
echo '<input type="hidden" name="sel_text" value="'.$sel_text.'">';
echo '<input type="hidden" name="sel_language" value="'.$sel_language.'">';
if(!is_array($textlibData)) {
	echo '<input type="hidden" name="modus" value="new_textlib">';
} else {
	echo '<input type="hidden" name="modus" value="update_textlib">';
}
echo '<div class="formfooter"><input type="submit" name="save_text" class="btn btn-success" value="'.$lang['save'].'"></div>';
echo '</form>';



?>