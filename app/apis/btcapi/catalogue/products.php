<?php

	authorize_api_call("", true);

	die(json_encode(["categories" => products_all()]));

?>