<?php
/**
 * Plugin Name: Gapi-posts
 * Description: This Plugin will get the most popular posts of your blog through Google Analytic API.
 * Version: 1.0
 * Author: Rehan Raees
 * Author URI: #
 * License: later
 */
 
 
global $wpdb, $wp_version;
define("WP_GAPI_CONFIG_TABLE", $wpdb->prefix . "gapi_configuration");

if ( ! defined( 'WP_gapi_BASENAME' ) )
	define( 'WP_gapi_BASENAME', plugin_basename( __FILE__ ) );
	
if ( ! defined( 'WP_gapi_PLUGIN_NAME' ) )
	define( 'WP_gapi_PLUGIN_NAME', trim( dirname( WP_gapi_BASENAME ), '/' ) );
	
if ( ! defined( 'WP_gapi_PLUGIN_URL' ) )
	define( 'WP_gapi_PLUGIN_URL', WP_PLUGIN_URL . '/' . WP_gapi_PLUGIN_NAME );
	
if ( ! defined( 'WP_gapi_ADMIN_URL' ) )
	define( 'WP_gapi_ADMIN_URL', get_option('siteurl') . '/wp-admin/options-general.php?page=gapi-posts' );
	


function gapi_installation() 
{
	global $wpdb;
	if($wpdb->get_var("show tables like '". WP_GAPI_CONFIG_TABLE . "'") != WP_GAPI_CONFIG_TABLE) 
	{
		$wpdb->query("
			CREATE TABLE IF NOT EXISTS `". WP_GAPI_CONFIG_TABLE . "` (
			  `gapi_id` int(11) NOT NULL auto_increment,
			  `ga_email` VARCHAR( 100 )  DEFAULT NULL,
			  `ga_password` VARCHAR( 100 )  DEFAULT NULL,
			  `ga_profile_id` int(100) DEFAULT NULL,
			  `ga_weekly` tinyint(1) DEFAULT NULL,
			  `ga_monthly` tinyint(1) DEFAULT NULL,
  			  `ga_yearly` tinyint(1) DEFAULT NULL,
  			  `max_records` int(255) DEFAULT NULL,
			  PRIMARY KEY  (`gapi_id`) ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
			");
		
		
		$sSql = "INSERT INTO `". WP_GAPI_CONFIG_TABLE . "` (ga_email, ga_password, ga_profile_id, ga_weekly, ga_monthly, ga_yearly, max_records) VALUES ('', '', Null, '0', '0', '0', '10');";
		$wpdb->query($sSql);
		
	}
	
}
add_option('gapi_title', "Google Analytic Top Postss");

function gapi_admin_options() 
{
	global $wpdb;
	include('pages/gapi-setting.php');
}

// Add link to option
function gapi_add_to_menu() 
{
	add_options_page( __('Gapi Posts','gapi-posts'), __('Gapi Posts','gapi-posts'), 'manage_options', 'gapi-posts', 'gapi_admin_options' );
}


//Plugin Internationalizing 
function gapi_textdomain() 
{
	  load_plugin_textdomain( 'gapi-posts', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}


include('gapi-widget-class.php');

// Register and load the widget
function gapi_load_widget() {
	
	global $wpdb;
	
	register_widget( 'gapi_widget' );
	
	$ugapi = "select * from ".WP_GAPI_CONFIG_TABLE." where gapi_id=1";
	$user_data = $wpdb->get_row($ugapi);
	
	define("GA_EMAIL", $user_data->ga_email);
	define("GA_PASSWORD", base64_decode($user_data->ga_password));
	define("GA_PROFILE_ID", $user_data->ga_profile_id);
	define("GA_WEEKLY", $user_data->ga_weekly);
	define("GA_MONTHLY", $user_data->ga_monthly);
	define("GA_YEARLY", $user_data->ga_yearly);
	define("GA_MAX_RECORD", $user_data->max_records);
}


add_action( 'widgets_init', 'gapi_load_widget' );
register_activation_hook(__FILE__, 'gapi_installation');
add_action('admin_menu', 'gapi_add_to_menu');
?>