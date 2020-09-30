<?php
	
include 'dict-backend.php';

$lang['progress'] = 'Progress';
$lang['step'] = 'Step';
$lang['permission_false'] = 'File or Folder needs writing permissions';
$lang['permission_true'] = 'Permissions correct';
$lang['missing_folder'] = 'Missing Folder';
$lang['files_and_folders'] = 'Files and Folders';
$lang['system_requirements'] = 'System Requirements';
$lang['php_false'] = 'flatCore needs PHP';
$lang['php_true'] = 'Sufficiently PHP Version';
$lang['pdo_true'] = 'PDO/SQLite is ready';
$lang['pdo_false'] = 'PDO/SQLite needs to be activated';
$lang['username'] = 'Username';
$lang['email'] = 'E-Mail';
$lang['password'] = 'Password';
$lang['password_too_short'] = 'The Password is too short (at least 8 characters)';
$lang['msg_form'] = 'To start the installation flatCore needs an Username and a Password for an administrator.<br><strong>Pay attention</strong> to the correctness of the entries<br>Note: All information can later be complemented or changed.';
$lang['start_install'] = 'Start Installation';
$lang['installed'] = 'Installation was successfully';
$lang['link_home'] = 'Homepage';
$lang['link_admin'] = 'Administration';

$lang['db_host'] = 'Database Host';
$lang['db_host_help'] = 'In most cases this is localhost. If not, ask your hosting provider.';
$lang['db_port'] = 'Database Port';
$lang['db_port_help'] = 'Port 3306 is the default port for the classic MySQL protocol.';
$lang['db_name'] = 'Database Name';
$lang['db_name_help'] = '';
$lang['db_username'] = 'Username';
$lang['db_username_help'] = 'Possibly this is <i>root</i>. Or a username that was assigned to you by your webhosting provider.';
$lang['db_psw'] = 'Password';
$lang['db_psw_help'] = 'The password for your database';
$lang['db_prefix'] = 'Prefix';
$lang['db_psw_help'] = 'If you want to - or have - other installations in this database. <strong>Make sure that you can only assign a prefix once.</strong>';

$lang['db_sqlite_help'] = 'If you want to use SQLite, you do not need to provide any further information.';
?>