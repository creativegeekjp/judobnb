<?php
/**
 * The template for displaying Archive pages.
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Jobera
 */
get_header();

global $vh_from_archive;
$vh_from_archive = true;
?>
<div class="page-<?php echo LAYOUT; ?> page-wrapper">
	<div class="clearfix"></div>
	<div class="page_info">
		<?php
		if ( !is_front_page() && !is_home() ) {
			echo vh_breadcrumbs();
		} ?>
		<div class="page-title">
			<h1>
			<?php if (is_day()) : ?>
				<?php printf(__('Daily Archives: %s', 'vh'), '<span>' . get_the_date() . '</span>'); ?>
			<?php elseif (is_month()) : ?>
				<?php printf(__('Monthly Archives: %s', 'vh'), '<span>' . get_the_date('F Y') . '</span>'); ?>
			<?php elseif (is_year()) : ?>
				<?php printf(__('Yearly Archives: %s', 'vh'), '<span>' . get_the_date('Y') . '</span>'); ?>
			<?php else : ?>
				<?php _e('Blog Archives', 'vh'); ?>
			<?php endif; ?>
			</h1>
		</div>
		<div class="clearfix"></div>
	</div>
	<div class="content vc_row wpb_row vc_row-fluid">
		<?php wp_reset_postdata(); ?>
		<div class="<?php echo LAYOUT; ?>-pull">
			<div class="main-content <?php echo (LAYOUT != 'sidebar-no') ? 'vc_col-sm-9' : 'vc_col-sm-12'; ?>">
				<div class="main-inner">
					<?php
					if (have_posts()) {
						// Include the Post-Format-specific template for the content.
						get_template_part('loop', get_post_format());
					} else { ?>
						<p><?php _e('Apologies, but no results were found for the requested archive. Perhaps searching will help find a related post.', 'vh'); ?></p>
						<?php get_search_form(); ?>
					<?php } ?>
					<div class="clearer"></div>
					<?php
					if(function_exists('wp_pagenavi')) {
						wp_pagenavi();
					} ?>
				</div>
			</div>
		</div>
		<?php $vh_is_in_sidebar = false; ?>
		<div class="clearfix"></div>
	</div><!--end of content-->
	<div class="clearfix"></div>
</div><!--end of page-wrapper-->
<?php get_footer();