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

    // This plugin exposes the original object oriented 'phpexcel' library
    // This also has functions that ease misc tasks related to spreadsheets and works like a wrapper
    // ---------------

    // Load stuff
    require_once("app/plugins/phpexcel/PHPExcel.php");

    register_shutdown_function("failure");

    // This function creates a excel/pdf file
    // This takes one array as input - [0] => { ["title"] => {}, ["cols"] => {}, ["data"] => {} }
    // The parent array represents each spreadsheet
    // Each spreadsheet has properties, column headers and column data
    // If column headers are not present, they are extracted automatically from the data (in case of direct DB_Query result)
    // This will optionally output it to the php output via the file download header
    function phpexcel_write($sheets, $docprop = null, $filename = null) {

        // Create new object
        $phpexcel = new PHPExcel();

        // Set document properties
        $phpexcel->getProperties()
            ->setCreator("JAWS v".JAWS_VERSION)
            ->setLastModifiedBy("JAWS v".JAWS_VERSION)
            ->setTitle(isset($docprop["title"]) ? $docprop["title"] : "Document Title Not Set")
            ->setSubject(isset($docprop["subject"]) ? $docprop["subject"] : "Doc Subject Not Set")
            ->setDescription(isset($docprop["desc"]) ? $docprop["desc"] : "Doc Description Not Set")
            ->setKeywords(isset($docprop["keywords"]) ? $docprop["keywords"] : "Doc Keywords Not Set")
            ->setCategory(isset($docprop["category"]) ? $docprop["category"] : "Doc category Not Set");

        // Add sheets and populate them
        $sheet_count = 0;
        foreach($sheets as $sheet) {
            phpexcel_sheet_add($phpexcel, $sheet, (!($sheet_count == 0)));
            $sheet_count ++;
        }

        // Set first sheet to active
        $phpexcel->setActiveSheetIndex(0);

        // Save or return
        if (isset($filename)) {
            header_download('application/vnd.ms-excel', $filename);
            $objWriter = PHPExcel_IOFactory::createWriter($phpexcel, 'Excel5');
            $objWriter->save('php://output');
        }

        // return the object
        else return $phpexcel;

    }

    // This will add a new spreadsheet to the existing PHPExcel object
    // It will intelligently add column headers if not present
    function phpexcel_sheet_add(&$phpexcel, $sheet, $allowadd = false) {

        // Create new sheet ?
        $sheet_count = $phpexcel->getSheetCount();
        $sheet_current = $sheet_count;
        if ($sheet_count == 1) {
            if ($allowadd) {
                $phpexcel->createSheet($sheet_count);
            }
            else {
                $sheet_current = 0;
            }
        }
        else {
            $phpexcel->createSheet($sheet_count);
        }
        $phpexcel->setActiveSheetIndex($sheet_current);

        // Add Cols
        $cols = array();
        if (!isset($sheet["cols"])) {
            foreach($sheet["data"][0] as $key => $value) {
                array_push($cols, $key);
            }
        }
        else {
            $cols = $sheet["cols"];
        }

        // Prep
        $phpexcel->getActiveSheet()->setTitle(isset($sheet["title"]) ? $sheet["title"] : "Sheet Title Not Set, Shit!");
        if (count($cols) <= 26) {
            $alphas = range('A', 'Z');
        }
        else {
            $alphas = column_range_get("ZZ");
        }
        $alpha_count = 0;
        $num_count = 1;

        foreach($cols as $col) {
            $phpexcel->getActiveSheet()->setCellValue($alphas[$alpha_count].strval($num_count), $col);
            $phpexcel->getActiveSheet()->getStyle($alphas[$alpha_count].strval($num_count))->getFont()->setBold(true); // Set Col Heading Style
            $alpha_count ++;
        }

        // Add Data
        foreach($sheet["data"] as $row) {
            $alpha_count = 0;
            $num_count ++;

            foreach($row as $value) {
                $phpexcel->getActiveSheet()->setCellValue($alphas[$alpha_count].strval($num_count), $value);
                $alpha_count ++;
            }
        }

        // All done
    }

    function column_range_get($last_col = null) {

        $range = range("A", "Z");
        $cols = range("A", "Z");

        foreach ($range as $first_char) {

            foreach ($range as $second_char) {

                if ($last_col != null && $first_char.$second_char == $last_col) {

                    $cols[] = $first_char.$second_char;
                    return $cols;

                }

                $cols[] = $first_char.$second_char;

            }

        }

        return $cols;

    }

    function failure() {
        // var_dump(error_get_last());
    }

?>
