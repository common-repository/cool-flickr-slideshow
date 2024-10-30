<script>
 jQuery(document).ready(function(){
	jQuery("#flickr_type").change(onSelectChange);
    });
function onSelectChange(){
	var selected = jQuery("#flickr_type option:selected");		
	var output = "";
	if(selected.val() == 'user'){
		jQuery("#lflickr_uid").show();
		jQuery("#gflickr_uid").hide();
		jQuery("#sflickr_uid").hide();
		jQuery("#gflickr_api").hide();
         }else if(selected.val() == 'group'){
		jQuery("#gflickr_uid").show();
		jQuery("#lflickr_uid").hide();
		jQuery("#sflickr_uid").hide();
		jQuery("#gflickr_api").hide();
         }else if(selected.val() == 'api'){
		jQuery("#gflickr_uid").hide();
		jQuery("#lflickr_uid").show();
		jQuery("#sflickr_uid").hide();
		jQuery("#gflickr_api").show();
	    }else{
        	jQuery("#sflickr_uid").show();
		jQuery("#gflickr_uid").hide();
		jQuery("#lflickr_uid").hide();
		jQuery("#gflickr_api").hide();
	  }	
	}
</script>
<?php   
if($_REQUEST['val']=='delete' && isset($_REQUEST['pid'])){
	global $wpdb;
	$table_prefix = $wpdb->prefix;
	$info = $_SERVER['DOCUMENT_ROOT'];
	$delete_query_result = $wpdb->query($wpdb->prepare("DELETE FROM `".$table_prefix."flickr_types` WHERE pid=".$id));
	wp_redirect($info_blog."/wp-admin/admin.php?page=flick_gallery_admin");
	exit;
}
if($_POST['flickr-gallery_hidden'] == 'Y' && isset($_POST['Submit'])) {  
		global $wpdb; 
		$flickr_type = $_POST['flickr_type'];
		$flickr_uid = $_POST['flickr_uid'];
		$flickr_groupid = $_POST['flickr_groupid'];
		$flickr_set = $_POST['flickr_set'];
		$flickr_api = $_POST['flickr_api'];
		if($_POST['flickr_width']==''){
			$flickr_width='400';
		}else{
		$flickr_width = $_POST['flickr_width'];
		}
		if($_POST['flickr_height']==''){
			$flickr_height='400';
		}else{
		$flickr_height = $_POST['flickr_height'];
		}
		if($flickr_uid!='' && $flickr_api ==''){ 
			$result = $wpdb->get_results("SELECT user_id FROM ". $wpdb->prefix."flicker_types WHERE user_id = $flickr_uid"); 
		if ($result) {
			if ($output) 
				$fg_Message .= _e(' <strong>\'' . $flickr_uid . '\'</strong> '.__('already exists'));		
		} else {  
			$tablename= $wpdb->prefix.'flicker_types';
			 $query = "INSERT INTO $tablename (ftype, user_id, width,height) VALUES ('user', '$flickr_uid', $flickr_width, $flickr_height)";
			$results =$wpdb->query($query);
			
			if ($results) {
				$fg_Message  = __("'$flickr_uid ' successfully added.<br/>");
			 	}	
			}	
		}else if($flickr_groupid!=''){
			$result = $wpdb->get_results("SELECT group_id FROM ". $wpdb->prefix."flicker_types WHERE group_id = $flickr_groupid");
		if ($result) {
			if ($output) 
				$fg_Message .= _e(' <strong>\'' . $flickr_groupid . '\'</strong> '.__('already exists'));		
		} else { 
			$tablename= $wpdb->prefix.'flicker_types';
			$query = "INSERT INTO $tablename (ftype, group_id, width,height) VALUES ('user', '$flickr_groupid', $flickr_width, $flickr_height)";
			$results =$wpdb->query($query);
			
			if ($results) {
				$message  = __("'$flickr_groupid ' successfully added.<br/>");
			 	}	
			}	
		}
		else if($flickr_set!=''){
			$result = $wpdb->get_results("SELECT set_id FROM ". $wpdb->prefix."flicker_types WHERE set_id = $flickr_set");
			if ($result) {
				if ($output)
					$fg_Message .= _e(' <strong>\'' . $flickr_set . '\'</strong> '.__('already exists'));
			} else {
				$tablename= $wpdb->prefix.'flicker_types';
				$query = "INSERT INTO $tablename (ftype, set_id, width,height) VALUES ('user', '$flickr_set', $flickr_width, $flickr_height)";
				$results =$wpdb->query($query);
				if ($results) {
					$message  = __("'$flickr_set ' successfully added.<br/>");
				}
			}
		}
		else if($flickr_api!=''){ 
		//$url = file_get_contents("http://api.flickr.com/services/rest/?method=flickr.photosets.getList&api_key=".$flickr_api."&user_id=".$flickr_uid."&format=json");
			$params = array(
					'api_key'   => $flickr_api,
					'method'    => 'flickr.photosets.getList',
					'user_id'   => $flickr_uid,
					'format'    => 'php_serial'
			);
			$encoded_params = array();
			foreach ($params as $k => $v){ $encoded_params[] = urlencode($k).'='.urlencode($v); }
			$ch = curl_init();
			$timeout = 5; // set to zero for no timeout
			curl_setopt ($ch, CURLOPT_URL, 'http://api.flickr.com/services/rest/?'.implode('&', $encoded_params));
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
			$file_contents = curl_exec($ch);
			curl_close($ch);
			
			$rsp_obj = unserialize($file_contents);
			if ($rsp_obj['stat'] == 'ok') {
			
				$photo_sets = $rsp_obj["photosets"]['photoset'];
				foreach($photo_sets as $photo_set) { 
					$photo_id= $photo_set['id'];
					$result = $wpdb->get_results("SELECT set_id FROM ". $wpdb->prefix."flicker_types WHERE set_id = $photo_id"); 	
					if ($result) {
						if ($output)
							$fg_Message .= _e(' <strong>\'' . $photo_id . '\'</strong> '.__('already exists'));
					} else { 
						 $tablename= $wpdb->prefix.'flicker_types';
					 	 $query = "INSERT INTO $tablename (ftype, set_id, width,height) VALUES ('API', $photo_id, $flickr_width, $flickr_height)";
						$results =$wpdb->query($wpdb->prepare($query,'API', $photo_id, $flickr_width, $flickr_height));
						if ($results) {
							$message  = __("'$photo_id ' successfully added.<br/>");
						}
					}
				
				}
			}
		}
		else {}
}	

