<?php
/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://BrianHenry.ie
 * @since      1.0.0
 *
 * @package    BH_WC_Block_Single_Use_Coupon_Abuse
 * @subpackage BH_WC_Block_Single_Use_Coupon_Abuse/includes
 */

namespace BH_WC_Block_Single_Use_Coupon_Abuse\includes;

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    BH_WC_Block_Single_Use_Coupon_Abuse
 * @subpackage BH_WC_Block_Single_Use_Coupon_Abuse/includes
 * @author      BrianHenryIE <BrianHenryIE@gmail.com>
 */
class I18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'bh-wc-block-single-use-coupon-abuse',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
