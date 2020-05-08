<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//LeadSqaure
define("LS_DOMAIN", "https://api-in21.leadsquared.com/v2/");
define("LS_CAPTURE_API", "LeadManagement.svc/Lead.Capture");

// Corporate keys
define("LS_CORP_AccessKey", "u\$r98364e50e96ad22f9ce3f40f2f2b3597");
define("LS_CORP_SecretKey", "75dfc985eefeb4c92a6eaeebb517155d049e840a");

define("LS_AccessKey", "u\$r6daf2e31c28ab58d15cb696d4e0f6a43");
define("LS_SecretKey", "95c34e5756021c31bc0ded96a0fafd70320cd9a2");
        
//define("LS_ACCOUNT_RETAIL_NEW_ACCESS",'u$ra77415d9fb3b4476342af0bea19a6b57');
//define("LS_ACCOUNT_RETAIL_NEW_SECRET",'1b49151116bf144653207c80d7df51692847ceed');
//Disabling it on May 8, 2020 @2.30PM
//define("LS_ACCOUNT_RETAIL_NEW_ACCESS",'u$rc7986d7d4dfc4a76a4123b7d92a5f379');
//define("LS_ACCOUNT_RETAIL_NEW_SECRET",'cff6d61151836432d652775cea7c3e6088211ba1');
define("LS_ACCOUNT_RETAIL_NEW_ACCESS",'u$rc9e44fac33d7cdbd37e0bc08633faa14');
define("LS_ACCOUNT_RETAIL_NEW_SECRET",'c695c883ec8aa10bc9b11700d6865cd616077f71');

//Basic Lead error log file name with path
define("BASIC_LEAD_LOG",'/var/log/apache2/leadcron/basiclead.log');
define("LEAD_LOG",'/var/log/apache2/leadcron/genriclead.log');
//Compiled Lead error log file name with path
define("COMPILED_LEAD_LOG",'/var/log/apache2/leadcron/compiledlead.log');
define("LS_API_LOG",'/var/log/apache2/leadcron/lsapi.log');