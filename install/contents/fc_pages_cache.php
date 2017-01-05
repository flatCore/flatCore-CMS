<?php

/**
 * must be a duplicate of fc_pages.php
 * except $table_name and columns 'page_id_original', 'page_cache_type'
 */

$database = "content";
$table_name = DB_PREFIX."pages_cache";

$cols = array(
	"page_id"  => 'INTEGER(12) NOT NULL PRIMARY KEY AUTO_INCREMENT',
	"page_id_original" => 'INTEGER(12)',
	"page_parent_id" => 'INTEGER(12)',
	"page_priority" => 'INTEGER(12)',
	"page_language"  => "VARCHAR(255) NOT NULL DEFAULT ''",
	"page_linkname"  => "VARCHAR(255) NOT NULL DEFAULT ''",
	"page_permalink" => "VARCHAR(255) NOT NULL DEFAULT ''",
	"page_permalink_short" => "VARCHAR(255) NOT NULL DEFAULT ''",
	"page_permalink_short_cnt" => "VARCHAR(255) NOT NULL DEFAULT ''",
	"page_redirect" => "VARCHAR(255) NOT NULL DEFAULT ''",
	"page_redirect_code" => "VARCHAR(255) NOT NULL DEFAULT ''",
	"page_hash" => "VARCHAR(255) NOT NULL DEFAULT ''",
	"page_psw" => "VARCHAR(255) NOT NULL DEFAULT ''",
	"page_title" => "VARCHAR(255) NOT NULL DEFAULT ''",
	"page_status" => "VARCHAR(255) NOT NULL DEFAULT ''",
	"page_usergroup" => "VARCHAR(255) NOT NULL DEFAULT ''",
	"page_content" => "VARCHAR(255) NOT NULL DEFAULT ''",
	"page_extracontent" => "VARCHAR(255) NOT NULL DEFAULT ''",
	"page_sort" => "VARCHAR(255) NOT NULL DEFAULT ''",
	"page_lastedit" => "VARCHAR(255) NOT NULL DEFAULT ''",
	"page_lastedit_from" => "VARCHAR(255) NOT NULL DEFAULT ''",
	"page_meta_author" => "VARCHAR(255) NOT NULL DEFAULT ''",
	"page_meta_date" => "VARCHAR(255) NOT NULL DEFAULT ''",
	"page_meta_keywords" => "VARCHAR(255) NOT NULL DEFAULT ''",
	"page_meta_description" => "VARCHAR(255) NOT NULL DEFAULT ''",
	"page_meta_robots" => "VARCHAR(255) NOT NULL DEFAULT ''",
	"page_meta_enhanced" => "VARCHAR(255) NOT NULL DEFAULT ''",
	"page_thumbnail" => "VARCHAR(255) NOT NULL DEFAULT ''",
	"page_head_styles" => "VARCHAR(255) NOT NULL DEFAULT ''",
	"page_head_enhanced" => "VARCHAR(255) NOT NULL DEFAULT ''",
	"page_template" => "VARCHAR(255) NOT NULL DEFAULT ''",
	"page_template_layout" => "VARCHAR(255) NOT NULL DEFAULT ''",
	"page_modul" => "VARCHAR(255) NOT NULL DEFAULT ''",
	"page_modul_query" => "VARCHAR(255) NOT NULL DEFAULT ''",
	"page_authorized_users" => "VARCHAR(255) NOT NULL DEFAULT ''",
	"page_version" => 'INTEGER(12)',
	"page_version_date" => "VARCHAR(255) NOT NULL DEFAULT ''",
	"page_labels" => "VARCHAR(255) NOT NULL DEFAULT ''",
	"page_cache_type" => "VARCHAR(20) NOT NULL DEFAULT ''"
  );

?>