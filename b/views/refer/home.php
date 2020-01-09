<!doctype html>
<html lang="en">
<head>
	<title>Jigsaw Referral Program</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
	<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
	<style>
		body {
			font-family: 'Montserrat', sans-serif;
		}
		.form-control {
			width: 90%;
		}
		.btn-raised {
			background: #007bff;
			box-shadow: 0 4px 5px 0 rgba(0,0,0,.14), 0 1px 10px 0 rgba(0,0,0,.12), 0 2px 4px -1px rgba(0,0,0,.2);
			outline: 0;
			transition: all .3s ease-out;
			border: none;
			color: white;
			padding: 5px 20px;
			letter-spacing: 1.2px;
			font-size: 15px;
		}
		.btn-raised:active {
			box-shadow: none;
		}
		.form-row {
			display: flex;
			justify-content: space-between;
		}
		.form-row .form-group {
			width: 25%;
		}
		@media (min-width: 768.02px) {
			.faq-list {
				font-size: 14px;
			}
		}
		@media (max-width: 768px) {
			.overflow-y-auto {
				overflow-y: auto;
			}
		}
		@media (max-width: 575.98px) {
			.subheading-2 {
				margin-top: 5px;
			}
			.form-row {
				flex-direction: column;
			}
			.form-row .form-group {
				width: 100%;
			}
			.form-control {
				width: 100%;
			}
			.overflow-y-auto {
				overflow-y: auto;
			}
		}
	</style>
</head>
<body>
	<div class="container-fluid pt-3">
		<div class="d-flex px-3 justify-content-center">
			<div class="text-primary text-uppercase text-center" style="font-size:6vh;letter-spacing: 2.5px">
				an <span class="text-warning">amazon</span> treat awaits you
			</div>
		</div>
		<div class="d-flex px-3 justify-content-center">
			<h6 class="text-secondary font-weight-normal mb-0">Introduce friends to Jigsaw Academy. Get them enrolled. Get rewarded.</h6>
		</div>
		<div class="d-flex px-3 justify-content-center mb-3 subheading-2">
			<h6 class="text-muted font-weight-normal mb-0">Get an Amazon* voucher of  1000 for every friend that enrolls.</h6>
		</div>
		<div class="d-flex px-3 justify-content-center mb-5 overflow-y-auto">
			<img src="https://www.jigsawacademy.com/jaws/media/jaws/frontend/images/referral-process-top-image.png">
		</div>
		<div class="d-flex px-3 justify-content-center mb-5">
			<form name="referral" class="d-flex flex-column w-100">
				<div class="form-row px-3">
					<div class="form-group">
						<input type="text" class="form-control" name="name" placeholder="Friend's name">
					</div>
					<div class="form-group">
						<input type="email" class="form-control" name="email" placeholder="Friend's email">
					</div>
					<div class="form-group">
						<input type="number" class="form-control" name="phone" placeholder="Friend's phone">
					</div>
					<div class="form-group">
						<input type="number" class="form-control" name="phone" placeholder="Friend's phone">
					</div>
				</div>
				<div class="d-flex px-3">
					<button type="button" class="btn-raised text-uppercase">preview invite</button>
				</div>
			</form>
		</div>
		<div class="d-flex px-3">
			<div id="accordion">
				<div class="card border-0 mt-3">
					<div class="card-header border-0 p-0" id="headingOne">
						<h5 class="mb-0">
							<button class="btn btn-link w-100 text-left" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
								Collapsible Group Item #1
							</button>
						</h5>
					</div>
					<div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
						<div class="card-body faq-list">
							Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
						</div>
					</div>
				</div>
				<div class="card border-0 mt-3">
					<div class="card-header border-0 p-0" id="headingTwo">
						<h5 class="mb-0">
							<button class="btn btn-link collapsed w-100 text-left" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
								Collapsible Group Item #2
							</button>
						</h5>
					</div>
					<div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion">
						<div class="card-body faq-list">
							Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
						</div>
					</div>
				</div>
				<div class="card border-0 mt-3">
					<div class="card-header border-0 p-0" id="headingThree">
						<h5 class="mb-0">
							<button class="btn btn-link collapsed w-100 text-left" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
								Collapsible Group Item #3
							</button>
						</h5>
					</div>
					<div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordion">
						<div class="card-body faq-list">
							Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>