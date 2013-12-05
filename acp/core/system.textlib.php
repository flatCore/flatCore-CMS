<?php

//prohibit unauthorized access
require("core/access.php");



$text = strip_tags($_REQUEST[text]);



/*
SAVE TEXT
*/
if(isset($_POST['save_text'])) {


	// connect to database
	$db = new PDO("sqlite:".CONTENT_DB);
	
	$sql = "UPDATE fc_textlib
					SET textlib_content = :new_text
					WHERE textlib_name = :text ";
	
	$sth = $db->prepare($sql);
	$sth->bindParam(':new_text', $_POST[new_text], PDO::PARAM_STR);
	$sth->bindParam(':text', $text, PDO::PARAM_STR);
	
	$cnt_changes = $sth->execute();
	
	$db = null;
	
	
	if($cnt_changes == TRUE) {
		$sys_message = "{OKAY} $lang[db_changed] ($text)";
		record_log("$_SESSION[user_nick]","edit textlib $text","0");
	} else {
		$sys_message = "{ERROR} $lang[db_not_changed] ($text)";
	}
	
	print_sysmsg("$sys_message");

} // eol save text



if($text == "") {
	$text = "extra_content_text";
}



switch ($text) {

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

} // eo switch $text


show_editor_switch($tn,$sub);

echo"<fieldset>";
echo"<legend>$lang[system_textlib]</legend>";
echo"<form action='$_SERVER[PHP_SELF]?tn=system&sub=sys_textlib' method='POST' name='sel_snippet' class='form-inline'>";


echo '<div class="row">';
echo '<div class="col-md-4">';
echo '<div class="form-group">';
echo '<select name="text" class="form-control">';

echo "<option value='agreement_text' $selected2>$lang[txtlib_agreement]</option>";
echo "<option value='extra_content_text' $selected3>$lang[txtlib_extra_content]</option>";
echo "<option value='footer_text' $selected4>$lang[txtlib_page_footer]</option>";
echo "<option value='account_confirm' $selected5>$lang[txtlib_account_confirm]</option>";
echo "<option value='account_confirm_mail' $selected6>$lang[txtlib_account_confirm_mail]</option>";
echo "<option value='no_access' $selected7>$lang[txtlib_no_access]</option>";

echo '</select> ';
echo '</div>';

echo ' <input type="submit" class="btn btn-default" name="sel_snippet" value="'.$lang['edit'].'">';

echo '</div>';
echo '<div class="col-md-8">';

echo"<p><i class='icon-info-sign'></i> $desc</p>";
echo '</div>';

echo '</div>'; // gridcontainer

echo"</form>";
echo"</fieldset>";

/* open selected text */


$dbh = new PDO("sqlite:".CONTENT_DB);

$sql = "SELECT * FROM fc_textlib WHERE textlib_name = '$text' ";

$result = $dbh->query($sql);
$result= $result->fetch(PDO::FETCH_ASSOC);

$dbh = null;


foreach($result as $k => $v) {
   $$k = stripslashes($v);
}




echo"<form action='$_SERVER[PHP_SELF]?tn=system&sub=sys_textlib' method='POST'>";

echo"<textarea class='$editor_class' id='textEditor' name='new_text'>$textlib_content</textarea>";
echo"<input type='hidden' name='text' value='$text'>";
echo"<div class='formfooter'><input type='submit' name='save_text' class='btn btn-success' value='$lang[save]'></div>";
echo"</form>";



?>