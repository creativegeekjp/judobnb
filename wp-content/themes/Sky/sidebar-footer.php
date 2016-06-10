<?php
/**
 * The Footer widget areas.
 */
?>

<?php
/* The footer widget area is triggered if any of the areas
	 * have widgets. So let's check that first.
	 *
	 */
if ( ! is_active_sidebar( 'sidebar-1' )
	&& ! is_active_sidebar( 'sidebar-2' )
	&& ! is_active_sidebar( 'sidebar-3' )
	&& ! is_active_sidebar( 'sidebar-4' )
)
	return;

// How many footer columns to show?
$footer_columns = get_option( 'vh_footer_columns' );
if ( $footer_columns == false ) {
	$footer_columns = 4;
}

$class = ' span12 ';
if ( $footer_columns == 4 ) {
	$class = ' vc_col-sm-3 ';
} elseif ( $footer_columns == 3 ) {
	$class = ' vc_col-sm-4 ';
} elseif ( $footer_columns == 2 ) {
	$class = ' vc_col-sm-6 ';
}

// If we get this far, we have widgets. Let do this.
?>
	<?php if ( is_active_sidebar( 'sidebar-1' ) && $footer_columns >= 1 ) { ?>
	<div id="first" class="widget-area footer-links <?php echo $class; if ( $footer_columns == 1 ) echo 'last'; ?>" role="complementary">
		<?php dynamic_sidebar( 'sidebar-1' ); ?>
		<h4 class="currency"><?php _e('Currencies','widgets'); ?></h4><br>
		<select id='lang_sel_click' onchange="javascript:location.href = this.value;" style="border:0px grey; background-color:white; font-family: helvetica; font-size: 12px; color: black; width:150px;">
		    <option value="/toyen.php" <?php if($_COOKIE['C_CURRENCY']=='JPY' || $_COOKIE['C_CURRENCY']=='') echo "selected=selected";  ?> >JPY</option>
		    <option value="/todollar.php" <?php if($_COOKIE['C_CURRENCY']=='USD') echo "selected=selected"; ?> >USD</option>
		</select>
	</div><!-- #first .widget-area -->
	<?php } ?>

	<?php if ( is_active_sidebar( 'sidebar-2' ) && $footer_columns >= 2 ) { ?>
	<div id="second" class="widget-area footer-links <?php echo $class; if ( $footer_columns == 2 ) echo 'last'; ?>" role="complementary">
		<?php dynamic_sidebar( 'sidebar-2' ); ?>
	</div><!-- #second .widget-area -->
	<?php } ?>

	<?php if ( is_active_sidebar( 'sidebar-3' ) && $footer_columns >= 3 ) { ?>
	<div id="third" class="widget-area footer-links <?php echo $class; if ( $footer_columns == 3 ) echo 'last'; ?>" role="complementary">
		<?php dynamic_sidebar( 'sidebar-3' ); ?>
	</div><!-- #third .widget-area -->
	<?php } ?>

	<?php if ( is_active_sidebar( 'sidebar-4' ) && $footer_columns >= 4 ) { ?>
	<div id="fourth" class="widget-area footer-links <?php echo $class; ?> fourth last" role="complementary">
		<?php dynamic_sidebar( 'sidebar-4' ); ?>
	</div><!-- #fourth .widget-area -->
	<?php }