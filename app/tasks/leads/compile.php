<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// Prevent exclusive access
	if (!defined("JAWS")) { 
		header('Location: '.WEBSITE_URL);
		die();
	}

	load_module("leads");