?>

<div class="wrap">
<?php    echo "<h2>" . __( 'Flickr Slideshow Display Options', 'flgl_trdom' ) . "</h2>"; ?>

<form name="flickr-gallery_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
<input type="hidden" name="flickr-gallery_hidden" value="Y">
<?php    echo "<h4>" . __( 'Flickr Slideshow Settings', 'flgl_trdom' ) . "</h4>"; ?>
<p>

<label for="flickr_type"><?php _e('Photos From: '); ?>
<select id="flickr_type" name="flickr_type" onchange="javascript:ChangeFlickrType(this.value);">
       <option selected="" value="">SELECT</option>      
                                          <option value="user">User</option>                                         
                                          <option value="group">Group</option>
                                          <option value="set">Set</option>     
                                          <option value="api">API</option>	                                    
                             
                  </select>
</p>
<div id="flickr_types">
              <p><label for="flickr_uid" style="display:none" id="lflickr_uid">
                    <span id="flickr_type_user" style="width: 75px;float: left;"><?php _e('User ID: '); ?></span>
 			<input id="flickr_uid" name="flickr_uid" type="text" style="width: 190px" value="<?php echo $flickr_uid; ?>" />
		  </label> </p>
 		 <p><label for="flickr_api" style="display:none" id="gflickr_api">
                    <span id="flickr_api" style="width: 75px;float: left;"><?php _e('Flickr API: '); ?></span>
 			<input id="flickr_api" name="flickr_api" type="text" style="width: 190px" value="<?php echo $flickr_api; ?>" />
		  </label> </p>
		 <p><label for="flickr_groupid" style="display:none" id="gflickr_uid">              
                    <span id="flickr_type_group" style="width: 75px;float: left;"><?php _e('Flickr Group ID: '); ?></span>
                    <input id="flickr_groupid" name="flickr_groupid" type="text" style="width: 190px" value="<?php echo $flickr_groupid; ?>" />
		</label>    </p>              
                    
                    <p><label id="sflickr_uid" for="flickr_type_set" style="display:none">
                    <span id="flickr_type_set" style="width: 75px;float: left;"><?php _e('Set ID: '); ?></span>
                    <input id="flickr_set" name="flickr_set" type="text" style="width: 190px" value="<?php echo $flickr_set; ?>" />
                    
                    </label></p>              
                  </div>
                  
                 
