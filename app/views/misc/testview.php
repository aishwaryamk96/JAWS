<?php
	header('Content-Type: text/event-stream');
	header("Cache-Control: no-cache");
	header("Pragma: no-cache");
	header('Connection: keep-alive');

	load_plugin("mongodb");
	//$time = date('r');
    	//error_reporting(E_ALL); // ini_set('display_errors', 1);
	//$GLOBALS['jaws_exec_live'] = false; 
    	load_plugin("mongodb");
	$lastInserted =  (new MongoDB\Client())->jaws->test->findOne();
	foreach($lastInserted as $res)
	{
		echo "data: {$res->m}\n";
	}
	//ob_flush();
	flush();
?>