<?php
/**
 * The woocommerce discounts/coupons-specific functionality of the plugin.
 *
 * @link       https://BrianHenry.ie
 * @since      1.0.0
 *
 * @package    BH_WC_Block_Single_Use_Coupon_Abuse
 * @subpackage BH_WC_Block_Single_Use_Coupon_Abuse/woocommerce
 */

namespace BH_WC_Block_Single_Use_Coupon_Abuse\woocommerce;

use BH_WC_Block_Single_Use_Coupon_Abuse\WPPB\WPPB_Object;

/**
 *
 * @package    BH_WC_Block_Single_Use_Coupon_Abuse
 * @subpackage BH_WC_Block_Single_Use_Coupon_Abuse/admin
 * @author      BrianHenryIE <BrianHenryIE@gmail.com>
 */
class Discounts extends WPPB_Object {

	/**
	 *
	 * @hooked woocommerce_applied_coupon
	 *
	 * @see WC_Cart::apply_coupon()
	 */
	public function check_coupon_as_it_is_added( $coupon_code ) {

		$coupon = new WC_Coupon( $coupon_code );

		$is_valid = true;
		$is_valid = $this->check_coupon_address_use( $is_valid, $coupon, null );

		if ( ! $is_valid ) {

			$coupon->add_coupon_message( WC_Coupon::E_WC_COUPON_INVALID_REMOVED );

			wc_add_notice( WC_Coupon::E_WC_COUPON_INVALID_REMOVED, 'error' );

			throw new Exception( WC_Coupon::E_WC_COUPON_INVALID_REMOVED );
		}
	}


	/**
	 *
	 * @hooked woocommerce_check_cart_items
	 *
	 * When checkout is being processed, it runs do_action('woocommerce_check_cart_items')
	 * which should
	 * wc_add_notice( $result->get_error_message(), 'error' );
	 * $return = false;
	 *
	 * @see \WC_Checkout::check_cart_items()
	 * @see WC_Cart::check_cart_items()
	 * @see WC_Cart::check_cart_coupons()
	 */
	public function process_coupons_at_checkout() {

		/** WC_Cart */
		$cart = WC()->cart;

		/** @var WC_Coupon[] $cart_coupons */
		$cart_coupons = $cart->get_coupons();

		foreach ( $cart_coupons as $coupon ) {

			$is_valid = true;
			$is_valid = $this->check_coupon_address_use( $is_valid, $coupon, null );

			if ( ! $is_valid ) {
				$coupon->add_coupon_message( WC_Coupon::E_WC_COUPON_INVALID_REMOVED );
				$cart->remove_coupon( $coupon->get_code() );

				wc_add_notice( WC_Coupon::E_WC_COUPON_INVALID_REMOVED, 'error' );
				throw new Exception( WC_Coupon::E_WC_COUPON_INVALID_REMOVED );
			}
		}
	}


	/**
	 * Customers were using new email addresses to use the same coupon more than once.
	 * This checks their address. Also checks if surname + city are the same.
	 *
	 * @hooked woocommerce_coupon_is_valid
	 *
	 * @see WC_Discounts::is_coupon_valid()
	 *
	 * @param bool         $is_valid
	 * @param WC_Coupon    $coupon
	 * @param WC_Discounts $wc_discounts
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function check_coupon_address_use( $is_valid, $coupon, $wc_discounts ) {

		if ( 1 === $coupon->get_usage_limit_per_user() ) {

			if ( isset( $_COOKIE[ $coupon->get_code() ] ) ) {
				error_log( 'coupon already used – cookie' );
				return false;
			}

			$date_created = $coupon->get_date_created();
			$search_since = $date_created->sub( new \DateInterval( 'P1D' ) );
			$start_date   = $search_since->format( 'Y-m-d' );

			/** @var \WC_Order[] $orders_for_coupon */
			$orders_for_coupon = $this->get_orders_by_coupon( $coupon->get_code(), $start_date );

			/** WC_Cart */
			$cart = WC()->cart;

			$customer_address  = $cart->get_customer()->get_billing_address_1();
			$customer_city     = $cart->get_customer()->get_billing_city();
			$customer_postcode = $cart->get_customer()->get_billing_postcode();
			$customer_name     = $cart->get_customer()->get_last_name();

			// WC_Cart wasn't returning the customer's name.
			if ( empty( $customer_name ) && isset( $_POST['post_data'] ) ) {

				$output_array = array();
				preg_match( '/billing_last_name=(.*?)&/', $_POST['post_data'], $output_array );
				$customer_name = $output_array[1];
			}

			foreach ( $orders_for_coupon as $order ) {

				// Address 1 + city
				if ( $order->get_billing_address_1() === $customer_address && $order->get_billing_city() === $customer_city ) {

					error_log( 'Coupon already used at this address on order ' . $order->get_id() );

					$cart->remove_coupon( $coupon->get_code() );

					$is_valid = false;
				}

				// City + name
				if ( $order->get_billing_city() === $customer_city && $order->get_billing_last_name() === $customer_name ) {

					error_log( 'Coupon already used by this family in this city on order ' . $order->get_id() );

					$cart->remove_coupon( $coupon->get_code() );

					$is_valid = false;
				}

				// Zip + name
				if ( $order->get_billing_postcode() === $customer_postcode && $order->get_billing_last_name() === $customer_name ) {

					error_log( 'Coupon already used by this family in this zip on order ' . $order->get_id() );

					$cart->remove_coupon( $coupon->get_code() );

					$is_valid = false;
				}
			}

			if ( ! $is_valid ) {
				$coupon_expiry = $coupon->get_date_expires();

				$cookie_expiry = ! is_null( $coupon_expiry ) ? $coupon_expiry->getTimestamp() : YEAR_IN_SECONDS;

				// TODO: " Cannot modify header information - headers already sent"
				// This function is run a few times, so maybe the cookie is already set anyway!
				setcookie( $coupon->get_code(), 'ALREADY_USED', $cookie_expiry );
			}
		}

		return $is_valid;
	}

	/**
	 * @see https://stackoverflow.com/questions/42769306/how-to-retrieve-a-list-of-woocommerce-orders-which-use-a-particular-coupon
	 *
	 * @param string     $coupon_code
	 * @param $start_date
	 * @param $end_date
	 * @return array
	 * @throws Exception
	 */
	protected function get_orders_by_coupon( $coupon_code, $start_date, $end_date = null ) {

		$tomorrow = new \DateTime();
		$tomorrow->add( new \DateInterval( 'P1D' ) );
		$tomorrow = $tomorrow->format( 'Y-m-d' );
		$end_date = is_null( $end_date ) ? $tomorrow : $end_date;

		global $wpdb;

		$query = "SELECT
        p.ID AS order_id
        FROM
        {$wpdb->prefix}posts AS p
        INNER JOIN {$wpdb->prefix}woocommerce_order_items AS woi ON p.ID = woi.order_id
        WHERE
        p.post_type = 'shop_order' AND
        p.post_status IN ('" . implode( "','", array_keys( wc_get_order_statuses() ) ) . "') AND
        woi.order_item_type = 'coupon' AND
        woi.order_item_name = '" . $coupon_code . "' AND
        DATE(p.post_date) BETWEEN '" . $start_date . "' AND '" . $end_date . "';";

		$order_ids = $wpdb->get_results( $query );

		$orders = array();

		foreach ( $order_ids as $key => $order_id ) {

			$order_id = $order_id->order_id;

			$orders[] = wc_get_order( $order_id );
		}

		return $orders;
	}
}