<p><span style="width: 75px;float: left;"><?php _e("Width: " ); ?></span><input type="text" name="flickr_width" value="<?php echo $flickr_width; ?>" size="20"></p>
<p><span style="width: 75px;float: left;"><?php _e("Height: " ); ?></span><input type="text" name="flickr_height" value="<?php echo $flickr_height; ?>" size="20"></p>

<p class="submit">
<input type="submit" name="Submit" value="<?php _e('Update Options', 'flickr-gallery' ) ?>" />
</p>
</form>
</div>
<div class="wrap">
<?php 
global $wpdb;
$table =$wpdb->prefix."flicker_types";
$result = $wpdb->get_results("SELECT * FROM $table");



?>
<?php    echo "<h2>" . __( 'Flickr Slideshow Display ', 'flgl_trdom' ) . "</h2>"; ?>
<table id="fsgListing" class="widefat"><thead><tr><th scope="col">ID</th><th scope="col">User ID</th><th class="size" scope="col">Group ID</th>
      <th scope="col">Set ID</th><th scope="col">Width</th>  <th scope="col">Height</th><th scope="col">Shortcode</th><th scope="col">Delete</th></tr></thead><tbody id="the-list">
   <?php foreach($result as $key => $value){ 
  
   		$width= $value->width;
   		$height= $value->height;
   		$user =$value->user_id;
   		$group=$value->group_id;
   		$set=$value->set_id;
   ?>
      <tr>
		    <td><?php echo $value->pid; ?></td>
		    <td><?php echo $value->user_id; ?></td>
		    <td><?php echo $value->group_id; ?></td>
		    <td><?php echo $value->set_id; ?></td>
		    <td><?php echo $value->width; ?></td>
		    <td><?php echo $value->height; ?></td>
		     <td>
		      <?php if($value->ftype=='user'){?>
		     <input type="text" readonly="readonly" value="[flickr-gallery user_id=<?php echo $user; ?> width=<?php echo  $width; ?> height=<?php echo $height; ?>]" />
		      <?php } else if($value->ftype=='group'){?>
		        <input type="text" readonly="readonly" value="[flickr-gallery group_id=<?php echo $group;?> width=<?php echo  $width; ?> height=<?php echo $height; ?>]" />
		      <?php } else if($value->ftype=='set' || $value->ftype=='API'){?>
		        <input type="text" readonly="readonly" value="[flickr-gallery set_id=<?php echo $set;?> width=<?php echo  $width; ?> height=<?php echo $height; ?>]" />
		      <?php } ?>
		     </td>
		     <td><a onclick="return confirm('Are you sure want to delete');" href="<?php echo site_url(); ?>/wp-admin/admin.php?page=flick_gallery_admin&val=delete&pid=<?php echo $value->pid; ?>" class="delete"><?php _e('Delete'); ?></a></td>
		</tr>
  <?php } ?>
</tbody>
</table>
</div>