<?php

$editor_tpl_folder = $page_template;

if($page_template == "use_standard" OR $page_template == "") {

	$dbh = new PDO("sqlite:".CONTENT_DB);
	$sql = "SELECT prefs_template FROM fc_preferences WHERE prefs_id = 1";
	$result = $dbh->query($sql);
	$result = $result->fetch(PDO::FETCH_ASSOC);
	$dbh = null;

	$editor_tpl_folder = "$result[prefs_template]";
}

$editor_styles = "../styles/$editor_tpl_folder/css/editor.css";

if(!is_file("$editor_styles")) {
	$editor_styles = "css/editor.css";
}


$tinyMCE_config = "../styles/$editor_tpl_folder/js/tinyMCE_config.js";

if(is_file("$tinyMCE_config")) {
	$tinyMCE_config_contents = file_get_contents($tinyMCE_config);
	echo '<script language="javascript" type="text/javascript">';
	echo "$tinyMCE_config_contents";
	echo '</script>';
} else {

?>
	
<script language="javascript" type="text/javascript">


tinymce.init({
    selector: 'textarea.mceEditor',
    language : 'de',
    toolbar_items_size: 'small',
    content_css : "<?php echo"$editor_styles"; ?>?v=12",
    plugins: [
        "advlist autolink lists link image charmap preview anchor",
        "searchreplace visualblocks code fullscreen wordcount template",
        "media table contextmenu paste textcolor"
    ],
    toolbar1: "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link unlink anchor image media",
    toolbar2: "forecolor backcolor fontsizeselect | table | hr removeformat | subscript superscript | fullscreen visualchars visualchars visualblocks | template",
    image_list : "core/imagelist.php",
    image_advtab: true,
    convert_urls: false,
    fontsize_formats : "10px 12px 13px 14px 16px 18px 20px",
		width : "100%",
		height : "480",
		remove_script_host : true,
		extended_valid_elements : "textarea[cols|rows|disabled|name|readonly|class]",
		visual : true,
		paste_as_text: true
});


tinymce.init({
    selector: 'textarea.mceEditor_small',
    language : 'de',
    toolbar_items_size: 'small',
    content_css : "<?php echo"$editor_styles"; ?>?v=12",
    plugins: [
        "advlist autolink lists link image charmap preview anchor",
        "searchreplace visualblocks code fullscreen wordcount template",
        "media table contextmenu paste textcolor"
    ],
    toolbar1: "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link unlink anchor image media",
    toolbar2: "forecolor backcolor fontsizeselect | table | hr removeformat | subscript superscript | fullscreen visualchars visualchars visualblocks | template",
    image_list : "core/imagelist.php",
    image_advtab: true,
    convert_urls: false,
    fontsize_formats : "10px 12px 13px 14px 16px 18px 20px",
		width : "100%",
		height : "350",
		remove_script_host : true,
		extended_valid_elements : "textarea[cols|rows|disabled|name|readonly|class]",
		visual : true,
		paste_as_text: true
});

</script>

<?php
}
?>