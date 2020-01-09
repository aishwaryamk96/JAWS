"use strict";

// variables declarations
var instalment_fees = function(mode){
	// tax will be added on instalment fees as well but for inr only not for usd.
	var instal = 0;
	if(getCookie("currency") == "usd"){
		instal = $("#payment-data").data("instalment-fees-usd");
	} else {
		instal = $("#payment-data").data("instalment-fees-inr");
		var tax_on_instal = Number(instal).getTax();
		instal = Number(instal) + Number(tax_on_instal);
	}
	return instal;
};
var instalment_date = function(){
	return $("#payment-data").data("instalment-date"); 
};
var search;

// prototype declarations
Number.prototype.round = function(places) {
  return Number(this.valueOf().toFixed());
}

Number.prototype.getTotal = function(i){
 // add instalment fees to the total value
  return (typeof i != "undefined" && i <= 1 ) ? this.valueOf() : this.valueOf() + Number(instalment_fees()); 
} 

Number.prototype.getTax = function(){ 
	// for usd no taxes will be there.
	var price = this.valueOf();
	var tax = (getCookie("currency") == "usd") ? Number($("#payment-data").data("tax-rate-usd")) : Number($("#payment-data").data("tax-rate-inr")); 
	var	tax_amount = ( tax > 0 ) ? (price * tax) / 100 : price; 
	if(getCookie("currency") == "usd"){
		return 0;
	} else {
		return tax_amount.round(2);
	}
} 

Number.prototype.getDiscount = function(val){ 
	var price = this.valueOf(), 
		discount = Number($('.section-col input[name="total_discount"]').val()), 
		discount_amount = ( discount > 0 ) ? (price * discount) / 100 : 0;
	if(typeof val != "undefined" && val > 0) discount_amount = (price * val) / 100;
	return discount_amount.round(2);
}

Number.prototype.getManualDiscount = function(ask){ 
	var current = this.valueOf();
	if( Number($("#specialization-price").data("specialization-price")) > 0 ){
		// if any specialization is present then, specialization price will be added to individual courses.
		var original = Number($("#courses-combo").data("combo")) + Number($("#specialization-price").data("specialization-price"));
	} else {
		// individual courses combined price
		var original = Number($("#courses-combo").data("combo"));
	}
	var discount = original - current;
	var discount_percent = ( discount / original ) * 100;
	if( ask == "amount") return discount.round();
	else return discount_percent.round();
} 

Number.prototype.countText = function(){
	if( this.valueOf() == 1 ) return "Down Payment";
	else if( this.valueOf() == 2 ) return this.valueOf() + "<sup>nd</sup> Installment";
	else if( this.valueOf() == 3 ) return this.valueOf() + "<sup>rd</sup> Installment";
	else return this.valueOf() + "<sup>th</sup> Installment";
}

function validateEmail(email) {
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}

function validateDiscount(){
	var max = Number($("#payment-data").data("max-discount")),
		discount = Number($('.section-col input[name="total_discount"]').val());
	if( discount > max ){
		alert("You have exceeded your maximum allowed discount. Package will be sent for approval.");
	}
}

function validateInstalment(count){
	var price = Number($("#actual_offered_price").data("actual_offered_price"));
	var currency = getCookie("currency");
	var instalment_setting = $("#payment-data").data("instalment-settings");
	if(currency == "inr"){
		var len = Object.keys(instalment_setting.inr).length;
		var thisPrice = instalment_setting.inr[count];
		var nextPrice = instalment_setting.inr[count+1];
	} else {
		var len = Object.keys(instalment_setting.usd).length;
		var thisPrice = instalment_setting.usd[count];
		var nextPrice = instalment_setting.usd[count+1];
	}
	if(count > len){
		// more instalment given than total available settings
		alert("Approval will be required.");
		return true;
	} else if(count < len) {
		// instalment given within available settings
		if(price >= nextPrice){
			// price is more than max allowed for this settings
			return true;
		} else if(price >= thisPrice && price < nextPrice){
			// price is within the allwoed limit for this settings
			return true;
		} else {
			// need approval for every other case like price < thisPrice
			alert("Approval will be required.");
			return true;
		}
	} else if(count == len) {
		// instalment given max of available settings
		if(typeof nextPrice == "undefined" && price >= thisPrice ){
			// price is more than max of available settings
			return true;
		}else {
			// price is not more than max of available settings.
			alert("Approval will be required.");
			return true;
		}
	} else {
		// any other condition
		console.log("Invalid Values :: Please refresh the page and try again.");
		alert("Please refresh the page and try again.");
		return false;
	}	
}

function getCookie(cname) { return $("#payment-data").data("currency"); } 

function setCookie(cname, cvalue, exdays) { $("#payment-data").data("currency",cvalue); }

function getSymbol(cookie){	if(cookie == "inr"){ return "&#8377;"; } else if(cookie == "usd") { return "&#36;";	} else { return "&#8377;"; } } 

// function declarations
function updateCurrency() {
	var type = "inr";
	if ($("#payment-data").data("currency") == "inr") {
		type="usd";
		$(".selected.currency").html("US Dollars");
	}
	else $(".selected.currency").html("Indian Rupees");

	setCookie("currency", type, 30);

	if ( type == "inr" ) changeCourseToINR("&#8377;");		
	else if( type == "usd" ) changeCourseToUSD("&#36;");		
	else  changeCourseToINR("&#8377;");
		
}


function changeCourseToUSD(symbol){
	var price = $(".mode");
	price.each(function(i, obj) {
		var premium = $(obj).data("il_price_usd");
		var regular = $(obj).data("sp_price_usd");
		if( typeof premium != "undefined" ){
			$(obj).children("p").children("span.price").html(symbol+" "+premium.toLocaleString("en-IN"));
		}
		if( typeof regular != "undefined" ){
			$(obj).children("p").children("span.price").html(symbol+" "+regular.toLocaleString("en-IN"));
		}
	});
	selectCourses("courses");
	selectSpecialization();
}

