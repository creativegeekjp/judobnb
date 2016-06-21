<?php
/**
 * The template for displaying content in the single.php template
 *
 * @package WordPress
 * @subpackage Sky
 */

global $vh_blog_image_layout;

$show_sep       = FALSE;
$style          = '';
$clear          = '';
$excerpt        = get_the_excerpt();
$top_left       = "";
$small_image    = FALSE;
$post_date_d    = get_the_date( 'd. M' );
$post_date_m    = get_the_date( 'Y' );
$is_author_desc = '';
$post_id = $post->ID;

$show_date = isset( $show_date ) ? $show_date : NULL;

if ( get_the_author_meta( 'description' ) ) { 
	$is_author_desc = ' is_author_desc';
}

// Determine blog image size
if ( LAYOUT == 'sidebar-no' ) {
	$clear     = ' style="float: none;"';
	$img_style = ' style="margin-left: 0;"';
} else {
	$small_image = TRUE;
	$img_style   = ' style="margin-left: 0;"';
}
$img           = wp_get_attachment_image_src( get_post_thumbnail_id(), 'open-listing' );
$entry_utility = '';

$entry_utility .= '<div class="page_title">' . get_the_title() . '</div>';
$entry_utility_bottom = '<div class="entry-bottom-utility">';
if ( 'post' == get_post_type() ) {

	/* translators: used between list items, there is a space after the comma */
	$categories_list = get_the_category_list( __( ', ', 'vh' ) );
	if ( $categories_list ) {
		$entry_utility_bottom .= '
		<div class="category-link">
		' . sprintf( __( '<span class="%1$s"></span> %2$s', 'vh' ), 'entry-utility-prep entry-utility-prep-cat-links icon-folder-open', $categories_list );
		$show_sep = TRUE;
		$entry_utility_bottom .= '
		</div>';
	}
}
$entry_utility_bottom .= '</div>';
?>
<div class="entry no_left_margin first-entry <?php echo $is_author_desc; ?> <?php if ( !isset($img[0]) ) { echo ' no-image'; } ?><?php echo (LAYOUT != 'sidebar-no') ? ' vc_col-sm-12' : ' vc_col-sm-12'; ?>">
	<div class="entry-image vh_animate_when_almost_visible with_full_image <?php echo $vh_blog_image_layout . $is_author_desc; ?>"<?php echo $clear; ?>>
		<?php
		$i                 = 2;
		$posts_slideshow   = ( get_option('vh_posts_slideshow_number') ) ? get_option('vh_posts_slideshow_number') : 5;
		$attachments_count = 1;
		?>
		<div class="main_image_wrapper">
			<?php
			if ( isset($img[0]) ) { ?>
				<div class="image_wrapper shadows">
					<img src="<?php echo $img[0]; ?> "<?php echo $img_style; ?> class="open_entry_image" alt="" />
				</div>
			<?php } ?>
		</div>
		<div class="single-listing-by-image">
			<?php echo get_avatar(get_userdata( get_post_field( 'post_author', $post->ID ) )->ID, 135); ?>
		</div>
		<div class="single-listing-by-container">
			<div class="single-listing-by-info">
				<span class="author-text"><?php _e('Author:', 'vh'); ?></span>
				<span class="author-name"><a href="<?php echo esc_url(get_author_posts_url( get_post_field( 'post_author', $post->ID ) )); ?>"><?php echo get_userdata( get_post_field( 'post_author', $post->ID ) )->display_name; ?></a></span>
			</div>
			<div class="single-listing-social">
				<div id="fb-root"></div>
				<script>(function(d, s, id) {
				  var js, fjs = d.getElementsByTagName(s)[0];
				  if (d.getElementById(id)) return;
				  js = d.createElement(s); js.id = id;
				  js.src = "//connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v2.0";
				  fjs.parentNode.insertBefore(js, fjs);
				}(document, 'script', 'facebook-jssdk'));
				</script>
				<div class="fb-like" data-href="<?php echo get_permalink($post->ID); ?>" data-layout="button_count" data-action="like" data-show-faces="false" data-share="false"></div>
				<a href="https://twitter.com/share" class="twitter-share-button">Tweet</a>
				<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
				<script src="https://apis.google.com/js/platform.js" async defer></script>

				<!-- Place this tag where you want the share button to render. -->
				<div class="g-plus" data-action="share" data-annotation="bubble"></div>
			</div>
		</div>
		<div class="open-blog-infobox">
			<a href="<?php echo get_author_posts_url( get_post_field( 'post_author', $post->id ) );?>" class="wpb_button single-listing-contact-author"><?php _e("More by this author", "vh"); ?></a>
			<?php $tc = wp_count_comments($post->ID); ?>
			<div class="open-blog-side">
				<div class="blog-post-comments icon-comment-1"><?php echo $tc->approved; ?></div>
				<?php echo $entry_utility_bottom; ?>
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="entry-content">
				<?php
					echo '<div class="title-and-utility';
					if ( $show_date == 'false' ) { echo ' no_left_margin'; };
					if ( !isset($img[0]) ) { echo ' no_image'; };
					echo '">';
					echo $entry_utility;
					echo '<div class="clearfix"></div>';
					echo '</div>';
				?>
			<div class="clearfix"></div>
			<?php
			if ( is_search() ) {
				the_excerpt();
				if( empty($excerpt) )
					echo 'No excerpt for this posting.';

			} else {
				echo '<div class="blog-open-content">';
					the_content(__('Read more', 'vh'));
					wp_link_pages( array( 'before' => '<div class="page-link"><span>' . __( 'Pages:', 'vh' ) . '</span>', 'after' => '</div>', 'link_before' => '<span class="page-link-wrap">', 'link_after' => '</span>', ) );
				echo '</div>';
			}
			?>
		</div>
		<div class="clearfix"></div>
	</div>
	<div class="clearfix"></div>
	<?php
	// If a user has filled out their description, show a bio on their entries
	if ( get_post_type( $post ) == 'post' && get_the_author_meta( 'description' ) ) { ?>
	<div id="author-info">
		<div class="author-infobox">
			<div id="author-avatar">
				<?php echo get_avatar( get_the_author_meta( 'user_email' ), apply_filters( 'vh_author_bio_avatar_size', 135 ) ); ?>
			</div>
			<div id="author-description">
				<span class="author-text"><?php _e('Author:', 'vh'); ?></span>
				<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" rel="author"><?php echo get_the_author(); ?></a>
				<p><?php the_author_meta( 'description' ); ?></p>
			</div><!-- end of author-description -->
		</div>
		<div class="clearfix"></div>
	</div><!-- end of entry-author-info -->
	<?php } ?>
</div>