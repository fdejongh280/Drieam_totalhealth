<?php
/**
 * Courses Widget
 *
 * Displays Eduframe course categories in one list.
 */
// Courses_Widget Class
class Alumni_Widget_Inlog extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'Alumni_Widget_Inlog', // Base ID
			__( 'Eduframe - Alumni inlog', 'eduframe' ), // Name
			array(
				'description' => __( 'Eduframe Alumin widget inlog functionaliteit', 'eduframe' ),
			) // Args
		);
	}

	private function scripts() {

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
       		<div>
				<button id = "alumni_logIn">Log in</button>
				<button id = "alumni_goToMyAlumniPage" style="display:none;">Ga naar mijn alumni pagina</button>
				<p id= "alumni_loggedInUserCallback"><?php if(isset($_SESSION['alumni_user'])){ echo "ingelogd als: ".$_SESSION['alumni_user'];}else{echo '';};?></p>
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
		the_widget( "Alumni_Widget_Login", $attributes, array(
			'widget_id'     => 'alumni-widget-login-shortcode-' . $i,
			'before_widget' => '<div id="alumni-widget-login-shortcode-' . $i . '">',
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

add_shortcode( 'eduframe_alumni', array( 'Alumni_Widget_Login', 'shortcode' ) );