function changeCourseToINR(symbol){
	var price = $(".mode");
	price.each(function(i, obj) {
		var premium = $(obj).data("il_price_inr");
		var regular = $(obj).data("sp_price_inr");
		if( typeof premium != "undefined" ){
			$(obj).children("p").children("span.price").html(symbol+" "+premium.toLocaleString("en-IN"));
		}
		if( typeof regular != "undefined" ){
			$(obj).children("p").children("span.price").html(symbol+" "+regular.toLocaleString("en-IN"));
		}
	});
	selectCourses("courses");
	selectSpecialization();
}

function removeCourses(section){
	
	if(section == 'courses'){
		var count = $('#courses .course.active').length;
		var currency = getCookie("currency");
		var updatePrice = 0;
		// set actual price
		$('#courses-actual').data('price',updatePrice);
		$('#courses-actual').html(getSymbol(currency)+' '+updatePrice.toLocaleString('en-IN'));
		// set combo price
		$('#courses-combo').data('combo',updatePrice);
		$('#courses-combo').html(getSymbol(currency)+' '+updatePrice.toLocaleString('en-IN'));
		// set combo min
		$('#courses-combo-min').data('combo-min',updatePrice);
		$('#courses-combo-min').html(getSymbol(currency)+' '+updatePrice.toLocaleString('en-IN'));
		// set discount price
		$('#courses-discount').data('discount',updatePrice);
		$('#courses-discount').html(getSymbol(currency)+' '+updatePrice.toLocaleString('en-IN'));
		if( count > 0 ){
			// some selected courses available. recalculate total courses.
			selectCourses(section);
		} else{
			/* $('.instl-container .instl').not('.add').each(function(){
				if(!$(this).hasClass('fst')){
					removeInstalment2($(this).find('.fa.fa-fw.fa-lg.fa-close'));
				}
			}); */
			$('.instl-container .instl').hide();
		}
		populateInfoBox(section);
		disableComplimentary();
		if( Number($("#specialization-price").data('specialization-price')) > 0 ){
			calculatePayment();
		} else {
			// any change in course selection after instalment section is modified must return instalment section to default state.
			initializePayment(count);
		}
	} else if(section == 'complimentary'){
		populateInfoBox(section);
	}
}

function calculateCourse(obj,type,count,combo_hierarchy){
	
	// il = primary; sp = regular
	// live = primary; video = regular
	var currency = getCookie("currency");
	// get actual price
	if (type == 'regular' && currency == 'inr') {
		var price = $(obj).data('sp_price_inr');
	} else if (type == 'regular' && currency == 'usd') {
		var price = $(obj).data('sp_price_usd');
	} else if (type == 'premium' && currency == 'inr') {
		var price = $(obj).data('il_price_inr');
	} else if (type == 'premium' && currency == 'usd') {
		var price = $(obj).data('il_price_usd');
	} else {
		var price = 0;
	}
	// get combo price
	if (type == 'regular' && currency == 'inr') {
		var comboprice = ( $(obj).data('sp_price_inr_alt') == 0 ) ? $(obj).data('sp_price_inr') : $(obj).data('sp_price_inr_alt');
		var applyDiscount = ( $(obj).data('sp_price_inr_alt') == 0 ) ? 0 : 1;
	} else if (type == 'regular' && currency == 'usd') {
		var comboprice = ( $(obj).data('sp_price_usd_alt') == 0 ) ? $(obj).data('sp_price_usd') : $(obj).data('sp_price_usd_alt');
		var applyDiscount = ( $(obj).data('sp_price_usd_alt') == 0 ) ? 0 : 1;
	} else if (type == 'premium' && currency == 'inr') {
		var comboprice = ( $(obj).data('il_price_inr_alt') == 0 ) ? $(obj).data('il_price_inr') : $(obj).data('il_price_inr_alt');
		var applyDiscount = ( $(obj).data('il_price_inr_alt') == 0 ) ? 0 : 1;
	} else if (type == 'premium' && currency == 'usd') {
		var comboprice = ( $(obj).data('il_price_usd_alt') == 0 ) ? $(obj).data('il_price_usd') : $(obj).data('il_price_usd_alt');
		var applyDiscount = ( $(obj).data('il_price_usd_alt') == 0 ) ? 0 : 1;
	} else {
		var comboprice = 0;
		var applyDiscount = 0;
	}
	
	// first find out the highest hierarchy (lowest combo_hierarchy value) and take actual price for that course. for rest of the courses follow above logic for comboprice.
	// if course has highest combo_hierarchy then its actual value to be taken.
	if(obj.parent().data('course-id') == combo_hierarchy){
		comboprice = price;
	}
	
	// set actual price
	if(price){
		var updatePrice = Number($('#courses-actual').data('price')) + Number(price);
		$('#courses-actual').data('price',updatePrice);
		$('#courses-actual').html(getSymbol(currency)+' '+updatePrice.toLocaleString('en-IN'));
	}
	// set combo price
	if(typeof comboprice != 'undefined'){
		var updateComboPrice = Number($('#courses-combo').data('combo')) + Number(comboprice);
		$('#courses-combo').data('combo',updateComboPrice);
		$('#courses-combo').html(getSymbol(currency)+' '+updateComboPrice.toLocaleString('en-IN'));
		// set discount price
		if(applyDiscount){
			var discount = updatePrice - updateComboPrice;
			$('#courses-discount').data('discount',discount);
			$('#courses-discount').html('-'+getSymbol(currency)+' '+discount.toLocaleString('en-IN'));
		}
		// set combo min
		$('#courses-combo-min').data('combo-min',updateComboPrice);
		$('#courses-combo-min').html(getSymbol(currency)+' '+updateComboPrice.toLocaleString('en-IN'));
	}
	// update the actual offered price section for further tax,discount and instalment calculation
	$('#actual_offered_price').html(getSymbol(currency) + ' ' + updateComboPrice.toLocaleString('en-IN'));
	$('#actual_offered_price').data('actual_offered_price', updateComboPrice);
	$('#actual_offered_price_without_tax').html(getSymbol(currency) + ' ' + updateComboPrice.toLocaleString('en-IN'));
	$('#editOfferedPrice').children('input').val(updateComboPrice);
	$('#actual_offered_price_min').html(getSymbol(currency) + ' ' + updateComboPrice.toLocaleString('en-IN'));
}

