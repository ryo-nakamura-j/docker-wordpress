<?php
class Mo2f_Setup_Wizard {


	private $wizard_steps;
	private $current_step;

	public function __construct() {
	 }

	public function mo2f_setup_page() {
		// Get page argument from $_GET array.
		$page = ( isset( $_GET['page'] ) ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : ''; 
		if ( empty( $page ) || 'mo2f-setup-wizard' !== $page ) {
			if ( empty( $page ) || 'mo2f-setup-wizard-method' !== $page ) {
				return;
			}
			global $Mo2fdbQueries;
			$mo2f_configured_2FA_method = $Mo2fdbQueries->get_user_detail( 'mo2f_configured_2FA_method',get_current_user_id() );
			if(empty($mo2f_configured_2FA_method))
				$this->mo2f_setup_twofa();
			else
				$this->mo2f_redirect_to_2fa_dashboard();
			return;
		}
		if(get_site_option('mo2f_setup_complete') == 1)
			$this->mo2f_redirect_to_2fa_dashboard();
			
		// Clear out any old notices.
		$user = wp_get_current_user();
		$get_array = filter_input_array( INPUT_GET );
		if ( isset( $get_array['wizard_type'] ) ) {
			$wizard_type = sanitize_text_field( $get_array['wizard_type'] );
		} else {
			$wizard_type = 'default';
		}

		$wizard_steps = array(
			'welcome'                => array(
				'name'        => esc_html__( 'Welcome', 'miniorange-2-factor-authentication' ),
				'content'     => array( $this, 'mo2f_step_welcome' ),
				'wizard_type' => 'welcome_wizard',
			),
			'settings_configuration' => array(
				'name'        => esc_html__( 'Select 2FA Methods', 'miniorange-2-factor-authentication' ),
				'content'     => array( $this, 'mo2f_step_global_2fa_methods' ),
				'save'        => array( $this, 'mo2f_step_global_2fa_methods_save' ),
				'wizard_type' => 'welcome_wizard',
			),
			'finish'                 => array(
				'name'        => esc_html__( 'Setup Finish', 'miniorange-2-factor-authentication' ),
				'content'     => array( $this, 'mo2f_step_finish' ),
				'save'        => array( $this, 'mo2f_step_finish_save' ),
				'wizard_type' => 'welcome_wizard',
			),
		);
		$this->wizard_steps = apply_filters( 'mo2f_wizard_default_steps', $wizard_steps );

		// Set current step.
		$current_step       = ( isset( $_GET['current-step'] ) ) ? sanitize_text_field( wp_unslash( $_GET['current-step'] ) ) : ''; // phpcs:ignore
		$this->current_step = ! empty( $current_step ) ? $current_step : current( array_keys( $this->wizard_steps ) );

			$redirect_to_finish = add_query_arg(
				array(
					'current-step' => 'finish',
				)
			);
		wp_register_style( 'mo_2fa_admin_setupWizard'		, plugins_url('includes'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'setup-wizard.css', dirname(__FILE__) ) ,[],MO2F_VERSION);
		wp_enqueue_script('mo2f_setup_wizard',plugins_url('includes'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'setup-wizard.js', dirname(__FILE__) ),[],MO2F_VERSION);
		$save_step = ( isset( $_POST['save_step'] ) ) ? sanitize_text_field( wp_unslash( $_POST['save_step'] ) ) : ''; // phpcs:ignore
		if ( ! empty( $save_step ) && ! empty( $this->wizard_steps[ $this->current_step ]['save'] ) ) {
			call_user_func( $this->wizard_steps[ $this->current_step ]['save'] );
		}

		$this->mo2f_setup_page_header();
		$this->mo2f_setup_page_content();
		exit();
	}

	private function mo2f_setup_page_header() {
		?>
		<!DOCTYPE html>
		<html <?php language_attributes(); ?>>
		<head>
			<meta name="viewport" content="width=device-width" />
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<title><?php esc_html_e( 'miniOrange 2FA &rsaquo; Setup Wizard', 'miniorange-2-factor-authentication' ); ?></title>
			<?php
		wp_print_styles('mo_2fa_admin_setupWizard');
		wp_print_scripts( 'jquery' );
		wp_print_scripts( 'jquery-ui-core' );
		wp_print_scripts('mo2f_setup_wizard');
		?>
		<head>
		<body class="mo2f_body">
				<header class="mo2f-setup-wizard-header">
					<img width="70px" height="auto" src="<?php echo  plugin_dir_url(dirname(__FILE__)) . 'includes'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'miniorange-new-logo.png' ; ?>" alt="<?php esc_attr_e( 'miniOrange 2-factor Logo', 'miniorange-2-factor-authentication' ); ?>" >
					<h1> miniOrange 2-factor authentication Setup</h1>
				</header>
		<?php
	}

	private function mo2f_redirect_to_2fa_dashboard() {
		wp_safe_redirect(add_query_arg( 
			array('page' => 'mo_2fa_two_fa')
			, admin_url('admin.php')
		));
	}
	private function mo2f_get_next_step() {
		// Get current step.
		$current_step = $this->current_step;

		// Array of step keys.
		$keys = array_keys( $this->wizard_steps );
		if ( end( $keys ) === $current_step ) { // If last step is active then return WP Admin URL.
			return admin_url();
		}

		// Search for step index in step keys.
		$step_index = array_search( $current_step, $keys, true );
		if ( false === $step_index ) { // If index is not found then return empty string.
			return '';
		}

		// Return next step.
		return add_query_arg( 'current-step', $keys[ $step_index + 1 ] );
	}

	private function mo2f_setup_page_content() {
		?>
		<div class="mo2f-setup-content">
			<?php
			if ( ! empty( $this->wizard_steps[ $this->current_step ]['content'] ) ) {
				call_user_func( $this->wizard_steps[ $this->current_step ]['content'] );
			}
			?>
		</div>
		<?php
	}

	private function mo2f_step_welcome() {
		$this->mo2f_welcome_step( $this->mo2f_get_next_step() );
		
	}
	function mo2f_welcome_step( $next_step ) {
		$redirect = 'enforce-2fa';
		$admin_url = is_network_admin() ? network_admin_url().'admin.php?page=mo_2fa_two_fa' : admin_url().'admin.php?page=mo_2fa_two_fa';

		?>
		<h3><?php esc_html_e( 'Let us help you get started', 'miniorange-2-factor-authentication' ); ?></h3>
		<p class="mo2f-setup-wizard-font"><?php esc_html_e( 'This wizard will assist you with plugin configuration and the 2FA settings for you and the users on this website.', 'miniorange-2-factor-authentication' ); ?></p>

		<div class="mo2f-setup-actions">
			<a class="button button-primary"
				href="<?php echo esc_url( $next_step ); ?>">
				<?php esc_html_e( 'Let’s get started!', 'miniorange-2-factor-authentication' ); ?>
			</a>
			<a class="button button-secondary mo2f-first-time-wizard"
				href="<?php echo esc_url( $admin_url ); ?>">
				<?php esc_html_e( 'Skip Setup Wizard', 'miniorange-2-factor-authentication' ); ?>
			</a>
		</div>
		<?php
	}

	private function mo2f_setup_twofa() {
		do_action( 'mo2f_admin_setup_wizard_load_setup_wizard_before', $this );
		wp_enqueue_script('jquery');
		wp_localize_script(
		'wp-mo2f-setup-wizard',
		'mo2f_setup_wizard',[
			'ajax_url'   => admin_url( 'admin-ajax.php' ),
			'plugin_url' => get_site_option('siteurl'),
			'nonce'      => wp_create_nonce( 'mo2f-setup-wizard-nonce' )]
		);
		$obj = new Miniorange_Authentication();
		$obj->mo2f_setup_wizard_header();
		$obj->mo2f_setup_wizard_content();
		$obj->mo2f_setup_wizard_footer();
		exit;
	}
	private function mo2f_step_finish() {
		$this->mo2f_congratulations_step( true );
	}

	function mo2f_congratulations_step($setup_wizard){
		
		if ( $setup_wizard ) {
			$this->mo2f_congratulations_step_plugin_wizard();
			return;
		}
		?>

		<div class="mo2f-step-setting-wrapper active">
		<h3><?php esc_html_e( 'Congratulations! You are all set.', 'miniorange-2-factor-authentication' ); ?></h3>
		<div class="mo2f-setup-actions">
			<button class="mo2f-modal__btn button" data-close-2fa-modal aria-label="Close this dialog window"><?php esc_html_e( 'Close wizard', 'miniorange-2-factor-authentication' ); ?></button>
		</div>
		</div>
		<?php
	}
	public static function mo2f_congratulations_step_plugin_wizard() {
		$redirect_to_2fa = is_network_admin() ? network_admin_url().'admin.php?page=mo2f-setup-wizard-method' : admin_url().'admin.php?page=mo2f-setup-wizard-method';
		$redirect = is_network_admin() ? network_admin_url().'admin.php?page=mo_2fa_two_fa' : admin_url().'admin.php?page=mo_2fa_two_fa';
		update_site_option('mo2f_setup_complete',1);
		$user  = wp_get_current_user();
        $roles = ( array ) $user->roles;
        $two_fa_enabled  = 0;
		foreach ( $roles as $role ) {
            if(get_option('mo2fa_'.$role)=='1')
            	$two_fa_enabled=1;
        }
		$is_user_excluded = $two_fa_enabled != 1;
		$slide_title = ($is_user_excluded ) ? esc_html__( 'Congratulations.', 'miniorange-2-factor-authentication' ) : esc_html__( 'Congratulations, you\'re almost there...', 'miniorange-2-factor-authentication' );
		?>
		<h3><?php echo \esc_html( $slide_title ); ?></h3>
		<p><?php esc_html_e( 'Great job, the plugin and 2FA policies are now configured. You can always change the plugin settings and 2FA policies at a later stage from the miniOrange 2FA entry in the WordPress menu.', 'miniorange-2-factor-authentication' ); ?></p>

			<?php
			if ( $is_user_excluded ) {
				?>
		<div class="mo2f-setup-actions">
			<a href="<?php echo esc_url( $redirect ); ?>" class="button button-secondary mo2f-first-time-wizard">
					<?php esc_html_e( 'Close wizard', 'miniorange-2-factor-authentication' ); ?>
			</a>
		</div>
				<?php
			} else {
				?>
		<p><?php esc_html_e( 'Now you need to configure 2FA for your own user account. You can do this now (recommended) or later.', 'miniorange-2-factor-authentication' ); ?></p>
		<div class="mo2f-setup-actions">
			<a href="<?php echo esc_url( $redirect_to_2fa ); ?>" class="button button-secondary">
				<?php esc_html_e( 'Configure 2FA for yourself', 'miniorange-2-factor-authentication' ); ?>
			</a>
			<a href="<?php echo esc_url( $redirect ); ?>" class="button button-secondary mo2f-first-time-wizard">
					<?php esc_html_e( 'Close wizard & configure 2FA later', 'miniorange-2-factor-authentication' ); ?>
			</a>
		</div>
			<?php } ?>
		<?php
	}
	private function mo2f_step_finish_save() {
		// Verify nonce.
		wp_safe_redirect( esc_url_raw( $this->mo2f_get_next_step() ) );
		exit();
	}

	private function mo2f_step_global_2fa_methods() {
		?>
			<form method="post" class="mo2f-setup-form mo2f-form-styles" autocomplete="off">
				<?php wp_nonce_field( 'mo2f-step-choose-method' ); ?>
			<div class="mo2f-step-setting-wrapper active" data-step-title="<?php esc_html_e( 'Inline Registration', 'miniorange-2-factor-authentication' ); ?>">
				<?php $this->mo2f_inline_registration( true ); ?>
				<div class="mo2f-setup-actions">
					<a class="button button-primary" name="next_step_setting" onclick="mo2f_change_settings()" value="<?php esc_attr_e( 'Continue Setup', 'miniorange-2-factor-authentication' ); ?>"><?php esc_html_e( 'Continue Setup', 'miniorange-2-factor-authentication' ); ?></a>
				</div>
			</div>
			<div class="mo2f-step-setting-wrapper" data-step-title="<?php esc_html_e( 'Choose User roles', 'miniorange-2-factor-authentication' ); ?>">
				<?php $this->mo2f_select_user_roles( true ); ?>
				<div class="mo2f-setup-actions">
					<a class="button button-primary" name="next_step_setting" onclick="mo2f_change_settings()" value="<?php esc_attr_e( 'Continue Setup', 'miniorange-2-factor-authentication' ); ?>"><?php esc_html_e( 'Continue Setup', 'miniorange-2-factor-authentication' ); ?></a>
				</div>
			</div>

			<div class="mo2f-step-setting-wrapper" data-step-title="<?php esc_html_e( 'Grace period', 'miniorange-2-factor-authentication' ); ?>">
			<?php $this->mo2f_grace_period( true ); ?>
				<div class="mo2f-setup-actions">
					<button class="button button-primary save-wizard" type="submit" name="save_step" value="<?php esc_attr_e( 'All done', 'miniorange-2-factor-authentication' ); ?>"><?php esc_html_e( 'All done', 'miniorange-2-factor-authentication' ); ?></button>
				</div>
			</div>
			
			</form>
		<?php
	}

	function mo2f_inline_registration( $setup_wizard = false ) {
		?>
		<h3 id="mo2f_login_with_mfa_settings"><?php esc_html_e( 'Prompt users to setup 2FA after login? ', 'miniorange-2-factor-authentication' ); ?></h3>
		<p class="mo2f_description">
			<?php esc_html_e( 'When you enable this, the users will be prompted to set up the 2FA method after entering username and password. Users can select from the list of all 2FA methods. Once selected, user will setup and will login to the site ', 'miniorange-2-factor-authentication' ); ?><a href="https://plugins.miniorange.com/setup-login-with-any-configured-method-wordpress-2fa" target="_blank" rel=noopener><?php esc_html_e( 'Learn more.', 'miniorange-2-factor-authentication' ); ?></a>
		</p>
		<fieldset class="mo2f-contains-hidden-inputs">
			<label for="mo2f-use-inline-registration" style="margin-bottom: 10px; display: block;">
				<input type="radio" name="mo2f_policy[mo2f_inline_registration]" id="mo2f-use-inline-registration" value="1"
				<?php checked( get_site_option( 'mo2f_inline_registration' ), '1' ); ?>
				>
			<span><?php esc_html_e( 'Users should setup 2FA after first login.', 'miniorange-2-factor-authentication' ); ?></span>
			</label>
			<label for="mo2f-no-inline-registration">
				<input type="radio" name="mo2f_policy[mo2f_inline_registration]" id="mo2f-no-inline-registration" value="0"
				<?php checked( get_site_option( 'mo2f_inline_registration' ), '0' ); ?>
				>
				<span><?php esc_html_e( 'Users will setup 2FA in plugin dashboard', 'miniorange-2-factor-authentication' ); ?></span>
			</label>
		</fieldset>
		<?php
	}

	function mo2f_select_user_roles( $setup_wizard = false ) {
		?>
		<h3 id="mo2f_enforcement_settings"><?php esc_html_e( 'Do you want to enable 2FA for some, or all the user roles? ', 'miniorange-2-factor-authentication' ); ?></h3>
		<p class="mo2f_description">
			<?php esc_html_e( 'When you enable 2FA, the users will be prompted to configure 2FA the next time they login. Users have a grace period for configuring 2FA. You can configure the grace period and also exclude role(s) in this settings page. ', 'miniorange-2-factor-authentication' ); ?>
		</p>
			<?php
			if ( ! $setup_wizard ) {
				?>
		<table class="form-table">
			<tbody>
				<tr>
					<th><label for="mo2f-enforcement-policy"><?php esc_html_e( 'Enforce 2FA on', 'miniorange-2-factor-authentication' ); ?></label></th>
					<td>
			<?php } ?>
						<fieldset class="mo2f-contains-hidden-inputs">
							<div onclick="mo2f_toggle_select_roles_and_users()">
								<label for="mo2f-all-users" style="margin:.35em 0 .5em !important; display: block;">
									<input type="radio" name="mo2f_policy[mo2f-enforcement-policy]" id="mo2f-all-users" value="mo2f-all-users"
									<?php checked( get_site_option( 'mo2f-enforcement-policy' ), 'mo2f-all-users' ); ?>
									>
									<span><?php esc_html_e( 'All users', 'miniorange-2-factor-authentication' ); ?></span>
								</label>
							</div>
							<div onclick="mo2f_toggle_select_roles_and_users()">
								<label for="mo2f-certain-roles-only" style="margin:.35em 0 .5em !important; display: block;">
									<?php $checked = in_array( get_site_option( 'mo2f-enforcement-policy' ), array( 'mo2f-certain-roles-only', 'certain-users-only' ), true ); ?>
									<input type="radio" name="mo2f_policy[mo2f-enforcement-policy]" id="mo2f-certain-roles-only" value="mo2f-certain-roles-only"
									data-unhide-when-checked=".mo2f-grace-period-inputs"
									<?php ( $setup_wizard ) ? checked( get_site_option( 'mo2f-enforcement-policy' ), 'mo2f-certain-roles-only' ) : checked( $checked ); ?>
									>
									<span><?php esc_html_e( 'Only for specific roles', 'miniorange-2-factor-authentication' ); ?></span>
								</label>
							</div>
							<div id='mo2f-show-certain-roles-only' style="display:none;">
								<fieldset class="hidden mo2f-certain-roles-only-inputs">
									<div class="mo2f-line-height">
										<?php $this->mo2f_display_user_roles(); ?>
									</div>
								</fieldset>
							</div>
						</fieldset>
						<?php
						if ( ! $setup_wizard ) {
							?>
					</td>
				</tr>
			</tbody>
		</table>
							<?php
						}
	}

	function mo2f_display_user_roles(){
		global $wp_roles;
			if(is_multisite()){
				$first_role=array('superadmin'=>'Superadmin');
				$wp_roles->role_names = array_merge($first_role,$wp_roles->role_names);
			}
			?>
			<input type="button" class="button button-secondary" name="mo2f_select_all_roles" id="mo2f_select_all_roles" value="Select all"/>
			<?php
			 foreach($wp_roles->role_names as $id => $name) {
				$setting = get_site_option('mo2fa_'.$id);
				?>
				<div>
					<input type="checkbox" name="mo2f_policy[mo2f-enforce-roles][]" value="<?php echo 'mo2fa_'.esc_html($id); ?>"
						<?php
						
							if(get_site_option('mo2fa_'.$id))
								echo 'checked' ;
							else
								echo 'unchecked'; 
						?>/>
					<?php
					echo esc_html($name);
					?>					
				</div>
				<?php
			}
	}
	private function mo2f_step_global_2fa_methods_save() {
		// Check nonce.
		check_admin_referer( 'mo2f-step-choose-method' );
		$settings = ( isset( $_POST[ 'mo2f_policy' ] ) ) ? wp_unslash( $_POST[ 'mo2f_policy' ] ) : array(); // phpcs:ignore
		$this->mo2f_update_plugin_settings( $settings );
		wp_safe_redirect( esc_url_raw( $this->mo2f_get_next_step() ) );
		exit();
	}

	function mo2f_update_plugin_settings($settings){
		global $wp_roles;
		foreach($settings as $setting => $value){
			$setting = sanitize_text_field($setting);
			$value = sanitize_text_field($value);
		
		if($setting =='mo2f_grace_period_value')
		{
			update_site_option($setting,($value<=10 and $value>0)?floor($value):1);
		}else{
		update_site_option($setting,$value);
		}
		}
		$wp_roles = new WP_Roles();
		if(isset($settings['mo2f-enforcement-policy']) && $settings['mo2f-enforcement-policy'] == 'mo2f-all-users'){
			if (isset($wp_roles)){
				foreach($wp_roles->role_names as $role => $name) {
					update_option('mo2fa_'.$role, 1);
				}
			}
		}else if(isset($settings['mo2f-enforcement-policy']) && $settings['mo2f-enforcement-policy'] == 'mo2f-certain-roles-only' && isset($settings['mo2f-enforce-roles']) && is_array($settings['mo2f-enforce-roles'])){
				foreach($wp_roles->role_names as $role => $name) {
					if(in_array('mo2fa_'.$role,$settings['mo2f-enforce-roles']))
					update_option('mo2fa_'.$role, 1);
					else
					update_option('mo2fa_'.$role, 0);
				}
		}
	}
	function mo2f_grace_period( $setup_wizard = false ) {
		$grace_period = get_site_option('mo2f_grace_period');
		$testing = apply_filters( 'mo2f_allow_grace_period_in_seconds', false );
		if ( $testing ) {
			$grace_max = 600;
		} else {
			$grace_max = 10;
		}
		?>
		<h3><?php esc_html_e( 'Should users be given a grace period or should they be directly enforced for 2FA setup?', 'miniorange-2-factor-authentication' ); ?></h3>
			<p class="mo2f_description"><?php esc_html_e( 'When you configure the 2FA policies and require users to configure 2FA, they can either have a grace period to configure 2FA (users who don\'t have 2fa setup after grace period, will be enforced to setup 2FA ). Choose which method you\'d like to use:', 'miniorange-2-factor-authentication' ); ?></p>
		<fieldset class="mo2f-contains-hidden-inputs">
			<label for="mo2f-no-grace-period" style="margin-bottom: 10px; display: block;">
				<input type="radio" name="mo2f_policy[mo2f_grace_period]" id="mo2f-no-grace-period" value="off"
				<?php checked( get_site_option( 'mo2f_grace_period' ), 'off' ); ?>
				>
			<span><?php esc_html_e( 'Users should be directly enforced for 2FA setup.', 'miniorange-2-factor-authentication' ); ?></span>
			</label>

			<label for="mo2f-use-grace-period">
				<input type="radio" name="mo2f_policy[mo2f_grace_period]" id="mo2f-use-grace-period" value="on"
				<?php checked( get_site_option( 'mo2f_grace_period' ), 'on' ); ?>
				data-unhide-when-checked=".mo2f-grace-period-inputs">
				<span><?php esc_html_e( 'Give users a grace period to configure 2FA (Users will be enforced to setup 2FA after grace period expiry).', 'miniorange-2-factor-authentication' ); ?></span>
			</label>
			<fieldset class="mo2f-grace-period-inputs" <?php if(get_site_option( 'mo2f_grace_period' )) {echo "hidden";}?> hidden>
				<br/>
				<input type="number" id="mo2f-grace-period"  name="mo2f_policy[mo2f_grace_period_value]" value="<?php echo (get_site_option('mo2f_grace_period_value') )?esc_attr( get_site_option('mo2f_grace_period_value') ):1; ?>" min="1" max="<?php echo esc_attr( $grace_max ); ?>">
				<label class="radio-inline">
					<input class="js-nested" type="radio" name="mo2f_policy[mo2f_grace_period_type]" value="hours"
					<?php checked( get_site_option( 'mo2f_grace_period_type' ), 'hours' ); ?>
					>
					<?php esc_html_e( 'hours', 'miniorange-2-factor-authentication' ); ?>
				</label>
				<label class="radio-inline">
					<input class="js-nested" type="radio" name="mo2f_policy[mo2f_grace_period_type]" value="days"
					<?php checked( get_site_option( 'mo2f_grace_period_type' ), 'days' ); ?>
					>
					<?php esc_html_e( 'days', 'miniorange-2-factor-authentication' ); ?>
				</label>
				<?php
					$after_grace_content = apply_filters( 'mo2f_after_grace_period', '', '', 'mo2f_policy' );
					echo $after_grace_content; // phpcs:ignore
				?>
				<?php
				/**
				 * Via that, you can change the grace period TTL.
				 *
				 * @param bool - Default at this point is true - no method is selected.
				 */
				$testing = apply_filters( 'mo2f_allow_grace_period_in_seconds', false );
				if ( $testing ) {
					?>
					<label class="radio-inline">
						<input class="js-nested" type="radio" name="mo2f_policy[mo2f_grace_period_type]" value="seconds"
						<?php checked( get_site_option( 'mo2f_grace_period_type' ), 'seconds' ); ?>
						>
						<?php esc_html_e( 'Seconds', 'miniorange-2-factor-authentication' ); ?>
					</label>
					<?php
				}

				if ( $setup_wizard ) {
					$user                         = wp_get_current_user();
					$last_user_to_update_settings = $user->ID;

					?>
				<input type="hidden" id="mo2f_main_user" name="mo2f_policy[2fa_settings_last_updated_by]" value="<?php echo esc_attr( $last_user_to_update_settings ); ?>">
				<?php } else { ?>
					<p><?php esc_html_e( 'Note: If users do not configure it within the configured stipulated time, their account will be locked and have to be unlocked manually.', 'miniorange-2-factor-authentication' ); ?></p>
				<?php } ?>
			</fieldset>
			<br/>
		</fieldset>
		<script>
			jQuery(document).ready(function($){
				jQuery("#mo2f-use-grace-period").click(function()
                {
				
						jQuery("#mo2f-grace-period").focus();
                });
				jQuery(".radio-inline").click(function()
                {
						
						jQuery("#mo2f-grace-period").focus();
                });
			});
			</script>
		<?php
	}
}
