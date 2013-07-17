tinymce.init({
  selector: 'textarea.mceEditor',
  language : 'de',
  toolbar_items_size: 'small',
  content_css : "../styles/blucent/css/editor.css?v=12",
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
  templates: [ 
    {title: 'row-fluid [6|6]', description: 'Zwei Spalten (Bootsrap)', url: '../styles/blucent/templates/editor_2cols.html'},
    {title: 'row-fluid [4|4|4]', description: 'Drei Spalten (Bootsrap)', url: '../styles/blucent/templates/editor_3cols.html'},
    {title: 'row-fluid [3|3|3|3]', description: 'Vier Spalten (Bootsrap)', url: '../styles/blucent/templates/editor_4cols.html'}
  ],
	style_formats : [
		{title : 'Absatz', block : 'p'},
		{title : 'Headline H1', block : 'h1'},
		{title : 'Headline H2', block : 'h2'},
		{title : 'Headline H3', block : 'h3'},
		{title : 'Headline H4', block : 'h4'},
		{title : 'Headline H5', block : 'h5'},
		{title : 'Headline H6', block : 'h6'},
		{title : 'Label', inline : 'span', classes : 'label'},
		{title : 'Label Success', inline : 'span', classes : 'label label-success'},
		{title : 'Label Warning', inline : 'span', classes : 'label label-warning'},
		{title : 'Label Important', inline : 'span', classes : 'label label-important'},
		{title : 'Label Info', inline : 'span', classes : 'label label-info'},
		{title : 'Label Inverse', inline : 'span', classes : 'label label-inverse'},
		{title : 'Lead paragraph', block : 'p', classes : 'lead'},
		{title : 'Div alert-error', block : 'div', classes : 'alert alert-error'},
		{title : 'Div alert-success', block : 'div', classes : 'alert alert-success'},
		{title : 'Div alert-info', block : 'div', classes : 'alert alert-info'},
		{title : 'Div brightBox', block : 'div', classes : 'brightBox'},
		{title : 'Div darkBox', block : 'div', classes : 'darkBox'},
		{title : 'Code PrettyPrint', block : 'pre', classes : 'prettyprint'},
		{title : 'Code PrettyPrint Linenums', block : 'pre', classes : 'prettyprint linenums'},
		{title : 'img-rounded', selector : 'img', classes : 'img-rounded'},
		{title : 'img-polaroid', selector : 'img', classes : 'img-polaroid'},
		{title : 'img-circle', selector : 'img', classes : 'img-circle'}
	],
  fontsize_formats : "10px 12px 13px 14px 16px 18px 20px",
	width : "100%",
	height : "480",
	remove_script_host : true,
	rel_list: [
    {title: 'Lightbox', value: 'lightbox'}
  ],
	extended_valid_elements : "textarea[cols|rows|disabled|name|readonly|class]",
	visual : true,
	paste_as_text: true
});


tinymce.init({
  selector: 'textarea.mceEditor_small',
  language : 'de',
  toolbar_items_size: 'small',
  content_css : "../styles/blucent/css/editor.css",
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
  templates: [ 
  	{title: 'row-fluid [6|6]', description: 'Zwei Spalten (Bootsrap)', url: '../styles/blucent/templates/editor_2cols.html'},
    {title: 'row-fluid [4|4|4]', description: 'Drei Spalten (Bootsrap)', url: '../styles/blucent/templates/editor_3cols.html'},
    {title: 'row-fluid [3|3|3|3]', description: 'Vier Spalten (Bootsrap)', url: '../styles/blucent/templates/editor_4cols.html'}
  ],
	style_formats : [
		{title : 'Absatz', block : 'p'},
		{title : 'Label', inline : 'span', classes : 'label'},
		{title : 'Label Success', inline : 'span', classes : 'label label-success'},
		{title : 'Label Warning', inline : 'span', classes : 'label label-warning'},
		{title : 'Label Important', inline : 'span', classes : 'label label-important'},
		{title : 'Label Info', inline : 'span', classes : 'label label-info'},
		{title : 'Label Inverse', inline : 'span', classes : 'label label-inverse'},
		{title : 'Lead paragraph', block : 'p', classes : 'lead'},
		{title : 'Div alert-error', block : 'div', classes : 'alert alert-error'},
		{title : 'Div alert-success', block : 'div', classes : 'alert alert-success'},
		{title : 'Div alert-info', block : 'div', classes : 'alert alert-info'},
		{title : 'Div brightBox', block : 'div', classes : 'brightBox'},
		{title : 'Div darkBox', block : 'div', classes : 'darkBox'},
		{title : 'Code PrettyPrint', block : 'pre', classes : 'prettyprint'},
		{title : 'Code PrettyPrint Linenums', block : 'pre', classes : 'prettyprint linenums'},
		{title : 'img-rounded', selector : 'img', classes : 'img-rounded'},
		{title : 'img-polaroid', selector : 'img', classes : 'img-polaroid'},
		{title : 'img-circle', selector : 'img', classes : 'img-circle'}
	],
  fontsize_formats : "10px 12px 13px 14px 16px 18px 20px",
	width : "100%",
	height : "350",
	remove_script_host : true,
	extended_valid_elements : "textarea[cols|rows|disabled|name|readonly|class]",
	visual : true,
	paste_as_text: true
});