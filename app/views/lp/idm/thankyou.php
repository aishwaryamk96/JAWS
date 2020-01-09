<?php 

if( !empty($_POST["source"]) ){ $source = trim($_POST["source"]); } else { $source = ""; }
if( !empty($_POST["medium"]) ){ $medium = trim($_POST["medium"]); } else { $medium = ""; }
if( !empty($_POST["term"]) ){ $term = trim($_POST["term"]); } else { $term = ""; }
if( !empty($_POST["content"]) ){ $content = trim($_POST["content"]); } else { $content = ""; }
if( !empty($_POST["campaign"]) ){ $campaign = trim($_POST["campaign"]); } else { $campaign = ""; }
if( !empty($_POST["segment"]) ){ $segment = trim($_POST["segment"]); } else { $segment = ""; }
if( !empty($_POST["numVisits"]) ){ $numvisits = trim($_POST["numVisits"]); } else { $numvisits = ""; }
if( !empty($_POST["gclid"]) ){ $gclid = trim($_POST["gclid"]); } else { $gclid = ""; }
if( !empty($_POST["referer"]) ){ $referer = trim($_POST["referer"]); } else { $referer = ""; }
if( !empty($_POST["ip"]) ){ $ip = trim($_POST["ip"]); } else { $ip = ""; }
if( !empty($_POST["requrl"]) ){ $requrl = trim($_POST["requrl"]); } else { $requrl = ""; }
if( !empty($_POST["global_id"]) ){ $global_id = trim($_POST["global_id"]); } else { $global_id = ""; }
if( !empty($_POST["detect"]) ){ $detect = trim($_POST["detect"]); } else { $detect = ""; }
if( !empty($_POST["dnt"]) ){ $dnt = trim($_POST["dnt"]); } else { $dnt = ""; }
if( !empty($_POST["name"]) ){ $name = trim($_POST["name"]); }
if( !empty($_POST["email"]) ){ $email = trim($_POST["email"]); }
if( !empty($_POST["mobile"]) ){ $phone = trim($_POST["mobile"]); }

$meta = array();
if( !empty($_POST["name"]) && !empty($_POST["email"]) && !empty($_POST["mobile"]) ) {
	$meta = array(
		"detect" => $detect,
		"dnt"	=> $dnt
	);
	
// Insert Data
$sql = "INSERT INTO user_leads_basic ( 
	email, 
	name, 
	phone,  
	utm_campaign, 
	utm_medium, 
	utm_source, 
	utm_term, 
	utm_content, 
	utm_segment, 
	utm_numvisits, 
	global_id_perm, 
	gcl_id, 
	ip, 
	referer, 
	ad_lp, 
	ad_url, 
	create_date,
	capture_trigger,
	capture_type,
	meta	) VALUES (
	" . db_sanitize($email) . ",
	" . db_sanitize($name) . ",
	" . db_sanitize($phone) . ",
	" . db_sanitize($campaign) . ",
	" . db_sanitize($medium) . ",
	" . db_sanitize($source) . ",
	" . db_sanitize($term) . ",
	" . db_sanitize($content) . ",
	" . db_sanitize($segment) . ",
	" . db_sanitize($numvisits) . ",
	" . db_sanitize($global_id) . ",
	" . db_sanitize($gclid) . ",
	" . db_sanitize($ip) . ",
	" . db_sanitize($referer) . ",
	'PGPDM-LP',
	" . db_sanitize($requrl) . ",
	NOW(),
	'formsubmit',
	'url',
	" . db_sanitize(json_encode($meta)) . "	)";

	if(!db_exec($sql)){
		die("Some error occured! Please try again after some time.");
	}
}
// remove parameters from brochure parameter as social login js adds redirect and soc to it.
if(!empty($_GET["brochure"])){ $temp = explode("?",$_GET["brochure"]); $_GET["brochure"] = $temp[0]; }

// Init Session
auth_session_init();
if(auth_session_is_logged() && !empty($_GET["brochure"]) && $_GET["brochure"] == "true" ){ 
	$brochure =  true; //enable download
} else { $brochure = false; }

