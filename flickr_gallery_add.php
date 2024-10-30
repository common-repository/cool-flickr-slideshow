<?php
/**
 * Section to add gallery and upload videos
 * @author Praveen Rajan
 */
?>
<?php
if($_POST['flickr-gallery_add_hidden'] == 'Y') { 
	  global $wpdb;
          $galleryname = $_POST['fg_add_gallery_name'];
          $gallery_desc = $_POST['fg_add_gallery_descr'];
	  $result = $wpdb->get_var("SELECT name FROM " . $wpdb->prefix . "flickr_gallery WHERE name = '$galleryname' ");
		
		if ($result) {
			if ($output) 
				CvgCore::show_video_error( _n( 'Gallery', 'Galleries', 1 ) .' <strong>\'' . $galleryname . '\'</strong> '.__('already exists'));
			return false;			
		} else { 
			$result = $wpdb->query( $wpdb->prepare("INSERT INTO " . $wpdb->prefix . "cvg_gallery (name, path, title, author, galdesc) VALUES (%s, %s, %s, %s, %s)", $galleryname, $video_path, $gallerytitle , $user_ID, $gallery_desc) );
			if ($result) {
				$message  = __("Gallery '$galleryname' successfully created.<br/>");
				if ($output)
					CvgCore::show_video_message($message); 
			}
}
?>
<?php
$title = __('Add Gallery');
?>
<form name="flickr-gallery_add_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
<input type="hidden" name="flickr-gallery_add_hidden" value="Y"
<div class="postbox-container" style="width:69%; margin-right:1%">
  <div id="poststuff">
<h3><?php echo $title; ?></h3>
     <div class="postbox" style='box-shadow:0 0 2px'>
          <table class='form-table'>
            <tr valign='top'>
                <th scope='row'>Gallery Name</th>
                <td><input maxlength='30' type='text' id='fg_add_gallery_name' name='fg_add_gallery_name' onblur='verifyBlank()' value='' /><font size='3' color='red'>*</font></td>
            </tr>
           <tr valign='top'>
               <th scope='row'>Gallery Description</th>
               <td><input maxlength='100' size='70%' type='text' id='fg_add_gallery_descr' name='fg_add_gallery_descr'" value="" /></td>
          </tr>
        </table>
<p class="submit">
<input type="submit" name="Submit" value="<?php _e('Add Gallery', 'flickr-gallery' ) ?>" />
</p>
   </div></div>
</form>
