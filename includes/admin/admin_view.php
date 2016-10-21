<?php 

//For settings Form Processing
if(!empty($_POST['Submit'] )) {
	check_admin_referer( 'post-hit_options' );
	$hits_options = array(
			
			 'exclude_bots'            => intval( views_options_parse('views_exclude_bots') )			
			, 'use_ajax'                => intval( views_options_parse('views_use_ajax') )
			, 'template'                => trim( views_options_parse('views_template_template') )
			, 'most_viewed_template'    => trim( views_options_parse('views_template_most_viewed') )
	);
	
	$update_views_queries = array();
	$update_views_text = array();	
	$update_views_queries[] = update_option( 'hits_options', $hits_options );
	$update_views_text[] = __( 'Post Hit stats Options', 'post-hit' );
	$i = 0;$text;
	foreach( $update_views_queries as $update_views_query ) {
		if( $update_views_query ) {
			$text .= '<p style="color: green;">' . $update_views_text[$i] . ' ' . __( 'Updated', 'post-hit' ) . '</p>';
		}
		$i++;
	}
	if( empty( $text ) ) {
		$text = '<p style="color: red;">' . __( 'No Post Views Option Updated', 'post-hit' ) . '</p>';
	}
}


$hits_options = get_option( 'hits_options' );


?>
<h4 class="wpeka-panel-heading"><?php _e( 'Post Hit Stats', 'post-hit' ); ?></h4>

<div style="line-height: 2.4em;">
<a href="http://club.wpeka.com/" target="_blank">
<img style="width: 96%;" src="<?php echo plugins_url('../../assets/images/promotion.png',__FILE__); ?>">
</a>
</div>

<div class="wrap">
<section id="tabbed">
<input id="t-1" class="radio-input" type="radio" name="tabbed-tabs" checked="checked">
<label class="tabs shadow" for="t-1"><?php _e( 'Settings', 'post-hit' ); ?></label>
<input id="t-2" class="radio-input" type="radio" name="tabbed-tabs" <?php if(isset($_POST['hidden-frm'])) { if($_POST['hidden-frm']=='frm'){ ?>checked="checked"<?php }} ?>>
<label class="tabs shadow " for="t-2"><?php _e( 'Stats', 'post-hit' ); ?></label>

<div class="wrapper">
<!-- Bot setting -->
 
<div class="tab-1">
<form action="" method="post">
<?php wp_nonce_field( 'post-hit_options' ); ?>

<table class="form-table" cellspacing="0" cellpadding="6" border="0" width="100%">
<tr valign="top">
    <td align="left" width="25%"><label class="title-mini"><?php _e( 'Exclude Bot Views:', 'post-hit' ); ?></label></td>
 <td align="left">

 		<select name="views_exclude_bots">       
			  <option value="0" <?php selected( '0', $hits_options['exclude_bots'] ); ?>><?php _e( 'Yes', 'post-hit' ); ?></option>
			 
			  <option value="1" <?php selected( '1', $hits_options['exclude_bots'] ); ?>><?php _e( 'No', 'post-hit' ); ?></option>
		
    </select>
    
   </td>
    
  </tr>						
</table>			
<input type="submit" name="Submit" class="button blue" value="<?php _e('Submit','post-hit') ?>" /> </form>
</div>

<!-- stats -->

<div class="tab-2" id="stats-hit">
<table class="form-table" cellspacing="0" cellpadding="6" border="0" width="100%">
<tr valign="top">
 
<td align="left">
<form action="#stats-hit" method="post">
<p><label for="from"><?php _e( 'From', 'post-hit' ); ?></label>&nbsp;<input type="text" id="from" name="from" />&nbsp;<label for="to"><?php _e( 'to', 'post-hit' ); ?></label>&nbsp;<input type="text" id="to" name="to" />&nbsp;<input type="submit" name ="Submit-date"class="button blue" value="<?php _e('Submit','post-hit') ?>" /></p>
<input type="hidden" name="hidden-frm" value="frm"/>
</form>
</td>
</table>
<?php
//Return counts track post data as per date
global $wpdb;
$table_name = $wpdb->prefix . "hit_track_post";

