<?php
/**
 * @package groundup
 */
?>
<?php
// Unclutter the <head>
remove_action( 'wp_head', 'feed_links', 2 );
remove_action( 'wp_head', 'feed_links_extra', 3 );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 );
remove_action( 'wp_head', '_admin_bar_bump_cb' );

// Enable compression in .htaccess
if ( !function_exists( 'groundup_compression' ) ) {
	function groundup_compression( $rewrites ) {
		global $wp_rewrite;
		$htaccess_file = get_home_path() . '.htaccess';
		if ((!file_exists($htaccess_file) && is_writable(get_home_path()) && $wp_rewrite->using_mod_rewrite_permalinks()) || is_writable($htaccess_file)) {
			if ( got_mod_rewrite() ) {
				$gzip = "<IfModule mod_headers.c>
	# Make sure proxies don't deliver the wrong content
	Header append Vary User-Agent env=!dont-vary
	</IfModule>
	<IfModule mod_deflate.c>
	# Insert filters
	AddOutputFilterByType DEFLATE text/plain
	AddOutputFilterByType DEFLATE text/html
	AddOutputFilterByType DEFLATE text/xml
	AddOutputFilterByType DEFLATE text/css
	AddOutputFilterByType DEFLATE text/javascript
	AddOutputFilterByType DEFLATE application/xml
	AddOutputFilterByType DEFLATE application/xhtmlxml
	AddOutputFilterByType DEFLATE application/rssxml
	AddOutputFilterByType DEFLATE application/javascript
	AddOutputFilterByType DEFLATE application/x-javascript
	AddOutputFilterByType DEFLATE application/json
	AddOutputFilterByType DEFLATE application/x-json
	AddOutputFilterByType DEFLATE application/x-httpd-php
	AddOutputFilterByType DEFLATE application/x-httpd-fastphp
	AddOutputFilterByType DEFLATE image/svgxml
	# Drop problematic browsers
	BrowserMatch ^Mozilla/4 gzip-only-text/html
	BrowserMatch ^Mozilla/4\.0[678] no-gzip
	# IE5.x and IE6 get no gzip, but 7 should
	BrowserMatch \bMSIE\s[789] !no-gzip !gzip-only-text/html
	# IE 6.0 after SP2 has no gzip bugs
	BrowserMatch \bMSIE.SV !no-gzip
	# Opera occasionally pretends to be IE with Mozilla/4.0
	BrowserMatch \bOpera !no-gzip
	</IfModule>";
				
				$gzip = explode( "\n", $gzip );
				return insert_with_markers( $htaccess_file, 'gzip', $gzip );
			}
		}
		return $rewrites;
	}
}
add_action( 'generate_rewrite_rules', 'groundup_compression');

// Add a few more url rewrites
if ( !function_exists( 'groundup_add_rewrites' ) ) {
	function groundup_add_rewrites( $rewrites ) {
		global $wp_rewrite;
		
		$groundup_new_non_wp_rules = array(
			'login'         =>   'wp-login.php', // Removed in favor of a custom login page
			'logout'        =>   'wp-login.php?action=logout', // No longer works without nonce
			'admin/(.*)'    =>   'wp-admin/$1',
			'register'      =>   'wp-login.php?action=register'
		);
		$wp_rewrite->non_wp_rules = array_merge($wp_rewrite->non_wp_rules, $groundup_new_non_wp_rules);
		
		return $rewrites;
		
	}
}
add_action( 'generate_rewrite_rules', 'groundup_add_rewrites' );

