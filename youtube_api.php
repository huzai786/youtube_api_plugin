<?php
/**
 * Plugin name: Query APIs
 * Plugin URI: https://github.com/huzai786/youtube-api
 * Description: Get information from external APIs in WordPress
 * Author: Muhammad huzaifa
 * Author URI: https://github.com/huzai786
 * version: 0.1.0
 * License: GPL2 or later.
 * text-domain: get-yotube-results
 */

// If this file is access directly, abort!!!
defined( 'ABSPATH' ) or die( 'Unauthorized Access' );
add_shortcode('search_youtube_webscrape', 'get_youtube_results');

function get_youtube_results(){

    echo '<form method="GET" enctype="multipart/form-data" autocomplete="off">'; // printing form tag
    echo '<input type="text" name="search" style="width:400px;">';
    echo '<input type="submit" name="send_btn" value="Submit">';
    echo '</form>';


	if ($_SERVER['REQUEST_METHOD'] === 'GET') {
		if (isset($_GET['send_btn'])) {  // checking is form was submitted, then accessing to value
			$query = $_GET['search'];
			get_youtube_results_api( $query );
		}
	}
}


function get_youtube_results_api( $qry ) {

	$url = sprintf('http://127.0.0.1:5000/extract/?query=%s', $qry);
	
	$arguments = array(
		'method' => 'GET',
	);
	
	$response = wp_remote_get( $url, $arguments );

	if ( is_wp_error( $response ) ) {
		$error_message = $response->get_error_message();
		return "Something went wrong: $error_message";
	} else {
		echo sprintf("<h1>%s</h1>", $qry);
		$data = json_decode($response['body'], true);  // A list of {"id": id, "thumbnail": thumbnail, "title":title}
		if (count($data) == 0) {
			echo "<h3>Something went wrong please try again!</h3>";
			return array();
		} 
		else {
			$content ="<div class='container'>";
			$content .= "<style>ul {list-style-type: none;}</style>";
			$content .= "<ul>";
			$counter = 1;
			foreach ($data as $value) {
				$li = "<li>";
				$id = $value["id"];
				$title = $value["title"];
				$thumbnail = $value['thumbnail'];
				$src = sprintf("https://www.youtube.com/embed/%s", $id);

				$li .= <<<EOT
					<div>
					<img src="$thumbnail" alt="$title" width="320" height="180">
					</div>
					<h3>$title</h3>
					<button onclick="get_$counter()" style="margin-bottom: 15px;">Play Video</button>
					<br><hr>
					<script>
						function get_$counter() {
							document.getElementById("iframe${id}").innerHTML = "<iframe src=$src height=\"500\" width=\"700\"></iframe>";
						}
					</script>
					<div id=iframe${id}></div>
				EOT;
				$counter++;
				
				$li .= "</li>";
				$content .= $li;
			}

			$content .= "</ul>";
			$content .= "</div>";
			echo $content;
		}
	}
}
// function create_post( $result_data, $query ) {
// 	$url = 'http://127.0.0.1:5000/create_post/';
// 	$data_arr = array("title" => $query, "links" => $result_data);
// 	$data_obj = (object) $data_arr;
	
// 	$body = json_encode( $data_obj );

// 	$arguments = array(
// 		'method' => 'POST',
// 		'body' => $body,
// 		'headers' => array(
// 			'Content-Type' => 'application/json',
// 			'accept' => 'application/json'
// 			)
// 		);
// 	wp_remote_post( $url, $arguments );
// }

?>

