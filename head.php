<?php
/**
 * @package groundup
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">	<meta name="viewport" content="width=device-width, initial-scale=1, minimal-ui">
		<link rel="alternate" type="application/rss+xml" title="<?= get_bloginfo('name'); ?> Feed" href="<?= esc_url(get_feed_link()); ?>">
		<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php do_action( 'groundup_inside_body' ); ?>