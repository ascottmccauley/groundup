<?php
/**
 *
 * @package groundup
 */
?>
<?php if ( has_nav_menu( 'primary' ) ) { ?>
	<nav><?php wp_nav_menu( array( 'theme_location' => 'primary', 'container' => 'false' ) ); ?></nav>
<?php }

