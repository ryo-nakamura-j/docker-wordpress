<?php

/**
 * Ask for some love.
 *
 * @package    WPPopups
 * @author     WPPopups
 * @since 2.0.0
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2017, WP Popups LLC
 */
class WPPopups_Review {

	/**
	 * Primary class constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		// Admin notice requesting review.
		add_action( 'admin_notices', [ $this, 'review_request' ] );
		add_action( 'wp_ajax_wppopups_review_dismiss', [ $this, 'review_dismiss' ] );

		// Admin footer text.
		add_filter( 'admin_footer_text', [ $this, 'admin_footer' ], 1, 2 );
	}

	/**
	 * Add admin notices as needed for reviews.
	 *
	 * @since 2.0.0
	 */
	public function review_request() {

		if ( ! is_super_admin() ) {
			return;
		}

		// Verify that we can do a check for reviews.
		$review = get_option( 'wppopups_review' );
		$time   = time();
		$load   = false;

		if ( ! $review ) {
			$review = [
				'time'      => $time,
				'dismissed' => false,
			];
			$load   = true;
		} else {
			// Check if it has been dismissed or not.
			if ( ( isset( $review['dismissed'] ) && ! $review['dismissed'] ) && ( isset( $review['time'] ) && ( ( $review['time'] + DAY_IN_SECONDS ) <= $time ) ) ) {
				$load = true;
			}
		}

		// If we cannot load, return early.
		if ( ! $load ) {
			return;
		}

		// Update the review option now.
		update_option( 'wppopups_review', $review );

		$this->review();
	}

	/**
	 * Maybe show Lite review request.
	 *
	 * @since 2.0.0
	 */
	public function review() {

		// Fetch when plugin was initially installed.
		$activated = get_option( 'wppopups_activated', [] );

		if ( ! empty( $activated['lite'] ) ) {
			// Only continue if plugin has been installed for at least 14 days.
			if ( ( $activated['lite'] + ( DAY_IN_SECONDS * 14 ) ) > time() ) {
				return;
			}
		} else {
			$activated['lite'] = time();
			update_option( 'wppopups_activated', $activated );

			return;
		}

		// Only proceed with displaying if the user created at least one popup.
		$popup_count = wp_count_posts( 'wppopups' );
		if ( empty( $popup_count->publish ) ) {
			return;
		}

		?>
		<div class="notice notice-info is-dismissible wppopups-review-notice">
			<p><?php esc_html_e( 'Hey, I noticed you created a popup with WP Popups - that’s awesome! Could you please do me a BIG favor and give it a 5-star rating on WordPress ? It would be a tremendous help for us', 'wp-popups-lite' ); ?></p>
			<p>
				<a href="https://wordpress.org/support/plugin/wp-popups-lite/reviews/?filter=5#new-post"
				   class="wppopups-dismiss-review-notice wppopups-review-out" target="_blank"
				   rel="noopener noreferrer"><?php esc_html_e( 'Ok, you deserve it', 'wp-popups-lite' ); ?></a><br>
				<a href="#" class="wppopups-dismiss-review-notice" target="_blank"
				   rel="noopener noreferrer"><?php esc_html_e( 'Nope, maybe later', 'wp-popups-lite' ); ?></a><br>
				<a href="#" class="wppopups-dismiss-review-notice" target="_blank"
				   rel="noopener noreferrer"><?php esc_html_e( 'I already did', 'wp-popups-lite' ); ?></a>
			</p>
		</div>
		<script type="text/javascript">
            jQuery(document).ready(function ($) {
                $(document).on('click', '.wppopups-dismiss-review-notice, .wppopups-review-notice button', function (event) {
                    if (!$(this).hasClass('wppopups-review-out')) {
                        event.preventDefault();
                    }
                    $.post(ajaxurl, {
                        action: 'wppopups_review_dismiss'
                    });
                    $('.wppopups-review-notice').remove();
                });
            });
		</script>
		<?php
	}

	/**
	 * Dismiss the review admin notice
	 *
	 * @since 2.0.0
	 */
	public function review_dismiss() {

		$review              = get_option( 'wppopups_review', [] );
		$review['time']      = time();
		$review['dismissed'] = true;

		update_option( 'wppopups_review', $review );
		die;
	}

	/**
	 * When user is on a WP Popups related admin page, display footer text
	 * that graciously asks them to rate us.
	 *
	 * @param string $text
	 *
	 * @return string
	 * @since 2.0.0
	 *
	 */
	public function admin_footer( $text ) {

		global $current_screen;

		if ( ! empty( $current_screen->id ) && strpos( $current_screen->id, 'popups' ) !== false ) {
			$url  = 'https://wordpress.org/support/plugin/wp-popups-lite/reviews/?filter=5#new-post';
			$text = sprintf(
				wp_kses(
				/* translators: $1$s - WP Popups plugin name; $2$s - WP.org review link; $3$s - WP.org review link. */
					__( 'Please rate %1$s <a href="%2$s" target="_blank" rel="noopener noreferrer">&#9733;&#9733;&#9733;&#9733;&#9733;</a> on <a href="%3$s" target="_blank" rel="noopener">WordPress.org</a> if you enjoy the plugin. It would be a tremendous help for us!', 'wp-popups-lite' ),
					[
						'a' => [
							'href'   => [],
							'target' => [],
							'rel'    => [],
						],
					]
				),
				'<strong>WP Popups</strong>',
				$url,
				$url
			);
		}

		return $text;
	}

}

new WPPopups_Review();
