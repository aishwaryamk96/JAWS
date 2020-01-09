<html>
<head>
	<title><?php echo $GLOBALS["title"] ?> Lab - Jigsaw Academy</title>
	<script src="https://use.fontawesome.com/fc49ce4973.js"></script>
	<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS["css"]."login.css" ?>">
</head>
<body>
	<div class="parent">
		<div class="modal">
			<div class="header">
				<span>Welcome to</span>
				<span>Jigsaw Lab</span>
				<span>
					<span>for</span>
					<span><?php echo $GLOBALS["title"] ?></span>
				</span>
			</div>
			<form method="post" class="login-form">
				<?php if (!empty($error_msg)) { ?>
					<div class="form-error">
						<label><?php echo $error_msg ?></label>
					</div>
				<?php } ?>
				<div class="form-elements">
					<i class="fa fa-user" aria-hidden="true"></i>
					<input type="text" name="username" placeholder="Username">
				</div>
				<div class="form-elements">
					<i class="fa fa-lock" aria-hidden="true"></i>
					<input type="password" name="password" placeholder="Password">
				</div>
				<div class="form-submit">
					<button type="submit">Login<i class="fa fa-chevron-right"></i></button>
				</div>
			</form>
		</div>
	</div>
</body>
</html>