function selectCourses(section){
	var currency = getCookie("currency");
	if(section == 'courses'){
		$('.instl-container .instl').css('display','inline-block');
		var count = $('#courses .course.active').length;
		var arr = [];	var course_ids = [];
		$('#courses-combo').data('combo',0);
		$('#courses-actual').data('price',0);
		$('#courses .course.active').each(function(){
			// find the highest combo_hierarchy value and send in courses calculation.
			var combo_hierarchy = $(this).data('combo_hierarchy');
			if(combo_hierarchy > 0){
				course_ids[combo_hierarchy] = $(this).data('course-id');
				arr.push(combo_hierarchy);
			}
		});
		$('#courses .course.active').each(function(){
			if($(this).hasClass('live')){
				calculateCourse($(this).find('.mode.live.active'),'premium',count,course_ids[arr.sort()[0]]);
			} else if($(this).hasClass('video')) {
				calculateCourse($(this).find('.mode.video.active'),'regular',count,course_ids[arr.sort()[0]]);
			} else {
				console.log($(this).find('.mode'));
			}
		});
		disableComplimentary();
		calculatePayment();
		if(count == 0){
			// update course side bar when currency changes without any course selection.
			$('#courses-actual').html(getSymbol(currency)+ ' ' +0);
			$('#courses-combo').html(getSymbol(currency)+' '+0);
			$('#courses-discount').html('-'+getSymbol(currency)+' '+0);
			$('#courses-combo-min').html(getSymbol(currency)+' '+0);
		}
	} else if(section == 'complimentary'){
		console.log('For Complimentary section no calculation needed as they are free courses.');
	}
}

function selectSpecialization(mode){
	var obj = $(".section-col select[name='specialization']");
	var currency = getCookie("currency"), symbol = getSymbol(currency);
	var selected = $(obj).val();
	var price_inr = $("option:selected", obj).data("price-inr");
	var price_usd = $("option:selected", obj).data("price-usd");
	var courses = $("option:selected", obj).data("courses").split(";");
	$(".spec-courses").html("");
	if(selected > 0){
		$('.instl-container .instl').css('display','inline-block');
	}
	$("#courses .course").each(function(){ $(this).removeClass("disabled"); });
	$(courses).each(function(key, value){
		$("#courses .mode").each(function(){ 
			if ($(this).data("mode") == value){
				$(this).parent().addClass("disabled");
				// get the mode of course from value
				var mode = value.split(",");
				var il = $(this).siblings(".title").data("il-code");
				var sp = $(this).siblings(".title").data("sp-code");
				if ( mode[1] == 1 ){
					// il
					var spec = $(".spec-courses").html();
					spec += '<div class="info-box">' + il + '</div>';
					$(".spec-courses").html(spec);
				} else {
					// sp
					var spec = $(".spec-courses").html();
					spec += '<div class="info-box">' + sp + '</div>';
					$(".spec-courses").html(spec);
				}
			}
		});
	});
	disableComplimentary("specialization");
	// update side bar prices
	if(currency == "usd") {
		$("#specialization-price").html(symbol+ " " +price_usd.toLocaleString("en-IN"));
		$("#specialization-price").data("specialization-price" , price_usd);
	} else {
		$("#specialization-price").html(symbol+ " " +price_inr.toLocaleString("en-IN"));
		$("#specialization-price").data("specialization-price" , price_inr);
	}
	if(typeof mode == "undefined"){
		populateInfoBox("courses");
		calculatePayment();
	}
}

function disableComplimentary(selection){
	if(typeof selection == "undefined") selection = "";
	$('#complimentary .course').each(function(){ $(this).removeClass('disabled'); });
	$('#courses .course.active').each(function(){
		var course = $(this).data('course-id');
		$('#complimentary .course').each(function(){ 
			if ($(this).data('course-id') == course){
				$(this).removeClass('active').removeClass('live').removeClass('video');
				$(this).children("div.mode").removeClass('active');
				$(this).addClass('disabled');
				populateInfoBox('complimentary');
			}
		});
	});
	if(selection == "specialization"){
		$('#courses .course.disabled').each(function(){
			var course = $(this).data('course-id');
			$('#complimentary .course').each(function(){
				if ($(this).data('course-id') == course){
					$(this).removeClass('active').removeClass('live').removeClass('video');
					$(this).children("div.mode").removeClass('active');
					$(this).addClass('disabled');
					populateInfoBox('complimentary');
				}
			});
		});
	}
}

function populateInfoBox(section){
	var currency = getCookie("currency"), symbol = getSymbol(currency);
	if(section == 'courses'){
		var html = '<div>Courses :</div>'; $('#courses-info').html('');
		$('#courses .course.active').each(function(){
			var course_id = $(this).data('course-id');
			if($(this).hasClass('live')){
				var course_code = $(this).children('.title').data('il-code');
				var course_combo_code = $(this).children('.mode.live').data('mode');
			} else if($(this).hasClass('video')) {
				var course_code = $(this).children('.title').data('sp-code');
				var course_combo_code = $(this).children('.mode.video').data('mode');
			} else {
				console.log('Invalid data' + $(this).find('.mode'));
			}
			
			// course combination is course_id,mode;course_id			
			html += '<div class="info-box" data-course_id="'+course_id+'" data-course_combo_code="'+course_combo_code+'" onclick="javascript:removeCode(\''+course_id+'\',\''+section+'\',event)">'+course_code+'<i class="fa fa-fw fa-lg fa-close"></i></div>';
		});
		if($('#courses .course.active').length == 0){
			html += '<b> None</b>';
		}
		$('#courses-info').html(html);
	} else if( section == 'instalments' ){
		var html = '<div>Installments :</div>'; $('#instalments-info').html('');
		var count = $('.instl-container .add').data('count');

		$('.instl-container .instl').not('.add').each(function(i,e){
			var price = Number($(this).find('div.sum-desc input').val());			
			var box = $(this).attr('id');
			if (price > 0) {
				html += '<div class="info-box" onclick="javascript:removeBox(\''+ box +'\',event);" >' + symbol + ' ' + price.getTotal(count).toLocaleString('en-IN');
				if( box != 'box1'){ 
					html += '<i class="fa fa-fw fa-lg fa-close"></i>';
				}
				html += '</div>';
			} else {
				html += "<b> None</b>";
			} 
		});
		if($('#courses .course.active').length != 0){
			html += '<div class="info-box"><i onclick="javascript:addInstalmentBox2();" class="fa fa-fw fa-lg fa-plus"></i></div>';
		}
		$('#instalments-info').html(html);
	} else if( section == 'complimentary' ){
		var html = '<div>Complimentary :</div>'; $('#complimentary-info').html('');
		$('#complimentary .course.active').each(function(){
			var course_id = $(this).data('course-id');
			if($(this).hasClass('live')){
				var course_code = $(this).children('.title').data('il-code');
				var course_combo_code = $(this).children('.mode.live').data('mode');
			} else if($(this).hasClass('video')) {
				var course_code = $(this).children('.title').data('sp-code');
				var course_combo_code = $(this).children('.mode.video').data('mode');
			} else {
				console.log('Invalid data' + $(this).find('.mode'));
			}
			
			// course combination is course_id,mode;course_id			
			html += '<div class="info-box" data-course_id="'+course_id+'" data-course_combo_code="'+course_combo_code+'" onclick="javascript:removeCode(\''+course_id+'\',\''+section+'\',event)">'+course_code+'<i class="fa fa-fw fa-lg fa-close"></i></div>';
		});
		if($('#complimentary .course.active').length == 0){
			html += '<b> None</b>';
		}
		$('#complimentary-info').html(html);
	}
}

