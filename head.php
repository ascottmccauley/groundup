<?php
/**
 * @package groundup
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">	<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="alternate" type="application/rss+xml" title="<?= get_bloginfo('name'); ?> Feed" href="<?= esc_url(get_feed_link()); ?>">
		<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php groundup_get_browser_warning(); ?>