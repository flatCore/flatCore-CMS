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


?>
	
<script language="javascript" type="text/javascript">


tinyMCE.init({
	mode : "textareas",
	theme : "advanced",
	skin : "o2k7",
	skin_variant : "black",
	content_css : "<?php echo"$editor_styles"; ?>",
	theme_advanced_font_sizes: "10px,12px,13px,14px,16px,18px,20px",
	font_size_style_values : "10px,12px,13px,14px,16px,18px,20px",
	plugins : "pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

// Theme options
	theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
	theme_advanced_buttons2 : "bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,code,syntaxhl,|,preview,|,forecolor,backcolor,|,styleprops,attribs,|,nonbreaking,template,advcode",
	theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,iespell,media,|,fullscreen",
	theme_advanced_buttons4 : "",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_statusbar_location : "bottom",
	theme_advanced_styles : "LightBox (Links)=boxed;brightBox (Container)=brightBox;darkBox (Container)=darkBox;",
	theme_advanced_resizing : true,
	theme_advanced_resize_horizontal : false,
	plugin_preview_width : "900",
	width : "100%",
	height : "480",
	editor_selector : "mceEditor",
	relative_urls : false,
	remove_script_host : true,
	external_image_list_url : "core/imagelist.php",
	template_external_list_url : "core/templatelist.php",
	extended_valid_elements : "textarea[cols|rows|disabled|name|readonly|class]",
	visual : true,
	language : "de"

	
});


tinyMCE.init({
	mode : "textareas",
	theme : "advanced",
	skin : "o2k7",
	skin_variant : "black",
	content_css : "<?php echo"$editor_styles"; ?>",
	theme_advanced_font_sizes: "10px,12px,13px,14px,16px,18px,20px",
	font_size_style_values : "10px,12px,13px,14px,16px,18px,20px",
	plugins : "style,advlink,advimage,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

// Theme options
	theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,formatselect,forecolor,backcolor",
	theme_advanced_buttons2 : "bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,code,|,preview",
	theme_advanced_buttons3 : 
	"hr,removeformat,visualaid,|,sub,sup,charmap,|,print,|,fullscreen,template",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_statusbar_location : "bottom",
	theme_advanced_resizing : true,
	theme_advanced_resize_horizontal : false,
	width : "100%",
	height : "350",
	editor_selector : "mceEditor_small",
	relative_urls : false,
	remove_script_host : true,
	external_image_list_url : "core/imagelist.php",
	template_external_list_url : "core/templatelist.php",
	visual : true,
	language : "de"

	
});

</script>