function removeCode(course_id,section,event){
	if(section == 'courses'){
		$('#courses .course.active').each(function(){
			if( $(this).data('course-id') == course_id ){
				$(this).removeClass('active').removeClass('live').removeClass('video');
				$(this).children("div.mode").removeClass('active');
				removeCourses(section);
			}
		});
	} else if(section == 'complimentary'){
		$('#complimentary .course.active').each(function(){
			if( $(this).data('course-id') == course_id ){
				$(this).removeClass('active').removeClass('live').removeClass('video');
				$(this).children("div.mode").removeClass('active');
				removeCourses(section);
			}
		});
	}
	event.stopPropagation();
}

function getComboCode(section){
	// sort on the basis of course_id ASC.
	// send the course combo in ajax call. course combination is course_id,mode;course_id,mode;...	
	// in case of any specialization selected, add the specialization courses as well.
	var arr = [];
	if( section == "courses"){
		$("#courses-info .info-box").each(function(){
			var course_id = $(this).data("course_id");
			arr[course_id] = $(this).data("course_combo_code");
		});
		if( Number($("#specialization-price").data("specialization-price")) > 0 ){
			// add specialization course codes
			var obj = $(".section-col select[name='specialization']");
			var specialization_course = $("option:selected", obj).data("courses");
			// combine the two arrays
			arr.push(specialization_course);
		}
	} else if( section == "complimentary"){
		$("#complimentary-info .info-box").each(function(){
			var course_id = $(this).data("course_id");
			arr[course_id] = $(this).data("course_combo_code");
		});
	}
	return arr.filter(function(n){ return n != undefined }).join(";");
}

function calculatePayment(mode){
	if( Number($("#specialization-price").data("specialization-price")) > 0 ){
		// if any specialization is present then, specialization price will be added to individual courses.
		var price = Number($("#courses-combo").data("combo")) + Number($("#specialization-price").data("specialization-price"));
	} else {
		// individual courses combined price
		var price = Number($("#courses-combo").data("combo"));
	}
	
	if(typeof mode != "undefined" && mode == "edit"){
		// in case of manual update of the offered price, all other prices are void.
		var price = Number($("#editOfferedPrice").data("editOfferedPrice"));
	}
	
	var discount = $('.section-col input[name="total_discount"]').val(), 
		currency = getCookie("currency"), 
		symbol = getSymbol(currency);
	var discount_amount = price.getDiscount(discount),
		tax = (price - discount_amount).getTax();
	// total price =  price (sum of all selected courses) - discount amount( discount selected in %) calculated on price + tax calculated on discounted price.
	var finalprice = (price - discount_amount) + tax;

	// update sidebar
	// update tax section
	$("#tax_amount").html(symbol + " " + tax.toLocaleString("en-IN"));
	// update discount section
	$("#total_discount").html("- " + symbol + " " + discount_amount.toLocaleString("en-IN"));
	// update actual_offered_price text
	$("#actual_offered_price").html(symbol + " " + finalprice.toLocaleString("en-IN"));
	// update actual_offered_price_without_tax text without tax and discount
	$("#actual_offered_price_without_tax").html(symbol + " " + price.toLocaleString("en-IN"));
	// update actual_offered_price_min text
	$("#actual_offered_price_min").html(symbol + " " + finalprice.toLocaleString("en-IN"));
	// update the data attribute as all further calculation is done on this value.
	$("#actual_offered_price").data("actual_offered_price" , finalprice);
	// update the data attribute to facilitate edit option. it has price without tax and discount added.
	$("#actual_offered_price_without_tax").data("actual_offered_price_without_tax" , price);
	// update the edit field with value without tax and discount.
	$("#editOfferedPrice").children("input").val(price);
	$("#editOfferedPrice").data("editOfferedPrice",price);
	
	if(typeof mode != "undefined" && mode == "edit"){
		// in case of manual update of the offered price, discount will be no other discount appilcable, but difference in amount considered as discount.
		$("#total_discount").html("- " + symbol + " " + price.getManualDiscount("amount").toLocaleString("en-IN") + " (" + price.getManualDiscount("percent") + "%) ");
		$("#total_discount").data("discount-amount",price.getManualDiscount("amount"));
		$("#total_discount").data("discount-percent",price.getManualDiscount("percent"));
	}
	setInstalment2();
}

