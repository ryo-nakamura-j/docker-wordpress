<?php
/**
 * Class to load freemius configuration
 */

namespace Stop_User_Enumeration\Includes;


class Freemius_Config {

	public function init() {

		global $sue_fs;

		if ( ! isset( $sue_fs ) ) {
			// Include Freemius SDK.
			require_once dirname( __FILE__ ) . '/vendor/freemius/wordpress-sdk/start.php';

			$sue_fs = fs_dynamic_init( array(
				'id'             => '1318',
				'slug'           => 'stop-user-enumeration',
				'type'           => 'plugin',
				'public_key'     => 'pk_bbbd29c5de1662b6753871351b01f',
				'is_premium'     => false,
				'has_addons'     => false,
				'has_paid_plans' => false,
				'menu'           => array(
					'slug'    => 'stop-user-enumeration',
					'account' => false,
					'contact' => false,
					'support' => false,
					'parent'  => array(
						'slug' => 'options-general.php',
					),
				),
			) );
		}

		return $sue_fs;
	}


}

