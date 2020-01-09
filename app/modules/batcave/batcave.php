<?php

	function batcave_load_component($name) {
		scan_dir($name);
	}

	function batcave_load_components() {
		batcave_scan_dir("all");
	}

	function batcave_scan_dir($name) {

		$name = $name == "all" || empty($name) ? "" : "/".$name;
		$dir = "app/modules/batcave".$name;
		$file_list = array_diff(scandir($dir), array('..', '.', "batcave.php"));
		foreach($file_list as $file_name) {

			if (is_dir("app/modules/batcave".$name."/".$file_name)) {
				batcave_scan_dir($name."/".$file_name);
			}
			else {
				require_once "app/modules/batcave".$name."/".$file_name;
			}

		}

	}

?>