<html>
<head>
	<title><?php echo $GLOBALS["title"] ?> Lab - Jigsaw Academy</title>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="https://use.fontawesome.com/fc49ce4973.js"></script>
	<script type="text/javascript">
		var jq = $.noConflict();
	</script>
	<script src="<?php echo $GLOBALS["js"]."lab.js" ?>"></script>
	<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS["css"]."lab.css" ?>">
</head>
<body>
	<div class="parent <?php echo $GLOBALS["launch"] ? "launch" : "" ?>">
		<div class="modal">
			<div class="header">
				<span>Welcome to</span>
				<span>Jigsaw Lab</span>
				<span>
					<span>for</span>
					<span><?php echo $_SESSION["lab"]["name"] ?></span>
				</span>
			</div>
			<div class="welcome">
				<div class="user">Welcome, <?php echo $_SESSION["user_name"] ?></div>
				<form class="form-logout" method="post">
					<button name="logout" value="1">Logout</button>
				</form>
			</div>
			<div class="start-form">
				<div class="form-submit">
					<button id="start">Start</button>
				</div>
				<div class="form-submit">
					<label></label>
				</div>
			</div>
			<form method="post" class="launch-form">
				<div class="form-submit">
					<button id="launch" type="submit" name="launch" value="1">Launch lab</button>
					<button id="close">Close lab</button>
				</div>
			</div>
		</div>
	</div>
	<div id="blog">
		<div id="blog-img">
			&nbsp;
		</div>
		<div id="blog-wrapper">
		</div>
	</div>
	<div id="footer">
		<div class="waiting">
			<label id="timer"></label>
			<i class="fa fa-stopwatch"></i>
		</div>
	</div>
</body>
</html>