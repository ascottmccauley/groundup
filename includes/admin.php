<?php
/**
 * A collection of functions for the admin screens and admin_bar
 * 
 * @package groundup
 */
?>
<?php // Only load if an admin is logged in
if ( current_user_can( 'edit_posts' ) ) {

	// Tell the TinyMCE editor to use a custom stylesheet
	// See http://codex.wordpress.org/Function_Reference/add_editor_style
	add_theme_support( 'editor_style' );
	add_editor_style( 'assets/css/editor-style.css' );
	
	// Prevent WordPress from uploading duplicate files
	// Checks to see if both filenames match and then overwrites only if the filesizes are different
	function groundup_remove_duplicate_attachment( $file ) {
		$upload_dir = wp_upload_dir();
		// replace spaces with dashes just like WordPress does
		$filename = str_replace( ' ', '-', $file['name'] );
		if ( file_exists( $upload_dir['path'] . '/' . $filename ) ) {
			// Compare filesizes
			if ( filesize( $upload_dir['path'] . '/' . $filename ) != $file['size'] ) {
				// query the attachment so that it will be deleted and removed from media library
				$args = array(
					'numberposts' => 1,
					'post_type' => 'attachment',
					'meta_query' => array(
						array(
							'key' => '_wp_attached_file',
							'value' => trim($upload_dir['subdir'] . '/' . $filename, '/')
						)
					)
				);
				$attachment_file = get_posts($args);
				wp_delete_attachment( $attachment_file[0]->ID, true );
			} else {
				// remove filename and replace with an error so the file will not be re-uploaded
				$file = array('name'=>$filename, 'error'=>'Image ' . $filename . ' already exists.');
			} 
		}
		return $file;
	}
	add_filter( 'wp_handle_upload_prefilter', 'groundup_remove_duplicate_attachment' );
	
	// change image suffixes to <filename>-<image_size>
	// TODO: Don't think this will work if multiple sizes share width or height
	// 	Look into finding out which image size it is another way!
	function groundup_image_suffix( $image ) {
		// Split the $image path into directory/extension/name
		$info = pathinfo( $image );
		$dir = $info['dirname'] . '/';
		$ext = '.' . $info['extension'];
		$file_name = wp_basename( $image, "$ext" );
		$image_name = substr( $file_name, 0, strrpos( $file_name, '-' ) );
		
		// Get image information 
		$img = wp_get_image_editor( $image );
		// Get image size, width and height
		$img_size = $img->get_size();
		
		// Get new image suffix by comparing image sizes
		$image_sizes = get_intermediate_image_sizes();
		
		foreach ( $image_sizes as $size ) {
			$sizeInfo = get_image_size_data( $size );
			if ( $img_size['width'] == $sizeInfo['width'] || $img_size['height'] == $sizeInfo['height'] ) {
				// Rename image
				$new_name = $dir . $image_name . '-' . $size . $ext;
				
				// Rename the intermediate size
				$rename_success = rename( $image, $new_name );
				if ( $rename_success ) {
					return $new_name;
				}
			}
		}
		
		// do nothing if not renamed
		return $image;
	}
	add_filter( 'image_make_intermediate_size', 'groundup_image_suffix' );

	// Remove the WordPress welcome panel
	function groundup_remove_welcome_panel() {
		update_user_meta( get_current_user_id(), 'show_welcome_panel', false );
	}
	add_action( 'wp_dashboard_setup', 'groundup_remove_welcome_panel' );
	
	// Remove some of the default WordPress dashboard panels
	function groundup_dashboard_panels() {
		remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'normal' );
		remove_meta_box( 'dashboard_plugins', 'dashboard', 'normal' );
		remove_meta_box( 'dashboard_primary', 'dashboard', 'normal' );
		remove_meta_box( 'dashboard_secondary', 'dashboard', 'normal' );
		remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
		remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'side' );
		remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
		remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' );
		remove_meta_box( 'dashboard_activity', 'dashboard', 'normal' );
	}
	add_action( 'admin_init', 'groundup_dashboard_panels' );
	
	// Remove some of the menus
	function groundup_admin_menus() {
		// Remove inline editing
		define( 'DISALLOW_FILE_EDIT', true );
		//define( 'DISALLOW_FILE_MODS', true );
		
		//remove_menu_page('index.php'); //Dashboard
		//remove_submenu_page( 'index.php', 'index.php' ); //Dashboard
		//remove_submenu_page( 'index.php', 'update-core.php' ); //Updates
		
		//remove_menu_page('edit.php'); //Posts
		//remove_submenu_page( 'edit.php', 'edit.php' ); //Posts
		//remove_submenu_page( 'edit.php', 'post-new.php' ); //Add New
		//remove_submenu_page( 'edit.php', 'edit-tags.php?taxonomy=category' ); //Categories
		//remove_submenu_page( 'edit.php', 'edit-tags.php?taxonomy=post_tag' ); //Post Tags
		
		//remove_menu_page('upload.php'); //Media
		//remove_submenu_page( 'upload.php', 'upload.php' ); //Library
		//remove_submenu_page( 'upload.php', 'media-new.php' ); //Add New
		
		remove_menu_page('link-manager.php'); //Links
		remove_submenu_page( 'link-manager.php', 'link-manager.php' ); //Links
		remove_submenu_page( 'link-manager.php', 'link-add.php' ); //Add New
		remove_submenu_page( 'link-manager.php', 'edit-tags.php?taxonomy=link_category' ); //Link Categories
		
		//remove_menu_page('edit.php?post_type=page'); //Pages
		//remove_submenu_page( 'edit.php?post_type=page', 'edit.php?post_type=page' ); //Pages
		//remove_submenu_page( 'edit.php?post_type=page', 'post-new.php?post_type=page' ); //Add New
		
		//remove_menu_page('edit-comments.php'); //Comments
		
		//remove_menu_page('themes.php'); //Appearance
		remove_submenu_page( 'themes.php', 'themes.php' ); //Themes
		//remove_submenu_page( 'themes.php', 'widgets.php' ); //Widgets
		//remove_submenu_page( 'themes.php', 'nav-menus.php' ); //Menus
		remove_submenu_page( 'themes.php', 'theme-editor.php' ); //Editor
		
		//remove_menu_page('plugins.php'); //Plugins
		//remove_submenu_page( 'plugins.php', 'plugins.php' ); //Plugins
		//remove_submenu_page( 'plugins.php', 'plugin-install.php' ); //Add New
		remove_submenu_page( 'plugins.php', 'plugin-editor.php' ); //Editor
		
		//remove_menu_page('users.php'); //Users
		//remove_submenu_page( 'users.php', 'users.php' ); //Users
		//remove_submenu_page( 'users.php', 'user-new.php' ); //Add New
		//remove_submenu_page( 'users.php', 'profile.php' ); //Your Profile
		
		//remove_menu_page('tools.php'); //Tools
		//remove_submenu_page( 'tools.php', 'tools.php' ); //Tools
		//remove_submenu_page( 'tools.php', 'import.php' ); //Import
		//remove_submenu_page( 'tools.php', 'export.php' ); //Export
		
		//remove_menu_page('options-general.php'); //Settings
		//remove_submenu_page( 'options-general.php', 'options-general.php' ); //General
		//remove_submenu_page( 'options-general.php', 'options-writing.php' ); //Writing
		//remove_submenu_page( 'options-general.php', 'options-reading.php' ); //Reading
		//remove_submenu_page( 'options-general.php', 'options-discussion.php' ); //Discussion
		//remove_submenu_page( 'options-general.php', 'options-media.php' ); //Media
		//remove_submenu_page( 'options-general.php', 'options-privacy.php' ); //Privacy
		//remove_submenu_page( 'options-general.php', 'options-permalink.php' ); //Permalinks
	}
	add_action( 'admin_menu', 'groundup_admin_menus' );

	// Remove some of the post meta boxes
	function groundup_remove_meta_boxes() {
	  // Removes meta boxes from Posts 
	  remove_meta_box( 'postcustom','post','normal' );
	  remove_meta_box( 'trackbacksdiv','post','normal' );
	  //remove_meta_box( 'commentstatusdiv','post','normal' );
	  //remove_meta_box( 'commentsdiv','post','normal' );
	  // remove_meta_box( 'tagsdiv-post_tag','post','normal' );
	  remove_meta_box( 'postexcerpt','post','normal' );
	  // Removes meta boxes from pages 
	  remove_meta_box( 'postcustom','page','normal' );
	  remove_meta_box( 'trackbacksdiv','page','normal' );
	  remove_meta_box( 'commentstatusdiv','page','normal' );
	  remove_meta_box( 'commentsdiv','page','normal' ); 
	}
	add_action( 'admin_init', 'groundup_remove_meta_boxes' );
	
	// Restyle the admin_bar
	function groundup_restyle_admin_bar( $admin_bar ) {
		// Remove WP Logo
		$admin_bar->remove_menu( 'wp-logo' );
		$admin_bar->remove_menu( 'about' );
		$admin_bar->remove_menu( 'wporg' );
		$admin_bar->remove_menu( 'documentation' );
		$admin_bar->remove_menu( 'support-forums' );
		$admin_bar->remove_menu( 'feedback' );
		
		// Change 'Howdy'
		$my_account=$admin_bar->get_node( 'my-account' );
		$newtitle = str_replace( 'Howdy,', '', $my_account->title );
		$admin_bar->add_node( array(
			'id' => 'my-account',
			'title' => $newtitle,
		));		
	}
	add_action( 'admin_bar_menu', 'groundup_restyle_admin_bar', 999 );
	
	// Change admin footer to the theme developr info
	function groundup_admin_footer_text() {
		$themeData = wp_get_theme();
		$developerName =$themeData->display( 'Author', FALSE );
		if ( $developerName != '' ) {
			$developerURI = $themeData->display( 'AuthorURI', FALSE );
			if ( $developerURI ) {
				$developerName = '<a href="' . $themeData . '">' . $themeData . '</a>';
			}
			return '<p>Developed by by: ' . $developerName . '</p>';
		} else {
			return '';
		}
	}
	add_filter( 'admin_footer_text', 'groundup_admin_footer_text' );
}
