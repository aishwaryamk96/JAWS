<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//URL declartions
define("APP_ENV", "devuat");
define("HTTP_PROTOCOL","http");
define("HTTPS_PROTOCOL","https");
//for checking the server protocol
//in case of need of forcing http protocol , suppose in dev or local machine
// check $SERVER['SERVER_PROTOCOL'] variable and set it as needed
//default to https, changes to http for dev invrionment
 $protocol = constant("HTTPS_PROTOCOL");

$basePrefix =  '';
if(APP_ENV == "dev"){
    $protocol = constant("HTTP_PROTOCOL");
    $basePrefix = "www.";
}

define("SERVER_PROTOCOL",$protocol);
//@TODO check HTTP_HOST also,for safer side
define("DOMAIN_TEXT","dev.jigsawacademy.com");
define("DOMAIN","dev.jigsawacademy.com");
define("BASEURL",$basePrefix.DOMAIN);

define("WEBSITE_URL", SERVER_PROTOCOL."://". constant("BASEURL"));

define("ACCOUNTS_HOST","accounts.".DOMAIN);
define("ACCOUNTS_URL",SERVER_PROTOCOL."://".ACCOUNTS_HOST);

define("BATCAVE_HOST", "batcave.".DOMAIN);
define("BATCAVE_URL", SERVER_PROTOCOL."://".BATCAVE_HOST);

define("JAWS_PATH", "jaws");
define("JAWS_HOST", BASEURL."/jaws");
define("JAWS_URL", SERVER_PROTOCOL."://".BASEURL."/jaws");

define("BATCAVE_API", "/btcapi");

//below to be replaced going forward.
define("JAWS_PATH_LOCAL",JAWS_PATH);
define("JAWS_PATH_WEB", JAWS_URL);
define("JAWS_VERSION","2.0");

define("MAILS_TO","medini.m@datamatics.digital.com");