function editOfferedPrice(){
	if( $("#editOfferedPrice").css('display') == 'none' ){
		$("#editOfferedPrice").show();		
		// $("#editOfferedPrice").children('input').focus();
	} else {
		var original_price = $("#actual_offered_price_without_tax").data("actual_offered_price_without_tax");
		var new_price = $("#editOfferedPrice").children("input").val();
		if(isNaN(new_price)){
			alert("Please provide valid price.");
			return false;
		}
		if( new_price > original_price ){
			alert("Please check price.Discount cannot be more than Offered price.");
			return false;
		}
		var symbol = getSymbol(getCookie("currency"));
		if( original_price != new_price ){
			if(confirm("Are you sure you want to modify the price?")){
				console.log("Manually price updated. Require Approval.");
				$("#actual_offered_price_without_tax").html(symbol + " " +new_price);
				$("#actual_offered_price_without_tax").data( "actual_offered_price_without_tax" , new_price);
				$("#editOfferedPrice").data("editOfferedPrice", new_price);
				calculatePayment("edit");
			} else { 
				console.log("Manually price not updated.");
			}
		} else {
			console.log("No change in manually update of price.");
		}
		/* $("#actual_offered_price_without_tax").show(); */
		$("#editOfferedPrice").hide();
	}	
}

function initializePayment(count){
	// if( count > 0 ){
		$(".instl-container .instl").not(".add").each(function(){
			if(!$(this).hasClass("fst")){
				removeInstalment2($(this).find(".fa.fa-fw.fa-lg.fa-close"));
			}
		});
	// } else {
	if( count == 0 ){
		var symbol = getSymbol(getCookie("currency"));
		$("#net_payable").html(symbol + " " + 0);
		$("#net_payable").data("net_payable" , 0);
		$("#net_payable_min").html(symbol + " " + 0);
		$("#instalment_fee").html(symbol + " " + 0);
		$("#instalment_fee").data("instalment_fee" , 0);
		$("#instalments-info").html("<div>Installments :</div><b> None</b>");
		
		$("#tax_amount").html(symbol + " " + 0);
		$("#total_discount").html("- " + symbol + " " + 0);
		$("#actual_offered_price").html(symbol + " " + 0);
		$("#actual_offered_price_min").html(symbol + " " + 0);
		$("#actual_offered_price_without_tax").html(symbol + " " + 0);
		$("#actual_offered_price").data("actual_offered_price" , 0);
		$("#editOfferedPrice").children("input").val(0);
	}	
}

function calculateInstalment(steps){
	var finalPrice = $("#actual_offered_price").data("actual_offered_price");
	switch(steps){
		case 0:	case 1: 
			var instalment = finalPrice; break;
		default: 
			var instalment = finalPrice / steps;
	}
	
	return instalment.round(2);	
}

function setInstalment2(){
	var currency = getCookie("currency"), symbol = getSymbol(currency), price = Number($('#actual_offered_price').data('actual_offered_price')), count = $('.instl-container .add').data('count'), instalment = calculateInstalment(count);
	$('.instl-container .instl').not('.add').each(function(i,e){
		// set instalment price text
		$('div.sum-desc span').html(symbol + ' ' + instalment.toLocaleString('en-IN'));
		// set instalment price input value
		$('div.sum-desc input').val(instalment);
		// set instalment count text
		var textInstalment = Number(i) + Number(1);
		$(this).find('div.count').html(textInstalment.countText());
		// set total instalment fees text
		var sum = instalment.getTotal(count);
		$('div.sum').html(symbol + ' ' + sum.toLocaleString('en-IN'));
		$('div.sum').data( 'sum' , sum);
	});
	
	// update sidebar 
	// update net payable text - update net payable min text - update total instalment_fee text
	if(count == 1){
		$('#net_payable').html(symbol + ' ' + price.getTotal(count).toLocaleString('en-IN'));
		$('#net_payable').data('net_payable' , price.getTotal(count));
		$('#net_payable_min').html(symbol + ' ' + price.getTotal(count).toLocaleString('en-IN'));
		$('#instalment_fee').html(symbol + ' ' + 0);
		$('#instalment_fee').data('instalment_fee' , 0);
	} else {
		var finaltotal = Number(price) + (Number(instalment_fees()) * Number(count));
		$('#net_payable').html(symbol + ' ' + finaltotal.toString().toLocaleString('en-IN'));
		$('#net_payable').data('net_payable' , finaltotal);
		$('#net_payable_min').html(symbol + ' ' + finaltotal.toString().toLocaleString('en-IN'));
		$('#instalment_fee').html(symbol + ' ' + (Number(instalment_fees()) * Number(count)).toString().toLocaleString('en-IN'));
		$('#instalment_fee').data('instalment_fee' , (Number(instalment_fees()) * Number(count)));
	}
	
	// set instalment info box
	populateInfoBox('instalments');
}

function addInstalmentBox2(){
	var currency = getCookie("currency");
	var symbol = getSymbol(currency);
	var count = $('.instl-container .add').data('count'); count++;
	// validateInstalment(count);
	if( validateInstalment(count) ){
		var instalment = calculateInstalment(count);
		var html = '<div id="box'+ count +'" class="instl">' +
				'<i onclick="javascript:removeInstalment2($(this));" class="fa fa-fw fa-lg fa-close"></i>' +
				'<i onclick="javascript:editInstalment2($(this));" class="fa fa-fw fa-lg fa-edit sum"></i>' +
				'<i onclick="javascript:editDate($(this));" class="fa fa-fw fa-lg fa-edit date"></i>' +
				
				'<div class="separater top"></div>' +
				'<div class="separater bottom"></div>' +

				'<div class="count"></div>' +
				'<div class="sum" data-sum="0">'+ symbol + ' ' + instalment.toLocaleString('en-IN') +'</div>' +
				'<div class="sum-desc"><span>' + symbol + ' ' + instalment.toLocaleString('en-IN') + '</span><input type="text" style="display:none;" placeholder="Please provide appropriate value!" pattern="/[^\d]+/" value="' + instalment.toLocaleString('en-IN') + '" /> + Instl Fee </div>' +
				'<div class="date" data-date="' + instalment_date() + '">' + instalment_date() + '</div>' +
				'<div class="date-desc"><span>Days From Previous</span><input type="text" style="display:none;" placeholder="Please provide appropriate value!" value="' + instalment_date() + '" /></div>' +
			'</div>';
		$('.instl-container .add').before(html);
		$('.instl-container .add').data('count',count);
		setInstalment2();
	}/*  else {
		alert('You have reached maximum instalment limit.');
	} */
}

