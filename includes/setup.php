<?php
/**
 * @package groundup
 */
?>
<?php
// basic settings that run when the theme is switched
if ( !function_exists( 'groundup_activation' ) ) {
	function groundup_activation() {
		// Check to see if activation has already been run
		if ( !get_option( 'groundup_activated' ) == 'activated' ) {
			add_option( 'groundup_activated', 'activated' );
			
			// Remove default site description
			if ( get_bloginfo('description') == 'Just another WordPress site' ) {
				update_option( 'blogdescription','' );
			}
			
			// Allow shortcodes in widgets
			add_filter( 'widget_text', 'shortcode_unautop' );
			add_filter( 'widget_text', 'do_shortcode', 11 );
			
			// Change Uploads folder to /media
			update_option( 'uploads_use_yearmonth_folders', 0 );
			update_option( 'upload_path', 'media' );
			update_option( 'upload_url_path', get_home_url() . '/media' );
			
			// Pretty Permalinks
			update_option( 'category_base', '/site/' );
			update_option( 'permalink_structure', '/%category%/%postname%/' );
			
			// change start of week to Sunday
			update_option( 'start_of_week', 0 );
			
			// Change default 'Uncategorized' to 'General'
			$category = get_term_by( 'id', '1', 'category' );
			if ( $category->name == 'Uncategorized' ) {
				$category->name = 'General';
				$category->slug = strtolower( str_replace( '_', ' ', 'general' ) );
			}	
			wp_update_term( $category->term_id, 'category', array( 'slug' => $category->slug, 'name'=> $category->name ) );
			
			// Disable Smilies
			update_option( 'use_smilies', 0 );
			
			// Set default comment status to closed
			update_option( 'default_comment_status', 'closed' );
			update_option( 'default_ping_status', 'closed' );
			
			// Set Timezone
			$timezone = "America/New_York";
			//$timezone = "America/Chicago";
			//$timezone = "America/Denver";
			//$timezone = "America/Los_Angeles";
			update_option( 'timezone_string', $timezone );
			
			// Clean up widget settings that weren't set at installation to prevent unecessary queries
			add_option( 'widget_pages', array( '_multiwidget' => 1 ) );
			add_option( 'widget_calendar', array( '_multiwidget' => 1 ) );
			add_option( 'widget_tag_cloud', array( '_multiwidget' => 1 ) );
			add_option( 'widget_nav_menu', array( '_multiwidget' => 1 ) );
			
			// Update default media sizes - additional sizes are added through groundup_init
			update_option( 'thumbnail_size_w', 330 );
			update_option( 'thumbnail_size_h', 330 );
			update_option( 'thumbnail_crop', true );
			update_option( 'medium_size_w', 600 );
			update_option( 'medium_size_h', 400 );
			update_option( 'large_size_w', 1200 );
			update_option( 'large_size_h', 800 );
			update_option( 'embed_size_w', 1200 );
			update_option( 'embed_size_h', 800 );
			
			// Add default menus
			groundup_create_nav_menus( array( 'Primary', 'Secondary', 'Mobile', 'Footer' ) );				
		}
	}
}
add_action( 'init', 'groundup_activation' );
add_action( 'admin_init', 'groundup_activation' );

// remove the 'groundup_activated' option so that groundup_activation will be run again.
if ( !function_exists( 'groundup_deactivation' ) ) {
	function groundup_deactivation() {
		do_action( 'groundup_deactivation' );
		delete_option( 'groundup_activated' );
	}
}
add_action( 'switch_theme', 'groundup_deactivation' );
add_action( 'after_switch_theme', 'groundup_deactivation' );

