<?php
/**
 * Courses Widget
 *
 * Displays Eduframe course categories in one list.
 */
// Courses_Widget Class
class Alumni_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'Alumni_Widget', // Base ID
			__( 'Eduframe - Alumni', 'eduframe' ), // Name
			array(
				'description' => __( 'Eduframe Alumni widget', 'eduframe' ),
			) // Args
		);
	}

	private function scripts() {

	wp_register_style( 'styleForMap',  plugin_dir_url( __FILE__ ) . '/style.css' );
	wp_enqueue_style('styleForMap');
	wp_register_style( 'styleForAlumniPage',  plugin_dir_url( __FILE__ ) . '/style2.css' );
	wp_enqueue_style('styleForAlumniPage');
	// JS loading ------------------------------------------------------------------
  	// wp_deregister_script('jquery');
  	// wp_register_script('jquery', "https://ajax.googleapis.com/ajax/libs/jquery/3.0.0/jquery.min.js", false, null);
   	wp_enqueue_script('jquery');
	wp_register_script( "babel-polyfill", "https://cdnjs.cloudflare.com/ajax/libs/babel-polyfill/6.26.0/polyfill.min.js");
	wp_enqueue_script('babel-polyfill');
	wp_register_script( "babel", "https://unpkg.com/@babel/standalone@7.0.0-beta.49/babel.min.js");
	wp_enqueue_script('babel');
	wp_register_script( "google", "https://maps.googleapis.com/maps/api/js?key=AIzaSyAFW3vAkUm_7An5IWXslzqQci7Y1rT_9C0&language=nl&libraries=geometry,places");
	wp_enqueue_script('google');
	wp_enqueue_script( "ajax-test", plugin_dir_url( __FILE__ ) . '/index.js', array( 'jquery' ) );
	// make the ajaxurl var available to the above script
	wp_localize_script( 'ajax-test', 'the_ajax_script', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );	
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		$this->scripts();

		// Get widget settings
		// However the widget doesn't have any settings
//		$category           = ( isset( $instance['category'] ) ) ? $instance['category'] : 'all';

		// Get unique identifier
		$uid = isset( $args['widget_id'] ) ? $args['widget_id'] : $this->id;

		echo $args['before_widget'];

		?>
        <body id = "alumni_custombody">
	<header id="alumni_zoekfunctie" role="banner" class="column-xs main-xs-center">
    <form id="alumni_locator" class="container column-xs row-md cross-xs-stretch cross-md-end" action="">
        <div class="col">
        <label id = "alumni_zoekveld" for="locator_text">Zoeken op postcode of woonplaats</label>
        <input type="text" id="alumni_searchTextField" name="searchTextField" placeholder="Zoek op postcode of woonplaats" required>
        </div>
    <div id = "alumni_searchOptionsContainer" class="col-xs-two-thirds col-md-quarter">
	<br />
    <label id = "alumni_zoekveld" for="locator_radius">Astand</label>
        <select id="alumni_locator_radius" name="locator_radius">
            <option id= "alumni_afstand" value="25">25 km</option>
            <option id= "alumni_afstand" value="50">50 km</option>
            <option id= "alumni_afstand" value="100">100 km</option>
            <option id= "alumni_afstand" value="125">125 km</option>
            <option id= "alumni_afstand" value="150">150 km</option>
            <option id= "alumni_afstand" value="400">Toon alles</option>
        </select>
    </div>
    <button id = "alumni_zoeken" type="submit">zoeken</button>
    </form>
</header>
</body>
<aside id="alumni_map"></aside>
<main id="alumni_results"></main>
<div class="alumni_alumnicontainer">
		<div class="alumni_sidebar">
			<div class="alumni_sidebar-top">
				<input type="file" style="display: none;" name="fileToUpload" id="alumni_fileToUpload">
				<img class="alumni_profile-image" src="https://i.stack.imgur.com/l60Hf.png" />
				<div class="alumni_profile-basic">
					<h1 class="alumni_name"></h1>
				</div>
			</div>
			<div class="alumni_profile-info">
				<p class="key">Email:</p>
				<p class="value" id = "email"></p><br>
                <p class="key">Telefoon:</p>
				<p class="value" id = "tel"></p><br>
                <p class="key">Adres:</p>
				<p class="value" id = "address"></p>
				<br />
                <!--<p class="key">Afgeronde cursussen:</p>
				<p class="value" id = "courses"></p><br>-->
			</div>
			<button id = "alumni_goBack" class = "btn">Terug</button>
		</div>
		<div class="alumni_content">
			<div class="alumni_work-experience">
				<h1 class="alumni_heading"> Masseuse</h1>
				<div class="alumni_info">
					<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                    
                    <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?</p>
                    
                    <p>Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt.</p>
				</div>
				<button id = "alumni_editText" style="display: none; ">Text bewerken</button>
				<button id = "alumni_saveChanges" style="display: none;">Bewerking opslaan</button>
				<script type="text/babel" data-presets="es2015,stage-3"></script>
			</div>
		</div>
	</div>

		<?php

		echo $args['after_widget'];

	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public
	function update( $new_instance, $old_instance ) {
		$instance = array();

		return $instance;
	}

	/**
	 * Register widget shortcode.
	 *
	 * Register a shortcode for the widget so it can be used anywhere.
	 *
	 * @param array $attributes Values just sent to be saved.
	 *
	 * @return string Output buffer containing the widget output.
	 */
	public static function shortcode( $attributes ) {

		// Shortcode id necessary to identify table in javascript
		STATIC $i = 1;

		ob_start();
		the_widget( "Alumni_Widget", $attributes, array(
			'widget_id'     => 'alumni-widget-shortcode-' . $i,
			'before_widget' => '<div id="alumni-widget-shortcode-' . $i . '">',
			'after_widget'  => '</div>',
			'before_title'  => '',
			'after_title'   => ''
		) );
		$output = ob_get_contents();
		ob_end_clean();

		// Short code id increment
		$i ++;

		return $output;
	}
}

add_shortcode( 'eduframe_alumni', array( 'Alumni_Widget', 'shortcode' ) );

function wis_wp_theme_script_loader_tag( $tag, $handle, $src ) {
  // Check that this is output of JSX file
  if ( 'ajax-test' == $handle ) {
    $tag = str_replace( "<script type='text/javascript'", "<script type='text/babel'", $tag );
  }

  return $tag;
}
add_filter( 'script_loader_tag', 'wis_wp_theme_script_loader_tag', 10, 3 );
