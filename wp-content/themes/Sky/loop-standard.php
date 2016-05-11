<?php
/**
 * The default template for displaying content
 *
 * @package WordPress
 * @subpackage Jobera
 */

global $vh_from_home_page, $post;

$tc = 0;
$excerpt = get_the_excerpt();

$img = wp_get_attachment_image_src(get_post_thumbnail_id(), 'popular-destinations-square');

if ( empty($img[0]) ) {
	$img[0] = get_template_directory_uri() . '/images/default-image.jpg';
}

?>

<?php if ( !in_category('help') ) : ?>

	<li class="blog-inner-container">
			<div  <?php post_class(); ?>>
				<div class="post-image">
					<img src="<?php echo $img[0]; ?>" alt="post-img" class="post-inner-picture">
					<div class="blog-picture-time">
						<?php echo human_time_diff(get_the_time('U',$post->id),current_time('timestamp')) . ' ' . __('ago', 'vh'); ?>
					</div>
					<div class="blog-picture-title">
						<a href="<?php echo get_permalink( $post->ID ); ?>" class="blog-picture-link"><?php echo get_the_title(); ?></a>
						<a href="<?php echo get_permalink( $post->ID ); ?>" class="blog-picture-read-link"><?php _e('Read article', 'vh'); ?></a>
					</div>
					<div class="blog-picture-read"><?php _e('Read article', 'vh'); ?></div>
					<div class="blog-author image">
						<div class="blog-author-image">
							<?php echo get_avatar(get_userdata( get_post_field( 'post_author', $post->ID ) )->ID, 70); ?>
						</div>
						<div class="blog-author-info">
							<?php if ( get_the_category_list(', ') != '' ) { ?>
								<div class="blog-category icon-folder-open">
									<?php echo get_the_category_list(', '); ?>
								</div>
							<?php } ?>
							<div class="blog-comments icon-comment-1">
								<?php
								$tc = wp_count_comments($post->ID);
								echo $tc->approved;
								?>
							</div>
							<div class="blog-author-inner">
								<span class="author-text"><?php _e('Author:', 'vh'); ?></span>
								<span class="author-name">
									<a href="<?php echo get_author_posts_url( get_post_field( 'post_author', $post->ID ) ); ?>"><?php echo get_userdata( get_post_field( 'post_author', $post->ID ) )->display_name; ?></a>
								</span>
							</div>
							<div class="clearfix"></div>
						</div>
						<div class="clearfix"></div>
					</div>
				</div>
				<div class="post-inner-side entry-content <?php echo get_post_type(); ?>">
					<div class="blog-date"><?php echo human_time_diff(get_the_time('U',$post->id),current_time('timestamp')) . ' ' . __('ago', 'vh'); ?></div>
					<div class="blog-title">
						<a href="<?php echo get_permalink( $post->ID ); ?>"><?php echo get_the_title(); ?></a>
					</div>
					<div class="blog-excerpt">
					<?php
						$post_content = '';
						if( empty($excerpt) ) {
							$post_content = __( 'No excerpt for this posting.', 'vh' );
						} else {
							if ( strlen($excerpt) > 80 ) {
								$post_content = substr($excerpt, 0, 80) . '..';
							} else {
								$post_content = $excerpt;
							}
							echo $post_content;
						}
					?>
					</div>
					<div class="blog-author">
						<div class="blog-author-image">
							<?php echo get_avatar(get_userdata( get_post_field( 'post_author', $post->ID ) )->ID, 70); ?>
						</div>
						<div class="blog-author-info">
							<?php if ( get_the_category_list(', ') != '' ) { ?>
								<div class="blog-category icon-folder-open">
									<?php echo get_the_category_list(', '); ?>
								</div>
							<?php } ?>
							<div class="blog-comments icon-comment-1">
								<?php
								$tc = wp_count_comments($post->ID);
								echo $tc->approved;
								?>
							</div>
							<div class="blog-author-inner">
								<span class="author-text"><?php _e('Author:', 'vh'); ?></span>
								<span class="author-name">
									<a href="<?php echo get_author_posts_url( get_post_field( 'post_author', $post->ID ) ); ?>"><?php echo get_userdata( get_post_field( 'post_author', $post->ID ) )->display_name; ?></a>
								</span>
							</div>
							<div class="clearfix"></div>
						</div>
						<div class="clearfix"></div>
					</div>
				</div>
			</div>
	</li>
	
<?php endif; ?>