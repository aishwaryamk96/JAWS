<?php error_reporting(E_ALL);
require_once "http_build_url.php";

Class Tourian{

	public function __construct(){

	$this->values = array();

	/** $servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "jaws"; **/


		try {
			// $this->conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
			$this->conn =  $GLOBALS["jaws_db"]["db"];
			/* // set the PDO error mode to exception
			$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); */
		}
		catch(PDOException $e){
			echo "Error: " . $e->getMessage();
		}
	}

	public function __destruct() {
        // echo 'Destroying Connection ' . PHP_EOL;
		$this->conn = null;
    }

	protected function create($data){
		// prepare sql and bind parameters
		if( $data['update'] == true ){
			$stmt = $this->conn->prepare("UPDATE `system_content` SET `content` = :content  WHERE `content_id` = :content_id AND `context_type` = :context_type");
			$stmt->bindParam(':content_id', $data['content_id']);
		} else {
			$stmt = $this->conn->prepare("INSERT INTO system_content (context_type, content) VALUES (:context_type, :content)");
		}

		$stmt->bindParam(':context_type', $data['context_type']);
		$stmt->bindParam(':content', $data['content']);
		if($stmt->execute()){
			return true;
		}else{
			return false;
		}
	}

	protected function createAssociation($assoc,$data){

		// for create or update, remove previous associations and create new.
		$stmt = $this->conn->prepare("DELETE FROM `system_content` WHERE `context_type` = :context_type AND `content` = :content");
		$stmt->bindParam(':context_type', $assoc['context_type']);
		$stmt->bindParam(':content', $assoc['content']);
		$stmt->execute();

		// prepare sql and bind parameters for new entry.
		$stmt = $this->conn->prepare("INSERT INTO system_content (context_type, context_id, content) VALUES (:context_type, :context_id, :content)");

		foreach( $data as &$d ){
			$stmt->bindParam(':context_type', $d['context_type']);
			$stmt->bindParam(':context_id', $d['context_id']);
			$stmt->bindParam(':content', $d['content']);
			$stmt->execute();
		} unset($d);
		return true;
	}

	protected function getLastId()
	 {  return $this->conn->lastInsertId();
	  }

	protected function findContextID($context_type){
		// prepare sql and bind parameters
		$stmt = $this->conn->prepare("SELECT `content_id`,`context_id` FROM `system_content` WHERE `context_type` = :context_type ORDER BY `content_id` DESC");
		$stmt->bindParam(':context_type', $context_type);
		$stmt->execute();
		$return = $stmt->fetch();
		if(!empty($return))	return $return['context_id'];
		else return 0;
	}

	protected function find($context_type,$content_id){
		// prepare sql and bind parameters
		$stmt = $this->conn->prepare("SELECT * FROM `system_content` WHERE `context_type` = :context_type AND `content_id` = :content_id");
		$stmt->bindParam(':context_type', $context_type);
		$stmt->bindParam(':content_id', $content_id);
		$stmt->execute();
		$return = $stmt->fetch();
		if(!empty($return))	return $return;
		else return 0;
	}

	protected function findContent($context_type,$content){

		// prepare sql and bind parameters
		$stmt = $this->conn->prepare("SELECT * FROM `system_content` WHERE `context_type` = :context_type AND `content` = :content");
		$stmt->bindParam(':context_type', $context_type);
		$stmt->bindParam(':content', $content);
		$stmt->execute();
		$return = $stmt->fetchAll();

		if(!empty($return))	return $return;
		else return 0;
	}

	public function findTourList(){
		// prepare sql and bind parameters
		$stmt = $this->conn->prepare("SELECT * FROM `system_content` WHERE `context_type` = :context_type ORDER BY `content_id` DESC");
		$context_type = 'tourian.tours';
		$stmt->bindParam(':context_type', $context_type );
		$stmt->execute();
		$data = $stmt->fetchAll();

		$return = array();
		if(!empty($data)){
			foreach($data as &$d){
				$a = json_decode($d['content']);
				$return[] = array(
					'content_id' => $d['content_id'],
					'tour_name' => $a->tour_name
				);
			}unset($d);
		}

		return $return;
	}

	public function createTour($data){

		$context = 'tourian.tours';

		$save = array(
			'context_type' => $context,
			'content' => json_encode(array(
				'tour_name' => $data['tour_name'],
				'steps' => $data['tour'],
			))
		);

		if(!empty($data['content_id'])){
			// in case of update.
			$save['update'] = true;
			$save['content_id'] = $data['content_id'];
		}else{
			// in case of insert.
			$save['update'] = false;
		}

		if($this->create($save)){
			$message = 'Successfully updated.';
		} else {
			$message = 'Unable to process. Please try after some time.';
		}

		return $message;
	}

	public function createURL($data){

		$context = 'tourian.url';

		$save_url = array(
			'context_type' => $context,
			'content' => $data['page_url']
		);

		if(!empty($data['content_id'])){
			// in case of update content id is avaliable with post data.
			$save_url['update'] = true;
			$save_url['content_id'] = $data['content_id'];
		}else{
			// in case of insert, find the last conext id and add one to create new.
			$save_url['update'] = false;
		}

		if($this->create($save_url)){
			// create association for tours and url
			foreach($data['tours'] as &$tour){
				$save_assoc = array(
					'context_type' => 'tourian.assoc',
					'content' => (!empty($data['content_id']))? $data['content_id'] : $this->getLastId()
				);

				$save_assoc_data[] = array(
					'context_type' => 'tourian.assoc',
					'content' => (!empty($data['content_id']))? $data['content_id'] : $this->getLastId(),
					'context_id' => $tour,
				);
			} unset($tour);

			$this->createAssociation($save_assoc,$save_assoc_data);

			$message = 'Succussefully updated.';
		} else {
			$message = 'Unable to process. Please try after some time.';
		}

		return $message;
	}

	public function getDetails($context,$content_id){
		if($context === 'all'){
			$context_type = 'tourian.url';
			// array list of all urls with their associated tours.
			$stmt = $this->conn->prepare("SELECT * FROM `system_content` WHERE `context_type` = :context_type ORDER BY `content_id` DESC");
			$stmt->bindParam(':context_type', $context_type);
			$stmt->execute();
			$urls = $stmt->fetchAll();

			foreach( $urls as $key => &$url ) {
				$return[$key]['url'] = $url['content'];
				$return[$key]['content_id'] = $url['content_id'];
				$assoc_tours = $this->findContent('tourian.assoc',$url['content_id']);
				if(!empty($assoc_tours)){
					foreach( $assoc_tours as &$tour ){
						$tour_details = $this->getDetails('tourian.tours',$tour['context_id']);
						$return[$key]['tours'][] = array(
							'id' => $tour['context_id'],
							'name' => $tour_details['tour_name'],
						);
					} unset($tour);
				}
			} unset($url);

			return $return;
		} else {

			$data = $this->find($context,$content_id);

			if( $context === 'tourian.url' ){
				// for tourian.url find and add the association as well
				$tours = $this->findContent('tourian.assoc',$content_id);
				$t = array();
				if(!empty($tours)){
					foreach( $tours as &$tour){
						// in tourian assoc, context id is the content id of tour content.
						$t[] = $tour['context_id'];
					} unset($tour);
				}
				$data['tours'] = $t;

				return $data;

			} else {
				if( empty($content_id) ){
					$data = array();
					$context_type = 'tourian.tours';
					// array list of all urls with their associated tours.
					$stmt = $this->conn->prepare("SELECT * FROM `system_content` WHERE `context_type` = :context_type ORDER BY `content_id` DESC");
					$stmt->bindParam(':context_type', $context_type);
					$stmt->execute();
					$tours = $stmt->fetchAll();
					foreach ($tours as $key => &$tour) {
						$data[$key]['id'] = $tour['content_id'];
						$data[$key]['tour'] = json_decode($tour['content'],true);
					} unset($tour);

					return $data;
				} else {
					 return json_decode($data['content'],true);
				}
			}
		}
	}

	public function remove($context,$content_id){

		$stmt = $this->conn->prepare("DELETE FROM `system_content` WHERE `context_type` = :context_type AND `content_id` = :content_id");
		$stmt->bindParam(':context_type', $context);
		$stmt->bindParam(':content_id', $content_id);
		$stmt->execute();

		if( $context === 'tourian.url' ){
			$context = 'tourian.assoc';
			$stmt = $this->conn->prepare("DELETE FROM `system_content` WHERE `context_type` = :context_type AND `content` = :content");
			$stmt->bindParam(':context_type', $context);
			$stmt->bindParam(':content', $content_id);
			$stmt->execute();
		} elseif( $context === 'tourian.tours' ) {
			$context = 'tourian.assoc';
			$stmt = $this->conn->prepare("DELETE FROM `system_content` WHERE `context_type` = :context_type AND `context_id` = :content_id");
			$stmt->bindParam(':context_type', $context);
			$stmt->bindParam(':content_id', $content_id);
			$a = $stmt->execute();
		}

		return true;
	}

	public function get( $url ){
		$data = array();
		// find all matching templates
		$tours = $this->findurl($url);

		if(!empty($tours)){
			// got tour
			$popped = $this->getPop();
			if(empty($popped)){
				// got url in 1st try only.
				// $this->formFindTour($tours);
				$this->formTour($tours[0]);
			}

			// didnot get tours in 1st try. had to remove paths to search. now search for accurate match by appending removed paths from the short listed urls.

			// replace the last popped with templated part for search.
			array_pop($popped);

			/* $last_value = end($popped); $prev_2_last_value = prev($popped); */

			// append all remaining popped values and add to match .
			$prev_2_last_value = implode('/',array_reverse($popped));

			$matches = array();

			foreach( $tours as &$tour ){
				if( $tour['content'] == $url.'/{}/'.$prev_2_last_value ){
					// $matches[] = $tour;
					$matches = $tour;
				}
			} unset($tour);

			if(!empty($matches)){
				$this->formTour($matches);
			} else {
				$data = array( 'type' => 'fail', 'message' => 'Unable to find tours for the requested page.' );
			}
		} else {
			// deconstruct the url.
			$parsed_url = parse_url($url);
			if(!empty($parsed_url['query'])){
				// if url has get parameters, then remove all parameter values. no need to consider get parameters
				$parsed_url['query'] = '';
			}

			// remove the last from path and then search.
			$parsed_path = explode('/',$parsed_url['path']);

			//save the pooped value for accurate search within shortlisted tours.
			$this->setPop(array_pop($parsed_path));

			// form url
			$parsed_url['path'] = implode('/',$parsed_path);
			$new_url = http_build_url($parsed_url);

			if( $new_url !== 'http://127.0.0.1' ){
				$this->get( $new_url );
			} else {
				$data = array( 'type' => 'fail', 'message' => 'Unable to find tours for the requested page.' );
			}
		}
		echo json_encode($data); exit;
	}

	public function get2( $url ){

		$url_limit = array(
			'https://jigsawacademy.net/',
			'https://www.jigsawacademy.net/',
			'https://sandbox.jigsawacademy.net/',
		);

		$data = array();
		// find all matching templates
		$tours = $this->findurl($url);

		if(!empty($tours)){
			$this->formTour($tours[0]);
		} else {
			// if url not found on first try then, shift {template} part towards left in url and search by like search in database.
			// eg. /jaws/tourian/list - not found
			// next search /jaws/tourian/{} - not found ? found:
			// next search /jaws/{}/list - so on till direct match not found from database.

			// deconstruct the url.
			$parsed_url = parse_url($url);
			$parsed_url_2 = parse_url($url);
			if(!empty($parsed_url['query'])){
				// if url has get parameters, then remove all parameter values. no need to consider get parameters
				$parsed_url['query'] = '';
				$parsed_url_2['query'] = '';
			}

			$parsed_path = explode('/',$parsed_url['path']);

			$count = count($parsed_path);
			for ( $i = $count-1; $i >= 0; $i-- ){

				$parsed_path = explode('/',$parsed_url['path']);
				$parsed_path[$i] = '{}';
				$parsed_url_2['path'] = implode('/',$parsed_path);
				$new_url = http_build_url($parsed_url_2);

				if( !in_array( $new_url, $url_limit) ){
					$data = $this->findTour( $new_url );

					$url_array = array(
						'https://jigsawacademy.net/courses/{}/modules/{}',
						'https://jigsawacademy.net/courses/{}/modules/{}/'
						);
					//elseif( validateSecondCurly($url) ==  'https://jigsawacademy.net/courses/{}/modules/{}' || validateSecondCurly($url) ==  'https://www.jigsawacademy.net/courses/{}/modules/{}')
					if( empty($data) && in_array( $this->validateSecondCurly($url),$url_array )  )
					{	//echo $url;exit();
						//$new_url = "https://jigsawacademy.net/courses/{}/modules/{}";
						$new_url = "https://jigsawacademy.net/courses/{}/modules/{}";
						$data = $this->findSecondCurly( $new_url );
					}
				}
				else {
					$data = array( 'type' => 'fail', 'message' => 'Unable to find tours for the requested page.' );
				}
			}
		}
		echo json_encode($data);
		exit;
	}

	protected function validateSecondCurly($url)
	{
		$parse_url = parse_url($url);
		$parse_url_2 =  parse_url($url);

		$exploded_url = explode('/',$parse_url['path']);
		$exploded_url[2] = '{}';
		if(count($exploded_url) > 4 && ($exploded_url[4] != ''))
		{ $exploded_url[4] = '{}';}
		$parse_url_2['path'] = implode('/', $exploded_url);
		$replaced_url = http_build_url($parse_url_2);
		$replaced_url = rtrim($replaced_url, "/");
		return $replaced_url;
	}

	protected function findurl($url){
		// this returns all matches with the url
		$stmt = $this->conn->prepare("SELECT * FROM `system_content` WHERE `context_type` = 'tourian.url' AND `content` LIKE :content");
		$stmt->bindValue(':content', '%'.$url.'%');
		$stmt->execute();
		$data = $stmt->fetchAll();

		return $data;
	}

	protected function findTour($url){
		// this finds all matches of the url and selects the first one to form tour.
		$stmt = $this->conn->prepare("SELECT * FROM `system_content` WHERE `context_type` = 'tourian.url' AND `content` LIKE :content");
		$stmt->bindValue(':content', '%'.$url);
		$stmt->execute();
		$data = $stmt->fetchAll();
		$return = false;
		if(!empty($data)){
			$return = $this->formTour($data[0]);
		}
		return $return;
	}

	protected function findSecondCurly($url){
		// this finds all matches of the url and selects the first one to form tour.
		$stmt = $this->conn->prepare("SELECT * FROM `system_content` WHERE `context_type` = 'tourian.url' AND `content` = :content");
		$stmt->bindValue(':content', $url);
		$stmt->execute();
		$data = $stmt->fetchAll();
		if(!empty($data)){
			$this->formTour($data[0]);
		}
	}

	protected function setPop($path){
		$this->values[] = $path;
	}

	protected function getPop(){
		return $this->values;
	}

	protected function formTour($tour){
		$steps_arr = array();
		$stmt = $this->conn->prepare("SELECT * FROM `system_content` WHERE `context_type` = 'tourian.assoc' AND `content` LIKE :content");
		$stmt->bindValue(':content', $tour['content_id']);
		$stmt->execute();
		$datas = $stmt->fetchAll();
		if( !empty($datas) ){
			// got related tours. find tour details and send.
			foreach($datas as &$data){
				$stmt = $this->conn->prepare("SELECT content FROM `system_content` WHERE `context_type` = 'tourian.tours' AND `content_id` LIKE :content_id");
				$stmt->bindValue(':content_id', $data['context_id']);
				$stmt->execute();
				$tour_details = $stmt->fetch();
				$tour_content = json_decode($tour_details['content'],true);
				foreach( $tour_content['steps'] as $steps ){
					// for tour type
					if( $steps['user_interaction'] == 1 ){
						$type = 'interactive';
					} else {
						$type = 'static';
					}
					// for tour selector
					if($steps['tour_selector']){
						$selector = $steps['tour_selector'];
					} else {
						$selector = '';
					}
					// for tour message
					if($steps['tour_message']){
						$message = $steps['tour_message'];
					} else {
						$message = '[No message Set]';
					}
					// for tour event type as user action
					if( $steps['interaction_type'] == 'event' && $steps['step_trigger']){
						$action = $steps['step_trigger'];
						$timeout = 0;
					}
					/*else {
						$action = '';
						$timeout = 0;
					}*/
					// for tour event type as timeout
					elseif($steps['interaction_type'] == 'timeout' && $steps['tour_timeout']){
						$action = '';
						$timeout = $steps['tour_timeout'] * 1000;
					}
					else {
						$action = '';
						$timeout = 0;
					}
					$steps_arr[] = array(
						'type'		=> $type,
						'selector'	=> $selector,
						'message'	=> $message,
						'timeout'	=> $timeout,
						'event'		=> $action
					);
				}
				$return[$tour_content['tour_name']] = $steps_arr;
			} unset($data);
		} else {
			$return = array( 'type' => 'fail', 'message' => 'Unable to find tours for the requested page.' );
		}
		echo json_encode($return);
		exit;
	}

	protected function formFindTour($tour){
		// for single tour direct match. not used anymore.
		$stmt = $this->conn->prepare("SELECT * FROM `system_content` WHERE `context_type` = 'tourian.assoc' AND `content` LIKE :content");
		$stmt->bindValue(':content', $tour[0]['content_id']);
		$stmt->execute();
		$datas = $stmt->fetchAll();
		if( !empty($datas) ){
			// got related tours. find tour details and send.
			foreach($datas as &$data){
				$stmt = $this->conn->prepare("SELECT content FROM `system_content` WHERE `context_type` = 'tourian.tours' AND `content_id` LIKE :content_id");
				$stmt->bindValue(':content_id', $data['context_id']);
				$stmt->execute();
				$tour_details = $stmt->fetch();
				$return[] = json_decode($tour_details['content'],true);
			} unset($data);
		} else {
			$return = array( 'type' => 'fail', 'message' => 'Unable to find tours for the requested page.' );
		}
		echo json_encode($return);
		exit;
	}

	protected function searchInTours(){
		// not used right now. can be used for multiple templated search.
		echo 'search in tours'; exit;
	}
}
?>