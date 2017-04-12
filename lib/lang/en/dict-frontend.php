<?php

/*
# content:		language file | English
# package:		lib/lang/de/frontend/
# author:		Patrick Konstandin <support@flatfiler.de>
# last edit:	18.08.2010 | Patrick Konstandin

# Text Encoding Unicode (UTF8)
*/

/* Headlines */
$lang['legend_login']				= "Login";
$lang['legend_register']			= "Register";
$lang['legend_delete_account']		= "Delete Account";
$lang['legend_required_fields']		= "Required fields";
$lang['legend_optional_fields']		= "Optional Information";
$lang['legend_adress_fields']		= "Adress";
$lang['legend_access_data']			= "Login details";
$lang['legend_avatar']				= "Avatar";
$lang['legend_searchbox']			= "Search site";
$lang['legend_lastedit']			= "Last edit";
$lang['legend_mostclicked']			= "Most viewed";
$lang['legend_tags']				= "Tags";
$lang['legend_toc']					= "Contents";

$lang['headline_editprofile']		= "Edit Profile";
$lang['headline_searchresults']		= "Search";

$lang['title_404']					= "404 | Page not found";
$lang['msg_404'] 					= "Sorry, the page does not exist";

$lang['menu'] = 'Menu';

/* Labels */
$lang['label_login']			= "Login";
$lang['label_psw']				= "Password";
$lang['label_psw_repeat']		= "Confirm Password";
$lang['label_username'] 		= "Username";
$lang['label_lastname']			= "Lastname";
$lang['label_firstname'] 		= "Firstname";
$lang['label_mail'] 			= "E-Mail";
$lang['label_mailrepeat']		= "Confirm E-Mail";
$lang['label_street'] 			= "Street";
$lang['label_nr'] 				= "Nbr.";
$lang['label_zip'] 				= "Zip/Code";
$lang['label_town'] 			= "City";
$lang['label_tel'] 				= "Telephone";
$lang['label_fax'] 				= "Fax";
$lang['label_about_you'] 		= "About You (public)";

/* Buttons, Links */
$lang['button_login'] 			= "Login";
$lang['button_profile'] 		= "Edit Profile";
$lang['button_logout'] 			= "Logout";
$lang['button_save'] 			= "Save";
$lang['button_delete'] 			= "Delete";
$lang['button_send'] 			= "Send";
$lang['button_search'] 			= "Search";
$lang['button_send_register'] 	= "Submit Registration";
$lang['button_back'] 			= "back";
$lang['button_next'] 			= "forward";
$lang['button_top'] 			= "top";
$lang['button_acp'] 			= "Administration";
$lang['button_acp_edit_page'] = "Edit Page";
$lang['link_delete_avatar']		= "Delete Avatar";
$lang['link_register'] 			= "Register now ...";



/* Messages */
$lang['msg_missingfield'] 		= "All fields are required";
$lang['msg_login_false'] 		= "There are no matching user data available.";
$lang['msg_login_true'] 		= "Welcome";
$lang['msg_logout'] 		    = "Successfully logged out";
$lang['msg_register'] 		    = "Not registered? Here you can register.";

$lang['msg_edit_psw'] 		    = "Complete these fields only if the password should be changed.";
$lang['msg_edit_mail'] 		    = "Fill this field only if your e-mail address should be changed. After changing the e-mail address you will automatically receive an activation link sent to your new address.";
$lang['msg_invalid_mail_format'] = 'Invalid e-mail format';
$lang['msg_avatar'] 		    = "Displays a graphic next to each of your posts.";

$lang['msg_delete_account'] 	= "Here you can delete your account. Personal information is so irrevocably removed from the database.";

$lang['msg_register_error']    = "Errors occurred";
$lang['msg_register_accept']    = "You must accept the privacy policy.";
$lang['msg_register_requiredfields']    = "All required fields must be filled.";
$lang['msg_register_userchars']    = "The specified user name contains too much, not enough or illegal characters";
$lang['msg_register_existinguser']    = "The specified username is already taken or disallowed.";
$lang['msg_register_existingusermail']    = "The e-mail address is already taken.";
$lang['msg_register_mailrepeat_error']    = "The e-mail addresses do not match.";
$lang['msg_register_pswrepeat_error']    = "The passwords do not match.";
$lang['msg_register_success']    = "The user account was created and an activation link to the e-mail address sent.";
$lang['msg_register_admin_notification_subject']    = "New user";
$lang['msg_register_admin_notification_text']    = "A new user account has been created.";

$lang['msg_confirm_delete_account']    = "Do you really want to delete this user account?";
$lang['msg_delete_account_success']    = "The user account was removed from the database";
$lang['msg_delete_account_error']    = "An error has occurred, the account was not deleted.";

$lang['msg_register_intro'] 	= "Please fill out the form below. You will receive an email with an activation link you must click to activate your account.";
$lang['msg_register_intro_disabled'] 	= "There are no registrations possible.";
$lang['msg_register_outro'] 	= "Please confirm here that you have read our terms of use and accept it.";


$lang['msg_update_profile']   		 = "The user data have been updated";
$lang['msg_update_profile_error']    = "The user data could not be updated";
$lang['msg_upload_avatar_success']   = "The Avatar has been updated";
$lang['msg_upload_avatar_filesize']  = "The Avatar is too big";
$lang['msg_upload_avatar_filetype']  = "The image must be of type PNG";
$lang['msg_signed_out']				 = "Signed out with success";


$lang['msg_search_undersized']    = "The query must be at least 3 letters long.";
$lang['msg_search_results']    = "The query returned %d matches:";
$lang['msg_search_no_results']    = "Your search returned no results";


$lang['forgotten_psw']    = "Forgot your password?";
$lang['forgotten_psw_mail_subject']    = "Your access data";

$lang['msg_forgotten_psw_step1']    = "You can now reset your password. The required activation key was sent to Your e-mail address.";
$lang['msg_forgotten_psw_step2']    = "Your new password has been sent to your e-mail address.";

$lang['legend_ask_for_psw']			= "Reset Password";
$lang['forgotten_psw_intro']    = "Please enter in the following field the email address of your user account. You will receive an email with the necessary information on how to reset your password.";

$lang['forgotten_psw_mail_info']    = "Hello {USERNAME},<br />You want to reset your password? Just click on the link below.<br />Please ignore this e-mail if the request to reset your password, is not by you.<br /><br />Reset your Password:<br />{RESET_LINK}";


$lang['forgotten_psw_mail_update']    = "<p>Hello {USERNAME},<br />Your password has been reset.</p> <p>Your new password is:<br />{temp_psw}</p>";


?>