function removeInstalment2(obj){
	// get the total instalments.
	var count = $('.instl-container .add').data('count');
	// first instalment cannot be removed.
	if( count == 1 || $(obj).parent('div.instl').hasClass('fst') ){
		alert('Cannot remove the instalment.');
	} else {
		// remove the instl div box.
		$(obj).parent('div.instl').remove();
		// update the instal counter.
		$('.instl-container .add').data('count',count - 1);
		// re intialize instalment counting.
		setInstalment2();
	}
}

function editInstalment2(obj){
	var currency = getCookie("currency"), symbol = getSymbol(currency);
	// if not in edit mode, enable edit mode
	if($(obj).siblings('div.sum-desc').children('span').css('display') == 'inline'){
		$(obj).siblings('div.sum-desc').children('span').hide();
		$(obj).siblings('div.sum-desc').children('input').show();
		$(obj).siblings('div.sum-desc').children('input').focus();
	} else {
		// if in edit mode, disable edit mode
		var nextInstalments = $(obj).parent('div.instl').nextUntil('div.add').length;
		var prevInstalments = $(obj).parent('div.instl').prevAll().length;
		
		if( nextInstalments > 0 ){
			// if next instalment box present.
			// total price.
			var price = $('#actual_offered_price').data('actual_offered_price'); 
			// price of current instalment box
			var currentValue = $(obj).siblings('div.sum-desc').children('input').val(); 
			
			if(isNaN(currentValue)){
				alert("Please provide price only.");
				return false;
			}
			
			if( prevInstalments > 0 ){
				// if previous instalment boxes present, then current value should be combination of all previous values and current value
				$(obj).parent('div.instl').prevAll().each(function(){
					var thisval = $(this).find('div.sum-desc input').val();
					currentValue = Number(currentValue) + Number(thisval);
				});
				// the above is done as upon edit of any instalment, only next instalments are adjusted as per total price.
			}
			if( price > 0 && currentValue <= price ){
				// only update next instalments.
				price = ( price - currentValue ) / nextInstalments;
				$(obj).parent('div.instl').nextUntil('div.add').each(function(){ 
					// set total value including instalment_fees
					var total = Number(price.round(2)) + Number(instalment_fees());
					$(this).find('div.sum').html(symbol + ' ' + price.round(2).getTotal().toLocaleString('en-IN'));
					$(this).find('div.sum').data('sum' ,price.round(2).getTotal());
					// set instalment value
					$(this).find('div.sum-desc span').html(symbol + ' ' + price.round(2).toLocaleString('en-IN'));
					$(this).find('div.sum-desc input').val(price.round(2));
				});
			} else {
				alert('Price mismatch. Please check the total instalment value.');
			}
		} else {
			// no next instalment box present
			// check if total instalments match the total value
			var finalPrice = 0;
			$('.instl-container .instl').not('.add').each(function(){
				finalPrice = Number(finalPrice) + Number($(this).find('div.sum-desc input').val());
			});
			if( finalPrice != $('#actual_offered_price').data('actual_offered_price') ){
				alert('Price mismatch. Please check the total instalment value.'); 
				$(obj).siblings('div.sum-desc').children('input').focus();
				return false;
			}
		}
		// set instalment values
		var total = Number($(obj).siblings('div.sum-desc').children('input').val());
		$(obj).siblings('div.sum-desc').children('input').hide();
		$(obj).siblings('div.sum-desc').children('span').html(symbol + ' ' + total.toLocaleString('en-IN')).show();
		// set total instalment value with instalment_fees
		$(obj).siblings('div.sum').html(symbol + ' ' + total.getTotal().toLocaleString('en-IN') ).show();
		$(obj).siblings('div.sum').data('sum' , total.getTotal());
	}	
}

function editDate(obj){
	// if not in edit mode, enable edit mode
	if($(obj).siblings('div.date-desc').children('span').css('display') == 'inline'){
		$(obj).siblings('div.date-desc').children('span').hide();
		$(obj).siblings('div.date-desc').children('input').show();
		$(obj).siblings('div.date-desc').children('input').focus();
	} else {
		// make and check changes
		var date = $(obj).siblings('div.date-desc').children('input').val();
		if(isNaN(date)){
			alert("Please provide proper date.");
			return false;
		}
		if( date < 7 || date > 30 ){
			alert('Please select date within 7 to 30 days only.');
			// $(obj).siblings('div.date-desc').children('input').focus(); 
			// for some reason, uncommeting above line causes infinite event loop in chrome.
			return false;
		}
		$(obj).siblings('div.date-desc').children('input').hide();
		$(obj).siblings('div.date-desc').children('span').html('Days From Previous').show();
		$(obj).siblings('div.date').data('date' , date);
		$(obj).siblings('div.date').html(date).show();
		// if in edit mode, disable edit mode
	}
}

function removeBox(box,event){
	if( box != 'box1'){
		// get the total instalments.
		var count = $('.instl-container .add').data('count');
		// remove the instl div box.
		$(".instl-container div.instl#"+box).remove();
		// update the instal counter.
		$('.instl-container .add').data('count',count - 1);
		// re intialize instalment counting.
		setInstalment2();
	}
	event.stopPropagation();
}

function searchUser(email){
	try { clearTimeout(search); }catch(err) { console.log('On search clear others.'); console.log(err); }
	$.ajax({ method: "POST", url: $("#payment-data").data("jaws-url") + "/webapi/backend/dashtemp/user.get", dataType: 'json',
		data: { email: email }
	}).done(function( msg ) {
		if(msg != 'undefined'){
			if (msg.status) {
				if(msg.name) $('#txt-name').val(msg.name).addClass('disable');
				if(msg.phone) $('#txt-phone').val(msg.phone).addClass('disable');
				if(msg.picture) $('#user-info .user-pic').css('background-image', "url('" + msg.picture + "')");
			}
			else {
				$('#txt-name').val("").removeClass('disable');
				$('#txt-phone').val("").removeClass('disable');
				$('#user-info .user-pic').css('background-image', "url('" + $("#payment-data").data("pic-default") + "')");
			}
		}
	});
}

