<?php
/**
 * @package groundup
 */
?>
<?php if( comments_open() || have_comments() ) { ?>
	<aside id="discussion">
		<?php // Comment Form
		comment_form();
		
		if ( have_comments() ) {
			// Password Protected
			if ( post_password_required() ) { ?>
				<p><?php _e( 'This area is password protected. Enter the password to view comments.', 'groundup' ); ?></p>
			<?php } ?>
			<section id="comments">
				<header>
					<h4><?php comments_number(
									__( 'No Comments', 'groundup' ),
									__( 'One Comment', 'groundup' ),
									__( '% Comments', 'groundup' )
								); ?></h4>
				</header>
				<ol class="comment-list">
					<?php wp_list_comments( array( 'callback' => 'groundup_comment' ) ); ?>
				</ol>
				<?php // Comment Pagination
				if ( get_comment_pages_count() > 1 ) { ?>
					 <nav id="comments-nav" class="pager">
					 	<ul class="pager">
					 		<?php if (get_previous_comments_link() ) { ?>
					 		  <li class="previous"><?php previous_comments_link( __( '&larr; Older comments', 'groundup' ) ); ?></li>
					 		<?php } else { ?>
					 		  <li class="previous disabled"><a><?php _e( '&larr; Older comments', 'groundup' ); ?></a></li>
					 		<?php } ?>
					 		<?php if ( get_next_comments_link() ) { ?>
					 		  <li class="next"><?php next_comments_link( __( 'Newer comments &rarr;', 'groundup' ) ); ?></li>
					 		<?php } else { ?>
					 		  <li class="next disabled"><a><?php _e( 'Newer comments &rarr;', 'groundup' ); ?></a></li>
					 		<?php } ?>
					 	</ul>
					 </nav>
				<?php }
				// Comments are closed
				if ( ! comments_open() && ! is_page() && post_type_supports( get_post_type(), 'comments' ) ) { ?>
					<p><?php _e('Comments are closed.', 'fin'); ?></p>
				<?php } ?>
			</section>
		<?php } ?>
	</aside>
<?php } ?>
