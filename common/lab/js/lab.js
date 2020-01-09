jq(document).ready(function() {
	var timeout = 240000;
	var remaining = timeout;
	var timer;
	jq("#start").click(function() {
		jq.post("", {start: 1}, function(response) {console.log(response)});
		jq(".parent").addClass("min");
		jq("#footer").addClass("min");
		jq("div#blog").addClass("active");
		startTimer();
		jq("div#blog-wrapper").html('<div style="margin: 0 auto; width: 500px; text-align:center; font-size: 19px; color: rgba(0,0,0,0.25); position: relative; top: calc(40vh + 180px);"><i class="fa fa-lg fa-2x fa-circle-o-notch fa-spin"></i><br /><br /><span style="position:relative;top:-5px;">Loading Jigsaw Blog</span></div>')
		//Fetch blog
		jq.ajax({ type: "POST", url: "https://www.jigsawacademy.com/jaws/labapi/blog", data: {}})
			.done(function(msg) {
				jq("div#blog-wrapper").html(msg);

				//Assign all the handlers
				jq(document).on("click", "div.blog-article", function(event){
					jq(event.currentTarget).addClass("max");
					jq("div#content").addClass("no-show");
					event.stopPropagation()
				});

				jq(document).on("click", "div.article-close", function(event){
					jq("div.blog-article").removeClass("max");
					jq("div#content").removeClass("no-show");
					event.stopPropagation()
				});
			});
	});
	jq("#close").click(function() {
		jq.post("", {close: 1}, function(response) {
			if (response == 1) {
				jq(".parent").removeClass("launch");;
			}
		});
	});
	function formatTime(t) {
  		var mins = Math.floor(t / 60000);
  		t = t % 60000;
  		var secs = Math.floor(t / 1000);
  		var fmins = String(mins);
  		var fsecs = String(secs);
  		while (fmins.length < 2) {fmins = '0' + fmins;}
  		while (fsecs.length < 2) {fsecs = '0' + fsecs;}
  		return fmins + ':' + fsecs;
	}
	function startTimer() {
		remaining -= 1000;
		if (remaining <= 0) {
			stopTimer();
			return;
		}
		jq("#timer").html(formatTime(remaining));
		timer = setTimeout(function() {startTimer();}, 1000);
	}
	function stopTimer() {
		clearTimeout(timer);
		jq(".parent").removeClass("min");
		jq("#footer").removeClass("min");
		jq("div#blog").removeClass("active");
		jq(".parent").addClass("launch");
	}
});