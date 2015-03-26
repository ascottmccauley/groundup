<?php
/**
 * @package groundup
 */
?>
<?php get_header(); ?>
<main id="main" role="main">
	<?php if ( have_posts() ) {
		// Display Header info
		if ( is_search() ) {
			$page_title = 'Results for <strong>"' . get_search_query() . '"</strong>'; 
		} elseif ( !is_singular() && !is_front_page() ) {
			$page_title = get_the_archive_title();
			// remove everything before and including first :
			if ( ( $pos = strpos( $page_title, ':' ) ) !== false ) {
			   $page_title = substr($page_title, $pos + 1);
			}
			if ( !empty( apply_filter( 'page_title', $page_title ) ) ) { ?>
				<header><h3 class="text-center"><?php echo $page_title; ?></h3></header>
			<?php } 
		}
		if ( ! empty( $page_title ) ) { ?>
			<header class="page-header">
				<h3><?php echo $page_title; ?></h3>
			</header>
		<?php } ?>
		
		<?php while( have_posts() ) : the_post(); 
			$post_type = get_post_type();
			if ( $post_type == 'post' ) {
				$post_type = get_post_format();
			}
			if ( is_single() || is_page() ) {
				get_template_part( 'templates/single', $post_type );
			} else {
				get_template_part( 'templates/excerpt', $post_type );
			}
		endwhile;
		
	} ?>
	
	<?php get_template_part( 'templates/pagination' ); ?>
	
</main>

<?php get_sidebar(); ?>

<?php get_footer(); ?>
			