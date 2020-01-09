<?php

register_shutdown_function(function() {
	var_dump(error_get_last());
});

load_library("email");

var_dump(send_email("wp.formsubmit", ["to" => "himanshu@jigsawacademy.com"], ["fname" => "Himanshu", "text" => "lol", "header" => "Hmm", "sub-header" => "Yay!"]));

?>