<?php
/**
 * Template for a single comment
 * Set up by the callback groundup_comment() in  
 *
 * @package groundup
 */
?>
<li id="comment-<?php comment_ID(); ?>" class="comment">
	<header>
		<cite class="fn"><?php echo get_comment_author_link(); ?></cite>
	</header>
	<section>
		<?php if ( $comment->comment_approved == '0' ) { ?>
			<div class="moderation notice">
				<?php _e( 'Awaiting Moderation.', 'groundup' ); ?>
			</div>
		<?php }
		echo get_comment_text(); ?>
	</section>
	<footer>
		<time datetime="<?php echo comment_date( 'c' ); ?>"><a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ); ?>"><?php echo get_time_ago( get_comment_time( 'U' ) ); ?></a></time>
		<?php edit_comment_link( '<i class="icon-pencil"></i> ' . __( 'edit', 'groundup' ), '', '');
		comment_reply_link( array_merge( $args, array( 'reply_text' => '<i class="icon-comments"></i> reply', 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ), $comment->comment_ID ); ?>
	</footer>
<?php // </li> is automatically added by the walker ?>
