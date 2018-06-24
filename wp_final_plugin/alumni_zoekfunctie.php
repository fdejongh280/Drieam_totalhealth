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
 * @package Alumni_Zoekfunctie
 * @category Core
 * @author Matty
 */
	require_once( 'main.php' );
  session_start();
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Returns the main instance of Alumni_Zoekfunctie to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object Alumni_Zoekfunctie_Plugin
 */
function Alumni_Zoekfunctie() {
	return Alumni_Zoekfunctie::instance();
} // End Alumni_Zoekfunctie()


add_action( 'plugins_loaded', 'Alumni_Zoekfunctie' );

// Register the widgets to Wordpress.
add_action( 'widgets_init', function () {
	register_widget( 'Alumni_Widget' );
} );

/**
 * Main Alumni_Zoekfunctie Class
 *
 * @class Alumni_Zoekfunctie
 * @version	1.0.0
 * @since 1.0.0
 * @package	Alumni_Zoekfunctie
 * @author Matty
 */
final class Alumni_Zoekfunctie {
	/**
	 * Alumni_Zoekfunctie The single instance of Alumni_Zoekfunctie.
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
		$this->token 			= 'alumni-zoekfunctie';
		$this->plugin_url 		= plugin_dir_url( __FILE__ );
		$this->plugin_path 		= plugin_dir_path( __FILE__ );
		$this->version 			= '1.0.0';

		//include ajax handler files
		// Admin - Start
		require_once( 'classes/class-alumni-zoekfunctie-settings.php' );
			$this->settings = Alumni_Zoekfunctie_Settings::instance();

		if ( is_admin() ) {
			require_once( 'classes/class-alumni-zoekfunctie-admin.php' );
			$this->admin = Alumni_Zoekfunctie_Admin::instance();
		}
		// Admin - End

		// Post Types - Start
		require_once( 'classes/class-alumni-zoekfunctie-post-type.php' );
		require_once( 'classes/class-alumni-zoekfunctie-taxonomy.php' );

		// Register an example post type. To register other post types, duplicate this line.
		$this->post_types['alumni'] = new Alumni_Zoekfunctie_Post_Type( 'alumni', __( 'Alumni', 'alumni-zoekfunctie' ), __( 'Alumnis', 'alumni-zoekfunctie' ), array( 'menu_icon' => 'dashicons-carrot' ) );
		// Post Types - End
		register_activation_hook( __FILE__, array( $this, 'install' ) );

		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
		//action filters for functions below
		add_action('wp_ajax_post_alumni_data', array( $this,'insert_or_update_data_from_alumni_in_db'));
		add_action('wp_ajax_nopriv_post_alumni_data', array( $this,'insert_or_update_data_from_alumni_in_db'));
		add_action('wp_ajax_get_alumni_content', array( $this,'fetch_alumni_data_from_db'));
		add_action('wp_ajax_nopriv_get_alumni_content', array( $this,'fetch_alumni_data_from_db'));
		add_action('wp_ajax_get_json_data', array( $this,'fetch_data_from_eduframe_endpoint'));
		add_action('wp_ajax_nopriv_get_json_data', array( $this,'fetch_data_from_eduframe_endpoint'));
		add_action('wp_ajax_get_username', array( $this,'echo_username_if_logged_in'));
		add_action('wp_ajax_nopriv_get_username', array( $this,'echo_username_if_logged_in'));
		add_action('wp_loaded', array( $this,'handle_cas'));
	} // End __construct()

	 //Begin funtions
		public function insert_or_update_data_from_alumni_in_db() // This function updates of inserts data provided by the ajax call
		{
			$post_id = "";
			if ( isset( $_POST["text"] ) ) 
			{
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

		public function fetch_alumni_data_from_db() // Tis function sends back the content of an alumni from the database
		{
			if ( isset( $_POST["id"] ) ) 
			{
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
			}
			else 
			{
				echo "false";
			}
			die();
		}



		public function fetch_data_from_eduframe_endpoint() { // This function sends back the data from the eduframe endpoint 

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
			array_push($responses, $getCustomersForMap, Alumni_Zoekfunctie()->settings->get_settings()['url']);
			if(isset($_SESSION['alumni_user']))
			{
				array_push($responses, $_SESSION['alumni_user']);
			}
			echo wp_send_json($responses);
			die();
		}

		public function echo_username_if_logged_in() { // This function sends back the data from the eduframe endpoint 
			if(isset($_SESSION['alumni_user']))
			{
				echo $_SESSION['alumni_user'];
			}
			else{
				echo "false";
			}
			die();
		}



		public function cas() // This function is called when the headers are send becase wp_redirect makes use of a header
			{
			//Construct ticketurl
				$uri_parts = explode('?', $_SERVER['REQUEST_URI'], 2);
				$ticketUrl = 'http://' . $_SERVER['HTTP_HOST'] . $uri_parts[0]; 
				$url = Alumni_Zoekfunctie()->settings->get_settings()['url']."/cas/proxyValidate.xml?service=".$ticketUrl."&ticket=" .$_GET['ticket'];
				$response = wp_remote_get($url);
				$xml = wp_remote_retrieve_body($response);
				$user = strip_tags($xml);
				if (strpos($user, '@') !== false) 
				{
				// split response into valid username
					$user = explode('@',$user,2);
					$user = $user[1]; 
					$_SESSION['alumni_user'] = preg_replace('/\s+/', '', $user);
				}
					
			}

		public function handle_cas()
		{
			if(!isset($_SESSION['alumni_user']) && isset($_GET['ticket']))
			{
				$this->cas();
			}
		}

	/**
	 * Main Alumni_Zoekfunctie Instance
	 *
	 * Ensures only one instance of Alumni_Zoekfunctie is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Alumni_Zoekfunctie()
	 * @return Main Alumni_Zoekfunctie instance
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
		load_plugin_textdomain( 'alumni-zoekfunctie', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
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
