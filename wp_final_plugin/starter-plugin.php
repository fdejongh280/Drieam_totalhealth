<?php
/**
 * Plugin Name: Zoekfunctie
 * Plugin URI: #
 * Description: Zoekfunctie TotalHealth
 * Version: 1.0.0
 * Author: Elwin van den Eijnden & Floris de Jongh
 * Author URI: www.dynamicpixel.nl
 * Requires at least: 4.0.0
 * Tested up to: 4.0.0
 *
 * Text Domain: Zoekfunctie
 * Domain Path: /languages/
 *
 * @package Starter_Plugin
 * @category Core
 * @author Matty
 */
	require_once( 'alumni.wdgt.php' );
  session_start();
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Returns the main instance of Starter_Plugin to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object Starter_Plugin
 */
function Starter_Plugin() {
	return Starter_Plugin::instance();
} // End Starter_Plugin()


add_action( 'plugins_loaded', 'Starter_Plugin' );
add_filter( 'allowed_http_origin', '__return_true' );

		function cas() // This function is called when the headers are send becase wp_redirect makes use of a header
			{
				if(!isset($_SESSION['alumni_user']))
				{
					//Construct ticketurl
					$uri_parts = explode('?', $_SERVER['REQUEST_URI'], 2);
					$ticketUrl = 'http://' . $_SERVER['HTTP_HOST'] . $uri_parts[0]; 
					if(!isset($_GET['ticket']) && empty($_GET['ticket'])){
						
						fetchNewTicket($ticketUrl);
					} 
					else {
						$url = "http://total-health.testing.edufra.me/cas/proxyValidate.xml?service=".$ticketUrl."&ticket=" .$_GET['ticket'];
						$response = wp_remote_get($url);
						$xml = wp_remote_retrieve_body($response);
						$user = strip_tags($xml);

							if (strpos($user, '@') !== false) {

								// split response into valid username
								$user = explode('@',$user,2);
								$user = $user[1]; 
								error_log("setuser starting");
								
								$_SESSION['alumni_user'] = $user;
								
							}
							else{
								fetchNewTicket($ticketUrl);
							}
					}
				}
			}
			function fetchNewTicket($ticketUrl)
			{
				wp_redirect("http://total-health.testing.edufra.me/cas/login?service=" .$ticketUrl, $status = 301);
				exit();
 			}
			add_action( 'send_headers', 'add_header_acc' );
			function add_header_acc() {
				header( 'Access-Control-Allow-Origin: *' );
				// Send allow cross domain header 
				cas();
			}

function custom_post_type() // This function sets the custom post type
{ 
	$labels = array(
			'name' => 'Alumni',
			'singular_name' => 'Alumni',
			'add_new' => 'Add Item',
			'all_items' => 'All Items',
			'add_new_item' => 'Add Item',
			'edit_item' => 'Edit Item',
			'new_item' => 'New Item',
			'view_item' => 'View Item',
			'search_item' => 'Zoek Alumni',
			'not_found' => 'No items found',
			'not_found_in_trash' => 'No items found in trash',
			'parent_item_colon' => 'Parent Item'
		);
		$args = array(
			'labels' => $labels,
			'public' => true,
			'has_archive' => true,
			'publicly_queryable' => true,
			'query_var' => true,
			'rewrite' => true,
			'capability_type' => 'post',
			'hierarchical' => false,
			'supports' => array(
				'title',
				'editor',
				'thumbnail',
				'revisions',
			),
			'taxonomies' => array('category', 'post_tag'),
			'menu_position' => 5,
			'exclude_from_search' => false
		);
		register_post_type('Alumni',$args);
}
add_action('init','custom_post_type');


// Register the widgets to Wordpress.
add_action( 'widgets_init', function () {
	register_widget( 'Alumni_Widget' );
} );



