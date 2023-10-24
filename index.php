<?php
/*
* Plugin Name: PowerLink
* Plugin URI: https://raphaelaboohi.cf
* Description: Plugin for PowerLink.
* Version: 1.0
* Author: Raphael Aboohi
* Author URI: https://raphaelaboohi.cf
*/



/**
 * Security check
 *
 * Prevent direct access to the file.
 *
 * @since 1.0
 */
if (! defined('ABSPATH')) {
    exit;
}
$store_name ="regular";
// $store_name = "homoetreat";

/**
 * Include plugin files
 */
include_once(plugin_dir_path(__FILE__) . 'settings_tab.php');
include_once(plugin_dir_path(__FILE__) . 'scripts-styles.php');
include_once(plugin_dir_path(__FILE__) . 'create.php');
// die('<hr /><pre>' . print_r(array($store_name,  '<br />Here: ' . __LINE__ . ' at ' . __FILE__), true) . '</pre><hr />');
