<?php
$args_com = array(
	'post_type' 			=> 'page',
	'post_status' 		=> 'publish',
	'order'           => 'ASC',
	'post__in'       => array(3211,3214,3217),
);

$query_com = new WP_Query( $args_com );

?>

<div id="community" class="column-3">
	
	<?php if ($query_com->have_posts()) : while ($query_com->have_posts()) : $query_com->the_post(); ?>
	
	<div class="column">
	<a href="<?php the_permalink(); ?>">
	<?php $img = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' ); ?>
	
		<div class="community-item text-center" style="background-image: url(<?php echo $img[0]; ?>);">
			
			<div class="community-content">
				
				<h4><?php the_title(); ?></h4>
				
				<div class="caption">
					<?php the_excerpt(); ?>
				</div>
			</div>
			
		</div>
	</a>
	</div><!--/.column-->
	
	
	<?php endwhile; endif; wp_reset_postdata(); ?>

	

</div><!--#community-->