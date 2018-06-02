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



add_action( 'plugins_loaded', 'Starter_Plugin' );


//add_action('wp_footer','cas');
function cas()
{
	$uri_parts = explode('?', $_SERVER['REQUEST_URI'], 2);
	$ticketUrl = 'http://' . $_SERVER['HTTP_HOST'] . $uri_parts[0];
	if(!isset($_GET['ticket']) && empty($_GET['ticket'])){
		
		fetchNewTicket($ticketUrl);
	} else {
		$url = "http://total-health.testing.edufra.me/cas/proxyValidate.xml?service=".$ticketUrl."&ticket=" .$_GET['ticket'];
		$response = wp_remote_get($url);
		$xml = wp_remote_retrieve_body($response);
		$user = strip_tags($xml);
		 
			if (strpos($user, '@') !== false) {
				// split response into valid username
				$user = explode('@',$user,2);
				$user = $user[1]; 
				$_SERVER['user'] = $user;
			}
			else{
				fetchNewTicket($ticketUrl);
			}
	}
}
function fetchNewTicket($ticketUrl)
{
	echo '<script>location.href="http://total-health.testing.edufra.me/cas/login?service=' .$ticketUrl.'";</script>';
}
function custom_post_type()
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

add_action( 'wp_footer', 'functionX' );


    function functionX () {
		cas();
		$auth = "f00c7fadeab67e69bc6e0f0dc0d1edf8";
		$headers = array(
			'Authorization' => 'Bearer ' . $auth 
		);
		$request = array(
			'headers' => $headers,
			'method'  => "GET",
		);
		$response = wp_remote_request("http://total-health.testing.edufra.me/api/v1/customers?include=address", $request);
		$json = wp_remote_retrieve_body($response);
        $_SERVER['user'] = "snelwin";
if(isset($_SERVER['user']))
{
echo '

<header id="zoekfunctie" role="banner" class="column-xs main-xs-center">

    <form id="locator" class="container column-xs row-md cross-xs-stretch cross-md-end" action="">
        <div class="col">
        <label for="locator_text">Zoeken op postcode of woonplaats</label>
        <input type="text" id="searchTextField" name="searchTextField" placeholder="Zoek op postcode of woonplaats" required>
        </div>
    <div class="col-xs-two-thirds col-md-quarter">
    <label for="locator_radius">Astand</label>
        <select id="locator_radius" name="locator_radius">
            <option value="25">25 km</option>
            <option value="50">50 km</option>
            <option value="100">100 km</option>
            <option value="125">125 km</option>
            <option value="150">150 km</option>
            <option value="400">Toon alles</option>
        </select>
    </div>
    <button type="submit">zoeken</button>
    </form>
</header>
    
<aside id="map"></aside>
<main id="results"></main>
<div class="alumnicontainer">
		<div class="sidebar">
			<div class="sidebar-top">
				<img class="profile-image" src="http://www.slate.com/content/dam/slate/blogs/xx_factor/2014/susan.jpg.CROP.promo-mediumlarge.jpg" />
				<div class="profile-basic">
					<h1 class="name"></h1>
				</div>
			</div>
			<div class="profile-info">
				<p class="key">Email:</p>
				<p class="value" id = "email"></p><br>
                <p class="key">Telefoon:</p>
				<p class="value" id = "tel"></p><br>
                <p class="key">Adres:</p>
				<p class="value" id = "address">
				</p>
                <p class="key">Afgeronde cursussen:</p>
				<p class="value" id = "cources"></p><br>
			</div>
			<button id = "goBack" class = "btn">Terug</button>
		</div>
        
		<div class="content">
			<div class="work-experience">
				<h1 class="heading"> Masseuse</h1>
				<div class="info">
					<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                    
                    <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?</p>
                    
                    <p>Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt.</p>
				</div>
                <br>
				<button id = "editText" style="display: none; ">Text bewerken</button>
				<button id = "saveChanges" style="display: none;">Bewerking opslaan</button>
			</div>
		</div>
	</div>
	<script>var jsonData = '.json_encode($response).'; var loggedInUser = '.json_encode($_SERVER["user"]).';</script>
    <script  src = "https://ajax.googleapis.com/ajax/libs/jquery/3.0.0/jquery.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAFW3vAkUm_7An5IWXslzqQci7Y1rT_9C0&language=nl&libraries=geometry,places"></script>
    <script  src="wp-content/plugins/starter-plugin-master/index.js"></script>
    <link rel="stylesheet" type="text/css" href="wp-content/plugins/starter-plugin-master/style.css">
	<link rel="stylesheet" type="text/css" href="wp-content/plugins/starter-plugin-master/style2.css">
	';
}

        
	} 

function test_ajax_load_scripts() {
	// load our jquery file that sends the $.post request
	wp_enqueue_script( "ajax-test", plugin_dir_url( __FILE__ ) . '/index.js', array( 'jquery' ) );
 
	// make the ajaxurl var available to the above script
	wp_localize_script( 'ajax-test', 'the_ajax_script', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );	
}

