<?php
/****************************************************************
Plugin Name: Cool Flickr Slideshow
Plugin URI: http://creativedev.in
Description: Creates beautiful flickr slideshows from Set,API,User ID or Group.
Version: 0.1
Author: Bhumi Shah
Author URI:http://creativedev.in
****************************************************************/
?>
<?php
//include(ABSPATH.'/wp-content/plugins/flickr-gallery/js.php');
//Activation hook so the DB is created when plugin is activated
register_activation_hook(__FILE__,'flickrgallery_db_create');	
register_deactivation_hook(basename(dirname(__FILE__)).'/'.basename(__FILE__),  'flickrgallery_uninstall');
add_action('admin_menu', 'flickr_gallery_admin_actions');
function flickrgallery_db_create()
{
	global $wpdb;
	if (function_exists('is_multisite') && is_multisite()) {
	// check if it is a network activation - if so, run the activation function for each blog id
		if (isset($_GET['networkwide']) && ($_GET['networkwide'] == 1)) {
			$old_blog = $wpdb->blogid;
			// Get all blog ids
			$blogids = $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM $wpdb->blogs"));
			foreach ($blogids as $blog_id) {
				switch_to_blog($blog_id);
				flickrgallery_activate();
			}
			switch_to_blog($old_blog);
			return;
		}
	}
	flickrgallery_activate();
}
function flickrgallery_activate(){

	global $wpdb;
	$sub_name_types = 'flicker_types';
	$table_types = $wpdb->prefix . $sub_name_types;
	if($wpdb->get_var("SHOW TABLES LIKE '$table_types'") !=  $table_types) {
		$sql_types = "CREATE TABLE " .  $table_types . " (
				`pid` bigint( 20  ) NOT NULL AUTO_INCREMENT  ,
				`ftype` varchar( 255 ) NOT NULL ,
				`user_id` varchar( 255 ) NOT NULL ,
				`group_id` varchar( 255 ) NOT NULL ,
				`set_id` varchar( 255 ) NOT NULL ,
			    `width` varchar( 255 ) NOT NULL ,
			  	`height` varchar( 255 ) NOT NULL ,
			  	`api` varchar( 255 ) NOT NULL ,
				PRIMARY KEY ( `pid` ));";
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			  dbDelta($sql_types);
			}
}
function flickrgallery_uninstall(){
	global $wpdb; 
	if (function_exists('is_multisite') && is_multisite()) {
			$old_blog = $wpdb->blogid;
				// Get all blog ids
			$blogids = $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM $wpdb->blogs"));
			foreach ($blogids as $blog_id) {
				switch_to_blog($blog_id);
				flickrgallery_deactivate();
			}
			switch_to_blog($old_blog);
			return;
		}
	flickrgallery_deactivate();
}
function flickrgallery_deactivate()
{
	global $wpdb; 
	//$sub_name_gallery = 'flicker_gallery';
	$sub_name_types = 'flicker_types';
	//$table_gallery  = $wpdb->prefix . $sub_name_gallery;
	$table_types = $wpdb->prefix . $sub_name_types;
	//$wpdb->query("DROP TABLE $table_gallery");
	$wpdb->query("DROP TABLE $table_types");
	
}
function flickr_gallery_admin_actions()
{
  $plugin_url = plugin_dir_url(__FILE__); 
  // Add sub menu page to the Settings menu.
  //add_options_page("Cool Flickr Gallery", "Cool Flickr Gallery", 'manage_options', "Flickr-Gallery", "flickr_gallery_Admin");
add_menu_page('Flickr Slideshow Overview', __('Cool Flickr Slideshow'), 'manage_options', 'flickr-gallery-overview' ,  'flickr_gallery_overview', $plugin_url.'/images/flickr-icon.png');
	//add_submenu_page( 'flickr-gallery-overview', __('Add Flickr Gallery'), 'Add Gallery', 'manage_options', 'flickr-gallery-add', 'flickr_gallery_add');
	add_submenu_page( 'flickr-gallery-overview', __('Flickr Settings'), 'Flickr Settings', 'manage_options', 'flickr-gallery-settings', 'flickr_gallery_Admin');
	//add_submenu_page( 'flickr-gallery-overview', __('CVG Uninstall'), 'Uninstall Flickr Gallery', 'manage_options', 'flickr-plugin-uninstall', 'uninstall_the_plugin');
}
function flickr_gallery_Admin()
{
 	include('flickr_gallery_admin.php');
}
function flickr_gallery_add()
{
	include('flickr_gallery_add.php');
}
function flickr_gallery($atts,$content=null)
{
  	/*wp_register_style('fgStyle',plugins_url('flickrGallery.css',__FILE__));
	wp_enqueue_style('fgStyle');
	wp_register_script('fjus','jquery.flickr-1.0.js');
	wp_enqueue_script('fjus');
	wp_register_script('fjs','jquery-ui-personalized-1.6rc2.min.js');
	wp_enqueue_script('fjs');	
	wp_register_script('fgjs','jquery.flickrGallery-1.0.2.js');
	wp_enqueue_script('fgjs');*/
        extract( shortcode_atts( array ( 'user_id' => '','set_id' => '','group_id' => '','width'=>'500','height'=>'500'), $atts ) );
  	 if($user_id!=''){
     		 $data = '<iframe align="center" src="http://www.flickr.com/slideShow/index.gne?&user_id='.$user_id.'" frameBorder="0" width="'.$width.'" height="'.$height.'" scrolling="no"></iframe>';
	}
	else if($group_id!=''){
		$data = '<iframe align="center" src="http://www.flickr.com/slideShow/index.gne?group_id='.$group_id.'" frameBorder="0" width="'.$width.'" height="'.$height.'" scrolling="no"></iframe>';
	}else{
		$data = '<iframe align="center" src="http://www.flickr.com/slideShow/index.gne?set_id='.$set_id.'" frameBorder="0" width="'.$width.'" height="'.$height.'" scrolling="no"></iframe>';
	}
      return $data;
}
add_shortcode('flickr-gallery','flickr_gallery');
function flickr_gallery_overview(){ ?>
<div class="wrap">
<?php   
 echo "<center><u><h2>" . __( 'How to Use On Site:') . "</h2></u></center>";
echo "<p class='info'>The Cool Flickr Slideshow plugin used to display flickr photos on site with using 
the shortcode given in setting section.
<br/>With the use of 'shortcodes', this plugin will allow you to quickly and easily incorporate your Flickr photos into your WordPress pages and posts.</p>";
?>
</div>
<?php
}
function uninstall_the_plugin(){

}
?>