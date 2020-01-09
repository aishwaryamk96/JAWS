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

    JIGSAW ACADEMY WORKFLOW SYSTEM v1
    ---------------------------------
*/

    // Prevent exclusive access
	if (!defined("JAWS")) {

		header('Location: ../index.php');
		die();

	}

	function zip_archive_error_string_get($error_code) {

		switch($error_code) {

			/*case ZipArchive::ZIP_ER_OK: return "No error";
			case ZipArchive::ZIP_ER_MULTIDISK: return "Multi-disk zip archives not supported";
			case ZipArchive::ZIP_ER_RENAME: return "Renaming temporary file failed";
			case ZipArchive::ZIP_ER_CLOSE: return "Closing zip archive failed";
			case ZipArchive::ZIP_ER_SEEK: return "Seek error";
			case ZipArchive::ZIP_ER_READ: return "Read error";
			case ZipArchive::ZIP_ER_WRITE: return "Write error";
			case ZipArchive::ZIP_ER_CRC: return "CRC error";
			case ZipArchive::ZIP_ER_ZIPCLOSED: return "Containing zip archive was closed";
			case ZipArchive::ZIP_ER_NOENT: return "No such file";
			case ZipArchive::ZIP_ER_EXISTS: return "File already exists";
			case ZipArchive::ZIP_ER_OPEN: return "Can't open file";
			case ZipArchive::ZIP_ER_TMPOPEN: return "Failure to create temporary file";
			case ZipArchive::ZIP_ER_ZLIB: return "Zlib error";
			case ZipArchive::ZIP_ER_MEMORY: return "Malloc failure";
			case ZipArchive::ZIP_ER_CHANGED: return "Entry has been changed";
			case ZipArchive::ZIP_ER_COMPNOTSUPP: return "Compression method not supported";
			case ZipArchive::ZIP_ER_EOF: return "Premature EOF";
			case ZipArchive::ZIP_ER_INVAL: return "Invalid argument";
			case ZipArchive::ZIP_ER_NOZIP: return "Not a zip archive";
			case ZipArchive::ZIP_ER_INTERNAL: return "Internal error";
			case ZipArchive::ZIP_ER_INCONS: return "Zip archive inconsistent";
			case ZipArchive::ZIP_ER_REMOVE: return "Can't remove file";
			case ZipArchive::ZIP_ER_DELETED: return "Entry has been deleted";*/

			case 0: return "No error";
			case 1: return "Multi-disk zip archives not supported";
			case 2: return "Renaming temporary file failed";
			case 3: return "Closing zip archive failed";
			case 4: return "Seek error";
			case 5: return "Read error";
			case 6: return "Write error";
			case 7: return "CRC error";
			case 8: return "Containing zip archive was closed";
			case 9: return "No such file";
			case 10: return "File already exists";
			case 11: return "Can't open file";
			case 12: return "Failure to create temporary file";
			case 13: return "Zlib error";
			case 14: return "Malloc failure";
			case 15: return "Entry has been changed";
			case 16: return "Compression method not supported";
			case 17: return "Premature EOF";
			case 18: return "Invalid argument";
			case 19: return "Not a zip archive";
			case 20: return "Internal error";
			case 21: return "Zip archive inconsistent";
			case 22: return "Can't remove file";
			case 23: return "Entry has been deleted";

		}

	}

?>