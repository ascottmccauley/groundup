<?php
/**
 * @package groundup
 */
?>
<?php

$includes = array(
	'includes/helpers.php', // Helper functions used throughout the theme
	'includes/setup.php', // Initial activation and theme setup options
	'includes/cleanup.php', // Cleans up some out of the box WordPress behavior
	'includes/admin.php', // Functions specific to the backend or admin_bar
	'includes/scripts.php', // Functions that load all of the scripts, styles and cookies
);

foreach ( $includes as $file ) {
	if( !$filepath = locate_template( $file ) ) {
		trigger_error( sprintf( __( 'Error locating %s for inclusion', 'groundup' ), $file ), E_USER_ERROR );
	}
	require_once $filepath;
}