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

    JIGSAW ACADEMY WORKFLOW SYSTEM v1
    ---------------------------------
*/

    // Prevent exclusive access
    if (!defined("JAWS")) {
        header('Location: https://www.jigsawacademy.com');
        die();
    }
   
    //active campaign api for iot lead sync
    function ac_event_contact_iot($user_data, $parameters = array(), $context = "", $optional = ""){
        // print_r($user_data);
        // print_r($parameters);
        $url = 'https://jigsawacademyautomation.api-us1.com';
        
        $params = array(
            'api_key'      => '515ad0ae47d0eb00af9c4ea6033fb85734ff3ebb25fc2f0fb72a8aeec93dac452eb69796',
            'api_action'   => 'contact_add',
            'api_output'   => 'json',
        );
        
        $utm_params = $parameters;
        
        $post = array(
            'email'                    => $user_data['email'],
            'first_name'               => empty($user_data['firstName']) ? $user_data['name'] : $user_data['firstName'],
            'last_name'                => empty($user_data['lastName']) ? '' : $user_data['lastName'],
            'phone'                    => $user_data['phone'],
            'tags'                     => 'api,'.$context,
            'field[%SUBSCRIPTION_URL%,0]'	=> !empty($parameters['url']) ? $parameters['url'] : $_SERVER["REQUEST_URI"],
            'field[%URL%,0]'				=> $_SERVER['HTTP_REFERER'],
            'p[34]'                         => 34,  // sync to IoT list id 34
        );
        
        if ( !empty($utm_params["utm_source"]) && (strlen($utm_params["utm_source"]) > 0) ) {
            $post['field[%UTM_SOURCE%,0]'] = $utm_params["utm_source"];
        } else if ( !empty($utm_params["source"]) && (strlen($utm_params["source"]) > 0) ) {
            $post['field[%UTM_SOURCE%,0]'] = $utm_params["source"];
        } else if ( (isset($_COOKIE["__utmz"])) && (strlen($_COOKIE["__utmz"]) > 0) ) {
            $post['field[%UTM_SOURCE%,0]'] = extract_var($_COOKIE["__utmz"], "utmcsr");
        }
        
        if ( !empty($utm_params["utm_medium"]) && (strlen($utm_params["utm_medium"]) > 0) ) { 
            $post['field[%UTM_MEDIUM%,0]'] = $utm_params["utm_medium"];
        } else if ( !empty($utm_params["medium"]) && (strlen($utm_params["medium"]) > 0) ) {
            $post['field[%UTM_MEDIUM%,0]'] = $utm_params["medium"];
        } else if ( (isset($_COOKIE["__utmz"])) && (strlen($_COOKIE["__utmz"]) > 0) ) { 
            $post['field[%UTM_MEDIUM%,0]'] = extract_var($_COOKIE["__utmz"], "utmcmd");
        }
        
        if ( !empty($utm_params["utm_campaign"]) && (strlen($utm_params["utm_campaign"]) > 0) ) {
            $post['field[%UTM_CAMPAIGN%,0]'] = $utm_params["utm_campaign"];
        } else if ( !empty($utm_params["campaign"]) && (strlen($utm_params["campaign"]) > 0) ) {
            $post['field[%UTM_CAMPAIGN%,0]'] = $utm_params["campaign"];
        } else if ( (isset($_COOKIE["__utmz"])) && (strlen($_COOKIE["__utmz"]) > 0) ) {
            $post['field[%UTM_CAMPAIGN%,0]'] = extract_var($_COOKIE["__utmz"], "utmccn");
        }
        if(isset($_COOKIE["__utmz"])) $post['tags'] = $post['tags'] . ',cookie';
        
        $query = "";
        foreach( $params as $key => $value ){ $query .= urlencode($key) . '=' . urlencode($value) . '&'; }
        $query = rtrim($query, '& ');
        $data = "";
        foreach( $post as $key => $value ){ $data .= urlencode($key) . '=' . urlencode($value) . '&'; }
        $data = rtrim($data, '& ');
        $url = rtrim($url, '/ ');
        
        // define a final API request - GET
        $api = $url . '/admin/api.php?' . $query;

        $request = curl_init($api);
        curl_setopt($request, CURLOPT_HEADER, 0);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($request, CURLOPT_POSTFIELDS, $data);
        curl_setopt($request, CURLOPT_FOLLOWLOCATION, true);

        $response = (string)curl_exec($request); $result = json_decode($response,true);
        curl_close($request);
        return json_encode( array( "status" => true, "message" => $result['result_message'] ) );

    }

    function extract_var($cookieval, $var) {

        $pos = strpos($cookieval, $var);
        if ($pos === false) return "";

        $strsub = substr($cookieval, $pos + strlen($var) + 1);
        $pos = strpos($strsub, "|");
        if ($pos === false) $pos = strlen($strsub);

        return substr($strsub, 0, $pos);

    }


?>