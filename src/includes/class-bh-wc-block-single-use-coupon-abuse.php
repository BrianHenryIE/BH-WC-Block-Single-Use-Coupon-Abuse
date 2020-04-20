<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * frontend-facing side of the site and the admin area.
 *
 * @link       https://BrianHenry.ie
 * @since      1.0.0
 *
 * @package    BH_WC_Block_Single_Use_Coupon_Abuse
 * @subpackage BH_WC_Block_Single_Use_Coupon_Abuse/includes
 */

namespace BH_WC_Block_Single_Use_Coupon_Abuse\includes;

use BH_WC_Block_Single_Use_Coupon_Abuse\WPPB\WPPB_Loader_Interface;
use BH_WC_Block_Single_Use_Coupon_Abuse\WPPB\WPPB_Object;

/**
 * The core plugin class.
 *
 * @since      1.0.0
 * @package    BH_WC_Block_Single_Use_Coupon_Abuse
 * @subpackage BH_WC_Block_Single_Use_Coupon_Abuse/includes
 * @author      BrianHenryIE <BrianHenryIE@gmail.com>
 */
class BH_WC_Block_Single_Use_Coupon_Abuse extends WPPB_Object {

	/**
	 * Allow access for testing and unhooking.
	 *
	 * @var Discounts The plugin Admin object instance.
	 */
	public $woocommerce_discounts;

	/**
	 * Allow access for testing and unhooking.
	 *
	 * @var I18n The plugin I18n object instance.
	 */
	public $i18n;

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      WPPB_Loader_Interface    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the frontend-facing side of the site.
	 *
	 * @since    1.0.0
	 *
	 * @param WPPB_Loader_Interface $loader The WPPB class which adds the hooks and filters to WordPress.
	 */
	public function __construct( $loader ) {
		if ( defined( 'BH_WC_BLOCK_SINGLE_USE_COUPON_ABUSE_VERSION' ) ) {
			$this->version = BH_WC_BLOCK_SINGLE_USE_COUPON_ABUSE_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'bh-wc-block-single-use-coupon-abuse';

		parent::__construct( $this->plugin_name, $this->version );

		$this->loader = $loader;

		$this->set_locale();
		$this->define_woocommerce_hooks();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$this->i18n = $plugin_i18n = new I18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to woocommerce functionality.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_woocommerce_hooks() {

		$this->woocommerce_discounts = new Discounts( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'woocommerce_applied_coupon', $this->woocommerce_discounts, 'check_coupon_as_it_is_added', 10, 1 );
		$this->loader->add_filter( 'woocommerce_coupon_is_valid', $this->woocommerce_discounts, 'check_coupon_address_use', 10, 3 );
		$this->loader->add_action( 'woocommerce_check_cart_items', $this->woocommerce_discounts, 'process_coupons_at_checkout', 10 );

	}


	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    WPPB_Loader_Interface    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

}
