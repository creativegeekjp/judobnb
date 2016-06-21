<?php
/*
* Template Name: Frontpage
*/
get_header();

$img = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large-image' );

if ( LAYOUT == 'sidebar-no' ) {
	$span_size = 'span12';
} else {
	$span_size = 'span9';
}

?>
<div class="page-<?php echo LAYOUT; ?> page-wrapper <?php if ( !is_front_page() ) { echo 'not_front_page';}?>">
	<div class="clearfix"></div>
	<div class="page_info">
		<?php
		if ( !is_front_page() && !is_home() ) {
			echo vh_breadcrumbs();
		} ?>
		<?php
		if ( !is_front_page() && !is_home() ) { ?>
			<div class="page-title">
				<?php echo  the_title( '<h1>', '</h1>' ); ?>
			</div>
		<?php } ?>
		<div class="clearfix"></div>
	</div>
	<div class="content vc_row wpb_row vc_row-fluid">
		<?php
		wp_reset_postdata();
		if (LAYOUT == 'sidebar-left') {
		?>
		<div class="vc_col-sm-3 <?php echo LAYOUT; ?>">
			<div class="sidebar-inner">
			<?php
				global $vh_is_in_sidebar;
				$vh_is_in_sidebar = true;
				generated_dynamic_sidebar();
			?>
			</div>
		</div><!--end of sidebars-->
		<?php } ?>
		<div class="<?php echo LAYOUT; ?>-pull <?php echo (LAYOUT != 'sidebar-no') ? 'vc_col-sm-9' : 'vc_col-sm-12'; ?>">
			<div class="main-content">
				<?php
				if ( isset($img[0]) ) { ?>
					<div class="entry-image">
						<img src="<?php echo $img[0]; ?>" class="open_entry_image <?php echo $span_size; ?>" alt="" />
					</div>
				<?php } ?>
				<div class="main-inner">
					<?php
					if (have_posts ()) {
						while (have_posts()) {
							the_post();
							the_content();
						}
					} else {
						echo '
							<h2>Nothing Found</h2>
							<p>Sorry, it appears there is no content in this section.</p>';
					}
					?>
				</div>
			</div>
		</div>
		<?php
		if (LAYOUT == 'sidebar-right') {
		?>
		<div class="vc_col-sm-3 pull-right <?php echo LAYOUT; ?>">
			<div class="sidebar-inner">
			<?php
				global $vh_is_in_sidebar;
				$vh_is_in_sidebar = true;
				generated_dynamic_sidebar();
			?>
			<div class="clearfix"></div>
			</div>
		</div><!--end of span3-->
		<?php } ?>
		<?php $vh_is_in_sidebar = false; ?>
		<div class="clearfix"></div>
	</div><!--end of content-->
	<div class="clearfix"></div>
</div><!--end of page-wrapper-->
<?php get_footer();