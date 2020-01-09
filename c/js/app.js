$(document).ready(function() {
	var post = function(url, data, success, failure) {
		$.post("/api/v1/" + url, data)
			.done(success)
			.fail(failure);
	};
	var toggleControls = function(disable = false) {
		if (disable) {
			$("#userInput").attr("disabled", true);
			$("#send").attr("disabled", true);
		}
		else {
			$("#userInput").attr("disabled", false);
			$("#send").attr("disabled", false);
		}
	};
	(function() {
		toggleControls(true);
		post("init", {i:1}, function(d) {
			d = $.parseJSON(d);
			createMsg(d.r, 1, 0, d.o);
			toggleControls();
		});
	})();
	$('body').bootstrapMaterialDesign();
	var chatOpen = true;
	$("#chatToggle").click(function() {
		if (chatOpen) {
			$(this).children("i").html("keyboard_arrow_up");
			$(".chat-body").addClass("hidden");
			$.postMessage("min","https://batcave.jigsawacademy.com",parent);
			chatOpen = false;
		}
		else {
			$(this).children("i").html("keyboard_arrow_down");
			$(".chat-body").removeClass("hidden");
			$.postMessage("max","https://batcave.jigsawacademy.com",parent);
			chatOpen = true;
		}
	});
	var sendMessage = function(msg) {
		toggleControls(true);
		post("post", {m: msg}, function() {
			createMsg(msg);
			pollMsg();
		}, function() {
			createMsg(msg, 0);
			toggleControls();
			$("#userInput").focus();
		});
	}
	var createMsg = function(msg, status = 1, user = 1, options = []) {
		var msgElem = $("#msg").clone(true, true).attr("id", "").removeClass("hidden");
		msgElem.find("#text").html(msg).attr("id", "");
		msgElem.find("#status").addClass(user ? "" : "flex-row-reverse").children("label").html(status ? (new Date).toLocaleTimeString() : 'Failed').addClass(status ? 'text-muted' : 'text-danger').attr("id", "");
		if (!user) {
			msgElem.find("#photoUrl").removeClass("ml-2").addClass("mr-2").children("img").attr("src", "https://chat.jigsawacademy.com/media/jaws/frontend/images/favicon.png").attr("id", "");
			msgElem.find("#msgContainer").addClass("flex-row-reverse");
			var optsElem = msgElem.find("#options").attr("id", "");
			if (options.length > 0) {
				options.forEach(function(o) {
					optsElem.append(createOption(o));
				});
				optsElem.removeClass("hidden");
			}
		}
		else {
			msgElem.find("#options").remove();
		}
		msgElem.find("#msgContainer").attr("id", "");
		$("#msgList").append(msgElem);
		$("#userInput").val("");
		$("html, body").animate({ scrollTop: $(document).height() }, 1000);
		msgElem.addClass("show");
	}
	var createOption = function(opt) {
		var optElem = $("#option").clone(true, true).attr("id", "").removeClass("hidden");
		optElem.find("button").html(opt);
		return optElem;
	}
	var retry = 1;
	var pollMsg = function() {
		post("response", {r:retry}, function(d) {
			d = $.parseJSON(d);
			if (d.w) {
				if (retry == 1) {
					createMsg(d.r, 1, 0);
				}
				retry++;
				setTimeout(pollMsg, 1000);
				return;
			}
			retry = 1;
			createMsg(d.r, 1, 0, d.o);
			toggleControls();
			$("#userInput").focus();
		}, function() {
			retry++;
			if (retry > 3) {
				return createMsg("Something went wrong...", 0, 0);
			}
			setTimeout(pollMsg, 1000);
		});
	}
	$("#send").click(function() {
		var msg = $("#userInput").val();
		if (msg != '') {
			sendMessage(msg);
		}
		$("#userInput").focus();
	});
	$(".btn-opt").click(function() {
		var text = $(this).html();
		// if ($(this).children().length > 0) {
		// 	text = this.childNodes[0].data;
		// }
		sendMessage(text);
	});
});