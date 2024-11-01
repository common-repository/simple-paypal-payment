<?php
/**
 * Simple PayPal Payment
 *
 * @package    SimplePayPalPayment
 * @subpackage Simple PayPal Payment Ajax
/*  Copyright (c) 2019- Katsushi Kawamori (email : dodesyoswift312@gmail.com)
	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; version 2 of the License.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

$simplepaypalpaymentajax = new SimplePayPalPaymentAjax();

/** ==================================================
 * Payment Ajax
 */
class SimplePayPalPaymentAjax {

	/** ==================================================
	 * Construct
	 *
	 * @since 1.00
	 */
	public function __construct() {

		$action1 = 'simple-paypal-payment-charge-ajax-action';
		add_action( 'wp_ajax_' . $action1, array( $this, 'simplepaypalpayment_charge_callback' ) );
		add_action( 'wp_ajax_nopriv_' . $action1, array( $this, 'simplepaypalpayment_charge_callback' ) );

	}

	/** ==================================================
	 * Charge Callback
	 *
	 * @param object $data  data.
	 * @since 1.00
	 */
	public function simplepaypalpayment_charge_callback( $data = null ) {

		$action1 = 'simple-paypal-payment-charge-ajax-action';
		if ( check_ajax_referer( $action1, 'nonce', false ) ) {
			if ( isset( $_POST['data'] ) && ! empty( $_POST['data'] ) ) {
				$data = sanitize_text_field( wp_unslash( $_POST['data'] ) );
			}
			if ( isset( $_POST['payment_data'] ) && ! empty( $_POST['payment_data'] ) ) {
				$payment_data = sanitize_text_field( wp_unslash( $_POST['payment_data'] ) );
			}
			if ( isset( $_POST['email'] ) && ! empty( $_POST['email'] ) ) {
				$email = sanitize_email( wp_unslash( $_POST['email'] ) );
			}
			if ( isset( $_POST['amount'] ) && ! empty( $_POST['amount'] ) ) {
				$amount = intval( $_POST['amount'] );
			}
			if ( isset( $_POST['currency'] ) && ! empty( $_POST['currency'] ) ) {
				$currency = sanitize_text_field( wp_unslash( $_POST['currency'] ) );
			}
			if ( isset( $_POST['payname'] ) && ! empty( $_POST['payname'] ) ) {
				$payname = sanitize_text_field( wp_unslash( $_POST['payname'] ) );
			}
			/* Payment */
			if ( ! is_null( $data ) ) {
				$data = apply_filters( 'simple_paypal_payment_charge', $data, $payment_data, $email, $amount, $currency, $payname );
				if ( is_wp_error( $data ) ) {
					return $data;
				}
			}
		} else {
			status_header( '403' );
			echo 'Forbidden';
		}

		wp_die();

	}

}


