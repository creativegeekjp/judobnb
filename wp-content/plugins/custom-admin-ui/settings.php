<?php
add_action( 'admin_menu', 'custom_admin_ui_add_admin_menu' );
add_action( 'admin_init', 'custom_admin_ui_settings_init' );
function custom_admin_ui_add_admin_menu(  ) {
	add_options_page( 'Custom Admin UI', 'Custom Admin UI', 'manage_options', 'custom_admin_ui', 'custom_admin_ui_options_page' );
}
function custom_admin_ui_settings_exist(  ) {
	if( false == get_option( 'custom_admin_ui_settings' ) ) {
		add_option( 'custom_admin_ui_settings' );
	}
}
function custom_admin_ui_settings_init(  ) {
	register_setting( 'pluginPage', 'custom_admin_ui_settings' );
	add_settings_section(
		'custom_admin_ui_pluginPage_section',
		__( 'Settings', 'custom-admin-ui' ),
		'custom_admin_ui_settings_section_callback',
		'pluginPage'
	);
	add_settings_field(
		'custom_admin_ui_text_field_8',
		__( 'Page Title', 'custom-admin-ui' ),
		'custom_admin_ui_text_field_8_render',
		'pluginPage',
		'custom_admin_ui_pluginPage_section'
	);
	add_settings_field(
		'custom_admin_ui_checkbox_field_7',
		__( 'WordPress Logo', 'custom-admin-ui' ),
		'custom_admin_ui_checkbox_field_7_render',
		'pluginPage',
		'custom_admin_ui_pluginPage_section'
	);
	add_settings_field(
		'custom_admin_ui_checkbox_field_5',
		__( 'Screen Options Tab', 'custom-admin-ui' ),
		'custom_admin_ui_checkbox_field_5_render',
		'pluginPage',
		'custom_admin_ui_pluginPage_section'
	);
	add_settings_field(
		'custom_admin_ui_checkbox_field_4',
		__( 'Help Tab', 'custom-admin-ui' ),
		'custom_admin_ui_checkbox_field_4_render',
		'pluginPage',
		'custom_admin_ui_pluginPage_section'
	);
	add_settings_field(
		'custom_admin_ui_text_field_0',
		__( 'Left Footer Text', 'custom-admin-ui' ),
		'custom_admin_ui_text_field_0_render',
		'pluginPage',
		'custom_admin_ui_pluginPage_section'
	);
	add_settings_field(
		'custom_admin_ui_text_field_1',
		__( 'Right Footer Text', 'custom-admin-ui' ),
		'custom_admin_ui_text_field_1_render',
		'pluginPage',
		'custom_admin_ui_pluginPage_section'
	);
	add_settings_field(
		'custom_admin_ui_checkbox_field_6',
		__( 'Color Scheme Picker', 'custom-admin-ui' ),
		'custom_admin_ui_checkbox_field_6_render',
		'pluginPage',
		'custom_admin_ui_pluginPage_section'
	);
	add_settings_field(
		'custom_admin_ui_checkbox_field_9',
		__( 'Toolbar', 'custom-admin-ui' ),
		'custom_admin_ui_checkbox_field_9_render',
		'pluginPage',
		'custom_admin_ui_pluginPage_section'
	);
}
function custom_admin_ui_text_field_8_render(	) {
	$options = get_option( 'custom_admin_ui_settings' );
	?>
	<input type='text' name='custom_admin_ui_settings[custom_admin_ui_text_field_8]' value='<?php echo $options['custom_admin_ui_text_field_8']; ?>' class='regular-text'>
	<?php
}
function custom_admin_ui_checkbox_field_7_render(	) {
	$options = get_option( 'custom_admin_ui_settings' );
	?>
	<input type='checkbox' name='custom_admin_ui_settings[custom_admin_ui_checkbox_field_7]' <?php checked( $options['custom_admin_ui_checkbox_field_7'], 1 ); ?> value='1'>
	Remove the WordPress Logo from the toolbar
	<?php
}
function custom_admin_ui_checkbox_field_5_render(	) {
	$options = get_option( 'custom_admin_ui_settings' );
	?>
	<input type='checkbox' name='custom_admin_ui_settings[custom_admin_ui_checkbox_field_5]' <?php checked( $options['custom_admin_ui_checkbox_field_5'], 1 ); ?> value='1'>
	Hiding the Screen Options Tab
	<?php
}
function custom_admin_ui_checkbox_field_6_render(	) {
	$options = get_option( 'custom_admin_ui_settings' );
	?>
	<input type='checkbox' name='custom_admin_ui_settings[custom_admin_ui_checkbox_field_6]' <?php checked( $options['custom_admin_ui_checkbox_field_6'], 1 ); ?> value='1'>
	Remove the Admin Color Scheme Picker from the profile page
	<?php
}
function custom_admin_ui_text_field_0_render(	) {
	$options = get_option( 'custom_admin_ui_settings' );
	?>
	<input type='text' name='custom_admin_ui_settings[custom_admin_ui_text_field_0]' value='<?php echo $options['custom_admin_ui_text_field_0']; ?>' class='regular-text'>
	<?php
}
function custom_admin_ui_text_field_1_render(	) {
	$options = get_option( 'custom_admin_ui_settings' );
	?>
	<input type='text' name='custom_admin_ui_settings[custom_admin_ui_text_field_1]' value='<?php echo $options['custom_admin_ui_text_field_1']; ?>' class='regular-text'>
	<?php
}
function custom_admin_ui_checkbox_field_4_render(	) {
	$options = get_option( 'custom_admin_ui_settings' );
	?>
	<input type='checkbox' name='custom_admin_ui_settings[custom_admin_ui_checkbox_field_4]' <?php checked( $options['custom_admin_ui_checkbox_field_4'], 1 ); ?> value='1'>
	Hide Admin Help Tab
	<?php
}
function custom_admin_ui_checkbox_field_9_render(	) {
	$options = get_option( 'custom_admin_ui_settings' );
	?>
	<input type='checkbox' name='custom_admin_ui_settings[custom_admin_ui_checkbox_field_9]' <?php checked( $options['custom_admin_ui_checkbox_field_9'], 1 ); ?> value='1'>
	Using the white Toolbar
	<?php
}

function custom_admin_ui_settings_section_callback(  ) {
	echo __( 'Customize the Admin User Interface of WordPress.', 'custom-admin-ui' );
}
function custom_admin_ui_options_page(	) {
	?>
	<div class="wrap">
	<h2>Custom Admin UI</h2>
	<form action='options.php' method='post'>
		<?php
		settings_fields( 'pluginPage' );
		do_settings_sections( 'pluginPage' );
		submit_button();
		?>
	</form>
	</div>
	<?php
}
?>