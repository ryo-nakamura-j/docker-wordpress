<?php

/**
 * Tools admin page class.
 *
 * @package    WPPopups
 * @author     WPPopups
 * @since 2.0.0
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2017, WP Popups LLC
 */
class WPPopups_Tools {

	/**
	 * The current active tab.
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	public $view;

	/**
	 * Template code if generated.
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	private $template = false;


	/**
	 * The available popups.
	 *
	 * @since 2.0.0
	 *
	 * @var array
	 */
	public $popups = false;

	/**
	 * The core views.
	 *
	 * @since 2.0.0
	 *
	 * @var array
	 */
	public $views = [];

	private $old_popups = [];

	/**
	 * Primary class constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		// Maybe load tools page.
		add_action( 'admin_init', [ $this, 'init' ] );
	}

	/**
	 * Determining if the user is viewing the tools page, if so, party on.
	 *
	 * @since 2.0.0
	 */
	public function init() {

		// Define the core views for the tools tab.
		$this->views = apply_filters(
			'wppopups_tools_views',
			[
				esc_html__( 'Import', 'wp-popups-lite' )      => [ 'import' ],
				esc_html__( 'Export', 'wp-popups-lite' )      => [ 'export' ],
				esc_html__( 'System Info', 'wp-popups-lite' ) => [ 'system' ],
			]
		);

		// Check what page we are on.
		$page = isset( $_GET['page'] ) ? sanitize_key( $_GET['page'] ) : '';

		// Only load if we are actually on the settings page.
		if ( 'wppopups-tools' === $page ) {

			// Determine the current active settings tab.
			$this->view = ! empty( $_GET['view'] ) ? esc_html( $_GET['view'] ) : 'import';

			// If the user tries to load a invalid view fallback to import.
			if (
				! in_array( $this->view, call_user_func_array( 'array_merge', array_values( $this->views ) ), true ) &&
				! has_action( 'wppopups_tools_display_tab_' . sanitize_key( $this->view ) )
			) {
				$this->view = 'import';
			}

			// Retrieve available popups.
			$this->popups = wppopups()->popups->get(
				'', [
					'orderby'     => 'title',
					'post_status' => [ 'publish', 'draft' ],
				]
			);

			$this->old_popups = get_posts( [
				'post_type'   => 'spucpt',
				'post_status' => [ 'publish', 'draft' ],
				'numberposts' => - 1,
				'meta_query'  => [
					[
						'key'     => 'spu_ab_parent',
						'compare' => 'NOT EXISTS',
					],
				],
			] );

			add_action( 'wppopups_tools_init', [ $this, 'import_export_process' ] );
			add_action( 'wppopups_admin_page', [ $this, 'output' ] );

			// Hook for addons.
			do_action( 'wppopups_tools_init' );
		}
	}

	/**
	 * Build the output for the Tools admin page.
	 *
	 * @since 2.0.0
	 */
	public function output() {

		set_time_limit( 0 );

		$show_nav = false;
		foreach ( $this->views as $view ) {
			if ( in_array( $this->view, (array) $view, true ) ) {
				$show_nav = true;
				break;
			}
		}
		?>

		<div id="wppopups-tools" class="wrap wppopups-admin-wrap">

			<?php
			if ( $show_nav ) {
				echo '<ul class="wppopups-admin-tabs">';
				foreach ( $this->views as $label => $view ) {
					$view  = (array) $view;
					$class = in_array( $this->view, $view, true ) ? ' class="active"' : '';
					echo '<li>';
					printf(
						'<a href="%s"%s>%s</a>',
						admin_url( 'admin.php?page=wppopups-tools&view=' . sanitize_key( $view[0] ) ),
						$class,
						esc_html( $label )
					);
					echo '</li>';
				}
				echo '</ul>';
			}
			?>

			<h1 class="wppopups-h1-placeholder"></h1>

			<?php
			if ( isset( $_GET['wppopups_notice'] ) && 'popups-imported' === $_GET['wppopups_notice'] ) {
				?>
				<div class="updated notice is-dismissible">
					<p>
						<?php
						printf(
							wp_kses(
							/* translators: %s - Popups list page URL. */
								__( 'Import was successfully finished. Please go and <a href="%s">publish your popups</a>.', 'wp-popups-lite' ),
								[
									'a' => [
										'href' => [],
									],
								]
							),
							admin_url( 'admin.php?page=wppopups-overview' )
						);
						?>
					</p>
				</div>
				<?php
			}
			?>

			<div class="wppopups-admin-content wppopups-admin-settings">
				<?php
				switch ( $this->view ) {
					case 'system':
						$this->system_info_tab();
						break;
					case 'export':
						$this->export_tab();
						break;
					case 'import':
						$this->import_tab();
						break;
					default:
						do_action( 'wppopups_tools_display_tab_' . sanitize_key( $this->view ) );
						break;
				}
				?>
			</div>
		</div>
		<?php
	}

