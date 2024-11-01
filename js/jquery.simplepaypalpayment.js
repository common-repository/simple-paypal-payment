/**
 * Simple PayPal Payment
 *
 * @package    Simple PayPal Payment Ajax
 * @subpackage jquery.simplepaypalpayment.js
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

jQuery(
	function($) {
		var paypal_env           = SIMPLEPAYPALPAYMENTCHARGE.env;
		var	paypal_sandbox_id    = SIMPLEPAYPALPAYMENTCHARGE.sandbox_id;
		var	paypal_production_id = SIMPLEPAYPALPAYMENTCHARGE.production_id;
		var	paypal_locale        = SIMPLEPAYPALPAYMENTCHARGE.locale;
		var paypal_button_id     = '#simple_paypal_payment_paypal-button';
		var button_size          = SIMPLEPAYPALPAYMENTCHARGE.size;
		var button_color         = SIMPLEPAYPALPAYMENTCHARGE.color;
		var button_shape         = SIMPLEPAYPALPAYMENTCHARGE.shape;
		var button_label         = SIMPLEPAYPALPAYMENTCHARGE.label;
		var paypal_amount        = SIMPLEPAYPALPAYMENTCHARGE.amount;
		var paypal_currency      = SIMPLEPAYPALPAYMENTCHARGE.currency;
		var html_after           = SIMPLEPAYPALPAYMENTCHARGE.after;
		var html_remove          = SIMPLEPAYPALPAYMENTCHARGE.remove;
		var html_remove2         = SIMPLEPAYPALPAYMENTCHARGE.remove2;
		paypal.Button.render(
			{
				/* Configure environment */
				env: paypal_env,
				client: {
					sandbox: paypal_sandbox_id,
					production: paypal_production_id
				},
				/* Customize button (optional) */
				locale: paypal_locale,
				style: {
					size: button_size,
					color: button_color,
					shape: button_shape,
					label: button_label
				},

				/* Enable Pay Now checkout flow (optional) */
				commit: true,

				/* Set up a payment */
				payment: function(data, actions) {
					return actions.payment.create(
						{
							payment: {
								transactions: [
								{
									amount: {
										total: paypal_amount,
										currency: paypal_currency
									}
								}
								]
							},
							experience: {
								input_fields: {
									no_shipping: 1
								}
							}
						}
					);
				},
				/* Execute the payment */
				onAuthorize: function(data, actions) {
					return actions.payment.execute().then(
						function(payment) {
							$( '.simple_paypal_payment_before' ).remove();
							$( paypal_button_id ).remove();
							$( html_remove ).remove();
							$( html_remove2 ).remove();
							$( '.simple_paypal_payment_after' ).empty();
							$( '.simple_paypal_payment_after' ).append( html_after );
							chargeServer( JSON.stringify( data, payment ) );
						}
					);
				}
			},
			paypal_button_id
		);

		/* Charge Server */
		function chargeServer(data, payment_data) {
			$.ajax(
				{
					type: 'POST',
					dataType: 'json',
					url: SIMPLEPAYPALPAYMENTCHARGE.ajax_url,
					data: {
						'action': SIMPLEPAYPALPAYMENTCHARGE.action,
						'nonce': SIMPLEPAYPALPAYMENTCHARGE.nonce,
						'data' : data,
						'payment_data' : payment_data,
						'email' : SIMPLEPAYPALPAYMENTCHARGE.email,
						'amount': SIMPLEPAYPALPAYMENTCHARGE.amount,
						'currency': SIMPLEPAYPALPAYMENTCHARGE.currency,
						'payname': SIMPLEPAYPALPAYMENTCHARGE.payname
					}
				}
			).done(
				function(callback){
						/* console.log(callback); */
				}
			).fail(
				function(XMLHttpRequest, textStatus, errorThrown){
						/* console.log("XMLHttpRequest : " + XMLHttpRequest.status); */
						/* console.log("textStatus     : " + textStatus); */
						/* console.log("errorThrown    : " + errorThrown.message); */
				}
			);
		}
	}
);
