<?php
/*
Template Name: Sitemap
*/
?>
<?php
/**
 * @package groundup
 * @filters:
 * 	'groundup_sitemap_exclude_post_types' - array of post types to be excluded from the sitemap page. Default is 'attachment'.
 */
?>

<?php get_header(); ?>
<main id="main" role="main">
	<article <?php post_class( 'single' ); ?>>
		<header>
			<h3 class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark" title="Bookmark for <?php the_title_attribute(); ?>" class="bookmark"><?php the_title(); ?></a></h3>
		</header>
		<section>
			<h4><?php _e( 'Pages', 'groundup' ); ?></h4>
			<?php wp_list_pages( 'depth' => 0, 'sort_column' => 'menu_order', 'title_li' => '' ); ?>
		</section>
		<?php // Loop through all public queryable post_types except pages and attachments
		$post_types = get_post_types( array( 'public' => true), 'objects' );
		$post_types = array_diff( $post_types, apply_filters( 'groundup_sitemap_exclude_post_types', array( 'attachment') );
		foreach ( $post_types as $post_type ) {
			$labels = $post_type->labels;
			$type_args = array(
			  'posts_per_page' => -1,
			  'post_type' => $post_type->name,
			  'post_status' => 'published'
			);
			$type_query = new WP_Query( $type_args );
			if ( $type_query->have_posts() ) { ?>
				<section>
					<h4><?php echo $labels->name; ?></h4>
					<?php // Use hierarchical taxonomies to organize if available
					$taxonomies = get_object_taxonomies( $post_type->name, 'objects' );
					$tax_check = false; // set to true when a taxonomy is found.
					foreach ($taxonomies as $taxonomy) {
						if ( $taxonomy->hierarchical == true && $taxonomy->public == true && $taxonomy->query_var != false ) {
							// Get Taxonomy Terms
							$terms = get_terms( $taxonomy->name );
							foreach ( $terms as $term ) {
								$tax_args = array(
								  'posts_per_page' => -1,
								  'post_type' => $post_type->name,
								  $taxonomy->query_var => $term->slug
								);
								$tax_query = new WP_Query( $tax_args );
								if ( $tax_query->have_posts() ) { ?>
									<div class="sub-section">
										<h5><?php echo $term->name; ?></h5>
										<ul>
											<?php while ( $tax_query->have_posts() ) : $tax_query->the_post(); ?>
												<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
											<?php endwhile; ?>
										</ul>
									</div>
								<?php }
							}
						}
						// If there is no hierarchical order, just display posts
						if ( $tax_check == false ) { ?>
							<ul>
								<?php while($type_query->have_posts()): $type_query->the_post(); ?>
									<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
								<?php endwhile; ?>
							</ul>
						<?php } ?>
				</section>
			<?php }
		} ?>
	</article>
</main>

<?php get_sidebar(); ?>

<?php get_footer(); ?>