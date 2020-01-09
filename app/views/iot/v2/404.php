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

    JIGSAW ACADEMY WORKFLOW SYSTEM v2
    ---------------------------------
*/

  	// Prevent exclusive access
	if (!defined("JAWS")) {
		header('Location: https://www.jigsawacademy.com');
		die();
	}

	// Load stuff
	load_module("iot");

	// Init View
	iot_view_init();

	$index = 5;

?>

<!-- HTML HEAD -->
<?php
	$GLOBALS["content"]["title"] = "Page not Found!";
	load_template("iot", "v2/head");
?>
<!-- HTML HEAD ENDS -->

<!-- HEADER MENU -->
<?php load_template("iot", "v2/header"); ?>
<!-- HEADER MENU ENDS -->

<div id="content" class="site-content" style="text-align: center;">
	<div class="bg-white">
		<div class="row">
			<main id="main" class="site-main" role="main">
				<section class="error-404 not-found">
					<div class="page-header">
						<h1 class="page-title">Oops! That page can’t be found.</h1>
					</div><!-- .page-header -->
					<div class="page-content">
						<p>It looks like nothing was found at this location. Maybe try one of the links below or a search?</p>
					</div><!-- .page-content -->
				</section><!-- .error-404 -->
			</main><!-- #main -->
		</div><!-- #primary -->
	</div>
</div>
<!-- FOOTER -->
  <?php load_template("iot", "v2/footer"); ?>
  <!-- FOOTER ENDS -->
<!-- FOOTER -->
	<?php load_template("iot", "v2/foot"); ?>