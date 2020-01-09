$(document).ready(function() {

	// Misc jQuery hasAttr Addition ----------
	$.fn.hasAttr = function(name) {  return this.attr(name) !== undefined; };

	// Begin

	setTimeout(function() {
		$("div.progress.min.pin").removeClass('pin');
	}, 3500);

	$("#main-container").scroll(function() {
		$("div.progress.min.pin").removeClass('pin');
	});

	$("#nav").mouseleave(function(event) {
		$("#nav > div.menu").scrollTop(0);
	})

	$("#nav").mouseenter(function(event) {
		$("#nav-container > .overlay").addClass('show');
		$("#main-container").addClass('blur');
		if ($(this).hasClass('pin')) $("#nav > i.hotspot").removeClass('fa-bars').addClass('fa-close');
	}).mouseleave(function(event) {
		$("#nav-container > .overlay").removeClass('show');
		$("#main-container").removeClass('blur');
		if ($(this).hasClass('pin')) $("#nav > i.hotspot").removeClass('fa-close').addClass('fa-bars');
	});

	$("#user").mouseenter(function(event) {
		$("#user-container > .overlay").addClass('show');
		$("#main-container").addClass('blur');
	}).mouseleave(function(event) {
		$("#user-container > .overlay").removeClass('show');
		$("#main-container").removeClass('blur');
	});

	$("#help").mouseenter(function(event) {
		$("#help-container > .overlay").addClass('show');
		$("#main-container").addClass('blur');
	}).mouseleave(function(event) {
		$("#help-container > .overlay").removeClass('show');
		$("#main-container").removeClass('blur');
	});

	$("#nav > div.menu > div.menu-item.has-submenu").mouseenter(function(event) {
		$("div.menu-item > i.fa").addClass('blur');
		$("div.menu-item > span.desc").addClass('blur');
		$(this).children().removeClass('blur');

		$("#nav").addClass('max');
	}).mouseleave(function(event) {
		$("div.menu-item > i.fa").removeClass('blur');
		$("div.menu-item > span.desc").removeClass('blur');

		$("#nav").removeClass('max');
	});

	// Header Show/Hide on Scroll ///////////////////////////////////////////////////////////////////////////
	// //////////////////////////////////////////////////////////////////////////////////////////////////////////

	var HandleScroll = false;
	var lastScrollTop = 0;
	var delta = 25;
	var TopContainerHeight = $("#top-container").outerHeight();

	setTimeout(function() {		
		TopContainerHeight = $("#top-container").outerHeight();
	}, 200);

	$("#main-container").scroll(function(event) {
		HandleScroll = true;
	});

	setInterval(function() {
		if (!HandleScroll) return;
		ScrollHandler_TopContainer();
		HandleScroll = false;
	}, 100);

	function ScrollHandler_TopContainer() {
		var st = $("#main-container").scrollTop();
    
    		if(Math.abs(lastScrollTop - st) <= delta) return;
		    
    		if ((st > lastScrollTop) && (st > TopContainerHeight)) $('#top-container').addClass('min');
        		else if ((st < lastScrollTop)) $('#top-container').removeClass('min');   	
		    
    		lastScrollTop = st;
	}

	// Progress Filler
	$.each($("div.progress.min"), function() {
		var Progress = $(this).data("progress") + "%";
		var Desc = " completed";
		if ($(this).children("div.fill").hasClass('animate')) Desc = " and progressing..";

		$(this).children("div.fill").css("width", Progress);
		$(this).children("div.desc").html(Progress + Desc);
	});

	var Progress = $("div#bread-crumbs div.progress.min").data("progress") + "%";
	$("div#bread-crumbs div.text-fore").css("width", Progress);

	// Set Focus on main 
	$("#main-container wrapper" ).focus();

	// Position the Classes Scroller
	function classes_scroller_reposition() {
		var main_height = $("#main-container").outerHeight();
		var top_height = $("#top-container").outerHeight();
		var class_scroller_height = $("#jlc-class-scroller").outerHeight(); 

		var class_scroller_top = ((main_height - top_height) / 2) - (class_scroller_height / 2) + top_height;
		$("#jlc-class-scroller").css("top", class_scroller_top.toString() + "px");
	}

	setTimeout(function() {classes_scroller_reposition(); }, 100);
	$(window).resize(function() { classes_scroller_reposition(); });

	// Click Ripple 
	$(".ripple").click(function(e) {

		// Select Element
		var element = $(this);

		// Bounding Element
		var bound_element;
		var bound_flag = false;

		if (element.hasAttr("data-ripple-bound-element")) {
			if (element.hasAttr("data-ripple-bound-parent")) bound_element = element.closest(element.data("ripple-bound-parent")).find(element.data("ripple-bound-element"));
			else bound_element = element.closest(element.data("ripple-bound-element"));
			bound_flag = true;
		}
		else bound_element = element;

		// Select the ripple
		ripple = $("#ripple");
		ripple_container = $("#ripple-container");

		// Set parent of the ripple and stop current animation
		//element.append(ripple_container);
		ripple.removeClass("animate");

		// Calculate size of the ripple
		var size = Math.max(bound_element.outerWidth(), bound_element.outerHeight());			
		ripple_container.css({height: bound_element.outerHeight(), width: bound_element.outerWidth()});
		ripple.css({height: size, width: size});

		// Set the positions
		var bound_offset = bound_element.offset();
		var x = e.pageX - bound_offset.left - ripple.width() / 2;
		var y = e.pageY - bound_offset.top - ripple.height() / 2;

		ripple_container.css({top: bound_offset.top + 'px', left: bound_offset.left + 'px'});
		ripple.css({top: y + 'px', left: x + 'px'});

		// Ripple off!
		ripple.addClass("animate");

	});

	// Click Traverse
	$("*").click(function(e) {
		
		var element = $(this);
		var type = $(element).attr('type');
		
		if ( ( typeof type === "undefined" ) && ( type !== "radio" || type !== "checkbox" ) ) { 
			e.preventDefault();
	
			// Find HREF
			if (!element.hasAttr("href")) return;
	
			// Traverse the HREF
			if (element.hasAttr("target")) window.open(element.attr("href"), element.attr("target"));
			else window.location = element.attr("href");		

		} else {
			e.stopPropagation();
		}
	});

	// Temp Test - Tour
	$("#tourian-trigger").click(function() {
		tourian_tour_start("test");
	});

	// Accord Panel Min/Expand
	$("div.accordian-panel:not(.pin) > div.panel-tab").click(function() {
		var accordian_panel = $(this).parent();

		if (accordian_panel.hasClass('min')) {
			accordian_panel.removeClass('min');
			$(this).children('i.fa').removeClass('fa-chevron-down').addClass('fa-chevron-up');
		}
		else {
			accordian_panel.addClass('min');
			$(this).children('i.fa').addClass('fa-chevron-down').removeClass('fa-chevron-up');
		}
	});

	$("div.accordian-panel:not(.pin) div.content-min").click(function() {
		var accordian_panel = $(this).closest('div.accordian-panel');

		if (accordian_panel.hasClass('min')) {
			accordian_panel.removeClass('min');
			$(this).children('i.fa').removeClass('fa-chevron-down').addClass('fa-chevron-up');
		}
		else {
			accordian_panel.addClass('min');
			$(this).children('i.fa').addClass('fa-chevron-down').removeClass('fa-chevron-up');
		}
	});
	
	// Modal close
	$("div.modal-container div.overlay.close").click(function(){
		$("body > div.wrapper").removeClass('blur');
		$(this).closest("div.modal-container").removeClass("active");
	});
	
	$("div.modal-container div.close > i.fa").click(function() {
		$("body > div.wrapper").removeClass('blur');
		$(this).closest("div.modal-container").removeClass("active");
	});
});