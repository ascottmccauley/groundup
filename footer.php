<?php
/**
 * @package groundup
 */
?>
<?php
// html is opened in header.php
// body is opened in header.php
?>
<footer>
	<div class="copyrights">
		<a href="<?php echo esc_url(get_permalink(get_page_by_title( 'Copyrights'))); ?>">&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?></a>
	</div>
</footer>

<?php wp_footer(); ?>

</body>
</html>