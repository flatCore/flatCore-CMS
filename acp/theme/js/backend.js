/**
 * Prepend to this file:
 * - jquery
 * - bootstrap
 * - popper
 * - accounting
 * - tempus dominus
 * - tempus dominus jQuery Provider
 * - tags-input
 * - clipboard
 * - dropzone
 * - datetimepicker
 * - image-picker
 * - dirtyforms
 * - textcounter
 * - moment
 */

$(function() {
	

	/* dirty forms */
	$('form').dirtyForms();
	$.DirtyForms.dialog = false;
			
				
	$("#toggleExpand").click(function() {
		$('.info-collapse').toggleClass('info-hide');
	});
				
					
	setTimeout(function() {
		$(".alert-auto-close").slideUp('slow');
	}, 2000);
			
	$('#showVersions').collapse('hide');
			
	$('[data-bs-toggle="popover"]').popover();
	$('[data-bs-toggle="tooltip"]').tooltip();
				
	var clipboard = new ClipboardJS('.copy-btn');

	/* time picker */
	$('.dp').tempusDominus({
		hooks: {
			inputFormat: (context, date) => {
				return moment(date).format('YYYY-MM-DD HH:mm')
      	}
      }
	});				

	/**
	 * image picker for choosing thumbnails
	 * we use this f.e. for pages thumbnails
	 */
	
	$("select.image-picker").imagepicker({
		hide_select : true,
		show_label  : true
	});
				
	$('.filter-images').keyup(function() {
		var value = $(this).val();
		var exp = new RegExp('^' + value, 'i');
				
		$('.thumbnail').not('.selected').each(function() {
			var isMatch = exp.test($('p:first', this).text());
			$(this).toggle(isMatch);
		});
	});
	
	
	Dropzone.options.myDropzone = {
		init: function() {
			this.on("success", function(file, responseText) {
				file.previewTemplate.appendChild(document.createTextNode(responseText));
			});
		}
	};
	
	Dropzone.options.dropAddons = {
		init: function() {
			this.on("success", function(file, responseText) {
				window.location.href = "acp.php?tn=moduls&sub=u";
			});
		}
	};
	

	/**
	 * count chars and words
	 * we use this f.e. in meta descriptions
	 */
	 
	$('.cntWords').textcounter({   
		type: "word",
		stopInputAtMaximum: false,
		counterText: '%d'
	});
	$('.cntChars').textcounter({   
		type: "character",
		stopInputAtMaximum: false,
		counterText: '%d'
	}); 
			  
		

				
	/* css and html editor for page header */
	if($('#CSSeditor').length != 0) {
		var CSSeditor = ace.edit("CSSeditor");
		var CSStextarea = $('textarea[class*=aceEditor_css]').hide();
		CSSeditor.$blockScrolling = Infinity;
		CSSeditor.getSession().setValue(CSStextarea.val());
		CSSeditor.setTheme("ace/theme/" + ace_theme);
		CSSeditor.getSession().setMode("ace/mode/css");
		CSSeditor.getSession().setUseWorker(false);
		CSSeditor.setShowPrintMargin(false);
		CSSeditor.getSession().on('change', function(){
			CSStextarea.val(CSSeditor.getSession().getValue());
		});
	}
				
	if($('#HTMLeditor').length != 0) {
		var HTMLeditor = ace.edit("HTMLeditor");
		var HTMLtextarea = $('textarea[class*=aceEditor_html]').hide();
		HTMLeditor.$blockScrolling = Infinity;
		HTMLeditor.getSession().setValue(HTMLtextarea.val());
		HTMLeditor.setTheme("ace/theme/" + ace_theme);
		HTMLeditor.getSession().setMode({ path:'ace/mode/html', inline:true });
		HTMLeditor.getSession().setUseWorker(false);
		HTMLeditor.setShowPrintMargin(false);
		HTMLeditor.getSession().on('change', function(){
			HTMLtextarea.val(HTMLeditor.getSession().getValue());
		});
	}
			  
	/* ace editor instead of <pre>, readonly */
	$('textarea[data-editor]').each(function () {
		var textarea = $(this);
		var mode = textarea.data('editor');
		var editDiv = $('<div>', {
      	position: 'absolute',
         width: '100%',
         height: '400px',
         'class': textarea.attr('class')
      }).insertBefore(textarea);
      textarea.css('display', 'none');
      var editor = ace.edit(editDiv[0]);
      editor.$blockScrolling = Infinity;
      editor.getSession().setValue(textarea.val());
      editor.setTheme("ace/theme/" + ace_theme);
      editor.getSession().setMode("ace/mode/" + mode);
      editor.getSession().setUseWorker(false);
      editor.setShowPrintMargin(false);
      editor.setReadOnly(true);
	});


 	stretchAppContainer();
   	
	$( "div.scroll-box" ).each(function() {
		var divTop = $(this).offset().top;
	   var newHeight = $('div.app-container').innerHeight() - divTop +40;
	   $(this).height(newHeight);
	});

	
	
	//SIDEBAR
	
	var sidebarState = sessionStorage.getItem('sidebarState');
	var sidebarHelpState = sessionStorage.getItem('sidebarHelpState');

	windowWidth = $(window).width();

	$(window).resize(function() {
		windowWidth = $(window).width();

		if( windowWidth < 992 ){ //992 is the value of $screen-md-min in boostrap variables.scss
			$('#page-sidebar-inner').addClass('sidebar-collapsed').removeClass('sidebar-expanded');
			$('#page-content').addClass('sb-collapsed').removeClass('sb-expanded');
			$('#page-sidebar').addClass('sb-collapsed').removeClass('sb-expanded');
			
		} else {
	    
		   if(sidebarState){
				$('#page-sidebar-inner').addClass('sidebar-collapsed').removeClass('sidebar-expanded');
				$('#page-content').addClass('sb-collapsed').removeClass('sb-expanded');
				$('#page-sidebar').addClass('sb-collapsed').removeClass('sb-expanded');
		   } else {
			 	$('#page-sidebar-inner').addClass('sidebar-expanded').removeClass('sidebar-collapsed');
			 	$('#page-content').addClass('sb-expanded').removeClass('sb-collapsed');
			 	$('#page-sidebar').addClass('sb-expanded').removeClass('sb-collapsed');
		   }
  		}  
	});

	function setSidebarState(item,value){
   	sessionStorage.setItem(item, value);
	}

	function clearSidebarState(item){
   	sessionStorage.removeItem(item);
	}

	function collapseSidebar(){
	    $('#page-sidebar-inner').addClass('sidebar-collapsed').removeClass('sidebar-expanded');
	    $('#page-content').addClass('sb-collapsed').removeClass('sb-expanded');
	    $('#page-sidebar').addClass('sb-collapsed').removeClass('sb-expanded');
	    $('.caret_left').addClass('d-none');
	    $('.caret_right').removeClass('d-none');
	}
	
	function expandSidebar(){
	    $('#page-sidebar-inner').addClass('sidebar-expanded').removeClass('sidebar-collapsed');
	    $('#page-content').addClass('sb-expanded').removeClass('sb-collapsed');
	    $('#page-sidebar').addClass('sb-expanded').removeClass('sb-collapsed');
	    $('.caret_right').addClass('d-none');
	    $('.caret_left').removeClass('d-none');
	}
	
	function collapseHelpSidebar(){
	    $('#page-sidebar-help-inner').addClass('sidebar-help-collapsed').removeClass('sidebar-help-expanded');
	    $('#page-content').addClass('sb-help-collapsed').removeClass('sb-help-expanded');
	    $('#page-sidebar-help').addClass('sb-help-collapsed').removeClass('sb-help-expanded');
	    setSidebarState('sidebarHelpState','collapsed');
	}
	
	function expandHelpSidebar(){
	    $('#page-sidebar-help-inner').addClass('sidebar-help-expanded').removeClass('sidebar-help-collapsed');
	    $('#page-content').addClass('sb-help-expanded').removeClass('sb-help-collapsed');
	    $('#page-sidebar-help').addClass('sb-help-expanded').removeClass('sb-help-collapsed');
	    setSidebarState('sidebarHelpState','expanded');
	}


    /** check sessionStorage to expand/collapse sidebar onload **/
    if (sidebarState == "collapsed") {
    	collapseSidebar();
    } else {

    	if( windowWidth < 992 ) {
				$('#page-sidebar-inner').addClass('sidebar-collapsed').removeClass('sidebar-expanded');
				$('#page-content').addClass('sb-collapsed').removeClass('sb-expanded');
				$('#page-sidebar').addClass('sb-collapsed').removeClass('sb-expanded');
      } else {
      
      	if(sidebarState){
					$('#page-sidebar-inner').addClass('sidebar-collapsed').removeClass('sidebar-expanded');
					$('#page-content').addClass('sb-collapsed').removeClass('sb-expanded');
					$('#page-sidebar').addClass('sb-collapsed').removeClass('sb-expanded');
        } else {
					$('#page-sidebar-inner').addClass('sidebar-expanded').removeClass('sidebar-collapsed');
					$('#page-content').addClass('sb-expanded').removeClass('sb-collapsed');
					$('#page-sidebar').addClass('sb-expanded').removeClass('sb-collapsed');
				  $('.caret_right').addClass('d-none');
					$('.caret_left').removeClass('d-none');
				}
      }  
    }
 
	  if(sidebarHelpState == "collapsed" || typeof sidebarHelpState==='undefined' || sidebarHelpState===null){
			collapseHelpSidebar();
	  } else {
			expandHelpSidebar();
	  }


    /** collapse the sidebar navigation **/    
    $('#toggleNav').click(function(){
        if(!($('#page-sidebar-inner').hasClass('sidebar-collapsed'))) { // if sidebar is not yet collapsed
          collapseSidebar();
          setSidebarState('sidebarState','collapsed');
        } else {
        	expandSidebar();
          clearSidebarState('sidebarState');
        }
        return false;
    })
    
    /** toggle the sidebar for help **/    
    $('.toggle_sb_help').click(function(){
        if(!($('#page-sidebar-help-inner').hasClass('sidebar-help-expanded'))) {
          
          expandHelpSidebar();
        } else {
        	collapseHelpSidebar();
        }
        return false;
    })




	$('.page-info-btn').click(function(){
				   
	   var pageid = $(this).data('id');
	   var csrf_token = $(this).data('token');

	   // AJAX request
		$.ajax({
			url: 'core/pages.info.php',
			type: 'post',
			data: {pageid: pageid, csrf_token: csrf_token},
			success: function(response){ 
				 // Add response in Modal body
				$('#pageInfoModal .modal-body').html(response);
				
				// Display Modal
				$('#pageInfoModal').modal('show'); 
			}
		});
	});
				 
				 
				 
				 
				 
  $(window).resize(function () {
  	stretchAppContainer();
		$( "div.scroll-box" ).each(function() {
			var divTop = $(this).offset().top;
		  var newHeight = $('div.app-container').innerHeight() - divTop +40;
		  $(this).height(newHeight);
		});
  });


  function stretchAppContainer() {
  	var appContainer = $('div.app-container');
  	if(appContainer.length) {
	  	if(window.matchMedia('(max-width: 767px)').matches) {
				appContainer.height('auto');
			} else {
    		var divTop = appContainer.offset().top;
				var winHeight = $(window).height();
				var divHeight = winHeight - divTop;
				appContainer.height(divHeight);
			}
    }
  }
			

		 

				 
	



  function addTax(price,addition,tax) {
  		addition = parseInt(addition);
		tax = parseInt(tax);
		price = price*(addition+100)/100;
		price = price*(tax+100)/100;
			return price;
		}
		  	
	function removeTax(price,addition,tax) {
		addition = parseInt(addition);
		tax = parseInt(tax);					
		price = price*100/(addition+100);
		price = price*100/(tax+100);
		return price;
	}

	if($("#price").val()) {
					
		get_price_net = $("#price").val();			
		var e = document.getElementById("tax");
		var get_tax = e.options[e.selectedIndex].text;
		get_tax = parseInt(get_tax);
		get_price_addition = $("#price_addition").val();
		get_net_calc = get_price_net.replace(/\./g, '');
		get_net_calc = get_net_calc.replace(",",".");
		current_gross = addTax(get_net_calc,get_price_addition,get_tax);
		current_gross = accounting.formatNumber(current_gross,4,".",",");
		$('#price_total').val(current_gross);
		
		calculated_net = addTax(get_net_calc,get_price_addition,0);
		calculated_net = accounting.formatNumber(calculated_net,4,".",",");
		$('#calculated_net').html(calculated_net);
		
		$('.show_price_tax').html(get_tax);
		$('.show_price_addition').html(get_price_addition);

		$('#price').keyup(function(){
			get_price_net = $('#price').val();
			get_price_addition = $("#price_addition").val();
			get_net_calc = get_price_net.replace(/\./g, '');
			get_net_calc = get_net_calc.replace(",",".");
			current_gross = addTax(get_net_calc,get_price_addition,get_tax);
			current_gross = accounting.formatNumber(current_gross,4,".",",");
			$('#price_total').val(current_gross);
			
			calculated_net = addTax(get_net_calc,get_price_addition,0);
			calculated_net = accounting.formatNumber(calculated_net,4,".",",");
			$('#calculated_net').html(calculated_net);
			
		});
					
		$('#price_total').keyup(function(){
			get_brutto = $('#price_total').val();
			get_price_addition = $("#price_addition").val();
			get_gross_calc = get_brutto.replace(/\./g, '');
			get_gross_calc = get_gross_calc.replace(",",".");
			current_net = removeTax(get_gross_calc,get_price_addition,get_tax);
			current_net = accounting.formatNumber(current_net,4,".",",");
			$('#price').val(current_net);
			$('#calculated_net').html(current_net);
		});
		
		$('#price_addition').keyup(function(){
			get_price_net = $('#price').val();
			get_price_addition = $("#price_addition").val();
			
			get_net_calc = get_price_net.replace(/\./g, '');
			get_net_calc = get_net_calc.replace(",",".");
			current_gross = addTax(get_net_calc,get_price_addition,get_tax);
			current_gross = accounting.formatNumber(current_gross,4,".",",");
			$('#price_total').val(current_gross);
		});
		
		$('#tax').bind("change keyup", function(){
			
			var e = document.getElementById("tax");
			var get_tax = e.options[e.selectedIndex].text;
			get_tax = parseInt(get_tax);
			
			get_price_addition = $('#price_addition').val();
			get_price_net = $('#price').val();
			get_net_calc = get_price_net.replace(",",".");

			current_gross = addTax(get_net_calc,get_price_addition,get_tax);
			current_gross = accounting.formatNumber(current_gross,4,".",",");

			$('#price_total').val(current_gross);
		});
					
					
		get_price_net_s1 = $('#price_s1').val();
		price_s1 = showScaledPrices(get_price_net_s1,get_price_addition,get_tax);
		$('#calculated_net_s1').html(price_s1['net']);
		$('#calculated_gross_s1').html(price_s1['gross']);
							
		$('#price_s1').keyup(function(){
			get_price_net_s1 = $('#price_s1').val();
			price = showScaledPrices(get_price_net_s1,get_price_addition,get_tax);
			$('#calculated_net_s1').html(price['net']);
			$('#calculated_gross_s1').html(price['gross']);
		});
		
		get_price_net_s2 = $('#price_s2').val();
		price_s2 = showScaledPrices(get_price_net_s2,get_price_addition,get_tax);
		$('#calculated_net_s2').html(price_s2['net']);
		$('#calculated_gross_s2').html(price_s2['gross']);
							
		$('#price_s2').keyup(function(){
			get_price_net_s2 = $('#price_s2').val();
			price = showScaledPrices(get_price_net_s2,get_price_addition,get_tax);
			$('#calculated_net_s2').html(price['net']);
			$('#calculated_gross_s2').html(price['gross']);
		});
		
		get_price_net_s3 = $('#price_s3').val();
		price_s3 = showScaledPrices(get_price_net_s3,get_price_addition,get_tax);
		$('#calculated_net_s3').html(price_s3['net']);
		$('#calculated_gross_s3').html(price_s3['gross']);
							
		$('#price_s3').keyup(function(){
			get_price_net_s3 = $('#price_s3').val();
			price = showScaledPrices(get_price_net_s3,get_price_addition,get_tax);
			$('#calculated_net_s3').html(price['net']);
			$('#calculated_gross_s3').html(price['gross']);
		});
		
		get_price_net_s4 = $('#price_s4').val();
		price_s4 = showScaledPrices(get_price_net_s4,get_price_addition,get_tax);
		$('#calculated_net_s4').html(price_s4['net']);
		$('#calculated_gross_s4').html(price_s4['gross']);
							
		$('#price_s4').keyup(function(){
			get_price_net_s4 = $('#price_s4').val();
			price = showScaledPrices(get_price_net_s4,get_price_addition,get_tax);
			$('#calculated_net_s4').html(price['net']);
			$('#calculated_gross_s4').html(price['gross']);
		});
					
		get_price_net_s5 = $('#price_s5').val();
		price_s5 = showScaledPrices(get_price_net_s5,get_price_addition,get_tax);
		$('#calculated_net_s5').html(price_s5['net']);
		$('#calculated_gross_s5').html(price_s5['gross']);
							
		$('#price_s5').keyup(function(){
			get_price_net_s5 = $('#price_s5').val();
			price = showScaledPrices(get_price_net_s5,get_price_addition,get_tax);
			$('#calculated_net_s5').html(price['net']);
			$('#calculated_gross_s5').html(price['gross']);
		});
		
		function showScaledPrices(price,addition,tax) {
			addition = parseInt(addition);
			tax = parseInt(tax);
			
			price = price.replace(/\./g, '');
			price = price.replace(",",".");
			
			price_net = price*(addition+100)/100;
			price_gross = price_net*(tax+100)/100;
			
			price_net = accounting.formatNumber(price_net,4,".",",");
			price_gross = accounting.formatNumber(price_gross,4,".",",");
			
			var prices = new Object();
			prices['net'] = price_net;
			prices['gross'] = price_gross;
	
			return prices;
		}
	}

});