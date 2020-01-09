<?php

/*
           8 8888       .8. `8.`888b                 ,8' d888888o.
           8 8888      .888. `8.`888b               ,8'.`8888:' `88.
           8 8888     :88888. `8.`888b             ,8' 8.`8888.   Y8
           8 8888    . `88888. `8.`888b     .b    ,8'  `8.`8888.
           8 8888   .8. `88888. `8.`888b    88b  ,8'    `8.`8888.
           8 8888  .8`8. `88888. `8.`888b .`888b,8'      `8.`8888.
88.        8 8888 .8' `8. `88888. `8.`888b8.`8888'        `8.`8888.
`88.       8 888'.8'   `8. `88888. `8.`888`8.`88'     8b   `8.`8888.
  `88o.    8 88'.888888888. `88888. `8.`8' `8,`'      `8b.  ;8.`8888
    `Y888888 ' .8'       `8. `88888. `8.`   `8'        `Y8888P ,88P'

    JIGSAW ACADEMY WORKFLOW SYSTEM v2
    ---------------------------------
*/

	// Prevent exclusive access
	if (!defined("JAWS")) {
	   	header('Location: https://www.jigsawacademy.com');
	   	die();
	}

  	// Connect to DB as soon as this library is loaded
  	// Requires DB config to be loaded beforehand
  	// Any queries executed using this PDO obj will throw errors as exception to be caught in Try/Catch blocks
  	// Error msgs will be stored in $GLOBALS["jaws_db"]["error"] for MySQL DB
  	// Error msgs will be stored in $GLOBALS["jaws_mongo"]["error"] for MongoDB

	try {
    		$GLOBALS["jaws_db"]["db"] = new PDO("mysql:host=".JAWS_DB_HOST.";dbname=".JAWS_DB_NAME, JAWS_DB_USER, JAWS_DB_PASS);
       		$GLOBALS["jaws_db"]["db"]->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    	}
	catch(PDOException $e) {
		die(nl2br("JAWS failed to connect to the MYSQL database.\n").$e->getMessage());
	}

	// Connect to Mongo DB if AutoLoad is set
	if (JAWS_MONGO_AUTOLOAD === true) mongo_init();

	// Functions pertaining to MySQL
	// --------------------

	// This will initialize the MySQL database
	function db_init() {
		try {
    			$GLOBALS["jaws_db"]["db"] = new PDO("mysql:host=".JAWS_DB_HOST.";dbname=".JAWS_DB_NAME, JAWS_DB_USER, JAWS_DB_PASS);
       			$GLOBALS["jaws_db"]["db"]->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
       			return true;
    		}
		catch(PDOException $e) {
			return $e->getMessage();
		}
	}

	// Escapes the string supplied
	function db_sanitize($str) { return $GLOBALS["jaws_db"]["db"]->quote($str); }

	// Sets the fetch mode on the result object
	function set_fetch_mode(&$res, $fetch_mode = -1) {

		if($fetch_mode == -1) $fetch_mode = $GLOBALS['jaws_db']["fetch_mode"];

		if ($fetch_mode == JAWS_DB_FETCHMODE_NAMED) $res->setFetchMode(PDO::FETCH_ASSOC);
		else if ($fetch_mode == JAWS_DB_FETCHMODE_INDEXED) $res->setFetchMode(PDO::FETCH_NUM);
		else $res->setFetchMode(PDO::FETCH_OBJ);
	}

	// This will query the DB and return a result
	function db_query($query, $fetch_mode = -1) {

		try {
			$res = $GLOBALS["jaws_db"]["db"]->query($query);
			set_fetch_mode($res, $fetch_mode);
			return $res->fetchAll();
		}
		catch (PDOException $e) {
			$GLOBALS["jaws_db"]["error"] = $e->getMessage();
			return false;
		}

	}

	// This will exec a SQL statement
	function db_exec($sql) {

		try {
			$res = $GLOBALS["jaws_db"]["db"]->exec($sql);
		}
		catch (PDOException $e) {
			$GLOBALS["jaws_db"]["error"] = $e->getMessage();
			activity_create("critical", "db.exec.fail", "fail", "",  "", "", "", $sql."|".$GLOBALS["jaws_db"]["error"], "logged");
			return false;
		}

		return true;

	}

	// This will create a prepared query PDO object
	function db_prepare($sql) {

		try {
			$res = $GLOBALS["jaws_db"]["db"]->prepare($sql);
		}
		catch (PDOException $e) {
			$GLOBALS["jaws_db"]["error"] = $e->getMessage();
			return false;
		}

		return $res;

	}

	// This will execute a prepared query PDO object after binding has been done
	function db_exec_prepared($obj) {

		try {
			$obj->execute();
		}
		catch (PDOException $e) {
			$GLOBALS["jaws_db"]["error"] = $e->getMessage();
			return false;
		}

		return true;

	}

	// This will query using a prepared PDO object after binding has been done
	function db_query_prepared($obj, $fetch_mode = -1) {

		try {
			$obj->execute();
			set_fetch_mode($obj, $fetch_mode);
			return $obj->fetchAll();
		}
		catch (PDOException $e) {
			$GLOBALS["jaws_db"]["error"] = $e->getMessage();
			return false;
		}

	}

	// This will return the last inserted auto-incremented value in the DB
	function db_get_last_insert_id() {
		//$res = db_query("SELECT LAST_INSERT_ID();");
		//return $res[0]["LAST_INSERT_ID()"];
		return $GLOBALS["jaws_db"]["db"]->lastInsertId();
	}

	// Functions pertaining to MongoDB
	// --------------------

	// This function will connect to the MongoDB Database
	function mongo_init() {
		if (!isset($GLOBALS["jaws_mongo"]["db"])) {
			try {
				$GLOBALS["jaws_mongo"]["db"] = new MongoDB\Driver\Manager(JAWS_MONGO_HOST);
			}
			catch(MongoDB\Driver\Exception\Exception $e) {
				die(nl2br("JAWS failed to connect to the MONGODB database.\n").$e->getMessage());
			}
		}
	}

	// This function will send a query to the MongoDB and return the result
	function mongo_query($collection=null, $filter=[], $opts=[]) {
		try {
			$query = new MongoDB\Driver\Query($filter, $opts);
			$res = $GLOBALS["jaws_mongo"]["db"]->executeQuery(JAWS_MONGO_NAME.(((isset($collection)) && (strlen($collection) > 0)) ? ".".$collection : ""), $query);
			return $res->toArray();
		}
		catch(MongoDB\Driver\Exception\Exception $e) {
			$GLOBALS["jaws_mongo"]["error"] = $e->getMessage();
			return false;
		}
	}

	// This function will send a command to the MongoDB and return the result
	function mongo_cmd($collection=null, $cmd=[]) {
		try {
			$command = new MongoDB\Driver\Command($cmd);
			$res = $GLOBALS["jaws_mongo"]["db"]->executeCommand(JAWS_MONGO_NAME.(((isset($collection)) && (strlen($collection) > 0)) ? ".".$collection : ""), $command);
			return $res->toArray();
		}
		catch(MongoDB\Driver\Exception\Exception $e) {
			$GLOBALS["jaws_mongo"]["error"] = $e->getMessage();
			return false;
		}
	}

	// This function will initialize a BulkWrite Object
	function mongo_bulk_init() {
		try {
			return new MongoDB\Driver\BulkWrite;
		}
		catch(MongoDB\Driver\Exception\Exception $e) {
			$GLOBALS["jaws_mongo"]["error"] = $e->getMessage();
			return false;
		}
	}

	// This function will execute a BulkWrite
	function mongo_bulk_exec($collection=null, $bulkobj) {
		try {
			$res = $GLOBALS["jaws_mongo"]["db"]->executeBulkWrite(JAWS_MONGO_NAME.(((isset($collection)) && (strlen($collection) > 0)) ? ".".$collection : ""), $bulkobj);
			return $res->toArray();
		}
		catch(MongoDB\Driver\Exception\Exception $e) {
			$GLOBALS["jaws_mongo"]["error"] = $e->getMessage();
			return false;
		}
	}

?>