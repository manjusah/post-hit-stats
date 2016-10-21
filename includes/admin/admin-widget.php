<?php 
//Function: Most viewed Post widget Template
if(!function_exists('get_most_viewed')) {
function get_most_viewed($limit = 5, $chars = 0, $display = true) {
	global $wpdb;
	$views_options = get_option('hits_options');	
	$temp = '';
	$output = '';
	
	$table_name = $wpdb->prefix . "hit_track_post";
	if(isset($_POST['to']) and isset($_POST['from'])) {
		$select = "SELECT *,count(*) as counts FROM $table_name WHERE create_date >='".sanitize_text_field($_POST['from'])."' AND create_date<='".sanitize_text_field($_POST['to'])."' group by post_id order by counts desc LIMIT 0,".$limit;
	} else {
		$select = "SELECT *,count(*) as counts FROM $table_name WHERE 1 group by post_id order by counts desc LIMIT 0,".$limit;
	}
	$most_viewed = $wpdb->get_results($wpdb->prepare($select));
	
	if($most_viewed) {
		foreach ($most_viewed as $post) {
			$post_views = intval($post->counts);	
			$posts = get_post($post->post_id);
			$post_title = $posts->post_title;
						
			if($chars > 0) {
				$post_title = snippet_text($post_title, $chars);
			}
			
			$post_excerpt = views_post_excerpt($posts->post_excerpt, $posts->post_content, $posts->post_password, $chars);
			$temp = stripslashes($views_options['most_viewed_template']);
			$temp = str_replace("%VIEW_COUNT%", number_format_i18n( $post_views ), $temp);
			$temp = str_replace("%POST_TITLE%", $post_title, $temp);
			$temp = str_replace("%POST_EXCERPT%", $post_excerpt, $temp);
			$temp = str_replace("%POST_CONTENT%", $posts->post_content, $temp);
			$temp = str_replace("%POST_URL%", get_permalink($posts), $temp);
			$temp = str_replace("%POST_DATE%", get_the_time(get_option('date_format'), $post), $temp);
			$temp = str_replace("%POST_TIME%", get_the_time(get_option('time_format'), $post), $temp);
			$output .= $temp;
		}
	} else {
		$output = '<li>'.__('N/A', 'post-hit').'</li>'."\n";
	}
	if($display) {
		echo $output;
	} else {
		return $output;
	}
}
}
// Function: Process Post Excerpt, For Some Reasons, The Default get_post_excerpt() Does Not Work As Expected
function views_post_excerpt($post_excerpt, $post_content, $post_password, $chars = 100) {
	if(!empty($post_password)) {
		if(!isset($_COOKIE['wp-postpass_'.COOKIEHASH]) || $_COOKIE['wp-postpass_'.COOKIEHASH] != $post_password) {
			return __('There is no excerpt because this is a protected post.', 'wp-postviews');
		}
	}
	if(empty($post_excerpt)) {
		return snippet_text(strip_tags($post_content), $chars);
	} else {
		return $post_excerpt;
	}
}
if(!function_exists('snippet_text')) {
	function snippet_text($text, $length = 0) {
		if (defined('MB_OVERLOAD_STRING')) {
			$text = @html_entity_decode($text, ENT_QUOTES, get_option('blog_charset'));
			if (mb_strlen($text) > $length) {
				return htmlentities(mb_substr($text,0,$length), ENT_COMPAT, get_option('blog_charset')).'...';
			} else {
				return htmlentities($text, ENT_COMPAT, get_option('blog_charset'));
			}
		} else {
			$text = @html_entity_decode($text, ENT_QUOTES, get_option('blog_charset'));
			if (strlen($text) > $length) {
				return htmlentities(substr($text,0,$length), ENT_COMPAT, get_option('blog_charset')).'...';
			} else {
				return htmlentities($text, ENT_COMPAT, get_option('blog_charset'));
			}
		}
	}
}

class WP_Widget_PostHitStats extends WP_Widget {
	// Constructor for add widget
	function __construct() {
		
		parent::__construct(
		
				'post-hit', // Base ID
		
				'Post Hit Stats', // Name
		
				array( 'description' => __('Post hit views statistics','post-hit'),
		
						'name' => __('Post Hit Stats','post-hit') ) // Args
		
		);
	}
	
		

	// Function :For get most viewed post Widget display at frontend
	function widget($args, $instance) {
		$title = apply_filters('widget_title', esc_attr($instance['title']));
		$limit = intval($instance['limit']);
		$chars = intval($instance['chars']);
		
		
		echo $args['before_widget'] . $args['before_title'] . $title . $args['after_title'];
		echo '<ul>'."\n";
					 
		get_most_viewed($limit, $chars);				
			
		echo '</ul>'."\n";
		echo  $args['after_widget'];
	}
				
	// Function : When Widget Control Form Is Posted
	function update($new_instance, $old_instance) {
		if (!isset($new_instance['submit'])) {
			return false;
		}
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['limit'] = intval($new_instance['limit']);
		$instance['chars'] = intval($new_instance['chars']);		
		return $instance;
	}

	// Function: DIsplay Widget Control Form
	function form($instance) {
		$instance = wp_parse_args((array) $instance, array('title' => __('Most Viewed', 'post-hit'), 'limit' => 5, 'chars' => 100));
		$title = esc_attr($instance['title']);
		$limit = intval($instance['limit']);
		$chars = intval($instance['chars']);
		$post_types = get_post_types(array(
			'public' => true
		));
?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'post-hit'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('limit'); ?>"><?php _e('No. Of Post To Show:', 'post-hit'); ?> <input class="widefat" id="<?php echo $this->get_field_id('limit'); ?>" name="<?php echo $this->get_field_name('limit'); ?>" type="text" value="<?php echo $limit; ?>" /></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('chars'); ?>"><?php _e('Maximum Post Title Length :', 'post-hit'); ?> <input class="widefat" id="<?php echo $this->get_field_id('chars'); ?>" name="<?php echo $this->get_field_name('chars'); ?>" type="text" value="<?php echo $chars; ?>" /></label><br />
			<small><?php _e('<strong>0</strong> to disable.', 'post-hit'); ?></small>
		</p>		
		
		<input type="hidden" id="<?php echo $this->get_field_id('submit'); ?>" name="<?php echo $this->get_field_name('submit'); ?>" value="1" />
<?php
	}
}


// Function: Init WP-PostViews Widget
add_action( 'widgets_init', 'widget_views_init' );
function widget_views_init() {
	register_widget( 'WP_Widget_PostHitStats' );
}
