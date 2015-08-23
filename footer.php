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
	<?php $menu_object = groundup_get_menu_object( 'Footer' );
  if ( $menu_object->count > 0 ) {
		wp_nav_menu( array(
      'menu' => $menu_object->term_id,
      'container' => 'false',
    ) );
	} else { ?>
		<div class="copyrights">
			<a href="<?php echo esc_url( get_permalink( get_page_by_title( 'Copyrights' ) ) ); ?>">&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?></a>
		</div>
	<?php } ?>
</footer>
<?php wp_footer(); ?>
</body>
</html>