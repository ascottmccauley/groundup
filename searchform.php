<?php
/**
 * Functions that just help out the theming process
 *
 * @package groundup
 */
?>
<form role="search" method="get" action="<?php esc_url( home_url( '/' ) ); ?>">
	<input type="search" name="s" value="<?php echo get_search_query(); ?>" placeholder="<?php _e( 'Search', 'groundup' ); ?>" required>
	<button type="submit"><?php _e( 'Search', 'groundup' ); ?></button>
	<?php do_action( 'groundup_inside_searchform' ); ?>
</form>