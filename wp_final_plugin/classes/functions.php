<?php
function text_ajax_process_request() {// This function updates of inserts data provided by the ajax call
	$post_id = "";
  	if ( isset( $_POST["text"] ) ) {
		if($_POST['user'] == $_SESSION['alumni_user'])
		{
			$my_query = new WP_Query( array( 'post_type' => 'alumni', 'meta_key' => 'alumni_author_id', 'meta_value' => $_POST['id'] ) );
			if( $my_query->have_posts()) {
				$post = array();
				$post['ID'] 				= $my_query->posts[0]->ID;
				$post['post_title']         = $_POST['title'];
				$post['post_content']       = $_POST['text'];
				$post['post_type']      = 'alumni';
				wp_update_post($post);
				$post_id = $post['ID'];
				echo 'tekst geupdate';
			}
			else{
				$post = array();
				$post['post_title']         = $_POST['title'];
				$post['post_content']       = $_POST['text'];
				$post['post_type']      = 'alumni';
				$postID                 = wp_insert_post($post);
				add_post_meta($postID, 'alumni_author_id', $_POST['id']);
				$post_id = $postID;
				echo 'tekst in de database gestopt';
			}
			if(isset($_FILES['image'] ) ) {
				require_once(ABSPATH . "wp-admin" . '/includes/image.php');
				require_once(ABSPATH . "wp-admin" . '/includes/file.php');
				require_once(ABSPATH . "wp-admin" . '/includes/media.php');
				$file_handler = 'image';
				$attach_id = media_handle_upload($file_handler,$post_id );
				update_post_meta($post_id,'_thumbnail_id',$attach_id);
			}
		}
		else
		{
			echo "Je bent niet ingelogd als deze gebruiker!";
		}

	}
			die();

}

add_action('wp_ajax_post_alumni_data', 'text_ajax_process_request');
add_action('wp_ajax_nopriv_post_alumni_data', 'text_ajax_process_request');

function get_alumni_content_process_request() { // Tis function sends back the content of an alumni from the database
	  if ( isset( $_POST["id"] ) ) {
		$my_query = new WP_Query( array( 'post_type' => 'alumni', 'meta_key' => 'alumni_author_id', 'meta_value' => $_POST['id'] ) );
	  }
	  if( $my_query->have_posts())
	  {
		$post_data = $my_query->posts[0];
		$thumb_id = get_post_thumbnail_id($post_data->ID);
		$data_response[] = $post_data;

		if($thumb_id)
		{
			$thumb_url = wp_get_attachment_image_src($thumb_id,'thumbnail-size', true);
			$data_response[] = $thumb_url[0];
		}
		echo (json_encode($data_response));
	  }else {
		  echo "false";
	  }

	  die();
		
}
add_action('wp_ajax_get_alumni_content', 'get_alumni_content_process_request');
add_action('wp_ajax_nopriv_get_alumni_content', 'get_alumni_content_process_request');


function get_json_data_process_request() { // This function sends back the data from the eduframe endpoint 

		$auth = Alumni_Zoekfunctie()->settings->get_settings()['auth'];
		$headers = array(
			'Authorization' => 'Bearer ' . $auth 
		);
		$request = array(
			'headers' => $headers,
			'method'  => "GET",
		);
		$responses = [];
		$getCustomersForMap = wp_remote_request(Alumni_Zoekfunctie()->settings->get_settings()['url']."/api/v1/customers?include=address", $request);
		$getPassedCources = wp_remote_request(Alumni_Zoekfunctie()->settings->get_settings()['url']."/api/v1/courses?include=planned_courses.customer_enrollments.enrollments", $request);
		$getEnrollments = wp_remote_request(Alumni_Zoekfunctie()->settings->get_settings()['url']."/api/v1/courses?include=planned_courses.customer_enrollments.enrollments", $request);
		//$alumni_user = $_SESSION['alumni_user'];
		array_push($responses, $getCustomersForMap, $getPassedCources, $getEnrollments);
		echo wp_send_json($responses);
		die();
}
add_action('wp_ajax_get_json_data', 'get_json_data_process_request');
add_action('wp_ajax_nopriv_get_json_data', 'get_json_data_process_request');


function echo_username() { // This function sends back the data from the eduframe endpoint 
	if(isset($_SESSION['alumni_user']))
	{
		echo $_SESSION['alumni_user'];
	}
	else{
		echo "false";
	}
	die();
}
add_action('wp_ajax_get_username', 'echo_username');
add_action('wp_ajax_nopriv_get_username', 'echo_username');


		function cas() // This function is called when the headers are send becase wp_redirect makes use of a header
			{
					//Construct ticketurl
					$uri_parts = explode('?', $_SERVER['REQUEST_URI'], 2);
					$ticketUrl = 'http://' . $_SERVER['HTTP_HOST'] . $uri_parts[0]; 
					//if(isset($_GET['ticket']) && !empty($_GET['ticket'])){
						$url = Alumni_Zoekfunctie()->settings->get_settings()['url']."/cas/proxyValidate.xml?service=".$ticketUrl."&ticket=" .$_GET['ticket'];
						$response = wp_remote_get($url);
						$xml = wp_remote_retrieve_body($response);
						$user = strip_tags($xml);
							if (strpos($user, '@') !== false) {

								// split response into valid username
								$user = explode('@',$user,2);
								$user = $user[1]; 
								$_SESSION['alumni_user'] = preg_replace('/\s+/', '', $user);
							}
					//}	
			}

function handle_cas()
{

	if(!isset($_SESSION['alumni_user']) && isset($_GET['ticket']))
	{
		cas();
	}

}
add_action('wp_loaded', 'handle_cas');
?>