function createSubscription(type,obj){
	if($(obj).hasClass("disabled")){
		alert("Please wait, we are processing the package.");
		return false;
	}
	var package_id = $("#payment-data").data("package-id");
	
	if( Number($("#specialization-price").data("specialization-price")) > 0 ){
		var sum_basic =  Number($("#courses-combo").data("combo")) + Number($("#specialization-price").data("specialization-price"));
	} else { 
		var sum_basic =  Number($("#courses-combo").data("combo")); 
	}
	var combo = getComboCode("courses"),
		combo_free = getComboCode("complimentary"),
		currency =  getCookie("currency"),
		sum_offered =  Number($("#actual_offered_price").data("actual_offered_price")),
		sum_total =  Number($("#net_payable").data("net_payable")),
		tax = (getCookie("currency") == "usd") ? Number($("#payment-data").data("tax-rate-usd")) : Number($("#payment-data").data("tax-rate-inr")),
		instl_fees = $("#instalment_fee").data("instalment_fee"),
		instl_total = $(".instl-container .add").data("count"),
		lead_email = $("#txt-email").val(),
		lead_name = $("#txt-name").val(),
		lead_phone = $("#txt-phone").val(),
		instl_arr = [],
		mode = $('.accordian-panel-select select[name="payment_mode"]').val(),
		creator_type = "agent",
		creator_token = $("#payment-data").data("creator-token"),
		creator_comment = $('.section-col.payment_comment input[name="payment_comment"]').val();
	
	var	courses_actual = $("#courses-actual").data("price"),
		courses_combo = $("#courses-combo").data("combo"),
		courses_discount = $("#courses-discount").data("discount"),
		payment_discount = $('.section-col input[name="total_discount"]').val(),
		tax_amount = Number(sum_basic).getTax(),
		discount_amount = Number(sum_basic).getDiscount(payment_discount),
		offered_amount = Number($("#actual_offered_price").data("actual_offered_price")),
		instalment_amount = instalment_fees(),
		net_payable = Number($("#net_payable").data("net_payable")),
		edit_offered = Number($("#editOfferedPrice").data("editOfferedPrice")),
		edit_tax = Number(edit_offered.getTax()),
		edit_discount = Number($("#total_discount").data("discount-amount")),
		edit_percent = Number($("#total_discount").data("discount-percent"));
		
		// initialize the 0th element as instalment array must start from 1st key.
		instl_arr.push(new Array("Start from index 1 pls")); 
		
	$('.instl-container .instl').not('.add').each(function(i,e){
		// update the key
		var key = Number(i) + Number(1);
		// create an array
		instl_arr[key] = {};
		instl_arr[key]['sum'] = $(this).find('div.sum').data('sum');
		instl_arr[key]['due_days'] = $(this).find('div.date').data('date');
	});
	
	if(!payment_discount){
		payment_discount = 0;
	}
		
	var save = { 
		'token': creator_token, 
		'package': { 
			'combo':combo, 
			'combo_free': combo_free, 
			'currency': currency, 
			'sum_basic':sum_basic, 
			'sum_offered': sum_offered, 
			'sum_total': sum_total, 
			'tax': tax, 
			'instl': instl_arr, 
			'instl_fees': instl_fees, 
			'instl_total': instl_total, 
			'email': lead_email, 
			'name': lead_name, 
			'phone': lead_phone, 
			'create_date': new Date(), 
			'creator_type': creator_type,
			'pay_mode': mode,
			'data_courses_actual': courses_actual,
			'data_courses_combo': courses_combo,
			'data_courses_discount': courses_discount,
			'data_payment_discount': payment_discount,
			'data_tax_amount': tax_amount,
			'data_discount_amount': discount_amount,
			'data_offered_amount': offered_amount,
			'data_instalment_amount': instalment_amount,
			'data_net_payable': net_payable,
			'data_edit_offered_price': edit_offered,
			'data_edit_discount_amount': edit_discount,
			'data_edit_discount_percent': edit_percent,
			'data_edit_tax_amount': edit_tax,
			'data_instalment_fees_inr': $("#payment-data").data("instalment-fees-inr"),
			'data_instalment_fees_usd': $("#payment-data").data("instalment-fees-usd"),
		},
		'persistence': {
			'package_id' : {
				'layer' : 'dynpepl',
				'type' : 'package'
			}
		}
	};
	// form comments field
	if( combo_free && !$('textarea[name="comments_combo_free"]').val() ){
		alert("Please provide reason for giving complimentary courses.");
		return false;
	}
	if( instl_total > 1 && !$('textarea[name="comments_instl"]').val() ){
		alert("Please provide reason for giving instalment.");
		return false;
	}
	if( edit_discount != 0 && !$('textarea[name="comments_discount"]').val() ){
		alert("Please provide reason for giving discount.");
		return false;
	}
	
	save['package']['creator_comment'] = {},
	save['package']['creator_comment']["instl"] = $('textarea[name="comments_combo_free"]').val();
	save['package']['creator_comment']["combo_free"] = $('textarea[name="comments_instl"]').val();
	save['package']['creator_comment']["discount"] = $('textarea[name="comments_discount"]').val();
	save['package']['creator_comment']["misc"] = creator_comment;
	
	if(typeof package_id != 'undefined') {
		save['package']['package_id'] = package_id;
	}
	
	// bundle id for specialization
	if( Number($("#specialization-price").data("specialization-price")) > 0 ){
		save['package']['bundle_id'] = $(".section-col select[name='specialization']").val();
		save['package']['data_bundle_price'] = $("#specialization-price").data("specialization-price");
		var bundle_obj = $(".section-col select[name='specialization']");
		save['package']['data_bundle_combo'] = $("option:selected", bundle_obj).data("courses");
	}
	
	if( !combo && !combo_free ) {
		alert("Please select courses.");
		return false;
	}

	if( type == "create" ){
		var sendUrl = $("#payment-data").data("jaws-url") + "/webapi/backend/dashtemp/package.send";
	} else {
		var sendUrl = $("#payment-data").data("jaws-url") + "/webapi/backend/dashtemp/package.create";
	}

	$.ajax({ method: "POST", url:sendUrl , dataType: "json", cache: false,	data: save, beforeSend: function(){ $(obj).addClass("disabled");  }
	}).done(function( response ) { 
		if( response.package_id ) $("#payment-data").data("package-id", response.package_id);
		if( type == "create" ){
			if(response.status == 0){
				alert("Oops! Seems there was some error. Please try again by refreshing the page.");
				$(obj).removeClass("disabled");
			} else if(response.status == 1){
				$(".section-boxed").each(function(i,e){
					$(this).find(".button").remove(".button");
				});
				alert("Package sent successfully.");
				$("#main-container").addClass("read-only");
			} else if(response.status == 2){
				$(".section-boxed").each(function(i,e){
					$(this).find(".button").remove(".button");
				});
				$("#main-container").addClass("read-only");
				alert("Package sent for approval.");
			} else {
				alert("Some error Occured!");
				$(obj).removeClass("disabled");
			}
		} else {
			if( response.package_id ){
				alert("Package Successfully Created");
				$(obj).removeClass("disabled"); 
			} else if( response.status == false ) {
				alert("Please check the form data.");
				$(obj).removeClass("disabled");
			} else {
				alert("Some error Occured.");
				$(obj).removeClass("disabled");
			}
		}
	}).fail(function() { $(obj).removeClass("disabled"); alert( "Oops! It seems that some error has occured. Please try refreshing the page or try after some time." );
	}).always(function( response ) { console.log(response); console.log("Completed Transaction."); });
}