// Change robots.txt
if ( !function_exists( 'groundup_robots_txt' ) ) {
	function groundup_robots_txt( $output, $public ) {
		$robots_txt = 'Disallow: /wp
		Disallow: /wp-content/plugins
		Disallow: /wp-content/cache
		Disallow: /wp-content/themes
		Disallow: /wp-includes/js
		Disallow: /feed/
		Disallow: /trackback/
		Disallow: /rss/
		Disallow: /comments/feed/
		Disallow: /tag
		Disallow: /author
		Disallow: /wget/
		Disallow: /httpd/
		Disallow: /category/*/*
		Disallow: */trackback
		Disallow: /*?*
		Disallow: /*?
		Disallow: /*~*
		Disallow: /*~
		
		Sitemap: ' . get_home_url() . '/sitemap.xml';
		$output .= $robots_txt;
		return $output;
	}
}
add_filter( 'robots_txt', 'groundup_robots_txt', 10, 2 );

// Add a class to the body_class to check whether the current page has a sidebar or not
if ( !function_exists( 'groundup_body_class' ) ) {
	function groundup_body_class( $classes ) {
		$classes[] = groundup_get_sidebar() ? 'with-sidebar' : 'no-sidebar';
		return $classes;
	}
}
add_filter( 'body_class', 'groundup_body_class' );

// remove width and height tags from images
if ( !function_exists( 'groundup_clean_img' ) ) {
	function groundup_clean_img( $html ) {
		$html = preg_replace( '/(width|height)=\"\d*\"\s/', "", $html );
		return $html;
	}
}
add_filter( 'post_thumbnail_html', 'groundup_clean_img', 10 );
add_filter( 'image_send_to_editor', 'groundup_clean_img', 10 );
add_filter( 'the_content', 'groundup_clean_img', 10 ); 

// remove "Private: " and "Protected: " from titles
if ( !function_exists( 'groundup_title_format' ) ) {
	function groundup_title_format() {
		return __( '%s', 'groundup' );
	}
}
add_filter( 'protected_title_format', 'groundup_title_format' );
add_filter( 'private_title_format', 'groundup_title_format' );


// Remove shortcodes from the_excerpt, but keep the content of that shortcode
if ( !function_exists( 'groundup_excerpt_shortcodes' ) ) {
	function groundup_excerpt_shortcodes( $excerpt = '' ) {
		$raw_excerpt = $excerpt;
		if ( '' == $excerpt ) {
			$excerpt = get_the_content();
			$excerpt = preg_replace( "~(?:\[/?)[^/\]]+/?\]~s", '', $excerpt );  # strip shortcodes, keep shortcode content		
			$excerpt_length = apply_filters( 'excerpt_length', 55 );
			$excerpt_more = apply_filters( 'excerpt_more', ' ' . '[...]' );
			$excerpt = wp_trim_words( $excerpt, $excerpt_length, $excerpt_more );
		}
		return apply_filters( 'wp_trim_excerpt', $excerpt, $raw_excerpt );
	}
}
remove_filter( 'get_the_excerpt', 'wp_trim_excerpt' );
add_filter( 'get_the_excerpt', 'groundup_excerpt_shortcodes' );

// After logging in, redirect admins to the admin, and everyone else to the page they were previously viewing, or the main homepage
if ( !function_exists( 'groundup_login_redirect' ) ) {
	function groundup_login_redirect( $redirect, $request, $user ) {
		global $user;
		if ( isset ( $_REQUEST['redirect_to'] ) ) {
			// check redirect value first
			$redirect = filter_var( $_REQUEST['redirect_to'], FILTER_SANITIZE_URL );
		} elseif ( isset($user->roles) && is_array($user->roles) ) {
			// check if user is an admin next
			$redirect = in_array('administrator', $user->roles) ? admin_url() : home_url();
		}
		return $redirect;
	}
}
add_filter( 'login_redirect', 'groundup_login_redirect', 10, 3 );

// Change the login logo link to the homepage instead of WordPress.org
if ( !function_exists( 'groundup_login_url' ) ) {
	function groundup_login_url( ) {
		return home_url();
	}
}
add_filter( 'login_headerurl', 'groundup_login_url' );

// Change the alt text on the login logo
if ( !function_exists( 'groundup_login_title' ) ) {
	function groundup_login_title() {
		return get_option( 'blogname' );
	}
}
add_filter( 'login_headertitle', 'groundup_login_title' );

// Disable login errors
add_filter( 'login_errors', create_function( '$a', 'return null;' ) );

// Redirect failed login attempts to the same page instead of /wp-login
// Note: This only works for failed logins; empty logins use groundup_empty_login_redirect
if ( !function_exists( 'groundup_failed_login_redirect' ) ) {
	function groundup_failed_login_redirect( $username ) {
		if ( isset( $_SERVER['HTTP_REFERER'] ) ) {
			$referrer = $_SERVER['HTTP_REFERER'];
			// if there's a valid referrer, and it's not the default log-in screen
			if ( !empty( $referrer ) && !strstr( $referrer,'wp-login' ) && !strstr($referrer,'wp-admin' ) ) {
				if ( !strstr( $referrer, '?login=failed' ) ) {
					 // Append some information (login=failed) to the URL for the theme to use
					wp_redirect( $referrer . '?login=failed' ); 
				}else {
					wp_redirect( $referrer );
				}
				exit;
			}
		}
	}
}
add_action( 'wp_login_failed', 'groundup_failed_login_redirect' );

// Redirect empty (failed) login attempts to the same page instead of /wp-login
// Note: This only works for empty logins; normal failed logins use groundup_failed_login_redirect
if ( !function_exists( 'groundup_empty_login_redirect' ) ) {
	function groundup_empty_login_redirect( $user, $username, $password ) {
		if ( $username == '' || $password == '' ) {
			if ( isset($_SERVER['HTTP_REFERER'] ) ) {
				$referrer = $_SERVER['HTTP_REFERER'];
				// if there's a valid referrer, and it's not the default log-in screen and there is no username
				if ( !empty( $referrer ) && strstr( $referrer, 'logout' ) ) {
					wp_redirect( home_url() );
					exit;
				}elseif ( !empty( $referrer ) && !strstr( $referrer, 'wp-login' ) && !strstr( $referrer,'wp-admin' ) ) {
					// Append some information (login=failed) to the URL for the theme to use
					wp_redirect( $referrer . '?login=empty' ); 
					exit;
				}
			}
		}
	}
}
add_filter( 'authenticate', 'groundup_empty_login_redirect', 1, 3 );

// Redirects search results from /?s=query to /search/query/, converts %20 to +
if ( !function_exists( 'groundup_search_url_rewrite' ) ) {
	function groundup_search_url_rewrite() {
		global $wp_rewrite;
		
		if ( !isset( $wp_rewrite ) || !is_object( $wp_rewrite ) || !$wp_rewrite->using_permalinks() ) {
			return;
		}
		
		$search_base = $wp_rewrite->search_base;
		
		$query_vars = $_GET;
		unset( $query_vars['s'] );
		$query_string = http_build_query( $query_vars );
		$query_string = str_replace( '%5B', '[', $query_string );
		$query_string = str_replace( '%5D', ']', $query_string );
		
		if ( is_search() && !is_admin() && strpos($_SERVER['REQUEST_URI'], "/{$search_base}/") === false ) {
			wp_redirect( home_url( "/{$search_base}/" . urlencode(get_query_var('s') ) ) . '?' . $query_string );
			exit();
		}
	}
}
add_action( 'template_redirect', 'groundup_search_url_rewrite' );

// Allows tax_queries to be searched using $_GET variables
// Just add <input type="hidden" name="tax_query[taxonomy_name]" value="tax_term" /> to the searchfrom
if ( !function_exists( 'groundup_tax_query_search' ) ) {
	function groundup_tax_query_search( $query ) {
		if ( !is_admin() && $query->is_main_query() && $query->is_search() ) {
			if ( isset( $_GET['tax_query'] ) ) {
				$tax_query = array();
				$tax_query['relation'] = 'OR';
				foreach ( $_GET['tax_query'] as $tax => $term ) {
					array_push( $tax_query, array(
						'taxonomy' => $tax,
						'field' => 'slug',
						'terms' => $term,
					));
				}
				$query->set( 'tax_query', $tax_query );
			}
		}
		return $query;
	}
}
add_action( 'pre_get_posts', 'groundup_tax_query_search' );

// change default search results to order by post_type && not paginate
if ( !function_exists( 'groundup_search_query_args' ) ) {
	function groundup_search_query_args($query) {
		if ( !is_admin() && $query->is_main_query() &&$query->is_search() ) {
			$query->set( 'orderby', 'type' );
			$query->set( 'posts_per_page', -1 );
			$query->set( 'nopaging', true );
		}
		return $query;
	}
}
add_filter( 'pre_get_posts', 'groundup_search_query_args' );

// remove inline style for recent comments widget
if ( !function_exists( 'groundup_remove_recent_comments_style' ) ) {
	function groundup_remove_recent_comments_style() {
		global $wp_widget_factory;
		remove_action( 'wp_head', array( $wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style' ) );
	}
}
add_action( 'widgets_init', 'groundup_remove_recent_comments_style' );

// Use the custom searchform located at /templates/searchform.php
if ( !function_exists( 'groundup_get_searchform' ) ) {
	function groundup_get_searchform( $echo = false ) {
		ob_start();
		get_template_part( 'templates/searchform' );
		
		if ( $echo ) {
			echo ob_get_clean();
		} else {
			return ob_get_clean();
		}
	}
}
add_filter( 'get_searchform', 'groundup_get_searchform' );

// Add items to nav menus
// Adds an "edit" link for admins
if ( !function_exists( 'groundup_nav_menu_items' ) ) {
	function groundup_nav_menu_items( $items, $args ) {
		// Get menus 
		$menu_locations = get_nav_menu_locations();
		$menu_object = get_term( $menu_locations[ $args->theme_location ], 'nav_menu' );
		
		// Only add items if menu is not empty
		if ( $menu_object->count != 0 ) {
			// Add edit link
			if ( current_user_can( 'manage_options' ) ) {
				$items .= '<li class="edit"><a href="' . admin_url( 'nav-menus.php' ) . '?action=edit&menu=' . $menu_object->term_id . '">Edit Menu</a></li>';
			}
		}
		return $items;
	}
}
add_filter( 'wp_nav_menu_items', 'groundup_nav_menu_items', 10, 2 );

// clean up menu item ids to be blank
if ( !function_exists( 'groundup_nav_menu_item_id' ) ) {
	function groundup_nav_menu_item_id( ) {
		return null;
	}
} 
add_filter( 'nav_menu_item_id', 'groundup_nav_menu_item_id' );

// clean up menu item classes to only include the menu-slug
if ( !function_exists( 'groundup_nav_menu_css_class' ) ) {
	function groundup_nav_menu_css_class( $classes, $item ) {
		// replace `current-menu-item`, `current_page_item`... with just `current`
		$classes = preg_replace( '/(current(-menu-|[-_]page[-_])(item|parent|ancestor))/', 'current', $classes );
		
		// remove default WP classes like `menu-item-type-post_type`, `menu-item-object-paage`...
		$classes = preg_replace( '/^((menu|page)[-_\w+]+)+/', '', $classes );
		
		// Add menu slug
		$slug = sanitize_title( $item->title );
		$classes[] = 'menu-' . $slug;
		
		foreach ( $classes as $class ) {
			$class = trim( $class );
		}
		
		// remove duplicates and empty values
		$classes = array_unique( $classes );
		// TODO: $classes = 
		
		return $classes;
	}
} 
add_filter( 'nav_menu_css_class', 'groundup_nav_menu_css_class', 10, 2 );

// Create a sitemap.xml every time a post is saved
if ( !function_exists( 'groundup_create_sitemap' ) ) {
	function groundup_create_sitemap( $post_id ) {
		// Don't create sitemap during autosaves
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
			return $post_id;
		} else {
			$output = '<?xml version="1.0" encoding="UTF-8"?>';
			$output .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
			
			$post_types = get_post_types();
			$posts = get_posts( array (
				'numberposts' => -1,
				'orderby' => 'modified',
				'post_type' => $post_types,
				'order' => 'DESC',
			) );
			
			foreach ( $posts as $post ) {
				setup_postdata( $post );
				
				$postdate = explode( ' ', $post->post_modified );
				$output .= '
	<url>
		<loc>'. get_permalink($post->ID) .'</loc>
		<lastmod>'. $postdate[0] .'</lastmod>
		<changefreq>monthly</changefreq>
	</url>';
			}
			$output .= '</urlset>';
			
			// Write output to sitemap.xml
			$url = wp_nonce_url( 'post.php?action=edit', 'groundup-sitemap' );
			if ( false === ( $creds = request_filesystem_credentials( $url , '', false, false, null ) ) ) {
				return $post_id; // stop processing here
			}
			if ( ! WP_Filesystem( $creds ) ) {
				request_filesystem_credentials($url, '', true, false, null);
				return $post_id;
			}
			// write to file
			global $wp_filesystem;
			if ( ! $wp_filesystem->put_contents( trailingslashit( get_home_path() ) . 'sitemap.xml', $output, FS_CHMOD_FILE ) ) {
				add_action ('admin_notices', create_function( '', "echo '<div class=\"error\"><p>There is a problem saving the sitemap.xml file</p></div>';" ) );
			}
		}
		return $post_id;
	}
}
add_action( 'save_post', 'groundup_create_sitemap' );
