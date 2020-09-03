<?php

//prohibit unauthorized access
require 'core/access.php';



foreach($_POST as $key => $val) {
	$$key = @htmlspecialchars($val, ENT_QUOTES); 
}


if(isset($_POST['save_prefs_contacts'])) {
	
	$data = $db_content->update("fc_preferences", [
		"prefs_mailer_adr" =>  $prefs_mailer_adr,
		"prefs_mailer_name" => $prefs_mailer_name,
		"prefs_mailer_type" => $_POST['prefs_mailer_type'],
		"prefs_smtp_host" => $_POST['prefs_smtp_host'],
		"prefs_smtp_port" => $_POST['prefs_smtp_port'],
		"prefs_smtp_encryption" => $_POST['prefs_smtp_encryption'],
		"prefs_smtp_username" => $_POST['prefs_smtp_username'],
		"prefs_smtp_psw" => $_POST['prefs_smtp_psw']
	], [
	"prefs_id" => 1
	]);

}


if(isset($_POST)) {
	/* read the preferences again */
	$fc_preferences = get_preferences();
	
	foreach($fc_preferences as $k => $v) {
	   $$k = stripslashes($v);
	}
}



echo '<fieldset>';
echo '<legend>System E-Mail</legend>';
echo '<form action="acp.php?tn=system&sub=mail" method="POST" class="form-horizontal">';

if($prefs_mailer_type == '') {
	$prefs_mailer_type = 'mail';
}

$prefs_mail_name_input = "<input class='form-control' type='text' name='prefs_mailer_name' value='$prefs_mailer_name'>";
$prefs_mail_adr_input = "<input class='form-control' type='text' name='prefs_mailer_adr' value='$prefs_mailer_adr'>";
$prefs_mail_smtp_host_input = "<input class='form-control' type='text' name='prefs_smtp_host' value='$prefs_smtp_host'>";
$prefs_mail_smtp_port_input = "<input class='form-control' type='text' name='prefs_smtp_port' value='$prefs_smtp_port'>";
$prefs_mail_smtp_encryption_input = "<input class='form-control' type='text' name='prefs_smtp_encryption' value='$prefs_smtp_encryption'>";
$prefs_mail_smtp_username_input = "<input class='form-control' type='text' name='prefs_smtp_username' value='$prefs_smtp_username'>";
$prefs_mail_smtp_psw_input = "<input class='form-control' type='password' name='prefs_smtp_psw' value='$prefs_smtp_psw'>";

$prefs_mail_type_input = '<div class="form-check">';
$prefs_mail_type_input .= '<input type="radio" class="form-check-input" id="mail" name="prefs_mailer_type" value="mail" '.($prefs_mailer_type == "mail" ? 'checked' :'').'>';
$prefs_mail_type_input .= '<label class="form-check-label" for="mail">'.$lang['prefs_mail_type_mail'].'</label>';
$prefs_mail_type_input .= '</div>';
$prefs_mail_type_input .= '<div class="form-check">';
$prefs_mail_type_input .= '<input type="radio" class="form-check-input" id="smtp" name="prefs_mailer_type" value="smtp" '.($prefs_mailer_type == "smtp" ? 'checked' :'').'>';
$prefs_mail_type_input .= '<label class="form-check-label" for="smtp">'.$lang['prefs_mail_type_smtp'].'</label>';
$prefs_mail_type_input .= '</div>';



echo tpl_form_control_group('',$lang['prefs_mailer_name'],$prefs_mail_name_input);
echo tpl_form_control_group('',$lang['prefs_mailer_adr'],$prefs_mail_adr_input);

echo $prefs_mail_type_input;

echo tpl_form_control_group('','','<p>SMTP</p>');

echo '<div class="row">';
echo '<div class="col-md-4">';
echo tpl_form_control_group('',$lang['prefs_mailer_smtp_host'],$prefs_mail_smtp_host_input);
echo '</div>';
echo '<div class="col-md-4">';
echo tpl_form_control_group('',$lang['prefs_mailer_smtp_port'],$prefs_mail_smtp_port_input);
echo '</div>';
echo '<div class="col-md-4">';
echo tpl_form_control_group('',$lang['prefs_mailer_smtp_encryption'],$prefs_mail_smtp_encryption_input);
echo '</div>';
echo '</div>';
echo '<div class="row">';
echo '<div class="col-md-6">';
echo tpl_form_control_group('',$lang['prefs_mailer_smtp_username'],$prefs_mail_smtp_username_input);
echo '</div>';
echo '<div class="col-md-6">';
echo tpl_form_control_group('',$lang['prefs_mailer_smtp_password'],$prefs_mail_smtp_psw_input);
echo '</div>';
echo '</div>';

echo '<input type="submit" class="btn btn-save" name="save_prefs_contacts" value="'.$lang['save'].'">';
echo '<input  type="hidden" name="csrf_token" value="'.$_SESSION['token'].'">';
echo '</form>';

echo '<div class="mt-3">';
if($prefs_mailer_adr != '') {
	echo '<a href="acp.php?tn=system&sub=mail&sendtest=1#mails" class="btn btn-fc btn-sm">'.$lang['prefs_mailer_send_test'].' ('.$prefs_mailer_adr.')</a>';
}


if($_GET['sendtest'] == 1) {
	require_once'../lib/Swift/lib/swift_required.php';
	
	if($prefs_mailer_type == 'smtp') {
		
		$trans = Swift_SmtpTransport::newInstance()
            ->setUsername("$prefs_smtp_username")
            ->setPassword("$prefs_smtp_psw")
            ->setHost("$prefs_smtp_host")
            ->setPort($prefs_smtp_port);
			
		if($prefs_mail_smtp_encryption_input != '') {
			$trans->setEncryption($prefs_smtp_encryption);
		}
	} else {
		$trans = Swift_MailTransport::newInstance();
	}
	

	$mailer = Swift_Mailer::newInstance($trans);
	$message = Swift_Message::newInstance('flatCore Test')
			->setFrom(array($prefs_mailer_adr => $prefs_mailer_name))
			->setTo(array($prefs_mailer_adr => $prefs_mailer_name))
			->setBody("flatCore Test (via $prefs_mailer_type)");
			
	if(!$mailer->send($message, $failures)) {
		echo '<div class="alert alert-danger mt-3">';
	  echo 'Failures:<br>';
	  print_r($failures);
	  echo '</div>';
	} else {
		echo '<p class="alert alert-success mt-3">'.$icon['check'].' '.$lang['prefs_mailer_send_test_success'].'</p>';
	}
	
}

echo '</div>';


echo '</fieldset>';



?>