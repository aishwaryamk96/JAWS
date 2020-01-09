<div id="page" class="container-fluid extend">

	<!-- PROGRESS SECTION -------------------------------------------------------------------------->
	<!---------------------------------------------------------------------------------------------->

	<div id="progress-container" class="row mar-none pad-none text-read">
		<div class="col-sm-16 container-fluid hidden-xs bdr-bottom bdr-top bdr-secondary">
			<div class="row pad-top-15">
				<div class="col-sm-16 text-center text-secondary fs-12 text-capitalize">Course Progress</div>
			</div>
			<div class="row v-align">
				<div class="col-sm-7 container-fluid pad-none mar-none rel">
					<div class="row pad-right-30 pad-left-0 mar-none">
						<div class="col-sm-4 col-md-5 col-lg-8 pad-none mar-none"></div>
						<div class="container-fluid col-sm-12 col-md-11 col-lg-8 mar-none pad-none text-right">
							<div class="row fs-15">
								<div class="i-progress-playlist-item col-sm-4" style="opacity: 0.075;"><i class="ion-play"></i></div>
								<div class="i-progress-playlist-item col-sm-4" style="opacity: 0.25;"><i class="ion-play"></i></div>
								<div class="i-progress-playlist-item col-sm-4" style="opacity: 0.4;"><i class="ion-android-list"></i></div>
								<div class="i-progress-playlist-item col-sm-4" style="opacity: 0.65;"><i class="ion-play"></i></div>
							</div>
							<!--<span class="font-alt"><span>8th Rank</span>&nbsp;<span class="text-ternary">(Out of 27)</span></span>-->
						</div>
					</div>
					<div class="row pad-right-15 pad-left-0 mar-row-15 mar-col-0">
						<div class="col-sm-4 col-md-5 col-lg-8 pad-none"></div>
						<div class="col-sm-12 col-md-11 col-lg-8 mar-none pad-none progress-line left"><div></div></div>
					</div>
					<div class="row pad-right-25">
						<div class="col-sm-16 font-alt text-primary text-capitalize text-right">
							<span>23% Completed</span>&nbsp;
							<span class="text-ternary">(8th Rank)</span>
						</div>
					</div>
				</div>

				<div class="i-resume col-sm-2 text-center pad-none">
					<i class="ion-play"></i>
				</div>

				<div class="col-sm-7 container-fluid pad-none mar-none">
					<div class="row pad-left-30 pad-right-0 mar-none">
						<div class="container-fluid col-sm-12 col-md-11 col-lg-8 mar-none pad-none">
							<div class="row fs-15">
								<div class="i-progress-playlist-item col-sm-4" style="opacity: 0.65;"><i class="ion-lightbulb"></i></div>
								<div class="i-progress-playlist-item col-sm-4" style="opacity: 0.4;"><i class="ion-ios-compose"></i></div>
								<div class="i-progress-playlist-item col-sm-4" style="opacity: 0.25;"><i class="ion-play"></i></div>
								<div class="i-progress-playlist-item col-sm-4" style="opacity: 0.075;"><i class="ion-play"></i></div>
							</div>
						</div>
						<div class="col-sm-4 col-md-5 col-lg-8 pad-none mar-none"></div>
					</div>
					<div class="row pad-left-15 pad-right-0 mar-row-15 mar-col-0">
						<div class="col-sm-12 col-md-11 col-lg-8 mar-none pad-none progress-line right"><div></div></div>
						<div class="col-sm-4 col-md-5 col-lg-8 pad-none mar-none"></div>
					</div>
					<div class="row pad-left-25">
						<div class="col-sm-16 font-alt text-primary">5 Items due by Friday</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-16 text-center font-alt fs-16 text-bold text-capitalize">Resume</div>
			</div>
			<div class="row pad-bottom-15 pad-top-5">
				<div class="col-sm-16 text-center text-ternary text-capitalize">Introduction to predictive models (4/9)</div>
			</div>
		</div>

		<div class="progress-min col-xs-16 pad-left-20 pad-right-0 bg-secondary">
			<i class="i-resume ion-play fs-26"></i>
			<span class="mar-left-20 text-uppercase fs-16 font-alt">Resume</span>
			<span class="mar-left-20 text-capitalize fs-12">Introduction to predictive models (Module 2)</span>
		</div>
	</div>

	<?php for($i = 0; $i < 5; $i++) {?>
	<div class="row module-container mar-none pad-none bdr-secondary bdr-bottom" onclick="$(this).toggleClass('active');">

		<!-- MODULE HEADER ------------------------------------------------------------------------->
		<!------------------------------------------------------------------------------------------>

		<div class="row module-header v-align mar-top-15 mar-col-0">
			<div class="col-xs-1 pad-left-20 pad-right-0 visible-xs" style="min-width: 42px;">
				<i class="ion-chevron-down i-anim-alt fs-22 text-secondary"></i>
				<i class="ion-chevron-up i-anim-alt fs-22 text-secondary"></i>
			</div>

			<div class="col-xs-10 col-sm-7 pad-left-15 pad-right-0 text-capitalize text-right-alt">
				<div class="fs-17 mar-bottom-5 text-read">1. an overview of analytics &amp; data science</div>
				<div class="fs-14 text-secondary text-read">4 topics, 9 videos</div>
			</div>

			<div class="col-sm-1 pad-none bdr-secondary bdr-right hidden-xs" style="height: 40px;"></div>
			<div class="col-sm-1 pad-none hidden-xs"></div>

			<div class="col-xs-4 col-sm-7 pad-left-0 pad-right-10 text-left-alt">
				<div class="fs-14 mar-bottom-7 text-secondary text-capitalize text-read">Estimated Time: 1 Week</div>
				<div class="fs-14 text-read">Due by Friday</div>
			</div>

			<div class="col-xs-1 pad-left-0 pad-right-15 text-right text-secondary visible-xs" style="min-width: 30px;">
				<i class="ion-android-radio-button-off fs-26 text-secondary" style="opacity: 0.25;"></i>
			</div>
		</div>

		<!-- MODULE CONTENT ------------------------------------------------------------------------>
		<!------------------------------------------------------------------------------------------>

		<div class="row module-content mar-row-15 mar-col-0">
			<div class="row module-topic v-align mar-col-0 text-capitalize">
				<div class="col-xs-1 pad-none visible-xs" style="min-width: 42px;"></div>
				<div class="col-xs-10 col-sm-7 pad-row-8 pad-left-15 pad-right-0 text-right-alt text-secondary text-read">An overview of analytics</div>
				<div class="col-sm-1 pad-right-0 pad-row-8 bdr-secondary bdr-right hidden-xs text-read">&nbsp;</div>
				<div class="col-sm-1 pad-none hidden-xs"></div>
				<div class="col-xs-4 col-sm-7 pad-left-0 pad-right-10 text-left-alt text-secondary text-read">completed</div>
				<div class="col-xs-1 pad-none visible-xs" style="min-width: 30px;"></div>
			</div>

			<div class="row module-topic v-align mar-col-0 text-capitalize active">
				<div class="col-xs-1 pad-none visible-xs" style="min-width: 42px;"></div>
				<div class="col-xs-10 col-sm-7 pad-row-8 pad-left-15 pad-right-0 text-right-alt text-read">why is analytics becoming so popular</div>
				<div class="col-sm-1 pad-right-0 pad-row-8 bdr-info bdr-right hidden-xs text-read">&nbsp;</div>
				<div class="col-sm-1 pad-none hidden-xs"></div>
				<div class="col-xs-4 col-sm-7 pad-left-0 pad-right-10 text-left-alt text-read">4 Videos</div>
				<div class="col-xs-1 pad-none visible-xs" style="min-width: 30px;"></div>
			</div>

			<div class="row module-topic v-align mar-col-0 text-capitalize">
				<div class="col-xs-1 pad-none visible-xs" style="min-width: 42px;"></div>
				<div class="col-xs-10 col-sm-7 pad-row-8 pad-left-15 pad-right-0 text-right-alt text-read">application of analytics</div>
				<div class="col-sm-1 pad-right-0 pad-row-8 bdr-ternary bdr-right hidden-xs text-read">&nbsp;</div>
				<div class="col-sm-1 pad-none hidden-xs"></div>
				<div class="col-xs-4 col-sm-7 pad-left-0 pad-right-10 text-left-alt text-read">6 Videos, 2 Assignments</div>
				<div class="col-xs-1 pad-none visible-xs" style="min-width: 30px;"></div>
			</div>

			<div class="row module-topic v-align mar-col-0 text-capitalize">
				<div class="col-xs-1 pad-none visible-xs" style="min-width: 42px;"></div>
				<div class="col-xs-10 col-sm-7 pad-row-8 pad-left-15 pad-right-0 text-right-alt text-read">analytics technology &amp; resources</div>
				<div class="col-sm-1 pad-right-0 pad-row-8 bdr-ternary bdr-right hidden-xs text-read">&nbsp;</div>
				<div class="col-sm-1 pad-none hidden-xs"></div>
				<div class="col-xs-4 col-sm-7 pad-left-0 pad-right-10 text-left-alt text-read">4 Videos, 1 Quiz</div>
				<div class="col-xs-1 pad-none visible-xs" style="min-width: 30px;"></div>
			</div>
		</div>

		<!-- MODULE EXPAND ------------------------------------------------------------------------->
		<!------------------------------------------------------------------------------------------>

		<div class="row mar-bottom-5 hidden-xs text-center" style="margin-top: -10px;">
			<div class="col-sm-7"></div>
			<div class="col-sm-2 pad-none">
				<i class="ion-chevron-down i-anim-alt fs-24 text-secondary"></i>
				<i class="ion-chevron-up i-anim-alt fs-24 text-secondary"></i>
			</div>
		</div>
	</div>
	<?php } ?>
</div>
