<?php
/**
 * @package groundup
 */
?>
<?php // Get correct sidebar for the page
$sidebar = groundup_get_sidebar();
if ( $sidebar ) { ?>
	<aside class="sidebar" role="complementary">
		<?php do_action( 'groundup_before_widgets' );
		dynamic_sidebar( $sidebar );
		do_action( 'groundup_after_widgets' ); ?>
	</aside>
<?php } ?>