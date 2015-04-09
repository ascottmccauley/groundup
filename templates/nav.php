<?php
/**
 *
 * @package groundup
 */
?>
<?php // Desktop Menu
if ( wp_is_mobile() && has_nav_menu( 'mobile' ) ) {
	wp_nav_menu( array( 'theme_location' => 'mobile', 'container' => 'nav' ) );
} else if ( has_nav_menu( 'primary' ) ) {
	wp_nav_menu( array( 'theme_location' => 'primary', 'container' => 'nav' ) );
} 
