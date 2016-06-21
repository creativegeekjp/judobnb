<?php
/**
 * Single template file.
 */
get_header();

$layout_type = get_post_meta(get_the_id(), 'layouts', true);

if(empty($layout_type)) {
	$layout_type = get_option('vh_layout_style') ? get_option('vh_layout_style') : 'full';
}

$img       = wp_get_attachment_image_src( get_post_thumbnail_id(), 'offer-image-large' );
$span_size = 'vc_col-sm-10';
?>
<div class="page-<?php echo LAYOUT; ?> page-wrapper">
	<div class="clearfix"></div>
	<div class="page_info">
	<?php
	if ( get_post_type( $post ) == 'post' ) { ?>
		<?php echo vh_breadcrumbs(); ?>
		<div class="page-title">
			<h1 class="blog_title"><?php echo get_the_title( $post->id ); ?></h1>
			<?php echo '<div class="blog-post-date icon-calendar">'.date('j M', strtotime($post->post_date_gmt)).', <span>'.date('G:H', strtotime($post->post_date_gmt)).'</span></div>'; ?>
		</div>
		<?php
	} elseif ( !is_front_page() && !is_home() ) { ?>
		<?php echo vh_breadcrumbs(); ?>
		<div class="page-title">
			<?php echo  the_title( '<h1>', '</h1>' );?>
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
		<div class="<?php echo LAYOUT; ?>-pull">
			<div class="main-content <?php echo (LAYOUT != 'sidebar-no') ? 'vc_col-sm-9' : 'vc_col-sm-12'; ?>">
				<div class="main-inner">
					<div class="vc_row wpb_row vc_row-fluid">
						<?php
						if ( have_posts() ) {
							while ( have_posts() ) {
								the_post();
								get_template_part( 'content', 'single' ); 
								if ( get_post_type( $post ) == 'post' ) { ?>
									<div class="clearfix"></div>
									<div class="comments_container">
										<div class="clearfix"></div>
										<?php
										comments_template( '', true ); ?>
									</div>
									<nav class="nav-single blog">
										<?php
										$prev_post = get_previous_post();
										$next_post = get_next_post();
										if (!empty( $prev_post )) { ?>
											<div class="nav_button left">
												<span class="prev-post-text"><?php _e('Previous blog entry', 'vh'); ?></span>
												<div class="clearfix"></div>
												<div class="prev-post-img">
													<?php echo '<a href="' . get_permalink( $prev_post->ID ) . '">'.get_the_post_thumbnail( $prev_post->ID, 'gallery-large' ).'</a>'; ?>
												</div>
												<div class="prev-post-link">
													<a href="<?php echo get_permalink( $prev_post->ID ); ?>" class="prev_blog_post"><?php echo get_the_title( $prev_post->ID ); ?></a>
												</div>
											</div>
										<?php }
										if (!empty( $next_post )) { ?>
											<div class="nav_button right">
												<span class="next-post-text"><?php _e('Next blog entry', 'vh'); ?></span>
												<div class="clearfix"></div>
												<div class="next-post-img">
													<?php echo '<a href="' . get_permalink( $next_post->ID ) . '">'.get_the_post_thumbnail( $next_post->ID, 'gallery-large' ).'</a>'; ?>
												</div>
												<div class="next-post-link">
													<a href="<?php echo get_permalink( $next_post->ID ); ?>" class="next_blog_post"><?php echo get_the_title( $next_post->ID ); ?></a>
												</div>
											</div>
										<?php } ?>
										<div class="clearfix"></div>
									</nav><!-- .nav-single -->
									<?php
								}
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
		</div>
	</div><!--end of content-->
	<div class="clearfix"></div>
</div><!--end of page-wrapper-->
<?php get_footer();