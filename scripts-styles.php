<?php
/**
 * Security check
 *
 * Prevent direct access to the file.
 *
 * @since 1.2
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}



/**
 * Plugin Scripts
 *
 * Register and Enqueues plugin scripts
 *
 * @since 1.2
 */
function pl_scripts() {

	wp_register_script( 'jquery_script', plugins_url( 'js/jquery.js', __FILE__ ), array( 'jquery' ), true );

	wp_register_script( 'script_admin', plugins_url( 'js/app.js', __FILE__ ), array( 'jquery' ), true );

	wp_register_script( 'multiselect_script', plugins_url( 'js/dropdown.min.js', __FILE__ ), array( 'jquery' ), true );
	
	wp_enqueue_script( 'jquery_script' );

	wp_enqueue_script( 'script_admin' );

	wp_enqueue_script( 'multiselect_script' );




}
add_action( 'admin_enqueue_scripts', 'pl_scripts' );



/**
 * Plugin Styles
 *
 * Register and Enqueues plugin styles
 *
 * @since 1.2
 */
function pl_styles() {

	// Register Styles
	wp_register_style( 'pl_styles', plugins_url( 'style.css', __FILE__ ), true);


	// Register Styles
	wp_register_style( 'multiselect_style', plugins_url( 'jquery.dropdown.min.css', __FILE__ ), true);

	// Enqueue Styles
	wp_enqueue_style( 'pl_styles' );
	wp_enqueue_style( 'multiselect_style' );


}
add_action( 'admin_enqueue_scripts', 'pl_styles' );

