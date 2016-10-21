<?php
class post_hit_stats{
	
	function post_hit_stats(){
		add_action('admin_menu', array(&$this,'post_add_pages'));
		add_action('admin_enqueue_scripts', array(&$this, 'register_admin_scripts'));
		add_action('publish_post', array(&$this,'add_post_hit_fields'));
		add_action('delete_post', array(&$this,'delete_post_hit_fields'));
		add_action('admin_head', array(&$this,'post_hit_jQuery_files'));
	}
	//Function: Enqueue the script files	
	function post_hit_jQuery_files()
	{
		wp_register_script('post-hit-stats-admin-js', POST_HIT_STATS_URL.'assets/js/posthitstats.js', array('jquery'));
		wp_enqueue_script('post-hit-stats-admin-js');
	}

   //Function: Enqueue the style files	
	function register_admin_scripts() {
			
		wp_register_style('post-hit-stats-admin', POST_HIT_STATS_URL.'assets/css/common.css');
		wp_enqueue_style('post-hit-stats-admin');
		wp_register_style('post-hit-stats-admin-tabbed', POST_HIT_STATS_URL.'assets/css/tabbed.min.css');
		wp_enqueue_style('post-hit-stats-admin-tabbed');
		wp_enqueue_script('jquery-ui-datepicker');
		wp_register_style('jquery-ui-css', POST_HIT_STATS_URL. 'assets/css/jquery-ui.css', array(), '1.9.0' );
		wp_enqueue_style( 'jquery-ui-css' );
	}
	//Function: Add the menu on admin dashboard
	public function post_add_pages() {
		
		add_menu_page('Post Hit Stats', 'Post Hit Stats', 'manage_options', 'post-hit-stats', array(&$this,'view_post_count_fn'),POST_HIT_STATS_URL.'assets/images/post_icon.png');
	}
	//Function: Include the admin view panel
	public function view_post_count_fn() {
		ob_start();
		include_once POST_HIT_STATS_PATH.'includes/admin/admin_view.php';
		$out1 = ob_get_contents();
		ob_end_clean();		
		echo $out1;
	}
	
}
$post_hit_stats = new post_hit_stats();