<?php
function pagination( $total = 0,$page = 1,$limit = 20, $params = array() ) {
	
	$num_links = 8;
	
	$get = '';
	$url = '';
	$text_first = '|&lt;';
	$text_last = '&gt;|';
	$text_next = '&gt;';
	$text_prev = '&lt;';
	
	if ($page < 1) {
		$page = 1;
	} else {
		$page = $page;
	}
	if (!(int)$limit) {
		$limit = 10;
	} else {
		$limit = $limit;
	}
	
	if(!empty($params)){
		foreach( $params as $param => $value ){
			$get[] = $param . "=" . $value;
		}
		$url = "&" . implode("&",$get);
	}
	
	$num_pages = ceil($total / $limit);
	
	$output = '<ul class="pagination">';
	
	if ($page > 1) {
		
		$output .= '<li><a href="' . '?page=' . 1 . $url . '">' . $text_first . '</a></li>';
		
		$output .= '<li><a href="' . '?page=' . ( $page - 1 ) . $url . '">' . $text_prev . '</a></li>';		
	}
	if ($num_pages > 1) {
		
		if ($num_pages <= $num_links) {
			$start = 1;
			$end = $num_pages;
		} else {
			$start = $page - floor($num_links / 2);
			$end = $page + floor($num_links / 2);
			if ($start < 1) {
				$end += abs($start) + 1;
				$start = 1;
			}
			if ($end > $num_pages) {
				$start -= ($end - $num_pages);
				$end = $num_pages;
			}
		}
		for ($i = $start; $i <= $end; $i++) {
			if ($page == $i) {
				$output .= '<li><a class="active" href="javascript:;">' . $i . '</a></li>';
			} else {
				if ($i === 1) {
					$output .= '<li><a href="' . '?page=' . $i . $url . '">' . $i . '</a></li>';
				} else {
					$output .= '<li><a href="' . '?page=' . $i . $url . '">' . $i . '</a></li>';
				}
			}
		}
		
	}
	
	if ($page < $num_pages) {
		$output .= '<li><a href="' . '?page=' . ( $page + 1 ) . $url . '">' . $text_next . '</a></li>';
		$output .= '<li><a href="' . '?page=' . $num_pages . $url . '">' . $text_last . '</a></li>';
	}
	
	$output .= '</ul>';
	
	if ($num_pages > 1) {
		return $output;
	} else {
		return '';
	}
}
?>