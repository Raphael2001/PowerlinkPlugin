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

	// Register Scripts
	wp_register_script( 'sctipt_admin', plugins_url( 'js/app.js', __FILE__ ), array( 'jquery' ), true );

	// Enqueue Scripts
	wp_enqueue_script( 'sctipt_admin' );




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

	// Enqueue Styles
	wp_enqueue_style( 'pl_styles' );

}
add_action( 'admin_enqueue_scripts', 'pl_styles' );

