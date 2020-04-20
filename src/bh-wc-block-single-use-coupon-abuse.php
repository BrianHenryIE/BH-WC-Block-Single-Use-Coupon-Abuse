<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://BrianHenry.ie
 * @since             1.0.0
 * @package           BH_WC_Block_Single_Use_Coupon_Abuse
 *
 * @wordpress-plugin
 * Plugin Name:       Block Single Use Coupon Abuse
 * Plugin URI:        http://github.com/BrianHenryIE/bh-wc-block-single-use-coupon-abuse/
 * Description:       When a WooCommerce single-use coupon is applied, past orders are checked against (Address 1 + city), (city + family name), (zip + family name) and a cookie set when the coupon is applied.
 * Version:           1.0.0
 * Author:            Brian Henry
 * Author URI:        https://BrianHenry.ie
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       bh-wc-block-single-use-coupon-abuse
 * Domain Path:       /languages
 */

namespace BH_WC_Block_Single_Use_Coupon_Abuse;

use BH_WC_Block_Single_Use_Coupon_Abuse\includes\Activator;
use BH_WC_Block_Single_Use_Coupon_Abuse\includes\Deactivator;
use BH_WC_Block_Single_Use_Coupon_Abuse\includes\BH_WC_Block_Single_Use_Coupon_Abuse;
use BH_WC_Block_Single_Use_Coupon_Abuse\WPPB\WPPB_Loader;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once plugin_dir_path( __FILE__ ) . 'autoload.php';

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'BH_WC_BLOCK_SINGLE_USE_COUPON_ABUSE_VERSION', '1.0.0' );


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function instantiate_bh_wc_block_single_use_coupon_abuse() {

	$loader = new WPPB_Loader();
	$plugin = new BH_WC_Block_Single_Use_Coupon_Abuse( $loader );

	return $plugin;
}

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and frontend-facing site hooks.
 */
$GLOBALS['bh_wc_block_single_use_coupon_abuse'] = $bh_wc_block_single_use_coupon_abuse = instantiate_bh_wc_block_single_use_coupon_abuse();
$bh_wc_block_single_use_coupon_abuse->run();
