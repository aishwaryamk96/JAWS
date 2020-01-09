<?php

	load_module("ui");

	// Init Session
	auth_session_init();

	// Prep
	$return_url = JAWS_PATH_WEB."/call.me";

	// Login Check
	if (!auth_session_is_logged()) {

		ui_render_login_front(array(
				"mode" => "login",
				"return_url" => $return_url,
				"text" => "Please login to access this page."
			));
		exit();

	}

	$rows = db_query("SELECT s.* FROM ws_form_submissions AS s INNER JOIN ws_forms AS f ON f.id = s.ws_form_id WHERE f.slug = 'give_me_call' ORDER BY id DESC;");

?>
<!DOCTYPE html>
<html>
<head>
	<title>Give Me a Call List</title>
	<link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
	<style>
		* {
			font-family: 'Roboto', sans-serif;
		}
		.d-flex {
			display: flex;
		}
		.flex-column {
			flex-direction: column;
		}
		.table {
			width: 100%;
			border-spacing: 0px;
		}
		.table th, td {
			padding: 5px 10px;
			text-align: center;
		}
		.table thead tr {
			background-color: rgba(225,225,225,.7);
			color: rgba(120,120,120,1);
			text-transform: uppercase;
		}
		.table-striped tbody tr:nth-child(even) {
			background-color: rgba(200,200,200,.7);
		}
	</style>
</head>
<body>
	<div>
		<table class="table table-striped">
			<thead>
				<tr>
					<th>#</th>
					<th>ISD</th>
					<th>Phone</th>
					<th>User</th>
					<th>On</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($rows as $i => $row) { ?>
					<tr>
						<th><?= $i + 1 ?></th>
						<td><?= $row["country_code"] ?></td>
						<td><?= $row["country_code"]."-".$row["phone"] ?></td>
						<td>
							<div class="d-flex flex-column">
								<label><?= $row["name"] ?></label>
								<label><?= $row["email"] ?></label>
							</div>
						</td>
						<td><?= $row["submitted_at"] ?></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
</body>
</html>