<?php
/**
 * @package groundup
 * @filters:
 *	groundup_image_count
 *	groundup_exif
 */
?>
<time class="updated" datetime="<?php echo get_the_time('c'); ?>"><?php echo get_time_ago( get_the_time('U') ); ?></time>
<?php // Comments
$comment_count = get_comment_count( $post->ID );
if ( comments_open() && $comment_count['approved'] > 0 ) {
	comments_popup_link( '', __('1 Comment', 'groundup' ),  __( '% Comments', 'groundup' ), 'meta comments' );
} ?>
<?php // Image Count
$image_count = get_image_count( $post->ID );
if ( $image_count > 0 ) { ?>
	<span class="meta image-count"><?php echo apply_filters( 'groundup_image_count', $image_count . ' images' ); ?></span>
<?php } ?>

<?php // Exif Info
$exif = get_exif( $post->ID, '</span><span class="meta exif">', '<span class="meta exif">', '</span>' );
if ( $exif != '' ) {
	echo apply_filters( 'groundup_exif', $exif );
} ?>

<?php // Categories
if ( count( get_the_category() ) ) { ?>
	<ul class="meta categories">
		<li><?php echo get_the_category_list( '</li><li>' ); ?></li>
	</ul>
<?php } ?>

<?php // Tags
$tag_list = get_the_tag_list( '<li>','</li><li>', '</li>' );
if ( $tag_list ) { ?>
	<ul class="meta tags">
		<?php echo $tag_list; ?>
	</ul>
<?php } ?>

<?php // Edit link
edit_post_link( 'Edit' ); ?>

