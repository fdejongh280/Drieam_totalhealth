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
 * @author Elwin van den Eijnden & Floris de Jongh
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




add_action( 'wp_footer', 'functionX' );

    function functionX () {

echo '

<body id="custombody">
<header id="zoekfunctie" role="banner" class="column-xs main-xs-center">

    <form id="locator" class="container column-xs row-md cross-xs-stretch cross-md-end" action="">
        <div class="col">
        <label id="zoekveld" for="locator_text">Zoeken op postcode of woonplaats</label>
        <input type="text" id="searchTextField" name="searchTextField" placeholder="Zoek op postcode of woonplaats" required>
        </div>
    <div class="col-xs-two-thirds col-md-quarter">
    <label id="zoekveld" for="locator_radius">Astand</label>
        <select id="locator_radius" name="locator_radius">
            <option id="afstand" value="25">25 km</option>
            <option id="afstand" value="50">50 km</option>
            <option id="afstand" value="100">100 km</option>
            <option id="afstand" value="125">125 km</option>
            <option id="afstand" value="150">150 km</option>
            <option id="afstand" value="400">Toon alles</option>
        </select>
    </div>
    <button id="zoeken" type="submit">zoeken</button>
    </form>
</header>
    </body>
    
<aside id="map"></aside>
<main id="results"></main>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAFW3vAkUm_7An5IWXslzqQci7Y1rT_9C0&language=nl&libraries=geometry,places"></script>
    <script  src="https://ajax.googleapis.com/ajax/libs/jquery/3.0.0/jquery.min.js"></script>
    <script  src="wp-content/plugins/starter-plugin-master/index.js"></script>
    <link rel="stylesheet" type="text/css" href="wp-content/plugins/starter-plugin-master/style.css">

        ';


	} 


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