	/**
	 * Import tab contents.
	 *
	 * @since 2.0.0
	 */
	public function import_tab() {
		if ( ! empty( $this->old_popups ) ) : ?>

			<div class="wppopups-setting-row tools">
				<h3><?php esc_html_e( 'Popups legacy plugin', 'wp-popups-lite' ); ?></h3>

				<p><?php esc_html_e( 'We have detected that you were using the old version of the Popups plugin. You can import old popups to the new version below.', 'wp-popups-lite' ); ?></p>

				<form method="post"
				      action="<?php echo admin_url( 'admin.php?page=wppopups-tools&view=import_old' ); ?>">
					<input type="hidden" name="action" value="import_old_popup">

					<span class="choicesjs-select-wrap">
						<select id="wppopups-tools-form-import-old" class="choicesjs-select" name="popups[]" multiple
						        data-placeholder="<?php echo esc_attr__( 'Select popup(s)', 'wp-popups-lite' ); ?>">
						<?php
						foreach ( $this->old_popups as $popup ) {
							printf( '<option value="%d">%s</option>', $popup->ID, esc_html( $popup->post_title ) );
						}
						?>
						</select>
					</span>

					<br/>
					<button type="submit" name="submit-importexport"
					        class="wppopups-btn wppopups-btn-md wppopups-btn-blue"><?php esc_html_e( 'Import legacy popups', 'wp-popups-lite' ); ?></button>
					<?php wp_nonce_field( 'wppopups_import_nonce', 'wppopups-tools-importexport-nonce' ); ?>
				</form>
			</div>
		<?php endif; ?>
		<div class="wppopups-setting-row tools">
			<h3><?php esc_html_e( 'WP Popups Import', 'wp-popups-lite' ); ?></h3>
			<p><?php esc_html_e( 'Select a WP Popups export file.', 'wp-popups-lite' ); ?></p>

			<form method="post" enctype="multipart/form-data"
			      action="<?php echo admin_url( 'admin.php?page=wppopups-tools&view=import' ); ?>">
				<div class="wppopups-file-upload">
					<input type="file" name="file" id="wppopups-tools-form-import" class="inputfile"
					       data-multiple-caption="<?php esc_attr_e( '{count} files selected', 'wp-popups-lite' ); ?>"
					       accept=".json"/>
					<label for="wppopups-tools-form-import">
						<span class="fld"><span
									class="placeholder"><?php esc_html_e( 'No file chosen', 'wp-popups-lite' ); ?></span></span>
						<strong class="wppopups-btn wppopups-btn-md wppopups-btn-light-grey">
							<i class="fa fa-upload"
							   aria-hidden="true"></i> <?php esc_html_e( 'Choose a file&hellip;', 'wp-popups-lite' ); ?>
						</strong>
					</label>
				</div>
				<br>
				<input type="hidden" name="action" value="import_popup">
				<button type="submit" name="submit-importexport"
				        class="wppopups-btn wppopups-btn-md wppopups-btn-blue"><?php esc_html_e( 'Import', 'wp-popups-lite' ); ?></button>
				<?php wp_nonce_field( 'wppopups_import_nonce', 'wppopups-tools-importexport-nonce' ); ?>
			</form>
		</div>

		<?php
	}

	/**
	 * Export tab contents.
	 *
	 * @since 2.0.0
	 */
	public function export_tab() {

		?>

		<div class="wppopups-setting-row tools">

			<h3 id="form-export"><?php esc_html_e( 'Popup Export', 'wp-popups-lite' ); ?></h3>

			<p><?php esc_html_e( 'Popup exports files can be used to create a backup of your popups or to import popups into another site.', 'wp-popups-lite' ); ?></p>

			<form method="post" action="<?php echo admin_url( 'admin.php?page=wppopups-tools&view=export' ); ?>">
				<?php
				if ( ! empty( $this->popups ) ) {
					echo '<span class="choicesjs-select-wrap">';
					echo '<select id="wppopups-tools-form-export" class="choicesjs-select" name="popups[]" multiple data-placeholder="' . esc_attr__( 'Select popup(s)', 'wp-popups-lite' ) . '">';
					foreach ( $this->popups as $popup ) {
						printf( '<option value="%d">%s</option>', $popup->id, esc_html( $popup->title ) );
					}
					echo '</select>';
					echo '</span>';
				} else {
					echo '<p>' . esc_html__( 'You need to create a popup before you can use popup export.', 'wp-popups-lite' ) . '</p>';
				}
				?>
				<br>
				<input type="hidden" name="action" value="export_popup">
				<?php wp_nonce_field( 'wppopups_import_nonce', 'wppopups-tools-importexport-nonce' ); ?>
				<button type="submit" name="submit-importexport"
				        class="wppopups-btn wppopups-btn-md wppopups-btn-blue"><?php esc_html_e( 'Export', 'wp-popups-lite' ); ?></button>
			</form>
		</div>
		<div class="wppopups-setting-row tools">

			<h3 id="template-export"><?php esc_html_e( 'Popup Template Export', 'wp-popups-lite' ); ?></h3>

			<?php
			if ( $this->template ) {
				echo '<p>' . esc_html__( 'The following code can be used to register your custom popup template. Copy and paste the following code to your theme\'s functions.php file or include it within an external file.', 'wp-popups-lite' ) . '<p>';
				echo '<p>' .
				     sprintf(
					     wp_kses(
						     __( 'For more information <a href="%s" target="_blank" rel="noopener noreferrer">see our documentation</a>.', 'wp-popups-lite' ),
						     [
							     'a' => [
								     'href'   => [],
								     'target' => [],
								     'rel'    => [],
							     ],
						     ]
					     ),
					     'https://wppopups.com/docs/how-to-create-a-custom-popup-template/'
				     ) .
				     '<p>';
				echo '<textarea class="info-area" readonly>' . esc_textarea( $this->template ) . '</textarea><br>';
			}
			?>

			<p><?php esc_html_e( 'Select a popup to generate PHP code that can be used to register a custom popup template.', 'wp-popups-lite' ); ?></p>

			<form method="post"
			      action="<?php echo esc_url( admin_url( 'admin.php?page=wppopups-tools&view=export#template-export' ) ); ?>">
				<?php
				if ( ! empty( $this->popups ) ) {
					echo '<span class="choicesjs-select-wrap">';
					echo '<select id="wppopups-tools-form-template" class="choicesjs-select" name="popup">';
					foreach ( $this->popups as $popup ) {
						printf( '<option value="%d">%s</option>', $popup->id, esc_html( $popup->title ) );
					}
					echo '</select>';
					echo '</span>';
				} else {
					echo '<p>' . esc_html__( 'You need to create a popup before you can generate a template.', 'wp-popups-lite' ) . '</p>';
				}
				?>
				<br>
				<input type="hidden" name="action" value="export_template">
				<?php wp_nonce_field( 'wppopups_import_nonce', 'wppopups-tools-importexport-nonce' ); ?>
				<button type="submit" name="submit-importexport"
				        class="wppopups-btn wppopups-btn-md wppopups-btn-blue"><?php esc_html_e( 'Export Template', 'wp-popups-lite' ); ?></button>
			</form>

		</div>
		<?php
	}