add_action('wp_print_scripts', 'test_ajax_load_scripts');
remove_action('pre_post_update', 'wp_save_post_revision');// stop revisions

function text_ajax_process_request() {
	// first check if data is being sent and that it is the data we want
  	if ( isset( $_POST["text"] ) ) {
		$query = "SELECT ID FROM `wpjy_posts` WHERE post_author = '".$_POST['id']."' AND post_type = 'alumni'";
		global $wpdb;
		$res = $wpdb->get_results($query);

		if (count($res) > 0) 
		{
			$post = array();
			$post['ID'] 				= $res[0]->ID;
			$post['post_title']         = $_POST['title'];
			$post['post_content']       = $_POST['text'];
			$post['post_author'] 	= $_POST['id'];
			$post['post_type']      = 'Alumni';
			wp_update_post($post);
			echo 'Tekst geüpdate';
		}
		else{
			$post = array();
			$post['post_title']         = $_POST['title'];
			$post['post_content']       = $_POST['text'];
			$post['post_author'] 	= $_POST['id'];
			$post['post_type']      = 'Alumni';
			$postID                 = wp_insert_post($post);
			echo 'tekst in de database gestopt';
		}

		die();
	}
}
add_action('wp_ajax_test_response', 'text_ajax_process_request');

function get_alumni_content_process_request() {
	// first check if data is being sent and that it is the data we want
  	$res = "";
	  if ( isset( $_POST["id"] ) ) {
		$query = "SELECT post_title, post_content FROM `wpjy_posts` WHERE post_author = '".$_POST['id']."' AND post_type = 'alumni'";
		global $wpdb;
		$res = $wpdb->get_results($query);
	  }
	  if(isset($res[0]))
	  {
		echo (json_encode($res[0]));
	  }else{
		  echo "false";
	  }

	  die();
		

}
add_action('wp_ajax_get_alumni_content', 'get_alumni_content_process_request');


//add_action( 'wp_head', 'Links');
/*
function Links () {
    echo '
            <head>
            <meta charset="UTF-8">
            <aside id="map"></aside>
<main id="results"></main>
    <script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAFW3vAkUm_7An5IWXslzqQci7Y1rT_9C0&language=nl&libraries=geometry,places"></script>
    <script  src="js/index.js"></script>
            <!-- zoekwoord -->
            </head>
    ';
}
*/
/*
 wp_enqueue_script('index.js');
wp_enqueue_script('style.css');
*/

/*
function wpdocs_theme_name_scripts() {
    wp_enqueue_style( 'style.css', get_stylesheet_uri('style.css') );
    wp_enqueue_script( 'index.js', get_template_directory_uri() . 'index.js', array(), '1.0.0', true );
}
add_action( 'wp_enqueue_scripts', 'wpdocs_theme_name_scripts' );



function theme_name_scripts() {


  wp_enqueue_style( 'style-css',get_template_directory_uri() . '/style.css' );
    
    
    
  wp_enqueue_script( 'jquery-script', get_template_directory_uri() . '/index.js', array(), '1.0.0', true );
    
    wp_enqueue_script( 'jquery-script', get_template_directory_uri() . '/https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css', array(), '1.0.0', true );
    wp_enqueue_script( 'google-font', get_template_directory_uri() . '/https://fonts.googleapis.com/css?family=Montserrat:400,700', array(), '1.0.0', true );
    wp_enqueue_script( 'bootstrap', get_template_directory_uri() . '/https://anacoelhovicente.github.io/tangerine/dist/tangerine-grid.min.css', array(), '1.0.0', true );
    
    wp_enqueue_script( 'jquery', get_template_directory_uri() . '/https://code.jquery.com/jquery-2.2.4.min.js', array(), '1.0.0', true );
    wp_enqueue_script( 'google-api', get_template_directory_uri() . '/https://maps.googleapis.com/maps/api/js?key=AIzaSyAFW3vAkUm_7An5IWXslzqQci7Y1rT_9C0&language=nl&libraries=geometry,places', array(), '1.0.0', true );


}

add_action( 'wp_enqueue_scripts', 'theme_name_scripts' );
*/
/*
function admin_assets() 
        {    
            wp_enqueue_media();
            wp_register_script('los_admin_js', script_uri(__FILE__, 'wp-content/plugins/starter-plugin-master/index.js'), ['jquery'], $this->script_version, false);
            wp_register_style('los_admin_css', stylesheet_uri(__FILE__, 'style.css'), false, $this->style_version, 'screen');
            wp_register_style('google-api', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyAFW3vAkUm_7An5IWXslzqQci7Y1rT_9C0&language=nl&libraries=geometry,places', false);
            
            wp_enqueue_script('los_admin_js');
            wp_enqueue_style('los_admin_css');
            wp_enqueue_style('google-api');
        };
*/
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
