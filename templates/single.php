<?php
/**
 * @package groundup
 */
?>
<article <?php post_class(); ?>>
	<?php $page_title = apply_filters( 'page_title', $page_title );
	if ( ! empty( $page_title ) ) { ?>
		<header class="page-header">
			<h2><?php echo $page_title; ?></h2>
		</header>
	<?php } ?>
	<section class="entry-content">
		<?php $thumbnail = get_the_post_thumbnail( $post->ID,'medium' );
			if ( $thumbnail != '' ) { ?>
				<aside class="entry-thumbnail th left">
					<?php echo $thumbnail; ?>
				</aside>
			<?php } ?>
			<?php the_content(); ?>
	</section>
	<?php if ( ! is_page() ) { ?>
		<footer>
			<?php get_template_part( 'templates/meta' ); ?>
		</footer>
	<?php } ?>
</article>
<?php comments_template(); ?>