	/**
	 * System Info tab contents.
	 *
	 * @since 2.0.0
	 */
	public function system_info_tab() {

		?>

		<div class="wppopups-setting-row tools">
			<h3 id="form-export"><?php esc_html_e( 'System Information', 'wp-popups-lite' ); ?></h3>
			<textarea readonly="readonly" class="info-area"><?php echo $this->get_system_info(); ?></textarea>
		</div>

		<div class="wppopups-setting-row tools">
			<h3 id="ssl-verify"><?php esc_html_e( 'Test SSL Connections', 'wp-popups-lite' ); ?></h3>
			<p><?php esc_html_e( 'Click the button below to verify your web server can perform SSL connections successfully.', 'wp-popups-lite' ); ?></p>
			<button type="button" id="wppopups-ssl-verify"
			        class="wppopups-btn wppopups-btn-md wppopups-btn-blue"><?php esc_html_e( 'Test Connection', 'wp-popups-lite' ); ?></button>
		</div>

		<?php
	}

	/**
	 * Import/Export processing.
	 *
	 * @since 2.0.0
	 */
	public function import_export_process() {

		// Check for triggered save.
		if (
			empty( $_POST['wppopups-tools-importexport-nonce'] ) ||
			empty( $_POST['action'] ) ||
			! isset( $_POST['submit-importexport'] )
		) {
			return;
		}

		// Check for valid nonce and permission.
		if (
			! wp_verify_nonce( $_POST['wppopups-tools-importexport-nonce'], 'wppopups_import_nonce' ) ||
			! wppopups_current_user_can()
		) {
			return;
		}

		// Export Popup(s).
		if ( 'export_popup' === $_POST['action'] && ! empty( $_POST['popups'] ) ) {

			$export = [];
			$popups = get_posts(
				[
					'post_type'     => 'wppopups',
					'numberposts'   => - 1,
					'no_found_rows' => true,
					'post_status'   => [ 'publish', 'draft' ],
					'nopaging'      => true,
					'post__in'      => array_map( 'intval', $_POST['popups'] ),
				]
			);

			foreach ( $popups as $popup ) {
				$export[] = wppopups_decode( $popup->post_content );
			}

			$this->set_time_out();

			nocache_headers();
			header( 'Content-Type: application/json; charset=utf-8' );
			header( 'Content-Disposition: attachment; filename=wppopups-export-' . date( 'm-d-Y' ) . '.json' );
			header( 'Expires: 0' );

			echo wp_json_encode( $export );
			exit;
		}

		// Import Popup(s).
		if ( 'import_popup' === $_POST['action'] && ! empty( $_FILES['file']['tmp_name'] ) ) {

			$ext = strtolower( pathinfo( $_FILES['file']['name'], PATHINFO_EXTENSION ) );

			if ( 'json' !== $ext ) {
				wp_die(
					esc_html__( 'Please upload a valid .json popup export file.', 'wp-popups-lite' ),
					esc_html__( 'Error', 'wp-popups-lite' ),
					[
						'response' => 400,
					]
				);
			}

			$popups = json_decode( file_get_contents( $_FILES['file']['tmp_name'] ), true );

			if ( ! empty( $popups ) ) {

				foreach ( $popups as $popup ) {

					$title  = ! empty( $popup['settings']['popup_title'] ) ? $popup['settings']['popup_title'] : '';
					$desc   = ! empty( $popup['settings']['popup_desc'] ) ? $popup['settings']['popup_desc'] : '';
					$new_id = wp_insert_post(
						[
							'post_title'   => $title,
							'post_status'  => 'publish',
							'post_type'    => 'wppopups',
							'post_excerpt' => $desc,
						]
					);
					if ( $new_id ) {
						$popup['id'] = $new_id;
						wppopups()->popups->update( $new_id, $popup, [ 'post_status' => 'draft' ] );
					}
				}
				wp_safe_redirect( admin_url( 'admin.php?page=wppopups-tools&view=importexport&wppopups_notice=popups-imported' ) );
				exit;
			}
		}
		// Import Old Popup(s).
		if ( 'import_old_popup' === $_POST['action'] && ! empty( $_POST['popups'] ) ) {
			update_option( 'wppopups_upgraded_from_1x', true );
			$this->set_time_out();

			$array_abgroup = [];
			$popups        = get_posts(
				[
					'post_type'   => 'spucpt',
					'numberposts' => - 1,
					'post_status' => [ 'publish', 'draft' ],
					'post__in'    => array_map( 'absint', $_POST['popups'] ),
				]
			);

			foreach ( $popups as $popup ) {

				$new_id = $this->legacy_import( $popup );

				$abgroup = wppopups_legacy_box_abgroup( $popup->ID );

				if ( $abgroup ) {
					$array_abgroup[ $popup->ID ] = $new_id;
				}
			}


			// Check if there is A/B testing popups
			if ( count( $array_abgroup ) > 0 ) {

				foreach ( $array_abgroup as $old_popup_id => $new_popup_id ) {

					$args = [
						'post_type'     => 'spucpt',
						'no_found_rows' => true,
						'nopaging'      => true,
						'meta_query'    => [
							[
								'key'     => 'spu_ab_parent',
								'value'   => $old_popup_id,
								'compare' => '=',
							],
						],
					];

					$children = new WP_Query( $args );

					$i = 1;
					foreach ( $children->get_posts() as $child ) {
						$new_id = $this->legacy_import( $child, $new_popup_id );
					}
				}

			}

			wp_safe_redirect( admin_url(
				'admin.php?page=wppopups-tools&view=importexport&wppopups_notice=popups-imported'
			) );
			exit;
		}
		// export template
		if ( 'export_template' === $_POST['action'] && ! empty( $_POST['popup'] ) ) {

			$popup = wppopups()->popups->get( absint( $_POST['popup'] ) );

			if ( ! $popup ) {
				return;
			}

			// Define basic data.
			$name  = sanitize_text_field( $popup->title );
			$slug  = sanitize_key( str_replace( ' ', '_', $popup->title ) );
			$class = 'WPPopups_Template_' . $slug;

			// Format template field and settings data.
			$data                     = $popup->data;
			$data['meta']['template'] = $slug;

			// remove non template fields
			unset( $data['id'] );
			unset( $data['rules'] );
			unset( $data['settings'] );
			unset( $data['triggers'] );
			unset( $data['meta'] );

			$data = var_export( $data, true );
			$data = str_replace( '  ', "\t", $data );
			$data = preg_replace( '/([\t\r\n]+?)array/', 'array', $data );

			// Build the final template string.
			$this->template = <<<EOT
if ( class_exists( 'WPPopups_Template', false ) ) :
/**
 * {$name}
 * Template for WPPopups.
 */
class {$class} extends WPPopups_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 2.0.0
	 */
	public function init() {

		// Template name
		\$this->name = '{$name}';

		// Template slug
		\$this->slug = '{$slug}';
		
		// Template description
		\$this->description = 'Add your description here';

		// Template field and settings
		\$this->data = {$data};
	}
}
new {$class};
endif;
EOT;
		} // End if().
	}


	/**
	 * Legacy Import
	 *
	 * @param OBJECT $popup post object
	 * @param INT $parent_id A/B testing parent
	 *
	 * @return INT $new_id        post ID
	 */
	public function legacy_import( $popup, $parent_id = 0 ) {

		$opts  = wppopups_legacy_box_options( $popup->ID );
		$rules = wppopups_legacy_box_rules( $popup->ID );

		$popup_hidden_class = $sticky_title = $sticky_title_color = $sticky_title_size = $sticky_title_bg = '';

		$i      = $j = 0;
		$irules = [];

		foreach ( $rules as $groups ) {

			foreach ( $groups as $items ) {

				$irules[ 'group_' . $i ][ 'rule_' . $j ]['rule']     = wppopups_sanitize_key( $items['param'] );
				$irules[ 'group_' . $i ][ 'rule_' . $j ]['operator'] = sanitize_text_field( $items['operator'] );
				$irules[ 'group_' . $i ][ 'rule_' . $j ]['value']    = sanitize_text_field( $items['value'] );
				$j ++;
			}

			$i ++;
		}


		// Default
		$css           = $opts['css'];
		$border_type   = wppopups_sanitize_key( $css['border_type'] );
		$border_color  = wppopups_sanitize_hex_color( $css['border_color'] );
		$border_width  = intval( $css['border_width'] );
		$border_radius = intval( $css['border_radius'] );
		$border_margin = 14;

		// Overlay Default
		$overlay_color = wppopups_hex2rgba(
			isset( $css['overlay_color'] ) ? $css['overlay_color'] : '',
			isset( $css['bgopacity'] ) ? $css['bgopacity'] : false
		);

		// BG Default
		$bg_color = wppopups_hex2rgba(
			isset( $css['background_color'] ) ? $css['background_color'] : '',
			isset( $css['background_opacity'] ) ? $css['background_opacity'] : false
		);


		$bg_img        = isset( $css['bgimage'] ) ? esc_url( $css['bgimage'] ) : '';
		$bg_img_repeat = isset( $css['bg_repeat'] ) ? wppopups_sanitize_key( $css['bg_repeat'] ) : '';
		$bg_img_size   = isset( $css['bg_size'] ) ? wppopups_sanitize_key( $css['bg_size'] ) : '';


		// Content
		$content = $popup->post_content;

		$dom = new DOMDocument;
		if ( function_exists( 'mb_convert_encoding' ) ) {
			$html = mb_convert_encoding( $content, 'HTML-ENTITIES', 'UTF-8' );
		} else {
			$html = htmlspecialchars_decode( utf8_decode( htmlentities( $content, ENT_COMPAT, 'utf-8', false ) ) );
		}

		$dom->loadHTML( $html );

		$form = $dom->getElementById( 'spu-optin-form' );
		if ( $form ) {
			$form->parentNode->removeChild( $form );
		}
		$finder    = new DomXPath( $dom );
		$classname = 'spu-fields-container';
		$nodes     = $finder->query( "//*[contains(@class, '$classname')]" );

		if ( $nodes ) {
			foreach ( $nodes as $node ) {
				$node->parentNode->removeChild( $node );
			}
		}

		$content = $dom->saveHTML();

		// Theme Class
		if ( isset( $opts['optin_theme'] ) && ! empty( $opts['optin_theme'] ) ) {

			switch ( $opts['optin_theme'] ) {
				case 'postal' :
				case 'cta' :
					$popup_hidden_class = 'spu-theme-' . wppopups_sanitize_key( $opts['optin_theme'] );

					if ( empty( $bg_color ) ) {
						$bg_color = wppopups_hex2rgba( '#FFFFFF', 1 );
					}

					break;

				case 'coupon' :
					$popup_hidden_class = 'spu-theme-coupon';

					$border_type   = 'dashed';
					$border_color  = '#000000';
					$border_width  = 10;
					$border_radius = 0;

					if ( empty( $bg_color ) ) {
						$bg_color = wppopups_hex2rgba( '#FFFFAD', 1 );
					}

					break;

				case 'simple':
				case 'blank':
					$popup_hidden_class = '';

					if ( empty( $bg_color ) ) {
						$bg_color = wppopups_hex2rgba( '#FFFFFF', 1 );
					}

					break;

				case 'bar':
					$popup_hidden_class = '';
					$position           = 'bottom-bar';

					if ( empty( $bg_color ) ) {
						$bg_color = wppopups_hex2rgba( '#FFFFFF', 1 );
					}

					if ( empty( $overlay_color ) ) {
						$overlay_color = wppopups_hex2rgba( '#000000', 0.5 );
					}

					break;
			}
		}


		// Animations
		switch ( $opts['animation'] ) {
			case 'wiggle'        :
				$animation = 'wobble';
				break;
			case 'speedy-left'    :
				$animation = 'slideInLeft';
				break;
			case 'speedy-right'    :
				$animation = 'slideInRight';
				break;
			case 'rotate-in'    :
				$animation = 'rotateIn';
				break;
			default :
				$animation = wppopups_sanitize_key( $opts['animation'] );
		}


		// Triggers
		switch ( $opts['trigger'] ) {
			case 'seconds' :
			case 'percentage' :
			case 'pixels' :
				$trigger_key   = wppopups_sanitize_key( $opts['trigger'] );
				$trigger_value = intval( $opts['trigger_number'] );
				break;

			case 'manual' :
				$trigger_key   = 'class';
				$trigger_value = sanitize_html_class( 'spu-open-' . intval( $popup->ID ) );
				break;

			case 'trigger-click' :
				$trigger_key   = 'class';
				$trigger_value = sanitize_html_class( $opts['trigger_value'] );
				break;

			case 'visible' :
				$trigger_key   = wppopups_sanitize_key( $opts['trigger'] );
				$trigger_value = sanitize_html_class( $opts['trigger_value'] );
				break;

			case 'exit-intent' :
				$trigger_key   = 'exit';
				$trigger_value = '';
				break;
		}


		// Positions
		switch ( $opts['css']['position'] ) {

			case 'after-content'    :
				$position = 'after-post';
				break;
			case 'full-screen'        :
				$position = 'fullscreen';
				break;
			default :
				$position = wppopups_sanitize_key( $opts['css']['position'] );
		}


		// Sticky
		if ( in_array( $position, [ 'sticky-left', 'sticky-right' ] ) ) {
			$sticky_title       = sanitize_text_field( get_the_title( $popup->ID ) );
			$sticky_title_color = isset( $opts['css']['sticky_color'] ) ? wppopups_hex2rgba( $opts['css']['sticky_color'] ) : '';
			$sticky_title_size  = '18px';
			$sticky_title_bg    = isset( $opts['css']['sticky_bg'] ) ? wppopups_hex2rgba( $opts['css']['sticky_bg'] ) : '';

			// Triggers
			$trigger_key   = 'seconds';
			$trigger_value = 5;

			// Animation
			$animation = 'disable';
		}


		$data = [
			'content'   => [
				//'popup_content'	=> wp_kses_post($content),
				'popup_content' => $content,
			],
			'position'  => [
				'position'           => $position,
				'sticky_title'       => $sticky_title,
				'sticky_title_color' => $sticky_title_color,
				'sticky_title_size'  => $sticky_title_size,
				'sticky_title_bg'    => $sticky_title_bg,
			],
			'animation' => [
				'animation' => $animation,
			],
			'popup_box' => [
				'width'       => wppopups_sanitize_key( $opts['css']['width'] ),
				'padding'     => intval( $opts['css']['padding'] ),
				'auto_height' => 'yes',
				'radius'      => '0',
				'height'      => '430px',
			],
			'colors'    => [
				'overlay_color' => $overlay_color,
				'bg_color'      => $bg_color,
				'bg_img'        => $bg_img,
				'bg_img_repeat' => $bg_img_repeat,
				'bg_img_size'   => $bg_img_size,
			],
			'border'    => [
				'border_type'   => $border_type,
				'border_color'  => $border_color,
				'border_width'  => $border_width,
				'border_radius' => $border_radius,
				'border_margin' => $border_margin,
			],
			'shadow'    => [
				'shadow_color'    => wppopups_sanitize_hex_color( $opts['css']['shadow_color'] ),
				'shadow_type'     => wppopups_sanitize_key( $opts['css']['shadow_type'] ),
				'shadow_x_offset' => intval( $opts['css']['shadow_x_offset'] ),
				'shadow_y_offset' => intval( $opts['css']['shadow_y_offset'] ),
				'shadow_blur'     => intval( $opts['css']['shadow_blur'] ),
				'shadow_spread'   => intval( $opts['css']['shadow_spread'] ),
			],
			'close'     => [
				'close_color'        => wppopups_sanitize_hex_color( $opts['css']['close_color'] ),
				'close_hover_color'  => wppopups_sanitize_hex_color( $opts['css']['close_hover_color'] ),
				'close_shadow_color' => wppopups_sanitize_hex_color( $opts['css']['close_shadow_color'] ),
				'close_size'         => intval( $opts['css']['close_size'] ),
				'close_position'     => wppopups_sanitize_key( $opts['css']['close_position'] ),
			],
			'css'       => [
				'custom_css' => '',
			],
			'settings'  => [
				'popup_title'                => sanitize_text_field( get_the_title( $popup->ID ) ),
				'popup_desc'                 => '',
				'test_mode'                  => wppopups_sanitize_key( $opts['test_mode'] ),
				'powered_link'               => wppopups_sanitize_key( $opts['powered_link'] ),
				'popup_class'                => '',
				'popup_hidden_class'         => $popup_hidden_class,
				'close_on_conversion'        => wppopups_sanitize_key( $opts['conversion_close'] ),
				'autoclose'                  => isset( $opts['autoclose'] ) ? intval( $opts['autoclose'] ) : 0,
				'disable_close'              => isset( $opts['disable_close'] ) ? intval( $opts['disable_close'] ) : 0,
				'advanced_close'             => isset( $opts['disable_advanced_close'] ) ? intval( $opts['disable_advanced_close'] ) : 0,
				'conversion_cookie_name'     => sanitize_text_field( $opts['name-convert-cookie'] ),
				'conversion_cookie_duration' => intval( $opts['duration-convert-cookie'] ),
				'conversion_cookie_type'     => wppopups_sanitize_key( $opts['type-convert-cookie'] ),
				'closing_cookie_name'        => sanitize_text_field( $opts['name-close-cookie'] ),
				'closing_cookie_duration'    => intval( $opts['duration-close-cookie'] ),
				'closing_cookie_type'        => wppopups_sanitize_key( $opts['type-close-cookie'] ),
				'auto_hide'                  => isset( $opts['auto_hide'] ) ? intval( $opts['auto_hide'] ) : 0,
			],
			'triggers'  => [
				'trigger_0' => [
					'trigger' => $trigger_key,
					'value'   => $trigger_value,
				],
			],
			'rules'     => $irules,

			'fields' => [
				//'optin_form_css'	=> "class_one class_two",
				'email_field_text' => isset( $opts['optin_placeholder'] ) ? sanitize_text_field( $opts['optin_placeholder'] ) : '',
				'name_field'       => isset( $opts['optin_display_name'] ) ? intval( $opts['optin_display_name'] ) : 0,
				'name_field_text'  => isset( $opts['optin_name_placeholder'] ) ? sanitize_text_field( $opts['optin_name_placeholder'] ) : '',
				'submit_text'      => isset( $opts['optin_submit'] ) ? sanitize_text_field( $opts['optin_submit'] ) : '',
				//'submit_processing_text'	=>'Sending...',
				//'submit_css'		=>'button_class one_class two_class',
				'gdpr_field'       => isset( $opts['optin_gdpr'] ) ? intval( $opts['optin_gdpr'] ) : 0,
				'gdpr_field_text'  => isset( $opts['optin_txtgdpr'] ) ? sanitize_text_field( $opts['optin_txtgdpr'] ) : '',
				'gdpr_url'         => '',
			],

			'optin_styles' => [
				'inline_fields'         => 0,
				'submit_text_color'     => isset( $css['button_color'] ) ? wppopups_sanitize_hex_color( $css['button_color'] ) : '',
				'submit_bg_color'       => isset( $css['button_bg'] ) ? wppopups_sanitize_hex_color( $css['button_bg'] ) : '',
				'submit_bg_color_hover' => isset( $css['button_bg'] ) ? wppopups_sanitize_hex_color( $css['button_bg'] ) : '',
				'submit_border_color'   => isset( $css['button_bg'] ) ? wppopups_sanitize_hex_color( $css['button_bg'] ) : '',
			],

			'success' => [
				'optin_success'         => isset( $opts['optin_success'] ) ? sanitize_text_field( $opts['optin_success'] ) : '',
				'optin_success_seconds' => 0,
			],

			'redirect' => [
				'optin_redirect' => isset( $opts['optin_redirect'] ) ? esc_url( $opts['optin_redirect'] ) : '',
				'pass_lead_data' => isset( $opts['optin_pass_redirect'] ) ? intval( $opts['optin_pass_redirect'] ) : 0,
			],
		];


		// Providers
		if ( wppopups()->pro && isset( $opts['optin'] ) && ! empty( $opts['optin'] ) && $opts['optin'] != 'custom' ) {
			$connection_id = 'connection_' . uniqid();

			$slug = wppopups_sanitize_key( $opts['optin'] );

			$integrations = get_option( 'spu_integrations' );

			$connection_data = [];

			switch ( $slug ) {
				case 'mailchimp' :
					$provider = new WPPopups_MailChimp();
					$fields   = [
						'apikey' => $integrations['mailchimp']['mc_api'],
						'label'  => get_the_title( $popup->ID ),
					];

					$account_id = $provider->api_auth( $fields );
					$list_id    = isset( $opts['optin_list'] ) ? $opts['optin_list'] : '';

					$connection_data = [
						'connection_name' => get_the_title( $popup->ID ),
						'account_id'      => $account_id,
						'list_id'         => $list_id,
						//'groups'			=> $opts['optin_list_segments']
						'options'         => [
							'doubleoptin'   => 0,
						],
					];
					break;

				case 'ccontact' :

					$provider = new WPPopups_Constant_Contact();
					$fields   = [
						'authcode' => $integrations['ccontact']['ccontact_auth'],
						'label'    => get_the_title( $popup->ID ),
					];

					$account_id = $provider->api_auth( $fields );
					$list_id    = isset( $opts['optin_list'] ) ? $opts['optin_list'] : '';

					$slug            = 'constant-contact';
					$connection_data = [
						'connection_name' => get_the_title( $popup->ID ),
						'account_id'      => $account_id,
						'list_id'         => $list_id,
					];
					break;

				case 'activecampaign' :
					if ( ! class_exists( 'WPPopups_ActiveCampaign' ) ) {
						break;
					}

					$provider = new WPPopups_ActiveCampaign();
					$fields   = [
						'apiurl' => $integrations['activecampaign']['ac_url'],
						'apikey' => $integrations['activecampaign']['ac_api'],
						'label'  => get_the_title( $popup->ID ),
					];

					$account_id = $provider->api_auth( $fields );
					$list_id    = isset( $opts['optin_list'] ) ? $opts['optin_list'] : '';

					$connection_data = [
						'connection_name' => get_the_title( $popup->ID ),
						'account_id'      => $account_id,
						'list_id'         => $list_id,
					];
					break;

				case 'mailerlite' :
					if ( ! class_exists( 'WPPopups_Mailerlite' ) ) {
						break;
					}

					$provider = new WPPopups_Mailerlite();
					$fields   = [
						'apikey' => $integrations['mailerlite']['lite_api'],
						'label'  => get_the_title( $popup->ID ),
					];

					$account_id = $provider->api_auth( $fields );
					$list_id    = isset( $opts['optin_list'] ) ? $opts['optin_list'] : '';

					$connection_data = [
						'connection_name' => get_the_title( $popup->ID ),
						'account_id'      => $account_id,
						'list_id'         => $list_id,
					];
					break;

				case 'mailigen' :
					if ( ! class_exists( 'WPPopups_Mailigen' ) ) {
						break;
					}

					$provider = new WPPopups_Mailigen();
					$fields   = [
						'apikey' => $integrations['mailigen']['mgen_api'],
						'label'  => get_the_title( $popup->ID ),
					];

					$account_id = $provider->api_auth( $fields );
					$list_id    = isset( $opts['optin_list'] ) ? $opts['optin_list'] : '';

					$connection_data = [
						'connection_name' => get_the_title( $popup->ID ),
						'account_id'      => $account_id,
						'list_id'         => $list_id,
						'options'         => [
							'doubleoptin'   => 0,
							'tags'          => '',
						],
					];
					break;

				case 'getresponse' :
					if ( ! class_exists( 'WPPopups_GetResponse' ) ) {
						break;
					}

					$provider = new WPPopups_GetResponse();
					$fields   = [
						'apikey' => $integrations['getresponse']['gr_api'],
						'label'  => get_the_title( $popup->ID ),
					];

					$account_id = $provider->api_auth( $fields );
					$list_id    = isset( $opts['optin_list'] ) ? $opts['optin_list'] : '';

					$connection_data = [
						'connection_name' => get_the_title( $popup->ID ),
						'account_id'      => $account_id,
						'list_id'         => $list_id,
					];
					break;
			}

			$data['providers'] = [ $slug => [ $connection_id => $connection_data ] ];
		}


		$new_id = wp_insert_post(
			[
				'post_title'   => $popup->post_title,
				'post_status'  => 'draft',
				'post_type'    => 'wppopups',
				'post_excerpt' => $popup->post_content,
				'post_parent'  => $parent_id,
			]
		);


		if ( $new_id ) {
			$data['id'] = $new_id;

			// Custom CSS
			$custom_css                = str_replace( '#spu-' . $popup->ID, '#spu-' . $new_id, $opts['css']['custom_css'] );
			$data['css']['custom_css'] = sanitize_textarea_field( $custom_css );

			// Check if upgrading from very old version and fix cookies
			if ( $data['settings']['conversion_cookie_name'] == 'spu_conversion' ) {
				$data['settings']['conversion_cookie_name'] .= '_' . $new_id;
				$data['settings']['closing_cookie_name']    .= '_' . $new_id;
			}
			wppopups()->popups->update( $new_id, $data, [ 'post_status' => 'draft' ] );
		}

		// Stats
		global $wpdb;

		$table = $wpdb->prefix . 'spu_hits_logs';
		$query = 'SELECT * FROM ' . $table . ' WHERE box_id = ' . $popup->ID;
		$hits  = $wpdb->get_results( $query );

		if ( $hits ) {
			foreach ( $hits as $hit ) {
				$hit_id = wppopups()->stats->save_record( $new_id, $hit->post_id, $hit->hit_type );
			}
		}

		return $new_id;
	}


	/**
	 * Get system information.
	 *
	 * Based on a function from Easy Digital Downloads by Pippin Williamson.
	 *
	 * @link https://github.com/easydigitaldownloads/easy-digital-downloads/blob/master/includes/admin/tools.php#L470
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_system_info() {

		global $wpdb;

		// Get theme info.
		$theme_data = wp_get_theme();
		$theme      = $theme_data->Name . ' ' . $theme_data->Version;

		$return = '### Begin System Info ###' . "\n\n";

		// WP Popups info.
		$activated = get_option( 'wppopups_activated', [] );
		$return    .= '-- WP Popups Info' . "\n\n";
		if ( ! empty( $activated['pro'] ) ) {
			$date   = $activated['pro'] + ( get_option( 'gmt_offset' ) * 3600 );
			$return .= 'Pro:                      ' . date_i18n( esc_html__( 'M j, Y @ g:ia' ), $date ) . "\n";
		}
		if ( ! empty( $activated['lite'] ) ) {
			$date   = $activated['lite'] + ( get_option( 'gmt_offset' ) * 3600 );
			$return .= 'Lite:                     ' . date_i18n( esc_html__( 'M j, Y @ g:ia' ), $date ) . "\n";
		}

		// Now the basics...
		$return .= "\n" . '-- Site Info' . "\n\n";
		$return .= 'Site URL:                 ' . site_url() . "\n";
		$return .= 'Home URL:                 ' . home_url() . "\n";
		$return .= 'Multisite:                ' . ( is_multisite() ? 'Yes' : 'No' ) . "\n";

		// WordPress configuration.
		$return .= "\n" . '-- WordPress Configuration' . "\n\n";
		$return .= 'Version:                  ' . get_bloginfo( 'version' ) . "\n";
		$return .= 'Language:                 ' . ( defined( 'WPLANG' ) && WPLANG ? WPLANG : 'en_US' ) . "\n";
		$return .= 'Permalink Structure:      ' . ( get_option( 'permalink_structure' ) ? get_option( 'permalink_structure' ) : 'Default' ) . "\n";
		$return .= 'Active Theme:             ' . $theme . "\n";
		$return .= 'Show On Front:            ' . get_option( 'show_on_front' ) . "\n";
		// Only show page specs if front page is set to 'page'.
		if ( get_option( 'show_on_front' ) === 'page' ) {
			$front_page_id = get_option( 'page_on_front' );
			$blog_page_id  = get_option( 'page_for_posts' );

			$return .= 'Page On Front:            ' . ( 0 != $front_page_id ? get_the_title( $front_page_id ) . ' (#' . $front_page_id . ')' : 'Unset' ) . "\n";
			$return .= 'Page For Posts:           ' . ( 0 != $blog_page_id ? get_the_title( $blog_page_id ) . ' (#' . $blog_page_id . ')' : 'Unset' ) . "\n";
		}
		$return .= 'ABSPATH:                  ' . ABSPATH . "\n";
		$return .= 'Table Prefix:             ' . 'Length: ' . strlen( $wpdb->prefix ) . '   Status: ' . ( strlen( $wpdb->prefix ) > 16 ? 'ERROR: Too long' : 'Acceptable' ) . "\n";
		$return .= 'WP_DEBUG:                 ' . ( defined( 'WP_DEBUG' ) ? WP_DEBUG ? 'Enabled' : 'Disabled' : 'Not set' ) . "\n";
		$return .= 'WPPOPUPS_DEBUG:            ' . ( defined( 'WPPOPUPS_DEBUG' ) ? WPPOPUPS_DEBUG ? 'Enabled' : 'Disabled' : 'Not set' ) . "\n";
		$return .= 'Memory Limit:             ' . WP_MEMORY_LIMIT . "\n";
		$return .= 'Registered Post Stati:    ' . implode( ', ', get_post_stati() ) . "\n";

		// @todo WP Popups configuration/specific details.
		$return .= "\n" . '-- WordPress Uploads/Constants' . "\n\n";
		$return .= 'WP_CONTENT_DIR:           ' . ( defined( 'WP_CONTENT_DIR' ) ? WP_CONTENT_DIR ? WP_CONTENT_DIR : 'Disabled' : 'Not set' ) . "\n";
		$return .= 'WP_CONTENT_URL:           ' . ( defined( 'WP_CONTENT_URL' ) ? WP_CONTENT_URL ? WP_CONTENT_URL : 'Disabled' : 'Not set' ) . "\n";
		$return .= 'UPLOADS:                  ' . ( defined( 'UPLOADS' ) ? UPLOADS ? UPLOADS : 'Disabled' : 'Not set' ) . "\n";

		$uploads_dir = wp_upload_dir();

		$return .= 'wp_uploads_dir() path:    ' . $uploads_dir['path'] . "\n";
		$return .= 'wp_uploads_dir() url:     ' . $uploads_dir['url'] . "\n";
		$return .= 'wp_uploads_dir() basedir: ' . $uploads_dir['basedir'] . "\n";
		$return .= 'wp_uploads_dir() baseurl: ' . $uploads_dir['baseurl'] . "\n";

		// Get plugins that have an update.
		$updates = get_plugin_updates();

		// Must-use plugins.
		// NOTE: MU plugins can't show updates!
		$muplugins = get_mu_plugins();
		if ( count( $muplugins ) > 0 && ! empty( $muplugins ) ) {
			$return .= "\n" . '-- Must-Use Plugins' . "\n\n";

			foreach ( $muplugins as $plugin => $plugin_data ) {
				$return .= $plugin_data['Name'] . ': ' . $plugin_data['Version'] . "\n";
			}
		}

		// WordPress active plugins.
		$return .= "\n" . '-- WordPress Active Plugins' . "\n\n";

		$plugins        = get_plugins();
		$active_plugins = get_option( 'active_plugins', [] );

		foreach ( $plugins as $plugin_path => $plugin ) {
			if ( ! in_array( $plugin_path, $active_plugins, true ) ) {
				continue;
			}
			$update = ( array_key_exists( $plugin_path, $updates ) ) ? ' (needs update - ' . $updates[ $plugin_path ]->update->new_version . ')' : '';
			$return .= $plugin['Name'] . ': ' . $plugin['Version'] . $update . "\n";
		}

		// WordPress inactive plugins.
		$return .= "\n" . '-- WordPress Inactive Plugins' . "\n\n";

		foreach ( $plugins as $plugin_path => $plugin ) {
			if ( in_array( $plugin_path, $active_plugins, true ) ) {
				continue;
			}
			$update = ( array_key_exists( $plugin_path, $updates ) ) ? ' (needs update - ' . $updates[ $plugin_path ]->update->new_version . ')' : '';
			$return .= $plugin['Name'] . ': ' . $plugin['Version'] . $update . "\n";
		}

		if ( is_multisite() ) {
			// WordPress Multisite active plugins.
			$return .= "\n" . '-- Network Active Plugins' . "\n\n";

			$plugins        = wp_get_active_network_plugins();
			$active_plugins = get_site_option( 'active_sitewide_plugins', [] );

			foreach ( $plugins as $plugin_path ) {
				$plugin_base = plugin_basename( $plugin_path );
				if ( ! array_key_exists( $plugin_base, $active_plugins ) ) {
					continue;
				}
				$update = ( array_key_exists( $plugin_path, $updates ) ) ? ' (needs update - ' . $updates[ $plugin_path ]->update->new_version . ')' : '';
				$plugin = get_plugin_data( $plugin_path );
				$return .= $plugin['Name'] . ': ' . $plugin['Version'] . $update . "\n";
			}
		}

		// Server configuration (really just versions).
		$return .= "\n" . '-- Webserver Configuration' . "\n\n";
		$return .= 'PHP Version:              ' . PHP_VERSION . "\n";
		$return .= 'MySQL Version:            ' . $wpdb->db_version() . "\n";
		$return .= 'Webserver Info:           ' . $_SERVER['SERVER_SOFTWARE'] . "\n";

		// PHP configs... now we're getting to the important stuff.
		$return .= "\n" . '-- PHP Configuration' . "\n\n";
		$return .= 'Memory Limit:             ' . ini_get( 'memory_limit' ) . "\n";
		$return .= 'Upload Max Size:          ' . ini_get( 'upload_max_filesize' ) . "\n";
		$return .= 'Post Max Size:            ' . ini_get( 'post_max_size' ) . "\n";
		$return .= 'Upload Max Filesize:      ' . ini_get( 'upload_max_filesize' ) . "\n";
		$return .= 'Time Limit:               ' . ini_get( 'max_execution_time' ) . "\n";
		$return .= 'Max Input Vars:           ' . ini_get( 'max_input_vars' ) . "\n";
		$return .= 'Display Errors:           ' . ( ini_get( 'display_errors' ) ? 'On (' . ini_get( 'display_errors' ) . ')' : 'N/A' ) . "\n";

		// PHP extensions and such.
		$return .= "\n" . '-- PHP Extensions' . "\n\n";
		$return .= 'cURL:                     ' . ( function_exists( 'curl_init' ) ? 'Supported' : 'Not Supported' ) . "\n";
		$return .= 'fsockopen:                ' . ( function_exists( 'fsockopen' ) ? 'Supported' : 'Not Supported' ) . "\n";
		$return .= 'SOAP Client:              ' . ( class_exists( 'SoapClient' ) ? 'Installed' : 'Not Installed' ) . "\n";
		$return .= 'Suhosin:                  ' . ( extension_loaded( 'suhosin' ) ? 'Installed' : 'Not Installed' ) . "\n";

		// Session stuff.
		$return .= "\n" . '-- Session Configuration' . "\n\n";
		$return .= 'Session:                  ' . ( isset( $_SESSION ) ? 'Enabled' : 'Disabled' ) . "\n";

		// The rest of this is only relevant if session is enabled.
		if ( isset( $_SESSION ) ) {
			$return .= 'Session Name:             ' . esc_html( ini_get( 'session.name' ) ) . "\n";
			$return .= 'Cookie Path:              ' . esc_html( ini_get( 'session.cookie_path' ) ) . "\n";
			$return .= 'Save Path:                ' . esc_html( ini_get( 'session.save_path' ) ) . "\n";
			$return .= 'Use Cookies:              ' . ( ini_get( 'session.use_cookies' ) ? 'On' : 'Off' ) . "\n";
			$return .= 'Use Only Cookies:         ' . ( ini_get( 'session.use_only_cookies' ) ? 'On' : 'Off' ) . "\n";
		}

		$return .= "\n" . '### End System Info ###';

		return $return;
	}

	private function set_time_out() {
		ignore_user_abort( true );

		if ( ! in_array( 'set_time_limit', explode( ',', ini_get( 'disable_functions' ) ), true ) ) {
			set_time_limit( 0 );
		}
	}
}

new WPPopups_Tools();
