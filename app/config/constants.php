<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//constants
define("DEBUG_LOG",'/var/log/genriclead.log');
define("ITEMS_PER_PAGE", 100);
define("BATCHES_PERIOD", 30);

define("ACTIVE", 1);
define("INACTIVE", 0);

//lead square


define("LS_CORPORATE", "corporate");
define("LS_RETAIL", "retail");
define("LS_RETAIL_NEW", "retail_new");

$lsOldKeyMapping = [
     "lead_id" => "ProspectID",
            "email" => "EmailAddress",
            "name" => "FirstName",
            "phone" => "Phone",
            "source_2" => "Source",
            "page_url" => "Website",
            "page_url_2" => "mx_Website_URL",
            "utm_campaign" => "mx_UTM_Campaign",
            "utm_medium" => "mx_UTM_Medium",
            "utm_source" => "mx_UTM_Source",
            "utm_term" => "mx_UTM_Term",
            "comments" => "mx_Comments",
            "company" => "mx_Company",
            "reassigned_at" => "mx_Lead_assigned_date",
            "alt_email_1" => "mx_Alt_Email_1",
            "alt_phone_1" => "Mobile",
            "alt_email_2" => "mx_Alt_Email_2",
            "alt_phone_2" => "mx_Alt_Phone_2",
            /* "category" => "mx_Source", */
            /* "source_2_2" => "mx_Source2", */
            /* "channel" => "mx_Sub_Source", */
            "path_finder_thingy" => "mx_Website_URL",
            "page_url_3" => "mx_Website_Form",
            "mx_City" => "mx_City",
            "mx_Location" => "mx_Location",
            "course" => "Course",
            "mx_Preferred_date" => "mx_Preferred_date"
];
define('LS_OLD_KEY_MAPPING', $lsOldKeyMapping);

$lsNewKeyMapping = [
    "lead_id" => "ProspectID",
    "email" => "EmailAddress",
    "name" => "FirstName",
    "phone" => "Phone",
    "source_2" => "Source",
   // "page_url" => "Website",
    "page_url" => "mx_Latest_Website_URL", //JA-172 fix
   // "page_url_2" => "mx_Website_URL",
    //"utm_campaign" => "SourceCampaign",
     "utm_campaign" => "mx_UTM_Campaign",//JA-172 fix
    //"utm_medium" => "SourceMedium",
    "utm_medium" => "mx_UTM_Medium",//JA-172 fix
    "utm_source" => "mx_UTM_Source",
    "utm_term" => "mx_UTM_Term",
    "comments" => "mx_Comments",
    "company" => "Company",
    "reassigned_at" => "mx_Assigned_date",
    "alt_email_1" => "mx_Alt_Email_1",
    "alt_phone_1" => "Mobile",
    "alt_email_2" => "mx_Alt_Email_2",
    "alt_phone_2" => "mx_Alt_Phone_1",
    /* "category" => "mx_Source", */
    /* "source_2_2" => "mx_Source2", */
    /* "channel" => "mx_Sub_Source", */
    "path_finder_thingy" => "mx_Website_URL",
    "page_url_3" => "mx_Website_Form",
    "mx_City" => "mx_Location",
    //"mx_Location" => "mx_Location",
    "course" => "mx_Course",
	"Experience" => "mx_Total_Experience",
    "mx_Preferred_date" => "mx_Preferred_date"
];

define('LS_KEY_MAPPING', $lsNewKeyMapping);

//LS Dashboard status
//leads_basic
define('LEAD_PROCESSING', 11);
define('BASIC_NEW', 0);
define('BASIC_PROCESSED', 1);
define('COMPILED_NEW', 1);
define('COMPILED_API', 2);
define('COMPILED_SUCCESS', 3);
define('COMPILED_FAILURE', 4);
define('COMPILED_NO_RESPONSE', 5);
define('OLD_LEAD', 9);
