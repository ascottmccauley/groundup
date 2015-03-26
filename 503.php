<?php
/*
Template Name: 503
*/
?>
<?php 
/**
 * Basic template for bad links and missing content
 * Empty search results will still use search.php
 * 
 * @package groundup
 */
?>
<?php get_header(); ?>
<main id="main" role="main">
	<?php // Search for a page with any of the following slugs and output its content
	$page_array = array( 'maintenance', 'construction', 'under-construction', '503' );
	foreach ( $page_array as $page_name ) {
		$page = get_page_by_path( 'maintenance' );
		if ( $page ) {
			$content = apply_filters( 'the_content', $page->post_content );
			if ( $content != '' ) {
				continue;
			}
		}
		if ( $content != '' ) {
			echo $content;
		} else {
			echo '<h1>' . __( 'Under Construction' ) . '</h1><h4>' . __( 'Please check back soon' ) . '</h4>';
		} 
	} ?>
</main>
<?php get_footer(); ?>
<?php die; // stop loading all other templates
	
