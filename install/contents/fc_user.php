<?php

$database = "user";
$table_name = "fc_user";

$cols = array(
  "user_id"  => 'INTEGER(12) NOT NULL PRIMARY KEY AUTO_INCREMENT',
  "user_class"  => "VARCHAR(50) NOT NULL DEFAULT ''",
  "user_nick"  => "VARCHAR(50) NOT NULL DEFAULT ''",
  "user_psw" => "VARCHAR(255) NOT NULL DEFAULT ''",
  "user_psw_hash" => "VARCHAR(255) NOT NULL DEFAULT ''",
  "user_groups" => "VARCHAR(255) NOT NULL DEFAULT ''",
  "user_avatar" => "VARCHAR(50) NOT NULL DEFAULT ''",
  "user_mail" => "VARCHAR(100) NOT NULL DEFAULT ''",
  "user_mail_p" => "VARCHAR(20) NOT NULL DEFAULT ''",
  "user_url" => "VARCHAR(100) NOT NULL DEFAULT ''",
  "user_icq" => "VARCHAR(20) NOT NULL DEFAULT ''",
  "user_icq_p" => "VARCHAR(50) NOT NULL DEFAULT ''",
  "user_aim" => "VARCHAR(20) NOT NULL DEFAULT ''",
  "user_aim_p" => "VARCHAR(20) NOT NULL DEFAULT ''",
  "user_msn" => "VARCHAR(20) NOT NULL DEFAULT ''",
  "user_msn_p" => "VARCHAR(20) NOT NULL DEFAULT ''",
  "user_skype" => "VARCHAR(20) NOT NULL DEFAULT ''",
  "user_skype_p" => "VARCHAR(20) NOT NULL DEFAULT ''",
  "user_jabber" => "VARCHAR(20) NOT NULL DEFAULT ''",
  "user_jabber_p" => "VARCHAR(20) NOT NULL DEFAULT ''",
  "user_yahoo" => "VARCHAR(20) NOT NULL DEFAULT ''",
  "user_yahoo_p" => "VARCHAR(20) NOT NULL DEFAULT ''",
  "user_tel" => "VARCHAR(20) NOT NULL DEFAULT ''",
  "user_tel_p" => "VARCHAR(20) NOT NULL DEFAULT ''",
  "user_fax" => "VARCHAR(20) NOT NULL DEFAULT ''",
  "user_fax_p" => "VARCHAR(20) NOT NULL DEFAULT ''",
  "user_registerdate" => "VARCHAR(25) NOT NULL DEFAULT ''",
  "user_verified" => "VARCHAR(25) NOT NULL DEFAULT ''",
  "user_drm" => "VARCHAR(255) NOT NULL DEFAULT ''",
  "user_firstname" => "VARCHAR(50) NOT NULL DEFAULT ''",
  "user_lastname" => "VARCHAR(50) NOT NULL DEFAULT ''",
  "user_company" => "VARCHAR(50) NOT NULL DEFAULT ''",
  "user_street" => "VARCHAR(50) NOT NULL DEFAULT ''",
  "user_street_nbr" => "VARCHAR(20) NOT NULL DEFAULT ''",
  "user_zipcode" => "VARCHAR(20) NOT NULL DEFAULT ''",
  "user_city" => "VARCHAR(50) NOT NULL DEFAULT ''",
  "user_newsletter" => "VARCHAR(20) NOT NULL DEFAULT ''",
  "user_activationkey" => "VARCHAR(255) NOT NULL DEFAULT ''",
  "user_reset_psw" => "VARCHAR(255) NOT NULL DEFAULT ''",
  "user_public_profile" => "VARCHAR(500) NOT NULL DEFAULT ''"
  
  );

?>