<?php
/**
 * Courses Widget
 *
 * Displays Eduframe course categories in one list.
 */

// Courses_Widget Class
class Agenda_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'Alumni_Widget', // Base ID
			__( 'Eduframe - Alumni', 'eduframe' ), // Name
			array(
				'description' => __( 'Eduframe Alumin widget', 'eduframe' ),
			) // Args
		);
	}

	private function scripts() {

		// Add scripts needed for the widget here

//		wp_enqueue_script(
//			'datatables',
//			"https://cdn.datatables.net/1.10.7/js/jquery.dataTables.min.js",
//			array( 'jquery' ),
//			'1.10.7'
//		);
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

        <p>Here should be the output of the widget</p>

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
	public function shortcode( $attributes ) {

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
