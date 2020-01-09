$.receiveMessage(function(e){
	if (e.data == "min") {
		$("#chatWindow").height("64px");
	}
	else {
		$("#chatWindow").height("400px");
	}
},"https://ailab.jigsawacademy.com");