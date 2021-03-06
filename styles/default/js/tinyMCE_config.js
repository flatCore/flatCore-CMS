$(function() {
	$('textarea.mceEditor').tinymce({
		selector: 'textarea.mceEditor',
	  language : languagePack,
	  skin: tinymce_skin,
	  schema: 'html5',
	  element_format: "html",
	  allow_html_in_named_anchor: true,
	  entity_encoding : "raw",
	  menubar: "edit insert table tools view",
	  toolbar_items_size: 'small',
	  content_css : "../styles/default/css/editor.css?v=1",
	  body_class: 'mce-content-body',
	  plugins: [
	    "advlist autolink lists link image charmap preview anchor",
	    "searchreplace visualblocks code fullscreen wordcount template",
	    "media table paste"
	  ],
	  toolbar1: "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link unlink anchor image media",
	  toolbar2: "forecolor backcolor fontsizeselect | table | hr removeformat | subscript superscript | fullscreen visualchars visualchars visualblocks | template ",
	  image_list : "core/imagelist.php",
	  image_advtab: true,
	  image_title: true,
	  
	  link_list : "core/linklist.php",
	  convert_urls: false,
	  
	  templates: [ 
	    {title: 'row [6|6]', description: 'Zwei Spalten (Bootsrap)', url: '../styles/default/templates/editor_2cols.html'},
	    {title: 'row [4|4|4]', description: 'Drei Spalten (Bootsrap)', url: '../styles/default/templates/editor_3cols.html'},
	    {title: 'row [3|3|3|3]', description: 'Vier Spalten (Bootsrap)', url: '../styles/default/templates/editor_4cols.html'}
	  ],
		style_formats : [
			{title: 'Headlines', items: [
				{title : 'Headline H1', block : 'h1'},
				{title : 'Headline H2', block : 'h2'},
				{title : 'Headline H3', block : 'h3'},
				{title : 'Headline H4', block : 'h4'},
				{title : 'Headline H5', block : 'h5'},
				{title : 'Headline H6', block : 'h6'}
			]},
			{title: 'Typo', items: [
				{title : 'Absatz', block : 'p'},
				{title : 'Lead paragraph', block : 'p', classes : 'lead'}
			]},
			{title: 'Links', items: [
				{title : 'btn', selector: 'a', classes : 'btn btn-secondary'},
				{title : 'btn-primary', selector: 'a', classes : 'btn btn-primary'},
				{title : 'btn-info', selector: 'a', classes : 'btn btn-info'},
				{title : 'btn-success', selector: 'a', classes : 'btn btn-success'},
				{title : 'btn-warning', selector: 'a', classes : 'btn btn-warning'},
				{title : 'btn-danger', selector: 'a', classes : 'btn btn-danger'}
			]},
			{title: 'Badge/Alerts', items: [
			{title : 'Badge', inline : 'span', classes : 'badge bg-secondary'},
			{title : 'Badge Success', inline : 'span', classes : 'badge bg-success'},
			{title : 'Badge Warning', inline : 'span', classes : 'badge bg-warning text-dark'},
			{title : 'Badge Danger', inline : 'span', classes : 'badge bg-danger'},
			{title : 'Alert danger', block : 'div', classes : 'alert alert-danger'},
			{title : 'Alert Success', block : 'div', classes : 'alert alert-success'},
			{title : 'Alert info', block : 'div', classes : 'alert alert-info'}
			]},
			{title: 'IMG', items: [
				{title : 'fluid', selector : 'img', classes : 'img-fluid'},
				{title : 'rounded', selector : 'img', classes : 'rounded'}
			]},
			{title: 'Code', items: [
				{title : 'Code <pre>', block : 'pre', classes : 'code'},
				{title : 'Code <code>', inline : 'code', classes : 'code'}
			]},
		],
	  fontsize_formats : "10px 12px 13px 14px 16px 18px 20px",
		width : "100%",
		height : "480px",
		remove_script_host : true,
		rel_list: [
			{title: 'Keine', value: ''},
	    {title: 'Lightbox', value: 'lightbox'}
	  ],
		extended_valid_elements : "*[*]",
		visual : true,
		paste_as_text: true
	});


	$('textarea.mceEditor_small').tinymce({
	  selector: 'textarea.mceEditor_small',
	  language : languagePack,
	  skin: tinymce_skin,
	  toolbar_items_size: 'small',
	  content_css : "../styles/default/css/editor.css",
	  plugins: [
	    "advlist autolink lists link image charmap preview anchor",
	    "searchreplace visualblocks code fullscreen wordcount template",
	    "media table paste"
	  ],
	  menubar: "edit insert table tools view",
	  toolbar1: "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link unlink anchor image media",
	  toolbar2: "forecolor backcolor fontsizeselect | table | hr removeformat | subscript superscript | fullscreen visualchars visualchars visualblocks | template",
	  image_list : "core/imagelist.php",
	  image_advtab: true,
	  image_title: true,
	  convert_urls: false,
	  templates: [ 
	  	{title: 'row [6|6]', description: 'Zwei Spalten (Bootsrap)', url: '../styles/default/templates/editor_2cols.html'},
	    {title: 'row [4|4|4]', description: 'Drei Spalten (Bootsrap)', url: '../styles/default/templates/editor_3cols.html'},
	    {title: 'row [3|3|3|3]', description: 'Vier Spalten (Bootsrap)', url: '../styles/default/templates/editor_4cols.html'}
	  ],
		style_formats : [
			{title: 'Headlines', items: [
				{title : 'Headline H1', block : 'h1'},
				{title : 'Headline H2', block : 'h2'},
				{title : 'Headline H3', block : 'h3'},
				{title : 'Headline H4', block : 'h4'},
				{title : 'Headline H5', block : 'h5'},
				{title : 'Headline H6', block : 'h6'}
			]},
			{title: 'Typo', items: [
				{title : 'Absatz', block : 'p'},
				{title : 'Lead paragraph', block : 'p', classes : 'lead'}
			]},
			{title: 'Links', items: [
				{title : 'btn', selector: 'a', classes : 'btn btn-secondary'},
				{title : 'btn-primary', selector: 'a', classes : 'btn btn-primary'},
				{title : 'btn-info', selector: 'a', classes : 'btn btn-info'},
				{title : 'btn-success', selector: 'a', classes : 'btn btn-success'},
				{title : 'btn-warning', selector: 'a', classes : 'btn btn-warning'},
				{title : 'btn-danger', selector: 'a', classes : 'btn btn-danger'}
			]},
			{title: 'Badge/Alerts', items: [
			{title : 'Badge', inline : 'span', classes : 'badge bg-secondary'},
			{title : 'Badge Success', inline : 'span', classes : 'badge bg-success'},
			{title : 'Badge Warning', inline : 'span', classes : 'badge bg-warning text-dark'},
			{title : 'Badge Danger', inline : 'span', classes : 'badge bg-danger'},
			{title : 'Alert danger', block : 'div', classes : 'alert alert-danger'},
			{title : 'Alert Success', block : 'div', classes : 'alert alert-success'},
			{title : 'Alert info', block : 'div', classes : 'alert alert-info'}
			]},
			{title: 'IMG', items: [
				{title : 'fluid', selector : 'img', classes : 'img-fluid'},
				{title : 'rounded', selector : 'img', classes : 'rounded'}
			]},
			{title: 'Code', items: [
				{title : 'Code <pre>', block : 'pre', classes : 'code'},
				{title : 'Code <code>', inline : 'code', classes : 'code'}
			]},
		],
	  fontsize_formats : "10px 12px 13px 14px 16px 18px 20px",
		width : "100%",
		height : 350,
		remove_script_host : true,
		extended_valid_elements : "*[*]",
		visual : true,
		paste_as_text: true
	});

});