<?php
/**
 * Functions that just help out the theming process
 *
 * @package groundup
 */
?>
<?php
// a simple check to see if we are on the login page
function is_login() {
	return in_array( $GLOBALS['pagenow'], array( 'wp-login.php') );
}

// a simple check to see if we are on the registration page
function is_register() {
	return in_array( $GLOBALS['pagenow'], array( 'wp-register.php' ) );
}

// Set a cookie after the first visit
if ( !function_exists( 'groundup_new_user_cookie' ) ) {
	function groundup_new_user_cookie() {
		if ( ! $_COOKIE[ 'new_user' ] ) {
			setcookie( 'new_user', '0', time()+3600*24*100, '/', $domain, false );
		}
	}
}
add_action( 'init', 'groundup_new_user_cookie' );

// Check to see if this is user's first visit
if ( !function_exists( 'groundup_is_new_user' ) ) {
	function groundup_is_new_user() {
		if ( $_COOKIE[ 'new_user' ] == '0' ) {
			return false;
		} else {
			return true;
		}
	}	
}

// For internet Exploder below 9.0 display a simple alert notifying them to update
// check $cached to prevent displaying the message on every page
// @filters: groundup_browser_warning - the html warning message for outdated browsers
if ( !function_exists( 'groundup_browser_warning' ) ) {
	function groundup_browser_warning() {
		// only show the warning message once
		if ( groundup_is_new_user() ) {
			$warning = '<!--[if lt IE 9]><div class="alert-error browser-warning">Your browser is out of date. <a href="http://browsehappy.com/">Please upgrade to a modern browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a>.</div><![endif]-->';
			echo apply_filters( 'groundup_browser_warning', $warning );
		}
	}
}
add_action( 'groundup_inside_body', 'groundup_browser_warning' );

// Use basic logic to determine correct sidebar for the page
// @filters: groundup_sidebars
if ( !function_exists( 'groundup_get_sidebar' ) ) {
	function groundup_get_sidebar( ) {
		$sidebars = array( 'Default' );
		if ( is_front_page() ) {
			$sidebars = array( 'Home' );
		} elseif ( is_attachment() ) {
			array_unshift( $sidebars, 'Image', 'Single' );
		} elseif ( is_single() || is_page() ) {
			array_unshift( $sidebars, 'Single' );
		} elseif ( is_category() ) {
			array_unshift( $sidebars, 'Category' );
		} elseif ( is_tax() ) {
			array_unshift( $sidebars, 'Tag', 'Category' );
		} elseif ( is_archive() ) {
			array_unshift( $sidebars, 'Archive','Category' );
		} elseif ( is_author() ) {
			array_unshift( $sidebars,'Author', 'Single' );
		} elseif ( is_search() ) {
			array_unshift( $sidebars,'Search' );
		}
		$sidebars = apply_filters( 'groundup_sidebars', $sidebars );
		
		// Loop through possible sidebars until one has widgets
		$groundup_sidebar = null;
		foreach ( $sidebars as $sidebar ) {
			if ( is_active_sidebar( 'sidebar-'.  strtolower( str_replace( ' ', '-', $sidebar ) ) ) ) {
				$groundup_sidebar = strtolower( str_replace( ' ', '-', $sidebar ) );
				break;
			}
		}
		return $groundup_sidebar;
	}
}

// Displays time as XYZ days/hours/minutes ago
function get_time_ago( $time ) {
	$difference = time() - $time;
	
	$min_in_secs = 60;
	$hour_in_secs = 3600;
	$day_in_secs = 86400;
	$month_in_secs = $day_in_secs * 31;
	$year_in_secs = $day_in_secs * 366;
	
	if ( $difference > $year_in_secs ) {
		return 'over ' . floor( $difference / $year_in_secs ) . __( ' years ago', 'groundup' );
	}else {
		return human_time_diff( $time, time() ) . ' ' . __( 'ago', 'groundup' );
	}
}

