<?php
/**
 * Default searchform used for anywhere get_search_form() is used
 * See /includes/cleanup.php | groundup_get_search_form() 
 *
 * @package groundup
 */
?>
<form role="search" method="get" id="searchbar" action="<?php echo home_url('/'); ?>">
	<input class="search search-query" type="search" value="<?php echo get_search_query(); ?>" name="s" required>
	<button class="searchsubmit"><?php _e( 'Search', 'groundup' ); ?></button>
	<?php do_action( 'groundup_searchform_fields' ); ?>
</form>