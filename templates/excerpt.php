<?php
/**
 * @package groundup
 */
?>
<article <?php post_class(); ?>>
	<header>
		<h3 class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark" title="Bookmark for <?php the_title_attribute(); ?>" class="bookmark"><?php the_title(); ?></a></h3>
	</header>
	<section class="entry-content">
		<?php $thumbnail = get_the_post_thumbnail( $post->ID,'medium' );
			if ( $thumbnail != '' ) { ?>
				<aside class="entry-thumbnail th left">
					<?php echo $thumbnail; ?>
				</aside>
			<?php } ?>
			<?php the_content(); ?>
	</section>
	<footer>
		<?php get_template_part( 'templates/meta' ); ?>
	</footer>
</article>
<?php comments_template(); ?>