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
			<?php wp_list_pages( array( 'depth' => 0, 'sort_column' => 'menu_order', 'title_li' => '') ); ?>
		</section>
		<?php // Loop through all public queryable post_types
		$post_types = get_post_types( array( 'public' => true ), 'objects' );
		foreach ( $post_types as $name => $post_type) {
			if ( in_array( $name, array( 'media', 'page', 'attachment' ) ) ) {
				continue;
			}
			$labels = $post_type->labels; ?>
			<section>
				<h4><?php echo $labels->name; ?></h4>
				<?php // Use hierarchical taxonomies to organize if available
				$taxonomies = get_object_taxonomies( $post_type->name, 'objects' );
				foreach ( $taxonomies as $taxonomy ) {
					if ( $taxonomy->hierarchical == true  && $taxonomy->public == true && $taxonomy->query_var != false ) {
						wp_list_categories( array( 
							'taxonomy' => $taxonomy->name,
							'show_count' => 1,
							'hierarchical' => 1,
							'title_li' => '',
							'order_by' => 'term_group',
						) );
					}
				} ?>
			</section>
	 	<?php	} ?>
	</article>
</main>

<?php get_sidebar(); ?>

<?php get_footer(); ?>