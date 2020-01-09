<?php

	function payment_info_change($payment) {

	}

	function payment_transfer_subs($pay_id, $subs_id) {

		$pay_id = db_sanitize($pay_id);
		$subs_id = db_sanitize($subs_id);

		db_exec("UPDATE payment SET subs_id = $subs_id WHERE pay_id = $pay_id;");
		db_exec("UPDATE payment_instl SET subs_id = $subs_id WHERE pay_id = $pay_id;");
		db_exec("UPDATE payment_link SET subs_id = $subs_id WHERE pay_id = $pay_id;");

	}

?>