$site_url = "https://www.jigsawacademy.com";  $url = "https://www.jigsawacademy.com/wp-content/uploads/2017/03/UC_brochure.pdf";
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" xml:lang="en" lang="en-US" prefix="og: http://ogp.me/ns#">
<head>
	<!--[if IE]><meta http-equiv='X-UA-Compatible' content='IE=edge,chrome=1'/><![endif]-->
	<!--[if lt IE 9]>
	<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta name="format-detection" content="telephone=no">
	<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no" name="viewport">
	
	<title>Postgraduate Program in Data Science &amp; Machine Learning (PGPDM) - Jigsaw</title>
	<link rel='stylesheet' id='jigsaw-bootstrap-css' href='<?php echo $site_url; ?>/wp-content/themes/jigsaw/css/bootstrap.custom.css' type='text/css' media='all' />
	<link rel='stylesheet' id='jigsaw-style-css' href='<?php echo $site_url; ?>/wp-content/themes/jigsaw/style.css' type='text/css' media='all' />
	<link rel='stylesheet' id='jigsaw-media-css' href='<?php echo $site_url; ?>/wp-content/themes/jigsaw/css/media.css' type='text/css' media='all' />
	<link rel="icon" href="<?php echo $site_url; ?>/wp-content/uploads/2015/12/cropped-favicon-32x32.png" sizes="32x32" />
	<link rel="icon" href="<?php echo $site_url; ?>/wp-content/uploads/2015/12/cropped-favicon-192x192.png" sizes="192x192" />
	<link rel="apple-touch-icon-precomposed" href="<?php echo $site_url; ?>/wp-content/uploads/2015/12/cropped-favicon-180x180.png">
	<meta name="msapplication-TileImage" content="<?php echo $site_url; ?>/wp-content/uploads/2015/12/cropped-favicon-270x270.png">
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
	<script type="text/javascript" src="/jaws/app/templates/iot/v2/js/login.js"></script>
	<!-- Facebook Pixel Code --> 
	<script>
	!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
	n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
	n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
	t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
	document,'script','https://connect.facebook.net/en_US/fbevents.js');
	fbq('init', '1528277680816161'); // Insert your pixel ID here.
	fbq('track', 'PageView');
	</script>
	<noscript><img height="1" width="1" style="display:none"
	src="https://www.facebook.com/tr?id=1528277680816161&ev=PageView&noscript=1" 
	/></noscript>
	<!-- DO NOT MODIFY -->
	<!-- End Facebook Pixel Code -->

	<!-- Google analytics code -->
	<script type="text/javascript">
		(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
		
		ga('create', 'UA-31889158-1', 'jigsawacademy.com');
		ga('send', 'pageview');
		
	</script>
	<!-- /Google analytics code -->

</head>
<body class="single single-program postid-113 group-blog">
<noscript>
	<span style="text-align: center;background: black;color: white;padding: 5pt;position: fixed;z-index: 1111;width: 100%;opacity: 0.6;text-transform: uppercase;">Jigsaw Academy needs JavaScript enabled to work properly.</span>
</noscript>
<div id="page">
  <div id="content" class="site-content">
  	<div id="banner" class="executive-detail-banner">
	<style type="text/css">.banner-bg1{background: url('<?php echo $site_url; ?>/wp-content/themes/jigsaw/images/UC_LP_BG.png')!important; background-repeat:no-repeat !important; background-size: 100% 100% !important;  background-color: white;position: absolute;top: 0;bottom: 0;left: 0;right: 0; } .uc-banner h1{ line-height: 1em; padding-bottom: 15px; width: 95%; } .uc-banner .coursereviewtop, .uc-banner .spacer { display: none; } .banner-button{ line-height: 50px; } .executive-detail-banner .banner-bg{ filter: blur(1px); } .certificate-features{ border: none; padding-bottom: 0px; margin-bottom: 0px; } .partner-logo{ margin-top: 35px; } #banner{ padding: 4.5em 0px; }.banner-video{padding: 10em 4em;color: white;}
	</style>
	<div class="banner-bg1 banner-overlay1"></div>	
	<div class="row clearfix banner-common">
	<div class="banner-content banner-content-left uc-banner">
		<h1>FUTURE-PROOF YOUR CAREER.</h1>
		<p style="font-weight: bold;">Take the first step with the Postgraduate Program in Data Science and Machine Learning (PGPDM)</p>
		<div style="font-size: 16px;">Learn to process and visualize data through data analysis with a hybrid learning model of online and in-person classes in Bangalore with the international faculty and industry experts from the University of Chicago Graham School and Jigsaw Academy.</div>
		<span class="spacer"></span>
		<div class="button-group">
            <a href="<?php echo $site_url."/pgpdm#uc-apply-form"; ?>" target="_blank" class="skewed banner-button button-orange button-mid button-lesswidth popup-with-form"><span>Apply Now </span></a>
			<span class="txt-or-light">OR</span>
     		<a id="brochure-link" href="javascript:;" data-toggle="modal" data-target="#loginmodal" class="banner-link popup-with-form btn-download" download=""><img src="<?php echo $site_url; ?>/wp-content/themes/jigsaw/images/icon-brochure.png" alt="arrow" class="image-icon image-icon-left"> Download Brochure </a>
			<div class="partner-logo">
				<img src="<?php echo $site_url; ?>/wp-content/themes/jigsaw/images/Home_page_slider_UC_logo.png" alt="announcement">
			</div>
		</div><!-- button-group ends here-->
	</div><!-- banner-content ends here-->
	<!-- video starts here-->
	<div class="banner-video">
		Thank you! Our counsellers will get in touch with you shortly.
	</div><!-- video ends here-->
    </div><!-- row ends here-->
    </div><!-- banner ends here-->
	</div><!-- #content -->
	<footer style="background: inherit; padding: 10px;font-family: 'Lato', sans-serif;font-size:12px;position: fixed;bottom: 0;z-index: 20;width: 100%;">
 	<div class="row">
		<div class="col-md-6">
			<a href="<?php echo $site_url; ?>" target="_blank"><img src="<?php echo $site_url; ?>/wp-content/themes/jigsaw/images/footer-logo-lp.png" height="50" width="50" alt="Jigsaw" /></a>
			<a href="<?php echo $site_url; ?>" target="_blank">&copy; Jigsaw Academy Education Pvt Ltd</a>
		</div>
		<div class="col-md-6" style="padding-top: 16px;text-align:right;">
			<a href="<?php echo $site_url; ?>/privacy-policy/" target="_blank" style="padding-right: 20px;"><span style="color: #8b8b8b;">Privacy Policy</span></a>
			<a href="<?php echo $site_url; ?>/terms-conditions/" target="_blank"><span style="color: #8b8b8b;">Terms and Conditions</span></a>
		</div>
 	</div><!-- row ends here-->    
  </footer><!-- footer ends here-->
  <!-- Login modal start-->
	<div class="modal fade loginmodal text-center" id="loginmodal" role="dialog">
	  <div class="modal-dialog"> 
	    <!-- Modal content-->
	    <div class="modal-content model-margin">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	      </div>
	      <div class="modal-body">
	        <div class="sign-in-sec-in"> <span class="sign-in-sec-head"> Sign up using any of the below options. </span>
	          <p style="font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 18px;">We will never spam you. It's a promise!</p>
	          <div class="sign-in-sec-button"> 
	          <a class="social-radio" id="soc-fb">
	            <div class="fb-button">
	              <button class="fb-button-in"> <img src="/jaws/common/iot/images/fb-img.png" alt="f"> Sign Up with Facebook </button>
	            </div>
	            </a> 
	            <!-- fb-button ends here--> 
	            <a class="social-radio" id="soc-gp">
	            <div class="google-button">
	              <button class="google-button-in"> <img src="/jaws/common/iot/images/google-img.png" alt="G"> Sign Up with Google </button>
	            </div>
	            </a> 
	            <!-- google-button ends here--> 
	            <a class="social-radio" id="soc-li">
	            <div class="in-button">
	              <button class="in-button-in"> <img src="/jaws/common/iot/images/link-in-img.png" alt="in"> Sign Up with LinkedIn </button>
	            </div>
	            </a> 
	            <!-- in-button ends here--> 
	          </div>
	          <!-- sign-in-sec-button ends here--> 
	        </div>
	      </div>
	    </div>
	  </div>
	</div>
	<!-- Login modal end--> 
</div><!-- #page -->
<input type="hidden" id="login_url" value="https://www.jigsawacademy.com/jaws/docreate" />
<input type="hidden" id="return_url" value="https%3A%2F%2Fwww.jigsawacademy.com%2Fjaws%2Fidm-thankyou%3Fbrochure%3Dtrue" />
<input type="hidden" id="reauth" value="false" />
<input type="hidden" id="wplogin_url" value="https%3A%2F%2Fwww.jigsawacademy.com%2Fjaws%2Fview%2Ffrontend%2Fredir%2Fwp.login" />
<script type='text/javascript' src='<?php echo $site_url; ?>/wp-content/themes/jigsaw/js/bootstrap.custom.min.js'></script>
<style>.modal-backdrop.in{ opacity: 0 !important; position: unset !important; }.sign-in-sec-in, .modal-header{ border: none;}</style>
<?php if($brochure){ ?>
<script>document.addEventListener("DOMContentLoaded", function(event) { 
setTimeout(function() { function saveFile(url) { /* Get file name from url. */ var filename = url.substring(url.lastIndexOf("/") + 1).split("?")[0]; var xhr = new XMLHttpRequest(); xhr.responseType = 'blob'; xhr.onload = function() { var a = document.createElement('a'); a.href = window.URL.createObjectURL(xhr.response); /* xhr.response is a blob */ a.download = filename; /* Set the file name. */ a.style.display = 'none'; document.body.appendChild(a); a.click(); delete a; }; xhr.open('GET', url); xhr.send(); } saveFile("<?php echo $url; ?>"); }, 1000); }); 
</script>
<?php } ?>
<!-- Google Code for Conversion Page -->
	<script type="text/javascript">
		/* <![CDATA[ */
		var google_conversion_id = 987804683;
		var google_conversion_language = "en";
		var google_conversion_format = "3";
		var google_conversion_color = "ffffff";
		var google_conversion_label = "oV-KCLmRklgQi-iC1wM";
		var google_remarketing_only = false;
		/* ]]> */
	</script>
	<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
	</script>
	<noscript>
		<div style="display:inline;">
			<img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/987804683/?label=oV-KCLmRklgQi-iC1wM&amp;guid=ON&amp;script=0"/>
		</div>
	</noscript>
	<!-- Google Code for Conversion Page ends here -->



Google Adwords Remarketing Codes

<!-- Google Remaketing code start -->
	<script type="text/javascript"> 
		/* <![CDATA[ */ 
		var google_conversion_id = 987804683; 
		var google_custom_params = window.google_tag_params; 
		var google_remarketing_only = true; 
		/* ]]> */ 
	</script> 
	<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js"> 
	</script> 
	<noscript> 
		<div style="display:inline;"> <img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/987804683/?value=0&guid=ON&script=0"/> </div> 
	</noscript>
	<!-- Google Remaketing code ends -->
</body>
</html>