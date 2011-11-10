<?php
/*
Plugin Name: KickoffLabs Subscription Widget
Plugin URI: http://www.kickofflabs.com
Description: A widget for displaying the KickoffLabs subscription form.
Version: 1.0.0
Author: KickoffLabs.com
Author URI: http://www.kickofflabs.com
*/

	/*	register widget	*/
	add_action( 'widgets_init', 'kickofflabs_load_widget' );

	function kickofflabs_load_widget() {
		require_once('KickoffLabs-widget.php');
		register_widget( 'KickoffLabs_Widget' );
	}


?>