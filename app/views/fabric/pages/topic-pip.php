<!-- TOPIC CONTAINER -------------------------------------------------------------------------------->
<!--------------------------------------------------------------------------------------------------->

<div id="topic-container" class="container-fluid pip">
	<div class="row topic-menu-wide topic-menu">

		<div class="col-xs-7 pad-none">
			<span class="topic-menu-lnk">Topic Resources</span>
			<span class="topic-menu-lnk active">Now Playing</span>
		</div>

		<div class="col-xs-9 pad-none text-right">
			<span class="topic-menu-lnk">Notes</span>
			<span class="topic-menu-lnk">Codes</span>
			<span class="topic-menu-lnk">Transcript</span>
			<span class="topic-menu-lnk active">Playlist</span>
		</div>

	</div>

	<div class="row">

		<div class="pane-left container-fluid col-xs-11 col-sm-10 col-md-11 col-lg-12 pad-none">
			<div class="row topic-menu-thin topic-menu text-center">
				<span class="col-xs-8 topic-menu-lnk">Topic Resources</span>
				<span class="col-xs-8 topic-menu-lnk active">Now Playing</span>
			</div>
			<div class="row topic-content pad-none mar-none">
				<div id="topic-overview" class="col-xs-16 pad-none">

				</div>

				<div id="topic-playing" class="col-xs-16 pad-none hidden">

				</div>
			</div>
		</div>

		<div class="pane-right container-fluid col-xs-5 col-sm-6 col-md-5 col-lg-4 pad-none">
			<div class="row topic-menu-thin topic-menu text-center">
				<span class="col-xs-4 topic-menu-lnk">Notes</span>
				<span class="col-xs-4 topic-menu-lnk">Codes</span>
				<span class="col-xs-4 topic-menu-lnk">Transcript</span>
				<span class="col-xs-4 topic-menu-lnk active">Playlist</span>
			</div>
			<div class="row topic-content bdr-left bdr-secondary pad-none mar-none">
				<div id="topic-playlist" class="col-xs-16 pad-none">

					<?php for($itm = 0; $itm < 25; $itm++) { ?>
					<div class="row topic-playlist-item <?php if($itm == 1) echo 'active'; ?> v-align pad-none mar-none bdr-bottom bdr-secondary text-primary text-capitalize">
						<div class="col-xs-16 pad-left-25 pad-right-10">
							<div class="item-desc pull-right fs-12">10 mins</div>
							<div class="item-icon pull-left ion-play fs-18"></div>
							<div class="item-name pad-left-20 pad-right-10">Introduction to predictive models</div>
						</div>
					</div>
					<?php } ?>

				</div>

				<div id="topic-transcript" class="col-xs-16 pad-none hidden">

				</div>

				<div id="topic-codes" class="col-xs-16 pad-none hidden">

				</div>

				<div id="topic-notes" class="col-xs-16 pad-none hidden">

				</div>
			</div>
		</div>

	</div>
</div>
