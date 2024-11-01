=== Simple PayPal Payment ===
Contributors: Katsushi Kawamori
Donate link: https://shop.riverforest-wp.info/donate/
Tags: block, paypal, shortcode, widget
Requires at least: 4.7
Requires PHP: 5.6
Tested up to: 5.8
Stable tag: 2.03
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Integrates PayPal checkout into WordPress.

== Description ==

= Integrates PayPal checkout into WordPress. =
* Paste PayPal checkout button to Single Post and Single Page by short code.
* Paste PayPal checkout button to Single Post and Single Page by block.
* Paste PayPal checkout button to Archive Page and Home Page by widget.
* Complete payment without screen transition.
* Can customize the PayPal checkout button.
* Can specify the text or html before payment and after payment.
* Can remove html elements to after payment.
* Prepared a filter hook for processing immediately after billing.

= Tutorial Video =
[youtube https://youtu.be/acUewo849YU]

= Sample of how to use the filter hook =
* Show button
~~~
echo do_shortcode('[simplepaypalpayment size="medium" amount=20 currency="USD" email="test@test.com" payname="testpay"]');
~~~
* shortcode
Attribute : Description
locale : Supported locale codes
size : Button styles
color : Button styles
shape : Button styles
label : Button styles
amount : Price
currency : Currency Codes
before : Display before payment
after : Display after payment
remove : HTML elements to remove after payment
remove2 : HTML elements to remove after payment
email : Email
payname : Unique name for this payment
* Filter hook & Function
~~~
/** ==================================================
 * Filter of Simple PayPal Payment
 *
 * @param string $data  data.
 * @param string $payment_data  payment_data.
 * @param string $email  email.
 * @param int    $amount  amount.
 * @param string $currency  currency.
 * @param string $payname  payname.
 */
function paypal_charge( $data, $payment_data, $email, $amount, $currency, $payname ) {

	/* Please write the process to be done when billing succeeds. */
	if ( 'testpay' === $payname ) {
		update_option( 'testpay_paypal', 'paypal' . $payname . $amount . $currency );
	}

}
add_filter( 'simple_paypal_payment_charge', 'paypal_charge', 10, 6 );
~~~
* Filter hook
Variable : Description : From
$data : Payment information by JSON : Value of PayPal
$payment_data : Payment information by JSON : Value of PayPal
$email : Email : Value of Simple PayPal Payment
$amount : Price : Value of Simple PayPal Payment
$currency : Currency Codes : Value of Simple PayPal Payment
$payname : Unique name for this payment : Value of Simple PayPal Payment

== Installation ==

1. Upload `simple-paypal-payment` directory to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

none

== Screenshots ==

1. PayPal Settings
2. Short code
3. block
4. Insert from block
5. Page view
6. Widget Settings

== Changelog ==

= 2.03 =
Minor change.

= 2.02 =
Rebuilt blocks.
Fixed admin screen.
Fixed uninstall.

= 2.01 =
Fixed sample code.
Added a "payname" to the block.

= 2.00 =
The block now supports ESNext.

= 1.17 =
Fixed problem shortcode.

= 1.16 =
Fixed a payment issue in the admin screen.

= 1.15 =
Conformed to the WordPress coding standard.

= 1.14 =
Fixed script loading error on archive page.

= 1.13 =
Minor change.

= 1.12 =
Fixed of filter sample code.
Fixed  problem of widget.

= 1.11 =
Fixed  problem of widget.

= 1.10 =
Add shortcode attribute 'payname'.

= 1.09 =
Fixed loading problem of Javascript.

= 1.08 =
Change translate.

= 1.07 =
Prepared a filter hook for processing immediately after billing.
Add shortcode attribute 'remove2'.

= 1.06 =
Fixed  problem of option table initialization.

= 1.05 =
Can remove html elements to after payment.

= 1.04 =
Add widget.

= 1.03 =
Can change default value by admin settings menu.

= 1.02 =
Add block default value.

= 1.01 =
Fixed problem of view for archive page.

= 1.00 =
Initial release.

== Upgrade Notice ==

= 1.00 =
Initial release.

