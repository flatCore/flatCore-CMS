<?php
	
include 'dict-backend.php';

$lang['progress'] = 'Fortschritt';
$lang['step'] = 'Stufe';
$lang['next_step'] = 'nächster Schritt';
$lang['prev_step'] = 'zurück';
$lang['btn_check_system'] = 'System-Check';
$lang['btn_enter_user'] = 'Benutzer anlegen';
$lang['btn_enter_page_infos'] = 'Seiten Infos';
$lang['btn_enter_database'] = 'Datenbank';

$lang['label_add_user'] = 'Benutzerdaten angeben';
$lang['description_add_user'] = 'Gib die gewünschten Daten für einen ersten Benutzeraccount ein. Merke Dir diese Daten gut - mit diesen Daten kannst Du Dich später im ACP anmelden.';

$lang['label_add_page_data'] = 'Informationen zu Deiner neuen Seite';
$lang['description_add_page_data'] = 'Hier kannst Du die ersten Informationen zu Deiner Seite angeben. Alle Angaben kannst Du natürlich später im ACP ändern oder ergänzen.';

$lang['permission_false'] = 'Datei/Verzeichnis benötigt Schreibrechte!';
$lang['permission_true'] = 'Datei/Verzeichnis ist beschreibbar';
$lang['missing_folder'] = 'Das Verzeichnis fehlt';
$lang['files_and_folders'] = 'Dateien und Verzeichnisse';
$lang['system_requirements'] = 'Systemvoraussetzungen';
$lang['php_false'] = 'flatCore benötigt mindestens PHP';
$lang['php_true'] = 'PHP Version ausreichend';
$lang['pdo_true'] = 'PDO/SQLite ist installiert';
$lang['pdo_false'] = 'PDO/SQLite muss installiert/aktiviert werden';
$lang['username'] = 'Username';
$lang['email'] = 'E-Mail';
$lang['password'] = 'Passwort';
$lang['password_help_text'] = 'Das Passwort muss mind. 8 Zeichen enthalten';
$lang['msg_form'] = 'Um die Installation zu starten benötigt flatCore die Zugangsdaten für einen Administrator.<br><strong>WICHTIG!</strong> Auf die Korrektheit der Eingaben achten!<br>Alle Angaben können später noch ergänzt bzw. geändert werden.';
$lang['start_install'] = 'Installation starten';
$lang['installed'] = 'Installation erfolgt';
$lang['link_home'] = 'Zur Startseite';
$lang['link_admin'] = 'Zur Administration';

$lang['db_host'] = 'Datenbank Host';
$lang['db_host_help'] = 'In den meisten Fällen ist das localhost. Falls nicht, frage bei Deinem Webhoster nach.';
$lang['db_port'] = 'Datenbank Port';
$lang['db_port_help'] = '3306 ist der Standardport für das klassische MySQL-Protokoll';
$lang['db_name'] = 'Datenbank Name';
$lang['db_name_help'] = '';
$lang['db_username'] = 'Benutzername';
$lang['db_username_help'] = 'Eventuell ist das <i>root</i>. Oder ein Benutzername welcher Dir von Deinem Webhoster zugeteilt wurde.';
$lang['db_psw'] = 'Passwort';
$lang['db_psw_help'] = 'Das Passwort für Deine Datenbank';
$lang['db_prefix'] = 'Prefix';
$lang['db_prefix_help'] = 'Falls Du noch weitere Installationen in dieser Datenbank installieren möchtest - oder hast. <strong>Achte darauf, dass Du ein Prefix nur einmal vergeben kannst.</strong>';

$lang['label_database'] = 'Datenbank';

$lang['db_sqlite_help'] = 'Falls Du SQLite als Datenbank nutzen möchtest, brauchst Du keine weiteren Angaben mehr machen.';

$lang['check_connection'] = 'Verbindung prüfen';

?>