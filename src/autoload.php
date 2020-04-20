<?php
/**
 * Loads all required classes
 *
 * Uses PSR4 & wp-namespace-autoloader.
 *
 * @link              https://BrianHenry.ie
 * @since             1.0.0
 * @package           BH_WC_Block_Single_Use_Coupon_Abuse
 *
 * @see https://github.com/pablo-sg-pacheco/wp-namespace-autoloader/
 */

namespace BH_WC_Block_Single_Use_Coupon_Abuse;

use BH_WC_Block_Single_Use_Coupon_Abuse\Pablo_Pacheco\WP_Namespace_Autoloader\WP_Namespace_Autoloader;


// The plugin-scoped namespace for composer required libraries, as specified in composer.json Mozart config.
$dep_namespace = 'BH_WC_Block_Single_Use_Coupon_Abuse';
// The Mozart config `dep_directory` adjusted for relative path.
$dep_directory = '/vendor/';

spl_autoload_register(
	function ( $namespaced_class_name ) use ( $dep_namespace, $dep_directory ) {

		$autoload_directory = __DIR__ . $dep_directory . '/';

		// The class name with its true namespace.
		$bare_namespaced_class_name = preg_replace( "#$dep_namespace\\\*#", '', $namespaced_class_name );

		$file_path = $autoload_directory . str_replace( '\\', '/', $bare_namespaced_class_name ) . '.php';

		if ( file_exists( $file_path ) ) {
			require_once $file_path;
		}
	}
);

$wpcs_autoloader = new WP_Namespace_Autoloader();
$wpcs_autoloader->init();
