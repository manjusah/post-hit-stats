<?php
/*
Plugin Name: Post Hit Stats
Plugin URI: https://club.wpeka.com/product/post-hits-stats/
Description:This plugin tracks each post hit by user. Admin will be able see a report of all the views in given period of time. Posts list will show Hits count against each of the posts. You can use the most viewed widget area to highlight most viewed posts of your site.
Version: 1.3
Author: WPEka Club
Author URI: URI:http://club.wpeka.com
*/

define('POST_HIT_STATS_URL',plugin_dir_url(__FILE__ ));
define('POST_HIT_STATS_PATH',plugin_dir_path(__FILE__ ));

include_once  POST_HIT_STATS_PATH.'includes/admin/admin.php';
include_once  POST_HIT_STATS_PATH.'includes/admin/admin-widget.php';
include_once  POST_HIT_STATS_PATH.'includes/admin/function.php';

register_activation_hook( __FILE__, 'post_views_activation' );
function post_views_activation( $network_wide ) {
	// Add Options for bots,template
	$option_name = 'hits_options';
	$option = array(			  
			 'exclude_bots'            => 0			
			, 'use_ajax'                => 1
			, 'template'                => __('%VIEW_COUNT%', 'post-hit')
			, 'most_viewed_template'    => '<li><a href="%POST_URL%"  title="%POST_TITLE%"><b>%POST_TITLE%</b></a> (%VIEW_COUNT%) </li>'
	);

	
		update_option( $option_name, $option );
	//Create table for viewed track post
		global $wpdb;
		$table_name = $wpdb->prefix . "hit_track_post";
		if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
		
			$sql2 = "CREATE TABLE `$table_name` (
			`id` bigint(20) NOT NULL auto_increment,
			`post_id` int(11) NOT NULL,
			`created_at` varchar(20) NOT NULL,
			`create_date` varchar(20) default NULL,
			PRIMARY KEY  (`id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0;";
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql2);
		}
}
define('PHS_PLUGIN_DIR_PATH',plugin_dir_path(__FILE__) );
define('PHS_PLUGIN_FILE_PATH',__FILE__);