// Basic Settings that must run every page load
if ( !function_exists( 'groundup_setup' ) ) {
	function groundup_setup() {
		
		// Default Comment Status
		update_option( 'default_comment_status', 'closed' );
		update_option( 'default_ping_status', 'closed' );
		
		// Disable Smilies
		update_option( 'use_smilies', 0);
		
		// Add support for featured thumbnails
		// See http://codex.wordpress.org/Post_Thumbnails
		add_theme_support( 'post-thumbnails' );
		
		// Allow WordPress to choose the most appropriate title tag
		// See http://codex.wordpress.org/Title_Tag
		add_theme_support( 'title-tag' );
		
		// Add support for html5 markup
		// See http://codex.wordpress.org/Semantic_Markup
		add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );
		
		// Add support for post formats
		// See http://codex.wordpress.org/Post_Formats
		add_theme_support( 'post-formats', array( 'aside', 'image', 'video', 'quote', 'link' ) );
		
		// allow shortcodes in widgets
		add_filter( 'widget_text', 'do_shortcode' );
		
		// Add default menu locations
		groundup_register_nav_menus( array( 'Primary', 'Secondary', 'Mobile', 'Footer' ) );				
		
		// Additional image sizes
		add_image_size( 'tiny', '60', '60', true );
		add_image_size( 'small', '120', '120', false );	
		
	}
}
add_action( 'init', 'groundup_setup' );
add_image_size( 'tiny', '60', '60', true );
add_image_size( 'small', '120', '120', false );

// Create default menus
// @filters: groundup_menus - an array of all the menus to be created
if ( !function_exists( 'groundup_create_nav_menus' ) ) {
	function groundup_create_nav_menus( $menus ) {
		$menus = apply_filters( 'groundup_menus', $menus );
		
		foreach ( $menus as $menu ) {
			$slug = strtolower( str_replace ( '_', ' ', $menu ) );
			// create menu if it doesn't exist yet
			if ( !wp_get_nav_menu_object( $slug ) ) {
				$menu_ID = wp_create_nav_menu( ucwords( __( $menu, 'groundup' ) ), array( 'slug' => $slug ) );
				
				// Add menu to it's correct location
				$menu_obj = wp_get_nav_menu_object( $slug );
				$locations = get_theme_mod( 'nav_menu_locations' );
				$locations[$slug] = $menu_obj->term_id;
				set_theme_mod( 'nav_menu_locations', $locations );
			}
		}
	}
}

// Create default menu locations
// See http://codex.wordpress.org/Function_Reference/register_nav_menus
// @filters: groundup_menus - an array of all the menu locations to be created
if ( !function_exists( 'groundup_register_nav_menus' ) ) {
	function groundup_register_nav_menus( $menus ) {
		$menus = apply_filters( 'groundup_menu_locations', $menus );
		foreach ( $menus as $menu ) {
			$slug = strtolower( str_replace ( '_', ' ', $menu ) );
			// create location
			register_nav_menus( array(
				$slug => ucwords( __( $menu, 'groundup' ) ),
			) );
		}
	}
}

// setup sidebars / widgets
// @filters: groundup_register_sidebars - an array of all the sidebars to be registered
if ( !function_exists( 'groundup_sidebars' ) ) {
	function groundup_sidebars( $sidebars ) {
		$sidebars = apply_filters( 'groundup_register_sidebars', array( 'Default','Home','Single' ) );
		foreach ( $sidebars as $sidebar ) {
			register_sidebar( array(
				'name'=> $sidebar,
				'id'=> 'sidebar-' . strtolower(str_replace(' ', '-', $sidebar)),
				'before_widget' => '<section class="widget %1$s %2$s">',
				'after_widget' => '</section>',
				'before_title' => '<h4>',
				'after_title' => '</h4>',
			) );
		}
	}
}
add_action( 'widgets_init', 'groundup_sidebars' );

// Callback for a single comment
if ( !function_exists( 'groundup_comment' ) ) {
	function groundup_comment( $comment, $args, $depth ) {
		include( locate_template( 'templates/comment.php' ) ); //Using include(locate_template()) to pass $args, $depth, and $comment vars to template.
	}
}