<?php
/**
 * Main Widgets File
 *
 * Declares widgets for use on wordpress pages.
 */

// Require the widget files.
require_once( 'alumni.wdgt.php' );

// Register the widgets to Wordpress.
add_action( 'widgets_init', function () {
	register_widget( 'Alumni_Widget' );
} );
