<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class Profile_editor_Settings {

	/**
	 * The single instance of Profile_editor_Settings.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * The main plugin object.
	 * @var 	object
	 * @access  public
	 * @since 	1.0.0
	 */
	public $parent = null;

	/**
	 * Prefix for plugin settings.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $base = '';

	/**
	 * Available settings for plugin.
	 * @var     array
	 * @access  public
	 * @since   1.0.0
	 */
	public $settings = array();

	public function __construct ( $parent ) {
		$this->parent = $parent;

		$this->base = 'pe_';

		// Initialise settings
		add_action( 'init', array( $this, 'init_settings' ), 11 );

		// Register plugin settings
		add_action( 'admin_init' , array( $this, 'register_settings' ) );

		// Add settings page to menu
		add_action( 'admin_menu' , array( $this, 'add_menu_item' ) );

		// Add settings link to plugins page
		add_filter( 'plugin_action_links_' . plugin_basename( $this->parent->file ) , array( $this, 'add_settings_link' ) );

		// Add Profile editor shortcodes
		add_shortcode('profile_editor_form', array( $this, 'profile_editor_form' ));
		add_shortcode('profile_editor_login_form', array( $this, 'profile_editor_login_form' ));
		add_shortcode('profile_editor_register_form', array( $this, 'profile_editor_register_form' ));

		// Add Profile editor actions
		add_action('template_redirect', array( $this, 'profile_editor_login_form_login' ));
		add_action('show_user_profile', array( $this, 'profile_editor_user_profile' ));
		add_action('profile_update', array( $this, 'profile_editor_user_profile_updated' ));
	}

	/**
	 * Initialise settings
	 * @return void
	 */
	public function init_settings () {
		$this->settings = $this->settings_fields();
	}

	/**
	 * Add settings page to admin menu
	 * @return void
	 */
	public function add_menu_item () {
		$page = add_options_page( __( 'Profile editor', 'profile-editor' ) , __( 'Profile editor', 'profile-editor' ) , 'manage_options' , $this->parent->_token . '_settings' ,  array( $this, 'settings_page' ) );
		add_action( 'admin_print_styles-' . $page, array( $this, 'settings_assets' ) );
	}

	/**
	 * Load settings JS & CSS
	 * @return void
	 */
	public function settings_assets () {

		// We're including the farbtastic script & styles here because they're needed for the colour picker
		// If you're not including a colour picker field then you can leave these calls out as well as the farbtastic dependency for the wpt-admin-js script below
		wp_enqueue_style( 'farbtastic' );
		wp_enqueue_script( 'farbtastic' );

		// We're including the WP media scripts here because they're needed for the image upload field
		// If you're not including an image upload then you can leave this function call out
		wp_enqueue_media();

		wp_register_script( $this->parent->_token . '-settings-js', $this->parent->assets_url . 'js/settings' . $this->parent->script_suffix . '.js', array( 'farbtastic', 'jquery' ), '1.0.0' );
		wp_enqueue_script( $this->parent->_token . '-settings-js' );
	}

	/**
	 * Add settings link to plugin list table
	 * @param  array $links Existing links
	 * @return array 		Modified links
	 */
	public function add_settings_link ( $links ) {
		$settings_link = '<a href="options-general.php?page=' . $this->parent->_token . '_settings">' . __( 'Settings', 'profile-editor' ) . '</a>';
		array_push( $links, $settings_link );
		return $links;
	}

	/**
	 * Build settings fields
	 * @return array Fields to be displayed on settings page
	 */
	private function settings_fields () {

		$settings['settings'] = array(
			'title'					=> __( 'Settings', 'profile-editor' ),
			'description'			=> __( 'These are your standard settings.', 'profile-editor' ),
			'fields'				=> array(
				array(
					'id' 			=> 'login_page_url',
					'label'			=> __( 'Login page URL' , 'profile-editor' ),
					'description'	=> __( 'URL to a page where you placed the login form shortcode - [profile_editor_login_form].', 'profile-editor' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> __( 'http://example.com', 'profile-editor' )
				),
				array(
					'id' 			=> 'register_page_url',
					'label'			=> __( 'Register page URL' , 'profile-editor' ),
					'description'	=> __( 'URL to a page where you placed the register form shortcode - [profile_editor_register_form].', 'profile-editor' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> __( 'http://example.com', 'profile-editor' )
				),
				array(
					'id' 			=> 'redirect_url',
					'label'			=> __( 'Login redirect URL' , 'profile-editor' ),
					'description'	=> __( 'URL to a page where you want to redirect users after login. Blank - homepage', 'profile-editor' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> __( 'http://example.com', 'profile-editor' )
				),
				array(
					'id' 			=> 'not_logged_in',
					'label'			=> __( 'Not logged in text' , 'profile-editor' ),
					'description'	=> __( 'This text will be displayed to users when they\'re not logged in.', 'profile-editor' ),
					'type'			=> 'textarea',
					'default'		=> 'You need to <a href="%LOGIN_URL%">login</a> to access this page.',
					'placeholder'	=> __( 'You need to <a href="%LOGIN_URL%">login</a> to access this page.', 'profile-editor' )
				),
				array(
					'id' 			=> 'conformation_email_template',
					'label'			=> __( 'Confirmation email template' , 'profile-editor' ),
					'description'	=> __( 'If you want to customize the email sent after registration, you can do that here.<br> Available tags: ', 'profile-editor' )."%BLOGNAME%, %BLOGURL%, %USERNAME%, %PASSWORD%",
					'type'			=> 'textarea',
					'default'		=> '%USERNAME%, great to see you decided to join %BLOGNAME%.&#13;&#10;<br><br>&#13;&#10;Your username: <strong>%USERNAME%</strong><br>&#13;&#10;Your password: <strong>%PASSWORD%</strong>&#13;&#10;<br><br>&#13;&#10;Hope everything goes well, your <a href=\'%BLOGURL%\'>%BLOGNAME%</a>',
					'placeholder'	=> __( 'Email template.', 'profile-editor' )
				),
				array(
					'id' 			=> 'profile_editor_form',
					'label'			=> __( 'Profile editor shortcode style', 'profile-editor' ),
					'description'	=> __( 'Choose what kind of style will be applied.', 'profile-editor' ),
					'type'			=> 'select',
					'options'		=> array( 'default' => 'Default', 'style1' => 'Slick blue', 'style2' => 'Bulky gray' ),
					'default'		=> 'default'
				)
			)
		);

		$settings['fields'] = array(
			'title'					=> __( 'Fields', 'profile-editor' ),
			'description'			=> __( 'This section if for adding new user fields and editing existing ones.', 'profile-editor' ),
			'fields'				=> array(
				array(
					'id' 			=> 'fake_field',
					'label'			=> '',
					'description'	=> '',
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> ''
				)
			)
		);

		$settings = apply_filters( $this->parent->_token . '_settings_fields', $settings );

		return $settings;
	}
	
	function profile_editor_user_profile() {
		$output = '';
		$user_id = get_current_user_id();

		if ( current_user_can( 'edit_user', $user_id ) ) {
			$output .= $this->pe_frontend_user_fields( $user_id, 'default', 'profile' );
		}

		echo $output;
	}

	function profile_editor_user_profile_updated() {
		$user_id = get_current_user_id();

		if ( is_admin() ) {
			if ( isset($_POST) && $_POST['form_action'] == 'edit_user_profile' ) {
				$this->pe_update_user_settings( $user_id );
			}
		}
	}

	function profile_editor_login_form_login() {
		$output = '';
		if ( isset( $_POST['pe-login-security'] ) && wp_verify_nonce($_POST['pe-login-security'], 'pe-login-nonce') ) {
			if ( isset($_POST['pe-rememberme']) && $_POST['pe-rememberme'] == 'on' ) {
				$remember = true;
			} else {
				$remember = false;
			}

			$info                  = array();
			$info['user_login']    = sanitize_user($_POST['pe-login']);
			$info['user_password'] = $_POST['pe-password'];
			$info['remember']      = $remember;

			$user_signon = wp_signon( $info, false );

			if ( is_wp_error($user_signon) ) {
				$error = $user_signon->get_error_codes();
				if ( in_array('invalid_username', $error) ) {
					$output = json_encode(array('loggedin' => false, 'message' => __('Please check your username', "profile-editor" )));
				} elseif ( in_array('incorrect_password', $error) ) {
					$output = json_encode(array('loggedin' => false, 'message' => __('Please check your password', "profile-editor" )));
				} elseif ( in_array('empty_password', $error) ) {
					$output = json_encode(array('loggedin' => false, 'message' => __('Please enter password', "profile-editor" )));
				} elseif ( in_array('empty_username', $error) ) {
					$output = json_encode(array('loggedin' => false, 'message' => __('Please enter username', "profile-editor" )));
				} else {
					$output = json_encode(array('loggedin' => false, 'message' => $error));
				}
			} else {
				if ( get_option('pe_redirect_url') == '' ) {
					$redirect = home_url();
				} else {
					$redirect = get_option('pe_redirect_url');
				}
				
				wp_redirect( $redirect );
			}
		}

		return $output;
	}

	function profile_editor_login_form($atts, $content = null, $code) {
		if ( !is_user_logged_in() ) {
			$login_json = $this->profile_editor_login_form_login();
			$login_error = json_decode($login_json);
			$form_style = get_option('pe_profile_editor_form');
			$output = '';
			if ( $login_error != null && !empty($login_error->message) ) {
				$output .= '<span class="pe-error-message"><strong>'.__('ERROR: ').'</strong>'.$login_error->message.'</span>';
			}

			if ( $form_style != 'default' ) {
				$output .= '
				<form id="login" action="" method="post">
					<div class="pe-edit-form login '.$form_style.'">
						<span class="pe-form-heading"><strong>'.__('User', 'profile-editor').'</strong> '.__('login', 'profile-editor').'</span>
						<div class="pe-edit-item">
							<label for="pe-username">'.__('Username', 'profile-editor').'</label>
							<input type="text" name="pe-login" placeholder="'.__( 'Username', 'profile-editor' ).'">
						</div>
						<div class="pe-edit-item">
							<label for="pe-display-name">'.__('Password', 'profile-editor').'</label>
							<input type="password" name="pe-password" placeholder="'.__( 'Password', 'profile-editor' ).'">
						</div>
						<div class="pe-edit-row">
							<div class="pe-edit-item">
								<label class="pe-checkbox" for="pe-rememberme">
									<span class="pe-fake-checkbox"></span>
									<input type="checkbox" id="pe-rememberme" name="pe-rememberme">
									'.__( 'Remember me', 'profile-editor' ).'
								</label>
							</div>
							<div class="pe-edit-item">
								<a href="'.wp_lostpassword_url().'">'.__( 'Forgot password?', 'profile-editor' ).'</a>';
								if ( get_option('pe_register_page_url') != '' ) {
									$output .= '<a href="'.get_option('pe_register_page_url').'">'.__( 'Not a member?', 'profile-editor' ).'</a>';
								}
								ob_start();
								wp_nonce_field( 'pe-login-nonce', 'pe-login-security' );
								$output .= ob_get_clean();
							$output .= '
							</div>
							<div class="clearfix"></div>
						</div>
						<input class="grey-button" type="submit" name="commit" value="'.__( 'Sign In', 'profile-editor' ).'">
					</div>
				</form>';
			} else {
				$output .= '
				<form class="pe-login-form" id="login" action="" method="post">
					<input type="text" name="pe-login" placeholder="'.__( 'Username', 'profile-editor' ).'"><br>
					<input type="password" name="pe-password" placeholder="'.__( 'Password', 'profile-editor' ).'"><br>
					<input type="checkbox" id="pe-rememberme" class="rememberme" name="pe-rememberme">
					<label for="pe-rememberme">'.__( 'Remember me', 'profile-editor' ).'</label><br>
					<a href="'.wp_lostpassword_url().'">'.__( 'Forgot password?', 'profile-editor' ).'</a>';
					if ( get_option('pe_register_page_url') != '' ) {
						$output .= '<a href="'.get_option('pe_register_page_url').'">'.__( 'Not a member?', 'profile-editor' ).'</a>';
					}
					ob_start();
					wp_nonce_field( 'pe-login-nonce', 'pe-login-security' );
					$output .= ob_get_clean();
					$output .= '
					<input class="grey-button" type="submit" name="commit" value="'.__( 'Sign In', 'profile-editor' ).'">
				</form>';
			}
		} else {
			$current_user = wp_get_current_user();
			$output = __('Logged in as', 'profile-editor').' '.$current_user->data->display_name.', '.__('not you?', 'profile-editor').' <a href="'.wp_logout_url( get_the_permalink() ).'">'.__('Log out', 'profile-editor').'<a>.';
		}

		return $output;
	}

	function profile_editor_register_form_register() {
		$output = '';
		if ( isset( $_POST['pe-register-security'] ) && wp_verify_nonce($_POST['pe-register-security'], 'pe-register-nonce') ) {

			$username = sanitize_user($_POST['pe-username']);
			$password = wp_generate_password();
			$email = esc_attr($_POST['pe-user-email']);

			if ( is_email( $email ) ) {
				$user_register = wp_create_user( $username, $password, $email );

				if ( is_wp_error( $user_register ) ) {
					$error  = $user_register->get_error_codes();
					if ( in_array('empty_user_login', $error) ) {
						$output = json_encode(array('loggedin'=>false, 'message'=>__('Enter your username.', 'profile-editor')));
					} elseif(in_array('existing_user_login',$error)) {
						$output = json_encode(array('loggedin'=>false, 'message'=>__('Username already exists.', 'profile-editor')));
					} elseif(in_array('email_exists',$error)) {
						$output = json_encode(array('loggedin'=>false, 'message'=>__('Email already exists.', 'profile-editor')));
					} elseif(in_array('empty_email',$error)) {
						$output = json_encode(array('loggedin'=>false, 'message'=>__('Enter email.', 'profile-editor')));
					} elseif(in_array('empty_username',$error)) {
						$output = json_encode(array('loggedin'=>false, 'message'=>__('Enter username.', 'profile-editor')));
					} else {
						$output = json_encode(array('loggedin'=>false, 'message'=>$error));
					}
				} else {
					$subject = get_bloginfo('name').' '.__('registration', 'profile-editor');
					$message = get_option('pe_conformation_email_template');
					if ( $message == '' ) {
						$message = '%USERNAME%, great to see you decided to join %BLOGNAME%.<br><br>Your username: <strong>%USERNAME%</strong><br>Your password: <strong>%PASSWORD%</strong><br><br>Hope everything goes well, your <a href="%BLOGURL%">%BLOGNAME%</a>';
					}
					$tag_array = array('%USERNAME%', '%BLOGNAME%', '%BLOGURL%', '%PASSWORD%');
					foreach ($tag_array as $tag_value) {
						switch ( $tag_value ) {
							case '%USERNAME%':
								$message = preg_replace($tag_value, $username, $message);
								break;
							case '%BLOGNAME%':
								$message = preg_replace($tag_value, get_bloginfo('name'), $message);
								break;
							case '%BLOGURL%':
								$message = preg_replace($tag_value, get_bloginfo('url'), $message);
								break;
							case '%PASSWORD%':
								$message = preg_replace($tag_value, $password, $message);
								break;
							default:
								//
								break;
						}
					}
					$message = preg_replace('/%+/', '', $message);
					$message = preg_replace('/\n+/', '', trim($message));

					add_filter('wp_mail_content_type', create_function('', 'return "text/html"; '));
					if ( wp_mail( $email, $subject, $message) ) {
						$success_msg = __('Your password has been emailed to ', 'profile-editor').'<strong>'.$email.'</strong>';
					} else {
						$success_msg = __('You are registered but something went wrong while sending an email to ', 'profile-editor').'<strong>'.$email.'</strong>';
					}

					if ( isset($_POST) && $_POST['form_action'] == 'edit_user_profile' ) {
						$this->pe_update_user_settings( $user_register );
					}

					$output = json_encode(array('loggedin'=>true, 'message'=>$success_msg));
				}
			} else {
				$output = json_encode(array('loggedin'=>false, 'message'=>__('Enter a valid email address.', 'profile-editor')));
			}
		}

		return $output;
	}

	function profile_editor_register_form($atts, $content = null, $code) {
		if ( !is_user_logged_in() ) {
			$register_json = $this->profile_editor_register_form_register();
			$form_style = get_option('pe_profile_editor_form');
			$set_username = $set_useremail = $output = '';

			if ( $register_json != '' ) {
				$register_error = json_decode($register_json);
				$pe_message = $register_error->message;

				if ( $register_error != null && $register_error->loggedin == false ) {
					$output .= '<span class="pe-error-message"><strong>'.__('ERROR: ').'</strong>'.$pe_message.'</span>';
				} elseif ( $register_error != null && $register_error->loggedin == true ) {
					$output .= '<span class="pe-success-message"><strong>'.__('Success: ').'</strong>'.$pe_message.'</span>';
				}

				if ( isset($_POST['pe-username']) && $register_error != null && $register_error->loggedin == false ) {
					$set_username = esc_attr($_POST['pe-username']);
				}

				if ( isset($_POST['pe-user-email']) && $register_error != null && $register_error->loggedin == false ) {
					$set_useremail = esc_attr($_POST['pe-user-email']);
				}
			}

			if ( $form_style != 'default' ) {
				$output .= '
				<form class="pe-register-form" id="register" action="" method="post">
					<div class="pe-edit-form register '.$form_style.'">
						<span class="pe-form-heading"><strong>'.__('User', 'profile-editor').'</strong> '.__('registration', 'profile-editor').'</span>
						<div class="pe-edit-item">
							<label for="pe-username">'.__('Username', 'profile-editor').'</label>
							<input type="text" id="pe-username" name="pe-username" placeholder="'.__( 'Username', 'profile-editor' ).'" value="'.$set_username.'">
						</div>
						<div class="pe-edit-item">
							<label for="pe-display-name">'.__('Email', 'profile-editor').'</label>
							<input type="text" id="pe-user-email" name="pe-user-email" placeholder="'.__( 'Email', 'profile-editor' ).'" value="'.$set_useremail.'">
							<span class="pe-description">'.__( 'A password will be emailed to you for future use.', 'profile-editor' ).'</span>
						</div>';
						ob_start();
						wp_nonce_field( 'pe-register-nonce', 'pe-register-security' );
						$output .= $this->pe_frontend_register_fields( $form_style );
						$output .= ob_get_clean();
						$output .= '
					</div>';
			} else {
				$output .= '
				<form class="pe-register-form" id="register" action="" method="post">
					<input type="text" id="pe-username" name="pe-username" placeholder="'.__( 'Username', 'profile-editor' ).'" value="'.$set_username.'"><br>
					<input type="text" id="pe-user-email" name="pe-user-email" placeholder="'.__( 'Email', 'profile-editor' ).'" value="'.$set_useremail.'"><br>
					<p>'.__( 'A password will be emailed to you for future use.', 'profile-editor' ).'</p>';
					ob_start();
					wp_nonce_field( 'pe-register-nonce', 'pe-register-security' );
					$output .= $this->pe_frontend_register_fields( $form_style );
					$output .= ob_get_clean();
			}
		} else {
			$current_user = wp_get_current_user();
			$output = __('Logged in as', 'profile-editor').' '.$current_user->data->display_name.', '.__('not you?', 'profile-editor').' <a href="'.wp_logout_url( get_the_permalink() ).'">'.__('Log out', 'profile-editor').'<a>.';
		}

		return $output;
	}

	public function pe_frontend_register_fields ( $form_style ) {
		global $wpdb;

		$sql = "SELECT * FROM ".$wpdb->prefix."profile_editor_fields ORDER BY F_ORDER";
		$field_list = $wpdb->get_results($sql);
		$i = 1;
		$field_html = $html = '';
		$current_user_meta = get_user_meta( '-1' );

		if ( $form_style == 'default' ) {

		} else {
			$html .= '
			<div id="user-profile-extra-fields" class="pe-edit-form extra '.$form_style.'">
				<span class="pe-form-heading"><strong>'.__('Extra', 'profile-editor').'</strong> '.__('fields', 'profile-editor').'</span>' ;
		}
		

		foreach ($field_list as $added_value) {
			$added_rule = json_decode($added_value->RULES);
			if ( $added_rule->field_registration == 'on' ) {
				switch ( $added_value->TYPE ) {
					case 'text':
						$rules_html = $this->pe_get_field_rules( $added_value->RULES );
						$current_user_value = $added_value->VALUE;
						$field_html = '<input type="text" id="pe-'.$added_value->NAME.'" name="author_'.$added_value->NAME.'" value="'.$current_user_value.'" placeholder="'.$added_value->PLACEHOLDER.'" '.$rules_html.'>';
						if ( $added_value->DESCRIPTION != '' ) {
							if ( $form_style == 'default' ) {
								$field_html .= '<p>'.$added_value->DESCRIPTION.'</p>';
							} else {
								$field_html .= '<span class="pe-description">'.$added_value->DESCRIPTION.'</span>';
							}
						}
						break;
					case 'textarea':
						$rules_html = $this->pe_get_field_rules( $added_value->RULES );
						$current_user_value = $added_value->VALUE;
						$field_html = '<textarea id="pe-'.$added_value->NAME.'" name="author_'.$added_value->NAME.'" placeholder="'.$added_value->PLACEHOLDER.'" '.$rules_html.'>'.$current_user_value.'</textarea>';
						if ( $added_value->DESCRIPTION != '' ) {
							if ( $form_style == 'default' ) {
								$field_html .= '<p>'.$added_value->DESCRIPTION.'</p>';
							} else {
								$field_html .= '<span class="pe-description">'.$added_value->DESCRIPTION.'</span>';
							}
						}
						break;
					case 'checkbox':
						$rules_html = $this->pe_get_field_rules( $added_value->RULES );
						$current_user_value = $added_value->VALUE;
						$field_state = $checkbox_active = '';
						if ( $current_user_value == 'on' ) {
							$field_state = 'checked="checked"';
							$checkbox_active = 'pe-active';
						}
						if ( $form_style != 'default' ) {
							$field_html = '
							<label class="pe-checkbox '.$checkbox_active.'" for="pe-'.$added_value->NAME.'">
								<span class="pe-fake-checkbox"></span>
								<input type="checkbox" id="pe-'.$added_value->NAME.'" name="author_'.$added_value->NAME.'" '.$field_state.'">
								<input type="hidden" name="author_'.$added_value->NAME.'" value="'.$current_user_value.'" '.$rules_html.'>
								'.$added_value->LABEL.'
							</label>';
						} else {
							$field_html = '<input type="checkbox" id="pe-'.$added_value->NAME.'" name="author_'.$added_value->NAME.'" '.$field_state.'">
										<input type="hidden" name="author_'.$added_value->NAME.'" value="'.$current_user_value.'" '.$rules_html.'>';
						}
						if ( $added_value->DESCRIPTION != '' ) {
							if ( $form_style == 'default' ) {
								$field_html .= '<p>'.$added_value->DESCRIPTION.'</p>';
							} else {
								$field_html .= '<span class="pe-description">'.$added_value->DESCRIPTION.'</span>';
							}
						}
						break;
					case 'radio':
						$rules_html = $this->pe_get_field_rules( $added_value->RULES );
						$current_user_value = '';
						$field_html = '';
						$radio = explode(',', $added_value->VALUE);
						if ( $form_style != 'default' ) {
							$field_html .= '<label for="pe-'.$added_value->NAME.'">'.$added_value->LABEL.'</label>';
						}
						foreach ($radio as $radio_value) {
							$checked = $radio_active = '';
							if ( $current_user_value == $radio_value ) {
								$checked = 'checked';
								$radio_active = 'pe-active';
							}
							if ( $form_style != 'default' ) {
								$field_html .= '
								<label class="pe-radio '.$radio_active.'">
									<span class="pe-fake-radio"></span>
									<input type="radio" name="author_'.$added_value->NAME.'" value="'.$radio_value.'" '.$checked.' '.$rules_html.'>'.$radio_value.'
								</label>';
							} else {
								$field_html .= '<input type="radio" name="author_'.$added_value->NAME.'" value="'.$radio_value.'" '.$checked.' '.$rules_html.'>'.$radio_value.'<br>';
							}
						}
						if ( $added_value->DESCRIPTION != '' ) {
							if ( $form_style == 'default' ) {
								$field_html .= '<p>'.$added_value->DESCRIPTION.'</p>';
							} else {
								$field_html .= '<span class="pe-description">'.$added_value->DESCRIPTION.'</span>';
							}
						}
						break;
					case 'picture':
						$rules_html = $this->pe_get_field_rules( $added_value->RULES );
						$field_html = '';
						$file_path = __('No file selected', 'profile-editor');
						$field_name = 'author_'.$added_value->NAME;
						if ( $form_style != 'default' ) {
							$field_html .= '
							<div class="pe-styled-upload">
								<button>'.__('Choose file', 'profile-editor').'</button>
								<span class="pe-file-path">'.$file_path.'</span>
								<input type="file" id="pe-'.$added_value->NAME.'" name="author_'.$added_value->NAME.'" id="author_'.$added_value->NAME.'" multiple="false" '.$rules_html.' />
								<div class="clearfix"></div>
							</div>';
						} else {
							$field_html .= '<input type="file" id="pe-'.$added_value->NAME.'" name="author_'.$added_value->NAME.'" id="author_'.$added_value->NAME.'" multiple="false" '.$rules_html.' />';
						}
						ob_start();
						wp_nonce_field( $field_name, $field_name.'_nonce' );
						$field_html .= ob_get_clean();
						if ( $added_value->DESCRIPTION != '' ) {
							if ( $form_style == 'default' ) {
								$field_html .= '<p>'.$added_value->DESCRIPTION.'</p>';
							} else {
								$field_html .= '<span class="pe-description">'.$added_value->DESCRIPTION.'</span>';
							}
						}
						break;
					case 'password':
						$rules_html = $this->pe_get_field_rules( $added_value->RULES );
						$current_user_value = $added_value->VALUE;
						$field_html = '<input type="password" id="pe-'.$added_value->NAME.'" name="author_'.$added_value->NAME.'" value="'.$current_user_value.'" placeholder="'.$added_value->PLACEHOLDER.'" '.$rules_html.'>';
						if ( $added_value->DESCRIPTION != '' ) {
							if ( $form_style == 'default' ) {
								$field_html .= '<p>'.$added_value->DESCRIPTION.'</p>';
							} else {
								$field_html .= '<span class="pe-description">'.$added_value->DESCRIPTION.'</span>';
							}
						}
						break;
					case 'file':
						$rules_html = $this->pe_get_field_rules( $added_value->RULES );
						$field_html = '';
						$field_name = 'author_'.$added_value->NAME;
						if ( $form_style != 'default' ) {
							$field_html .= '
							<div class="pe-styled-upload">
								<button>'.__('Choose file', 'profile-editor').'</button>
								<span class="pe-file-path">'.$file_path.'</span>
								<input type="file" id="pe-'.$added_value->NAME.'" name="author_'.$added_value->NAME.'" id="author_'.$added_value->NAME.'" multiple="false" '.$rules_html.' />
								<div class="clearfix"></div>
							</div>';
						} else {
							$field_html .= '<input type="file" name="author_'.$added_value->NAME.'" id="author_'.$added_value->NAME.'" multiple="false" '.$rules_html.' />';
						}
						ob_start();
						wp_nonce_field( $field_name, $field_name.'_nonce' );
						$field_html .= ob_get_clean();
						if ( $added_value->DESCRIPTION != '' ) {
							if ( $form_style == 'default' ) {
								$field_html .= '<p>'.$added_value->DESCRIPTION.'</p>';
							} else {
								$field_html .= '<span class="pe-description">'.$added_value->DESCRIPTION.'</span>';
							}
						}
						break;
					case 'dropdown':
						$rules_html = $this->pe_get_field_rules( $added_value->RULES );
						$current_user_value = '';
						$field_html = '<select id="pe-'.$added_value->NAME.'" name="author_'.$added_value->NAME.'" '.$rules_html.'>';
						$dropdown = explode(',', $added_value->VALUE);
						foreach ($dropdown as $dropdown_value) {
							$selected = '';
							if ( $current_user_value == $dropdown_value ) {
								$selected = 'selected="selected"';
							}
							$field_html .= '<option value="'.$dropdown_value.'" '.$selected.'>'.$dropdown_value.'</option>';
						}
						$field_html .= '/<select>';
						if ( $added_value->DESCRIPTION != '' ) {
							if ( $form_style == 'default' ) {
								$field_html .= '<p>'.$added_value->DESCRIPTION.'</p>';
							} else {
								$field_html .= '<span class="pe-description">'.$added_value->DESCRIPTION.'</span>';
							}
						}
						break;
					case 'hidden':
						$rules_html = $this->pe_get_field_rules( $added_value->RULES );
						$current_user_value = $added_value->VALUE;
						$field_html = '<input type="hidden" name="author_'.$added_value->NAME.'" value="'.$current_user_value.'" '.$rules_html.'>';
						break;
					case 'WYSIWYG':
						$current_user_value = $added_value->VALUE;
						$editor_settings = array('textarea_name' => 'author_'.$added_value->NAME, 'media_buttons' => false);
						ob_start();
						wp_editor( $current_user_value, 'author_'.$added_value->NAME, $editor_settings );
						$field_html = ob_get_clean();
						if ( $added_value->DESCRIPTION != '' ) {
							if ( $form_style == 'default' ) {
								$field_html .= '<p>'.$added_value->DESCRIPTION.'</p>';
							} else {
								$field_html .= '<span class="pe-description">'.$added_value->DESCRIPTION.'</span>';
							}
						}
						break;
					default:
						$field_html = '';
						break;
				}

				if ( $added_value->TYPE != 'hidden' ) {
					if ( $form_style == 'default' ) {
						$html .= '
							'.$added_value->LABEL.'
							'.$field_html.'
						';
					} else {
						if ( $added_value->TYPE == 'checkbox' || $added_value->TYPE == 'radio' ) {
							$html .= '
							<div class="pe-edit-item">
								'.$field_html.'
							</div>';
						} else {
							$html .= '
							<div class="pe-edit-item">
								<label for="pe-'.$added_value->NAME.'">'.$added_value->LABEL.'</label>
								'.$field_html.'
							</div>';
						}
					}
				} else {
					$html .= $field_html;
				}
			}
		}

		if ( $form_style == 'default' ) {
					$html .= '
				<input type="hidden" name="form_action" value="register_user_profile">
				<input class="grey-button" type="submit" name="commit" value="'.__( 'Register', 'profile-editor' ).'">
			</form>';
		} else {
				$html .= '
				</div>
				<input type="hidden" name="form_action" value="register_user_profile">
				<input class="grey-button" type="submit" name="commit" value="'.__( 'Register', 'profile-editor' ).'">
			</form>';
		}

		return $html;
	}

	function profile_editor_form($atts, $content = null, $code) {
		extract( shortcode_atts( array(
			'height' => 10,
		), $atts ) );

		$login_page_url = get_option('pe_login_page_url');
		$not_logged_in = str_replace('%LOGIN_URL%', $login_page_url, get_option('pe_not_logged_in'));
		
		if ( !is_user_logged_in() ) {
			$output = $not_logged_in;
		} else {
			$current_user = wp_get_current_user();
			$current_style = get_option($this->base.'profile_editor_form');

			if ( isset($_POST) && isset($_POST['form_action']) && $_POST['form_action'] == 'edit_user_profile' ) {
				$this->pe_update_user_settings( $current_user->data->ID );
			}

			$output = '
			<form method="post" action="" id="pe-user-edit-form" enctype="multipart/form-data">';

			if ( $current_style != 'default' ) {
				$output .= '
				<div class="pe-edit-form name '.$current_style.'">
					<span class="pe-form-heading"><strong>'.__('User', 'profile-editor').'</strong> '.__('info', 'profile-editor').'</span>
					<div class="pe-edit-row">
						<div class="pe-edit-item">
							<label for="pe-username">'.__('Username', 'profile-editor').'</label>
							<input type="text" class="pe-read-only" value="'.$current_user->data->user_login.'" readonly>
							<span class="pe-description">'.__('Usernames cannot be changed.', 'profile-editor').'</span>
						</div>
						<div class="pe-edit-item">
							<label for="pe-display-name">'.__('Display name', 'profile-editor').'</label>
							<input type="text" id="pe-display-name" name="author_def_display_name" value="'.get_the_author_meta('display_name').'">
						</div>
						<div class="clearfix"></div>
					</div>
					<div class="pe-edit-row">
						<div class="pe-edit-item">
							<label for="pe-first-name">'.__('First name', 'profile-editor').'</label>
							<input type="text" id="pe-first-name" name="author_def_first_name" value="'.get_the_author_meta('first_name').'">
						</div>
						<div class="pe-edit-item">
							<label for="pe-last-name">'.__('Last name', 'profile-editor').'</label>
							<input type="text" id="pe-last-name" name="author_def_last_name" value="'.get_the_author_meta('last_name').'">
						</div>
						<div class="clearfix"></div>
					</div>
				</div>';

				$output .= '
				<div class="pe-edit-form contact '.$current_style.'">
					<span class="pe-form-heading"><strong>'.__('Contact', 'profile-editor').'</strong> '.__('info', 'profile-editor').'</span>
					<div class="pe-edit-item">
						<label for="pe-email">'.__('Email', 'profile-editor').'</label>
						<input type="text" id="pe-email" name="author_def_user_email" value="'.$current_user->data->user_email.'">
					</div>
					<div class="pe-edit-item">
						<label for="pe-website">'.__('Website', 'profile-editor').'</label>
						<input type="text" id="pe-website" name="author_def_user_url" value="'.$current_user->data->user_url.'">
					</div>
				</div>';

				$output .= '
				<div class="pe-edit-form yourself '.$current_style.'">
					<span class="pe-form-heading"><strong>'.__('About', 'profile-editor').'</strong> '.__('yourself', 'profile-editor').'</span>
					<div class="pe-edit-item">
						<label for="pe-user-description">'.__('Biographical info', 'profile-editor').'</label>
						<textarea id="pe-user-description" name="author_def_description">'.get_the_author_meta('description').'</textarea>
					</div>
					<div class="pe-edit-row">
						<div class="pe-edit-item">
							<label for="pe-password">'.__('New password', 'profile-editor').'</label>
							<input type="password" id="pe-password" name="author_pe_password" value="">
						</div>
						<div class="pe-edit-item">
							<label for="pe-repeat-password">'.__('Repeat new password', 'profile-editor').'</label>
							<input type="password" id="pe-repeat-password" name="author_pe_repeat_password" value="">
						</div>
						<div class="clearfix"></div>
					</div>
				</div>';
			} else {
				$output .= '
				<table class="form-table profile-editor">
					<tbody>
						<tr>
							<th>'.__('Name', 'profile-editor').'</th>
						</tr>
						<tr>
							<th>'.__('Username', 'profile-editor').'</th>
							<td>
								<input type="text" value="'.$current_user->data->user_login.'" readonly>
								<p>'.__('Usernames cannot be changed.', 'profile-editor').'</p>
							</td>
						</tr>
						<tr>
							<th>First name</th>
							<td>
								<input type="text" name="author_def_first_name" value="'.get_the_author_meta('first_name').'">
							</td>
						</tr>
						<tr>
							<th>Last name</th>
							<td>
								<input type="text" name="author_def_last_name" value="'.get_the_author_meta('last_name').'">
							
							</td>
						</tr>
						<tr>
							<th>Display name</th>
							<td>
								<input type="text" name="author_def_display_name" value="'.get_the_author_meta('display_name').'">
							
							</td>
						</tr>
					</tbody>
				</table>';

				$output .= '
				<table class="form-table profile-editor">
					<tbody>
						<tr>
							<th>Contact info</th>
						</tr>
						<tr>
							<th>Email</th>
							<td>
								<input type="text" name="author_def_user_email" value="'.$current_user->data->user_email.'">
							</td>
						</tr>
						<tr>
							<th>Website</th>
							<td>
								<input type="text" name="author_def_user_url" value="'.$current_user->data->user_url.'">
							</td>
						</tr>
					</tbody>
				</table>';

				$output .= '
				<table class="form-table profile-editor">
					<tbody>
						<tr>
							<th>About yourself</th>
						</tr>
						<tr>
							<th>Biographical Info</th>
							<td>
								<textarea name="author_def_description">'.get_the_author_meta('description').'</textarea>
							</td>
						</tr>
						<tr>
							<th>New password</th>
							<td>
								<input type="password" name="author_pe_password" value="">
								<p>'.__('Password needs to be atleast 8 symbols long.', 'profile-editor').'</p>
							</td>
						</tr>
						<tr>
							<th>Repeat New Password</th>
							<td>
								<input type="password" name="author_pe_repeat_password" value="">
							</td>
						</tr>
					</tbody>
				</table>';
			}

			$output .= $this->pe_frontend_user_fields( $current_user->data->ID, $current_style );
		}

		return $output;
	}

	function pe_get_field_rules ( $rules, $input_type='' ) {
		$rules_decoded = json_decode( $rules );
		$rules_output = '';
		
		foreach ($rules_decoded as $rules_key => $rules_value) {
				if ( $rules_key == 'field_empty' || $rules_key == 'field_min' || $rules_key == 'field_syntax' ) {
					$data_rule = str_replace('field_', 'data-', $rules_key);
					$rules_output .= $data_rule.'="'.$rules_value.'" ';
				} elseif ( $rules_key == 'field_max' ) {
					$rules_output .= 'maxlength="'.$rules_value.'" ';
				}
		}

		if ( $input_type == 'file' ) {
			foreach ($rules_decoded as $rules_key => $rules_value) {
				if ( $rules_key == 'field_max_size' || $rules_key == 'field_extensions' ) {
					$data_rule = str_replace('field_', 'data-', $rules_key);
					$rules_output .= $data_rule.'="'.$rules_value.'" ';
				}
			}
		}

		return $rules_output;
	}

	public function pe_frontend_user_fields ( $current_user_id, $form_style, $location='' ) {
		global $wpdb;

		$sql = "SELECT * FROM ".$wpdb->prefix."profile_editor_fields ORDER BY F_ORDER";
		$field_list = $wpdb->get_results($sql);
		$i = 1;
		$field_html = $html = '';
		$current_user_meta = get_user_meta( $current_user_id );

		if ( $form_style == 'default' ) {
			$html .= '
			<table class="form-table profile-editor extra">
				<tbody>
					<tr>';
						if ( $location != 'profile' ) {
							$html .= '<th id="user-profile-extra-fields">Extra fields</th>';
						} else {
							$html .= '<th id="user-profile-extra-fields"><h3>Extra fields</h3></th>';
						}
					$html .= '
					</tr>';
		} else {
			$html .= '
			<div id="user-profile-extra-fields" class="pe-edit-form extra '.$form_style.'">
				<span class="pe-form-heading"><strong>'.__('Extra', 'profile-editor').'</strong> '.__('fields', 'profile-editor').'</span>' ;
		}
		

		foreach ($field_list as $added_value) {
			switch ( $added_value->TYPE ) {
				case 'text':
					$rules_html = $this->pe_get_field_rules( $added_value->RULES );
					$object_param = $this->base.$added_value->NAME;
					if ( array_key_exists($object_param, $current_user_meta) ) {
						$current_user_value = $current_user_meta[$object_param]['0'];
					} else {
						$current_user_value = '';
					}
					if ( $current_user_value == '' ) {
						$current_user_value = $added_value->VALUE;
					}
					$field_html = '<input type="text" id="pe-'.$added_value->NAME.'" name="author_'.$added_value->NAME.'" value="'.$current_user_value.'" placeholder="'.$added_value->PLACEHOLDER.'" '.$rules_html.'>';
					if ( $added_value->DESCRIPTION != '' ) {
						if ( $form_style == 'default' ) {
							$field_html .= '<p>'.$added_value->DESCRIPTION.'</p>';
						} else {
							$field_html .= '<span class="pe-description">'.$added_value->DESCRIPTION.'</span>';
						}
					}
					break;
				case 'textarea':
					$rules_html = $this->pe_get_field_rules( $added_value->RULES );
					$object_param = $this->base.$added_value->NAME;
					if ( array_key_exists($object_param, $current_user_meta) ) {
						$current_user_value = $current_user_meta[$object_param]['0'];
					} else {
						$current_user_value = '';
					}
					if ( $current_user_value == '' ) {
						$current_user_value = $added_value->VALUE;
					}
					$field_html = '<textarea id="pe-'.$added_value->NAME.'" name="author_'.$added_value->NAME.'" placeholder="'.$added_value->PLACEHOLDER.'" '.$rules_html.'>'.$current_user_value.'</textarea>';
					if ( $added_value->DESCRIPTION != '' ) {
						if ( $form_style == 'default' ) {
							$field_html .= '<p>'.$added_value->DESCRIPTION.'</p>';
						} else {
							$field_html .= '<span class="pe-description">'.$added_value->DESCRIPTION.'</span>';
						}
					}
					break;
				case 'checkbox':
					$rules_html = $this->pe_get_field_rules( $added_value->RULES );
					$object_param = $this->base.$added_value->NAME;
					if ( array_key_exists($object_param, $current_user_meta) ) {
						$current_user_value = $current_user_meta[$object_param]['0'];
					} else {
						$current_user_value = '';
					}
					if ( $current_user_value == '' ) {
						$current_user_value = $added_value->VALUE;
					}
					$field_state = $checkbox_active = '';
					if ( $current_user_value == 'on' ) {
						$field_state = 'checked="checked"';
						$checkbox_active = 'pe-active';
					}
					if ( $form_style != 'default' ) {
						$field_html = '
						<label class="pe-checkbox '.$checkbox_active.'" for="pe-'.$added_value->NAME.'">
							<span class="pe-fake-checkbox"></span>
							<input type="checkbox" id="pe-'.$added_value->NAME.'" name="author_'.$added_value->NAME.'" '.$field_state.'">
							<input type="hidden" name="author_'.$added_value->NAME.'" value="'.$current_user_value.'" '.$rules_html.'>
							'.$added_value->LABEL.'
						</label>';
					} else {
						$field_html = '<input type="checkbox" id="pe-'.$added_value->NAME.'" name="author_'.$added_value->NAME.'" '.$field_state.'">
									<input type="hidden" name="author_'.$added_value->NAME.'" value="'.$current_user_value.'" '.$rules_html.'>';
					}
					if ( $added_value->DESCRIPTION != '' ) {
						if ( $form_style == 'default' ) {
							$field_html .= '<p>'.$added_value->DESCRIPTION.'</p>';
						} else {
							$field_html .= '<span class="pe-description">'.$added_value->DESCRIPTION.'</span>';
						}
					}
					break;
				case 'radio':
					$rules_html = $this->pe_get_field_rules( $added_value->RULES );
					$object_param = $this->base.$added_value->NAME;
					if ( array_key_exists($object_param, $current_user_meta) ) {
						$current_user_value = $current_user_meta[$object_param]['0'];
					} else {
						$current_user_value = '';
					}
					$field_html = '';
					$radio = explode(',', $added_value->VALUE);
					if ( $form_style != 'default' ) {
						$field_html .= '<label for="pe-'.$added_value->NAME.'">'.$added_value->LABEL.'</label>';
					}
					foreach ($radio as $radio_value) {
						$checked = $radio_active = '';
						if ( $current_user_value == $radio_value ) {
							$checked = 'checked';
							$radio_active = 'pe-active';
						}
						if ( $form_style != 'default' ) {
							$field_html .= '
							<label class="pe-radio '.$radio_active.'">
								<span class="pe-fake-radio"></span>
								<input type="radio" name="author_'.$added_value->NAME.'" value="'.$radio_value.'" '.$checked.' '.$rules_html.'>'.$radio_value.'
							</label>';
						} else {
							$field_html .= '<input type="radio" name="author_'.$added_value->NAME.'" value="'.$radio_value.'" '.$checked.' '.$rules_html.'>'.$radio_value.'<br>';
						}
					}
					if ( $added_value->DESCRIPTION != '' ) {
						if ( $form_style == 'default' ) {
							$field_html .= '<p>'.$added_value->DESCRIPTION.'</p>';
						} else {
							$field_html .= '<span class="pe-description">'.$added_value->DESCRIPTION.'</span>';
						}
					}
					break;
				case 'picture':
					$rules_html = $this->pe_get_field_rules( $added_value->RULES, 'file' );
					$field_html = '';
					$object_param = $this->base.$added_value->NAME;
					if ( array_key_exists($object_param, $current_user_meta) ) {
						$current_user_value = $current_user_meta[$object_param]['0'];
					} else {
						$current_user_value = null;
					}
					if ( $current_user_value != null ) {
						$attachment_link = wp_get_attachment_url( $current_user_value );
						$file_array = explode('/', $attachment_link);
						$file_path = 'C:/fakepath/'.$file_array[count($file_array)-1];
						$delete_checkbox = 'author_'.$added_value->NAME.'_delete';
						$field_html .= '
						<div class="pe-uploaded-image">
							<img src="'.$attachment_link.'" alt="'.$added_value->LABEL.'">
							<span class="pe-close-icon"></span>
							<input type="checkbox" name="'.$delete_checkbox.'" class="hidden">
						</div>
						<div class="clearfix"></div>';
						$file_exists = 'file-added';
					} else {
						$file_path = __('No file selected', 'profile-editor');
						$file_exists = 'no-file';
					}
					$field_name = 'author_'.$added_value->NAME;
					if ( $form_style != 'default' ) {
						$field_html .= '
						<div class="pe-styled-upload">
							<button>'.__('Choose file', 'profile-editor').'</button>
							<span class="pe-file-path">'.$file_path.'</span>
							<input type="file" id="pe-'.$added_value->NAME.'" name="author_'.$added_value->NAME.'" id="author_'.$added_value->NAME.'" class="'.$file_exists.'" multiple="false" '.$rules_html.' />
							<div class="clearfix"></div>
						</div>';
					} else {
						$field_html .= '<input type="file" id="pe-'.$added_value->NAME.'" name="author_'.$added_value->NAME.'" id="author_'.$added_value->NAME.'" class="'.$file_exists.'" multiple="false" '.$rules_html.' />';
					}
					ob_start();
					wp_nonce_field( $field_name, $field_name.'_nonce' );
					$field_html .= ob_get_clean();
					if ( $added_value->DESCRIPTION != '' ) {
						if ( $form_style == 'default' ) {
							$field_html .= '<p>'.$added_value->DESCRIPTION.'</p>';
						} else {
							$field_html .= '<span class="pe-description">'.$added_value->DESCRIPTION.'</span>';
						}
					}
					break;
				case 'password':
					$rules_html = $this->pe_get_field_rules( $added_value->RULES );
					$object_param = $this->base.$added_value->NAME;
					if ( array_key_exists($object_param, $current_user_meta) ) {
						$current_user_value = $current_user_meta[$object_param]['0'];
					} else {
						$current_user_value = '';
					}
					if ( $current_user_value == '' ) {
						$current_user_value = $added_value->VALUE;
					}
					$field_html = '<input type="password" id="pe-'.$added_value->NAME.'" name="author_'.$added_value->NAME.'" value="'.$current_user_value.'" placeholder="'.$added_value->PLACEHOLDER.'" '.$rules_html.'>';
					if ( $added_value->DESCRIPTION != '' ) {
						if ( $form_style == 'default' ) {
							$field_html .= '<p>'.$added_value->DESCRIPTION.'</p>';
						} else {
							$field_html .= '<span class="pe-description">'.$added_value->DESCRIPTION.'</span>';
						}
					}
					break;
				case 'file':
					$rules_html = $this->pe_get_field_rules( $added_value->RULES, 'file' );
					$field_html = '';
					$object_param = $this->base.$added_value->NAME;
					if ( array_key_exists($object_param, $current_user_meta) ) {
						$current_user_value = $current_user_meta[$object_param]['0'];
					} else {
						$current_user_value = null;
					}
					if ( $current_user_value != null ) {
						$attachment_link = wp_get_attachment_url( $current_user_value );
						$file_array = explode('/', $attachment_link);
						$file_path = 'C:/fakepath/'.$file_array[count($file_array)-1];
						$delete_checkbox = 'author_'.$added_value->NAME.'_delete';

						$field_html .= '
						<div class="pe-uploaded-file">
							<span class="pe-close-icon"></span>
							<a href="'.$attachment_link.'" target="_blank">'.__('Download your attached file.', 'profile-editor').'</a>
							<input type="checkbox" name="'.$delete_checkbox.'" class="hidden">
						</div>';
						$file_exists = 'file-added';
					} else {
						$file_path = __('No file selected', 'profile-editor');
						$file_exists = 'no-added';
					}
					$field_name = 'author_'.$added_value->NAME;
					if ( $form_style != 'default' ) {
						$field_html .= '
						<div class="pe-styled-upload">
							<button>'.__('Choose file', 'profile-editor').'</button>
							<span class="pe-file-path">'.$file_path.'</span>
							<input type="file" id="pe-'.$added_value->NAME.'" name="author_'.$added_value->NAME.'" id="author_'.$added_value->NAME.'" class="'.$file_exists.'" multiple="false" '.$rules_html.' />
							<div class="clearfix"></div>
						</div>';
					} else {
						$field_html .= '<input type="file" name="author_'.$added_value->NAME.'" id="author_'.$added_value->NAME.'" multiple="false" class="'.$file_exists.'" '.$rules_html.' />';
					}
					ob_start();
					wp_nonce_field( $field_name, $field_name.'_nonce' );
					$field_html .= ob_get_clean();
					if ( $added_value->DESCRIPTION != '' ) {
						if ( $form_style == 'default' ) {
							$field_html .= '<p>'.$added_value->DESCRIPTION.'</p>';
						} else {
							$field_html .= '<span class="pe-description">'.$added_value->DESCRIPTION.'</span>';
						}
					}
					break;
				case 'dropdown':
					$rules_html = $this->pe_get_field_rules( $added_value->RULES );
					$object_param = $this->base.$added_value->NAME;
					if ( array_key_exists($object_param, $current_user_meta) ) {
						$current_user_value = $current_user_meta[$object_param]['0'];
					} else {
						$current_user_value = '';
					}
					$field_html = '<select id="pe-'.$added_value->NAME.'" name="author_'.$added_value->NAME.'" '.$rules_html.'>';
					$dropdown = explode(',', $added_value->VALUE);
					foreach ($dropdown as $dropdown_value) {
						$selected = '';
						if ( $current_user_value == $dropdown_value ) {
							$selected = 'selected="selected"';
						}
						$field_html .= '<option value="'.$dropdown_value.'" '.$selected.'>'.$dropdown_value.'</option>';
					}
					$field_html .= '/<select>';
					if ( $added_value->DESCRIPTION != '' ) {
						if ( $form_style == 'default' ) {
							$field_html .= '<p>'.$added_value->DESCRIPTION.'</p>';
						} else {
							$field_html .= '<span class="pe-description">'.$added_value->DESCRIPTION.'</span>';
						}
					}
					break;
				case 'hidden':
					$rules_html = $this->pe_get_field_rules( $added_value->RULES );
					$object_param = $this->base.$added_value->NAME;
					if ( array_key_exists($object_param, $current_user_meta) ) {
						$current_user_value = $current_user_meta[$object_param]['0'];
					} else {
						$current_user_value = '';
					}
					if ( $current_user_value == '' ) {
						$current_user_value = $added_value->VALUE;
					}
					$field_html = '<input type="hidden" name="author_'.$added_value->NAME.'" value="'.$current_user_value.'" '.$rules_html.'>';
					break;
				case 'WYSIWYG':
					$object_param = $this->base.$added_value->NAME;
					if ( array_key_exists($object_param, $current_user_meta) ) {
						$current_user_value = $current_user_meta[$object_param]['0'];
					} else {
						$current_user_value = '';
					}
					if ( $current_user_value == '' ) {
						$current_user_value = $added_value->VALUE;
					}
					$editor_settings = array('textarea_name' => 'author_'.$added_value->NAME, 'media_buttons' => false);
					ob_start();
					wp_editor( $current_user_value, 'author_'.$added_value->NAME, $editor_settings );
					$field_html = ob_get_clean();
					if ( $added_value->DESCRIPTION != '' ) {
						if ( $form_style == 'default' ) {
							$field_html .= '<p>'.$added_value->DESCRIPTION.'</p>';
						} else {
							$field_html .= '<span class="pe-description">'.$added_value->DESCRIPTION.'</span>';
						}
					}
					break;
				default:
					$field_html = '';
					break;
			}

			if ( $added_value->TYPE != 'hidden' ) {
				if ( $form_style == 'default' ) {
					$html .= '
					<tr>
						<th>'.$added_value->LABEL.'</th>
						<td>
							'.$field_html.'
						</td>
					</tr>';
				} else {
					if ( $added_value->TYPE == 'checkbox' || $added_value->TYPE == 'radio' ) {
						$html .= '
						<div class="pe-edit-item">
							'.$field_html.'
						</div>';
					} else {
						$html .= '
						<div class="pe-edit-item">
							<label for="pe-'.$added_value->NAME.'">'.$added_value->LABEL.'</label>
							'.$field_html.'
						</div>';
					}
				}
			} else {
				$html .= $field_html;
			}
			
		}

		if ( $form_style == 'default' ) {
					$html .= '
					</tbody>
				</table>';
				if ( $location != 'profile' ) {
						$html .= '
						<p class="submit">
							<input type="hidden" name="form_action" value="edit_user_profile">
							<input name="Submit" type="submit" class="button-primary" value="' . esc_attr( __( 'Save changes' , 'profile-editor' ) ) . '" />
						</p>
					</form>';
				} else {
					$html .= '<input type="hidden" name="form_action" value="edit_user_profile">';
				}
		} else {
				$html .= '
				</div>';
				if ( $location != 'profile' ) {
						$html .= '
						<p class="submit">
							<input type="hidden" name="form_action" value="edit_user_profile">
							<input name="Submit" type="submit" class="button-primary" value="' . esc_attr( __( 'Save changes' , 'profile-editor' ) ) . '" />
						</p>
					</form>';
				} else {
					$html .= '<input type="hidden" name="form_action" value="edit_user_profile">';
				}

		}

		return $html;
	}

	function pe_update_user_settings ( $current_user_id ) {
		global $wpdb;
		$updatable_fields = $_POST;
		$file_uploads = $_FILES;

		// Lets upload files
		if ( !empty( $file_uploads ) ) {
			require_once( ABSPATH . 'wp-admin/includes/image.php' );
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
			require_once( ABSPATH . 'wp-admin/includes/media.php' );
			
			foreach ($file_uploads as $file_key => $file_value) {
				$file_nonce = $file_key.'_nonce';
				$file_delete = $file_key.'_delete';

				if ( isset($_POST[$file_nonce]) && wp_verify_nonce( $_POST[$file_nonce], $file_key ) && !isset($_POST[$file_delete]) && isset($file_value['size']) && $file_value['size'] > 0 ) {
					$attachment_id = media_handle_upload( $file_key, '0' );

					if ( !is_wp_error( $attachment_id ) ) {
						$meta_key = str_replace('author_', $this->base, $file_key);
						$meta_value = $attachment_id;
						update_user_meta($current_user_id, $meta_key, $meta_value);
					}
				} elseif ( isset($_POST[$file_delete]) ) {
					$current_user_meta = get_user_meta( $current_user_id );
					$meta_key = str_replace('author_', $this->base, $file_key);
					if ( isset($current_user_meta[$meta_key]) ) {
						$current_user_value = $current_user_meta[$meta_key]['0'];
					} else {
						$current_user_value = '';
					}

					if ( false !== wp_delete_attachment( $current_user_value ) ) {
						delete_user_meta($current_user_id, $meta_key);
					}
				}
			}
		}

		// Lets update added fields
		foreach ($updatable_fields as $updatable_key => $updatable_value) {
			if (strpos($updatable_key,'author_') !== false) {
				$meta_key = str_replace('author_', $this->base, $updatable_key);
				$meta_value = $updatable_value;
				update_user_meta($current_user_id, $meta_key, $meta_value);
			}
		}

		if ( !is_admin() ) {
			// Lets update default fields
			$default_field_array = array('ID' => $current_user_id);
			foreach ($updatable_fields as $updatable_def_key => $updatable_def_value) {
				if (strpos($updatable_def_key,'author_def_') !== false) {
					$meta_key = str_replace('author_def_', '', $updatable_def_key);
					$meta_value = $updatable_def_value;
					$default_field_array[$meta_key] = $meta_value;
				}
			}
			wp_update_user( $default_field_array );
		}

		if ( !is_admin() ) {
			// Check for password
			if ( ( isset($updatable_fields['author_pe_password']) && isset($updatable_fields['author_pe_repeat_password']) ) && $updatable_fields['author_pe_password'] == $updatable_fields['author_pe_repeat_password'] && strlen($updatable_fields['author_pe_password']) > 8 ) {
				wp_set_password( $updatable_fields['author_pe_password'], $current_user_id );
			}
		}
	}

	/**
	 * Register plugin settings
	 * @return void
	 */
	public function register_settings () {
		if ( is_array( $this->settings ) ) {

			// Check posted/selected tab
			$current_section = '';
			if ( isset( $_POST['tab'] ) && $_POST['tab'] ) {
				$current_section = $_POST['tab'];
			} else {
				if ( isset( $_GET['tab'] ) && $_GET['tab'] ) {
					$current_section = $_GET['tab'];
				}
			}

			foreach ( $this->settings as $section => $data ) {

				if ( $current_section && $current_section != $section ) continue;

				// Add section to page
				add_settings_section( $section, $data['title'], array( $this, 'settings_section' ), $this->parent->_token . '_settings' );

				foreach ( $data['fields'] as $field ) {

					// Validation callback for field
					$validation = '';
					if ( isset( $field['callback'] ) ) {
						$validation = $field['callback'];
					}

					// Register field
					$option_name = $this->base . $field['id'];
					register_setting( $this->parent->_token . '_settings', $option_name, $validation );

					// Add field to page
					add_settings_field( $field['id'], $field['label'], array( $this->parent->admin, 'display_field' ), $this->parent->_token . '_settings', $section, array( 'field' => $field, 'prefix' => $this->base ) );
				}

				if ( ! $current_section ) break;
			}
		}
	}

	public function settings_section ( $section ) {
		$html = '<p> ' . $this->settings[ $section['id'] ]['description'] . '</p>' . "\n";
		echo $html;
	}

	public function pe_get_field_order () {
		global $wpdb;
		
		$sql = "SELECT max(f_order) FROM ".$wpdb->prefix."profile_editor_fields";
		$current_max = $wpdb->get_var($sql);

		return intval($current_max)+1;
	}

	public function pe_get_field_count () {
		global $wpdb;
		
		$sql = "SELECT count(*) FROM ".$wpdb->prefix."profile_editor_fields";
		$current_max = $wpdb->get_var($sql);

		return intval($current_max);
	}

	public function pe_add_new_field ( $new_field ) {
		global $wpdb;

		$field_table = $wpdb->prefix . 'profile_editor_fields';
		$field_order = $this->pe_get_field_order();
		$field_name = esc_attr($new_field['field_name']);
		$field_label = esc_attr($new_field['field_label']);
		$field_description = esc_attr($new_field['field_description']);
		$field_type = esc_attr($new_field['field_type']);
		$field_value = esc_attr($new_field['field_value']);
		$field_placeholder = esc_attr($new_field['field_placeholder']);
		$exist = false;

		if ( $field_type == 'empty' ) {
			$status = false;
			$_POST['field_status'] = 'failed';
			$_POST['pe_error_msg'] = 'Select your new field type';
			return;
		}

		switch ( $field_type ) {
			case 'empty':
				$field_rules = array();
				break;
			case 'text':
				$field_rules = array('field_empty' => 'off', 'field_syntax' => 'off', 'field_min' => '', 'field_max' => '', 'field_registration' => 'off');
				break;
			case 'textarea':
				$field_rules = array('field_empty' => 'off', 'field_min' => '', 'field_max' => '', 'field_registration' => 'off');
				break;
			case 'checkbox':
				$field_rules = array('field_empty' => 'off', 'field_registration' => 'off');
				break;
			case 'radio':
				$field_rules = array('field_empty' => 'off', 'field_registration' => 'off');
				break;
			case 'picture':
				$field_rules = array('field_empty' => 'off', 'field_max_size' => '', 'field_extensions' => 'all', 'field_registration' => 'off');
				break;
			case 'password':
				$field_rules = array('field_empty' => 'off', 'field_min' => '', 'field_max' => '', 'field_registration' => 'off');
				break;
			case 'file':
				$field_rules = array('field_empty' => 'off', 'field_max_size' => '', 'field_extensions' => 'all', 'field_registration' => 'off');
				break;
			case 'dropdown':
				$field_rules = array('field_empty' => 'off', 'field_registration' => 'off');
				break;
			case 'hidden':
				$field_rules = array('field_empty' => 'off', 'field_min' => '', 'field_max' => '', 'field_registration' => 'off');
				break;
			case 'WYSIWYG':
				$field_rules = array('field_empty' => 'off', 'field_min' => '', 'field_max' => '', 'field_registration' => 'off');
				break;
			default:
				$field_rules = array();
				break;
		}
		

		if ( $field_label == '' ) {
			$field_label = $field_name;
		}

		$field_name_converted = strtolower($field_name);
		$field_name_converted = preg_replace('/\s+/', '_', $field_name_converted);

		foreach ($new_field as $each_field_key => $each_field_value) {
			if ( array_key_exists($each_field_key, $field_rules) && $each_field_value != '' ) {
				$field_rules[$each_field_key] = $each_field_value;
			}
		}

		$field_rules_encoded = json_encode($field_rules);

		$sql_check = "SELECT NAME FROM ".$field_table." WHERE NAME LIKE '".$field_name_converted."' LIMIT 1";
		$status_check = $wpdb->get_results($sql_check);

		if ( empty($status_check) ) {
			$sql = "INSERT INTO ".$field_table." SET F_ORDER='".$field_order."', NAME='".$field_name_converted."', TYPE='".$field_type."', LABEL='".$field_label."', VALUE='".$field_value."', PLACEHOLDER='".$field_placeholder."', RULES='".$field_rules_encoded."', DESCRIPTION='".$field_description."'";
			$status = $wpdb->query($sql);
		} else {
			$status = false;
			$exist = true;
		}

		if ( $status ) {
			$_POST['field_status'] = 'success';
			$_POST['pe_error_msg'] = 'Successfully added new field <strong>'.$field_name.'</strong>';
		} else {
			$_POST['field_status'] = 'failed';
			if ( $exist ) {
				$_POST['pe_error_msg'] = 'Field with name <strong>'.$field_name.'</strong> already exists';
			} else {
				$_POST['pe_error_msg'] = 'Something went wrong';
			}
		}
	}

	public function pe_update_existing_field ( $existing_field, $form_action ) {
		global $wpdb;
		$exploded_action = explode('_', $form_action);
		$form_action_name = $exploded_action['2'];
		$form_action_id = $exploded_action['3'];



		if ( $form_action_name == 'update' ) {
			switch ( $existing_field['added_field_type'][$form_action_id] ) {
				case 'empty':
					$field_rules = $field_values = array();
					break;
				case 'text':
					$field_rules = array('field_empty' => 'off', 'field_syntax' => 'off', 'field_min' => '', 'field_max' => '', 'field_registration' => 'off');
					$field_values = array('added_field_type' => '', 'added_field_name' => '', 'added_field_label' => '', 'added_field_placeholder' => '', 'added_field_value' => '', 'added_field_description' => '');
					break;
				case 'textarea':
					$field_rules = array('field_empty' => 'off', 'field_min' => '', 'field_max' => '', 'field_registration' => 'off');
					$field_values = array('added_field_type' => '', 'added_field_name' => '', 'added_field_label' => '', 'added_field_placeholder' => '', 'added_field_value' => '', 'added_field_description' => '');
					break;
				case 'checkbox':
					$field_rules = array('field_empty' => 'off', 'field_registration' => 'off');
					$field_values = array('added_field_type' => '', 'added_field_name' => '', 'added_field_label' => '', 'added_field_value' => '', 'added_field_description' => '');
					break;
				case 'radio':
					$field_rules = array('field_empty' => 'off', 'field_registration' => 'off');
					$field_values = array('added_field_type' => '', 'added_field_name' => '', 'added_field_label' => '', 'added_field_value' => '', 'added_field_description' => '');
					break;
				case 'picture':
					$field_rules = array('field_empty' => 'off', 'field_max_size' => '', 'field_extensions' => 'all', 'field_registration' => 'off');
					$field_values = array('added_field_type' => '', 'added_field_name' => '', 'added_field_label' => '', 'added_field_value' => '', 'added_field_description' => '');
					break;
				case 'password':
					$field_rules = array('field_empty' => 'off', 'field_min' => '', 'field_max' => '', 'field_registration' => 'off');
					$field_values = array('added_field_type' => '', 'added_field_name' => '', 'added_field_label' => '', 'added_field_placeholder' => '', 'added_field_value' => '', 'added_field_description' => '');
					break;
				case 'file':
					$field_rules = array('field_empty' => 'off', 'field_max_size' => '', 'field_extensions' => 'all', 'field_registration' => 'off');
					$field_values = array('added_field_type' => '', 'added_field_name' => '', 'added_field_label' => '', 'added_field_value' => '', 'added_field_description' => '');
					break;
				case 'dropdown':
					$field_rules = array('field_empty' => 'off', 'field_registration' => 'off');
					$field_values = array('added_field_type' => '', 'added_field_name' => '', 'added_field_label' => '', 'added_field_value' => '', 'added_field_description' => '');
					break;
				case 'hidden':
					$field_rules = array('field_empty' => 'off', 'field_min' => '', 'field_max' => '', 'field_registration' => 'off');
					$field_values = array('added_field_type' => '', 'added_field_name' => '', 'added_field_label' => '', 'added_field_value' => '', 'added_field_description' => '');
					break;
				case 'WYSIWYG':
					$field_rules = array('field_empty' => 'off', 'field_min' => '', 'field_max' => '', 'field_registration' => 'off');
					$field_values = array('added_field_type' => '', 'added_field_name' => '', 'added_field_label' => '', 'added_field_placeholder' => '', 'added_field_value' => '', 'added_field_description' => '');
					break;
				default:
					$field_rules = $field_values = array();
					break;
			}

			foreach ($existing_field as $update_key => $update_value) {
				if ( array_key_exists(str_replace('added_', '', $update_key), $field_rules) && isset($update_value[$form_action_id]) && $update_value[$form_action_id] != null ) {
					$field_rules[str_replace('added_', '', $update_key)] = $update_value[$form_action_id];
				} elseif ( array_key_exists($update_key, $field_values) ) {
					$field_values[$update_key] = $update_value[$form_action_id];
				}
			}

			$field_rules_converted = json_encode($field_rules);
			$field_table = $wpdb->prefix . 'profile_editor_fields';
			$current_field_id = $existing_field['added_field_id'][$form_action_id];
			$current_order = $existing_field['added_field_order'][$form_action_id];
			$set_query = "F_ORDER='".$current_order."', ";
			
			foreach ($field_values as $f_values_key => $f_values_value) {
				if ( $f_values_key == 'added_field_type' && $f_values_value != '' ) {
					$set_query .= "TYPE='".$f_values_value."', ";
				} elseif ( $f_values_key == 'added_field_name' && $f_values_value != '' ) {
					$field_name_converted = strtolower($f_values_value);
					$field_name_converted = preg_replace('/\s+/', '_', $field_name_converted);

					$set_query .= "NAME='".$field_name_converted."', ";
				} elseif ( $f_values_key == 'added_field_label' && $f_values_value != '' ) {
					$set_query .= "LABEL='".$f_values_value."', ";
				} elseif ( $f_values_key == 'added_field_placeholder' && $f_values_value != '' ) {
					$set_query .= "PLACEHOLDER='".$f_values_value."', ";
				} elseif ( $f_values_key == 'added_field_value' && $f_values_value != '' ) {
					$set_query .= "VALUE='".$f_values_value."', ";
				} elseif ( $f_values_key == 'added_field_description' && $f_values_value != '' ) {
					$set_query .= "DESCRIPTION='".$f_values_value."', ";
				}
			}

			$order_sql = "SELECT F_ORDER FROM ".$field_table." WHERE F_ORDER='".$current_order."'";
			$order_check = $wpdb->get_results($order_sql);

			if ( !empty( $order_check ) ) {
				$order_fix_sql = "UPDATE ".$field_table." SET F_ORDER='-1' WHERE F_ORDER='".$current_order."'";
				$order_fix = $wpdb->query($order_fix_sql);
			}

			$update_sql = "UPDATE ".$field_table." SET ".rtrim($set_query, ', ').", RULES='".$field_rules_converted."' WHERE ID='".$current_field_id."'";
			$wpdb->query($update_sql);

		} elseif ( $form_action_name == 'delete' ) {
			$field_table = $wpdb->prefix . 'profile_editor_fields';
			$current_field_id = $existing_field['added_field_id'][$form_action_id];

			$delete_sql = "DELETE FROM ".$field_table." WHERE ID='".$current_field_id."'";
			$wpdb->query($delete_sql);
		}

	}

	public function fields_form () {
		// Field info
		$html = '
		<h3>'.__('Add extra user fields.', 'profile-editor').'</h3>
		<p>'.__('This section if for adding new user fields.', 'profile-editor').'</p>';

		if( !empty($_POST) && isset($_POST['field_status']) ) {
			if ( $_POST['field_status'] == 'success' ) {
				$html .= '
				<div id="setting-error-settings_updated" class="updated settings-error"> 
					<p>'.$_POST['pe_error_msg'].'.</p>
				</div>';
			} else {
				$html .= '
				<div id="setting-error-settings_updated" class="error settings-error"> 
					<p>'.$_POST['pe_error_msg'].'.</p>
				</div>';
			}
		}

		$html .= '<form method="post" action="#profile_editor_settings" enctype="multipart/form-data">';

		// Settings field values
		$html .=
		'<table class="form-table profile-editor values">
			<tbody>
				<tr>
					<th><h3>'.__('Field values').'</h3></th>
				</tr>
				<tr>
					<th>'.__('Your field type', 'profile-editor').'</th>
					<td>
						<select name="field_type" id="pe-main-select">
							<option value="empty" selected="selected">'.__('empty', 'profile-editor').'</option>
							<option value="text">'.__('text', 'profile-editor').'</option>
							<option value="textarea">'.__('textarea', 'profile-editor').'</option>
							<option value="checkbox">'.__('checkbox', 'profile-editor').'</option>
							<option value="radio">'.__('radio', 'profile-editor').'</option>
							<option value="picture">'.__('picture', 'profile-editor').'</option>
							<option value="password">'.__('password', 'profile-editor').'</option>
							<option value="file">'.__('file', 'profile-editor').'</option>
							<option value="dropdown">'.__('dropdown', 'profile-editor').'</option>
							<option value="hidden">'.__('hidden', 'profile-editor').'</option>
							<option value="WYSIWYG">'.__('WYSIWYG', 'profile-editor').'</option>
						</select>
					</td>
				</tr>
				<tr id="pe-name-section">
					<th>'.__('Your field name', 'profile-editor').'</th>
					<td>
						<input type="text" name="field_name" id="pe-field-name" value="">
					</th>
				</tr>
				<tr id="pe-label-section">
					<th>'.__('Your field label', 'profile-editor').'</th>
					<td>
						<input type="text" name="field_label" id="pe-field-label" value="">
					</th>
				</tr>
				<tr id="pe-placeholder-section">
					<th>'.__('Your field placeholder', 'profile-editor').'</th>
					<td>
						<input type="text" name="field_placeholder" id="pe-field-placeholder" value="">
					</th>
				</tr>
				<tr id="pe-value-section">
					<th>'.__('Your field default value', 'profile-editor').'</th>
					<td>
						<input type="text" name="field_value" id="pe-field-value" value="">
					</th>
				</tr>
				<tr id="pe-description-section">
					<th>'.__('Your field description', 'profile-editor').'</th>
					<td>
						<input type="text" name="field_description" id="pe-field-description" value="">
					</th>
				</tr>
			</tbody>
		</table>';

		// Settings field rules
		$html .=
		'<table class="form-table profile-editor rules">
			<tbody>
				<tr>
					<th><h3>'.__('Field rules').'</h3></th>
				</tr>
				<tr id="pe-empty-section">
					<th>'.__('This field can\'t be empty', 'profile-editor').'</th>
					<td>
						<input type="checkbox" name="field_empty" id="pe-field-empty">
					</th>
				</tr>
				<tr id="pe-syntax-section">
					<th>'.__('Check email/URL syntax', 'profile-editor').'</th>
					<td>
						<input type="checkbox" name="field_syntax" id="pe-field-syntax">
					</th>
				</tr>
				<tr id="pe-min-section">
					<th>'.__('Min length', 'profile-editor').'</th>
					<td>
						<input type="text" name="field_min" id="pe-field-min" value="">
					</th>
				</tr>
				<tr id="pe-max-section">
					<th>'.__('Max length', 'profile-editor').'</th>
					<td>
						<input type="text" name="field_max" id="pe-field-max" value="">
					</th>
				</tr>
				<tr id="pe-max-size-section">
					<th>'.__('Max size', 'profile-editor').'</th>
					<td>
						<input type="text" name="field_max_size" id="pe-field-max-size" value="">
					</th>
				</tr>
				<tr id="pe-extensions-section">
					<th>'.__('Allowed extensions', 'profile-editor').'</th>
					<td>
						<input type="text" name="field_extensions" id="pe-field-extensions">
					</th>
				</tr>
				<tr id="pe-registration-section">
					<th>'.__('Show at registration', 'profile-editor').'</th>
					<td>
						<input type="checkbox" name="field_registration" id="pe-field-registration">
					</th>
				</tr>
			</tbody>
		</table>';

		$html .= '
		<div class="clearfix"></div>
		<p class="submit">
			<input type="hidden" name="form_action" value="add_new_field">
			<input name="Submit" type="submit" class="button-primary" value="' . esc_attr( __( 'Add field' , 'profile-editor' ) ) . '" />
		</p>
		</form>';

		echo $html;
	}

	public function pe_get_select_html ( $selected, $count ) {
		$select_options = array('empty', 'text', 'textarea', 'checkbox', 'radio', 'picture', 'password', 'file', 'dropdown', 'hidden', 'WYSIWYG');
		$html = '
		<select name="added_field_type['.$count.']">';
			foreach ($select_options as $select_value) {
				$selected_true = '';
				if ( $selected == $select_value ) {
					$selected_true = 'selected="selected"';
				}
				$html .= '<option value="'.$select_value.'" '.$selected_true.'>'.$select_value.'</option>';
			}
		$html .= '	
		</select>';

		return $html;
	}

	public function pe_get_added_rules_html ( $rules_json, $count ) {
		$added_rules = json_decode($rules_json);
		$html = '';
		foreach ($added_rules as $added_rule_key => $added_rule_value) {
			if ( $added_rule_key == 'field_empty' ) {
				$checked = '';
				if ( $added_rule_value == 'on' ) {
					$checked = 'checked="checked"';
				}
				$html .= 
				'<span class="pe-checkbox-label">'.__('This field can\'t be empty', 'profile-editor').':</span><input type="checkbox" name="added_field_empty['.$count.']" '.$checked.'><span class="pe-table-gap clearfix"></span>';
			} elseif ( $added_rule_key == 'field_syntax' ) {
				$checked = '';
				if ( $added_rule_value == 'on' ) {
					$checked = 'checked="checked"';
				}
				$html .= 
				'<span class="pe-checkbox-label">'.__('Check email/URL syntax', 'profile-editor').':</span><input type="checkbox" name="added_field_syntax['.$count.']" '.$checked.'><span class="pe-table-gap clearfix"></span>';
			} elseif ( $added_rule_key == 'field_min' ) {
				$html .= 
				'<span class="pe-input-label">'.__('Min length', 'profile-editor').':</span>
				<input type="text" name="added_field_min['.$count.']" value="'.$added_rule_value.'"><span class="pe-table-gap clearfix"></span>';
			} elseif ( $added_rule_key == 'field_max' ) {
				$html .= 
				'<span class="pe-input-label">'.__('Max length', 'profile-editor').':</span>
				<input type="text" name="added_field_max['.$count.']" value="'.$added_rule_value.'"><span class="pe-table-gap clearfix"></span>';
			} elseif ( $added_rule_key == 'field_max_size' ) {
				$html .= 
				'<span class="pe-input-label">'.__('Max size', 'profile-editor').':</span>
				<input type="text" name="added_field_max_size['.$count.']" value="'.$added_rule_value.'"><span class="pe-table-gap clearfix"></span>';
			} elseif ( $added_rule_key == 'field_extensions' ) {
				$html .= 
				'<span class="pe-input-label">'.__('Allowed extensions', 'profile-editor').':</span>
				<input type="text" name="added_field_extensions['.$count.']" value="'.$added_rule_value.'"><span class="pe-table-gap clearfix"></span>';
			} elseif ( $added_rule_key == 'field_registration' ) {
				$checked = '';
				if ( $added_rule_value == 'on' ) {
					$checked = 'checked="checked"';
				}
				$html .= 
				'<span class="pe-checkbox-label">'.__('Show at registration', 'profile-editor').':</span><input type="checkbox" name="added_field_registration['.$count.']" '.$checked.'><span class="pe-table-gap clearfix"></span>';
			}
		}

		return $html;
	}

	public function added_fields () {
		global $wpdb;

		$sql = "SELECT * FROM ".$wpdb->prefix."profile_editor_fields ORDER BY F_ORDER";
		$field_list = $wpdb->get_results($sql);
		$i = 1;

		// Field info
		$html = '
		<h3>'.__('Existing extra user fields.', 'profile-editor').'</h3>
		<p>'.__('This section if for editing existing extra user fields.', 'profile-editor').'</p>
		<p>'.__('Note: When updating order, if order is set to number that already is set to another field, that other fields order number will change to -1.', 'profile-editor').'</p>';


		$html .=
		'<form method="post" action="#profile_editor_settings" enctype="multipart/form-data">
			<table class="profile-editor added" cellspacing="0">
				<thead>
					<tr>
						<th class="pe-order">#</th>
						<th>Field values</th>
						<th>Field rules</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>';

					foreach ($field_list as $added_value) {
						$html .= '
						<tr>
							<td class="pe-order">
								'.__('Order', 'profile-editor').': <br> <input type="text" name="added_field_order['.$i.']" value="'.$added_value->F_ORDER.'">
							</td>
							<td>'.
								'<span class="pe-input-label">'.__('Your field type', 'profile-editor').':</span>
								'.$this->pe_get_select_html($added_value->TYPE, $i).'<span class="pe-table-gap clearfix"></span>'.

								'<span class="pe-input-label">'.__('Your field name', 'profile-editor').':</span>
								<input type="text" name="added_field_name['.$i.']" value="'.$added_value->NAME.'"><span class="pe-table-gap clearfix"></span>'.

								'<span class="pe-input-label">'.__('Your field label', 'profile-editor').':</span>
								<input type="text" name="added_field_label['.$i.']" value="'.$added_value->LABEL.'"><span class="pe-table-gap clearfix"></span>';

								if ( $added_value->TYPE == 'text' || $added_value->TYPE == 'textarea' || $added_value->TYPE == 'password' || $added_value->TYPE == 'WYSIWYG' ) {
									$html .=
									'<span class="pe-input-label">'.__('Your field placeholder', 'profile-editor').':</span>
									<input type="text" name="added_field_placeholder['.$i.']" value="'.$added_value->PLACEHOLDER.'"><span class="pe-table-gap clearfix"></span>';
								}

								$html .= 
								'<span class="pe-input-label">'.__('Your field default value', 'profile-editor').':</span>
								<input type="text" name="added_field_value['.$i.']" value="'.$added_value->VALUE.'"><span class="pe-table-gap clearfix"></span>'.

								'<span class="pe-input-label">'.__('Your field description', 'profile-editor').':</span>
								<input type="text" name="added_field_description['.$i.']" value="'.$added_value->DESCRIPTION.'">
							</td>
							<td>';

								$html .= $this->pe_get_added_rules_html($added_value->RULES, $i);

							$html .= '	
							</td>
							<td>
								<input type="hidden" name="added_field_id['.$i.']" value="'.$added_value->ID.'">
								<input type="hidden" name="form_action" value="existing_fields">
								<input type="submit" class="added_field_update button-primary" name="form_action_update_'.$i.'" value="'.__('Update', 'profile-editor').'"><br><br>
								<input type="submit" class="added_field_delete button-primary delete" name="form_action_delete_'.$i.'" value="'.__('Delete', 'profile-editor').'" onclick="return confirm(\''.__('Are you sure you want to delete field ID:', 'profile-editor').$added_value->ID.'?\');">
							</td>
						</tr>';
						$i++;
					}

				$html .= '
				</tbody>
			</table>
		</form>';

		$html .= '
		<div class="clearfix"></div>
		</form>';

		echo $html;
	}

	/**
	 * Load settings page content
	 * @return void
	 */
	public function settings_page () {

		// Build page HTML
		$html = '<div class="wrap" id="' . $this->parent->_token . '_settings">' . "\n";
			$html .= '<h2>' . __( 'Profile editor' , 'profile-editor' ) . '</h2>' . "\n";

			$tab = '';
			if ( isset( $_GET['tab'] ) && $_GET['tab'] ) {
				$tab .= $_GET['tab'];
			}

			// Show page tabs
			if ( is_array( $this->settings ) && 1 < count( $this->settings ) ) {

				$html .= '<h2 class="nav-tab-wrapper">' . "\n";

				$c = 0;
				foreach ( $this->settings as $section => $data ) {

					// Set tab class
					$class = 'nav-tab';
					if ( ! isset( $_GET['tab'] ) ) {
						if ( 0 == $c ) {
							$class .= ' nav-tab-active';
						}
					} else {
						if ( isset( $_GET['tab'] ) && $section == $_GET['tab'] ) {
							$class .= ' nav-tab-active';
						}
					}

					// Set tab link
					$tab_link = add_query_arg( array( 'tab' => $section ) );
					if ( isset( $_GET['settings-updated'] ) ) {
						$tab_link = remove_query_arg( 'settings-updated', $tab_link );
					}

					// Output tab
					$html .= '<a href="' . $tab_link . '" class="' . esc_attr( $class ) . '">' . esc_html( $data['title'] ) . '</a>' . "\n";

					++$c;
				}

				$html .= '</h2>' . "\n";
			}

			if ( $tab == 'fields' ) {
				if ( !empty($_POST) && isset($_POST['form_action']) && $_POST['form_action'] == 'add_new_field' ) {
					$this->pe_add_new_field( $_POST );
				}

				if ( !empty($_POST) && isset($_POST['form_action']) && $_POST['form_action'] == 'existing_fields' ) {
					$post_data = $_POST;
					$post_action = '';

					foreach ($post_data as $post_key => $post_value) {
						if ( strpos($post_key,'form_action_update') !== false ) {
							$post_action_split = explode('_', $post_key);
							$post_action = $post_key;
						} elseif ( strpos($post_key,'form_action_delete') !== false ) {
							$post_action_split = explode('_', $post_key);
							$post_action = $post_key;
						}
					}
					$this->pe_update_existing_field ( $_POST, $post_action );
				}
				
				ob_start();
				$this->fields_form();
				if ( $this->pe_get_field_count() > 0 ) {
					$this->added_fields();
				}
				$html .= ob_get_clean();
			}

			if ( $tab != 'fields' ) {
				$html .= '<form method="post" action="options.php" enctype="multipart/form-data">' . "\n";

				// Get settings fields
				ob_start();
				settings_fields( $this->parent->_token . '_settings' );
				do_settings_sections( $this->parent->_token . '_settings' );
				$html .= ob_get_clean();

				$html .= '<p class="submit">' . "\n";
					$html .= '<input type="hidden" name="tab" value="' . esc_attr( $tab ) . '" />' . "\n";
					$html .= '<input name="Submit" type="submit" class="button-primary" value="' . esc_attr( __( 'Save Settings' , 'profile-editor' ) ) . '" />' . "\n";
				$html .= '</p>' . "\n";

				$html .= '</form>' . "\n";
			}

		$html .= '</div>' . "\n";

		echo $html;
	}

	/**
	 * Main Profile_editor_Settings Instance
	 *
	 * Ensures only one instance of Profile_editor_Settings is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Profile_editor()
	 * @return Main Profile_editor_Settings instance
	 */
	public static function instance ( $parent ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $parent );
		}
		return self::$_instance;
	} // End instance()

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->parent->_version );
	} // End __clone()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->parent->_version );
	} // End __wakeup()

}