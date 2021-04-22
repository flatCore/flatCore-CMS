<?php
	
/**
 * post_type -> m = message, i = image, f = file, g = gallery, e = event, v = video, l = link
 * post_status -> 1 = public, 2 = draft
 * post_rss -> 1 = yes, 2 = no
 * post_fixed -> 1 = yes, 2 = no
 * post_votings -> 1 = no, 2 = yes for registered useres, 3 = yes for everybody
 *
 * post_event_guestlist -> 1 = yes, 2 = no guestlist
 * post_event_guestlist_type -> 1 = everybody can confirm, 2 = registered user can confirm
 * post_event_guestlist_public -> 1 = yes, 2 = no
 * post_event_guestlist_public_nbr -> 1 = yes, 2 = no
 * post_event_guestlist_limit -> null = no limit, number = limit of guests
 */

$database = "posts";
$table_name = "fc_posts";

$cols = array(
	"post_id" => 'INTEGER(50) NOT NULL PRIMARY KEY AUTO_INCREMENT',
	"post_type"  => "VARCHAR(20) NOT NULL DEFAULT ''",
	"post_date"  => 'INTEGER(12)',
	"post_releasedate"  => 'INTEGER(12)',
	"post_lastedit"  => 'INTEGER(12)',
	"post_lastedit_from"  => "VARCHAR(50) NOT NULL DEFAULT ''",
	"post_title" => "VARCHAR(255) NOT NULL DEFAULT ''",
	"post_teaser" => "LONGTEXT NOT NULL DEFAULT ''",
	"post_text" => "LONGTEXT NOT NULL DEFAULT ''",
	"post_images" => "VARCHAR(255) NOT NULL DEFAULT ''",
	"post_tags" => "VARCHAR(255) NOT NULL DEFAULT ''",
	"post_link" => "VARCHAR(255) NOT NULL DEFAULT ''",
	"post_video_url" => "VARCHAR(100) NOT NULL DEFAULT ''",
	"post_categories" => "VARCHAR(100) NOT NULL DEFAULT ''",
	"post_comments" => 'INTEGER(12)',
	"post_author" => "VARCHAR(100) NOT NULL DEFAULT ''",
	"post_source" => "VARCHAR(100) NOT NULL DEFAULT ''",
	"post_status" => 'INTEGER(12)',
	"post_rss" => 'INTEGER(12)',
	"post_rss_url" => "VARCHAR(100) NOT NULL DEFAULT ''",
	"post_lang" => "VARCHAR(50) NOT NULL DEFAULT ''",
	"post_slug" => "VARCHAR(100) NOT NULL DEFAULT ''",
	"post_priority" => 'INTEGER(12)',
	"post_fixed" => 'INTEGER(12)',
	"post_hits" => 'INTEGER(12)',
	"post_votings" => 'INTEGER(12)',
	"post_labels" => "VARCHAR(50) NOT NULL DEFAULT ''",
	"post_attachments" => "VARCHAR(255) NOT NULL DEFAULT ''",
	/* events */
	"post_event_startdate"  => 'INTEGER(12)',
	"post_event_enddate" => 'INTEGER(12)',
	"post_event_zip" => "VARCHAR(50) NOT NULL DEFAULT ''",
	"post_event_city" => "VARCHAR(100) NOT NULL DEFAULT ''",
	"post_event_street" => "VARCHAR(100) NOT NULL DEFAULT ''",
	"post_event_street_nbr" => "VARCHAR(100) NOT NULL DEFAULT ''",
	"post_event_price_note" => "LONGTEXT NOT NULL DEFAULT ''",
	"post_event_guestlist" => 'INTEGER(12)',
	"post_event_guestlist_type" => 'INTEGER(12)',
	"post_event_guestlist_public" => 'INTEGER(12)',
	"post_event_guestlist_public_nbr" => 'INTEGER(12)',
	"post_event_guestlist_limit" => "VARCHAR(50) NOT NULL DEFAULT ''",
	/* products */
	"post_product_number" => "VARCHAR(100) NOT NULL DEFAULT ''",
	"post_product_manufacturer" => "VARCHAR(100) NOT NULL DEFAULT ''",
	"post_product_supplier" => "VARCHAR(100) NOT NULL DEFAULT ''",
	"post_product_tax" => 'INTEGER(12)',
	"post_product_price_net" => "VARCHAR(100) NOT NULL DEFAULT ''",
	"post_product_price_label" => "VARCHAR(100) NOT NULL DEFAULT ''",
	"post_product_textlib_price" => "VARCHAR(100) NOT NULL DEFAULT ''",
	"post_product_textlib_content" => "VARCHAR(100) NOT NULL DEFAULT ''",
	"post_product_currency" => "VARCHAR(100) NOT NULL DEFAULT ''",
	"post_product_unit" => "VARCHAR(100) NOT NULL DEFAULT ''",
	"post_product_amount" => "VARCHAR(100) NOT NULL DEFAULT ''",
	/* files */
	"post_file_attachment" => "VARCHAR(100) NOT NULL DEFAULT ''",
	"post_file_attachment_hits" => 'INTEGER(12)',
	"post_file_attachment_external" => "VARCHAR(100) NOT NULL DEFAULT ''",
	"post_file_license" => "VARCHAR(100) NOT NULL DEFAULT ''",
	"post_file_version" => "VARCHAR(100) NOT NULL DEFAULT ''"
	
);


?>