<?php

$database = "user";
$table_name = "fc_user";

$cols = array(
  "user_id"  => 'INTEGER NOT NULL PRIMARY KEY',
  "user_class"  => 'VARCHAR',
  "user_nick"  => 'VARCHAR',
  "user_psw" => 'VARCHAR',
  "user_psw_hash" => 'VARCHAR',
  "user_groups" => 'VARCHAR',
  "user_avatar" => 'VARCHAR',
  "user_mail" => 'VARCHAR',
  "user_mail_p" => 'VARCHAR',
  "user_url" => 'VARCHAR',
  "user_icq" => 'VARCHAR',
  "user_icq_p" => 'VARCHAR',
  "user_aim" => 'VARCHAR',
  "user_aim_p" => 'VARCHAR',
  "user_msn" => 'VARCHAR',
  "user_msn_p" => 'VARCHAR',
  "user_skype" => 'VARCHAR',
  "user_skype_p" => 'VARCHAR',
  "user_jabber" => 'VARCHAR',
  "user_jabber_p" => 'VARCHAR',
  "user_yahoo" => 'VARCHAR',
  "user_yahoo_p" => 'VARCHAR',
  "user_tel" => 'VARCHAR',
  "user_tel_p" => 'VARCHAR',
  "user_fax" => 'VARCHAR',
  "user_fax_p" => 'VARCHAR',
  "user_registerdate" => 'VARCHAR',
  "user_verified" => 'VARCHAR',
  "user_drm" => 'VARCHAR',
  "user_firstname" => 'VARCHAR',
  "user_lastname" => 'VARCHAR',
  "user_company" => 'VARCHAR',
  "user_street" => 'VARCHAR',
  "user_street_nbr" => 'VARCHAR',
  "user_zipcode" => 'VARCHAR',
  "user_city" => 'VARCHAR',
  "user_newsletter" => 'VARCHAR',
  "user_activationkey" => 'VARCHAR',
  "user_reset_psw" => 'VARCHAR',
  "user_public_profile" => 'VARCHAR'
  
  );

?>