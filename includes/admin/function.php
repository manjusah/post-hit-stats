<?php

//Function: Add the view post record as per visit also checks bots option
add_action( 'wp_head', 'process_postviews' );
function process_postviews() { echo "in fn";
	global $user_ID, $post;	
	if( is_int( $post ) ) {		
		$post = get_post( $post );		
	} 
	if( !wp_is_post_revision( $post ) ) {		
		if( is_single()) {
			$id = intval( $post->ID );
			$views_options = get_option( 'hits_options' );					
			$should_count = true;
			
			switch( intval( hit_post_get_view_count($post->ID)) ) {
				case 0:
					$should_count = true;
					break;
				case 1:
					if(empty( $_COOKIE[USER_COOKIE] ) && intval( $user_ID ) === 0) {
						$should_count = true;
					}
					break;
				case 2:
					if( intval( $user_ID ) > 0 ) {
						$should_count = true;
					}
					break;
			}
			
			if( intval( $views_options['exclude_bots'] ) === 0 ) {
				$bots = array
				(
						'Google Bot' => 'googlebot'
						, 'Google Bot' => 'google'
						, 'MSN' => 'msnbot'
						, 'Alex' => 'ia_archiver'
						, 'Lycos' => 'lycos'
						, 'Ask Jeeves' => 'jeeves'
						, 'Altavista' => 'scooter'
						, 'AllTheWeb' => 'fast-webcrawler'
						, 'Inktomi' => 'slurp@inktomi'
						, 'Turnitin.com' => 'turnitinbot'
						, 'Technorati' => 'technorati'
						, 'Yahoo' => 'yahoo'
						, 'Findexa' => 'findexa'
						, 'NextLinks' => 'findlinks'
						, 'Gais' => 'gaisbo'
						, 'WiseNut' => 'zyborg'
						, 'WhoisSource' => 'surveybot'
						, 'Bloglines' => 'bloglines'
						, 'BlogSearch' => 'blogsearch'
						, 'PubSub' => 'pubsub'
						, 'Syndic8' => 'syndic8'
						, 'RadioUserland' => 'userland'
						, 'Gigabot' => 'gigabot'
						, 'Become.com' => 'become.com'
						, 'Baidu' => 'baiduspider'
						, 'so.com' => '360spider'
						, 'Sogou' => 'spider'
						, 'soso.com' => 'sosospider'
						, 'Yandex' => 'yandex'
						
				);
				$useragent = $_SERVER['HTTP_USER_AGENT'];
				foreach ( $bots as $name => $lookfor ) {
					if ( stristr( $useragent, $lookfor ) !== false ) {
						$should_count = false;
						break;
					}
				}
				
			}
			if( $should_count && ( ( isset( $views_options['use_ajax'] ) && intval( $views_options['use_ajax'] ) === 0 ) || ( !defined( 'WP_CACHE' ) || !WP_CACHE ) ) ) {
				
			global $wpdb;
			
				
				$current_user = wp_get_current_user();				
				$user_role = $current_user->roles?$current_user->roles[0]:'guest';
				
			if ( $user_role != 'administrator' ) {
				$table_name = $wpdb->prefix . "hit_track_post";
				$insert = "INSERT INTO " . $table_name . "( post_id, created_at, create_date ) VALUES (" . $post->ID . ",'" . time() . "','" . date('Y-m-d')."')";
				$results = $wpdb->query($wpdb->prepare ($insert) );
				if($results) $msg = "Updated";
				}
			
		   }
		}
	}
}

//Function:Returns viewed post count
function hit_post_get_view_count($post_ID) {
	global $wpdb;
	$hits;
	$table_name = $wpdb->prefix . "hit_track_post";
	$select="SELECT *,count(*) as hit_counts FROM $table_name WHERE post_id=$post_ID group by post_id order by hit_counts desc";	
	$tabledata = $wpdb->get_row($wpdb->prepare($select));

	if(is_object($tabledata)){
	$hits = $tabledata->hit_counts;
	}else{$hits=0;}
	
	return $hits; 
}


//Function: Add hit counts column at Posts page
add_filter('manage_posts_columns', 'hit_post_columns_head');
function hit_post_columns_head($defaults) {
	$defaults['hit_view_count'] = __( 'Hit Counts', 'post-hit' );;
	return $defaults;
}
//Function: Add hit counts content in columns as post viewed counts
add_action('manage_posts_custom_column', 'hit_post_columns_content', 10, 2);
function hit_post_columns_content($column_name, $post_ID) {
	if ($column_name == 'hit_view_count') {
		echo hit_post_get_view_count($post_ID);		
	}
}


//Function: Parse View Options
function views_options_parse($key) {
	return !empty($_POST[$key]) ? sanitize_text_field($_POST[$key]) : null;
}