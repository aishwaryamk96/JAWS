<?php

	function user_can($verb, $object) {

		$edit = false;
		if ($verb == "view") {
			$edit = auth_session_is_allowed("batcave.edit.$object");
		}

		return auth_session_is_allowed("batcave.$verb.$object") || $edit;

	}

?>