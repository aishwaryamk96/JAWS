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
		header('Location: https://www.jigsawacademy.com/');
		die();
	}

    // Start Output Buffering - Optional
    ob_start();

?>
<!DOCTYPE html>

    <html>
    <head>
        <title>JAWS - Test Page</title>
        <style>
            body {
                display:block;

                background-color: black;
                color: white;
                font-family: "consolas", "sans-serif";
                font-size: 90%;

                margin: 0;
                padding: 20px;

                overflow-x: hidden;
                overflow-y: scroll;
            }
        </style>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    </head>
    <body>

<?php

    error_reporting(E_ALL);

    echo "JAWS v".JAWS_VERSION, "<br/>Jigsaw Academy Workflow System", "<br/>Running PHP v".phpversion()." on ".$_SERVER['SERVER_SOFTWARE'], "<br/>&copy; Jigsaw Academy Education Pvt. Ltd.<br/>";
    echo "<br/>";

    echo "JAWS Test Routine - Testing Auto Loaders";
    echo " <br />";

    echo "Loading auto loaders..";
    echo " &#10004;<br /><br />";

    echo "JAWS Test Routine - Testing Manual Loaders";
    echo " <br />";

    echo "Loading ui..";
    load_module("ui");
    echo " &#10004;<br />";

    echo "Loading user..";
    load_module("user");
    echo " &#10004;<br />";

    echo "Loading course..";
    load_module("course");
    echo " &#10004;<br />";

    echo "Loading subs..";
    load_module("subs");
    echo " &#10004;<br />";

    echo "Loading leads..";
    load_module("leads");
    echo " &#10004;<br />";

    echo "Loading payment lib..";
    load_library("payment");
    echo " &#10004;<br />";

    echo "Loading email lib..";
    load_library("email");
    echo " &#10004;<br />";

    echo "Loading misc lib..";
    load_library("misc");
    echo " &#10004;<br />";

    echo "<br />";
    echo "Test Routine Successfull - Testing Custom Code..";
    echo "<br /><br />";

    // ----- CUSTOM CODE ---------------------------------------------------------
    // --------------------------------------------------------------------------------

	$GLOBALS['jaws_exec_live'] = false;

 ?>
 <div id="top-ten">
 	<h2>Page load top ten records</h2>

 </div>
<div id="live-track">
	<h2>Live follow update</h2>
</div>

<script type='text/javascript'>
    $(document).ready(function() {var count = 0;

	function serialize(obj, prefix) {
		var str = [], p;
		for(p in obj) {
			if (obj.hasOwnProperty(p)) {
				var k = prefix ? prefix + "[" + p + "]" : p, v = obj[p];
				str.push((v !== null && typeof v === "object") ?
				serialize(v, k) :
				encodeURIComponent(k) + "=" + encodeURIComponent(v));
			}
		}
		return str.join("&");
	};

    if(typeof(EventSource) !== "undefined") // check if browser supports SSE( Server Sent events)
     {
            var source = new EventSource("<?php echo JAWS_PATH_WEB ?>/webapi/feed/feed?nocache=" + Date.now() + '&' + serialize({
				activity: {
					counter: -1,
					data: ['payments', 'it']
				}
			}, 'feeds'), { withCredentials: true }); // create object of EventSource
                    source.addEventListener("activity", function(e) {
                    document.getElementById("live-track").innerHTML += "<p>"+e.data + "</p><br>";
            }, false);
        } else {
                    console.log('else');
                    document.getElementById("live-track").innerHTML = "Sorry, your browser does not support server-sent events...";
        }

    // var data = {
    //             "t": "1298292839",
    //             "w" : "{0} has complted the payment of amount {1}",
    //             "d" : { "0" : "nikita soni", "1" : "24000" }
    //     };
    // data = Json.parse(data);
    // for(var i=0; i< data.length; i++)
    // {

    // }
});
</script>

<br /><br />
Custom code execution complete - Ciao!
</body>
