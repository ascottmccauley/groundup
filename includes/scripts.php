<?php
/**
 * Functions that load all of the necessary cookies and scripts
 *
 * @package groundup
 */
?>
<?php
// Compare and reset the value of `$_COOKIE['cached']` with the filedate for `main.css`
if ( !function_exists( 'groundup_is_cached' ) ) {
	function groundup_is_cached() {
		global $cached;
		
		if ( ! is_admin() && ! is_login() && ! is_register() ) {
			$main_css_file = trailingslashit( get_stylesheet_directory() ) . 'assets/css/main.css';
			if ( file_exists( $main_css_file ) ) {
				$version = filemtime( $main_css_file );
				if ( empty( $_COOKIE[ 'cached' ] ) || $_COOKIE[ 'cached' ] != $version ) {
					$url = parse_url( get_site_url() );
					$domain = $url['host'];
					setcookie( 'cached', $version, time()+3600*24*100, '/', COOKIE_DOMAIN, false );
					$cached = false;
				}	else {
					$cached = true;
				}
			}
		}
	}
}
add_action( 'init', 'groundup_is_cached' );

// Load all scripts and styles for the theme
if ( !function_exists( 'groundup_enqueue_scripts' ) ) {
	function groundup_enqueue_scripts() {
		// variable set on init from `groundup_defer_css` to determine whether or not assets have been cached
		global $cached;
		
		// Replace WP jquery with google CDN and include a local fallback
		wp_deregister_script('jquery');
		wp_register_script( 'jquery', '//ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js', array(), '2.1.3', false );
		
		// enqueue main js
		$main_js_file = get_stylesheet_directory() . '/assets/js/main.js';
		if ( file_exists( $main_js_file ) ) {
			$main_js_ver = filemtime( $main_js_file );
			wp_enqueue_script( 'groundup-main', get_stylesheet_directory_uri() . '/assets/js/main.js', array('jquery'), $main_js_ver, false );
			// add fallback local jquery after the CDN
			add_filter('script_loader_src', 'groundup_local_jquery_fallback', 10, 2);
		}
		
		// add main style
		$main_css_file = get_stylesheet_directory() . '/assets/css/main.css';
		if ( file_exists( $main_css_file ) ) {
			$main_css_ver = filemtime( $main_css_file );
			wp_enqueue_style( 'groundup-main', get_stylesheet_directory_uri() . '/assets/css/main.css', NULL, $main_css_ver );
		}
		
		// add inline style and defer main style
		$inline_css_file = trailingslashit( get_stylesheet_directory() ) . 'assets/css/inline.css';
		if ( file_exists( $inline_css_file ) && $cached != true ) {
			// add inline style to head
			echo '<style>' . file_get_contents( $inline_css_file ) . '</style>';
		
			// defer main style
			if ( file_exists( $main_css_file ) ) {
				wp_dequeue_style( 'groundup-main' );
				$main_css_ver = filemtime( $main_css_file );
				if ( $cached != true ) {
					// add js to defer loading main.css
					echo '<script>
						var cb = function() {
							var l = document.createElement("link"); l.rel = "stylesheet";
							l.href = "' . get_stylesheet_directory_uri() . '/assets/css/main.css?ver=' . $main_css_ver . '";
							var h = document.getElementsByTagName("head")[0]; h.appendChild(l, h);
						};
						var raf = requestAnimationFrame || mozRequestAnimationFrame ||
						webkitRequestAnimationFrame || msRequestAnimationFrame;
						if (raf) raf(cb);
						else window.addEventListener("load", cb);
					</script>';
				}
			}
		}
		
		// add style specific to the admin-bar
		if ( is_admin_bar_showing() ) {
			wp_enqueue_style( 'groundup-admin-bar', get_stylesheet_directory_uri() . '/assets/css/admin-bar.css', NULL, '' );
		}
	}
}
add_action( 'wp_enqueue_scripts', 'groundup_enqueue_scripts' );

// enqueue admin.css for backend pages
if ( !function_exists( 'groundup_admin_scripts' ) ) {
	function groundup_admin_scripts() {
		wp_enqueue_style( 'groundup-admin', get_stylesheet_directory_uri() . '/assets/css/admin.css', NULL, '' );
	}
}
add_action( 'admin_enqueue_scripts', 'groundup_admin_scripts' );

// enqueue login.css for login and registration pages
// Temporary fix for having stylesheet print in footer instead of head.
if ( ! has_action( 'login_enqueue_scripts', 'wp_print_styles' ) ) {
	add_action( 'login_enqueue_scripts', 'wp_print_styles', 11 );
}
if ( !function_exists( 'groundup_login_scripts' ) ) {
	function groundup_login_scripts() {
		$login_css_file = get_stylesheet_directory() . '/assets/css/login.css';
		if ( file_exists( $login_css_file ) ) {
			$login_css_ver = filemtime( $login_css_file );
			wp_enqueue_style( 'groundup-login', get_stylesheet_directory_uri() . '/assets/css/login.css', NULL, $login_css_ver );
		}
	}
}
add_action( 'login_enqueue_scripts', 'groundup_login_scripts' );

// local fallback adds script to head after enqueueing jquery CDN
if ( !function_exists( 'groundup_local_jquery_fallback' ) ) {
	function groundup_local_jquery_fallback( $src, $handle = null ) {
		static $add_jquery_fallback = false;
	  if ( $add_jquery_fallback ) {
	    echo '<script>window.jQuery || document.write(\'<script src="' . get_stylesheet_directory_uri() . '/assets/js/jquery.js" ><\/script>\')</script>' . "\n";
	    $add_jquery_fallback = false;
	  }
	  if ( $handle === 'jquery' ) {
	    $add_jquery_fallback = true;
	  }
	  return $src;
	}
}

// set all js scripts to defer loading until after render
if ( !function_exists( 'groundup_defer_script' ) ) {
	function groundup_defer_script( $tag, $handle ) {
		if ( $handle != 'jquery' ) {
			$tag = str_replace(' src', ' defer="defer" src', $tag );
		}
		return $tag;
	}
}
add_filter('script_loader_tag', 'groundup_defer_script', 10, 2);