function text_ajax_process_request() {// This function updates of inserts data provided by the ajax call
	$post_id = "";
  	if ( isset( $_POST["text"] ) ) {
		$my_query = new WP_Query( array( 'post_type' => 'Alumni', 'meta_key' => 'alumni_author_id', 'meta_value' => $_POST['id'] ) );
		if( $my_query->have_posts()) {
			$post = array();
			$post['ID'] 				= $my_query->posts[0]->ID;
			$post['post_title']         = $_POST['title'];
			$post['post_content']       = $_POST['text'];
			$post['post_type']      = 'Alumni';
			wp_update_post($post);
			$post_id = $post['ID'];
			echo 'tekst geupdate';
		}
		else{
			$post = array();
			$post['post_title']         = $_POST['title'];
			$post['post_content']       = $_POST['text'];
			$post['post_type']      = 'Alumni';
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
			die();

}

add_action('wp_ajax_test_response', 'text_ajax_process_request');
add_action('wp_ajax_nopriv_test_response', 'text_ajax_process_request');

function get_alumni_content_process_request() { // Tis function sends back the content of an alumni from the database
	  if ( isset( $_POST["id"] ) ) {
		$my_query = new WP_Query( array( 'post_type' => 'Alumni', 'meta_key' => 'alumni_author_id', 'meta_value' => $_POST['id'] ) );
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

		$auth = "f00c7fadeab67e69bc6e0f0dc0d1edf8";
		$headers = array(
			'Authorization' => 'Bearer ' . $auth 
		);
		$request = array(
			'headers' => $headers,
			'method'  => "GET",
		);
		$responses = [];
		$getCustomersForMap = wp_remote_request("http://total-health.testing.edufra.me/api/v1/customers?include=address", $request);
		$getPassedCources = wp_remote_request("http://total-health.testing.edufra.me/api/v1/courses?include=planned_courses.customer_enrollments.enrollments", $request);
		$getEnrollments = wp_remote_request("http://total-health.testing.edufra.me/api/v1/courses?include=planned_courses.customer_enrollments.enrollments", $request);
		$alumni_user = $_SESSION['alumni_user'];
		array_push($responses, $getCustomersForMap, $getPassedCources, $getEnrollments, $alumni_user);
		echo wp_send_json($responses);
		die();
}
add_action('wp_ajax_get_json_data', 'get_json_data_process_request');
add_action('wp_ajax_nopriv_get_json_data', 'get_json_data_process_request');



			//add_action('plugins_loaded', "cas");
/**
 * Main Starter_Plugin Class
 *
 * @class Starter_Plugin
 * @version	1.0.0
 * @since 1.0.0
 * @package	Starter_Plugin
 * @author Matty
 */
final class Starter_Plugin {
	/**
	 * Starter_Plugin The single instance of Starter_Plugin.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * The token.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $token;

	/**
	 * The version number.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $version;

	/**
	 * The plugin directory URL.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $plugin_url;

	/**
	 * The plugin directory path.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $plugin_path;

	// Admin - Start
	/**
	 * The admin object.
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $admin;

	/**
	 * The settings object.
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $settings;
	// Admin - End

	// Post Types - Start
	/**
	 * The post types we're registering.
	 * @var     array
	 * @access  public
	 * @since   1.0.0
	 */
	public $post_types = array();
	// Post Types - End
	/**
	 * Constructor function.
	 * @access  public
	 * @since   1.0.0
	 */
	public function __construct () {
		$this->token 			= 'starter-plugin';
		$this->plugin_url 		= plugin_dir_url( __FILE__ );
		$this->plugin_path 		= plugin_dir_path( __FILE__ );
		$this->version 			= '1.0.0';

		// Admin - Start
		require_once( 'classes/class-starter-plugin-settings.php' );
			$this->settings = Starter_Plugin_Settings::instance();

		if ( is_admin() ) {
			require_once( 'classes/class-starter-plugin-admin.php' );
			$this->admin = Starter_Plugin_Admin::instance();
		}
		// Admin - End

		// Post Types - Start
		require_once( 'classes/class-starter-plugin-post-type.php' );
		require_once( 'classes/class-starter-plugin-taxonomy.php' );

		// Register an example post type. To register other post types, duplicate this line.
		$this->post_types['thing'] = new Starter_Plugin_Post_Type( 'thing', __( 'Thing', 'starter-plugin' ), __( 'Things', 'starter-plugin' ), array( 'menu_icon' => 'dashicons-carrot' ) );
		// Post Types - End
		register_activation_hook( __FILE__, array( $this, 'install' ) );

		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
	} // End __construct()

	/**
	 * Main Starter_Plugin Instance
	 *
	 * Ensures only one instance of Starter_Plugin is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Starter_Plugin()
	 * @return Main Starter_Plugin instance
	 */
	public static function instance () {
		if ( is_null( self::$_instance ) )
			self::$_instance = new self();
		return self::$_instance;
	} // End instance()

	/**
	 * Load the localisation file.
	 * @access  public
	 * @since   1.0.0
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'starter-plugin', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	} // End load_plugin_textdomain()

	/**
	 * Cloning is forbidden.
	 * @access public
	 * @since 1.0.0
	 */
	public function __clone () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0.0' );
	} // End __clone()

	/**
	 * Unserializing instances of this class is forbidden.
	 * @access public
	 * @since 1.0.0
	 */
	public function __wakeup () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0.0' );
	} // End __wakeup()

	/**
	 * Installation. Runs on activation.
	 * @access  public
	 * @since   1.0.0
	 */
	public function install () {
		$this->_log_version_number();
	} // End install()

	/**
	 * Log the plugin version number.
	 * @access  private
	 * @since   1.0.0
	 */
	private function _log_version_number () {
		// Log the version number.
		update_option( $this->token . '-version', $this->version );
	} // End _log_version_number()
} // End Class