// Takes two dates and returns them in a prettier format, such as:
// February 27th - March 3rd, 2021
function get_date_range($startDate = '', $endDate = '', $separator = ' - ') {
	$range = '';
	if ( !empty( $startDate ) && !empty( $endDate ) && $startDate != $endDate ) {
		if ( date( 'Y', $startDate ) != date( 'Y', $endDate ) ) {
			// Different Years
			$range = date( 'F j<\s\up>S</\s\up>, Y', $startDate ) . $separator . date( 'F j<\s\up>S</\s\up>, Y', $endDate );
		}elseif ( date( 'm', $startDate ) != date( 'm', $endDate ) ) {
			// Different Months
			$range = date( 'F j<\s\up>S</\s\up>', $startDate ) . $separator . date( 'F j<\s\up>S</\s\up>, Y', $endDate );
		}else {
			// Different Days
			$range = date( 'F j<\s\up>S</\s\up>', $startDate ) . $separator . date( 'j<\s\up>S</\s\up>, Y', $endDate ); 
		}
	}else {
		// only 1 date, so just make that pretty
		$date = $startDate != '' ? $startDate : $endDate;
		$range = date( 'F j<\s\up>S</\s\up>, Y', $date );
	}
	return $range;
}

// counts the number of images attached to a post
function get_image_count($ID) {
	$images = get_children( array(
		'post_parent'=>$ID,
		'post_type'=>'attachment',
		'post_mime_type'=>'image',
		'orderby'=>'menu_order',
		'order' => 'ASC',
		'numberposts' => 999
	) );
	return count($images);
}

// Displays camera exif information for an attachment
function get_exif ( $att, $separator = '', $before = '', $after = '' ) {
	$imgmeta = wp_get_attachment_metadata($att);
	if ( $imgmeta ) { // Check for Bad Data
		if ( $imgmeta['image_meta']['focal_length'] == 0
		|| $imgmeta['image_meta']['aperture'] == 0
		|| $imgmeta['image_meta']['shutter_speed'] == 0
		|| $imgmeta['image_meta']['iso'] == 0 ) {
			$output = '';
		} else { // Convert the shutter speed retrieve from database to fraction
			if ( ( 1 / $imgmeta['image_meta']['shutter_speed'] ) > 1 ) {
				if ( ( number_format( ( 1 / $imgmeta['image_meta']['shutter_speed'] ), 1 ) ) == 1.3
				|| number_format((1 / $imgmeta['image_meta']['shutter_speed']), 1) == 1.5
				|| number_format((1 / $imgmeta['image_meta']['shutter_speed']), 1) == 1.6
				|| number_format((1 / $imgmeta['image_meta']['shutter_speed']), 1) == 2.5) {
					$pshutter = "1/" . number_format((1 / $imgmeta['image_meta']['shutter_speed']), 1, '.', '') . " second";
				} else {
					$pshutter = "1/" . number_format((1 / $imgmeta['image_meta']['shutter_speed']), 0, '.', '') . " second";
				}
			} else {
				$pshutter = $imgmeta['image_meta']['shutter_speed'] . " seconds";
			}
			
			$output = $before;
			$output .=  '<time datetime="' . date('c', $imgmeta['image_meta']['created_timestamp']) . '"><span class="month">' . date('F', $imgmeta['image_meta']['created_timestamp']).'</span> <span class="day">'.date('j', $imgmeta['image_meta']['created_timestamp']) . '</span><span class="suffix">' . date('S', $imgmeta['image_meta']['created_timestamp']) . '</span> <span class="year">' . date('Y', $imgmeta['image_meta']['created_timestamp']) . '</span></time>' . $separator;
			$output .=  $imgmeta['image_meta']['camera'] . $separator;
			$output .=  $imgmeta['image_meta']['focal_length'] . 'mm' . $separator;
			$output .=  '<span style="font-style:italic;font-family: Trebuchet MS,Candara,Georgia; text-transform:lowercase">f</span>/' . $imgmeta['image_meta']['aperture'] . $separator;
			$output .=  $pshutter . $separator;
			$output .=  $imgmeta['image_meta']['iso'] .' ISO';
			$output .= $after;
		}
	}else { // No Data Found
		$output = '';
	}
	return $output;
}

