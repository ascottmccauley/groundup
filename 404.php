<?php
/**
 * Basic template for bad links and missing content
 * Empty search results will still use search.php
 * 
 * @package groundup
 */
?>
<?php get_header(); ?>
<h4 class="text-center"><?php _e( 'It seems as though something or someone got lost.' ); ?></h4>
<h5 class="text-center"><?php _e( 'Sorry about the inconvenience.' ); ?></h5>
<?php get_footer(); ?>