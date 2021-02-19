<?php

$database = "index";
$table_name = "pages";
$table_type = "virtual";

$cols = array(
  "page_id"  => 'VARCHAR',
  "page_url"  => 'VARCHAR',
  "page_title"  => 'VARCHAR',
  "page_description"  => 'VARCHAR',
  "page_thumbnail"  => 'VARCHAR',
  "page_keywords"  => 'VARCHAR',
 	"page_robots" => 'VARCHAR',
	"page_h1" => 'VARCHAR',
	"page_h2" => 'VARCHAR',
	"page_h3" => 'VARCHAR',
	"page_h4" => 'VARCHAR',
	"page_h5" => 'VARCHAR',
	"page_h6" => 'VARCHAR',
	"page_cnt_h1" => 'INTEGER',
	"page_cnt_h2" => 'INTEGER',
	"page_cnt_h3" => 'INTEGER',
	"page_images" => 'VARCHAR',
	"page_links" => 'VARCHAR',
  "page_content"  => 'VARCHAR',
  "indexed_time"  => 'VARCHAR'
  );

?>