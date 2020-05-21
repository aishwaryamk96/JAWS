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
		header('Location: https://www.jigsawacademy.com');
		die();
	}

	// Load stuff
	load_library("payment");
	load_module("user");
	load_module("course");
	load_module("subs");
	load_module("ui");

	// Init Session
	auth_session_init();

	// Prep
	$user;
	$login_params["return_url"] = JAWS_PATH_WEB."/setupaccess";

        $mFlag = $_GET["m"];
        
	// Check Auth
	if (isset($_GET["user"])) {

		$user = user_get_by_webid($_GET["user"]);
		if (!$user) {        

			ui_render_msg_front(array(
				"type" => "error",
				"title" => "A problem ran into you :(",
				"header" => "Oops !",
				"text" => "Sorry, but this link seems to be invalid!<br /><br />Please contact our support team for assistance."
			));
	
			exit();

		}

		$login_params["return_url"] .= "?user=".$_GET["user"];

		 // Log Parse
		activity_create("ignore", "lms.setup", "parse", "-", 0, "user_id", $user["user_id"], "Setup Access Link Parsed", "logged");

		// Someone is logged in
		if (auth_session_is_logged()) { 

			// Partial account check
			if (strcmp($user["status"], "pending") == 0) {

				// Havent been offered options to Log in correctly before link is 'attached'
				if (!isset($_COOKIE["attach_partial"])) {

					auth_session_logout();
					setcookie("attach_partial",'1');

					// Offer login options here (to attach the link to the right account) .......................
					activity_create("ignore", "lms.setup", "login", "-", 0, "user_id", $user["user_id"], "Setup Access Login Attempt", "logged");
					ui_render_login_front(array(
						"mode" => "create",
						"reauth" => true,
						"return_url" => $login_params["return_url"],
						"text" => "Please sign-in or register your new account.<br/>Select your account on a social network for this.<br/>Note: Access to your course materials will be available using this account."
						));

					exit();
				}

				// Offered options already
				else {

					// Auth - restrict internal Jigsaw teams from accidentally attaching links
					if ((isset($_SESSION["user"]["roles"]["feature_keys"]["subs.paylink.parse"])) && (!auth_session_is_allowed("subs.paylink.parse")))  {        

						ui_render_msg_front(array(
							"type" => "error",
							"title" => "A problem ran into you :(",
							"header" => "Oops !",
							"text" => "Sorry, but either this link cannot be used by employees of Jigsaw Academy, or you do not have the required priviledges."
						));
					
						exit();

					}

					// Proceed with attach
					user_attach_partial($_SESSION["user"]["user_id"], $user["user_id"]);
					setcookie("attach_partial","", time() - 3600);

					// Refresh info
					auth_session_logout();
					auth_session_login_forced($user["email"]);
					$user = user_get_by_id($_SESSION["user"]["user_id"]);

					// Proceed to Setup Page Here
				}

			}

			// Link belongs to normal account
			else {

				// Link does not belong to the account current logged in
				if ($user["user_id"] != $_SESSION["user"]["user_id"]) {
					auth_session_logout();
					activity_create("ignore", "lms.setup", "login", "-", 0, "user_id", $user["user_id"], "Setup Access Login Attempt", "logged");
					ui_render_login_front(array(
								"mode" => "login",
								"reauth" => true,
								"return_url" => $login_params["return_url"],
								"text" => "This link does not belong to the account you are signed-in to.<br />Please sign-in to the correct account."
							));

					exit();
				}
			
				// Link belongs to logged in normal account               
				// Proceed to Setup Page           

			}

		}    

		// Not logged in
		else {

			// Check - links belonging to partial accounts
			if (strcmp($user["status"],"pending") == 0) {

				setcookie("attach_partial",'1');

				activity_create("ignore", "lms.setup", "login", "-", 0, "user_id", $user["user_id"], "Setup Access Login Attempt", "logged");
				ui_render_login_front(array(
							"mode" => "create",
							"reauth" => true,
							"return_url" => $login_params["return_url"],
							"text" => "Please sign-in or register your new account.<br/>Select your account on a social network for this.<br/>Note: Access to your course materials will be available using this account."
						));
				
				exit();
			}

			// Link belongs to normal account
			else {

				activity_create("ignore", "lms.setup", "login", "-", 0, "user_id", $user["user_id"], "Setup Access Login Attempt", "logged");
				ui_render_login_front(array(
					"mode" => "login",
					"reauth" => true,
					"return_url" => $login_params["return_url"],
					"text" => "Please sign-in to your account."
				));

				exit();

			}

		}

	}

	// User param not set
	else {

		// No one is logged in
		if (!auth_session_is_logged()) { 

			ui_render_login_front(array(
				"mode" => "login",
				"reauth" => true,
				"return_url" => $login_params["return_url"],
				"text" => "Please sign-in to your account."
				));

			exit();

		}           

		// Someone is logged in
		else $user = user_get_by_id($_SESSION["user"]["user_id"]);

	}

	// Log
	activity_create("ignore", "lms.setup", "setup", "-", 0, "user_id", $user["user_id"], "Setting Up Access", "logged");

	// Alternate displayed msgs for first payment/instl/setup-complete - get payment status
	$instl_count = 0;
	$flag_paid = true;
	if (isset($_SESSION["temp"]["lms.setup.alt"])) {
		$flag_paid = true;
		$instl_count = $_SESSION["temp"]["lms.setup.alt"];
		unset($_SESSION["temp"]["lms.setup.alt"]);
	}
	else $flag_paid = false;

	// Alternate displayed msgs for first payment/instl/setup-complete - get setup status
	$flag_navigate = true;
        if($mFlag != 1){
            if (!isset($user["lms_soc"]) || (strlen($user["lms_soc"]) == 0)) $flag_navigate = true;
            else {

                    // display message for lms setup already done
                    if (!$flag_paid) {

                            ui_render_msg_front(array(
                                    "type" => "error",
                                    "title" => "Setup Your Access",
                                    "header" => "Setup Complete !",
                                    "text" => "You have already setup your access to the Learning Center. If you are unable to login, please contact our support team.<br /><br />Note that it takes upto 24 hours for us to enable your access once the setup is complete."
                                    ));

                            exit();
                    }
                    else $flag_navigate = false;

            }
        }
	// Proceed with rendering the UI
	ui_render_head_front(array(
		"title" => ($flag_paid ? "Payment Successful" : "Setup Your Access"),
		"scripts" => array(1 => "app/templates/jaws/frontend/modal.js"),
		"styles" => array(1 => "app/templates/jaws/frontend/modal.css")
		));

	?>

		<div id="bkg-img"> </div>
		<div id="bkg-overlay"> </div>

		<div class="modal">

		 <div class="page bkg active">  
                     
                                <?php if($mFlag == 1){  ?>
                     <div class="header"><?php echo "ACKNOWLEDGEMENT";?></div>
                                <?php }else{ ?>
                                <div class="header"><?php echo ($flag_paid ? (($instl_count == 1) ? "Congratulations" : "Payment Successfull") : "Setup Your Access"); ?></div>
				<?php	} ?>
				<div class="text">
					<br/>
                                        <?php if($mFlag == 1){ ?>
                                            Congratulations on you enrollment! Please check you email for confirmation of payment. Our Admissions team will get in touch with you shortly.
                                            
                                       
					<?php }else if ($flag_paid) {

						if ($instl_count > 1) {

							?>
							We have received your payment for this installment.<br />
							<?php if (!$flag_navigate) { ?> Thank you!<br /> <?php }
							
						}
						else {

							?>
							You've successfully subscribed to our course.<br/>
							<?php

						}

						if ($flag_navigate) { 
						   
							if ($instl_count > 1) { ?> Please take a moment to provide us with a few details. <br /> <?php }
							else { ?> Now setup your access to our learning center to get started with your course materials.<br /> <?php } ?>
							
							<a class="button skewed" id="btn-begin">
								<span class="button-main-text">LET'S GO</span>
								<span class="button-main-arrow-image">
									<img class="image-icon" src="<?php echo JAWS_PATH_WEB.'/media/jaws/frontend/images/long-arrow-orange.png'; ?>">
								</span>

							</a>

						<?php } else { 

							if ($instl_count > 1) {

							?>

							<?php } else { ?>

							We will email you the details of your Jigsaw Learning Center account within 24 hours. 
							For any assistance, do not hesistate to call us on +91 90192-17000, we are eager to help!<br /><br />
							Happy Learning!

							<?php } 
						}

					} else { ?>

						Hello <?php echo substr($_SESSION["user"]["name"], 0, strpos($_SESSION["user"]["name"], " ")); ?>,<br />
						Please take a moment to provide us with a few details.<br />

						<a class="button skewed" id="btn-begin">
							<span class="button-main-text">LET'S GO</span>
							<span class="button-main-arrow-image">
								<img class="image-icon" src="<?php echo JAWS_PATH_WEB.'/media/jaws/frontend/images/long-arrow-orange.png'; ?>">
							</span>
						</a>

					<?php } ?>                  

				</div>
			</div>

			<?php if ($flag_navigate) { ?>

			<div class="page">              
				<div class="header">personal info</div>
				<div class="sub-header"><?php echo $user["name"]; ?></div>
					
				<div class="text">
					
					<div class="panel">
						<label for="phone">Phone <sup style="font-size: 15px; color: red;">*</sup></label>
						<input type="text" id="txt-phone" name="phone" class="field" value="<?php echo $user["phone"]; ?>" />
						<label class="error" for="phone"></label>
	
						<label for="age">Age</label>
						<select id="select-age" name="age" class="field">
							<option value="< 20">Less than 20</option>
							<option value="20 - 24" selected>20 - 24</option>
							<option value="25 - 30">25 - 30</option>
							<option value="31 - 35">31 - 35</option>
							<option value="36 - 40">36 - 40</option>
							<option value="> 40">Above 40</option>
						</select>
						<label class="error" for="age"></label>
					</div>

					<div class="panel">
						<label for="email" title="We will send you important links and notifications to this inbox."><i class="fa fa-info-circle fa-lg" title="We will send you important links and notifications to this inbox."></i>Communication Email <sup style="font-size: 15px; color: red;">*</sup></label>
						<input type="text" id="txt-email" name="email" class="field" value="<?php echo $user["email"]; ?>" />
						<label class="error" for="email"></label>
	
						<label for="gender">Gender</label>
						<select id="select-gender" name="gender" class="field">
					   

							<option value="male">Male</option>
							<option value="female" <?php 
								$fname = substr($user["name"], 0, strpos($user["name"], " ")); 
								$lastchar = substr($fname, -1);
								if ((strcmp("a", $lastchar) == 0) || (strcmp("i", $lastchar) == 0)) echo "selected"; 
							?>>Female</option>
							<option value="other">Other</option>
						</select>
						<label class="error" for="gender"></label>
					</div>

				</div>
			</div>

			<div class="page">              
				<div class="header">personal info</div>
				<div class="sub-header">Location</div>
					
				<div class="text">
					
					<div class="panel" style="float: left;">
						<label for="country">Country <sup style="font-size: 15px; color: red;">*</sup></label>
						<select id="select-country" name="country" class="field">
							<option value="ABW">Aruba</option>
							<option value="AFG">Afghanistan</option>
							<option value="AGO">Angola</option>
							<option value="AIA">Anguilla</option>
							<option value="ALA">Åland Islands</option>
							<option value="ALB">Albania</option>
							<option value="AND">Andorra</option>
							<option value="ANT">Netherlands Antilles</option>
							<option value="ARE">United Arab Emirates</option>
							<option value="ARG">Argentina</option>
							<option value="ARM">Armenia</option>
							<option value="ASM">American Samoa</option>
							<option value="ATA">Antarctica</option>
							<option value="ATF">French Southern Territories</option>
							<option value="ATG">Antigua and Barbuda</option>
							<option value="AUS">Australia</option>
							<option value="AUT">Austria</option>
							<option value="AZE">Azerbaijan</option>
							<option value="BDI">Burundi</option>
							<option value="BEL">Belgium</option>
							<option value="BEN">Benin</option>
							<option value="BFA">Burkina Faso</option>
							<option value="BGD">Bangladesh</option>
							<option value="BGR">Bulgaria</option>
							<option value="BHR">Bahrain</option>
							<option value="BHS">Bahamas</option>
							<option value="BIH">Bosnia and Herzegovina</option>
							<option value="BLM">Saint Barthélemy</option>
							<option value="BLR">Belarus</option>
							<option value="BLZ">Belize</option>
							<option value="BMU">Bermuda</option>
							<option value="BOL">Bolivia</option>
							<option value="BRA">Brazil</option>
							<option value="BRB">Barbados</option>
							<option value="BRN">Brunei Darussalam</option>
							<option value="BTN">Bhutan</option>
							<option value="BVT">Bouvet Island</option>
							<option value="BWA">Botswana</option>
							<option value="CAF">Central African Republic</option>
							<option value="CAN">Canada</option>
							<option value="CCK">Cocos (Keeling) Islands</option>
							<option value="CHE">Switzerland</option>
							<option value="CHL">Chile</option>
							<option value="CHN">China</option>
							<option value="CIV">Côte d`Ivoire</option>
							<option value="CMR">Cameroon</option>
							<option value="COD">Congo, the Democratic Republic of the</option>
							<option value="COG">Congo</option>
							<option value="COK">Cook Islands</option>
							<option value="COL">Colombia</option>
							<option value="COM">Comoros</option>
							<option value="CPV">Cape Verde</option>
							<option value="CRI">Costa Rica</option>
							<option value="CUB">Cuba</option>
							<option value="CXR">Christmas Island</option>
							<option value="CYM">Cayman Islands</option>
							<option value="CYP">Cyprus</option>
							<option value="CZE">Czech Republic</option>
							<option value="DEU">Germany</option>
							<option value="DJI">Djibouti</option>
							<option value="DMA">Dominica</option>
							<option value="DNK">Denmark</option>
							<option value="DOM">Dominican Republic</option>
							<option value="DZA">Algeria</option>
							<option value="ECU">Ecuador</option>
							<option value="EGY">Egypt</option>
							<option value="ERI">Eritrea</option>
							<option value="ESH">Western Sahara</option>
							<option value="ESP">Spain</option>
							<option value="EST">Estonia</option>
							<option value="ETH">Ethiopia</option>
							<option value="FIN">Finland</option>
							<option value="FJI">Fiji</option>
							<option value="FLK">Falkland Islands (Malvinas)</option>
							<option value="FRA">France</option>
							<option value="FRO">Faroe Islands</option>
							<option value="FSM">Micronesia, Federated States of</option>
							<option value="GAB">Gabon</option>
							<option value="GBR">United Kingdom</option>
							<option value="GEO">Georgia</option>
							<option value="GGY">Guernsey</option>
							<option value="GHA">Ghana</option>
							<option value="GIN">N Guinea</option>
							<option value="GIB">Gibraltar</option>
							<option value="GLP">Guadeloupe</option>
							<option value="GMB">Gambia</option>
							<option value="GNB">Guinea-Bissau</option>
							<option value="GNQ">Equatorial Guinea</option>
							<option value="GRC">Greece</option>
							<option value="GRD">Grenada</option>
							<option value="GRL">Greenland</option>
							<option value="GTM">Guatemala</option>
							<option value="GUF">French Guiana</option>
							<option value="GUM">Guam</option>
							<option value="GUY">Guyana</option>
							<option value="HKG">Hong Kong</option>
							<option value="HMD">Heard Island and McDonald Islands</option>
							<option value="HND">Honduras</option>
							<option value="HRV">Croatia</option>
							<option value="HTI">Haiti</option>
							<option value="HUN">Hungary</option>
							<option value="IDN">Indonesia</option>
							<option value="IMN">Isle of Man</option>
							<option value="IND" selected>India</option>
							<option value="IOT">British Indian Ocean Territory</option>
							<option value="IRL">Ireland</option>
							<option value="IRN">Iran, Islamic Republic of</option>
							<option value="IRQ">Iraq</option>
							<option value="ISL">Iceland</option>
							<option value="ISR">Israel</option>
							<option value="ITA">Italy</option>
							<option value="JAM">Jamaica</option>
							<option value="JEY">Jersey</option>
							<option value="JOR">Jordan</option>
							<option value="JPN">Japan</option>
							<option value="KAZ">Kazakhstan</option>
							<option value="KEN">Kenya</option>
							<option value="KGZ">Kyrgyzstan</option>
							<option value="KHM">Cambodia</option>
							<option value="KIR">Kiribati</option>
							<option value="KNA">Saint Kitts and Nevis</option>
							<option value="KOR">Korea, Republic of</option>
							<option value="KWT">Kuwait</option>
							<option value="LAO">Lao People`s Democratic Republic</option>
							<option value="LBN">Lebanon</option>
							<option value="LBR">Liberia</option>
							<option value="LBY">Libyan Arab Jamahiriya</option>
							<option value="LCA">Saint Lucia</option>
							<option value="LIE">Liechtenstein</option>
							<option value="LKA">Sri Lanka</option>
							<option value="LSO">Lesotho</option>
							<option value="LTU">Lithuania</option>
							<option value="LUX">Luxembourg</option>
							<option value="LVA">Latvia</option>
							<option value="MAC">Macao</option>
							<option value="MAF">Saint Martin (French part)</option>
							<option value="MAR">Morocco</option>
							<option value="MCO">Monaco</option>
							<option value="MDA">Moldova</option>
							<option value="MDG">Madagascar</option>
							<option value="MDV">Maldives</option>
							<option value="MEX">Mexico</option>
							<option value="MHL">Marshall Islands</option>
							<option value="MKD">Macedonia, the former Yugoslav Republic of</option>
							<option value="MLI">Mali</option>
							<option value="MLT">Malta</option>
							<option value="MMR">Myanmar</option>
							<option value="MNE">Montenegro</option>
							<option value="MNG">Mongolia</option>
							<option value="MNP">Northern Mariana Islands</option>
							<option value="MOZ">Mozambique</option>
							<option value="MRT">Mauritania</option>
							<option value="MSR">Montserrat</option>
							<option value="MTQ">Martinique</option>
							<option value="MUS">Mauritius</option>
							<option value="MWI">Malawi</option>
							<option value="MYS">Malaysia</option>
							<option value="MYT">Mayotte</option>
							<option value="NAM">Namibia</option>
							<option value="NCL">New Caledonia</option>
							<option value="NER">Niger</option>
							<option value="NFK">Norfolk Island</option>
							<option value="NGA">Nigeria</option>
							<option value="NIC">Nicaragua</option>
							<option value="NOR">R Norway</option>
							<option value="NIU">Niue</option>
							<option value="NLD">Netherlands</option>
							<option value="NPL">Nepal</option>
							<option value="NRU">Nauru</option>
							<option value="NZL">New Zealand</option>
							<option value="OMN">Oman</option>
							<option value="PAK">Pakistan</option>
							<option value="PAN">Panama</option>
							<option value="PCN">Pitcairn</option>
							<option value="PER">Peru</option>
							<option value="PHL">Philippines</option>
							<option value="PLW">Palau</option>
							<option value="PNG">Papua New Guinea</option>
							<option value="POL">Poland</option>
							<option value="PRI">Puerto Rico</option>
							<option value="PRK">Korea, Democratic People`s Republic of</option>
							<option value="PRT">Portugal</option>
							<option value="PRY">Paraguay</option>
							<option value="PSE">Palestinian Territory, Occupied</option>
							<option value="PYF">French Polynesia</option>
							<option value="QAT">Qatar</option>
							<option value="REU">Réunion</option>
							<option value="ROU">Romania</option>
							<option value="RUS">Russian Federation</option>
							<option value="RWA">Rwanda</option>
							<option value="SAU">Saudi Arabia</option>
							<option value="SDN">Sudan</option>
							<option value="SEN">Senegal</option>
							<option value="SGP">Singapore</option>
							<option value="SGS">South Georgia and the South Sandwich Islands</option>
							<option value="SHN">Saint Helena</option>
							<option value="SJM">Svalbard and Jan Mayen</option>
							<option value="SLB">Solomon Islands</option>
							<option value="SLE">Sierra Leone</option>
							<option value="SLV">El Salvador</option>
							<option value="SMR">San Marino</option>
							<option value="SOM">Somalia</option>
							<option value="SPM">Saint Pierre and Miquelon</option>
							<option value="SRB">Serbia</option>
							<option value="STP">Sao Tome and Principe</option>
							<option value="SUR">Suriname</option>
							<option value="SVK">Slovakia</option>
							<option value="SVN">Slovenia</option>
							<option value="SWE">Sweden</option>
							<option value="SWZ">Swaziland</option>
							<option value="SYC">Seychelles</option>
							<option value="SYR">Syrian Arab Republic</option>
							<option value="TCA">Turks and Caicos Islands</option>
							<option value="TCD">Chad</option>
							<option value="TGO">Togo</option>
							<option value="THA">Thailand</option>
							<option value="TJK">Tajikistan</option>
							<option value="TKL">Tokelau</option>
							<option value="TKM">Turkmenistan</option>
							<option value="TLS">Timor-Leste</option>
							<option value="TON">Tonga</option>
							<option value="TTO">Trinidad and Tobago</option>
							<option value="TUN">Tunisia</option>
							<option value="TUR">Turkey</option>
							<option value="TUV">Tuvalu</option>
							<option value="TWN">Taiwan, Province of China</option>
							<option value="TZA">Tanzania, United Republic of</option>
							<option value="UGA">Uganda</option>
							<option value="UKR">Ukraine</option>
							<option value="UMI">United States Minor Outlying Islands</option>
							<option value="URY">Uruguay</option>
							<option value="USA">United States</option>
							<option value="UZB">Uzbekistan</option>
							<option value="VAT">Holy See (Vatican City State)</option>
							<option value="VCT">Saint Vincent and the Grenadines</option>
							<option value="VEN">Venezuela</option>
							<option value="VGB">Virgin Islands, British</option>
							<option value="VIR">Virgin Islands, U.S.</option>
							<option value="VNM">Viet Nam</option>
							<option value="VUT">Vanuatu</option>
							<option value="WLF">Wallis and Futuna</option>
							<option value="WSM">Samoa</option>
							<option value="YEM">Yemen</option>
							<option value="ZAF">South Africa</option>
							<option value="ZMB">Zambia</option>
							<option value="ZWE">Zimbabwe</option>
						</select>
						<label class="error" for="country"></label>
	
						<label for="city">City <sup style="font-size: 15px; color: red;">*</sup></label>
						<input type="text" id="txt-city" name="city" class="field" value="" />
						<label class="error" for="city"></label>
					</div>

					<div class="panel">
                        <label for="state">State <sup style="font-size: 15px; color: red;">*</sup></label>
                        <select id="select-state" name="state" class="field">
                            <option value="">Please Select</option>
                            <optgroup label="States">
                                <option value="andhra-pradesh">Andhra Pradesh</option>
                                <option value="arunachal-pradesh">Arunachal Pradesh</option>
                                <option value="assam">Assam</option>
                                <option value="bihar">Bihar</option>
                                <option value="chhattisgarh">Chhattisgarh</option>
                                <option value="goa">Goa</option>
                                <option value="gujurat">Gujurat</option>
                                <option value="haryana">Haryana</option>
                                <option value="himachal-pradesh">Himachal Pradesh</option>
                                <option value="jammu-and-kashmir">Jammu & Kashmir</option>
                                <option value="jharkhand">Jharkhand</option>
                                <option value="karnataka" selected>Karnataka</option>
                                <option value="kerala">Kerala</option>
                                <option value="madhya-pradesh">Madhya Pradesh</option>
                                <option value="maharashtra">Maharashtra</option>
                                <option value="manipur">Manipur</option>
                                <option value="meghalaya">Meghalaya</option>
                                <option value="mizoram">Mizoram</option>
                                <option value="nagaland">Nagaland</option>
                                <option value="odisha">Odisha</option>
                                <option value="punjab">Punjab</option>
                                <option value="rajasthan">Rajasthan</option>
                                <option value="sikkim">Sikkim</option>
                                <option value="tamil-nadu">Tamil Nadu</option>
                                <option value="telangana">Telangana</option>
                                <option value="tripura">Tripura</option>
                                <option value="uttar-pradesh">Uttar Pradesh</option>
                                <option value="west-bengal">West Bengal</option>
                            </optgroup>
                            <optgroup label="Union Territories">
                                <option value="andaman-and-nicobar-islands">Andaman & Nicobar Islands</option>
                                <option value="chandigarh">Chandigarh</option>
                                <option value="dadra-and-nagar-haveli">Dadra & Nagar Haveli</option>
                                <option value="daman-and-diu">Daman & Diu</option>
                                <option value="delhi">The Government of NCT of Delhi</option>
                                <option value="lakshadweep">Lakshadweep</option>
                                <option value="puducherry">Puducherry</option>
                            </optgroup>
                        </select>
						<!-- <select id="select-state" name="state" class="field">
							<option value="Andaman and Nicobar Islands">Andaman and Nicobar Islands</option>
							<option value="Andhra Pradesh">Andhra Pradesh</option>
							<option value="Arunachal Pradesh">Arunachal Pradesh</option>
							<option value="Assam">Assam</option>
							<option value="Bihar">Bihar</option>
							<option value="Chandigarh">Chandigarh</option>
							<option value="Chhattisgarh">Chhattisgarh</option>
							<option value="Dadra and Nagar Haveli">Dadra and Nagar Haveli</option>
							<option value="Daman and Diu">Daman and Diu</option>
							<option value="Delhi">Delhi</option>
							<option value="Goa">Goa</option>
							<option value="Gujarat">Gujarat</option>
							<option value="Haryana">Haryana</option>
							<option value="Himachal Pradesh">Himachal Pradesh</option>
							<option value="Jammu and Kashmir">Jammu and Kashmir</option>
							<option value="Jharkhand">Jharkhand</option>
							<option value="Karnataka" selected>Karnataka</option>
							<option value="Kerala">Kerala</option>
							<option value="Lakshadweep">Lakshadweep</option>
							<option value="Madhya Pradesh">Madhya Pradesh</option>
							<option value="Maharashtra">Maharashtra</option>
							<option value="Manipur">Manipur</option>
							<option value="Meghalaya">Meghalaya</option>
							<option value="Mizoram">Mizoram</option>
							<option value="Nagaland">Nagaland</option>
							<option value="Orissa">Orissa</option>
							<option value="Pondicherry">Pondicherry</option>
							<option value="Punjab">Punjab</option>
							<option value="Rajasthan">Rajasthan</option>
							<option value="Sikkim">Sikkim</option>
							<option value="Tamil Nadu">Tamil Nadu</option>
							<option value="Tripura">Tripura</option>
							<option value="Uttaranchal">Uttaranchal</option>
							<option value="Uttar Pradesh">Uttar Pradesh</option>
							<option value="West Bengal">West Bengal</option>
						</select> -->
						<label class="error" for="state"></label>
	
						<label for="zipcode">Zipcode</label>
						<input type="text" id="txt-zipcode" name="zipcode" class="field" value="" />
						<label class="error" for="zipcode"></label>
					</div>

				</div>
			</div>

			<div class="page">              
				<div class="header">Personal Info</div>
				<div class="sub-header">career</div>
					
				<div class="text">
					<div class="panel center">
						<label for="qualification">Highest Degree</label>
						<select id="select-qualification" name="qualification" class="field">
							<option value="B.Com">B.Com</option>
							<option value="B.E. / B.Tech / M.Tech" selected>B.E. / B.Tech / M.Tech</option>
							<option value="MBA / PGDM / PGDBM">MBA / PGDM / PGDBM</option>
							<option value="B.Sc / B.Pharm">B.Sc / B.Pharm</option>
							<option value="BCA / MCA">BCA / MCA</option>
							<option value="Ph.D">Ph.D</option>
							<option value="Other">Other</option>
						</select>
						<label class="error" for="qualification"></label>
	
						<label for="experience">Work Experience</label>
						<select id="select-experience" name="experience" class="field">
							<option value="0 - 1 Year">0 - 1 Year</option>
							<option value="1 - 3 Years" selected>1 - 3 Years</option>
							<option value="3 - 5 Years">3 - 5 Years</option>
							<option value="5 - 8 Years">5 - 8 Years</option>
							<option value="8 - 10 Years">8 - 10 Years</option>
							<option value="More than 10 years">More than 10 years</option>
						</select>
						<label class="error" for="experience"></label>
					</div>              

				</div>
			</div>

			<div class="page">              
				<div class="header">Short Survey</div>
				<div class="sub-header">1 / 4</div>
					
				<div class="text">
					Why did you choose a course in Analytics? <span class="light">(Select all that apply)</span>
					<br />
					<label class="error alt" for="why"></label>
					<br />
					
					<div style="margin-left: 35px;">
						<input type="checkbox" name="chk-why" value="Passion" id="ichk-why-1"/><label for="ichk-why-1"><span><i class="fa fa-fw fa-lg fa-check"></i></span>&nbsp;I am passionate about analytics</label><br/>
						<input type="checkbox" name="chk-why" value="Job Raise" id="ichk-why-2"/><label for="ichk-why-2"><span><i class="fa fa-fw fa-lg fa-check"></i></span>&nbsp;It will help me to move ahead in my current company</label><br/>
						<input type="checkbox" name="chk-why" value="Role requirement" id="ichk-why-3"/><label for="ichk-why-3"><span><i class="fa fa-fw fa-lg fa-check"></i></span>&nbsp;My current role requires knowledge in analytics</label><br/>
						<input type="checkbox" name="chk-why" value="Problem Solving" id="ichk-why-4"/><label for="ichk-why-4"><span><i class="fa fa-fw fa-lg fa-check"></i></span>&nbsp;I want to use more analytics in my current role</label><br/>
						<input type="checkbox" name="chk-why" value="Job Search" id="ichk-why-5"/><label for="ichk-why-5"><span><i class="fa fa-fw fa-lg fa-check"></i></span>&nbsp;I want to get a job in the field of analytics</label>                   
					</div>

				</div>
			</div>

			<div class="page">              
				<div class="header">Short Survey</div>
				<div class="sub-header">2 / 4</div>
					
				<div class="text">
					How did you get to know about Jigsaw Academy? <span class="light">(Select all that apply)</span>
					<br />
					<label class="error alt" for="marketing"></label>
					<br />
					
					<div class="panel" style="margin-left: 35px; margin-right: -150px; margin-top: -15px;">
						<input type="checkbox" name="chk-marketing" value="Google"id="ichk-marketing-1"/><label for="ichk-marketing-1"><span><i class="fa fa-fw fa-lg fa-check"></i></span>&nbsp;Google</label><br/>
						<input type="checkbox" name="chk-marketing" value="Bing"id="ichk-marketing-2"/><label for="ichk-marketing-2"><span><i class="fa fa-fw fa-lg fa-check"></i></span>&nbsp;Bing</label><br/>
						<input type="checkbox" name="chk-marketing" value="Facebook"id="ichk-marketing-3"/><label for="ichk-marketing-3"><span><i class="fa fa-fw fa-lg fa-check"></i></span>&nbsp;Facebook</label><br/>
						<input type="checkbox" name="chk-marketing" value="LinkedIn"id="ichk-marketing-4"/><label for="ichk-marketing-4"><span><i class="fa fa-fw fa-lg fa-check"></i></span>&nbsp;LinkdIn</label><br/>
						<input type="checkbox" name="chk-marketing" value="Twitter"id="ichk-marketing-5"/><label for="ichk-marketing-5"><span><i class="fa fa-fw fa-lg fa-check"></i></span>&nbsp;Twitter</label><br/>
						<input type="checkbox" name="chk-marketing" value="Google ads"id="ichk-marketing-6"/><label for="ichk-marketing-6"><span><i class="fa fa-fw fa-lg fa-check"></i></span>&nbsp;Google ads</label>
					</div>

					<div class="panel" style="margin-right: -35px; margin-top: -15px;">
						<input type="checkbox" name="chk-marketing" value="Banner ads"id="ichk-marketing-7"/><label for="ichk-marketing-7"><span><i class="fa fa-fw fa-lg fa-check"></i></span>&nbsp;Banner ads</label><br/>
						<input type="checkbox" name="chk-marketing" value="Newspaper"id="ichk-marketing-8"/><label for="ichk-marketing-8"><span><i class="fa fa-fw fa-lg fa-check"></i></span>&nbsp;Newspaper</label><br/>
						<input type="checkbox" name="chk-marketing" value="PRWEB"id="ichk-marketing-10"/><label for="ichk-marketing-10"><span><i class="fa fa-fw fa-lg fa-check"></i></span>&nbsp;PRWEB</label><br/>
						<input type="checkbox" name="chk-marketing" value="Alumni referral"id="ichk-marketing-11"/><label for="ichk-marketing-11"><span><i class="fa fa-fw fa-lg fa-check"></i></span>&nbsp;Alumni ref.</label><br/>
						<input type="checkbox" name="chk-marketing" value="College Magazine"id="ichk-marketing-14"/><label for="ichk-marketing-14"><span><i class="fa fa-fw fa-lg fa-check"></i></span>&nbsp;Magazine</label><br/>
						<input type="checkbox" name="chk-marketing" value="Other"id="ichk-marketing-15"/><label for="ichk-marketing-15"><span><i class="fa fa-fw fa-lg fa-check"></i></span>&nbsp;Other</label>
					</div>  

				</div>
			</div>

			<div class="page">              
				<div class="header">Short Survey</div>
				<div class="sub-header">3 / 4</div>
					
				<div class="text">
					How did you get in touch with us? <span class="light">(Select one)</span>
					<br />
					<label class="error alt" for="enquiry"></label>
					<br />

					<div class="panel" style="margin-left: 35px; margin-right: -100px; margin-top: 30px;">
						<input type="radio" name="opt-enquiry" value="Website Form" id="iopt-enquiry-1"/><label for="iopt-enquiry-1"><span><i class="fa fa-fw fa-lg fa-check"></i></span>&nbsp;Filled a form on website</label><br/>
						<input type="radio" name="opt-enquiry" value="Phone" id="iopt-enquiry-2"/><label for="iopt-enquiry-2"><span><i class="fa fa-fw fa-lg fa-check"></i></span>&nbsp;Called</label><br/>
						<input type="radio" name="opt-enquiry" value="Chat" id="iopt-enquiry-3"/><label for="iopt-enquiry-3"><span><i class="fa fa-fw fa-lg fa-check"></i></span>&nbsp;Did a chat session</label><br/>
					</div>

					<div class="panel" style="margin-right: -35px; margin-top: 30px;">
						<input type="radio" name="opt-enquiry" value="Social Media" id="iopt-enquiry-4"/><label for="iopt-enquiry-4"><span><i class="fa fa-fw fa-lg fa-check"></i></span>&nbsp;Social media</label><br/>
						<input type="radio" name="opt-enquiry" value="Office" id="iopt-enquiry-5"/><label for="iopt-enquiry-5"><span><i class="fa fa-fw fa-lg fa-check"></i></span>&nbsp;Office walk-in</label><br/>
						<input type="radio" name="opt-enquiry" value="Other" id="iopt-enquiry-6"/><label for="iopt-enquiry-6"><span><i class="fa fa-fw fa-lg fa-check"></i></span>&nbsp;Other</label>
					</div>  

				</div>
			</div>

			 <div class="page">              
				<div class="header">Short Survey</div>
				<div class="sub-header">4 / 4</div>
					
				<div class="text">
					Which of these helped you make up your mind to enroll? <span class="light">(Select all that apply)</span>
					<br />
					<label class="error alt" for="sales"></label>
					<br />

					<div class="panel" style="margin-left: 35px; margin-top: 15px;">
						<input type="checkbox" name="chk-sales" value="Online Demo" id="ichk-sales-1"/><label for="ichk-sales-1"><span><i class="fa fa-fw fa-lg fa-check"></i></span>&nbsp;Attending the online demo session</label><br/>
						<input type="checkbox" name="chk-sales" value="Students" id="ichk-sales-2"/><label for="ichk-sales-2"><span><i class="fa fa-fw fa-lg fa-check"></i></span>&nbsp;Talking to other students</label><br/>
						<input type="checkbox" name="chk-sales" value="Website" id="ichk-sales-3"/><label for="ichk-sales-3"><span><i class="fa fa-fw fa-lg fa-check"></i></span>&nbsp;Based on information on the website</label><br/>
						<input type="checkbox" name="chk-sales" value="Jigsaw Team" id="ichk-sales-4"/><label for="ichk-sales-4"><span><i class="fa fa-fw fa-lg fa-check"></i></span>&nbsp;Communicating with Jigsaw team on email</label>
					</div>

					<div class="panel" style="margin-right: -35px; margin-top: 15px;">
						<input type="checkbox" name="chk-sales" value="Free Class" id="ichk-sales-5"/><label for="ichk-sales-5"><span><i class="fa fa-fw fa-lg fa-check"></i></span>&nbsp;Attending the 1st class for free</label><br/>
						<input type="checkbox" name="chk-sales" value="Testimonials or Reviews" id="ichk-sales-6"/><label for="ichk-sales-6"><span><i class="fa fa-fw fa-lg fa-check"></i></span>&nbsp;Reading online testimonials and reviews</label><br/>                         
						<input type="checkbox" name="chk-sales" value="Other" id="ichk-sales-7"/><label for="ichk-sales-7"><span><i class="fa fa-fw fa-lg fa-check"></i></span>&nbsp;Other</label><br/>
						<input type="checkbox" name="chk-sales" value="" id="ichk-sales-8" style="visibility:hidden"/><label for="ichk-sales-8" style="visibility: hidden;"><span><i class="fa fa-fw fa-lg fa-check"></i></span></label>
					</div>  

				</div>
			</div>

			<div class="page">              
				<div class="header">Learning Center</div>
				<div class="sub-header">Set Up your access</div>
					
				<div class="text">
					Which social account do you want to use to log in to the Learning Center?<br />
					<span class="light" style="color: rgba(0,0,0,0.25); font-weight: 700; font-size: 12px; text-transform: uppercase; position: relative; top: -7px;">Note: You cannot go back or select a different account after you click submit.</span>
					<br/><br/>
					
					<div style="display: block; margin: 25px auto; width: 70%; position: relative; left: 5%">
						<div id="soc-fb" class="social-radio" title="Select Facebook"><i class="fa fa-facejbook-square fa-fw fa-4x"></i><input type="hidden" class="soc-info" style="display:none;" value="<?php echo (isset($user["soc_fb"]) ? $user["soc_fb"] : ""); ?>" /><span style="display: block; font-size: 40%; font-weight: bold; text-transform: uppercase; text-align: center;">Facebook</span></div>
						<div id="soc-gp" class="social-radio" title="Select Google+"><i class="fa fa-goojgle-plus-square fa-fw fa-4x"></i><input type="hidden" class="soc-info" style="display:none;" value="<?php echo (isset($user["soc_gp"]) ? $user["soc_gp"] : ""); ?>" /><span style="display: block; font-size: 40%; font-weight: bold; text-transform: uppercase; text-align: center;">Google+</span></div>
						<div id="soc-li" class="social-radio" title="Select linkedin"><i class="fa fa-linkjedin-square fa-fw fa-4x"></i><input type="hidden" class="soc-info" style="display:none;" value="<?php echo (isset($user["soc_li"]) ? $user["soc_li"] : ""); ?>" /><span style="display: block; font-size: 40%; font-weight: bold; text-transform: uppercase; text-align: center;">LinkedIn</span></div>
					</div>  

					<label class="error" for="soc" style="width: 100%; text-align: center; position: relative; top: 5px; left: -15px;"></label>

				</div>
			</div>

			<div class="page bkg">              
				<div class="header">Done !</div>
					
				<div class="text">
					<br />

					Thanks, that's all the information we need for now.<br /><br/>
					Our team of little minions is busy setting up your access to the Jigsaw Learning Center. Once done, we will notify you by email.<br/><br/>
					
					<!-- Jigsaw Learning Center is undergoing schedules maintenance from 25<sup>th</sup> Oct 9 am to 26<sup>th</sup> Oct 6pm (IST). You will be able to access your learning account after the maintenance period.<br/><br/>
					Hey, don't worry, we are setting up your access and will ensure that you don't loose anytime. Your course access period will start only from 27<sup>th</sup> Oct.<br/><br/>
					An email will be sent to you with all access details. -->
					
					Happy Learning!                    

				</div>
			</div>

			<?php } ?>

			<div class="nav">
                                <?php if($mFlag == 1){ ?>
                                    
                                    <div class="panel left">					
					<div class="link-button active" id="btn-mind-website">Back to website</div>
                                    </div>
                                <?php }else{ ?>
				<div class="panel left">
					<?php if (($flag_navigate) && ($flag_paid)) { ?><div class="link-button red active" id="btn-later">I'll do this later on</div> <?php } ?>
					<div class="link-button <?php echo ((!$flag_navigate) ? 'active' : ''); ?>" id="btn-website">Back to website</div>
				</div>
                                <?php } ?>
				<?php if ($flag_navigate) { ?>

				<div class="panel right" >
					<div class="link-button" id="btn-prev">Prev</div>
					<div class="link-button" id="btn-next">Next</div>
				</div>

				<?php } ?>

			</div>

			<?php if ($flag_navigate) { ?>

			<div class="progress">
				<div class="RL"></div>
			</div>
		
			<?php } ?>

		</div>

		<div style="visibility: hidden; display: none;">
			<input type="hidden" value="<?php echo JAWS_PATH_WEB; ?>" id="txt-jaws-url" style="visibility: hidden; display: none;" />
		</div>

		<!-- TRACKING SCRIPTS ADDED -->
		   <?php if ($flag_paid) {
						if (($instl_count == 1) && (isset($_SESSION["temp"]["pay.success.creator"])) && (strcmp($_SESSION["temp"]["pay.success.creator"], "system") == 0)) { ?>
								<!--SteelHouse Conversion Pixel-
							 Install ONLY on conversion page/event
					<script type="text/javascript">
				(function(){var x=null,p,q,m,
				o="13185",
				l='<?php //echo $_SESSION["temp"]["pay.success.id"]; ?>',
				i='<?php //echo $_SESSION["temp"]["pay.success.total"]; ?>',
				c="",
				k='<?php //echo $_SESSION["temp"]["pay.success.scstr"]; ?>',
				g="1",
				j='<?php //echo $_SESSION["temp"]["pay.success.pricestr"]; ?>',
				u="",
				shadditional="";
				try{p=top.document.referer!==""?encodeURIComponent(top.document.referrer.substring(0,512)):""}catch(n){p=document.referrer!==null?document.referrer.toString().substring(0,512):""}try{q=window&&window.top&&document.location&&window.top.location===document.location?document.location:window&&window.top&&window.top.location&&""!==window.top.location?window.top.location:document.location}catch(b){q=document.location}try{m=parent.location.href!==""?encodeURIComponent(parent.location.href.toString().substring(0,512)):""}catch(z){try{m=q!==null?encodeURIComponent(q.toString().substring(0,512)):""}catch(h){m=""}}var A,y=document.createElement("script"),w=null,v=document.getElementsByTagName("script"),t=Number(v.length)-1,r=document.getElementsByTagName("script")[t];if(typeof A==="undefined"){A=Math.floor(Math.random()*100000000000000000)}w="dx.steelhousemedia.com/spx?conv=1&shaid="+o+"&tdr="+p+"&plh="+m+"&cb="+A+"&shoid="+l+"&shoamt="+i+"&shocur="+c+"&shopid="+k+"&shoq="+g+"&shoup="+j+"&shpil="+u+shadditional;y.type="text/javascript";y.src=("https:"===document.location.protocol?"https://":"http://")+w;r.parentNode.insertBefore(y,r)}());
				</script> -->

										<!-- Google analytics code -->
				<script type="text/javascript">
					(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
					(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
					m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
					})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
					ga('create', 'UA-31889158-1', 'auto');
					ga('send', 'pageview');
				</script>
				<!-- Google analytics code -->

			<?php }} ?>

		<?php 

	// Done 
	exit();

?>