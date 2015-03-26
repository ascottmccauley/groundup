<?php
/**
 * @package groundup
 */
?>
<?php get_template_part( 'head' ); ?>
<header>
	<h1 class="brand"><a href="<?php echo home_url(); ?>/"><?php bloginfo( 'name' ); ?></a></h1>
	<?php if(get_bloginfo( 'description' ) != '') { ?>
		<h2 class="description"><a href="<?php echo esc_url( get_permalink( get_page_by_path( 'about' ) ) ); ?>"><?php echo get_bloginfo( 'description' ); ?></a></h2>
	<?php } ?>
</header>
<?php get_template_part( 'templates/nav', 'primary'); ?>