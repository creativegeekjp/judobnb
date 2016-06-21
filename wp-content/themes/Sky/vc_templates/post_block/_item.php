<?php
$block = $block_data[0];
$settings = $block_data[1];
?>
<?php if($block === 'title'): ?>
	<?php if (empty($post->thumbnail)): ?>
		<div class="post-thumb nothumbnail">
			<div class="blog-infobox">
				<?php 
				$tc = wp_count_comments($post->id);
				?>
				<div class="blog-post-date icon-calendar"><?php echo get_the_date('j M', $post->id); ?></div>
				<div class="blog-post-comments icon-comment-1"><?php echo $tc->total_comments; ?></div>
			</div>
			<div class="clearfix"></div>
			<h2 class="post-title">
				<?php echo !empty($settings[0]) && $settings[0]!='no_link' ? $this->getLinked($post, $post->title, $settings[0], 'link_title') : $post->title ?>
			</h2>
			<div class="clearfix"></div>
		</div>
	<?php else: ?>
		<h2 class="post-title">
			<?php echo !empty($settings[0]) && $settings[0]!='no_link' ? $this->getLinked($post, $post->title, $settings[0], 'link_title') : $post->title ?>
		</h2>
	<?php endif ?>
<?php elseif($block === 'image' && !empty($post->thumbnail)): ?>
		<div class="post-thumb">
			<div class="post-thumb-img-wrapper">
				<div class="blog-post-img-overlay"><a href="<?php echo get_permalink($post->id); ?>"><?php _e('Read article', 'vh'); ?></a></div>
				<?php echo !empty($settings[0]) && $settings[0]!='no_link' ? $this->getLinked($post, $post->thumbnail, $settings[0], 'link_image') : $post->thumbnail ?>
			</div>
			<div class="blog-infobox">
				<?php 
				$tc = wp_count_comments($post->id);
				?>
				<div class="blog-post-date icon-calendar"><?php echo get_the_date('j M', $post->id); ?></div>
				<div class="blog-post-comments icon-comment-1"><?php echo $tc->total_comments; ?></div>
			</div>
		</div>
<?php elseif($block === 'text'): ?>
		<div class="entry-content">
			<?php echo !empty($settings[0]) && $settings[0]==='text' ?  $post->content : $post->excerpt; ?>
		</div>
<?php elseif($block === 'link'): ?>
		<div class="read_more"><a href="<?php echo $post->link ?>" class="vc_read_more wpb_button wpb_btn-transparent wpb_small" title="<?php echo esc_attr(sprintf(__( 'Permalink to %s', "vh" ), $post->title_attribute)); ?>"<?php echo $this->link_target ?>><?php _e('Read more', "vh") ?></a></div>
		<?php
		$category_array = get_the_category($post->id);
		$categories = '';
		foreach ($category_array as $category) {
			$categories .= '<a href="' . $category->term_id . '">' . $category->name . '</a>, ';
		}
		?>
		<div class="blog-post-category"><?php echo rtrim($categories, ', '); ?></div>
<?php endif; ?>
