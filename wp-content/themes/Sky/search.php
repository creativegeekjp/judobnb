<?php
/**
 * The template for displaying Search Results pages.
 *
 * @package WordPress
 * @subpackage Jobera
 */

get_header();
global $vh_from_search, $vh_blog_image_layout;
$vh_blog_image_layout = 'with_full_image';
?>

<div class="page-<?php echo LAYOUT; ?> page-wrapper">
	<div class="clearfix"></div>
	<div class="page_info">
		<?php
		if ( !is_front_page() && !is_home() ) {
			echo vh_breadcrumbs();
		} ?>
		<div class="page-title">
			<?php if ( have_posts() ) { ?>
				<h1><?php printf( __( 'Search Results for: %s', 'vh' ), '<span>' . get_search_query() . '</span>' ); ?></h1>
			<?php } else { ?>
				<h1><?php _e( 'Nothing Found', 'vh' ); ?></h1>
			<?php } ?>
		</div>
		<div class="clearfix"></div>
	</div>
	<div class="content vc_row wpb_row vc_row-fluid">
		<div class="<?php echo LAYOUT; ?>-pull <?php echo (LAYOUT != 'sidebar-no') ? 'vc_col-sm-9' : 'vc_col-sm-12'; ?>">
			<div class="main-content <?php echo (LAYOUT != 'sidebar-no') ? 'vc_col-sm-9' : 'vc_col-sm-12'; ?>">
				<?php
				if ( isset($img[0]) ) { ?>
					<div class="entry-image">
						<img src="<?php echo $img[0]; ?>" class="open_entry_image <?php echo $span_size; ?>" alt="" />
					</div>
				<?php } ?>
				<div class="main-inner">
					<?php
					if ( have_posts() ) {
						$vh_from_search = true;

						// Include the Post-Format-specific template for the content.
						get_template_part( 'loop', get_post_format() );

						if(function_exists('wp_pagenavi')) { wp_pagenavi(); }
						echo '<div style="display: none;">' . paginate_links() . '</div>';

					} else { ?>
						<div class="vc_row wpb_row vc_row-fluid">
							<p><?php _e('Sorry, but nothing matched your search criteria. Please try again with some different keywords.', 'vh'); ?></p>
							<?php
							$vh_is_in_sidebar = 'content';
							get_search_form();
							?>
						</div><!--end of entry-content-->
					<?php } ?>
				</div>
			</div>
		</div>
		<?php $vh_is_in_sidebar = false; ?>
		<div class="clearfix"></div>
	</div><!--end of content-->
	<div class="clearfix"></div>
</div><!--end of page-wrapper-->
<?php get_footer();