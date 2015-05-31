<?php
/**
 * @package groundup
 */
?>
<?php
// html is opened in header.php
// body is opened in header.php
?>
<?php do_action( 'groundup_before_footer' ); ?>
<footer id="footer">
    <?php do_action( 'groundup_inside_footer' ); ?>
	<?php if ( has_nav_menu( 'footer' ) ) {
		wp_nav_menu( array( 'theme_location' => 'footer', 'container' => 'false' ) );
	} else { ?>
		<div class="copyrights">
			<a href="<?php echo esc_url(get_permalink(get_page_by_title( 'Copyrights'))); ?>">&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?></a>
		</div>
	<?php } ?>
</footer>
<?php wp_footer(); ?>
</body>
</html>