if(isset($_POST['to']) and isset($_POST['from']) and $_POST['from'] !='' and $_POST['to']!='') {
	$select = "SELECT *,count(*) as counts FROM $table_name WHERE create_date >='".sanitize_text_field($_POST['from'])."' AND create_date<='".sanitize_text_field($_POST['to'])."' group by post_id order by counts desc LIMIT 0,100";
} else {
	$select = "SELECT *,count(*) as counts FROM $table_name WHERE 1 group by post_id order by counts desc LIMIT 0,100";
}
$postdata = $wpdb->get_results($wpdb->prepare($select,null));
?>
<h2 style="font-size:20px"><?php _e( 'View counts ', 'post-hit' ); if(isset($_POST['to']) and isset($_POST['from'])) { echo ' | '. sanitize_text_field($_POST['from']).' - '. sanitize_text_field($_POST['to']); } ?></h2><br>
 <table class="widefat page fixed" cellspacing="0">
	<thead>
	<tr valign="top">
		<th class="manage-column column-title" scope="col" width="50"><?php _e( 'Serial', 'post-hit' ); ?></th>
		<th class="manage-column column-title" scope="col" width="100"><?php _e( 'Post ID', 'post-hit' ); ?></th>
		<th class="manage-column column-title" scope="col" width="500"><?php _e( 'Post Title', 'post-hit' ); ?></th>
		<th class="manage-column column-title" scope="col" width="100"><?php _e( 'Author', 'post-hit' ); ?></th>
		<th class="manage-column column-title" scope="col" width="70"><?php _e( 'Comments', 'post-hit' ); ?></th>
		<th class="manage-column column-title" scope="col" width="50"><?php _e( 'Hits', 'post-hit' ); ?></th>
	</tr>
	</thead>
	<tbody>
	
	<?php 
	if($postdata){
	$i=1; foreach($postdata as $data) { 
	$posts = get_post($data->post_id); 
	$title = $posts->post_title;
	$user_info = get_userdata($posts->post_author);
	?>
	<tr valign="top">
		<td>
			<?php echo $i ?>
		</td>
		<td>
			<a target="_blank" href="<?php echo get_option('siteurl').'/wp-admin/post.php?post='.$data->post_id.'&action=edit' ?>"><?php echo $data->post_id ?></a>
		</td>
		<td>
			<a target="_blank" href="<?php echo get_permalink( $data->post_id ); ?>"><?php echo $title?$title:'(No Title)' ?></a>
		</td>
		<td>
			<?php echo $user_info->user_login ?>
		</td>
		<td>
			<?php echo $posts->comment_count ?>
		</td>
		<td>
			<?php echo $data->counts ?>
		</td>
	</tr>
	<?php $i++; }}else{?> <tr valign="top">
	<td colspan="3" align="center"> No result found </td> </tr><?php }?>
	</tbody>
	
	

	<tfoot>
	<tr valign="top">
		<th class="manage-column column-title" scope="col" width="50"><?php _e( 'Serial', 'post-hit' ); ?></th>
		<th class="manage-column column-title" scope="col" width="100"><?php _e( 'Post ID', 'post-hit' ); ?></th>
		<th class="manage-column column-title" scope="col" width="500"><?php _e( 'Post Title', 'post-hit' ); ?></th>
		<th class="manage-column column-title" scope="col" width="100"><?php _e( 'Author', 'post-hit' ); ?></th>
		<th class="manage-column column-title" scope="col" width="70"><?php _e( 'Comments', 'post-hit' ); ?></th>
		<th class="manage-column column-title" scope="col" width="50"><?php _e( 'Hits', 'post-hit' ); ?></th>
	</tr>

	</tfoot>
 </table>

</div>

</div>
</section>
</div>
