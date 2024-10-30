<?php
/**
 * Plugin Name: BuyHTTP Super Polls
 * Plugin URI: http://www.buyhttp.com/wordpress_plugins.html
 * Description: An easy to use poll plugin with widget and shortcode
 * Version: 1.1.0
 * Author: BuyHTTP
 * Author URI: http://www.buyhttp.com
 * License: GPL
 */

defined('ABSPATH') or die("No script kiddies please!");

require_once('bsp.inlinejs.php');
require_once('bsp.pluginpage.php');
require_once('bsp.post.php');
require_once('bsp.settings.php');
require_once('bsp.shortcode.php');
require_once('bsp.widget.php');


register_activation_hook( __FILE__, 'pollInstall' );
add_action( 'widgets_init', 'registerPollWidget' );
add_action('admin_menu','bspPollPages');
add_action( 'admin_print_footer_scripts', 'inlineJs' );
add_action('admin_enqueue_scripts','loadScripts');
add_action('wp_enqueue_scripts','frontScripts');
add_action('wp_footer','frontJs');
add_action( 'wp_ajax_bspVote', 'saveVote' );
add_action( 'wp_ajax_nopriv_bspVote', 'saveVote' );
add_shortcode('bsp_show_results','bsp_show_results');
add_action( 'admin_menu', 'bsp_add_admin_menu' );
add_action( 'admin_init', 'bsp_settings_init' );


function pollInstall()
{
	global $wpdb;
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	
	$table_name = $wpdb->prefix . "bsppolls"; 
	$sql = "CREATE TABLE $table_name (
	  poll_id mediumint(9) NOT NULL AUTO_INCREMENT,
	  question text NOT NULL,
	  options text NULL,
	  UNIQUE KEY poll_id (poll_id)
	);";
	dbDelta( $sql );
	
	$table_name = $wpdb->prefix . "bsppollvotes"; 
	$sql = "CREATE TABLE $table_name (
	  vote_id mediumint(9) NOT NULL AUTO_INCREMENT,
	  poll_id mediumint(9) NOT NULL,
	  option_id mediumint(9) NOT NULL,
	  timestamp timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	  ip varchar(15) NOT NULL,
	  UNIQUE KEY vote_id (vote_id)
	);";
	dbDelta( $sql );
}