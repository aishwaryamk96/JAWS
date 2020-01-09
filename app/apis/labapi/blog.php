<div class="blog-content">
	<?php

		/*ini_set('display_errors',1);
		ini_set('display_startup_errors',1);
		error_reporting(-1);*/

		global $text, $maxchar, $end;

		function GetFeed($url) {

			$feed_array = array();

			$feed = simplexml_load_file($url);
			foreach ($feed->channel->item as $story) {

				$namespaces = $story->getNameSpaces(true);
				$dc = $story->children($namespaces['dc']);
				$contentns = $story->children($namespaces['content']);

				$story_array = [
					'title' => $story->title,
					'desc' => $story->description,
					'link' => $story->link,
					'date' => $story->pubDate,
					'creator' => $dc->creator,
					'content' => $contentns->encoded,
					'category' => $story->category
				];

				array_push($feed_array, $story_array);

			}

			return $feed_array;

		}

		$feed = GetFeed('http://analyticstraining.com/feed');

		$limit = 8;
		for ($x = 0; $x < $limit; $x++) {

			$title = str_replace(' & ', ' &amp; ', $feed[$x]['title']);
			$link = $feed[$x]['link'];
			$description = $feed[$x]['desc'];
			$date = date('F d, Y', strtotime($feed[$x]['date']));
			$category = $feed[$x]['category'];
			$author = $feed[$x]['creator'];
			$content = $feed[$x]['content'];

	?>
			<div class="blog-article">
				<div class="article-title">
					<span>
						<?php echo $title; ?>
					</span>
				</div>
				<div class="article-seperator"><!--DO NOT DELETE--></div>
				<div class="article-link">
					<a href="<?php echo $link; ?>" target="_blank">
						<i class="fa fa-fw fa-lg fa-2x fa-external-link-square"></i>
					</a>
				</div>
				<div class="article-close">
					<i class="fa fa-fw fa-lg fa-2x fa-times"></i>
				</div>
				<div class="article-category">
					<?php echo $category; ?>
				</div>
				<div class="article-date">
					<i class="fa fa-fw fa-lg fa-2x fa-calendar-o"></i>
					<?php echo $date; ?>
				</div>
				<div class="article-author">
					<?php echo $author; ?>
				</div>
				<div class="article-desc">
					<?php echo $description; ?>
				</div>
				<div class="article-content">
					<?php echo $content; ?>
				</div>
			</div>
		<?php }
	?>
</div>
<?php exit(); ?>