$(document).ready(function(){

	
	$(document).on('blur','.instl-container .instl .sum-desc input',function(e) {
		editInstalment2($(this).parent('.sum-desc').siblings('i.sum'));
	});	

	$(document).on('keyup','.instl-container .instl .sum-desc input',function(e) {
		if (e.keyCode == 13) {
			// Do something
			editInstalment2($(this).parent('.sum-desc').siblings('i.sum'));
		}
	});
	
	$(document).on('keyup','.instl-container .instl .date-desc input',function(e) {
		/* if (e.keyCode == 13) {
			// Do something
			editDate($(this).parent('.date-desc').siblings('i.date'));
		} */
	});
	
	$(document).on('blur','.instl-container .instl .date-desc input',function(e) {
		editDate($(this).parent('.date-desc').siblings('i.date'));
	});
	
	$(document).on('blur','#editOfferedPrice input',function(e) {
		editOfferedPrice();
	});
	
	$(document).on('keyup','#editOfferedPrice input',function(e) {
		/* if (e.keyCode == 13) {
			editOfferedPrice();
		} */
	});

	$("#txt-email").keyup(function(e) {
		var email = this.value;

		try { clearTimeout(search); }
		catch(err) {}

		if (email.length > 5 && validateEmail(email)) {
		    	if (e.which == 13) searchUser(email);
		    	else search = setTimeout(function(){ searchUser(email); }, 500);
	   	}
	});

	$(document).on('change','.accordian-panel-select select[name="payment_mode"]',function(e) {
		var mode = $(this).val(), mode_text = $("option:selected", $(this)).text();
		if( mode == 'online' ){
			$('.section-col.payment_comment').hide();
		} else {
			$('.section-col.payment_comment').show();
		}
		$('#payment_mode_min').html(mode_text);
	});
	
	$(document).on('keyup mouseup','.section-col input[name="total_discount"]',function(e) {
		validateDiscount();
		calculatePayment();          
	});
	
	$(document).on('click','div.info-box',function(e) {
		e.stopPropagation();   
	});
});

$(document).ready(function() {
	// Course selector
	$("div.course").click(function() {
		var section = $(this).parent().parent().prop('id');
		if ($(this).hasClass('active')) {
			$(this).removeClass('active').removeClass('live').removeClass('video');
			$(this).children("div.mode").removeClass('active');
			removeCourses(section);
		}
		else {
			$(this).addClass('active');
			if ($(this).children("div.mode.live").length > 0) {
				$(this).children("div.mode.live").addClass('active');
				$(this).addClass('live'); 
				selectCourses(section);
			}
			else {
				$(this).children("div.mode.video").addClass('active');
				$(this).addClass('video');
				selectCourses(section);
			}
			populateInfoBox(section);
		}
	});

	$("div.course > div.mode.video").click(function(e) {
		e.stopPropagation();
		var section = $(this).parent().parent().parent().prop('id');
		if ($(this).hasClass('active')) {
			$(this).removeClass('active');
			$(this).parent().removeClass('active').removeClass('live').removeClass('video');
			removeCourses(section);
		}
		else {
			$(this).siblings().removeClass('active');
			$(this).addClass('active');
			$(this).parent().addClass('active').addClass('video').removeClass('live');
			selectCourses(section);
			populateInfoBox(section);
		}
	});

	$("div.course > div.mode.live").click(function(e) {
		e.stopPropagation();
		var section = $(this).parent().parent().parent().prop('id');
		if ($(this).hasClass('active')) {
			$(this).removeClass('active');
			$(this).parent().removeClass('active').removeClass('live').removeClass('video');
			removeCourses(section);
		}
		else {
			$(this).siblings().removeClass('active');
			$(this).addClass('active');
			$(this).parent().addClass('active').addClass('live').removeClass('video');
			selectCourses(section);
			populateInfoBox(section);
		}
	});

	// Course-Mini Selector
	$('div.info-box').click(function(e) {
		e.stopPropagation(); 
	});
	
});

//popup section
function confirmPayment(){
	$("#modal-container").addClass("active");
	$("body > div.wrapper").addClass("blur");
}

function updatePaymentDetails(obj){
	$(obj).siblings("input[type='checkbox']").each(function(){
		console.log($(this));
	});
}

// Unused now.
function initiateSelect(section){ console.log("Old function not used."); }function setInstalment(){ console.log("Set Instalment - Old function not used."); }function addInstalmentBox(){ console.log("Add Instalment Box - Old function not used."); }function removeInstalment(){ console.log("Remove Instalment - Old function not used."); }function editInstalment(){ console.log("Edit Instalment - Old function not used."); }