// replaces WordPress's standard get_avatar so that it returns '' if there is no avatar
if ( !function_exists( 'groundup_get_avatar' ) ) {
	function groundup_get_avatar( $id_or_email, $size='64', $placeholder='404' ) {
		// get $email from input. Could be $comment, $email, or $user_id
		$authorURL = '';
		$authorName = '';
		if ( is_numeric( $id_or_email ) ) {
			$id = (int) $id_or_email;
			$user = get_userdata($id);
			if ( $user ) {
				$email = $user->user_email;
			}
		} elseif ( is_object( $id_or_email ) ) {
			if ( !empty( $id_or_email->user_id ) ) {
				$id = (int) $id_or_email->user_id;
				$user = get_userdata($id);
				if ( $user ) {
					$email = $user->user_email;
					$authorName = $user->display_name;
					$authorURL = get_edit_profile_url($id);
				}
			}elseif ( !empty($id_or_email->comment_author_email ) ) {
				$email = $id_or_email->comment_author_email;
				$authorName = $id_or_email->comment_author;
				$authorURL = $id_or_email->comment_author_url;
			}
		}else {
			$email = $id_or_email;
		}
		
		// Create gravatar url using placeholder or 404
		if ( $placeholder != '404' ) {
			$placeholder = urlencode( $placeholder );
		}
		$image = 'http://www.gravatar.com/avatar/' . md5($email) . '?s=' . $size . '&d=' . $placeholder;
		$headers = get_headers($image);
		if ( !strpos($headers[0],'200' ) ) {	// no avatar
			return '';
		}
		
		$avatar = '<img src="' . $image . '" class="avatar" alt="' . $authorName . '">';
		
		if ( $authorURL ) {
			return '<a href="' . $authorURL . '" rel="external nofollow">' . $avatar . '</a>';
		}else { // no URL
			return $avatar;
		}
	}
}

// checks to see if a category is a parent
function is_parent_category( $cat = null ) {
	if ( is_numeric( $cat ) ) {
		$category = get_the_category_by_ID( $cat );
	} elseif ( is_string($cat)) {
		$category = get_category_by_slug( $cat );
	} elseif ( is_null( $cat ) )  {
		$category = get_queried_object()  ;
	}
	
	$children = get_categories( "parent={$category->term_id}" );
	if ( !empty( $children ) ) {
		return true;
	}else {
		return false;
	}
}

// checks to see if a category has a parent
function is_child_category( $cat = null ) {
	if ( is_numeric( $cat ) ) {
		$category = get_the_category_by_ID( $cat );
	} elseif ( is_string( $cat ) ) {
		$category = get_category_by_slug( $cat );
	} elseif ( is_null($cat ) )  {
		$category = get_queried_object()  ;
	}
	
	if ( $category->parent != 0 ) {
		return true;
	}else {
		return false;
	}
}

// Returns an array of the max-width and max-height and crop of the requested image size 
function get_image_size_data( $size = 'thumbnail' ) {
	$default_image_sizes = array('thumbnail', 'medium', 'large'); // Standard sizes
	if ( in_array($size, $default_image_sizes ) ) {
		$result['width'] = intval(get_option($size . '_size_w'));
		$result['height'] = intval(get_option($size . '_size_h'));
		// If not set: crop false per default.
		$result [$size]['crop'] = false;
		if (get_option($size . '_crop')) {
			$result[$size]['crop'] = get_option($size . '_crop');
		}
	}else {
		global $_wp_additional_image_sizes;
		if ( in_array( $size, array_keys( $_wp_additional_image_sizes ) ) ) {
			$result = $_wp_additional_image_sizes[$size];
		}
	}

	return $result;
}
